@extends('layouts.admin', ['title' => 'Product Analytics | Gut Reset Admin'])

@section('content')
<div class="min-h-screen bg-stone-50/60 pb-20">
    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8 pt-6 space-y-6">

        {{-- =========================================================================
             PAGE HEADER
             ========================================================================= --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">Product Analytics</h1>
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-800 border border-emerald-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live BI Engine
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-stone-500 mt-1">Analyze product performance, revenue, inventory health, customer demand, and profitability.</p>
            </div>

            {{-- Top Right Controls (Export options & Refresh) --}}
            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Refresh Button --}}
                <button
                    type="button"
                    onclick="reloadProductAnalytics(true)"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition"
                    title="Bypass cache and reload metrics"
                >
                    <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Refresh</span>
                </button>

                {{-- Export Dropdown --}}
                <div class="relative inline-block text-left" id="export-dropdown-wrapper">
                    <button
                        type="button"
                        onclick="toggleExportMenu()"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition"
                    >
                        <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export</span>
                        <svg class="h-3 w-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="export-dropdown-menu" class="hidden absolute right-0 z-30 mt-1.5 w-44 rounded-xl border border-stone-200 bg-white p-1 shadow-lg ring-1 ring-black/5">
                        <a href="{{ route('admin.analytics.products.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-stone-700 hover:bg-stone-50 transition">
                            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export CSV
                        </a>
                        <a href="{{ route('admin.analytics.products.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-stone-700 hover:bg-stone-50 transition">
                            <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Export Excel (.xls)
                        </a>
                        <button type="button" onclick="window.print()" class="w-full flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-stone-700 hover:bg-stone-50 transition text-left">
                            <svg class="h-3.5 w-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print / PDF
                        </button>
                    </div>
                </div>

                {{-- Catalog Quick Jump --}}
                <a
                    href="{{ route('admin.products.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-stone-900 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-stone-800 transition"
                >
                    <span>Products Catalog</span>
                    <svg class="h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        {{-- =========================================================================
             GLOBAL STICKY FILTER BAR
             Affects every KPI, chart, and table.
             ========================================================================= --}}
        <div class="sticky top-2 z-30 rounded-2xl border border-stone-200/90 bg-white/95 backdrop-blur-md p-4 shadow-sm transition-all duration-200 space-y-3">
            <form method="GET" action="{{ route('admin.analytics.products') }}" id="product-analytics-filters">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    {{-- Date Presets --}}
                    @php
                        $preset = $filters['date_preset'] ?? 'last_30_days';
                        $presets = [
                            'today' => 'Today',
                            'yesterday' => 'Yesterday',
                            'last_7_days' => 'Last 7 Days',
                            'last_30_days' => 'Last 30 Days',
                            'last_90_days' => 'Last 90 Days',
                            'this_month' => 'This Month',
                            'last_month' => 'Last Month',
                            'this_year' => 'This Year',
                            'custom' => 'Custom Range',
                        ];
                    @endphp
                    <div class="flex flex-wrap items-center gap-1.5">
                        @foreach($presets as $key => $lbl)
                            <button
                                type="button"
                                onclick="applyPreset('{{ $key }}')"
                                class="rounded-xl px-3 py-1.5 text-xs font-semibold transition {{ $preset === $key ? 'bg-stone-900 text-white shadow-2xs' : 'bg-stone-100/80 text-stone-600 hover:bg-stone-200/70 hover:text-stone-900' }}"
                            >
                                {{ $lbl }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Previous Period Comparison Toggle --}}
                    <div class="flex items-center gap-3 ml-auto">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none text-xs text-stone-600 font-medium">
                            <input
                                type="checkbox"
                                name="compare_previous"
                                value="1"
                                onchange="document.getElementById('product-analytics-filters').submit()"
                                {{ !empty($dashboardData['meta']['compare_previous']) ? 'checked' : '' }}
                                class="sr-only peer"
                            >
                            <div class="relative w-8 h-4 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-emerald-600"></div>
                            <span class="text-xs text-stone-600 font-medium">Compare Previous Period</span>
                        </label>
                    </div>
                </div>

                {{-- Hidden Preset Input --}}
                <input type="hidden" name="date_preset" id="active-date-preset-input" value="{{ $preset }}">

                {{-- Multi-Criteria Global Filters Row --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2.5 pt-3 border-t border-stone-100 text-xs">
                    {{-- Product Filter --}}
                    <div>
                        <select name="product_id" onchange="this.form.submit()" class="w-full h-8 rounded-lg border border-stone-200 bg-stone-50/60 px-2 text-xs text-stone-800 focus:outline-none">
                            <option value="">All Products</option>
                            @foreach($filterOptions['products'] as $p)
                                <option value="{{ $p['id'] }}" {{ request('product_id') == $p['id'] ? 'selected' : '' }}>{{ $p['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Category Filter --}}
                    <div>
                        <select name="category_id" id="category-filter-select" onchange="this.form.submit()" class="w-full h-8 rounded-lg border border-stone-200 bg-stone-50/60 px-2 text-xs text-stone-800 focus:outline-none">
                            <option value="">All Categories</option>
                            @foreach($filterOptions['categories'] as $c)
                                <option value="{{ $c['id'] }}" {{ request('category_id') == $c['id'] ? 'selected' : '' }}>{{ $c['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Brand Filter --}}
                    <div>
                        <select name="brand" onchange="this.form.submit()" class="w-full h-8 rounded-lg border border-stone-200 bg-stone-50/60 px-2 text-xs text-stone-800 focus:outline-none">
                            <option value="">All Brands</option>
                            @foreach($filterOptions['brands'] as $b)
                                <option value="{{ $b }}" {{ request('brand') === $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Product Status --}}
                    <div>
                        <select name="product_status" onchange="this.form.submit()" class="w-full h-8 rounded-lg border border-stone-200 bg-stone-50/60 px-2 text-xs text-stone-800 focus:outline-none">
                            <option value="">All Product Statuses</option>
                            @foreach($filterOptions['statuses'] as $st)
                                <option value="{{ $st }}" {{ request('product_status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Inventory Status --}}
                    <div>
                        <select name="inventory_status" onchange="this.form.submit()" class="w-full h-8 rounded-lg border border-stone-200 bg-stone-50/60 px-2 text-xs text-stone-800 focus:outline-none">
                            <option value="">All Stock Health</option>
                            @foreach($filterOptions['inventory_statuses'] as $ist => $istLabel)
                                <option value="{{ $ist }}" {{ request('inventory_status') === $ist ? 'selected' : '' }}>{{ $istLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sales Channel --}}
                    <div>
                        <select name="channel" onchange="this.form.submit()" class="w-full h-8 rounded-lg border border-stone-200 bg-stone-50/60 px-2 text-xs text-stone-800 focus:outline-none">
                            <option value="">All Channels</option>
                            @foreach($filterOptions['channels'] as $ch)
                                <option value="{{ $ch }}" {{ request('channel') === $ch ? 'selected' : '' }}>{{ ucfirst($ch) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Country --}}
                    <div>
                        <select name="country" onchange="this.form.submit()" class="w-full h-8 rounded-lg border border-stone-200 bg-stone-50/60 px-2 text-xs text-stone-800 focus:outline-none">
                            <option value="">All Countries</option>
                            @foreach($filterOptions['countries'] as $country)
                                <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- State --}}
                    <div>
                        <select name="state" onchange="this.form.submit()" class="w-full h-8 rounded-lg border border-stone-200 bg-stone-50/60 px-2 text-xs text-stone-800 focus:outline-none">
                            <option value="">All States</option>
                            @foreach($filterOptions['states'] as $st)
                                <option value="{{ $st }}" {{ request('state') === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- City --}}
                    <div>
                        <select name="city" onchange="this.form.submit()" class="w-full h-8 rounded-lg border border-stone-200 bg-stone-50/60 px-2 text-xs text-stone-800 focus:outline-none">
                            <option value="">All Cities</option>
                            @foreach($filterOptions['cities'] as $ct)
                                <option value="{{ $ct }}" {{ request('city') === $ct ? 'selected' : '' }}>{{ $ct }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Customer Type --}}
                    <div>
                        <select name="customer_type" onchange="this.form.submit()" class="w-full h-8 rounded-lg border border-stone-200 bg-stone-50/60 px-2 text-xs text-stone-800 focus:outline-none">
                            @foreach($filterOptions['customer_types'] as $ck => $cv)
                                <option value="{{ $ck }}" {{ request('customer_type', 'all') === $ck ? 'selected' : '' }}>{{ $cv }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Reset Button --}}
                    <div class="col-span-2 flex items-center justify-end gap-2">
                        <a
                            href="{{ route('admin.analytics.products') }}"
                            class="inline-flex items-center gap-1 rounded-lg border border-stone-200 bg-white px-2.5 py-1 text-xs font-medium text-stone-600 hover:bg-stone-50"
                        >
                            Reset Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- =========================================================================
             10 KPI CARDS
             Total Products, Active Products, Units Sold, Gross Revenue, Net Revenue,
             Average Selling Price, Refund Rate, Inventory Value, Best Selling, Worst Performing
             ========================================================================= --}}
        @php $k = $dashboardData['kpis']; @endphp
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3.5">

            {{-- 1. Total Products --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:shadow-xs transition duration-200">
                <div class="flex items-center justify-between text-stone-500 mb-1">
                    <span class="text-xs font-medium text-stone-500">Total Products</span>
                    <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-emerald-600">
                        {{ $k['total_products']['change_pct'] > 0 ? '+' : '' }}{{ $k['total_products']['change_pct'] }}%
                    </span>
                </div>
                <div class="text-xl font-bold font-mono text-stone-900">{{ $k['total_products']['formatted'] }}</div>
                <div class="mt-2 pt-2 border-t border-stone-100 flex items-center justify-between text-[10.5px] text-stone-400">
                    <span>Prev: {{ $k['total_products']['prev_formatted'] }}</span>
                    <span>Catalog</span>
                </div>
            </div>

            {{-- 2. Active Products --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:shadow-xs transition duration-200">
                <div class="flex items-center justify-between text-stone-500 mb-1">
                    <span class="text-xs font-medium text-stone-500">Active Products</span>
                    <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-emerald-600">
                        {{ $k['active_products']['change_pct'] > 0 ? '+' : '' }}{{ $k['active_products']['change_pct'] }}%
                    </span>
                </div>
                <div class="text-xl font-bold font-mono text-stone-900">{{ $k['active_products']['formatted'] }}</div>
                <div class="mt-2 pt-2 border-t border-stone-100 flex items-center justify-between text-[10.5px] text-stone-400">
                    <span>Prev: {{ $k['active_products']['prev_formatted'] }}</span>
                    <span>Published</span>
                </div>
            </div>

            {{-- 3. Units Sold --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:shadow-xs transition duration-200">
                <div class="flex items-center justify-between text-stone-500 mb-1">
                    <span class="text-xs font-medium text-stone-500">Units Sold</span>
                    <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-emerald-600">
                        {{ $k['units_sold']['change_pct'] > 0 ? '+' : '' }}{{ $k['units_sold']['change_pct'] }}%
                    </span>
                </div>
                <div class="text-xl font-bold font-mono text-stone-900">{{ $k['units_sold']['formatted'] }}</div>
                <div class="mt-2 pt-2 border-t border-stone-100 flex items-center justify-between text-[10.5px] text-stone-400">
                    <span>Prev: {{ $k['units_sold']['prev_formatted'] }}</span>
                    <span>Volume</span>
                </div>
            </div>

            {{-- 4. Gross Revenue --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:shadow-xs transition duration-200">
                <div class="flex items-center justify-between text-stone-500 mb-1">
                    <span class="text-xs font-medium text-stone-500">Gross Revenue</span>
                    <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-emerald-600">
                        {{ $k['gross_revenue']['change_pct'] > 0 ? '+' : '' }}{{ $k['gross_revenue']['change_pct'] }}%
                    </span>
                </div>
                <div class="text-xl font-bold font-mono text-stone-900">{{ $k['gross_revenue']['formatted'] }}</div>
                <div class="mt-2 pt-2 border-t border-stone-100 flex items-center justify-between text-[10.5px] text-stone-400">
                    <span>Prev: {{ $k['gross_revenue']['prev_formatted'] }}</span>
                    <span>Gross Sales</span>
                </div>
            </div>

            {{-- 5. Net Revenue --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:shadow-xs transition duration-200">
                <div class="flex items-center justify-between text-stone-500 mb-1">
                    <span class="text-xs font-medium text-stone-500">Net Revenue</span>
                    <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-emerald-600">
                        {{ $k['net_revenue']['change_pct'] > 0 ? '+' : '' }}{{ $k['net_revenue']['change_pct'] }}%
                    </span>
                </div>
                <div class="text-xl font-bold font-mono text-emerald-800">{{ $k['net_revenue']['formatted'] }}</div>
                <div class="mt-2 pt-2 border-t border-stone-100 flex items-center justify-between text-[10.5px] text-stone-400">
                    <span>Prev: {{ $k['net_revenue']['prev_formatted'] }}</span>
                    <span>After Refunds</span>
                </div>
            </div>

            {{-- 6. Average Selling Price (ASP) --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:shadow-xs transition duration-200">
                <div class="flex items-center justify-between text-stone-500 mb-1">
                    <span class="text-xs font-medium text-stone-500">Avg Selling Price</span>
                    <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-emerald-600">
                        {{ $k['asp']['change_pct'] > 0 ? '+' : '' }}{{ $k['asp']['change_pct'] }}%
                    </span>
                </div>
                <div class="text-xl font-bold font-mono text-stone-900">{{ $k['asp']['formatted'] }}</div>
                <div class="mt-2 pt-2 border-t border-stone-100 flex items-center justify-between text-[10.5px] text-stone-400">
                    <span>Prev: {{ $k['asp']['prev_formatted'] }}</span>
                    <span>ASP Metric</span>
                </div>
            </div>

            {{-- 7. Refund Rate --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:shadow-xs transition duration-200">
                <div class="flex items-center justify-between text-stone-500 mb-1">
                    <span class="text-xs font-medium text-stone-500">Refund Rate</span>
                    <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-emerald-600">
                        {{ $k['refund_rate']['formatted'] }}
                    </span>
                </div>
                <div class="text-xl font-bold font-mono text-stone-900">{{ $k['refund_rate']['formatted'] }}</div>
                <div class="mt-2 pt-2 border-t border-stone-100 flex items-center justify-between text-[10.5px] text-stone-400">
                    <span>Prev: {{ $k['refund_rate']['prev_formatted'] }}</span>
                    <span>Order Quality</span>
                </div>
            </div>

            {{-- 8. Inventory Value --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:shadow-xs transition duration-200">
                <div class="flex items-center justify-between text-stone-500 mb-1">
                    <span class="text-xs font-medium text-stone-500">Inventory Value</span>
                    <span class="text-[11px] font-medium text-stone-400">Live</span>
                </div>
                <div class="text-xl font-bold font-mono text-stone-900">{{ $k['inventory_value']['formatted'] }}</div>
                <div class="mt-2 pt-2 border-t border-stone-100 flex items-center justify-between text-[10.5px] text-stone-400">
                    <span>Stock on Hand</span>
                    <span>Asset Value</span>
                </div>
            </div>

            {{-- 9. Best Selling Product --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:shadow-xs transition duration-200">
                <div class="flex items-center justify-between text-stone-500 mb-1">
                    <span class="text-xs font-medium text-emerald-800 font-semibold">Best Seller</span>
                    <span class="text-xs">🏆</span>
                </div>
                <div class="text-sm font-bold text-stone-900 truncate" title="{{ $k['best_selling_product']['name'] }}">
                    {{ $k['best_selling_product']['name'] }}
                </div>
                <div class="mt-2 pt-2 border-t border-stone-100 text-[10.5px] text-stone-500 truncate">
                    {{ $k['best_selling_product']['subtext'] }}
                </div>
            </div>

            {{-- 10. Worst Performing Product --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:shadow-xs transition duration-200">
                <div class="flex items-center justify-between text-stone-500 mb-1">
                    <span class="text-xs font-medium text-rose-700 font-semibold">Underperformer</span>
                    <span class="text-xs">⚠️</span>
                </div>
                <div class="text-sm font-bold text-stone-900 truncate" title="{{ $k['worst_performing_product']['name'] }}">
                    {{ $k['worst_performing_product']['name'] }}
                </div>
                <div class="mt-2 pt-2 border-t border-stone-100 text-[10.5px] text-stone-500 truncate">
                    {{ $k['worst_performing_product']['subtext'] }}
                </div>
            </div>

        </div>

        {{-- =========================================================================
             SECTION 1: REVENUE TREND (Line Chart) & SECTION 2: UNITS SOLD (Bar Chart)
             ========================================================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Section 1: Revenue Trend (Line Chart) --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-stone-900">Section 1: Revenue Trend</h3>
                        <p class="text-[11px] text-stone-400">Gross revenue, Net revenue & Previous period trajectory</p>
                    </div>
                    <div class="flex items-center gap-1 bg-stone-100 p-0.5 rounded-lg text-[11px]">
                        <button type="button" onclick="switchTrendInterval('daily')" class="interval-btn px-2 py-0.5 rounded font-semibold bg-white text-stone-800 shadow-2xs">Daily</button>
                        <button type="button" onclick="switchTrendInterval('weekly')" class="interval-btn px-2 py-0.5 rounded font-medium text-stone-500 hover:text-stone-800">Weekly</button>
                        <button type="button" onclick="switchTrendInterval('monthly')" class="interval-btn px-2 py-0.5 rounded font-medium text-stone-500 hover:text-stone-800">Monthly</button>
                    </div>
                </div>
                <div id="chart-product-revenue-trend" class="min-h-[280px]"></div>
            </div>

            {{-- Section 2: Units Sold (Vertical Bar Chart) --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-stone-900">Section 2: Units Sold</h3>
                        <p class="text-[11px] text-stone-400">Quantity of physical units sold across the period</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-stone-800">
                        Total: {{ number_format($dashboardData['units_sold_trend']['total_units']) }} units
                    </span>
                </div>
                <div id="chart-units-sold-bar" class="min-h-[280px]"></div>
            </div>

        </div>

        {{-- =========================================================================
             SECTION 3: TOP SELLING PRODUCTS & SECTION 4: REVENUE BY CATEGORY
             ========================================================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Section 3: Top Selling Products (Horizontal Bar Chart) --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-stone-900">Section 3: Top Selling Products</h3>
                        <p class="text-[11px] text-stone-400">Rank catalog items by volume velocity or revenue</p>
                    </div>
                    <div class="flex items-center gap-1 bg-stone-100 p-0.5 rounded-lg text-[11px]">
                        <button type="button" onclick="toggleTopProductsMetric('units')" id="btn-metric-units" class="px-2 py-0.5 rounded font-semibold bg-white text-stone-800 shadow-2xs">Units Sold</button>
                        <button type="button" onclick="toggleTopProductsMetric('revenue')" id="btn-metric-revenue" class="px-2 py-0.5 rounded font-medium text-stone-500 hover:text-stone-800">Revenue</button>
                    </div>
                </div>
                <div id="chart-top-selling-bar" class="min-h-[280px]"></div>
            </div>

            {{-- Section 4: Revenue by Category (Interactive Donut Chart) --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-stone-900">Section 4: Revenue by Category</h3>
                        <p class="text-[11px] text-stone-400">Click a slice to instantly filter the entire dashboard</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-800">
                        ₹{{ number_format($dashboardData['category_revenue']['total_revenue'], 2) }}
                    </span>
                </div>
                <div id="chart-category-donut" class="min-h-[280px]"></div>
            </div>

        </div>

        {{-- =========================================================================
             SECTION 5: PRODUCT PERFORMANCE MATRIX (Bubble Chart)
             ========================================================================= --}}
        <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3 border-b border-stone-100 gap-2">
                <div>
                    <h3 class="text-sm font-semibold text-stone-900">Section 5: Product Performance Matrix</h3>
                    <p class="text-[11px] text-stone-400">X = Revenue (₹) · Y = Units Sold · Bubble Size = Gross Profit · Color = Stock Health</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-[11px]">
                    <span class="inline-flex items-center gap-1 text-emerald-700 font-medium">
                        <span class="h-2 w-2 rounded-full bg-[#10B981]"></span> In Stock
                    </span>
                    <span class="inline-flex items-center gap-1 text-amber-700 font-medium">
                        <span class="h-2 w-2 rounded-full bg-[#F59E0B]"></span> Low Stock
                    </span>
                    <span class="inline-flex items-center gap-1 text-rose-700 font-medium">
                        <span class="h-2 w-2 rounded-full bg-[#EF4444]"></span> Out of Stock
                    </span>
                    <span class="inline-flex items-center gap-1 text-blue-700 font-medium">
                        <span class="h-2 w-2 rounded-full bg-[#3B82F6]"></span> Overstock
                    </span>
                </div>
            </div>

            {{-- Matrix Legend Badges --}}
            <div class="flex flex-wrap items-center gap-3 text-xs bg-stone-50 p-2.5 rounded-xl border border-stone-100">
                <span class="font-bold text-stone-700">Identified Quadrants:</span>
                <span class="inline-flex items-center gap-1 rounded-md bg-emerald-100 text-emerald-800 px-2 py-0.5 text-[10.5px] font-semibold">
                    Best Sellers: {{ implode(', ', $dashboardData['performance_matrix']['segments']['best_sellers']) ?: 'None' }}
                </span>
                <span class="inline-flex items-center gap-1 rounded-md bg-blue-100 text-blue-800 px-2 py-0.5 text-[10.5px] font-semibold">
                    Hidden Opportunities: {{ implode(', ', $dashboardData['performance_matrix']['segments']['hidden_opportunities']) ?: 'None' }}
                </span>
                <span class="inline-flex items-center gap-1 rounded-md bg-stone-200 text-stone-700 px-2 py-0.5 text-[10.5px] font-semibold">
                    Low Performers: {{ implode(', ', $dashboardData['performance_matrix']['segments']['low_performers']) ?: 'None' }}
                </span>
            </div>

            <div id="chart-performance-matrix" class="min-h-[300px]"></div>
        </div>

        {{-- =========================================================================
             SECTION 6: INVENTORY HEALTH & SECTION 7: REVENUE DISTRIBUTION (Treemap)
             ========================================================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Section 6: Inventory Health (Stacked Bar Chart) --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-stone-900">Section 6: Inventory Health</h3>
                        <p class="text-[11px] text-stone-400">Live stock level distribution: Healthy, Low, Out of Stock, Overstock</p>
                    </div>
                    <span class="text-xs font-semibold text-stone-600">Real-Time Sync</span>
                </div>
                <div id="chart-inventory-health-stacked" class="min-h-[280px]"></div>
            </div>

            {{-- Section 7: Revenue Distribution (Treemap) --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-stone-900">Section 7: Revenue Distribution</h3>
                        <p class="text-[11px] text-stone-400">Rectangle Size = Product Revenue · Color Shading = Profitability Margin</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-stone-800">Catalog Share</span>
                </div>
                <div id="chart-revenue-treemap" class="min-h-[280px]"></div>
            </div>

        </div>

        {{-- =========================================================================
             SECTION 8: PRODUCT LIFECYCLE & SECTION 9: AI BUSINESS INSIGHTS
             ========================================================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Section 8: Product Lifecycle (Stage Curve) --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-stone-900">Section 8: Product Lifecycle</h3>
                        <p class="text-[11px] text-stone-400">Sales progression from Launch → Growth → Peak → Decline</p>
                    </div>
                    <span class="text-xs font-semibold text-stone-700">Cohort Lifecycle</span>
                </div>
                <div id="chart-product-lifecycle" class="min-h-[280px]"></div>
            </div>

            {{-- Section 9: AI Business Insights --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div>
                        <h3 class="text-sm font-semibold text-stone-900 flex items-center gap-1.5">
                            <span>Section 9: AI Business Insights</span>
                            <span class="inline-flex items-center rounded-full bg-purple-50 border border-purple-200 px-2 py-0.5 text-[10px] font-bold text-purple-700">AI Intelligence</span>
                        </h3>
                        <p class="text-[11px] text-stone-400">Automated diagnostic alerts on revenue concentration, velocity, and stock runway.</p>
                    </div>
                </div>

                <div class="space-y-2.5 max-h-[300px] overflow-y-auto pr-1">
                    @forelse($dashboardData['ai_insights'] as $ins)
                        @php
                            $typeStyles = [
                                'risk' => 'border-rose-200 bg-rose-50/70 text-rose-900',
                                'warning' => 'border-amber-200 bg-amber-50/70 text-amber-900',
                                'opportunity' => 'border-emerald-200 bg-emerald-50/70 text-emerald-900',
                                'trend' => 'border-blue-200 bg-blue-50/70 text-blue-900',
                            ];
                            $cardCls = $typeStyles[$ins['type']] ?? 'border-stone-200 bg-stone-50 text-stone-900';
                        @endphp
                        <div class="rounded-xl border p-3 text-xs {{ $cardCls }} space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold tracking-tight">{{ $ins['title'] }}</span>
                                <span class="inline-flex rounded-full bg-white/80 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                                    {{ $ins['badge'] }}
                                </span>
                            </div>
                            <p class="text-[11px] opacity-90">{{ $ins['description'] }}</p>
                        </div>
                    @empty
                        <div class="rounded-xl border border-stone-200 bg-stone-50/60 p-6 text-center text-xs text-stone-400">
                            No operational risks detected. All product metrics operating within baseline benchmarks.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- =========================================================================
             4 MASTER TABLES
             Table 1: Best Selling Products
             Table 2: Slow Moving Products
             Table 3: Never Sold Products
             Table 4: Most Refunded Products
             ========================================================================= --}}
        <div class="space-y-6">

            {{-- Table 1: Best Selling Products --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-stone-100 flex items-center justify-between bg-stone-50/40">
                    <div>
                        <h3 class="text-sm font-bold text-stone-900">Table 1: Best Selling Products</h3>
                        <p class="text-[11px] text-stone-400">Ranked by gross sales volume, net contribution, and inventory health</p>
                    </div>
                    <span class="text-xs font-semibold text-emerald-800">Top Revenue Champions</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-stone-50/70 border-b border-stone-200/80 text-[11px] font-bold uppercase text-stone-500">
                            <tr>
                                <th class="py-3 px-4">Product</th>
                                <th class="py-3 px-4">SKU</th>
                                <th class="py-3 px-4">Units Sold</th>
                                <th class="py-3 px-4">Revenue</th>
                                <th class="py-3 px-4">Net Revenue</th>
                                <th class="py-3 px-4">Refund %</th>
                                <th class="py-3 px-4">Current Stock</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Drill Down</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($dashboardData['tables']['best_selling'] as $row)
                                <tr class="hover:bg-stone-50/60 transition">
                                    <td class="py-3.5 px-4 font-semibold text-stone-900">
                                        <a href="{{ route('admin.products.show', $row['id']) }}" class="hover:text-emerald-800 hover:underline">
                                            {{ $row['name'] }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-stone-500">{{ $row['sku'] }}</td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-stone-900">{{ number_format($row['units_sold']) }}</td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-stone-900">{{ $row['formatted_revenue'] }}</td>
                                    <td class="py-3.5 px-4 font-mono font-semibold text-emerald-800">{{ $row['net_revenue'] }}</td>
                                    <td class="py-3.5 px-4 text-stone-600">{{ $row['refund_pct'] }}</td>
                                    <td class="py-3.5 px-4 font-mono">{{ $row['current_stock'] }} units</td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-[10.5px] font-semibold {{ $row['inventory_status'] === 'in_stock' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                                            {{ ucfirst(str_replace('_', ' ', $row['inventory_status'])) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <a href="{{ route('admin.products.show', $row['id']) }}" class="inline-flex items-center gap-1 rounded-lg border border-stone-200 bg-white px-2.5 py-1 text-xs font-semibold text-stone-700 hover:bg-stone-50">
                                            Open →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-8 text-center text-stone-400">No product sales in the selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Table 2: Slow Moving Products & Table 3: Never Sold Products --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Table 2: Slow Moving Products --}}
                <div class="rounded-2xl border border-stone-200/90 bg-white shadow-2xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-stone-100 bg-stone-50/40">
                        <h3 class="text-sm font-bold text-stone-900">Table 2: Slow Moving Products</h3>
                        <p class="text-[11px] text-stone-400">Inventory on hand with stagnant velocity (> 14 days since sale)</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-stone-50/70 border-b border-stone-200/80 text-[11px] font-bold uppercase text-stone-500">
                                <tr>
                                    <th class="py-3 px-4">Product</th>
                                    <th class="py-3 px-4">Stock</th>
                                    <th class="py-3 px-4">Days Since Sale</th>
                                    <th class="py-3 px-4">Recent Rev</th>
                                    <th class="py-3 px-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @forelse($dashboardData['tables']['slow_moving'] as $row)
                                    <tr class="hover:bg-stone-50/60">
                                        <td class="py-3.5 px-4 font-semibold text-stone-900">
                                            <a href="{{ route('admin.products.show', $row['id']) }}" class="hover:underline">
                                                {{ $row['name'] }}
                                            </a>
                                        </td>
                                        <td class="py-3.5 px-4 font-mono">{{ $row['current_stock'] }} units</td>
                                        <td class="py-3.5 px-4 font-semibold text-amber-700">{{ $row['days_since_sale'] }} days</td>
                                        <td class="py-3.5 px-4 font-mono">{{ $row['formatted_revenue'] }}</td>
                                        <td class="py-3.5 px-4 text-right">
                                            <a href="{{ route('admin.products.show', $row['id']) }}" class="text-emerald-800 font-semibold hover:underline">Open</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-stone-400">No stagnant products identified.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Table 3: Never Sold Products --}}
                <div class="rounded-2xl border border-stone-200/90 bg-white shadow-2xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-stone-100 bg-stone-50/40">
                        <h3 class="text-sm font-bold text-stone-900">Table 3: Never Sold Products</h3>
                        <p class="text-[11px] text-stone-400">Catalog items with zero historical checkout orders</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-stone-50/70 border-b border-stone-200/80 text-[11px] font-bold uppercase text-stone-500">
                                <tr>
                                    <th class="py-3 px-4">Product</th>
                                    <th class="py-3 px-4">Created Date</th>
                                    <th class="py-3 px-4">Stock</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @forelse($dashboardData['tables']['never_sold'] as $row)
                                    <tr class="hover:bg-stone-50/60">
                                        <td class="py-3.5 px-4 font-semibold text-stone-900">
                                            <a href="{{ route('admin.products.show', $row['id']) }}" class="hover:underline">
                                                {{ $row['name'] }}
                                            </a>
                                        </td>
                                        <td class="py-3.5 px-4 text-stone-500">{{ $row['created_date'] }}</td>
                                        <td class="py-3.5 px-4 font-mono">{{ $row['stock'] }} units</td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex rounded-full px-2 py-0.5 text-[10.5px] font-semibold bg-stone-100 text-stone-700 capitalize">
                                                {{ $row['status'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
                                            <a href="{{ route('admin.products.show', $row['id']) }}" class="text-emerald-800 font-semibold hover:underline">Open</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-stone-400">Every catalog item has recorded sales!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- Table 4: Most Refunded Products --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-stone-100 bg-stone-50/40 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-stone-900">Table 4: Most Refunded Products</h3>
                        <p class="text-[11px] text-stone-400">Products with returns, replacements, or refunded amounts</p>
                    </div>
                    <span class="text-xs font-semibold text-rose-700">Quality Monitor</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-stone-50/70 border-b border-stone-200/80 text-[11px] font-bold uppercase text-stone-500">
                            <tr>
                                <th class="py-3 px-4">Product</th>
                                <th class="py-3 px-4">Refund Count</th>
                                <th class="py-3 px-4">Refund %</th>
                                <th class="py-3 px-4">Revenue Lost</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($dashboardData['tables']['most_refunded'] as $row)
                                <tr class="hover:bg-stone-50/60">
                                    <td class="py-3.5 px-4 font-semibold text-stone-900">
                                        <a href="{{ route('admin.products.show', $row['id']) }}" class="hover:underline">
                                            {{ $row['name'] }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-rose-600">{{ $row['refund_count'] }}</td>
                                    <td class="py-3.5 px-4 font-semibold">{{ $row['refund_pct'] }}</td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-rose-700">{{ $row['formatted_lost'] }}</td>
                                    <td class="py-3.5 px-4 text-right">
                                        <a href="{{ route('admin.products.show', $row['id']) }}" class="text-emerald-800 font-semibold hover:underline">Open</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center">
                                        <div class="flex flex-col items-center justify-center text-stone-400">
                                            <svg class="h-8 w-8 text-emerald-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span class="text-xs font-semibold text-stone-700">Zero Returns & Refunds</span>
                                            <span class="text-[11px] text-stone-400 mt-0.5">Exceptional product satisfaction with 0.0% return rate across the period.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- ApexCharts CDN & Dynamic Script Execution --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var topSellingChart = null;
    var topProductsMetric = 'units';
    var topSellingData = @json($dashboardData['top_selling_products']);

    document.addEventListener('DOMContentLoaded', function () {
        // Section 1: Revenue Trend Line Chart
        var revTrendOptions = {
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'Inter, system-ui, sans-serif'
            },
            stroke: { curve: 'smooth', width: [2.5, 2, 2], dashArray: [0, 0, 4] },
            fill: {
                type: 'gradient',
                gradient: { opacityFrom: 0.35, opacityTo: 0.05 }
            },
            colors: ['#10B981', '#3B82F6', '#A8A29E'],
            series: @json($dashboardData['revenue_trend']['series']),
            xaxis: {
                categories: @json($dashboardData['revenue_trend']['labels']),
                labels: { style: { colors: '#78716c', fontSize: '11px' } }
            },
            yaxis: {
                labels: {
                    formatter: function (val) { return '₹' + Number(val).toLocaleString(); },
                    style: { colors: '#78716c', fontSize: '11px' }
                }
            },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 },
            tooltip: {
                y: { formatter: function (val) { return '₹' + Number(val).toLocaleString(); } }
            }
        };
        new ApexCharts(document.querySelector("#chart-product-revenue-trend"), revTrendOptions).render();

        // Section 2: Units Sold Vertical Bar Chart
        var unitsBarOptions = {
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'Inter, system-ui, sans-serif'
            },
            plotOptions: {
                bar: { borderRadius: 4, columnWidth: '50%' }
            },
            colors: ['#1c3d2c'],
            series: @json($dashboardData['units_sold_trend']['series']),
            xaxis: {
                categories: @json($dashboardData['units_sold_trend']['labels']),
                labels: { style: { colors: '#78716c', fontSize: '11px' } }
            },
            yaxis: {
                labels: { style: { colors: '#78716c', fontSize: '11px' } }
            },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 },
            tooltip: { theme: 'light' }
        };
        new ApexCharts(document.querySelector("#chart-units-sold-bar"), unitsBarOptions).render();

        // Section 3: Top Selling Products Horizontal Bar Chart
        var topSellingOptions = {
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'Inter, system-ui, sans-serif'
            },
            plotOptions: {
                bar: { horizontal: true, borderRadius: 4, barHeight: '55%' }
            },
            colors: ['#10B981'],
            series: [topSellingData.by_units],
            xaxis: {
                categories: topSellingData.categories,
                labels: { style: { colors: '#78716c', fontSize: '11px' } }
            },
            yaxis: {
                labels: { style: { colors: '#44403c', fontSize: '11px', fontWeight: 600 } }
            },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 }
        };
        topSellingChart = new ApexCharts(document.querySelector("#chart-top-selling-bar"), topSellingOptions);
        topSellingChart.render();

        // Section 4: Revenue by Category Interactive Donut
        var catDonutOptions = {
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'Inter, system-ui, sans-serif',
                events: {
                    dataPointSelection: function (event, chartContext, config) {
                        var catName = config.w.config.labels[config.dataPointIndex];
                        var select = document.getElementById('category-filter-select');
                        for (var i = 0; i < select.options.length; i++) {
                            if (select.options[i].text.toLowerCase() === catName.toLowerCase()) {
                                select.selectedIndex = i;
                                document.getElementById('product-analytics-filters').submit();
                                break;
                            }
                        }
                    }
                }
            },
            labels: @json($dashboardData['category_revenue']['labels']),
            series: @json($dashboardData['category_revenue']['series']),
            colors: ['#10B981', '#3B82F6', '#F59E0B', '#8B5CF6'],
            legend: { position: 'bottom', fontSize: '11px' },
            dataLabels: { enabled: true, dropShadow: { enabled: false } },
            stroke: { width: 0 }
        };
        new ApexCharts(document.querySelector("#chart-category-donut"), catDonutOptions).render();

        // Section 5: Product Performance Matrix (Bubble Chart)
        var matrixData = @json($dashboardData['performance_matrix']['bubbles']);
        var matrixSeries = [{
            name: 'Products',
            data: matrixData.map(function(b) {
                return [b.x, b.y, b.z];
            })
        }];
        var matrixOptions = {
            chart: {
                type: 'bubble',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'Inter, system-ui, sans-serif'
            },
            series: matrixSeries,
            fill: { opacity: 0.8 },
            colors: ['#10B981'],
            xaxis: {
                title: { text: 'Revenue (₹)', style: { fontSize: '11px' } },
                labels: {
                    formatter: function (v) { return '₹' + Number(v).toLocaleString(); },
                    style: { fontSize: '11px' }
                }
            },
            yaxis: {
                title: { text: 'Units Sold', style: { fontSize: '11px' } },
                labels: { style: { fontSize: '11px' } }
            },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 },
            tooltip: {
                custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                    var item = matrixData[dataPointIndex];
                    if (!item) return '';
                    return '<div class="p-2.5 text-xs space-y-1 bg-white border border-stone-200 rounded-lg shadow-md">' +
                        '<div class="font-bold text-stone-900">' + item.name + '</div>' +
                        '<div class="text-stone-600">Revenue: ₹' + Number(item.x).toLocaleString() + '</div>' +
                        '<div class="text-stone-600">Units Sold: ' + item.y + '</div>' +
                        '<div class="text-emerald-700 font-semibold">Gross Profit: ₹' + Number(item.gross_profit).toLocaleString() + '</div>' +
                        '<div class="text-stone-500">Stock on Hand: ' + item.stock + '</div>' +
                    '</div>';
                }
            }
        };
        new ApexCharts(document.querySelector("#chart-performance-matrix"), matrixOptions).render();

        // Section 6: Inventory Health Stacked Bar Chart
        var invHealthOptions = {
            chart: {
                type: 'bar',
                height: 280,
                stacked: true,
                toolbar: { show: false },
                fontFamily: 'Inter, system-ui, sans-serif'
            },
            plotOptions: {
                bar: { horizontal: false, borderRadius: 4, columnWidth: '45%' }
            },
            colors: ['#10B981', '#F59E0B', '#EF4444', '#3B82F6'],
            series: @json($dashboardData['inventory_health']['series']),
            xaxis: {
                categories: @json($dashboardData['inventory_health']['categories']),
                labels: { style: { colors: '#78716c', fontSize: '11px' } }
            },
            yaxis: {
                title: { text: 'Units / SKUs', style: { fontSize: '11px' } },
                labels: { style: { colors: '#78716c', fontSize: '11px' } }
            },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px' },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 }
        };
        new ApexCharts(document.querySelector("#chart-inventory-health-stacked"), invHealthOptions).render();

        // Section 7: Revenue Distribution Treemap
        var treemapOptions = {
            chart: {
                type: 'treemap',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'Inter, system-ui, sans-serif'
            },
            colors: ['#1c3d2c'],
            series: @json($dashboardData['revenue_treemap']['series']),
            plotOptions: {
                treemap: {
                    distributed: true,
                    enableShades: true,
                    shadeIntensity: 0.5
                }
            },
            tooltip: {
                y: { formatter: function (val) { return '₹' + Number(val).toLocaleString(); } }
            }
        };
        new ApexCharts(document.querySelector("#chart-revenue-treemap"), treemapOptions).render();

        // Section 8: Product Lifecycle Stage Curve
        var lifecycleOptions = {
            chart: {
                type: 'line',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'Inter, system-ui, sans-serif'
            },
            stroke: { curve: 'monotoneCubic', width: 3 },
            colors: ['#8B5CF6'],
            series: @json($dashboardData['product_lifecycle']['curve_series']),
            xaxis: {
                categories: @json($dashboardData['product_lifecycle']['categories']),
                labels: { style: { colors: '#78716c', fontSize: '11px', fontWeight: 600 } }
            },
            yaxis: {
                labels: {
                    formatter: function (v) { return '₹' + Number(v).toLocaleString(); },
                    style: { colors: '#78716c', fontSize: '11px' }
                }
            },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 }
        };
        new ApexCharts(document.querySelector("#chart-product-lifecycle"), lifecycleOptions).render();
    });

    // Toggle Top Products Units vs Revenue
    function toggleTopProductsMetric(metric) {
        topProductsMetric = metric;
        var btnUnits = document.getElementById('btn-metric-units');
        var btnRev = document.getElementById('btn-metric-revenue');

        if (metric === 'revenue') {
            btnRev.classList.add('bg-white', 'text-stone-800', 'shadow-2xs');
            btnRev.classList.remove('text-stone-500');
            btnUnits.classList.remove('bg-white', 'text-stone-800', 'shadow-2xs');
            btnUnits.classList.add('text-stone-500');
            topSellingChart.updateSeries([topSellingData.by_revenue]);
        } else {
            btnUnits.classList.add('bg-white', 'text-stone-800', 'shadow-2xs');
            btnUnits.classList.remove('text-stone-500');
            btnRev.classList.remove('bg-white', 'text-stone-800', 'shadow-2xs');
            btnRev.classList.add('text-stone-500');
            topSellingChart.updateSeries([topSellingData.by_units]);
        }
    }

    // Apply Date Preset Handler
    function applyPreset(presetKey) {
        document.getElementById('active-date-preset-input').value = presetKey;
        document.getElementById('product-analytics-filters').submit();
    }

    // Toggle Export Menu
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

    // Reload with cache bypass
    function reloadProductAnalytics(skipCache) {
        var url = new URL(window.location.href);
        if (skipCache) url.searchParams.set('refresh', '1');
        window.location.href = url.toString();
    }
</script>
@endsection
