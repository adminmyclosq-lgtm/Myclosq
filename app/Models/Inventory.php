<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';
    protected $guarded = [];

    protected $casts = [
        'quantity_on_hand' => 'integer',
        'quantity_reserved' => 'integer',
        'reorder_level' => 'integer',
        'min_level' => 'integer',
        'max_level' => 'integer',
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class)->orderByDesc('id');
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->quantity_on_hand - $this->quantity_reserved);
    }

    public function getStockStatusAttribute(): string
    {
        $avail = $this->available_stock;
        if ($avail <= 0) {
            return 'out_of_stock';
        }
        $threshold = $this->reorder_level ?: ($this->min_level ?: 10);
        if ($avail <= $threshold) {
            return 'low_stock';
        }
        return 'in_stock';
    }
}
