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

        $user = auth()->user();
        if (!$user || !$user->isStudent()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('Seuls les étudiants peuvent générer des quiz IA.')], 403);
            }
            return back()->with('error', __('Seuls les étudiants peuvent générer des quiz IA.'));
        }

        $userId = $user->id;

        $quiz = $aiQuizService->generateQuiz($book, $userId);

        if ($quiz) {
            $quizUrl = route('student.quiz.start', $quiz);
            $message = __('Votre quiz IA personnalisé a été généré avec succès !') . ' <a href="' . $quizUrl . '" class="btn btn-sm btn-outline-success ms-2">' . __('Prendre le quiz') . '</a>';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'quiz_url' => $quizUrl
                ]);
            }

            return back()->with('success', $message);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => __('Erreur lors de la génération de votre quiz. Veuillez réessayer plus tard.')], 500);
        }
        return back()->with('error', __('Erreur lors de la génération de votre quiz. Veuillez réessayer plus tard.'));
    }
}
