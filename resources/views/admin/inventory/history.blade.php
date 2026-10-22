@extends('layouts.admin')

@section('title', 'Inventory Movement History')

@section('content')
<div class="p-6 md:p-8 space-y-6 max-w-[1600px] mx-auto">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.inventory.index') }}" class="h-9 w-9 rounded-xl bg-white border border-stone-200 flex items-center justify-center text-stone-500 hover:text-stone-800 hover:bg-stone-50 transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                        Audit Trail
                    </span>
                    <span class="text-xs text-stone-400">• Warehouse Stock Movements</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-stone-900 tracking-tight mt-0.5 font-serif">Inventory Movement History</h1>
            </div>
        </div>

        <div>
            <a href="{{ route('admin.inventory.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-stone-200 text-stone-700 hover:bg-stone-50 text-xs font-semibold rounded-xl shadow-xs transition">
                <span>Stock Levels Table</span>
                <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="bg-white rounded-2xl border border-stone-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.inventory.history') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search -->
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Search Item / Remarks</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Product, SKU, or remarks..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                    <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Movement Type -->
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Movement Type</label>
                <select name="type" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                    <option value="">All Movement Types</option>
                    <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Sale (Order Deduction)</option>
                    <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Purchase / Inward Supply</option>
                    <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund / Restock</option>
                    <option value="cancellation" {{ request('type') == 'cancellation' ? 'selected' : '' }}>Order Cancellation</option>
                    <option value="manual_adjustment" {{ request('type') == 'manual_adjustment' ? 'selected' : '' }}>Manual Adjustment</option>
                    <option value="damage" {{ request('type') == 'damage' ? 'selected' : '' }}>Damage Write-off</option>
                    <option value="opening_stock" {{ request('type') == 'opening_stock' ? 'selected' : '' }}>Opening Stock</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-500 mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl transition shadow-xs flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'type', 'date_from', 'date_to']))
                    <a href="{{ route('admin.inventory.history') }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-semibold rounded-xl transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Movements History Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-600">
                <thead class="bg-stone-50/80 border-b border-stone-200/80 text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                    <tr>
                        <th class="py-3.5 px-4">Date & Time</th>
                        <th class="py-3.5 px-3">Product Item</th>
                        <th class="py-3.5 px-3">Movement Type</th>
                        <th class="py-3.5 px-3 text-center">Previous Qty</th>
                        <th class="py-3.5 px-3 text-center">Difference</th>
                        <th class="py-3.5 px-3 text-center">New Balance</th>
                        <th class="py-3.5 px-4">Reason / Remarks</th>
                        <th class="py-3.5 px-4 text-right">User / Staff</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($movements as $m)
                        @php
                            $variant = $m->inventory?->productVariant;
                            $product = $variant?->product;
                            $type = $m->transaction_type;
                            $badgeColor = match($type) {
                                'sale' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'purchase', 'opening_stock' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'refund', 'cancellation' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'damage' => 'bg-amber-50 text-amber-700 border-amber-200',
                                default => 'bg-purple-50 text-purple-700 border-purple-200',
                            };
                        @endphp
                        <tr class="hover:bg-stone-50/70 transition">
                            <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">
                                {{ $m->created_at ? $m->created_at->format('d M Y, h:i A') : '—' }}
                            </td>
                            <td class="py-3.5 px-3 min-w-[200px]">
                                <a href="{{ route('admin.inventory.show', $m->inventory_id) }}" class="font-semibold text-stone-900 hover:text-emerald-700 transition block">
                                    {{ $product?->name ?? 'Product' }}
                                </a>
                                <div class="text-[11px] text-stone-400 font-mono mt-0.5">
                                    SKU: {{ $variant?->sku ?? '—' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border {{ $badgeColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-center font-medium text-stone-700 whitespace-nowrap">
                                {{ $m->previous_quantity ?? ($m->balance_after - $m->quantity) }}
                            </td>
                            <td class="py-3.5 px-3 text-center font-bold text-sm whitespace-nowrap {{ $m->quantity >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $m->diff_label }}
                            </td>
                            <td class="py-3.5 px-3 text-center font-bold text-stone-900 whitespace-nowrap">
                                {{ $m->balance_after }}
                            </td>
                            <td class="py-3.5 px-4 text-stone-700 max-w-[280px]">
                                <div class="truncate" title="{{ $m->remarks }}">{{ $m->remarks ?: '—' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-medium text-stone-800 whitespace-nowrap">
                                {{ $m->creator?->name ?? 'System' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-stone-400">
                                No stock movements logged matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="p-4 border-t border-stone-200/80 bg-stone-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-stone-500">
                <div>
                    Showing <span class="font-semibold text-stone-700">{{ $movements->firstItem() }}</span> to <span class="font-semibold text-stone-700">{{ $movements->lastItem() }}</span> of <span class="font-semibold text-stone-700">{{ $movements->total() }}</span> records
                </div>
                <div>
                    {{ $movements->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
