@extends('layouts.course')

@section('content')
<div class="mx-auto max-w-[1550px] px-5 py-8 md:px-10 md:py-12">
    @if(session('success'))
        <div class="mb-7 rounded-2xl border border-[#b9cfbe] bg-[#edf5ee] px-5 py-4 text-sm text-[#204b34]">{{ session('success') }}</div>
    @endif

    @if($profile?->safety_flag_active)
        <div class="mb-7 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
            <div class="font-semibold">Your reset is paused for review.</div>
            <div class="mt-1">A programme review is required before automated reminders or the next daily step continue.</div>
        </div>
    @endif

    <div class="grid gap-7 lg:grid-cols-[1.25fr_.75fr]">
        <section class="rounded-3xl bg-[#204b34] p-8 text-white md:p-12">
            <div class="text-[11px] font-semibold uppercase tracking-[.2em] text-white/60">30-Day Myclosq</div>
            <h1 class="display-serif mt-7 max-w-3xl text-5xl leading-[1.02] md:text-7xl">
                {{ $profile?->status === 'completed' ? 'Your reset is complete.' : ($currentDay ? 'Continue your reset.' : 'Start with a clear baseline.') }}
            </h1>
            <p class="mt-7 max-w-2xl text-[16px] leading-8 text-white/75">
                {{ $profile?->status === 'completed' ? 'Your individual Gut Response Brief is ready. Your account stays active so previous cycles remain available.' : 'A simple sequence of Day 0 setup, daily capsule tracking, milestone moments and a Day 30 response brief.' }}
            </p>
            <div class="mt-9 flex flex-wrap gap-3">
                @if($profile?->status === 'completed')
                    <a class="rounded-xl bg-white px-6 py-4 text-[15px] font-semibold text-[#204b34]" href="{{ route('reset.report') }}">My Brief</a>
                    <a class="rounded-xl border border-white/30 px-6 py-4 text-[15px] font-semibold text-white" href="{{ route('reset.reentry') }}">Request another cycle</a>
                @elseif($currentDay > 0)
                    <a class="rounded-xl bg-white px-6 py-4 text-[15px] font-semibold text-[#204b34]" href="{{ route('reset.day', ['day' => $currentDay]) }}">Open Day {{ $currentDay }}</a>
                    <a class="rounded-xl border border-white/30 px-6 py-4 text-[15px] font-semibold text-white" href="{{ route('reset.report') }}">Progress</a>
                @else
                    <a class="rounded-xl bg-white px-6 py-4 text-[15px] font-semibold text-[#204b34]" href="{{ route('reset.day0') }}">Set up Day 0</a>
                @endif
            </div>
            @if($profile)
                <div class="mt-9 grid max-w-2xl grid-cols-3 gap-4 border-t border-white/15 pt-7 text-sm">
                    <div><div class="text-white/55">Cycle</div><div class="mt-1 font-semibold">#{{ $profile->cycle_number }}</div></div>
                    <div><div class="text-white/55">Status</div><div class="mt-1 font-semibold">{{ ucfirst(str_replace('_', ' ', $profile->status)) }}</div></div>
                    <div><div class="text-white/55">Day</div><div class="mt-1 font-semibold">{{ $currentDay }} / 30</div></div>
                </div>
            @endif
        </section>

        <aside class="overflow-hidden rounded-3xl border border-stone-200 bg-[#f3f0e9]">
            <div class="p-8 md:p-10">
                <div class="text-[11px] font-semibold uppercase tracking-[.2em] text-stone-500">Journey map</div>
                <div class="mt-7 space-y-3">
                    @foreach([['01','Day 0','Starting point'],['02','Days 1-2','Daily routine'],['03','Day 3','First milestone'],['04','Day 7','Pattern check'],['05','Day 14','Mid-course check'],['06','Day 21','Final milestone'],['07','Day 30','Gut Response Brief']] as $step)
                        <div class="flex items-center gap-4 rounded-2xl border border-stone-200 bg-white px-4 py-4">
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-[#e3e8df] text-xs font-bold text-[#204b34]">{{ $step[0] }}</span>
                            <div><div class="text-sm font-semibold">{{ $step[1] }}</div><div class="mt-0.5 text-xs text-stone-500">{{ $step[2] }}</div></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>

    <div class="mt-8 grid gap-7 lg:grid-cols-[1.2fr_.8fr]">
        <section class="rounded-3xl border border-stone-200 bg-white p-7 md:p-9">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div><div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">Progress</div><h2 class="display-serif mt-2 text-4xl">Day {{ $currentDay }} of 30</h2></div>
                <div class="text-sm text-stone-500">{{ count($completedDays) }} completed day{{ count($completedDays) === 1 ? '' : 's' }}</div>
            </div>
            <div class="mt-7 h-3 overflow-hidden rounded-full bg-stone-100"><div class="h-full rounded-full bg-[#204b34]" style="width: {{ min(100, ($currentDay / 30) * 100) }}%"></div></div>
            <div class="mt-6 grid grid-cols-6 gap-2 sm:grid-cols-10">
                @for($day = 1; $day <= 30; $day++)
                    <a href="{{ $day <= $currentDay ? route('reset.day', ['day' => $day]) : '#' }}" class="aspect-square rounded-md {{ in_array($day, $completedDays, true) ? 'bg-[#204b34]' : ($day === $currentDay ? 'bg-[#78917f]' : ($day < $currentDay ? 'bg-[#acbba7]' : 'bg-stone-100')) }}" title="Day {{ $day }}"></a>
                @endfor
            </div>
        </section>

        <section class="rounded-3xl border border-stone-200 bg-[#fcfbf7] p-7 md:p-9">
            <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">Past cycles</div>
            <div class="mt-5 space-y-3">
                @forelse($history as $cycle)
                    <a href="{{ route('reset.report', ['cycle' => $cycle->cycle_number]) }}" class="flex items-center justify-between rounded-2xl border border-stone-200 bg-white px-5 py-4 transition hover:border-[#78917f]">
                        <div><div class="text-sm font-semibold">Cycle #{{ $cycle->cycle_number }}</div><div class="mt-1 text-xs text-stone-500">{{ $cycle->created_at?->format('d M Y') }} · {{ ucfirst(str_replace('_',' ',$cycle->status)) }}</div></div>
                        <span class="text-sm font-semibold text-[#204b34]">View</span>
                    </a>
                @empty
                    <p class="text-sm leading-7 text-stone-500">Your first reset cycle will appear here after activation.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
