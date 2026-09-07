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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" data-precedence="default" />
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-background text-foreground">
<x-site-header />

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
