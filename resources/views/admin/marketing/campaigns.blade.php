@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1400px] p-6 lg:p-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-stone-900 font-serif">Marketing Campaigns</h1>
            <p class="text-xs text-stone-500 mt-1">Omnichannel wellness outreach, WhatsApp broadcasts, and promotional sequences.</p>
        </div>
        <button type="button" onclick="alert('Campaign creation wizard opened.')" class="inline-flex items-center gap-2 rounded-xl bg-[#204b34] px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-[#183927] transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Campaign
        </button>
    </div>

    {{-- Campaigns Table --}}
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-stone-100 text-[11px] font-medium uppercase tracking-[.08em] text-stone-400">
                        <th class="pb-3 pr-4">Campaign Name</th>
                        <th class="pb-3 px-4">Channel</th>
                        <th class="pb-3 px-4">Status</th>
                        <th class="pb-3 px-4">Audience</th>
                        <th class="pb-3 px-4 text-center">Open Rate</th>
                        <th class="pb-3 px-4 text-center">Conversions</th>
                        <th class="pb-3 pl-4 text-right">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @foreach($campaigns as $camp)
                        <tr>
                            <td class="py-3.5 pr-4 font-semibold text-stone-900">{{ $camp['name'] }}</td>
                            <td class="py-3.5 px-4 text-stone-600">{{ $camp['channel'] }}</td>
                            <td class="py-3.5 px-4">
                                @if($camp['status'] === 'active')
                                    <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-semibold text-emerald-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full border border-stone-200 bg-stone-50 px-2.5 py-0.5 text-[10px] font-semibold text-stone-600">
                                        Scheduled
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-stone-600">{{ $camp['audience'] }}</td>
                            <td class="py-3.5 px-4 text-center font-medium">{{ $camp['opened_rate'] }}</td>
                            <td class="py-3.5 px-4 text-center font-medium">{{ $camp['conversions'] }}</td>
                            <td class="py-3.5 pl-4 text-right font-bold text-stone-900">₹{{ number_format($camp['revenue'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
