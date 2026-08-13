<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShipmentTrackingEvent extends Model
{
    use HasFactory;
    protected $table = 'shipment_tracking_events';
    protected $guarded = [];
    protected $casts = ['raw_payload'=>'array','event_time'=>'datetime'];
    public function shipment() { return $this->belongsTo(Shipment::class); }
}
