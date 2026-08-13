<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Phase2DAnalyticsService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request, Phase2DAnalyticsService $analytics)
    {
        $data=$analytics->paymentDashboard($request->only('status','gateway','from','to'));
        return view('admin.payments.index',$data);
    }

    public function show(Payment $payment)
    {
        $payment->load('order.user.customerProfile','transactions');
        return view('admin.payments.show',compact('payment'));
    }

    public function reconcile(Payment $payment, PaymentService $payments)
    {
        try {
            $payments->reconcile($payment);
            return back()->with('success','Payment reconciliation completed.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error','Reconciliation failed: '.$e->getMessage());
        }
    }
}
