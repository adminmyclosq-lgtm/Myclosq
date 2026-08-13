<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';
    protected $guarded = [];
    protected $casts = ['is_featured'=>'boolean'];

    public function category() { return $this->belongsTo(Category::class); }
    public function variants() { return $this->hasMany(ProductVariant::class); }
    public function productImages() { return $this->hasManyThrough(ProductImage::class, ProductVariant::class, 'product_id', 'product_variant_id'); }
    public function prices() { return $this->hasManyThrough(ProductPrice::class, ProductVariant::class, 'product_id', 'product_variant_id'); }
    public function coupons() { return $this->belongsToMany(Coupon::class, 'coupon_products', 'product_id', 'coupon_id'); }
}
