@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10">
    <div class="flex flex-wrap items-end justify-between gap-4"><div><a href="{{ route('admin.reset.operations') }}" class="text-sm text-stone-500">← Day 0–30 operations</a><h1 class="serif mt-3 text-4xl">Re-entry approvals</h1><p class="mt-2 text-sm text-stone-500">A completed cycle remains intact; approval creates a new cycle under the same account.</p></div></div>
    @if(session('success'))<div class="mt-6 rounded-xl bg-green-50 p-4 text-green-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mt-6 rounded-xl bg-red-50 p-4 text-red-800">{{ $errors->first() }}</div>@endif
    <div class="mt-7 space-y-4">
        @forelse($requests as $request)
            <section class="card">
                <div class="grid gap-5 lg:grid-cols-[1fr_1fr_.8fr]">
                    <div><div class="text-xs uppercase tracking-wide text-stone-500">Request #{{ $request->id }}</div><div class="mt-2 font-bold">{{ $request->user?->name }}</div><div class="text-sm text-stone-500">{{ $request->user?->email }}</div><div class="mt-3 text-sm">Requested {{ $request->requested_at?->format('d M Y H:i') }}</div></div>
                    <div><div class="text-xs uppercase tracking-wide text-stone-500">Reason</div><p class="mt-2 text-sm leading-6">{{ $request->request_reason }}</p><div class="mt-3 text-xs text-stone-500">Previous cycle #{{ $request->previousResetProfile?->cycle_number }} · {{ $request->previousResetProfile?->day30_completed_at?->format('d M Y') }}</div></div>
                    <div><div class="text-xs uppercase tracking-wide text-stone-500">Status</div><div class="mt-2 font-semibold">{{ ucfirst($request->status) }}</div>@if($request->status==='pending')<form class="mt-4" method="POST" action="{{ route('admin.reset.reentry.approve',$request) }}">@csrf<textarea name="review_notes" rows="2" class="w-full rounded-lg border border-stone-300 p-2 text-sm" placeholder="Optional review notes"></textarea><button class="mt-2 w-full rounded-lg bg-stone-900 px-3 py-2 text-sm font-semibold text-white">Approve & create cycle</button></form><form class="mt-2" method="POST" action="{{ route('admin.reset.reentry.reject',$request) }}">@csrf<input type="hidden" name="review_notes" value="Rejected after admin review"><button class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm font-semibold">Reject</button></form>@else<div class="mt-2 text-sm text-stone-500">Reviewed {{ $request->reviewed_at?->format('d M Y H:i') }} by {{ $request->reviewer?->name ?: '—' }}</div>@endif</div>
                </div>
            </section>
        @empty
            <div class="card text-sm text-stone-500">No re-entry requests found.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $requests->links() }}</div>
</div>
@endsection
