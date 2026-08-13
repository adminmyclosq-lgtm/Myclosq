<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q = Order::with(['user','orderItems.productVariant.product','shipments']);

        if ($request->filled('status')) {
            $q->where('fulfilment_status', $request->string('status'));
        }

        return $q->latest('id')->paginate(30);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'payment_status' => ['sometimes','string','max:30'],
            'fulfilment_status' => ['sometimes','string','max:30'],
            'shipment_status' => ['sometimes','string','max:30'],
        ]);

        $order->update($data);

        return response()->json($order->fresh());
    }
}
