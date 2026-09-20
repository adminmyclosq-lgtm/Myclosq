<?php

namespace App\Services;

use App\Jobs\SendWhatsAppTemplate;
use App\Models\Order;
use App\Models\Shipment;

class OrderAutomationService
{
    public function paymentCaptured(Order $order): void
    {
        $order->loadMissing('user.customerProfile');
        $this->dispatchTemplate($order->user,'order_paid',[
            $order->order_number,
            number_format((float)$order->grand_total,2),
        ]);

        if (config('services.razorpay.key_id')) {
            app(ShipmentService::class)->createForOrder($order);
        }
    }

    public function shipmentStatusChanged(Shipment $shipment): void
    {
        $shipment->loadMissing('order.user');
        $order=$shipment->order;
        $template=match($shipment->status) {
            'created','packed' => 'order_packed',
            'dispatched','in_transit' => 'order_dispatched',
            'out_for_delivery' => 'order_out_for_delivery',
            'delivered' => 'order_delivered',
            default => null,
        };

        if ($shipment->status === 'delivered') {
            app(ResetJourneyService::class)->createCycleFromDelivery($order->user);
        }

        if ($template) {
            $this->dispatchTemplate($order->user, $template, [
                $order->order_number,
                $shipment->tracking_number ?? '',
            ]);
        }
    }

    private function dispatchTemplate($user,string $template,array $parameters): void
    {
        if (!($user->customerProfile?->whatsapp_opt_in ?? false)) return;
        SendWhatsAppTemplate::dispatch($user,$template,'en',$parameters);
    }
}
