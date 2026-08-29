<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $guarded = [];
    protected $casts = [
        'subtotal'=>'decimal:2','discount_amount'=>'decimal:2','tax_amount'=>'decimal:2',
        'shipping_amount'=>'decimal:2','grand_total'=>'decimal:2',
        'billing_address_json'=>'array','shipping_address_json'=>'array','placed_at'=>'datetime',
    ];
    public function user() { return $this->belongsTo(User::class); }
    public function coupon() { return $this->belongsTo(Coupon::class); }
    public function shippingMethod() { return $this->belongsTo(ShippingMethod::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function shipments() { return $this->hasMany(Shipment::class); }
    public function statusHistory() { return $this->hasMany(OrderStatusHistory::class); }
}
