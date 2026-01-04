<?php

namespace App\Providers;

use App\Models\AdultAccess;
use App\Models\AuthorPayout;
use App\Models\Book;
use App\Models\Message;
use App\Models\Revenue;
use App\Models\School;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
            Paginator::useBootstrap();

        View::composer('layouts.dashboard', function ($view) {
            if (Auth::check()) {
                $unreadCount = Message::whereIn('conversation_id', Auth::user()->conversations()->pluck('id'))
                    ->where('sender_id', '!=', Auth::id())
                    ->whereNull('read_at')
                    ->count();
                $view->with('unreadMessagesCount', $unreadCount);
            } else {
                $view->with('unreadMessagesCount', 0);
            }
        });

        View::composer('layouts.admin', function ($view) {
            $adultInvitationCount = AdultAccess::where('status', 'pending')->count();
            $pendingBooks = Book::where('status', 'pending')->count();
            $pendingSchools = School::where('status', 'pending')->count();
            $pendingPayoutsCount = AuthorPayout::where('status', 'pending')->count();
            $pendingRevenuesCount = Revenue::where('status', 'pending')->count();

            $view->with(compact('adultInvitationCount', 'pendingBooks', 'pendingSchools', 'pendingPayoutsCount', 'pendingRevenuesCount'));
        });
    }
}
