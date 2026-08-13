<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function options(Request $request, ShippingService $shipping)
    {
        $data=$request->validate([
            'country'=>['required','string'],
            'state'=>['nullable','string'],
            'postal_code'=>['nullable','string'],
            'order_value'=>['required','numeric','min:0'],
            'weight_grams'=>['required','numeric','min:0'],
        ]);
        return response()->json($shipping->available($data,$data['order_value'],$data['weight_grams']));
    }
}
