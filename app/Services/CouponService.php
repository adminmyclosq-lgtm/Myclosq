<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\User;

class CouponService
{
    public function validate(string $code, Cart $cart, User $user): array
    {
        $coupon = Coupon::whereRaw('UPPER(code)=?', [strtoupper(trim($code))])->first();

        if (!$coupon || !$coupon->is_active) {
            return ['valid'=>false,'message'=>'Coupon is not valid.'];
        }
        $now = now();
        if (($coupon->starts_at && $coupon->starts_at > $now) || ($coupon->expires_at && $coupon->expires_at < $now)) {
            return ['valid'=>false,'message'=>'Coupon is outside its validity period.'];
        }

        $items = $cart->load('cartItems.productVariant.product')->cartItems;
        $subtotal = $items->sum(function($item) {
            return (float)$item->unit_price_snapshot * $item->quantity;
        });

        if ($subtotal < (float)$coupon->minimum_cart_value) {
            return ['valid'=>false,'message'=>'Minimum cart value is not met.'];
        }

        $used = $coupon->usages()->where('user_id',$user->id)->count();
        if ($coupon->per_customer_limit !== null && $used >= $coupon->per_customer_limit) {
            return ['valid'=>false,'message'=>'Coupon usage limit reached for this customer.'];
        }

        $totalUsed = $coupon->usages()->count();
        if ($coupon->usage_limit !== null && $totalUsed >= $coupon->usage_limit) {
            return ['valid'=>false,'message'=>'Coupon usage limit reached.'];
        }

        if ($coupon->products()->exists()) {
            $allowed = $coupon->products()->pluck('products.id')->all();
            $hasEligible = $items->contains(fn($item)=>in_array($item->productVariant->product_id,$allowed,true));
            if (!$hasEligible) {
                return ['valid'=>false,'message'=>'Coupon does not apply to the products in this cart.'];
            }
        }

        $discount = match ($coupon->discount_type) {
            'percentage' => $subtotal * ((float)$coupon->discount_value / 100),
            'fixed' => (float)$coupon->discount_value,
            default => 0,
        };

        if ($coupon->maximum_discount !== null) {
            $discount = min($discount, (float)$coupon->maximum_discount);
        }

        return ['valid'=>true,'message'=>'Coupon applied.','coupon'=>$coupon,'discount'=>round(min($discount,$subtotal),2)];
    }
}
