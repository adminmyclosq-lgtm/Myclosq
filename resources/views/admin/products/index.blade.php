@extends('layouts.admin', ['title' => 'Products Catalog | Guided Wellness Enterprise Admin'])

@section('content')
<div class="min-h-screen bg-stone-50 pb-16">
    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8 pt-6 space-y-6">

        {{-- Header & Quick Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">Products</h1>
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 border border-emerald-200">
                        {{ number_format($kpis['total_products']) }} items
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-stone-500 mt-1">Master catalog source of truth. Manage SKUs, pricing, physical specs, inventory stock, and order linkages.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Export Dropdown --}}
                <div class="relative inline-block text-left" id="export-dropdown-wrapper">
                    <button
                        type="button"
                        onclick="toggleExportDropdown()"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 hover:border-stone-300 transition"
                    >
                        <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export Catalog</span>
                        <svg class="h-3 w-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="export-dropdown-menu" class="hidden absolute right-0 z-30 mt-1.5 w-44 rounded-xl border border-stone-200 bg-white p-1 shadow-lg ring-1 ring-black/5">
                        <a href="{{ route('admin.products.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-stone-700 hover:bg-stone-50 transition">
                            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export to CSV
                        </a>
                        <a href="{{ route('admin.products.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-stone-700 hover:bg-stone-50 transition">
                            <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Export to Excel (.xls)
                        </a>
                    </div>
                </div>

                {{-- Add Product Primary Button --}}
                <a
                    href="{{ route('admin.products.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#163826] transition active:scale-98"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add Product</span>
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 text-xs font-semibold text-emerald-900 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900">&times;</button>
            </div>
        @endif

        {{-- 1. KPI Cards (Shopify / Stripe BI style) --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            {{-- Total Products --}}
            <a href="{{ route('admin.products.index') }}" class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:border-stone-300 hover:shadow-xs transition group">
                <div class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Total Products</div>
                <div class="mt-1 text-2xl font-bold font-mono text-stone-900">{{ number_format($kpis['total_products']) }}</div>
                <div class="mt-1 text-[11px] text-stone-400 font-medium">Master SKU Count</div>
            </a>

            {{-- Active Products --}}
            <a href="{{ route('admin.products.index', ['status' => 'active']) }}" class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:border-emerald-300 hover:shadow-xs transition group">
                <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Active
                </div>
                <div class="mt-1 text-2xl font-bold font-mono text-stone-900">{{ number_format($kpis['active_products']) }}</div>
                <div class="mt-1 text-[11px] text-emerald-700 font-medium">Visible to customers</div>
            </a>

            {{-- Out of Stock --}}
            <a href="{{ route('admin.products.index', ['stock' => 'out_of_stock']) }}" class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:border-rose-300 hover:shadow-xs transition group">
                <div class="text-[11px] font-bold text-rose-600 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                    Out of Stock
                </div>
                <div class="mt-1 text-2xl font-bold font-mono text-rose-600">{{ number_format($kpis['out_of_stock']) }}</div>
                <div class="mt-1 text-[11px] text-stone-400 font-medium">0 units remaining</div>
            </a>

            {{-- Low Stock --}}
            <a href="{{ route('admin.products.index', ['stock' => 'low_stock']) }}" class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:border-amber-300 hover:shadow-xs transition group">
                <div class="text-[11px] font-bold text-amber-700 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                    Low Stock
                </div>
                <div class="mt-1 text-2xl font-bold font-mono text-amber-700">{{ number_format($kpis['low_stock']) }}</div>
                <div class="mt-1 text-[11px] text-stone-400 font-medium">≤ 5 units remaining</div>
            </a>

            {{-- Draft Products --}}
            <a href="{{ route('admin.products.index', ['status' => 'draft']) }}" class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:border-stone-300 hover:shadow-xs transition group">
                <div class="text-[11px] font-bold text-stone-500 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-stone-400"></span>
                    Drafts
                </div>
                <div class="mt-1 text-2xl font-bold font-mono text-stone-900">{{ number_format($kpis['draft_products']) }}</div>
                <div class="mt-1 text-[11px] text-stone-400 font-medium">Unpublished products</div>
            </a>

            {{-- Archived Products --}}
            <a href="{{ route('admin.products.index', ['status' => 'archived']) }}" class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs hover:border-stone-300 hover:shadow-xs transition group">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                    Archived
                </div>
                <div class="mt-1 text-2xl font-bold font-mono text-stone-700">{{ number_format($kpis['archived_products']) }}</div>
                <div class="mt-1 text-[11px] text-stone-400 font-medium">Discontinued items</div>
            </a>
        </div>

        {{-- 2. Advanced Search & Filter Bar --}}
        <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs space-y-3.5">
            <form method="GET" action="{{ route('admin.products.index') }}" id="product-filter-form">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 items-center">
                    {{-- Search Input --}}
                    <div class="sm:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search by name, SKU, barcode, brand..."
                            class="h-9 w-full rounded-xl border border-stone-200 bg-stone-50/50 pl-9 pr-3 text-xs text-stone-900 placeholder-stone-400 focus:bg-white focus:border-[#204b34] focus:outline-none focus:ring-2 focus:ring-[#204b34]/15 transition"
                        >
                    </div>

                    {{-- Category Filter --}}
                    <div>
                        <select
                            name="category_id"
                            onchange="this.form.submit()"
                            class="h-9 w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 text-xs text-stone-800 focus:bg-white focus:border-[#204b34] focus:outline-none transition cursor-pointer"
                        >
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Brand Filter --}}
                    <div>
                        <select
                            name="brand"
                            onchange="this.form.submit()"
                            class="h-9 w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 text-xs text-stone-800 focus:bg-white focus:border-[#204b34] focus:outline-none transition cursor-pointer"
                        >
                            <option value="">All Brands</option>
                            @foreach($brands as $brandName)
                                <option value="{{ $brandName }}" @selected(request('brand') == $brandName)>
                                    {{ $brandName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <select
                            name="status"
                            onchange="this.form.submit()"
                            class="h-9 w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 text-xs text-stone-800 focus:bg-white focus:border-[#204b34] focus:outline-none transition cursor-pointer"
                        >
                            <option value="">All Statuses</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                            <option value="archived" @selected(request('status') === 'archived')>Archived</option>
                        </select>
                    </div>

                    {{-- Stock Filter --}}
                    <div>
                        <select
                            name="stock"
                            onchange="this.form.submit()"
                            class="h-9 w-full rounded-xl border border-stone-200 bg-stone-50/50 px-2.5 text-xs text-stone-800 focus:bg-white focus:border-[#204b34] focus:outline-none transition cursor-pointer"
                        >
                            <option value="">All Stock Levels</option>
                            <option value="in_stock" @selected(request('stock') === 'in_stock')>In Stock (> 5)</option>
                            <option value="low_stock" @selected(request('stock') === 'low_stock')>Low Stock (1 - 5)</option>
                            <option value="out_of_stock" @selected(request('stock') === 'out_of_stock')>Out of Stock (0)</option>
                        </select>
                    </div>
                </div>

                {{-- Price & Sort Row --}}
                <div class="mt-3 pt-3 border-t border-stone-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-stone-400 font-medium">Price (₹):</span>
                        <input
                            type="number"
                            step="0.01"
                            name="price_min"
                            value="{{ request('price_min') }}"
                            placeholder="Min"
                            class="h-8 w-20 rounded-lg border border-stone-200 bg-stone-50/50 px-2 text-xs font-mono focus:bg-white focus:border-[#204b34] focus:outline-none"
                        >
                        <span class="text-stone-400">–</span>
                        <input
                            type="number"
                            step="0.01"
                            name="price_max"
                            value="{{ request('price_max') }}"
                            placeholder="Max"
                            class="h-8 w-20 rounded-lg border border-stone-200 bg-stone-50/50 px-2 text-xs font-mono focus:bg-white focus:border-[#204b34] focus:outline-none"
                        >
                        <button type="submit" class="h-8 rounded-lg border border-stone-200 bg-white px-2.5 font-semibold text-stone-700 hover:bg-stone-50 shadow-2xs transition">Apply</button>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Sorting --}}
                        <div class="flex items-center gap-1.5">
                            <span class="text-stone-400 font-medium">Sort:</span>
                            <select
                                name="sort"
                                onchange="this.form.submit()"
                                class="h-8 rounded-lg border border-stone-200 bg-stone-50/50 px-2 text-xs font-semibold text-stone-700 focus:bg-white focus:border-[#204b34] focus:outline-none transition cursor-pointer"
                            >
                                <option value="newest" @selected(request('sort') === 'newest')>Newest First</option>
                                <option value="oldest" @selected(request('sort') === 'oldest')>Oldest First</option>
                                <option value="name_asc" @selected(request('sort') === 'name_asc')>Name (A – Z)</option>
                                <option value="name_desc" @selected(request('sort') === 'name_desc')>Name (Z – A)</option>
                                <option value="sku_asc" @selected(request('sort') === 'sku_asc')>SKU (A – Z)</option>
                            </select>
                        </div>

                        {{-- Clear Filters --}}
                        @if(request()->anyFilled(['search', 'category_id', 'brand', 'status', 'stock', 'price_min', 'price_max', 'sort']))
                            <a
                                href="{{ route('admin.products.index') }}"
                                class="text-rose-600 hover:text-rose-700 font-semibold inline-flex items-center gap-1"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Reset Filters</span>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- 3. Bulk Action Bar (Hidden until rows are selected) --}}
        <form method="POST" action="{{ route('admin.products.bulk-action') }}" id="bulk-action-form">
            @csrf
            <div id="bulk-action-bar" class="hidden rounded-xl border border-stone-200 bg-stone-900 px-4 py-2.5 text-xs text-white shadow-md flex items-center justify-between transition-all duration-200">
                <div class="flex items-center gap-3">
                    <span class="font-bold text-emerald-400"><span id="selected-count">0</span> products selected</span>
                    <span class="text-stone-500">|</span>
                    <div class="flex items-center gap-2">
                        <select name="action" id="bulk-action-select" class="h-8 rounded-lg bg-stone-800 border border-stone-700 px-2 text-xs text-white focus:outline-none">
                            <option value="status">Change Status to...</option>
                            <option value="category">Assign Category...</option>
                            <option value="delete">Delete Selected</option>
                        </select>

                        <select name="value" id="bulk-status-select" class="h-8 rounded-lg bg-stone-800 border border-stone-700 px-2 text-xs text-white focus:outline-none">
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                            <option value="archived">Archived</option>
                        </select>

                        <select name="value_category" id="bulk-category-select" class="hidden h-8 rounded-lg bg-stone-800 border border-stone-700 px-2 text-xs text-white focus:outline-none">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>

                        <button type="button" onclick="submitBulkAction()" class="h-8 px-3 rounded-lg bg-emerald-600 font-semibold text-white hover:bg-emerald-500 transition">Apply</button>
                    </div>
                </div>
                <button type="button" onclick="deselectAll()" class="text-stone-400 hover:text-white font-medium">Cancel</button>
            </div>

            {{-- 4. Products Data Table --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-600">
                        <thead class="bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500 border-b border-stone-200/80 select-none">
                            <tr>
                                <th class="py-3.5 pl-4 pr-2 w-10">
                                    <input type="checkbox" id="select-all-checkbox" onclick="toggleSelectAll(this)" class="rounded border-stone-300 text-[#204b34] focus:ring-[#204b34]/20">
                                </th>
                                <th class="py-3.5 px-3">Product</th>
                                <th class="py-3.5 px-3">SKU</th>
                                <th class="py-3.5 px-3">Category</th>
                                <th class="py-3.5 px-3 text-right">Price</th>
                                <th class="py-3.5 px-3 text-center">Stock</th>
                                <th class="py-3.5 px-3 text-center">Status</th>
                                <th class="py-3.5 px-3 text-center">Orders Sold</th>
                                <th class="py-3.5 px-3 text-right">Revenue</th>
                                <th class="py-3.5 pl-3 pr-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($products as $p)
                                @php
                                    $variant = $p->variants->first();
                                    $price = $variant?->currentPrice ?? $variant?->prices->first();
                                    $stock = $variant ? $variant->inventory->sum('quantity_on_hand') : 0;
                                    $primaryImage = $p->productImages->firstWhere('is_primary', true) ?? $p->productImages->first();
                                @endphp
                                <tr class="hover:bg-stone-50/70 transition-colors group">
                                    {{-- Checkbox --}}
                                    <td class="py-3 pl-4 pr-2">
                                        <input
                                            type="checkbox"
                                            name="product_ids[]"
                                            value="{{ $p->id }}"
                                            class="product-row-checkbox rounded border-stone-300 text-[#204b34] focus:ring-[#204b34]/20"
                                            onchange="handleRowCheckboxChange()"
                                        >
                                    </td>

                                    {{-- Product Name & Image --}}
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-3 min-w-[220px]">
                                            @if($primaryImage?->media)
                                                <img
                                                    src="{{ $primaryImage->media->url }}"
                                                    alt="{{ $p->name }}"
                                                    class="h-10 w-10 rounded-xl object-cover border border-stone-200/80 shrink-0 bg-stone-100"
                                                    onerror="this.onerror=null; this.src='https://placehold.co/80x80/f5f5f4/a8a29e?text=No+Image';"
                                                >
                                            @else
                                                <div class="h-10 w-10 rounded-xl bg-stone-100 border border-stone-200 flex items-center justify-center font-bold text-stone-400 text-xs shrink-0 select-none">
                                                    {{ strtoupper(substr($p->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <a
                                                    href="{{ route('admin.products.show', $p) }}"
                                                    class="font-semibold text-stone-900 hover:text-emerald-900 transition-colors truncate block group-hover:underline"
                                                    title="{{ $p->name }}"
                                                >
                                                    {{ $p->name }}
                                                </a>
                                                <div class="text-[11px] text-stone-400 flex items-center gap-1.5 mt-0.5">
                                                    <span>{{ $p->brand ?? 'Guided Wellness' }}</span>
                                                    @if($p->is_featured)
                                                        <span class="inline-flex items-center gap-0.5 text-amber-700 font-semibold text-[10px]">
                                                            ★ Featured
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Base SKU --}}
                                    <td class="py-3 px-3 font-mono font-medium text-stone-700 whitespace-nowrap">
                                        {{ $p->base_sku }}
                                    </td>

                                    {{-- Category --}}
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if($p->category)
                                            <span class="inline-flex items-center rounded-lg bg-stone-100 px-2 py-0.5 text-[11px] font-medium text-stone-700">
                                                {{ $p->category->name }}
                                            </span>
                                        @else
                                            <span class="text-stone-400 italic">Uncategorized</span>
                                        @endif
                                    </td>

                                    {{-- Price --}}
                                    <td class="py-3 px-3 text-right whitespace-nowrap">
                                        <div class="font-bold text-stone-900 font-mono">
                                            ₹{{ number_format((float) ($price?->selling_price ?? 0), 2) }}
                                        </div>
                                        @if($price && $price->mrp > $price->selling_price)
                                            <div class="text-[10px] text-stone-400 line-through font-mono">
                                                ₹{{ number_format((float) $price->mrp, 2) }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Stock Level --}}
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        @if($stock > 5)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-800 border border-emerald-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                {{ $stock }} in stock
                                            </span>
                                        @elseif($stock > 0)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-semibold text-amber-800 border border-amber-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                {{ $stock }} low stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-[11px] font-semibold text-rose-700 border border-rose-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                Out of stock
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status Pill --}}
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        @if($p->status === 'active')
                                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-800 border border-emerald-200/80">
                                                Active
                                            </span>
                                        @elseif($p->status === 'draft')
                                            <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-800 border border-amber-200/80">
                                                Draft
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700 border border-slate-200">
                                                Archived
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Orders Sold --}}
                                    <td class="py-3 px-3 text-center font-mono whitespace-nowrap">
                                        <span class="font-bold text-stone-900">{{ number_format($p->units_sold_computed ?? 0) }}</span>
                                        <span class="text-[10px] text-stone-400 block">({{ $p->orders_count_computed ?? 0 }} orders)</span>
                                    </td>

                                    {{-- Revenue --}}
                                    <td class="py-3 px-3 text-right font-mono font-bold text-stone-900 whitespace-nowrap">
                                        ₹{{ number_format($p->total_revenue_computed ?? 0, 2) }}
                                    </td>

                                    {{-- Actions Dropdown --}}
                                    <td class="py-3 pl-3 pr-4 text-right whitespace-nowrap">
                                        <div class="relative inline-block text-left" id="action-wrapper-{{ $p->id }}">
                                            <button
                                                type="button"
                                                onclick="toggleActionMenu({{ $p->id }})"
                                                class="rounded-lg p-1.5 text-stone-400 hover:bg-stone-100 hover:text-stone-700 transition"
                                                title="Product actions"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                            </button>
                                            <div id="action-menu-{{ $p->id }}" class="hidden absolute right-0 z-30 mt-1 w-44 rounded-xl border border-stone-200 bg-white p-1 shadow-lg ring-1 ring-black/5 text-left">
                                                {{-- View Details Hub --}}
                                                <a href="{{ route('admin.products.show', $p) }}" class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium text-stone-700 hover:bg-stone-50 hover:text-stone-900 transition">
                                                    <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    View Details
                                                </a>

                                                {{-- Edit Product --}}
                                                <a href="{{ route('admin.products.edit', $p) }}" class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium text-stone-700 hover:bg-stone-50 hover:text-stone-900 transition">
                                                    <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit Product
                                                </a>

                                                {{-- Duplicate Product --}}
                                                <form method="POST" action="{{ route('admin.products.duplicate', $p) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="w-full flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium text-stone-700 hover:bg-stone-50 hover:text-stone-900 transition text-left">
                                                        <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                                        Duplicate
                                                    </button>
                                                </form>

                                                {{-- Archive / Restore --}}
                                                @if($p->status === 'archived')
                                                    <form method="POST" action="{{ route('admin.products.restore', $p) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-50 transition text-left">
                                                            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                            Restore Product
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.products.archive', $p) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="w-full flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium text-stone-700 hover:bg-stone-50 hover:text-stone-900 transition text-left">
                                                            <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                                            Archive Product
                                                        </button>
                                                    </form>
                                                @endif

                                                <div class="border-t border-stone-100 my-1"></div>

                                                {{-- Delete --}}
                                                <form method="POST" action="{{ route('admin.products.destroy', $p) }}" onsubmit="return confirm('Are you sure you want to delete this product? All catalog relations will be safely archived.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50 transition text-left">
                                                        <svg class="h-3.5 w-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-16 text-center">
                                        <div class="mx-auto h-12 w-12 rounded-2xl bg-stone-100 flex items-center justify-center text-stone-400 mb-3">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                        <div class="text-sm font-semibold text-stone-800">No products match your criteria</div>
                                        <p class="text-xs text-stone-400 mt-1 max-w-sm mx-auto">Try adjusting your filters or search keywords, or add your first product to the catalog.</p>
                                        <div class="mt-4 flex items-center justify-center gap-3">
                                            @if(request()->anyFilled(['search', 'category_id', 'brand', 'status', 'stock']))
                                                <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 shadow-2xs">Clear Filters</a>
                                            @endif
                                            <a href="{{ route('admin.products.create') }}" class="rounded-xl bg-[#204b34] px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-[#163826]">+ Add Product</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                    <div class="border-t border-stone-200/80 px-4 py-3 bg-stone-50/50 flex items-center justify-between">
                        <div class="text-xs text-stone-500">
                            Showing <span class="font-semibold text-stone-900">{{ $products->firstItem() }}</span> to <span class="font-semibold text-stone-900">{{ $products->lastItem() }}</span> of <span class="font-semibold text-stone-900">{{ $products->total() }}</span> products
                        </div>
                        <div>
                            {{ $products->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </form>

    </div>
</div>

{{-- Interactive Scripts for Bulk Actions, Modals & Dropdowns --}}
<script>
    function toggleExportDropdown() {
        const menu = document.getElementById('export-dropdown-menu');
        menu.classList.toggle('hidden');
    }

    function toggleActionMenu(id) {
        // Close other menus first
        document.querySelectorAll('[id^="action-menu-"]').forEach(el => {
            if (el.id !== 'action-menu-' + id) el.classList.add('hidden');
        });
        const menu = document.getElementById('action-menu-' + id);
        if (menu) menu.classList.toggle('hidden');
    }

    document.addEventListener('click', (e) => {
        // Export dropdown close on outside click
        const exportWrapper = document.getElementById('export-dropdown-wrapper');
        const exportMenu = document.getElementById('export-dropdown-menu');
        if (exportWrapper && !exportWrapper.contains(e.target) && exportMenu && !exportMenu.classList.contains('hidden')) {
            exportMenu.classList.add('hidden');
        }

        // Action menus close on outside click
        if (!e.target.closest('[id^="action-wrapper-"]')) {
            document.querySelectorAll('[id^="action-menu-"]').forEach(el => el.classList.add('hidden'));
        }
    });

    // Bulk Actions Selection Engine
    function toggleSelectAll(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.product-row-checkbox');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateBulkActionBar();
    }

    function handleRowCheckboxChange() {
        updateBulkActionBar();
    }

    function updateBulkActionBar() {
        const selected = document.querySelectorAll('.product-row-checkbox:checked');
        const bar = document.getElementById('bulk-action-bar');
        const countSpan = document.getElementById('selected-count');
        const master = document.getElementById('select-all-checkbox');

        if (countSpan) countSpan.textContent = selected.length;

        if (selected.length > 0) {
            bar.classList.remove('hidden');
        } else {
            bar.classList.add('hidden');
            if (master) master.checked = false;
        }
    }

    function deselectAll() {
        document.querySelectorAll('.product-row-checkbox').forEach(cb => cb.checked = false);
        const master = document.getElementById('select-all-checkbox');
        if (master) master.checked = false;
        updateBulkActionBar();
    }

    document.getElementById('bulk-action-select')?.addEventListener('change', function() {
        const statusSelect = document.getElementById('bulk-status-select');
        const categorySelect = document.getElementById('bulk-category-select');

        if (this.value === 'status') {
            statusSelect.classList.remove('hidden');
            categorySelect.classList.add('hidden');
        } else if (this.value === 'category') {
            statusSelect.classList.add('hidden');
            categorySelect.classList.remove('hidden');
        } else {
            statusSelect.classList.add('hidden');
            categorySelect.classList.add('hidden');
        }
    });

    function submitBulkAction() {
        const form = document.getElementById('bulk-action-form');
        const action = document.getElementById('bulk-action-select').value;

        if (action === 'delete') {
            if (!confirm('Are you sure you want to delete the selected products?')) return;
        }

        if (action === 'category') {
            const catVal = document.getElementById('bulk-category-select').value;
            const hiddenVal = document.createElement('input');
            hiddenVal.type = 'hidden';
            hiddenVal.name = 'value';
            hiddenVal.value = catVal;
            form.appendChild(hiddenVal);
        }

        form.submit();
    }
</script>
@endsection
