@extends('layouts.course')

@section('content')
<div class="mx-auto max-w-[900px] px-5 py-10 md:px-10 md:py-16">
    <div class="rounded-3xl bg-[#204b34] p-8 text-white md:p-12"><div class="text-[11px] uppercase tracking-[.18em] text-white/60">After Day 30</div><h1 class="display-serif mt-4 text-5xl">Share your experience.</h1><p class="mt-5 max-w-2xl text-sm leading-7 text-white/75">Your testimonial is saved for moderation. Publication is a separate admin decision.</p></div>
    @if($errors->any())<div class="mt-7 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('reset.testimonial.store') }}" class="mt-7 space-y-6 rounded-3xl border border-stone-200 bg-white p-8 md:p-10">
        @csrf
        <label class="block"><span class="block text-sm font-semibold">May we use your testimonial?</span><select name="consent" required class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"><option value="yes">Yes</option><option value="no">No</option></select></label>
        <label class="block"><span class="block text-sm font-semibold">Display name</span><input name="display_name" maxlength="150" class="mt-3 w-full rounded-xl border border-stone-300 p-3.5" placeholder="First name or initials"></label>
        <label class="block"><span class="block text-sm font-semibold">Your testimonial</span><textarea name="testimonial_text" rows="7" class="mt-3 w-full rounded-2xl border border-stone-300 p-4" placeholder="What did the guided experience help you observe or understand?"></textarea></label>
        <label class="block"><span class="block text-sm font-semibold">Usage permission</span><select name="usage_permission" class="mt-3 w-full rounded-xl border border-stone-300 p-3.5"><option value="site">Website</option><option value="internal">Internal only</option><option value="both">Website and internal</option></select></label>
        <div class="flex gap-3"><button class="rounded-xl bg-[#204b34] px-6 py-4 text-sm font-semibold text-white">Save testimonial</button><a href="{{ route('reset.report') }}" class="rounded-xl border border-stone-300 px-6 py-4 text-sm font-semibold">Back to brief</a></div>
    </form>
</div>
@endsection
