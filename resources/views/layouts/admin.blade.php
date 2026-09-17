<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? (!empty($isMyClosq) ? 'My CLOSQ Admin' : 'Gut Reset Admin') }}</title>
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}?v=2">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}?v=2">
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-stone-50 flex flex-col h-screen overflow-hidden">
<!-- Top Navbar -->
<header class="w-full border-b border-stone-200 bg-white text-stone-600 flex items-center justify-between px-5 py-3 text-sm select-none relative z-50 shrink-0 md:px-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('home') }}" class="flex items-center gap-3 font-semibold tracking-tight text-[var(--ink)]">
            <img src="{{ asset('images/closq-logo.png') }}" alt="CLOS.Q Logo" class="h-10 w-auto object-contain">
        </a>
        <a href="{{ route('home') }}" class="hidden font-medium text-stone-600 transition hover:text-[var(--ink)] md:inline-flex">Home</a>
        <span class="hidden border-l border-stone-200 pl-4 text-xs font-medium uppercase tracking-[.14em] text-stone-500 lg:inline">Admin</span>
        <!-- Search Bar -->
        <div class="relative hidden md:block">
            <svg class="absolute left-3 top-2.5 h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" placeholder="Search..." class="w-64 rounded-md border border-stone-300 bg-white py-1.5 pl-8 pr-3 text-sm text-stone-800 outline-none transition-colors placeholder:text-stone-400 focus:border-[var(--ink)] focus:ring-2 focus:ring-[var(--sage)]/30">
        </div>
    </div>
    
    <!-- Account Menu (CSS Hover) -->
    <details class="group relative flex h-full items-center">
        <summary class="flex cursor-pointer list-none items-center gap-2 px-2 transition-colors hover:text-[var(--ink)] [&::-webkit-details-marker]:hidden">
            <span>{{ Auth::user()->name ?? 'Administrator' }}</span>
            <svg class="h-3 w-3 opacity-50 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"></path></svg>
        </summary>
        <div class="absolute right-0 top-full z-50 mt-3 w-56 rounded-md border border-stone-200 bg-white py-1 shadow-xl">
            <div class="mb-1 border-b border-stone-100 px-4 py-3">
                <div class="truncate text-sm font-medium text-stone-900">{{ Auth::user()->name ?? 'Administrator' }}</div>
                <div class="text-[10px] text-stone-400 mt-1 truncate uppercase tracking-widest font-semibold">
                    {{ Auth::user()->roles->first()->name ?? 'Admin Role' }}
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full px-4 py-2 text-left text-stone-700 transition-colors hover:bg-stone-50 hover:text-[var(--ink)]">Sign Out</button>
            </form>
        </div>
    </details>
</header>

<div class="flex min-h-0 flex-1 flex-col overflow-hidden md:flex-row">
<aside class="w-full shrink-0 overflow-y-auto border-b border-stone-200 bg-white md:w-64 md:border-b-0 md:border-r" style="height: 100%; max-height: 100%;">
<nav class="space-y-1 p-4 text-sm font-medium text-stone-600">
@foreach([
['Dashboard','admin.dashboard'],['Products','admin.products.index'],['Orders','admin.orders.index'],['Customers','admin.customers.index'],
['Inventory','admin.inventory.index'],['Shipping','admin.shipping.index'],['Coupons','admin.coupons.index'],['Media','admin.media.index'],['CMS','admin.cms.index'],['Global CMS','admin.cms.global'],['Payments','admin.payments.index'],['Fulfilment','admin.fulfilment.index'],['WhatsApp','admin.whatsapp.dashboard'],['Day 0–30','admin.reset.operations']
] as $item)
<a class="block rounded-lg px-3 py-2 hover:bg-stone-100 hover:text-stone-900 transition-colors {{ request()->routeIs($item[1].'*') ? 'bg-stone-100 text-stone-900' : '' }}" href="{{ route($item[1]) }}">{{ $item[0] }}</a>
@endforeach
</nav>
</aside>
<main id="admin-content" class="min-w-0 flex-1 overflow-y-scroll bg-stone-50">@yield('content')</main>
</div>
<script>
    const adminContent = document.getElementById('admin-content');
    const adminNavigation = document.querySelector('aside nav');

    adminNavigation?.addEventListener('click', (event) => {
        if (event.target.closest('a')) adminContent.scrollTop = 0;
    });

    window.addEventListener('pageshow', () => {
        adminContent.scrollTop = 0;
    });
</script>
</body>
</html>
