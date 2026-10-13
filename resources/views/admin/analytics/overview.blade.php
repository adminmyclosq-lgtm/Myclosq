@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1400px] p-6 lg:p-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-stone-900 font-serif">Business Overview</h1>
            <p class="text-xs text-stone-500 mt-1">Key executive metrics, revenue performance, and order volume velocity.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-stone-400 font-medium">Timeframe:</span>
            <div class="inline-flex rounded-xl border border-stone-200 bg-white p-1 text-xs">
                <a href="?days=7" class="rounded-lg px-3 py-1 font-medium {{ ($days ?? 30) == 7 ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-stone-600 hover:text-stone-900' }}">7D</a>
                <a href="?days=30" class="rounded-lg px-3 py-1 font-medium {{ ($days ?? 30) == 30 ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-stone-600 hover:text-stone-900' }}">30D</a>
                <a href="?days=90" class="rounded-lg px-3 py-1 font-medium {{ ($days ?? 30) == 90 ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-stone-600 hover:text-stone-900' }}">90D</a>
            </div>
        </div>
    </div>

    {{-- Top Metric Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Total Net Revenue</div>
            <div class="mt-2 text-2xl font-bold text-stone-900">₹{{ number_format($totalRevenue, 2) }}</div>
            <div class="mt-1 flex items-center text-xs text-emerald-600 font-medium">
                <svg class="h-3.5 w-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Paid Orders
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Total Orders</div>
            <div class="mt-2 text-2xl font-bold text-stone-900">{{ number_format($totalOrders) }}</div>
            <div class="mt-1 text-xs text-stone-500">
                {{ $paidOrders }} paid, {{ max(0, $totalOrders - $paidOrders) }} pending
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Average Order Value (AOV)</div>
            <div class="mt-2 text-2xl font-bold text-stone-900">₹{{ number_format($aov, 2) }}</div>
            <div class="mt-1 text-xs text-stone-500">Per paid customer cart</div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-xs">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">New Customers</div>
            <div class="mt-2 text-2xl font-bold text-stone-900">{{ number_format($newCustomers) }}</div>
            <div class="mt-1 text-xs text-emerald-600 font-medium">Acquired in timeframe</div>
        </div>
    </div>

    {{-- Daily Performance Breakdown --}}
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-stone-900">Daily Order Revenue Trend (Last 14 Days)</h2>
            <span class="text-xs text-stone-400">Paid Order GMV</span>
        </div>

        @if($dailySales->isEmpty())
            <div class="p-8 text-center text-xs text-stone-400 border border-dashed border-stone-200 rounded-xl">
                No orders recorded in this date range.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-stone-100 text-[11px] font-medium uppercase tracking-[.08em] text-stone-400">
                            <th class="pb-3 pr-4">Date</th>
                            <th class="pb-3 px-4 text-center">Orders Count</th>
                            <th class="pb-3 pl-4 text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700">
                        @foreach($dailySales as $day)
                            <tr>
                                <td class="py-3 pr-4 font-medium text-stone-900">{{ \Carbon\Carbon::parse($day->date)->format('D, d M Y') }}</td>
                                <td class="py-3 px-4 text-center font-medium">{{ $day->orders_count }}</td>
                                <td class="py-3 pl-4 text-right font-semibold text-emerald-800">₹{{ number_format($day->daily_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
