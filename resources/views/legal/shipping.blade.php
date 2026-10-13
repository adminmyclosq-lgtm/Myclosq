@extends('layouts.app', ['title' => 'Shipping Policy - Guided Wellness'])
@section('content')
<div class="mx-auto max-w-4xl px-5 py-16 sm:px-8">
    <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Fulfilment & Delivery</div>
    <h1 class="serif mt-3 text-3xl md:text-4xl text-stone-900">Shipping Policy</h1>
    <p class="mt-2 text-xs text-stone-500">Effective Date: January 1, 2026 | Last Updated: October 2026</p>

    <div class="mt-8 space-y-6 text-sm text-stone-700 leading-relaxed border-t border-stone-200 pt-6">
        <section>
            <h2 class="serif text-xl text-stone-900 mb-2">1. Dispatch Timeline</h2>
            <p>All orders placed on Guided Wellness are verified, packed in temperature-stable protective materials, and handed over to our premium courier partners within 24 to 48 hours of order confirmation (excluding Sundays and national holidays).</p>
        </section>

        <section>
            <h2 class="serif text-xl text-stone-900 mb-2">2. Delivery Estimates Across India</h2>
            <ul class="list-disc pl-5 space-y-1 text-stone-600">
                <li><strong>Metro Cities (Mumbai, Delhi NCR, Bengaluru, Hyderabad, Chennai, Kolkata):</strong> 2 to 4 business days.</li>
                <li><strong>Tier 2 & Tier 3 Cities:</strong> 4 to 6 business days.</li>
                <li><strong>North-East & Remote Regions:</strong> 5 to 7 business days.</li>
            </ul>
        </section>

        <section>
            <h2 class="serif text-xl text-stone-900 mb-2">3. Tracking & Live Updates</h2>
            <p>Once your kit is dispatched, you will receive an automated tracking link via SMS, WhatsApp, and email. You can also monitor your shipment live inside your Guided Wellness account dashboard.</p>
        </section>

        <section>
            <h2 class="serif text-xl text-stone-900 mb-2">4. Shipping Charges</h2>
            <p>Standard delivery is complimentary on all 30-Day Gut Reset protocols nationwide. Any applicable express delivery fees are calculated and transparently displayed at checkout prior to payment.</p>
        </section>
    </div>
</div>
@endsection
