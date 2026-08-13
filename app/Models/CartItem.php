<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;
    protected $table = 'cart_items';
    protected $guarded = [];
    protected $casts = ['unit_price_snapshot'=>'decimal:2'];
    public function cart() { return $this->belongsTo(Cart::class); }
    public function productVariant() { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
}
