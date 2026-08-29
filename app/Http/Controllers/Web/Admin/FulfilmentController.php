<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\Phase2DAnalyticsService;
use App\Services\ShipmentService;
use Illuminate\Http\Request;

class FulfilmentController extends Controller
{
    public function index(Request $request, Phase2DAnalyticsService $analytics)
    {
        $shipments = app(ShipmentService::class);

        Order::where('payment_status', 'paid')
            ->whereIn('shipment_status', ['packed', 'dispatched', 'in_transit', 'out_for_delivery', 'delivered'])
            ->whereDoesntHave('shipments')
            ->each(function (Order $order) use ($shipments): void {
                $status = $order->shipment_status;
                $shipment = $shipments->createForOrder($order);
                $shipments->recordTracking($shipment, [
                    'status' => $status,
                    'description' => 'Shipment backfilled from order status',
                    'event_time' => now(),
                ]);
            });

        $data=$analytics->fulfilmentDashboard($request->only('status','courier'));
        return view('admin.fulfilment.index',$data);
    }

    public function create(Order $order, ShipmentService $shipments)
    {
        abort_if($order->payment_status!=='paid',422,'Only paid orders can be fulfilled.');
        $shipment=$shipments->createForOrder($order);
        return back()->with('success','Shipment created: '.$shipment->shipment_number);
    }

    public function update(Request $request, Shipment $shipment, ShipmentService $shipments)
    {
        $data=$request->validate([
            'status'=>['required','string','max:40'],
            'courier'=>['nullable','string','max:100'],
            'tracking_number'=>['nullable','string','max:150'],
            'expected_delivery'=>['nullable','date'],
        ]);

        $shipment->update($data);

        $shipments->recordTracking($shipment,[
            'status'=>$data['status'],
            'description'=>'Admin fulfilment status update',
            'event_time'=>now(),
            'raw_payload'=>$data,
        ]);

        return back()->with('success','Shipment updated.');
    }
}
