<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private CheckoutService $checkout) {}

    public function index(Request $request)
    {
        return OrderResource::collection(
            Order::where('user_id', $request->user()->id)
                ->with(['orderItems.productVariant.product','shipments'])
                ->latest('id')->paginate(20)
        );
    }

    public function store(PlaceOrderRequest $request)
    {
        $order = $this->checkout->placeOrder(
            $request->user()->id,
            $request->array('shipping_address'),
            $request->input('billing_address'),
            $request->integer('shipping_method_id') ?: null,
            $request->input('coupon_code')
        );

        return (new OrderResource($order))->response()->setStatusCode(201);
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        return new OrderResource($order->load(['orderItems.productVariant.product','shipments']));
    }
}
