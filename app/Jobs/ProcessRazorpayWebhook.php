<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable as FoundationQueueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessRazorpayWebhook implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries=5;
    public int $timeout=30;

    public function __construct(public string $rawBody, public string $signature) {}

    public function handle(PaymentService $payments): void
    {
        $secret=(string)config('services.razorpay.webhook_secret');
        $expected=hash_hmac('sha256',$this->rawBody,$secret);

        if (!$secret || !$this->signature || !hash_equals($expected,$this->signature)) {
            throw new \RuntimeException('Invalid Razorpay webhook signature.');
        }

        $payload=json_decode($this->rawBody,true,512,JSON_THROW_ON_ERROR);
        $createdAt=(int)($payload['created_at'] ?? 0);
        if ($createdAt && abs(time()-$createdAt)>300) throw new \RuntimeException('Stale Razorpay webhook.');
        $event=(string)($payload['event'] ?? 'unknown');
        $gatewayPayment=data_get($payload,'payload.payment.entity');

        if (!$gatewayPayment) return;

        $gatewayPaymentId=$gatewayPayment['id'] ?? null;
        $gatewayOrderId=$gatewayPayment['order_id'] ?? null;
        if (!$gatewayOrderId && $event === 'order.paid') {
            $gatewayOrderId=data_get($payload,'payload.order.entity.id');
        }
        if (!$gatewayOrderId) return;

        $payment=Payment::where('gateway','razorpay')->where('gateway_order_id',$gatewayOrderId)->first();
        if (!$payment) return;

        $payment->transactions()->updateOrCreate(
            ['gateway_transaction_id'=>$gatewayPaymentId,'transaction_type'=>'webhook'],
            [
                'gateway_status'=>$gatewayPayment['status'] ?? $event,
                'amount'=>((float)($gatewayPayment['amount'] ?? 0))/100 ?: $payment->amount,
                'currency'=>$gatewayPayment['currency'] ?? $payment->currency,
                'signature_verified'=>true,
                'raw_response'=>$payload,
                'processed_at'=>now(),
            ]
        );

        if (in_array($event,['payment.captured','order.paid'],true) || ($gatewayPayment['status'] ?? null)==='captured') {
            $payments->applyGatewayPayment($payment,$gatewayPayment,true);
        } elseif ($event==='payment.failed') {
            $payment->update(['status'=>'failed']);
            $payment->order->update(['payment_status'=>'failed']);
        }
    }
}
