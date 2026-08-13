<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;
    protected $table = 'coupons';
    protected $guarded = [];
    protected $casts = [
        'discount_value'=>'decimal:2',
        'minimum_cart_value'=>'decimal:2',
        'maximum_discount'=>'decimal:2',
        'starts_at'=>'datetime',
        'expires_at'=>'datetime',
        'is_active'=>'boolean',
    ];
    public function products() { return $this->belongsToMany(Product::class, 'coupon_products', 'coupon_id', 'product_id'); }
    public function usages() { return $this->hasMany(CouponUsage::class); }
}
