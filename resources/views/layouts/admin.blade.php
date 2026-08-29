<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? 'Gut Reset Admin' }}</title>
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-stone-50 flex flex-col h-screen overflow-hidden">
<!-- Top Navbar -->
<header class="h-10 w-full bg-stone-900 border-b border-stone-950 text-stone-300 flex items-center justify-between px-4 text-xs select-none relative z-50 shrink-0">
    <div class="flex items-center gap-4">
        <div class="font-semibold text-white tracking-wider flex items-center gap-2 opacity-90">
            Gut Reset Admin
        </div>
        <!-- Search Bar -->
        <div class="relative hidden md:block ml-4">
            <svg class="absolute left-2 top-1.5 w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" placeholder="Search..." class="bg-stone-800 border border-stone-700 text-stone-200 rounded px-2 py-0.5 pl-7 w-64 focus:outline-none focus:border-stone-500 placeholder-stone-500 transition-colors">
        </div>
    </div>
    
    <!-- Account Menu (CSS Hover) -->
    <div class="relative group h-full flex items-center cursor-default">
        <div class="flex items-center gap-2 hover:text-white transition-colors h-full px-2">
            <span>{{ Auth::user()->name ?? 'Administrator' }}</span>
            <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>
        
        <!-- Dropdown -->
        <div class="absolute right-0 top-10 mt-0 w-56 bg-stone-800 border border-stone-700 rounded-bl shadow-xl py-1 z-50 hidden group-hover:block">
            <div class="px-4 py-3 border-b border-stone-700 mb-1">
                <div class="text-white font-medium truncate text-sm">{{ Auth::user()->name ?? 'Administrator' }}</div>
                <div class="text-[10px] text-stone-400 mt-1 truncate uppercase tracking-widest font-semibold">
                    {{ Auth::user()->roles->first()->name ?? 'Admin Role' }}
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-stone-700 text-stone-200 hover:text-white transition-colors">Sign Out</button>
            </form>
        </div>
    </div>
</header>

<div class="flex-1 flex overflow-hidden">
<aside class="w-full border-r bg-white md:w-64 overflow-y-auto shrink-0">
<nav class="space-y-1 p-4 text-sm font-medium text-stone-600">
@foreach([
['Dashboard','admin.dashboard'],['Products','admin.products.index'],['Orders','admin.orders.index'],['Customers','admin.customers.index'],
['Inventory','admin.inventory.index'],['Shipping','admin.shipping.index'],['Coupons','admin.coupons.index'],['Media','admin.media.index'],['CMS','admin.cms.index'],['Payments','admin.payments.index'],['Fulfilment','admin.fulfilment.index'],['WhatsApp','admin.whatsapp.dashboard'],['Day 0–30','admin.reset.operations']
] as $item)
<a class="block rounded-lg px-3 py-2 hover:bg-stone-100 hover:text-stone-900 transition-colors {{ request()->routeIs($item[1].'*') ? 'bg-stone-100 text-stone-900' : '' }}" href="{{ route($item[1]) }}">{{ $item[0] }}</a>
@endforeach
</nav>
</aside>
<main class="flex-1 overflow-y-auto bg-stone-50">@yield('content')</main>
</div>
</body>
</html>
