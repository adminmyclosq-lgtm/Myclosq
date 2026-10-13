@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1400px] p-6 lg:p-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-stone-900 font-serif">System Settings &amp; Configuration</h1>
            <p class="text-xs text-stone-500 mt-1">Environment status, application parameters, cache controls, and maintenance utilities.</p>
        </div>
        <form method="POST" action="{{ route('admin.settings.clear-cache') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-xs font-semibold text-stone-700 shadow-xs hover:bg-stone-50 transition">
                <svg class="h-4 w-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Clear All Caches
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- System Parameters Grid --}}
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-xs">
        <h2 class="text-base font-semibold text-stone-900 mb-4">Runtime Environment</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
            @foreach($systemInfo as $key => $val)
                <div class="p-3 bg-stone-50/70 rounded-xl border border-stone-100">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-stone-400 block mb-1">{{ str_replace('_', ' ', $key) }}</span>
                    <span class="font-mono font-semibold text-stone-800">{{ is_bool($val) ? ($val ? 'true' : 'false') : $val }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
