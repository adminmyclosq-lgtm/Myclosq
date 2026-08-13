<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';
    protected $guarded = [];
    protected $casts = ['amount'=>'decimal:2','paid_at'=>'datetime'];
    public function order() { return $this->belongsTo(Order::class); }
    public function transactions() { return $this->hasMany(PaymentTransaction::class); }
}
