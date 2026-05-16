<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdultAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class UserManagementController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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

        return redirect()->route('admin.users.index')->with('success', __('User created successfully.'));
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
        $payments = $user->payments()->with('book', __('subscription.subscriptionPlan'))->latest()->paginate(10, ['*'], 'payments_page');
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
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only('first_name', 'last_name', 'email', 'role', 'phone', 'school_id', 'is_active');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', __('User updated successfully.'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', __('User deleted successfully.'));
    }

    public function activate(User $user)
    {
        $user->update(['is_active' => true]);

        $this->notificationService->sendNotification(
            $user,
            __('Compte activé'),
            __('Votre compte a été activé avec succès.'),
            null,
            'success'
        );

        return back()->with('success', __('User activated successfully.'));
    }

    public function deactivate(User $user)
    {
        $user->update(['is_active' => false]);

        $this->notificationService->sendNotification(
            $user,
            __('Compte désactivé'),
            __('Votre compte a été désactivé par un administrateur.'),
            null,
            'warning'
        );

        return back()->with('success', __('User deactivated successfully.'));
    }

    public function changeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|in:user,admin,author,school,student',
        ]);

        $user->update(['role' => $request->role]);

        $this->notificationService->sendNotification(
            $user,
            __('Rôle mis à jour'),
            __('Votre rôle a été mis à jour par un administrateur. Nouveau rôle : :role.', ['role' => __($request->role)]),
            null,
            'info'
        );

        return back()->with('success', __('User role updated successfully.'));
    }

    public function adultInvitations()
    {
        $invitations = AdultAccess::with('creator', 'user')->paginate(10);

        return view('admin.users.adult-invitations', compact('invitations'));
    }

    public function editAdultInvitation(string $token)
    {
        $invitation = AdultAccess::where('access_token', $token)->firstOrFail();
        $users = User::where('role', 'adult_reader')->get();

        return view('admin.users.edit-adult-invitation', compact('invitation', 'users'));
    }

    public function updateAdultInvitation(Request $request, string $token)
    {
        $invitation = AdultAccess::where('access_token', $token)->firstOrFail();

        $request->validate([
            'email' => 'nullable|email',
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'nullable|date',
            'status' => 'required|in:pending,used,expired,revoked',
        ]);

        $invitation->update([
            'email' => $request->email,
            'max_uses' => $request->max_uses,
            'expires_at' => $request->expires_at,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.users.adult-invitations')->with('success', __('Invitation updated successfully!'));
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

        return back()->with('success', __('Invitation generated successfully!'));
    }

    public function revokeInvitation(string $token)
    {
        $invitation = AdultAccess::where('access_token', $token)->firstOrFail();
        $invitation->update(['status' => 'revoked']);

        return back()->with('success', __('Invitation revoked successfully!'));
    }

    public function impersonate(User $user)
    {
        if (session()->has('impersonating')) {
            // Admin is already impersonating, so stop it first
            $this->stopImpersonating();
        }

        $adminId = auth()->id();
        session()->put('impersonating', $adminId);
        session()->put('impersonating_hash', hash_hmac('sha256', $adminId, config('app.key')));

        Auth::login($user);

        return redirect()->route('dashboard'); // Or any other route
    }

    public function stopImpersonating()
    {
        if (session()->has('impersonating') && session()->has('impersonating_hash')) {
            $impersonatingId = session('impersonating');
            $hash = session('impersonating_hash');
            $expectedHash = hash_hmac('sha256', $impersonatingId, config('app.key'));

            if (hash_equals($expectedHash, $hash)) {
                $adminUser = User::find($impersonatingId);
                if ($adminUser && $adminUser->role === 'admin') {
                    Auth::login($adminUser);
                }
            }
        }

        session()->forget(['impersonating', 'impersonating_hash']);

        return redirect()->route('admin.users.index');
    }

    public function sendResetLink(User $user)
    {
        if (!$user->email) {
            return back()->with('error', __('L\'utilisateur n\'a pas d\'adresse email.'));
        }

        $code = Str::random(6);
        $expiresAt = now()->addMinutes(10);
        $type = 'email';

        // We store this in session for the user who will click the link,
        // but wait, session is only for the current admin.
        // For admin-initiated resets, we actually need to store it in the database
        // or just rely on the magic link being generated and the session being set when THEY click it.
        // Actually, the magic link method in ForgotPasswordController checks the session.
        // So we SHOULD store it in a way that it can be retrieved by anyone with the link.
        // For now, let's stick to the session-based approach and see if we can improve it.
        // BUT session-based won't work if they open the link in a different browser.

        // Let's reconsider. Maybe use the native Laravel password broker or just keep it simple with session but explain the limitation.
        // OR better: use Cache with a key based on email and code.

        $cacheKey = "password_reset_{$type}_{$user->email}";
        \Illuminate\Support\Facades\Cache::put($cacheKey, [
            'code' => $code,
            'expires_at' => $expiresAt
        ], $expiresAt);

        $resetLink = route('password.reset.magic', [
            'email' => $user->email,
            'code' => $code,
            'type' => 'email'
        ]);

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($code, $resetLink));
            return back()->with('success', __('Lien de réinitialisation envoyé avec succès.'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Admin UserManagementController: Erreur d'envoi email de réinitialisation", [
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', __('Erreur lors de l\'envoi de l\'email.'));
        }
    }
}
