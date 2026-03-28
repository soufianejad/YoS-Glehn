<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationApiController extends Controller
{
    /**
     * Get all unread notifications for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $notifications = $user->notifications()->where('is_read', false)->latest()->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $notifications->count(),
        ]);
    }

    /**
     * Mark a specific notification as read.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => __('Unauthorized')], 403);
        }

        $notification->markAsRead();

        return response()->json(['message' => __('Notification marked as read.')]);
    }

    /**
     * Mark all unread notifications for the authenticated user as read.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['message' => __('All notifications marked as read.')]);
    }
}
