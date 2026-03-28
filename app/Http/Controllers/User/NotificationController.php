<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of all notifications.
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark all notifications as read and redirect back.
     */
    public function markAllRead()
    {
        Auth::user()->notifications()->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', __('All notifications marked as read.'));
    }
}
