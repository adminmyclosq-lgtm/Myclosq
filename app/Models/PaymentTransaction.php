<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransaction extends Model
{
    use HasFactory;
    protected $table = 'payment_transactions';
    protected $guarded = [];
    protected $casts = ['amount'=>'decimal:2','signature_verified'=>'boolean','raw_response'=>'array','processed_at'=>'datetime'];
    public function payment() { return $this->belongsTo(Payment::class); }
}
