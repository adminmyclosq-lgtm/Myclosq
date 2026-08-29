<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'order_items';
    protected $guarded = [];
    protected $casts = [
        'mrp_snapshot'=>'decimal:2','unit_price'=>'decimal:2','discount_amount'=>'decimal:2',
        'tax_amount'=>'decimal:2','line_total'=>'decimal:2',
    ];
    public function order() { return $this->belongsTo(Order::class); }
    public function productVariant() { return $this->belongsTo(ProductVariant::class); }
}
