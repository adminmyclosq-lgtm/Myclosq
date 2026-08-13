<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    public function createOrder(Order $order, Payment $payment): array;
    public function verifyCheckoutSignature(string $gatewayOrderId, string $gatewayPaymentId, string $signature): bool;
    public function fetchPayment(string $gatewayPaymentId): array;
    public function fetchOrder(string $gatewayOrderId): array;
}
