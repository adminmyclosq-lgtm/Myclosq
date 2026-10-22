@extends('layouts.admin', ['title' => ($isEdit ? 'Edit ' . $product->name : 'Add New Product') . ' | Guided Wellness Admin'])

@section('content')
<div class="min-h-screen bg-stone-50 pb-24">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 pt-6">

        {{-- Form Header with Back link, title, and action buttons --}}
        <form
            method="POST"
            action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}"
            enctype="multipart/form-data"
            id="product-form"
            class="space-y-6"
        >
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-stone-200/80 pb-5">
                <div>
                    <div class="flex items-center gap-2 text-xs font-medium text-stone-500 mb-1">
                        <a href="{{ route('admin.products.index') }}" class="hover:text-stone-900 transition flex items-center gap-1">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Products</span>
                        </a>
                        <span>/</span>
                        <span class="text-stone-800 font-semibold">{{ $isEdit ? 'Edit Product' : 'Add Product' }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">
                            {{ $isEdit ? $product->name : 'Add New Product' }}
                        </h1>
                        @if($isEdit)
                            <span class="inline-flex items-center rounded-full {{ $product->status === 'active' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($product->status === 'draft' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-700 border-slate-200') }} px-2.5 py-0.5 text-xs font-semibold border">
                                {{ ucfirst($product->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if($isEdit)
                        <a
                            href="{{ route('admin.products.show', $product) }}"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition"
                        >
                            <svg class="h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>View Details</span>
                        </a>
                    @endif
                    <a
                        href="{{ route('admin.products.index') }}"
                        class="inline-flex items-center rounded-xl border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 transition"
                    >
                        Discard
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-[#204b34] px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#163826] transition active:scale-98"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ $isEdit ? 'Save Changes' : 'Publish Product' }}</span>
                    </button>
                </div>
            </div>

            {{-- Form Error Summary --}}
            @if($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50/80 p-4 text-xs font-medium text-rose-800 shadow-2xs">
                    <div class="font-bold flex items-center gap-1.5 text-rose-900 mb-1">
                        <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Please fix the following validation issues:</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Main 2-Column Shopify Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left Column (2/3 width on desktop) --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- 1. BASIC INFORMATION --}}
                    <div class="rounded-2xl border border-stone-200/90 bg-white p-5 sm:p-6 shadow-2xs space-y-4">
                        <div class="border-b border-stone-100 pb-3">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-800">Basic Information</h2>
                            <p class="text-xs text-stone-400 mt-0.5">Product title, description, and primary copywriting specifications.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-stone-800 mb-1">Title <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                name="name"
                                id="product-name-input"
                                value="{{ old('name', $product->name) }}"
                                placeholder="e.g. Morning Tea of Gut Reset"
                                required
                                class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3.5 py-2 text-sm text-stone-900 placeholder-stone-400 focus:bg-white focus:border-[#204b34] focus:outline-none focus:ring-2 focus:ring-[#204b34]/15 transition"
                            >
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">Base SKU <span class="text-rose-500">*</span></label>
                                <input
                                    type="text"
                                    name="base_sku"
                                    value="{{ old('base_sku', $product->base_sku) }}"
                                    placeholder="e.g. GR-TEA-001"
                                    required
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs font-mono text-stone-900 placeholder-stone-400 focus:bg-white focus:border-[#204b34] focus:outline-none focus:ring-2 focus:ring-[#204b34]/15 transition"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">URL Slug / Handle</label>
                                <input
                                    type="text"
                                    name="slug"
                                    id="product-slug-input"
                                    value="{{ old('slug', $product->slug) }}"
                                    placeholder="auto-generated-from-title"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs font-mono text-stone-900 placeholder-stone-400 focus:bg-white focus:border-[#204b34] focus:outline-none focus:ring-2 focus:ring-[#204b34]/15 transition"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-stone-800 mb-1">Short Description / Subtitle</label>
                            <input
                                type="text"
                                name="short_description"
                                value="{{ old('short_description', $product->short_description) }}"
                                placeholder="One-line summary for shop cards and search snippets"
                                class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3.5 py-2 text-xs text-stone-900 placeholder-stone-400 focus:bg-white focus:border-[#204b34] focus:outline-none focus:ring-2 focus:ring-[#204b34]/15 transition"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-stone-800 mb-1">Full Description</label>
                            <textarea
                                name="description"
                                rows="6"
                                placeholder="Detailed overview of product, benefits, and instructions..."
                                class="w-full rounded-xl border border-stone-200 bg-stone-50/40 p-3.5 text-xs text-stone-900 placeholder-stone-400 focus:bg-white focus:border-[#204b34] focus:outline-none focus:ring-2 focus:ring-[#204b34]/15 transition leading-relaxed"
                            >{{ old('description', $product->description) }}</textarea>
                        </div>

                        {{-- Collapsible Formulation & Medical Specs --}}
                        <details class="group rounded-xl border border-stone-100 bg-stone-50/50 p-3">
                            <summary class="flex cursor-pointer items-center justify-between text-xs font-bold text-stone-700 select-none">
                                <span>Formulation, Ingredients & Usage Guidelines</span>
                                <svg class="h-4 w-4 text-stone-400 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </summary>
                            <div class="mt-4 space-y-3 pt-3 border-t border-stone-200/60">
                                <div>
                                    <label class="block text-xs font-semibold text-stone-800 mb-1">Active Ingredients</label>
                                    <textarea name="ingredients" rows="3" placeholder="Herbal extracts, botanicals, active ingredients..." class="w-full rounded-xl border border-stone-200 bg-white p-2.5 text-xs text-stone-900 placeholder-stone-400 focus:outline-none focus:border-[#204b34]">{{ old('ingredients', $product->ingredients) }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-stone-800 mb-1">Key Health Benefits</label>
                                    <textarea name="benefits" rows="3" placeholder="Gut barrier repair, motility support, reduced bloating..." class="w-full rounded-xl border border-stone-200 bg-white p-2.5 text-xs text-stone-900 placeholder-stone-400 focus:outline-none focus:border-[#204b34]">{{ old('benefits', $product->benefits) }}</textarea>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-stone-800 mb-1">Usage & Dosage Instructions</label>
                                        <textarea name="usage_instructions" rows="2" placeholder="Steep 1 bag in 200ml boiling water..." class="w-full rounded-xl border border-stone-200 bg-white p-2.5 text-xs text-stone-900 placeholder-stone-400 focus:outline-none focus:border-[#204b34]">{{ old('usage_instructions', $product->usage_instructions) }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-stone-800 mb-1">Warnings & Contraindications</label>
                                        <textarea name="warnings" rows="2" placeholder="Not recommended for pregnant women..." class="w-full rounded-xl border border-stone-200 bg-white p-2.5 text-xs text-stone-900 placeholder-stone-400 focus:outline-none focus:border-[#204b34]">{{ old('warnings', $product->warnings) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </details>
                    </div>

                    {{-- 2. MEDIA / IMAGES MANAGEMENT --}}
                    <div class="rounded-2xl border border-stone-200/90 bg-white p-5 sm:p-6 shadow-2xs space-y-4">
                        <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                            <div>
                                <h2 class="text-sm font-bold uppercase tracking-wider text-stone-800">Product Media & Gallery</h2>
                                <p class="text-xs text-stone-400 mt-0.5">Upload product photos. First image is featured on cards and checkout.</p>
                            </div>
                            <span class="text-[11px] font-semibold text-stone-400">JPG, PNG, WebP up to 5MB</span>
                        </div>

                        {{-- Existing Images (in edit mode) --}}
                        @if($isEdit && $product->productImages->count() > 0)
                            <div>
                                <label class="block text-xs font-semibold text-stone-700 mb-2">Current Media Library ({{ $product->productImages->count() }})</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="existing-images-grid">
                                    @foreach($product->productImages as $img)
                                        @if($img->media)
                                            <div class="relative group rounded-xl border {{ $img->is_primary ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-stone-200' }} bg-stone-50 overflow-hidden shadow-2xs aspect-square">
                                                <img src="{{ $img->media->url }}" alt="{{ $img->alt_text }}" class="h-full w-full object-cover">

                                                {{-- Primary Badge --}}
                                                @if($img->is_primary)
                                                    <span class="absolute top-1.5 left-1.5 rounded-md bg-[#204b34] px-1.5 py-0.5 text-[9.5px] font-bold text-white shadow-xs">
                                                        Primary
                                                    </span>
                                                @endif

                                                {{-- Overlay actions on hover --}}
                                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-1.5 p-2">
                                                    @if(!$img->is_primary)
                                                        <button
                                                            type="button"
                                                            onclick="makeImagePrimary({{ $product->id }}, {{ $img->id }})"
                                                            class="w-full rounded-md bg-white/90 hover:bg-white px-2 py-1 text-[10.5px] font-semibold text-stone-900 shadow-xs transition"
                                                        >
                                                            Make Primary
                                                        </button>
                                                    @endif
                                                    <button
                                                        type="button"
                                                        onclick="deleteProductImage({{ $product->id }}, {{ $img->id }})"
                                                        class="w-full rounded-md bg-rose-600/90 hover:bg-rose-600 px-2 py-1 text-[10.5px] font-semibold text-white shadow-xs transition"
                                                    >
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Drag & Drop Upload Zone --}}
                        <div>
                            <label class="block text-xs font-semibold text-stone-800 mb-1.5">Add Images</label>
                            <div
                                id="drop-zone"
                                onclick="document.getElementById('file-upload-input').click()"
                                class="cursor-pointer rounded-2xl border-2 border-dashed border-stone-200 hover:border-[#204b34] bg-stone-50/50 hover:bg-stone-50/80 p-8 text-center transition-all group"
                            >
                                <input
                                    type="file"
                                    id="file-upload-input"
                                    name="gallery_images[]"
                                    multiple
                                    accept="image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    onchange="handleFilesSelected(this.files)"
                                >
                                <div class="mx-auto h-12 w-12 rounded-2xl bg-white border border-stone-200 flex items-center justify-center text-stone-400 group-hover:text-[#204b34] group-hover:border-[#204b34]/30 shadow-2xs transition mb-3">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="text-xs font-semibold text-stone-800">
                                    <span class="text-emerald-700 underline underline-offset-2">Click to upload</span> or drag and drop photos
                                </div>
                                <p class="text-[11px] text-stone-400 mt-1">Select one or multiple images (PNG, JPG, WebP up to 5MB each)</p>
                            </div>

                            {{-- Live Previews of Newly Selected Files --}}
                            <div id="new-previews-container" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-3 hidden"></div>
                        </div>
                    </div>

                    {{-- 3. PRICING & PROFIT MARGIN ENGINE --}}
                    @php
                        $variant = $product->variants->first();
                        $priceObj = $variant?->currentPrice ?? $variant?->prices->first();
                        $initSelling = old('selling_price', $priceObj?->selling_price ?? '');
                        $initMrp = old('mrp', $priceObj?->mrp ?? '');
                        $initCost = old('cost_price', $product->cost_price ?? '');
                    @endphp
                    <div class="rounded-2xl border border-stone-200/90 bg-white p-5 sm:p-6 shadow-2xs space-y-4">
                        <div class="border-b border-stone-100 pb-3">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-800">Pricing & Economics</h2>
                            <p class="text-xs text-stone-400 mt-0.5">Define selling price, MRP, cost of goods, and live profit margins.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">Selling Price (₹) <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-stone-400 font-mono text-xs">₹</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        name="selling_price"
                                        id="input-selling-price"
                                        value="{{ $initSelling }}"
                                        placeholder="0.00"
                                        required
                                        oninput="recalculateMargins()"
                                        class="w-full rounded-xl border border-stone-200 bg-stone-50/40 pl-7 pr-3 py-2 text-xs font-mono font-bold text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                    >
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">Compare-at Price / MRP (₹)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-stone-400 font-mono text-xs">₹</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        name="mrp"
                                        id="input-mrp"
                                        value="{{ $initMrp }}"
                                        placeholder="0.00"
                                        oninput="recalculateMargins()"
                                        class="w-full rounded-xl border border-stone-200 bg-stone-50/40 pl-7 pr-3 py-2 text-xs font-mono text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                    >
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">Cost per item (₹)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-stone-400 font-mono text-xs">₹</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        name="cost_price"
                                        id="input-cost-price"
                                        value="{{ $initCost }}"
                                        placeholder="0.00"
                                        oninput="recalculateMargins()"
                                        class="w-full rounded-xl border border-stone-200 bg-stone-50/40 pl-7 pr-3 py-2 text-xs font-mono text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                    >
                                </div>
                                <span class="text-[10.5px] text-stone-400 block mt-1">Customers won't see this</span>
                            </div>
                        </div>

                        {{-- Live Profit & Margin Indicator Box (Shopify Style) --}}
                        <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-3.5 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-4">
                                <div>
                                    <span class="text-stone-500 font-medium block text-[11px]">Profit per unit:</span>
                                    <span class="font-bold font-mono text-emerald-900 text-sm" id="calc-profit-display">₹0.00</span>
                                </div>
                                <div class="h-6 w-px bg-emerald-200/80"></div>
                                <div>
                                    <span class="text-stone-500 font-medium block text-[11px]">Gross Margin:</span>
                                    <span class="font-bold font-mono text-emerald-900 text-sm" id="calc-margin-display">0.0%</span>
                                </div>
                            </div>
                            <div>
                                <span class="text-stone-500 font-medium block text-[11px] text-right">Discount from MRP:</span>
                                <span class="font-bold font-mono text-stone-700 text-sm" id="calc-discount-display">0.0% off</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">Tax Percentage (%)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="tax_percentage"
                                    value="{{ old('tax_percentage', $priceObj?->tax_percentage ?? 0) }}"
                                    placeholder="0.00"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs font-mono text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">Customer Group</label>
                                <input
                                    type="text"
                                    name="customer_group"
                                    value="{{ old('customer_group', $priceObj?->customer_group ?? 'default') }}"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                >
                            </div>
                        </div>
                    </div>

                    {{-- 4. INVENTORY & FULFILMENT --}}
                    @php
                        $initStock = $variant ? $variant->inventory->sum('quantity_on_hand') : 0;
                    @endphp
                    <div class="rounded-2xl border border-stone-200/90 bg-white p-5 sm:p-6 shadow-2xs space-y-4">
                        <div class="border-b border-stone-100 pb-3">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-800">Inventory & Stock Tracking</h2>
                            <p class="text-xs text-stone-400 mt-0.5">Control SKU codes, barcodes, track quantities, and low stock warnings.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">Variant SKU</label>
                                <input
                                    type="text"
                                    name="variant_sku"
                                    value="{{ old('variant_sku', $variant?->sku ?? $product->base_sku) }}"
                                    placeholder="Variant SKU"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs font-mono text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">Barcode (ISBN, UPC, GTIN)</label>
                                <input
                                    type="text"
                                    name="barcode"
                                    value="{{ old('barcode', $variant?->barcode) }}"
                                    placeholder="e.g. 890123456789"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs font-mono text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                >
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <input
                                type="checkbox"
                                name="track_quantity"
                                id="track-quantity-checkbox"
                                value="1"
                                @checked(old('track_quantity', $product->track_quantity ?? true))
                                class="rounded border-stone-300 text-[#204b34] focus:ring-[#204b34]/20"
                            >
                            <label for="track-quantity-checkbox" class="text-xs font-semibold text-stone-800 cursor-pointer">
                                Track quantity in inventory
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">
                                    {{ $isEdit ? 'Current Stock on Hand' : 'Opening Stock' }}
                                </label>
                                <input
                                    type="number"
                                    name="quantity_on_hand"
                                    value="{{ old('quantity_on_hand', $initStock) }}"
                                    min="0"
                                    {{ $isEdit ? 'readonly title=Use-Adjust-Inventory-in-Details-tab' : '' }}
                                    class="w-full rounded-xl border border-stone-200 {{ $isEdit ? 'bg-stone-100/80 cursor-not-allowed' : 'bg-stone-50/40' }} px-3 py-2 text-xs font-mono font-bold text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                >
                                @if($isEdit)
                                    <span class="text-[10px] text-stone-400 mt-1 block">Adjust stock in Product Details</span>
                                @endif
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">Low Stock Threshold</label>
                                <input
                                    type="number"
                                    name="low_stock_threshold"
                                    value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}"
                                    min="0"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs font-mono text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                >
                                <span class="text-[10px] text-stone-400 mt-1 block">Triggers low stock badge</span>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">Warehouse Location</label>
                                <input
                                    type="text"
                                    name="warehouse_code"
                                    value="{{ old('warehouse_code', 'DEFAULT') }}"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs font-mono text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                >
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <input
                                type="checkbox"
                                name="continue_selling_out_of_stock"
                                id="continue-selling-checkbox"
                                value="1"
                                @checked(old('continue_selling_out_of_stock', $product->continue_selling_out_of_stock ?? false))
                                class="rounded border-stone-300 text-[#204b34] focus:ring-[#204b34]/20"
                            >
                            <label for="continue-selling-checkbox" class="text-xs font-medium text-stone-700 cursor-pointer">
                                Continue selling when out of stock (allow backorders)
                            </label>
                        </div>
                    </div>

                    {{-- 5. SHIPPING SPECIFICATIONS --}}
                    <div class="rounded-2xl border border-stone-200/90 bg-white p-5 sm:p-6 shadow-2xs space-y-4">
                        <div class="border-b border-stone-100 pb-3">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-800">Shipping & Dimensions</h2>
                            <p class="text-xs text-stone-400 mt-0.5">Physical specs for parcel calculations, carrier rates, and customs.</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                name="requires_shipping"
                                id="requires-shipping-checkbox"
                                value="1"
                                @checked(old('requires_shipping', $product->requires_shipping ?? true))
                                class="rounded border-stone-300 text-[#204b34] focus:ring-[#204b34]/20"
                            >
                            <label for="requires-shipping-checkbox" class="text-xs font-semibold text-stone-800 cursor-pointer">
                                This is a physical product that requires shipping
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">Weight (in Grams)</label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        step="0.01"
                                        name="weight_grams"
                                        value="{{ old('weight_grams', $variant?->weight_grams ?? 0) }}"
                                        placeholder="0.00"
                                        class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs font-mono text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                    >
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 text-xs">g</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-stone-800 mb-1">Harmonized System (HS) Code</label>
                                <input
                                    type="text"
                                    name="hs_code"
                                    value="{{ old('hs_code', $product->hs_code) }}"
                                    placeholder="e.g. 0902.10.00"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs font-mono text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                >
                            </div>
                        </div>

                        {{-- Dimensions --}}
                        <div class="pt-1">
                            <label class="block text-xs font-semibold text-stone-800 mb-1.5">Package Dimensions (cm)</label>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <input
                                        type="number"
                                        step="0.1"
                                        name="length_cm"
                                        value="{{ old('length_cm', $variant?->length_cm) }}"
                                        placeholder="Length (cm)"
                                        class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs font-mono text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                    >
                                </div>
                                <div>
                                    <input
                                        type="number"
                                        step="0.1"
                                        name="width_cm"
                                        value="{{ old('width_cm', $variant?->width_cm) }}"
                                        placeholder="Width (cm)"
                                        class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs font-mono text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                    >
                                </div>
                                <div>
                                    <input
                                        type="number"
                                        step="0.1"
                                        name="height_cm"
                                        value="{{ old('height_cm', $variant?->height_cm) }}"
                                        placeholder="Height (cm)"
                                        class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs font-mono text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right Column (1/3 width on desktop - Sidebar) --}}
                <div class="space-y-6">

                    {{-- 1. STATUS & VISIBILITY --}}
                    <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs space-y-3.5">
                        <div class="border-b border-stone-100 pb-2.5">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-stone-800">Status & Publishing</h2>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Status</label>
                            <select
                                name="status"
                                class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-3 py-2 text-xs font-semibold text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition cursor-pointer"
                            >
                                <option value="active" @selected(old('status', $product->status ?? 'active') === 'active')>Active (Published)</option>
                                <option value="draft" @selected(old('status', $product->status) === 'draft')>Draft (Hidden)</option>
                                <option value="archived" @selected(old('status', $product->status) === 'archived')>Archived (Discontinued)</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <input
                                type="checkbox"
                                name="is_featured"
                                id="is-featured-checkbox"
                                value="1"
                                @checked(old('is_featured', $product->is_featured ?? false))
                                class="rounded border-stone-300 text-[#204b34] focus:ring-[#204b34]/20"
                            >
                            <label for="is-featured-checkbox" class="text-xs font-semibold text-stone-800 cursor-pointer">
                                ★ Featured Product
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Catalog Sort Priority</label>
                            <input
                                type="number"
                                name="sort_order"
                                value="{{ old('sort_order', $product->sort_order ?? 0) }}"
                                class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-1.5 text-xs font-mono text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                            >
                            <span class="text-[10px] text-stone-400 mt-1 block">Lower numbers appear first</span>
                        </div>
                    </div>

                    {{-- 2. ORGANIZATION --}}
                    <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs space-y-3.5">
                        <div class="border-b border-stone-100 pb-2.5">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-stone-800">Organization</h2>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Category</label>
                            <select
                                name="category_id"
                                class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-3 py-2 text-xs text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition cursor-pointer"
                            >
                                <option value="">None / Uncategorized</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Brand / Vendor</label>
                            <input
                                type="text"
                                name="brand"
                                value="{{ old('brand', $product->brand ?? 'Guided Wellness') }}"
                                placeholder="e.g. Guided Wellness"
                                class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Product Type</label>
                            <input
                                type="text"
                                name="product_type"
                                value="{{ old('product_type', $product->product_type) }}"
                                placeholder="e.g. Tea, Supplement, Kit"
                                class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Tags (comma-separated)</label>
                            <input
                                type="text"
                                name="tags"
                                value="{{ old('tags', $product->tags) }}"
                                placeholder="gut, tea, detox, organic"
                                class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-2 text-xs text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                            >
                        </div>
                    </div>

                    {{-- 3. SEARCH ENGINE OPTIMIZATION (SEO) --}}
                    <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs space-y-3.5">
                        <div class="border-b border-stone-100 pb-2.5">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-stone-800">Search Engine Listing (SEO)</h2>
                            <p class="text-[11px] text-stone-400 mt-0.5">Preview of how this product appears in Google search results.</p>
                        </div>

                        {{-- SERP Snippet Preview --}}
                        <div class="rounded-xl border border-stone-100 bg-stone-50/60 p-3 text-left select-none">
                            <div class="text-[10px] text-stone-400 font-mono truncate" id="seo-preview-url">
                                https://gutreset.com/product/<span id="seo-preview-slug-text">{{ $product->slug ?: 'product-handle' }}</span>
                            </div>
                            <div class="text-xs font-semibold text-blue-700 hover:underline truncate mt-0.5" id="seo-preview-title">
                                {{ $product->seo_title ?: ($product->name ?: 'Product Title Preview') }}
                            </div>
                            <div class="text-[11px] text-stone-600 line-clamp-2 mt-1 leading-snug" id="seo-preview-desc">
                                {{ $product->seo_description ?: ($product->short_description ?: 'Product description will appear here in search engine rankings...') }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Page Title</label>
                            <input
                                type="text"
                                name="seo_title"
                                id="seo-title-input"
                                value="{{ old('seo_title', $product->seo_title) }}"
                                placeholder="SEO Title"
                                oninput="updateSeoPreview()"
                                class="w-full rounded-xl border border-stone-200 bg-stone-50/40 px-3 py-1.5 text-xs text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-stone-700 mb-1">Meta Description</label>
                            <textarea
                                name="seo_description"
                                id="seo-desc-input"
                                rows="3"
                                placeholder="SEO Meta Description (max 160 characters recommended)"
                                oninput="updateSeoPreview()"
                                class="w-full rounded-xl border border-stone-200 bg-stone-50/40 p-2.5 text-xs text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                            >{{ old('seo_description', $product->seo_description) }}</textarea>
                        </div>
                    </div>

                    {{-- 4. INTERNAL ADMIN NOTES --}}
                    <div class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs space-y-3">
                        <div class="border-b border-stone-100 pb-2.5">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-stone-800">Internal Staff Notes</h2>
                            <p class="text-[11px] text-stone-400 mt-0.5">Private notes, supplier references, or special manufacturing details.</p>
                        </div>
                        <textarea
                            name="internal_notes"
                            rows="4"
                            placeholder="Add private staff notes..."
                            class="w-full rounded-xl border border-stone-200 bg-stone-50/40 p-2.5 text-xs text-stone-900 focus:bg-white focus:border-[#204b34] focus:outline-none transition"
                        >{{ old('internal_notes', $product->internal_notes) }}</textarea>
                    </div>

                </div>
            </div>

            {{-- Sticky Bottom Save Bar on Mobile/Desktop --}}
            <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-stone-200 bg-white/95 backdrop-blur-md py-3 px-4 sm:px-8 shadow-lg">
                <div class="mx-auto max-w-[1400px] flex items-center justify-between">
                    <span class="text-xs text-stone-500 hidden sm:inline">
                        {{ $isEdit ? "Unsaved changes for {$product->name}" : 'New product in draft mode' }}
                    </span>
                    <div class="flex items-center gap-3 ml-auto">
                        <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition">
                            Discard
                        </a>
                        <button
                            type="submit"
                            class="rounded-xl bg-[#204b34] px-6 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#163826] transition active:scale-98"
                        >
                            {{ $isEdit ? 'Save Changes' : 'Save & Publish Product' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

{{-- Interactive Client-side Logic: Margins, SEO Preview, Image uploads --}}
<script>
    // Live Margin & Profit Calculator
    function recalculateMargins() {
        const selling = parseFloat(document.getElementById('input-selling-price')?.value) || 0;
        const mrp = parseFloat(document.getElementById('input-mrp')?.value) || 0;
        const cost = parseFloat(document.getElementById('input-cost-price')?.value) || 0;

        const profitDisplay = document.getElementById('calc-profit-display');
        const marginDisplay = document.getElementById('calc-margin-display');
        const discountDisplay = document.getElementById('calc-discount-display');

        const profit = Math.max(0, selling - cost);
        const margin = selling > 0 ? ((profit / selling) * 100).toFixed(1) : 0;
        const discount = (mrp > selling && mrp > 0) ? (((mrp - selling) / mrp) * 100).toFixed(1) : 0;

        if (profitDisplay) profitDisplay.textContent = '₹' + profit.toFixed(2);
        if (marginDisplay) marginDisplay.textContent = margin + '%';
        if (discountDisplay) discountDisplay.textContent = discount + '% off';
    }

    // Auto-generate Slug from Name if not already set
    document.getElementById('product-name-input')?.addEventListener('input', function() {
        const slugInput = document.getElementById('product-slug-input');
        if (!slugInput.dataset.manuallyEdited) {
            const slug = this.value.toLowerCase().trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
            document.getElementById('seo-preview-slug-text').textContent = slug || 'product-handle';
        }
        updateSeoPreview();
    });

    document.getElementById('product-slug-input')?.addEventListener('input', function() {
        this.dataset.manuallyEdited = 'true';
        document.getElementById('seo-preview-slug-text').textContent = this.value || 'product-handle';
    });

    // Update SEO SERP Preview
    function updateSeoPreview() {
        const titleInput = document.getElementById('seo-title-input');
        const nameInput = document.getElementById('product-name-input');
        const descInput = document.getElementById('seo-desc-input');

        const titleText = (titleInput?.value || nameInput?.value || 'Product Title Preview');
        const descText = (descInput?.value || 'Product description will appear here in search engine rankings...');

        const previewTitle = document.getElementById('seo-preview-title');
        const previewDesc = document.getElementById('seo-preview-desc');

        if (previewTitle) previewTitle.textContent = titleText;
        if (previewDesc) previewDesc.textContent = descText;
    }

    // Live Client-side Image Upload Previews
    function handleFilesSelected(files) {
        const container = document.getElementById('new-previews-container');
        if (!container) return;

        container.innerHTML = '';
        if (files.length === 0) {
            container.classList.add('hidden');
            return;
        }

        container.classList.remove('hidden');

        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const card = document.createElement('div');
                card.className = 'relative rounded-xl border border-stone-200 bg-stone-50 overflow-hidden shadow-2xs aspect-square';
                card.innerHTML = `
                    <img src="${e.target.result}" class="h-full w-full object-cover">
                    <span class="absolute top-1 left-1 rounded bg-stone-900/80 px-1 py-0.5 text-[9px] font-bold text-white">
                        New ${index + 1}
                    </span>
                `;
                container.appendChild(card);
            };
            reader.readAsDataURL(file);
        });
    }

    // Delete existing product image (in edit mode)
    function deleteProductImage(productId, imageId) {
        if (!confirm('Are you sure you want to delete this product photo?')) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/products/${productId}/images/${imageId}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    }

    // Make image primary
    function makeImagePrimary(productId, imageId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/products/${productId}/images/make-primary`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="image_id" value="${imageId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }

    // Run initial calculations on page load
    document.addEventListener('DOMContentLoaded', () => {
        recalculateMargins();
        updateSeoPreview();
    });
</script>
@endsection
