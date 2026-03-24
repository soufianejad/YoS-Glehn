<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;

class BadgeService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Check all badge conditions for a user and award them if met.
     */
    public function checkAndAwardBadges(User $user): void
    {
        if (! $user->isReader() && ! $user->isStudent()) {
            return;
        }

        $badges = Badge::where('is_active', true)->get();

        foreach ($badges as $badge) {
            $this->checkAndAwardBadge($user, $badge);
        }
    }

    /**
     * Check the conditions for a single badge and award it if met.
     */
    private function checkAndAwardBadge(User $user, Badge $badge): void
    {
        if ($this->hasBadge($user, $badge)) {
            return;
        }

        $conditionsMet = 0;
        $conditionsRequired = 0;

        if ($badge->books_required > 0) {
            $conditionsRequired++;
            if ($this->checkBooksRequired($user, $badge->books_required)) {
                $conditionsMet++;
            }
        }

        if ($badge->minutes_required > 0) {
            $conditionsRequired++;
            if ($this->checkMinutesRequired($user, $badge->minutes_required)) {
                $conditionsMet++;
            }
        }

        if ($badge->quizzes_required > 0) {
            $conditionsRequired++;
            if ($this->checkQuizzesRequired($user, $badge->quizzes_required)) {
                $conditionsMet++;
            }
        }

        if ($conditionsRequired > 0 && $conditionsMet === $conditionsRequired) {
            $this->awardBadge($user, $badge);
        }
    }

    /**
     * Check if a user has already been awarded a badge.
     */
    private function hasBadge(User $user, Badge $badge): bool
    {
        return $user->badges()->where('badge_id', $badge->id)->exists();
    }

    /**
     * Award a badge to a user.
     */
    private function awardBadge(User $user, Badge $badge): void
    {
        $user->badges()->attach($badge->id, ['earned_at' => now()]);

        // Send a notification for the new badge
        $this->notificationService->sendNotification(
            user: $user,
            title: 'Félicitations ! Vous avez gagné un nouveau badge.',
            message: "Vous avez débloqué le badge : {$badge->name}. {$badge->description}",
            link: route('reader.badges'),
            type: 'info'
        );
    }

    private function checkBooksRequired(User $user, int $requiredCount): bool
    {
        return $user->getCompletedBooksCount() >= $requiredCount;
    }

    private function checkMinutesRequired(User $user, int $requiredMinutes): bool
    {
        // Assuming a method getReadingMinutes() exists on the User model
        return $user->getReadingMinutes() >= $requiredMinutes;
    }

    private function checkQuizzesRequired(User $user, int $requiredCount): bool
    {
        // Assuming a method getPassedQuizzesCount() exists on the User model
        return $user->getPassedQuizzesCount() >= $requiredCount;
    }
}
