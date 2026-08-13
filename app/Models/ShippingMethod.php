<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingMethod extends Model
{
    use HasFactory;
    protected $table = 'shipping_methods';
    protected $guarded = [];
    protected $casts = ['is_active'=>'boolean'];
    public function rates() { return $this->hasMany(ShippingRate::class); }
    public function shippingRates() { return $this->rates(); }
    public function orders() { return $this->hasMany(Order::class); }
}
