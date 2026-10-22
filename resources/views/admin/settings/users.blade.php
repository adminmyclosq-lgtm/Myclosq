@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1400px] p-6 lg:p-8 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-stone-900 font-serif">Users &amp; Team Management</h1>
            <p class="text-xs text-stone-500 mt-1">Manage admin team members, role assignments, and dashboard access.</p>
        </div>
        <button
            type="button"
            onclick="document.getElementById('invite-staff-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-[#204b34] px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-[#183927] transition cursor-pointer"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Invite Staff Member
        </button>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-800 flex items-center gap-2">
            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-medium text-rose-800 flex items-center gap-2">
            <svg class="h-4 w-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-medium text-rose-800">
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                        <th class="pb-3 px-4 text-center">Joined</th>
                        <th class="pb-3 pl-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @foreach($users as $user)
                        @php
                            $userRole = $user->roles->first();
                            $displayName = $user->customerProfile?->display_name ?: $user->name ?: explode('@', $user->email)[0];
                        @endphp
                        <tr>
                            <td class="py-3.5 pr-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($displayName, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-stone-900">{{ $displayName }}</div>
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
                                @if($user->status === 'active' || !$user->status)
                                    <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center text-stone-400">{{ $user->created_at?->format('d M Y') ?? '—' }}</td>
                            <td class="py-3.5 pl-4 text-right">
                                <button
                                    type="button"
                                    onclick="openEditRoleModal({{ $user->id }}, '{{ addslashes($displayName) }}', '{{ $user->email }}', {{ $userRole?->id ?? 'null' }}, '{{ $user->status ?? 'active' }}')"
                                    class="inline-flex items-center gap-1 rounded-lg border border-stone-200 bg-white px-2.5 py-1 text-[11px] font-medium text-stone-700 hover:bg-stone-50 shadow-2xs transition"
                                >
                                    <svg class="h-3 w-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Edit Role
                                </button>
                            </td>
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

{{-- 1. Invite Staff Member Modal --}}
<div id="invite-staff-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4 hidden">
    <div class="w-full max-w-md rounded-2xl border border-stone-200 bg-white p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-stone-100 pb-3">
            <h2 class="text-base font-bold text-stone-900">Invite Staff Member</h2>
            <button type="button" onclick="document.getElementById('invite-staff-modal').classList.add('hidden')" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.settings.users.store') }}" class="space-y-3.5">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" required placeholder="John" class="w-full rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs text-stone-800 outline-none focus:border-[#204b34] focus:ring-1 focus:ring-[#204b34]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" placeholder="Doe" class="w-full rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs text-stone-800 outline-none focus:border-[#204b34] focus:ring-1 focus:ring-[#204b34]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Email Address *</label>
                <input type="email" name="email" required placeholder="colleague@guidedwellness.in" class="w-full rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs text-stone-800 outline-none focus:border-[#204b34] focus:ring-1 focus:ring-[#204b34]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Mobile Number</label>
                <input type="text" name="mobile" placeholder="+91 98765 43210" class="w-full rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs text-stone-800 outline-none focus:border-[#204b34] focus:ring-1 focus:ring-[#204b34]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Temporary Password *</label>
                <input type="password" name="password" required minlength="8" placeholder="••••••••" class="w-full rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs text-stone-800 outline-none focus:border-[#204b34] focus:ring-1 focus:ring-[#204b34]">
                <p class="text-[10px] text-stone-400 mt-0.5">Minimum 8 characters. The user can change this after first sign in.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Assigned Operational Role *</label>
                <select name="role_id" required class="w-full rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs text-stone-800 outline-none focus:border-[#204b34] focus:ring-1 focus:ring-[#204b34]">
                    <option value="">Select a role...</option>
                    @foreach($availableRoles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }} ({{ $role->code }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                <button type="button" onclick="document.getElementById('invite-staff-modal').classList.add('hidden')" class="rounded-xl border border-stone-200 px-4 py-2 text-xs font-semibold text-stone-600 hover:bg-stone-50 transition">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#183927] transition">
                    Create &amp; Assign Role
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 2. Edit User Role Modal --}}
<div id="edit-role-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4 hidden">
    <div class="w-full max-w-md rounded-2xl border border-stone-200 bg-white p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-stone-100 pb-3">
            <div>
                <h2 class="text-base font-bold text-stone-900">Edit Staff Role &amp; Access</h2>
                <p id="edit-role-subheading" class="text-xs text-stone-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="document.getElementById('edit-role-modal').classList.add('hidden')" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="edit-role-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Role Assignment *</label>
                <select id="edit-role-select" name="role_id" required class="w-full rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs text-stone-800 outline-none focus:border-[#204b34] focus:ring-1 focus:ring-[#204b34]">
                    @foreach($availableRoles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }} ({{ $role->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Account Status *</label>
                <select id="edit-status-select" name="status" required class="w-full rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs text-stone-800 outline-none focus:border-[#204b34] focus:ring-1 focus:ring-[#204b34]">
                    <option value="active">Active (Access Allowed)</option>
                    <option value="inactive">Inactive (Access Suspended)</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                <button type="button" onclick="document.getElementById('edit-role-modal').classList.add('hidden')" class="rounded-xl border border-stone-200 px-4 py-2 text-xs font-semibold text-stone-600 hover:bg-stone-50 transition">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#183927] transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditRoleModal(userId, name, email, currentRoleId, currentStatus) {
        document.getElementById('edit-role-subheading').textContent = `${name} (${email})`;
        document.getElementById('edit-role-form').action = `{{ url('admin/settings/users') }}/${userId}/role`;
        if (currentRoleId) {
            document.getElementById('edit-role-select').value = currentRoleId;
        }
        document.getElementById('edit-status-select').value = currentStatus || 'active';
        document.getElementById('edit-role-modal').classList.remove('hidden');
    }
</script>
@endsection
