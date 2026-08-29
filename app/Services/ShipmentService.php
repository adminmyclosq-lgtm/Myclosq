<?php

namespace App\Services;

use App\Contracts\ShipmentProviderInterface;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShipmentService
{
    public function __construct(private ShipmentProviderInterface $provider) {}

    public function createForOrder(Order $order): Shipment
    {
        return DB::transaction(function() use($order) {
            $shipment=$order->shipments()->first();
            if (!$shipment) {
                $shipment=$order->shipments()->create([
                    'shipment_number'=>'SHP-'.now()->format('ymdHis').'-'.strtoupper(Str::random(5)),
                    'courier'=>null,
                    'tracking_number'=>null,
                    'status'=>'pending',
                ]);
            }

            $result=$this->provider->create($order,$shipment);
            $shipment->update([
                'status'=>$result['status'] ?? 'created',
                'tracking_number'=>$result['tracking_number'] ?? $shipment->tracking_number,
            ]);
            $order->update([
                'fulfilment_status'=>'processing',
                'shipment_status'=>$shipment->status,
            ]);

            return $shipment->fresh();
        });
    }

    public function recordTracking(Shipment $shipment, array $event): void
    {
        DB::transaction(function() use($shipment,$event) {
            $shipment->trackingEvents()->create([
                'status'=>$event['status'],
                'location'=>$event['location'] ?? null,
                'event_time'=>$event['event_time'] ?? now(),
                'description'=>$event['description'] ?? null,
                'raw_payload'=>$event['raw_payload'] ?? $event,
            ]);

            $status=(string)$event['status'];
            $shipment->update([
                'status'=>$status,
                'dispatch_date'=>in_array($status,['dispatched','in_transit'],true) ? ($shipment->dispatch_date ?: now()) : $shipment->dispatch_date,
                'delivered_at'=>$status==='delivered' ? ($shipment->delivered_at ?: now()) : $shipment->delivered_at,
            ]);

            $shipment->order->update([
                'shipment_status'=>$status,
                'fulfilment_status'=>$status==='delivered'?'completed':$shipment->order->fulfilment_status,
            ]);

            app(OrderAutomationService::class)->shipmentStatusChanged($shipment->fresh('order'));
        });
    }
}
