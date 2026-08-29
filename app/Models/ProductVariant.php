<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;
    protected $table = 'product_variants';
    protected $guarded = [];

    protected $casts = [
        'unit_value'=>'decimal:3',
        'weight_grams'=>'decimal:3',
        'length_cm'=>'decimal:2',
        'width_cm'=>'decimal:2',
        'height_cm'=>'decimal:2',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function prices() { return $this->hasMany(ProductPrice::class); }
    public function images() { return $this->hasMany(ProductImage::class); }
    public function inventory() { return $this->hasMany(Inventory::class); }

    public function currentPrice()
    {
        return $this->hasOne(ProductPrice::class)
            ->where('is_active', true)
            ->where('effective_from', '<=', now())
            ->where(fn($q) => $q->whereNull('effective_to')->orWhere('effective_to','>=',now()))
            ->latestOfMany('effective_from');
    }
}
