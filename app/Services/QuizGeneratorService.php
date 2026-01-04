<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Quiz Generator Service
 * Génère automatiquement des quiz basés sur le contenu des livres
 * Utilise l'API Claude (ou autre IA) pour créer les questions
 */
class QuizGeneratorService
{
    protected $apiKey;

    protected $apiUrl = 'https://api.anthropic.com/v1/messages';

    protected $model = 'claude-sonnet-4-20250514';

    public function __construct()
    {
        // Récupérer la clé API depuis le fichier .env
        $this->apiKey = config('services.anthropic.api_key');
    }

    /**
     * Générer un quiz complet pour un livre
     *
     * @param  int  $questionsCount  Nombre de questions à générer
     */
    public function generateQuizForBook(Book $book, int $questionsCount = 10): ?Quiz
    {
        try {
            // Extraire le contenu du livre (premières pages ou résumé)
            $bookContent = $this->extractBookContent($book);

            if (empty($bookContent)) {
                throw new \Exception('Impossible d\'extraire le contenu du livre');
            }

            // Générer les questions via l'IA
            $questions = $this->generateQuestionsWithAI($bookContent, $book->title, $questionsCount);

            if (empty($questions)) {
                throw new \Exception('Aucune question générée');
            }

            // Créer le quiz
            $quiz = Quiz::create([
                'book_id' => $book->id,
                'title' => 'Quiz - '.$book->title,
                'description' => 'Quiz automatique basé sur le contenu du livre',
                'questions_count' => count($questions),
                'pass_score' => 60,
                'time_limit' => 30, // 30 minutes
                'is_active' => true,
                'show_correct_answers' => true,
                'randomize_questions' => true,
            ]);

            // Créer les questions
            foreach ($questions as $index => $questionData) {
                Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionData['question'],
                    'question_type' => $questionData['type'] ?? 'multiple_choice',
                    'options' => json_encode($questionData['options'] ?? []),
                    'correct_answer' => $questionData['correct_answer'],
                    'explanation' => $questionData['explanation'] ?? null,
                    'points' => 1,
                    'order' => $index + 1,
                ]);
            }

            // Marquer le livre comme ayant un quiz
            $book->update(['has_quiz' => true]);

            return $quiz;

        } catch (\Exception $e) {
            Log::error('Erreur génération quiz: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Extraire le contenu d'un livre pour l'analyse
     */
    protected function extractBookContent(Book $book): string
    {
        // Si le livre a un fichier PDF
        if ($book->pdf_file && file_exists(storage_path('app/public/'.$book->pdf_file))) {
            return $this->extractPdfContent($book->pdf_file);
        }

        // Sinon, utiliser la description comme base
        return $book->description;
    }

    /**
     * Extraire le texte d'un fichier PDF (premières pages)
     *
     * @param  int  $maxPages  Nombre maximum de pages à extraire
     */
    protected function extractPdfContent(string $pdfPath, int $maxPages = 10): string
    {
        try {
            $fullPath = storage_path('app/public/'.$pdfPath);

            // Utiliser Smalot\PdfParser ou une bibliothèque similaire
            // Ici, exemple simplifié
            // composer require smalot/pdfparser

            $parser = new \Smalot\PdfParser\Parser;
            $pdf = $parser->parseFile($fullPath);

            $text = '';
            $pages = $pdf->getPages();
            $pageCount = min(count($pages), $maxPages);

            for ($i = 0; $i < $pageCount; $i++) {
                $text .= $pages[$i]->getText();
            }

            // Limiter à 15000 caractères pour éviter de dépasser les limites de l'API
            return substr($text, 0, 15000);

        } catch (\Exception $e) {
            Log::error('Erreur extraction PDF: '.$e->getMessage());

            return '';
        }
    }

    /**
     * Générer des questions en utilisant l'API Claude
     *
     * @param  string  $content  Contenu du livre
     * @param  string  $bookTitle  Titre du livre
     * @param  int  $count  Nombre de questions
     */
    protected function generateQuestionsWithAI(string $content, string $bookTitle, int $count = 10): array
    {
        try {
            $prompt = $this->buildPrompt($content, $bookTitle, $count);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'max_tokens' => 4000,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

            if (! $response->successful()) {
                throw new \Exception('API request failed: '.$response->status());
            }

            $data = $response->json();
            $responseText = $data['content'][0]['text'] ?? '';

            // Nettoyer la réponse et parser le JSON
            $responseText = $this->cleanJsonResponse($responseText);
            $questions = json_decode($responseText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from AI');
            }

            return $questions['questions'] ?? [];

        } catch (\Exception $e) {
            Log::error('Erreur API IA: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Construire le prompt pour l'IA
     */
    protected function buildPrompt(string $content, string $bookTitle, int $count): string
    {
        return <<<PROMPT
Tu es un expert en création de quiz éducatifs. Basé sur le contenu suivant du livre "{$bookTitle}", génère {$count} questions de quiz pertinentes et variées.

CONTENU DU LIVRE:
{$content}

INSTRUCTIONS CRITIQUES:
1. Crée exactement {$count} questions basées sur le contenu du livre
2. Les questions doivent être claires, précises et en français
3. Chaque question doit avoir 4 options de réponse (A, B, C, D)
4. Une seule réponse correcte par question
5. Ajoute une explication courte pour chaque réponse correcte
6. Varie les types de questions (compréhension, analyse, détails spécifiques)

FORMAT DE RÉPONSE - TRÈS IMPORTANT:
Réponds UNIQUEMENT avec un objet JSON valide dans ce format exact, SANS AUCUN TEXTE AVANT OU APRÈS:

{
  "questions": [
    {
      "question": "Texte de la question ici",
      "type": "multiple_choice",
      "options": ["Option A", "Option B", "Option C", "Option D"],
      "correct_answer": "0",
      "explanation": "Explication de la réponse correcte"
    }
  ]
}

NOTES:
- "correct_answer" doit être l'index (0, 1, 2, ou 3) correspondant à la bonne réponse
- N'inclus PAS de backticks, de ```json, ou tout autre formatage markdown
- Réponds UNIQUEMENT avec le JSON brut
PROMPT;
    }

    /**
     * Nettoyer la réponse JSON de l'IA
     */
    protected function cleanJsonResponse(string $response): string
    {
        // Supprimer les backticks markdown
        $response = preg_replace('/```json\s?/i', '', $response);
        $response = preg_replace('/```\s?/i', '', $response);

        // Supprimer les espaces en début et fin
        $response = trim($response);

        return $response;
    }

    /**
     * Régénérer un quiz existant
     */
    public function regenerateQuiz(Quiz $quiz): bool
    {
        try {
            $book = $quiz->book;

            // Supprimer les anciennes questions
            $quiz->questions()->delete();

            // Extraire le contenu et générer de nouvelles questions
            $bookContent = $this->extractBookContent($book);
            $questions = $this->generateQuestionsWithAI($bookContent, $book->title, $quiz->questions_count);

            if (empty($questions)) {
                throw new \Exception('Aucune question générée');
            }

            // Créer les nouvelles questions
            foreach ($questions as $index => $questionData) {
                Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionData['question'],
                    'question_type' => $questionData['type'] ?? 'multiple_choice',
                    'options' => json_encode($questionData['options'] ?? []),
                    'correct_answer' => $questionData['correct_answer'],
                    'explanation' => $questionData['explanation'] ?? null,
                    'points' => 1,
                    'order' => $index + 1,
                ]);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Erreur régénération quiz: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Valider les questions générées
     */
    protected function validateQuestions(array $questions): bool
    {
        if (empty($questions)) {
            return false;
        }

        foreach ($questions as $question) {
            // Vérifier que tous les champs requis sont présents
            if (empty($question['question']) ||
                empty($question['options']) ||
                ! isset($question['correct_answer'])) {
                return false;
            }

            // Vérifier qu'il y a au moins 2 options
            if (count($question['options']) < 2) {
                return false;
            }

            // Vérifier que la réponse correcte est valide
            $correctIndex = (int) $question['correct_answer'];
            if ($correctIndex < 0 || $correctIndex >= count($question['options'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Générer des questions manuellement (sans IA)
     * Utilisé comme fallback si l'API échoue
     */
    protected function generateDefaultQuestions(Book $book): array
    {
        return [
            [
                'question' => 'Quel est le titre de ce livre ?',
                'type' => 'multiple_choice',
                'options' => [
                    $book->title,
                    'Un autre titre',
                    'Titre différent',
                    'Autre option',
                ],
                'correct_answer' => '0',
                'explanation' => "Le titre du livre est '{$book->title}'",
            ],
            [
                'question' => 'Dans quelle catégorie se trouve ce livre ?',
                'type' => 'multiple_choice',
                'options' => [
                    $book->category->name,
                    'Catégorie 1',
                    'Catégorie 2',
                    'Catégorie 3',
                ],
                'correct_answer' => '0',
                'explanation' => "Ce livre appartient à la catégorie '{$book->category->name}'",
            ],
        ];
    }

    /**
     * Obtenir des statistiques sur les quiz générés
     */
    public function getQuizStatistics(): array
    {
        return [
            'total_quizzes' => Quiz::count(),
            'active_quizzes' => Quiz::where('is_active', true)->count(),
            'total_questions' => Question::count(),
            'total_attempts' => \App\Models\QuizAttempt::count(),
            'average_score' => \App\Models\QuizAttempt::avg('percentage'),
        ];
    }
}
