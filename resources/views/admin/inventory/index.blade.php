@extends('layouts.admin')

@section('title', 'Inventory & Stock Management')

@section('content')
<div class="p-6 md:p-8 space-y-8 max-w-[1600px] mx-auto">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                    Warehouse ERP
                </span>
                <span class="text-xs text-stone-400">• Master Stock Ledger</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-stone-900 tracking-tight mt-1.5 font-serif">Inventory Management</h1>
            <p class="text-sm text-stone-500 mt-1">Track physical stock, monitor reorder thresholds, and audit stock movements across all warehouses.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.inventory.history') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-stone-200 text-stone-700 hover:bg-stone-50 text-xs font-semibold rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Movements Log
            </a>
            <a href="{{ route('admin.inventory.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-stone-200 text-stone-700 hover:bg-stone-50 text-xs font-semibold rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 text-xs font-medium text-emerald-900 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
        </div>
    @endif

    <!-- 6 Master Inventory KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- 1. Total SKUs -->
        <a href="{{ route('admin.inventory.index') }}" class="group block bg-white p-4 rounded-2xl border border-stone-200/80 hover:border-emerald-500/50 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Total SKUs</span>
                <div class="h-7 w-7 rounded-lg bg-stone-100 flex items-center justify-center text-stone-600 group-hover:bg-emerald-50 group-hover:text-emerald-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-stone-900 tracking-tight">{{ number_format($kpis['total_skus']) }}</div>
            <div class="mt-1 text-[11px] text-stone-400">Tracked stock variants</div>
        </a>

        <!-- 2. Total Stock on Hand -->
        <a href="{{ route('admin.inventory.index') }}" class="group block bg-white p-4 rounded-2xl border border-stone-200/80 hover:border-emerald-500/50 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Total Stock</span>
                <div class="h-7 w-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-emerald-700 tracking-tight">{{ number_format($kpis['total_stock']) }}</div>
            <div class="mt-1 text-[11px] text-stone-400">Physical units on hand</div>
        </a>

        <!-- 3. Low Stock Items -->
        <a href="{{ route('admin.inventory.index', ['status' => 'low_stock']) }}" class="group block bg-white p-4 rounded-2xl border {{ request('status') === 'low_stock' ? 'border-amber-500 ring-2 ring-amber-100' : 'border-stone-200/80' }} hover:border-amber-500/50 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Low Stock</span>
                <div class="h-7 w-7 rounded-lg bg-amber-50 flex items-center justify-center text-amber-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-amber-700 tracking-tight">{{ number_format($kpis['low_stock']) }}</div>
            <div class="mt-1 text-[11px] text-stone-400">At or below reorder level</div>
        </a>

        <!-- 4. Out of Stock Items -->
        <a href="{{ route('admin.inventory.index', ['status' => 'out_of_stock']) }}" class="group block bg-white p-4 rounded-2xl border {{ request('status') === 'out_of_stock' ? 'border-rose-500 ring-2 ring-rose-100' : 'border-stone-200/80' }} hover:border-rose-500/50 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Out of Stock</span>
                <div class="h-7 w-7 rounded-lg bg-rose-50 flex items-center justify-center text-rose-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-rose-700 tracking-tight">{{ number_format($kpis['out_of_stock']) }}</div>
            <div class="mt-1 text-[11px] text-stone-400">0 available for fulfillment</div>
        </a>

        <!-- 5. Inventory Valuation at Cost -->
        <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Inventory Value</span>
                <div class="h-7 w-7 rounded-lg bg-blue-50 flex items-center justify-center text-blue-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-2 text-xl font-bold text-stone-900 tracking-tight">₹{{ number_format($kpis['inventory_value'], 2) }}</div>
            <div class="mt-1 text-[11px] text-stone-400">Total valuation at cost price</div>
        </div>

        <!-- 6. Movements Today -->
        <a href="{{ route('admin.inventory.history') }}" class="group block bg-white p-4 rounded-2xl border border-stone-200/80 hover:border-emerald-500/50 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Movements (24h)</span>
                <div class="h-7 w-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-indigo-700 tracking-tight">{{ number_format($kpis['movements_today']) }}</div>
            <div class="mt-1 text-[11px] text-stone-400">Sales, adjusts & receipts</div>
        </a>
    </div>

    <!-- Status Segmented Tabs Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-stone-200 pb-3">
        <div class="flex items-center gap-1.5 overflow-x-auto text-xs font-semibold">
            @php $currStatus = request('status', 'all'); @endphp
            <a href="{{ route('admin.inventory.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" class="px-3.5 py-2 rounded-xl transition {{ $currStatus === 'all' ? 'bg-stone-900 text-white shadow-xs' : 'bg-white border border-stone-200 text-stone-600 hover:bg-stone-50' }}">
                All Items ({{ $kpis['total_skus'] }})
            </a>
            <a href="{{ route('admin.inventory.index', array_merge(request()->except('status', 'page'), ['status' => 'in_stock'])) }}" class="px-3.5 py-2 rounded-xl transition {{ $currStatus === 'in_stock' ? 'bg-emerald-800 text-white shadow-xs' : 'bg-white border border-stone-200 text-stone-600 hover:bg-stone-50' }}">
                In Stock
            </a>
            <a href="{{ route('admin.inventory.index', array_merge(request()->except('status', 'page'), ['status' => 'low_stock'])) }}" class="px-3.5 py-2 rounded-xl transition {{ $currStatus === 'low_stock' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white border border-stone-200 text-stone-600 hover:bg-stone-50' }}">
                Low Stock ({{ $kpis['low_stock'] }})
            </a>
            <a href="{{ route('admin.inventory.index', array_merge(request()->except('status', 'page'), ['status' => 'out_of_stock'])) }}" class="px-3.5 py-2 rounded-xl transition {{ $currStatus === 'out_of_stock' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white border border-stone-200 text-stone-600 hover:bg-stone-50' }}">
                Out of Stock ({{ $kpis['out_of_stock'] }})
            </a>
        </div>
    </div>

    <!-- Search & Advanced Filter Bar -->
    <div class="bg-white rounded-2xl border border-stone-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.inventory.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">

            <!-- Search -->
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Search Product / SKU</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Product title, SKU, barcode..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                    <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Warehouse -->
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Warehouse</label>
                <select name="warehouse" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh }}" {{ request('warehouse') == $wh ? 'selected' : '' }}>{{ $wh }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Category</label>
                <select name="category_id" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                    <option value="">All Categories</option>
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort By -->
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Sort By</label>
                <select name="sort" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                    <option value="inventory.id" {{ request('sort') == 'inventory.id' ? 'selected' : '' }}>Default (Created)</option>
                    <option value="stock" {{ request('sort') == 'stock' ? 'selected' : '' }}>Stock Quantity</option>
                    <option value="available" {{ request('sort') == 'available' ? 'selected' : '' }}>Available Stock</option>
                    <option value="product" {{ request('sort') == 'product' ? 'selected' : '' }}>Product Name</option>
                    <option value="sku" {{ request('sort') == 'sku' ? 'selected' : '' }}>SKU</option>
                </select>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl transition shadow-xs flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Apply Filter
                </button>
                @if(request()->anyFilled(['search', 'warehouse', 'category_id', 'sort']))
                    <a href="{{ route('admin.inventory.index', ['status' => request('status', 'all')]) }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-semibold rounded-xl transition" title="Clear Filters">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Enterprise Stock Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-600">
                <thead class="bg-stone-50/80 border-b border-stone-200/80 text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="py-3.5 px-3 w-14 text-center">Image</th>
                        <th class="py-3.5 px-3">Product</th>
                        <th class="py-3.5 px-3">SKU</th>
                        <th class="py-3.5 px-3 text-center">Current Stock</th>
                        <th class="py-3.5 px-3 text-center">Reserved</th>
                        <th class="py-3.5 px-3 text-center">Available</th>
                        <th class="py-3.5 px-3 text-center">Min Level</th>
                        <th class="py-3.5 px-3 text-center">Max Level</th>
                        <th class="py-3.5 px-3 text-center">Reorder Level</th>
                        <th class="py-3.5 px-3 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($items as $item)
                        @php
                            $variant = $item->productVariant;
                            $product = $variant?->product;
                            $thumb = $product?->primary_image_url;
                            $avail = $item->available_stock;
                            $status = $item->stock_status;
                        @endphp
                        <tr class="hover:bg-stone-50/70 transition group">
                            <!-- Image -->
                            <td class="py-3 px-3 text-center">
                                @if($thumb)
                                    <img src="{{ $thumb }}" alt="{{ $product?->name }}" class="h-10 w-10 rounded-lg object-cover border border-stone-200 mx-auto">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-stone-100 border border-stone-200 flex items-center justify-center text-stone-400 mx-auto">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </td>

                            <!-- Product Name & Category -->
                            <td class="py-3 px-3 min-w-[200px]">
                                <a href="{{ route('admin.inventory.show', $item->id) }}" class="font-semibold text-stone-900 hover:text-emerald-700 transition text-[13px] block">
                                    {{ $product?->name ?? 'Unknown Product' }}
                                </a>
                                <div class="text-[11px] text-stone-400 flex items-center gap-1.5 mt-0.5">
                                    <span>{{ $variant?->name }}</span>
                                    <span>•</span>
                                    <span class="text-stone-500">{{ $product?->category?->name ?? 'Uncategorized' }}</span>
                                </div>
                            </td>

                            <!-- SKU -->
                            <td class="py-3 px-3 font-mono font-medium text-stone-700 whitespace-nowrap">
                                <div>{{ $variant?->sku ?? '—' }}</div>
                                @if($variant?->barcode)
                                    <div class="text-[10px] text-stone-400">UPC: {{ $variant->barcode }}</div>
                                @endif
                            </td>

                            <!-- Current Stock -->
                            <td class="py-3 px-3 text-center font-bold text-stone-900 text-sm whitespace-nowrap">
                                {{ number_format($item->quantity_on_hand) }}
                            </td>

                            <!-- Reserved Stock -->
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $item->quantity_reserved > 0 ? 'bg-amber-50 text-amber-800' : 'text-stone-400' }}">
                                    {{ $item->quantity_reserved }}
                                </span>
                            </td>

                            <!-- Available Stock -->
                            <td class="py-3 px-3 text-center font-bold text-sm whitespace-nowrap {{ $avail <= 0 ? 'text-rose-600' : ($avail <= ($item->reorder_level ?: 10) ? 'text-amber-600' : 'text-emerald-700') }}">
                                {{ number_format($avail) }}
                            </td>

                            <!-- Min Level -->
                            <td class="py-3 px-3 text-center text-stone-500 whitespace-nowrap">
                                {{ $item->min_level ?? 5 }}
                            </td>

                            <!-- Max Level -->
                            <td class="py-3 px-3 text-center text-stone-500 whitespace-nowrap">
                                {{ $item->max_level ?? 1000 }}
                            </td>

                            <!-- Reorder Level -->
                            <td class="py-3 px-3 text-center text-stone-700 font-semibold whitespace-nowrap">
                                {{ $item->reorder_level ?? 10 }}
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                @if($status === 'out_of_stock')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Out of Stock
                                    </span>
                                @elseif($status === 'low_stock')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Low Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        In Stock
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Quick Adjust Trigger -->
                                    <button type="button" onclick="openAdjustModal('{{ $item->id }}', '{{ addslashes($product?->name ?? 'Product') }}', '{{ $variant?->sku }}', {{ $item->quantity_on_hand }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-stone-200 text-stone-700 hover:bg-stone-100 text-[11px] font-semibold transition" title="Adjust Stock">
                                        <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        Adjust
                                    </button>

                                    <!-- Details View Link -->
                                    <a href="{{ route('admin.inventory.show', $item->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-stone-200 text-stone-700 hover:bg-stone-100 text-[11px] font-semibold transition" title="View 6-Tab Details">
                                        <span>Details</span>
                                        <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-12 text-center text-stone-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-12 w-12 rounded-full bg-stone-100 flex items-center justify-center text-stone-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <p class="text-sm font-medium text-stone-700">No inventory items match criteria</p>
                                    <p class="text-xs text-stone-400 mt-1">Try resetting your filters or check other status tabs.</p>
                                    <a href="{{ route('admin.inventory.index') }}" class="mt-4 px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold rounded-xl transition">
                                        Reset Filters
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($items->hasPages())
            <div class="p-4 border-t border-stone-200/80 bg-stone-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-stone-500">
                <div>
                    Showing <span class="font-semibold text-stone-700">{{ $items->firstItem() }}</span> to <span class="font-semibold text-stone-700">{{ $items->lastItem() }}</span> of <span class="font-semibold text-stone-700">{{ $items->total() }}</span> stock items
                </div>
                <div>
                    {{ $items->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Quick Adjust Stock Modal -->
<div id="adjust-stock-modal" class="fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-stone-200">
        <div class="flex items-center justify-between pb-3 border-b border-stone-100">
            <div>
                <h3 class="text-sm font-bold text-stone-900">Adjust Inventory Stock</h3>
                <p id="modal-product-subtitle" class="text-xs text-stone-500 mt-0.5">Product Name • SKU</p>
            </div>
            <button type="button" onclick="closeAdjustModal()" class="text-stone-400 hover:text-stone-600">✕</button>
        </div>

        <form id="adjust-stock-form" method="POST" action="" class="mt-4 space-y-4">
            @csrf

            <!-- Mode Selector -->
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1.5">Adjustment Action</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex items-center justify-center gap-1.5 p-2 rounded-xl border border-stone-200 text-xs font-medium cursor-pointer hover:bg-stone-50 has-checked:border-emerald-600 has-checked:bg-emerald-50 has-checked:text-emerald-900">
                        <input type="radio" name="adjustment_mode" value="add" checked class="hidden">
                        <span>+ Add Stock</span>
                    </label>
                    <label class="flex items-center justify-center gap-1.5 p-2 rounded-xl border border-stone-200 text-xs font-medium cursor-pointer hover:bg-stone-50 has-checked:border-rose-600 has-checked:bg-rose-50 has-checked:text-rose-900">
                        <input type="radio" name="adjustment_mode" value="remove" class="hidden">
                        <span>- Remove Stock</span>
                    </label>
                    <label class="flex items-center justify-center gap-1.5 p-2 rounded-xl border border-stone-200 text-xs font-medium cursor-pointer hover:bg-stone-50 has-checked:border-blue-600 has-checked:bg-blue-50 has-checked:text-blue-900">
                        <input type="radio" name="adjustment_mode" value="set" class="hidden">
                        <span>= Set Exact</span>
                    </label>
                </div>
            </div>

            <!-- Current Stock vs Input Quantity -->
            <div class="grid grid-cols-2 gap-3 bg-stone-50 p-3 rounded-xl border border-stone-200/60">
                <div>
                    <span class="text-[11px] text-stone-500 font-medium block">Current On Hand</span>
                    <span id="modal-current-stock" class="text-lg font-bold text-stone-900">0</span>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-stone-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" min="0" placeholder="Qty" class="w-full text-xs p-2 rounded-lg border border-stone-200 bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition font-bold" required>
                </div>
            </div>

            <!-- Reason Selector -->
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Reason / Movement Type</label>
                <select name="reason" class="w-full text-xs p-2.5 rounded-xl border border-stone-200 bg-stone-50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" required>
                    <option value="manual_adjustment">Manual Reconciliation / Recount</option>
                    <option value="purchase">Purchase / Supplier Inward Replenishment</option>
                    <option value="damage">Damaged / Expired Write-off</option>
                    <option value="refund">Customer Return Restock</option>
                    <option value="transfer">Warehouse Transfer</option>
                </select>
            </div>

            <!-- Remarks -->
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Audit Notes / Remarks</label>
                <textarea name="remarks" rows="2" placeholder="e.g. PO-409 receipt, cycle count correction, or damaged bottle..." class="w-full text-xs p-2.5 rounded-xl border border-stone-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"></textarea>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-100">
                <button type="button" onclick="closeAdjustModal()" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl transition shadow-xs">
                    Apply Adjustment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAdjustModal(id, productName, sku, currentStock) {
        const modal = document.getElementById('adjust-stock-modal');
        const form = document.getElementById('adjust-stock-form');
        const subtitle = document.getElementById('modal-product-subtitle');
        const stockDisplay = document.getElementById('modal-current-stock');

        form.action = `/admin/inventory/${id}/adjust`;
        subtitle.textContent = `${productName} • ${sku}`;
        stockDisplay.textContent = currentStock;

        modal.classList.remove('hidden');
    }

    function closeAdjustModal() {
        document.getElementById('adjust-stock-modal').classList.add('hidden');
    }
</script>
@endsection
