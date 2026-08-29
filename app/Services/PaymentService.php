<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentService
{
    public function __construct(private PaymentGatewayInterface $gateway) {}

    public function createGatewayOrder(Order $order): Payment
    {
        if ($order->payment_status === 'paid') {
            return $order->payments()->latest('id')->firstOrFail();
        }

        return DB::transaction(function () use ($order) {
            $payment = $order->payments()->where('status','pending')->latest('id')->first();

            if (!$payment) {
                $payment = $order->payments()->create([
                    'gateway' => 'razorpay',
                    'amount' => $order->grand_total,
                    'currency' => $order->currency,
                    'status' => 'pending',
                    'idempotency_key' => (string) Str::uuid(),
                ]);
            }

            if (!$payment->gateway_order_id) {
                $gatewayOrder = $this->gateway->createOrder($order,$payment);
                $payment->update(['gateway_order_id'=>$gatewayOrder['id'] ?? null]);
                $payment->transactions()->create([
                    'transaction_type'=>'gateway_order_created',
                    'gateway_transaction_id'=>$gatewayOrder['id'] ?? null,
                    'gateway_status'=>$gatewayOrder['status'] ?? 'created',
                    'amount'=>$payment->amount,
                    'currency'=>$payment->currency,
                    'signature_verified'=>false,
                    'raw_response'=>$gatewayOrder,
                    'processed_at'=>now(),
                ]);
            }

            return $payment->fresh();
        });
    }

    public function verifyCheckout(Order $order, array $payload): Payment
    {
        $payment = $order->payments()
            ->where('gateway','razorpay')
            ->where('gateway_order_id',$payload['razorpay_order_id'] ?? '')
            ->latest('id')->firstOrFail();

        $valid = $this->gateway->verifyCheckoutSignature(
            $payment->gateway_order_id,
            $payload['razorpay_payment_id'],
            $payload['razorpay_signature']
        );

        if (!$valid) {
            $payment->transactions()->create([
                'transaction_type'=>'checkout_signature_failed',
                'gateway_transaction_id'=>$payload['razorpay_payment_id'],
                'gateway_status'=>'signature_invalid',
                'amount'=>$payment->amount,
                'currency'=>$payment->currency,
                'signature_verified'=>false,
                'raw_response'=>$payload,
                'processed_at'=>now(),
            ]);
            throw new RuntimeException('Payment signature verification failed.');
        }

        $gatewayPayment=$this->gateway->fetchPayment($payload['razorpay_payment_id']);
        $this->applyGatewayPayment($payment,$gatewayPayment,true);

        return $payment->fresh();
    }

    public function reconcile(Payment $payment): Payment
    {
        if (!$payment->gateway_order_id) return $payment;

        $gatewayPaymentId = $payment->transactions()
            ->whereNotNull('gateway_transaction_id')
            ->whereIn('transaction_type',['payment_authorized','payment_captured','payment_failed','webhook'])
            ->latest('id')->value('gateway_transaction_id');

        if (!$gatewayPaymentId) {
            $gatewayOrder=$this->gateway->fetchOrder($payment->gateway_order_id);
            $gatewayPaymentId=data_get($gatewayOrder,'payments.0.id');
        }

        if (!$gatewayPaymentId) {
            $gatewayOrder=$this->gateway->fetchOrder($payment->gateway_order_id);
            $payment->transactions()->create([
                'transaction_type'=>'reconciliation',
                'gateway_transaction_id'=>null,
                'gateway_status'=>$gatewayOrder['status'] ?? 'created',
                'amount'=>$payment->amount,
                'currency'=>$payment->currency,
                'signature_verified'=>false,
                'raw_response'=>$gatewayOrder,
                'processed_at'=>now(),
            ]);
            return $payment->fresh();
        }

        $gatewayPayment=$this->gateway->fetchPayment($gatewayPaymentId);
        $this->applyGatewayPayment($payment,$gatewayPayment,false);
        return $payment->fresh();
    }

    public function applyGatewayPayment(Payment $payment, array $gatewayPayment, bool $signatureVerified): void
    {
        DB::transaction(function () use ($payment,$gatewayPayment,$signatureVerified) {
            $status=(string)($gatewayPayment['status'] ?? 'unknown');
            $gatewayId=$gatewayPayment['id'] ?? null;
            $amount=((float)($gatewayPayment['amount'] ?? 0))/100;

            if ($amount > 0 && abs($amount-(float)$payment->amount) > 0.009) {
                throw new RuntimeException('Gateway amount does not match local payment amount.');
            }

            $transactionType = match ($status) {
                'captured' => 'payment_captured',
                'authorized' => 'payment_authorized',
                'failed' => 'payment_failed',
                default => 'gateway_status',
            };

            $payment->transactions()->updateOrCreate(
                ['gateway_transaction_id'=>$gatewayId,'transaction_type'=>$transactionType],
                [
                    'gateway_status'=>$status,
                    'amount'=>$payment->amount,
                    'currency'=>$payment->currency,
                    'signature_verified'=>$signatureVerified,
                    'raw_response'=>$gatewayPayment,
                    'processed_at'=>now(),
                ]
            );

            if ($status === 'captured') {
                $wasPaid = $payment->order->payment_status === 'paid';
                $payment->update(['status'=>'paid','paid_at'=>$payment->paid_at ?: now()]);
                $payment->order->update(['payment_status'=>'paid']);
                if (! $wasPaid) {
                    foreach ($payment->order->orderItems as $item) {
                        app(InventoryService::class)->consume($item->product_variant_id, $item->quantity, 'order', $payment->order->id);
                    }
                }
                app(OrderAutomationService::class)->paymentCaptured($payment->order);
            } elseif ($status === 'failed') {
                $payment->update(['status'=>'failed']);
            } elseif ($status === 'authorized') {
                $payment->update(['status'=>'authorized']);
            }
        });
    }
}
