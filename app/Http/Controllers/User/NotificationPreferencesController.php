<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationPreferencesController extends Controller
{
    // Define the notification types that users can manage.
    // In a real app, this might come from a config file or be discovered dynamically.
    protected $notificationTypes = [
        'new_message' => __('New Messages'),
        'quiz_result' => __('Quiz Results'),
        'subscription_update' => __('Subscription Updates'),
        'badge_unlocked' => __('New Badges Unlocked'),
        'book_assignment' => __('New Book Assignments'), // For students
    ];

    /**
     * Show the form for editing the user's notification preferences.
     */
    public function edit()
    {
        $user = Auth::user();
        $preferences = $user->notification_preferences ?? [];

        return view('profile.notifications', [
            'user' => $user,
            'notificationTypes' => $this->notificationTypes,
            'preferences' => $preferences,
        ]);
    }

    /**
     * Update the user's notification preferences.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $newPreferences = [];

        foreach ($this->notificationTypes as $type => $label) {
            $newPreferences[$type] = [
                'site' => $request->has("prefs.{$type}.site"),
                'email' => $request->has("prefs.{$type}.email"),
            ];
        }

        $user->update(['notification_preferences' => $newPreferences]);

        return back()->with('success', __('Notification preferences updated successfully.'));
    }
}
