@extends('layouts.admin', ['title' => 'Sales Analytics | Guided Wellness Business Intelligence'])

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<style>
    /* Guided Wellness Business Intelligence Dashboard Grid System */
    .gw-bi-grid-12 {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        width: 100%;
        box-sizing: border-box;
    }
    .gw-bi-grid-2 {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        width: 100%;
        box-sizing: border-box;
    }
    .gw-bi-grid-3 {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        width: 100%;
        box-sizing: border-box;
    }
    .gw-bi-grid-4 {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        width: 100%;
        box-sizing: border-box;
    }
    .gw-bi-filter-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
        width: 100%;
        box-sizing: border-box;
    }

    @media (min-width: 640px) {
        .gw-bi-grid-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .gw-bi-grid-3 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .gw-bi-filter-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (min-width: 1024px) {
        .gw-bi-grid-12 {
            grid-template-columns: repeat(12, minmax(0, 1fr));
        }
        .gw-bi-grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .gw-bi-grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .gw-bi-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
        .gw-bi-filter-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .gw-bi-span-12 { grid-column: span 12 / span 12; width: 100%; min-width: 0; }
        .gw-bi-span-8  { grid-column: span 8 / span 8; min-width: 0; }
        .gw-bi-span-7  { grid-column: span 7 / span 7; min-width: 0; }
        .gw-bi-span-6  { grid-column: span 6 / span 6; min-width: 0; }
        .gw-bi-span-5  { grid-column: span 5 / span 5; min-width: 0; }
        .gw-bi-span-4  { grid-column: span 4 / span 4; min-width: 0; }
    }

    /* Enterprise Card Styling */
    .gw-card {
        background-color: #ffffff;
        border: 1px solid rgba(231, 229, 228, 0.9);
        border-radius: 1rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
        padding: 1.25rem;
        box-sizing: border-box;
        min-width: 0;
        width: 100%;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .gw-card:hover {
        border-color: #d6d3d1;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
    }

    /* ApexCharts responsive container overrides */
    .apexcharts-canvas {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 auto;
    }
    .apexcharts-canvas svg {
        width: 100% !important;
        max-width: 100% !important;
    }
    .apexcharts-canvas foreignObject {
        width: 100% !important;
    }

    /* Leaflet container */
    #sales-geo-map {
        height: 380px;
        width: 100%;
        border-radius: 0.75rem;
        border: 1px solid #e7e5e4;
        z-index: 10;
    }
</style>

<div class="min-h-screen bg-stone-50 pb-16">
    {{-- Top Loading Bar --}}
    <div id="analytics-loader-bar" class="fixed top-0 left-0 right-0 h-1 bg-emerald-600 z-50 transition-all duration-300 transform -translate-y-full"></div>

    {{-- Main Container --}}
    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8 pt-6 space-y-6">

        {{-- Header & Quick Actions --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">Sales Analytics</h1>
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-800 border border-emerald-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live BI Engine
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-stone-500 mt-1">Real-time revenue intelligence, SKU performance, customer cohort economics, and geographical demand.</p>
            </div>

            {{-- Header Actions: Live Refresh & Exports --}}
            <div class="flex flex-wrap items-center gap-2.5">
                <button
                    type="button"
                    onclick="refreshAnalytics(true)"
                    id="btn-refresh-data"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 hover:border-stone-300 transition"
                    title="Bypass cache and refresh live database metrics"
                >
                    <svg id="refresh-icon" class="h-3.5 w-3.5 text-stone-500 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Refresh</span>
                </button>

                {{-- Export Dropdown --}}
                <div class="relative inline-block text-left" id="export-dropdown-wrapper">
                    <button
                        type="button"
                        onclick="toggleExportMenu()"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 hover:border-stone-300 transition"
                    >
                        <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export Report</span>
                        <svg class="h-3 w-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="export-dropdown-menu" class="hidden absolute right-0 z-30 mt-1.5 w-48 rounded-xl border border-stone-200 bg-white p-1 shadow-lg ring-1 ring-black/5">
                        <a href="javascript:void(0)" onclick="triggerExport('csv')" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-stone-700 hover:bg-stone-50 hover:text-stone-900 transition">
                            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export to CSV
                        </a>
                        <a href="javascript:void(0)" onclick="triggerExport('excel')" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-stone-700 hover:bg-stone-50 hover:text-stone-900 transition">
                            <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Export to Excel (.xls)
                        </a>
                    </div>
                </div>

                {{-- Orders Page Jump --}}
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-stone-900 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-stone-800 transition"
                >
                    <span>View Orders</span>
                    <svg class="h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        {{-- =========================================================================
             1. GLOBAL STICKY FILTER BAR
             ========================================================================= --}}
        <div class="sticky top-2 z-30 rounded-2xl border border-stone-200/90 bg-white/95 backdrop-blur-md p-3.5 sm:p-4 shadow-sm transition-all duration-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                {{-- Date Presets --}}
                <div class="flex flex-wrap items-center gap-1.5">
                    @php
                        $activePreset = $filters['date_preset'] ?? 'last_30_days';
                        $presets = [
                            'today' => 'Today',
                            'yesterday' => 'Yesterday',
                            'last_7_days' => 'Last 7 Days',
                            'last_30_days' => 'Last 30 Days',
                            'this_month' => 'This Month',
                            'last_month' => 'Last Month',
                            'custom' => 'Custom Range',
                        ];
                    @endphp

                    @foreach($presets as $key => $label)
                        <button
                            type="button"
                            onclick="setFilterPreset('{{ $key }}')"
                            id="preset-btn-{{ $key }}"
                            class="preset-filter-btn rounded-xl px-3 py-1.5 text-xs font-semibold transition {{ $activePreset === $key ? 'bg-stone-900 text-white shadow-2xs' : 'bg-stone-100/80 text-stone-600 hover:bg-stone-200/70 hover:text-stone-900' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                {{-- Comparison Toggle & Drawer Toggle --}}
                <div class="flex items-center gap-3 ml-auto">
                    {{-- Compare Previous Period Switch --}}
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none text-xs text-stone-600 font-medium">
                        <input
                            type="checkbox"
                            id="filter-compare-previous"
                            onchange="triggerFilterChange()"
                            {{ !empty($filters['compare_previous']) && $filters['compare_previous'] == '1' ? 'checked' : '' }}
                            class="sr-only peer"
                        >
                        <div class="relative w-8 h-4 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="text-xs text-stone-600 font-medium">Compare Previous Period</span>
                    </label>

                    {{-- Additional Filters Button --}}
                    <button
                        type="button"
                        onclick="toggleFilterDrawer()"
                        id="filter-drawer-btn"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-stone-50 px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-100 transition"
                    >
                        <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Filter By</span>
                        <span id="active-filter-badge" class="hidden h-4 px-1.5 rounded-full bg-emerald-600 text-white text-[10px] font-bold">0</span>
                    </button>
                </div>
            </div>

            {{-- Custom Date Inputs (Displayed when custom is active) --}}
            <div id="custom-date-row" class="{{ $activePreset === 'custom' ? 'flex' : 'hidden' }} flex-wrap items-center gap-3 pt-3 mt-3 border-t border-stone-100">
                <div class="flex items-center gap-2 text-xs text-stone-500">
                    <span class="font-medium">From:</span>
                    <input
                        type="date"
                        id="filter-start-date"
                        value="{{ $dashboardData['meta']['start_date'] ?? '' }}"
                        onchange="triggerFilterChange()"
                        class="rounded-xl border border-stone-200 bg-white px-2.5 py-1 text-xs text-stone-800 outline-none focus:border-emerald-600"
                    >
                </div>
                <div class="flex items-center gap-2 text-xs text-stone-500">
                    <span class="font-medium">To:</span>
                    <input
                        type="date"
                        id="filter-end-date"
                        value="{{ $dashboardData['meta']['end_date'] ?? '' }}"
                        onchange="triggerFilterChange()"
                        class="rounded-xl border border-stone-200 bg-white px-2.5 py-1 text-xs text-stone-800 outline-none focus:border-emerald-600"
                    >
                </div>
                <button
                    type="button"
                    onclick="triggerFilterChange()"
                    class="rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-3 py-1 text-xs font-semibold transition"
                >
                    Apply Range
                </button>
            </div>

            {{-- Collapsible Multi-Attribute Filters Drawer --}}
            <div id="filter-drawer" class="hidden pt-4 mt-3 border-t border-stone-100 space-y-4">
                <div class="gw-bi-filter-grid text-xs">
                    {{-- Country --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">Country</label>
                        <select id="filter-country" onchange="triggerFilterChange()" class="w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs text-stone-800 outline-none focus:border-emerald-600">
                            <option value="">All Countries</option>
                            @foreach($filterOptions['countries'] ?? [] as $country)
                                <option value="{{ $country }}" {{ ($filters['country'] ?? '') === $country ? 'selected' : '' }}>{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- State --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">State / Province</label>
                        <select id="filter-state" onchange="triggerFilterChange()" class="w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs text-stone-800 outline-none focus:border-emerald-600">
                            <option value="">All States</option>
                            @foreach($filterOptions['states'] ?? [] as $st)
                                <option value="{{ $st }}" {{ ($filters['state'] ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- City --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">City</label>
                        <select id="filter-city" onchange="triggerFilterChange()" class="w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs text-stone-800 outline-none focus:border-emerald-600">
                            <option value="">All Cities</option>
                            @foreach($filterOptions['cities'] ?? [] as $city)
                                <option value="{{ $city }}" {{ ($filters['city'] ?? '') === $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">Payment Gateway</label>
                        <select id="filter-payment-method" onchange="triggerFilterChange()" class="w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs text-stone-800 outline-none focus:border-emerald-600">
                            <option value="">All Payment Methods</option>
                            @foreach($filterOptions['payment_methods'] ?? [] as $pm)
                                <option value="{{ $pm['value'] }}" {{ ($filters['payment_method'] ?? '') === $pm['value'] ? 'selected' : '' }}>{{ $pm['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Product --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">Product</label>
                        <select id="filter-product-id" onchange="triggerFilterChange()" class="w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs text-stone-800 outline-none focus:border-emerald-600">
                            <option value="">All Products</option>
                            @foreach($filterOptions['products'] ?? [] as $prod)
                                <option value="{{ $prod['id'] }}" {{ ($filters['product_id'] ?? '') == $prod['id'] ? 'selected' : '' }}>{{ $prod['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">Category</label>
                        <select id="filter-category-id" onchange="triggerFilterChange()" class="w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs text-stone-800 outline-none focus:border-emerald-600">
                            <option value="">All Categories</option>
                            @foreach($filterOptions['categories'] ?? [] as $cat)
                                <option value="{{ $cat['id'] }}" {{ ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' }}>{{ $cat['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Channel / Source --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">Sales Channel</label>
                        <select id="filter-channel" onchange="triggerFilterChange()" class="w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs text-stone-800 outline-none focus:border-emerald-600">
                            <option value="">All Channels</option>
                            @foreach($filterOptions['channels'] ?? [] as $ch)
                                <option value="{{ $ch['value'] }}" {{ ($filters['channel'] ?? '') === $ch['value'] ? 'selected' : '' }}>{{ $ch['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Order Status --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-1">Status</label>
                        <select id="filter-order-status" onchange="triggerFilterChange()" class="w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs text-stone-800 outline-none focus:border-emerald-600">
                            <option value="">All Statuses</option>
                            @foreach($filterOptions['order_statuses'] ?? [] as $st)
                                <option value="{{ $st['value'] }}" {{ ($filters['order_status'] ?? '') === $st['value'] ? 'selected' : '' }}>{{ $st['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Filter Actions --}}
                <div class="flex items-center justify-between pt-2">
                    <button
                        type="button"
                        onclick="clearAllFilters()"
                        class="text-xs text-rose-600 hover:text-rose-800 font-semibold transition"
                    >
                        Reset All Filters
                    </button>
                    <button
                        type="button"
                        onclick="toggleFilterDrawer()"
                        class="rounded-xl border border-stone-200 bg-white px-3 py-1 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition"
                    >
                        Close Drawer
                    </button>
                </div>
            </div>
        </div>

        {{-- =========================================================================
             2. KPI SECTION (8 PREMIUM CARDS)
             ========================================================================= --}}
        @php
            $kpis = $dashboardData['kpis'] ?? [];
        @endphp
        <div class="gw-bi-grid-4">
            {{-- 1. Gross Sales --}}
            <div class="rounded-2xl border border-stone-200/80 bg-white p-4 sm:p-5 shadow-2xs hover:border-stone-300 hover:shadow-xs transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-1 mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Gross Sales</span>
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-stone-100 text-stone-600">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    <div id="kpi-gross-sales" class="text-xl sm:text-2xl font-bold tracking-tight text-stone-900 font-mono">
                        {{ $kpis['gross_sales']['formatted'] ?? '₹0.00' }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px]">
                    <span id="kpi-gross-sales-delta" class="inline-flex items-center gap-1 font-semibold {{ ($kpis['gross_sales']['trend'] ?? '') === 'up' ? 'text-emerald-700' : 'text-stone-500' }}">
                        <span>{{ ($kpis['gross_sales']['delta_percent'] ?? 0) >= 0 ? '+' : '' }}{{ $kpis['gross_sales']['delta_percent'] ?? 0 }}%</span>
                    </span>
                    <span id="kpi-gross-sales-prev" class="text-stone-400 truncate" title="Previous: {{ $kpis['gross_sales']['formatted_previous'] ?? '₹0' }}">
                        vs {{ $kpis['gross_sales']['formatted_previous'] ?? '₹0' }}
                    </span>
                </div>
            </div>

            {{-- 2. Net Sales --}}
            <div class="rounded-2xl border border-stone-200/80 bg-white p-4 sm:p-5 shadow-2xs hover:border-stone-300 hover:shadow-xs transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-1 mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Net Sales</span>
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </span>
                    </div>
                    <div id="kpi-net-sales" class="text-xl sm:text-2xl font-bold tracking-tight text-[#204b34] font-mono">
                        {{ $kpis['net_sales']['formatted'] ?? '₹0.00' }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px]">
                    <span id="kpi-net-sales-delta" class="inline-flex items-center gap-1 font-semibold {{ ($kpis['net_sales']['trend'] ?? '') === 'up' ? 'text-emerald-700' : 'text-rose-600' }}">
                        <span>{{ ($kpis['net_sales']['delta_percent'] ?? 0) >= 0 ? '+' : '' }}{{ $kpis['net_sales']['delta_percent'] ?? 0 }}%</span>
                    </span>
                    <span id="kpi-net-sales-prev" class="text-stone-400 truncate" title="Previous: {{ $kpis['net_sales']['formatted_previous'] ?? '₹0' }}">
                        vs {{ $kpis['net_sales']['formatted_previous'] ?? '₹0' }}
                    </span>
                </div>
            </div>

            {{-- 3. Total Orders --}}
            <div class="rounded-2xl border border-stone-200/80 bg-white p-4 sm:p-5 shadow-2xs hover:border-stone-300 hover:shadow-xs transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-1 mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Total Orders</span>
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </span>
                    </div>
                    <div id="kpi-total-orders" class="text-xl sm:text-2xl font-bold tracking-tight text-stone-900 font-mono">
                        {{ $kpis['total_orders']['formatted'] ?? '0' }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px]">
                    <span id="kpi-total-orders-delta" class="inline-flex items-center gap-1 font-semibold {{ ($kpis['total_orders']['trend'] ?? '') === 'up' ? 'text-emerald-700' : 'text-stone-500' }}">
                        <span>{{ ($kpis['total_orders']['delta_percent'] ?? 0) >= 0 ? '+' : '' }}{{ $kpis['total_orders']['delta_percent'] ?? 0 }}%</span>
                    </span>
                    <span id="kpi-total-orders-prev" class="text-stone-400 truncate">
                        vs {{ $kpis['total_orders']['formatted_previous'] ?? '0' }}
                    </span>
                </div>
            </div>

            {{-- 4. Average Order Value --}}
            <div class="rounded-2xl border border-stone-200/80 bg-white p-4 sm:p-5 shadow-2xs hover:border-stone-300 hover:shadow-xs transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-1 mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Average Order Value</span>
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </span>
                    </div>
                    <div id="kpi-aov" class="text-xl sm:text-2xl font-bold tracking-tight text-amber-950 font-mono">
                        {{ $kpis['average_order_value']['formatted'] ?? '₹0.00' }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px]">
                    <span id="kpi-aov-delta" class="inline-flex items-center gap-1 font-semibold {{ ($kpis['average_order_value']['trend'] ?? '') === 'up' ? 'text-emerald-700' : 'text-rose-600' }}">
                        <span>{{ ($kpis['average_order_value']['delta_percent'] ?? 0) >= 0 ? '+' : '' }}{{ $kpis['average_order_value']['delta_percent'] ?? 0 }}%</span>
                    </span>
                    <span id="kpi-aov-prev" class="text-stone-400 truncate">
                        vs {{ $kpis['average_order_value']['formatted_previous'] ?? '₹0' }}
                    </span>
                </div>
            </div>

            {{-- 5. Refund Amount --}}
            <div class="rounded-2xl border border-stone-200/80 bg-white p-4 sm:p-5 shadow-2xs hover:border-stone-300 hover:shadow-xs transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-1 mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Refund Amount</span>
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-rose-50 text-rose-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        </span>
                    </div>
                    <div id="kpi-refund-amount" class="text-xl sm:text-2xl font-bold tracking-tight text-rose-950 font-mono">
                        {{ $kpis['refund_amount']['formatted'] ?? '₹0.00' }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px]">
                    <span id="kpi-refund-amount-delta" class="inline-flex items-center gap-1 font-semibold {{ ($kpis['refund_amount']['trend'] ?? '') === 'up' ? 'text-emerald-700' : 'text-rose-600' }}">
                        <span>{{ ($kpis['refund_amount']['delta_percent'] ?? 0) >= 0 ? '+' : '' }}{{ $kpis['refund_amount']['delta_percent'] ?? 0 }}%</span>
                    </span>
                    <span id="kpi-refund-amount-prev" class="text-stone-400 truncate">
                        vs {{ $kpis['refund_amount']['formatted_previous'] ?? '₹0' }}
                    </span>
                </div>
            </div>

            {{-- 6. Refund Count --}}
            <div class="rounded-2xl border border-stone-200/80 bg-white p-4 sm:p-5 shadow-2xs hover:border-stone-300 hover:shadow-xs transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-1 mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Refund Count</span>
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-rose-50 text-rose-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m0 0l-6-6m6 6H3"/></svg>
                        </span>
                    </div>
                    <div id="kpi-refund-count" class="text-xl sm:text-2xl font-bold tracking-tight text-stone-900 font-mono">
                        {{ $kpis['refund_count']['formatted'] ?? '0' }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px]">
                    <span id="kpi-refund-count-delta" class="inline-flex items-center gap-1 font-semibold {{ ($kpis['refund_count']['trend'] ?? '') === 'up' ? 'text-emerald-700' : 'text-rose-600' }}">
                        <span>{{ ($kpis['refund_count']['delta_percent'] ?? 0) >= 0 ? '+' : '' }}{{ $kpis['refund_count']['delta_percent'] ?? 0 }}%</span>
                    </span>
                    <span id="kpi-refund-count-prev" class="text-stone-400 truncate">
                        vs {{ $kpis['refund_count']['formatted_previous'] ?? '0' }}
                    </span>
                </div>
            </div>

            {{-- 7. Total Customers --}}
            <div class="rounded-2xl border border-stone-200/80 bg-white p-4 sm:p-5 shadow-2xs hover:border-stone-300 hover:shadow-xs transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-1 mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Total Customers</span>
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-teal-50 text-teal-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                    </div>
                    <div id="kpi-total-customers" class="text-xl sm:text-2xl font-bold tracking-tight text-stone-900 font-mono">
                        {{ $kpis['total_customers']['formatted'] ?? '0' }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px]">
                    <span id="kpi-total-customers-delta" class="inline-flex items-center gap-1 font-semibold {{ ($kpis['total_customers']['trend'] ?? '') === 'up' ? 'text-emerald-700' : 'text-stone-500' }}">
                        <span>{{ ($kpis['total_customers']['delta_percent'] ?? 0) >= 0 ? '+' : '' }}{{ $kpis['total_customers']['delta_percent'] ?? 0 }}%</span>
                    </span>
                    <span id="kpi-total-customers-prev" class="text-stone-400 truncate">
                        vs {{ $kpis['total_customers']['formatted_previous'] ?? '0' }}
                    </span>
                </div>
            </div>

            {{-- 8. Returning Customers --}}
            <div class="rounded-2xl border border-stone-200/80 bg-white p-4 sm:p-5 shadow-2xs hover:border-stone-300 hover:shadow-xs transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-1 mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Returning Customers</span>
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-violet-50 text-violet-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </span>
                    </div>
                    <div id="kpi-returning-customers" class="text-xl sm:text-2xl font-bold tracking-tight text-violet-950 font-mono">
                        {{ $kpis['returning_customers']['formatted'] ?? '0' }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px]">
                    <span id="kpi-returning-customers-delta" class="inline-flex items-center gap-1 font-semibold {{ ($kpis['returning_customers']['trend'] ?? '') === 'up' ? 'text-emerald-700' : 'text-stone-500' }}">
                        <span>{{ ($kpis['returning_customers']['delta_percent'] ?? 0) >= 0 ? '+' : '' }}{{ $kpis['returning_customers']['delta_percent'] ?? 0 }}%</span>
                    </span>
                    <span id="kpi-returning-customers-prev" class="text-stone-400 truncate">
                        vs {{ $kpis['returning_customers']['formatted_previous'] ?? '0' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- =========================================================================
             14. OPERATIONAL INSIGHTS (AI / SMART INTELLIGENCE)
             ========================================================================= --}}
        <div class="gw-card space-y-3.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-100 text-emerald-800">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                    <h2 class="text-sm font-bold text-stone-900 uppercase tracking-wider">Operational BI Insights</h2>
                </div>
                <span class="text-[11px] text-stone-400">Algorithmic business diagnosis</span>
            </div>

            <div id="operational-insights-container" class="gw-bi-grid-3">
                @foreach($dashboardData['operational_insights'] ?? [] as $insight)
                    <div class="rounded-xl border border-stone-100 bg-stone-50/60 p-3.5 flex flex-col justify-between hover:bg-stone-50 transition">
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-stone-500 text-[10.5px] uppercase tracking-wider">{{ $insight['title'] }}</span>
                                <span class="h-2 w-2 rounded-full {{ $insight['trend'] === 'positive' ? 'bg-emerald-500' : ($insight['trend'] === 'warning' ? 'bg-amber-500' : 'bg-stone-400') }}"></span>
                            </div>
                            <div class="text-lg font-bold text-stone-900 font-mono mt-0.5">{{ $insight['metric'] }}</div>
                            <p class="text-xs text-stone-600 mt-1 leading-snug">{{ $insight['subtitle'] }}</p>
                        </div>
                        @if(!empty($insight['action_label']))
                            <div class="mt-3 pt-2 border-t border-stone-200/60 flex items-center justify-between text-xs">
                                <button
                                    type="button"
                                    onclick="applyInsightFilter({{ json_encode($insight['filter_param'] ?? []) }})"
                                    class="text-emerald-700 hover:text-emerald-900 font-semibold inline-flex items-center gap-1 transition"
                                >
                                    <span>{{ $insight['action_label'] }}</span>
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- =========================================================================
             3. REVENUE TREND (LARGE FULL-WIDTH CHART)
             ========================================================================= --}}
        <div class="gw-card space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Revenue Trend</h2>
                    <p class="text-xs text-stone-500">Gross Sales, Realized Net Revenue, and Prior Period Comparisons over time.</p>
                </div>

                {{-- Time Bucket Interval Switcher (Hourly, Daily, Weekly, Monthly) --}}
                <div class="inline-flex rounded-xl border border-stone-200 bg-stone-50 p-1 text-xs">
                    @foreach(['hourly' => 'Hourly', 'daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $ivKey => $ivLabel)
                        <button
                            type="button"
                            onclick="setIntervalGrouping('{{ $ivKey }}')"
                            id="interval-btn-{{ $ivKey }}"
                            class="interval-btn rounded-lg px-2.5 py-1 font-semibold transition {{ ($dashboardData['meta']['interval'] ?? 'daily') === $ivKey ? 'bg-white text-stone-900 shadow-2xs' : 'text-stone-600 hover:text-stone-900' }}"
                        >
                            {{ $ivLabel }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- ApexChart Container --}}
            <div id="chart-revenue-trend" class="min-h-[340px]"></div>
        </div>

        {{-- =========================================================================
             4. ORDERS TREND & 5. SALES BY CHANNEL
             ========================================================================= --}}
        <div class="gw-bi-grid-12">
            {{-- 4. Orders Trend Line/Bar --}}
            <div class="gw-card gw-bi-span-7 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <h2 class="text-base font-bold text-stone-900">Orders Velocity</h2>
                        <span id="orders-peak-badge" class="rounded-lg bg-indigo-50 text-indigo-700 px-2.5 py-1 text-[11px] font-semibold">
                            Peak: {{ $dashboardData['orders_trend']['peak_day']['count'] ?? 0 }} on {{ $dashboardData['orders_trend']['peak_day']['date'] ?? '—' }}
                        </span>
                    </div>
                    <p class="text-xs text-stone-500">Order frequency against period average.</p>
                    <div id="chart-orders-trend" class="min-h-[280px] mt-4"></div>
                </div>
            </div>

            {{-- 5. Sales By Channel (Horizontal Bar) --}}
            <div class="gw-card gw-bi-span-5 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Sales by Acquisition Channel</h2>
                    <p class="text-xs text-stone-500">Attributed order revenue across marketing channels.</p>
                    <div id="chart-channels" class="min-h-[280px] mt-4"></div>
                </div>
            </div>
        </div>

        {{-- =========================================================================
             6. PAYMENT METHODS & 7. REFUND ANALYTICS
             ========================================================================= --}}
        <div class="gw-bi-grid-12">
            {{-- 6. Payment Method Distribution (Donut) --}}
            <div class="gw-card gw-bi-span-5 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Payment Gateway Distribution</h2>
                    <p class="text-xs text-stone-500">Checkout preferences and gateway transaction volumes.</p>
                    <div id="chart-payment-methods" class="min-h-[260px] mt-4"></div>
                </div>
                <div id="payment-methods-legend" class="mt-4 pt-3 border-t border-stone-100 space-y-1.5 text-xs">
                    @foreach($dashboardData['payment_distribution']['details'] ?? [] as $pm)
                        <div class="flex items-center justify-between text-stone-600">
                            <span class="font-medium truncate">{{ $pm['label'] }} ({{ $pm['count'] }})</span>
                            <span class="font-mono font-bold text-stone-900">₹{{ number_format($pm['amount'], 2) }} ({{ $pm['share_pct'] }}%)</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 7. Refund Analytics (Dual Axis Line/Area) --}}
            <div class="gw-card gw-bi-span-7 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <h2 class="text-base font-bold text-stone-900">Refund & Return Analytics</h2>
                        <span id="refund-spikes-badge" class="rounded-lg bg-rose-50 text-rose-700 px-2.5 py-1 text-[11px] font-semibold">
                            Total: ₹{{ number_format($dashboardData['refund_analytics']['total_refund_amount'] ?? 0, 2) }}
                        </span>
                    </div>
                    <p class="text-xs text-stone-500">Refund value and volume tracking with abnormal spike detection.</p>
                    <div id="chart-refunds" class="min-h-[280px] mt-4"></div>
                </div>
            </div>
        </div>

        {{-- =========================================================================
             8. TOP SELLING PRODUCTS & 9. PRODUCT CATEGORY PERFORMANCE
             ========================================================================= --}}
        <div class="gw-bi-grid-12">
            {{-- 8. Top Selling Products with Horizontal Progress Bars --}}
            <div class="gw-card gw-bi-span-7">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-stone-900">Top-Selling Products</h2>
                        <p class="text-xs text-stone-500">Revenue contributions, velocity, and period-over-period growth.</p>
                    </div>
                    <a href="{{ route('admin.products.index') }}" class="text-xs text-emerald-700 font-semibold hover:underline">
                        Catalog
                    </a>
                </div>

                <div id="top-products-container" class="space-y-3.5">
                    @forelse($dashboardData['top_products'] ?? [] as $prod)
                        <div class="rounded-xl border border-stone-100 p-3 hover:border-stone-200 hover:bg-stone-50/50 transition">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <div class="min-w-0 pr-2">
                                    <span class="font-bold text-stone-900 truncate block">{{ $prod['name'] }}</span>
                                    <span class="text-stone-400 font-mono text-[10.5px]">SKU: {{ $prod['sku'] }} • {{ $prod['orders_count'] }} orders</span>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-stone-900 font-mono">₹{{ number_format($prod['revenue'], 2) }}</span>
                                    <span class="block text-[10.5px] font-semibold {{ $prod['growth'] >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $prod['growth'] >= 0 ? '+' : '' }}{{ $prod['growth'] }}% growth
                                    </span>
                                </div>
                            </div>
                            {{-- Mini Revenue Bar --}}
                            <div class="w-full bg-stone-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ $prod['revenue_bar_pct'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-stone-400 border border-dashed border-stone-200 rounded-xl">
                            No product sales recorded for this timeframe.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 9. Product Category Performance --}}
            <div class="gw-card gw-bi-span-5 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Category Revenue Split</h2>
                    <p class="text-xs text-stone-500">Protocol categories and sales weight.</p>
                    <div id="chart-categories" class="min-h-[280px] mt-4"></div>
                </div>
            </div>
        </div>

        {{-- =========================================================================
             10. LOCATION ANALYTICS & INTERACTIVE GEOGRAPHIC MAP
             ========================================================================= --}}
        <div class="gw-card space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <h2 class="text-base font-bold text-stone-900">Geographic Sales Map</h2>
                    </div>
                    <p class="text-xs text-stone-500">Live shipping destinations, regional order concentration, and fulfillment heat points. Click any location to filter.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        onclick="resetMapView()"
                        class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-100 transition"
                    >
                        Reset Map View
                    </button>
                </div>
            </div>

            {{-- Interactive Leaflet Map Container --}}
            <div id="sales-geo-map" class="h-[380px] w-full rounded-xl border border-stone-200 overflow-hidden z-10"></div>
        </div>

        {{-- =========================================================================
             11. SALES BY STATE & 12. SALES BY CITY
             ========================================================================= --}}
        <div class="gw-bi-grid-2">
            {{-- 11. Sales By State --}}
            <div class="gw-card">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-base font-bold text-stone-900">Sales by State / Province</h2>
                    <span class="text-xs text-stone-400">Top 10 States</span>
                </div>
                <p class="text-xs text-stone-500">Revenue volume by administrative region.</p>
                <div id="chart-states" class="min-h-[280px] mt-4"></div>
            </div>

            {{-- 12. Sales By City --}}
            <div class="gw-card">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-base font-bold text-stone-900">Sales by Metro / City</h2>
                    <span class="text-xs text-stone-400">Top 10 Cities</span>
                </div>
                <p class="text-xs text-stone-500">Order revenue concentration across metropolitan areas.</p>
                <div id="chart-cities" class="min-h-[280px] mt-4"></div>
            </div>
        </div>

        {{-- =========================================================================
             13. AVERAGE ORDER VALUE (AOV) TREND
             ========================================================================= --}}
        <div class="gw-card space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Average Order Value (AOV) Trajectory</h2>
                    <p class="text-xs text-stone-500">Monitoring customer cart sizing momentum and expansion across cycles.</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-stone-400 block">Baseline Period Mean</span>
                    <span id="aov-mean-val" class="font-mono font-bold text-sm text-stone-900">₹{{ number_format($dashboardData['aov_trend']['avg_aov'] ?? 0, 2) }}</span>
                </div>
            </div>
            <div id="chart-aov-trend" class="min-h-[260px]"></div>
        </div>

        {{-- =========================================================================
             15. SALES TABLE (COMPREHENSIVE CATALOG PERFORMANCE GRID)
             ========================================================================= --}}
        <div class="gw-card space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Detailed Product Sales Matrix</h2>
                    <p class="text-xs text-stone-500">Itemized SKU economics, unit volumes, revenue attribution, and growth rates.</p>
                </div>

                {{-- Table Search & Export --}}
                <div class="flex items-center gap-2">
                    <div class="relative w-48 sm:w-64">
                        <input
                            type="text"
                            id="table-search-input"
                            oninput="filterSalesTable(this.value)"
                            placeholder="Filter table rows..."
                            class="w-full rounded-xl border border-stone-200 bg-stone-50/70 px-3 py-1.5 text-xs text-stone-800 outline-none focus:border-emerald-600 focus:bg-white"
                        >
                    </div>
                    <button
                        type="button"
                        onclick="triggerExport('csv')"
                        class="rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition"
                    >
                        Export CSV
                    </button>
                    <button
                        type="button"
                        onclick="triggerExport('excel')"
                        class="rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition"
                    >
                        Export Excel
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="sales-detail-table">
                    <thead>
                        <tr class="border-b border-stone-200 bg-stone-50/70 text-[10.5px] font-bold uppercase tracking-wider text-stone-500">
                            <th class="py-3 px-4 rounded-l-xl">Product</th>
                            <th class="py-3 px-3">SKU</th>
                            <th class="py-3 px-3 text-center">Units Sold</th>
                            <th class="py-3 px-3 text-right">Revenue</th>
                            <th class="py-3 px-3 text-right">Avg Price</th>
                            <th class="py-3 px-3 text-center">Orders</th>
                            <th class="py-3 px-3 text-right">Refunds</th>
                            <th class="py-3 px-3 text-center">Growth</th>
                            <th class="py-3 px-4 text-right rounded-r-xl">Catalog Share</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700" id="sales-table-tbody">
                        @forelse($dashboardData['sales_table'] ?? [] as $row)
                            <tr class="hover:bg-stone-50/60 transition table-row-item">
                                <td class="py-3 px-4 font-semibold text-stone-900 product-name-col">{{ $row['product'] }}</td>
                                <td class="py-3 px-3 font-mono text-stone-500">{{ $row['sku'] }}</td>
                                <td class="py-3 px-3 text-center font-mono">{{ $row['units_sold'] }}</td>
                                <td class="py-3 px-3 text-right font-bold text-stone-900 font-mono">{{ $row['revenue_formatted'] }}</td>
                                <td class="py-3 px-3 text-right font-mono text-stone-600">{{ $row['average_price_formatted'] }}</td>
                                <td class="py-3 px-3 text-center font-mono">{{ $row['orders'] }}</td>
                                <td class="py-3 px-3 text-right font-mono text-rose-700">{{ $row['refunds_formatted'] }}</td>
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10.5px] font-bold {{ $row['growth_trend'] === 'up' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $row['growth'] >= 0 ? '+' : '' }}{{ $row['growth'] }}%
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-stone-700">{{ $row['conversion_share'] }}%</td>
                            </tr>
                        @empty
                            <tr id="table-empty-row">
                                <td colspan="9" class="py-8 text-center text-xs text-stone-400">
                                    No transaction records found for current criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Leaflet Map & ApexCharts Scripts --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // Initial Server State
    let currentData = @json($dashboardData);
    let activePreset = "{{ $activePreset }}";
    let activeInterval = "{{ $dashboardData['meta']['interval'] ?? 'daily' }}";

    // Chart & Map Instance References
    let chartRevenue, chartOrders, chartChannels, chartPayments, chartRefunds, chartCategories, chartStates, chartCities, chartAov;
    let mapInstance, mapMarkersLayer;

    // Currency Formatter
    function fmtINR(val) {
        return '₹' + Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Initialize all Charts and Leaflet Map
    document.addEventListener('DOMContentLoaded', () => {
        initRevenueTrendChart();
        initOrdersTrendChart();
        initChannelsChart();
        initPaymentMethodsChart();
        initRefundsChart();
        initCategoriesChart();
        initStatesChart();
        initCitiesChart();
        initAovTrendChart();
        initGeoMap();
        updateActiveFilterBadge();

        // Recalibrate chart canvas and Leaflet map dimensions after initial DOM paint
        setTimeout(() => {
            window.dispatchEvent(new Event('resize'));
            if (mapInstance) mapInstance.invalidateSize();
        }, 200);
    });

    // Keep map and chart dimensions synced on viewport or sidebar size changes
    window.addEventListener('resize', () => {
        if (mapInstance) {
            setTimeout(() => mapInstance.invalidateSize(), 220);
        }
    });

    // =========================================================================
    // 3. REVENUE TREND (Line / Area ApexChart)
    // =========================================================================
    function initRevenueTrendChart() {
        const options = {
            chart: {
                type: 'area',
                height: 340,
                toolbar: { show: false },
                fontFamily: 'inherit',
                zoom: { enabled: false },
                events: {
                    markerClick: function(event, chartContext, { seriesIndex, dataPointIndex, config }) {
                        const dateLabel = config.xaxis.categories[dataPointIndex];
                        if (dateLabel) {
                            window.open("{{ route('admin.orders.index') }}?search=" + encodeURIComponent(dateLabel), '_blank');
                        }
                    }
                }
            },
            colors: ['#0f172a', '#204b34', '#94a3b8'],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: [2, 2.5, 1.5],
                dashArray: [0, 0, 4]
            },
            fill: {
                type: ['solid', 'gradient', 'solid'],
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.28,
                    opacityTo: 0.02,
                    stops: [0, 95, 100]
                }
            },
            series: currentData.revenue_trend.series,
            xaxis: {
                categories: currentData.revenue_trend.categories,
                labels: { style: { colors: '#78716c', fontSize: '11px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#78716c', fontSize: '11px' },
                    formatter: (val) => '₹' + Number(val).toLocaleString('en-IN')
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: { formatter: (val) => fmtINR(val) }
            },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 }
        };

        chartRevenue = new ApexCharts(document.querySelector("#chart-revenue-trend"), options);
        chartRevenue.render();
    }

    // =========================================================================
    // 4. ORDERS VELOCITY TREND
    // =========================================================================
    function initOrdersTrendChart() {
        const avg = currentData.orders_trend.average || 0;
        const options = {
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'inherit',
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        const dateLabel = config.w.config.xaxis.categories[config.dataPointIndex];
                        if (dateLabel) {
                            window.open("{{ route('admin.orders.index') }}?search=" + encodeURIComponent(dateLabel), '_blank');
                        }
                    }
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '40%',
                }
            },
            colors: ['#4f46e5'],
            dataLabels: { enabled: false },
            series: currentData.orders_trend.series,
            xaxis: {
                categories: currentData.orders_trend.categories,
                labels: { style: { colors: '#78716c', fontSize: '10.5px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#78716c', fontSize: '11px' },
                    formatter: (val) => Math.round(val)
                }
            },
            annotations: {
                yaxis: [{
                    y: avg,
                    borderColor: '#10b981',
                    strokeDashArray: 3,
                    label: {
                        borderColor: '#10b981',
                        style: { color: '#fff', background: '#10b981', fontSize: '10px' },
                        text: 'Avg Orders: ' + avg
                    }
                }]
            },
            tooltip: {
                y: { formatter: (val) => val + ' Orders' }
            },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 }
        };

        chartOrders = new ApexCharts(document.querySelector("#chart-orders-trend"), options);
        chartOrders.render();
    }

    // =========================================================================
    // 5. SALES BY CHANNEL
    // =========================================================================
    function initChannelsChart() {
        const options = {
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 6,
                    barHeight: '55%',
                }
            },
            colors: ['#0d9488'],
            dataLabels: { enabled: false },
            series: currentData.channel_sales.series,
            xaxis: {
                categories: currentData.channel_sales.categories,
                labels: {
                    style: { colors: '#78716c', fontSize: '10px' },
                    formatter: (val) => '₹' + Number(val).toLocaleString('en-IN')
                }
            },
            yaxis: { labels: { style: { colors: '#44403c', fontSize: '11px', fontWeight: 500 } } },
            tooltip: { y: { formatter: (val) => fmtINR(val) } },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 }
        };

        chartChannels = new ApexCharts(document.querySelector("#chart-channels"), options);
        chartChannels.render();
    }

    // =========================================================================
    // 6. PAYMENT METHODS DISTRIBUTION (Donut)
    // =========================================================================
    function initPaymentMethodsChart() {
        const options = {
            chart: {
                type: 'donut',
                height: 260,
                fontFamily: 'inherit',
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        const selectedMethod = currentData.payment_distribution.details[config.dataPointIndex];
                        if (selectedMethod) {
                            window.open("{{ route('admin.orders.index') }}?payment_method=" + encodeURIComponent(selectedMethod.gateway), '_blank');
                        }
                    }
                }
            },
            colors: ['#059669', '#d97706', '#6366f1', '#ec4899', '#0284c7', '#8b5cf6'],
            labels: currentData.payment_distribution.labels,
            series: currentData.payment_distribution.series,
            legend: { show: false },
            dataLabels: { enabled: true, formatter: (val) => Math.round(val) + '%' },
            tooltip: {
                y: { formatter: (val) => fmtINR(val) }
            },
            stroke: { width: 2, colors: ['#ffffff'] }
        };

        chartPayments = new ApexCharts(document.querySelector("#chart-payment-methods"), options);
        chartPayments.render();
    }

    // =========================================================================
    // 7. REFUND ANALYTICS
    // =========================================================================
    function initRefundsChart() {
        const options = {
            chart: {
                type: 'line',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            colors: ['#e11d48', '#f43f5e'],
            stroke: { width: [2, 2], curve: 'smooth' },
            fill: {
                type: ['gradient', 'solid'],
                gradient: { opacityFrom: 0.3, opacityTo: 0.05 }
            },
            series: currentData.refund_analytics.series,
            xaxis: {
                categories: currentData.refund_analytics.categories,
                labels: { style: { colors: '#78716c', fontSize: '10.5px' } }
            },
            yaxis: [
                {
                    title: { text: 'Refund Amount', style: { color: '#e11d48', fontSize: '10.5px' } },
                    labels: { formatter: (val) => '₹' + Number(val).toLocaleString('en-IN') }
                },
                {
                    opposite: true,
                    title: { text: 'Count', style: { color: '#f43f5e', fontSize: '10.5px' } },
                    labels: { formatter: (val) => Math.round(val) }
                }
            ],
            tooltip: { shared: true },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 }
        };

        chartRefunds = new ApexCharts(document.querySelector("#chart-refunds"), options);
        chartRefunds.render();
    }

    // =========================================================================
    // 9. CATEGORY PERFORMANCE
    // =========================================================================
    function initCategoriesChart() {
        const options = {
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 6,
                    barHeight: '50%',
                }
            },
            colors: ['#204b34'],
            dataLabels: { enabled: false },
            series: currentData.category_performance.series,
            xaxis: {
                categories: currentData.category_performance.categories,
                labels: { formatter: (val) => '₹' + Number(val).toLocaleString('en-IN') }
            },
            tooltip: { y: { formatter: (val) => fmtINR(val) } },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 }
        };

        chartCategories = new ApexCharts(document.querySelector("#chart-categories"), options);
        chartCategories.render();
    }

    // =========================================================================
    // 11. SALES BY STATE & 12. SALES BY CITY
    // =========================================================================
    function initStatesChart() {
        const options = {
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'inherit',
                events: {
                    dataPointSelection: function(e, chart, config) {
                        const state = config.w.config.xaxis.categories[config.dataPointIndex];
                        if (state) {
                            document.getElementById('filter-state').value = state;
                            triggerFilterChange();
                        }
                    }
                }
            },
            plotOptions: { bar: { horizontal: true, borderRadius: 5, barHeight: '55%' } },
            colors: ['#0284c7'],
            series: currentData.location_analytics.states.series,
            xaxis: {
                categories: currentData.location_analytics.states.categories,
                labels: { formatter: (val) => '₹' + Number(val).toLocaleString('en-IN') }
            },
            tooltip: { y: { formatter: (val) => fmtINR(val) } },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 }
        };
        chartStates = new ApexCharts(document.querySelector("#chart-states"), options);
        chartStates.render();
    }

    function initCitiesChart() {
        const options = {
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'inherit',
                events: {
                    dataPointSelection: function(e, chart, config) {
                        const city = config.w.config.xaxis.categories[config.dataPointIndex];
                        if (city) {
                            document.getElementById('filter-city').value = city;
                            triggerFilterChange();
                        }
                    }
                }
            },
            plotOptions: { bar: { horizontal: true, borderRadius: 5, barHeight: '55%' } },
            colors: ['#7c3aed'],
            series: currentData.location_analytics.cities.series,
            xaxis: {
                categories: currentData.location_analytics.cities.categories,
                labels: { formatter: (val) => '₹' + Number(val).toLocaleString('en-IN') }
            },
            tooltip: { y: { formatter: (val) => fmtINR(val) } },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 }
        };
        chartCities = new ApexCharts(document.querySelector("#chart-cities"), options);
        chartCities.render();
    }

    // =========================================================================
    // 13. AOV TREND CHART
    // =========================================================================
    function initAovTrendChart() {
        const avg = currentData.aov_trend.avg_aov || 0;
        const options = {
            chart: {
                type: 'line',
                height: 260,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            colors: ['#d97706'],
            stroke: { width: 2.5, curve: 'smooth' },
            series: currentData.aov_trend.series,
            xaxis: {
                categories: currentData.aov_trend.categories,
                labels: { style: { colors: '#78716c', fontSize: '11px' } }
            },
            yaxis: {
                labels: { formatter: (val) => '₹' + Number(val).toLocaleString('en-IN') }
            },
            annotations: {
                yaxis: [{
                    y: avg,
                    borderColor: '#b45309',
                    strokeDashArray: 4,
                    label: {
                        borderColor: '#b45309',
                        style: { color: '#fff', background: '#b45309', fontSize: '10px' },
                        text: 'Baseline Mean: ₹' + avg
                    }
                }]
            },
            tooltip: { y: { formatter: (val) => fmtINR(val) } },
            grid: { borderColor: '#f5f5f4', strokeDashArray: 4 }
        };

        chartAov = new ApexCharts(document.querySelector("#chart-aov-trend"), options);
        chartAov.render();
    }

    // =========================================================================
    // 10. LEAFLET GEOGRAPHIC MAP
    // =========================================================================
    function initGeoMap() {
        const mapContainer = document.getElementById('sales-geo-map');
        if (!mapContainer) return;

        // India view default
        mapInstance = L.map('sales-geo-map', {
            scrollWheelZoom: false,
            zoomControl: true,
        }).setView([20.5937, 78.9629], 5);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18,
        }).addTo(mapInstance);

        mapMarkersLayer = L.layerGroup().addTo(mapInstance);
        renderMapPoints(currentData.location_analytics.map_points || []);
    }

    function renderMapPoints(points) {
        if (!mapMarkersLayer) return;
        mapMarkersLayer.clearLayers();

        if (!points || points.length === 0) return;

        const bounds = [];

        points.forEach(pt => {
            if (!pt.lat || !pt.lng) return;

            const radius = Math.min(24, Math.max(8, Math.sqrt(pt.revenue) / 4));

            const circle = L.circleMarker([pt.lat, pt.lng], {
                radius: radius,
                fillColor: '#059669',
                color: '#047857',
                weight: 1.5,
                opacity: 0.9,
                fillOpacity: 0.65
            });

            const popupContent = `
                <div style="font-family: inherit; font-size: 12px; min-width: 170px; line-height: 1.4;">
                    <div style="font-weight: 700; color: #1c1917; font-size: 13px; margin-bottom: 2px;">${pt.city}, ${pt.state}</div>
                    <div style="color: #78716c; font-size: 10px; margin-bottom: 6px;">Order ${pt.order_number}</div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                        <span style="color: #78716c;">Revenue:</span>
                        <span style="font-weight: 700; color: #047857;">${fmtINR(pt.revenue)}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                        <span style="color: #78716c;">Status:</span>
                        <span style="text-transform: uppercase; font-size: 10px; font-weight: 600; color: #44403c;">${pt.payment_status}</span>
                    </div>
                    <button onclick="applyLocationFilter('${pt.city}', '${pt.state}')" style="width: 100%; border: none; background: #0f172a; color: #fff; padding: 4px 8px; border-radius: 6px; font-size: 10.5px; font-weight: 600; cursor: pointer;">
                        Filter Dashboard
                    </button>
                </div>
            `;

            circle.bindPopup(popupContent);
            mapMarkersLayer.addLayer(circle);
            bounds.push([pt.lat, pt.lng]);
        });

        if (bounds.length > 0 && mapInstance) {
            try {
                mapInstance.fitBounds(bounds, { padding: [40, 40], maxZoom: 8 });
            } catch (e) {}
        }
    }

    function resetMapView() {
        if (mapInstance) {
            mapInstance.setView([20.5937, 78.9629], 5);
        }
    }

    function applyLocationFilter(city, state) {
        if (city && document.getElementById('filter-city')) {
            document.getElementById('filter-city').value = city;
        }
        if (state && document.getElementById('filter-state')) {
            document.getElementById('filter-state').value = state;
        }
        triggerFilterChange();
    }

    // =========================================================================
    // FILTER CONTROLS & INSTANT AJAX REFRESH ENGINE
    // =========================================================================
    function setFilterPreset(presetKey) {
        activePreset = presetKey;

        // Toggle preset buttons active class
        document.querySelectorAll('.preset-filter-btn').forEach(btn => {
            btn.classList.remove('bg-stone-900', 'text-white', 'shadow-2xs');
            btn.classList.add('bg-stone-100/80', 'text-stone-600');
        });

        const activeBtn = document.getElementById('preset-btn-' + presetKey);
        if (activeBtn) {
            activeBtn.classList.remove('bg-stone-100/80', 'text-stone-600');
            activeBtn.classList.add('bg-stone-900', 'text-white', 'shadow-2xs');
        }

        // Show/hide custom dates row
        const customRow = document.getElementById('custom-date-row');
        if (customRow) {
            if (presetKey === 'custom') {
                customRow.classList.remove('hidden');
                customRow.classList.add('flex');
            } else {
                customRow.classList.add('hidden');
                customRow.classList.remove('flex');
                triggerFilterChange();
            }
        }
    }

    function setIntervalGrouping(interval) {
        activeInterval = interval;
        document.querySelectorAll('.interval-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-stone-900', 'shadow-2xs');
            btn.classList.add('text-stone-600');
        });
        const currentBtn = document.getElementById('interval-btn-' + interval);
        if (currentBtn) {
            currentBtn.classList.add('bg-white', 'text-stone-900', 'shadow-2xs');
            currentBtn.classList.remove('text-stone-600');
        }
        triggerFilterChange();
    }

    function toggleFilterDrawer() {
        const drawer = document.getElementById('filter-drawer');
        if (drawer) {
            drawer.classList.toggle('hidden');
        }
    }

    function toggleExportMenu() {
        const menu = document.getElementById('export-dropdown-menu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    document.addEventListener('click', (e) => {
        const wrap = document.getElementById('export-dropdown-wrapper');
        const menu = document.getElementById('export-dropdown-menu');
        if (wrap && !wrap.contains(e.target) && menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
        }
    });

    function getActiveFiltersPayload() {
        const payload = {
            date_preset: activePreset,
            interval: activeInterval,
            compare_previous: document.getElementById('filter-compare-previous')?.checked ? '1' : '0',
            country: document.getElementById('filter-country')?.value || '',
            state: document.getElementById('filter-state')?.value || '',
            city: document.getElementById('filter-city')?.value || '',
            payment_method: document.getElementById('filter-payment-method')?.value || '',
            product_id: document.getElementById('filter-product-id')?.value || '',
            category_id: document.getElementById('filter-category-id')?.value || '',
            channel: document.getElementById('filter-channel')?.value || '',
            order_status: document.getElementById('filter-order-status')?.value || '',
        };

        if (activePreset === 'custom') {
            payload.start_date = document.getElementById('filter-start-date')?.value || '';
            payload.end_date = document.getElementById('filter-end-date')?.value || '';
        }

        return payload;
    }

    function updateActiveFilterBadge() {
        const payload = getActiveFiltersPayload();
        let count = 0;
        if (payload.country) count++;
        if (payload.state) count++;
        if (payload.city) count++;
        if (payload.payment_method) count++;
        if (payload.product_id) count++;
        if (payload.category_id) count++;
        if (payload.channel) count++;
        if (payload.order_status) count++;

        const badge = document.getElementById('active-filter-badge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    }

    function clearAllFilters() {
        if (document.getElementById('filter-country')) document.getElementById('filter-country').value = '';
        if (document.getElementById('filter-state')) document.getElementById('filter-state').value = '';
        if (document.getElementById('filter-city')) document.getElementById('filter-city').value = '';
        if (document.getElementById('filter-payment-method')) document.getElementById('filter-payment-method').value = '';
        if (document.getElementById('filter-product-id')) document.getElementById('filter-product-id').value = '';
        if (document.getElementById('filter-category-id')) document.getElementById('filter-category-id').value = '';
        if (document.getElementById('filter-channel')) document.getElementById('filter-channel').value = '';
        if (document.getElementById('filter-order-status')) document.getElementById('filter-order-status').value = '';

        setFilterPreset('last_30_days');
    }

    // Trigger Live Fetch
    let fetchDebounceTimer = null;
    function triggerFilterChange() {
        clearTimeout(fetchDebounceTimer);
        fetchDebounceTimer = setTimeout(() => {
            refreshAnalytics(false);
        }, 150);
    }

    function refreshAnalytics(bypassCache = false) {
        const filters = getActiveFiltersPayload();
        if (bypassCache) filters.refresh = 1;

        updateActiveFilterBadge();

        // Loader animation
        const loader = document.getElementById('analytics-loader-bar');
        const refreshIcon = document.getElementById('refresh-icon');
        if (loader) loader.classList.remove('-translate-y-full');
        if (refreshIcon) refreshIcon.classList.add('rotate-180');

        const params = new URLSearchParams(filters);

        fetch("{{ route('admin.analytics.sales.data') }}?" + params.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(response => {
            if (response.success && response.data) {
                currentData = response.data;
                updateAllDashboardComponents(response.data);
                // Update URL history state seamlessly
                history.replaceState(null, '', '?' + params.toString());
            }
        })
        .catch(err => {
            console.error('Failed to load sales analytics data', err);
        })
        .finally(() => {
            if (loader) loader.classList.add('-translate-y-full');
            if (refreshIcon) refreshIcon.classList.remove('rotate-180');
        });
    }

    // Update Every Component with new Live Data
    function updateAllDashboardComponents(data) {
        // 1. Update KPI Cards
        updateKpiCards(data.kpis);

        // 2. Update Charts
        if (chartRevenue && data.revenue_trend) {
            chartRevenue.updateOptions({
                xaxis: { categories: data.revenue_trend.categories }
            });
            chartRevenue.updateSeries(data.revenue_trend.series);
        }

        if (chartOrders && data.orders_trend) {
            chartOrders.updateOptions({
                xaxis: { categories: data.orders_trend.categories },
                annotations: {
                    yaxis: [{
                        y: data.orders_trend.average || 0,
                        borderColor: '#10b981',
                        label: { text: 'Avg Orders: ' + (data.orders_trend.average || 0) }
                    }]
                }
            });
            chartOrders.updateSeries(data.orders_trend.series);
            const peakBadge = document.getElementById('orders-peak-badge');
            if (peakBadge && data.orders_trend.peak_day) {
                peakBadge.textContent = `Peak: ${data.orders_trend.peak_day.count || 0} on ${data.orders_trend.peak_day.date || '—'}`;
            }
        }

        if (chartChannels && data.channel_sales) {
            chartChannels.updateOptions({
                xaxis: { categories: data.channel_sales.categories }
            });
            chartChannels.updateSeries(data.channel_sales.series);
        }

        if (chartPayments && data.payment_distribution) {
            chartPayments.updateOptions({
                labels: data.payment_distribution.labels
            });
            chartPayments.updateSeries(data.payment_distribution.series);
            updatePaymentLegend(data.payment_distribution.details);
        }

        if (chartRefunds && data.refund_analytics) {
            chartRefunds.updateOptions({
                xaxis: { categories: data.refund_analytics.categories }
            });
            chartRefunds.updateSeries(data.refund_analytics.series);
            const refBadge = document.getElementById('refund-spikes-badge');
            if (refBadge) {
                refBadge.textContent = 'Total: ' + fmtINR(data.refund_analytics.total_refund_amount || 0);
            }
        }

        if (chartCategories && data.category_performance) {
            chartCategories.updateOptions({
                xaxis: { categories: data.category_performance.categories }
            });
            chartCategories.updateSeries(data.category_performance.series);
        }

        if (chartStates && data.location_analytics?.states) {
            chartStates.updateOptions({
                xaxis: { categories: data.location_analytics.states.categories }
            });
            chartStates.updateSeries(data.location_analytics.states.series);
        }

        if (chartCities && data.location_analytics?.cities) {
            chartCities.updateOptions({
                xaxis: { categories: data.location_analytics.cities.categories }
            });
            chartCities.updateSeries(data.location_analytics.cities.series);
        }

        if (chartAov && data.aov_trend) {
            chartAov.updateOptions({
                xaxis: { categories: data.aov_trend.categories },
                annotations: {
                    yaxis: [{
                        y: data.aov_trend.avg_aov || 0,
                        borderColor: '#b45309',
                        label: { text: 'Baseline Mean: ₹' + (data.aov_trend.avg_aov || 0) }
                    }]
                }
            });
            chartAov.updateSeries(data.aov_trend.series);
            const aovMean = document.getElementById('aov-mean-val');
            if (aovMean) {
                aovMean.textContent = fmtINR(data.aov_trend.avg_aov || 0);
            }
        }

        // 3. Update Geo Map Markers
        if (data.location_analytics?.map_points) {
            renderMapPoints(data.location_analytics.map_points);
        }

        // 4. Update Top Products List
        renderTopProducts(data.top_products || []);

        // 5. Update Operational Insights
        renderOperationalInsights(data.operational_insights || []);

        // 6. Update Sales Matrix Table
        renderSalesTable(data.sales_table || []);
    }

    function updateKpiCards(kpis) {
        if (!kpis) return;

        const updateCard = (key, idPrefix) => {
            const item = kpis[key];
            if (!item) return;

            const valEl = document.getElementById(idPrefix);
            const deltaEl = document.getElementById(idPrefix + '-delta');
            const prevEl = document.getElementById(idPrefix + '-prev');

            if (valEl) valEl.textContent = item.formatted;
            if (deltaEl) {
                const sign = item.delta_percent >= 0 ? '+' : '';
                deltaEl.textContent = `${sign}${item.delta_percent}%`;
                deltaEl.className = 'inline-flex items-center gap-1 font-semibold ' + (item.trend === 'up' ? 'text-emerald-700' : (item.trend === 'down' ? 'text-rose-600' : 'text-stone-500'));
            }
            if (prevEl) {
                prevEl.textContent = 'vs ' + item.formatted_previous;
                prevEl.title = 'Previous: ' + item.formatted_previous;
            }
        };

        updateCard('gross_sales', 'kpi-gross-sales');
        updateCard('net_sales', 'kpi-net-sales');
        updateCard('total_orders', 'kpi-total-orders');
        updateCard('average_order_value', 'kpi-aov');
        updateCard('refund_amount', 'kpi-refund-amount');
        updateCard('refund_count', 'kpi-refund-count');
        updateCard('total_customers', 'kpi-total-customers');
        updateCard('returning_customers', 'kpi-returning-customers');
    }

    function updatePaymentLegend(details) {
        const container = document.getElementById('payment-methods-legend');
        if (!container || !details) return;

        container.innerHTML = details.map(pm => `
            <div class="flex items-center justify-between text-stone-600">
                <span class="font-medium truncate">${pm.label} (${pm.count})</span>
                <span class="font-mono font-bold text-stone-900">${fmtINR(pm.amount)} (${pm.share_pct}%)</span>
            </div>
        `).join('');
    }

    function renderTopProducts(products) {
        const container = document.getElementById('top-products-container');
        if (!container) return;

        if (products.length === 0) {
            container.innerHTML = `
                <div class="p-8 text-center text-xs text-stone-400 border border-dashed border-stone-200 rounded-xl">
                    No product sales recorded for this timeframe.
                </div>
            `;
            return;
        }

        container.innerHTML = products.map(prod => `
            <div class="rounded-xl border border-stone-100 p-3 hover:border-stone-200 hover:bg-stone-50/50 transition">
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <div class="min-w-0 pr-2">
                        <span class="font-bold text-stone-900 truncate block">${prod.name}</span>
                        <span class="text-stone-400 font-mono text-[10.5px]">SKU: ${prod.sku} • ${prod.orders_count} orders</span>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="font-bold text-stone-900 font-mono">${fmtINR(prod.revenue)}</span>
                        <span class="block text-[10.5px] font-semibold ${prod.growth >= 0 ? 'text-emerald-700' : 'text-rose-600'}">
                            ${prod.growth >= 0 ? '+' : ''}${prod.growth}% growth
                        </span>
                    </div>
                </div>
                <div class="w-full bg-stone-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-emerald-600 h-1.5 rounded-full" style="width: ${prod.revenue_bar_pct}%"></div>
                </div>
            </div>
        `).join('');
    }

    function renderOperationalInsights(insights) {
        const container = document.getElementById('operational-insights-container');
        if (!container || !insights) return;

        container.innerHTML = insights.map(insight => `
            <div class="rounded-xl border border-stone-100 bg-stone-50/60 p-3.5 flex flex-col justify-between hover:bg-stone-50 transition">
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-stone-500 text-[10.5px] uppercase tracking-wider">${insight.title}</span>
                        <span class="h-2 w-2 rounded-full ${insight.trend === 'positive' ? 'bg-emerald-500' : (insight.trend === 'warning' ? 'bg-amber-500' : 'bg-stone-400')}"></span>
                    </div>
                    <div class="text-lg font-bold text-stone-900 font-mono mt-0.5">${insight.metric}</div>
                    <p class="text-xs text-stone-600 mt-1 leading-snug">${insight.subtitle}</p>
                </div>
                ${insight.action_label ? `
                    <div class="mt-3 pt-2 border-t border-stone-200/60 flex items-center justify-between text-xs">
                        <button
                            type="button"
                            onclick='applyInsightFilter(${JSON.stringify(insight.filter_param || {})})'
                            class="text-emerald-700 hover:text-emerald-900 font-semibold inline-flex items-center gap-1 transition"
                        >
                            <span>${insight.action_label}</span>
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                ` : ''}
            </div>
        `).join('');
    }

    function applyInsightFilter(params) {
        if (!params) return;
        if (params.state && document.getElementById('filter-state')) {
            document.getElementById('filter-state').value = params.state;
        }
        if (params.city && document.getElementById('filter-city')) {
            document.getElementById('filter-city').value = params.city;
        }
        if (params.payment_method && document.getElementById('filter-payment-method')) {
            document.getElementById('filter-payment-method').value = params.payment_method;
        }
        if (params.order_status && document.getElementById('filter-order-status')) {
            document.getElementById('filter-order-status').value = params.order_status;
        }
        if (params.search) {
            window.open("{{ route('admin.orders.index') }}?search=" + encodeURIComponent(params.search), '_blank');
            return;
        }
        triggerFilterChange();
    }

    function renderSalesTable(rows) {
        const tbody = document.getElementById('sales-table-tbody');
        if (!tbody) return;

        if (rows.length === 0) {
            tbody.innerHTML = `
                <tr id="table-empty-row">
                    <td colspan="9" class="py-8 text-center text-xs text-stone-400">
                        No transaction records found for current criteria.
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = rows.map(row => `
            <tr class="hover:bg-stone-50/60 transition table-row-item">
                <td class="py-3 px-4 font-semibold text-stone-900 product-name-col">${row.product}</td>
                <td class="py-3 px-3 font-mono text-stone-500">${row.sku}</td>
                <td class="py-3 px-3 text-center font-mono">${row.units_sold}</td>
                <td class="py-3 px-3 text-right font-bold text-stone-900 font-mono">${row.revenue_formatted}</td>
                <td class="py-3 px-3 text-right font-mono text-stone-600">${row.average_price_formatted}</td>
                <td class="py-3 px-3 text-center font-mono">${row.orders}</td>
                <td class="py-3 px-3 text-right font-mono text-rose-700">${row.refunds_formatted}</td>
                <td class="py-3 px-3 text-center">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10.5px] font-bold ${row.growth_trend === 'up' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'}">
                        ${row.growth >= 0 ? '+' : ''}${row.growth}%
                    </span>
                </td>
                <td class="py-3 px-4 text-right font-mono text-stone-700">${row.conversion_share}%</td>
            </tr>
        `).join('');
    }

    function filterSalesTable(query) {
        const term = (query || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.table-row-item');
        let visibleCount = 0;

        rows.forEach(r => {
            const text = r.textContent.toLowerCase();
            if (text.includes(term)) {
                r.style.display = '';
                visibleCount++;
            } else {
                r.style.display = 'none';
            }
        });

        const emptyRow = document.getElementById('table-empty-row');
        if (emptyRow) {
            emptyRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    // Trigger Reports Export
    function triggerExport(format) {
        const filters = getActiveFiltersPayload();
        filters.format = format;
        const params = new URLSearchParams(filters);
        window.location.href = "{{ route('admin.analytics.sales.export') }}?" + params.toString();
        toggleExportMenu();
    }
</script>
@endsection
