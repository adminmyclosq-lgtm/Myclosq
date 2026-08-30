@extends('layouts.auth')
@section('content')
<div class="grid min-h-[calc(100vh-142px)] grid-cols-1 lg:grid-cols-2">
    <section class="bg-[var(--ink)] px-6 py-12 text-white sm:px-12 lg:px-16 lg:py-16 xl:px-20">
        <div class="mx-auto max-w-md">
            <div class="text-[11px] font-medium uppercase tracking-[.18em] text-white/70">Your Guided Wellness Account</div>
            <h1 class="display-serif mt-5 text-5xl leading-[1.02] sm:text-6xl">Your product.<br>Your course.<br>Your brief.</h1>
            <p class="mt-6 max-w-sm text-[15px] leading-7 text-white/80">Activate your product, continue your guided course and access your Gut Response Brief.</p>
            <div class="mt-9 space-y-6">
                <div class="flex gap-4"><div class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-full border border-white/25 text-white/80"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg></div><div><div class="text-[11px] font-medium uppercase tracking-[.16em]">Activate a product</div><p class="mt-1.5 text-[14px] leading-6 text-white/75">Link a new Guided Wellness product to your account.</p></div></div>
                <div class="flex gap-4"><div class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-full border border-white/25 text-white/80"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg></div><div><div class="text-[11px] font-medium uppercase tracking-[.16em]">Continue your course</div><p class="mt-1.5 text-[14px] leading-6 text-white/75">Return directly to the current point in your guided course.</p></div></div>
                <div class="flex gap-4"><div class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-full border border-white/25 text-white/80"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2" /></svg></div><div><div class="text-[11px] font-medium uppercase tracking-[.16em]">Access your Gut Response Brief</div><p class="mt-1.5 text-[14px] leading-6 text-white/75">View your personal output when the course is complete.</p></div></div>
            </div>
            <div class="mt-10 overflow-hidden rounded-md bg-[var(--sand)]"><img src="https://guided-gut-reset-lovable-app.lovable.app/assets/course-kit-DtVPc2tT.jpg" alt="Guided Wellness product and course kit" class="aspect-[4/3] w-full max-w-sm object-cover"></div>
        </div>
    </section>

    <section class="bg-white px-6 py-12 sm:px-12 lg:px-16 lg:py-16 xl:px-20">
        <div class="mx-auto max-w-md">
            <h2 class="display-serif text-4xl leading-[1.05] sm:text-5xl">Sign in or activate.</h2>
            <p class="mt-4 text-[15px] leading-7 text-stone-600">Already activated? Sign in below. Have a new product? Activate it first.</p>
            @if($errors->any())<div class="mt-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm leading-6 text-red-700">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
            <form method="POST" action="{{ route('login.post') }}" class="mt-9">
                @csrf
                <div class="text-[11px] font-medium uppercase tracking-[.16em]">Already activated?</div>
                <p class="mt-2 text-sm text-stone-600">Sign in with your phone number or email.</p>
                <div class="mt-6"><label for="identifier" class="text-[11px] font-medium uppercase tracking-[.16em]">Email or mobile number</label><input id="identifier" name="identifier" value="{{ old('identifier') }}" required autocomplete="username" class="mt-2 w-full rounded-md border border-stone-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-stone-400 focus:border-[var(--ink)] focus:ring-2 focus:ring-[var(--sage)]/35" placeholder="Enter your email or mobile number"></div>
                <div class="mt-5"><label for="password" class="text-[11px] font-medium uppercase tracking-[.16em]">Password</label><input id="password" name="password" type="password" required autocomplete="current-password" class="mt-2 w-full rounded-md border border-stone-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[var(--ink)] focus:ring-2 focus:ring-[var(--sage)]/35" placeholder="Enter your password"></div>
                <button type="submit" class="mt-6 inline-flex h-14 w-full items-center justify-center rounded-md bg-[var(--ink)] px-5 text-[13px] font-medium uppercase tracking-[.14em] text-white shadow-sm transition hover:bg-[#315743] focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--sage)] focus-visible:ring-offset-2">Sign In</button>
            </form>
            <div class="my-10 h-px bg-stone-200"></div>
            <div><div class="text-[11px] font-medium uppercase tracking-[.16em]">New customer?</div><a href="{{ route('register') }}" class="display-serif mt-2 inline-block text-2xl underline decoration-[var(--sage)] underline-offset-4">Create an account</a><p class="mt-2 text-sm leading-6 text-stone-600">Create your account before beginning your guided experience.</p><div class="mt-6 rounded-md bg-[var(--cream)] p-5"><div class="text-[11px] font-medium uppercase tracking-[.16em]">Purchased through Amazon?</div><p class="mt-2 text-[13px] leading-6 text-stone-600">You can sign in and access the same Guided Wellness experience using the account linked to your product.</p></div></div>
            <div class="mt-10"><div class="text-[11px] font-medium uppercase tracking-[.16em]">Having trouble accessing your account?</div><ul class="mt-3 space-y-2 text-[13px] text-stone-600"><li><a href="{{ url('/#faq') }}" class="hover:text-[var(--ink)]">I am unable to sign in</a></li><li><a href="{{ url('/#faq') }}" class="hover:text-[var(--ink)]">My phone number or email has changed</a></li><li><a href="{{ url('/#faq') }}" class="font-medium hover:text-[var(--ink)]">Contact Support</a></li></ul><p class="mt-8 text-xs leading-5 text-stone-500">By continuing, you agree to the applicable Terms of Use and acknowledge the Privacy Policy.</p></div>
        </div>
    </section>
</div>
@endsection
