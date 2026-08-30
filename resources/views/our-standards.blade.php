@extends('layouts.app', ['title' => 'Our Standards - Guided Wellness'])

@section('content')
<section class="border-b border-border/60">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid gap-10 py-20 md:py-32 lg:grid-cols-[1fr_1.4fr] lg:gap-14">
        <div>
            <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Our Standards</div>
            <h1 class="mt-4 font-serif text-5xl leading-[1.05] md:text-6xl">Standards you should be able to inspect.</h1>
            <p class="mt-6 max-w-md text-[14px] leading-relaxed text-foreground/70">Before you buy, you should be able to understand what is in the product, why it is there, how quality is checked and where the limits of the product and guided experience are.</p>
            <div class="mt-8 flex gap-3">
                <a href="{{ route('shop') }}" class="btn-primary">Explore Our Standards</a>
                <a href="{{ url('/fit-check') }}" class="btn-secondary">Review Safety &amp; Suitability</a>
            </div>
        </div>
        <div class="grid gap-4 mt-8 lg:mt-0">
            <div class="rounded-xl border border-border/60 bg-card p-8">
                <div class="text-[13px] font-semibold text-foreground/60">01</div>
                <div class="mt-2 font-serif text-2xl">Transparent formulation</div>
                <div class="mt-2 text-[14px] text-foreground/60">Every ingredient is disclosed with its intended role and rationale.</div>
            </div>
            <div class="rounded-xl border border-border/60 bg-card p-8">
                <div class="text-[13px] font-semibold text-foreground/60">02</div>
                <div class="mt-2 font-serif text-2xl">Verifiable quality</div>
                <div class="mt-2 text-[14px] text-foreground/60">How data and testing certificates are made accessible for independent verification.</div>
            </div>
            <div class="rounded-xl border border-border/60 bg-card p-8">
                <div class="text-[13px] font-semibold text-foreground/60">03</div>
                <div class="mt-2 font-serif text-2xl">Responsible guidance</div>
                <div class="mt-2 text-[14px] text-foreground/60">Safety information is presented up-front to ensure our products are the right fit for you.</div>
            </div>
        </div>
    </div>
</section>

<section class="py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <h2 class="font-serif text-3xl leading-tight md:text-4xl text-center">Ingredients &amp; Formulation</h2>
        <p class="mt-3 text-[14px] text-muted-foreground text-center">Selected product: 30-Day Gut Reset</p>
        
        <div class="mt-10 overflow-hidden rounded-lg border border-border/60">
            <div class="bg-primary px-6 py-4 text-[12px] uppercase tracking-[0.14em] text-primary-foreground grid grid-cols-5 gap-4">
                <div>Ingredient</div>
                <div>Declared Quantity</div>
                <div>Intended Role</div>
                <div>Source / Form</div>
                <div>Supporting Information</div>
            </div>
            <div class="bg-cream/50 px-6 py-12 text-center text-[14px] text-foreground/60">
                No data available. Select a product to view formulation details.
            </div>
        </div>
        <p class="mt-4 text-[13px] text-foreground/60 text-center">Select an ingredient to review its intended role, selection rationale, supporting information and evidence limitations.</p>
    </div>
</section>

<section class="bg-primary text-primary-foreground py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid gap-10 lg:grid-cols-[1.2fr_1.4fr] lg:gap-16 items-center">
        <div>
            <h2 class="font-serif text-3xl leading-tight md:text-4xl">Quality information should be specific — not implied.</h2>
            <p class="mt-6 max-w-md text-[15px] leading-relaxed text-primary-foreground/80">&quot;Premium Quality&quot; is a marketing term. We replace it with specific testing frameworks and verifiable batch data recorded for every production run.</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-6">
                <div class="font-serif text-lg">Ingredient Identity</div>
                <div class="mt-4 space-y-2 text-[13px] text-primary-foreground/80">
                    <div>What&#x27;s checked</div>
                    <div>Why it matters</div>
                    <div>Standard</div>
                    <div>Document</div>
                </div>
            </div>
            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-6">
                <div class="font-serif text-lg">Purity Testing</div>
                <div class="mt-4 space-y-2 text-[13px] text-primary-foreground/80">
                    <div>What&#x27;s checked</div>
                    <div>Why it matters</div>
                    <div>Standard</div>
                    <div>Document</div>
                </div>
            </div>
            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-6">
                <div class="font-serif text-lg">Declared Potency</div>
                <div class="mt-4 space-y-2 text-[13px] text-primary-foreground/80">
                    <div>What&#x27;s checked</div>
                    <div>Why it matters</div>
                    <div>Standard</div>
                    <div>Document</div>
                </div>
            </div>
            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-6">
                <div class="font-serif text-lg">Microbiological Quality</div>
                <div class="mt-4 space-y-2 text-[13px] text-primary-foreground/80">
                    <div>What&#x27;s checked</div>
                    <div>Why it matters</div>
                    <div>Standard</div>
                    <div>Document</div>
                </div>
            </div>
            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-6">
                <div class="font-serif text-lg">Packaging Integrity</div>
                <div class="mt-4 space-y-2 text-[13px] text-primary-foreground/80">
                    <div>What&#x27;s checked</div>
                    <div>Why it matters</div>
                    <div>Standard</div>
                    <div>Document</div>
                </div>
            </div>
            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-6">
                <div class="font-serif text-lg">Batch Traceability</div>
                <div class="mt-4 space-y-2 text-[13px] text-primary-foreground/80">
                    <div>What&#x27;s checked</div>
                    <div>Why it matters</div>
                    <div>Standard</div>
                    <div>Document</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 mx-auto max-w-3xl text-center">
        <h2 class="font-serif text-3xl leading-tight md:text-4xl text-center">An observation is not the same as a conclusion.</h2>
        <p class="mx-auto mt-6 text-[15px] max-w-2xl leading-relaxed text-foreground/70">We distinguish between established facts and observations within our guided courses. These observations do not diagnose conditions or prove universal causation.</p>
        
        <div class="mt-10 flex flex-wrap justify-center gap-3">
            <span class="rounded-full border border-border/70 bg-cream px-4 py-2 text-[12px] uppercase">Product Fact</span>
            <span class="rounded-full border border-border/70 bg-cream px-4 py-2 text-[12px] uppercase">Ingredient Rationale</span>
            <span class="rounded-full border border-border/70 bg-cream px-4 py-2 text-[12px] uppercase">Observed Within the Course</span>
            <span class="rounded-full border border-border/70 bg-cream px-4 py-2 text-[12px] uppercase">Possible Pattern</span>
            <span class="rounded-full border border-border/70 bg-cream px-4 py-2 text-[12px] uppercase">Not Established</span>
        </div>
        
        <div class="mx-auto mt-14 grid gap-6 md:grid-cols-2 text-left">
            <div class="rounded-xl border border-border/60 bg-card p-8">
                <div class="text-[12px] font-medium uppercase tracking-[0.18em] text-muted-foreground">What this information helps with</div>
                <ul class="mt-5 space-y-3 text-[14px] text-foreground/70 leading-relaxed">
                    <li>· Understanding documented physiological support for specific bodily systems.</li>
                    <li>· Providing biological context for how results are typically interpreted.</li>
                    <li>· Setting realistic timeframes for biological adaptation.</li>
                </ul>
            </div>
            <div class="rounded-xl border border-destructive/30 bg-destructive/[0.04] p-8">
                <div class="text-[12px] font-medium uppercase tracking-[0.18em] text-muted-foreground">What it cannot establish</div>
                <ul class="mt-5 space-y-3 text-[14px] text-foreground/70 leading-relaxed">
                    <li>· Cures for diagnosed medical conditions or disease treatments.</li>
                    <li>· Universal outcomes — biological individuality means responses will vary.</li>
                    <li>· Results that override the need for foundational healthy lifestyle choices.</li>
                </ul>
            </div>
        </div>
        <p class="mt-10 text-[12px] max-w-2xl mx-auto uppercase tracking-wide text-foreground/50">DISCLAIMER: The information above is educational. Always speak with a qualified healthcare professional regarding specific health concerns.</p>
    </div>
</section>

<section class="bg-cream/60 py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl">Safety and suitability are part of the product.</h2>
        <p class="mx-auto mt-4 max-w-xl text-center text-[15px] leading-relaxed text-foreground/70">We help you qualify yourself for our products before purchase. Review the criteria below.</p>
        
        <div class="mx-auto mt-12 grid max-w-4xl gap-6 md:grid-cols-2">
            <div class="rounded-xl border border-border/60 bg-background p-8">
                <div class="text-[12px] font-medium uppercase tracking-[0.18em] text-muted-foreground">May be relevant</div>
                <ul class="mt-5 space-y-3 text-[14px] text-foreground/75 leading-relaxed">
                    <li>· Seeking structured gut-health support</li>
                    <li>· Struggling with supplement consistency</li>
                    <li>· Desiring data-driven personal health insights</li>
                </ul>
            </div>
            <div class="rounded-xl border border-destructive/30 bg-destructive/[0.04] p-8">
                <div class="text-[12px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Speak to a doctor first</div>
                <ul class="mt-5 space-y-3 text-[14px] text-foreground/75 leading-relaxed">
                    <li>· If you are pregnant or breast-feeding</li>
                    <li>· Diagnosed gastrointestinal condition</li>
                    <li>· Taking prescription medication</li>
                </ul>
            </div>
        </div>
        
        <div class="mt-12 text-center">
            <div class="flex flex-wrap justify-center gap-6 text-[12px] uppercase tracking-[0.14em] text-foreground/60">
                <span>Usage Instructions</span>
                <span>Full Warnings</span>
                <span>Storage Guidance</span>
            </div>
            <div class="mt-10">
                <a href="{{ url('/fit-check') }}" class="btn-primary">Check Your Fit</a>
            </div>
        </div>
    </div>
</section>

<section class="py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 mx-auto max-w-3xl text-center">
        <h2 class="font-serif text-3xl leading-tight md:text-4xl">Product information you can review.</h2>
        <div class="mt-8 text-[15px] text-foreground/60 bg-cream/30 py-4 px-6 inline-block rounded-xl">Document Unavailable</div>
    </div>
</section>

<section class="bg-primary text-primary-foreground">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 py-24 lg:py-32 text-center">
        <h2 class="font-serif text-3xl md:text-4xl">Understand the product before you begin.</h2>
        <div class="mt-8 flex justify-center gap-4">
            <a href="{{ route('shop') }}" class="btn-primary !bg-background !text-foreground hover:!bg-background/90">View the 30-Day Gut Reset</a>
            <a href="{{ route('how-it-works') }}" class="btn-secondary !border-primary-foreground/30 !text-primary-foreground hover:!bg-primary-foreground/10">See How It Works</a>
        </div>
    </div>
</section>
@endsection
