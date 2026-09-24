@php
    $headerCart = auth()->check()
        ? \App\Models\Cart::with('cartItems.productVariant.product')
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first()
        : null;
    $headerCartItemCount = (int) ($headerCart?->cartItems->sum('quantity') ?? 0);
@endphp
<header class="sticky top-0 z-40 border-b border-stone-200 bg-white/85 backdrop-blur-xl">
    <div class="mx-auto flex h-16 w-full max-w-[1200px] items-center justify-between gap-6 px-5 sm:px-8">
        <a href="{{ route('home') }}" class="display-serif text-lg leading-tight text-[var(--ink)]">
            Guided<br class="hidden sm:inline"><span class="hidden sm:inline">Wellness</span><span class="sm:hidden"> Wellness</span>
        </a>
        <nav class="hidden items-center gap-7 text-sm md:flex">
            @auth
                @unless(auth()->user()?->hasRole('SUPER_ADMIN'))
                    <a href="{{ route('my-brief') }}">My Brief</a>
                @endunless
            @endauth
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('how-it-works') }}">How It Works</a>
            <a href="{{ url('/#standards') }}">Our Standards</a>
            <a href="{{ url('/#learn') }}">Learn</a>
            <a href="{{ url('/#faq') }}">Support</a>
            <a href="{{ route('shop') }}" class="inline-flex shrink-0 items-center justify-center rounded-full px-5 py-2.5 text-sm font-medium text-white transition hover:opacity-90" style="background-color: #587762;">Shop Myclosq</a>
        </nav>
        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('shop') }}" class="inline-flex shrink-0 items-center justify-center rounded-full px-4 py-2 text-sm font-medium text-white transition hover:opacity-90 md:hidden" style="background-color: #587762;">Shop Myclosq</a>
            <a href="{{ route('cart') }}" class="inline-flex items-center gap-1.5 font-semibold text-[var(--ink)]" aria-label="Cart, {{ $headerCartItemCount }} item{{ $headerCartItemCount === 1 ? '' : 's' }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-1.1 2.2A1 1 0 0 0 6.8 17H19M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" /></svg>
                <span data-cart-count>{{ $headerCartItemCount }}</span>
                <span>Cart</span>
            </a>
            @auth
                @php
                    $accountRoute = auth()->user()?->hasRole('SUPER_ADMIN') ? 'admin.dashboard' : 'account';
                    $userName = auth()->user()?->name ?: (auth()->user()?->customerProfile?->first_name ?: auth()->user()?->email);
                @endphp
                <details class="group relative hidden md:block">
                    <summary class="flex max-w-[14rem] cursor-pointer list-none items-center gap-2 rounded-full border border-stone-200 bg-white px-3 py-2 text-left leading-tight [&::-webkit-details-marker]:hidden">
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-semibold text-stone-800">{{ $userName }}</span>
                            <span class="block truncate text-xs text-stone-500">Customer</span>
                        </span>
                        <svg class="h-4 w-4 shrink-0 text-stone-500 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                    </summary>
                    <div class="absolute right-0 z-50 mt-2 w-52 rounded-lg border border-stone-200 bg-white p-2 shadow-lg">
                        <a href="{{ route('home') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Home</a>
                        @unless(auth()->user()?->hasRole('SUPER_ADMIN'))
                            <a href="{{ route('my-brief') }}" class="block rounded-md px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">My Brief</a>
                        @endunless
                        <a href="{{ route($accountRoute) }}" class="block rounded-md px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Account</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-stone-100 pt-2">
                            @csrf
                            <button type="submit" class="w-full rounded-md px-3 py-2 text-left text-sm font-semibold text-red-700 transition hover:bg-red-50">Sign out</button>
                        </form>
                    </div>
                </details>
            @else
                <!-- <a href="{{ route('login') }}" class="hidden items-center gap-1.5 font-semibold text-[var(--ink)] md:inline-flex"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4" /><path stroke-linecap="round" d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6" /></svg>Login</a> -->
            @endauth
        </div>
    </div>
</header>
