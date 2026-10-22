@props([
    'minimal' => null,
])

@if(!auth()->check())
    @php
        $isMinimal = $minimal ?? request()->routeIs('login', 'register', 'password.*');
    @endphp

    @if($isMinimal)
    {{-- Minimal Footer: Login / Register Pages --}}
    <footer class="w-full border-t border-stone-200 bg-white py-5 px-4 text-xs text-stone-500">
        <div class="mx-auto flex max-w-[1200px] flex-col items-center justify-center gap-3 text-center sm:flex-row sm:justify-between sm:text-left">
            <p class="text-[12px] md:text-[13px] text-stone-500 font-normal">
                © 2026 Kurate Wellness Private Limited. All rights reserved.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1.5 text-[12px] md:text-[13px]">
                <a href="{{ route('privacy-policy') }}" class="text-stone-500 transition-colors hover:text-[var(--ink)] hover:underline">
                    Privacy Policy
                </a>
                <span class="text-stone-300 select-none hidden sm:inline">·</span>
                <a href="{{ route('refund-policy') }}" class="text-stone-500 transition-colors hover:text-[var(--ink)] hover:underline">
                    Refund Policy
                </a>
                <span class="text-stone-300 select-none hidden sm:inline">·</span>
                <a href="{{ route('shipping-policy') }}" class="text-stone-500 transition-colors hover:text-[var(--ink)] hover:underline">
                    Shipping Policy
                </a>
                <span class="text-stone-300 select-none hidden sm:inline">·</span>
                <a href="{{ route('terms-of-service') }}" class="text-stone-500 transition-colors hover:text-[var(--ink)] hover:underline">
                    Terms of Service
                </a>
            </div>
        </div>
    </footer>
@else
    {{-- Full Website Footer: Logged Out Public Website --}}
    <footer class="bg-[var(--ink)] text-white">
        <div class="section py-10 md:py-16 grid gap-8 sm:gap-10 md:grid-cols-[1.6fr_2.4fr]">
            <div>
                <div class="display-serif text-2xl leading-tight">Guided Wellness</div>
                <p class="mt-3 max-w-sm text-[13px] leading-6 text-white/70">Redefining the supplement experience through transparency, guidance, and respect for biological complexity.</p>
            </div>
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">
                <div>
                    <div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/60">Shop</div>
                    <a class="mt-3 block text-[13px] text-white/85 transition hover:text-white" href="{{ route('shop') }}">30-Day Gut Reset</a>
                    <a class="mt-2 block text-[13px] text-white/85 transition hover:text-white" href="{{ route('how-it-works') }}">How It Works</a>
                </div>
                <div>
                    <div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/60">Learn</div>
                    <a class="mt-3 block text-[13px] text-white/85 transition hover:text-white" href="{{ route('learn') }}">Guides</a>
                    <a class="mt-2 block text-[13px] text-white/85 transition hover:text-white" href="{{ route('our-standards') }}">Standards</a>
                    <a class="mt-2 block text-[13px] text-white/85 transition hover:text-white" href="{{ route('support') }}">Help Centre</a>
                </div>
                <div>
                    <div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/60">Legal</div>
                    <a class="mt-3 block text-[13px] text-white/85 transition hover:text-white" href="{{ route('privacy-policy') }}">Privacy Policy</a>
                    <a class="mt-2 block text-[13px] text-white/85 transition hover:text-white" href="{{ route('refund-policy') }}">Refund Policy</a>
                    <a class="mt-2 block text-[13px] text-white/85 transition hover:text-white" href="{{ route('shipping-policy') }}">Shipping Policy</a>
                    <a class="mt-2 block text-[13px] text-white/85 transition hover:text-white" href="{{ route('terms-of-service') }}">Terms of Service</a>
                </div>
                <div>
                    <div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/60">Account</div>
                    <a class="mt-3 block text-[13px] text-white/85 transition hover:text-white" href="{{ route('login') }}">Sign In</a>
                    <a class="mt-2 block text-[13px] text-white/85 transition hover:text-white" href="{{ route('support') }}">Contact Support</a>
                </div>
            </div>
        </div>
        <div class="border-t border-white/15 py-5 text-center text-[12px] text-white/60">
            © 2026 Kurate Wellness Private Limited. All rights reserved.
        </div>
    </footer>
@endif
@endif
