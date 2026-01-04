<?php

namespace App\Console\Commands;

use App\Models\Badge;
use App\Models\ReadingProgress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AwardMonthlyReaderBadge extends Command
{
    protected $signature = 'app:award-monthly-reader-badge';

    protected $description = 'Award a badge to the most active reader of the last month.';

    public function handle()
    {
        $this->info('Calculating last month\'s most active reader...');

        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        // Find the user with the most time spent reading in the last month
        $topReader = ReadingProgress::select('user_id', DB::raw('SUM(time_spent) as total_time'))
            ->whereBetween('last_read_at', [$startOfLastMonth, $endOfLastMonth])
            ->groupBy('user_id')
            ->orderBy('total_time', 'desc')
            ->first();

        if (! $topReader) {
            $this->info('No reading activity found for last month.');

            return 0;
        }

        $user = User::find($topReader->user_id);
        $badge = Badge::where('slug', 'lecteur-du-mois')->first();

        if ($user && $badge) {
            // Remove the badge from the previous winner, if any
            $previousWinner = $badge->users()->whereMonth('pivot_earned_at', $startOfLastMonth->month)->first();
            if ($previousWinner) {
                $previousWinner->badges()->detach($badge->id);
            }

            // Award badge to the new winner
            if (! $user->badges()->where('badge_id', $badge->id)->whereMonth('pivot_earned_at', $startOfLastMonth->month)->exists()) {
                $user->badges()->attach($badge->id, ['earned_at' => now()]);
                $this->info("Awarded 'Lecteur du Mois' badge to user: {$user->name}");
            } else {
                $this->info("User {$user->name} already has this month's badge.");
            }
        } else {
            $this->warn('Top reader or badge not found.');
        }

        $this->info('Monthly reader badge check complete.');

        return 0;
    }
}
