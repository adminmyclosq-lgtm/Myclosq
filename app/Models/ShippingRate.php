<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingRate extends Model
{
    use HasFactory;
    protected $table = 'shipping_rates';
    protected $guarded = [];
    protected $casts = [
        'min_order_value'=>'decimal:2','max_order_value'=>'decimal:2',
        'min_weight_grams'=>'decimal:3','max_weight_grams'=>'decimal:3',
        'shipping_charge'=>'decimal:2','free_shipping'=>'boolean',
        'effective_from'=>'datetime','effective_to'=>'datetime','is_active'=>'boolean',
    ];
    public function shippingMethod() { return $this->belongsTo(ShippingMethod::class); }
}
