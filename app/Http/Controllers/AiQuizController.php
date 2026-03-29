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
            return back()->with('error', __('Le générateur de quiz IA n\'est pas activé pour ce livre.'));
        }

        $userId = auth()->id();

        $quiz = $aiQuizService->generateQuiz($book, $userId);

        if ($quiz) {
            // Redirect logic depending on whether user is student or normal reader
            if (auth()->user()->isStudent()) {
                // If they are a student, we assume there's a specific route for them to view the quiz,
                // but let's just go back with a success message for now since we just generated it.
                return back()->with('success', __('Votre quiz IA personnalisé a été généré avec succès !'));
            } else {
                return back()->with('success', __('Votre quiz IA personnalisé a été généré avec succès !'));
            }
        }

        return back()->with('error', __('Erreur lors de la génération de votre quiz. Veuillez réessayer plus tard.'));
    }
}
