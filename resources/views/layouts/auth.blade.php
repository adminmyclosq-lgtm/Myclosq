<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? ($isMyClosq ? 'Sign in - My CLOSQ' : 'Sign in - Guided Wellness') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}?v=2">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-[var(--ink)]">
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
                    <a class="mt-3 block text-[13px] text-white/85 hover:text-white" href="{{ route('shop') }}">{{ $isMyClosq ? '30-Day My CLOSQ' : '30-Day Gut Reset' }}</a>
                    <a class="mt-2 block text-[13px] text-white/85 hover:text-white" href="{{ route('how-it-works') }}">How It Works</a>
                </div>
                <div>
                    <div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/60">Learn</div>
                    <a class="mt-3 block text-[13px] text-white/85 hover:text-white" href="{{ url('/#learn') }}">Guides</a>
                    <a class="mt-2 block text-[13px] text-white/85 hover:text-white" href="{{ url('/#standards') }}">Standards</a>
                    <a class="mt-2 block text-[13px] text-white/85 hover:text-white" href="{{ url('/#faq') }}">About</a>
                </div>
                <div>
                    <div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/60">Support</div>
                    <a class="mt-3 block text-[13px] text-white/85 hover:text-white" href="{{ route('account') }}">Account</a>
                    <a class="mt-2 block text-[13px] text-white/85 hover:text-white" href="{{ url('/#faq') }}">Help Centre</a>
                </div>
            </div>
        </div>
        <div class="border-t border-white/15 py-5 text-center text-[11px] text-white/60">© 2026 Kurate Wellness Private Limited. All rights reserved.</div>
    </footer>
</body>
</html>