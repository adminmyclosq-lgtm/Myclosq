<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory;
    protected $table = 'product_images';
    protected $guarded = [];
    protected $casts = ['is_primary'=>'boolean'];
    public function variant() { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
    public function media() { return $this->belongsTo(Media::class, 'media_id'); }
}
