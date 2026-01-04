<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = Badge::latest()->paginate(15);

        return view('admin.badges.index', compact('badges'));
    }

    public function create()
    {
        return view('admin.badges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:badges,name',
            'description' => 'required|string',
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'points' => 'required|integer|min:0',
            'books_required' => 'nullable|integer|min:0',
            'minutes_required' => 'nullable|integer|min:0',
            'quizzes_required' => 'nullable|integer|min:0',
        ]);

        $badgeData = $request->only(['name', 'description', 'points', 'books_required', 'minutes_required', 'quizzes_required']);
        $badgeData['slug'] = Str::slug($request->name);

        if ($request->hasFile('icon')) {
            $badgeData['icon'] = $request->file('icon')->store('badges/icons', 'public');
        }

        Badge::create($badgeData);

        return redirect()->route('admin.badges.index')->with('success', 'Badge created successfully.');
    }

    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', compact('badge'));
    }

    public function update(Request $request, Badge $badge)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:badges,name,'.$badge->id,
            'description' => 'required|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'points' => 'required|integer|min:0',
            'books_required' => 'nullable|integer|min:0',
            'minutes_required' => 'nullable|integer|min:0',
            'quizzes_required' => 'nullable|integer|min:0',
        ]);

        $badgeData = $request->only(['name', 'description', 'points', 'books_required', 'minutes_required', 'quizzes_required']);
        $badgeData['slug'] = Str::slug($request->name);

        if ($request->hasFile('icon')) {
            if ($badge->icon) {
                Storage::disk('public')->delete($badge->icon);
            }
            $badgeData['icon'] = $request->file('icon')->store('badges/icons', 'public');
        }

        $badge->update($badgeData);

        return redirect()->route('admin.badges.index')->with('success', 'Badge updated successfully.');
    }

    public function destroy(Badge $badge)
    {
        if ($badge->icon) {
            Storage::disk('public')->delete($badge->icon);
        }

        $badge->delete();

        return redirect()->route('admin.badges.index')->with('success', 'Badge deleted successfully.');
    }
}
