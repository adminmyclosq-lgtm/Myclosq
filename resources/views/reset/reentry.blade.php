@extends('layouts.course')

@section('content')
<div class="mx-auto max-w-[1050px] px-5 py-10 md:px-10 md:py-16">
    <div class="rounded-3xl border border-stone-200 bg-[#f3f0e9] p-8 md:p-12"><div class="text-[11px] uppercase tracking-[.18em] text-stone-500">New cycle</div><h1 class="display-serif mt-4 text-5xl">Request re-entry using the same account.</h1><p class="mt-5 max-w-3xl text-[16px] leading-8 text-stone-600">Your earlier cycle stays unchanged. A new cycle is created only after an admin reviews the request.</p></div>
    @if(session('success'))<div class="mt-7 rounded-2xl border border-[#b9cfbe] bg-[#edf5ee] p-5 text-sm text-[#204b34]">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mt-7 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">{{ $errors->first() }}</div>@endif

    @if($pending)
        <div class="mt-7 rounded-3xl border border-stone-200 bg-white p-8"><div class="text-sm font-semibold">Request #{{ $pending->id }} is pending.</div><p class="mt-2 text-sm leading-7 text-stone-600">Submitted {{ $pending->requested_at?->format('d M Y H:i') }}. You can keep using the same login while this request is reviewed.</p></div>
    @elseif($latest?->status === 'completed')
        <form method="POST" action="{{ route('reset.reentry.store') }}" class="mt-7 rounded-3xl border border-stone-200 bg-white p-8 md:p-10">
            @csrf
            <div class="text-[11px] uppercase tracking-[.18em] text-stone-500">Previous cycle</div>
            <div class="mt-4 grid gap-4 sm:grid-cols-3"><div class="rounded-2xl bg-[#fcfbf7] p-5"><div class="text-xs text-stone-500">Cycle</div><div class="mt-2 text-xl font-semibold">#{{ $latest->cycle_number }}</div></div><div class="rounded-2xl bg-[#fcfbf7] p-5"><div class="text-xs text-stone-500">Status</div><div class="mt-2 text-xl font-semibold">Complete</div></div><div class="rounded-2xl bg-[#fcfbf7] p-5"><div class="text-xs text-stone-500">Completed</div><div class="mt-2 text-xl font-semibold">{{ $latest->day30_completed_at?->format('d M Y') }}</div></div></div>
            <label class="mt-7 block"><span class="block text-sm font-semibold">Why would you like to start another cycle?</span><textarea name="reason" rows="6" required maxlength="1000" class="mt-3 w-full rounded-2xl border border-stone-300 p-4" placeholder="Tell the programme team what you would like to observe in the next cycle."></textarea></label>
            <button class="mt-7 rounded-xl bg-[#204b34] px-6 py-4 text-sm font-semibold text-white">Submit re-entry request</button>
        </form>
    @else
        <div class="mt-7 rounded-3xl border border-stone-200 bg-white p-8 text-sm leading-7 text-stone-600">A completed cycle is required before a new re-entry request can be submitted.</div>
    @endif
</div>
@endsection
