<?php

namespace App\Services;

use App\Models\ShippingMethod;
use Illuminate\Support\Collection;

class ShippingService
{
    public function available(array $address, float $orderValue, float $weightGrams): Collection
    {
        return ShippingMethod::with(['rates'=>function($q) use ($address,$orderValue,$weightGrams) {
            $q->where('is_active',true)
              ->where('country',$address['country'] ?? 'India')
              ->where(function($x) use ($address) {
                  $state = $address['state'] ?? null;
                  $postal = $address['postal_code'] ?? null;

                  $x->whereNull('state');
                  if ($state) {
                      $x->orWhere('state',$state);
                  }

                  if ($postal) {
                      $x->where(function($p) use ($postal) {
                          $p->whereNull('postal_code_prefix')
                            ->orWhere('postal_code_prefix','like',$postal.'%');
                      });
                  }
              })
              ->where('min_order_value','<=',$orderValue)
              ->where(fn($x)=>$x->whereNull('max_order_value')->orWhere('max_order_value','>=',$orderValue))
              ->where('min_weight_grams','<=',$weightGrams)
              ->where(fn($x)=>$x->whereNull('max_weight_grams')->orWhere('max_weight_grams','>=',$weightGrams))
              ->where('effective_from','<=',now())
              ->where(fn($x)=>$x->whereNull('effective_to')->orWhere('effective_to','>=',now()));
        }])->where('is_active',true)->orderBy('sort_order')->get();
    }
}
