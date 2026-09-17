<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? (!empty($isMyClosq) ? 'My Brief - My CLOSQ' : 'My Brief - Guided Wellness') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}?v=2">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f7f2] text-[#27241f]">
    <x-site-header />
    <div class="min-h-[calc(100vh-4rem)] lg:grid lg:grid-cols-[300px_minmax(0,1fr)]">
        <aside class="flex flex-col border-b border-stone-200 bg-[#f5f3ed] lg:fixed lg:top-16 lg:h-[calc(100vh-4rem)] lg:w-[300px] lg:border-b-0 lg:border-r">
            <div class="px-7 py-6">
                <a href="{{ route('home') }}" class="display-serif block text-2xl leading-[.9]">Guided<br>Wellness</a>
                <div class="mt-5 text-[13px] font-medium uppercase tracking-[.15em] text-stone-500">{{ !empty($isMyClosq) ? '30-Day My CLOSQ' : '30-Day Gut Reset' }}</div>
                <div class="mt-6 text-[11px] font-medium uppercase tracking-[.15em] text-stone-500">Current status</div>
                <div class="mt-2 inline-flex rounded-full bg-[#242019] px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[.13em] text-white">{{ $profile?->status === 'completed' ? 'Complete' : ($currentDay > 0 ? 'In progress' : 'Setting up') }}</div>
            </div>
            <nav class="border-y border-stone-200 px-5 py-5 lg:border-b-0" aria-label="Course navigation">
                <a href="{{ route('my-brief') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-[14px] font-medium uppercase tracking-[.12em] transition {{ request()->routeIs('my-brief') ? 'bg-[#e3e5de] text-[#284c38]' : 'text-stone-600 hover:bg-stone-100' }}"><svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m3 11 9-7 9 7v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z" /></svg>Course Home</a>
                <a href="{{ route('my-plan') }}" class="mt-2 flex items-start gap-3 rounded-xl px-4 py-3 text-[14px] font-medium uppercase tracking-[.12em] transition {{ request()->routeIs('my-plan*') ? 'bg-[#e3e5de] text-[#284c38]' : 'text-stone-600 hover:bg-stone-100' }}"><svg class="mt-0.5 h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M6 2h9l4 4v16H6z" /><path d="M15 2v5h5" /></svg><span>My Plan<span class="mt-1.5 block text-[13px] normal-case font-normal tracking-normal text-stone-400">{{ $profile?->day0Baseline?->priorityAreas->isNotEmpty() ? 'Focus areas saved' : 'Set up first' }}</span></span></a>
                <a href="#progress" class="mt-1 flex items-start gap-3 rounded-xl px-4 py-3 text-[14px] font-medium uppercase tracking-[.12em] text-stone-600 transition hover:bg-stone-100"><svg class="mt-0.5 h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 20V10m6 10V4m6 16v-7m4 7H2" /></svg><span>Progress<span class="mt-1.5 block text-[13px] normal-case font-normal tracking-normal text-stone-400">{{ $currentDay > 0 ? 'Day '.$currentDay.' of 30' : 'Begins after capsule one' }}</span></span></a>
                <a href="#brief" class="mt-1 flex items-start gap-3 rounded-xl px-4 py-3 text-[14px] font-medium uppercase tracking-[.12em] text-stone-600 transition hover:bg-stone-100"><svg class="mt-0.5 h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M6 3h12v18H6z" /><path d="M9 8h6m-6 4h6m-6 4h4" /></svg><span>My Brief<span class="mt-1.5 block text-[13px] normal-case font-normal tracking-normal text-stone-400">{{ $profile?->day30Decision ? 'Ready to view' : 'Available at Day 30' }}</span></span></a>
            </nav>
            <div class="mt-auto hidden border-t border-stone-200 px-7 py-7 lg:block"><a href="{{ route('account') }}" class="flex items-center gap-4 text-[16px] font-medium uppercase tracking-[.14em] text-stone-600"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.06 2.06-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1.03 1.56v.08h-2.92v-.08A1.7 1.7 0 0 0 10.82 18.6a1.7 1.7 0 0 0-1.88.34l-.06.06-2.06-2.06.06-.06A1.7 1.7 0 0 0 7.22 15a1.7 1.7 0 0 0-1.56-1.03h-.08v-2.92h.08A1.7 1.7 0 0 0 7.22 10a1.7 1.7 0 0 0-.34-1.88l-.06-.06L8.88 6l.06.06A1.7 1.7 0 0 0 10.82 6.4a1.7 1.7 0 0 0 1.03-1.56v-.08h2.92v.08A1.7 1.7 0 0 0 15.8 6.4a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.06 2.06-.06.06A1.7 1.7 0 0 0 19.4 10a1.7 1.7 0 0 0 1.56 1.03h.08v2.92h-.08A1.7 1.7 0 0 0 19.4 15Z" /></svg>Account &amp; Settings</a><a href="{{ route('home') }}" class="mt-7 flex items-center gap-4 text-[16px] font-medium uppercase tracking-[.14em] text-stone-600"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>Back to website</a></div>
        </aside>
        <main class="min-w-0 lg:col-start-2">
            @yield('content')
        </main>
    </div>
    <footer class="bg-[var(--ink)] text-white">
        <div class="section grid gap-10 md:grid-cols-[1.6fr_2.4fr]">
            <div><div class="display-serif text-2xl leading-tight">Guided Wellness</div><p class="mt-3 max-w-sm text-[13px] leading-6 text-white/70">Redefining the supplement experience through transparency, guidance, and respect for biological complexity.</p></div>
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-3">
                <div><div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/60">Shop</div><a class="mt-3 block text-[13px] text-white/85 hover:text-white" href="{{ route('shop') }}">{{ !empty($isMyClosq) ? '30-Day My CLOSQ' : '30-Day Gut Reset' }}</a><a class="mt-2 block text-[13px] text-white/85 hover:text-white" href="{{ route('how-it-works') }}">How It Works</a></div>
                <div><div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/60">Learn</div><a class="mt-3 block text-[13px] text-white/85 hover:text-white" href="{{ url('/#learn') }}">Guides</a><a class="mt-2 block text-[13px] text-white/85 hover:text-white" href="{{ url('/#standards') }}">Standards</a><a class="mt-2 block text-[13px] text-white/85 hover:text-white" href="{{ url('/#faq') }}">About</a></div>
                <div><div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/60">Support</div><a class="mt-3 block text-[13px] text-white/85 hover:text-white" href="{{ route('account') }}">Account</a><a class="mt-2 block text-[13px] text-white/85 hover:text-white" href="{{ url('/#faq') }}">Help Centre</a></div>
            </div>
        </div>
        <div class="border-t border-white/15 py-5 text-center text-[11px] text-white/60">© 2026 Kurate Wellness Private Limited. All rights reserved.</div>
    </footer>
</body>
</html>