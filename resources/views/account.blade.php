@extends('layouts.app')
@section('content')
<div class="section">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><span class="badge">Account</span><h1 class="serif mt-4 text-5xl">Hello, {{ $user->customerProfile->first_name ?? $user->email }}.</h1></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn-secondary">Sign out</button></form>
    </div>
    @if(session('success'))<div class="mt-6 rounded-xl bg-green-50 p-4 text-green-800">{{ session('success') }}</div>@endif
    <div class="mt-10 grid gap-6 md:grid-cols-3">
        <div class="card md:col-span-3 bg-[#f3f0e9]"><div class="flex flex-wrap items-center justify-between gap-4"><div><div class="text-xs uppercase text-stone-500">30-Day Gut Reset</div><div class="mt-2 text-2xl font-bold">Continue your guided journey</div><p class="mt-2 text-sm text-stone-600">Open your current day, view your response brief, or request a new cycle after a completed reset.</p></div><a class="btn-primary" href="{{ route('reset.home') }}">Open Reset Journey</a></div></div>
        <div class="card"><div class="text-xs uppercase text-stone-500">Course</div><div class="mt-2 text-2xl font-bold">30 days</div><p class="mt-3 text-sm text-stone-600">Daily check-ins and milestone moments.</p></div>
        <div class="card"><div class="text-xs uppercase text-stone-500">Orders</div><div class="mt-2 text-2xl font-bold">{{ $user->orders->count() }}</div><a class="mt-3 inline-block underline" href="{{ $user->orders->first()?route('account.order',$user->orders->first()):route('shop') }}">View latest order</a></div>
        <div class="card"><div class="text-xs uppercase text-stone-500">WhatsApp</div><div class="mt-2 text-2xl font-bold">{{ ($user->customerProfile->whatsapp_opt_in ?? false)?'Enabled':'Not enabled' }}</div></div>
    </div>
</div>
@endsection
