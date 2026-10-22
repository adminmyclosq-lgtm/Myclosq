@extends('layouts.admin', ['title' => 'Coupon ' . $coupon->code . ' | Details Hub'])

@section('content')
<div class="min-h-screen bg-stone-50/60 pb-20">
    <div class="mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8 pt-6 space-y-6">

        {{-- Top Navigation & Actions --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-stone-200">
            <div>
                <nav class="flex items-center gap-2 text-xs text-stone-500 mb-1">
                    <a href="{{ route('admin.coupons.index') }}" class="hover:text-stone-900 transition">Coupons & Promotions</a>
                    <span>/</span>
                    <span class="font-mono text-stone-900 font-bold">{{ $coupon->code }}</span>
                </nav>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-mono font-bold text-stone-900 tracking-tight flex items-center gap-2">
                        {{ $coupon->code }}
                        <button
                            type="button"
                            onclick="navigator.clipboard.writeText('{{ $coupon->code }}'); alert('Code copied!');"
                            class="text-stone-300 hover:text-stone-700 transition"
                            title="Copy code"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </h1>

                    @php
                        $st = $coupon->calculated_status;
                        $statusStyles = [
                            'active' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                            'scheduled' => 'bg-blue-50 text-blue-800 border-blue-200',
                            'paused' => 'bg-amber-50 text-amber-800 border-amber-200',
                            'expired' => 'bg-stone-100 text-stone-600 border-stone-200',
                            'draft' => 'bg-purple-50 text-purple-700 border-purple-200',
                        ];
                        $badgeClass = $statusStyles[$st] ?? 'bg-stone-100 text-stone-700 border-stone-200';
                    @endphp
                    <span class="inline-flex items-center rounded-full border px-3 py-0.5 text-xs font-semibold capitalize {{ $badgeClass }}">
                        <span class="h-1.5 w-1.5 rounded-full mr-1.5 {{ $st === 'active' ? 'bg-emerald-500' : ($st === 'paused' ? 'bg-amber-500' : 'bg-stone-400') }}"></span>
                        {{ $st }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-stone-500 mt-1">{{ $coupon->name }} @if($coupon->campaign_name) · 🏷️ {{ $coupon->campaign_name }} @endif</p>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Quick Pause / Activate Toggle --}}
                <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon) }}" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition shadow-2xs"
                    >
                        @if($coupon->status === 'active')
                            <svg class="h-3.5 w-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Pause Promotion</span>
                        @else
                            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Activate Promotion</span>
                        @endif
                    </button>
                </form>

                {{-- Edit Button --}}
                <a
                    href="{{ route('admin.coupons.edit', $coupon) }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition shadow-2xs"
                >
                    <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit</span>
                </a>

                {{-- Delete Button --}}
                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('Permanently delete coupon {{ $coupon->code }}?');">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition shadow-2xs"
                    >
                        <svg class="h-3.5 w-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Delete</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Quick Stats Strip --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs">
                <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block">Total Redemptions</span>
                <div class="text-2xl font-bold font-mono text-stone-900 mt-1">
                    {{ $coupon->usages->count() }}
                    <span class="text-xs font-normal text-stone-400">/ {{ $coupon->usage_limit ?: '∞' }}</span>
                </div>
            </div>

            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs">
                <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block">Revenue Generated</span>
                <div class="text-2xl font-bold font-mono text-stone-900 mt-1">
                    {{ $analytics['formatted_revenue'] }}
                </div>
            </div>

            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs">
                <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block">Discount Subsidized</span>
                <div class="text-2xl font-bold font-mono text-rose-600 mt-1">
                    {{ $analytics['formatted_discount'] }}
                </div>
            </div>

            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs">
                <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block">Avg Order Value (AOV)</span>
                <div class="text-2xl font-bold font-mono text-stone-900 mt-1">
                    {{ $analytics['formatted_aov'] }}
                </div>
            </div>
        </div>

        {{-- =========================================================================
             6 TABS NAVIGATION (Overview, Usage, Orders, Customers, Analytics, History)
             ========================================================================= --}}
        <div class="rounded-2xl border border-stone-200/90 bg-white shadow-2xs overflow-hidden">
            <div class="border-b border-stone-200/80 bg-stone-50/50 px-4 sm:px-6">
                <nav class="flex space-x-6 overflow-x-auto" aria-label="Tabs">
                    <button type="button" onclick="switchTab('overview')" id="tab-btn-overview" class="tab-link border-b-2 border-[#1c3d2c] py-3.5 px-1 text-xs font-bold text-stone-900 whitespace-nowrap">
                        1. Overview
                    </button>
                    <button type="button" onclick="switchTab('usage')" id="tab-btn-usage" class="tab-link border-b-2 border-transparent py-3.5 px-1 text-xs font-semibold text-stone-500 hover:text-stone-800 whitespace-nowrap">
                        2. Usage & Timeline
                    </button>
                    <button type="button" onclick="switchTab('orders')" id="tab-btn-orders" class="tab-link border-b-2 border-transparent py-3.5 px-1 text-xs font-semibold text-stone-500 hover:text-stone-800 whitespace-nowrap">
                        3. Orders ({{ $coupon->orders->count() }})
                    </button>
                    <button type="button" onclick="switchTab('customers')" id="tab-btn-customers" class="tab-link border-b-2 border-transparent py-3.5 px-1 text-xs font-semibold text-stone-500 hover:text-stone-800 whitespace-nowrap">
                        4. Customers ({{ $analytics['total_customers'] }})
                    </button>
                    <button type="button" onclick="switchTab('analytics')" id="tab-btn-analytics" class="tab-link border-b-2 border-transparent py-3.5 px-1 text-xs font-semibold text-stone-500 hover:text-stone-800 whitespace-nowrap">
                        5. BI Analytics & ROI
                    </button>
                    <button type="button" onclick="switchTab('history')" id="tab-btn-history" class="tab-link border-b-2 border-transparent py-3.5 px-1 text-xs font-semibold text-stone-500 hover:text-stone-800 whitespace-nowrap">
                        6. Audit History
                    </button>
                </nav>
            </div>

            {{-- TAB 1: OVERVIEW --}}
            <div id="tab-content-overview" class="tab-pane p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    {{-- Card: Core Configuration --}}
                    <div class="rounded-xl border border-stone-200/80 bg-stone-50/40 p-4 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-stone-400">Core Configuration</h4>
                        <dl class="divide-y divide-stone-100 text-xs">
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Coupon Code</dt>
                                <dd class="font-mono font-bold text-stone-900">{{ $coupon->code }}</dd>
                            </div>
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Name</dt>
                                <dd class="font-semibold text-stone-900 text-right">{{ $coupon->name }}</dd>
                            </div>
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Type</dt>
                                <dd class="capitalize font-medium text-stone-800">{{ str_replace('_', ' ', $coupon->discount_type) }}</dd>
                            </div>
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Discount Value</dt>
                                <dd class="font-bold text-emerald-800">{{ $coupon->formatted_discount }}</dd>
                            </div>
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Minimum Order Value</dt>
                                <dd class="font-medium text-stone-900">{{ $coupon->minimum_cart_value > 0 ? '₹' . number_format($coupon->minimum_cart_value, 2) : 'None (₹0)' }}</dd>
                            </div>
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Maximum Discount Cap</dt>
                                <dd class="font-medium text-stone-900">{{ $coupon->maximum_discount > 0 ? '₹' . number_format($coupon->maximum_discount, 2) : 'No Cap' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Card: Validity & Scheduling --}}
                    <div class="rounded-xl border border-stone-200/80 bg-stone-50/40 p-4 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-stone-400">Validity & Scheduling</h4>
                        <dl class="divide-y divide-stone-100 text-xs">
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Status</dt>
                                <dd class="font-bold capitalize text-stone-900">{{ $coupon->calculated_status }}</dd>
                            </div>
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Start Date</dt>
                                <dd class="font-medium text-stone-900">{{ $coupon->starts_at ? $coupon->starts_at->format('d M Y, h:i A') : 'Immediate' }}</dd>
                            </div>
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Expiry Date</dt>
                                <dd class="font-medium text-stone-900">{{ $coupon->expires_at ? $coupon->expires_at->format('d M Y, h:i A') : 'Never expires' }}</dd>
                            </div>
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Created Date</dt>
                                <dd class="font-medium text-stone-900">{{ $coupon->created_at->format('d M Y, h:i A') }}</dd>
                            </div>
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Created By</dt>
                                <dd class="font-medium text-stone-900">{{ $coupon->creator?->name ?? 'Admin System' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Card: Audience & Restrictions --}}
                    <div class="rounded-xl border border-stone-200/80 bg-stone-50/40 p-4 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-stone-400">Audience & Stacking</h4>
                        <dl class="divide-y divide-stone-100 text-xs">
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Target Eligibility</dt>
                                <dd class="capitalize font-semibold text-stone-900">{{ $coupon->customer_eligibility ?: 'All Customers' }}</dd>
                            </div>
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Per Customer Limit</dt>
                                <dd class="font-medium text-stone-900">{{ $coupon->per_customer_limit ?: 'Unlimited' }}</dd>
                            </div>
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Free Shipping Grant</dt>
                                <dd class="font-medium text-stone-900">{{ $coupon->free_shipping ? 'Yes (Waived)' : 'Standard' }}</dd>
                            </div>
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Promotion Stacking</dt>
                                <dd class="font-medium text-stone-900">{{ $coupon->allow_stacking ? 'Allowed' : 'Exclusive (No Stacking)' }}</dd>
                            </div>
                            <div class="py-2 flex justify-between">
                                <dt class="text-stone-500">Specific Products</dt>
                                <dd class="font-medium text-stone-900">{{ $coupon->products->count() > 0 ? $coupon->products->count() . ' SKUs' : 'All Catalog' }}</dd>
                            </div>
                        </dl>
                    </div>

                </div>

                {{-- Internal Notes & Description --}}
                @if($coupon->internal_notes || $coupon->description)
                    <div class="rounded-xl border border-stone-200 bg-white p-4 space-y-2 text-xs">
                        @if($coupon->description)
                            <div>
                                <span class="font-bold text-stone-700">Public Terms:</span>
                                <p class="text-stone-600 mt-0.5">{{ $coupon->description }}</p>
                            </div>
                        @endif
                        @if($coupon->internal_notes)
                            <div class="pt-2 border-t border-stone-100">
                                <span class="font-bold text-stone-700">Internal Admin Notes:</span>
                                <p class="text-stone-500 mt-0.5">{{ $coupon->internal_notes }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- TAB 2: USAGE --}}
            <div id="tab-content-usage" class="tab-pane hidden p-6 space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-stone-900">Recent Redemptions Timeline</h3>
                    <p class="text-xs text-stone-400 mt-0.5">Chronological record of every checkout order where this code was successfully redeemed.</p>
                </div>

                @if($coupon->usages->count() > 0)
                    <div class="overflow-x-auto rounded-xl border border-stone-200">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="bg-stone-50 border-b border-stone-200 text-[11px] font-bold uppercase text-stone-500">
                                <tr>
                                    <th class="py-3 px-4">Redemption ID</th>
                                    <th class="py-3 px-4">Customer</th>
                                    <th class="py-3 px-4">Order #</th>
                                    <th class="py-3 px-4">Discount Provided</th>
                                    <th class="py-3 px-4">Order Total</th>
                                    <th class="py-3 px-4">Used At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100 text-stone-700">
                                @foreach($coupon->usages as $usage)
                                    <tr class="hover:bg-stone-50">
                                        <td class="py-3 px-4 font-mono">#{{ $usage->id }}</td>
                                        <td class="py-3 px-4">
                                            <div class="font-semibold text-stone-900">{{ $usage->user?->name ?? 'Guest Customer' }}</div>
                                            <div class="text-[10.5px] text-stone-400">{{ $usage->user?->email ?? '-' }}</div>
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($usage->order)
                                                <a href="{{ route('admin.orders.show', $usage->order) }}" class="font-mono font-bold text-emerald-800 hover:underline">
                                                    {{ $usage->order->order_number }}
                                                </a>
                                            @else
                                                <span class="text-stone-400">Order #{{ $usage->order_id }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 font-bold text-emerald-700">
                                            ₹{{ number_format($usage->discount_amount, 2) }}
                                        </td>
                                        <td class="py-3 px-4 font-mono font-semibold text-stone-900">
                                            ₹{{ number_format($usage->order?->grand_total ?? 0, 2) }}
                                        </td>
                                        <td class="py-3 px-4 text-stone-500">
                                            {{ $usage->used_at ? Carbon\Carbon::parse($usage->used_at)->format('d M Y, h:i A') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-stone-200 p-8 text-center text-xs text-stone-400">
                        No redemptions recorded for this coupon code yet.
                    </div>
                @endif
            </div>

            {{-- TAB 3: ORDERS --}}
            <div id="tab-content-orders" class="tab-pane hidden p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-stone-900">Orders Utilizing Promotion</h3>
                        <p class="text-xs text-stone-400 mt-0.5">Every paid transaction linked with this promotional campaign.</p>
                    </div>
                    <span class="text-xs font-semibold text-stone-600 font-mono">{{ $coupon->orders->count() }} Orders</span>
                </div>

                @if($coupon->orders->count() > 0)
                    <div class="overflow-x-auto rounded-xl border border-stone-200">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="bg-stone-50 border-b border-stone-200 text-[11px] font-bold uppercase text-stone-500">
                                <tr>
                                    <th class="py-3 px-4">Order Number</th>
                                    <th class="py-3 px-4">Customer</th>
                                    <th class="py-3 px-4">Discount</th>
                                    <th class="py-3 px-4">Grand Total</th>
                                    <th class="py-3 px-4">Payment Status</th>
                                    <th class="py-3 px-4">Fulfilment Status</th>
                                    <th class="py-3 px-4">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100 text-stone-700">
                                @foreach($coupon->orders as $ord)
                                    <tr class="hover:bg-stone-50">
                                        <td class="py-3 px-4">
                                            <a href="{{ route('admin.orders.show', $ord) }}" class="font-mono font-bold text-stone-900 hover:text-emerald-800 transition">
                                                {{ $ord->order_number }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-medium text-stone-900">{{ $ord->user?->name ?? 'Guest Shopper' }}</div>
                                            <div class="text-[10.5px] text-stone-400">{{ $ord->user?->email ?? '' }}</div>
                                        </td>
                                        <td class="py-3 px-4 font-semibold text-rose-600">
                                            -₹{{ number_format($ord->discount_amount, 2) }}
                                        </td>
                                        <td class="py-3 px-4 font-mono font-bold text-stone-900">
                                            ₹{{ number_format($ord->grand_total, 2) }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex rounded-full px-2 py-0.5 text-[10.5px] font-semibold {{ $ord->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-800' : 'bg-stone-100 text-stone-600' }}">
                                                {{ ucfirst($ord->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex rounded-full px-2 py-0.5 text-[10.5px] font-semibold bg-stone-100 text-stone-700">
                                                {{ ucfirst($ord->fulfilment_status ?: 'unfulfilled') }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-stone-500">
                                            {{ $ord->created_at->format('d M Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-stone-200 p-8 text-center text-xs text-stone-400">
                        No orders recorded under this promotion yet.
                    </div>
                @endif
            </div>

            {{-- TAB 4: CUSTOMERS --}}
            <div id="tab-content-customers" class="tab-pane hidden p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-stone-900">Customers Redeeming Promotion</h3>
                        <p class="text-xs text-stone-400 mt-0.5">Cohort profile, order frequency, and lifetime spend of qualifying customers.</p>
                    </div>
                    <span class="text-xs font-semibold text-stone-600">{{ $analytics['total_customers'] }} unique buyers</span>
                </div>

                @php
                    $customerMap = [];
                    foreach ($coupon->usages as $usg) {
                        if ($usg->user) {
                            $uid = $usg->user->id;
                            if (!isset($customerMap[$uid])) {
                                $customerMap[$uid] = [
                                    'user' => $usg->user,
                                    'redemptions_count' => 0,
                                    'last_used' => $usg->used_at,
                                ];
                            }
                            $customerMap[$uid]['redemptions_count']++;
                        }
                    }
                @endphp

                @if(count($customerMap) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($customerMap as $cItem)
                            @php
                                $u = $cItem['user'];
                                $ordersCount = $u->orders()->count();
                                $lifetimeSpend = (float) $u->orders()->where('payment_status', 'paid')->sum('grand_total');
                            @endphp
                            <div class="rounded-xl border border-stone-200/90 bg-stone-50/40 p-4 space-y-2 hover:bg-white transition">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('admin.customers.show', $u) }}" class="font-semibold text-stone-900 hover:text-emerald-800 transition block truncate text-xs">
                                            {{ $u->name }}
                                        </a>
                                        <div class="text-[11px] text-stone-400 truncate">{{ $u->email }}</div>
                                    </div>
                                </div>
                                <div class="pt-2 border-t border-stone-100 grid grid-cols-3 gap-1 text-[11px]">
                                    <div>
                                        <span class="text-stone-400 block text-[10px]">Total Orders</span>
                                        <span class="font-semibold text-stone-800">{{ $ordersCount }}</span>
                                    </div>
                                    <div>
                                        <span class="text-stone-400 block text-[10px]">Lifetime Spend</span>
                                        <span class="font-semibold text-stone-800">₹{{ number_format($lifetimeSpend, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-stone-400 block text-[10px]">Redeemed</span>
                                        <span class="font-semibold text-emerald-700">{{ $cItem['redemptions_count'] }}x</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-stone-200 p-8 text-center text-xs text-stone-400">
                        No customer accounts have applied this coupon code yet.
                    </div>
                @endif
            </div>

            {{-- TAB 5: ANALYTICS --}}
            <div id="tab-content-analytics" class="tab-pane hidden p-6 space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-stone-900">Campaign Business Intelligence & ROI</h3>
                    <p class="text-xs text-stone-400 mt-0.5">Evaluation of profit margin impact, retention velocity, and geographical distribution.</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="rounded-xl border border-stone-200 bg-stone-50/50 p-4">
                        <span class="text-[11px] font-semibold text-stone-400 uppercase">Gross Revenue</span>
                        <div class="text-xl font-bold font-mono text-stone-900 mt-1">{{ $analytics['formatted_revenue'] }}</div>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-stone-50/50 p-4">
                        <span class="text-[11px] font-semibold text-stone-400 uppercase">Discount Cost</span>
                        <div class="text-xl font-bold font-mono text-rose-600 mt-1">{{ $analytics['formatted_discount'] }}</div>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-stone-50/50 p-4">
                        <span class="text-[11px] font-semibold text-stone-400 uppercase">Net Revenue Captured</span>
                        <div class="text-xl font-bold font-mono text-emerald-800 mt-1">{{ $analytics['formatted_net_revenue'] }}</div>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-stone-50/50 p-4">
                        <span class="text-[11px] font-semibold text-stone-400 uppercase">Calculated ROI</span>
                        <div class="text-xl font-bold font-mono text-stone-900 mt-1">{{ $analytics['roi'] }}%</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    {{-- Top Products Purchased --}}
                    <div class="rounded-xl border border-stone-200 p-4 space-y-3">
                        <h4 class="text-xs font-bold text-stone-900 uppercase tracking-wider">Top Products Purchased with Code</h4>
                        @forelse($analytics['top_products'] as $tp)
                            <div class="flex items-center justify-between text-xs py-1.5 border-b border-stone-100 last:border-none">
                                <span class="font-medium text-stone-800 truncate max-w-[240px]">{{ $tp->name }}</span>
                                <div class="text-right">
                                    <span class="font-bold text-stone-900">₹{{ number_format($tp->revenue, 2) }}</span>
                                    <span class="text-stone-400 text-[10.5px]">({{ $tp->units_sold }} units)</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-stone-400">No product orders recorded yet.</p>
                        @endforelse
                    </div>

                    {{-- Top Locations --}}
                    <div class="rounded-xl border border-stone-200 p-4 space-y-3">
                        <h4 class="text-xs font-bold text-stone-900 uppercase tracking-wider">Top Shipping Geographies</h4>
                        @forelse($analytics['top_locations'] as $loc)
                            <div class="flex items-center justify-between text-xs py-1.5 border-b border-stone-100 last:border-none">
                                <span class="font-medium text-stone-800">{{ $loc->state_name }}</span>
                                <div class="text-right">
                                    <span class="font-bold text-stone-900">{{ $loc->orders_count }} orders</span>
                                    <span class="text-stone-400 text-[10.5px]">(₹{{ number_format($loc->revenue, 2) }})</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-stone-400">No geographical data recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- TAB 6: HISTORY --}}
            <div id="tab-content-history" class="tab-pane hidden p-6 space-y-4">
                <div>
                    <h3 class="text-sm font-bold text-stone-900">Audit & Modification Timeline</h3>
                    <p class="text-xs text-stone-400 mt-0.5">Immutable audit trail of administrator modifications and status toggles.</p>
                </div>

                <div class="space-y-4 relative before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-stone-200 pl-6">
                    <div class="relative">
                        <span class="absolute -left-6 top-1.5 h-2.5 w-2.5 rounded-full bg-emerald-600 ring-4 ring-white"></span>
                        <div class="text-xs font-bold text-stone-900">Coupon Created & Published</div>
                        <div class="text-[11px] text-stone-400 mt-0.5">{{ $coupon->created_at->format('d M Y, h:i A') }} by {{ $coupon->creator?->name ?? 'Admin System' }}</div>
                        <p class="text-xs text-stone-600 mt-1">Initialized promotion with discount {{ $coupon->formatted_discount }} and code {{ $coupon->code }}.</p>
                    </div>

                    @if($coupon->updated_at->gt($coupon->created_at))
                        <div class="relative">
                            <span class="absolute -left-6 top-1.5 h-2.5 w-2.5 rounded-full bg-blue-600 ring-4 ring-white"></span>
                            <div class="text-xs font-bold text-stone-900">Coupon Parameters Updated</div>
                            <div class="text-[11px] text-stone-400 mt-0.5">{{ $coupon->updated_at->format('d M Y, h:i A') }}</div>
                            <p class="text-xs text-stone-600 mt-1">Rule parameters or expiration settings reconfigured by administration.</p>
                        </div>
                    @endif

                    @if($coupon->status === 'paused')
                        <div class="relative">
                            <span class="absolute -left-6 top-1.5 h-2.5 w-2.5 rounded-full bg-amber-500 ring-4 ring-white"></span>
                            <div class="text-xs font-bold text-stone-900">Promotion Paused</div>
                            <div class="text-[11px] text-stone-400 mt-0.5">{{ $coupon->updated_at->format('d M Y, h:i A') }}</div>
                            <p class="text-xs text-stone-600 mt-1">Deactivated temporarily from active checkout sessions.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    function switchTab(tabId) {
        // Hide all tab panes
        document.querySelectorAll('.tab-pane').forEach(function(pane) {
            pane.classList.add('hidden');
        });
        // Deactivate all tab links
        document.querySelectorAll('.tab-link').forEach(function(btn) {
            btn.classList.remove('border-[#1c3d2c]', 'text-stone-900', 'font-bold');
            btn.classList.add('border-transparent', 'text-stone-500', 'font-semibold');
        });

        // Show target pane
        var activePane = document.getElementById('tab-content-' + tabId);
        if (activePane) {
            activePane.classList.remove('hidden');
        }

        // Highlight active tab button
        var activeBtn = document.getElementById('tab-btn-' + tabId);
        if (activeBtn) {
            activeBtn.classList.remove('border-transparent', 'text-stone-500', 'font-semibold');
            activeBtn.classList.add('border-[#1c3d2c]', 'text-stone-900', 'font-bold');
        }
    }
</script>
@endsection
