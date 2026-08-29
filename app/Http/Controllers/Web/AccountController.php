<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
class AccountController extends Controller {
    public function index(Request $request) {
        return view('account',['user'=>$request->user()->load('customerProfile','orders')]);
    }
    public function order(Request $request, Order $order) {
        abort_unless($order->user_id===$request->user()->id,403);
        return view('account.order',['order'=>$order->load('orderItems','shipments.trackingEvents')]);
    }
}
