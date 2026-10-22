<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Gut Reset Admin | Guided Wellness' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Immediately apply saved sidebar state before DOM paint to prevent layout flash (FOUC)
        (function() {
            try {
                if (localStorage.getItem('admin_sidebar_collapsed') === 'true') {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>
    <style>
        /* Smooth transitions without layout jumping (200-250ms duration) */
        .sidebar-transition {
            transition: width 220ms cubic-bezier(0.4, 0, 0.2, 1), padding 220ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        #admin-sidebar {
            height: 100% !important;
            max-height: 100% !important;
            min-height: 0 !important;
            border-right: 1px solid #E5E7EB !important;
            box-shadow: none !important; /* Continuous vertical divider without shadow break */
            box-sizing: border-box !important;
        }
        @media (min-width: 1024px) {
            #admin-sidebar {
                width: 260px !important;
            }
            #header-brand-container {
                width: 260px !important;
            }
        }
        #header-brand-container {
            height: 5rem !important; /* Exactly 80px fixed height */
            border-bottom: 1px solid #E5E7EB !important;
            border-right: 1px solid #E5E7EB !important;
            background-color: #ffffff;
            position: relative !important;
            box-sizing: border-box !important;
            transition: width 220ms cubic-bezier(0.4, 0, 0.2, 1), padding 220ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        #admin-sidebar-nav {
            flex: 1 1 0% !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            scrollbar-width: thin;
            scrollbar-color: #E2E8F0 transparent;
        }
        /* Circular 30px Sidebar Collapse / Expand Toggle Button directly on Divider (Shopify Admin style) */
        #sidebar-collapse-btn {
            position: absolute !important;
            right: -15px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 50 !important;
            width: 30px !important;
            height: 30px !important;
            border-radius: 9999px !important;
            background-color: #ffffff !important;
            border: 1px solid #E5E7EB !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            flex-shrink: 0 !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.04) !important;
            transition: all 220ms cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        #sidebar-collapse-btn:hover {
            background-color: #f9fafb !important;
            border-color: #D1D5DB !important;
            color: #111827 !important;
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.12), 0 1px 2px 0 rgba(0, 0, 0, 0.08) !important;
            transform: translateY(-50%) scale(1.08) !important;
        }
        .sidebar-chevron-icon {
            transition: transform 220ms cubic-bezier(0.4, 0, 0.2, 1) !important;
            transform: rotate(0deg);
        }

        /* Collapsed Sidebar State */
        @media (min-width: 1024px) {
            html.sidebar-collapsed #admin-sidebar {
                width: 72px !important;
            }
            html.sidebar-collapsed #header-brand-container {
                width: 72px !important;
            }
        }
        html.sidebar-collapsed #header-brand-container {
            padding-left: 0 !important;
            padding-right: 0 !important;
            justify-content: center !important;
        }
        html.sidebar-collapsed #brand-block {
            justify-content: center !important;
            width: 100% !important;
            padding: 0 !important;
        }
        html.sidebar-collapsed #brand-name-wrapper {
            display: none !important;
        }
        html.sidebar-collapsed .sidebar-chevron-icon {
            transform: rotate(180deg) !important;
        }
        /* When sidebar is collapsed, force ALL navigation groups and icons to remain visible */
        html.sidebar-collapsed .nav-group-items {
            display: block !important;
        }
        /* Hide section header labels and text in collapsed mode */
        html.sidebar-collapsed .nav-group-header {
            display: none !important;
        }
        html.sidebar-collapsed .sidebar-text {
            display: none !important;
        }
        /* Center navigation icons in collapsed mode */
        html.sidebar-collapsed .nav-item {
            justify-content: center !important;
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        html.sidebar-collapsed .sidebar-profile-card {
            justify-content: center !important;
            padding: 0.375rem !important;
        }
        /* Unified Brand GW Logo Style */
        .brand-gw-logo {
            width: 2.375rem !important; /* 38px */
            height: 2.375rem !important; /* 38px */
            border-radius: 0.75rem !important; /* rounded-xl */
            background-color: #1c3d2c !important;
            color: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: 700 !important;
            font-size: 0.875rem !important;
            line-height: 1 !important;
            flex-shrink: 0 !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.08) !important;
            user-select: none;
            transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        #brand-block:hover .brand-gw-logo {
            background-color: #142e21 !important;
            transform: scale(1.04);
        }
        /* Search bar perfect vertical centering */
        .admin-search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .admin-search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            line-height: 0;
        }
        .admin-search-kbd {
            position: absolute;
            right: 0.625rem;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            line-height: 0;
        }
        @media (min-width: 1024px) {
            #admin-sidebar {
                position: static !important;
                inset: auto !important;
                transform: none !important;
                z-index: 10 !important;
            }
        }
        /* Custom scrollbar for sidebar */
        #admin-sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        #admin-sidebar-nav::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="bg-stone-50 flex flex-col h-screen overflow-hidden text-stone-800 antialiased font-sans">

    @php
        $authUser = Auth::user();
        $isSuperAdmin = $authUser && ($authUser->hasRole('SUPER_ADMIN') || $authUser->id === 1);
        $roleName = $authUser?->roles->first()?->name ?? ($isSuperAdmin ? 'Super Admin' : 'Admin Staff');
    @endphp

    <!-- Top Header (Fixed ~80px Height with subtle 1px #E5E7EB horizontal bottom border) -->
    <header class="w-full h-20 border-b border-[#E5E7EB] bg-white text-stone-600 flex items-center justify-between px-0 select-none relative z-40 shrink-0">
        <!-- Unified Top-Left SaaS Brand & Sidebar Header (Fixed ~80px Height, 1px #E5E7EB border) -->
        <div
            id="header-brand-container"
            class="sidebar-transition h-full flex items-center px-4 border-r border-[#E5E7EB] w-auto lg:w-[260px] shrink-0 overflow-visible relative bg-white"
        >
            <!-- Mobile Drawer Button (Mobile only) -->
            <button
                type="button"
                onclick="toggleMobileSidebar()"
                class="lg:hidden p-1.5 -ml-1 mr-2 rounded-xl text-stone-500 hover:bg-stone-100 hover:text-stone-800 transition"
                title="Toggle Menu"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <!-- Unified Single Branding Block (Vertically Aligned Logo, Title & Subtitle) -->
            <a
                href="{{ route('admin.dashboard') }}"
                id="brand-block"
                onclick="handleBrandClick(event)"
                class="flex items-center gap-3.5 min-w-0 group select-none transition-all duration-200"
                title="Guided Wellness Enterprise Admin"
            >
                <x-gw-logo class="brand-gw-logo shrink-0" />
                <div id="brand-name-wrapper" class="sidebar-text flex flex-col justify-center min-w-0 transition-opacity duration-200">
                    <span class="font-bold text-stone-900 text-[14.5px] tracking-tight leading-snug group-hover:text-emerald-950 transition-colors whitespace-nowrap">Guided Wellness</span>
                    <span class="text-[10px] text-stone-400 font-bold uppercase tracking-wider leading-none mt-1 whitespace-nowrap">Enterprise Admin</span>
                </div>
            </a>

            <!-- Circular 30px Collapse/Expand Toggle Button (Directly on the Vertical Divider - Shopify style) -->
            <button
                type="button"
                id="sidebar-collapse-btn"
                onclick="toggleSidebarCollapse()"
                class="hidden lg:flex items-center justify-center shrink-0 text-stone-500 hover:text-stone-900 focus:outline-none"
                title="Collapse sidebar"
                aria-label="Toggle sidebar navigation"
            >
                <svg class="h-3.5 w-3.5 sidebar-chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        <!-- Right Header Area (Search, Storefront, Notifications, User) -->
        <div class="flex items-center justify-between flex-1 px-4 sm:px-6 min-w-0">
            <!-- Center Search Bar (Stripe / Linear style) -->
            <div class="hidden md:flex items-center flex-1 max-w-md mr-6">
                <div class="admin-search-wrapper relative w-full flex items-center">
                    <div class="admin-search-icon absolute left-3 top-1/2 -translate-y-1/2 flex items-center pointer-events-none text-stone-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input
                        type="text"
                        id="admin-global-search"
                        placeholder="Search orders, customers, products..."
                        class="h-9 w-full rounded-xl border border-stone-200 bg-stone-50/70 pl-9 pr-14 text-xs leading-none text-stone-800 placeholder-stone-400 outline-none transition focus:border-[#204b34] focus:bg-white focus:ring-2 focus:ring-[#204b34]/15"
                    >
                    <div class="admin-search-kbd absolute right-2.5 top-1/2 -translate-y-1/2 flex items-center pointer-events-none">
                        <kbd class="inline-flex items-center justify-center rounded-md border border-stone-200 bg-white px-1.5 py-0.5 text-[10px] font-medium text-stone-400 shadow-2xs leading-none select-none">⌘K</kbd>
                    </div>
                </div>
            </div>

            <!-- Right Header Actions -->
            <div class="flex items-center gap-3 ml-auto">
                <!-- View Live Store -->
                <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-stone-600 hover:text-stone-900 hover:bg-stone-50 transition shadow-2xs">
                    <span>Storefront</span>
                    <svg class="h-3 w-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>

                <!-- Notification Bell -->
                <button type="button" class="relative rounded-xl p-2 text-stone-400 hover:bg-stone-100 hover:text-stone-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute top-1.5 right-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                </button>

                <!-- User Menu Dropdown -->
                <details class="group relative flex items-center">
                    <summary class="flex cursor-pointer list-none items-center gap-2 rounded-xl border border-stone-200 bg-white p-1.5 sm:px-3 sm:py-1.5 text-xs font-medium text-stone-700 shadow-2xs hover:bg-stone-50 transition select-none [&::-webkit-details-marker]:hidden">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                            {{ strtoupper(substr($authUser->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="hidden md:inline font-semibold text-stone-800">{{ $authUser->name ?? 'Admin' }}</span>
                        <svg class="h-3 w-3 text-stone-400 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                    </summary>
                    <div class="absolute right-0 top-full z-50 mt-2 w-56 rounded-2xl border border-stone-200 bg-white p-2 shadow-xl ring-1 ring-black/5">
                        <div class="border-b border-stone-100 px-3 py-2.5 mb-1 pb-3">
                            <div class="font-semibold text-xs text-stone-900 truncate">{{ $authUser->name ?? 'Administrator' }}</div>
                            <div class="text-[11px] text-stone-400 truncate mt-0.5">{{ $authUser->email ?? '' }}</div>
                            <div class="mt-2 inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[10px] font-semibold text-emerald-800 uppercase tracking-wider">
                                {{ $roleName }}
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="pt-1">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left text-xs font-medium text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                <svg class="h-3.5 w-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </details>
            </div>
        </div>
    </header>

    <!-- Main Container: Sidebar + Content -->
    <div class="flex min-h-0 flex-1 overflow-hidden relative">

        <!-- Mobile Backdrop -->
        <div id="mobile-sidebar-backdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-xs hidden lg:hidden"></div>

        <!-- Modern Enterprise SaaS Sidebar -->
        <aside
            id="admin-sidebar"
            class="sidebar-transition fixed inset-y-0 left-0 z-50 -translate-x-full lg:translate-x-0 flex flex-col bg-white border-r border-[#E5E7EB] lg:static lg:z-auto w-[260px] shrink-0 h-full max-h-full min-h-0"
        >
            <!-- Scrollable Navigation Tree (Starts immediately below header) -->
            <nav id="admin-sidebar-nav" class="flex-1 overflow-y-auto px-3 pt-3 pb-8 space-y-3">

                <!-- 1. GENERAL GROUP -->
                @php
                    $isDashActive = request()->routeIs('admin.dashboard');
                @endphp
                <div class="nav-group" data-group="general">
                    <button
                        type="button"
                        onclick="toggleNavGroup('general')"
                        class="nav-group-header w-full flex items-center justify-between px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-400 hover:text-stone-600 transition"
                    >
                        <span class="sidebar-text">General</span>
                        <svg class="nav-group-chevron h-3 w-3 sidebar-text transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="nav-group-items space-y-1 mt-1">
                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isDashActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}"
                            data-title="Dashboard"
                        >
                            @if($isDashActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isDashActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span class="sidebar-text truncate">Dashboard</span>
                        </a>
                    </div>
                </div>

                <!-- 2. STORE GROUP -->
                @php
                    $isStoreActive = request()->routeIs('admin.products.*') || request()->routeIs('admin.orders.*') || request()->routeIs('admin.customers.*') || request()->routeIs('admin.inventory.*');
                @endphp
                <div class="nav-group" data-group="store">
                    <button
                        type="button"
                        onclick="toggleNavGroup('store')"
                        class="nav-group-header w-full flex items-center justify-between px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-400 hover:text-stone-600 transition"
                    >
                        <span class="sidebar-text">Store</span>
                        <svg class="nav-group-chevron h-3 w-3 sidebar-text transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="nav-group-items space-y-1 mt-1">
                        <!-- Products -->
                        @php $isActive = request()->routeIs('admin.products.*'); @endphp
                        <a href="{{ route('admin.products.index') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Products">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span class="sidebar-text truncate">Products</span>
                        </a>

                        <!-- Orders -->
                        @php $isActive = request()->routeIs('admin.orders.*'); @endphp
                        <a href="{{ route('admin.orders.index') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Orders">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span class="sidebar-text truncate">Orders</span>
                        </a>

                        <!-- Customers -->
                        @php $isActive = request()->routeIs('admin.customers.*'); @endphp
                        <a href="{{ route('admin.customers.index') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Customers">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="sidebar-text truncate">Customers</span>
                        </a>

                        <!-- Inventory -->
                        @php $isActive = request()->routeIs('admin.inventory.*'); @endphp
                        <a href="{{ route('admin.inventory.index') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Inventory">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span class="sidebar-text truncate">Inventory</span>
                        </a>
                    </div>
                </div>

                <!-- 3. OPERATIONS GROUP -->
                @php
                    $isOpsActive = request()->routeIs('admin.shipping.*') || request()->routeIs('admin.payments.*') || request()->routeIs('admin.fulfilment.*') || request()->routeIs('admin.reset.*');
                @endphp
                <div class="nav-group" data-group="operations">
                    <button
                        type="button"
                        onclick="toggleNavGroup('operations')"
                        class="nav-group-header w-full flex items-center justify-between px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-400 hover:text-stone-600 transition"
                    >
                        <span class="sidebar-text">Operations</span>
                        <svg class="nav-group-chevron h-3 w-3 sidebar-text transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="nav-group-items space-y-1 mt-1">
                        <!-- Shipping -->
                        @php $isActive = request()->routeIs('admin.shipping.*'); @endphp
                        <a href="{{ route('admin.shipping.index') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Shipping">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            <span class="sidebar-text truncate">Shipping</span>
                        </a>

                        <!-- Payments -->
                        @php $isActive = request()->routeIs('admin.payments.*'); @endphp
                        <a href="{{ route('admin.payments.index') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Payments">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span class="sidebar-text truncate">Payments</span>
                        </a>

                        <!-- Fulfillment -->
                        @php $isActive = request()->routeIs('admin.fulfilment.*'); @endphp
                        <a href="{{ route('admin.fulfilment.index') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Fulfillment">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span class="sidebar-text truncate">Fulfillment</span>
                        </a>

                        <!-- Day 0-30 (Preserved Reset Operations) -->
                        @php $isActive = request()->routeIs('admin.reset.*'); @endphp
                        <a href="{{ route('admin.reset.operations') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Day 0–30">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="sidebar-text truncate">Day 0–30</span>
                        </a>
                    </div>
                </div>

                <!-- 4. ANALYTICS GROUP -->
                @php
                    $isAnalyticsActive = request()->routeIs('admin.analytics.*');
                @endphp
                <div class="nav-group" data-group="analytics">
                    <button
                        type="button"
                        onclick="toggleNavGroup('analytics')"
                        class="nav-group-header w-full flex items-center justify-between px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-400 hover:text-stone-600 transition"
                    >
                        <span class="sidebar-text">Analytics</span>
                        <svg class="nav-group-chevron h-3 w-3 sidebar-text transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="nav-group-items space-y-1 mt-1">
                        <!-- Business Overview -->
                        @php $isActive = request()->routeIs('admin.analytics.overview'); @endphp
                        <a href="{{ route('admin.analytics.overview') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Business Overview">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span class="sidebar-text truncate">Business Overview</span>
                        </a>

                        <!-- Sales Analytics -->
                        @php $isActive = request()->routeIs('admin.analytics.sales'); @endphp
                        <a href="{{ route('admin.analytics.sales') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Sales Analytics">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <span class="sidebar-text truncate">Sales Analytics</span>
                        </a>

                        <!-- Product Analytics -->
                        @php $isActive = request()->routeIs('admin.analytics.products*'); @endphp
                        <a href="{{ route('admin.analytics.products') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Product Analytics">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="sidebar-text truncate">Product Analytics</span>
                        </a>

                        <!-- Customer Analytics -->
                        @php $isActive = request()->routeIs('admin.analytics.customers'); @endphp
                        <a href="{{ route('admin.analytics.customers') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Customer Analytics">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span class="sidebar-text truncate">Customer Analytics</span>
                        </a>

                        <!-- Marketing Analytics -->
                        @php $isActive = request()->routeIs('admin.analytics.marketing'); @endphp
                        <a href="{{ route('admin.analytics.marketing') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Marketing Analytics">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                            <span class="sidebar-text truncate">Marketing Analytics</span>
                        </a>

                        <!-- Location & Heatmap (from AddressAnalytics) -->
                        @php $isActive = request()->routeIs('admin.analytics.shipping-locations*'); @endphp
                        <a href="{{ route('admin.analytics.shipping-locations') }}" target="_blank" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Shipping Locations API">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="sidebar-text truncate">Geo Heatmap Feed</span>
                        </a>
                    </div>
                </div>

                <!-- 5. MARKETING GROUP -->
                @php
                    $isMktActive = request()->routeIs('admin.coupons.*') || request()->routeIs('admin.whatsapp.*') || request()->routeIs('admin.marketing.*');
                @endphp
                <div class="nav-group" data-group="marketing">
                    <button
                        type="button"
                        onclick="toggleNavGroup('marketing')"
                        class="nav-group-header w-full flex items-center justify-between px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-400 hover:text-stone-600 transition"
                    >
                        <span class="sidebar-text">Marketing</span>
                        <svg class="nav-group-chevron h-3 w-3 sidebar-text transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="nav-group-items space-y-1 mt-1">
                        <!-- Coupons -->
                        @php $isActive = request()->routeIs('admin.coupons.*'); @endphp
                        <a href="{{ route('admin.coupons.index') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Coupons">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <span class="sidebar-text truncate">Coupons</span>
                        </a>

                        <!-- WhatsApp -->
                        @php $isActive = request()->routeIs('admin.whatsapp.*'); @endphp
                        <a href="{{ route('admin.whatsapp.dashboard') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="WhatsApp">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span class="sidebar-text truncate">WhatsApp</span>
                        </a>

                        <!-- Campaigns -->
                        @php $isActive = request()->routeIs('admin.marketing.campaigns'); @endphp
                        <a href="{{ route('admin.marketing.campaigns') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Campaigns">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            <span class="sidebar-text truncate">Campaigns</span>
                        </a>
                    </div>
                </div>

                <!-- 6. CONTENT GROUP -->
                @php
                    $isContentActive = request()->routeIs('admin.cms.*') || request()->routeIs('admin.media.*');
                @endphp
                <div class="nav-group" data-group="content">
                    <button
                        type="button"
                        onclick="toggleNavGroup('content')"
                        class="nav-group-header w-full flex items-center justify-between px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-400 hover:text-stone-600 transition"
                    >
                        <span class="sidebar-text">Content</span>
                        <svg class="nav-group-chevron h-3 w-3 sidebar-text transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="nav-group-items space-y-1 mt-1">
                        <!-- CMS -->
                        @php $isActive = request()->routeIs('admin.cms.*'); @endphp
                        <a href="{{ route('admin.cms.index') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="CMS">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            <span class="sidebar-text truncate">CMS</span>
                        </a>

                        <!-- Media Library -->
                        @php $isActive = request()->routeIs('admin.media.*'); @endphp
                        <a href="{{ route('admin.media.index') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Media Library">
                            @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                            <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="sidebar-text truncate">Media Library</span>
                        </a>
                    </div>
                </div>

                <!-- 7. SETTINGS GROUP (Only visible if user has SUPER_ADMIN role or is Admin) -->
                @if($isSuperAdmin || $authUser->hasPermission('VIEW_SETTINGS'))
                    @php
                        $isSettingsActive = request()->routeIs('admin.settings.*');
                    @endphp
                    <div class="nav-group" data-group="settings">
                        <button
                            type="button"
                            onclick="toggleNavGroup('settings')"
                            class="nav-group-header w-full flex items-center justify-between px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-400 hover:text-stone-600 transition"
                        >
                            <span class="sidebar-text">Settings</span>
                            <svg class="nav-group-chevron h-3 w-3 sidebar-text transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="nav-group-items space-y-1 mt-1">
                            <!-- Users -->
                            @php $isActive = request()->routeIs('admin.settings.users'); @endphp
                            <a href="{{ route('admin.settings.users') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Users">
                                @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                                <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <span class="sidebar-text truncate">Users</span>
                            </a>

                            <!-- Roles -->
                            @php $isActive = request()->routeIs('admin.settings.roles'); @endphp
                            <a href="{{ route('admin.settings.roles') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="Roles & Permissions">
                                @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                                <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span class="sidebar-text truncate">Roles</span>
                            </a>

                            <!-- System Settings -->
                            @php $isActive = request()->routeIs('admin.settings.system'); @endphp
                            <a href="{{ route('admin.settings.system') }}" class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition {{ $isActive ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}" data-title="System Settings">
                                @if($isActive)<span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-[#16A34A] rounded-r-sm"></span>@endif
                                <svg class="h-4 w-4 shrink-0 {{ $isActive ? 'text-[#16A34A]' : 'text-stone-400 group-hover:text-stone-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="sidebar-text truncate">System Settings</span>
                            </a>
                        </div>
                    </div>
                @endif

            </nav>

            <!-- Bottom User Profile Pill in Sidebar (Linear / Notion style) -->
            <div class="p-3 border-t border-stone-100 shrink-0">
                <div class="sidebar-profile-card flex items-center justify-between rounded-xl p-2 bg-stone-50/70 border border-stone-100 text-xs">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="h-7 w-7 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($authUser->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="sidebar-text min-w-0 truncate">
                            <div class="font-semibold text-stone-800 truncate text-[11px]">{{ $authUser->name ?? 'Admin' }}</div>
                            <div class="text-[10px] text-stone-400 truncate">{{ $roleName }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Floating Tooltip Container for Collapsed Mode -->
        <div id="sidebar-tooltip" class="fixed hidden z-50 rounded-lg bg-stone-900 px-2.5 py-1 text-[11px] font-medium text-white shadow-xl pointer-events-none transition-opacity duration-150">
            Tooltip
        </div>

        <!-- Main Content Area -->
        <main id="admin-content" class="min-w-0 flex-1 overflow-y-auto bg-stone-50">
            <div class="min-h-0 flex-1">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Interactive Navigation Scripts -->
    <script>
        const sidebar = document.getElementById('admin-sidebar');
        const tooltip = document.getElementById('sidebar-tooltip');
        const brandContainer = document.getElementById('header-brand-container');
        const brandBlock = document.getElementById('brand-block');
        const collapseBtn = document.getElementById('sidebar-collapse-btn');

        // Check if sidebar is currently in collapsed state
        function isCollapsed() {
            return document.documentElement.classList.contains('sidebar-collapsed');
        }

        // Handle clicking the brand logo in collapsed mode (expands sidebar)
        function handleBrandClick(e) {
            if (isCollapsed()) {
                e.preventDefault();
                toggleSidebarCollapse();
            }
        }

        // Toggle Sidebar Collapse (260px <-> 72px) with smooth 220ms ease-in-out
        function toggleSidebarCollapse() {
            const willCollapse = !isCollapsed();
            if (willCollapse) {
                document.documentElement.classList.add('sidebar-collapsed');
                sidebar.classList.remove('w-[260px]');
                sidebar.classList.add('w-[72px]');
                if (brandContainer) {
                    brandContainer.classList.remove('lg:w-[260px]', 'px-4');
                    brandContainer.classList.add('lg:w-[72px]', 'px-0', 'justify-center');
                }
                if (collapseBtn) {
                    collapseBtn.setAttribute('title', 'Expand sidebar');
                    collapseBtn.setAttribute('aria-label', 'Expand sidebar navigation');
                }
                localStorage.setItem('admin_sidebar_collapsed', 'true');
            } else {
                document.documentElement.classList.remove('sidebar-collapsed');
                sidebar.classList.remove('w-[72px]');
                sidebar.classList.add('w-[260px]');
                if (brandContainer) {
                    brandContainer.classList.remove('lg:w-[72px]', 'px-0', 'justify-center');
                    brandContainer.classList.add('lg:w-[260px]', 'px-4');
                }
                if (collapseBtn) {
                    collapseBtn.setAttribute('title', 'Collapse sidebar');
                    collapseBtn.setAttribute('aria-label', 'Collapse sidebar navigation');
                }
                localStorage.setItem('admin_sidebar_collapsed', 'false');
            }
            updateSidebarTextVisibility();
        }

        // Toggle Expandable Group
        function toggleNavGroup(groupKey) {
            if (isCollapsed()) return;
            const groupEl = document.querySelector(`.nav-group[data-group="${groupKey}"]`);
            if (!groupEl) return;
            const items = groupEl.querySelector('.nav-group-items');
            const chevron = groupEl.querySelector('.nav-group-chevron');
            
            const isCurrentlyOpen = items.style.display !== 'none';
            if (isCurrentlyOpen) {
                items.style.display = 'none';
                if (chevron) chevron.classList.add('-rotate-90');
                localStorage.setItem(`admin_nav_group_${groupKey}`, 'closed');
            } else {
                items.style.display = 'block';
                if (chevron) chevron.classList.remove('-rotate-90');
                localStorage.setItem(`admin_nav_group_${groupKey}`, 'open');
            }
        }

        // Update Text Visibility on Collapse/Expand
        function updateSidebarTextVisibility() {
            const texts = document.querySelectorAll('.sidebar-text');
            const collapsed = isCollapsed();
            texts.forEach(el => {
                if (collapsed) {
                    el.classList.add('hidden');
                } else {
                    el.classList.remove('hidden');
                }
            });
        }

        // Mobile Drawer Toggle
        function toggleMobileSidebar() {
            const isMobileOpen = sidebar.classList.contains('translate-x-0');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            if (isMobileOpen) {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            } else {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                backdrop.classList.remove('hidden');
            }
        }

        // Tooltip handlers for icon-only collapsed mode
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('mouseenter', (e) => {
                if (!isCollapsed()) return;
                const title = item.getAttribute('data-title');
                if (!title) return;
                tooltip.textContent = title;
                const rect = item.getBoundingClientRect();
                tooltip.style.top = `${rect.top + (rect.height / 2) - 12}px`;
                tooltip.style.left = `80px`;
                tooltip.classList.remove('hidden');
            });

            item.addEventListener('mouseleave', () => {
                tooltip.classList.add('hidden');
            });
        });

        // Tooltip for top brand block in collapsed mode
        if (brandBlock) {
            brandBlock.addEventListener('mouseenter', () => {
                if (!isCollapsed()) return;
                tooltip.textContent = 'Guided Wellness (Click to expand)';
                const rect = brandBlock.getBoundingClientRect();
                tooltip.style.top = `${rect.top + (rect.height / 2) - 12}px`;
                tooltip.style.left = `80px`;
                tooltip.classList.remove('hidden');
            });

            brandBlock.addEventListener('mouseleave', () => {
                tooltip.classList.add('hidden');
            });
        }

        // Hide tooltip while scrolling nav
        const sidebarNav = document.getElementById('admin-sidebar-nav');
        if (sidebarNav) {
            sidebarNav.addEventListener('scroll', () => {
                if (tooltip) tooltip.classList.add('hidden');
            });
        }

        // Initialize state on DOM ready
        document.addEventListener('DOMContentLoaded', () => {
            // Apply saved sidebar width state
            if (localStorage.getItem('admin_sidebar_collapsed') === 'true') {
                sidebar.classList.remove('w-[260px]');
                sidebar.classList.add('w-[72px]');
                if (brandContainer) {
                    brandContainer.classList.remove('lg:w-[260px]', 'px-4');
                    brandContainer.classList.add('lg:w-[72px]', 'px-0', 'justify-center');
                }
                if (collapseBtn) {
                    collapseBtn.setAttribute('title', 'Expand sidebar');
                    collapseBtn.setAttribute('aria-label', 'Expand sidebar navigation');
                }
            } else {
                sidebar.classList.add('w-[260px]');
                sidebar.classList.remove('w-[72px]');
                if (brandContainer) {
                    brandContainer.classList.add('lg:w-[260px]', 'px-4');
                    brandContainer.classList.remove('lg:w-[72px]', 'px-0', 'justify-center');
                }
                if (collapseBtn) {
                    collapseBtn.setAttribute('title', 'Collapse sidebar');
                    collapseBtn.setAttribute('aria-label', 'Collapse sidebar navigation');
                }
            }
            updateSidebarTextVisibility();

            // Initialize accordion groups (keep open if child is active or default open)
            document.querySelectorAll('.nav-group').forEach(group => {
                const groupKey = group.getAttribute('data-group');
                const hasActive = group.querySelector('.bg-emerald-50');
                const savedState = localStorage.getItem(`admin_nav_group_${groupKey}`);

                if (savedState === 'closed' && !hasActive) {
                    const items = group.querySelector('.nav-group-items');
                    const chevron = group.querySelector('.nav-group-chevron');
                    if (items) items.style.display = 'none';
                    if (chevron) chevron.classList.add('-rotate-90');
                }
            });

            // Global search keyboard shortcut (Cmd+K / Ctrl+K)
            window.addEventListener('keydown', (e) => {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    document.getElementById('admin-global-search')?.focus();
                }
            });
        });
    </script>
</body>
</html>
