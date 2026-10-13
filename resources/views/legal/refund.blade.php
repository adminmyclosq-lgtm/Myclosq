@extends('layouts.app', ['title' => 'Refund Policy - Guided Wellness'])
@section('content')
<div class="mx-auto max-w-4xl px-5 py-16 sm:px-8">
    <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Legal & Financial</div>
    <h1 class="serif mt-3 text-3xl md:text-4xl text-stone-900">Refund & Return Policy</h1>
    <p class="mt-2 text-xs text-stone-500">Effective Date: January 1, 2026 | Last Updated: October 2026</p>

    <div class="mt-8 space-y-6 text-sm text-stone-700 leading-relaxed border-t border-stone-200 pt-6">
        <section>
            <h2 class="serif text-xl text-stone-900 mb-2">1. 30-Day Protocol Satisfaction Guarantee</h2>
            <p>At Guided Wellness by Kurate Wellness Private Limited, we stand behind the clinical integrity and biological design of our 30-Day Gut Reset program. If you follow the complete 30-day reset protocol with verified daily adherence and do not experience meaningful digestive support, you may initiate a refund review within 30 days of product delivery.</p>
        </section>

        <section>
            <h2 class="serif text-xl text-stone-900 mb-2">2. Damaged or Defective Items</h2>
            <p>If your package arrives damaged, unsealed, or compromised in transit, please notify us within 48 hours of delivery at <a href="mailto:support@guidedwellness.in" class="underline text-[var(--ink)]">support@guidedwellness.in</a> with photos of the outer box and product seal. We will dispatch an immediate replacement at zero cost or issue a 100% refund.</p>
        </section>

        <section>
            <h2 class="serif text-xl text-stone-900 mb-2">3. Refund Processing Timeline</h2>
            <p>Approved refunds are processed back to the original method of payment (UPI, Credit/Debit Card, Net Banking via Razorpay) within 5 to 7 business days from authorization. For offline bank transfers, our accounts team will request your NEFT/IMPS details.</p>
        </section>

        <section>
            <h2 class="serif text-xl text-stone-900 mb-2">4. Order Cancellation Before Dispatch</h2>
            <p>Orders can be cancelled prior to dispatch directly from your account or by contacting support. Once an order is dispatched and with the courier, cancellation is not possible until delivery or return transit.</p>
        </section>
    </div>
</div>
@endsection
