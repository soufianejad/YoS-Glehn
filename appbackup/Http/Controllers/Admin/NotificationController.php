<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of all notifications.
     */
    public function index(Request $request)
    {
        $notifications = Notification::with('user')->latest()->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }
}
