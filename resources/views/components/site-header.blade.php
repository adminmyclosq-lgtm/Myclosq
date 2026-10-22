@php
    $authUser = auth()->user();
    $headerCart = $authUser
        ? \App\Models\Cart::with('cartItems.productVariant.product')
            ->where('user_id', $authUser->id)
            ->where('status', 'active')
            ->first()
        : null;
    $headerCartItemCount = (int) ($headerCart?->cartItems->sum('quantity') ?? 0);

    $primaryRole = $authUser?->roles()->first();
    $accountRoute = $authUser?->hasRole('SUPER_ADMIN') ? 'admin.dashboard' : 'account';
    $roleLabel = $primaryRole
        ? ucwords(strtolower(str_replace('_', ' ', (string) $primaryRole->code)))
        : ($authUser?->hasRole('SUPER_ADMIN') ? 'Administrator' : 'Customer');
    $userName = $authUser?->name ?: ($authUser?->customerProfile?->first_name ?: $authUser?->email);
    $userInitial = strtoupper(substr($userName ?? 'C', 0, 1));
@endphp

<!-- Website Header (Standardized to exact Admin Header height, styling, and proportions) -->
<header class="sticky top-0 z-50 w-full h-20 border-b border-[#E5E7EB] bg-white/95 backdrop-blur-md text-stone-600 select-none shadow-xs" style="position: sticky; top: 0; z-index: 50;">
    <div class="mx-auto flex h-full w-full max-w-[1200px] items-center justify-between gap-2 sm:gap-6 px-3.5 sm:px-8">
        
        <!-- Left: Unified SaaS Branding Block (Identical to Admin Header) -->
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3.5 min-w-0 group select-none transition-all duration-200">
            <x-gw-logo class="brand-gw-logo shrink-0" />
            <div class="flex flex-col justify-center min-w-0">
                <span class="font-bold text-stone-900 text-[13.5px] sm:text-[14.5px] tracking-tight leading-snug group-hover:text-emerald-950 transition-colors truncate">Guided Wellness</span>
                <span class="text-[9.5px] sm:text-[10px] text-stone-400 font-bold uppercase tracking-wider leading-none mt-0.5 sm:mt-1 hidden sm:block truncate">Gut Health Platform</span>
            </div>
        </a>

        <!-- Center: Desktop Navigation Links -->
        <nav class="hidden md:flex items-center gap-4 lg:gap-7 text-xs font-semibold tracking-tight text-stone-600">
            <a href="{{ route('how-it-works') }}" class="whitespace-nowrap transition-colors {{ request()->routeIs('how-it-works') ? 'text-[#204b34] font-bold' : 'hover:text-stone-900' }}">
                How It Works
            </a>
            <a href="{{ route('our-standards') }}" class="whitespace-nowrap transition-colors {{ request()->routeIs('our-standards') ? 'text-[#204b34] font-bold' : 'hover:text-stone-900' }}">
                Our Standards
            </a>
            <a href="{{ route('learn') }}" class="whitespace-nowrap transition-colors {{ request()->routeIs('learn') ? 'text-[#204b34] font-bold' : 'hover:text-stone-900' }}">
                Learn
            </a>
            <a href="{{ route('support') }}" class="whitespace-nowrap transition-colors {{ request()->routeIs('support') ? 'text-[#204b34] font-bold' : 'hover:text-stone-900' }}">
                Support
            </a>
            
            {{-- Post Login: My Brief is added --}}
            @auth
                @unless($authUser?->hasRole('SUPER_ADMIN'))
                    <a href="{{ route('my-brief') }}" class="whitespace-nowrap transition-colors flex items-center gap-1.5 {{ request()->routeIs('my-brief') ? 'text-[#204b34] font-bold underline' : 'text-[#204b34] hover:underline font-semibold' }}">
                        <span>My Brief</span>
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#204b34]"></span>
                    </a>
                @endunless
            @endauth
        </nav>

        <!-- Right Side Actions (Identical Proportions, Alignment, and Button Styles) -->
        <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">

            <!-- Mobile Drawer Button (Mobile only - hidden on md+) -->
            <details class="group relative md:hidden">
                <summary class="inline-flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-xl border border-stone-200 bg-white text-stone-700 shadow-2xs hover:bg-stone-50 transition [&::-webkit-details-marker]:hidden" aria-label="Open menu">
                    <svg class="h-4 w-4 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                    <svg class="hidden h-4 w-4 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" /></svg>
                </summary>
                <nav class="fixed left-3 right-3 top-[76px] z-50 sm:absolute sm:left-auto sm:right-0 sm:top-full sm:mt-2 sm:w-64 rounded-2xl border border-stone-200 bg-white p-2.5 shadow-2xl ring-1 ring-black/5">
                    <div class="border-b border-stone-100 px-3 py-2 mb-1.5 text-xs font-bold text-stone-400 uppercase tracking-wider">Navigation</div>
                    <a href="{{ route('how-it-works') }}" class="block rounded-xl px-3.5 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition">How It Works</a>
                    <a href="{{ route('our-standards') }}" class="block rounded-xl px-3.5 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition">Our Standards</a>
                    <a href="{{ route('learn') }}" class="block rounded-xl px-3.5 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition">Learn</a>
                    <a href="{{ route('support') }}" class="block rounded-xl px-3.5 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition">Support</a>
                    @auth
                        @unless($authUser?->hasRole('SUPER_ADMIN'))
                            <a href="{{ route('my-brief') }}" class="block rounded-xl px-3.5 py-2.5 text-xs font-bold text-[#204b34] bg-emerald-50/70 hover:bg-emerald-50 transition">My Brief</a>
                        @endunless
                        <a href="{{ route($accountRoute) }}" class="block rounded-xl px-3.5 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition">Account & Orders</a>
                    @else
                        <a href="{{ route('login') }}" class="block rounded-xl px-3.5 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition">Login</a>
                    @endauth
                    <div class="pt-2 mt-1 border-t border-stone-100">
                        <a href="{{ route('shop') }}" class="block rounded-xl bg-[#204b34] px-4 py-2.5 text-center text-xs font-semibold text-white shadow-2xs">Shop Gut Reset</a>
                    </div>
                </nav>
            </details>

            <!-- 1. Shop Button (Always displayed in both Logged Out and Customer views) -->
            <a href="{{ route('shop') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl px-3 sm:px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#163826] whitespace-nowrap shadow-2xs" style="background-color: #204b34;">
                <span class="hidden sm:inline">Shop Gut Reset</span>
                <span class="sm:hidden">Shop</span>
            </a>

            <!-- 2. Cart Button (Always displayed in both Logged Out and Customer views) -->
            <div class="group relative">
                <a href="{{ route('cart') }}" class="inline-flex items-center gap-1 sm:gap-2 rounded-xl border border-stone-200 bg-white px-2.5 sm:px-3.5 py-2 text-xs font-semibold text-stone-700 hover:text-stone-900 hover:bg-stone-50 transition shadow-2xs whitespace-nowrap" aria-label="Cart, {{ $headerCartItemCount }} item{{ $headerCartItemCount === 1 ? '' : 's' }}">
                    <svg class="h-4 w-4 text-stone-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-1.1 2.2A1 1 0 0 0 6.8 17H19M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" />
                    </svg>
                    <span class="inline-flex items-center justify-center rounded-full bg-emerald-100 text-[#204b34] px-1.5 py-0.5 text-[10px] font-bold" data-cart-count>{{ $headerCartItemCount }}</span>
                    <span class="hidden sm:inline">Cart</span>
                </a>

                <!-- Hover Cart Mini Dropdown -->
                <div class="invisible absolute right-0 top-full z-50 mt-2 w-80 translate-y-1 rounded-2xl border border-stone-200 bg-white p-4 opacity-0 shadow-xl ring-1 ring-black/5 transition duration-150 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
                    <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                        <span class="font-bold text-xs text-stone-900 uppercase tracking-wider">Your Cart</span>
                        <span class="text-xs text-stone-500"><span data-cart-count>{{ $headerCartItemCount }}</span> item{{ $headerCartItemCount === 1 ? '' : 's' }}</span>
                    </div>
                    @auth
                        @if($headerCart && $headerCart->cartItems->isNotEmpty())
                            <div class="max-h-56 divide-y divide-stone-100 overflow-y-auto my-2">
                                @foreach($headerCart->cartItems as $item)
                                    <div class="flex items-start justify-between gap-4 py-2.5 text-xs">
                                        <span class="line-clamp-2 font-medium text-stone-800">{{ $item->productVariant->product->name }}</span>
                                        <span class="shrink-0 text-stone-500 font-semibold">x{{ $item->quantity }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="py-5 text-center text-xs text-stone-500">Your cart is currently empty.</p>
                        @endif
                    @else
                        <p class="py-5 text-center text-xs text-stone-500">Sign in to view your saved cart items.</p>
                    @endauth
                    <a href="{{ route('cart') }}" class="mt-2 block w-full rounded-xl bg-stone-900 hover:bg-stone-800 text-center text-xs font-semibold text-white py-2 transition shadow-2xs">
                        View Full Cart
                    </a>
                </div>
            </div>

            <!-- 3. ACTION SWITCH: Login (Logged Out) VS Customer Menu (Logged In) -->
            @auth
                <!-- CUSTOMER HEADER: Customer Menu Dropdown (Matches Admin Header Profile Dropdown) -->
                <details class="group relative flex items-center">
                    <summary class="flex cursor-pointer list-none items-center gap-2 rounded-xl border border-stone-200 bg-white p-1.5 sm:px-3 sm:py-1.5 text-xs font-medium text-stone-700 shadow-2xs hover:bg-stone-50 transition select-none [&::-webkit-details-marker]:hidden">
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-[#204b34] text-xs font-bold shrink-0">
                            {{ $userInitial }}
                        </div>
                        <span class="hidden md:inline font-semibold text-stone-800 max-w-[120px] truncate">{{ $userName }}</span>
                        <svg class="h-3.5 w-3.5 text-stone-400 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                    </summary>

                    <div class="absolute right-0 top-full z-50 mt-2 w-56 rounded-2xl border border-stone-200 bg-white p-2 shadow-xl ring-1 ring-black/5">
                        <div class="border-b border-stone-100 px-3 py-2.5 mb-1 pb-3">
                            <div class="font-semibold text-xs text-stone-900 truncate">{{ $userName }}</div>
                            <div class="text-[11px] text-stone-400 truncate mt-0.5">{{ $authUser->email ?? '' }}</div>
                            <div class="mt-2 inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[10px] font-semibold text-[#204b34] uppercase tracking-wider">
                                {{ $roleLabel }}
                            </div>
                        </div>

                        <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-medium text-stone-700 hover:bg-stone-50 transition">
                            <svg class="h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Home</span>
                        </a>

                        @unless($authUser?->hasRole('SUPER_ADMIN'))
                            <a href="{{ route('my-brief') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold text-[#204b34] bg-emerald-50/70 hover:bg-emerald-50 transition">
                                <svg class="h-3.5 w-3.5 text-[#204b34]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>My Brief</span>
                            </a>
                        @endunless

                        <a href="{{ route($accountRoute) }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-medium text-stone-700 hover:bg-stone-50 transition">
                            <svg class="h-3.5 w-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>{{ $authUser?->hasRole('SUPER_ADMIN') ? 'Admin Dashboard' : 'Account & Orders' }}</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="border-t border-stone-100 pt-1 mt-1">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left text-xs font-medium text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                <svg class="h-3.5 w-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                </details>
            @else
                <!-- LOGGED OUT HEADER: Login Button (Shown on sm+, available in mobile drawer on xs) -->
                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 hover:text-stone-900 hover:bg-stone-50 transition shadow-2xs whitespace-nowrap">
                    <svg class="h-4 w-4 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="12" cy="8" r="4" />
                        <path stroke-linecap="round" d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6" />
                    </svg>
                    <span>Login</span>
                </a>
            @endauth

        </div>
    </div>
</header>
