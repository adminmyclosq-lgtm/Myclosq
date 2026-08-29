<?php
namespace App\Http\Controllers\Web\Admin;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
class CouponController extends Controller {
    public function index() { $coupons=Coupon::latest('id')->paginate(25); return view('admin.coupons.index',compact('coupons')); }
    public function store(Request $request) {
        $data=$request->validate(['code'=>'required|max:80|unique:coupons,code','name'=>'required|max:120','description'=>'nullable|string','discount_type'=>'required|max:30','discount_value'=>'required|numeric|min:0','minimum_cart_value'=>'numeric|min:0','maximum_discount'=>'nullable|numeric|min:0','starts_at'=>'nullable|date','expires_at'=>'nullable|date','usage_limit'=>'nullable|integer|min:1','per_customer_limit'=>'nullable|integer|min:1','is_active'=>'boolean']);
        Coupon::create($data);
        return back()->with('success','Coupon created.');
    }
}
