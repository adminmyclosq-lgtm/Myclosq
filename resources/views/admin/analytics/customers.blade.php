@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1400px] p-6 lg:p-8 space-y-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-stone-900 font-serif">Customer Analytics</h1>
        <p class="text-xs text-stone-500 mt-1">Customer acquisition, repeat buyer ratios, and highest lifetime value accounts.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Total Registered Users</div>
            <div class="mt-2 text-2xl font-bold text-stone-900">{{ number_format($totalUsers) }}</div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Active Buyers</div>
            <div class="mt-2 text-2xl font-bold text-stone-900">{{ number_format($orderingUsers) }}</div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Repeat Customers</div>
            <div class="mt-2 text-2xl font-bold text-stone-900">{{ number_format($repeatCustomers) }}</div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Repeat Purchase Rate</div>
            <div class="mt-2 text-2xl font-bold text-emerald-700">{{ $repeatRate }}%</div>
        </div>
    </div>

    {{-- Top Customers by Lifetime Value --}}
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-xs">
        <h2 class="text-base font-semibold text-stone-900 mb-4">Top Customers by Lifetime Value (LTV)</h2>
        @if($topCustomers->isEmpty())
            <div class="p-8 text-center text-xs text-stone-400 border border-dashed border-stone-200 rounded-xl">
                No customer orders recorded yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-stone-100 text-[11px] font-medium uppercase tracking-[.08em] text-stone-400">
                            <th class="pb-3 pr-4">Customer Name</th>
                            <th class="pb-3 px-4">Email</th>
                            <th class="pb-3 px-4 text-center">Total Orders</th>
                            <th class="pb-3 px-4 text-right">Lifetime Spend</th>
                            <th class="pb-3 pl-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700">
                        @foreach($topCustomers as $cust)
                            <tr>
                                <td class="py-3 pr-4 font-semibold text-stone-900">{{ $cust->name }}</td>
                                <td class="py-3 px-4 text-stone-500">{{ $cust->email }}</td>
                                <td class="py-3 px-4 text-center">{{ $cust->orders_count }}</td>
                                <td class="py-3 px-4 text-right font-bold text-stone-900">₹{{ number_format($cust->orders_sum_grand_total ?? 0, 2) }}</td>
                                <td class="py-3 pl-4 text-right">
                                    <a href="{{ route('admin.customers.show', $cust->id) }}" class="font-medium text-[#204b34] hover:underline">
                                        View Profile
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
