@extends('layouts.admin', ['title' => 'Create Promotion | Gut Reset Admin'])

@section('content')
<div class="min-h-screen bg-stone-50/60 pb-20">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 pt-6">

        {{-- Top Navigation Breadcrumb & Header --}}
        <div class="flex items-center justify-between pb-6 border-b border-stone-200">
            <div>
                <nav class="flex items-center gap-2 text-xs text-stone-500 mb-1">
                    <a href="{{ route('admin.coupons.index') }}" class="hover:text-stone-900 transition">Coupons & Promotions</a>
                    <span>/</span>
                    <span class="text-stone-800 font-semibold">New Coupon</span>
                </nav>
                <h1 class="text-2xl font-serif font-bold text-stone-900 tracking-tight">Create Coupon & Promotion</h1>
                <p class="text-xs text-stone-500 mt-0.5">Configure promotional incentives, eligibility constraints, stacking permissions, and scheduling.</p>
            </div>

            <div class="flex items-center gap-2.5">
                <a
                    href="{{ route('admin.coupons.index') }}"
                    class="rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    form="create-coupon-form"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-[#1c3d2c] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#142e21] transition hover:shadow-md"
                >
                    <svg class="h-3.5 w-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Save & Publish</span>
                </button>
            </div>
        </div>

        {{-- Validation Errors Notification --}}
        @if($errors->any())
            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-xs text-rose-800">
                <div class="font-bold flex items-center gap-2 mb-1">
                    <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Please correct the following errors:
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Start --}}
        <form method="POST" action="{{ route('admin.coupons.store') }}" id="create-coupon-form" class="mt-6 space-y-8">
            @csrf

            {{-- 1. BASIC INFORMATION --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-6 shadow-2xs space-y-5">
                <div class="border-b border-stone-100 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-stone-900 tracking-tight flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">1</span>
                            Basic Information
                        </h2>
                        <p class="text-[11px] text-stone-400 mt-0.5">Primary identification and customer-facing promotional title.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Coupon Code with Generator --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-stone-700">Coupon Code <span class="text-rose-500">*</span></label>
                            <button
                                type="button"
                                onclick="generateRandomCode()"
                                class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-900 hover:underline"
                            >
                                ⚡ Generate Random
                            </button>
                        </div>
                        <input
                            type="text"
                            name="code"
                            id="coupon-code-input"
                            value="{{ old('code') }}"
                            required
                            placeholder="e.g. RESETSUMMER20"
                            class="w-full h-10 rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 text-xs font-mono font-bold tracking-wider text-stone-900 uppercase focus:border-[#1c3d2c] focus:bg-white focus:outline-none"
                        >
                        <p class="text-[10.5px] text-stone-400 mt-1">Customers enter this code at checkout. Will be capitalized automatically.</p>
                    </div>

                    {{-- Promotion Name --}}
                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Promotion Name <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            placeholder="e.g. 20% Off 30-Day Gut Protocol"
                            class="w-full h-10 rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 text-xs text-stone-900 focus:border-[#1c3d2c] focus:bg-white focus:outline-none"
                        >
                        <p class="text-[10.5px] text-stone-400 mt-1">Displayed in order receipts and admin transaction records.</p>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Initial Status <span class="text-rose-500">*</span></label>
                        <select name="status" class="w-full h-10 rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 text-xs text-stone-900 focus:border-[#1c3d2c] focus:bg-white focus:outline-none">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active (Publish Immediately)</option>
                            <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Scheduled (Wait for Start Date)</option>
                            <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>Paused (Temporarily Deactivated)</option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Internal Staging)</option>
                        </select>
                    </div>

                    {{-- Campaign Tag --}}
                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Campaign Tag / Objective</label>
                        <input
                            type="text"
                            name="campaign_name"
                            value="{{ old('campaign_name') }}"
                            placeholder="e.g. Q4 Instagram Influencer Collab"
                            class="w-full h-10 rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 text-xs text-stone-900 focus:border-[#1c3d2c] focus:bg-white focus:outline-none"
                        >
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Description / Customer Terms</label>
                        <textarea
                            name="description"
                            rows="2"
                            placeholder="Briefly describe the promotional terms or context..."
                            class="w-full rounded-xl border border-stone-200 bg-stone-50/50 p-3 text-xs text-stone-900 focus:border-[#1c3d2c] focus:bg-white focus:outline-none"
                        >{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- 2. DISCOUNT RULES --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-6 shadow-2xs space-y-5">
                <div class="border-b border-stone-100 pb-3">
                    <h2 class="text-sm font-bold text-stone-900 tracking-tight flex items-center gap-2">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">2</span>
                        Discount Rules
                    </h2>
                    <p class="text-[11px] text-stone-400 mt-0.5">Determine how discount deductions are calculated at checkout.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Discount Type --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Discount Type <span class="text-rose-500">*</span></label>
                        <select
                            name="discount_type"
                            id="discount-type-select"
                            onchange="updateDiscountTypeUI()"
                            class="w-full h-10 rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 text-xs text-stone-900 focus:border-[#1c3d2c] focus:bg-white focus:outline-none font-medium"
                        >
                            <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>Percentage Discount (%)</option>
                            <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Cart Amount (₹)</option>
                            <option value="free_shipping" {{ old('discount_type') === 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
                            <option value="bogo" {{ old('discount_type') === 'bogo' ? 'selected' : '' }}>Buy One Get One (BOGO)</option>
                            <option value="bundle" {{ old('discount_type') === 'bundle' ? 'selected' : '' }}>Bundle Savings Offer</option>
                            <option value="first_order" {{ old('discount_type') === 'first_order' ? 'selected' : '' }}>First Order Incentive</option>
                            <option value="referral" {{ old('discount_type') === 'referral' ? 'selected' : '' }}>Referral Benefit</option>
                        </select>
                    </div>

                    {{-- Discount Value --}}
                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1" id="discount-val-label">Discount Value <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-stone-400" id="discount-val-prefix">%</span>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="discount_value"
                                id="discount-value-input"
                                value="{{ old('discount_value', 10) }}"
                                required
                                class="w-full h-10 rounded-xl border border-stone-200 bg-stone-50/50 pl-8 pr-3.5 text-xs font-mono font-bold text-stone-900 focus:border-[#1c3d2c] focus:bg-white focus:outline-none"
                            >
                        </div>
                    </div>

                    {{-- Max Discount Cap (for percentage) --}}
                    <div id="max-discount-wrapper">
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Max Discount Cap (₹)</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="maximum_discount"
                            value="{{ old('maximum_discount') }}"
                            placeholder="Optional cap (e.g. 1000)"
                            class="w-full h-10 rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 text-xs font-mono text-stone-900 focus:border-[#1c3d2c] focus:bg-white focus:outline-none"
                        >
                    </div>

                    {{-- Minimum Order / Cart Spend --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Minimum Order Value (₹)</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-stone-400">₹</span>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="minimum_cart_value"
                                value="{{ old('minimum_cart_value', 0) }}"
                                placeholder="0 = no minimum required"
                                class="w-full h-10 rounded-xl border border-stone-200 bg-stone-50/50 pl-8 pr-3.5 text-xs font-mono text-stone-900 focus:border-[#1c3d2c] focus:bg-white focus:outline-none"
                            >
                        </div>
                        <p class="text-[10.5px] text-stone-400 mt-1">Applies only when customer cart subtotal meets or exceeds this threshold.</p>
                    </div>
                </div>
            </div>

            {{-- 3. USAGE LIMITS --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-6 shadow-2xs space-y-5">
                <div class="border-b border-stone-100 pb-3">
                    <h2 class="text-sm font-bold text-stone-900 tracking-tight flex items-center gap-2">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">3</span>
                        Usage Limits
                    </h2>
                    <p class="text-[11px] text-stone-400 mt-0.5">Protect promotion budget and prevent code abuse.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Total Usage Limit</label>
                        <input
                            type="number"
                            min="1"
                            name="usage_limit"
                            value="{{ old('usage_limit') }}"
                            placeholder="Unlimited if left empty"
                            class="w-full h-10 rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 text-xs font-mono text-stone-900 focus:border-[#1c3d2c] focus:bg-white focus:outline-none"
                        >
                        <p class="text-[10.5px] text-stone-400 mt-1">Coupon automatically expires once overall redemptions hit this ceiling.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Limit Per Customer</label>
                        <input
                            type="number"
                            min="1"
                            name="per_customer_limit"
                            value="{{ old('per_customer_limit', 1) }}"
                            placeholder="e.g. 1"
                            class="w-full h-10 rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 text-xs font-mono text-stone-900 focus:border-[#1c3d2c] focus:bg-white focus:outline-none"
                        >
                        <p class="text-[10.5px] text-stone-400 mt-1">Maximum times an individual registered customer or email can redeem.</p>
                    </div>
                </div>
            </div>

            {{-- 4 & 5. ELIGIBLE PRODUCTS & CATEGORIES --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Eligible Products --}}
                <div class="rounded-2xl border border-stone-200/90 bg-white p-6 shadow-2xs space-y-4">
                    <div class="border-b border-stone-100 pb-3">
                        <h2 class="text-sm font-bold text-stone-900 tracking-tight flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">4</span>
                            Eligible Products
                        </h2>
                        <p class="text-[11px] text-stone-400 mt-0.5">Restrict discount to specific catalog items or apply to all.</p>
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-center gap-2 text-xs font-medium text-stone-700 cursor-pointer">
                            <input type="radio" name="product_scope" value="all" checked onchange="toggleProductList(false)" class="text-[#1c3d2c] focus:ring-emerald-500">
                            <span>All Products in Store</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs font-medium text-stone-700 cursor-pointer">
                            <input type="radio" name="product_scope" value="specific" onchange="toggleProductList(true)" class="text-[#1c3d2c] focus:ring-emerald-500">
                            <span>Specific Selected Products</span>
                        </label>

                        <div id="product-picker-container" class="hidden mt-3 max-h-48 overflow-y-auto space-y-1.5 p-3 rounded-xl border border-stone-200 bg-stone-50/60">
                            @foreach($products as $prod)
                                <label class="flex items-center gap-2 text-xs text-stone-800 cursor-pointer hover:bg-white p-1 rounded-lg transition">
                                    <input type="checkbox" name="product_ids[]" value="{{ $prod->id }}" class="rounded text-[#1c3d2c] focus:ring-emerald-500">
                                    <span class="truncate">{{ $prod->name }} <span class="text-stone-400 font-mono text-[10px]">({{ $prod->base_sku }})</span></span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Eligible Categories --}}
                <div class="rounded-2xl border border-stone-200/90 bg-white p-6 shadow-2xs space-y-4">
                    <div class="border-b border-stone-100 pb-3">
                        <h2 class="text-sm font-bold text-stone-900 tracking-tight flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">5</span>
                            Eligible Categories
                        </h2>
                        <p class="text-[11px] text-stone-400 mt-0.5">Apply promotion across targeted product collections.</p>
                    </div>

                    <div class="space-y-3">
                        <p class="text-xs text-stone-500">Select categories that qualify for this coupon:</p>
                        <div class="space-y-2 p-3 rounded-xl border border-stone-200 bg-stone-50/60">
                            @forelse($categories as $cat)
                                <label class="flex items-center gap-2 text-xs text-stone-800 cursor-pointer hover:bg-white p-1 rounded-lg transition">
                                    <input type="checkbox" name="eligible_categories[]" value="{{ $cat->id }}" class="rounded text-[#1c3d2c] focus:ring-emerald-500">
                                    <span>{{ $cat->name }}</span>
                                </label>
                            @empty
                                <span class="text-xs text-stone-400">All categories automatically qualify.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

            {{-- 6. ELIGIBLE CUSTOMERS --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-6 shadow-2xs space-y-5">
                <div class="border-b border-stone-100 pb-3">
                    <h2 class="text-sm font-bold text-stone-900 tracking-tight flex items-center gap-2">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">6</span>
                        Customer Eligibility
                    </h2>
                    <p class="text-[11px] text-stone-400 mt-0.5">Define target audience cohorts and segment qualifications.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="rounded-xl border border-stone-200 p-3.5 hover:border-emerald-700 cursor-pointer flex flex-col justify-between transition bg-stone-50/50">
                        <div class="flex items-center gap-2">
                            <input type="radio" name="customer_eligibility" value="all" checked class="text-[#1c3d2c] focus:ring-emerald-500">
                            <span class="text-xs font-bold text-stone-900">All Customers</span>
                        </div>
                        <span class="text-[11px] text-stone-400 mt-2">Open to every shopper, guest, or registered account.</span>
                    </label>

                    <label class="rounded-xl border border-stone-200 p-3.5 hover:border-emerald-700 cursor-pointer flex flex-col justify-between transition bg-stone-50/50">
                        <div class="flex items-center gap-2">
                            <input type="radio" name="customer_eligibility" value="new" class="text-[#1c3d2c] focus:ring-emerald-500">
                            <span class="text-xs font-bold text-stone-900">New Customers</span>
                        </div>
                        <span class="text-[11px] text-stone-400 mt-2">First purchase only. Validates zero prior order history.</span>
                    </label>

                    <label class="rounded-xl border border-stone-200 p-3.5 hover:border-emerald-700 cursor-pointer flex flex-col justify-between transition bg-stone-50/50">
                        <div class="flex items-center gap-2">
                            <input type="radio" name="customer_eligibility" value="returning" class="text-[#1c3d2c] focus:ring-emerald-500">
                            <span class="text-xs font-bold text-stone-900">Returning Customers</span>
                        </div>
                        <span class="text-[11px] text-stone-400 mt-2">Must possess at least one completed previous order.</span>
                    </label>

                    <label class="rounded-xl border border-stone-200 p-3.5 hover:border-emerald-700 cursor-pointer flex flex-col justify-between transition bg-stone-50/50">
                        <div class="flex items-center gap-2">
                            <input type="radio" name="customer_eligibility" value="vip" class="text-[#1c3d2c] focus:ring-emerald-500">
                            <span class="text-xs font-bold text-stone-900">VIP Cohort</span>
                        </div>
                        <span class="text-[11px] text-stone-400 mt-2">High lifetime spend subscribers and premium cohort members.</span>
                    </label>
                </div>
            </div>

            {{-- 7 & 8. SHIPPING & STACKING RULES --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Shipping Rules --}}
                <div class="rounded-2xl border border-stone-200/90 bg-white p-6 shadow-2xs space-y-4">
                    <div class="border-b border-stone-100 pb-3">
                        <h2 class="text-sm font-bold text-stone-900 tracking-tight flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">7</span>
                            Shipping Rules
                        </h2>
                        <p class="text-[11px] text-stone-400 mt-0.5">Control courier delivery fee overrides.</p>
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-stone-200 bg-stone-50/50 cursor-pointer hover:bg-white transition">
                            <input type="checkbox" name="free_shipping" value="1" class="mt-0.5 rounded text-[#1c3d2c] focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-semibold text-stone-900 block">Grant Free Shipping</span>
                                <span class="text-[11px] text-stone-400">Waives all standard shipping and handling charges across all courier rates.</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Stacking Rules --}}
                <div class="rounded-2xl border border-stone-200/90 bg-white p-6 shadow-2xs space-y-4">
                    <div class="border-b border-stone-100 pb-3">
                        <h2 class="text-sm font-bold text-stone-900 tracking-tight flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">8</span>
                            Stacking Rules
                        </h2>
                        <p class="text-[11px] text-stone-400 mt-0.5">Combination permissions with other promotional campaigns.</p>
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-stone-200 bg-stone-50/50 cursor-pointer hover:bg-white transition">
                            <input type="checkbox" name="allow_stacking" value="1" class="mt-0.5 rounded text-[#1c3d2c] focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-semibold text-stone-900 block">Allow Promotion Stacking</span>
                                <span class="text-[11px] text-stone-400">Can be combined with other product discounts, bundle deals, or automatic offers.</span>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            {{-- 9. SCHEDULE --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-6 shadow-2xs space-y-5">
                <div class="border-b border-stone-100 pb-3">
                    <h2 class="text-sm font-bold text-stone-900 tracking-tight flex items-center gap-2">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">9</span>
                        Promotion Schedule
                    </h2>
                    <p class="text-[11px] text-stone-400 mt-0.5">Define active time window and automated expiration timestamp.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Start Date & Time</label>
                        <input
                            type="datetime-local"
                            name="starts_at"
                            value="{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}"
                            class="w-full h-10 rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 text-xs text-stone-900 focus:border-[#1c3d2c] focus:bg-white focus:outline-none"
                        >
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-stone-700">End Date & Time</label>
                            <label class="flex items-center gap-1.5 text-[11px] text-stone-500 cursor-pointer">
                                <input type="checkbox" id="never-expires-cb" onchange="toggleNeverExpires(this)" class="rounded text-[#1c3d2c] focus:ring-emerald-500">
                                <span>Never expires</span>
                            </label>
                        </div>
                        <input
                            type="datetime-local"
                            name="expires_at"
                            id="expires-at-input"
                            value="{{ old('expires_at', now()->addDays(30)->format('Y-m-d\TH:i')) }}"
                            class="w-full h-10 rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 text-xs text-stone-900 focus:border-[#1c3d2c] focus:bg-white focus:outline-none"
                        >
                    </div>
                </div>
            </div>

            {{-- 10. SEO / INTERNAL NOTES --}}
            <div class="rounded-2xl border border-stone-200/90 bg-white p-6 shadow-2xs space-y-4">
                <div class="border-b border-stone-100 pb-3">
                    <h2 class="text-sm font-bold text-stone-900 tracking-tight flex items-center gap-2">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">10</span>
                        Internal Admin Notes
                    </h2>
                    <p class="text-[11px] text-stone-400 mt-0.5">Confidential notes visible solely to administrators and marketing teams.</p>
                </div>

                <div>
                    <textarea
                        name="internal_notes"
                        rows="3"
                        placeholder="e.g. Approved by marketing director for Q4 customer acquisition push. Budget cap set at ₹50,000 subsidy."
                        class="w-full rounded-xl border border-stone-200 bg-stone-50/50 p-3.5 text-xs text-stone-900 focus:border-[#1c3d2c] focus:bg-white focus:outline-none"
                    >{{ old('internal_notes') }}</textarea>
                </div>
            </div>

            {{-- Bottom Action Bar --}}
            <div class="pt-4 flex items-center justify-end gap-3">
                <a
                    href="{{ route('admin.coupons.index') }}"
                    class="rounded-xl border border-stone-200 bg-white px-5 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition shadow-2xs"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#1c3d2c] px-6 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-[#142e21] transition hover:shadow-md"
                >
                    <svg class="h-4 w-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Save & Publish Promotion</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    // Generate Random Clean Alphanumeric Code
    function generateRandomCode() {
        var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        var result = 'RESET';
        for (var i = 0; i < 5; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('coupon-code-input').value = result;
    }

    // Toggle specific products checklist
    function toggleProductList(show) {
        var box = document.getElementById('product-picker-container');
        if (show) {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }

    // Toggle never expires
    function toggleNeverExpires(cb) {
        var input = document.getElementById('expires-at-input');
        if (cb.checked) {
            input.value = '';
            input.setAttribute('disabled', 'disabled');
            input.classList.add('opacity-50');
        } else {
            input.removeAttribute('disabled');
            input.classList.remove('opacity-50');
        }
    }

    // Dynamic Discount Type UI adjustments
    function updateDiscountTypeUI() {
        var sel = document.getElementById('discount-type-select').value;
        var prefix = document.getElementById('discount-val-prefix');
        var maxWrapper = document.getElementById('max-discount-wrapper');
        var valInput = document.getElementById('discount-value-input');

        if (sel === 'percentage') {
            prefix.textContent = '%';
            maxWrapper.classList.remove('hidden');
            valInput.value = valInput.value || 10;
        } else if (sel === 'free_shipping') {
            prefix.textContent = '₹';
            valInput.value = 0;
            maxWrapper.classList.add('hidden');
        } else {
            prefix.textContent = '₹';
            maxWrapper.classList.add('hidden');
            valInput.value = valInput.value || 100;
        }
    }
</script>
@endsection
