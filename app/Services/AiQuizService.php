<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiQuizService
{
    /**
     * Generate a quiz for a book synchronously using OpenAI API.
     *
     * @param Book $book
     * @param int|null $userId Null means it's a global quiz.
     * @return Quiz|null Returns the created quiz, or null on failure.
     */
    public function generateQuiz(Book $book, ?int $userId = null): ?Quiz
    {
        $apiKey = config('services.openai.key');

        if (empty($apiKey)) {
            Log::error('OpenAI API Key is missing.');
            return null;
        }

        $prompt = $this->buildPrompt($book);

        try {
            $response = Http::withToken($apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo', // or gpt-4 depending on preference
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert educator who generates high-quality multiple-choice quizzes in French based on book information. You must respond strictly in JSON format.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ],
                    ],
                    'response_format' => ['type' => 'json_object']
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $content = $responseData['choices'][0]['message']['content'] ?? '';

                $quizData = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE && isset($quizData['questions'])) {
                    return $this->saveQuizToDatabase($book, $userId, $quizData);
                } else {
                    Log::error('Invalid JSON received from OpenAI', ['content' => $content]);
                    return null;
                }
            } else {
                Log::error('OpenAI API Error', ['response' => $response->body()]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exception during AI Quiz Generation', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function buildPrompt(Book $book): string
    {
        return "Create a 10-question multiple-choice quiz for the book titled '{$book->title}'.\n"
             . "Here is the book's description to base the questions on:\n"
             . "{$book->description}\n\n"
             . "The response must be a JSON object containing a 'title' (a short title for the quiz), a 'description' (a short description of the quiz), and an array called 'questions'. "
             . "Each item in the 'questions' array must have: 'question_text' (string), 'options' (array of 4 strings), 'correct_answer' (string, must exactly match one of the options), and 'explanation' (string, explaining why it's correct).\n\n"
             . "Make sure the JSON structure exactly matches this format:\n"
             . "{\n"
             . '  "title": "Quiz Title",'."\n"
             . '  "description": "Quiz Description",'."\n"
             . '  "questions": ['."\n"
             . "    {\n"
             . '      "question_text": "...",'."\n"
             . '      "options": ["A", "B", "C", "D"],'."\n"
             . '      "correct_answer": "A",'."\n"
             . '      "explanation": "..."'."\n"
             . "    }\n"
             . "  ]\n"
             . "}\n"
             . "The language of the quiz should be French.";
    }

    private function saveQuizToDatabase(Book $book, ?int $userId, array $quizData): Quiz
    {
        $quiz = Quiz::create([
            'book_id' => $book->id,
            'user_id' => $userId,
            'title' => $quizData['title'] ?? "Quiz : {$book->title}",
            'description' => $quizData['description'] ?? "Quiz généré par l'IA pour {$book->title}",
            'questions_count' => count($quizData['questions'] ?? []),
            'pass_score' => 60,
            'is_active' => true,
        ]);

        if (isset($quizData['questions']) && is_array($quizData['questions'])) {
            foreach ($quizData['questions'] as $index => $q) {
                $correctIndex = array_search($q['correct_answer'], $q['options']);
                if ($correctIndex === false) {
                    $correctIndex = 0; // Fallback in case of AI mismatch
                }

                Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $q['question_text'],
                    'question_type' => 'multiple_choice',
                    'options' => $q['options'],
                    'correct_answer' => (string) $correctIndex,
                    'explanation' => $q['explanation'] ?? null,
                    'order' => $index + 1,
                    'points' => 1,
                ]);
            }
        }

        return $quiz;
    }
}
