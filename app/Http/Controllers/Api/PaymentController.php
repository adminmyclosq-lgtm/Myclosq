<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Request $request, Order $order, PaymentService $payments)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $payment=$payments->createGatewayOrder($order);

        return response()->json([
            'payment_id'=>$payment->id,
            'gateway'=>'razorpay',
            'gateway_order_id'=>$payment->gateway_order_id,
            'amount'=>(float)$payment->amount,
            'currency'=>$payment->currency,
            'key_id'=>config('services.razorpay.key_id'),
            'order_id'=>$order->id,
            'order_number'=>$order->order_number,
        ]);
    }

    public function verify(Request $request, Order $order, PaymentService $payments)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $data=$request->validate([
            'razorpay_order_id'=>['required','string'],
            'razorpay_payment_id'=>['required','string'],
            'razorpay_signature'=>['required','string'],
        ]);

        $payment=$payments->verifyCheckout($order,$data);

        return response()->json([
            'status'=>$payment->status,
            'order_status'=>$order->fresh()->payment_status,
        ]);
    }
}
