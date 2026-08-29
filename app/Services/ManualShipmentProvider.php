<?php

namespace App\Services;

use App\Contracts\ShipmentProviderInterface;
use App\Models\Order;
use App\Models\Shipment;

class ManualShipmentProvider implements ShipmentProviderInterface
{
    public function create(Order $order, Shipment $shipment): array
    {
        return [
            'status'=>'created',
            'shipment_number'=>$shipment->shipment_number,
            'tracking_number'=>$shipment->tracking_number,
        ];
    }

    public function tracking(string $trackingNumber): array
    {
        return ['status'=>'unknown','tracking_number'=>$trackingNumber,'events'=>[]];
    }
}
