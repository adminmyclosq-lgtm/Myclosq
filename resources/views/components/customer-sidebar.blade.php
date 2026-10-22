@php
    $authUser = auth()->user();
    $profile = $profile ?? ($authUser ? app(\App\Services\ResetJourneyService::class)->activeProfile($authUser) ?? app(\App\Services\ResetJourneyService::class)->latestProfile($authUser) : null);
    $currentDay = $currentDay ?? ($profile?->status === 'completed' ? 30 : (int) ($profile?->current_reset_day ?? 0));
    $userName = $authUser?->name ?: ($authUser?->customerProfile?->first_name ?: $authUser?->email ?: 'Customer');
    $userInitial = strtoupper(substr($userName, 0, 1));
    $orderCount = (int) ($authUser?->orders()->count() ?? 0);
@endphp

<!-- Mobile Backdrop -->
<div id="customer-mobile-sidebar-backdrop" onclick="toggleCustomerMobileSidebar()" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-xs hidden lg:hidden"></div>

<!-- Modern Customer SaaS Sidebar (Matches Admin Sidebar Exact Proportions & Quality) -->
<aside
    id="customer-sidebar"
    class="sidebar-transition fixed inset-y-0 left-0 z-50 -translate-x-full lg:translate-x-0 flex flex-col bg-white border-r border-[#E5E7EB] lg:static lg:z-auto w-[260px] shrink-0 h-full max-h-full min-h-0 select-none"
>
    <!-- Scrollable Navigation Tree (Starts immediately below header) -->
    <nav id="customer-sidebar-nav" class="flex-1 overflow-y-auto px-3 pt-3 pb-8 space-y-4">

        <!-- 1. PROGRAM GROUP -->
        <div class="nav-group" data-group="program">
            <div class="nav-group-header px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-400">
                <span class="sidebar-text">PROGRAM</span>
            </div>
            <div class="nav-group-items space-y-1 mt-1">
                <!-- Dashboard -->
                @php $isDashActive = request()->routeIs('reset.home') && !str_contains(request()->fullUrl(), '#'); @endphp
                <a
                    href="{{ route('reset.home') }}"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isDashActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}"
                    data-title="Dashboard"
                >
                    @if($isDashActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                    <svg class="h-4 w-4 shrink-0 {{ $isDashActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="sidebar-text truncate">Dashboard</span>
                </a>

                <!-- My Journey -->
                @php $isJourneyActive = request()->routeIs('reset.home') && str_contains(request()->fullUrl(), '#journey'); @endphp
                <a
                    href="{{ route('reset.home') }}"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isJourneyActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}"
                    data-title="My Journey"
                >
                    @if($isJourneyActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                    <svg class="h-4 w-4 shrink-0 {{ $isJourneyActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <span class="sidebar-text truncate">My Journey</span>
                    @if($currentDay > 0 && $currentDay < 30)
                        <span class="sidebar-text ml-auto px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-[#204b34]">Day {{ $currentDay }}</span>
                    @endif
                </a>

                <!-- My Plan -->
                @php $isPlanActive = request()->routeIs('my-plan') || request()->routeIs('reset.day0*'); @endphp
                <a
                    href="{{ route('my-plan') }}"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isPlanActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}"
                    data-title="My Plan"
                >
                    @if($isPlanActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                    <svg class="h-4 w-4 shrink-0 {{ $isPlanActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="sidebar-text truncate">My Plan</span>
                </a>

                <!-- Daily Tasks -->
                @php
                    $isTaskActive = request()->routeIs('reset.day');
                    $taskRoute = ($currentDay > 0 && $currentDay <= 30) ? route('reset.day', $currentDay) : route('reset.day0');
                @endphp
                <a
                    href="{{ $taskRoute }}"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isTaskActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}"
                    data-title="Daily Tasks"
                >
                    @if($isTaskActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                    <svg class="h-4 w-4 shrink-0 {{ $isTaskActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="sidebar-text truncate">Daily Tasks</span>
                    @if($currentDay > 0 && $currentDay <= 30)
                        <span class="sidebar-text ml-auto inline-flex items-center justify-center w-2 h-2 rounded-full bg-emerald-500" title="Active Check-in"></span>
                    @endif
                </a>

                <!-- Progress -->
                @php $isProgressActive = request()->routeIs('reset.home') && str_contains(request()->fullUrl(), '#progress'); @endphp
                <a
                    href="{{ route('reset.home') }}#progress"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isProgressActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}"
                    data-title="Progress"
                >
                    @if($isProgressActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                    <svg class="h-4 w-4 shrink-0 {{ $isProgressActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="sidebar-text truncate">Progress</span>
                </a>

                <!-- My Brief -->
                @php $isBriefActive = request()->routeIs('my-brief') || request()->routeIs('reset.report*') || request()->routeIs('report'); @endphp
                <a
                    href="{{ route('my-brief') }}"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isBriefActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}"
                    data-title="My Brief"
                >
                    @if($isBriefActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                    <svg class="h-4 w-4 shrink-0 {{ $isBriefActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="sidebar-text truncate">My Brief</span>
                    @if($profile?->day30Decision || $profile?->status === 'completed')
                        <span class="sidebar-text ml-auto px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-[#204b34]">Ready</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- 2. SHOP GROUP -->
        <div class="nav-group" data-group="shop">
            <div class="nav-group-header px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-400">
                <span class="sidebar-text">SHOP</span>
            </div>
            <div class="nav-group-items space-y-1 mt-1">
                <!-- Shop Storefront -->
                @php $isShopActive = request()->routeIs('shop*') || request()->routeIs('product.*'); @endphp
                <a
                    href="{{ route('shop') }}"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isShopActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}"
                    data-title="Shop"
                >
                    @if($isShopActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                    <svg class="h-4 w-4 shrink-0 {{ $isShopActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span class="sidebar-text truncate">Shop</span>
                </a>

                <!-- Orders -->
                @php $isOrderActive = request()->routeIs('account.order*') || (request()->routeIs('account') && str_contains(request()->fullUrl(), 'order')); @endphp
                <a
                    href="{{ route('account') }}#orders"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isOrderActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}"
                    data-title="Orders"
                >
                    @if($isOrderActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                    <svg class="h-4 w-4 shrink-0 {{ $isOrderActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span class="sidebar-text truncate">Orders</span>
                    @if($orderCount > 0)
                        <span class="sidebar-text ml-auto px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-stone-100 text-stone-600">{{ $orderCount }}</span>
                    @endif
                </a>

                <!-- Subscriptions -->
                <a
                    href="{{ route('account') }}#subscriptions"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition text-stone-600 hover:bg-stone-50 hover:text-stone-900"
                    data-title="Subscriptions"
                >
                    <svg class="h-4 w-4 shrink-0 text-stone-400 group-hover:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span class="sidebar-text truncate">Subscriptions</span>
                </a>

                <!-- Downloads -->
                <a
                    href="{{ route('account') }}#downloads"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition text-stone-600 hover:bg-stone-50 hover:text-stone-900"
                    data-title="Downloads"
                >
                    <svg class="h-4 w-4 shrink-0 text-stone-400 group-hover:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span class="sidebar-text truncate">Downloads</span>
                </a>
            </div>
        </div>

        <!-- 3. ACCOUNT GROUP -->
        <div class="nav-group" data-group="account">
            <div class="nav-group-header px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-400">
                <span class="sidebar-text">ACCOUNT</span>
            </div>
            <div class="nav-group-items space-y-1 mt-1">
                <!-- Profile -->
                @php $isAccountActive = request()->routeIs('account') && !str_contains(request()->fullUrl(), '#'); @endphp
                <a
                    href="{{ route('account') }}"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isAccountActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}"
                    data-title="Profile"
                >
                    @if($isAccountActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                    <svg class="h-4 w-4 shrink-0 {{ $isAccountActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="sidebar-text truncate">Profile</span>
                </a>

                <!-- Addresses -->
                <a
                    href="{{ route('account') }}#addresses"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition text-stone-600 hover:bg-stone-50 hover:text-stone-900"
                    data-title="Addresses"
                >
                    <svg class="h-4 w-4 shrink-0 text-stone-400 group-hover:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="sidebar-text truncate">Addresses</span>
                </a>

                <!-- Payment Methods -->
                <a
                    href="{{ route('account') }}#payment-methods"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition text-stone-600 hover:bg-stone-50 hover:text-stone-900"
                    data-title="Payment Methods"
                >
                    <svg class="h-4 w-4 shrink-0 text-stone-400 group-hover:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span class="sidebar-text truncate">Payment Methods</span>
                </a>

                <!-- Notifications -->
                <a
                    href="{{ route('account') }}#notifications"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition text-stone-600 hover:bg-stone-50 hover:text-stone-900"
                    data-title="Notifications"
                >
                    <svg class="h-4 w-4 shrink-0 text-stone-400 group-hover:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="sidebar-text truncate">Notifications</span>
                </a>

                <!-- Security -->
                <a
                    href="{{ route('account') }}#security"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition text-stone-600 hover:bg-stone-50 hover:text-stone-900"
                    data-title="Security"
                >
                    <svg class="h-4 w-4 shrink-0 text-stone-400 group-hover:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="sidebar-text truncate">Security</span>
                </a>
            </div>
        </div>

        <!-- 4. SUPPORT GROUP -->
        <div class="nav-group" data-group="support">
            <div class="nav-group-header px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-400">
                <span class="sidebar-text">SUPPORT</span>
            </div>
            <div class="nav-group-items space-y-1 mt-1">
                <!-- Help Center -->
                <a
                    href="{{ route('support') }}"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition text-stone-600 hover:bg-stone-50 hover:text-stone-900"
                    data-title="Help Center"
                >
                    <svg class="h-4 w-4 shrink-0 text-stone-400 group-hover:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="sidebar-text truncate">Help Center</span>
                </a>

                <!-- Contact Support -->
                <a
                    href="{{ route('support') }}#contact"
                    class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition text-stone-600 hover:bg-stone-50 hover:text-stone-900"
                    data-title="Contact Support"
                >
                    <svg class="h-4 w-4 shrink-0 text-stone-400 group-hover:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span class="sidebar-text truncate">Contact Support</span>
                </a>
            </div>
        </div>

    </nav>

    <!-- Bottom User Profile Pill in Sidebar (Linear / Notion style, Identical to Admin) -->
    <div class="p-3 border-t border-stone-100 shrink-0">
        <a href="{{ route('account') }}" class="sidebar-profile-card flex items-center justify-between rounded-xl p-2 bg-stone-50/70 border border-stone-100 text-xs hover:bg-stone-100 transition">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="h-7 w-7 rounded-full bg-emerald-100 text-[#204b34] text-[11px] font-bold flex items-center justify-center shrink-0">
                    {{ $userInitial }}
                </div>
                <div class="sidebar-text min-w-0 truncate">
                    <div class="font-semibold text-stone-800 truncate text-[11px]">{{ $userName }}</div>
                    <div class="text-[10px] text-stone-400 truncate">Customer Portal</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="sidebar-text ml-auto" onclick="event.stopPropagation()">
                @csrf
                <button type="submit" class="p-1 text-stone-400 hover:text-rose-600 rounded transition" title="Sign out">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </a>
    </div>
</aside>
