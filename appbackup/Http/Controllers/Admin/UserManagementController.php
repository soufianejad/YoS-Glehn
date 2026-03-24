<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdultAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $usersQuery = User::query();

        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $usersQuery->where('role', $role);
        }

        $users = $usersQuery->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,author,school,student,reader,adult_reader',
            'phone' => 'nullable|string|max:255',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'school_id' => $request->school_id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        // Eager load some basic relationships
        $user->load('school');

        // Key Stats
        $stats = [
            'total_spent' => $user->payments()->where('status', 'completed')->sum('amount'),
            'purchases_count' => $user->purchases()->count(),
            'quizzes_taken' => $user->quizAttempts()->count(),
            'avg_quiz_score' => $user->quizAttempts()->avg('percentage'),
        ];

        // Paginated data for tabs, with custom page names to avoid conflicts
        $payments = $user->payments()->with('book', 'subscription.subscriptionPlan')->latest()->paginate(10, ['*'], 'payments_page');
        $quizAttempts = $user->quizAttempts()->with('quiz.book')->latest()->paginate(10, ['*'], 'quizzes_page');
        $readingProgress = $user->readingProgress()->with('book')->where('progress_percentage', '>', 0)->latest('last_read_at')->paginate(10, ['*'], 'reading_page');

        return view('admin.users.show', compact('user', 'stats', 'payments', 'quizAttempts', 'readingProgress'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|string|in:admin,author,school,student,reader,adult_reader',
            'phone' => 'nullable|string|max:255',
            'school_id' => 'nullable|exists:schools,id',
            'is_active' => 'boolean',
        ]);

        $user->update($request->only('first_name', 'last_name', 'email', 'role', 'phone', 'school_id', 'is_active'));

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function activate(User $user)
    {
        $user->update(['is_active' => true]);

        return back()->with('success', 'User activated successfully.');
    }

    public function deactivate(User $user)
    {
        $user->update(['is_active' => false]);

        return back()->with('success', 'User deactivated successfully.');
    }

    public function changeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|in:user,admin,author,school,student',
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', 'User role updated successfully.');
    }

    public function adultInvitations()
    {
        $invitations = AdultAccess::with('creator', 'user')->paginate(10);

        return view('admin.users.adult-invitations', compact('invitations'));
    }

    public function generateAdultInvitation(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        AdultAccess::create([
            'access_token' => Str::random(32),
            'email' => $request->email,
            'created_by' => auth()->id(),
            'max_uses' => $request->max_uses,
            'expires_at' => $request->expires_at,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Invitation generated successfully!');
    }

    public function revokeInvitation(string $token)
    {
        $invitation = AdultAccess::where('access_token', $token)->firstOrFail();
        $invitation->update(['status' => 'revoked']);

        return back()->with('success', 'Invitation revoked successfully!');
    }

    public function impersonate(User $user)
    {
        if (session()->has('impersonating')) {
            // Admin is already impersonating, so stop it first
            Auth::loginUsingId(session('impersonating'));
            session()->forget('impersonating');
        }

        session()->put('impersonating', auth()->id());
        Auth::login($user);

        return redirect()->route('dashboard'); // Or any other route
    }

    public function stopImpersonating()
    {
        if (session()->has('impersonating')) {
            Auth::loginUsingId(session('impersonating'));
            session()->forget('impersonating');
        }

        return redirect()->route('admin.users.index');
    }
}
