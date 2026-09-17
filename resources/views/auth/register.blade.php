@extends('layouts.app')
@section('content')
<div class="section max-w-2xl">
    <span class="badge">Create account</span><h1 class="serif mt-4 text-5xl">Start your {{ $isMyClosq ? 'My CLOSQ' : 'Gut Reset' }} journey.</h1>
    @if($errors->any()) <div class="mt-5 rounded-xl bg-red-50 p-4 text-sm text-red-700">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div> @endif
    <form method="POST" action="{{ route('register.post') }}" class="card mt-8 grid gap-5 md:grid-cols-2">
        @csrf
        <div><label class="text-sm font-semibold">First name</label><input name="first_name" value="{{ old('first_name') }}" required class="mt-2 w-full rounded-xl border p-3"></div>
        <div><label class="text-sm font-semibold">Last name</label><input name="last_name" value="{{ old('last_name') }}" class="mt-2 w-full rounded-xl border p-3"></div>
        <div><label class="text-sm font-semibold">Email</label><input name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-xl border p-3"></div>
        <div><label class="text-sm font-semibold">Mobile</label><input name="mobile" value="{{ old('mobile') }}" class="mt-2 w-full rounded-xl border p-3"></div>
        <div><label class="text-sm font-semibold">Password</label><input name="password" type="password" required class="mt-2 w-full rounded-xl border p-3"></div>
        <div><label class="text-sm font-semibold">Confirm password</label><input name="password_confirmation" type="password" required class="mt-2 w-full rounded-xl border p-3"></div>
        <label class="md:col-span-2 flex gap-3 text-sm"><input type="checkbox" name="whatsapp_opt_in" value="1"> I agree to receive {{ $isMyClosq ? 'My CLOSQ' : 'Gut Reset' }} WhatsApp messages.</label>
        <label class="md:col-span-2 flex gap-3 text-sm"><input type="checkbox" name="marketing_opt_in" value="1"> I would like product/news updates.</label>
        <button class="btn-primary md:col-span-2">Create account</button>
    </form>
</div>
@endsection
