<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPrice extends Model
{
    use HasFactory;
    protected $table = 'product_prices';
    protected $guarded = [];
    protected $casts = [
        'mrp'=>'decimal:2',
        'selling_price'=>'decimal:2',
        'tax_percentage'=>'decimal:2',
        'effective_from'=>'datetime',
        'effective_to'=>'datetime',
        'is_active'=>'boolean',
    ];
    public function variant() { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
}
