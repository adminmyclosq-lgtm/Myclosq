@extends('layouts.admin')

@section('title', 'Customers Management')

@section('content')
<div class="p-6 md:p-8 space-y-8 max-w-[1600px] mx-auto">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                    Lightweight CRM
                </span>
                <span class="text-xs text-stone-400">• Master Customer Hub</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-stone-900 tracking-tight mt-1.5 font-serif">Customer Management</h1>
            <p class="text-sm text-stone-500 mt-1">Manage relationships, analyze customer lifetime spend, and access 360° customer history.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.customers.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-stone-200 text-stone-700 hover:bg-stone-50 text-xs font-semibold rounded-xl shadow-xs transition">
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

    <!-- 6 Master Customer KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- 1. Total Customers -->
        <a href="{{ route('admin.customers.index') }}" class="group block bg-white p-4 rounded-2xl border border-stone-200/80 hover:border-emerald-500/50 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Total Customers</span>
                <div class="h-7 w-7 rounded-lg bg-stone-100 flex items-center justify-center text-stone-600 group-hover:bg-emerald-50 group-hover:text-emerald-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-stone-900 tracking-tight">{{ number_format($kpis['total_customers']) }}</div>
            <div class="mt-1 text-[11px] text-stone-400">Total registered profiles</div>
        </a>

        <!-- 2. Active Customers -->
        <a href="{{ route('admin.customers.index', ['status' => 'active']) }}" class="group block bg-white p-4 rounded-2xl border border-stone-200/80 hover:border-emerald-500/50 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Active</span>
                <div class="h-7 w-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-emerald-700 tracking-tight">{{ number_format($kpis['active_customers']) }}</div>
            <div class="mt-1 text-[11px] text-stone-400">Verified & active accounts</div>
        </a>

        <!-- 3. New Customers -->
        <a href="{{ route('admin.customers.index', ['sort' => 'users.id', 'direction' => 'desc']) }}" class="group block bg-white p-4 rounded-2xl border border-stone-200/80 hover:border-emerald-500/50 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">New (30d)</span>
                <div class="h-7 w-7 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-blue-600 tracking-tight">{{ number_format($kpis['new_customers']) }}</div>
            <div class="mt-1 text-[11px] text-stone-400">Joined last 30 days</div>
        </a>

        <!-- 4. Returning Customers -->
        <a href="{{ route('admin.customers.index', ['order_count' => '2-5']) }}" class="group block bg-white p-4 rounded-2xl border border-stone-200/80 hover:border-emerald-500/50 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Returning</span>
                <div class="h-7 w-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-indigo-600 tracking-tight">{{ number_format($kpis['returning_customers']) }}</div>
            <div class="mt-1 text-[11px] text-stone-400">Placed > 1 orders</div>
        </a>

        <!-- 5. VIP Customers -->
        <a href="{{ route('admin.customers.index', ['vip' => '1']) }}" class="group block bg-white p-4 rounded-2xl border border-stone-200/80 hover:border-amber-500/50 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">VIP Clients</span>
                <div class="h-7 w-7 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-amber-600 tracking-tight">{{ number_format($kpis['vip_customers']) }}</div>
            <div class="mt-1 text-[11px] text-stone-400">High spend & tagged VIP</div>
        </a>

        <!-- 6. WhatsApp Opt-ins -->
        <a href="{{ route('admin.customers.index') }}" class="group block bg-white p-4 rounded-2xl border border-stone-200/80 hover:border-emerald-500/50 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">WhatsApp</span>
                <div class="h-7 w-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-emerald-700 tracking-tight">{{ number_format($kpis['whatsapp_opt_ins']) }}</div>
            <div class="mt-1 text-[11px] text-stone-400">Consent for updates</div>
        </a>
    </div>

    <!-- Search & Advanced Filters Bar -->
    <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-xs">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="space-y-4">
            <!-- Row 1: Search, Country, State, City -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Input -->
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Search Customer</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, or mobile..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                        <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <!-- Country Filter -->
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Country</label>
                    <select name="country" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                        <option value="">All Countries</option>
                        @foreach($filterOptions['countries'] as $c)
                            <option value="{{ $c }}" {{ request('country') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- State Filter -->
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">State</label>
                    <select name="state" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                        <option value="">All States</option>
                        @foreach($filterOptions['states'] as $st)
                            <option value="{{ $st }}" {{ request('state') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- City Filter -->
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">City</label>
                    <select name="city" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                        <option value="">All Cities</option>
                        @foreach($filterOptions['cities'] as $ct)
                            <option value="{{ $ct }}" {{ request('city') == $ct ? 'selected' : '' }}>{{ $ct }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Row 2: Order Count, Lifetime Spend, Status, Tag & Action Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-1 border-t border-stone-100">
                <!-- Order Count -->
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Order Count</label>
                    <select name="order_count" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                        <option value="">Any Order Count</option>
                        <option value="0" {{ request('order_count') === '0' ? 'selected' : '' }}>0 Orders (Leads)</option>
                        <option value="1" {{ request('order_count') === '1' ? 'selected' : '' }}>1 Order (First Time)</option>
                        <option value="2-5" {{ request('order_count') === '2-5' ? 'selected' : '' }}>2 - 5 Orders (Regular)</option>
                        <option value="5+" {{ request('order_count') === '5+' ? 'selected' : '' }}>5+ Orders (Frequent)</option>
                    </select>
                </div>

                <!-- Lifetime Spend -->
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Lifetime Spend</label>
                    <select name="lifetime_spend" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                        <option value="">Any Spend Amount</option>
                        <option value="0-1000" {{ request('lifetime_spend') === '0-1000' ? 'selected' : '' }}>₹0 – ₹1,000</option>
                        <option value="1000-5000" {{ request('lifetime_spend') === '1000-5000' ? 'selected' : '' }}>₹1,000 – ₹5,000</option>
                        <option value="5000+" {{ request('lifetime_spend') === '5000+' ? 'selected' : '' }}>₹5,000+ (High Value)</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Customer Status</label>
                    <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <!-- Customer Tag -->
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Customer Tag</label>
                    <select name="tag" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                        <option value="">All Tags</option>
                        @foreach($filterOptions['tags'] as $tg)
                            <option value="{{ $tg }}" {{ request('tag') == $tg ? 'selected' : '' }}>{{ $tg }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl transition shadow-xs flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'country', 'state', 'city', 'order_count', 'lifetime_spend', 'status', 'tag', 'vip']))
                        <a href="{{ route('admin.customers.index') }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-semibold rounded-xl transition" title="Clear All Filters">
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Sticky Floating Strip -->
    <div id="bulk-actions-bar" class="hidden bg-stone-900 text-white px-5 py-3 rounded-2xl shadow-xl flex items-center justify-between transition-all duration-200">
        <div class="flex items-center gap-3">
            <span class="text-xs font-medium text-stone-300"><span id="selected-count" class="font-bold text-white">0</span> customers selected</span>
        </div>
        <div class="flex items-center gap-2">
            <!-- Add Tag Form -->
            <form method="POST" action="{{ route('admin.customers.bulk-action') }}" class="inline-flex items-center gap-1.5" id="bulk-tag-form">
                @csrf
                <input type="hidden" name="action" value="add_tag">
                <input type="text" name="payload" placeholder="Tag name (e.g. VIP)" class="text-xs px-2.5 py-1.5 rounded-lg bg-stone-800 border border-stone-700 text-white focus:outline-none focus:border-emerald-500 w-32">
                <button type="submit" class="text-xs px-3 py-1.5 bg-stone-700 hover:bg-stone-600 text-white rounded-lg font-medium transition">Add Tag</button>
            </form>

            <!-- Change Status Form -->
            <form method="POST" action="{{ route('admin.customers.bulk-action') }}" class="inline-flex items-center gap-1.5" id="bulk-status-form">
                @csrf
                <input type="hidden" name="action" value="status">
                <select name="payload" class="text-xs px-2.5 py-1.5 rounded-lg bg-stone-800 border border-stone-700 text-white focus:outline-none focus:border-emerald-500">
                    <option value="active">Set Active</option>
                    <option value="inactive">Set Inactive</option>
                    <option value="suspended">Set Suspended</option>
                </select>
                <button type="submit" class="text-xs px-3 py-1.5 bg-stone-700 hover:bg-stone-600 text-white rounded-lg font-medium transition">Apply</button>
            </form>

            <button type="button" onclick="clearSelectedCustomers()" class="text-xs text-stone-400 hover:text-white ml-2 transition">Deselect</button>
        </div>
    </div>

    <!-- Enterprise Customers Data Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-600">
                <thead class="bg-stone-50/80 border-b border-stone-200/80 text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="p-4 w-10 text-center">
                            <input type="checkbox" id="select-all-customers" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                        </th>
                        <th class="py-4 px-3">Avatar</th>
                        <th class="py-4 px-3">Customer</th>
                        <th class="py-4 px-3">Email</th>
                        <th class="py-4 px-3">Phone</th>
                        <th class="py-4 px-3 text-center">Orders</th>
                        <th class="py-4 px-3 text-right">Lifetime Spend</th>
                        <th class="py-4 px-3">Last Order</th>
                        <th class="py-4 px-3 text-center">Status</th>
                        <th class="py-4 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($customers as $c)
                        @php
                            $profile = $c->customerProfile;
                            $displayName = $c->name;
                            $initials = strtoupper(substr($displayName ?: $c->email, 0, 2));
                            $tags = $profile?->tags_array ?? [];
                            $isVip = $profile?->is_vip || in_array('VIP', $tags) || ($c->lifetime_spend >= 5000);
                        @endphp
                        <tr class="hover:bg-stone-50/70 transition group">
                            <!-- Checkbox -->
                            <td class="p-4 text-center">
                                <input type="checkbox" name="customer_ids[]" value="{{ $c->id }}" class="customer-checkbox rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                            </td>

                            <!-- Avatar -->
                            <td class="py-3 px-3">
                                <div class="h-9 w-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $isVip ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $initials }}
                                </div>
                            </td>

                            <!-- Customer Name & Tags -->
                            <td class="py-3 px-3 min-w-[180px]">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('admin.customers.show', $c->id) }}" class="font-semibold text-stone-900 hover:text-emerald-700 transition text-[13px]">
                                        {{ $displayName }}
                                    </a>
                                    @if($isVip)
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            VIP
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5 text-[11px] text-stone-400 mt-0.5">
                                    <span>ID #{{ $c->id }}</span>
                                    @if(!empty($tags))
                                        <span>•</span>
                                        <span class="text-stone-500 truncate max-w-[150px]">{{ implode(', ', array_slice($tags, 0, 2)) }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="py-3 px-3 text-stone-700">
                                <div class="truncate max-w-[200px]" title="{{ $c->email }}">{{ $c->email }}</div>
                            </td>

                            <!-- Phone -->
                            <td class="py-3 px-3 text-stone-700 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $c->mobile ?: '—' }}</span>
                                    @if($profile?->whatsapp_opt_in || $c->whatsappContact?->opt_in)
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200" title="WhatsApp Opted In">
                                            WA
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Orders Count -->
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $c->total_orders > 0 ? 'bg-stone-100 text-stone-800' : 'bg-stone-50 text-stone-400' }}">
                                    {{ $c->total_orders }}
                                </span>
                            </td>

                            <!-- Lifetime Spend -->
                            <td class="py-3 px-3 text-right font-medium text-stone-900 whitespace-nowrap">
                                ₹{{ number_format((float)$c->lifetime_spend, 2) }}
                            </td>

                            <!-- Last Order Date -->
                            <td class="py-3 px-3 text-stone-500 whitespace-nowrap">
                                @if($c->last_order_date)
                                    <div>{{ \Carbon\Carbon::parse($c->last_order_date)->format('d M Y') }}</div>
                                    <div class="text-[10px] text-stone-400">{{ \Carbon\Carbon::parse($c->last_order_date)->diffForHumans() }}</div>
                                @else
                                    <span class="text-stone-400 italic">No orders</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                @if($c->status === 'active' || empty($c->status))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Active
                                    </span>
                                @elseif($c->status === 'suspended')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                        Suspended
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-stone-100 text-stone-600 border border-stone-200">
                                        {{ ucfirst($c->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.customers.show', $c->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-stone-200 text-stone-700 hover:bg-stone-100 text-[11px] font-semibold transition" title="View 360° Customer Profile">
                                        <span>360° Profile</span>
                                        <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-stone-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-12 w-12 rounded-full bg-stone-100 flex items-center justify-center text-stone-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <p class="text-sm font-medium text-stone-700">No customers match your criteria</p>
                                    <p class="text-xs text-stone-400 mt-1">Try resetting your filters or modifying search keywords.</p>
                                    <a href="{{ route('admin.customers.index') }}" class="mt-4 px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold rounded-xl transition">
                                        Reset Filters
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($customers->hasPages())
            <div class="p-4 border-t border-stone-200/80 bg-stone-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-stone-500">
                <div>
                    Showing <span class="font-semibold text-stone-700">{{ $customers->firstItem() }}</span> to <span class="font-semibold text-stone-700">{{ $customers->lastItem() }}</span> of <span class="font-semibold text-stone-700">{{ $customers->total() }}</span> customers
                </div>
                <div>
                    {{ $customers->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Bulk Actions Selection Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all-customers');
        const checkboxes = document.querySelectorAll('.customer-checkbox');
        const bulkBar = document.getElementById('bulk-actions-bar');
        const countSpan = document.getElementById('selected-count');
        const bulkTagForm = document.getElementById('bulk-tag-form');
        const bulkStatusForm = document.getElementById('bulk-status-form');

        function updateSelection() {
            const checked = document.querySelectorAll('.customer-checkbox:checked');
            const total = checked.length;
            countSpan.textContent = total;

            if (total > 0) {
                bulkBar.classList.remove('hidden');
                // Synchronize hidden inputs inside forms
                [bulkTagForm, bulkStatusForm].forEach(form => {
                    form.querySelectorAll('input[name="customer_ids[]"]').forEach(el => el.remove());
                    checked.forEach(cb => {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'customer_ids[]';
                        hidden.value = cb.value;
                        form.appendChild(hidden);
                    });
                });
            } else {
                bulkBar.classList.add('hidden');
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateSelection();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                updateSelection();
            });
        });

        window.clearSelectedCustomers = function () {
            if (selectAll) selectAll.checked = false;
            checkboxes.forEach(cb => cb.checked = false);
            updateSelection();
        };
    });
</script>
@endsection
