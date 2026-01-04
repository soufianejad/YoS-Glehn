<?php

namespace App\Services;

use App\Mail\GenericNotificationMail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Create a notification and send an email.
     */
    public function sendNotification(
        User $user,
        string $title,
        string $message,
        ?string $link = null,
        string $type = 'info',
        bool $sendEmail = true
    ): void {
        // 1. Create notification in the database (on-site notification)
        if ($user->canReceiveNotification($type, 'site')) {
            $user->notifications()->create([
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'type' => $type,
            ]);
        }

        // 2. Send email notification
        if ($sendEmail && $user->email && $user->canReceiveNotification($type, 'email')) {
            Mail::to($user->email)->send(new GenericNotificationMail($title, $message, $link));
        }
    }
}
