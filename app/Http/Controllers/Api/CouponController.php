<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use App\Services\CartService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request, CouponService $coupons, CartService $carts)
    {
        $data=$request->validate(['code'=>['required','string','max:80']]);
        $cart=$carts->currentCart($request->user()->id);
        return response()->json($coupons->validate($data['code'],$cart,$request->user()));
    }
}
