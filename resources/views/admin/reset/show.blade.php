@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10 space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('admin.reset.operations') }}" class="text-sm text-stone-500 hover:text-stone-900">← Day 0–30 operations</a>
            <h1 class="serif mt-3 text-4xl">Reset profile #{{ $resetProfile->id }} · Cycle #{{ $resetProfile->cycle_number }}</h1>
            <p class="mt-2 text-sm text-stone-500">{{ $resetProfile->user?->name }} · {{ $resetProfile->user?->email }}</p>
        </div>
        <div class="flex flex-wrap gap-2 text-xs">
            <span class="rounded-full bg-stone-100 px-3 py-2 font-semibold uppercase tracking-wide">{{ str_replace('_',' ',$resetProfile->status) }}</span>
            <span class="rounded-full {{ $resetProfile->safety_flag_active ? 'bg-amber-100 text-amber-900' : 'bg-emerald-100 text-emerald-900' }} px-3 py-2 font-semibold uppercase tracking-wide">{{ $resetProfile->safety_flag_active ? 'Safety paused' : 'No active safety flag' }}</span>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="card"><div class="text-xs uppercase text-stone-500">Current day</div><div class="mt-2 text-2xl font-bold">{{ $resetProfile->status === 'completed' ? 30 : $resetProfile->current_reset_day }} / 30</div></div>
        <div class="card"><div class="text-xs uppercase text-stone-500">Day 0</div><div class="mt-2 text-sm font-semibold">{{ $resetProfile->day0_completed_at?->format('d M Y H:i') ?: 'Pending' }}</div></div>
        <div class="card"><div class="text-xs uppercase text-stone-500">Day 30</div><div class="mt-2 text-sm font-semibold">{{ $resetProfile->day30_completed_at?->format('d M Y H:i') ?: 'Pending' }}</div></div>
        <div class="card"><div class="text-xs uppercase text-stone-500">Capsules</div><div class="mt-2 text-2xl font-bold">{{ $resetProfile->resetCardUsage->where('marked_yes_no', true)->count() }}</div></div>
        <div class="card"><div class="text-xs uppercase text-stone-500">Manual review</div><div class="mt-2 text-2xl font-bold">{{ $resetProfile->manual_review_required ? 'Yes' : 'No' }}</div></div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="card">
            <h2 class="font-bold">Day 0 baseline</h2>
            <div class="mt-5 grid gap-4 sm:grid-cols-2 text-sm">
                <div><div class="text-stone-500">Primary expectation</div><div class="mt-1 font-semibold">{{ $resetProfile->day0Baseline?->success_expectation_primary ?: '—' }}</div></div>
                <div><div class="text-stone-500">Regularity</div><div class="mt-1 font-semibold">{{ $resetProfile->day0Baseline?->regularity_frequency ?: '—' }}</div></div>
                <div><div class="text-stone-500">Rescue remedy</div><div class="mt-1 font-semibold">{{ $resetProfile->day0Baseline?->rescue_remedy_used ?: '—' }}</div></div>
                <div><div class="text-stone-500">WhatsApp opt-in</div><div class="mt-1 font-semibold">{{ $resetProfile->day0Baseline?->whatsapp_opt_in ? 'Yes' : 'No' }}</div></div>
            </div>
            <div class="mt-5"><div class="text-xs uppercase tracking-wide text-stone-500">Priority areas</div><div class="mt-2 flex flex-wrap gap-2">@foreach($resetProfile->day0Baseline?->priorityAreas ?? [] as $area)<span class="rounded-full bg-stone-100 px-3 py-1.5 text-xs">{{ ucwords(str_replace('_',' ',$area->priority_area)) }}</span>@endforeach</div></div>
            <div class="mt-5"><div class="text-xs uppercase tracking-wide text-stone-500">Triggers</div><div class="mt-2 flex flex-wrap gap-2">@forelse($resetProfile->day0Baseline?->triggers ?? [] as $trigger)<span class="rounded-full bg-stone-100 px-3 py-1.5 text-xs">{{ $trigger->trigger_name }}</span>@empty<span class="text-sm text-stone-500">None recorded</span>@endforelse</div></div>
        </section>

        <section class="card">
            <h2 class="font-bold">Signal checkpoints</h2>
            <div class="mt-5 overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b"><th class="p-2 text-left">Day</th><th class="p-2">Bloating</th><th class="p-2">Gas</th><th class="p-2">Heavy</th><th class="p-2">Acid</th><th class="p-2">Comfort</th></tr></thead><tbody>@foreach([0,3,7,14,21,30] as $d) @php $c=$resetProfile->gutSignalCheckpoints->where('checkpoint_day',$d)->first(); @endphp<tr class="border-b"><td class="p-2 font-semibold">{{ $d }}</td><td class="p-2 text-center">{{ $c?->bloating_score ?? '—' }}</td><td class="p-2 text-center">{{ $c?->gas_burping_score ?? '—' }}</td><td class="p-2 text-center">{{ $c?->heaviness_score ?? '—' }}</td><td class="p-2 text-center">{{ $c?->acidity_score ?? '—' }}</td><td class="p-2 text-center">{{ $c?->overall_comfort_score ?? '—' }}</td></tr>@endforeach</tbody></table></div>
        </section>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.3fr_.7fr]">
        <section class="card overflow-x-auto">
            <div class="flex items-end justify-between gap-4"><div><h2 class="font-bold">Daily activity</h2><p class="mt-1 text-sm text-stone-500">One record per programme day.</p></div><div class="text-sm">{{ $resetProfile->dailyAdherence->where('response_status','completed')->count() }} completed</div></div>
            <table class="mt-5 w-full text-sm"><thead><tr class="border-b"><th class="p-2 text-left">Day</th><th class="p-2">Calendar date</th><th class="p-2">Status</th><th class="p-2">Capsule</th><th class="p-2">Responded</th></tr></thead><tbody>@foreach($resetProfile->dailyAdherence->sortBy('reset_day') as $row)<tr class="border-b"><td class="p-2 font-semibold">{{ $row->reset_day }}</td><td class="p-2 text-center">{{ $row->calendar_date }}</td><td class="p-2 text-center">{{ $row->response_status ?: '—' }}</td><td class="p-2 text-center">{{ optional($resetProfile->resetCardUsage->where('reset_day',$row->reset_day)->first())->marked_yes_no ? 'Yes' : 'No' }}</td><td class="p-2 text-center">{{ $row->responded_at?->format('d M H:i') ?: '—' }}</td></tr>@endforeach</tbody></table>
        </section>

        <section class="card">
            <h2 class="font-bold">Scores</h2>
            @php $gri=$resetProfile->griScores->first(); $grs=$resetProfile->grsScores->first(); @endphp
            <div class="mt-5 space-y-4 text-sm">
                <div class="rounded-2xl bg-stone-50 p-4"><div class="text-xs uppercase tracking-wide text-stone-500">GRI</div><div class="mt-1 text-3xl font-bold">{{ $gri?->gri_score ?? '—' }}</div><div class="mt-1 text-stone-500">{{ $gri?->gri_band ?? 'Pending' }}</div></div>
                <div class="rounded-2xl bg-stone-50 p-4"><div class="text-xs uppercase tracking-wide text-stone-500">GRS movement</div><div class="mt-1 text-3xl font-bold">{{ $grs?->grs_movement ?? '—' }}</div><div class="mt-1 text-stone-500">{{ $grs?->movement_classification ?? 'Pending' }} · comfort Δ {{ $grs?->comfort_delta ?? '—' }}</div></div>
            </div>
        </section>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="card">
            <h2 class="font-bold">Unusual events & safety</h2>
            <div class="mt-5 space-y-3">
                @forelse($resetProfile->unusualEvents as $event)
                    <div class="rounded-2xl border border-stone-200 p-4"><div class="flex justify-between gap-4"><span class="font-semibold">Day {{ $event->reset_day }} · {{ $event->symptom_type }}</span><span class="text-xs uppercase text-stone-500">{{ $event->severity }}</span></div><p class="mt-2 text-sm text-stone-600">{{ $event->description ?: 'No description' }}</p></div>
                @empty
                    <p class="text-sm text-stone-500">No unusual events recorded.</p>
                @endforelse
            </div>
            @if($resetProfile->safetyFlags->count())
                <div class="mt-6 border-t pt-5 space-y-3">@foreach($resetProfile->safetyFlags as $flag)<div class="rounded-2xl bg-amber-50 p-4"><div class="flex justify-between"><b>{{ $flag->flag_type }}</b><span>{{ $flag->manual_review_required ? 'Open' : 'Resolved' }}</span></div><p class="mt-2 text-sm">{{ $flag->resolution ?: 'Awaiting review.' }}</p>@if($flag->manual_review_required)<form class="mt-4" method="POST" action="{{ route('admin.reset.safety.resolve',$flag) }}">@csrf<textarea name="resolution" required rows="2" class="w-full rounded-xl border border-stone-300 p-3" placeholder="Record the review resolution"></textarea><button class="mt-2 rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-white">Resolve</button></form>@endif</div>@endforeach</div>
            @endif
        </section>

        <section class="card">
            <h2 class="font-bold">Day 30 review</h2>
            @if($resetProfile->finalReview)
                <div class="mt-5 grid gap-4 sm:grid-cols-2 text-sm">@foreach([['Capsule consistency',$resetProfile->finalReview->capsule_consistency],['Product comfort',$resetProfile->finalReview->product_comfort],['User verdict',$resetProfile->finalReview->user_verdict],['Repeat intent',$resetProfile->finalReview->continue_repeat_intent],['Recommendation',$resetProfile->finalReview->recommendation_intent],['Trigger pattern',$resetProfile->finalReview->trigger_pattern]] as $item)<div><div class="text-stone-500">{{ $item[0] }}</div><div class="mt-1 font-semibold">{{ $item[1] ?: '—' }}</div></div>@endforeach</div>
                <div class="mt-5 rounded-2xl bg-stone-50 p-4 text-sm"><div class="text-xs uppercase tracking-wide text-stone-500">Decision</div><div class="mt-2 font-semibold">{{ $resetProfile->day30Decision?->next_step_code ?: '—' }}</div><p class="mt-2 text-stone-600">{{ $resetProfile->day30Decision?->user_recommendation ?: '' }}</p></div>
            @else
                <p class="mt-5 text-sm text-stone-500">Day 30 final review has not been completed.</p>
            @endif
        </section>
    </div>
</div>
@endsection
