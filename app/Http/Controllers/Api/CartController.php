<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddCartItemRequest;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $service) {}

    public function show(Request $request)
    {
        return response()->json(
            $this->service->currentCart($request->user()->id)->load('cartItems.productVariant.product')
        );
    }

    public function store(AddCartItemRequest $request)
    {
        return response()->json(
            $this->service->add($request->user()->id, $request->integer('product_variant_id'), $request->integer('quantity')),
            201
        );
    }

    public function update(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id, 403);
        $data = $request->validate(['quantity' => ['required','integer','min:0','max:99']]);
        return response()->json($this->service->update($cartItem, $data['quantity']));
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id, 403);
        $cartItem->delete();
        return response()->json(['message' => 'Removed']);
    }
}
