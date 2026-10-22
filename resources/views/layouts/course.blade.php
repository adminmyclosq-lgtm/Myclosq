<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Customer Portal | Guided Wellness' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Immediately apply saved sidebar state before DOM paint to prevent layout flash (FOUC)
        (function() {
            try {
                if (localStorage.getItem('customer_sidebar_collapsed') === 'true') {
                    document.documentElement.classList.add('customer-sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>
    <style>
        /* Smooth transitions without layout jumping (200-250ms duration) */
        .sidebar-transition {
            transition: width 220ms cubic-bezier(0.4, 0, 0.2, 1), padding 220ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        #customer-sidebar {
            height: 100% !important;
            max-height: 100% !important;
            min-height: 0 !important;
            border-right: 1px solid #E5E7EB !important;
            box-shadow: none !important;
            box-sizing: border-box !important;
        }
        @media (min-width: 1024px) {
            #customer-sidebar {
                width: 260px !important;
            }
            #customer-header-brand-container {
                width: 260px !important;
            }
        }
        #customer-header-brand-container {
            height: 5rem !important; /* Exactly 80px fixed height */
            border-bottom: 1px solid #E5E7EB !important;
            border-right: 1px solid #E5E7EB !important;
            background-color: #ffffff;
            position: relative !important;
            box-sizing: border-box !important;
            transition: width 220ms cubic-bezier(0.4, 0, 0.2, 1), padding 220ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        #customer-sidebar-nav {
            flex: 1 1 0% !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            scrollbar-width: thin;
            scrollbar-color: #E2E8F0 transparent;
        }
        /* Circular 30px Sidebar Collapse / Expand Toggle Button directly on Divider (Shopify Admin style) */
        #customer-sidebar-collapse-btn {
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
        #customer-sidebar-collapse-btn:hover {
            background-color: #f9fafb !important;
            border-color: #D1D5DB !important;
            color: #111827 !important;
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.12), 0 1px 2px 0 rgba(0, 0, 0, 0.08) !important;
            transform: translateY(-50%) scale(1.08) !important;
        }
        .customer-sidebar-chevron-icon {
            transition: transform 220ms cubic-bezier(0.4, 0, 0.2, 1) !important;
            transform: rotate(0deg);
        }

        /* Collapsed Sidebar State */
        @media (min-width: 1024px) {
            html.customer-sidebar-collapsed #customer-sidebar {
                width: 72px !important;
            }
            html.customer-sidebar-collapsed #customer-header-brand-container {
                width: 72px !important;
            }
        }
        html.customer-sidebar-collapsed #customer-header-brand-container {
            padding-left: 0 !important;
            padding-right: 0 !important;
            justify-content: center !important;
        }
        html.customer-sidebar-collapsed #customer-brand-block {
            justify-content: center !important;
            width: 100% !important;
            padding: 0 !important;
        }
        html.customer-sidebar-collapsed #customer-brand-name-wrapper {
            display: none !important;
        }
        html.customer-sidebar-collapsed .customer-sidebar-chevron-icon {
            transform: rotate(180deg) !important;
        }
        /* When sidebar is collapsed, force ALL navigation items to remain visible */
        html.customer-sidebar-collapsed .nav-group-items {
            display: block !important;
        }
        /* Hide section header labels and text in collapsed mode */
        html.customer-sidebar-collapsed .nav-group-header {
            display: none !important;
        }
        html.customer-sidebar-collapsed .sidebar-text {
            display: none !important;
        }
        /* Center navigation icons in collapsed mode */
        html.customer-sidebar-collapsed .nav-item {
            justify-content: center !important;
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        html.customer-sidebar-collapsed .sidebar-profile-card {
            justify-content: center !important;
            padding: 0.375rem !important;
        }
        /* Brand Logo hover effect */
        #customer-brand-block:hover .brand-gw-logo {
            background-color: #142e21 !important;
            transform: scale(1.04);
        }
        /* Search bar perfect vertical centering */
        .customer-search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .customer-search-icon {
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
        @media (min-width: 1024px) {
            #customer-sidebar {
                position: static !important;
                inset: auto !important;
                transform: none !important;
                z-index: 10 !important;
            }
        }
        /* Custom scrollbar for sidebar */
        #customer-sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        #customer-sidebar-nav::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="bg-stone-50 flex flex-col h-screen overflow-hidden text-stone-800 antialiased font-sans">

    @php
        $authUser = Auth::user();
        $profile = $profile ?? ($authUser ? app(\App\Services\ResetJourneyService::class)->activeProfile($authUser) ?? app(\App\Services\ResetJourneyService::class)->latestProfile($authUser) : null);
        $currentDay = $currentDay ?? ($profile?->status === 'completed' ? 30 : (int) ($profile?->current_reset_day ?? 0));
        $userName = $authUser?->name ?: ($authUser?->customerProfile?->first_name ?: $authUser?->email ?: 'Customer');
        $userInitial = strtoupper(substr($userName, 0, 1));
        $cartCount = (int) (session('cart') ? count(session('cart')) : 0);
    @endphp

    <!-- Top Header (Fixed ~80px Height with subtle 1px #E5E7EB horizontal bottom border) -->
    <header class="w-full h-20 border-b border-[#E5E7EB] bg-white text-stone-600 flex items-center justify-between px-0 select-none relative z-40 shrink-0">
        <!-- Unified Top-Left SaaS Brand & Sidebar Header (Fixed ~80px Height, 1px #E5E7EB border) -->
        <div
            id="customer-header-brand-container"
            class="sidebar-transition h-full flex items-center px-4 border-r border-[#E5E7EB] w-auto lg:w-[260px] shrink-0 overflow-visible relative bg-white"
        >
            <!-- Mobile Drawer Button (Mobile only) -->
            <button
                type="button"
                onclick="toggleCustomerMobileSidebar()"
                class="lg:hidden p-1.5 -ml-1 mr-2 rounded-xl text-stone-500 hover:bg-stone-100 hover:text-stone-800 transition"
                title="Toggle Menu"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <!-- Unified Single Branding Block (Vertically Aligned Logo, Title & Subtitle) -->
            <a
                href="{{ route('reset.home') }}"
                id="customer-brand-block"
                onclick="handleCustomerBrandClick(event)"
                class="flex items-center gap-3.5 min-w-0 group select-none transition-all duration-200"
                title="Guided Wellness Customer Portal"
            >
                <x-gw-logo class="brand-gw-logo shrink-0" />
                <div id="customer-brand-name-wrapper" class="sidebar-text flex flex-col justify-center min-w-0 transition-opacity duration-200">
                    <span class="font-bold text-stone-900 text-[14.5px] tracking-tight leading-snug group-hover:text-emerald-950 transition-colors whitespace-nowrap">Guided Wellness</span>
                    <span class="text-[10px] text-stone-400 font-bold uppercase tracking-wider leading-none mt-1 whitespace-nowrap">Customer Portal</span>
                </div>
            </a>

            <!-- Circular 30px Collapse/Expand Toggle Button (Directly on the Vertical Divider - Shopify style) -->
            <button
                type="button"
                id="customer-sidebar-collapse-btn"
                onclick="toggleCustomerSidebarCollapse()"
                class="hidden lg:flex items-center justify-center shrink-0 text-stone-500 hover:text-stone-900 focus:outline-none"
                title="Collapse sidebar"
                aria-label="Toggle customer sidebar"
            >
                <svg class="h-3.5 w-3.5 customer-sidebar-chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        <!-- Right Header Area (Search, Storefront, Notifications, Cart, User) -->
        <div class="flex items-center justify-between flex-1 px-4 sm:px-6 min-w-0">
            <!-- Center Search Bar / Quick Switcher -->
            <div class="hidden md:flex items-center flex-1 max-w-md mr-6">
                <div class="customer-search-wrapper relative w-full flex items-center">
                    <div class="customer-search-icon absolute left-3 top-1/2 -translate-y-1/2 flex items-center pointer-events-none text-stone-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input
                        type="text"
                        id="customer-global-search"
                        placeholder="Search your journey, plan, orders, help..."
                        class="h-9 w-full rounded-xl border border-stone-200 bg-stone-50/70 pl-9 pr-4 text-xs leading-none text-stone-800 placeholder-stone-400 outline-none transition focus:border-[#204b34] focus:bg-white focus:ring-2 focus:ring-[#204b34]/15"
                    >
                </div>
            </div>

            <!-- Right Header Actions -->
            <div class="flex items-center gap-3 ml-auto">
                <!-- Shop / Storefront Link -->
                <a href="{{ route('shop') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-stone-600 hover:text-stone-900 hover:bg-stone-50 transition shadow-2xs">
                    <svg class="h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span>Shop</span>
                </a>

                <!-- Cart Button -->
                <a href="{{ route('cart') }}" class="relative inline-flex items-center rounded-xl p-2 text-stone-500 hover:bg-stone-100 hover:text-stone-800 transition" title="Shopping Cart">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    @if($cartCount > 0)
                        <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-[#16A34A] text-[9px] font-bold text-white">{{ $cartCount }}</span>
                    @endif
                </a>

                <!-- Notification Bell -->
                <button type="button" class="relative rounded-xl p-2 text-stone-400 hover:bg-stone-100 hover:text-stone-700 transition" title="Notifications">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @if($currentDay > 0 && $currentDay <= 30)
                        <span class="absolute top-1.5 right-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    @endif
                </button>

                <!-- User Menu Dropdown -->
                <details class="group relative flex items-center">
                    <summary class="flex cursor-pointer list-none items-center gap-2 rounded-xl border border-stone-200 bg-white p-1.5 sm:px-3 sm:py-1.5 text-xs font-medium text-stone-700 shadow-2xs hover:bg-stone-50 transition select-none [&::-webkit-details-marker]:hidden">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-[#204b34] text-[11px] font-bold">
                            {{ $userInitial }}
                        </div>
                        <span class="hidden md:inline font-semibold text-stone-800">{{ $userName }}</span>
                        <svg class="h-3 w-3 text-stone-400 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                    </summary>
                    <div class="absolute right-0 top-full z-50 mt-2 w-56 rounded-2xl border border-stone-200 bg-white p-2 shadow-xl ring-1 ring-black/5">
                        <div class="border-b border-stone-100 px-3 py-2.5 mb-1 pb-3">
                            <div class="font-semibold text-xs text-stone-900 truncate">{{ $userName }}</div>
                            <div class="text-[11px] text-stone-400 truncate mt-0.5">{{ $authUser?->email ?? '' }}</div>
                            <div class="mt-2 inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[10px] font-semibold text-emerald-800 uppercase tracking-wider">
                                {{ $profile?->status === 'completed' ? 'Reset Complete' : ($currentDay > 0 ? 'Day '.$currentDay.' of 30' : 'Member') }}
                            </div>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('reset.home') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-medium text-stone-700 hover:bg-stone-50 transition">
                                <svg class="h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                My Journey
                            </a>
                            <a href="{{ route('account') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-medium text-stone-700 hover:bg-stone-50 transition">
                                <svg class="h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Account Settings
                            </a>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="pt-1 border-t border-stone-100">
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
        <!-- Modular Customer SaaS Sidebar Component -->
        <x-customer-sidebar :profile="$profile ?? null" :currentDay="$currentDay ?? null" />

        <!-- Main Content Area (Scrolls independently) -->
        <main class="flex-1 overflow-y-auto bg-stone-50 min-w-0">
            @yield('content')
        </main>
    </div>

    <!-- Floating Tooltip for Icon-only Collapsed Mode -->
    <div id="customer-sidebar-tooltip" class="fixed z-50 hidden rounded-md bg-stone-900 px-2 py-1 text-[11px] font-semibold text-white shadow-md pointer-events-none transition-opacity duration-150"></div>

    <!-- Sidebar JavaScript & State Management (Exact Behavior & Animation as Admin Sidebar) -->
    <script>
        const customerSidebar = document.getElementById('customer-sidebar');
        const customerBrandContainer = document.getElementById('customer-header-brand-container');
        const customerCollapseBtn = document.getElementById('customer-sidebar-collapse-btn');
        const customerBrandBlock = document.getElementById('customer-brand-block');
        const customerTooltip = document.getElementById('customer-sidebar-tooltip');

        function isCustomerCollapsed() {
            return document.documentElement.classList.contains('customer-sidebar-collapsed');
        }

        // Handle clicking the brand logo in collapsed mode (expands sidebar)
        function handleCustomerBrandClick(e) {
            if (isCustomerCollapsed()) {
                e.preventDefault();
                toggleCustomerSidebarCollapse();
            }
        }

        // Toggle Sidebar Collapse (260px <-> 72px) with smooth 220ms ease-in-out
        function toggleCustomerSidebarCollapse() {
            const willCollapse = !isCustomerCollapsed();
            if (willCollapse) {
                document.documentElement.classList.add('customer-sidebar-collapsed');
                customerSidebar.classList.remove('w-[260px]');
                customerSidebar.classList.add('w-[72px]');
                if (customerBrandContainer) {
                    customerBrandContainer.classList.remove('lg:w-[260px]', 'px-4');
                    customerBrandContainer.classList.add('lg:w-[72px]', 'px-0', 'justify-center');
                }
                if (customerCollapseBtn) {
                    customerCollapseBtn.setAttribute('title', 'Expand sidebar');
                    customerCollapseBtn.setAttribute('aria-label', 'Expand customer sidebar');
                }
                localStorage.setItem('customer_sidebar_collapsed', 'true');
            } else {
                document.documentElement.classList.remove('customer-sidebar-collapsed');
                customerSidebar.classList.remove('w-[72px]');
                customerSidebar.classList.add('w-[260px]');
                if (customerBrandContainer) {
                    customerBrandContainer.classList.remove('lg:w-[72px]', 'px-0', 'justify-center');
                    customerBrandContainer.classList.add('lg:w-[260px]', 'px-4');
                }
                if (customerCollapseBtn) {
                    customerCollapseBtn.setAttribute('title', 'Collapse sidebar');
                    customerCollapseBtn.setAttribute('aria-label', 'Collapse customer sidebar');
                }
                localStorage.setItem('customer_sidebar_collapsed', 'false');
            }
            updateCustomerSidebarTextVisibility();
        }

        // Mobile Drawer Toggle
        function toggleCustomerMobileSidebar() {
            const isMobileOpen = customerSidebar.classList.contains('translate-x-0');
            const backdrop = document.getElementById('customer-mobile-sidebar-backdrop');
            if (isMobileOpen) {
                customerSidebar.classList.remove('translate-x-0');
                customerSidebar.classList.add('-translate-x-full');
                backdrop?.classList.add('hidden');
            } else {
                customerSidebar.classList.remove('-translate-x-full');
                customerSidebar.classList.add('translate-x-0');
                backdrop?.classList.remove('hidden');
            }
        }

        // Update Text Visibility on Collapse/Expand
        function updateCustomerSidebarTextVisibility() {
            const texts = document.querySelectorAll('.sidebar-text');
            const collapsed = isCustomerCollapsed();
            texts.forEach(el => {
                if (collapsed) {
                    el.classList.add('hidden');
                } else {
                    el.classList.remove('hidden');
                }
            });
        }

        // Tooltip handlers for icon-only collapsed mode
        document.querySelectorAll('#customer-sidebar .nav-item').forEach(item => {
            item.addEventListener('mouseenter', () => {
                if (!isCustomerCollapsed()) return;
                const title = item.getAttribute('data-title');
                if (!title || !customerTooltip) return;
                customerTooltip.textContent = title;
                const rect = item.getBoundingClientRect();
                customerTooltip.style.top = `${rect.top + (rect.height / 2) - 12}px`;
                customerTooltip.style.left = `80px`;
                customerTooltip.classList.remove('hidden');
            });

            item.addEventListener('mouseleave', () => {
                if (customerTooltip) customerTooltip.classList.add('hidden');
            });
        });

        // Tooltip for top brand block in collapsed mode
        if (customerBrandBlock) {
            customerBrandBlock.addEventListener('mouseenter', () => {
                if (!isCustomerCollapsed()) return;
                if (!customerTooltip) return;
                customerTooltip.textContent = 'Guided Wellness (Click to expand)';
                const rect = customerBrandBlock.getBoundingClientRect();
                customerTooltip.style.top = `${rect.top + (rect.height / 2) - 12}px`;
                customerTooltip.style.left = `80px`;
                customerTooltip.classList.remove('hidden');
            });

            customerBrandBlock.addEventListener('mouseleave', () => {
                if (customerTooltip) customerTooltip.classList.add('hidden');
            });
        }

        // Hide tooltip while scrolling nav
        const customerSidebarNav = document.getElementById('customer-sidebar-nav');
        if (customerSidebarNav) {
            customerSidebarNav.addEventListener('scroll', () => {
                if (customerTooltip) customerTooltip.classList.add('hidden');
            });
        }

        // Initialize state on DOM ready
        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('customer_sidebar_collapsed') === 'true') {
                customerSidebar?.classList.remove('w-[260px]');
                customerSidebar?.classList.add('w-[72px]');
                if (customerBrandContainer) {
                    customerBrandContainer.classList.remove('lg:w-[260px]', 'px-4');
                    customerBrandContainer.classList.add('lg:w-[72px]', 'px-0', 'justify-center');
                }
                if (customerCollapseBtn) {
                    customerCollapseBtn.setAttribute('title', 'Expand sidebar');
                    customerCollapseBtn.setAttribute('aria-label', 'Expand customer sidebar');
                }
            } else {
                customerSidebar?.classList.add('w-[260px]');
                customerSidebar?.classList.remove('w-[72px]');
                if (customerBrandContainer) {
                    customerBrandContainer.classList.add('lg:w-[260px]', 'px-4');
                    customerBrandContainer.classList.remove('lg:w-[72px]', 'px-0', 'justify-center');
                }
                if (customerCollapseBtn) {
                    customerCollapseBtn.setAttribute('title', 'Collapse sidebar');
                    customerCollapseBtn.setAttribute('aria-label', 'Collapse customer sidebar');
                }
            }
            updateCustomerSidebarTextVisibility();
        });
    </script>
</body>
</html>