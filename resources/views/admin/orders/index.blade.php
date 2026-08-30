@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10"><span class="badge">Ecommerce</span><h1 class="serif mt-3 text-4xl">Orders & fulfilment</h1>
@if(session('success'))<div class="mt-5 rounded-xl bg-green-50 p-4 text-green-800">{{ session('success') }}</div>@endif
<form class="mt-6 flex flex-wrap items-center justify-between gap-3"><div class="flex flex-wrap gap-2"><select name="status" class="rounded-xl border p-3"><option value="">All fulfilment statuses</option>@foreach(['pending','processing','packed','dispatched','delivered','cancelled'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>@endforeach</select><button class="btn-secondary">Filter</button></div><a class="btn-secondary" href="{{ route('admin.orders.export', request()->query()) }}">Download Excel</a></form>
<div class="mt-6 overflow-x-auto rounded-2xl border bg-white"><table class="w-full text-sm"><thead class="bg-stone-50 text-left"><tr><th class="p-4">Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Fulfilment</th><th></th></tr></thead><tbody>
@foreach($orders as $o)<tr class="border-t"><td class="p-4"><b>{{ $o->order_number }}</b><div class="text-xs text-stone-500">{{ $o->created_at }}</div></td><td>{{ $o->user->email ?? $o->user->mobile }}</td><td>₹{{ number_format($o->grand_total,2) }}</td><td>{{ $o->payment_status }}</td><td>{{ $o->fulfilment_status }}</td><td><a class="underline" href="{{ route('admin.orders.show',$o) }}">Open</a></td></tr>@endforeach
</tbody></table></div><div class="mt-6">{{ $orders->links() }}</div></div>
@endsection
