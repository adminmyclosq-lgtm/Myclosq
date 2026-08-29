<?php

namespace App\Services;

use App\Models\ProductPrice;

class PricingService
{
    public function current(ProductPrice $price): bool
    {
        $now = now();
        return $price->is_active
            && $price->effective_from <= $now
            && (!$price->effective_to || $price->effective_to >= $now);
    }

    public function forVariant(int $variantId, ?string $customerGroup = null): ?ProductPrice
    {
        $q = ProductPrice::where('product_variant_id',$variantId)
            ->where('is_active',true)
            ->where('effective_from','<=',now())
            ->where(fn($x)=>$x->whereNull('effective_to')->orWhere('effective_to','>=',now()));

        if ($customerGroup) {
            $q->where(function($x) use ($customerGroup) {
                $x->whereNull('customer_group')->orWhere('customer_group',$customerGroup);
            });
        }

        return $q->orderByRaw('CASE WHEN customer_group = ? THEN 0 ELSE 1 END', [$customerGroup])
            ->orderByDesc('effective_from')
            ->first();
    }
}
