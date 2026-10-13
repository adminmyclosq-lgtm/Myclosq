@extends('layouts.app', ['title' => 'Privacy Policy - Guided Wellness'])
@section('content')
<div class="mx-auto max-w-4xl px-5 py-16 sm:px-8">
    <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Legal & Privacy</div>
    <h1 class="serif mt-3 text-3xl md:text-4xl text-stone-900">Privacy Policy</h1>
    <p class="mt-2 text-xs text-stone-500">Effective Date: January 1, 2026 | Last Updated: October 2026</p>

    <div class="mt-8 space-y-6 text-sm text-stone-700 leading-relaxed border-t border-stone-200 pt-6">
        <section>
            <h2 class="serif text-xl text-stone-900 mb-2">1. Overview</h2>
            <p>Kurate Wellness Private Limited ("Guided Wellness", "we", "us", or "our") is dedicated to protecting your personal information and biological privacy. This Privacy Policy outlines how we collect, handle, process, and safeguard information when you use our website, mobile interface, 30-day reset programs, or customer care channels.</p>
        </section>

        <section>
            <h2 class="serif text-xl text-stone-900 mb-2">2. Information We Collect</h2>
            <ul class="list-disc pl-5 space-y-1 text-stone-600">
                <li><strong>Identity & Contact Information:</strong> Full name, email address, mobile number, and shipping addresses for order delivery.</li>
                <li><strong>Reset Journey Data:</strong> Check-in responses, adherence milestones, digestion notes, and lifestyle indicators submitted voluntarily during your 30-day protocol.</li>
                <li><strong>Transaction Records:</strong> Payment gateway transaction IDs, order histories, and fulfilment tracking details (card and bank credentials are processed directly through secure RBI-authorized payment aggregators such as Razorpay and are never stored on our servers).</li>
            </ul>
        </section>

        <section>
            <h2 class="serif text-xl text-stone-900 mb-2">3. How We Use Your Data</h2>
            <p>We use your information exclusively to fulfill your orders, provide day-by-day protocol guidance, deliver customer support, comply with applicable regulatory mandates, and improve protocol efficacy. We do not sell, rent, or trade your personal or health-related responses to third-party advertisers.</p>
        </section>

        <section>
            <h2 class="serif text-xl text-stone-900 mb-2">4. Data Security</h2>
            <p>All data transmitted between your browser and Guided Wellness is encrypted using TLS 1.3 encryption. Health signals and journey responses are stored with granular access controls and audit logging.</p>
        </section>

        <section>
            <h2 class="serif text-xl text-stone-900 mb-2">5. Contact Us</h2>
            <p>For data inquiries, access requests, or privacy concerns, please contact our Data Protection Team at <a href="mailto:support@guidedwellness.in" class="underline text-[var(--ink)]">support@guidedwellness.in</a> or visit our Help Centre.</p>
        </section>
    </div>
</div>
@endsection
