<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;
    protected $table = 'inventory';
    protected $guarded = [];
    protected $casts = ['quantity_on_hand'=>'integer','quantity_reserved'=>'integer','reorder_level'=>'integer'];
    public function productVariant() { return $this->belongsTo(ProductVariant::class); }
    public function batch() { return $this->belongsTo(ProductBatch::class, 'batch_id'); }
    public function transactions() { return $this->hasMany(InventoryTransaction::class); }
}
