@extends('layouts.course')

@section('content')
<div class="mx-auto max-w-[1200px] px-5 py-12 md:px-10 md:py-16">
    <div class="rounded-3xl border border-stone-200 bg-white p-8 md:p-14 shadow-sm text-center">
        <div class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full bg-[#f3f0e9] text-[#204b34]">
            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
        </div>

        <div class="mt-6 text-[12px] font-semibold uppercase tracking-[.2em] text-stone-500">Day 30 Brief</div>
        <h1 class="display-serif mt-3 text-4xl font-normal text-stone-900 md:text-5xl">No content added yet</h1>
        <p class="mx-auto mt-4 max-w-lg text-[15px] leading-7 text-stone-600">
            Your personalised Gut Response Brief will be automatically compiled and available here once your 30-day journey is complete.
        </p>

        <div class="mt-8 inline-flex items-center gap-3 rounded-full bg-[#f8f7f2] px-5 py-2.5 text-xs font-medium text-stone-600 border border-stone-200">
            <span class="inline-block h-2 w-2 rounded-full bg-[#204b34]"></span>
            <span>Current Progress: <strong>Day {{ $currentDay }} of 30</strong></span>
        </div>

        <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('reset.home') }}" class="rounded-xl bg-[#204b34] px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#173d29]">
                Continue Reset Journey
            </a>
            <a href="{{ route('my-plan') }}" class="rounded-xl border border-stone-300 bg-white px-6 py-3.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-50">
                View My Plan
            </a>
        </div>
    </div>
</div>
@endsection
