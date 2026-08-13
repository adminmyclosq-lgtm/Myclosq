<?php

namespace App\Contracts;

use App\Models\Shipment;
use App\Models\Order;

interface ShipmentProviderInterface
{
    public function create(Order $order, Shipment $shipment): array;
    public function tracking(string $trackingNumber): array;
}
