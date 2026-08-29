@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10">
<div class="flex flex-wrap items-end justify-between"><div><span class="badge">Management</span><h1 class="serif mt-4 text-5xl">Dashboard</h1></div></div>
<div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
@foreach([['Customers',number_format($stats['customers'])],['Orders',number_format($stats['orders'])],['Paid orders',number_format($stats['paid_orders'])],['Sales','₹'.number_format($stats['gross_sales'],2)],['AOV','₹'.number_format($stats['average_order_value']??0,2)]] as $s)
<div class="card"><div class="text-sm text-stone-500">{{ $s[0] }}</div><div class="mt-2 text-2xl font-bold">{{ $s[1] }}</div></div>
@endforeach
</div>
<div class="mt-8 grid gap-6 lg:grid-cols-2">
<div class="card"><h2 class="text-xl font-bold">Sales analytics</h2><div class="mt-5">@forelse($stats['sales_by_day'] as $row)<div class="flex justify-between border-b py-3 text-sm"><span>{{ $row->day }}</span><span>₹{{ number_format($row->sales,2) }} · {{ $row->orders }}</span></div>@empty<p class="text-stone-500">No paid sales yet.</p>@endforelse</div></div>
<div class="card"><h2 class="text-xl font-bold">Operations</h2><div class="mt-5 grid grid-cols-2 gap-3 text-sm"><a class="rounded-xl bg-stone-100 p-4" href="{{ route('admin.products.index') }}">Catalogue</a><a class="rounded-xl bg-stone-100 p-4" href="{{ route('admin.orders.index') }}">Fulfilment</a><a class="rounded-xl bg-stone-100 p-4" href="{{ route('admin.inventory.index') }}">Inventory</a><a class="rounded-xl bg-stone-100 p-4" href="{{ route('admin.media.index') }}">Media</a></div></div>
</div>
<div class="mt-8 grid gap-6 lg:grid-cols-4">
<a class="card hover:bg-white" href="{{ route('admin.payments.index') }}"><div class="text-xs uppercase text-stone-500">Finance</div><div class="mt-2 text-xl font-bold">Payments & reconciliation</div><p class="mt-2 text-sm text-stone-500">Gateway transactions, pending payments and reconciliation.</p></a>
<a class="card hover:bg-white" href="{{ route('admin.fulfilment.index') }}"><div class="text-xs uppercase text-stone-500">Operations</div><div class="mt-2 text-xl font-bold">Shipment fulfilment</div><p class="mt-2 text-sm text-stone-500">Shipment status, tracking and delivery operations.</p></a>
<a class="card hover:bg-white" href="{{ route('admin.whatsapp.dashboard') }}"><div class="text-xs uppercase text-stone-500">Engagement</div><div class="mt-2 text-xl font-bold">WhatsApp workflow</div><p class="mt-2 text-sm text-stone-500">Message funnel, templates and delivery status.</p></a>
<a class="card hover:bg-white" href="{{ route('admin.reset.operations') }}"><div class="text-xs uppercase text-stone-500">Programme</div><div class="mt-2 text-xl font-bold">Day 0–30 operations</div><p class="mt-2 text-sm text-stone-500">Adherence, milestones, safety and active reset days.</p></a>
</div>
</div>
@endsection
