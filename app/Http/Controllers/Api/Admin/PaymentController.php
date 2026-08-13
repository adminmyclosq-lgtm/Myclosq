<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $q=Payment::with('order.user')->latest('id');
        if($request->status) $q->where('status',$request->status);
        return response()->json($q->paginate(30));
    }

    public function show(Payment $payment)
    {
        return response()->json($payment->load('order.user','transactions'));
    }

    public function reconcile(Payment $payment, PaymentService $service)
    {
        return response()->json($service->reconcile($payment));
    }
}
