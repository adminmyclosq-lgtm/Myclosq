<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartService
{
    public function currentCart(int $userId): Cart
    {
        return Cart::firstOrCreate(
            ['user_id'=>$userId,'status'=>'active'],
            ['currency'=>'INR','session_token'=>Str::random(48)]
        );
    }

    public function add(int $userId, int $variantId, int $quantity): Cart
    {
        return DB::transaction(function() use($userId,$variantId,$quantity) {
            $variant = ProductVariant::with('product')->lockForUpdate()->findOrFail($variantId);
            abort_unless($variant->status === 'active', 422, 'Product variant is not available.');

            $price = app(PricingService::class)->forVariant($variantId);
            abort_unless($price, 422, 'No active price is configured.');

            $cart = $this->currentCart($userId);
            $item = $cart->cartItems()->where('product_variant_id',$variantId)->first();

            if ($item) {
                $item->update([
                    'quantity'=>$item->quantity+$quantity,
                    'unit_price_snapshot'=>$price->selling_price,
                ]);
            } else {
                $cart->cartItems()->create([
                    'product_variant_id'=>$variantId,
                    'quantity'=>$quantity,
                    'unit_price_snapshot'=>$price->selling_price,
                ]);
            }

            return $cart->fresh('cartItems.productVariant.product');
        });
    }

    public function update(CartItem $item, int $quantity): Cart
    {
        if ($quantity < 1) $item->delete();
        else $item->update(['quantity'=>$quantity]);

        return $item->cart->fresh('cartItems.productVariant.product');
    }
}
