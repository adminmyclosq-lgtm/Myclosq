@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10"><span class="badge">Configuration</span><h1 class="serif mt-3 text-4xl">Shipping methods & rates</h1>
@if(session('success'))<div class="mt-5 rounded-xl bg-green-50 p-4 text-green-800">{{ session('success') }}</div>@endif
<div class="mt-8 grid gap-6 lg:grid-cols-3">
<div class="card"><h2 class="font-bold">Add method</h2><form method="POST" action="{{ route('admin.shipping.store') }}" class="mt-4 space-y-3">@csrf<input name="name" required placeholder="Name" class="w-full rounded-xl border p-3"><input name="code" required placeholder="Code" class="w-full rounded-xl border p-3"><input name="courier" placeholder="Courier" class="w-full rounded-xl border p-3"><div class="grid grid-cols-2 gap-3"><input name="estimated_min_days" placeholder="Min days" class="rounded-xl border p-3"><input name="estimated_max_days" placeholder="Max days" class="rounded-xl border p-3"></div><button class="btn-primary w-full">Add</button></form></div>
<div class="lg:col-span-2 space-y-4">@foreach($methods as $m)<div class="card"><div class="flex justify-between"><div><b>{{ $m->name }}</b> <span class="text-xs text-stone-500">{{ $m->code }}</span></div><span>{{ $m->estimated_min_days }}–{{ $m->estimated_max_days }} days</span></div><div class="mt-4 space-y-2">@foreach($m->rates as $r)<div class="flex justify-between text-sm rounded-xl bg-stone-50 p-3"><span>{{ $r->country }} {{ $r->state }} {{ $r->postal_code_prefix }}</span><span>{{ $r->free_shipping?'Free':'₹'.number_format($r->shipping_charge,2) }}</span></div>@endforeach</div></div>@endforeach</div>
</div></div>
@endsection
