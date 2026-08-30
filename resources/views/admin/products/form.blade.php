@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10 max-w-5xl">
<a class="text-sm underline" href="{{ route('admin.products.index') }}">← Products</a>
<h1 class="serif mt-4 text-4xl">{{ $product->exists?'Edit product':'Create product' }}</h1>
@if($errors->any())<div class="mt-5 rounded-xl bg-red-50 p-4 text-red-700">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
<form method="POST" action="{{ $product->exists?route('admin.products.update',$product):route('admin.products.store') }}" enctype="multipart/form-data" class="mt-8 space-y-6">
@csrf @if($product->exists) @method('PUT') @endif
<div class="card grid gap-5 md:grid-cols-2">
<div><label>Name</label><input name="name" value="{{ old('name',$product->name) }}" required class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Base SKU</label><input name="base_sku" value="{{ old('base_sku',$product->base_sku) }}" required class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Slug</label><input name="slug" value="{{ old('slug',$product->slug) }}" class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Category</label><select name="category_id" class="mt-2 w-full rounded-xl border p-3"><option value="">None</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id',$product->category_id)==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
<div class="md:col-span-2"><label>Short description</label><textarea name="short_description" class="mt-2 w-full rounded-xl border p-3">{{ old('short_description',$product->short_description) }}</textarea></div>
<div class="md:col-span-2"><label>Description</label><textarea name="description" rows="5" class="mt-2 w-full rounded-xl border p-3">{{ old('description',$product->description) }}</textarea></div>
<div><label>Status</label><select name="status" class="mt-2 w-full rounded-xl border p-3"><option value="draft">draft</option><option value="active" @selected($product->status==='active')>active</option><option value="inactive">inactive</option></select></div>
<label class="flex items-center gap-3 pt-7"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured',$product->is_featured))> Featured product</label>
</div>
<div class="card"><h2 class="text-xl font-bold">Primary variant</h2><div class="mt-5 grid gap-5 md:grid-cols-3">
@php($variant=$product->variants->first() ?? new \App\Models\ProductVariant())
<div><label>Variant name</label><input name="variant[name]" value="{{ old('variant.name',$variant->name) }}" required class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Variant SKU</label><input name="variant[sku]" value="{{ old('variant.sku',$variant->sku) }}" required class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Unit label</label><input name="variant[unit_label]" value="{{ old('variant.unit_label',$variant->unit_label) }}" class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Weight grams</label><input name="variant[weight_grams]" value="{{ old('variant.weight_grams',$variant->weight_grams) }}" class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Variant status</label><select name="variant[status]" class="mt-2 w-full rounded-xl border p-3"><option value="active">active</option><option value="inactive">inactive</option></select></div>
<div class="md:col-span-3"><label class="font-semibold">Product images</label><p class="mt-1 text-sm text-stone-500">Upload one main image for shop cards and the primary product image, plus up to three gallery images for the product page.</p></div>
<div><label>Main image</label><input type="file" name="main_image" accept="image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-xl border p-3">@if($product->productImages->firstWhere('is_primary', true)?->media)<img src="{{ $product->productImages->firstWhere('is_primary', true)->media->url }}" alt="Current main product image" class="mt-3 aspect-square w-full rounded-xl border object-cover">@elseif($product->productImages->first()?->media)<img src="{{ $product->productImages->first()->media->url }}" alt="Current main product image" class="mt-3 aspect-square w-full rounded-xl border object-cover">@endif</div>
@for($slot = 0; $slot < 3; $slot++)
	<div><label>Gallery image {{ $slot + 1 }}</label><input type="file" name="gallery_images[{{ $slot }}]" accept="image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-xl border p-3">@if($product->productImages->where('is_primary', false)->sortBy('sort_order')->values()->get($slot)?->media)<img src="{{ $product->productImages->where('is_primary', false)->sortBy('sort_order')->values()->get($slot)->media->url }}" alt="Current gallery image {{ $slot + 1 }}" class="mt-3 aspect-square w-full rounded-xl border object-cover">@endif</div>
@endfor
</div></div>
<div class="card"><h2 class="text-xl font-bold">Price</h2><div class="mt-5 grid gap-5 md:grid-cols-3">
@php($price=$variant->prices->first() ?? new \App\Models\ProductPrice())
<div><label>MRP</label><input type="number" step="0.01" name="price[mrp]" value="{{ old('price.mrp',$price->mrp) }}" required class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Selling price</label><input type="number" step="0.01" name="price[selling_price]" value="{{ old('price.selling_price',$price->selling_price) }}" required class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Tax %</label><input type="number" step="0.01" name="price[tax_percentage]" value="{{ old('price.tax_percentage',$price->tax_percentage) }}" class="mt-2 w-full rounded-xl border p-3"></div>
</div></div>
@if(! $product->exists)
<div class="card"><h2 class="text-xl font-bold">Opening inventory</h2><div class="mt-5 max-w-sm"><label>Quantity on hand</label><input type="number" min="0" name="inventory[quantity_on_hand]" value="{{ old('inventory.quantity_on_hand',0) }}" required class="mt-2 w-full rounded-xl border p-3"></div></div>
@endif
<button class="btn-primary">{{ $product->exists?'Update product':'Create product' }}</button>
</form>
</div>
@endsection
