<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'order_number' => $this->order_number,
            'currency' => $this->currency,
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'tax_amount' => $this->tax_amount,
            'shipping_amount' => $this->shipping_amount,
            'grand_total' => $this->grand_total,
            'payment_status' => $this->payment_status,
            'fulfilment_status' => $this->fulfilment_status,
            'shipment_status' => $this->shipment_status,
            'placed_at' => $this->placed_at,
            'items' => $this->whenLoaded('orderItems'),
            'shipments' => $this->whenLoaded('shipments'),
        ];
    }
}
