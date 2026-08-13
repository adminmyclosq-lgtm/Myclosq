<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable as FoundationQueueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReconcilePayments implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public function handle(PaymentService $payments): void
    {
        Payment::whereIn('status',['pending','authorized'])
            ->where('created_at','>=',now()->subDays(2))
            ->orderBy('id')
            ->chunkById(100,function($items) use($payments) {
                foreach($items as $payment) {
                    try { $payments->reconcile($payment); } catch(\Throwable $e) {
                        report($e);
                    }
                }
            });
    }
}
