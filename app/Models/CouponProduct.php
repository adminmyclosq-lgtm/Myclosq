<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CouponProduct extends Model
{
    use HasFactory;
    protected $table = 'coupon_products';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function coupon() { return $this->belongsTo(Coupon::class, 'coupon_id'); }

    public function product() { return $this->belongsTo(Product::class, 'product_id'); }

}
