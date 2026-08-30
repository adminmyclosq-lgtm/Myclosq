<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Phase2DAnalyticsService;
use App\Services\AdminExcelExportService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request, Phase2DAnalyticsService $analytics)
    {
        $data=$analytics->paymentDashboard($request->only('status','gateway','from','to'));
        return view('admin.payments.index',$data);
    }

    public function export(Request $request, AdminExcelExportService $exporter)
    {
        $payments=\App\Models\Payment::with('order.user')->latest('id');
        if ($request->status) $payments->where('status', $request->status);
        if ($request->gateway) $payments->where('gateway', $request->gateway);
        if ($request->from) $payments->whereDate('created_at', '>=', $request->from);
        if ($request->to) $payments->whereDate('created_at', '<=', $request->to);
        return $exporter->download('payments-'.now()->format('Y-m-d'), ['Payment', 'Order', 'Customer', 'Gateway', 'Amount', 'Status', 'Created'], $payments->get()->map(fn(\App\Models\Payment $payment)=>[
            $payment->id, $payment->order?->order_number, $payment->order?->user?->name ?: $payment->order?->user?->email, $payment->gateway, $payment->amount, $payment->status, $payment->created_at,
        ]));
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
