@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10">
<div class="flex flex-wrap items-center justify-between gap-4"><div><span class="badge">Ecommerce</span><h1 class="serif mt-3 text-4xl">Products</h1></div><a class="btn-primary" href="{{ route('admin.products.create') }}">+ Add product</a></div>
@if(session('success'))<div class="mt-5 rounded-xl bg-green-50 p-4 text-green-800">{{ session('success') }}</div>@endif
<form class="mt-6"><input name="search" value="{{ request('search') }}" placeholder="Search product" class="rounded-xl border p-3"><button class="btn-secondary ml-2">Search</button></form>
<div class="mt-6 overflow-x-auto rounded-2xl border bg-white"><table class="w-full text-sm"><thead class="bg-stone-50 text-left"><tr><th class="p-4">Product</th><th>SKU</th><th>Price</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($products as $p)<tr class="border-t"><td class="p-4"><b>{{ $p->name }}</b><div class="text-xs text-stone-500">{{ $p->category->name ?? 'Uncategorised' }}</div></td><td>{{ $p->base_sku }}</td><td>₹{{ number_format($p->variants->first()?->currentPrice?->selling_price ?? 0,2) }}</td><td>{{ $p->status }}</td><td><a class="underline" href="{{ route('admin.products.edit',$p) }}">Edit</a></td></tr>@endforeach
</tbody></table></div><div class="mt-6">{{ $products->links() }}</div>
</div>
@endsection
