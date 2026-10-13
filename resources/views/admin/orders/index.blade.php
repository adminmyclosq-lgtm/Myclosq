@extends('layouts.admin')
@section('content')
<div class="p-4 sm:p-6 lg:p-8 max-w-full space-y-5">
    {{-- Page Header & Top Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="serif text-2xl md:text-3xl font-semibold text-stone-900 tracking-tight">All Orders &amp; Fulfilment</h1>
            <p class="mt-1 text-xs md:text-sm text-stone-500">Monitor incoming customer orders, payment status, refunds, and fulfilment tracking.</p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <button type="button" id="top-clear-filters-btn" onclick="resetAllFilters()" class="{{ ($activeFilters ?? 0) > 0 ? '' : 'hidden' }} inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs font-medium text-stone-600 shadow-2xs hover:bg-stone-50 hover:text-stone-900 transition">
                <svg class="h-3.5 w-3.5 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                <span>Clear Filters (<span id="top-clear-count">{{ $activeFilters ?? 0 }}</span>)</span>
            </button>
            <button type="button" id="toggle-filters-btn" onclick="toggleFilters()" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-stone-300 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 hover:text-stone-900 transition cursor-pointer" title="Toggle Filters Visibility">
                <svg id="toggle-filters-icon" class="h-3.5 w-3.5 text-stone-500 transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m18 15-6-6-6 6"/></svg>
                <span id="toggle-filters-label">Hide Filters</span>
                <span id="toggle-filters-badge" class="{{ ($activeFilters ?? 0) > 0 ? '' : 'hidden' }} ml-0.5 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-stone-900 px-1 text-[10px] font-bold text-white">{{ $activeFilters ?? 0 }}</span>
            </button>
            <a id="orders-export-btn" class="inline-flex items-center justify-center gap-2 rounded-xl border border-stone-300 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 hover:text-stone-900 transition" href="{{ route('admin.orders.export', request()->query()) }}">
                <svg class="h-4 w-4 text-stone-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    {{-- Session alerts --}}
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50/90 p-3 text-xs text-emerald-800 border border-emerald-200 flex items-center gap-2.5 shadow-2xs">
            <svg class="h-4 w-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="rounded-xl bg-blue-50/90 p-3 text-xs text-blue-800 border border-blue-200 flex items-center gap-2.5 shadow-2xs">
            <svg class="h-4 w-4 text-blue-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl bg-rose-50/90 p-3 text-xs text-rose-800 border border-rose-200 shadow-2xs">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Operations Dashboard KPI Cards (Always visible & filter-synced) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3 sm:gap-3.5">
        {{-- 1. Total Orders --}}
        <div class="group relative rounded-2xl border border-stone-200/90 bg-white p-3.5 sm:p-4 shadow-2xs hover:border-stone-300 hover:shadow-xs transition duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-1 mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-stone-500">Total Orders</span>
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-stone-100 text-stone-600">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-2Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </span>
                </div>
                <div id="kpi-total-orders" class="text-xl sm:text-2xl font-bold tracking-tight text-stone-900 font-mono">
                    {{ number_format($kpis['total_orders'] ?? 0) }}
                </div>
            </div>
            <div id="kpi-cohort-label" class="mt-2 pt-2 border-t border-stone-100 flex items-center text-[10.5px] text-stone-500 font-medium truncate">
                @if(($activeFilters ?? 0) > 0)
                    <span class="inline-flex items-center text-amber-700 font-semibold gap-1 truncate">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500 shrink-0"></span>
                        Filtered cohort
                    </span>
                @else
                    <span class="truncate">All records</span>
                @endif
            </div>
        </div>

        {{-- 2. Net Sales --}}
        <div class="group relative rounded-2xl border border-stone-200/90 bg-white p-3.5 sm:p-4 shadow-2xs hover:border-stone-300 hover:shadow-xs transition duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-1 mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-stone-500">Net Sales</span>
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </span>
                </div>
                <div id="kpi-net-sales" class="text-xl sm:text-2xl font-bold tracking-tight text-emerald-950 font-mono truncate" title="₹{{ number_format($kpis['net_sales'] ?? 0, 2) }}">
                    ₹{{ number_format($kpis['net_sales'] ?? 0, 2) }}
                </div>
            </div>
            <div id="kpi-gross-sales-sub" class="mt-2 pt-2 border-t border-stone-100 flex items-center text-[10.5px] text-stone-500 font-medium truncate" title="Gross: ₹{{ number_format($kpis['gross_sales'] ?? 0, 2) }}">
                <span class="truncate">Gross ₹{{ number_format($kpis['gross_sales'] ?? 0, 2) }}</span>
            </div>
        </div>

        {{-- 3. Pending Payments --}}
        <div class="group relative rounded-2xl border border-stone-200/90 bg-white p-3.5 sm:p-4 shadow-2xs hover:border-stone-300 hover:shadow-xs transition duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-1 mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-stone-500">Pending Pay</span>
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </span>
                </div>
                <div id="kpi-pending-payments" class="text-xl sm:text-2xl font-bold tracking-tight text-amber-950 font-mono">
                    {{ number_format($kpis['pending_payments_count'] ?? 0) }}
                </div>
            </div>
            <div id="kpi-pending-payments-sub" class="mt-2 pt-2 border-t border-stone-100 flex items-center text-[10.5px] text-stone-500 font-medium truncate" title="Uncollected: ₹{{ number_format($kpis['pending_payments_amount'] ?? 0, 2) }}">
                <span class="truncate">₹{{ number_format($kpis['pending_payments_amount'] ?? 0, 2) }} uncollected</span>
            </div>
        </div>

        {{-- 4. Pending Fulfillment --}}
        <div class="group relative rounded-2xl border border-stone-200/90 bg-white p-3.5 sm:p-4 shadow-2xs hover:border-stone-300 hover:shadow-xs transition duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-1 mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-stone-500">Pending Fulfil</span>
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/><path d="m7.5 4.27 9 5.15"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" y1="22" x2="12" y2="12"/></svg>
                    </span>
                </div>
                <div id="kpi-pending-fulfillment" class="text-xl sm:text-2xl font-bold tracking-tight text-indigo-950 font-mono">
                    {{ number_format($kpis['pending_fulfillment_count'] ?? 0) }}
                </div>
            </div>
            <div class="mt-2 pt-2 border-t border-stone-100 flex items-center text-[10.5px] text-stone-500 font-medium truncate">
                <span class="truncate">Awaiting dispatch</span>
            </div>
        </div>

        {{-- 5. Average Order Value --}}
        <div class="group relative rounded-2xl border border-stone-200/90 bg-white p-3.5 sm:p-4 shadow-2xs hover:border-stone-300 hover:shadow-xs transition duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-1 mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-stone-500">Avg Order Value</span>
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-violet-50 text-violet-700">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </span>
                </div>
                <div id="kpi-average-order-value" class="text-xl sm:text-2xl font-bold tracking-tight text-violet-950 font-mono truncate" title="₹{{ number_format($kpis['average_order_value'] ?? 0, 2) }}">
                    ₹{{ number_format($kpis['average_order_value'] ?? 0, 2) }}
                </div>
            </div>
            <div class="mt-2 pt-2 border-t border-stone-100 flex items-center text-[10.5px] text-stone-500 font-medium truncate">
                <span class="truncate">Per paid order</span>
            </div>
        </div>

        {{-- 6. Refunds --}}
        <div class="group relative rounded-2xl border border-stone-200/90 bg-white p-3.5 sm:p-4 shadow-2xs hover:border-stone-300 hover:shadow-xs transition duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-1 mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-stone-500">Refunds</span>
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-rose-50 text-rose-700">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    </span>
                </div>
                <div id="kpi-refunds-count" class="text-xl sm:text-2xl font-bold tracking-tight text-rose-950 font-mono">
                    {{ number_format($kpis['refunds_count'] ?? 0) }}
                </div>
            </div>
            <div id="kpi-refunds-amount-sub" class="mt-2 pt-2 border-t border-stone-100 flex items-center text-[10.5px] text-stone-500 font-medium truncate" title="Total: ₹{{ number_format($kpis['refunds_amount'] ?? 0, 2) }}">
                <span class="truncate">₹{{ number_format($kpis['refunds_amount'] ?? 0, 2) }} returned</span>
            </div>
        </div>

        {{-- 7. Active Reset Cycles --}}
        <div class="group relative rounded-2xl border border-stone-200/90 bg-white p-3.5 sm:p-4 shadow-2xs hover:border-stone-300 hover:shadow-xs transition duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-1 mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-stone-500">Reset Cycles</span>
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-teal-50 text-teal-700">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/><path d="M16 2v4"/><path d="M8 2v4"/></svg>
                    </span>
                </div>
                <div id="kpi-active-reset-cycles" class="text-xl sm:text-2xl font-bold tracking-tight text-teal-950 font-mono">
                    {{ number_format($kpis['active_reset_cycles'] ?? 0) }}
                </div>
            </div>
            <div class="mt-2 pt-2 border-t border-stone-100 flex items-center text-[10.5px] text-stone-500 font-medium truncate">
                <span class="truncate">Active 30-day cohorts</span>
            </div>
        </div>
    </div>

    {{-- Collapsible Filters Wrapper --}}
    <div id="filters-collapse-wrapper" class="transition-all duration-300 ease-in-out overflow-hidden" style="max-height: 1500px; opacity: 1;">
        {{-- Advanced Multi-Filter Form --}}
        <form method="GET" action="{{ route('admin.orders.index') }}" id="order-filters-form" class="rounded-2xl border border-stone-200/90 bg-white p-4 md:p-5 shadow-xs space-y-4">
        {{-- Row 1: Search Bar & Date Presets --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3.5 border-b border-stone-100 pb-4">
            {{-- Search Bar --}}
            <div class="relative w-full lg:w-96">
                <svg class="absolute left-3.5 top-2.5 h-4 w-4 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="search" id="filter-search-input" value="{{ request('search') }}" placeholder="Search by Order ID, Customer name, email, mobile..." class="w-full rounded-xl border border-stone-200 bg-stone-50/60 py-2 pl-9 pr-3 text-xs text-stone-800 placeholder:text-stone-400 transition hover:bg-stone-50 focus:border-stone-400 focus:bg-white focus:outline-none">
            </div>

            {{-- Date Presets & Custom Range --}}
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400 mr-1">Date:</span>
                @php $currentRange = request('date_range', 'all'); @endphp
                <div class="inline-flex rounded-xl bg-stone-100/90 p-1 text-xs">
                    @foreach([
                        'all' => 'All Time',
                        'today' => 'Today',
                        'last_7_days' => 'Last 7 Days',
                        'last_30_days' => 'Last 30 Days',
                        'custom' => 'Custom'
                    ] as $rKey => $rLabel)
                        <button type="button" 
                            onclick="setDateRange('{{ $rKey }}')"
                            class="date-pill rounded-lg px-3 py-1 font-medium transition text-xs {{ $currentRange === $rKey ? 'bg-stone-900 text-white shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}">
                            {{ $rLabel }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="date_range" id="filter-date-range" value="{{ $currentRange }}">

                {{-- Custom Date Inputs --}}
                <div id="custom-date-container" class="flex items-center gap-1.5 {{ $currentRange === 'custom' ? '' : 'hidden' }}">
                    <input type="date" name="from_date" id="filter-from-date" value="{{ request('from_date') }}" onchange="triggerFilterUpdate()" class="rounded-xl border border-stone-200 bg-white px-2.5 py-1 text-xs text-stone-700 focus:border-stone-400 focus:outline-none" title="From Date">
                    <span class="text-stone-400 text-xs font-medium">to</span>
                    <input type="date" name="to_date" id="filter-to-date" value="{{ request('to_date') }}" onchange="triggerFilterUpdate()" class="rounded-xl border border-stone-200 bg-white px-2.5 py-1 text-xs text-stone-700 focus:border-stone-400 focus:outline-none" title="To Date">
                </div>
            </div>
        </div>

        {{-- Row 2: Status & Operational Dropdown Filters Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            {{-- Payment Status --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-stone-500 mb-1">Payment</label>
                <select name="payment_status" id="filter-payment-status" onchange="triggerFilterUpdate()" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 py-2 text-xs font-medium text-stone-700 transition hover:bg-stone-50 focus:bg-white focus:border-stone-400 focus:outline-none cursor-pointer">
                    <option value="">All Payments</option>
                    @foreach(['pending' => 'Pending', 'paid' => 'Paid', 'partially_refunded' => 'Partially Refunded', 'refunded' => 'Refunded', 'failed' => 'Failed', 'cancelled' => 'Cancelled'] as $pval => $plabel)
                        <option value="{{ $pval }}" @selected(request('payment_status') === $pval)>{{ $plabel }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fulfilment Status --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-stone-500 mb-1">Fulfilment</label>
                <select name="fulfilment_status" id="filter-fulfilment-status" onchange="triggerFilterUpdate()" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 py-2 text-xs font-medium text-stone-700 transition hover:bg-stone-50 focus:bg-white focus:border-stone-400 focus:outline-none cursor-pointer">
                    <option value="">All Fulfilment</option>
                    @foreach(['pending' => 'Pending', 'processing' => 'Processing', 'packed' => 'Packed', 'dispatched' => 'Dispatched', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $fval => $flabel)
                        <option value="{{ $fval }}" @selected((request('fulfilment_status') ?: request('status')) === $fval)>{{ $flabel }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Delivery Status --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-stone-500 mb-1">Delivery</label>
                <select name="shipment_status" id="filter-shipment-status" onchange="triggerFilterUpdate()" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 py-2 text-xs font-medium text-stone-700 transition hover:bg-stone-50 focus:bg-white focus:border-stone-400 focus:outline-none cursor-pointer">
                    <option value="">All Delivery</option>
                    @foreach(['pending' => 'Pending', 'dispatched' => 'Dispatched', 'in_transit' => 'In Transit', 'out_for_delivery' => 'Out for Delivery', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled', 'failed' => 'Failed'] as $sval => $slabel)
                        <option value="{{ $sval }}" @selected(request('shipment_status') === $sval)>{{ $slabel }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Payment Method --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-stone-500 mb-1">Method</label>
                <select name="payment_method" id="filter-payment-method" onchange="triggerFilterUpdate()" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 py-2 text-xs font-medium text-stone-700 transition hover:bg-stone-50 focus:bg-white focus:border-stone-400 focus:outline-none cursor-pointer">
                    <option value="">All Methods</option>
                    @foreach($paymentMethods ?? ['razorpay', 'cod', 'manual'] as $method)
                        <option value="{{ $method }}" @selected(request('payment_method') === $method)>{{ ucfirst($method) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sales Source --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-stone-500 mb-1">Source</label>
                <select name="source" id="filter-source" onchange="triggerFilterUpdate()" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 py-2 text-xs font-medium text-stone-700 transition hover:bg-stone-50 focus:bg-white focus:border-stone-400 focus:outline-none cursor-pointer">
                    <option value="">All Sources</option>
                    @foreach(['website' => 'Website', 'instagram' => 'Instagram', 'twitter' => 'Twitter / X', 'whatsapp' => 'WhatsApp', 'amazon' => 'Amazon', 'direct' => 'Direct', 'other' => 'Other'] as $srcVal => $srcLabel)
                        <option value="{{ $srcVal }}" @selected(request('source') === $srcVal)>{{ $srcLabel }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Refund Status --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-stone-500 mb-1">Refund</label>
                <select name="refund_status" id="filter-refund-status" onchange="triggerFilterUpdate()" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 py-2 text-xs font-medium text-stone-700 transition hover:bg-stone-50 focus:bg-white focus:border-stone-400 focus:outline-none cursor-pointer">
                    <option value="">All Refunds</option>
                    <option value="none" @selected(request('refund_status') === 'none')>No Refund</option>
                    <option value="partially_refunded" @selected(request('refund_status') === 'partially_refunded')>Partially Refunded</option>
                    <option value="refunded" @selected(request('refund_status') === 'refunded')>Fully Refunded</option>
                </select>
            </div>
        </div>

        {{-- Row 3: Geographic / Location Hierarchy Cascading Dropdowns --}}
        <div class="border-t border-stone-100 pt-3">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500 flex items-center gap-1.5">
                    <svg class="h-3 w-3 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    Shipping Location Hierarchy (Country &rarr; State &rarr; City &rarr; Area &rarr; Postal Code)
                </span>
                <span id="cascade-live-pill" class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full flex items-center gap-1">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Cascading &amp; Instant AJAX Active
                </span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
                {{-- Country --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-stone-500 mb-1">Country</label>
                    <select name="country" id="filter-country" onchange="handleLocationFilterChange('country')" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 py-2 text-xs font-medium text-stone-700 transition hover:bg-stone-50 focus:bg-white focus:border-stone-400 focus:outline-none cursor-pointer">
                        <option value="">All Countries</option>
                        @foreach($locationOptions['countries'] ?? ['India'] as $c)
                            <option value="{{ $c }}" @selected(request('country') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- State --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-stone-500 mb-1">State</label>
                    <select name="state" id="filter-state" onchange="handleLocationFilterChange('state')" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 py-2 text-xs font-medium text-stone-700 transition hover:bg-stone-50 focus:bg-white focus:border-stone-400 focus:outline-none cursor-pointer">
                        <option value="">All States</option>
                        @foreach($locationOptions['states'] ?? [] as $st)
                            <option value="{{ $st }}" @selected(request('state') === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- City --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-stone-500 mb-1">City</label>
                    <select name="city" id="filter-city" onchange="handleLocationFilterChange('city')" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 py-2 text-xs font-medium text-stone-700 transition hover:bg-stone-50 focus:bg-white focus:border-stone-400 focus:outline-none cursor-pointer">
                        <option value="">All Cities</option>
                        @foreach($locationOptions['cities'] ?? [] as $ct)
                            <option value="{{ $ct }}" @selected(request('city') === $ct)>{{ $ct }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Area --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-stone-500 mb-1">Area / Locality</label>
                    <select name="area" id="filter-area" onchange="handleLocationFilterChange('area')" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 py-2 text-xs font-medium text-stone-700 transition hover:bg-stone-50 focus:bg-white focus:border-stone-400 focus:outline-none cursor-pointer">
                        <option value="">All Areas</option>
                        @foreach($locationOptions['areas'] ?? [] as $ar)
                            <option value="{{ $ar }}" @selected(request('area') === $ar)>{{ $ar }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Postal Code --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-stone-500 mb-1">Postal Code</label>
                    <select name="postal_code" id="filter-postal-code" onchange="handleLocationFilterChange('postal_code')" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 py-2 text-xs font-medium text-stone-700 transition hover:bg-stone-50 focus:bg-white focus:border-stone-400 focus:outline-none cursor-pointer">
                        <option value="">All Postal Codes</option>
                        @foreach($locationOptions['postal_codes'] ?? [] as $pc)
                            <option value="{{ $pc }}" @selected(request('postal_code') === $pc)>{{ $pc }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Row 4: Submit, Reset, and Live Counter --}}
        <div class="flex items-center justify-between pt-1 border-t border-stone-100">
            <span class="text-xs font-medium text-stone-500">
                Showing <strong id="orders-total-counter" class="text-stone-900 font-semibold">{{ $orders->total() }}</strong> matching order{{ $orders->total() === 1 ? '' : 's' }}
            </span>
            <div class="flex items-center gap-3">
                <button type="button" id="reset-filters-btn" onclick="resetAllFilters()" class="{{ ($activeFilters ?? 0) > 0 ? '' : 'hidden' }} text-xs font-medium text-stone-500 hover:text-stone-900 transition underline underline-offset-2">
                    Reset all
                </button>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-stone-900 px-4 py-2 text-xs font-semibold text-white shadow-2xs hover:bg-stone-800 transition">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Apply Filters
                </button>
            </div>
        </div>
    </form>
    </div>

    {{-- Bulk Action Bar (Appears when rows are selected) --}}
    <form id="bulk-action-form" method="POST" action="{{ route('admin.orders.bulk-action') }}" class="hidden rounded-2xl border border-stone-800 bg-stone-900/95 backdrop-blur-md p-3 px-4 text-white shadow-lg flex-wrap items-center justify-between gap-3 transition">
        @csrf
        <div class="flex items-center gap-3 text-xs">
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/20 text-xs font-bold" id="selected-count">0</span>
            <span class="font-medium text-stone-200">orders selected</span>
            <button type="button" onclick="deselectAll()" class="text-stone-400 hover:text-white underline ml-1 text-xs">Deselect all</button>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 text-xs">
            {{-- Bulk Fulfilment --}}
            <div class="flex items-center gap-1.5">
                <select name="fulfilment_status" id="bulk-fulfilment-select" class="rounded-xl border border-stone-700 bg-stone-800 px-3 py-1.5 text-xs text-white outline-none">
                    <option value="">Update Fulfilment to...</option>
                    <option value="processing">Processing</option>
                    <option value="packed">Packed</option>
                    <option value="dispatched">Dispatched</option>
                    <option value="delivered">Delivered</option>
                </select>
                <button type="button" onclick="submitBulk('update_fulfilment')" class="rounded-xl bg-white/10 hover:bg-white/20 px-3 py-1.5 text-xs font-medium text-white transition">
                    Apply
                </button>
            </div>

            {{-- Bulk Source --}}
            <div class="flex items-center gap-1.5">
                <select name="source" id="bulk-source-select" class="rounded-xl border border-stone-700 bg-stone-800 px-3 py-1.5 text-xs text-white outline-none">
                    <option value="">Update Source to...</option>
                    <option value="website">Website</option>
                    <option value="instagram">Instagram</option>
                    <option value="twitter">Twitter / X</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="amazon">Amazon</option>
                    <option value="direct">Direct</option>
                </select>
                <button type="button" onclick="submitBulk('update_source')" class="rounded-xl bg-white/10 hover:bg-white/20 px-3 py-1.5 text-xs font-medium text-white transition">
                    Apply
                </button>
            </div>

            {{-- Bulk Export Selected --}}
            <button type="button" onclick="submitBulk('export')" class="rounded-xl bg-emerald-600 hover:bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-white transition inline-flex items-center gap-1.5">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export Selected
            </button>
            <input type="hidden" name="action" id="bulk-action-type" value="">
        </div>
    </form>

    {{-- Main Orders Table Card with Shimmer / Loading State --}}
    <div id="orders-table-card" class="rounded-2xl border border-stone-200/90 bg-white shadow-xs overflow-hidden relative">
        <div id="orders-loading-overlay" class="hidden absolute inset-0 bg-white/70 backdrop-blur-2xs z-30 flex items-center justify-center transition-opacity duration-150">
            <div class="inline-flex items-center gap-2 rounded-xl bg-stone-900/90 text-white px-4 py-2 text-xs font-medium shadow-lg">
                <svg class="h-4 w-4 animate-spin text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                <span>Updating orders &amp; KPIs...</span>
            </div>
        </div>
        <div id="orders-table-content">
            @include('admin.orders.partials.orders_table', ['orders' => $orders, 'activeFilters' => $activeFilters])
        </div>
    </div>

    {{-- Pagination Container --}}
    <div id="orders-pagination-container" class="mt-4">
        @include('admin.orders.partials.orders_pagination', ['orders' => $orders])
    </div>
</div>

{{-- MODAL 1: Update Fulfilment Modal --}}
<div id="fulfilment-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-stone-900/50 backdrop-blur-sm p-4">
    <div class="w-full max-w-md rounded-2xl border border-stone-200 bg-white p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-stone-100 pb-3">
            <div>
                <h3 class="text-base font-bold text-stone-900">Update Fulfilment Stage</h3>
                <p class="text-xs text-stone-500 mt-0.5" id="fulfilment-modal-order-number">Order: #</p>
            </div>
            <button type="button" onclick="closeModal('fulfilment-modal')" class="text-stone-400 hover:text-stone-600 text-sm font-semibold">✕</button>
        </div>

        <form id="fulfilment-form" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="space-y-3.5 text-xs">
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Fulfilment Status</label>
                    <select name="fulfilment_status" id="modal-fulfilment-status" class="w-full rounded-xl border border-stone-300 p-2.5 text-xs text-stone-800 focus:outline-none focus:border-stone-900">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="packed">Packed</option>
                        <option value="dispatched">Dispatched</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Delivery / Shipment Status</label>
                    <select name="shipment_status" id="modal-shipment-status" class="w-full rounded-xl border border-stone-300 p-2.5 text-xs text-stone-800 focus:outline-none focus:border-stone-900">
                        <option value="">Auto-sync with Fulfilment</option>
                        <option value="pending">Pending</option>
                        <option value="dispatched">Dispatched</option>
                        <option value="in_transit">In Transit</option>
                        <option value="out_for_delivery">Out for Delivery</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-2.5 border-t border-stone-100 pt-3">
                <button type="button" onclick="closeModal('fulfilment-modal')" class="rounded-xl border border-stone-300 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition">Cancel</button>
                <button type="submit" class="rounded-xl bg-stone-900 px-4 py-2 text-xs font-semibold text-white hover:bg-stone-800 transition">Update Status</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 2: Cancel Order Modal --}}
<div id="cancel-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-stone-900/50 backdrop-blur-sm p-4">
    <div class="w-full max-w-md rounded-2xl border border-rose-200 bg-white p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-rose-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-rose-900">Cancel Order</h3>
                    <p class="text-xs text-rose-500 mt-0.5" id="cancel-modal-order-number">Order: #</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('cancel-modal')" class="text-stone-400 hover:text-stone-600 text-sm font-semibold">✕</button>
        </div>

        <form id="cancel-form" method="POST" action="">
            @csrf
            <div class="space-y-3.5 text-xs">
                <p class="text-stone-600">Are you sure you want to cancel this order? This will mark the fulfilment and shipment as cancelled.</p>
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Cancellation Reason</label>
                    <input type="text" name="reason" placeholder="Customer request / Out of stock..." class="w-full rounded-xl border border-stone-300 p-2.5 text-xs text-stone-800 focus:outline-none focus:border-stone-900" required>
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="restock" id="cancel-restock" value="1" checked class="h-4 w-4 rounded border-stone-300 text-stone-900">
                    <label for="cancel-restock" class="text-stone-700 select-none font-medium">Restock purchased items back to inventory</label>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-2.5 border-t border-stone-100 pt-3">
                <button type="button" onclick="closeModal('cancel-modal')" class="rounded-xl border border-stone-300 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition">Keep Order</button>
                <button type="submit" class="rounded-xl bg-rose-600 hover:bg-rose-700 px-4 py-2 text-xs font-semibold text-white transition">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 3: Initiate Refund Modal --}}
<div id="refund-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-stone-900/50 backdrop-blur-sm p-4">
    <div class="w-full max-w-md rounded-2xl border border-purple-200 bg-white p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-purple-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 text-purple-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-purple-900">Initiate Order Refund</h3>
                    <p class="text-xs text-purple-500 mt-0.5" id="refund-modal-order-number">Order: #</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('refund-modal')" class="text-stone-400 hover:text-stone-600 text-sm font-semibold">✕</button>
        </div>

        <form id="refund-form" method="POST" action="">
            @csrf
            <div class="space-y-3.5 text-xs">
                <div class="rounded-xl bg-purple-50 p-3 border border-purple-100 grid grid-cols-2 gap-2 text-purple-900">
                    <div>
                        <span class="block text-[10px] text-purple-500 uppercase font-bold">Total Order</span>
                        <span class="text-sm font-bold font-mono" id="refund-modal-total">₹0.00</span>
                    </div>
                    <div>
                        <span class="block text-[10px] text-purple-500 uppercase font-bold">Refundable Balance</span>
                        <span class="text-sm font-bold font-mono text-emerald-700" id="refund-modal-balance">₹0.00</span>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Refund Type</label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="flex items-center gap-2 rounded-xl border border-stone-200 p-2.5 cursor-pointer hover:bg-stone-50 font-medium">
                            <input type="radio" name="refund_type" value="full" checked onchange="toggleRefundType('full')">
                            <span>Full Refund</span>
                        </label>
                        <label class="flex items-center gap-2 rounded-xl border border-stone-200 p-2.5 cursor-pointer hover:bg-stone-50 font-medium">
                            <input type="radio" name="refund_type" value="partial" onchange="toggleRefundType('partial')">
                            <span>Partial Refund</span>
                        </label>
                    </div>
                </div>

                <div id="refund-amount-container" class="hidden">
                    <label class="block font-semibold text-stone-700 mb-1">Custom Refund Amount (₹)</label>
                    <input type="number" step="0.01" min="0.01" name="amount" id="refund-amount-input" class="w-full rounded-xl border border-stone-300 p-2.5 text-xs text-stone-800 focus:outline-none focus:border-stone-900 font-mono" placeholder="0.00">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Refund Mode</label>
                    <select name="mode" class="w-full rounded-xl border border-stone-300 p-2.5 text-xs text-stone-800 focus:outline-none focus:border-stone-900">
                        <option value="online">Online Gateway (Razorpay)</option>
                        <option value="manual">Manual / Offline Record</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Reason for Refund</label>
                    <textarea name="reason" rows="2" placeholder="Customer return request, defect, cancellation..." class="w-full rounded-xl border border-stone-300 p-2.5 text-xs text-stone-800 focus:outline-none focus:border-stone-900" required></textarea>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-2.5 border-t border-stone-100 pt-3">
                <button type="button" onclick="closeModal('refund-modal')" class="rounded-xl border border-stone-300 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition">Cancel</button>
                <button type="submit" class="rounded-xl bg-purple-700 hover:bg-purple-800 px-4 py-2 text-xs font-semibold text-white transition">Process Refund</button>
            </div>
        </form>
    </div>
</div>

<script>
// Number and Currency formatters for Indian locale
function formatNumber(num) {
    return new Intl.NumberFormat('en-IN').format(Math.round(num || 0));
}

function formatCurrency(num) {
    return new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(num || 0));
}

// In-flight fetch controller
let activeFilterController = null;
let searchDebounceTimer = null;

// Helper to update a select element's options dynamically
function updateSelectOptions(selectId, options, placeholderText) {
    const sel = document.getElementById(selectId);
    if (!sel) return;

    const currentVal = sel.value;
    sel.innerHTML = '';

    const defaultOpt = document.createElement('option');
    defaultOpt.value = '';
    defaultOpt.textContent = placeholderText;
    sel.appendChild(defaultOpt);

    let foundMatch = false;
    (options || []).forEach(opt => {
        if (!opt) return;
        const optionEl = document.createElement('option');
        optionEl.value = opt;
        optionEl.textContent = opt;
        if (opt === currentVal) {
            optionEl.selected = true;
            foundMatch = true;
        }
        sel.appendChild(optionEl);
    });

    if (!foundMatch && currentVal) {
        sel.value = '';
    }
}

// Cascading Level Handler
function handleLocationFilterChange(changedLevel) {
    const stateSel = document.getElementById('filter-state');
    const citySel = document.getElementById('filter-city');
    const areaSel = document.getElementById('filter-area');
    const postalSel = document.getElementById('filter-postal-code');

    if (changedLevel === 'country') {
        if (stateSel) stateSel.value = '';
        if (citySel) citySel.value = '';
        if (areaSel) areaSel.value = '';
        if (postalSel) postalSel.value = '';
    } else if (changedLevel === 'state') {
        if (citySel) citySel.value = '';
        if (areaSel) areaSel.value = '';
        if (postalSel) postalSel.value = '';
    } else if (changedLevel === 'city') {
        if (areaSel) areaSel.value = '';
        if (postalSel) postalSel.value = '';
    } else if (changedLevel === 'area') {
        if (postalSel) postalSel.value = '';
    }

    // Trigger immediate AJAX filter update
    triggerFilterUpdate(1);
}

// Core AJAX Filter & Table Refresh Function
function triggerFilterUpdate(page = 1) {
    const form = document.getElementById('order-filters-form');
    if (!form) return;

    const overlay = document.getElementById('orders-loading-overlay');
    if (overlay) overlay.classList.remove('hidden');

    if (activeFilterController) {
        activeFilterController.abort();
    }
    activeFilterController = new AbortController();

    const formData = new FormData(form);
    const params = new URLSearchParams();

    for (const [key, value] of formData.entries()) {
        if (value !== null && value.trim() !== '') {
            params.append(key, value.trim());
        }
    }

    if (page && page > 1) {
        params.set('page', page);
    } else {
        params.delete('page');
    }

    const requestUrl = '{{ route("admin.orders.index") }}?' + params.toString();

    fetch(requestUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        signal: activeFilterController.signal
    })
    .then(response => {
        if (!response.ok) throw new Error('Filter request failed');
        return response.json();
    })
    .then(data => {
        if (!data.success) throw new Error('Server returned error');

        // 1. Update KPI Cards
        if (data.kpis) {
            const k = data.kpis;
            const elTotal = document.getElementById('kpi-total-orders');
            if (elTotal) elTotal.textContent = formatNumber(k.total_orders);

            const elCohort = document.getElementById('kpi-cohort-label');
            if (elCohort) {
                if (data.active_filters > 0) {
                    elCohort.innerHTML = '<span class="inline-flex items-center text-amber-700 font-semibold gap-1 truncate"><span class="h-1.5 w-1.5 rounded-full bg-amber-500 shrink-0"></span>Filtered cohort</span>';
                } else {
                    elCohort.innerHTML = '<span class="truncate">All records</span>';
                }
            }

            const elNetSales = document.getElementById('kpi-net-sales');
            if (elNetSales) {
                elNetSales.textContent = '₹' + formatCurrency(k.net_sales);
                elNetSales.title = '₹' + formatCurrency(k.net_sales);
            }

            const elGrossSalesSub = document.getElementById('kpi-gross-sales-sub');
            if (elGrossSalesSub) {
                elGrossSalesSub.innerHTML = '<span class="truncate">Gross ₹' + formatCurrency(k.gross_sales) + '</span>';
                elGrossSalesSub.title = 'Gross: ₹' + formatCurrency(k.gross_sales);
            }

            const elPendingPay = document.getElementById('kpi-pending-payments');
            if (elPendingPay) elPendingPay.textContent = formatNumber(k.pending_payments_count);

            const elPendingPaySub = document.getElementById('kpi-pending-payments-sub');
            if (elPendingPaySub) {
                elPendingPaySub.innerHTML = '<span class="truncate">₹' + formatCurrency(k.pending_payments_amount) + ' uncollected</span>';
                elPendingPaySub.title = 'Uncollected: ₹' + formatCurrency(k.pending_payments_amount);
            }

            const elPendingFulfil = document.getElementById('kpi-pending-fulfillment');
            if (elPendingFulfil) elPendingFulfil.textContent = formatNumber(k.pending_fulfillment_count);

            const elAov = document.getElementById('kpi-average-order-value');
            if (elAov) {
                elAov.textContent = '₹' + formatCurrency(k.average_order_value);
                elAov.title = '₹' + formatCurrency(k.average_order_value);
            }

            const elRefunds = document.getElementById('kpi-refunds-count');
            if (elRefunds) elRefunds.textContent = formatNumber(k.refunds_count);

            const elRefundsSub = document.getElementById('kpi-refunds-amount-sub');
            if (elRefundsSub) {
                elRefundsSub.innerHTML = '<span class="truncate">₹' + formatCurrency(k.refunds_amount) + ' returned</span>';
                elRefundsSub.title = 'Total: ₹' + formatCurrency(k.refunds_amount);
            }

            const elCycles = document.getElementById('kpi-active-reset-cycles');
            if (elCycles) elCycles.textContent = formatNumber(k.active_reset_cycles);
        }

        // 2. Update Cascading Location Options
        if (data.cascading) {
            updateSelectOptions('filter-country', data.cascading.countries, 'All Countries');
            updateSelectOptions('filter-state', data.cascading.states, 'All States');
            updateSelectOptions('filter-city', data.cascading.cities, 'All Cities');
            updateSelectOptions('filter-area', data.cascading.areas, 'All Areas');
            updateSelectOptions('filter-postal-code', data.cascading.postal_codes, 'All Postal Codes');
        }

        // 3. Update Orders Table & Pagination HTML
        const tableContainer = document.getElementById('orders-table-content');
        if (tableContainer && data.table_html) {
            tableContainer.innerHTML = data.table_html;
        }

        const paginationContainer = document.getElementById('orders-pagination-container');
        if (paginationContainer && data.pagination_html !== undefined) {
            paginationContainer.innerHTML = data.pagination_html;
        }

        // 4. Update Counters & Badges
        const totalCounter = document.getElementById('orders-total-counter');
        if (totalCounter) totalCounter.textContent = data.total_count ?? 0;

        const resetBtn = document.getElementById('reset-filters-btn');
        if (resetBtn) {
            if (data.active_filters > 0) {
                resetBtn.classList.remove('hidden');
            } else {
                resetBtn.classList.add('hidden');
            }
        }

        const topClearBtn = document.getElementById('top-clear-filters-btn');
        const topClearCount = document.getElementById('top-clear-count');
        if (topClearBtn) {
            if (data.active_filters > 0) {
                topClearBtn.classList.remove('hidden');
                if (topClearCount) topClearCount.textContent = data.active_filters;
            } else {
                topClearBtn.classList.add('hidden');
            }
        }

        const toggleBadge = document.getElementById('toggle-filters-badge');
        if (toggleBadge) {
            if (data.active_filters > 0) {
                toggleBadge.classList.remove('hidden');
                toggleBadge.textContent = data.active_filters;
            } else {
                toggleBadge.classList.add('hidden');
            }
        }

        // 5. Update Export CSV URL
        const exportBtn = document.getElementById('orders-export-btn');
        if (exportBtn) {
            exportBtn.href = '{{ route("admin.orders.export") }}?' + params.toString();
        }

        // 6. Synchronize Browser History without page reload
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({ path: newUrl }, '', newUrl);

        // Reset bulk selection
        deselectAll();

        if (overlay) overlay.classList.add('hidden');
    })
    .catch(error => {
        if (error.name === 'AbortError') return; // Cancelled by subsequent request
        console.error('Filter AJAX error:', error);
        if (overlay) overlay.classList.add('hidden');
    });
}

// Reset All Filters
function resetAllFilters() {
    const form = document.getElementById('order-filters-form');
    if (!form) return;

    form.reset();
    const searchInput = document.getElementById('filter-search-input');
    if (searchInput) searchInput.value = '';

    const dateRange = document.getElementById('filter-date-range');
    if (dateRange) dateRange.value = 'all';

    const customDateBox = document.getElementById('custom-date-container');
    if (customDateBox) customDateBox.classList.add('hidden');

    const fromDate = document.getElementById('filter-from-date');
    if (fromDate) fromDate.value = '';

    const toDate = document.getElementById('filter-to-date');
    if (toDate) toDate.value = '';

    ['payment-status', 'fulfilment-status', 'shipment-status', 'payment-method', 'source', 'refund-status', 'country', 'state', 'city', 'area', 'postal-code'].forEach(id => {
        const el = document.getElementById('filter-' + id);
        if (el) el.value = '';
    });

    // Reset date pill buttons styles
    document.querySelectorAll('.date-pill').forEach((btn, idx) => {
        if (idx === 0) {
            btn.className = 'date-pill rounded-lg px-3 py-1 font-medium transition text-xs bg-stone-900 text-white shadow-2xs';
        } else {
            btn.className = 'date-pill rounded-lg px-3 py-1 font-medium transition text-xs text-stone-600 hover:text-stone-900';
        }
    });

    triggerFilterUpdate(1);
}

// Date Range presets
function setDateRange(range) {
    const hiddenRange = document.getElementById('filter-date-range');
    if (hiddenRange) hiddenRange.value = range;

    const container = document.getElementById('custom-date-container');
    if (range === 'custom') {
        if (container) container.classList.remove('hidden');
    } else {
        if (container) container.classList.add('hidden');
        triggerFilterUpdate(1);
    }

    // Update active pill UI
    document.querySelectorAll('.date-pill').forEach(btn => {
        if (btn.textContent.trim().toLowerCase().replace(/\s+/g, '_') === range || 
            (range === 'all' && btn.textContent.trim() === 'All Time') ||
            (range === 'last_7_days' && btn.textContent.trim() === 'Last 7 Days') ||
            (range === 'last_30_days' && btn.textContent.trim() === 'Last 30 Days') ||
            (range === 'today' && btn.textContent.trim() === 'Today') ||
            (range === 'custom' && btn.textContent.trim() === 'Custom')) {
            btn.className = 'date-pill rounded-lg px-3 py-1 font-medium transition text-xs bg-stone-900 text-white shadow-2xs';
        } else {
            btn.className = 'date-pill rounded-lg px-3 py-1 font-medium transition text-xs text-stone-600 hover:text-stone-900';
        }
    });
}

// Live search debouncing & Form Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('filter-search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                triggerFilterUpdate(1);
            }, 350);
        });
    }

    const form = document.getElementById('order-filters-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            triggerFilterUpdate(1);
        });
    }

    // Intercept pagination clicks for seamless AJAX navigation
    const paginationContainer = document.getElementById('orders-pagination-container');
    if (paginationContainer) {
        paginationContainer.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                try {
                    const parsedUrl = new URL(link.href, window.location.origin);
                    const page = parsedUrl.searchParams.get('page') || 1;
                    triggerFilterUpdate(page);
                } catch(err) {
                    window.location.href = link.href;
                }
            }
        });
    }

    // Browser back/forward navigation support
    window.addEventListener('popstate', function(e) {
        window.location.reload();
    });
});

// Live inline Source updater
function handleSourceChange(selectElement) {
    const url = selectElement.getAttribute('data-url');
    const newSource = selectElement.value;
    const statusIcon = selectElement.parentElement.querySelector('.source-status');

    selectElement.disabled = true;
    if (statusIcon) statusIcon.classList.add('hidden');

    fetch(url, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ source: newSource })
    })
    .then(response => {
        if (!response.ok) throw new Error('Update failed');
        return response.json();
    })
    .then(data => {
        selectElement.disabled = false;
        if (statusIcon) {
            statusIcon.classList.remove('hidden');
            setTimeout(() => statusIcon.classList.add('hidden'), 2500);
        }
    })
    .catch(error => {
        selectElement.disabled = false;
        alert('Could not update order source. Please try again.');
    });
}

// Bulk Selection
function toggleSelectAll(masterCheckbox) {
    const checkboxes = document.querySelectorAll('.order-select-checkbox');
    checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
    handleRowSelect();
}

function handleRowSelect() {
    const checked = document.querySelectorAll('.order-select-checkbox:checked');
    const bulkBar = document.getElementById('bulk-action-form');
    const countBadge = document.getElementById('selected-count');

    if (countBadge) countBadge.textContent = checked.length;
    if (bulkBar) {
        if (checked.length > 0) {
            bulkBar.classList.remove('hidden');
            bulkBar.classList.add('flex');
        } else {
            bulkBar.classList.add('hidden');
            bulkBar.classList.remove('flex');
            const master = document.getElementById('select-all-checkbox');
            if (master) master.checked = false;
        }
    }
}

function deselectAll() {
    const checkboxes = document.querySelectorAll('.order-select-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
    const master = document.getElementById('select-all-checkbox');
    if (master) master.checked = false;
    handleRowSelect();
}

function submitBulk(actionType) {
    const checked = document.querySelectorAll('.order-select-checkbox:checked');
    if (checked.length === 0) {
        alert('Please select at least one order.');
        return;
    }

    const form = document.getElementById('bulk-action-form');
    document.getElementById('bulk-action-type').value = actionType;

    form.querySelectorAll('input[name="order_ids[]"]').forEach(el => el.remove());

    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'order_ids[]';
        input.value = cb.value;
        form.appendChild(input);
    });

    if (actionType === 'update_fulfilment') {
        const val = document.getElementById('bulk-fulfilment-select').value;
        if (!val) {
            alert('Please select a fulfilment status.');
            return;
        }
    }

    if (actionType === 'update_source') {
        const val = document.getElementById('bulk-source-select').value;
        if (!val) {
            alert('Please select a sales source.');
            return;
        }
    }

    form.submit();
}

// Expand / Collapse Row Details
function toggleOrderDetails(orderId) {
    const row = document.getElementById('details-row-' + orderId);
    const icon = document.getElementById('expand-icon-' + orderId);
    if (!row) return;

    if (row.classList.contains('hidden')) {
        row.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-180');
    } else {
        row.classList.add('hidden');
        if (icon) icon.classList.remove('rotate-180');
    }
}

// Close open dropdown menus on click outside
function closeAllMenus() {
    document.querySelectorAll('.order-action-menu').forEach(el => el.removeAttribute('open'));
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.order-action-menu')) {
        closeAllMenus();
    }
});

// Modals
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openFulfilmentModal(orderId, orderNumber, currentFulfilment, currentShipment) {
    document.getElementById('fulfilment-modal-order-number').textContent = 'Order: ' + orderNumber;
    document.getElementById('modal-fulfilment-status').value = currentFulfilment || 'pending';
    document.getElementById('modal-shipment-status').value = currentShipment || '';
    document.getElementById('fulfilment-form').action = '{{ url("admin/orders") }}/' + orderId + '/fulfilment';
    openModal('fulfilment-modal');
}

function openCancelModal(orderId, orderNumber) {
    document.getElementById('cancel-modal-order-number').textContent = 'Order: ' + orderNumber;
    document.getElementById('cancel-form').action = '{{ url("admin/orders") }}/' + orderId + '/cancel';
    openModal('cancel-modal');
}

let activeRefundBalance = 0;
function openRefundModal(orderId, orderNumber, grandTotal, alreadyRefunded) {
    activeRefundBalance = Math.max(0, grandTotal - alreadyRefunded);
    document.getElementById('refund-modal-order-number').textContent = 'Order: ' + orderNumber;
    document.getElementById('refund-modal-total').textContent = '₹' + grandTotal.toFixed(2);
    document.getElementById('refund-modal-balance').textContent = '₹' + activeRefundBalance.toFixed(2);
    document.getElementById('refund-amount-input').max = activeRefundBalance;
    document.getElementById('refund-amount-input').value = activeRefundBalance.toFixed(2);
    document.getElementById('refund-form').action = '{{ url("admin/orders") }}/' + orderId + '/refund';
    toggleRefundType('full');
    openModal('refund-modal');
}

function toggleRefundType(type) {
    const container = document.getElementById('refund-amount-container');
    const input = document.getElementById('refund-amount-input');
    if (type === 'partial') {
        container.classList.remove('hidden');
        input.focus();
    } else {
        container.classList.add('hidden');
        input.value = activeRefundBalance.toFixed(2);
    }
}

// Collapsible Filters Logic & Admin Preference Persistence
const FILTERS_STORAGE_KEY = 'admin_orders_filters_visible';

function initFiltersToggle() {
    let isVisible = true;
    try {
        const stored = localStorage.getItem(FILTERS_STORAGE_KEY);
        if (stored !== null) {
            isVisible = (stored === 'true');
        }
    } catch (e) {}

    applyFiltersVisibility(isVisible, false);
}

function toggleFilters() {
    const wrapper = document.getElementById('filters-collapse-wrapper');
    if (!wrapper) return;
    const isCurrentlyVisible = wrapper.getAttribute('data-visible') !== 'false';
    const nextState = !isCurrentlyVisible;
    applyFiltersVisibility(nextState, true);
    try {
        localStorage.setItem(FILTERS_STORAGE_KEY, nextState ? 'true' : 'false');
    } catch(e) {}
}

function applyFiltersVisibility(visible, animate) {
    const wrapper = document.getElementById('filters-collapse-wrapper');
    const label = document.getElementById('toggle-filters-label');
    const icon = document.getElementById('toggle-filters-icon');
    if (!wrapper) return;

    wrapper.setAttribute('data-visible', visible ? 'true' : 'false');

    if (!animate) {
        wrapper.style.transition = 'none';
    } else {
        wrapper.style.transition = 'all 300ms cubic-bezier(0.4, 0, 0.2, 1)';
    }

    if (visible) {
        wrapper.style.maxHeight = wrapper.scrollHeight ? (wrapper.scrollHeight + 50) + 'px' : '1500px';
        wrapper.style.opacity = '1';
        wrapper.style.pointerEvents = 'auto';
        wrapper.style.marginTop = '';
        wrapper.style.marginBottom = '';
        if (label) label.textContent = 'Hide Filters';
        if (icon) icon.classList.remove('rotate-180');

        setTimeout(() => {
            if (wrapper.getAttribute('data-visible') === 'true') {
                wrapper.style.maxHeight = '2000px';
            }
        }, 320);
    } else {
        wrapper.style.maxHeight = wrapper.scrollHeight ? (wrapper.scrollHeight + 'px') : '1500px';
        wrapper.offsetHeight; // Force reflow
        wrapper.style.maxHeight = '0px';
        wrapper.style.opacity = '0';
        wrapper.style.pointerEvents = 'none';
        wrapper.style.marginTop = '0px';
        wrapper.style.marginBottom = '0px';
        if (label) label.textContent = 'Show Filters';
        if (icon) icon.classList.add('rotate-180');
    }

    if (!animate) {
        setTimeout(() => {
            wrapper.style.transition = 'all 300ms cubic-bezier(0.4, 0, 0.2, 1)';
        }, 50);
    }
}

// Auto-init on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFiltersToggle);
} else {
    initFiltersToggle();
}
</script>
@endsection
