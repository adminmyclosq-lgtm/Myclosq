<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Gut Reset' }}</title>
    <script>
        window.AppRoutes = {
            cartItems: @json(url('/api/v1/cart/items')),
            login: @json(route('login')),
        };
    </script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" data-precedence="default" />
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-background text-foreground">
<header class="sticky top-0 z-40 border-b border-border/60 bg-background/85 backdrop-blur">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 flex h-16 items-center justify-between gap-6">
        <a href="{{ route('home') }}" class="font-serif text-lg leading-tight tracking-tight text-foreground">
            Guided<br class="hidden sm:inline"/><span class="hidden sm:inline">Wellness</span><span class="sm:hidden"> Wellness</span>
        </a>
        <nav class="hidden items-center gap-7 text-sm md:flex">
            <a href="{{ route('shop') }}">Shop</a>
            <a href="{{ url('/#how-it-works') }}">How It Works</a>
            <a href="{{ url('/#standards') }}">Our Standards</a>
            <a href="{{ url('/#learn') }}">Learn</a>
            <a href="{{ url('/#faq') }}">Support</a>
        </nav>
        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('cart') }}" class="hidden md:inline-flex">Cart</a>
            @auth
                @php
                    $authUser = auth()->user();
                    $primaryRole = $authUser?->roles()->first();
                    $accountRoute = $authUser?->hasRole('SUPER_ADMIN') ? 'admin.dashboard' : 'account';
                    $roleLabel = $primaryRole
                        ? ucwords(strtolower(str_replace('_', ' ', (string) $primaryRole->code)))
                        : 'User';
                @endphp
                <a href="{{ route($accountRoute) }}" class="hidden max-w-[14rem] rounded-full border border-stone-200 bg-white px-3 py-2 text-left leading-tight md:block">
                    <span class="block truncate font-semibold text-stone-800">{{ $authUser?->name ?? 'Account' }}</span>
                    <span class="block truncate text-xs text-stone-500">{{ $roleLabel }}</span>
                </a>
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
