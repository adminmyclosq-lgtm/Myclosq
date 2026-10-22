@extends('layouts.admin')

@section('title', "Customer: {$customer->name}")

@section('content')
<div class="p-6 md:p-8 space-y-6 max-w-[1600px] mx-auto">

    <!-- Top Breadcrumb & Actions Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.index') }}" class="h-9 w-9 rounded-xl bg-white border border-stone-200 flex items-center justify-center text-stone-500 hover:text-stone-800 hover:bg-stone-50 transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-stone-400 font-mono">ID #{{ $customer->id }}</span>
                    @if($metrics['is_vip'])
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            ★ VIP Client
                        </span>
                    @endif
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $customer->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-600' }}">
                        {{ ucfirst($customer->status ?? 'active') }}
                    </span>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-stone-900 tracking-tight mt-0.5 font-serif">{{ $customer->name }}</h1>
            </div>
        </div>

        <!-- Quick Top Action Controls -->
        <div class="flex items-center gap-2">
            @if($customer->mobile)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->mobile) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    WhatsApp
                </a>
            @endif

            <!-- Quick Add Note Trigger -->
            <button type="button" onclick="switchCustomerTab('notes'); document.getElementById('note-textarea')?.focus();" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-stone-200 text-stone-700 hover:bg-stone-50 text-xs font-semibold rounded-xl shadow-xs transition">
                <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Add Note
            </button>

            <!-- Status Dropdown Form -->
            <form method="POST" action="{{ route('admin.customers.status', $customer->id) }}" class="inline-block">
                @csrf
                <select name="status" onchange="this.form.submit()" class="text-xs px-3 py-2 rounded-xl border border-stone-200 bg-white font-medium text-stone-700 hover:bg-stone-50 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition cursor-pointer shadow-xs">
                    <option value="active" {{ $customer->status === 'active' ? 'selected' : '' }}>Active Status</option>
                    <option value="inactive" {{ $customer->status === 'inactive' ? 'selected' : '' }}>Inactive Status</option>
                    <option value="suspended" {{ $customer->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </form>
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

    <!-- Profile Header Card (HubSpot / Shopify Style) -->
    <div class="bg-white rounded-2xl border border-stone-200/80 p-6 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-2xl flex items-center justify-center font-bold text-xl shrink-0 {{ $metrics['is_vip'] ? 'bg-amber-100 text-amber-900 border-2 border-amber-300' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xl font-bold text-stone-900">{{ $customer->name }}</span>
                        @if($metrics['is_vip'])
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                VIP Customer
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 text-xs text-stone-500 mt-1 flex-wrap">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ $customer->email }}
                        </span>
                        @if($customer->mobile)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $customer->mobile }}
                            </span>
                        @endif
                        <span class="text-stone-300">|</span>
                        <span>Joined {{ $customer->created_at->format('M d, Y') }} ({{ $customer->created_at->diffForHumans() }})</span>
                    </div>
                </div>
            </div>

            <!-- Tags Component -->
            <div class="flex flex-col md:items-end gap-2">
                <div class="flex items-center gap-1.5 flex-wrap">
                    @forelse($metrics['tags'] as $tag)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-stone-100 text-stone-700 border border-stone-200">
                            #{{ $tag }}
                        </span>
                    @empty
                        <span class="text-xs text-stone-400 italic">No tags assigned</span>
                    @endforelse
                    <button type="button" onclick="document.getElementById('edit-tags-modal').classList.remove('hidden')" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-stone-50 border border-stone-200 text-stone-600 hover:bg-stone-100 transition">
                        + Edit Tags
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 8 Tabs Header Bar -->
    <div class="border-b border-stone-200">
        <nav class="flex space-x-1 sm:space-x-3 overflow-x-auto text-xs font-semibold" aria-label="Customer Tabs">
            <button type="button" onclick="switchCustomerTab('overview')" id="tab-btn-overview" class="customer-tab-btn py-3 px-3.5 border-b-2 border-emerald-600 text-emerald-800 whitespace-nowrap transition">
                Overview
            </button>
            <button type="button" onclick="switchCustomerTab('orders')" id="tab-btn-orders" class="customer-tab-btn py-3 px-3.5 border-b-2 border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300 whitespace-nowrap transition">
                Orders ({{ $metrics['total_orders'] }})
            </button>
            <button type="button" onclick="switchCustomerTab('products')" id="tab-btn-products" class="customer-tab-btn py-3 px-3.5 border-b-2 border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300 whitespace-nowrap transition">
                Products Purchased ({{ $productsPurchased->count() }})
            </button>
            <button type="button" onclick="switchCustomerTab('addresses')" id="tab-btn-addresses" class="customer-tab-btn py-3 px-3.5 border-b-2 border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300 whitespace-nowrap transition">
                Addresses ({{ $addresses->count() }})
            </button>
            <button type="button" onclick="switchCustomerTab('payments')" id="tab-btn-payments" class="customer-tab-btn py-3 px-3.5 border-b-2 border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300 whitespace-nowrap transition">
                Payments ({{ $payments->count() }})
            </button>
            <button type="button" onclick="switchCustomerTab('shipping')" id="tab-btn-shipping" class="customer-tab-btn py-3 px-3.5 border-b-2 border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300 whitespace-nowrap transition">
                Shipping ({{ $shipments->count() }})
            </button>
            <button type="button" onclick="switchCustomerTab('notes')" id="tab-btn-notes" class="customer-tab-btn py-3 px-3.5 border-b-2 border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300 whitespace-nowrap transition">
                Notes ({{ $notes->count() }})
            </button>
            <button type="button" onclick="switchCustomerTab('timeline')" id="tab-btn-timeline" class="customer-tab-btn py-3 px-3.5 border-b-2 border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300 whitespace-nowrap transition">
                Timeline ({{ count($timeline) }})
            </button>
        </nav>
    </div>

    <!-- TAB 1: OVERVIEW -->
    <div id="customer-tab-overview" class="customer-tab-content space-y-6">
        <!-- Financial & Purchasing KPI Strip -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
            <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Lifetime Spend</span>
                <div class="mt-1.5 text-2xl font-bold text-stone-900 tracking-tight">₹{{ number_format($metrics['lifetime_spend'], 2) }}</div>
                <div class="mt-0.5 text-[11px] text-stone-400">Total revenue generated</div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Avg Order Value</span>
                <div class="mt-1.5 text-2xl font-bold text-stone-900 tracking-tight">₹{{ number_format($metrics['aov'], 2) }}</div>
                <div class="mt-0.5 text-[11px] text-stone-400">Per completed order</div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Total Orders</span>
                <div class="mt-1.5 text-2xl font-bold text-stone-900 tracking-tight">{{ $metrics['total_orders'] }}</div>
                <div class="mt-0.5 text-[11px] text-stone-400">{{ $metrics['paid_orders_count'] }} paid / completed</div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Units Purchased</span>
                <div class="mt-1.5 text-2xl font-bold text-stone-900 tracking-tight">{{ $metrics['total_units'] }}</div>
                <div class="mt-0.5 text-[11px] text-stone-400">Bottles / packs across orders</div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Last Purchase</span>
                <div class="mt-1.5 text-sm font-bold text-stone-900 truncate">
                    @if($metrics['last_purchase_date'])
                        {{ \Carbon\Carbon::parse($metrics['last_purchase_date'])->format('d M Y') }}
                    @else
                        No Purchases
                    @endif
                </div>
                <div class="mt-0.5 text-[11px] text-stone-400">
                    {{ $metrics['last_order_number'] ? "#{$metrics['last_order_number']}" : '—' }}
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Member Tenure</span>
                <div class="mt-1.5 text-sm font-bold text-stone-900 truncate">{{ $customer->created_at->diffForHumans(null, true) }}</div>
                <div class="mt-0.5 text-[11px] text-stone-400">Since {{ $customer->created_at->format('M Y') }}</div>
            </div>
        </div>

        <!-- 2-Column Overview Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column: Customer Profile & Preferred Payment -->
            <div class="space-y-6">
                <!-- Customer Details Card -->
                <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-xs">
                    <h3 class="text-sm font-bold text-stone-900 mb-4 pb-2 border-b border-stone-100 flex items-center justify-between">
                        <span>Customer Information</span>
                        <span class="text-xs font-normal text-stone-400">Account #{{ $customer->id }}</span>
                    </h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-3.5 gap-x-6 text-xs">
                        <div>
                            <dt class="text-stone-400 font-medium">Full Name</dt>
                            <dd class="text-stone-900 font-semibold mt-0.5">{{ $customer->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-stone-400 font-medium">Display Name</dt>
                            <dd class="text-stone-900 font-semibold mt-0.5">{{ $customer->customerProfile?->display_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-stone-400 font-medium">Email Address</dt>
                            <dd class="text-stone-900 font-semibold mt-0.5">{{ $customer->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-stone-400 font-medium">Mobile Number</dt>
                            <dd class="text-stone-900 font-semibold mt-0.5">{{ $customer->mobile ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-stone-400 font-medium">Gender / Birthday</dt>
                            <dd class="text-stone-900 font-semibold mt-0.5">
                                {{ ucfirst($customer->customerProfile?->gender ?? 'Not Specified') }}
                                @if($customer->customerProfile?->date_of_birth)
                                    • {{ $customer->customerProfile->date_of_birth->format('d M Y') }}
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-stone-400 font-medium">Acquisition Channel</dt>
                            <dd class="text-stone-900 font-semibold mt-0.5">{{ ucfirst($customer->customerProfile?->source_channel ?? 'Direct Web') }}</dd>
                        </div>
                        <div>
                            <dt class="text-stone-400 font-medium">WhatsApp Updates</dt>
                            <dd class="text-stone-900 font-semibold mt-0.5">
                                @if($customer->customerProfile?->whatsapp_opt_in || $customer->whatsappContact?->opt_in)
                                    <span class="text-emerald-700 font-bold">✓ Opted-in</span>
                                @else
                                    <span class="text-stone-400">Not opted in</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-stone-400 font-medium">Marketing Consent</dt>
                            <dd class="text-stone-900 font-semibold mt-0.5">
                                {{ $customer->customerProfile?->marketing_opt_in ? 'Subscribed' : 'No Consent' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Preferred Payment Method Card -->
                <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-xs">
                    <h3 class="text-sm font-bold text-stone-900 mb-3 pb-2 border-b border-stone-100 flex items-center justify-between">
                        <span>Preferred Payment Method</span>
                        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">Primary</span>
                    </h3>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-stone-100 flex items-center justify-center text-stone-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            <div class="font-bold text-stone-900 text-sm">{{ ucfirst($metrics['preferred_payment']) }}</div>
                            <div class="text-xs text-stone-500">
                                Total processed through this channel: ₹{{ number_format($metrics['preferred_payment_spent'], 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Preferred Address & CRM Notes Snippet -->
            <div class="space-y-6">
                <!-- Preferred Shipping Address Card -->
                <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-xs">
                    <h3 class="text-sm font-bold text-stone-900 mb-3 pb-2 border-b border-stone-100 flex items-center justify-between">
                        <span>Preferred Shipping Address</span>
                        <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">Default</span>
                    </h3>
                    @if($metrics['preferred_address'])
                        @php $addr = $metrics['preferred_address']; @endphp
                        <div class="text-xs text-stone-700 space-y-1">
                            <div class="font-bold text-stone-900 text-sm">{{ $addr->recipient_name ?? $customer->name }}</div>
                            <div>{{ $addr->address_line1 }}</div>
                            @if($addr->address_line2) <div>{{ $addr->address_line2 }}</div> @endif
                            <div>{{ $addr->city }}, {{ $addr->state }} — {{ $addr->postal_code }}</div>
                            <div class="text-stone-500 font-medium">{{ $addr->country ?? 'India' }}</div>
                            @if($addr->phone)
                                <div class="text-stone-500 pt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $addr->phone }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-xs text-stone-400 italic py-2">No saved address found on file.</div>
                    @endif
                </div>

                <!-- Recent CRM Notes Preview -->
                <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-xs">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-stone-100">
                        <h3 class="text-sm font-bold text-stone-900">CRM Staff Notes</h3>
                        <button type="button" onclick="switchCustomerTab('notes')" class="text-xs font-semibold text-emerald-700 hover:underline">
                            View all ({{ $notes->count() }}) →
                        </button>
                    </div>
                    <div class="space-y-3">
                        @forelse($notes->take(2) as $n)
                            <div class="p-3 rounded-xl border border-stone-100 {{ $n->is_pinned ? 'bg-purple-50/50 border-purple-200' : 'bg-stone-50/50' }} text-xs">
                                <div class="flex items-center justify-between text-[11px] text-stone-500 mb-1">
                                    <span class="font-semibold text-stone-800">{{ $n->author?->name ?? 'Staff' }}</span>
                                    <span>{{ $n->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-stone-700 leading-relaxed">{{ $n->note }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-stone-400 italic py-2">No CRM notes recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: ORDERS -->
    <div id="customer-tab-orders" class="customer-tab-content hidden space-y-4">
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-stone-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-stone-900">Order History</h3>
                    <p class="text-xs text-stone-400">All purchase transactions initiated by this customer.</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 border-b border-stone-200 text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                        <tr>
                            <th class="py-3 px-4">Order</th>
                            <th class="py-3 px-3">Date</th>
                            <th class="py-3 px-3">Items</th>
                            <th class="py-3 px-3 text-center">Payment</th>
                            <th class="py-3 px-3 text-center">Fulfillment</th>
                            <th class="py-3 px-4 text-right">Total</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($orders as $o)
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="py-3.5 px-4 font-semibold text-stone-900">
                                    <a href="{{ route('admin.orders.show', $o->id) }}" class="text-emerald-700 hover:underline">
                                        #{{ $o->order_number }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-3 whitespace-nowrap text-stone-500">
                                    {{ $o->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-stone-100 text-stone-800">
                                        {{ $o->items->count() }} items
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                    @if($o->payment_status === 'paid')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Paid</span>
                                    @elseif($o->payment_status === 'pending')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">Pending</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">{{ ucfirst($o->payment_status) }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium {{ $o->fulfilment_status === 'delivered' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-stone-100 text-stone-700 border border-stone-200' }}">
                                        {{ ucfirst($o->fulfilment_status ?? 'Unfulfilled') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-stone-900 whitespace-nowrap">
                                    ₹{{ number_format($o->grand_total, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.orders.show', $o->id) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                                        View Order →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-stone-400 italic">No orders placed by this customer yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
                <div class="p-3 border-t border-stone-100">
                    {{ $orders->appends(request()->except('orders_page'))->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 3: PRODUCTS PURCHASED -->
    <div id="customer-tab-products" class="customer-tab-content hidden space-y-4">
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-stone-100">
                <h3 class="text-sm font-bold text-stone-900">Products Purchased Aggregation</h3>
                <p class="text-xs text-stone-400">Total units and spend broken down per unique product item.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 border-b border-stone-200 text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                        <tr>
                            <th class="py-3 px-4">Product</th>
                            <th class="py-3 px-3">SKU</th>
                            <th class="py-3 px-3 text-center">Units Bought</th>
                            <th class="py-3 px-3 text-right">Avg Unit Price</th>
                            <th class="py-3 px-4 text-right">Total Spent</th>
                            <th class="py-3 px-4 text-right">Last Purchased</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($productsPurchased as $prod)
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="py-3.5 px-4 font-semibold text-stone-900">
                                    <a href="{{ route('admin.products.show', $prod->product_id) }}" class="hover:text-emerald-700 transition">
                                        {{ $prod->product_name }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-3 font-mono text-stone-500">
                                    {{ $prod->variant_sku ?: $prod->base_sku }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800">
                                        {{ $prod->total_units_bought }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right text-stone-700">
                                    ₹{{ number_format($prod->avg_unit_price, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-stone-900">
                                    ₹{{ number_format($prod->total_spent, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-stone-500">
                                    {{ \Carbon\Carbon::parse($prod->last_purchased_date)->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-stone-400 italic">No products purchased yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: ADDRESSES -->
    <div id="customer-tab-addresses" class="customer-tab-content hidden space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($addresses as $addr)
                <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-xs relative">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-stone-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-stone-500">
                            {{ ucfirst($addr->address_type ?? 'Shipping Address') }}
                        </span>
                        @if($addr->is_default)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                Default
                            </span>
                        @endif
                    </div>
                    <div class="text-xs text-stone-700 space-y-1">
                        <div class="font-bold text-stone-900 text-sm">{{ $addr->recipient_name ?? $customer->name }}</div>
                        <div>{{ $addr->address_line1 }}</div>
                        @if($addr->address_line2) <div>{{ $addr->address_line2 }}</div> @endif
                        <div>{{ $addr->city }}, {{ $addr->state }} — {{ $addr->postal_code }}</div>
                        <div class="text-stone-500 font-medium">{{ $addr->country ?? 'India' }}</div>
                        @if($addr->phone)
                            <div class="text-stone-500 pt-2 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $addr->phone }}
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-stone-200/80 p-8 text-center text-stone-400 italic">
                    No registered customer addresses found.
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 5: PAYMENTS -->
    <div id="customer-tab-payments" class="customer-tab-content hidden space-y-4">
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-stone-100">
                <h3 class="text-sm font-bold text-stone-900">Payment Transactions</h3>
                <p class="text-xs text-stone-400">All gateway payments and capture records associated with this customer.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 border-b border-stone-200 text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                        <tr>
                            <th class="py-3 px-4">Payment Ref</th>
                            <th class="py-3 px-3">Order</th>
                            <th class="py-3 px-3">Gateway</th>
                            <th class="py-3 px-3 text-right">Amount</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($payments as $p)
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="py-3.5 px-4 font-mono font-medium text-stone-800">
                                    {{ $p->gateway_order_id ?: "PAY-{$p->id}" }}
                                </td>
                                <td class="py-3.5 px-3">
                                    @if($p->order)
                                        <a href="{{ route('admin.orders.show', $p->order_id) }}" class="text-emerald-700 hover:underline font-semibold">
                                            #{{ $p->order->order_number }}
                                        </a>
                                    @else
                                        #{{ $p->order_id }}
                                    @endif
                                </td>
                                <td class="py-3.5 px-3 font-semibold text-stone-800">
                                    {{ ucfirst($p->gateway ?? 'Gateway') }}
                                </td>
                                <td class="py-3.5 px-3 text-right font-bold text-stone-900">
                                    ₹{{ number_format($p->amount, 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium {{ $p->status === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right text-stone-500">
                                    {{ ($p->paid_at ?: $p->created_at)->format('d M Y, h:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-stone-400 italic">No payments logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 6: SHIPPING -->
    <div id="customer-tab-shipping" class="customer-tab-content hidden space-y-4">
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-stone-100">
                <h3 class="text-sm font-bold text-stone-900">Shipments & Deliveries</h3>
                <p class="text-xs text-stone-400">All carrier shipments dispatched for this customer's orders.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 border-b border-stone-200 text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                        <tr>
                            <th class="py-3 px-4">Shipment #</th>
                            <th class="py-3 px-3">Order</th>
                            <th class="py-3 px-3">Courier</th>
                            <th class="py-3 px-3">Tracking / AWB</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Dispatch / Delivered</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($shipments as $sh)
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="py-3.5 px-4 font-mono font-medium text-stone-900">
                                    #{{ $sh->shipment_number }}
                                </td>
                                <td class="py-3.5 px-3">
                                    @if($sh->order)
                                        <a href="{{ route('admin.orders.show', $sh->order_id) }}" class="text-emerald-700 hover:underline font-semibold">
                                            #{{ $sh->order->order_number }}
                                        </a>
                                    @else
                                        #{{ $sh->order_id }}
                                    @endif
                                </td>
                                <td class="py-3.5 px-3 font-semibold text-stone-800">
                                    {{ $sh->courier ?? 'Express' }}
                                </td>
                                <td class="py-3.5 px-3 font-mono text-stone-700">
                                    {{ $sh->tracking_number ?: 'Not Assigned' }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium {{ $sh->status === 'delivered' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-sky-50 text-sky-700 border border-sky-200' }}">
                                        {{ ucfirst($sh->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right text-stone-500">
                                    {{ ($sh->delivered_at ?: ($sh->dispatch_date ?: $sh->created_at))->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-stone-400 italic">No shipments created for this customer yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 7: NOTES (LIGHTWEIGHT CRM) -->
    <div id="customer-tab-notes" class="customer-tab-content hidden space-y-6">
        <!-- New Note Creation Form -->
        <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-xs">
            <h3 class="text-sm font-bold text-stone-900 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Record CRM Note
            </h3>
            <form method="POST" action="{{ route('admin.customers.notes.store', $customer->id) }}" class="space-y-3">
                @csrf
                <textarea id="note-textarea" name="note" rows="3" placeholder="Enter customer note, phone call summary, consultation update, or internal memo..." class="w-full text-xs p-3 rounded-xl border border-stone-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" required></textarea>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                    <div class="flex items-center gap-3">
                        <select name="category" class="text-xs px-3 py-1.5 rounded-lg border border-stone-200 bg-stone-50 focus:bg-white focus:border-emerald-500 transition">
                            <option value="general">General Note</option>
                            <option value="call">Call Log</option>
                            <option value="email">Email Log</option>
                            <option value="support">Support Inquiry</option>
                            <option value="follow_up">Follow-up Required</option>
                            <option value="urgent">Urgent</option>
                        </select>
                        <label class="inline-flex items-center gap-1.5 text-xs text-stone-600 cursor-pointer">
                            <input type="checkbox" name="is_pinned" value="1" class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 h-3.5 w-3.5">
                            <span>Pin to Top</span>
                        </label>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl transition shadow-xs">
                        Save Note
                    </button>
                </div>
            </form>
        </div>

        <!-- Notes Roster -->
        <div class="space-y-3">
            @forelse($notes as $note)
                <div class="bg-white rounded-2xl border {{ $note->is_pinned ? 'border-purple-300 bg-purple-50/20' : 'border-stone-200/80' }} p-5 shadow-xs transition">
                    <div class="flex items-center justify-between text-xs mb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-stone-900">{{ $note->author?->name ?? 'Admin Staff' }}</span>
                            <span class="inline-flex items-center px-2 py-0.2 rounded text-[10px] font-semibold {{ $note->is_pinned ? 'bg-purple-100 text-purple-800' : 'bg-stone-100 text-stone-600' }}">
                                {{ ucfirst($note->category) }}
                            </span>
                            @if($note->is_pinned)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-purple-700">
                                    ★ Pinned
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-stone-400">{{ $note->created_at->format('d M Y, h:i A') }}</span>
                            <form method="POST" action="{{ route('admin.customers.notes.destroy', [$customer->id, $note->id]) }}" onsubmit="return confirm('Remove this note?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-stone-400 hover:text-rose-600 transition" title="Delete Note">
                                    ✕
                                </button>
                            </form>
                        </div>
                    </div>
                    <p class="text-xs text-stone-700 whitespace-pre-line leading-relaxed">{{ $note->note }}</p>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-stone-200/80 p-8 text-center text-stone-400 italic">
                    No CRM notes on file. Use the box above to log the first customer note.
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 8: TIMELINE (360° CHRONOLOGICAL FEED) -->
    <div id="customer-tab-timeline" class="customer-tab-content hidden space-y-4">
        <div class="bg-white rounded-2xl border border-stone-200/80 p-6 shadow-xs">
            <div class="mb-6">
                <h3 class="text-base font-bold text-stone-900 font-serif">Customer Activity Timeline</h3>
                <p class="text-xs text-stone-500">Unified 360° chronological activity feed spanning registration, orders, payments, shipments, WhatsApp, refunds, and notes.</p>
            </div>

            <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-stone-200">
                @forelse($timeline as $event)
                    <div class="relative group">
                        <!-- Dot on vertical line -->
                        <div class="absolute -left-6 top-1 h-5 w-5 rounded-full border-2 border-white flex items-center justify-center text-[10px] font-bold shadow-xs
                            {{ $event['badge_color'] === 'emerald' ? 'bg-emerald-600 text-white' : '' }}
                            {{ $event['badge_color'] === 'blue' ? 'bg-blue-600 text-white' : '' }}
                            {{ $event['badge_color'] === 'amber' ? 'bg-amber-500 text-white' : '' }}
                            {{ $event['badge_color'] === 'rose' ? 'bg-rose-500 text-white' : '' }}
                            {{ $event['badge_color'] === 'purple' ? 'bg-purple-600 text-white' : '' }}
                            {{ $event['badge_color'] === 'sky' ? 'bg-sky-500 text-white' : '' }}
                            {{ $event['badge_color'] === 'stone' ? 'bg-stone-500 text-white' : '' }}">
                        </div>

                        <!-- Event Content Card -->
                        <div class="bg-stone-50/70 hover:bg-stone-50 rounded-xl p-4 border border-stone-200/80 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-stone-900">{{ $event['title'] }}</span>
                                    <span class="inline-flex items-center px-2 py-0.2 rounded text-[10px] font-semibold
                                        {{ $event['badge_color'] === 'emerald' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $event['badge_color'] === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $event['badge_color'] === 'amber' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $event['badge_color'] === 'rose' ? 'bg-rose-100 text-rose-800' : '' }}
                                        {{ $event['badge_color'] === 'purple' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $event['badge_color'] === 'sky' ? 'bg-sky-100 text-sky-800' : '' }}
                                        {{ $event['badge_color'] === 'stone' ? 'bg-stone-100 text-stone-800' : '' }}">
                                        {{ $event['badge'] }}
                                    </span>
                                </div>
                                <span class="text-[11px] text-stone-400">
                                    {{ $event['timestamp'] ? \Carbon\Carbon::parse($event['timestamp'])->format('d M Y, h:i A') : '—' }}
                                </span>
                            </div>

                            <p class="text-xs text-stone-600 mt-1 leading-relaxed">{{ $event['description'] }}</p>

                            @if(!empty($event['meta']))
                                <div class="flex items-center gap-3 mt-2.5 pt-2 border-t border-stone-200/60 text-[11px] text-stone-500 flex-wrap">
                                    @foreach($event['meta'] as $k => $v)
                                        <span><strong class="text-stone-700">{{ $k }}:</strong> {{ $v }}</span>
                                    @endforeach
                                    @if(!empty($event['link']))
                                        <a href="{{ $event['link'] }}" class="text-emerald-700 font-semibold hover:underline ml-auto">
                                            View Details →
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-stone-400 italic">No activity timeline events recorded.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- Modal: Edit Customer Tags -->
<div id="edit-tags-modal" class="fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-stone-200">
        <div class="flex items-center justify-between pb-3 border-b border-stone-100">
            <h3 class="text-sm font-bold text-stone-900">Manage Customer Tags</h3>
            <button type="button" onclick="document.getElementById('edit-tags-modal').classList.add('hidden')" class="text-stone-400 hover:text-stone-600">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.customers.tags', $customer->id) }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Tags (Comma separated)</label>
                <input type="text" name="tags" value="{{ implode(', ', $metrics['tags']) }}" placeholder="e.g. VIP, Gut Health, Repeat Buyer, High Value" class="w-full text-xs p-2.5 rounded-xl border border-stone-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                <p class="text-[11px] text-stone-400 mt-1">Tagging with "VIP" will automatically enable VIP status badges across the admin panel.</p>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('edit-tags-modal').classList.add('hidden')" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl transition">
                    Save Tags
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabs Switching Script -->
<script>
    function switchCustomerTab(tabName) {
        document.querySelectorAll('.customer-tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.customer-tab-btn').forEach(btn => {
            btn.classList.remove('border-emerald-600', 'text-emerald-800');
            btn.classList.add('border-transparent', 'text-stone-500');
        });

        const activeContent = document.getElementById(`customer-tab-${tabName}`);
        const activeBtn = document.getElementById(`tab-btn-${tabName}`);

        if (activeContent) activeContent.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.classList.add('border-emerald-600', 'text-emerald-800');
            activeBtn.classList.remove('border-transparent', 'text-stone-500');
        }

        // Keep hash in URL
        window.location.hash = `tab-${tabName}`;
    }

    // Auto-select tab if URL has hash
    document.addEventListener('DOMContentLoaded', function () {
        const hash = window.location.hash;
        if (hash && hash.startsWith('#tab-')) {
            const tabName = hash.replace('#tab-', '');
            if (document.getElementById(`customer-tab-${tabName}`)) {
                switchCustomerTab(tabName);
            }
        }
    });
</script>
@endsection
