<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CouponUsage extends Model
{
    use HasFactory;
    protected $table = 'coupon_usages';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['discount_amount'=>'decimal:2','used_at'=>'datetime'];
    public function coupon() { return $this->belongsTo(Coupon::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function order() { return $this->belongsTo(Order::class); }
}
