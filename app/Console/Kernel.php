<?php

namespace App\Console;

use App\Console\Commands\AwardMonthlyReaderBadge;
use App\Console\Commands\SendSubscriptionReminders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\DistributeRevenues::class,
        SendSubscriptionReminders::class,
        AwardMonthlyReaderBadge::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('app:send-subscription-reminders')->daily();
        $schedule->command('app:award-monthly-reader-badge')->monthlyOn(1, '01:00'); // Run on the 1st of every month at 1 AM
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
