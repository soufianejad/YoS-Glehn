<?php

namespace App\Console\Commands;

use App\Services\RevenueCalculatorService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DistributeRevenues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'distribute:revenues {--month=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Distribute subscription revenues for a given month';

    /**
     * The RevenueCalculatorService instance.
     *
     * @var RevenueCalculatorService
     */
    protected $revenueCalculator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RevenueCalculatorService $revenueCalculator)
    {
        parent::__construct();
        $this->revenueCalculator = $revenueCalculator;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $month = $this->option('month');
        if ($month) {
            try {
                $date = Carbon::createFromFormat('Y-m', $month);
            } catch (\Exception $e) {
                $this->error('Invalid month format. Please use YYYY-MM.');

                return 1;
            }
        } else {
            $date = now()->subMonth();
        }

        $this->info('Distributing subscription revenues for '.$date->format('F Y').'...');

        $result = $this->revenueCalculator->distributeSubscriptionRevenues($date);

        if ($result['success']) {
            $this->info('Subscription revenues distributed successfully.');
            $this->info($result['revenues_distributed'].' revenues distributed for a total of '.$result['total_amount'].'.');
        } else {
            $this->error('An error occurred while distributing subscription revenues: '.$result['error']);
        }

        return 0;
    }
}
