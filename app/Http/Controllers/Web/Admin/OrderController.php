<?php
namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request) {
        $orders=Order::with('user')->when($request->status,fn($q,$v)=>$q->where('fulfilment_status',$v))->latest('id')->paginate(25);
        return view('admin.orders.index',compact('orders'));
    }

    public function show(Order $order) {
        $order->load('user.customerProfile','orderItems.productVariant.product','payments.transactions','shipments.trackingEvents','statusHistory');
        return view('admin.orders.show',compact('order'));
    }

    public function update(Request $request, Order $order, ShipmentService $shipments) {
        $data=$request->validate([
            'payment_status'=>['required','string','max:30'],
            'fulfilment_status'=>['required','string','max:30'],
            'shipment_status'=>['required','string','max:30'],
        ]);
        $requiresShipment = in_array($data['shipment_status'], ['packed','dispatched','in_transit','out_for_delivery','delivered'], true);

        if ($requiresShipment && $data['payment_status'] !== 'paid') {
            return back()->withErrors(['payment_status' => 'Only paid orders can be sent to fulfilment or dispatched.']);
        }

        DB::transaction(function() use($order,$data,$requiresShipment,$shipments) {
            $old=$order->only(array_keys($data));
            $order->update($data);
            foreach($data as $key=>$new) {
                if (($old[$key] ?? null) !== $new) {
                    $order->statusHistory()->create([
                        'status_type'=>$key,
                        'old_status'=>$old[$key] ?? null,
                        'new_status'=>$new,
                        'changed_by'=>auth()->id(),
                        'remarks'=>'Admin status update',
                        'created_at'=>now(),
                    ]);
                }
            }

            if ($data['payment_status'] === 'paid' && ($requiresShipment || ! $order->shipments()->exists())) {
                $shipment = $shipments->createForOrder($order);

                if ($requiresShipment) {
                    $shipments->recordTracking($shipment, [
                        'status' => $data['shipment_status'],
                        'description' => 'Status updated from the order dashboard',
                        'event_time' => now(),
                    ]);
                }
            }
        });
        return back()->with('success','Order status updated.');
    }
}
