<?php
namespace App\Http\Controllers\Web\Admin;
use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ShippingController extends Controller {
    public function index() { $methods=ShippingMethod::with('rates')->orderBy('sort_order')->get(); return view('admin.shipping.index',compact('methods')); }
    public function store(Request $request) {
        $data=$request->validate(['name'=>'required|max:120','code'=>'required|max:50','courier'=>'nullable|max:120','estimated_min_days'=>'nullable|integer|min:0','estimated_max_days'=>'nullable|integer|min:0','is_active'=>'boolean','sort_order'=>'integer|min:0']);
        ShippingMethod::create($data);
        return back()->with('success','Shipping method created.');
    }
    public function rate(Request $request, ShippingMethod $shippingMethod) {
        $data=$request->validate(['country'=>'required|max:100','state'=>'nullable|max:100','postal_code_prefix'=>'nullable|max:10','min_order_value'=>'numeric|min:0','max_order_value'=>'nullable|numeric|min:0','min_weight_grams'=>'numeric|min:0','max_weight_grams'=>'nullable|numeric|min:0','shipping_charge'=>'numeric|min:0','free_shipping'=>'boolean','effective_from'=>'required|date']);
        $shippingMethod->rates()->create($data+['is_active'=>true]);
        return back()->with('success','Shipping rate created.');
    }
}
