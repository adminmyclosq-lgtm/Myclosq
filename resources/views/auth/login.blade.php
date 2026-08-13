@extends('layouts.app')
@section('content')
<div class="section max-w-xl">
    <span class="badge">Customer login</span>
    <h1 class="serif mt-4 text-5xl">Welcome back.</h1>
    @if($errors->any()) <div class="mt-5 rounded-xl bg-red-50 p-4 text-sm text-red-700">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div> @endif
    <form method="POST" action="{{ route('login.post') }}" class="card mt-8 space-y-5">
        @csrf
        <div><label class="text-sm font-semibold">Email or mobile</label><input name="identifier" value="{{ old('identifier') }}" required class="mt-2 w-full rounded-xl border p-3"></div>
        <div><label class="text-sm font-semibold">Password</label><input name="password" type="password" required class="mt-2 w-full rounded-xl border p-3"></div>
        <button class="btn-primary w-full">Sign in</button>
        <p class="text-center text-sm text-stone-600">New customer? <a class="underline" href="{{ route('register') }}">Create an account</a></p>
    </form>
</div>
@endsection
