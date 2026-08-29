@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10">
<div class="flex flex-wrap items-end justify-between gap-4"><div><span class="badge">Finance</span><h1 class="serif mt-3 text-4xl">Payments & reconciliation</h1></div><a class="rounded-xl bg-stone-900 px-4 py-3 text-sm font-semibold text-white" href="{{ route('admin.dashboard') }}">Dashboard</a></div>
<div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
@foreach([['All',$totals['all']],['Paid',$totals['paid']],['Pending',$totals['pending']],['Failed',$totals['failed']],['Paid value','₹'.number_format($totals['paid_amount'],2)],['Pending value','₹'.number_format($totals['pending_amount'],2)]] as $x)<div class="card"><div class="text-xs uppercase tracking-wide text-stone-500">{{ $x[0] }}</div><div class="mt-2 text-xl font-bold">{{ $x[1] }}</div></div>@endforeach
</div>
<div class="card mt-8 overflow-x-auto"><form class="mb-5 flex flex-wrap gap-2"><select name="status" class="rounded-xl border p-2"><option value="">All status</option>@foreach(['pending','authorized','paid','failed'] as $s)<option @selected(request('status')===$s)>{{ $s }}</option>@endforeach</select><select name="gateway" class="rounded-xl border p-2"><option value="">All gateways</option><option value="razorpay" @selected(request('gateway')==='razorpay')>Razorpay</option></select><button class="rounded-xl bg-stone-200 px-4 py-2">Filter</button></form>
<table class="w-full text-sm"><thead><tr class="border-b text-left"><th class="p-3">Payment</th><th class="p-3">Order</th><th class="p-3">Customer</th><th class="p-3">Gateway</th><th class="p-3">Amount</th><th class="p-3">Status</th><th class="p-3"></th></tr></thead><tbody>
@foreach($payments as $p)<tr class="border-b"><td class="p-3">#{{ $p->id }}</td><td class="p-3">{{ $p->order?->order_number }}</td><td class="p-3">{{ $p->order?->user?->name ?: $p->order?->user?->email }}</td><td class="p-3">{{ $p->gateway }}</td><td class="p-3">₹{{ number_format($p->amount,2) }}</td><td class="p-3"><span class="rounded-full bg-stone-100 px-2 py-1">{{ $p->status }}</span></td><td class="p-3"><a class="underline" href="{{ route('admin.payments.show',$p) }}">View</a></td></tr>@endforeach
</tbody></table><div class="mt-5">{{ $payments->links() }}</div></div>
</div>
@endsection
