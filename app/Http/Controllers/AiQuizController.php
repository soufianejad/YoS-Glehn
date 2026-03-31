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
                return response()->json([
                    'success' => true,
                    'message' => __('Votre quiz IA personnalisé a été généré avec succès !'),
                    'quiz_url' => auth()->user()->isStudent() ? route('student.quiz.show', $quiz) : null
                ]);
            }
            // Redirect logic depending on whether user is student or normal reader
            if (auth()->user()->isStudent()) {
                // If they are a student, we assume there's a specific route for them to view the quiz,
                // but let's just go back with a success message for now since we just generated it.
                return back()->with('success', __('Votre quiz IA personnalisé a été généré avec succès !'));
            } else {
                return back()->with('success', __('Votre quiz IA personnalisé a été généré avec succès !'));
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => __('Erreur lors de la génération de votre quiz. Veuillez réessayer plus tard.')], 500);
        }
        return back()->with('error', __('Erreur lors de la génération de votre quiz. Veuillez réessayer plus tard.'));
    }
}
