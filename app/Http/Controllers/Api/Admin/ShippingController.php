<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
class ShippingController extends Controller {
    public function index() { return ShippingMethod::with('rates')->orderBy('sort_order')->get(); }
    public function store(Request $request) {
        return response()->json(ShippingMethod::create($request->validate(['name'=>'required|max:120','code'=>'required|max:50','courier'=>'nullable|max:120','estimated_min_days'=>'nullable|integer','estimated_max_days'=>'nullable|integer','is_active'=>'boolean','sort_order'=>'integer'])),201);
    }
    public function rate(Request $request, ShippingMethod $shippingMethod) {
        $data=$request->validate(['country'=>'required|max:100','state'=>'nullable|max:100','postal_code_prefix'=>'nullable|max:10','min_order_value'=>'numeric','max_order_value'=>'nullable|numeric','min_weight_grams'=>'numeric','max_weight_grams'=>'nullable|numeric','shipping_charge'=>'numeric|min:0','free_shipping'=>'boolean','effective_from'=>'required|date']);
        return response()->json($shippingMethod->rates()->create($data+['is_active'=>true]),201);
    }
}
