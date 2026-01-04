<?php

namespace App\Http\Controllers\Reader;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ReaderController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Continue Reading section
        $continueReading = $user->readingProgress()
            ->with('book')
            ->orderBy('last_read_at', 'desc')
            ->take(3)
            ->get();

        // Recommendations section
        $recommendations = collect();
        $lastReadBook = $continueReading->first()->book ?? null;
        if ($lastReadBook && $lastReadBook->category_id) {
            $recommendations = \App\Models\Book::where('category_id', $lastReadBook->category_id)
                ->where('id', '!=', $lastReadBook->id) // Exclude the book itself
                ->where('status', 'published')
                ->inRandomOrder()
                ->take(4)
                ->get();
        } else {
            // Fallback: get 4 random popular books if no reading history
            $recommendations = \App\Models\Book::where('status', 'published')->orderBy('views', 'desc')->take(4)->get();
        }

        // Reader Statistics
        $completedBooks = $user->getCompletedBooksCount();
        $totalReadingTime = round($user->getReadingMinutes() / 60); // in hours
        $badgesCount = $user->badges()->count();

        // Latest Badge section
        $latestBadge = $user->badges()->orderBy('pivot_earned_at', 'desc')->first();

        return view('reader.dashboard', compact(
            'user', 
            'continueReading', 
            'recommendations', 
            'latestBadge',
            'completedBooks',
            'totalReadingTime',
            'badgesCount'
        ));
    }

    public function favorites()
    {
        $user = auth()->user();
        $favorites = $user->favorites()->withCount('reviews')->with([
            'quizzes',
            'readingProgress' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            },
        ])->paginate(10);

        return view('reader.favorites', compact('favorites'));
    }

    public function library(Request $request)
    {
        $user = auth()->user();
        $query = $user->purchases()->with([
            'book.author',
            'book.quizzes',
            'book.readingProgress' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            },
        ]);

        // Filter by access type
        if ($request->filled('access_type')) {
            if ($request->access_type === 'purchased') {
                $query->whereIn('purchase_type', ['pdf', 'pdf_download', 'audio']);
            } elseif ($request->access_type === 'subscription') {
                // This logic assumes that subscription access is not represented by a 'purchase' record.
                // A more complex query joining with subscriptions might be needed if that's the case.
                // For now, this filter might not work as expected for subscription books.
            }
        }

        // Filter by reading status
        if ($request->filled('reading_status')) {
            $status = $request->reading_status;
            $query->whereHas('book.readingProgress', function ($q) use ($user, $status) {
                $q->where('user_id', $user->id);
                if ($status === 'not_started') {
                    $q->where('progress_percentage', '=', 0);
                } elseif ($status === 'in_progress') {
                    $q->where('progress_percentage', '>', 0)->where('progress_percentage', '<', 100);
                } elseif ($status === 'finished') {
                    $q->where('progress_percentage', '>=', 100);
                }
            });
        }

        // Search by title
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('book', function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%");
            });
        }

        $purchases = $query->latest()->paginate(12)->withQueryString();

        return view('reader.library', compact('purchases'));
    }

    public function profile()
    {
        $user = auth()->user();

        return view('reader.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $userData = $request->only('first_name', 'last_name', 'email');

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $userData['avatar'] = $path;
        }

        $user->update($userData);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
                if (! Hash::check($value, $user->password)) {
                    $fail('The provided password does not match your current password.');
                }
            }],
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function updateNotificationPreferences(Request $request)
    {
        $user = auth()->user();

        // Define all possible notification types and channels
        $notificationTypes = [
            'new_message',
            'quiz_result',
            'book_purchase',
            'new_subscription',
            'book_assignment',
            'subscription_reminder',
            'new_review',
            'book_approved',
            'first_sale',
            'payout_processed',
        ];
        $channels = ['email', 'site'];

        $preferences = [];
        foreach ($notificationTypes as $type) {
            foreach ($channels as $channel) {
                $key = "{$type}_{$channel}";
                $preferences[$type][$channel] = $request->boolean($key);
            }
        }

        $user->update(['notification_preferences' => $preferences]);

        return back()->with('success', 'Notification preferences updated successfully.');
    }

    public function subscription()
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription()->with('subscriptionPlan')->first();
        $subscriptionPlans = \App\Models\SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        $subscriptionHistory = $user->subscriptions()->with('subscriptionPlan')->latest()->get();

        return view('reader.subscription', compact('subscription', 'subscriptionPlans', 'subscriptionHistory'));
    }

    public function quizzes()
    {
        $attempts = auth()->user()->quizAttempts()
            ->with('quiz.book') // Eager load relationships
            ->latest()
            ->get();

        // Group the attempts by the book title
        $groupedAttempts = $attempts->groupBy('quiz.book.title');

        return view('reader.quizzes', compact('groupedAttempts'));
    }

    public function reviews()
    {
        $user = auth()->user();
        $reviews = $user->reviews()->with('book')->latest()->paginate(10);
        return view('reader.reviews', compact('reviews'));
    }

    public function bookmarks()
    {
        $user = auth()->user();
        $bookmarks = $user->bookmarks()->with('book')->latest()->paginate(10);
        return view('reader.bookmarks', compact('bookmarks'));
    }

    public function cancelSubscription(\App\Models\Subscription $subscription)
    {
        $user = auth()->user();

        if ($user->id !== $subscription->user_id) {
            return back()->with('error', 'Unauthorized action.');
        }

        if (! $subscription || ! $subscription->isActive()) {
            return back()->with('error', 'No active subscription to cancel.');
        }

        $subscription->update([
            'status' => 'cancelled',
            'auto_renew' => false,
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Subscription cancelled successfully.');
    }

    public function renewSubscription(Request $request)
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription()->first(); // Get the active subscription

        if (! $subscription) {
            return back()->with('error', 'You do not have an active subscription to renew.');
        }

        // In a real application, this would integrate with a payment gateway.
        // For now, we'll simulate a successful payment and extend the subscription.

        // Create a dummy payment record
        \App\Models\Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'transaction_id' => 'TRX-'.uniqid(),
            'payment_type' => 'subscription_renewal',
            'amount' => $subscription->subscriptionPlan->price,
            'currency' => 'XOF',
            'payment_method' => 'simulated',
            'payment_provider' => 'simulated',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        // Extend the subscription end date
        $subscription->update([
            'end_date' => $subscription->end_date->addDays($subscription->subscriptionPlan->duration_days),
            'status' => 'active',
            'cancelled_at' => null, // Clear cancelled status if it was cancelled
        ]);

        return back()->with('success', 'Subscription renewed successfully!');
    }

    public function payments()
    {
        $user = auth()->user();
        $payments = $user->payments()->latest()->paginate(10);

        return view('reader.payments', compact('payments'));
    }
}
