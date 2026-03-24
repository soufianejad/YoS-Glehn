<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\BadgeService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    protected $notificationService;

    protected $badgeService;

    public function __construct(NotificationService $notificationService, BadgeService $badgeService)
    {
        $this->notificationService = $notificationService;
        $this->badgeService = $badgeService;
    }

    public function show(Book $book)
    {
        $quiz = $book->quizzes()->first();
        if (! $quiz) {
            return back()->with('error', 'No quiz is available for this book.');
        }

        // Redirect to the start method to create an attempt first
        return redirect()->route('quiz.start', $quiz);
    }

    /**
     * Create a quiz attempt and show the quiz questions.
     */
    public function start(Quiz $quiz)
    {
        $user = auth()->user();
        $quiz->load('book', 'questions');

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'total_questions' => $quiz->questions->count(),
            'started_at' => now(),
            'answers' => [], // Initialize with empty answers
        ]);

        return view('quiz.show', [
            'book' => $quiz->book,
            'quiz' => $quiz,
            'attempt' => $attempt,
        ]);
    }

    /**
     * Submit the quiz and store the attempt.
     */
    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
            'questions' => 'present|array',
        ]);

        $attempt = QuizAttempt::findOrFail($request->attempt_id);

        if ($attempt->user_id !== Auth::id() || $attempt->quiz_id !== $quiz->id) {
            abort(403);
        }

        if ($attempt->completed_at) {
            return redirect()->route('quiz.result', $attempt)->with('error', 'Quiz already submitted.');
        }

        $score = 0;
        $correctAnswersCount = 0;
        $userAnswers = $request->input('questions', []);

        foreach ($quiz->questions as $question) {
            if (isset($userAnswers[$question->id]) && $userAnswers[$question->id] == $question->correct_answer) {
                $score += $question->points ?? 1;
                $correctAnswersCount++;
            }
        }

        $percentage = ($attempt->total_questions > 0) ? (($correctAnswersCount / $attempt->total_questions) * 100) : 0;
        $isPassed = $percentage >= ($quiz->pass_score ?? 50);

        // Check for perfect score to award badges
        if ($percentage === 100.0) {
            $this->badgeService->checkAndAwardBadges(Auth::user(), 'quiz_passed_perfectly');
        }

        $attempt->update([
            'score' => $score,
            'correct_answers' => $correctAnswersCount,
            'percentage' => $percentage,
            'is_passed' => $isPassed,
            'answers' => $userAnswers,
            'completed_at' => now(),
            'time_spent' => now()->diffInMinutes($attempt->started_at),
        ]);

        // Send notification to the user
        $message = $isPassed
            ? "Félicitations ! Vous avez réussi le quiz '{$quiz->title}' avec un score de {$score} points ({$percentage}%)."
            : "Vous avez terminé le quiz '{$quiz->title}' avec un score de {$score} points ({$percentage}%).";

        $this->notificationService->sendNotification(
            Auth::user(),
            'Résultat du Quiz',
            $message,
            route('quiz.result', $attempt),
            $isPassed ? 'success' : 'info'
        );

        // Notify the school if this was an assigned book
        $user = Auth::user();
        if ($user->role === 'student' && $user->school_id) {
            $assignment = \App\Models\BookAssignment::where('book_id', $quiz->book_id)
                ->whereIn('class_id', $user->classes->pluck('id'))
                ->first();

            if ($assignment && $assignment->class && $assignment->class->school) {
                $schoolAdmin = $assignment->class->school->user; // Assuming a 'user' relationship on the School model that points to the school admin
                if ($schoolAdmin) {
                    $this->notificationService->sendNotification(
                        $schoolAdmin,
                        'Quiz terminé par un étudiant',
                        "L'étudiant {$user->name} a terminé le quiz pour le livre '{$quiz->book->title}' avec un score de {$score} points.",
                        route('quiz.result', $attempt), // Maybe a school-specific result view in the future
                        'info'
                    );
                }
            }
        }

        return redirect()->route('quiz.result', $attempt);
    }

    /**
     * Display the results of a quiz attempt.
     */
    public function result(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $attempt->load('quiz.book');

        return view('quiz.result', compact('attempt'));
    }
}
