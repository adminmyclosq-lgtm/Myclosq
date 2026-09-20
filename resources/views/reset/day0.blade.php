@extends('layouts.course')

@section('content')
@php
    $baseline = $profile->day0Baseline;
    $checkpoint = $profile->gutSignalCheckpoints->where('checkpoint_day', 0)->first();
    $plan = $profile->startPlan;
    $selected = $baseline?->priorityAreas?->pluck('priority_area')->all() ?? [];
    $triggers = $baseline?->triggers?->pluck('trigger_name')->all() ?? [];
    $triggerOptions = ['meal_timing','eating_out','spicy_rich_food','late_meals','stress','travel','irregular_routine','other'];
@endphp
<div class="mx-auto max-w-[1500px] px-5 py-8 md:px-10 md:py-12">
    <div class="mb-8 rounded-2xl border border-stone-200 bg-[#f3f0e9] p-6 text-sm leading-7 text-stone-600">
        <div class="font-semibold uppercase tracking-[.16em] text-stone-500">Day 0 · Starting point</div>
        <div class="mt-2">Set the baseline once. These observations stay attached to this cycle and are used again at Day 30.</div>
    </div>

    @if($errors->any())
        <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('reset.day0.store') }}" class="space-y-8">
        @csrf
        <section class="rounded-3xl border border-stone-200 bg-white p-7 md:p-10">
            <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">01 · What are you observing?</div>
            <h1 class="display-serif mt-3 max-w-4xl text-5xl leading-[1.05] md:text-6xl">Choose up to three focus areas.</h1>
            <p class="mt-5 max-w-3xl text-[16px] leading-8 text-stone-600">These are observation areas for your course, not diagnoses or predictions.</p>
            <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach(['bloating'=>'Bloating or fullness','gas_burping'=>'Gas or burping','heaviness'=>'Heaviness after meals','acidity'=>'Acidity or burning','regularity'=>'Regularity','comfort'=>'Overall comfort'] as $value=>$label)
                    <label class="flex cursor-pointer items-center justify-between rounded-2xl border border-stone-200 bg-[#fcfbf7] p-5 has-[:checked]:border-[#204b34] has-[:checked]:bg-[#f0f4ef]"><span class="text-sm font-semibold">{{ $label }}</span><input type="checkbox" name="priority_areas[]" value="{{ $value }}" class="h-5 w-5 accent-[#204b34]" @checked(in_array($value,$selected,true) || in_array($value,old('priority_areas',[]),true))></label>
                @endforeach
            </div>
            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold">What would count as a useful outcome for you?</label>
                    <select name="success_expectation_primary" class="mt-3 w-full rounded-xl border border-stone-300 bg-white p-3.5" required>
                        @foreach(['understanding'=>'Better understanding of my pattern','comfort'=>'A clearer view of day-to-day comfort','regularity'=>'A clearer view of regularity','routine'=>'A more consistent routine','observation'=>'Useful observations to discuss later'] as $value=>$label)
                            <option value="{{ $value }}" @selected(old('success_expectation_primary',$baseline?->success_expectation_primary)===$value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <textarea name="success_expectation_open_text" rows="3" class="mt-3 w-full rounded-xl border border-stone-300 p-3.5" placeholder="Optional: anything else you want to understand.">{{ old('success_expectation_open_text',$baseline?->success_expectation_open_text) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold">How would you describe your current regularity?</label>
                    <select name="regularity_frequency" class="mt-3 w-full rounded-xl border border-stone-300 bg-white p-3.5" required>
                        @foreach(['daily'=>'Daily','most_days'=>'Most days','several_times_week'=>'Several times a week','less_often'=>'Less often','variable'=>'Varies a lot','prefer_not_to_say'=>'Prefer not to say'] as $value=>$label)
                            <option value="{{ $value }}" @selected(old('regularity_frequency',$baseline?->regularity_frequency)===$value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <label class="mt-5 block text-sm font-semibold">Do you use a rescue remedy or similar routine?</label>
                    <select name="rescue_remedy_used" class="mt-3 w-full rounded-xl border border-stone-300 bg-white p-3.5" required>
                        @foreach(['none'=>'No','sometimes'=>'Sometimes','regularly'=>'Regularly','other'=>'Other'] as $value=>$label)
                            <option value="{{ $value }}" @selected(old('rescue_remedy_used',$baseline?->rescue_remedy_used)===$value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input name="rescue_remedy_detail" value="{{ old('rescue_remedy_detail',$baseline?->rescue_remedy_detail) }}" class="mt-3 w-full rounded-xl border border-stone-300 p-3.5" placeholder="Optional detail">
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-stone-200 bg-white p-7 md:p-10">
            <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">02 · Context</div>
            <h2 class="display-serif mt-3 text-4xl">What tends to shape the pattern?</h2>
            <div class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($triggerOptions as $trigger)
                    <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-stone-200 p-4 has-[:checked]:border-[#204b34] has-[:checked]:bg-[#f0f4ef]"><input type="checkbox" name="triggers[]" value="{{ $trigger }}" class="h-5 w-5 accent-[#204b34]" @checked(in_array($trigger,$triggers,true) || in_array($trigger,old('triggers',[]),true))><span class="text-sm">{{ ucwords(str_replace('_',' ',$trigger)) }}</span></label>
                @endforeach
            </div>
            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold">Any recent disruption?</label>
                    <div class="mt-3 flex gap-3"><label class="rounded-xl border p-3.5"><input type="radio" name="recent_disruption_yes_no" value="1" @checked(old('recent_disruption_yes_no',$baseline?->recent_disruption_yes_no)===true || old('recent_disruption_yes_no',$baseline?->recent_disruption_yes_no)==='1')> Yes</label><label class="rounded-xl border p-3.5"><input type="radio" name="recent_disruption_yes_no" value="0" @checked(old('recent_disruption_yes_no',$baseline?->recent_disruption_yes_no)===false || old('recent_disruption_yes_no',$baseline?->recent_disruption_yes_no)==='0')> No</label></div>
                    <textarea name="recent_disruption_detail" rows="3" class="mt-3 w-full rounded-xl border border-stone-300 p-3.5" placeholder="Optional context.">{{ old('recent_disruption_detail',$baseline?->recent_disruption_detail) }}</textarea>
                </div>
                <div class="rounded-2xl bg-[#f3f0e9] p-6 text-sm leading-7 text-stone-600">Keep the baseline practical: recent routines, context and the things you actually notice are more useful than trying to make a diagnosis from one day.</div>
            </div>
        </section>

        <section class="rounded-3xl border border-stone-200 bg-white p-7 md:p-10">
            <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">03 · Gut signal baseline</div>
            <h2 class="display-serif mt-3 text-4xl">Record Day 0 in a simple 0–10 scale.</h2>
            <p class="mt-4 text-sm leading-7 text-stone-500">For these four symptoms, 0 means not noticed and 10 means very noticeable. Overall comfort is 1–10, with a higher number meaning more comfortable.</p>
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
                @foreach(['bloating_score'=>'Bloating','gas_burping_score'=>'Gas / burping','heaviness_score'=>'Heaviness','acidity_score'=>'Acidity','overall_comfort_score'=>'Overall comfort'] as $field=>$label)
                    <label class="rounded-2xl border border-stone-200 bg-[#fcfbf7] p-5"><span class="block text-sm font-semibold">{{ $label }}</span><input type="number" min="{{ $field==='overall_comfort_score' ? 1 : 0 }}" max="10" name="{{ $field }}" value="{{ old($field,$checkpoint?->{$field}) }}" required class="mt-4 w-full rounded-xl border border-stone-300 p-3.5 text-lg"></label>
                @endforeach
            </div>
        </section>

        <section class="rounded-3xl border border-stone-200 bg-white p-7 md:p-10">
            <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">04 · Start plan</div>
            <h2 class="display-serif mt-3 text-4xl">Choose how the 30 days fit your routine.</h2>
            <div class="mt-8 grid gap-6 lg:grid-cols-3">
                <label class="lg:col-span-1"><span class="block text-sm font-semibold">Selected start date</span><input type="date" min="{{ today()->toDateString() }}" name="selected_start_date" value="{{ old('selected_start_date',$plan?->selected_start_date?->toDateString() ?? today()->toDateString()) }}" required class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"></label>
                <label><span class="block text-sm font-semibold">Capsule timing</span><select name="capsule_timing" class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"><option value="morning" @selected(old('capsule_timing',$plan?->capsule_timing)==='morning')>Morning</option><option value="with_meal" @selected(old('capsule_timing',$plan?->capsule_timing)==='with_meal')>With a meal</option><option value="evening" @selected(old('capsule_timing',$plan?->capsule_timing)==='evening')>Evening</option><option value="custom" @selected(old('capsule_timing',$plan?->capsule_timing)==='custom')>Custom</option></select></label>
                <label><span class="block text-sm font-semibold">Custom time</span><input type="time" name="custom_capsule_time" value="{{ old('custom_capsule_time',$plan?->custom_capsule_time) }}" class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"></label>
                <label><span class="block text-sm font-semibold">Reset card location</span><select name="capsule_card_location" class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"><option value="bottle" @selected(old('capsule_card_location',$plan?->capsule_card_location)==='bottle')>By the bottle</option><option value="kitchen" @selected(old('capsule_card_location',$plan?->capsule_card_location)==='kitchen')>Kitchen</option><option value="bedside" @selected(old('capsule_card_location',$plan?->capsule_card_location)==='bedside')>Bedside</option><option value="desk" @selected(old('capsule_card_location',$plan?->capsule_card_location)==='desk')>Desk / work bag</option><option value="custom" @selected(old('capsule_card_location',$plan?->capsule_card_location)==='custom')>Other</option></select></label>
                <label><span class="block text-sm font-semibold">Other location</span><input name="custom_location" value="{{ old('custom_location',$plan?->custom_location) }}" class="mt-3 w-full rounded-xl border border-stone-300 p-3.5" placeholder="Optional"></label>
                <label><span class="block text-sm font-semibold">Reminder time</span><input type="time" name="reminder_time" value="{{ old('reminder_time',$plan?->reminder_time) }}" class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"></label>
            </div>
            <div class="mt-7 flex flex-wrap gap-6 text-sm">
                <label class="flex items-center gap-2"><input type="checkbox" name="daily_reminder_enabled" value="1" class="h-4 w-4 accent-[#204b34]" @checked(old('daily_reminder_enabled',$plan?->daily_reminder_enabled))> Daily reminders</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="milestone_reminder_enabled" value="1" class="h-4 w-4 accent-[#204b34]" @checked(old('milestone_reminder_enabled',$plan?->milestone_reminder_enabled ?? true))> Milestone reminders</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="whatsapp_opt_in" value="1" class="h-4 w-4 accent-[#204b34]" @checked(old('whatsapp_opt_in',$baseline?->whatsapp_opt_in))> WhatsApp course reminders</label>
            </div>
        </section>

        <section class="rounded-3xl border border-stone-200 bg-[#f7f5ef] p-7 md:p-10">
            <div class="text-[11px] font-semibold uppercase tracking-[.18em] text-stone-500">05 · Review & start</div>
            <h2 class="display-serif mt-3 text-4xl">Keep the course grounded.</h2>
            <div class="mt-6 grid gap-4 lg:grid-cols-2">
                <label class="flex gap-3 rounded-2xl border border-stone-200 bg-white p-5"><input type="checkbox" name="safety_acknowledged" value="1" required class="mt-1 h-5 w-5 accent-[#204b34]"><span class="text-sm leading-7">I understand the programme is for guided observation and is not a diagnosis.</span></label>
                <label class="flex gap-3 rounded-2xl border border-stone-200 bg-white p-5"><input type="checkbox" name="medical_disclaimer_acknowledged" value="1" required class="mt-1 h-5 w-5 accent-[#204b34]"><span class="text-sm leading-7">I understand unusual or concerning experiences should be reported and reviewed appropriately.</span></label>
            </div>
            <button class="mt-8 inline-flex min-h-14 items-center justify-center rounded-xl bg-[#204b34] px-8 text-[15px] font-semibold text-white">Save Day 0 and start the course</button>
        </section>
    </form>
</div>
@endsection
