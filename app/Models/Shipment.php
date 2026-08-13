<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory;
    protected $table = 'shipments';
    protected $guarded = [];
    protected $casts = ['pickup_date'=>'datetime','dispatch_date'=>'datetime','expected_delivery'=>'datetime','delivered_at'=>'datetime'];
    public function order() { return $this->belongsTo(Order::class); }
    public function trackingEvents() { return $this->hasMany(ShipmentTrackingEvent::class); }
}
