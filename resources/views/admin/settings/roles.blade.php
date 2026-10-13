@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1400px] p-6 lg:p-8 space-y-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-stone-900 font-serif">Roles &amp; Permissions Matrix</h1>
        <p class="text-xs text-stone-500 mt-1">Configure role-based access control (RBAC) across dashboard modules and operational permissions.</p>
    </div>

    {{-- Roles Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($roles as $role)
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-[10px] font-bold text-stone-400 uppercase tracking-widest">{{ $role->code }}</span>
                        <span class="rounded-full bg-stone-100 px-2 py-0.5 text-[10px] font-medium text-stone-600">
                            {{ $role->users->count() }} Users
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-stone-900 mt-1">{{ $role->name }}</h3>
                    <p class="text-xs text-stone-500 mt-1">{{ $role->description ?: 'Full system privileges for this role level.' }}</p>

                    <div class="mt-4 border-t border-stone-100 pt-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-2">Granted Permissions</span>
                        <div class="flex flex-wrap gap-1 max-h-32 overflow-y-auto">
                            @forelse($role->permissions as $perm)
                                <span class="rounded-md bg-stone-50 border border-stone-200 px-1.5 py-0.5 text-[10px] text-stone-700">
                                    {{ $perm->name ?? $perm->code }}
                                </span>
                            @empty
                                <span class="text-[11px] text-stone-400 italic">No specific permissions configured.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
