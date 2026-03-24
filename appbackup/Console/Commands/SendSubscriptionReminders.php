<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendSubscriptionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-subscription-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to users whose subscriptions are about to expire.';

    /**
     * The notification service instance.
     *
     * @var \App\Services\NotificationService
     */
    protected $notificationService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Sending subscription expiration reminders...');

        $reminderDays = 3;
        $targetDate = Carbon::now()->addDays($reminderDays)->startOfDay();

        $subscriptions = Subscription::where('status', 'active')
            ->whereDate('end_date', $targetDate)
            ->with('user')
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No subscriptions expiring in '.$reminderDays.' days.');

            return 0;
        }

        foreach ($subscriptions as $subscription) {
            if ($subscription->user) {
                $this->notificationService->sendNotification(
                    $subscription->user,
                    'Votre abonnement arrive à expiration',
                    "Votre abonnement '{$subscription->subscriptionPlan->name}' expire dans {$reminderDays} jours. Renouvelez-le pour ne pas perdre l'accès.",
                    route('subscription.plans'),
                    'warning'
                );
                $this->line("Reminder sent to: {$subscription->user->email}");
            }
        }

        $this->info('All reminders sent successfully.');

        return 0;
    }
}
