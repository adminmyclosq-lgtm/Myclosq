<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\Request;

class ShipmentTrackingController extends Controller
{
    public function receive(Request $request, ShipmentService $shipments)
    {
        $secret=(string)config('services.shipment.webhook_secret');
        $raw=$request->getContent();
        $provided=(string)$request->header('X-Shipment-Signature');

        if ($secret) {
            $expected=hash_hmac('sha256',$raw,$secret);
            abort_unless($provided && hash_equals($expected,$provided),403,'Invalid shipment webhook signature.');
        }

        $data=$request->validate([
            'tracking_number'=>['required','string'],
            'status'=>['required','string','max:50'],
            'location'=>['nullable','string','max:200'],
            'event_time'=>['nullable','date'],
            'description'=>['nullable','string','max:500'],
            'raw_payload'=>['nullable','array'],
        ]);

        $shipment=Shipment::where('tracking_number',$data['tracking_number'])->firstOrFail();
        $shipments->recordTracking($shipment,$data);

        return response()->json(['received'=>true]);
    }
}
