@extends('layouts.course')

@section('content')
@php
    $areas = [
        'bloating' => ['Bloating or fullness', 'Feeling unusually full, tight or bloated.'],
        'gas_burping' => ['Gas or burping', 'Gas, burping or related discomfort.'],
        'heaviness' => ['Heaviness after meals', 'A heavy or slow feeling after eating.'],
        'acidity' => ['Acidity or burning', 'Burning, reflux or acidic discomfort.'],
        'regularity' => ['Regularity', 'A pattern you would like to observe over time.'],
        'comfort' => ['Overall comfort', 'How settled your digestion feels day to day.'],
    ];
@endphp
<div class="border-b border-stone-200 bg-[#faf9f5] px-6 pt-4 md:px-12">
    <div class="text-[16px] font-medium uppercase tracking-[.17em] text-stone-500">Course Home <span class="mx-2 text-stone-300">/</span> <span class="text-[#27241f]">Set your starting point</span></div>
    <div class="mt-7 grid grid-cols-5 gap-3 text-[14px] font-medium uppercase tracking-[.15em] text-stone-400">
        @foreach(['01 Focus', '02 Pattern', '03 Context', '04 Safety', '05 Review'] as $step)
            <div class="border-t-[3px] py-3 {{ $loop->first ? 'border-[#204b34] text-[#27241f]' : 'border-stone-200' }}">{{ $step }}</div>
        @endforeach
    </div>
</div>

<div class="mx-auto max-w-[1480px] px-6 py-12 md:px-12 lg:py-20">
    @if(session('success'))<div class="mb-8 rounded-lg border border-[#b9cfbe] bg-[#edf5ee] px-5 py-4 text-sm text-[#204b34]">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mb-8 rounded-lg border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif
    @if($profile)
        <form method="POST" action="{{ route('my-plan.store') }}" class="grid gap-12 lg:grid-cols-[1.7fr_.9fr]">
            @csrf
            <div>
                <h1 class="display-serif max-w-4xl text-5xl leading-[1.06] md:text-6xl">What would you like to understand better?</h1>
                <p class="mt-7 max-w-4xl text-[16px] leading-8 text-stone-600">Choose up to three areas you would like to observe during your 30-day course. Your selections will keep the next questions focused and concise.</p>
                <div class="mt-8 border-t border-stone-200 pt-7 text-[15px] italic leading-7 text-stone-500">Your selections help structure the course. They do not diagnose a condition or predict how you will respond.</div>
                <div class="mt-6 text-[14px] font-medium uppercase tracking-[.16em] text-stone-500"><span data-selected-count>{{ count($selectedAreas) }}</span> of 3 selected</div>
                <div class="mt-8 grid gap-5 sm:grid-cols-2">
                    @foreach($areas as $value => [$label, $description])
                        <label class="group flex min-h-30 cursor-pointer items-start justify-between rounded-2xl border border-stone-200 bg-[#fcfbf7] p-6 transition hover:border-[#78917f] has-[:checked]:border-[#204b34] has-[:checked]:bg-[#f0f4ef]">
                            <span><span class="block text-[16px] font-semibold uppercase tracking-[.08em]">{{ $label }}</span><span class="mt-4 block text-[17px] leading-7 text-stone-600">{{ $description }}</span></span>
                            <input type="checkbox" name="priority_areas[]" value="{{ $value }}" class="mt-1 h-6 w-6 shrink-0 accent-[#204b34]" @checked(in_array($value, $selectedAreas, true))>
                        </label>
                    @endforeach
                </div>
                <button type="submit" class="mt-10 inline-flex min-h-14 items-center justify-center rounded-xl bg-[#204b34] px-8 text-[16px] font-semibold text-white transition hover:bg-[#173d29]">Save my focus areas</button>
            </div>
            <aside class="h-fit rounded-2xl border border-stone-200 bg-[#f3f0e9] p-9"><h2 class="display-serif text-3xl">Why we ask</h2><p class="mt-7 text-[17px] leading-8 text-stone-600">Your selection keeps the next questions focused. At Day 30, your Gut Response Brief will reflect what you wanted to understand, what you reported across the course and what remains uncertain.</p><p class="mt-7 text-[17px] italic leading-8 text-stone-500">Note: Selecting an area does not mean the product is expected or guaranteed to change it.</p></aside>
        </form>
    @else
        <div class="mx-auto max-w-2xl rounded-2xl border border-stone-200 bg-[#fcfbf7] p-10 text-center"><h1 class="display-serif text-5xl leading-tight">Activate your course first.</h1><p class="mt-5 text-[17px] leading-8 text-stone-600">Your starting-point plan becomes available after you activate a Guided Wellness product.</p><a href="{{ route('shop') }}" class="btn-primary mt-8">{{ !empty($isMyClosq) ? 'Shop My CLOSQ' : 'Shop Gut Reset' }}</a></div>
    @endif
</div>

<script>
    document.querySelectorAll('input[name="priority_areas[]"]').forEach((input) => {
        input.addEventListener('change', () => {
            const selected = document.querySelectorAll('input[name="priority_areas[]"]:checked');
            if (selected.length > 3) input.checked = false;
            document.querySelector('[data-selected-count]').textContent = document.querySelectorAll('input[name="priority_areas[]"]:checked').length;
        });
    });
</script>
@endsection
