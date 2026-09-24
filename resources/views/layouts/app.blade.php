<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Gut Reset' }}</title>
    <script>
        window.AppRoutes = {
            cartItems: @json(url('/api/v1/cart/items')),
            cartItemBase: @json(url('/api/v1/cart/items')),
            login: @json(route('login')),
        };
    </script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" data-precedence="default" />
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-background text-foreground">
@php
    $headerCart = auth()->check()
        ? \App\Models\Cart::with('cartItems.productVariant.product')
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first()
        : null;
    $headerCartItemCount = (int) ($headerCart?->cartItems->sum('quantity') ?? 0);

    $authUser = auth()->user();
    $primaryRole = $authUser?->roles()->first();
    $accountRoute = $authUser?->hasRole('SUPER_ADMIN') ? 'admin.dashboard' : 'account';
    $roleLabel = $primaryRole
        ? ucwords(strtolower(str_replace('_', ' ', (string) $primaryRole->code)))
        : 'User';
@endphp
<header class="sticky top-0 z-40 border-b border-border/60 bg-background/85 backdrop-blur">
    <div class="mx-auto flex w-full max-w-[1200px] items-center justify-between gap-6 px-5 h-16 sm:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3 text-lg font-semibold tracking-tight text-foreground">
            @if(!empty($headerLogo))
                <img src="{{ $headerLogo->url }}" alt="Guided Wellness" class="h-10 w-10 rounded-full object-cover">
                <span>Guided Wellness</span>
            @else
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[var(--ink)] text-sm font-bold text-white">GW</span>
                <span>Guided Wellness</span>
            @endif
        </a>
        <nav class="hidden items-center gap-7 text-sm md:flex">
            @auth
                @unless(auth()->user()?->hasRole('SUPER_ADMIN'))
                    <a href="{{ route('my-brief') }}">My Brief</a>
                @endunless
            @endauth
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('how-it-works') }}">How It Works</a>
            <a href="{{ route('our-standards') }}">Our Standards</a>
            <a href="{{ route('learn') }}">Learn</a>
            <a href="{{ route('support') }}">Support</a>
            <a href="{{ route('shop') }}" class="inline-flex shrink-0 items-center justify-center rounded-full px-5 py-2.5 text-sm font-medium text-white transition hover:opacity-90" style="background-color: #587762;">Shop Gut Reset</a>
        </nav>
        <div class="flex items-center gap-3 text-sm">
            <details class="group relative md:hidden">
                <summary class="inline-flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-full border border-stone-200 bg-white text-[var(--ink)] [&::-webkit-details-marker]:hidden" aria-label="Open menu">
                    <svg class="h-5 w-5 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                    <svg class="hidden h-5 w-5 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" /></svg>
                </summary>
                <nav class="absolute right-0 top-full z-50 mt-3 w-64 rounded-xl border border-stone-200 bg-white p-2 shadow-xl">
                    <a href="{{ route('home') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-stone-50">Home</a>
                    <a href="{{ route('how-it-works') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-stone-50">How It Works</a>
                    <a href="{{ route('our-standards') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-stone-50">Our Standards</a>
                    <a href="{{ route('learn') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-stone-50">Learn</a>
                    <a href="{{ route('support') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-stone-50">Support</a>
                    @auth
                        @unless(auth()->user()?->hasRole('SUPER_ADMIN'))
                            <a href="{{ route('my-brief') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-stone-50">My Brief</a>
                        @endunless
                        @php($mobileAccountRoute = auth()->user()?->hasRole('SUPER_ADMIN') ? 'admin.dashboard' : 'account')
                        <a href="{{ route($mobileAccountRoute) }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-stone-50">Account</a>
                    @else
                        <!-- <a href="{{ route('login') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-stone-50">Login</a> -->
                    @endauth
                    <a href="{{ route('shop') }}" class="mt-1 block rounded-lg bg-[#587762] px-4 py-3 text-center font-medium text-white">Shop Gut Reset</a>
                </nav>
            </details>
            <div class="group relative">
                <a href="{{ route('cart') }}" class="inline-flex items-center gap-1.5 font-semibold text-[var(--ink)]" aria-label="Cart, {{ $headerCartItemCount }} item{{ $headerCartItemCount === 1 ? '' : 's' }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-1.1 2.2A1 1 0 0 0 6.8 17H19M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" /></svg>
                    <span data-cart-count>{{ $headerCartItemCount }}</span>
                    <span>Cart</span>
                </a>
                <div class="invisible absolute right-0 top-full z-50 mt-3 w-80 translate-y-1 rounded-lg border border-stone-200 bg-white p-4 opacity-0 shadow-lg transition duration-150 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
                    <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                        <span class="font-semibold text-stone-900">Your cart</span>
                        <span class="text-xs text-stone-500"><span data-cart-count>{{ $headerCartItemCount }}</span> item{{ $headerCartItemCount === 1 ? '' : 's' }}</span>
                    </div>
                    @auth
                        @if($headerCart && $headerCart->cartItems->isNotEmpty())
                            <div class="max-h-56 divide-y divide-stone-100 overflow-y-auto">
                                @foreach($headerCart->cartItems as $item)
                                    <div class="flex items-start justify-between gap-4 py-3 text-sm">
                                        <span class="line-clamp-2 font-medium text-stone-800">{{ $item->productVariant->product->name }}</span>
                                        <span class="shrink-0 text-stone-500">x{{ $item->quantity }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="py-5 text-sm text-stone-500">Your cart is empty.</p>
                        @endif
                    @else
                        <p class="py-5 text-sm text-stone-500">Sign in to view your cart.</p>
                    @endauth
                    <a href="{{ route('cart') }}" class="btn-primary mt-3 w-full !rounded-lg !px-4 !py-2 text-sm">View cart</a>
                </div>
            </div>
            @auth
                <details class="group relative hidden md:block">
                    <summary class="flex max-w-[14rem] cursor-pointer list-none items-center gap-2 rounded-full border border-stone-200 bg-white px-3 py-2 text-left leading-tight [&::-webkit-details-marker]:hidden">
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-semibold text-stone-800">{{ $authUser?->name ?? 'Account' }}</span>
                            <span class="block truncate text-xs text-stone-500">{{ $roleLabel }}</span>
                        </span>
                        <svg class="h-4 w-4 shrink-0 text-stone-500 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                    </summary>
                    <div class="absolute right-0 z-50 mt-2 w-52 rounded-lg border border-stone-200 bg-white p-2 shadow-lg">
                        <a href="{{ route('home') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Home</a>
                        @unless($authUser?->hasRole('SUPER_ADMIN'))
                            <a href="{{ route('my-brief') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">My Brief</a>
                        @endunless
                        <a href="{{ route($accountRoute) }}" class="block rounded-md px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Account</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-stone-100 pt-2">
                            @csrf
                            <button type="submit" class="w-full rounded-md px-3 py-2 text-left text-sm font-semibold text-red-700 transition hover:bg-red-50">Sign out</button>
                        </form>
                    </div>
                </details>
            @else
                <!-- <a href="{{ route('login') }}" class="hidden items-center gap-1.5 font-semibold text-[var(--ink)] md:inline-flex"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4" /><path stroke-linecap="round" d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6" /></svg>Login</a> -->
            @endauth
        </div>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer class="bg-[var(--ink)] text-white">
    <div class="section grid gap-10 md:grid-cols-[1.6fr_2.4fr]">
        <div>
            <div class="display-serif text-2xl leading-tight">Guided Wellness</div>
            <p class="mt-3 max-w-sm text-[13px] leading-6 text-white/70">Redefining the supplement experience through transparency, guidance, and respect for biological complexity.</p>
        </div>
        <div class="grid grid-cols-2 gap-6 sm:grid-cols-3">
            <div>
                <div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/60">Shop</div>
                <a class="mt-3 block text-[13px] text-white/85 hover:text-white" href="{{ route('shop') }}">30-Day Gut Reset</a>
                <a class="mt-2 block text-[13px] text-white/85 hover:text-white" href="{{ route('how-it-works') }}">How It Works</a>
            </div>
            <div>
                <div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/60">Learn</div>
                <a class="mt-3 block text-[13px] text-white/85 hover:text-white" href="{{ route('learn') }}">Guides</a>
                <a class="mt-2 block text-[13px] text-white/85 hover:text-white" href="{{ route('our-standards') }}">Standards</a>
                <a class="mt-2 block text-[13px] text-white/85 hover:text-white" href="{{ url('/about') }}">About</a>
            </div>
            <div>
                <div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/60">Support</div>
                <a class="mt-3 block text-[13px] text-white/85 hover:text-white" href="{{ route('account') }}">Account</a>
                <a class="mt-2 block text-[13px] text-white/85 hover:text-white" href="{{ route('support') }}">Help Centre</a>
            </div>
        </div>
    </div>
    <div class="border-t border-white/15 py-5 text-center text-[11px] text-white/60">© 2026 Kurate Wellness Private Limited. All rights reserved.</div>
</footer>
</body>
</html>
