@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1400px] p-6 lg:p-8 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-stone-900 font-serif">Roles &amp; Permissions Matrix</h1>
            <p class="text-xs text-stone-500 mt-1">Configure role-based access control (RBAC) across dashboard modules and operational permissions.</p>
        </div>
        <button
            type="submit"
            form="roles-matrix-form"
            class="inline-flex items-center gap-2 rounded-xl bg-[#204b34] px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-[#183927] transition cursor-pointer"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Save Matrix Changes
        </button>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-800 flex items-center gap-2">
            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Roles Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($roles as $role)
            <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-[10px] font-bold text-stone-400 uppercase tracking-widest">{{ $role->code }}</span>
                        <span class="rounded-full bg-stone-100 px-2 py-0.5 text-[10px] font-medium text-stone-600">
                            {{ $role->users->count() }} Active Users
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-stone-900 mt-1">{{ $role->name }}</h3>
                    <p class="text-[11px] text-stone-500 mt-0.5 leading-snug">{{ $role->description ?: 'Operational permissions for this tier.' }}</p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                    <span>{{ $role->code === 'SUPER_ADMIN' ? 'All Privileges' : $role->permissions->count() . ' permissions active' }}</span>
                    @if($role->code !== 'SUPER_ADMIN')
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="toggleRoleColumn({{ $role->id }}, true)" class="text-[10px] text-emerald-700 font-semibold hover:underline">All</button>
                            <span class="text-stone-300">|</span>
                            <button type="button" onclick="toggleRoleColumn({{ $role->id }}, false)" class="text-[10px] text-stone-400 font-semibold hover:underline">None</button>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Interactive RBAC Matrix Form --}}
    <form id="roles-matrix-form" method="POST" action="{{ route('admin.settings.roles.sync-permissions') }}">
        @csrf
        <div class="rounded-2xl border border-stone-200 bg-white shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-stone-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-stone-900">Module Privilege Allocation</h2>
                    <p class="text-xs text-stone-400 mt-0.5">Toggle specific capabilities for each operational role. Changes apply to all users holding the role.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-stone-100 bg-stone-50/70 text-[11px] font-semibold uppercase tracking-[.06em] text-stone-500">
                            <th class="py-3 px-6 min-w-[240px]">Permission</th>
                            @foreach($roles as $role)
                                <th class="py-3 px-4 text-center min-w-[110px]">
                                    <div class="font-bold text-stone-800">{{ $role->name }}</div>
                                    <div class="font-mono text-[9px] text-stone-400 tracking-normal">{{ $role->code }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700">
                        @foreach($permissions as $perm)
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-3 px-6">
                                    <div class="font-semibold text-stone-900 text-xs">{{ $perm->name }}</div>
                                    <div class="text-[11px] text-stone-400 font-mono">{{ $perm->code }}</div>
                                    @if($perm->description)
                                        <div class="text-[10px] text-stone-400 mt-0.5">{{ $perm->description }}</div>
                                    @endif
                                </td>
                                @foreach($roles as $role)
                                    @php
                                        $hasPerm = $role->code === 'SUPER_ADMIN' || $role->permissions->contains('id', $perm->id);
                                    @endphp
                                    <td class="py-3 px-4 text-center">
                                        @if($role->code === 'SUPER_ADMIN')
                                            <input type="checkbox" checked disabled class="h-4 w-4 rounded border-stone-300 text-emerald-800 bg-emerald-50 cursor-not-allowed">
                                        @else
                                            <input
                                                type="checkbox"
                                                name="matrix[{{ $role->id }}][{{ $perm->id }}]"
                                                value="1"
                                                class="role-perm-checkbox-{{ $role->id }} h-4 w-4 rounded border-stone-300 text-[#204b34] focus:ring-[#204b34] cursor-pointer"
                                                {{ $hasPerm ? 'checked' : '' }}
                                            >
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-stone-100 bg-stone-50/50 flex items-center justify-between">
                <span class="text-xs text-stone-500">Super Admin role always retains 100% of permissions for system safety.</span>
                <button
                    type="submit"
                    class="rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#183927] transition cursor-pointer"
                >
                    Save Matrix Changes
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function toggleRoleColumn(roleId, state) {
        document.querySelectorAll(`.role-perm-checkbox-${roleId}`).forEach(cb => {
            cb.checked = state;
        });
    }
</script>
@endsection
