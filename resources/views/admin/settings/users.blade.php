@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1400px] p-6 lg:p-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-stone-900 font-serif">Users &amp; Team Management</h1>
            <p class="text-xs text-stone-500 mt-1">Manage admin team members, role assignments, and dashboard access.</p>
        </div>
        <button type="button" onclick="alert('Invite staff modal')" class="inline-flex items-center gap-2 rounded-xl bg-[#204b34] px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-[#183927] transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Invite Staff Member
        </button>
    </div>

    {{-- Users List Table --}}
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-stone-100 text-[11px] font-medium uppercase tracking-[.08em] text-stone-400">
                        <th class="pb-3 pr-4">User</th>
                        <th class="pb-3 px-4">Contact</th>
                        <th class="pb-3 px-4">Assigned Roles</th>
                        <th class="pb-3 px-4 text-center">Status</th>
                        <th class="pb-3 pl-4 text-right">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @foreach($users as $user)
                        <tr>
                            <td class="py-3.5 pr-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-stone-900">{{ $user->name }}</div>
                                        <div class="text-[11px] text-stone-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-stone-600">{{ $user->mobile ?: '—' }}</td>
                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        <span class="rounded-full border border-stone-200 bg-stone-50 px-2 py-0.5 text-[10px] font-semibold text-stone-700">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-stone-400 text-[11px]">No roles assigned</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-800">
                                    Active
                                </span>
                            </td>
                            <td class="py-3.5 pl-4 text-right text-stone-400">{{ $user->created_at?->format('d M Y') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 border-t border-stone-100 pt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
