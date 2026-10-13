@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1400px] p-6 lg:p-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-stone-900 font-serif">Sales Analytics</h1>
            <p class="text-xs text-stone-500 mt-1">Deep dive into gross vs. net sales, best-selling SKUs, and gateway distribution.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-stone-400 font-medium">Period:</span>
            <div class="inline-flex rounded-xl border border-stone-200 bg-white p-1 text-xs">
                <a href="?days=30" class="rounded-lg px-3 py-1 font-medium {{ ($days ?? 30) == 30 ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-stone-600 hover:text-stone-900' }}">30 Days</a>
                <a href="?days=90" class="rounded-lg px-3 py-1 font-medium {{ ($days ?? 30) == 90 ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-stone-600 hover:text-stone-900' }}">90 Days</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Gross Sales Volume</div>
            <div class="mt-2 text-2xl font-bold text-stone-900">₹{{ number_format($grossSales, 2) }}</div>
            <p class="mt-1 text-xs text-stone-500">Total value of all placed carts prior to deductions</p>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Net Realized Revenue</div>
            <div class="mt-2 text-2xl font-bold text-[#204b34]">₹{{ number_format($netSales, 2) }}</div>
            <p class="mt-1 text-xs text-stone-500">Paid orders minus customer refunds</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Top Selling Products --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-xs lg:col-span-8">
            <h2 class="text-base font-semibold text-stone-900 mb-4">Top-Selling Products by Revenue</h2>
            @if($topProducts->isEmpty())
                <div class="p-8 text-center text-xs text-stone-400 border border-dashed border-stone-200 rounded-xl">
                    No sales recorded for this timeframe.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-stone-100 text-[11px] font-medium uppercase tracking-[.08em] text-stone-400">
                                <th class="pb-3 pr-4">Product Name</th>
                                <th class="pb-3 px-4">SKU</th>
                                <th class="pb-3 px-4 text-center">Units Sold</th>
                                <th class="pb-3 pl-4 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-stone-700">
                            @foreach($topProducts as $item)
                                <tr>
                                    <td class="py-3 pr-4 font-medium text-stone-900">{{ $item->product_name_snapshot }}</td>
                                    <td class="py-3 px-4 font-mono text-stone-500">{{ $item->sku_snapshot ?: '—' }}</td>
                                    <td class="py-3 px-4 text-center">{{ $item->units_sold }}</td>
                                    <td class="py-3 pl-4 text-right font-semibold text-stone-900">₹{{ number_format($item->total_revenue, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Payment Gateway Distribution --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-xs lg:col-span-4">
            <h2 class="text-base font-semibold text-stone-900 mb-4">Payment Method Distribution</h2>
            @if($paymentGateways->isEmpty())
                <div class="p-8 text-center text-xs text-stone-400 border border-dashed border-stone-200 rounded-xl">
                    No payment records.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($paymentGateways as $gw)
                        <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-3 text-xs flex items-center justify-between">
                            <div>
                                <span class="font-semibold text-stone-900 capitalize">{{ str_replace('_', ' ', $gw->gateway) }}</span>
                                <span class="text-stone-400 text-[11px] block">{{ $gw->count }} transactions</span>
                            </div>
                            <span class="font-bold text-stone-800">₹{{ number_format($gw->total, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
