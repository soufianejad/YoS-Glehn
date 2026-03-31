<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\AiQuizService;
use Illuminate\Http\Request;

class AiQuizController extends Controller
{
    /**
     * Generate an AI quiz for a specific user.
     */
    public function generate(Request $request, Book $book, AiQuizService $aiQuizService)
    {
        if (!$book->is_ai_quiz_enabled) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('Le générateur de quiz IA n\'est pas activé pour ce livre.')], 403);
            }
            return back()->with('error', __('Le générateur de quiz IA n\'est pas activé pour ce livre.'));
        }

        $userId = auth()->id();

        $quiz = $aiQuizService->generateQuiz($book, $userId);

        if ($quiz) {
            if ($request->expectsJson()) {
                $user = auth()->user();
                $quizUrl = null;

                if ($user) {
                    if ($user->isStudent() && \Illuminate\Support\Facades\Route::has('student.quiz.show')) {
                        $quizUrl = route('student.quiz.show', $quiz);
                    } else if (\Illuminate\Support\Facades\Route::has('quiz.show')) {
                        // The public quiz show route expects the book slug
                        $quizUrl = route('quiz.show', $book->slug);
                    }
                }

                $message = __('Votre quiz IA personnalisé a été généré avec succès !');
                if ($quizUrl) {
                    $message .= ' <a href="' . $quizUrl . '" class="btn btn-sm btn-outline-success ms-2">' . __('Prendre le quiz') . '</a>';
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'quiz_url' => $quizUrl
                ]);
            }

            // Non-JSON request
            $user = auth()->user();
            $quizUrl = null;
            if ($user) {
                if ($user->isStudent() && \Illuminate\Support\Facades\Route::has('student.quiz.show')) {
                    $quizUrl = route('student.quiz.show', $quiz);
                } else if (\Illuminate\Support\Facades\Route::has('quiz.show')) {
                    $quizUrl = route('quiz.show', $book->slug);
                }
            }

            $successMsg = __('Votre quiz IA personnalisé a été généré avec succès !');
            if ($quizUrl) {
                $successMsg .= ' <a href="' . $quizUrl . '" class="btn btn-sm btn-outline-success ms-2">' . __('Prendre le quiz') . '</a>';
            }

            return back()->with('success', $successMsg);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => __('Erreur lors de la génération de votre quiz. Veuillez réessayer plus tard.')], 500);
        }
        return back()->with('error', __('Erreur lors de la génération de votre quiz. Veuillez réessayer plus tard.'));
    }
}
