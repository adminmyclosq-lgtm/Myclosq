<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductBatche extends Model
{
    use HasFactory;
    protected $table = 'product_batches';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function productvariant() { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }

}
