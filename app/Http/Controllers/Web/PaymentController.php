<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id===$request->user()->id,403);
        abort_if($order->payment_status==='paid',302,route('payment.success',$order));
        return view('payment',compact('order'));
    }

    public function success(Request $request, Order $order)
    {
        abort_unless($order->user_id===$request->user()->id,403);
        $order->load('payments.transactions','shipments.trackingEvents');
        return view('payment-success',compact('order'));
    }
}
