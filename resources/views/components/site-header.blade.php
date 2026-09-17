@php
    $authUser = auth()->user();
    $headerCart = auth()->check()
        ? \App\Models\Cart::with('cartItems.productVariant.product')
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first()
        : null;
    $headerCartItemCount = (int) ($headerCart?->cartItems->sum('quantity') ?? 0);
    
    $primaryRole = $authUser ? $authUser->roles()->first() : null;
    $accountRoute = $authUser && $authUser->hasRole('SUPER_ADMIN') ? 'admin.dashboard' : 'account';
    $roleLabel = $primaryRole
        ? ucwords(strtolower(str_replace('_', ' ', (string) $primaryRole->code)))
        : 'User';
        
    $isMyClosq = in_array(request()->getHost(), ['myclosq.com', 'www.myclosq.com']);

    // Global CMS Data
    $globalPage = \App\Models\CmsPage::where('slug', 'global')->with('sections')->first();
    $headerSection = $globalPage ? $globalPage->sections->where('section_type', 'header')->first() : null;
    $headerContent = $headerSection ? (is_string($headerSection->content) ? json_decode($headerSection->content, true) : $headerSection->content) : [];
    if (!is_array($headerContent)) $headerContent = [];
    
    $navItems = !empty($headerContent['nav_items']) ? $headerContent['nav_items'] : [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'How It Works', 'url' => route('how-it-works')],
        ['label' => 'Our Standards', 'url' => route('our-standards')],
        ['label' => 'Learn', 'url' => route('learn')],
        ['label' => 'Support', 'url' => route('support')],
    ];
    $btnText = $headerContent['button_text'] ?? (!empty($isMyClosq) ? 'Shop My CLOSQ' : 'Shop Gut Reset');
    if (!empty($isMyClosq) && $btnText === 'Shop Gut Reset') {
        $btnText = 'Shop My CLOSQ';
    }
    $btnUrl = $headerContent['button_url'] ?? route('shop');
    $btnColor = $headerContent['settings']['button_primary_color'] ?? '#587762';
@endphp
<header class="sticky top-0 z-40 border-b border-border/60 bg-background/85 backdrop-blur">
    <div class="mx-auto flex w-full max-w-[1200px] items-center justify-between gap-6 px-5 py-3 sm:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3 text-lg font-semibold tracking-tight text-foreground">
            <img src="{{ asset('images/closq-logo.png') }}" alt="CLOS.Q Logo" class="h-10 w-auto object-contain">
        </a>
        <nav class="hidden items-center gap-7 text-sm md:flex">
            @unless($isMyClosq)
                @auth
                    @unless(auth()->user()?->hasRole('SUPER_ADMIN'))
                        <a href="{{ route('my-brief') }}">My Brief</a>
                    @endunless
                @endauth
            @endunless
            @foreach($navItems as $item)
                <a href="{{ url($item['url'] ?? '#') }}">{{ $item['label'] ?? '' }}</a>
            @endforeach
            <a href="{{ url($btnUrl) }}" class="inline-flex shrink-0 items-center justify-center rounded-full px-5 py-2.5 text-sm font-medium text-white transition hover:opacity-90" style="background-color: {{ $btnColor }};">{{ $btnText }}</a>
        </nav>
        <div class="flex items-center gap-3 text-sm">
            <details class="group relative md:hidden">
                <summary class="inline-flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-full border border-stone-200 bg-white text-[var(--ink)] [&::-webkit-details-marker]:hidden" aria-label="Open menu">
                    <svg class="h-5 w-5 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                    <svg class="hidden h-5 w-5 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" /></svg>
                </summary>
                <nav class="absolute right-0 top-full z-50 mt-3 w-64 rounded-xl border border-stone-200 bg-white p-2 shadow-xl">
                    @foreach($navItems as $item)
                        <a href="{{ url($item['url'] ?? '#') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-stone-50">{{ $item['label'] ?? '' }}</a>
                    @endforeach
                    @unless($isMyClosq)
                        @auth
                            @unless(auth()->user()?->hasRole('SUPER_ADMIN'))
                                <a href="{{ route('my-brief') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-stone-50">My Brief</a>
                            @endunless
                            <a href="{{ route($accountRoute) }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-stone-50">Account</a>
                        @else
                            <a href="{{ route('login') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-stone-50">Login</a>
                        @endauth
                    @endunless
                    <a href="{{ url($btnUrl) }}" class="mt-1 block rounded-lg px-4 py-3 text-center font-medium text-white" style="background-color: {{ $btnColor }};">{{ $btnText }}</a>
                </nav>
            </details>
            @unless($isMyClosq)
            <div class="group relative">
                <a href="{{ route('cart') }}" class="inline-flex items-center gap-1.5 font-semibold text-[var(--ink)]" aria-label="Cart, {{ $headerCartItemCount }} item{{ $headerCartItemCount === 1 ? '' : 's' }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-1.1 2.2A1 1 0 0 0 6.8 17H19M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" /></svg>
                    <span data-cart-count>{{ $headerCartItemCount }}</span>
                    <span>Cart</span>
                </a>
                <div class="invisible absolute right-0 top-full z-50 mt-3 w-80 translate-y-1 rounded-lg border border-stone-200 bg-white p-4 opacity-0 shadow-lg transition duration-150 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
                    <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                        <span class="font-semibold text-stone-900">Your cart</span>
                        <span class="text-xs text-stone-500"><span data-cart-count>{{ $headerCartItemCount }}</span> item{{ $headerCartItemCount === 1 ? '' : 's' }}</span>
                    </div>
                    @auth
                        @if($headerCart && $headerCart->cartItems->isNotEmpty())
                            <div class="max-h-56 divide-y divide-stone-100 overflow-y-auto">
                                @foreach($headerCart->cartItems as $item)
                                    <div class="flex items-start justify-between gap-4 py-3 text-sm">
                                        <span class="line-clamp-2 font-medium text-stone-800">{{ $item->productVariant->product->name }}</span>
                                        <span class="shrink-0 text-stone-500">x{{ $item->quantity }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="py-5 text-sm text-stone-500">Your cart is empty.</p>
                        @endif
                    @else
                        <p class="py-5 text-sm text-stone-500">Sign in to view your cart.</p>
                    @endauth
                    <a href="{{ route('cart') }}" class="btn-primary mt-3 w-full !rounded-lg !px-4 !py-2 text-sm">View cart</a>
                </div>
            </div>
            @endunless
            @unless($isMyClosq)
                @auth
                    <details class="group relative hidden md:block">
                        <summary class="flex max-w-[14rem] cursor-pointer list-none items-center gap-2 rounded-full border border-stone-200 bg-white px-3 py-2 text-left leading-tight [&::-webkit-details-marker]:hidden">
                            <span class="min-w-0 flex-1">
                                <span class="block truncate font-semibold text-stone-800">{{ $authUser?->name ?? 'Account' }}</span>
                                <span class="block truncate text-xs text-stone-500">{{ $roleLabel }}</span>
                            </span>
                            <svg class="h-4 w-4 shrink-0 text-stone-500 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                        </summary>
                        <div class="absolute right-0 z-50 mt-2 w-48 rounded-lg border border-stone-200 bg-white p-2 shadow-lg">
                            <a href="{{ route($accountRoute) }}" class="block rounded-md px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Account</a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-stone-100 pt-2">
                                @csrf
                                <button type="submit" class="w-full rounded-md px-3 py-2 text-left text-sm font-semibold text-red-700 transition hover:bg-red-50">Sign out</button>
                            </form>
                        </div>
                    </details>
                @else
                    <a href="{{ route('login') }}" class="hidden items-center gap-1.5 font-semibold text-[var(--ink)] md:inline-flex"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4" /><path stroke-linecap="round" d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6" /></svg>Login</a>
                @endauth
            @endunless
        </div>
    </div>
</header>
