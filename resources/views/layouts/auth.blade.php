<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sign in - Guided Wellness' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-[var(--ink)]">
    <x-site-header />

    <main>
        @yield('content')
    </main>

    <footer class="bg-[var(--ink)] text-white">
        <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-4 px-5 py-6 text-[12px] text-white/75 sm:flex-row sm:items-center md:px-8">
            <div><div class="display-serif text-base text-white">Guided Wellness</div><div class="mt-1">© 2026 Kurate Wellness Private Limited</div></div>
            <div class="flex items-center gap-6"><a href="{{ url('/#faq') }}" class="hover:text-white">Privacy Policy</a><a href="{{ url('/#faq') }}" class="hover:text-white">Terms of Use</a><a href="{{ url('/#faq') }}" class="hover:text-white">Support</a></div>
        </div>
    </footer>
</body>
</html>