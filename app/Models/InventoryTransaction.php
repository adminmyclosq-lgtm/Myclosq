<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryTransaction extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'inventory_transactions';
    protected $guarded = [];
    protected $casts = ['quantity'=>'integer','balance_after'=>'integer','created_at'=>'datetime'];
    public function inventory() { return $this->belongsTo(Inventory::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
