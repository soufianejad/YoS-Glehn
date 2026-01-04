<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Book;
use App\Models\BookAssignment;
use App\Models\Favorite;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\ReadingProgress;
use App\Models\Review;
use App\Models\School;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Database\Seeder;

class InteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding interactions...');

        // Fetch existing data
        $readers = User::where('role', 'reader')->get();
        $students = User::where('role', 'student')->get();
        $publicBooks = Book::where('space', 'public')->get();
        $educationalBooks = Book::where('space', 'educational')->get();
        $schools = School::with('classes')->get();
        $individualPlans = SubscriptionPlan::where('type', 'individual')->get();
        $schoolPlans = SubscriptionPlan::where('type', 'school')->get();
        $badges = Badge::all();

        // --- Seed Reviews and Favorites for Public books ---
        if ($readers->isNotEmpty() && $publicBooks->isNotEmpty()) {
            foreach ($publicBooks as $book) {
                // Each book gets 1 to 5 reviews
                if ($readers->count() > 0) {
                    $reviewers = $readers->random(rand(1, min(5, $readers->count())));
                    foreach ($reviewers as $reviewer) {
                        Review::factory()->create(['user_id' => $reviewer->id, 'book_id' => $book->id]);
                    }
                }

                // Each book gets 2 to 10 favorites
                if ($readers->count() > 1) {
                    $favoriters = $readers->random(rand(2, min(10, $readers->count())));
                    foreach ($favoriters as $favoriter) {
                        Favorite::create(['user_id' => $favoriter->id, 'book_id' => $book->id]);
                    }
                }
            }
        }

        // --- Seed School-related interactions ---
        if ($schools->isNotEmpty() && $educationalBooks->isNotEmpty()) {
            foreach ($schools as $school) {
                // Assign 5-10 books to each class
                foreach ($school->classes as $class) {
                    if ($educationalBooks->count() > 0) {
                        $booksToAssign = $educationalBooks->random(rand(5, min(10, $educationalBooks->count())));
                        foreach ($booksToAssign as $book) {
                            BookAssignment::create([
                                'book_id' => $book->id,
                                'class_id' => $class->id,
                                'school_id' => $school->id,
                                'assigned_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }

        // --- Seed Quizzes for some educational books ---
        $assignedBooks = Book::whereHas('assignments')->where('space', 'educational')->get()->take(10);
        foreach ($assignedBooks as $book) {
            $quiz = Quiz::factory()->has(Question::factory()->count(10), 'questions')->create(['book_id' => $book->id]);
            $book->update(['has_quiz' => true]);

            // --- Seed Quiz Attempts ---
            $studentsForQuiz = User::whereIn('id', function ($query) use ($quiz) {
                $query->select('user_id')->from('class_student')->whereIn('class_id', $quiz->book->assignments->pluck('class_id'));
            })->get();

            if ($studentsForQuiz->isNotEmpty()) {
                foreach ($studentsForQuiz->random(min(5, $studentsForQuiz->count())) as $student) {
                    $correctAnswers = rand(3, 10);
                    QuizAttempt::create([
                        'quiz_id' => $quiz->id,
                        'user_id' => $student->id,
                        'total_questions' => 10,
                        'correct_answers' => $correctAnswers,
                        'score' => $correctAnswers,
                        'percentage' => $correctAnswers * 10,
                        'is_passed' => $correctAnswers >= 6,
                        'started_at' => now()->subMinutes(rand(10, 30)),
                        'completed_at' => now(),
                    ]);
                }
            }
        }

        // --- Seed Reading Progress ---
        if ($students->isNotEmpty()) {
            $studentsWithAssignments = $students->filter(function ($student) {
                return $student->classes->flatMap->assignments->isNotEmpty();
            });

            if ($studentsWithAssignments->isNotEmpty()) {
                foreach ($studentsWithAssignments->random(min(15, $studentsWithAssignments->count())) as $student) {
                    $assignedBook = $student->classes->flatMap->assignments->pluck('book')->random();
                    if ($assignedBook) {
                        ReadingProgress::factory()->create(['user_id' => $student->id, 'book_id' => $assignedBook->id]);
                    }
                }
            }
        }

        // --- Seed Subscriptions ---
        if ($readers->isNotEmpty() && $individualPlans->isNotEmpty()) {
            if ($readers->count() > 0) {
                foreach ($readers->random(floor($readers->count() / 2)) as $reader) {
                    $plan = $individualPlans->random();
                    Subscription::create([
                        'user_id' => $reader->id,
                        'subscription_plan_id' => $plan->id,
                        'start_date' => now()->subDays(rand(1, 30)),
                        'end_date' => now()->addDays($plan->duration_days - rand(1, 30)),
                        'status' => 'active',
                    ]);
                }
            }
        }
        if ($schools->isNotEmpty() && $schoolPlans->isNotEmpty()) {
            foreach ($schools as $school) {
                $plan = $schoolPlans->random();
                Subscription::create([
                    'user_id' => $school->user_id,
                    'subscription_plan_id' => $plan->id,
                    'start_date' => now()->subDays(rand(1, 30)),
                    'end_date' => now()->addDays($plan->duration_days - rand(1, 30)),
                    'status' => 'active',
                ]);
            }
        }

        // --- Seed Badges ---
        if ($students->isNotEmpty() && $badges->isNotEmpty()) {
            $firstBookBadge = $badges->where('name', 'Premier Livre Lu')->first();
            $completedBookStudents = User::whereHas('readingProgress', function ($q) {
                $q->where('progress_percentage', '>=', 100);
            })->get();
            foreach ($completedBookStudents as $student) {
                if ($firstBookBadge) {
                    UserBadge::create(['user_id' => $student->id, 'badge_id' => $firstBookBadge->id, 'earned_at' => now()]);
                }
            }
        }

        $this->command->info('Interactions seeded successfully!');
    }
}
