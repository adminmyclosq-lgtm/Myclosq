@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1400px] p-6 lg:p-8 space-y-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-stone-900 font-serif">Marketing Analytics</h1>
        <p class="text-xs text-stone-500 mt-1">Coupon code redemption velocity, channel attribution, and traffic conversions.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Coupon Performance --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-xs lg:col-span-6">
            <h2 class="text-base font-semibold text-stone-900 mb-4">Coupon Code Redemptions</h2>
            @if($couponStats->isEmpty())
                <div class="p-8 text-center text-xs text-stone-400 border border-dashed border-stone-200 rounded-xl">
                    No coupon usages recorded yet.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-stone-100 text-[11px] font-medium uppercase tracking-[.08em] text-stone-400">
                                <th class="pb-3 pr-4">Code</th>
                                <th class="pb-3 px-4 text-center">Redemptions</th>
                                <th class="pb-3 pl-4 text-right">Total Discount Given</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-stone-700">
                            @foreach($couponStats as $c)
                                <tr>
                                    <td class="py-3 pr-4 font-mono font-bold text-stone-900">{{ $c->code }}</td>
                                    <td class="py-3 px-4 text-center font-medium">{{ $c->uses_count }}</td>
                                    <td class="py-3 pl-4 text-right font-semibold text-emerald-800">₹{{ number_format($c->total_discount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Acquisition Channel Attribution --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-xs lg:col-span-6">
            <h2 class="text-base font-semibold text-stone-900 mb-4">Channel / Source Attribution</h2>
            @if($sourceBreakup->isEmpty())
                <div class="p-8 text-center text-xs text-stone-400 border border-dashed border-stone-200 rounded-xl">
                    No order source data available.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-stone-100 text-[11px] font-medium uppercase tracking-[.08em] text-stone-400">
                                <th class="pb-3 pr-4">Channel / Source</th>
                                <th class="pb-3 px-4 text-center">Orders</th>
                                <th class="pb-3 pl-4 text-right">Attributed Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-stone-700">
                            @foreach($sourceBreakup as $s)
                                <tr>
                                    <td class="py-3 pr-4 font-medium text-stone-900 capitalize">{{ $s->source }}</td>
                                    <td class="py-3 px-4 text-center font-medium">{{ $s->orders_count }}</td>
                                    <td class="py-3 pl-4 text-right font-semibold text-stone-900">₹{{ number_format($s->total_revenue, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
