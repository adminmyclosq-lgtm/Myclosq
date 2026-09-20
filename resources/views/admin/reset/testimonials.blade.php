@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10">
    <div><a href="{{ route('admin.reset.operations') }}" class="text-sm text-stone-500">← Day 0–30 operations</a><h1 class="serif mt-3 text-4xl">Testimonial moderation</h1><p class="mt-2 text-sm text-stone-500">Saved testimonials stay pending until an administrator approves publication.</p></div>
    @if(session('success'))<div class="mt-6 rounded-xl bg-green-50 p-4 text-green-800">{{ session('success') }}</div>@endif
    <div class="mt-7 space-y-4">
        @forelse($testimonials as $testimonial)
            <section class="card">
                <div class="flex flex-wrap items-start justify-between gap-4"><div><div class="text-xs uppercase tracking-wide text-stone-500">Cycle #{{ $testimonial->resetprofile?->cycle_number }}</div><div class="mt-2 font-bold">{{ $testimonial->display_name ?: $testimonial->resetprofile?->user?->name }}</div><div class="text-xs text-stone-500">{{ $testimonial->created_at?->format('d M Y H:i') }}</div></div><div class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold uppercase">{{ $testimonial->moderation_status }}</div></div>
                <div class="mt-5 rounded-2xl bg-stone-50 p-5 text-sm leading-7">{{ $testimonial->testimonial_text ?: 'No testimonial text provided.' }}</div>
                <div class="mt-5 flex flex-wrap items-center justify-between gap-4"><div class="text-xs text-stone-500">Consent: {{ $testimonial->consent }} · Usage: {{ $testimonial->usage_permission ?: '—' }}</div>@if($testimonial->moderation_status !== 'approved' || !$testimonial->published_at)<form method="POST" action="{{ route('admin.reset.testimonials.moderate',$testimonial) }}" class="flex flex-wrap gap-2">@csrf<select name="moderation_status" class="rounded-lg border border-stone-300 px-3 py-2 text-sm"><option value="approved">Approve</option><option value="rejected">Reject</option><option value="pending">Keep pending</option></select><label class="flex items-center gap-2 px-2 text-sm"><input type="checkbox" name="published" value="1"> Publish</label><button class="rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-white">Save</button></form>@else<span class="text-sm font-semibold text-green-700">Published {{ $testimonial->published_at?->format('d M Y') }}</span>@endif</div>
            </section>
        @empty
            <div class="card text-sm text-stone-500">No testimonials found.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $testimonials->links() }}</div>
</div>
@endsection
