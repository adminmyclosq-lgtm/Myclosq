@extends('layouts.app', ['title' => 'Support - Guided Wellness'])

@section('content')
<section class="py-16 md:py-20 text-center">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Support</div>
        <h1 class="mt-4 font-serif text-5xl leading-[1.05] md:text-6xl">How can we help?</h1>
        <p class="mx-auto mt-4 max-w-xl text-[15px] text-foreground/70">Get help with your order, product, account or course activation.</p>
        
        <div class="mx-auto mt-10 grid max-w-4xl gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-border/60 bg-card p-5 text-[14px] text-foreground/70 hover:bg-cream transition cursor-pointer">Track my order</div>
            <div class="rounded-xl border border-border/60 bg-card p-5 text-[14px] text-foreground/70 hover:bg-cream transition cursor-pointer">Ask a product question</div>
            <div class="rounded-xl border border-border/60 bg-card p-5 text-[14px] text-foreground/70 hover:bg-cream transition cursor-pointer">Suitability question</div>
            <div class="rounded-xl border border-border/60 bg-card p-5 text-[14px] text-foreground/70 hover:bg-cream transition cursor-pointer">Something else</div>
        </div>
    </div>
</section>

<section class="bg-cream/60 py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl">Where did you purchase the product?</h2>
        <div class="mx-auto mt-12 grid max-w-4xl gap-6 md:grid-cols-2">
            <div class="rounded-2xl bg-background p-8">
                <div class="font-serif text-xl border-l-[3px] border-primary pl-4">Guided Wellness Website</div>
                <p class="mt-4 text-[14px] text-foreground/60 pl-4">Order details, status, invoice, returns and refunds are all supported here.</p>
                <div class="mt-8 pl-4">
                    <a class="btn-primary" href="#">Get help with your order</a>
                </div>
            </div>
            <div class="rounded-2xl bg-background p-8">
                <div class="font-serif text-xl border-l-[3px] border-border pl-4">Amazon Purchase</div>
                <p class="mt-4 text-[14px] text-foreground/60 pl-4">Guidance for order, delivery or return questions from Amazon. Product questions still route to us.</p>
                <div class="mt-8 pl-4">
                    <a class="btn-secondary" href="#">Get Amazon Help</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl">Choose what you need help with.</h2>
        <div class="mx-auto mt-14 grid max-w-5xl gap-10 md:grid-cols-4">
            <div>
                <div class="text-[12px] font-medium uppercase tracking-[0.14em] text-primary">Order &amp; Delivery</div>
                <ul class="mt-5 space-y-3 text-[14px] text-foreground/70">
                    <li>&middot; Delayed order</li>
                    <li>&middot; Missing item</li>
                    <li>&middot; Damaged package</li>
                </ul>
            </div>
            <div>
                <div class="text-[12px] font-medium uppercase tracking-[0.14em] text-primary">Product Questions</div>
                <ul class="mt-5 space-y-3 text-[14px] text-foreground/70">
                    <li>&middot; Ingredient list</li>
                    <li>&middot; Suitability check</li>
                    <li>&middot; Dosage</li>
                </ul>
            </div>
            <div>
                <div class="text-[12px] font-medium uppercase tracking-[0.14em] text-primary">Course Activation</div>
                <ul class="mt-5 space-y-3 text-[14px] text-foreground/70">
                    <li>&middot; QR not scanning</li>
                    <li>&middot; Change my email</li>
                    <li>&middot; Restart course</li>
                </ul>
            </div>
            <div>
                <div class="text-[12px] font-medium uppercase tracking-[0.14em] text-primary">Amazon Support</div>
                <ul class="mt-5 space-y-3 text-[14px] text-foreground/70">
                    <li>&middot; Order enquiry</li>
                    <li>&middot; Return via Amazon</li>
                    <li>&middot; Amazon refunds</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="bg-cream/60 py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl">Having trouble starting your course?</h2>
        <div class="mx-auto mt-12 grid max-w-4xl gap-6 md:grid-cols-3">
            <div class="rounded-xl bg-background p-8 text-center flex flex-col items-center">
                <div class="grid h-10 w-10 place-items-center rounded-full bg-primary/10 text-primary font-semibold text-lg">1</div>
                <div class="mt-5 font-serif text-lg">Scan the QR</div>
                <div class="mt-3 text-[14px] text-foreground/60 leading-relaxed max-w-[200px]">Point your camera at the QR on the bottle.</div>
            </div>
            <div class="rounded-xl bg-background p-8 text-center flex flex-col items-center">
                <div class="grid h-10 w-10 place-items-center rounded-full bg-primary/10 text-primary font-semibold text-lg">2</div>
                <div class="mt-5 font-serif text-lg">Set Day 0</div>
                <div class="mt-3 text-[14px] text-foreground/60 leading-relaxed max-w-[200px]">Around 2&ndash;3 minutes to complete.</div>
            </div>
            <div class="rounded-xl bg-background p-8 text-center flex flex-col items-center">
                <div class="grid h-10 w-10 place-items-center rounded-full bg-primary/10 text-primary font-semibold text-lg">3</div>
                <div class="mt-5 font-serif text-lg">Begin daily</div>
                <div class="mt-3 text-[14px] text-foreground/60 leading-relaxed max-w-[200px]">Take your first capsule from Day 1.</div>
            </div>
        </div>
        <div class="mt-12 flex justify-center gap-4">
            <a href="{{ route('how-it-works') }}" class="btn-primary">Activate Course</a>
            <a href="#" class="btn-secondary">Contact Us</a>
        </div>
    </div>
</section>

<section class="py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl">Concerned about the product<br/>or how you are feeling?</h2>
        <div class="mx-auto mt-12 grid max-w-4xl gap-6 md:grid-cols-2">
            <div class="rounded-2xl border border-border/60 bg-card p-10 flex flex-col justify-between">
                <div>
                    <div class="text-[12px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Report a product concern</div>
                    <p class="mt-4 text-[15px] leading-relaxed text-foreground/70">If you are experiencing an issue with the product itself (such as packaging defects, unexpected smell, or other manufacturing-related concerns), please let us know.</p>
                </div>
                <div class="mt-8">
                    <a class="btn-primary" href="#">Report Now</a>
                </div>
            </div>
            <div class="rounded-2xl border border-destructive/30 bg-destructive/[0.04] p-10 flex flex-col justify-between">
                <div>
                    <div class="text-[12px] font-medium uppercase tracking-[0.18em] text-muted-foreground"><span class="text-destructive">Seek professional guidance first</span></div>
                    <p class="mt-4 text-[15px] leading-relaxed text-foreground/70">If you have severe or new symptoms, contact a healthcare professional before continuing. Supplements are not a replacement for medical care.</p>
                </div>
                <div class="mt-8">
                    <a href="{{ route('our-standards') }}" class="btn-secondary">Read Safety Standards</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-cream/60 py-20 lg:py-24">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <div class="mx-auto grid max-w-5xl gap-6 md:grid-cols-3">
            <div class="rounded-2xl bg-background p-8 flex flex-col h-full">
                <div class="font-serif text-xl border-l-[3px] border-[#25d366] pl-4">WhatsApp Support</div>
                <div class="mt-4 text-[14px] text-foreground/60 pl-4 flex-grow">Send us a quick message directly from your phone and our team will reply as soon as possible.</div>
                <div class="mt-8 pl-4">
                    <a class="btn-secondary" href="#">Open WhatsApp</a>
                </div>
            </div>
            <div class="rounded-2xl bg-background p-8 flex flex-col h-full">
                <div class="font-serif text-xl border-l-[3px] border-primary pl-4">Email Support</div>
                <div class="mt-4 text-[14px] text-foreground/60 pl-4 flex-grow">For non-urgent questions, documentation, or detailed inquiries about your course formulation.</div>
                <div class="mt-8 pl-4">
                    <a class="btn-secondary" href="#">Send Email</a>
                </div>
            </div>
            <div class="rounded-2xl bg-background p-8 flex flex-col h-full">
                <div class="font-serif text-xl border-l-[3px] border-primary pl-4">Contact Form</div>
                <div class="mt-4 text-[14px] text-foreground/60 pl-4 flex-grow">Share a detailed message through our secure portal to ensure all correct context is provided.</div>
                <div class="mt-8 pl-4">
                    <a class="btn-secondary" href="#">Open Form</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 lg:py-24">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <div class="mx-auto max-w-4xl rounded-2xl border border-primary/20 bg-primary/5 p-8 md:p-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
            <div>
                <div class="font-serif text-2xl">Keep these details ready.</div>
                <div class="mt-2 text-[14px] text-foreground/70">To help us assist you faster, please have this information on hand before reaching out:</div>
            </div>
            <ul class="shrink-0 space-y-3 text-[14px] text-foreground/70 font-medium">
                <li>&bull; Order number</li>
                <li>&bull; Purchase channel <span class="font-normal opacity-70">(site or Amazon)</span></li>
                <li>&bull; Batch number <span class="font-normal opacity-70">(bottom of bottle)</span></li>
                <li>&bull; Delivery date</li>
            </ul>
        </div>
    </div>
</section>

<section class="py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 mx-auto max-w-3xl">
        <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl">Common support questions.</h2>
        <div class="mt-12 divide-y divide-border/70 border-t border-b border-border/70">
            <!-- Accordion items -->
            <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 text-left font-medium [&::-webkit-details-marker]:hidden">
                    <span class="text-[15px] text-foreground group-hover:text-primary transition-colors">How can I track my order?</span>
                    <span class="text-foreground/40 transition-transform duration-300 group-open:rotate-45 text-2xl shrink-0">+</span>
                </summary>
                <div class="pb-6 pr-8 text-[14px] leading-relaxed text-foreground/70">
                    Once your order ships, you will receive a tracking link by email. You can also view your order status by logging into your account dashboard.
                </div>
            </details>
            <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 text-left font-medium [&::-webkit-details-marker]:hidden">
                    <span class="text-[15px] text-foreground group-hover:text-primary transition-colors">How do I cancel my order?</span>
                    <span class="text-foreground/40 transition-transform duration-300 group-open:rotate-45 text-2xl shrink-0">+</span>
                </summary>
                <div class="pb-6 pr-8 text-[14px] leading-relaxed text-foreground/70">
                    You can cancel your order within the first 2 hours of placement. Navigate to your recent orders in your account settings and select "Cancel".
                </div>
            </details>
            <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 text-left font-medium [&::-webkit-details-marker]:hidden">
                    <span class="text-[15px] text-foreground group-hover:text-primary transition-colors">How do I activate my course?</span>
                    <span class="text-foreground/40 transition-transform duration-300 group-open:rotate-45 text-2xl shrink-0">+</span>
                </summary>
                <div class="pb-6 pr-8 text-[14px] leading-relaxed text-foreground/70">
                    Simply scan the QR code located on the top of your product bottle using your smartphone camera. This will take you to your personal Day 0 setup page.
                </div>
            </details>
            <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 text-left font-medium [&::-webkit-details-marker]:hidden">
                    <span class="text-[15px] text-foreground group-hover:text-primary transition-colors">When are refunds processed?</span>
                    <span class="text-foreground/40 transition-transform duration-300 group-open:rotate-45 text-2xl shrink-0">+</span>
                </summary>
                <div class="pb-6 pr-8 text-[14px] leading-relaxed text-foreground/70">
                    Refunds are typically processed within 5-7 business days of an approved return reaching our facility. Your bank may take an additional 3-5 days to post the credit.
                </div>
            </details>
            <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 text-left font-medium [&::-webkit-details-marker]:hidden">
                    <span class="text-[15px] text-foreground group-hover:text-primary transition-colors">How do I use and store the product?</span>
                    <span class="text-foreground/40 transition-transform duration-300 group-open:rotate-45 text-2xl shrink-0">+</span>
                </summary>
                <div class="pb-6 pr-8 text-[14px] leading-relaxed text-foreground/70">
                    Store in a cool, dark place out of direct sunlight. Do not keep in the bathroom where humidity fluctuates. Take exactly as directed on your personalized portal.
                </div>
            </details>
            <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 text-left font-medium [&::-webkit-details-marker]:hidden">
                    <span class="text-[15px] text-foreground group-hover:text-primary transition-colors">What is your return and refund policy?</span>
                    <span class="text-foreground/40 transition-transform duration-300 group-open:rotate-45 text-2xl shrink-0">+</span>
                </summary>
                <div class="pb-6 pr-8 text-[14px] leading-relaxed text-foreground/70">
                    We offer a 30-day return policy for unopened items in their original packaging. Please contact support to initiate a return request with your order details.
                </div>
            </details>
            <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 text-left font-medium [&::-webkit-details-marker]:hidden">
                    <span class="text-[15px] text-foreground group-hover:text-primary transition-colors">I bought this from Amazon, can you help?</span>
                    <span class="text-foreground/40 transition-transform duration-300 group-open:rotate-45 text-2xl shrink-0">+</span>
                </summary>
                <div class="pb-6 pr-8 text-[14px] leading-relaxed text-foreground/70">
                    We can answer all product and course-related questions. However, for issues regarding delivery, missing packages, or returns, you must go through Amazon's customer service portal.
                </div>
            </details>
            <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 text-left font-medium [&::-webkit-details-marker]:hidden">
                    <span class="text-[15px] text-foreground group-hover:text-primary transition-colors">I have a concern about the packaging?</span>
                    <span class="text-foreground/40 transition-transform duration-300 group-open:rotate-45 text-2xl shrink-0">+</span>
                </summary>
                <div class="pb-6 pr-8 text-[14px] leading-relaxed text-foreground/70">
                    Please take a photo of the defect along with the batch number on the bottom of the bottle, and email it to our support team so we can investigate.
                </div>
            </details>
            <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 text-left font-medium [&::-webkit-details-marker]:hidden">
                    <span class="text-[15px] text-foreground group-hover:text-primary transition-colors">Should I consult a doctor before starting?</span>
                    <span class="text-foreground/40 transition-transform duration-300 group-open:rotate-45 text-2xl shrink-0">+</span>
                </summary>
                <div class="pb-6 pr-8 text-[14px] leading-relaxed text-foreground/70">
                    Yes. We always advise checking with a qualified healthcare provider before starting any new supplement regimen, especially if you have known medical conditions or take prescription medication.
                </div>
            </details>
        </div>
    </div>
</section>

<section class="bg-primary text-primary-foreground">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 py-24 text-center">
        <h2 class="font-serif text-3xl md:text-4xl">Already in your 30-day course?</h2>
        <p class="mx-auto mt-5 max-w-md text-[15px] text-primary-foreground/80 leading-relaxed">Log in for specific help based on your current product, course day and journey state.</p>
        <div class="mt-8">
            <a href="{{ route('login') }}" class="btn-primary !bg-background !text-foreground hover:!bg-background/90">Log in for help</a>
        </div>
    </div>
</section>
@endsection
