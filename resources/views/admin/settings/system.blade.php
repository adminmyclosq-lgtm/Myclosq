@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1400px] p-6 lg:p-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="badge">Settings</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    System Operational
                </span>
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-stone-900 font-serif mt-1">System & Server Diagnostics</h1>
            <p class="text-xs text-stone-500 mt-1">Runtime environment status, framework parameters, driver configurations, and cache controls.</p>
        </div>

        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('admin.settings.clear-cache') }}" onsubmit="return confirm('Clear compiled views, configuration, and route caches?');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-xs font-semibold text-stone-700 shadow-sm hover:bg-stone-50 transition active:scale-95">
                    <svg class="h-4 w-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Flush Application Caches
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs text-emerald-800 font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    <!-- Settings Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-stone-200 pb-3 text-xs font-medium">
        <a href="{{ route('admin.settings.users') }}" class="px-3.5 py-1.5 rounded-lg text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition">
            Staff Members
        </a>
        <a href="{{ route('admin.settings.roles') }}" class="px-3.5 py-1.5 rounded-lg text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition">
            Roles & Permissions
        </a>
        <a href="{{ route('admin.settings.system') }}" class="px-3.5 py-1.5 rounded-lg bg-stone-900 text-white font-semibold">
            System Diagnostics
        </a>
    </div>

    <!-- Quick Stats Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-4">
            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">PHP Runtime</span>
            <div class="mt-1 text-xl font-bold font-mono text-stone-900">{{ $systemInfo['php_version'] }}</div>
            <div class="mt-1 text-[11px] text-stone-500">Zend Engine</div>
        </div>
        <div class="card p-4">
            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Laravel Framework</span>
            <div class="mt-1 text-xl font-bold font-mono text-stone-900">v{{ $systemInfo['laravel_version'] }}</div>
            <div class="mt-1 text-[11px] text-stone-500">App Core</div>
        </div>
        <div class="card p-4">
            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Database Driver</span>
            <div class="mt-1 text-xl font-bold font-mono text-stone-900 uppercase">{{ $systemInfo['db_connection'] }}</div>
            <div class="mt-1 text-[11px] text-stone-500">MySQL / MariaDB</div>
        </div>
        <div class="card p-4">
            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Max Upload Limit</span>
            <div class="mt-1 text-xl font-bold font-mono text-stone-900">{{ $systemInfo['max_upload'] }}</div>
            <div class="mt-1 text-[11px] text-stone-500">Memory: {{ $systemInfo['memory_limit'] }}</div>
        </div>
    </div>

    <!-- Environment & Application Cards -->
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Application Architecture -->
        <div class="card p-6">
            <h2 class="text-sm font-semibold text-stone-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Application Architecture
            </h2>
            <dl class="space-y-3 text-xs divide-y divide-stone-100">
                <div class="flex items-center justify-between pt-2 first:pt-0">
                    <dt class="text-stone-500 font-medium">Application Name</dt>
                    <dd class="font-semibold text-stone-900">{{ $systemInfo['app_name'] }}</dd>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <dt class="text-stone-500 font-medium">Environment Mode</dt>
                    <dd>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $systemInfo['app_env'] === 'production' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $systemInfo['app_env'] }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <dt class="text-stone-500 font-medium">Debug Mode</dt>
                    <dd class="font-mono text-[11px] {{ $systemInfo['app_debug'] ? 'text-amber-600 font-bold' : 'text-stone-600' }}">
                        {{ $systemInfo['app_debug'] ? 'ENABLED (Verbose)' : 'DISABLED' }}
                    </dd>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <dt class="text-stone-500 font-medium">Application URL</dt>
                    <dd class="font-mono text-[11px] text-stone-700 truncate max-w-[240px]">{{ $systemInfo['app_url'] }}</dd>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <dt class="text-stone-500 font-medium">Timezone</dt>
                    <dd class="font-medium text-stone-800">{{ $systemInfo['timezone'] }}</dd>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <dt class="text-stone-500 font-medium">Default Currency</dt>
                    <dd class="font-medium text-stone-800">{{ $systemInfo['currency'] }}</dd>
                </div>
            </dl>
        </div>

        <!-- Infrastructure & Drivers -->
        <div class="card p-6">
            <h2 class="text-sm font-semibold text-stone-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                Infrastructure &amp; Drivers
            </h2>
            <dl class="space-y-3 text-xs divide-y divide-stone-100">
                <div class="flex items-center justify-between pt-2 first:pt-0">
                    <dt class="text-stone-500 font-medium">Cache Driver</dt>
                    <dd class="font-mono text-stone-900 font-semibold uppercase">{{ $systemInfo['cache_driver'] }}</dd>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <dt class="text-stone-500 font-medium">Queue Driver</dt>
                    <dd class="font-mono text-stone-900 font-semibold uppercase">{{ $systemInfo['queue_connection'] }}</dd>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <dt class="text-stone-500 font-medium">Session Store</dt>
                    <dd class="font-mono text-stone-900 font-semibold uppercase">{{ $systemInfo['session_driver'] }}</dd>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <dt class="text-stone-500 font-medium">Mail Driver</dt>
                    <dd class="font-mono text-stone-900 font-semibold uppercase">{{ $systemInfo['mail_mailer'] }}</dd>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <dt class="text-stone-500 font-medium">Web Server Software</dt>
                    <dd class="font-mono text-[11px] text-stone-700 truncate max-w-[240px]">{{ $systemInfo['server_software'] }}</dd>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <dt class="text-stone-500 font-medium">Memory Allocation</dt>
                    <dd class="font-mono text-[11px] text-stone-800">{{ $systemInfo['memory_limit'] }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection

