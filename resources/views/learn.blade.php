@extends('layouts.app', ['title' => 'Learn - Guided Wellness'])

@section('content')
<section class="border-b border-border/60">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid gap-10 py-20 md:py-32 lg:grid-cols-[1.05fr_1fr] lg:gap-14">
        <div>
            <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Learn</div>
            <h1 class="mt-4 font-serif text-5xl leading-[1.05] md:text-6xl">Understand your gut — and judge a product trial more clearly.</h1>
            <p class="mt-6 max-w-md text-[15px] leading-relaxed text-foreground/70">Practical guidance for observing meaningful signals from ordinary day-to-day variation, giving a product a fair trial, and deciding what to do next.</p>
            <div class="mt-8 flex gap-3">
                <a href="{{ route('how-it-works') }}" class="btn-primary">Product Guides</a>
                <a href="{{ url('/our-standards') }}" class="btn-secondary">See Our Standards</a>
            </div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-cream min-h-[400px]">
            <img src="{{ asset('assets/hero-product-CftTmpl1.jpg') }}" alt="Learn — window light" class="h-full w-full object-cover"/>
        </div>
    </div>
</section>

<section class="py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl">Start with what you are trying to understand.</h2>
        <div class="mt-12 grid gap-6 md:grid-cols-3">
            <a class="block rounded-2xl border border-border/60 bg-card p-8 transition hover:bg-cream/60" href="#">
                <div class="font-serif text-lg uppercase tracking-[0.14em]">Understand everyday gut patterns</div>
                <p class="mt-4 text-[14px] leading-relaxed text-foreground/60">Why digestion can feel different from one day to the next.</p>
                <div class="mt-6 text-[12px] uppercase tracking-[0.14em] text-primary font-medium">Read the guide &rarr;</div>
            </a>
            <a class="block rounded-2xl border border-border/60 bg-card p-8 transition hover:bg-cream/60" href="#">
                <div class="font-serif text-lg uppercase tracking-[0.14em]">Judge a product trial</div>
                <p class="mt-4 text-[14px] leading-relaxed text-foreground/60">How to give a product a fair chance.</p>
                <div class="mt-6 text-[12px] uppercase tracking-[0.14em] text-primary font-medium">Read the guide &rarr;</div>
            </a>
            <a class="block rounded-2xl border border-border/60 bg-card p-8 transition hover:bg-cream/60" href="#">
                <div class="font-serif text-lg uppercase tracking-[0.14em]">Know when guidance is needed</div>
                <p class="mt-4 text-[14px] leading-relaxed text-foreground/60">What self-observation is meant for — and where it ends.</p>
                <div class="mt-6 text-[12px] uppercase tracking-[0.14em] text-primary font-medium">Read the guide &rarr;</div>
            </a>
        </div>
    </div>
</section>

<section class="bg-cream/60 py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid items-center gap-14 lg:grid-cols-[1fr_1fr] lg:gap-20">
        <div class="overflow-hidden rounded-2xl bg-background min-h-[400px]">
            <img src="{{ asset('assets/bottle-capsules-COsDFlLa.jpg') }}" alt="Bedside supplement" class="h-full w-full object-cover"/>
        </div>
        <div>
            <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Product Trials</div>
            <h3 class="mt-4 font-serif text-3xl leading-tight md:text-4xl">Why supplements are easy to start — and difficult to judge.</h3>
            <p class="mt-6 max-w-md text-[15px] leading-relaxed text-foreground/70">Taking the first capsule is simple. Interpreting what happened is harder. Starting conditions, variables, observation and drift — the context all influences when a product trial leads to a useful conclusion.</p>
            <div class="mt-8">
                <a class="btn-primary" href="#">Read the guide &rarr;</a>
            </div>
        </div>
    </div>
</section>

<section class="py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <div class="grid gap-8 md:grid-cols-3">
            <a class="group block" href="#">
                <div class="overflow-hidden rounded-2xl bg-cream">
                    <img src="{{ asset('assets/hero-product-CftTmpl1.jpg') }}" alt="" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-[1.03]"/>
                </div>
                <div class="mt-5">
                    <div class="text-[11px] font-medium uppercase tracking-[0.14em] text-primary">Trial Design</div>
                    <div class="mt-3 font-serif text-xl leading-snug">What a fair 30-day product trial looks like</div>
                    <div class="mt-3 text-[12px] font-medium uppercase tracking-[0.14em] text-foreground/50 group-hover:text-primary transition-colors">Read the guide &rarr;</div>
                </div>
            </a>
            <a class="group block" href="#">
                <div class="overflow-hidden rounded-2xl bg-cream">
                    <img src="{{ asset('assets/bottle-capsules-COsDFlLa.jpg') }}" alt="" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-[1.03]"/>
                </div>
                <div class="mt-5">
                    <div class="text-[11px] font-medium uppercase tracking-[0.14em] text-primary">Observation</div>
                    <div class="mt-3 font-serif text-xl leading-snug">Why one good day — or one difficult day — does not prove much</div>
                    <div class="mt-3 text-[12px] font-medium uppercase tracking-[0.14em] text-foreground/50 group-hover:text-primary transition-colors">Read the guide &rarr;</div>
                </div>
            </a>
            <a class="group block" href="#">
                <div class="overflow-hidden rounded-2xl bg-cream">
                    <img src="{{ asset('assets/course-kit-DtVPc2tT.jpg') }}" alt="" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-[1.03]"/>
                </div>
                <div class="mt-5">
                    <div class="text-[11px] font-medium uppercase tracking-[0.14em] text-primary">Context</div>
                    <div class="mt-3 font-serif text-xl leading-snug">How meals, sleep, stress and travel affect what you notice</div>
                    <div class="mt-3 text-[12px] font-medium uppercase tracking-[0.14em] text-foreground/50 group-hover:text-primary transition-colors">Read the guide &rarr;</div>
                </div>
            </a>
            <a class="group block" href="#">
                <div class="overflow-hidden rounded-2xl bg-cream">
                    <img src="{{ asset('assets/hero-product-CftTmpl1.jpg') }}" alt="" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-[1.03]"/>
                </div>
                <div class="mt-5">
                    <div class="text-[11px] font-medium uppercase tracking-[0.14em] text-primary">Reporting</div>
                    <div class="mt-3 font-serif text-xl leading-snug">What the Gut Response Brief can — and cannot — tell you</div>
                    <div class="mt-3 text-[12px] font-medium uppercase tracking-[0.14em] text-foreground/50 group-hover:text-primary transition-colors">Read the guide &rarr;</div>
                </div>
            </a>
            <a class="group block" href="#">
                <div class="overflow-hidden rounded-2xl bg-cream">
                    <img src="{{ asset('assets/bottle-capsules-COsDFlLa.jpg') }}" alt="" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-[1.03]"/>
                </div>
                <div class="mt-5">
                    <div class="text-[11px] font-medium uppercase tracking-[0.14em] text-primary">Practicality</div>
                    <div class="mt-3 font-serif text-xl leading-snug">How to use a daily supplement without building your life around it</div>
                    <div class="mt-3 text-[12px] font-medium uppercase tracking-[0.14em] text-foreground/50 group-hover:text-primary transition-colors">Read the guide &rarr;</div>
                </div>
            </a>
            <a class="group block" href="#">
                <div class="overflow-hidden rounded-2xl bg-cream">
                    <img src="{{ asset('assets/course-kit-DtVPc2tT.jpg') }}" alt="" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-[1.03]"/>
                </div>
                <div class="mt-5">
                    <div class="text-[11px] font-medium uppercase tracking-[0.14em] text-primary">Safety</div>
                    <div class="mt-3 font-serif text-xl leading-snug">When digestive symptoms need medical attention</div>
                    <div class="mt-3 text-[12px] font-medium uppercase tracking-[0.14em] text-foreground/50 group-hover:text-primary transition-colors">Read the guide &rarr;</div>
                </div>
            </a>
        </div>
    </div>
</section>

<section class="bg-primary text-primary-foreground py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid gap-12 lg:grid-cols-[1fr_1.5fr] lg:gap-20">
        <div>
            <h2 class="font-serif text-3xl leading-tight md:text-4xl">An observation is not the same as a conclusion.</h2>
            <p class="mt-6 max-w-sm text-[15px] leading-relaxed text-primary-foreground/80">We use precise language to help differentiate between immediate changes and established long-term fits.</p>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-6">
                <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground"><span class="text-primary-foreground/70">Reported</span></div>
                <p class="mt-3 text-[14px] leading-relaxed text-primary-foreground/85">An experience or observation shared by the user during the course.</p>
            </div>
            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-6">
                <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground"><span class="text-primary-foreground/70">Observed During the Course</span></div>
                <p class="mt-3 text-[14px] leading-relaxed text-primary-foreground/85">A change that plausibly reflects a signal, not a whole outcome.</p>
            </div>
            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-6">
                <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground"><span class="text-primary-foreground/70">Possible Pattern</span></div>
                <p class="mt-3 text-[14px] leading-relaxed text-primary-foreground/85">Emerging response signal that requires more time to confirm.</p>
            </div>
            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-6">
                <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground"><span class="text-primary-foreground/70">Not Established</span></div>
                <p class="mt-3 text-[14px] leading-relaxed text-primary-foreground/85">Signals that need external evaluation to become a conclusion.</p>
            </div>
        </div>
        <div class="lg:col-span-2 mt-4">
            <a href="{{ url('/our-standards') }}" class="btn-secondary !border-primary-foreground/30 !text-primary-foreground hover:!bg-primary-foreground/10">Review Our Standards</a>
        </div>
    </div>
</section>

<section class="bg-cream/60 py-24 lg:py-32 text-center">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Ready when you are</div>
        <h2 class="mx-auto mt-5 max-w-2xl font-serif text-3xl leading-tight md:text-4xl">A product trial should lead to a clearer decision.</h2>
        <p class="mx-auto mt-6 max-w-md text-[15px] leading-relaxed text-foreground/70">The 30-Day Gut Reset combines the daily capsule, a few guided moments and a personal Gut Response Brief.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('how-it-works') }}" class="btn-primary">See How It Works</a>
            <a href="{{ route('shop') }}" class="btn-secondary">View the 30-Day Gut Reset</a>
        </div>
    </div>
</section>

<section class="border-t border-border/60 py-10">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 flex flex-col items-center justify-between gap-4 text-[13px] text-foreground/60 sm:flex-row">
        <div>Some symptoms should not wait for a product trial.</div>
        <div class="flex flex-wrap items-center gap-6">
            <a href="{{ url('/our-standards') }}" class="underline hover:text-foreground transition-colors">Review Safety &amp; Suitability</a>
            <a href="{{ url('/support') }}" class="underline hover:text-foreground transition-colors">Visit Support</a>
        </div>
    </div>
</section>
@endsection
