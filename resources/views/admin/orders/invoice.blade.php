<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice - {{ $order->order_number }} - Guided Wellness</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .invoice-card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body class="bg-stone-100 text-stone-900 font-sans min-h-screen py-8 px-4 sm:px-6">

    {{-- Top Action Toolbar (hidden on print) --}}
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between">
        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-600 hover:text-stone-900 transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Order Details
        </a>
        <div class="flex items-center gap-3">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-[#204b34] px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-[#183927] transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print / Save as PDF
            </button>
        </div>
    </div>

    {{-- Printable Invoice Card --}}
    <div class="invoice-card max-w-4xl mx-auto bg-white rounded-2xl border border-stone-200 shadow-sm p-8 sm:p-12">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 border-b border-stone-200 pb-8">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-[#204b34] text-xs font-bold text-white tracking-wider">GW</span>
                    <span class="text-xl font-bold tracking-tight text-stone-900">Guided Wellness</span>
                </div>
                <div class="mt-2 text-xs text-stone-500 leading-relaxed">
                    <p class="font-semibold text-stone-800">Kurate Wellness Private Limited</p>
                    <p>contact@guidedwellness.in · +91 98765 43210</p>
                    <p>www.guidedwellness.in</p>
                </div>
            </div>

            <div class="sm:text-right">
                <div class="text-2xl font-serif font-bold text-[#204b34] tracking-tight">TAX INVOICE</div>
                <div class="mt-2 space-y-1 text-xs text-stone-600">
                    <div><span class="text-stone-400">Invoice No:</span> <strong class="font-mono text-stone-900">INV-{{ $order->order_number }}</strong></div>
                    <div><span class="text-stone-400">Order ID:</span> <span class="font-mono">{{ $order->order_number }}</span></div>
                    <div><span class="text-stone-400">Date:</span> {{ $order->placed_at ? $order->placed_at->format('d M Y') : $order->created_at->format('d M Y') }}</div>
                    <div><span class="text-stone-400">Payment Status:</span> <strong class="uppercase {{ $order->payment_status === 'paid' ? 'text-emerald-700' : 'text-amber-700' }}">{{ $order->payment_status }}</strong></div>
                </div>
            </div>
        </div>

        {{-- Addresses Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 py-7 border-b border-stone-200 text-xs">
            {{-- Billed To --}}
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-2">Billed To</div>
                @php
                    $billing = $order->billing_address_json ?: $order->shipping_address_json;
                @endphp
                <div class="font-semibold text-stone-900 text-sm mb-1">{{ $billing['recipient_name'] ?? ($order->user?->name ?? 'Demo Customer') }}</div>
                <div class="text-stone-600 leading-relaxed space-y-0.5">
                    <div>{{ $billing['address_line1'] ?? '' }}</div>
                    @if(!empty($billing['address_line2']))
                        <div>{{ $billing['address_line2'] }}</div>
                    @endif
                    <div>{{ $billing['city'] ?? '' }}, {{ $billing['state'] ?? '' }} {{ $billing['postal_code'] ?? '' }}</div>
                    <div>{{ $billing['country'] ?? 'India' }}</div>
                    <div class="pt-1 text-stone-500 font-medium">{{ $billing['phone'] ?? ($order->user?->mobile ?? '') }}</div>
                    <div class="text-stone-500">{{ $order->user?->email ?? '' }}</div>
                </div>
            </div>

            {{-- Shipped To --}}
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-2">Shipped To</div>
                @php
                    $shipping = $order->shipping_address_json ?: [];
                @endphp
                <div class="font-semibold text-stone-900 text-sm mb-1">{{ $shipping['recipient_name'] ?? ($order->user?->name ?? 'Demo Customer') }}</div>
                <div class="text-stone-600 leading-relaxed space-y-0.5">
                    <div>{{ $shipping['address_line1'] ?? '' }}</div>
                    @if(!empty($shipping['address_line2']))
                        <div>{{ $shipping['address_line2'] }}</div>
                    @endif
                    <div>{{ $shipping['city'] ?? '' }}, {{ $shipping['state'] ?? '' }} {{ $shipping['postal_code'] ?? '' }}</div>
                    <div>{{ $shipping['country'] ?? 'India' }}</div>
                    <div class="pt-1 text-stone-500 font-medium">{{ $shipping['phone'] ?? '' }}</div>
                </div>
            </div>
        </div>

        {{-- Order Items Table --}}
        <div class="py-6">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-stone-200 text-[11px] font-bold uppercase tracking-wider text-stone-400">
                        <th class="pb-3 pr-4">#</th>
                        <th class="pb-3 pr-4">Description</th>
                        <th class="pb-3 px-4">SKU</th>
                        <th class="pb-3 px-4 text-right">Unit Price</th>
                        <th class="pb-3 px-4 text-center">Qty</th>
                        <th class="pb-3 pl-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @foreach($order->orderItems as $idx => $item)
                        <tr>
                            <td class="py-3.5 pr-4 font-mono text-stone-400">{{ $idx + 1 }}</td>
                            <td class="py-3.5 pr-4">
                                <div class="font-semibold text-stone-900">{{ $item->product_name_snapshot }}</div>
                                @if($item->productVariant?->name)
                                    <div class="text-[11px] text-stone-500 mt-0.5">Variant: {{ $item->productVariant->name }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-stone-500">
                                {{ $item->sku_snapshot ?: ($item->productVariant?->sku ?: '—') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                ₹{{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-medium">
                                {{ $item->quantity }}
                            </td>
                            <td class="py-3.5 pl-4 text-right font-semibold text-stone-900">
                                ₹{{ number_format($item->line_total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Financial Summary --}}
        <div class="border-t border-stone-200 pt-5 flex justify-end">
            <div class="w-full max-w-xs space-y-2 text-xs text-stone-600">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span class="font-medium text-stone-900">₹{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Shipping Charges</span>
                    <span class="font-medium text-stone-900">₹{{ number_format($order->shipping_amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Taxes (GST)</span>
                    <span class="font-medium text-stone-900">₹{{ number_format($order->tax_amount, 2) }}</span>
                </div>
                @if((float)$order->discount_amount > 0)
                    <div class="flex justify-between text-emerald-700">
                        <span>Discount</span>
                        <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="border-t-2 border-stone-900 pt-3 flex items-baseline justify-between text-base font-bold text-[#204b34]">
                    <span>Total Amount</span>
                    <span class="text-xl">₹{{ number_format($order->grand_total, 2) }}</span>
                </div>
                @if((float)$order->refund_amount > 0)
                    <div class="flex justify-between text-purple-700 pt-1 border-t border-dashed border-stone-200">
                        <span>Total Refunded</span>
                        <span>-₹{{ number_format($order->refund_amount, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Payment & Terms Info --}}
        <div class="mt-10 pt-6 border-t border-stone-100 grid grid-cols-1 sm:grid-cols-2 gap-6 text-[11px] text-stone-500">
            <div>
                <div class="font-semibold text-stone-700 mb-1">Payment Information</div>
                <p>Method: {{ ucfirst(str_replace('_', ' ', $order->payments->first()?->gateway ?? 'Online')) }}</p>
                @if($order->payments->first()?->transactions->first()?->gateway_transaction_id)
                    <p class="font-mono">Transaction ID: {{ $order->payments->first()->transactions->first()->gateway_transaction_id }}</p>
                @endif
                <p>Status: {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</p>
            </div>
            <div class="sm:text-right">
                <div class="font-semibold text-stone-700 mb-1">Thank you for your business!</div>
                <p>This is a computer-generated tax invoice and requires no physical signature.</p>
                <p>For questions or support: support@guidedwellness.in</p>
            </div>
        </div>

    </div>

</body>
</html>
