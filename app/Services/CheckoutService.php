<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function placeOrder(int $userId, array $shippingAddress, ?array $billingAddress=null, ?int $shippingMethodId=null, ?string $couponCode=null): Order
    {
        return DB::transaction(function() use($userId,$shippingAddress,$billingAddress,$shippingMethodId,$couponCode) {
            $cart = Cart::where('user_id',$userId)->where('status','active')
                ->with('cartItems.productVariant.product')->lockForUpdate()->firstOrFail();

            if ($cart->cartItems->isEmpty()) abort(422,'Cart is empty.');

            $subtotal = 0;
            $tax = 0;
            $items = [];
            $weight = 0;

            foreach ($cart->cartItems as $cartItem) {
                $variant = $cartItem->productVariant;
                $price = app(PricingService::class)->forVariant($variant->id);
                abort_unless($price,422,"No active price configured for {$variant->sku}.");

                $line = (float)$price->selling_price * $cartItem->quantity;
                $lineTax = $line * ((float)$price->tax_percentage / 100);
                $subtotal += $line;
                $tax += $lineTax;
                $weight += (float)$variant->weight_grams * $cartItem->quantity;

                $items[] = compact('variant','cartItem','price','line','lineTax');
            }

            $discount = 0;
            $coupon = null;
            if ($couponCode) {
                $result = app(CouponService::class)->validate($couponCode,$cart,$cart->user);
                if (!$result['valid']) abort(422,$result['message']);
                $coupon = $result['coupon'];
                $discount = $result['discount'];
            }

            $shipping = 0;
            $shippingName = null;
            if ($shippingMethodId) {
                $method = \App\Models\ShippingMethod::with('rates')->findOrFail($shippingMethodId);
                $rate = app(ShippingService::class)->available($shippingAddress,$subtotal,$weight)
                    ->firstWhere('id',$method->id)?->rates->sortBy('shipping_charge')->first();
                if ($rate) $shipping = $rate->free_shipping ? 0 : (float)$rate->shipping_charge;
                $shippingName = $method->name;
            }

            $grand = max(0, $subtotal - $discount + $tax + $shipping);

            $order = Order::create([
                'uuid'=>(string)Str::uuid(),
                'order_number'=>'GR-'.now()->format('ymdHis').'-'.strtoupper(Str::random(5)),
                'user_id'=>$userId,
                'coupon_id'=>$coupon?->id,
                'currency'=>'INR',
                'subtotal'=>$subtotal,
                'discount_amount'=>$discount,
                'tax_amount'=>$tax,
                'shipping_amount'=>$shipping,
                'shipping_method_id'=>$shippingMethodId,
                'shipping_method_name_snapshot'=>$shippingName,
                'grand_total'=>$grand,
                'payment_status'=>'pending',
                'fulfilment_status'=>'pending',
                'shipment_status'=>'pending',
                'billing_address_json'=>$billingAddress ?: $shippingAddress,
                'shipping_address_json'=>$shippingAddress,
                'customer_note'=>null,
                'placed_at'=>now(),
            ]);

            foreach($items as $item) {
                $order->orderItems()->create([
                    'product_variant_id'=>$item['variant']->id,
                    'product_name_snapshot'=>$item['variant']->product->name,
                    'sku_snapshot'=>$item['variant']->sku,
                    'quantity'=>$item['cartItem']->quantity,
                    'mrp_snapshot'=>$item['price']->mrp,
                    'unit_price'=>$item['price']->selling_price,
                    'discount_amount'=>0,
                    'tax_amount'=>$item['lineTax'],
                    'line_total'=>$item['line'],
                ]);
                app(InventoryService::class)->reserve($item['variant']->id,$item['cartItem']->quantity,'order',$order->id);
            }

            if ($coupon) {
                $coupon->usages()->create([
                    'user_id'=>$userId,
                    'order_id'=>$order->id,
                    'discount_amount'=>$discount,
                    'used_at'=>now(),
                ]);
            }

            $cart->update(['status'=>'converted']);

            $order->statusHistory()->create([
                'status_type'=>'order',
                'old_status'=>null,
                'new_status'=>'placed',
                'changed_by'=>$userId,
                'remarks'=>'Order placed through storefront',
                'created_at'=>now(),
            ]);

            return $order->load('orderItems.productVariant.product','payments','shipments');
        });
    }
}
