<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuizController extends Controller
{
    protected BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    public function index(Request $request)
    {
        $student = auth()->user();
        $search = $request->input('search');

        $quizzes = Quiz::with('book')->where('is_active', true)
            ->whereHas('book', function ($query) {
                $query->where('space', 'educational');
            });

        if ($search) {
            $quizzes->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $quizzes = $quizzes->paginate(10);

        return view('student.quizzes.index', compact('quizzes', 'search'));
    }

    public function show(Quiz $quiz)
    {
        if (! $quiz->is_active) {
            abort(403, 'This quiz is not active.');
        }
        $quiz->load('book');

        return view('student.quizzes.show', compact('quiz'));
    }

    public function start(Quiz $quiz)
    {
        if (! $quiz->is_active) {
            abort(403, 'This quiz is not active.');
        }

        $user = auth()->user();

        // Create a new quiz attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'total_questions' => $quiz->questions_count,
            'started_at' => now(),
            'answers' => [], // Initialize with empty answers
        ]);

        $questions = $quiz->questions;
        if ($quiz->randomize_questions) {
            $questions = $questions->shuffle();
        }

        return view('student.quizzes.take', compact('quiz', 'attempt', 'questions'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate([
            'attempt_id' => [
                'required',
                Rule::exists('quiz_attempts', 'id')->where('user_id', auth()->id())->where('quiz_id', $quiz->id),
            ],
            'answers' => 'present|array',
            'answers.*' => 'required|integer|min:0',
        ]);

        $attempt = QuizAttempt::findOrFail($request->attempt_id);

        if ($attempt->completed_at) {
            return redirect()->route('student.quiz.results', $attempt)->with('error', 'Quiz already submitted.');
        }

        $score = 0;
        $correctAnswersCount = 0;
        $userAnswers = $request->input('answers', []);

        $validQuestionIds = $quiz->questions->pluck('id')->all();

        foreach ($quiz->questions as $question) {
            $questionId = $question->id;
            if (! in_array($questionId, array_keys($userAnswers))) {
                continue; // Ignore if question was not answered
            }

            $correctAnswerIndex = (int) $question->correct_answer;

            if ((int) $userAnswers[$questionId] === $correctAnswerIndex) {
                $score += $question->points;
                $correctAnswersCount++;
            }
        }

        $percentage = ($quiz->questions_count > 0) ? (($correctAnswersCount / $quiz->questions_count) * 100) : 0;
        $isPassed = $percentage >= $quiz->pass_score;

        $attempt->update([
            'score' => $score,
            'correct_answers' => $correctAnswersCount,
            'percentage' => $percentage,
            'is_passed' => $isPassed,
            'answers' => $userAnswers,
            'completed_at' => now(),
            'time_spent' => now()->diffInSeconds($attempt->started_at),
        ]);

        if ($isPassed) {
            $this->badgeService->checkAndAwardBadges(auth()->user());
        }

        return redirect()->route('student.quiz.results', $attempt)->with('success', 'Quiz submitted successfully!');
    }

    public function results(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        $attempt->load('quiz.questions');

        return view('student.quizzes.results', compact('attempt'));
    }

    public function bookQuiz(Request $request, Book $book)
    {
        $search = $request->input('search');

        $quizzes = $book->quizzes()->where('is_active', true);

        if ($search) {
            $quizzes->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $quizzes = $quizzes->paginate(10);

        return view('student.quizzes.book-quizzes', compact('book', 'quizzes', 'search'));
    }
}
