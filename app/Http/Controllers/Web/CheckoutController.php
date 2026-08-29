<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PlaceOrderRequest;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function show(Request $request)
    {
        $cart=app(\App\Services\CartService::class)->currentCart($request->user()->id)->load('cartItems.productVariant.product');
        $methods=\App\Models\ShippingMethod::with('rates')->where('is_active',true)->orderBy('sort_order')->get();
        return view('checkout',compact('cart','methods'));
    }

    public function store(PlaceOrderRequest $request, CheckoutService $checkout)
    {
        $order=$checkout->placeOrder(
            $request->user()->id,
            $request->array('shipping_address'),
            $request->input('billing_address'),
            $request->integer('shipping_method_id') ?: null,
            $request->input('coupon_code')
        );
        return redirect()->route('payment.show', ['order' => $order->id])->with('success','Order created. Complete secure payment.');
    }
}
