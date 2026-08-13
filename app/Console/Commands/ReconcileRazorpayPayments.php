<?php

namespace App\Console\Commands;

use App\Jobs\ReconcilePayments;
use Illuminate\Console\Command;

class ReconcileRazorpayPayments extends Command
{
    protected $signature='payments:reconcile';
    protected $description='Reconcile pending/authorized Razorpay payments';

    public function handle(): int
    {
        ReconcilePayments::dispatch();
        $this->info('Payment reconciliation job queued.');
        return self::SUCCESS;
    }
}
