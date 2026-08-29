<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Gut Reset' }}</title>
    <script>
        window.AppRoutes = {
            cartItems: @json(url('/api/v1/cart/items')),
            cartItemBase: @json(url('/api/v1/cart/items')),
            login: @json(route('login')),
        };
    </script>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
@php
    $headerCart = auth()->check()
        ? \App\Models\Cart::with('cartItems.productVariant.product')
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first()
        : null;
    $headerCartItemCount = (int) ($headerCart?->cartItems->sum('quantity') ?? 0);
@endphp
<header class="sticky top-0 z-40 border-b border-stone-200 bg-white/85 backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 py-4 md:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3 text-lg font-semibold tracking-tight text-[var(--ink)]">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[var(--ink)] text-sm font-bold text-white">GW</span>
            <span>Guided Wellness</span>
        </a>
        <nav class="hidden items-center gap-7 text-sm md:flex">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ url('/#how-it-works') }}">How It Works</a>
            <a href="{{ url('/#standards') }}">Our Standards</a>
            <a href="{{ url('/#learn') }}">Learn</a>
            <a href="{{ url('/#faq') }}">Support</a>
        </nav>
        <div class="flex items-center gap-3 text-sm">
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
                @php
                    $authUser = auth()->user();
                    $primaryRole = $authUser?->roles()->first();
                    $accountRoute = $authUser?->hasRole('SUPER_ADMIN') ? 'admin.dashboard' : 'account';
                    $roleLabel = $primaryRole
                        ? ucwords(strtolower(str_replace('_', ' ', (string) $primaryRole->code)))
                        : 'User';
                @endphp
                <details class="group relative hidden md:block">
                    <summary class="flex max-w-[14rem] cursor-pointer list-none items-center gap-2 rounded-full border border-stone-200 bg-white px-3 py-2 text-left leading-tight [&::-webkit-details-marker]:hidden">
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-semibold text-stone-800">{{ $authUser?->name ?? 'Account' }}</span>
                            <span class="block truncate text-xs text-stone-500">{{ $roleLabel }}</span>
                        </span>
                        <svg class="h-4 w-4 shrink-0 text-stone-500 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                    </summary>
                    <div class="absolute right-0 z-50 mt-2 w-48 rounded-lg border border-stone-200 bg-white p-2 shadow-lg">
                        <a href="{{ route($accountRoute) }}" class="block rounded-md px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Account</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-stone-100 pt-2">
                            @csrf
                            <button type="submit" class="w-full rounded-md px-3 py-2 text-left text-sm font-semibold text-red-700 transition hover:bg-red-50">Sign out</button>
                        </form>
                    </div>
                </details>
                <a href="{{ route($accountRoute) }}" class="inline-flex md:hidden">Account</a>
            @else
                <a href="{{ route('login') }}" class="hidden md:inline-flex">Account</a>
            @endauth
            <a href="{{ route('shop') }}" class="btn-primary !px-5 !py-2.5">Shop Gut Reset</a>
        </div>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer class="border-t border-stone-200 bg-[var(--cream)]">
    <div class="section grid gap-10 md:grid-cols-4">
        <div>
            <div class="text-lg font-semibold">Guided Wellness</div>
            <p class="mt-3 max-w-sm text-sm leading-7 text-stone-600">Redefining the supplement experience through transparency, guidance, and respect for biological complexity.</p>
        </div>
        <div>
            <div class="font-semibold">Shop</div>
            <a class="mt-3 block text-sm text-stone-600" href="{{ route('shop') }}">30-Day Gut Reset</a>
            <a class="mt-2 block text-sm text-stone-600" href="{{ url('/#how-it-works') }}">How It Works</a>
        </div>
        <div>
            <div class="font-semibold">Learn</div>
            <a class="mt-3 block text-sm text-stone-600" href="{{ url('/#learn') }}">Guides</a>
            <a class="mt-2 block text-sm text-stone-600" href="{{ url('/#standards') }}">Standards</a>
            <a class="mt-2 block text-sm text-stone-600" href="{{ url('/#faq') }}">About</a>
        </div>
        <div>
            <div class="font-semibold">Support</div>
            <a class="mt-3 block text-sm text-stone-600" href="{{ route('account') }}">Account</a>
            <a class="mt-2 block text-sm text-stone-600" href="{{ url('/#faq') }}">Help Centre</a>
        </div>
    </div>
    <div class="border-t border-stone-200 py-6 text-center text-sm text-stone-500">© 2026 Kurate Wellness Private Limited. All rights reserved.</div>
</footer>
</body>
</html>
