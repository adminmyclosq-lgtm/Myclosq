<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PlaceOrderRequest;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RuntimeException;

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
        try {
            $order=$checkout->placeOrder(
                $request->user()->id,
                $request->array('shipping_address'),
                $request->input('billing_address'),
                $request->integer('shipping_method_id') ?: null,
                $request->input('coupon_code'),
                $request->input('payment_method')
            );
        } catch (RuntimeException $exception) {
            if (! in_array($exception->getMessage(), ['Insufficient inventory.', 'Inventory record not configured.'], true)) {
                throw $exception;
            }

            throw ValidationException::withMessages([
                'cart' => 'One or more items are currently out of stock. Please adjust inventory before placing this order.',
            ]);
        }

        if ($request->input('payment_method') === 'cod') {
            return redirect()->route('payment.success', ['order' => $order->id])->with('success', 'Cash on delivery order created.');
        }
        return redirect()->route('payment.show', ['order' => $order->id])->with('success','Order created. Complete secure payment.');
    }
}
