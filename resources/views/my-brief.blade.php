@extends('layouts.course')

@section('content')
<div class="mx-auto max-w-[1600px] px-5 py-7 md:px-10 md:py-10">
    <div class="grid gap-7 lg:grid-cols-[1fr_1fr] lg:gap-12">
        <section class="rounded-2xl border border-stone-200 bg-[#fcfbf7] px-8 py-16 md:px-16 md:py-16">
            <span class="inline-flex rounded-full bg-[#242019] px-4 py-2 text-[13px] font-semibold uppercase tracking-[.16em] text-white">{{ $currentDay > 0 ? 'Course in progress' : 'Ready to start' }}</span>
            <h1 class="display-serif mt-8 text-5xl leading-[1.06] md:text-6xl">{{ $currentDay > 0 ? 'Continue your course.' : 'Start before capsule one.' }}</h1>
            <p class="mt-7 max-w-xl text-[16px] leading-8 text-stone-600">{{ $currentDay > 0 ? 'Your course is in progress. Continue with your next check-in and keep your response story clear.' : 'Set your starting point before taking the first capsule. This gives your Day 30 Gut Response Brief a clear place to begin.' }}</p>
            <a id="plan" href="{{ $currentDay > 0 ? '#progress' : route('my-plan') }}" class="mt-8 inline-flex min-h-20 min-w-64 flex-col items-center justify-center rounded-xl bg-[#204b34] px-8 text-center text-[16px] font-semibold text-white shadow-[0_3px_8px_rgba(20,45,31,.18)] transition hover:bg-[#173d29]"><span>{{ $currentDay > 0 ? 'View My Progress' : 'Set My Starting Point' }}</span><span class="mt-1 text-[13px] font-medium text-white/75">{{ $currentDay > 0 ? 'Day '.$currentDay.' of 30' : 'Around 2 minutes' }}</span></a>
            @if($currentDay === 0)<p class="mt-4 text-[14px] text-stone-600">Already taken your first capsule? <a class="underline decoration-[#204b34] underline-offset-4" href="#progress">Continue Honestly</a></p>@endif
        </section>
        <div class="min-h-[360px] overflow-hidden rounded-2xl bg-[#ddd2c2] lg:min-h-0 lg:aspect-[1/1.03]"><img src="https://guided-gut-reset-lovable-app.lovable.app/assets/hero-product-CftTmpl1.jpg" alt="Guided Wellness 30-Day Gut Reset bottle and carton" class="h-full w-full object-cover"></div>
    </div>

    <section id="progress" class="mt-10 grid gap-7 lg:grid-cols-[1.3fr_.7fr]">
        <div class="rounded-2xl border border-stone-200 bg-white p-7 md:p-9"><div class="flex flex-wrap items-end justify-between gap-4"><div><div class="text-[12px] font-medium uppercase tracking-[.16em] text-stone-500">Your progress</div><h2 class="display-serif mt-2 text-4xl">Day {{ $currentDay }} of 30</h2></div><div class="text-sm text-stone-500">{{ count($completedDays) }} check-ins complete</div></div><div class="mt-7 h-3 overflow-hidden rounded-full bg-stone-100"><div class="h-full rounded-full bg-[#204b34]" style="width: {{ $currentDay / 30 * 100 }}%"></div></div><div class="mt-7 grid grid-cols-6 gap-2 sm:grid-cols-10">@for($day = 1; $day <= 30; $day++)<span class="aspect-square rounded-sm {{ in_array($day, $completedDays, true) ? 'bg-[#204b34]' : ($day <= $currentDay ? 'bg-[#acbba7]' : 'bg-stone-100') }}" title="Day {{ $day }}"></span>@endfor</div></div>
        <div id="brief" class="rounded-2xl bg-[#204b34] p-7 text-white md:p-9"><div class="text-[12px] font-medium uppercase tracking-[.16em] text-white/70">My Brief</div><h2 class="display-serif mt-3 text-3xl leading-tight">{{ $profile?->day30Decision ? 'Your brief is ready.' : 'Available at Day 30.' }}</h2><p class="mt-5 text-[15px] leading-7 text-white/75">{{ $profile?->day30Decision?->user_recommendation ?: 'Your personal response brief will bring together the course, check-ins, and real-life context.' }}</p></div>
    </section>
</div>
@endsection
