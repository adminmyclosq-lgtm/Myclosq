@extends('layouts.admin', ['title' => 'Coupons & Promotions | Enterprise Discount Engine'])

@section('content')
<div class="min-h-screen bg-stone-50/60 pb-16">
    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8 pt-6 space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">Coupons & Promotions</h1>
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-800 border border-emerald-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Active Engine
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-stone-500 mt-1">Create, manage, monitor and analyze promotional campaigns and discount codes.</p>
            </div>

            {{-- Top Right Actions --}}
            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Export Dropdown --}}
                <div class="relative inline-block text-left" id="export-dropdown-wrapper">
                    <button
                        type="button"
                        onclick="toggleExportMenu()"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 hover:border-stone-300 transition"
                    >
                        <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export</span>
                        <svg class="h-3 w-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="export-dropdown-menu" class="hidden absolute right-0 z-30 mt-1.5 w-44 rounded-xl border border-stone-200 bg-white p-1 shadow-lg ring-1 ring-black/5">
                        <a href="{{ route('admin.coupons.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-stone-700 hover:bg-stone-50 transition">
                            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export to CSV
                        </a>
                        <a href="{{ route('admin.coupons.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-stone-700 hover:bg-stone-50 transition">
                            <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Export to Excel (.xls)
                        </a>
                    </div>
                </div>

                {{-- Import (Future) --}}
                <button
                    type="button"
                    disabled
                    class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-stone-100/70 px-3.5 py-2 text-xs font-semibold text-stone-400 cursor-not-allowed"
                    title="Bulk coupon CSV import coming in next release"
                >
                    <svg class="h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Import</span>
                </button>

                {{-- Create Coupon Primary Button --}}
                <a
                    href="{{ route('admin.coupons.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-[#1c3d2c] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#142e21] transition hover:shadow-md"
                >
                    <svg class="h-3.5 w-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Create Coupon</span>
                </a>
            </div>
        </div>

        {{-- Session Flash Notifications --}}
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/90 p-4 text-xs font-medium text-emerald-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50/90 p-4 text-xs font-medium text-rose-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900">&times;</button>
            </div>
        @endif

        {{-- =========================================================================
             TOP 8 KPI CARDS
             ========================================================================= --}}
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($kpis as $key => $kpi)
                <div class="rounded-2xl border border-stone-200/90 bg-white p-4 sm:p-5 shadow-2xs hover:shadow-xs transition duration-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-stone-500 mb-1.5">
                            <span class="text-xs font-medium text-stone-500 tracking-tight">{{ $kpi['label'] }}</span>
                            @if($kpi['trend'] === 'up')
                                <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold {{ $kpi['is_positive'] ? 'text-emerald-600' : 'text-rose-600' }}">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                    {{ $kpi['change_pct'] > 0 ? '+' . $kpi['change_pct'] : $kpi['change_pct'] }}%
                                </span>
                            @elseif($kpi['trend'] === 'down')
                                <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold {{ $kpi['is_positive'] ? 'text-emerald-600' : 'text-rose-600' }}">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                    {{ $kpi['change_pct'] }}%
                                </span>
                            @else
                                <span class="text-[11px] font-medium text-stone-400">0.0%</span>
                            @endif
                        </div>
                        <div class="text-xl sm:text-2xl font-bold font-mono text-stone-900 tracking-tight">
                            {{ $kpi['value'] }}
                        </div>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-400">
                        <span>Prev: <strong class="font-medium text-stone-600">{{ $kpi['prev_value'] }}</strong></span>
                        <span class="truncate max-w-[120px] text-[10.5px]" title="{{ $kpi['subtext'] }}">{{ $kpi['subtext'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- =========================================================================
             ANALYTICS SECTION (Directly below KPI cards)
             ========================================================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Graph 1: Coupon Usage Trend (Line Chart) --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-stone-900">Coupon Usage Trend</h3>
                        <p class="text-[11px] text-stone-400">Daily and aggregate redemptions across checkout sessions</p>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full bg-stone-100 px-2 py-0.5 text-[10px] font-medium text-stone-600">
                        Total: {{ number_format($charts['usage_trend']['total']) }}
                    </span>
                </div>
                <div id="chart-coupon-usage-trend" class="min-h-[260px]"></div>
            </div>

            {{-- Graph 2: Coupon Type Distribution (Interactive Donut Chart) --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-stone-900">Coupon Type Distribution</h3>
                        <p class="text-[11px] text-stone-400">Breakdown of promotional mechanics & discount structures</p>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full bg-stone-100 px-2 py-0.5 text-[10px] font-medium text-stone-600">
                        {{ $charts['type_distribution']['total'] }} Campaigns
                    </span>
                </div>
                <div id="chart-coupon-type-donut" class="min-h-[260px]"></div>
            </div>

            {{-- Graph 3: Revenue Generated by Coupon (Horizontal Bar Chart) --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-stone-900">Revenue Generated by Coupon</h3>
                        <p class="text-[11px] text-stone-400">Top ranked promotional codes by gross order volume</p>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700">Top Performers</span>
                </div>
                <div id="chart-revenue-by-coupon" class="min-h-[260px]"></div>
            </div>

            {{-- Graph 4: Discount Cost vs Revenue (Dual Line / Area Chart) --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-stone-900">Discount Cost vs Revenue</h3>
                        <p class="text-[11px] text-stone-400">Marketing subsidy investment vs net gross revenue captured</p>
                    </div>
                    <div class="flex items-center gap-2 text-[11px]">
                        <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Net: ₹{{ number_format($charts['cost_vs_revenue']['totals']['net_revenue'], 2) }}
                        </span>
                    </div>
                </div>
                <div id="chart-cost-vs-revenue" class="min-h-[260px]"></div>
            </div>

        </div>

        {{-- =========================================================================
             SEARCH & ADVANCED FILTERS BAR
             ========================================================================= --}}
        <div class="rounded-2xl border border-stone-200/90 bg-white p-4 sm:p-5 shadow-2xs space-y-4">
            <form method="GET" action="{{ route('admin.coupons.index') }}" id="coupons-filter-form">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">

                    {{-- Search Input --}}
                    <div class="sm:col-span-2 relative">
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-400 mb-1">Search</label>
                        <div class="relative">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Search by code, campaign, or description..."
                                class="w-full h-9 rounded-xl border border-stone-200 bg-stone-50/60 pl-8 pr-3 text-xs text-stone-800 placeholder-stone-400 focus:border-[#1c3d2c] focus:bg-white focus:outline-none"
                            >
                            <svg class="h-3.5 w-3.5 text-stone-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    {{-- Coupon Status --}}
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-400 mb-1">Status</label>
                        <select name="status" class="w-full h-9 rounded-xl border border-stone-200 bg-stone-50/60 px-3 text-xs text-stone-800 focus:border-[#1c3d2c] focus:bg-white focus:outline-none">
                            <option value="all">All Statuses</option>
                            @foreach($filterOptions['statuses'] as $k => $lbl)
                                <option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Coupon Type --}}
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-400 mb-1">Discount Type</label>
                        <select name="discount_type" class="w-full h-9 rounded-xl border border-stone-200 bg-stone-50/60 px-3 text-xs text-stone-800 focus:border-[#1c3d2c] focus:bg-white focus:outline-none">
                            <option value="all">All Types</option>
                            @foreach($filterOptions['types'] as $k => $lbl)
                                <option value="{{ $k }}" {{ request('discount_type') === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Customer Eligibility --}}
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-400 mb-1">Eligibility</label>
                        <select name="customer_eligibility" class="w-full h-9 rounded-xl border border-stone-200 bg-stone-50/60 px-3 text-xs text-stone-800 focus:border-[#1c3d2c] focus:bg-white focus:outline-none">
                            <option value="all">All Customers</option>
                            @foreach($filterOptions['customer_eligibilities'] as $k => $lbl)
                                <option value="{{ $k }}" {{ request('customer_eligibility') === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Presets --}}
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-400 mb-1">Date Range</label>
                        <select name="date_preset" class="w-full h-9 rounded-xl border border-stone-200 bg-stone-50/60 px-3 text-xs text-stone-800 focus:border-[#1c3d2c] focus:bg-white focus:outline-none">
                            <option value="today" {{ request('date_preset') === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="yesterday" {{ request('date_preset') === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="last_7_days" {{ request('date_preset') === 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="last_30_days" {{ (request('date_preset', 'last_30_days') === 'last_30_days') ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="this_month" {{ request('date_preset') === 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="last_month" {{ request('date_preset') === 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="this_year" {{ request('date_preset') === 'this_year' ? 'selected' : '' }}>This Year</option>
                        </select>
                    </div>

                    {{-- Product Filter --}}
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-400 mb-1">Product</label>
                        <select name="product_id" class="w-full h-9 rounded-xl border border-stone-200 bg-stone-50/60 px-3 text-xs text-stone-800 focus:border-[#1c3d2c] focus:bg-white focus:outline-none">
                            <option value="">All Products</option>
                            @foreach($filterOptions['products'] as $p)
                                <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Channel Filter --}}
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-400 mb-1">Sales Channel</label>
                        <select name="channel" class="w-full h-9 rounded-xl border border-stone-200 bg-stone-50/60 px-3 text-xs text-stone-800 focus:border-[#1c3d2c] focus:bg-white focus:outline-none">
                            <option value="">All Channels</option>
                            @foreach($filterOptions['channels'] as $ch)
                                <option value="{{ $ch }}" {{ request('channel') === $ch ? 'selected' : '' }}>{{ ucfirst($ch) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort By --}}
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-400 mb-1">Sort By</label>
                        <select name="sort_by" class="w-full h-9 rounded-xl border border-stone-200 bg-stone-50/60 px-3 text-xs text-stone-800 focus:border-[#1c3d2c] focus:bg-white focus:outline-none">
                            <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Recently Created</option>
                            <option value="revenue" {{ request('sort_by') === 'revenue' ? 'selected' : '' }}>Highest Revenue</option>
                            <option value="usage" {{ request('sort_by') === 'usage' ? 'selected' : '' }}>Most Redemptions</option>
                            <option value="discount" {{ request('sort_by') === 'discount' ? 'selected' : '' }}>Discount Value</option>
                            <option value="code" {{ request('sort_by') === 'code' ? 'selected' : '' }}>Coupon Code (A-Z)</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            class="flex-1 h-9 inline-flex items-center justify-center gap-1 rounded-xl bg-stone-900 px-3 text-xs font-semibold text-white shadow-2xs hover:bg-stone-800 transition"
                        >
                            <svg class="h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Apply
                        </button>
                        <a
                            href="{{ route('admin.coupons.index') }}"
                            class="h-9 inline-flex items-center justify-center rounded-xl border border-stone-200 bg-white px-3 text-xs font-medium text-stone-600 hover:bg-stone-50 transition"
                            title="Reset all filters"
                        >
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- Bulk Actions Bar (Conditional upon selection) --}}
            <form method="POST" action="{{ route('admin.coupons.bulk-action') }}" id="bulk-action-form" class="pt-3 border-t border-stone-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                @csrf
                <div class="flex items-center gap-3">
                    <span id="selected-count-label" class="text-stone-500 font-medium">0 coupons selected</span>
                    <select name="bulk_action" id="bulk-action-select" class="h-8 rounded-lg border border-stone-200 bg-white px-2.5 text-xs text-stone-700 focus:outline-none">
                        <option value="">Bulk Actions...</option>
                        <option value="bulk_activate">Bulk Activate</option>
                        <option value="bulk_pause">Bulk Pause</option>
                        <option value="bulk_delete">Bulk Delete</option>
                    </select>
                    <button
                        type="submit"
                        id="btn-apply-bulk"
                        disabled
                        class="h-8 px-3 rounded-lg bg-stone-800 text-white font-semibold text-xs disabled:opacity-40 disabled:cursor-not-allowed hover:bg-stone-900 transition"
                    >
                        Execute
                    </button>
                </div>

                <div class="flex items-center gap-2 text-stone-400 text-xs">
                    <span>Showing {{ $coupons->firstItem() ?? 0 }}–{{ $coupons->lastItem() ?? 0 }} of {{ $coupons->total() }} promotions</span>
                </div>
            </form>
        </div>

        {{-- =========================================================================
             COUPON TABLE (Modern enterprise table with Shopify styling)
             ========================================================================= --}}
        <div class="rounded-2xl border border-stone-200/90 bg-white shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-stone-200/80 bg-stone-50/75 text-[11px] font-bold uppercase tracking-wider text-stone-500">
                            <th class="py-3 px-4 w-10 text-center">
                                <input type="checkbox" id="select-all-coupons" onchange="toggleSelectAll(this)" class="rounded border-stone-300 text-[#1c3d2c] focus:ring-emerald-500">
                            </th>
                            <th class="py-3 px-4">Coupon Code</th>
                            <th class="py-3 px-4">Name / Campaign</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Discount</th>
                            <th class="py-3 px-4">Min. Order</th>
                            <th class="py-3 px-4">Usage / Limit</th>
                            <th class="py-3 px-4">Revenue</th>
                            <th class="py-3 px-4">Validity</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-xs text-stone-700">
                        @forelse($coupons as $coupon)
                            @php
                                $status = $coupon->calculated_status;
                                $statusStyles = [
                                    'active' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    'scheduled' => 'bg-blue-50 text-blue-800 border-blue-200',
                                    'paused' => 'bg-amber-50 text-amber-800 border-amber-200',
                                    'expired' => 'bg-stone-100 text-stone-600 border-stone-200',
                                    'draft' => 'bg-purple-50 text-purple-700 border-purple-200',
                                ];
                                $badgeClass = $statusStyles[$status] ?? 'bg-stone-100 text-stone-700 border-stone-200';
                            @endphp
                            <tr class="hover:bg-stone-50/60 transition group">
                                {{-- Checkbox --}}
                                <td class="py-3.5 px-4 text-center">
                                    <input
                                        type="checkbox"
                                        form="bulk-action-form"
                                        name="selected_ids[]"
                                        value="{{ $coupon->id }}"
                                        class="coupon-row-checkbox rounded border-stone-300 text-[#1c3d2c] focus:ring-emerald-500"
                                        onchange="updateSelectedCount()"
                                    >
                                </td>

                                {{-- Code --}}
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.coupons.show', $coupon) }}" class="font-mono font-bold text-stone-900 hover:text-emerald-800 transition tracking-tight">
                                            {{ $coupon->code }}
                                        </a>
                                        <button
                                            type="button"
                                            onclick="copyCouponCode('{{ $coupon->code }}')"
                                            class="text-stone-300 hover:text-stone-600 transition"
                                            title="Copy code"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </button>
                                    </div>
                                </td>

                                {{-- Name / Campaign --}}
                                <td class="py-3.5 px-4 max-w-[200px]">
                                    <div class="font-medium text-stone-900 truncate" title="{{ $coupon->name }}">{{ $coupon->name }}</div>
                                    @if($coupon->campaign_name)
                                        <span class="inline-flex items-center text-[10.5px] text-stone-400 truncate">
                                            🏷️ {{ $coupon->campaign_name }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Type --}}
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center rounded-md bg-stone-100 px-2 py-0.5 text-[11px] font-medium text-stone-700 capitalize">
                                        {{ str_replace('_', ' ', $coupon->discount_type) }}
                                    </span>
                                </td>

                                {{-- Discount --}}
                                <td class="py-3.5 px-4 font-semibold text-stone-900">
                                    {{ $coupon->formatted_discount }}
                                </td>

                                {{-- Minimum Order --}}
                                <td class="py-3.5 px-4 text-stone-600">
                                    {{ $coupon->minimum_cart_value > 0 ? '₹' . number_format($coupon->minimum_cart_value, 2) : 'No min' }}
                                </td>

                                {{-- Usage Progress --}}
                                <td class="py-3.5 px-4">
                                    @php
                                        $used = $coupon->usages_count;
                                        $limit = $coupon->usage_limit;
                                        $pct = ($limit && $limit > 0) ? min(100, round(($used / $limit) * 100)) : 0;
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-stone-900 font-medium">{{ $used }}</span>
                                        <span class="text-stone-400">/ {{ $limit ? $limit : '∞' }}</span>
                                    </div>
                                    @if($limit)
                                        <div class="w-20 bg-stone-100 h-1 rounded-full mt-1 overflow-hidden">
                                            <div class="bg-emerald-600 h-full rounded-full" style="width: {{ $pct }}%"></div>
                                        </div>
                                    @endif
                                </td>

                                {{-- Revenue Generated --}}
                                <td class="py-3.5 px-4 font-mono font-semibold text-stone-900">
                                    ₹{{ number_format((float) ($coupon->orders_sum_grand_total ?? 0), 2) }}
                                </td>

                                {{-- Validity --}}
                                <td class="py-3.5 px-4 text-[11px] text-stone-500">
                                    @if($coupon->starts_at)
                                        <div>From: {{ $coupon->starts_at->format('d M Y') }}</div>
                                    @endif
                                    @if($coupon->expires_at)
                                        <div class="{{ $coupon->expires_at->isPast() ? 'text-rose-500 font-medium' : '' }}">
                                            Exp: {{ $coupon->expires_at->format('d M Y') }}
                                        </div>
                                    @else
                                        <span class="text-emerald-700">Never expires</span>
                                    @endif
                                </td>

                                {{-- Status Badge --}}
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10.5px] font-semibold capitalize {{ $badgeClass }}">
                                        {{ $status }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        {{-- View Details Hub --}}
                                        <a
                                            href="{{ route('admin.coupons.show', $coupon) }}"
                                            class="p-1 rounded-lg text-stone-400 hover:text-stone-900 hover:bg-stone-100 transition"
                                            title="View 360° Coupon Details"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('admin.coupons.edit', $coupon) }}"
                                            class="p-1 rounded-lg text-stone-400 hover:text-stone-900 hover:bg-stone-100 transition"
                                            title="Edit Coupon"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        {{-- Quick Toggle Active / Pause --}}
                                        <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon) }}" class="inline">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="p-1 rounded-lg text-stone-400 hover:text-amber-600 hover:bg-amber-50 transition"
                                                title="{{ $coupon->status === 'active' ? 'Pause Coupon' : 'Activate Coupon' }}"
                                            >
                                                @if($coupon->status === 'active')
                                                    <svg class="h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @else
                                                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('Permanently delete coupon {{ $coupon->code }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="p-1 rounded-lg text-stone-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                                title="Delete Coupon"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-12 text-center">
                                    <div class="mx-auto max-w-sm text-center">
                                        <div class="mx-auto h-12 w-12 rounded-2xl bg-stone-100 flex items-center justify-center text-stone-400 mb-3">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        </div>
                                        <h3 class="text-sm font-semibold text-stone-900">No promotions found</h3>
                                        <p class="text-xs text-stone-500 mt-1">No coupons match your filter criteria or search query.</p>
                                        <div class="mt-4 flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.coupons.index') }}" class="rounded-xl border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-50">Reset Filters</a>
                                            <a href="{{ route('admin.coupons.create') }}" class="rounded-xl bg-[#1c3d2c] px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-[#142e21]">Create First Coupon</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination footer --}}
            @if($coupons->hasPages())
                <div class="p-4 border-t border-stone-100 bg-stone-50/50">
                    {{ $coupons->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

{{-- Scripts for Charts & Client Interactivity --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Graph 1: Usage Trend Line Chart
        var usageTrendOptions = {
            chart: {
                type: 'area',
                height: 260,
                toolbar: { show: false },
                fontFamily: 'Inter, system-ui, sans-serif'
            },
            stroke: { curve: 'smooth', width: 2.5 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 95, 100]
                }
            },
            colors: ['#10B981'],
            series: @json($charts['usage_trend']['series']),
            xaxis: {
                categories: @json($charts['usage_trend']['labels']),
                labels: { style: { colors: '#78716c', fontSize: '11px' } }
            },
            yaxis: {
                labels: { style: { colors: '#78716c', fontSize: '11px' } }
            },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: { theme: 'light' }
        };
        new ApexCharts(document.querySelector("#chart-coupon-usage-trend"), usageTrendOptions).render();

        // 2. Graph 2: Coupon Type Distribution Donut Chart
        var typeDonutOptions = {
            chart: {
                type: 'donut',
                height: 260,
                fontFamily: 'Inter, system-ui, sans-serif'
            },
            labels: @json($charts['type_distribution']['labels']),
            series: @json($charts['type_distribution']['series']),
            colors: ['#10B981', '#3B82F6', '#F59E0B', '#8B5CF6', '#EC4899', '#6366F1'],
            legend: { position: 'bottom', fontSize: '11px' },
            dataLabels: { enabled: true, dropShadow: { enabled: false } },
            stroke: { width: 0 }
        };
        new ApexCharts(document.querySelector("#chart-coupon-type-donut"), typeDonutOptions).render();

        // 3. Graph 3: Revenue Generated by Coupon Horizontal Bar
        var revenueByCouponOptions = {
            chart: {
                type: 'bar',
                height: 260,
                toolbar: { show: false },
                fontFamily: 'Inter, system-ui, sans-serif'
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    barHeight: '60%'
                }
            },
            colors: ['#1c3d2c'],
            series: @json($charts['revenue_by_coupon']['series']),
            xaxis: {
                categories: @json($charts['revenue_by_coupon']['categories']),
                labels: {
                    formatter: function (val) { return '₹' + Number(val).toLocaleString(); },
                    style: { colors: '#78716c', fontSize: '11px' }
                }
            },
            yaxis: {
                labels: { style: { colors: '#44403c', fontSize: '11px', fontWeight: 600 } }
            },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: {
                y: { formatter: function (val) { return '₹' + Number(val).toLocaleString(); } }
            }
        };
        new ApexCharts(document.querySelector("#chart-revenue-by-coupon"), revenueByCouponOptions).render();

        // 4. Graph 4: Cost vs Revenue Dual Line Chart
        var costVsRevenueOptions = {
            chart: {
                type: 'line',
                height: 260,
                toolbar: { show: false },
                fontFamily: 'Inter, system-ui, sans-serif'
            },
            stroke: { curve: 'smooth', width: [2.5, 2, 2.5] },
            colors: ['#10B981', '#EF4444', '#3B82F6'],
            series: @json($charts['cost_vs_revenue']['series']),
            xaxis: {
                categories: @json($charts['cost_vs_revenue']['labels']),
                labels: { style: { colors: '#78716c', fontSize: '11px' } }
            },
            yaxis: {
                labels: {
                    formatter: function (val) { return '₹' + Number(val).toLocaleString(); },
                    style: { colors: '#78716c', fontSize: '11px' }
                }
            },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px' },
            tooltip: {
                y: { formatter: function (val) { return '₹' + Number(val).toLocaleString(); } }
            }
        };
        new ApexCharts(document.querySelector("#chart-cost-vs-revenue"), costVsRevenueOptions).render();
    });

    // Toggle Export Dropdown
    function toggleExportMenu() {
        var menu = document.getElementById('export-dropdown-menu');
        menu.classList.toggle('hidden');
    }
    document.addEventListener('click', function (e) {
        var wrapper = document.getElementById('export-dropdown-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            var menu = document.getElementById('export-dropdown-menu');
            if (menu) menu.classList.add('hidden');
        }
    });

    // Copy Coupon Code
    function copyCouponCode(code) {
        navigator.clipboard.writeText(code).then(function () {
            alert('Coupon code ' + code + ' copied to clipboard!');
        });
    }

    // Select All Checkbox Handler
    function toggleSelectAll(master) {
        var checkboxes = document.querySelectorAll('.coupon-row-checkbox');
        checkboxes.forEach(function (cb) {
            cb.checked = master.checked;
        });
        updateSelectedCount();
    }

    // Update Selected Count & Bulk Execute Button State
    function updateSelectedCount() {
        var checked = document.querySelectorAll('.coupon-row-checkbox:checked').length;
        var label = document.getElementById('selected-count-label');
        var btn = document.getElementById('btn-apply-bulk');

        label.textContent = checked + ' coupons selected';
        if (checked > 0) {
            btn.removeAttribute('disabled');
            btn.classList.remove('opacity-40');
        } else {
            btn.setAttribute('disabled', 'disabled');
            btn.classList.add('opacity-40');
        }
    }
</script>
@endsection
