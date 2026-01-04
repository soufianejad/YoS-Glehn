<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

    public function create(Book $book)
    {
        // Authorization: Ensure the teacher can only create quizzes for educational books.
        if ($book->space !== 'educational') {
            abort(403, 'Vous ne pouvez créer des quiz que pour les livres de l\'espace éducatif.');
        }

        return view('teacher.quizzes.form', compact('book'));
    }

    /**
     * Show the form for editing an existing quiz.
     */
    public function edit(Quiz $quiz)
    {
        $this->authorize('update', $quiz); // Assuming a QuizPolicy exists or will be created

        $quiz->load('questions');
        $book = $quiz->book;

        return view('teacher.quizzes.form', compact('quiz', 'book'));
    }

    /**
     * Store a newly created quiz in storage.
     */
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

        $book = Book::findOrFail($request->book_id);
        // Authorization check
        if ($book->space !== 'educational') {
            abort(403);
        }

        $quiz = DB::transaction(function () use ($request, $book) {
            $quiz = Quiz::create([
                'book_id' => $book->id,
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

        return redirect()->route('teacher.dashboard')->with('success', 'Quiz créé avec succès !');
    }

    /**
     * Update the specified quiz in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz);

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
                        // Update existing question
                        $question = Question::find($questionData['id']);
                        if ($question) {
                            $question->update($questionPayload);
                            $incomingQuestionIds[] = $question->id;
                        }
                    } else {
                        // Create new question
                        $newQuestion = $quiz->questions()->create($questionPayload);
                        $incomingQuestionIds[] = $newQuestion->id;
                    }
                    $questionCount++;
                }
            }

            // Delete questions that were removed from the form
            $quiz->questions()->whereNotIn('id', $incomingQuestionIds)->delete();

            // Update the total questions count
            $quiz->update(['questions_count' => $questionCount]);
        });

        return redirect()->route('teacher.dashboard')->with('success', 'Quiz mis à jour avec succès !');
    }
}
