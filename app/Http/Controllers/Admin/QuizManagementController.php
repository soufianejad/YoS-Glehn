<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizManagementController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with('book')->paginate(10);

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create(Book $book)
    {
        // Reuse the teacher's creation form
        return view('teacher.quizzes.form', compact('book'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pass_score' => 'required|integer|min:0|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'questions' => 'sometimes|array',
            'questions.*.question_text' => 'required|string|max:255',
            'questions.*.options' => 'required|array|min:4|max:4',
            'questions.*.options.*' => 'required|string|max:255',
            'questions.*.correct_answer' => 'required|integer|min:0|max:3',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.order' => 'required|integer',
        ]);

        $quiz = DB::transaction(function () use ($request) {
            $quiz = Quiz::create([
                'book_id' => $request->book_id,
                'title' => $request->title,
                'description' => $request->description,
                'pass_score' => $request->pass_score,
                'time_limit' => $request->time_limit,
                'is_active' => $request->boolean('is_active', false),
                'questions_count' => 0, // Will be updated after filtering
            ]);

            $questionCount = 0;
            if ($request->has('questions')) {
                foreach ($request->questions as $questionData) {
                    if (empty($questionData['question_text'])) {
                        continue;
                    }
                    
                    $quiz->questions()->create([
                        'question_text' => $questionData['question_text'],
                        'question_type' => 'multiple_choice',
                        'options' => $questionData['options'],
                        'correct_answer' => $questionData['correct_answer'],
                        'points' => $questionData['points'],
                        'explanation' => $questionData['explanation'],
                        'order' => $questionData['order'],
                    ]);
                    $questionCount++;
                }
            }

            $quiz->update(['questions_count' => $questionCount]);
            return $quiz;
        });

        return redirect()->route('admin.quiz.index')->with('success', __('Quiz créé avec succès !'));
    }

    public function generate(Request $request, Book $book, QuizGeneratorService $quizGeneratorService)
    {
        $request->validate([
            'number_of_questions' => 'required|integer|min:1|max:20',
        ]);

        $quiz = $quizGeneratorService->generateQuizForBook(
            $book,
            $request->number_of_questions,
            $request->difficulty
        );

        if ($request->expectsJson()) {
            if ($quiz) {
                return response()->json([
                    'success' => true,
                    'message' => __('Quiz generated successfully!'),
                    'redirect_url' => route('admin.quiz.show', $quiz)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to generate quiz.')
                ], 400);
            }
        }

        if ($quiz) {
            return redirect()->route('admin.quiz.show', $quiz)->with('success', __('Quiz generated successfully!'));
        } else {
            return back()->with('error', __('Failed to generate quiz.'));
        }
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('book', 'questions');

        return view('admin.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load('questions');
        $book = $quiz->book;

        return view('teacher.quizzes.form', compact('quiz', 'book'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pass_score' => 'required|integer|min:0|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'questions' => 'sometimes|array',
            'questions.*.id' => 'nullable|integer|exists:questions,id',
            'questions.*.question_text' => 'required|string|max:255',
            'questions.*.options' => 'required|array|min:4|max:4',
            'questions.*.options.*' => 'required|string|max:255',
            'questions.*.correct_answer' => 'required|integer|min:0|max:3',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.order' => 'required|integer',
        ]);

        DB::transaction(function () use ($validated, $quiz) {
            $quiz->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'pass_score' => $validated['pass_score'],
                'time_limit' => $validated['time_limit'],
                'is_active' => $validated['is_active'] ?? false,
            ]);

            $incomingQuestionIds = [];
            $questionCount = 0;

            if (isset($validated['questions'])) {
                foreach ($validated['questions'] as $questionData) {
                    if (empty($questionData['question_text'])) {
                        continue;
                    }

                    $questionPayload = [
                        'question_text' => $questionData['question_text'],
                        'question_type' => 'multiple_choice',
                        'options' => $questionData['options'],
                        'correct_answer' => $questionData['correct_answer'],
                        'points' => $questionData['points'],
                        'explanation' => $questionData['explanation'],
                        'order' => $questionData['order'],
                    ];

                    if (isset($questionData['id'])) {
                        $question = Question::find($questionData['id']);
                        if ($question) {
                            $question->update($questionPayload);
                            $incomingQuestionIds[] = $question->id;
                        }
                    } else {
                        $newQuestion = $quiz->questions()->create($questionPayload);
                        $incomingQuestionIds[] = $newQuestion->id;
                    }
                    $questionCount++;
                }
            }

            $quiz->questions()->whereNotIn('id', $incomingQuestionIds)->delete();
            $quiz->update(['questions_count' => $questionCount]);
        });

        return redirect()->route('admin.quiz.index')->with('success', __('Quiz mis à jour avec succès !'));
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')->with('success', __('Quiz deleted successfully.'));
    }

    public function regenerate(Request $request, Quiz $quiz, QuizGeneratorService $quizGeneratorService)
    {
        $success = $quizGeneratorService->regenerateQuiz($quiz);

        if ($request->expectsJson()) {
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => __('Quiz questions regenerated successfully!')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to regenerate quiz questions.')
                ], 400);
            }
        }

        if ($success) {
            return back()->with('success', __('Quiz questions regenerated successfully!'));
        } else {
            return back()->with('error', __('Failed to regenerate quiz questions.'));
        }
    }

    public function results(Quiz $quiz)
    {
        $attempts = QuizAttempt::with('user')->where('quiz_id', $quiz->id)->paginate(10);

        return view('admin.quizzes.results', compact('quiz', 'attempts'));
    }
}
