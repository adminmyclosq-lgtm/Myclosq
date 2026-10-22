@extends('layouts.customer')

@section('content')
<div class="mx-auto max-w-[1400px] px-5 py-8 md:px-10 md:py-10 space-y-8">

    <!-- Top Account Banner & User Greeting -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-stone-200 pb-6">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-emerald-100 text-[#204b34] text-xl font-bold flex items-center justify-center shrink-0 shadow-xs">
                {{ strtoupper(substr($user->customerProfile->first_name ?? $user->name ?? $user->email, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-stone-900">
                        Hello, {{ $user->customerProfile->first_name ?? $user->name ?? 'there' }}
                    </h1>
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 border border-emerald-200">
                        Active Member
                    </span>
                </div>
                <p class="text-xs text-stone-500 mt-1">
                    {{ $user->email }} · Member since {{ $user->created_at?->format('M Y') ?? '2026' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('reset.home') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white shadow-2xs hover:bg-[#163524] transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>My Journey</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition shadow-2xs">
                    <svg class="h-4 w-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-xs font-semibold text-emerald-900 shadow-2xs">
            {{ session('success') }}
        </div>
    @endif

    <!-- Quick Stat Cards -->
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-2xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Programme</div>
            <div class="mt-2 text-xl font-bold text-stone-900">30-Day Gut Reset</div>
            <div class="mt-3 flex items-center justify-between text-xs">
                <span class="text-stone-500">Daily capsule routine</span>
                <a href="{{ route('reset.home') }}" class="font-semibold text-emerald-700 hover:underline">Continue &rarr;</a>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-2xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Total Orders</div>
            <div class="mt-2 text-xl font-bold text-stone-900">{{ $user->orders->count() }}</div>
            <div class="mt-3 flex items-center justify-between text-xs">
                <span class="text-stone-500">Shop purchases</span>
                <a href="#orders" class="font-semibold text-emerald-700 hover:underline">View history &rarr;</a>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-2xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-stone-400">WhatsApp Check-ins</div>
            <div class="mt-2 text-xl font-bold text-stone-900">
                {{ ($user->customerProfile->whatsapp_opt_in ?? false) ? 'Active' : 'Disabled' }}
            </div>
            <div class="mt-3 flex items-center justify-between text-xs">
                <span class="text-stone-500">{{ ($user->customerProfile->whatsapp_opt_in ?? false) ? 'Daily reminders' : 'Not opted in' }}</span>
                <a href="#notifications" class="font-semibold text-emerald-700 hover:underline">Manage &rarr;</a>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-2xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Response Brief</div>
            <div class="mt-2 text-xl font-bold text-stone-900">
                {{ $user->customerProfile?->is_vip ? 'VIP Brief' : 'Standard' }}
            </div>
            <div class="mt-3 flex items-center justify-between text-xs">
                <span class="text-stone-500">Day 30 personalized report</span>
                <a href="{{ route('my-brief') }}" class="font-semibold text-emerald-700 hover:underline">View brief &rarr;</a>
            </div>
        </div>
    </div>

    <!-- 1. PROFILE SECTION -->
    <section id="profile" class="scroll-mt-24 rounded-2xl border border-stone-200 bg-white p-6 shadow-2xs">
        <div class="flex items-center justify-between border-b border-stone-100 pb-4 mb-5">
            <div>
                <h2 class="text-base font-bold text-stone-900">Personal Information</h2>
                <p class="text-xs text-stone-500 mt-0.5">Manage your personal details and contact preferences</p>
            </div>
            <span class="text-[11px] font-medium text-stone-400">Profile ID #{{ $user->id }}</span>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 text-xs">
            <div>
                <label class="block font-semibold text-stone-600 mb-1">Full Name</label>
                <div class="rounded-xl border border-stone-200 bg-stone-50/70 px-3.5 py-2.5 text-stone-800">
                    {{ $user->name ?: ($user->customerProfile->first_name ?? '—') . ' ' . ($user->customerProfile->last_name ?? '') }}
                </div>
            </div>

            <div>
                <label class="block font-semibold text-stone-600 mb-1">Email Address</label>
                <div class="rounded-xl border border-stone-200 bg-stone-50/70 px-3.5 py-2.5 text-stone-800">
                    {{ $user->email }}
                </div>
            </div>

            <div>
                <label class="block font-semibold text-stone-600 mb-1">Mobile Phone</label>
                <div class="rounded-xl border border-stone-200 bg-stone-50/70 px-3.5 py-2.5 text-stone-800">
                    {{ $user->mobile ?: ($user->customerProfile->phone ?? 'Not provided') }}
                </div>
            </div>

            <div>
                <label class="block font-semibold text-stone-600 mb-1">Date of Birth</label>
                <div class="rounded-xl border border-stone-200 bg-stone-50/70 px-3.5 py-2.5 text-stone-800">
                    {{ $user->customerProfile?->date_of_birth ? $user->customerProfile->date_of_birth->format('d M Y') : 'Not specified' }}
                </div>
            </div>

            <div>
                <label class="block font-semibold text-stone-600 mb-1">Gender</label>
                <div class="rounded-xl border border-stone-200 bg-stone-50/70 px-3.5 py-2.5 text-stone-800">
                    {{ ucfirst($user->customerProfile?->gender ?? 'Not specified') }}
                </div>
            </div>

            <div>
                <label class="block font-semibold text-stone-600 mb-1">Account Created</label>
                <div class="rounded-xl border border-stone-200 bg-stone-50/70 px-3.5 py-2.5 text-stone-800">
                    {{ $user->created_at?->format('d M Y, h:i A') ?? '—' }}
                </div>
            </div>
        </div>
    </section>

    <!-- 2. ORDERS SECTION -->
    <section id="orders" class="scroll-mt-24 rounded-2xl border border-stone-200 bg-white p-6 shadow-2xs">
        <div class="flex items-center justify-between border-b border-stone-100 pb-4 mb-5">
            <div>
                <h2 class="text-base font-bold text-stone-900">Order History</h2>
                <p class="text-xs text-stone-500 mt-0.5">Track your orders, view receipts, and monitor delivery timelines</p>
            </div>
            <a href="{{ route('shop') }}" class="text-xs font-semibold text-[#204b34] hover:underline">
                Browse Shop &rarr;
            </a>
        </div>

        @if($user->orders->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-stone-200 text-[10px] font-bold uppercase tracking-wider text-stone-400">
                            <th class="pb-3 pl-1">Order #</th>
                            <th class="pb-3">Date</th>
                            <th class="pb-3">Items</th>
                            <th class="pb-3">Payment</th>
                            <th class="pb-3">Shipment</th>
                            <th class="pb-3">Total</th>
                            <th class="pb-3 text-right pr-1">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($user->orders as $order)
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="py-3.5 pl-1 font-semibold text-stone-900">
                                    <a href="{{ route('account.order', $order) }}" class="hover:text-emerald-700">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="py-3.5 text-stone-500">
                                    {{ $order->created_at?->format('d M Y') }}
                                </td>
                                <td class="py-3.5 text-stone-600">
                                    {{ $order->orderItems->count() }} item(s)
                                </td>
                                <td class="py-3.5">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="py-3.5">
                                    <span class="inline-flex rounded-full bg-stone-100 px-2 py-0.5 text-[10px] font-semibold text-stone-700">
                                        {{ ucfirst(str_replace('_', ' ', $order->shipment_status ?? 'processing')) }}
                                    </span>
                                </td>
                                <td class="py-3.5 font-semibold text-stone-900">
                                    ₹{{ number_format($order->grand_total, 2) }}
                                </td>
                                <td class="py-3.5 text-right pr-1">
                                    <a href="{{ route('account.order', $order) }}" class="inline-flex items-center gap-1 font-semibold text-emerald-700 hover:underline">
                                        <span>View</span>
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-stone-200 p-8 text-center">
                <svg class="mx-auto h-8 w-8 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <p class="mt-3 text-xs font-semibold text-stone-700">No orders placed yet</p>
                <p class="mt-1 text-xs text-stone-400">When you place an order in our store, it will appear here for easy tracking.</p>
                <a href="{{ route('shop') }}" class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white shadow-2xs hover:bg-[#163524] transition">
                    Explore Shop
                </a>
            </div>
        @endif
    </section>

    <!-- 3. SUBSCRIPTIONS SECTION -->
    <section id="subscriptions" class="scroll-mt-24 rounded-2xl border border-stone-200 bg-white p-6 shadow-2xs">
        <div class="flex items-center justify-between border-b border-stone-100 pb-4 mb-5">
            <div>
                <h2 class="text-base font-bold text-stone-900">Subscriptions &amp; Memberships</h2>
                <p class="text-xs text-stone-500 mt-0.5">Manage your recurring deliveries and membership benefits</p>
            </div>
        </div>

        <div class="rounded-xl border border-stone-100 bg-stone-50/70 p-5 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="h-10 w-10 rounded-xl bg-emerald-100 text-[#204b34] flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold text-stone-900">30-Day Gut Reset Programme Access</div>
                    <div class="text-[11px] text-stone-500 mt-0.5">Full daily journey guidance, milestone checklists, and Gut Response Brief</div>
                </div>
            </div>
            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-bold text-[#204b34]">
                Active Access
            </span>
        </div>
    </section>

    <!-- 4. DOWNLOADS SECTION -->
    <section id="downloads" class="scroll-mt-24 rounded-2xl border border-stone-200 bg-white p-6 shadow-2xs">
        <div class="flex items-center justify-between border-b border-stone-100 pb-4 mb-5">
            <div>
                <h2 class="text-base font-bold text-stone-900">Digital Resources &amp; Downloads</h2>
                <p class="text-xs text-stone-500 mt-0.5">Access protocol handbooks, diet templates, and digital materials</p>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="flex items-center justify-between p-4 rounded-xl border border-stone-200 bg-stone-50/50 hover:bg-stone-50 transition">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-lg bg-stone-200 text-stone-600 flex items-center justify-center shrink-0">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-stone-800">30-Day Reset Protocol Guide</div>
                        <div class="text-[10px] text-stone-400 mt-0.5">PDF · Version 2.4 · 1.8 MB</div>
                    </div>
                </div>
                <a href="{{ route('reset.home') }}" class="p-2 text-stone-500 hover:text-stone-900 transition" title="View Guide">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </a>
            </div>

            <div class="flex items-center justify-between p-4 rounded-xl border border-stone-200 bg-stone-50/50 hover:bg-stone-50 transition">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-lg bg-stone-200 text-stone-600 flex items-center justify-center shrink-0">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-stone-800">Personal Gut Response Brief</div>
                        <div class="text-[10px] text-stone-400 mt-0.5">Day 30 Analysis</div>
                    </div>
                </div>
                <a href="{{ route('my-brief') }}" class="p-2 text-stone-500 hover:text-stone-900 transition" title="Open Brief">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- 5. ADDRESSES SECTION -->
    <section id="addresses" class="scroll-mt-24 rounded-2xl border border-stone-200 bg-white p-6 shadow-2xs">
        <div class="flex items-center justify-between border-b border-stone-100 pb-4 mb-5">
            <div>
                <h2 class="text-base font-bold text-stone-900">Saved Addresses</h2>
                <p class="text-xs text-stone-500 mt-0.5">Manage delivery addresses for orders and supplement dispatches</p>
            </div>
        </div>

        @if($user->addresses && $user->addresses->isNotEmpty())
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach($user->addresses as $address)
                    <div class="p-4 rounded-xl border border-stone-200 bg-stone-50/50">
                        <div class="flex items-center justify-between text-xs font-semibold text-stone-800">
                            <span>{{ $address->recipient_name ?? $user->name }}</span>
                            @if($address->is_default)
                                <span class="rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] text-emerald-800">Default</span>
                            @endif
                        </div>
                        <p class="mt-2 text-xs text-stone-600 leading-relaxed">
                            {{ $address->address_line1 }}<br>
                            @if($address->address_line2){{ $address->address_line2 }}<br>@endif
                            {{ $address->city }}, {{ $address->state }} - {{ $address->postal_code }}<br>
                            Phone: {{ $address->phone ?? $user->mobile ?? '—' }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-dashed border-stone-200 p-6 text-center text-xs text-stone-500">
                Addresses provided during checkout will automatically be saved here.
            </div>
        @endif
    </section>

    <!-- 6. PAYMENT METHODS SECTION -->
    <section id="payment-methods" class="scroll-mt-24 rounded-2xl border border-stone-200 bg-white p-6 shadow-2xs">
        <div class="flex items-center justify-between border-b border-stone-100 pb-4 mb-5">
            <div>
                <h2 class="text-base font-bold text-stone-900">Payment Methods</h2>
                <p class="text-xs text-stone-500 mt-0.5">Secure payment methods for express checkout</p>
            </div>
        </div>

        <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-stone-200 text-stone-600 flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-stone-800">Secure Razorpay / UPI Express Checkout</div>
                    <div class="text-[11px] text-stone-500 mt-0.5">UPI, Debit Cards, Credit Cards, and Net Banking</div>
                </div>
            </div>
            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-semibold text-emerald-800 border border-emerald-200">
                Active Gateway
            </span>
        </div>
    </section>

    <!-- 7. NOTIFICATIONS SECTION -->
    <section id="notifications" class="scroll-mt-24 rounded-2xl border border-stone-200 bg-white p-6 shadow-2xs">
        <div class="flex items-center justify-between border-b border-stone-100 pb-4 mb-5">
            <div>
                <h2 class="text-base font-bold text-stone-900">Notification Preferences</h2>
                <p class="text-xs text-stone-500 mt-0.5">Choose how and when Guided Wellness communicates with you</p>
            </div>
        </div>

        <div class="space-y-4 text-xs">
            <div class="flex items-center justify-between p-4 rounded-xl border border-stone-100 bg-stone-50/50">
                <div>
                    <div class="font-semibold text-stone-800">WhatsApp Daily Protocol Reminders</div>
                    <div class="text-[11px] text-stone-500 mt-0.5">Receive daily morning check-in links and capsule reminders on your phone</div>
                </div>
                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-semibold {{ ($user->customerProfile->whatsapp_opt_in ?? false) ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-200 text-stone-600' }}">
                    {{ ($user->customerProfile->whatsapp_opt_in ?? false) ? 'Enabled' : 'Disabled' }}
                </span>
            </div>

            <div class="flex items-center justify-between p-4 rounded-xl border border-stone-100 bg-stone-50/50">
                <div>
                    <div class="font-semibold text-stone-800">Order &amp; Shipping Status Notifications</div>
                    <div class="text-[11px] text-stone-500 mt-0.5">Receive tracking numbers and dispatch confirmations via email</div>
                </div>
                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-semibold text-emerald-800">
                    Always On
                </span>
            </div>
        </div>
    </section>

    <!-- 8. SECURITY SECTION -->
    <section id="security" class="scroll-mt-24 rounded-2xl border border-stone-200 bg-white p-6 shadow-2xs">
        <div class="flex items-center justify-between border-b border-stone-100 pb-4 mb-5">
            <div>
                <h2 class="text-base font-bold text-stone-900">Account Security</h2>
                <p class="text-xs text-stone-500 mt-0.5">Manage passwords, authentication, and login sessions</p>
            </div>
        </div>

        <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-4 flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-stone-800">Password &amp; Authentication</div>
                <div class="text-[11px] text-stone-500 mt-0.5">Last login recorded: {{ $user->last_login_at?->diffForHumans() ?? 'Current session' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">
                    Sign Out All Sessions &rarr;
                </button>
            </form>
        </div>
    </section>

</div>
@endsection
