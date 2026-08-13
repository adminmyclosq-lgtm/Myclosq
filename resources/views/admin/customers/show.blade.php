@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10"><a class="underline text-sm" href="{{ route('admin.customers.index') }}">← Customers</a><h1 class="serif mt-4 text-4xl">{{ $user->customerProfile->display_name ?? $user->email }}</h1>
<div class="mt-8 grid gap-6 md:grid-cols-3"><div class="card"><div class="text-xs text-stone-500">Email</div><div class="mt-2">{{ $user->email }}</div></div><div class="card"><div class="text-xs text-stone-500">Mobile</div><div class="mt-2">{{ $user->mobile }}</div></div><div class="card"><div class="text-xs text-stone-500">Orders</div><div class="mt-2 text-2xl font-bold">{{ $user->orders->count() }}</div></div></div>
<div class="card mt-6"><h2 class="font-bold">Recent orders</h2><div class="mt-5 space-y-3">@foreach($user->orders->sortByDesc('id')->take(10) as $o)<div class="flex justify-between border-b pb-3"><span>{{ $o->order_number }}</span><span>₹{{ number_format($o->grand_total,2) }}</span></div>@endforeach</div></div>
</div>
@endsection
