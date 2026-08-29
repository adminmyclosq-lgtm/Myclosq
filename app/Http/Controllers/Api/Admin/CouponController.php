<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
class CouponController extends Controller {
    public function index() { return Coupon::latest('id')->paginate(30); }
    public function store(Request $request) {
        $data=$request->validate(['code'=>'required|max:80|unique:coupons,code','name'=>'required|max:120','discount_type'=>'required|max:30','discount_value'=>'required|numeric|min:0','minimum_cart_value'=>'numeric|min:0','maximum_discount'=>'nullable|numeric|min:0','starts_at'=>'nullable|date','expires_at'=>'nullable|date','usage_limit'=>'nullable|integer','per_customer_limit'=>'nullable|integer','is_active'=>'boolean']);
        return response()->json(Coupon::create($data),201);
    }
}
