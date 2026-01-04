<?php

namespace App\Http\Controllers\Reader;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get all badges available on the platform
        $allBadges = Badge::where('is_active', true)->get();

        // Get the IDs of badges the user has earned
        $earnedBadgeIds = $user->badges()->pluck('badges.id')->toArray();

        return view('reader.badges', compact('allBadges', 'earnedBadgeIds'));
    }
}
