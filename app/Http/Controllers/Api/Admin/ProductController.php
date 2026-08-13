<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        return Product::with(['images','variants','prices'])->orderByDesc('id')->paginate(30);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255'],
            'short_description' => ['nullable','string'],
            'description' => ['nullable','string'],
            'status' => ['required','string','max:30'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer','min:0'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        return response()->json(Product::create($data), 201);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($request->validate([
            'name' => ['sometimes','string','max:255'],
            'short_description' => ['nullable','string'],
            'description' => ['nullable','string'],
            'status' => ['sometimes','string','max:30'],
            'is_featured' => ['sometimes','boolean'],
            'sort_order' => ['sometimes','integer','min:0'],
        ]));

        return response()->json($product->fresh());
    }
}
