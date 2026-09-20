@extends('layouts.course')

@section('content')
<div class="mx-auto max-w-[1500px] px-5 py-8 md:px-10 md:py-12">
    @if(session('success'))<div class="mb-7 rounded-2xl border border-[#b9cfbe] bg-[#edf5ee] px-5 py-4 text-sm text-[#204b34]">{{ session('success') }}</div>@endif
    <div class="grid gap-7 lg:grid-cols-[1.2fr_.8fr]">
        <section class="rounded-3xl bg-[#204b34] p-8 text-white md:p-12">
            <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-white/60">Gut Response Brief · Cycle #{{ $profile->cycle_number }}</div>
            <h1 class="display-serif mt-5 text-5xl leading-[1.02] md:text-7xl">{{ $profile->status === 'completed' ? 'Your 30-day response, in one place.' : 'Your course is still collecting evidence.' }}</h1>
            <p class="mt-6 max-w-2xl text-[16px] leading-8 text-white/75">This brief reflects what you recorded during this cycle. It does not turn the observations into a diagnosis.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                @if($profile->status === 'completed')<a href="{{ route('reset.testimonial') }}" class="rounded-xl bg-white px-6 py-4 text-sm font-semibold text-[#204b34]">Share a testimonial</a>@endif
                <a href="{{ route('reset.home') }}" class="rounded-xl border border-white/25 px-6 py-4 text-sm font-semibold text-white">Back to course home</a>
            </div>
        </section>

        <aside class="rounded-3xl border border-stone-200 bg-[#f3f0e9] p-8 md:p-10">
            <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">Cycle dates</div>
            <div class="mt-6 space-y-4 text-sm">
                <div class="flex justify-between border-b border-stone-200 pb-4"><span>Day 0 complete</span><span class="font-semibold">{{ $profile->day0_completed_at?->format('d M Y H:i') ?: 'Pending' }}</span></div>
                <div class="flex justify-between border-b border-stone-200 pb-4"><span>Start date</span><span class="font-semibold">{{ $profile->actual_start_date?->format('d M Y') ?: ($profile->startPlan?->selected_start_date?->format('d M Y') ?: 'Pending') }}</span></div>
                <div class="flex justify-between"><span>Day 30 complete</span><span class="font-semibold">{{ $profile->day30_completed_at?->format('d M Y H:i') ?: 'Pending' }}</span></div>
            </div>
        </aside>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-4">
        <div class="rounded-3xl border border-stone-200 bg-white p-7"><div class="text-[11px] uppercase tracking-[.18em] text-stone-500">Capsules recorded</div><div class="display-serif mt-3 text-5xl">{{ $gri?->capsules_taken ?? $profile->resetCardUsage->where('marked_yes_no',true)->count() }}</div><div class="mt-2 text-sm text-stone-500">out of 30 days</div></div>
        <div class="rounded-3xl border border-stone-200 bg-white p-7"><div class="text-[11px] uppercase tracking-[.18em] text-stone-500">GRI</div><div class="display-serif mt-3 text-5xl">{{ $gri?->gri_score !== null ? rtrim(rtrim(number_format((float)$gri->gri_score,2,'.',''), '0'), '.') : '—' }}</div><div class="mt-2 text-sm text-stone-500">Band: {{ $gri?->gri_band ?? 'Pending' }}</div></div>
        <div class="rounded-3xl border border-stone-200 bg-white p-7"><div class="text-[11px] uppercase tracking-[.18em] text-stone-500">GRS movement</div><div class="display-serif mt-3 text-5xl">{{ $grs?->grs_movement !== null ? (($grs->grs_movement>0?'+':'').rtrim(rtrim(number_format((float)$grs->grs_movement,2,'.',''), '0'), '.')) : '—' }}</div><div class="mt-2 text-sm text-stone-500">{{ $grs?->movement_classification ?? 'Pending' }}</div></div>
        <div class="rounded-3xl border border-stone-200 bg-white p-7"><div class="text-[11px] uppercase tracking-[.18em] text-stone-500">Comfort delta</div><div class="display-serif mt-3 text-5xl">{{ $grs?->comfort_delta !== null ? (($grs->comfort_delta>0?'+':'').rtrim(rtrim(number_format((float)$grs->comfort_delta,2,'.',''), '0'), '.')) : '—' }}</div><div class="mt-2 text-sm text-stone-500">Day 30 minus Day 0</div></div>
    </div>

    @if($gri && $grs)
        <section class="mt-8 grid gap-7 lg:grid-cols-2">
            <div class="rounded-3xl border border-stone-200 bg-white p-8 md:p-9">
                <div class="text-[11px] uppercase tracking-[.18em] text-stone-500">Score logic</div>
                <h2 class="display-serif mt-3 text-3xl">GRI</h2>
                <p class="mt-5 text-sm leading-7 text-stone-600">GRI = (capsules taken × 2) + (Day 30 comfort × 4), capped at 100.</p>
                <div class="mt-5 rounded-2xl bg-[#f3f0e9] p-5 text-sm">{{ $gri->capsules_taken }} × 2 + {{ $gri->day30_comfort_rating }} × 4 = <strong>{{ $gri->gri_score }}</strong> · band {{ $gri->gri_band }}</div>
            </div>
            <div class="rounded-3xl border border-stone-200 bg-white p-8 md:p-9">
                <div class="text-[11px] uppercase tracking-[.18em] text-stone-500">Movement</div>
                <h2 class="display-serif mt-3 text-3xl">GRS</h2>
                <p class="mt-5 text-sm leading-7 text-stone-600">GRS movement = Day 0 symptom burden − Day 30 symptom burden. Comfort delta = Day 30 comfort − Day 0 comfort.</p>
                <div class="mt-5 rounded-2xl bg-[#f3f0e9] p-5 text-sm">{{ $grs->day0_symptom_burden }} − {{ $grs->day30_symptom_burden }} = <strong>{{ $grs->grs_movement }}</strong></div>
            </div>
        </section>
    @endif

    <section class="mt-8 grid gap-7 lg:grid-cols-[1fr_.75fr]">
        <div class="rounded-3xl border border-stone-200 bg-white p-8 md:p-9">
            <div class="text-[11px] uppercase tracking-[.18em] text-stone-500">Day 0 → Day 30</div>
            <h2 class="display-serif mt-3 text-3xl">What changed in the numbers?</h2>
            <div class="mt-7 overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b text-left"><th class="p-3">Signal</th><th class="p-3">Day 0</th><th class="p-3">Day 30</th></tr></thead><tbody>@foreach([['Bloating','bloating_score'],['Gas / burping','gas_burping_score'],['Heaviness','heaviness_score'],['Acidity','acidity_score'],['Overall comfort','overall_comfort_score']] as [$label,$field])<tr class="border-b"><td class="p-3 font-semibold">{{ $label }}</td><td class="p-3">{{ $profile->gutSignalCheckpoints->where('checkpoint_day',0)->first()?->{$field} ?? '—' }}</td><td class="p-3">{{ $profile->gutSignalCheckpoints->where('checkpoint_day',30)->first()?->{$field} ?? '—' }}</td></tr>@endforeach</tbody></table></div>
        </div>
        <aside class="rounded-3xl border border-stone-200 bg-[#fcfbf7] p-8 md:p-9"><div class="text-[11px] uppercase tracking-[.18em] text-stone-500">Your focus</div><div class="mt-6 flex flex-wrap gap-2">@foreach($profile->day0Baseline?->priorityAreas ?? [] as $area)<span class="rounded-full bg-[#e3e8df] px-4 py-2 text-sm text-[#204b34]">{{ ucwords(str_replace('_',' ',$area->priority_area)) }}</span>@endforeach</div><p class="mt-6 text-sm leading-7 text-stone-600">These areas were selected at Day 0. The brief reports what you recorded against them; it does not infer a cause.</p></aside>
    </section>

    <section class="mt-8 rounded-3xl border border-stone-200 bg-white p-8 md:p-9">
        <div class="flex flex-wrap items-end justify-between gap-4"><div><div class="text-[11px] uppercase tracking-[.18em] text-stone-500">Milestone timeline</div><h2 class="display-serif mt-3 text-3xl">Six programme moments</h2></div><div class="text-sm text-stone-500">{{ count($completedDays) }} daily check-ins recorded</div></div>
        <div class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach([0,3,7,14,21,30] as $milestoneDay)
                @php
                    if ($milestoneDay === 0) {
                        $m = $profile->day0_completed_at ? (object) ['completed_at' => $profile->day0_completed_at] : null;
                    } elseif ($milestoneDay === 30) {
                        $m = $profile->day30_completed_at ? (object) ['completed_at' => $profile->day30_completed_at] : $profile->milestoneCheckins->where('milestone_day', 30)->first();
                    } else {
                        $m = $profile->milestoneCheckins->where('milestone_day', $milestoneDay)->first();
                    }
                @endphp
                <div class="rounded-2xl border {{ $m ? 'border-[#b9cfbe] bg-[#f0f4ef]' : 'border-stone-200 bg-[#fcfbf7]' }} p-5"><div class="text-xs uppercase tracking-[.16em] text-stone-500">Day {{ $milestoneDay }}</div><div class="mt-2 text-sm font-semibold">{{ $m ? 'Completed' : 'Pending' }}</div><div class="mt-1 text-xs text-stone-500">{{ $milestoneDay === 0 ? ($profile->day0_completed_at?->format('d M Y H:i') ?: '') : ($m?->completed_at?->format('d M Y H:i') ?: ($profile->day30_completed_at?->format('d M Y H:i') ?: '')) }}</div></div>
            @endforeach
        </div>
    </section>

    @if($profile->status === 'completed')
        <section class="mt-8 grid gap-7 lg:grid-cols-2">
            <div class="rounded-3xl bg-[#f3f0e9] p-8 md:p-9"><div class="text-[11px] uppercase tracking-[.18em] text-stone-500">Next step</div><h2 class="display-serif mt-3 text-3xl">{{ $profile->day30Decision?->user_recommendation }}</h2><p class="mt-5 text-sm leading-7 text-stone-600">{{ $profile->day30Decision?->commercial_action }}</p></div>
            <div class="rounded-3xl border border-stone-200 bg-white p-8 md:p-9"><div class="text-[11px] uppercase tracking-[.18em] text-stone-500">Re-entry</div><h2 class="display-serif mt-3 text-3xl">Your account stays open.</h2><p class="mt-4 text-sm leading-7 text-stone-600">A later cycle is created only after a new re-entry request is reviewed and approved. Your earlier cycle remains available.</p><a href="{{ route('reset.reentry') }}" class="mt-6 inline-flex rounded-xl bg-[#204b34] px-5 py-3 text-sm font-semibold text-white">Request another cycle</a></div>
        </section>
    @endif

    <section class="mt-8">
        <div class="text-[11px] uppercase tracking-[.18em] text-stone-500">All cycles</div>
        <div class="mt-4 grid gap-3 md:grid-cols-2 lg:grid-cols-3">
            @foreach($history as $cycle)
                <a href="{{ route('reset.report',['cycle'=>$cycle->cycle_number]) }}" class="rounded-2xl border {{ $cycle->id === $profile->id ? 'border-[#78917f] bg-[#f0f4ef]' : 'border-stone-200 bg-white' }} p-5"><div class="text-sm font-semibold">Cycle #{{ $cycle->cycle_number }}</div><div class="mt-1 text-xs text-stone-500">{{ ucfirst(str_replace('_',' ',$cycle->status)) }} · {{ $cycle->created_at?->format('d M Y') }}</div></a>
            @endforeach
        </div>
    </section>
</div>
@endsection
