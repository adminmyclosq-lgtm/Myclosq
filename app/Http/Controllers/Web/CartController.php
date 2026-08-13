<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request, CartService $service)
    {
        $cart = $request->user()
            ? $service->currentCart($request->user()->id)->load('cartItems.productVariant.product')
            : null;

        return view('cart', compact('cart'));
    }
}
