@extends('layouts.course')

@section('content')
@php
    $isMilestone = in_array($day, [3,7,14,21,30], true);
    $milestoneTitle = [3=>'First milestone',7=>'Pattern check',14=>'Mid-course check',21=>'Final milestone',30=>'Day 30 response'][ $day ] ?? null;
@endphp
<div class="mx-auto max-w-[1450px] px-5 py-8 md:px-10 md:py-12">
    @if(session('success'))<div class="mb-7 rounded-2xl border border-[#b9cfbe] bg-[#edf5ee] px-5 py-4 text-sm text-[#204b34]">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mb-7 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif

    @if($profile->safety_flag_active)
        <div class="mb-8 rounded-3xl border border-amber-200 bg-amber-50 p-7 text-amber-900">
            <div class="text-[11px] font-semibold uppercase tracking-[.18em]">Paused for review</div>
            <h1 class="display-serif mt-2 text-4xl">This day is temporarily paused.</h1>
            <p class="mt-4 max-w-3xl text-sm leading-7">Your unusual event has been recorded. The next automated course action stays paused until the programme team resolves the safety review.</p>
        </div>
    @endif

    <div class="grid gap-7 lg:grid-cols-[1fr_.42fr]">
        <div>
            <div class="flex flex-wrap items-center gap-3"><div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">Day {{ $day }} of 30 @if($isMilestone) · {{ $milestoneTitle }}@endif</div>@if($isCurrent)<span class="rounded-full bg-[#e3e8df] px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[.15em] text-[#204b34]">Current day</span>@elseif($profile->status === 'completed')<span class="rounded-full bg-stone-200 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[.15em] text-stone-600">Cycle complete</span>@endif</div>
            <h1 class="display-serif mt-4 text-5xl leading-[1.03] md:text-6xl">{{ $day === 30 ? 'Close the course with your Day 30 response.' : 'Keep today simple.' }}</h1>
            <p class="mt-6 max-w-3xl text-[16px] leading-8 text-stone-600">{{ $day === 30 ? 'Record your final response, complete the final review and generate your Gut Response Brief.' : 'Mark the capsule routine honestly. On milestone days, add the short response checkpoint below.' }}</p>
            @if(!$isCurrent && $profile->status !== 'completed')
                <div class="mt-5 rounded-2xl border border-stone-200 bg-white px-5 py-4 text-sm text-stone-600">This is not your current day. The programme will accept the check-in only for Day {{ $profile->current_reset_day }}.</div>
            @endif

            <form method="POST" action="{{ route('reset.day.store',['day'=>$day]) }}" class="mt-9 space-y-7">
                @csrf
                <section class="rounded-3xl border border-stone-200 bg-white p-7 md:p-9">
                    <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">Daily capsule</div>
                    <h2 class="display-serif mt-3 text-3xl">Did you take your capsule today?</h2>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <label class="cursor-pointer rounded-2xl border border-stone-200 bg-[#fcfbf7] p-5 has-[:checked]:border-[#204b34] has-[:checked]:bg-[#f0f4ef]"><input class="mr-3 h-5 w-5 accent-[#204b34]" type="radio" name="capsule_taken" value="1" @checked(old('capsule_taken',(int)($card?->marked_yes_no ?? 0))===1)> Yes, taken</label>
                        <label class="cursor-pointer rounded-2xl border border-stone-200 bg-[#fcfbf7] p-5 has-[:checked]:border-[#204b34] has-[:checked]:bg-[#f0f4ef]"><input class="mr-3 h-5 w-5 accent-[#204b34]" type="radio" name="capsule_taken" value="0" @checked(old('capsule_taken',(int)($card?->marked_yes_no ?? 0))===0 && old('capsule_taken',null)!==null)> Not today</label>
                    </div>
                    <textarea name="notes" rows="3" class="mt-5 w-full rounded-2xl border border-stone-300 p-4" placeholder="Optional note about today.">{{ old('notes',$card?->notes) }}</textarea>
                </section>

                @if($isMilestone)
                <section class="rounded-3xl border border-stone-200 bg-white p-7 md:p-9">
                    <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">Milestone response</div>
                    <h2 class="display-serif mt-3 text-3xl">Record the same five gut signals again.</h2>
                    <div class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
                        @foreach(['bloating_score'=>'Bloating','gas_burping_score'=>'Gas / burping','heaviness_score'=>'Heaviness','acidity_score'=>'Acidity','overall_comfort_score'=>'Overall comfort'] as $field=>$label)
                            <label class="rounded-2xl border border-stone-200 bg-[#fcfbf7] p-5"><span class="block text-sm font-semibold">{{ $label }}</span><input type="number" min="{{ $field==='overall_comfort_score' ? 1 : 0 }}" max="10" name="{{ $field }}" value="{{ old($field,$checkpoint?->{$field}) }}" required class="mt-4 w-full rounded-xl border border-stone-300 p-3.5 text-lg"></label>
                        @endforeach
                    </div>
                </section>
                @endif

                @if(in_array($day,[3,7,14,21],true))
                <section class="rounded-3xl border border-stone-200 bg-white p-7 md:p-9">
                    <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">Milestone moment</div>
                    <div class="mt-7 grid gap-6 lg:grid-cols-2">
                        <label><span class="block text-sm font-semibold">How has the routine felt?</span><select name="capsule_consistency" required class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"><option value="consistent" @selected(old('capsule_consistency',$milestone?->answers?->where('question_code','capsule_consistency')->first()?->answer_text)==='consistent')>Consistent</option><option value="mostly" @selected(old('capsule_consistency',$milestone?->answers?->where('question_code','capsule_consistency')->first()?->answer_text)==='mostly')>Mostly consistent</option><option value="mixed" @selected(old('capsule_consistency',$milestone?->answers?->where('question_code','capsule_consistency')->first()?->answer_text)==='mixed')>Mixed</option><option value="low" @selected(old('capsule_consistency',$milestone?->answers?->where('question_code','capsule_consistency')->first()?->answer_text)==='low')>Hard to maintain</option></select></label>
                        <label><span class="block text-sm font-semibold">What changed, if anything?</span><input name="what_changed" value="{{ old('what_changed',$milestone?->answers?->where('question_code','what_changed')->first()?->answer_text) }}" class="mt-3 w-full rounded-xl border border-stone-300 p-3.5" placeholder="Optional"></label>
                        <label class="lg:col-span-2"><span class="block text-sm font-semibold">What was happening around this milestone?</span><textarea name="day_context" rows="3" class="mt-3 w-full rounded-xl border border-stone-300 p-3.5" placeholder="Routine, travel, meals, workload or other context.">{{ old('day_context',$milestone?->answers?->where('question_code','day_context')->first()?->answer_text) }}</textarea></label>
                        <label class="flex items-start gap-3 rounded-2xl border border-stone-200 bg-[#f8f7f2] p-5"><input type="checkbox" name="needs_support" value="1" class="mt-1 h-5 w-5 accent-[#204b34]" @checked(old('needs_support',$milestone?->answers?->where('question_code','needs_support')->first()?->answer_boolean))><span class="text-sm leading-7">I would like programme support around this point.</span></label>
                    </div>
                </section>
                @endif

                @if($day === 30)
                <section class="rounded-3xl border border-stone-200 bg-white p-7 md:p-9">
                    <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">Final review</div>
                    <div class="mt-7 grid gap-6 sm:grid-cols-2">
                        <label><span class="block text-sm font-semibold">How has the routine felt?</span><select name="capsule_consistency" required class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"><option value="">Select</option><option value="consistent">Consistent</option><option value="mostly">Mostly consistent</option><option value="mixed">Mixed</option><option value="low">Hard to maintain</option></select></label>
                        <label><span class="block text-sm font-semibold">Final tracking usage</span><select name="final_tracking_usage" required class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"><option value="strong">Strong</option><option value="regular">Regular</option><option value="occasional">Occasional</option><option value="low">Low</option></select></label>
                        <label><span class="block text-sm font-semibold">Product comfort</span><select name="product_comfort" required class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"><option value="comfortable">Comfortable</option><option value="mixed">Mixed</option><option value="uncomfortable">Uncomfortable</option></select></label>
                        <label><span class="block text-sm font-semibold">Areas you feel improved</span><input type="number" min="0" max="3" name="areas_improved_count" value="0" required class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"></label>
                        <label><span class="block text-sm font-semibold">Areas still unresolved</span><input type="number" min="0" max="3" name="areas_unresolved_count" value="0" required class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"></label>
                        <label><span class="block text-sm font-semibold">Observed trigger pattern</span><input name="trigger_pattern" maxlength="100" class="mt-3 w-full rounded-xl border border-stone-300 p-3.5" placeholder="Optional"></label>
                        <label><span class="block text-sm font-semibold">Your own verdict</span><select name="user_verdict" required class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"><option value="clearer">Clearer than Day 0</option><option value="mixed">Mixed / still learning</option><option value="unclear">Not much clearer yet</option></select></label>
                        <label><span class="block text-sm font-semibold">Would you repeat later?</span><select name="continue_repeat_intent" required class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"><option value="yes">Yes</option><option value="maybe">Maybe / later</option><option value="no">No</option></select></label>
                        <label><span class="block text-sm font-semibold">Would you recommend the guided format?</span><select name="recommendation_intent" required class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"><option value="yes">Yes</option><option value="not_sure">Not sure</option><option value="no">No</option></select></label>
                    </div>
                    <div class="mt-8 border-t border-stone-200 pt-8">
                        <div class="text-sm font-semibold">Optional course feedback</div>
                        <div class="mt-5 grid gap-6 sm:grid-cols-2">
                            <select name="usefulness_rating" class="rounded-xl border border-stone-300 p-3.5"><option value="">Usefulness rating</option><option value="very_useful">Very useful</option><option value="useful">Useful</option><option value="mixed">Mixed</option><option value="not_useful">Not useful</option></select>
                            <select name="most_valuable_element_1" class="rounded-xl border border-stone-300 p-3.5"><option value="">Most valuable element</option><option>Day 0 baseline</option><option>Daily tracking</option><option>Milestones</option><option>Gut Response Brief</option></select>
                            <select name="most_valuable_element_2" class="rounded-xl border border-stone-300 p-3.5"><option value="">Second valuable element</option><option>Day 0 baseline</option><option>Daily tracking</option><option>Milestones</option><option>Gut Response Brief</option></select>
                            <select name="least_useful_element" class="rounded-xl border border-stone-300 p-3.5"><option value="">Least useful element</option><option>Day 0 baseline</option><option>Daily tracking</option><option>Milestones</option><option>Gut Response Brief</option></select>
                        </div>
                        <textarea name="final_feedback_text" rows="4" class="mt-5 w-full rounded-2xl border border-stone-300 p-4" placeholder="Anything else you want the programme team to know."></textarea>
                    </div>
                </section>
                @endif

                <button class="inline-flex min-h-14 items-center justify-center rounded-xl bg-[#204b34] px-8 text-[15px] font-semibold text-white" {{ $profile->safety_flag_active ? 'disabled' : '' }}>{{ $day === 30 ? 'Complete Day 30 and generate my brief' : 'Complete Day '.$day }}</button>
            </form>
        </div>

        <aside class="space-y-6">
            <div class="rounded-3xl bg-[#f3f0e9] p-7">
                <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">Scheduled date</div>
                <div class="display-serif mt-2 text-3xl">{{ $scheduledDate ? \Illuminate\Support\Carbon::parse($scheduledDate)->format('d M Y') : 'Set after Day 0' }}</div>
                <div class="mt-5 text-sm leading-7 text-stone-500">Day {{ $day }} stays attached to this cycle even when the actual check-in happens later.</div>
            </div>

            <div class="rounded-3xl border border-stone-200 bg-white p-7">
                <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">Something unusual?</div>
                <p class="mt-3 text-sm leading-7 text-stone-600">Record it separately so the programme team can review it without changing your normal day response.</p>
                <form method="POST" action="{{ route('reset.day.unusual',['day'=>$day]) }}" class="mt-5 space-y-3">
                    @csrf
                    <select name="symptom_type" required class="w-full rounded-xl border border-stone-300 p-3"><option value="">Type</option><option value="digestive_discomfort">Digestive discomfort</option><option value="product_reaction">Product-related reaction</option><option value="other">Other</option></select>
                    <select name="severity" required class="w-full rounded-xl border border-stone-300 p-3"><option value="low">Low</option><option value="moderate">Moderate</option><option value="high">High</option><option value="critical">Critical</option></select>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-stone-300 p-3" placeholder="What happened?"></textarea>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="product_related_yes_no" value="1" class="h-4 w-4 accent-[#204b34]"> I think this may relate to the product</label>
                    <button class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm font-semibold">Record unusual event</button>
                </form>
            </div>

            <div class="rounded-3xl border border-stone-200 bg-white p-7">
                <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">Positive change</div>
                <form method="POST" action="{{ route('reset.day.positive',['day'=>$day]) }}" class="mt-5 space-y-3">
                    @csrf
                    <input name="event_type" required class="w-full rounded-xl border border-stone-300 p-3" placeholder="e.g. felt more comfortable">
                    <textarea name="description" rows="2" class="w-full rounded-xl border border-stone-300 p-3" placeholder="Optional context"></textarea>
                    <button class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm font-semibold">Record positive event</button>
                </form>
            </div>
        </aside>
    </div>
</div>
@endsection
