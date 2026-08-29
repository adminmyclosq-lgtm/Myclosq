<?php
namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

    public function update(Request $request, Order $order) {
        $data=$request->validate([
            'payment_status'=>['required','string','max:30'],
            'fulfilment_status'=>['required','string','max:30'],
            'shipment_status'=>['required','string','max:30'],
        ]);
        DB::transaction(function() use($order,$data) {
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
        });
        return back()->with('success','Order status updated.');
    }
}
