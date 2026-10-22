@extends('layouts.admin', ['title' => $product->name . ' | Product Master Hub | Guided Wellness Admin'])

@section('content')
<div class="min-h-screen bg-stone-50 pb-20">
    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8 pt-6 space-y-6">

        {{-- Top Navigation & Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-stone-200/80 pb-5">
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-stone-500 mb-1">
                    <a href="{{ route('admin.products.index') }}" class="hover:text-stone-900 transition flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Products</span>
                    </a>
                    <span>/</span>
                    <span class="text-stone-800 font-semibold">{{ $product->base_sku }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">
                        {{ $product->name }}
                    </h1>
                    {{-- Status Badge --}}
                    @if($product->status === 'active')
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 border border-emerald-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            Active
                        </span>
                    @elseif($product->status === 'draft')
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-800 border border-amber-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                            Draft
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700 border border-slate-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span>
                            Archived
                        </span>
                    @endif

                    @if($product->is_featured)
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-800 border border-amber-200">
                            ★ Featured
                        </span>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Storefront Link --}}
                <a
                    href="{{ route('product.show', $product->slug) }}"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition"
                    title="Preview on live storefront"
                >
                    <svg class="h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Storefront</span>
                </a>

                {{-- Duplicate --}}
                <form method="POST" action="{{ route('admin.products.duplicate', $product) }}" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition"
                        title="Duplicate product"
                    >
                        <svg class="h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                        <span>Duplicate</span>
                    </button>
                </form>

                {{-- Archive / Restore --}}
                @if($product->status === 'archived')
                    <form method="POST" action="{{ route('admin.products.restore', $product) }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-800 hover:bg-emerald-100 transition">
                            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Restore</span>
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.products.archive', $product) }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition">
                            <svg class="h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            <span>Archive</span>
                        </button>
                    </form>
                @endif

                {{-- Edit Product --}}
                <a
                    href="{{ route('admin.products.edit', $product) }}"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#163826] transition active:scale-98"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Product</span>
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

        {{-- Quick Operational KPI Strip --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs">
                <div class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Selling Price</div>
                <div class="mt-1 text-xl font-bold font-mono text-stone-900">₹{{ number_format($pricingStats['selling_price'], 2) }}</div>
                <div class="mt-0.5 text-[10.5px] text-stone-400 font-mono">MRP: ₹{{ number_format($pricingStats['mrp'], 2) }}</div>
            </div>

            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs">
                <div class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Available Stock</div>
                <div class="mt-1 text-xl font-bold font-mono {{ $performance['available_stock'] > 5 ? 'text-emerald-700' : ($performance['available_stock'] > 0 ? 'text-amber-700' : 'text-rose-600') }}">
                    {{ number_format($performance['available_stock']) }}
                </div>
                <div class="mt-0.5 text-[10.5px] text-stone-400 font-mono">Reserved: {{ $performance['stock_reserved'] }}</div>
            </div>

            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs">
                <div class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Total Units Sold</div>
                <div class="mt-1 text-xl font-bold font-mono text-stone-900">{{ number_format($performance['units_sold']) }}</div>
                <div class="mt-0.5 text-[10.5px] text-emerald-700 font-medium">From paid orders</div>
            </div>

            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs">
                <div class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Total Revenue</div>
                <div class="mt-1 text-xl font-bold font-mono text-emerald-900">₹{{ number_format($performance['total_revenue'], 2) }}</div>
                <div class="mt-0.5 text-[10.5px] text-stone-400 font-medium">Cumulative gross</div>
            </div>

            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs">
                <div class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Unit Margin</div>
                <div class="mt-1 text-xl font-bold font-mono text-stone-900">{{ $pricingStats['margin_percentage'] }}%</div>
                <div class="mt-0.5 text-[10.5px] text-stone-400 font-mono">Profit: ₹{{ number_format($pricingStats['profit_per_unit'], 2) }}</div>
            </div>

            <div class="rounded-2xl border border-stone-200/90 bg-white p-4 shadow-2xs">
                <div class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Customers</div>
                <div class="mt-1 text-xl font-bold font-mono text-stone-900">{{ number_format($performance['unique_customers']) }}</div>
                <div class="mt-0.5 text-[10.5px] text-stone-400 font-medium">Unique buyers</div>
            </div>
        </div>

        {{-- 7 Master Hub Tabs Header --}}
        @php
            $activeTab = request('tab', 'overview');
        @endphp
        <div class="border-b border-stone-200/90 flex items-center gap-1 sm:gap-2 overflow-x-auto select-none">
            <button
                type="button"
                onclick="switchTab('overview')"
                id="tab-btn-overview"
                class="tab-trigger pb-3 px-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'overview' ? 'border-[#204b34] text-[#204b34]' : 'border-transparent text-stone-500 hover:text-stone-800' }}"
            >
                Overview
            </button>
            <button
                type="button"
                onclick="switchTab('inventory')"
                id="tab-btn-inventory"
                class="tab-trigger pb-3 px-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'inventory' ? 'border-[#204b34] text-[#204b34]' : 'border-transparent text-stone-500 hover:text-stone-800' }}"
            >
                Inventory ({{ $performance['stock_on_hand'] }})
            </button>
            <button
                type="button"
                onclick="switchTab('orders')"
                id="tab-btn-orders"
                class="tab-trigger pb-3 px-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'orders' ? 'border-[#204b34] text-[#204b34]' : 'border-transparent text-stone-500 hover:text-stone-800' }}"
            >
                Orders ({{ $orderItems->total() }})
            </button>
            <button
                type="button"
                onclick="switchTab('customers')"
                id="tab-btn-customers"
                class="tab-trigger pb-3 px-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'customers' ? 'border-[#204b34] text-[#204b34]' : 'border-transparent text-stone-500 hover:text-stone-800' }}"
            >
                Customers ({{ $customers->count() }})
            </button>
            <button
                type="button"
                onclick="switchTab('pricing')"
                id="tab-btn-pricing"
                class="tab-trigger pb-3 px-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'pricing' ? 'border-[#204b34] text-[#204b34]' : 'border-transparent text-stone-500 hover:text-stone-800' }}"
            >
                Pricing & Rules
            </button>
            <button
                type="button"
                onclick="switchTab('shipping')"
                id="tab-btn-shipping"
                class="tab-trigger pb-3 px-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'shipping' ? 'border-[#204b34] text-[#204b34]' : 'border-transparent text-stone-500 hover:text-stone-800' }}"
            >
                Shipping
            </button>
            <button
                type="button"
                onclick="switchTab('history')"
                id="tab-btn-history"
                class="tab-trigger pb-3 px-3 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'history' ? 'border-[#204b34] text-[#204b34]' : 'border-transparent text-stone-500 hover:text-stone-800' }}"
            >
                Audit History
            </button>
        </div>

        {{-- TAB 1: OVERVIEW --}}
        <div id="tab-pane-overview" class="tab-pane {{ $activeTab === 'overview' ? '' : 'hidden' }} space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Product Card & Media Gallery --}}
                <div class="rounded-2xl border border-stone-200/90 bg-white p-5 sm:p-6 shadow-2xs space-y-4">
                    <div class="aspect-square w-full rounded-2xl bg-stone-100 overflow-hidden border border-stone-200/80">
                        @if($product->primary_image_url)
                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex items-center justify-center font-bold text-stone-400 text-3xl select-none">
                                {{ strtoupper(substr($product->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>

                    {{-- Image Gallery Strip --}}
                    @if($product->productImages->count() > 1)
                        <div class="grid grid-cols-4 gap-2 pt-1">
                            @foreach($product->productImages as $img)
                                @if($img->media)
                                    <div class="aspect-square rounded-xl border border-stone-200 bg-stone-50 overflow-hidden">
                                        <img src="{{ $img->media->url }}" alt="{{ $img->alt_text }}" class="h-full w-full object-cover">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-3 border-t border-stone-100 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-stone-400 font-medium">Category:</span>
                            <span class="font-semibold text-stone-900">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-stone-400 font-medium">Brand / Vendor:</span>
                            <span class="font-semibold text-stone-900">{{ $product->brand ?? 'Guided Wellness' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-stone-400 font-medium">Product Type:</span>
                            <span class="font-semibold text-stone-900">{{ $product->product_type ?? 'Standard' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-stone-400 font-medium">Base SKU:</span>
                            <span class="font-mono font-bold text-stone-900">{{ $product->base_sku }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-stone-400 font-medium">Created At:</span>
                            <span class="text-stone-700">{{ $product->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Full Specs & Information (2 cols) --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-2xl border border-stone-200/90 bg-white p-5 sm:p-6 shadow-2xs space-y-4">
                        <div class="border-b border-stone-100 pb-3">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-800">Description & Details</h2>
                        </div>

                        @if($product->short_description)
                            <div>
                                <h3 class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">Summary</h3>
                                <p class="text-xs text-stone-700 font-medium leading-relaxed">{{ $product->short_description }}</p>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">Full Description</h3>
                            <div class="text-xs text-stone-800 leading-relaxed whitespace-pre-line bg-stone-50/50 p-3.5 rounded-xl border border-stone-100">
                                {{ $product->description ?: 'No detailed description provided.' }}
                            </div>
                        </div>

                        @if($product->ingredients || $product->benefits)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                @if($product->ingredients)
                                    <div>
                                        <h3 class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">Ingredients</h3>
                                        <div class="text-xs text-stone-700 bg-stone-50/50 p-3 rounded-xl border border-stone-100 whitespace-pre-line">
                                            {{ $product->ingredients }}
                                        </div>
                                    </div>
                                @endif
                                @if($product->benefits)
                                    <div>
                                        <h3 class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">Benefits</h3>
                                        <div class="text-xs text-stone-700 bg-stone-50/50 p-3 rounded-xl border border-stone-100 whitespace-pre-line">
                                            {{ $product->benefits }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($product->usage_instructions || $product->warnings)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                @if($product->usage_instructions)
                                    <div>
                                        <h3 class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">Usage Instructions</h3>
                                        <div class="text-xs text-stone-700 bg-stone-50/50 p-3 rounded-xl border border-stone-100 whitespace-pre-line">
                                            {{ $product->usage_instructions }}
                                        </div>
                                    </div>
                                @endif
                                @if($product->warnings)
                                    <div>
                                        <h3 class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">Warnings</h3>
                                        <div class="text-xs text-rose-800 bg-rose-50/50 p-3 rounded-xl border border-rose-100 whitespace-pre-line">
                                            {{ $product->warnings }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($product->internal_notes)
                            <div class="pt-2">
                                <h3 class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">Internal Notes (Staff Only)</h3>
                                <div class="text-xs text-stone-600 bg-amber-50/40 p-3 rounded-xl border border-amber-200/60 whitespace-pre-line">
                                    {{ $product->internal_notes }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: INVENTORY & STOCK MOVEMENTS --}}
        <div id="tab-pane-inventory" class="tab-pane {{ $activeTab === 'inventory' ? '' : 'hidden' }} space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Inventory Management</h2>
                    <p class="text-xs text-stone-500">Live warehouse batches, stock reservations, and historical movement ledger.</p>
                </div>
                <button
                    type="button"
                    onclick="openInventoryModal()"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#163826] transition"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span>Adjust Stock</span>
                </button>
            </div>

            {{-- Warehouses & Batches Breakdown Table --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white shadow-2xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-stone-100 bg-stone-50/50 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-stone-700">Stock by Warehouse & Batch</span>
                    <span class="text-xs font-mono text-stone-500">Total Available: <b>{{ $performance['available_stock'] }}</b></span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-600">
                        <thead class="bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500 border-b border-stone-100">
                            <tr>
                                <th class="py-3 px-4">Warehouse</th>
                                <th class="py-3 px-4">Batch Number</th>
                                <th class="py-3 px-4">Mfg Date</th>
                                <th class="py-3 px-4">Expiry Date</th>
                                <th class="py-3 px-4 text-center">On Hand</th>
                                <th class="py-3 px-4 text-center">Reserved</th>
                                <th class="py-3 px-4 text-center">Reorder Level</th>
                                <th class="py-3 px-4 text-center">Batch Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($inventories as $inv)
                                <tr>
                                    <td class="py-3 px-4 font-mono font-semibold text-stone-900">{{ $inv->warehouse_code }}</td>
                                    <td class="py-3 px-4 font-mono text-stone-700">{{ $inv->batch?->batch_number ?? 'DEFAULT' }}</td>
                                    <td class="py-3 px-4 text-stone-500">{{ $inv->batch?->manufacture_date ?? '—' }}</td>
                                    <td class="py-3 px-4 text-stone-500">{{ $inv->batch?->expiry_date ?? '—' }}</td>
                                    <td class="py-3 px-4 text-center font-mono font-bold text-stone-900">{{ $inv->quantity_on_hand }}</td>
                                    <td class="py-3 px-4 text-center font-mono text-stone-500">{{ $inv->quantity_reserved }}</td>
                                    <td class="py-3 px-4 text-center font-mono text-stone-500">{{ $inv->reorder_level }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Active
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-xs text-stone-400">No inventory batch records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Historical Inventory Transactions Ledger --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white shadow-2xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-stone-100 bg-stone-50/50">
                    <span class="text-xs font-bold uppercase tracking-wider text-stone-700">Stock Movement Audit Trail (Last 30 Transactions)</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-600">
                        <thead class="bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500 border-b border-stone-100">
                            <tr>
                                <th class="py-3 px-4">Date & Time</th>
                                <th class="py-3 px-4">Type / Reason</th>
                                <th class="py-3 px-4 text-center">Delta</th>
                                <th class="py-3 px-4 text-center">Balance After</th>
                                <th class="py-3 px-4">Remarks</th>
                                <th class="py-3 px-4">Initiated By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($inventoryTransactions as $tx)
                                <tr>
                                    <td class="py-3 px-4 text-stone-500 font-mono">{{ $tx->created_at ? $tx->created_at->format('d M Y, h:i A') : '—' }}</td>
                                    <td class="py-3 px-4 font-semibold text-stone-800 capitalize">{{ str_replace('_', ' ', $tx->transaction_type) }}</td>
                                    <td class="py-3 px-4 text-center font-mono font-bold {{ $tx->quantity >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $tx->quantity >= 0 ? '+' : '' }}{{ $tx->quantity }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono font-bold text-stone-900">{{ $tx->balance_after }}</td>
                                    <td class="py-3 px-4 text-stone-600 max-w-xs truncate">{{ $tx->remarks ?: '—' }}</td>
                                    <td class="py-3 px-4 text-stone-700">{{ $tx->creator?->name ?? 'System Process' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-xs text-stone-400">No stock movements recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 3: ORDERS TAB --}}
        <div id="tab-pane-orders" class="tab-pane {{ $activeTab === 'orders' ? '' : 'hidden' }} space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Orders Containing This Product</h2>
                    <p class="text-xs text-stone-500">Live orders linked via Product ID ({{ $orderItems->total() }} line items found).</p>
                </div>
            </div>

            <div class="rounded-2xl border border-stone-200/90 bg-white shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-600">
                        <thead class="bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500 border-b border-stone-100">
                            <tr>
                                <th class="py-3 px-4">Order #</th>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Customer</th>
                                <th class="py-3 px-4 text-center">Qty</th>
                                <th class="py-3 px-4 text-right">Unit Price</th>
                                <th class="py-3 px-4 text-right">Line Total</th>
                                <th class="py-3 px-4 text-center">Payment</th>
                                <th class="py-3 px-4 text-center">Fulfilment</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($orderItems as $item)
                                @php $order = $item->order; @endphp
                                <tr class="hover:bg-stone-50/50 transition">
                                    <td class="py-3 px-4 font-mono font-bold text-stone-900">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="hover:text-emerald-800 hover:underline">
                                            #{{ $order->id }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 text-stone-500 font-mono">{{ $order->created_at->format('d M Y') }}</td>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-stone-900">{{ $order->shippingAddress?->name ?? $order->user?->name ?? 'Guest' }}</div>
                                        <div class="text-[10.5px] text-stone-400 truncate">{{ $order->shippingAddress?->email ?? $order->user?->email ?? '—' }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono font-bold text-stone-900">{{ $item->quantity }}</td>
                                    <td class="py-3 px-4 text-right font-mono">₹{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-stone-900">₹{{ number_format((float) $item->line_total, 2) }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-semibold {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-semibold bg-stone-100 text-stone-700">
                                            {{ ucfirst($order->fulfilment_status ?? 'unfulfilled') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-xs font-semibold text-emerald-800 hover:underline">
                                            View Order →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-xs text-stone-400">
                                        No orders placed for this product yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($orderItems->hasPages())
                    <div class="p-3 border-t border-stone-100 bg-stone-50/50">
                        {{ $orderItems->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB 4: CUSTOMERS TAB --}}
        <div id="tab-pane-customers" class="tab-pane {{ $activeTab === 'customers' ? '' : 'hidden' }} space-y-4">
            <div>
                <h2 class="text-base font-bold text-stone-900">Customer Cohort for this Product</h2>
                <p class="text-xs text-stone-500">Unique buyers aggregated by lifetime product volume ({{ $customers->count() }} unique customers).</p>
            </div>

            <div class="rounded-2xl border border-stone-200/90 bg-white shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-600">
                        <thead class="bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500 border-b border-stone-100">
                            <tr>
                                <th class="py-3 px-4">Customer Name</th>
                                <th class="py-3 px-4">Email Address</th>
                                <th class="py-3 px-4">Phone</th>
                                <th class="py-3 px-4 text-center">Units Purchased</th>
                                <th class="py-3 px-4 text-center">Orders Count</th>
                                <th class="py-3 px-4 text-right">Total Spent on Product</th>
                                <th class="py-3 px-4">Last Ordered</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($customers as $c)
                                <tr class="hover:bg-stone-50/50 transition">
                                    <td class="py-3 px-4 font-semibold text-stone-900">{{ $c->customer_name }}</td>
                                    <td class="py-3 px-4 text-stone-600">{{ $c->customer_email }}</td>
                                    <td class="py-3 px-4 font-mono text-stone-500">{{ $c->customer_phone }}</td>
                                    <td class="py-3 px-4 text-center font-mono font-bold text-stone-900">{{ $c->total_units_bought }}</td>
                                    <td class="py-3 px-4 text-center font-mono">{{ $c->orders_count }}</td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-emerald-900">₹{{ number_format((float) $c->total_spent, 2) }}</td>
                                    <td class="py-3 px-4 text-stone-500 font-mono">{{ $c->last_order_date ? date('d M Y', strtotime($c->last_order_date)) : '—' }}</td>
                                    <td class="py-3 px-4 text-right">
                                        @if($c->user_id)
                                            <a href="{{ route('admin.customers.show', $c->user_id) }}" class="text-xs font-semibold text-emerald-800 hover:underline">
                                                Profile →
                                            </a>
                                        @else
                                            <span class="text-stone-400 text-xs italic">Guest</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-xs text-stone-400">
                                        No customer purchase records recorded for this product yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 5: PRICING TAB --}}
        <div id="tab-pane-pricing" class="tab-pane {{ $activeTab === 'pricing' ? '' : 'hidden' }} space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                    <span class="text-xs font-bold text-stone-400 uppercase tracking-wider">Selling Price</span>
                    <div class="text-2xl font-bold font-mono text-stone-900 mt-1">₹{{ number_format($pricingStats['selling_price'], 2) }}</div>
                    <span class="text-xs text-stone-400 mt-1 block">Live storefront checkout price</span>
                </div>
                <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                    <span class="text-xs font-bold text-stone-400 uppercase tracking-wider">Compare-at Price (MRP)</span>
                    <div class="text-2xl font-bold font-mono text-stone-900 mt-1">₹{{ number_format($pricingStats['mrp'], 2) }}</div>
                    <span class="text-xs text-emerald-700 font-semibold mt-1 block">{{ $pricingStats['discount_percentage'] }}% discount to shopper</span>
                </div>
                <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs">
                    <span class="text-xs font-bold text-stone-400 uppercase tracking-wider">Unit Profit Margin</span>
                    <div class="text-2xl font-bold font-mono text-emerald-800 mt-1">{{ $pricingStats['margin_percentage'] }}%</div>
                    <span class="text-xs text-stone-500 font-mono mt-1 block">Profit: ₹{{ number_format($pricingStats['profit_per_unit'], 2) }} (Cost: ₹{{ number_format($pricingStats['cost_price'], 2) }})</span>
                </div>
            </div>

            {{-- Price Revision History --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white shadow-2xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-stone-100 bg-stone-50/50">
                    <span class="text-xs font-bold uppercase tracking-wider text-stone-700">Historical Price Revisions</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-600">
                        <thead class="bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500 border-b border-stone-100">
                            <tr>
                                <th class="py-3 px-4">Effective From</th>
                                <th class="py-3 px-4">Effective To</th>
                                <th class="py-3 px-4 text-right">Selling Price</th>
                                <th class="py-3 px-4 text-right">MRP</th>
                                <th class="py-3 px-4 text-center">Tax %</th>
                                <th class="py-3 px-4">Customer Group</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($priceHistory as $pr)
                                <tr>
                                    <td class="py-3 px-4 font-mono text-stone-700">{{ $pr->effective_from ? date('d M Y, h:i A', strtotime($pr->effective_from)) : '—' }}</td>
                                    <td class="py-3 px-4 font-mono text-stone-500">{{ $pr->effective_to ? date('d M Y, h:i A', strtotime($pr->effective_to)) : 'Ongoing' }}</td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-stone-900">₹{{ number_format((float) $pr->selling_price, 2) }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-stone-500">₹{{ number_format((float) $pr->mrp, 2) }}</td>
                                    <td class="py-3 px-4 text-center font-mono">{{ $pr->tax_percentage }}%</td>
                                    <td class="py-3 px-4 font-medium text-stone-700">{{ $pr->customer_group }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @if($pr->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-semibold bg-stone-100 text-stone-600">
                                                Superseded
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-xs text-stone-400">No price records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 6: SHIPPING TAB --}}
        <div id="tab-pane-shipping" class="tab-pane {{ $activeTab === 'shipping' ? '' : 'hidden' }} space-y-6">
            <div class="rounded-2xl border border-stone-200/90 bg-white p-5 sm:p-6 shadow-2xs space-y-4 max-w-3xl">
                <div class="border-b border-stone-100 pb-3">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-stone-800">Physical Parcel & Delivery Constraints</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-4">
                        <span class="text-stone-400 font-medium block text-[11px]">Physical Delivery:</span>
                        <span class="font-bold text-stone-900 text-sm mt-0.5 block">
                            {{ $product->requires_shipping ? 'Requires Physical Shipping' : 'Digital / Non-shippable Product' }}
                        </span>
                    </div>

                    <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-4">
                        <span class="text-stone-400 font-medium block text-[11px]">Weight:</span>
                        <span class="font-bold text-stone-900 font-mono text-sm mt-0.5 block">
                            {{ $primaryVariant?->weight_grams ? number_format((float) $primaryVariant->weight_grams, 1) . ' g (' . number_format((float) ($primaryVariant->weight_grams / 1000), 2) . ' kg)' : 'Not specified' }}
                        </span>
                    </div>

                    <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-4">
                        <span class="text-stone-400 font-medium block text-[11px]">Package Dimensions (L × W × H):</span>
                        <span class="font-bold text-stone-900 font-mono text-sm mt-0.5 block">
                            @if($primaryVariant?->length_cm || $primaryVariant?->width_cm || $primaryVariant?->height_cm)
                                {{ $primaryVariant->length_cm ?? 0 }} cm × {{ $primaryVariant->width_cm ?? 0 }} cm × {{ $primaryVariant->height_cm ?? 0 }} cm
                            @else
                                Standard box dimensions
                            @endif
                        </span>
                    </div>

                    <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-4">
                        <span class="text-stone-400 font-medium block text-[11px]">HS / Tariff Code:</span>
                        <span class="font-bold text-stone-900 font-mono text-sm mt-0.5 block">
                            {{ $product->hs_code ?: 'Default Herbal HS Code' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 7: AUDIT HISTORY TAB --}}
        <div id="tab-pane-history" class="tab-pane {{ $activeTab === 'history' ? '' : 'hidden' }} space-y-4">
            <div>
                <h2 class="text-base font-bold text-stone-900">Product Modification Audit Trail</h2>
                <p class="text-xs text-stone-500">Chronological history of all admin updates, pricing shifts, and stock modifications.</p>
            </div>

            <div class="rounded-2xl border border-stone-200/90 bg-white shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-600">
                        <thead class="bg-stone-50/80 text-[11px] font-bold uppercase tracking-wider text-stone-500 border-b border-stone-100">
                            <tr>
                                <th class="py-3 px-4">Timestamp</th>
                                <th class="py-3 px-4">Action</th>
                                <th class="py-3 px-4">Admin User</th>
                                <th class="py-3 px-4">Details / Values Changed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($auditLogs as $log)
                                <tr>
                                    <td class="py-3 px-4 text-stone-500 font-mono whitespace-nowrap">{{ $log->created_at->format('d M Y, h:i A') }}</td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-stone-100 text-stone-800">
                                            {{ str_replace('_', ' ', $log->action) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-stone-900">{{ $log->user?->name ?? 'System' }}</td>
                                    <td class="py-3 px-4 font-mono text-[11px] text-stone-600">
                                        @if($log->new_values)
                                            <span class="text-stone-800 font-medium">{{ json_encode($log->new_values) }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-xs text-stone-400">
                                        No audit entries logged yet.
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

{{-- Stock Adjustment Modal --}}
<div id="inventory-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-stone-200">
        <div class="flex items-center justify-between border-b border-stone-100 pb-3 mb-4">
            <h3 class="text-sm font-bold text-stone-900">Adjust Inventory Stock</h3>
            <button type="button" onclick="closeInventoryModal()" class="text-stone-400 hover:text-stone-700 text-lg">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.products.inventory.adjust', $product) }}">
            @csrf
            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-stone-800 mb-1">Adjustment Mode</label>
                    <select name="adjustment_mode" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 p-2 text-xs font-semibold text-stone-900 focus:bg-white focus:outline-none">
                        <option value="add">Add Stock (+)</option>
                        <option value="subtract">Deduct Stock (-)</option>
                        <option value="set">Set Exact Count (=)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-stone-800 mb-1">Quantity</label>
                    <input type="number" name="quantity" min="0" required placeholder="Units" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 p-2 text-xs font-mono font-bold text-stone-900 focus:bg-white focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-stone-800 mb-1">Reason</label>
                    <select name="reason" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 p-2 text-xs text-stone-900 focus:bg-white focus:outline-none">
                        <option value="new_stock_received">New Stock Received from Supplier</option>
                        <option value="correction">Inventory Audit Correction</option>
                        <option value="damaged">Damaged / Expired Goods</option>
                        <option value="customer_return">Customer Return</option>
                        <option value="cycle_count">Routine Cycle Count</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-stone-800 mb-1">Remarks / Reference</label>
                    <textarea name="remarks" rows="2" placeholder="e.g. Received PO-9482 from factory" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 p-2 text-xs text-stone-900 focus:bg-white focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeInventoryModal()" class="rounded-xl border border-stone-200 bg-white px-4 py-2 font-semibold text-stone-700 hover:bg-stone-50">Cancel</button>
                    <button type="submit" class="rounded-xl bg-[#204b34] px-4 py-2 font-semibold text-white hover:bg-[#163826]">Save Adjustment</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-trigger').forEach(b => {
            b.classList.remove('border-[#204b34]', 'text-[#204b34]');
            b.classList.add('border-transparent', 'text-stone-500');
        });

        const activePane = document.getElementById('tab-pane-' + tabId);
        const activeBtn = document.getElementById('tab-btn-' + tabId);

        if (activePane) activePane.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.classList.add('border-[#204b34]', 'text-[#204b34]');
            activeBtn.classList.remove('border-transparent', 'text-stone-500');
        }

        const url = new URL(window.location);
        url.searchParams.set('tab', tabId);
        window.history.replaceState({}, '', url);
    }

    function openInventoryModal() {
        document.getElementById('inventory-modal').classList.remove('hidden');
    }

    function closeInventoryModal() {
        document.getElementById('inventory-modal').classList.add('hidden');
    }
</script>
@endsection
