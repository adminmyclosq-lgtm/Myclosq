<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RazorpayPaymentGateway implements PaymentGatewayInterface
{
    private function client()
    {
        return Http::withBasicAuth(
            (string) config('services.razorpay.key_id'),
            (string) config('services.razorpay.key_secret')
        )->acceptJson()->asJson()->timeout(15);
    }

    public function createOrder(Order $order, Payment $payment): array
    {
        if (!config('services.razorpay.key_id') || !config('services.razorpay.key_secret')) {
            throw new RuntimeException('Razorpay credentials are not configured.');
        }

        $response = $this->client()->post(rtrim(config('services.razorpay.base_url'),'/').'/orders', [
            'amount' => (int) round(((float) $payment->amount) * 100),
            'currency' => $payment->currency,
            'receipt' => $order->order_number,
            'notes' => [
                'myclosq_order_id' => (string) $order->id,
                'myclosq_order_uuid' => (string) $order->uuid,
            ],
        ]);

        if (!$response->successful()) {
            throw new RuntimeException('Razorpay order creation failed: '.$response->body());
        }

        return $response->json();
    }

    public function verifyCheckoutSignature(string $gatewayOrderId, string $gatewayPaymentId, string $signature): bool
    {
        $secret = (string) config('services.razorpay.key_secret');
        if ($secret === '') return false;

        $expected = hash_hmac('sha256', $gatewayOrderId.'|'.$gatewayPaymentId, $secret);
        return hash_equals($expected, $signature);
    }

    public function fetchPayment(string $gatewayPaymentId): array
    {
        $response = $this->client()->get(
            rtrim(config('services.razorpay.base_url'),'/').'/payments/'.urlencode($gatewayPaymentId)
        );
        if (!$response->successful()) {
            throw new RuntimeException('Razorpay payment fetch failed: '.$response->body());
        }
        return $response->json();
    }

    public function fetchOrder(string $gatewayOrderId): array
    {
        $response = $this->client()->get(
            rtrim(config('services.razorpay.base_url'),'/').'/orders/'.urlencode($gatewayOrderId)
        );
        if (!$response->successful()) {
            throw new RuntimeException('Razorpay order fetch failed: '.$response->body());
        }
        return $response->json();
    }
}
