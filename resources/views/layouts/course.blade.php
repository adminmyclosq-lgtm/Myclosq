<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'My Brief - Guided Wellness' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f7f2] text-[#27241f]">
    <x-site-header />
    <style>
        .course-layout-container {
            display: flex;
            min-height: calc(100vh - 4rem);
            width: 100%;
        }
        .course-layout-sidebar {
            width: 280px;
            min-width: 280px;
            max-width: 280px;
            flex-shrink: 0;
            background-color: #f5f3ed;
            border-right: 1px solid #e7e5e4;
            display: flex;
            flex-direction: column;
        }
        .course-layout-main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: #f8f7f2;
        }
        @media (max-width: 768px) {
            .course-layout-container {
                flex-direction: column;
            }
            .course-layout-sidebar {
                width: 100%;
                min-width: 100%;
                max-width: 100%;
                border-right: none;
                border-bottom: 1px solid #e7e5e4;
            }
        }
    </style>
    <div class="course-layout-container">
        <aside class="course-layout-sidebar">
            <div class="px-7 py-6">
                <div class="text-[13px] font-bold uppercase tracking-[.15em] text-stone-800">30-Day Gut Reset</div>
                <div class="mt-5 text-[11px] font-medium uppercase tracking-[.15em] text-stone-500">Current status</div>
                <div class="mt-2 inline-flex rounded-full bg-[#242019] px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[.13em] text-white">{{ $profile?->status === 'completed' ? 'Complete' : ($currentDay > 0 ? 'In progress' : 'Setting up') }}</div>
            </div>
            <nav class="border-y border-stone-200 px-5 py-5" aria-label="Course navigation">
                <a href="{{ route('reset.home') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-[13px] font-medium uppercase tracking-[.08em] transition {{ request()->routeIs('reset.home') ? 'bg-[#e3e5de] text-[#284c38]' : 'text-stone-600 hover:bg-stone-100' }}"><svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m3 11 9-7 9 7v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z" /></svg>Course Home</a>
                <a href="{{ route('my-plan') }}" class="mt-1 flex items-start gap-3 rounded-xl px-4 py-3 text-[13px] font-medium uppercase tracking-[.08em] transition {{ request()->routeIs('my-plan') || request()->routeIs('reset.day0') ? 'bg-[#e3e5de] text-[#284c38]' : 'text-stone-600 hover:bg-stone-100' }}"><svg class="mt-0.5 h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M6 2h9l4 4v16H6z" /><path d="M15 2v5h5" /></svg><span>My Plan<span class="mt-1.5 block text-[13px] normal-case font-normal tracking-normal text-stone-400">{{ $profile?->day0Baseline?->priorityAreas->isNotEmpty() ? 'Focus areas saved' : ($profile ? 'View your plan' : 'Set up first') }}</span></span></a>
                <a href="{{ route('reset.home') }}#progress" class="mt-1 flex items-start gap-3 rounded-xl px-4 py-3 text-[13px] font-medium uppercase tracking-[.08em] text-stone-600 transition hover:bg-stone-100"><svg class="mt-0.5 h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 20V10m6 10V4m6 16v-7m4 7H2" /></svg><span>Progress<span class="mt-1.5 block text-[13px] normal-case font-normal tracking-normal text-stone-400">{{ $currentDay > 0 ? 'Day '.$currentDay.' of 30' : 'Begins after capsule one' }}</span></span></a>
                <a href="{{ route('reset.report') }}" class="mt-1 flex items-start gap-3 rounded-xl px-4 py-3 text-[13px] font-medium uppercase tracking-[.08em] transition {{ request()->routeIs('reset.report') || request()->routeIs('report') ? 'bg-[#e3e5de] text-[#284c38]' : 'text-stone-600 hover:bg-stone-100' }}"><svg class="mt-0.5 h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M6 3h12v18H6z" /><path d="M9 8h6m-6 4h6m-6 4h4" /></svg><span>My Brief<span class="mt-1.5 block text-[13px] normal-case font-normal tracking-normal text-stone-400">{{ $profile?->day30Decision ? 'Ready to view' : 'Available at Day 30' }}</span></span></a>
            </nav>
            <div class="mt-auto hidden border-t border-stone-200 px-5 py-5 md:block">
                <a href="{{ route('account') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-[13px] font-medium uppercase tracking-[.08em] whitespace-nowrap transition {{ request()->routeIs('account*') ? 'bg-[#e3e5de] text-[#284c38]' : 'text-stone-600 hover:bg-stone-100' }}">
                    <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.06 2.06-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1.03 1.56v.08h-2.92v-.08A1.7 1.7 0 0 0 10.82 18.6a1.7 1.7 0 0 0-1.88.34l-.06.06-2.06-2.06.06-.06A1.7 1.7 0 0 0 7.22 15a1.7 1.7 0 0 0-1.56-1.03h-.08v-2.92h.08A1.7 1.7 0 0 0 7.22 10a1.7 1.7 0 0 0-.34-1.88l-.06-.06L8.88 6l.06.06A1.7 1.7 0 0 0 10.82 6.4a1.7 1.7 0 0 0 1.03-1.56v-.08h2.92v.08A1.7 1.7 0 0 0 15.8 6.4a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.06 2.06-.06.06A1.7 1.7 0 0 0 19.4 10a1.7 1.7 0 0 0 1.56 1.03h.08v2.92h-.08A1.7 1.7 0 0 0 19.4 15Z" /></svg>
                    Account &amp; Settings
                </a>
                <a href="{{ route('home') }}" class="mt-1 flex items-center gap-3 rounded-xl px-4 py-3 text-[13px] font-medium uppercase tracking-[.08em] whitespace-nowrap text-stone-600 transition hover:bg-stone-100">
                    <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>
                    Back to website
                </a>
            </div>
        </aside>
        <main class="course-layout-main">
            @yield('content')
        </main>
    </div>
</body>
</html>