@extends('layouts.admin')

@section('content')
<style>
    #section-editor-drawer {
        width: 100% !important;
        max-width: 560px !important;
        transform: translateX(100%) !important;
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    #section-editor-drawer.open {
        transform: translateX(0) !important;
    }
</style>
<div class="h-[calc(100vh-5rem)] flex flex-col bg-stone-100 overflow-hidden select-none font-sans" id="cms-builder-app">

    <!-- TOP TOOLBAR -->
    <header class="h-14 bg-white border-b border-stone-200 px-4 sm:px-6 flex items-center justify-between shrink-0 z-30">
        <!-- Left: Breadcrumb & Title -->
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('admin.cms.index') }}" class="p-1.5 rounded-lg text-stone-500 hover:text-stone-900 hover:bg-stone-100 transition" title="Back to Pages">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="h-4 w-px bg-stone-200"></div>
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-xs uppercase tracking-wider font-semibold text-stone-400 truncate">Page</span>
                <h1 class="text-sm sm:text-base font-bold text-stone-900 truncate" id="top-bar-page-title">{{ $page->title }}</h1>
                <span id="page-status-badge" class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border {{ $page->status === 'published' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($page->status === 'draft' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-stone-100 text-stone-700 border-stone-200') }}">
                    {{ $page->status }}
                </span>
            </div>
        </div>

        <!-- Center: Device Viewport Switcher -->
        <div class="hidden md:flex items-center bg-stone-100 p-0.5 rounded-xl border border-stone-200/80 shadow-2xs">
            <button type="button" onclick="setViewport('desktop')" id="btn-vp-desktop" class="vp-btn px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition bg-white text-stone-900 shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Desktop</span>
            </button>
            <button type="button" onclick="setViewport('tablet')" id="btn-vp-tablet" class="vp-btn px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition text-stone-500 hover:text-stone-900">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Tablet</span>
            </button>
            <button type="button" onclick="setViewport('mobile')" id="btn-vp-mobile" class="vp-btn px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition text-stone-500 hover:text-stone-900">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Mobile</span>
            </button>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-2">
            <!-- Refresh Preview -->
            <button type="button" onclick="refreshPreview()" class="p-1.5 rounded-lg text-stone-500 hover:text-stone-900 hover:bg-stone-100 transition" title="Refresh Live Preview">
                <svg class="w-4 h-4" id="refresh-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>

            <!-- Page Settings & SEO Button -->
            <button type="button" onclick="openPageSettingsModal()" class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 hover:text-stone-900 transition shadow-2xs">
                <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Page SEO & Settings</span>
            </button>

            <!-- Storefront Live Link -->
            <a href="{{ url($page->slug == 'home' ? '/' : $page->slug) }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition shadow-2xs" title="Open published page in new tab">
                <span>View Live</span>
                <svg class="w-3 h-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>

            <!-- Add Section Button -->
            <button type="button" onclick="openAddSectionModal()" class="rounded-xl bg-[#204b34] px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-[#163826] transition shadow-2xs flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Section</span>
            </button>
        </div>
    </header>

    <!-- STUDIO 2-PANEL WORKSPACE -->
    <div class="flex-1 flex min-h-0 relative overflow-hidden">

        <!-- LEFT PANEL: SECTION TREE & MANAGEMENT (380px) -->
        <aside class="w-full lg:w-[410px] bg-white border-r border-stone-200 flex flex-col shrink-0 z-20 shadow-sm" id="cms-tree-panel">
            <!-- Tree Header -->
            <div class="p-4 border-b border-stone-100 flex items-center justify-between bg-stone-50/50">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-stone-500">Sections Hierarchy</span>
                    <span id="sections-count-pill" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-stone-200/80 text-stone-700">
                        {{ $page->sections->count() }}
                    </span>
                </div>
                <div class="text-[11px] text-stone-400 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                    <span>Drag to reorder</span>
                </div>
            </div>

            <!-- Scrollable Sections List -->
            <div class="flex-1 overflow-y-auto p-3 space-y-2.5" id="sections-sortable-container">
                @forelse($page->sections->sortBy('sort_order') as $section)
                    @php
                        $contentData = json_decode($section->content, true);
                        if (!is_array($contentData)) $contentData = [];
                        $settings = $contentData['settings'] ?? [];
                        $isActive = $section->is_active ?? true;
                    @endphp
                    <div class="section-tree-card group/card bg-white border border-stone-200 hover:border-stone-300 rounded-xl p-3 shadow-2xs hover:shadow-xs transition-all cursor-pointer {{ !$isActive ? 'opacity-60 bg-stone-50/60' : '' }}"
                         data-id="{{ $section->id }}"
                         data-type="{{ $section->section_type }}"
                         onclick="openSectionEditor({{ $section->id }})">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <!-- Drag Handle -->
                                <div class="drag-handle p-1 text-stone-300 group-hover/card:text-stone-500 cursor-grab active:cursor-grabbing hover:bg-stone-100 rounded-md transition" onclick="event.stopPropagation()">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                                </div>

                                <!-- Section Index & Title -->
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-stone-900 truncate">
                                            <span class="section-index-num text-stone-400">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}.</span>
                                            {{ ucwords(str_replace('_', ' ', $section->section_type)) }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-stone-400 truncate mt-0.5 section-card-subtitle">
                                        {{ $section->title ? Str::limit(strip_tags($section->title), 32) : 'Untitled Section' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Card Quick Actions -->
                            <div class="flex items-center gap-1 shrink-0" onclick="event.stopPropagation()">
                                <!-- Visibility Toggle -->
                                <button type="button" onclick="toggleSectionVisibility({{ $section->id }}, this)"
                                        class="p-1.5 rounded-lg {{ $isActive ? 'text-stone-400 hover:text-stone-700 hover:bg-stone-100' : 'text-amber-500 bg-amber-50' }} transition"
                                        title="{{ $isActive ? 'Click to hide on storefront' : 'Click to show on storefront' }}">
                                    @if($isActive)
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                    @endif
                                </button>

                                <!-- Duplicate -->
                                <button type="button" onclick="duplicateSection({{ $section->id }})" class="p-1.5 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition" title="Duplicate Section">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>

                                <!-- Delete -->
                                <button type="button" onclick="deleteSection({{ $section->id }})" class="p-1.5 rounded-lg text-stone-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete Section">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Spacing & Style Badges -->
                        <div class="mt-2.5 pt-2 border-t border-stone-100 flex items-center justify-between text-[10px] text-stone-400">
                            <div class="flex items-center gap-2">
                                <span>T: {{ $settings['top_spacing'] ?? '80px' }}</span>
                                <span>·</span>
                                <span>B: {{ $settings['bottom_spacing'] ?? '80px' }}</span>
                                @if(!empty($settings['bg_color']) && $settings['bg_color'] !== 'bg-white')
                                    <span>·</span>
                                    <span class="inline-block w-2.5 h-2.5 rounded-full border border-stone-300 {{ $settings['bg_color'] }}" title="Background Color"></span>
                                @endif
                            </div>
                            <span class="text-emerald-700 font-semibold group-hover/card:underline">Edit Content &rarr;</span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-stone-400">
                        <svg class="w-8 h-8 mx-auto text-stone-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <p class="text-xs font-semibold text-stone-600">No sections added yet</p>
                        <p class="text-[11px] text-stone-400 mt-1">Click "+ Add Section" below to start designing.</p>
                    </div>
                @endforelse
            </div>

            <!-- Add Section Bottom Button -->
            <div class="p-3 border-t border-stone-200 bg-stone-50/50">
                <button type="button" onclick="openAddSectionModal()" class="w-full py-2.5 rounded-xl border border-dashed border-stone-300 hover:border-emerald-600 hover:bg-emerald-50/40 text-stone-700 hover:text-emerald-900 text-xs font-semibold flex items-center justify-center gap-2 transition">
                    <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add New Section</span>
                </button>
            </div>
        </aside>

        <!-- RIGHT PANEL: LIVE INTERACTIVE PREVIEW CANVAS -->
        <main class="flex-1 bg-stone-200/70 overflow-hidden flex flex-col items-center justify-center p-2 sm:p-4 relative">
            <!-- Viewport Canvas Container -->
            <div id="preview-viewport-wrapper" class="w-full h-full max-w-full bg-white rounded-xl shadow-lg border border-stone-300/80 overflow-hidden transition-all duration-300 flex flex-col relative">
                <!-- Preview Canvas Frame -->
                <iframe id="live-preview-iframe"
                        src="{{ route('admin.cms.preview', $page) }}"
                        class="w-full h-full border-0 bg-white"
                        title="Live Storefront Studio Preview"></iframe>
            </div>
        </main>

    </div>
</div>

<!-- ========================================== -->
<!-- SLIDE-OVER DRAWER: SECTION EDITOR          -->
<!-- ========================================== -->
<div id="section-editor-drawer" class="fixed inset-y-0 right-0 z-50 w-full sm:w-[540px] bg-white shadow-2xl border-l border-stone-200 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    <!-- Drawer Header -->
    <div class="h-14 px-6 border-b border-stone-200 flex items-center justify-between bg-stone-50/70 shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
            <h3 class="text-sm font-bold text-stone-900" id="drawer-section-title">Edit Section</h3>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-stone-200 text-stone-700 uppercase" id="drawer-section-type">hero</span>
        </div>
        <button type="button" onclick="closeSectionEditor()" class="p-1.5 text-stone-400 hover:text-stone-700 hover:bg-stone-100 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Drawer Tabs -->
    <div class="flex border-b border-stone-200 px-6 bg-white shrink-0">
        <button type="button" onclick="setDrawerTab('content')" id="tab-btn-content" class="px-4 py-2.5 text-xs font-semibold text-emerald-800 border-b-2 border-emerald-800 transition">
            Content Details
        </button>
        <button type="button" onclick="setDrawerTab('design')" id="tab-btn-design" class="px-4 py-2.5 text-xs font-semibold text-stone-400 hover:text-stone-700 border-b-2 border-transparent transition">
            Design & Spacing
        </button>
    </div>

    <!-- Drawer Form -->
    <form id="drawer-section-form" onsubmit="handleSectionFormSubmit(event)" class="flex-1 flex flex-col min-h-0">
        <input type="hidden" name="section_id" id="edit-section-id">
        <input type="hidden" name="section_type" id="edit-section-type">

        <!-- Scrollable Form Body -->
        <div class="flex-1 overflow-y-auto p-6 space-y-5">
            <!-- TAB 1: CONTENT TAB -->
            <div id="drawer-tab-content" class="space-y-5">
            <!-- Dynamic Form Fields will be inserted via JS for clean modularity -->
            <div id="dynamic-content-fields" class="space-y-4">
                <!-- Injected via openSectionEditor() -->
            </div>

            <!-- Media / Image Picker Section -->
            <div class="pt-4 border-t border-stone-100" id="drawer-media-wrapper">
                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block mb-2">Featured Image / Media</label>
                <div class="flex items-start gap-4 p-3 bg-stone-50 border border-stone-200 rounded-xl">
                    <div id="drawer-media-preview-box" class="w-20 h-20 rounded-lg border border-stone-300 bg-white overflow-hidden shrink-0 flex items-center justify-center">
                        <span class="text-[10px] text-stone-400">No Image</span>
                    </div>
                    <div class="flex-1 space-y-2">
                        <input type="hidden" name="media_id" id="edit-media-id">
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openMediaPickerModal()" class="px-3 py-1.5 rounded-lg bg-stone-900 text-white text-xs font-semibold hover:bg-stone-800 transition shadow-2xs">
                                Choose from Media Library
                            </button>
                            <button type="button" onclick="clearSelectedMedia()" id="btn-clear-media" class="px-2.5 py-1.5 rounded-lg border border-stone-300 text-stone-600 text-xs font-medium hover:bg-stone-100 transition hidden">
                                Remove
                            </button>
                        </div>
                        <p class="text-[11px] text-stone-400">Select an existing image from the media library or upload a new one.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: DESIGN & SPACING TAB -->
        <div id="drawer-tab-design" class="space-y-6 hidden">
            <!-- Section Spacing -->
            <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/90 space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-stone-800">Section Spacing (Padding)</h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1">Top</label>
                        <select name="content[settings][top_spacing]" id="edit-top-spacing" class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white">
                            <option value="0px">0px (None)</option>
                            <option value="20px">20px</option>
                            <option value="40px">40px</option>
                            <option value="60px">60px</option>
                            <option value="80px" selected>80px (Std)</option>
                            <option value="100px">100px</option>
                            <option value="120px">120px</option>
                            <option value="160px">160px</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1">Bottom</label>
                        <select name="content[settings][bottom_spacing]" id="edit-bottom-spacing" class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white">
                            <option value="0px">0px (None)</option>
                            <option value="20px">20px</option>
                            <option value="40px">40px</option>
                            <option value="60px">60px</option>
                            <option value="80px" selected>80px (Std)</option>
                            <option value="100px">100px</option>
                            <option value="120px">120px</option>
                            <option value="160px">160px</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1">Left</label>
                        <select name="content[settings][left_spacing]" id="edit-left-spacing" class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white">
                            <option value="0px" selected>0px (None)</option>
                            <option value="16px">16px</option>
                            <option value="24px">24px</option>
                            <option value="32px">32px</option>
                            <option value="48px">48px</option>
                            <option value="64px">64px</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1">Right</label>
                        <select name="content[settings][right_spacing]" id="edit-right-spacing" class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white">
                            <option value="0px" selected>0px (None)</option>
                            <option value="16px">16px</option>
                            <option value="24px">24px</option>
                            <option value="32px">32px</option>
                            <option value="48px">48px</option>
                            <option value="64px">64px</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Background & Layout -->
            <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/90 space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-stone-800">Background & Layout</h4>
                <div class="space-y-3">
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1.5">Background Tone Preset</label>
                        <select name="content[settings][bg_color]" id="edit-bg-color" class="w-full rounded-lg border border-stone-300 px-3 py-1.5 text-xs bg-white">
                            <option value="">Default / Custom Color</option>
                            <option value="bg-white">White (#FFFFFF)</option>
                            <option value="bg-[var(--cream)]">Guided Cream (#F5F1E8)</option>
                            <option value="bg-[var(--sand)]">Guided Sand (#E5DDCD)</option>
                            <option value="bg-[var(--ink)] text-white">Guided Ink Dark (#254536)</option>
                            <option value="bg-stone-50">Light Stone (#F9FAFB)</option>
                            <option value="bg-stone-900 text-white">Stone 900 Dark</option>
                        </select>
                    </div>

                    <!-- Custom Section BG Colour Picker -->
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1.5">Custom Background Colour</label>
                        <div class="flex items-center gap-2 mb-2">
                            <input type="color" id="edit-bg-custom-color" value="#ffffff"
                                   oninput="syncColorField('bg', this.value)"
                                   class="w-10 h-8 rounded-lg border border-stone-300 cursor-pointer p-0.5 bg-white">
                            <input type="text" name="content[settings][bg_custom_color]" id="edit-bg-custom-hex"
                                   placeholder="#ffffff" maxlength="7"
                                   oninput="syncHexField('bg', this.value)"
                                   class="w-28 rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-mono">
                            <button type="button" onclick="clearColorField('bg')" class="text-xs text-stone-400 hover:text-stone-700 px-2 py-1 rounded hover:bg-stone-200 transition">
                                Reset
                            </button>
                        </div>
                        <!-- Brand Swatches -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[10px] text-stone-400 font-medium mr-1">Brand swatches:</span>
                            <button type="button" onclick="applyColorSwatch('bg', '#204b34')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition" style="background:#204b34" title="Forest Green #204b34"></button>
                            <button type="button" onclick="applyColorSwatch('bg', '#254536')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition" style="background:#254536" title="Guided Ink #254536"></button>
                            <button type="button" onclick="applyColorSwatch('bg', '#F5F1E8')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition" style="background:#F5F1E8" title="Guided Cream #F5F1E8"></button>
                            <button type="button" onclick="applyColorSwatch('bg', '#E5DDCD')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition" style="background:#E5DDCD" title="Guided Sand #E5DDCD"></button>
                            <button type="button" onclick="applyColorSwatch('bg', '#FFFFFF')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition bg-white" title="Pure White #FFFFFF"></button>
                            <button type="button" onclick="applyColorSwatch('bg', '#1C1917')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition" style="background:#1C1917" title="Ink Black #1C1917"></button>
                            <button type="button" onclick="applyColorSwatch('bg', '#F9FAFB')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition" style="background:#F9FAFB" title="Light Stone #F9FAFB"></button>
                            <button type="button" onclick="applyColorSwatch('bg', '#44403C')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition" style="background:#44403C" title="Stone Dark #44403C"></button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="text-[11px] font-semibold text-stone-600 block mb-1">Container Width</label>
                            <select name="content[settings][container_width]" id="edit-container-width" class="w-full rounded-lg border border-stone-300 px-3 py-1.5 text-xs bg-white">
                                <option value="standard" selected>Standard (1200px)</option>
                                <option value="wide">Wide (1400px)</option>
                                <option value="full">Full Width</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-stone-600 block mb-1">Text Alignment</label>
                            <select name="content[settings][alignment]" id="edit-alignment" class="w-full rounded-lg border border-stone-300 px-3 py-1.5 text-xs bg-white">
                                <option value="left" selected>Left</option>
                                <option value="center">Center</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1">Custom Section HTML Anchor ID</label>
                        <input type="text" name="content[settings][anchor_id]" id="edit-anchor-id" placeholder="e.g. standards, fit, faqs" class="w-full rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-mono">
                        <span class="text-[10px] text-stone-400 mt-1 block">Used for direct link navigation (e.g. /#standards)</span>
                    </div>
                </div>
            </div>

            <!-- Typography Controls -->
            <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/90 space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-stone-800">Typography</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1">Font Family</label>
                        <select name="content[settings][font_family]" id="edit-font-family" class="w-full rounded-lg border border-stone-300 px-3 py-1.5 text-xs bg-white">
                            <option value="">Default (Inherit)</option>
                            <option value="font-serif">Serif (Playfair / Georgia)</option>
                            <option value="font-sans">Sans-serif (Inter)</option>
                            <option value="font-mono">Monospace</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1">Heading Size</label>
                        <select name="content[settings][font_size]" id="edit-font-size" class="w-full rounded-lg border border-stone-300 px-3 py-1.5 text-xs bg-white">
                            <option value="">Default</option>
                            <option value="text-2xl">Small (2xl)</option>
                            <option value="text-3xl">Medium (3xl)</option>
                            <option value="text-4xl">Large (4xl)</option>
                            <option value="text-5xl">X-Large (5xl)</option>
                            <option value="text-6xl">2X-Large (6xl)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1">Font Weight</label>
                        <select name="content[settings][font_weight]" id="edit-font-weight" class="w-full rounded-lg border border-stone-300 px-3 py-1.5 text-xs bg-white">
                            <option value="">Default</option>
                            <option value="font-normal">Normal (400)</option>
                            <option value="font-medium">Medium (500)</option>
                            <option value="font-semibold">Semibold (600)</option>
                            <option value="font-bold">Bold (700)</option>
                            <option value="font-extrabold">Extrabold (800)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1">Text Colour Preset</label>
                        <select name="content[settings][text_color]" id="edit-text-color" class="w-full rounded-lg border border-stone-300 px-3 py-1.5 text-xs bg-white">
                            <option value="">Default (Inherit)</option>
                            <option value="text-stone-900">Ink Black (#1C1917)</option>
                            <option value="text-stone-700">Stone Dark (#44403C)</option>
                            <option value="text-stone-500">Stone Mid (#78716C)</option>
                            <option value="text-white">White (#FFFFFF)</option>
                            <option value="text-[#204b34]">Brand Green (#204b34)</option>
                            <option value="text-[#254536]">Brand Ink (#254536)</option>
                            <option value="text-emerald-700">Emerald (700)</option>
                        </select>
                    </div>
                </div>

                <!-- Custom Text Colour Picker -->
                <div>
                    <label class="text-[11px] font-semibold text-stone-600 block mb-1.5">Custom Font / Text Colour</label>
                    <div class="flex items-center gap-2 mb-2">
                        <input type="color" id="edit-text-custom-color" value="#1c1917"
                               oninput="syncColorField('text', this.value)"
                               class="w-10 h-8 rounded-lg border border-stone-300 cursor-pointer p-0.5 bg-white">
                        <input type="text" name="content[settings][text_custom_color]" id="edit-text-custom-hex"
                               placeholder="#1c1917" maxlength="7"
                               oninput="syncHexField('text', this.value)"
                               class="w-28 rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-mono">
                        <button type="button" onclick="clearColorField('text')" class="text-xs text-stone-400 hover:text-stone-700 px-2 py-1 rounded hover:bg-stone-200 transition">
                            Reset
                        </button>
                    </div>
                    <!-- Brand Swatches -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[10px] text-stone-400 font-medium mr-1">Brand swatches:</span>
                        <button type="button" onclick="applyColorSwatch('text', '#1C1917')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition" style="background:#1C1917" title="Ink Black #1C1917"></button>
                        <button type="button" onclick="applyColorSwatch('text', '#44403C')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition" style="background:#44403C" title="Stone Dark #44403C"></button>
                        <button type="button" onclick="applyColorSwatch('text', '#204b34')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition" style="background:#204b34" title="Forest Green #204b34"></button>
                        <button type="button" onclick="applyColorSwatch('text', '#254536')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition" style="background:#254536" title="Brand Ink #254536"></button>
                        <button type="button" onclick="applyColorSwatch('text', '#FFFFFF')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition bg-white" title="Pure White #FFFFFF"></button>
                        <button type="button" onclick="applyColorSwatch('text', '#047857')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition bg-emerald-700" title="Emerald #047857"></button>
                        <button type="button" onclick="applyColorSwatch('text', '#B45309')" class="w-5 h-5 rounded-full border border-stone-300 shadow-2xs hover:scale-110 transition bg-amber-700" title="Amber #B45309"></button>
                    </div>
                </div>
            </div>

            <!-- Section Button Styling Controls -->
            <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/90 space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-stone-800">Button Global Styling</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1">Button Font</label>
                        <select name="content[settings][button_font]" id="edit-button-font" class="w-full rounded-lg border border-stone-300 px-3 py-1.5 text-xs bg-white">
                            <option value="">Default (Inherit)</option>
                            <option value="font-serif">Serif</option>
                            <option value="font-sans">Sans-serif</option>
                            <option value="font-mono">Monospace</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1">Button Border Radius</label>
                        <select name="content[settings][button_border_radius]" id="edit-button-radius" class="w-full rounded-lg border border-stone-300 px-3 py-1.5 text-xs bg-white">
                            <option value="0px">Square (0px)</option>
                            <option value="4px">Subtle (4px)</option>
                            <option value="8px">Rounded (8px)</option>
                            <option value="12px" selected>Standard (12px)</option>
                            <option value="9999px">Pill (Full)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1.5">Button Default BG Colour</label>
                        <div class="flex items-center gap-2 mb-1.5">
                            <input type="color" id="edit-button-bg-color" value="#204b34"
                                   oninput="syncColorField('button-bg', this.value)"
                                   class="w-10 h-8 rounded-lg border border-stone-300 cursor-pointer p-0.5 bg-white">
                            <input type="text" name="content[settings][button_bg_color]" id="edit-button-bg-hex"
                                   placeholder="#204b34" maxlength="7"
                                   oninput="syncHexField('button-bg', this.value)"
                                   class="w-24 rounded-lg border border-stone-300 px-2 py-1.5 text-xs font-mono">
                            <button type="button" onclick="clearColorField('button-bg')" class="text-[10px] text-stone-400 hover:text-stone-700">Clear</button>
                        </div>
                        <div class="flex items-center gap-1 flex-wrap">
                            <button type="button" onclick="applyColorSwatch('button-bg', '#204b34')" class="w-4 h-4 rounded-full border border-stone-300" style="background:#204b34" title="#204b34"></button>
                            <button type="button" onclick="applyColorSwatch('button-bg', '#254536')" class="w-4 h-4 rounded-full border border-stone-300" style="background:#254536" title="#254536"></button>
                            <button type="button" onclick="applyColorSwatch('button-bg', '#FFFFFF')" class="w-4 h-4 rounded-full border border-stone-300 bg-white" title="#FFFFFF"></button>
                            <button type="button" onclick="applyColorSwatch('button-bg', '#1C1917')" class="w-4 h-4 rounded-full border border-stone-300" style="background:#1C1917" title="#1C1917"></button>
                            <button type="button" onclick="applyColorSwatch('button-bg', '#047857')" class="w-4 h-4 rounded-full border border-stone-300 bg-emerald-700" title="#047857"></button>
                        </div>
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-stone-600 block mb-1.5">Button Default Text Colour</label>
                        <div class="flex items-center gap-2 mb-1.5">
                            <input type="color" id="edit-button-text-color" value="#ffffff"
                                   oninput="syncColorField('button-text', this.value)"
                                   class="w-10 h-8 rounded-lg border border-stone-300 cursor-pointer p-0.5 bg-white">
                            <input type="text" name="content[settings][button_text_color]" id="edit-button-text-hex"
                                   placeholder="#ffffff" maxlength="7"
                                   oninput="syncHexField('button-text', this.value)"
                                   class="w-24 rounded-lg border border-stone-300 px-2 py-1.5 text-xs font-mono">
                            <button type="button" onclick="clearColorField('button-text')" class="text-[10px] text-stone-400 hover:text-stone-700">Clear</button>
                        </div>
                        <div class="flex items-center gap-1 flex-wrap">
                            <button type="button" onclick="applyColorSwatch('button-text', '#FFFFFF')" class="w-4 h-4 rounded-full border border-stone-300 bg-white" title="#FFFFFF"></button>
                            <button type="button" onclick="applyColorSwatch('button-text', '#1C1917')" class="w-4 h-4 rounded-full border border-stone-300" style="background:#1C1917" title="#1C1917"></button>
                            <button type="button" onclick="applyColorSwatch('button-text', '#204b34')" class="w-4 h-4 rounded-full border border-stone-300" style="background:#204b34" title="#204b34"></button>
                            <button type="button" onclick="applyColorSwatch('button-text', '#F5F1E8')" class="w-4 h-4 rounded-full border border-stone-300" style="background:#F5F1E8" title="#F5F1E8"></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Device Visibility -->
            <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/90 space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-stone-800">Responsive Visibility</h4>
                <div class="space-y-2">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="hidden" name="content[settings][hidden_desktop]" value="0">
                        <input type="checkbox" name="content[settings][hidden_desktop]" id="edit-hidden-desktop" value="1" class="rounded border-stone-300 text-[#204b34] focus:ring-0">
                        <span class="text-xs font-medium text-stone-700">Hide on Desktop viewports (&ge;1024px)</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="hidden" name="content[settings][hidden_tablet]" value="0">
                        <input type="checkbox" name="content[settings][hidden_tablet]" id="edit-hidden-tablet" value="1" class="rounded border-stone-300 text-[#204b34] focus:ring-0">
                        <span class="text-xs font-medium text-stone-700">Hide on Tablet viewports (768px - 1023px)</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="hidden" name="content[settings][hidden_mobile]" value="0">
                        <input type="checkbox" name="content[settings][hidden_mobile]" id="edit-hidden-mobile" value="1" class="rounded border-stone-300 text-[#204b34] focus:ring-0">
                        <span class="text-xs font-medium text-stone-700">Hide on Mobile viewports (&lt;768px)</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Sticky Drawer Footer Buttons -->
        <div class="px-6 py-4 bg-stone-50 border-t border-stone-200 flex items-center justify-between shrink-0 shadow-[0_-4px_12px_rgba(0,0,0,0.03)]">
            <button type="button" onclick="closeSectionEditor()" class="px-4 py-2 rounded-xl border border-stone-300 text-stone-600 hover:bg-stone-100 text-xs font-semibold transition">
                Cancel
            </button>
            <button type="submit" id="btn-save-drawer-section" class="px-6 py-2.5 rounded-xl bg-[#204b34] hover:bg-[#163826] text-white text-xs font-bold transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                <span>Save Section</span>
            </button>
        </div>
    </form>
</div>

<!-- ========================================== -->
<!-- MODAL: ADD SECTION PRESET CATALOG          -->
<!-- ========================================== -->
<dialog id="modal-add-section" class="backdrop:bg-stone-900/60 m-auto p-0 shadow-2xl rounded-2xl w-full max-w-3xl bg-white border border-stone-200 overflow-hidden">
    <div class="p-5 border-b border-stone-200 flex items-center justify-between bg-stone-50/70">
        <div>
            <h3 class="text-base font-bold text-stone-900">Add Section to Page</h3>
            <p class="text-xs text-stone-500 mt-0.5">Select a component to insert into your Guided Wellness layout.</p>
        </div>
        <button type="button" onclick="document.getElementById('modal-add-section').close()" class="p-1.5 text-stone-400 hover:text-stone-700 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="p-6 max-h-[70vh] overflow-y-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
        @php
            $sectionTypes = [
                ['type' => 'hero', 'name' => 'Hero Banner', 'desc' => 'High-impact headline, product shot, badges and dual CTAs.'],
                ['type' => 'problem', 'name' => 'Problem Statement', 'desc' => 'Side-by-side pain points with clean indicator bullets.'],
                ['type' => 'features', 'name' => 'Feature Cards', 'desc' => 'Three columnar cards highlighting key product features.'],
                ['type' => 'standards', 'name' => 'Clinical Standards', 'desc' => 'Transparency and formulation methodology cards.'],
                ['type' => 'showcase', 'name' => 'Product Showcase', 'desc' => 'Package contents checklist with companion imagery.'],
                ['type' => 'testimonials', 'name' => 'Response Brief / Reviews', 'desc' => 'User outcome summary and Day 30 brief presentation.'],
                ['type' => 'feature_cards', 'name' => 'Check Your Fit', 'desc' => 'Two-column suitability and medical caution checklist.'],
                ['type' => 'faq', 'name' => 'FAQ Accordion / Grid', 'desc' => 'Answers to common customer pre-purchase questions.'],
                ['type' => 'cta', 'name' => 'Call To Action Banner', 'desc' => 'Dark forest gradient trial banner with shop button.'],
                ['type' => 'learn', 'name' => 'Articles / Learn Grid', 'desc' => 'Educational cards linking to deeper wellness guides.'],
                ['type' => 'statistics', 'name' => 'Statistics / Metrics', 'desc' => 'Four numerical metric callouts with unit labels.'],
                ['type' => 'video', 'name' => 'Video Embed', 'desc' => 'Responsive YouTube, Vimeo or MP4 video container.'],
                ['type' => 'gallery', 'name' => 'Image Gallery', 'desc' => 'Clean 4-column responsive product image grid.'],
                ['type' => 'spacer', 'name' => 'Spacer / Divider', 'desc' => 'Vertical spacing divider with optional hairline border.'],
                ['type' => 'custom_html', 'name' => 'Custom HTML / Embed', 'desc' => 'Custom markup for third-party scripts or special widgets.'],
            ];
        @endphp
        @foreach($sectionTypes as $st)
            <button type="button" onclick="confirmAddSection('{{ $st['type'] }}')" class="p-4 rounded-xl border border-stone-200 hover:border-emerald-600 hover:bg-emerald-50/30 text-left transition group">
                <span class="text-xs font-bold text-stone-900 group-hover:text-emerald-900 block">{{ $st['name'] }}</span>
                <p class="text-[11px] text-stone-500 mt-1.5 leading-relaxed">{{ $st['desc'] }}</p>
                <span class="mt-3 inline-flex items-center text-[10px] font-bold uppercase tracking-wider text-emerald-800">Select &rarr;</span>
            </button>
        @endforeach
    </div>
</dialog>

<!-- ========================================== -->
<!-- MODAL: PAGE SETTINGS & SEO SUITE           -->
<!-- ========================================== -->
<dialog id="modal-page-settings" class="backdrop:bg-stone-900/60 m-auto p-0 shadow-2xl rounded-2xl w-full max-w-2xl bg-white border border-stone-200 overflow-hidden">
    <div class="p-5 border-b border-stone-200 flex items-center justify-between bg-stone-50/70">
        <div>
            <h3 class="text-base font-bold text-stone-900">Page Settings & SEO Suite</h3>
            <p class="text-xs text-stone-500 mt-0.5">Configure URL slug, publishing schedule, and search engine optimization.</p>
        </div>
        <button type="button" onclick="document.getElementById('modal-page-settings').close()" class="p-1.5 text-stone-400 hover:text-stone-700 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="form-page-settings" onsubmit="handlePageSettingsSubmit(event)" class="p-6 max-h-[75vh] overflow-y-auto space-y-5">
        <!-- Page Title & Slug -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-stone-700 block mb-1">Page Title</label>
                <input type="text" name="title" id="page-title-input" value="{{ $page->title }}" required class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-[#204b34] focus:outline-none">
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-stone-700 block mb-1">URL Slug</label>
                <input type="text" name="slug" id="page-slug-input" value="{{ $page->slug }}" {{ $page->slug === 'home' ? 'readonly' : '' }} required class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm font-mono {{ $page->slug === 'home' ? 'bg-stone-100 text-stone-500' : '' }} focus:border-[#204b34] focus:outline-none">
                <span class="text-[10px] text-stone-400 mt-1 block">{{ $page->slug === 'home' ? 'Root Homepage (slug cannot be renamed)' : 'Route: /'.($page->slug) }}</span>
            </div>
        </div>

        <!-- Publishing Status & Schedule -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-stone-100">
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-stone-700 block mb-1">Publishing Status</label>
                <select name="status" id="page-status-input" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm bg-white">
                    <option value="draft" @selected($page->status === 'draft')>Draft (Hidden from customers)</option>
                    <option value="published" @selected($page->status === 'published')>Published (Live on storefront)</option>
                    <option value="archived" @selected($page->status === 'archived')>Archived (Deactivated)</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-stone-700 block mb-1">Published At Date</label>
                <input type="datetime-local" name="published_at" id="page-published-at-input" value="{{ optional($page->published_at)->format('Y-m-d\TH:i') }}" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            </div>
        </div>

        <!-- SEO Section -->
        <div class="pt-4 border-t border-stone-100 space-y-4">
            <div class="flex items-center justify-between">
                <label class="text-xs font-bold uppercase tracking-wider text-stone-800">Search Engine Optimization (SEO)</label>
                <span class="text-[10px] text-emerald-800 font-semibold bg-emerald-50 px-2 py-0.5 rounded">Google Snippet Preview</span>
            </div>

            <!-- Meta Title with Counter -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold text-stone-700">Meta Title</span>
                    <span class="text-[10px] text-stone-400" id="meta-title-counter">0 / 60 chars</span>
                </div>
                <input type="text" name="meta_title" id="page-meta-title-input" value="{{ $page->meta_title }}" oninput="updateSeoPreview()" placeholder="E.g. Guided 30-Day Gut Reset | Guided Wellness" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            </div>

            <!-- Meta Description with Counter -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold text-stone-700">Meta Description</span>
                    <span class="text-[10px] text-stone-400" id="meta-desc-counter">0 / 160 chars</span>
                </div>
                <textarea name="meta_description" id="page-meta-desc-input" rows="3" oninput="updateSeoPreview()" placeholder="A 30-day guided gut-support capsule course with traceable ingredients..." class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">{{ $page->meta_description }}</textarea>
            </div>

            <!-- Live Google SERP Snippet Preview Box -->
            <div class="bg-stone-50 p-4 rounded-xl border border-stone-200">
                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-400 mb-2">Google Search Snippet Preview</div>
                <div class="font-sans text-left">
                    <div class="text-xs text-stone-600 flex items-center gap-1.5">
                        <span class="inline-block w-4 h-4 rounded-full bg-emerald-900 text-[10px] text-white text-center font-bold">G</span>
                        <span class="truncate">{{ url('/') }} &rsaquo; {{ $page->slug === 'home' ? '' : $page->slug }}</span>
                    </div>
                    <div class="text-base text-[#1a0dab] hover:underline font-medium mt-1 cursor-pointer truncate" id="serp-title-preview">
                        {{ $page->meta_title ?: $page->title . ' | Guided Wellness' }}
                    </div>
                    <div class="text-xs text-stone-600 mt-1 line-clamp-2 leading-relaxed" id="serp-desc-preview">
                        {{ $page->meta_description ?: 'Discover Guided Wellness 30-Day Gut Reset capsule course with personal response brief.' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-stone-200 flex items-center justify-end gap-3">
            <button type="button" onclick="document.getElementById('modal-page-settings').close()" class="px-4 py-2 rounded-xl border border-stone-300 text-stone-600 text-xs font-semibold hover:bg-stone-100 transition">
                Close
            </button>
            <button type="submit" id="btn-save-page-settings" class="px-5 py-2 rounded-xl bg-[#204b34] text-white text-xs font-semibold hover:bg-[#163826] transition shadow-2xs">
                Save Page Details
            </button>
        </div>
    </form>
</dialog>

<!-- ========================================== -->
<!-- MODAL: MEDIA LIBRARY PICKER                -->
<!-- ========================================== -->
<dialog id="modal-media-picker" class="backdrop:bg-stone-900/60 m-auto p-0 shadow-2xl rounded-2xl w-full max-w-4xl bg-white border border-stone-200 overflow-hidden">
    <div class="p-5 border-b border-stone-200 flex items-center justify-between bg-stone-50/70">
        <div>
            <h3 class="text-base font-bold text-stone-900">Media Library</h3>
            <p class="text-xs text-stone-500 mt-0.5">Select an existing image or upload new media for this section.</p>
        </div>
        <button type="button" onclick="document.getElementById('modal-media-picker').close()" class="p-1.5 text-stone-400 hover:text-stone-700 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Media Controls (Search & Instant Upload) -->
    <div class="p-4 border-b border-stone-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white">
        <div class="relative w-full sm:w-72">
            <input type="text" id="media-search-input" oninput="debounceMediaSearch()" placeholder="Search media files..." class="w-full rounded-xl border border-stone-200 pl-8 pr-3 py-1.5 text-xs text-stone-800 focus:outline-none focus:border-[#204b34]">
            <svg class="w-4 h-4 text-stone-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <label class="cursor-pointer px-4 py-1.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold flex items-center gap-1.5 transition shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Upload New Image</span>
                <input type="file" accept="image/*" class="hidden" onchange="uploadNewMediaFile(this)">
            </label>
        </div>
    </div>

    <!-- Media Thumbnails Grid -->
    <div class="p-5 max-h-[55vh] overflow-y-auto">
        <div id="media-grid-container" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
            @foreach($recentMedia as $m)
                @php
                    $url = $m->storage_path ? asset('storage/'.$m->storage_path) : $m->url;
                @endphp
                <div class="media-pick-item group relative aspect-square rounded-xl border-2 border-transparent hover:border-emerald-600 bg-stone-100 overflow-hidden cursor-pointer shadow-2xs"
                     data-id="{{ $m->id }}"
                     data-url="{{ $url }}"
                     data-name="{{ $m->file_name }}"
                     onclick="selectMediaItem({{ $m->id }}, '{{ $url }}', '{{ $m->file_name }}')">
                    <img src="{{ $url }}" alt="{{ $m->file_name }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center p-2 text-center">
                        <span class="text-[10px] text-white font-medium truncate">{{ $m->file_name }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        <div id="media-empty-state" class="hidden py-12 text-center text-stone-400">
            No media files found matching your search.
        </div>
    </div>

    <div class="p-4 border-t border-stone-200 bg-stone-50 flex items-center justify-between">
        <span id="selected-media-info" class="text-xs text-stone-500 font-medium">Click an image to select it.</span>
        <button type="button" onclick="document.getElementById('modal-media-picker').close()" class="px-4 py-1.5 rounded-xl bg-[#204b34] text-white text-xs font-semibold hover:bg-[#163826] transition">
            Done
        </button>
    </div>
</dialog>

<!-- TOAST CONTAINER -->
<div id="cms-toast" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none bg-stone-900 text-white px-4 py-2.5 rounded-xl shadow-xl flex items-center gap-2 text-xs font-medium">
    <span id="cms-toast-msg">Saved successfully</span>
</div>

<!-- SCRIPTS & INTERACTION ENGINE -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    // Global routes configuration for AJAX requests (works seamlessly on Apache subdirectory or standalone host)
    const CMS_ROUTES = {
        previewUrl: @json(route('admin.cms.preview', $page)),
        sectionBaseUrl: @json(url('admin/cms/sections')),
        mediaPickerUrl: @json(route('admin.cms.media.picker')),
    };

    // In-memory cache of page sections
    const pageData = @json($page);
    let currentSections = @json($page->sections);

    // Initial load
    document.addEventListener("DOMContentLoaded", function() {
        initSortable();
        updateSeoPreview();
    });

    // 1. Sortable Drag-and-drop
    function initSortable() {
        const container = document.getElementById('sections-sortable-container');
        if (!container) return;

        new Sortable(container, {
            animation: 200,
            handle: '.drag-handle',
            ghostClass: 'bg-emerald-50',
            onEnd: function () {
                let order = [];
                container.querySelectorAll('.section-tree-card').forEach(function(el) {
                    order.push(el.getAttribute('data-id'));
                });

                // Renumber visually
                container.querySelectorAll('.section-tree-card').forEach(function(el, idx) {
                    const badge = el.querySelector('.section-index-num');
                    if (badge) badge.innerText = String(idx + 1).padStart(2, '0') + '.';
                });

                // Persist order via AJAX
                fetch("{{ route('admin.cms.sections.reorder', $page) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ order: order })
                }).then(res => res.json()).then(data => {
                    showToast("Order updated");
                    refreshPreview();
                });
            }
        });
    }

    // 2. Viewport Switcher
    function setViewport(device) {
        const wrapper = document.getElementById('preview-viewport-wrapper');
        document.querySelectorAll('.vp-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-stone-900', 'shadow-2xs');
            btn.classList.add('text-stone-500');
        });

        const activeBtn = document.getElementById('btn-vp-' + device);
        if (activeBtn) {
            activeBtn.classList.add('bg-white', 'text-stone-900', 'shadow-2xs');
            activeBtn.classList.remove('text-stone-500');
        }

        if (device === 'desktop') {
            wrapper.style.maxWidth = '100%';
            wrapper.style.width = '100%';
        } else if (device === 'tablet') {
            wrapper.style.maxWidth = '768px';
            wrapper.style.width = '768px';
        } else if (device === 'mobile') {
            wrapper.style.maxWidth = '375px';
            wrapper.style.width = '375px';
        }
    }

    // 3. Refresh Preview Iframe
    function refreshPreview() {
        const icon = document.getElementById('refresh-icon');
        if (icon) icon.classList.add('animate-spin');
        const iframe = document.getElementById('live-preview-iframe');
        if (iframe) {
            try {
                const currentSrc = iframe.src || CMS_ROUTES.previewUrl;
                const url = new URL(currentSrc, window.location.href);
                url.searchParams.set('_v', Date.now());
                iframe.src = url.toString();
            } catch(e) {
                iframe.src = CMS_ROUTES.previewUrl + (CMS_ROUTES.previewUrl.includes('?') ? '&' : '?') + '_v=' + Date.now();
            }
        }
        setTimeout(() => {
            if (icon) icon.classList.remove('animate-spin');
        }, 800);
    }

    // 4. Toggle Section Visibility
    function toggleSectionVisibility(sectionId, btn) {
        fetch(`${CMS_ROUTES.sectionBaseUrl}/${sectionId}/toggle-active`, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        }).then(res => res.json()).then(data => {
            if (data.success) {
                showToast(data.message);
                const card = btn.closest('.section-tree-card');
                if (data.is_active) {
                    card.classList.remove('opacity-60', 'bg-stone-50/60');
                    btn.classList.remove('text-amber-500', 'bg-amber-50');
                    btn.classList.add('text-stone-400');
                    btn.title = "Click to hide on storefront";
                } else {
                    card.classList.add('opacity-60', 'bg-stone-50/60');
                    btn.classList.add('text-amber-500', 'bg-amber-50');
                    btn.classList.remove('text-stone-400');
                    btn.title = "Click to show on storefront";
                }
                refreshPreview();
            }
        });
    }

    // 5. Duplicate Section
    function duplicateSection(sectionId) {
        fetch(`${CMS_ROUTES.sectionBaseUrl}/${sectionId}/duplicate`, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        }).then(res => res.json()).then(data => {
            if (data.success) {
                showToast("Section duplicated");
                window.location.reload();
            }
        });
    }

    // 6. Delete Section
    function deleteSection(sectionId) {
        if (!confirm("Are you sure you want to delete this section? This cannot be undone.")) return;

        fetch(`${CMS_ROUTES.sectionBaseUrl}/${sectionId}`, {
            method: 'DELETE',
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        }).then(res => res.json()).then(data => {
            if (data.success) {
                showToast("Section deleted");
                const card = document.querySelector(`.section-tree-card[data-id="${sectionId}"]`);
                if (card) card.remove();
                refreshPreview();
            }
        });
    }

    // Universal Helper to resolve field elements regardless of naming convention
    function getFieldElement(key, suffix) {
        return document.getElementById(`edit-${key}-custom-${suffix}`) ||
               document.getElementById(`edit-${key}-${suffix}`);
    }

    // Color Sync Helpers
    function syncColorField(key, color) {
        const hexInput = getFieldElement(key, 'hex');
        if (hexInput) hexInput.value = color;
        triggerLivePreviewUpdate();
    }

    function syncHexField(key, hex) {
        if (/^#[0-9A-Fa-f]{3,8}$/.test(hex)) {
            const colorInput = getFieldElement(key, 'color');
            if (colorInput) {
                const formatted = hex.length === 7 ? hex : (hex.length === 4 ? '#' + hex[1]+hex[1]+hex[2]+hex[2]+hex[3]+hex[3] : hex);
                colorInput.value = formatted;
            }
        }
        triggerLivePreviewUpdate();
    }

    function applyColorSwatch(key, hex) {
        const colorInput = getFieldElement(key, 'color');
        const hexInput = getFieldElement(key, 'hex');
        if (colorInput) colorInput.value = hex;
        if (hexInput) hexInput.value = hex;
        showToast(`Selected colour ${hex}`);
        triggerLivePreviewUpdate();
    }

    function clearColorField(key) {
        const colorInput = getFieldElement(key, 'color');
        const hexInput = getFieldElement(key, 'hex');
        if (hexInput) hexInput.value = '';
        if (colorInput) colorInput.value = '#ffffff';
        showToast("Colour reset to default");
        triggerLivePreviewUpdate();
    }

    // Instant Real-Time Preview Updater for active editing
    function triggerLivePreviewUpdate() {
        const iframe = document.getElementById('live-preview-iframe');
        if (!iframe || !iframe.contentDocument) return;
        const sectionId = document.getElementById('edit-section-id')?.value;
        if (!sectionId) return;

        try {
            const doc = iframe.contentDocument;
            const sec = doc.getElementById('section-' + sectionId) || doc.querySelector(`[data-section-id="${sectionId}"]`);
            if (!sec) return;

            // Background color
            const customBg = document.getElementById('edit-bg-custom-hex')?.value;
            const presetBg = document.getElementById('edit-bg-color')?.value;
            if (customBg && customBg.startsWith('#')) {
                sec.style.setProperty('background-color', customBg, 'important');
            } else if (presetBg) {
                const map = {
                    'bg-white': '#FFFFFF',
                    'bg-[var(--cream)]': '#F5F1E8',
                    'bg-[var(--sand)]': '#E5DDCD',
                    'bg-[var(--ink)] text-white': '#254536',
                    'bg-stone-50': '#F9FAFB',
                    'bg-stone-900 text-white': '#1C1917'
                };
                if (map[presetBg]) sec.style.setProperty('background-color', map[presetBg], 'important');
            }

            // Text color
            const customText = document.getElementById('edit-text-custom-hex')?.value;
            const presetText = document.getElementById('edit-text-color')?.value;
            let targetColor = null;
            if (customText && customText.startsWith('#')) {
                targetColor = customText;
            } else if (presetText) {
                const map = {
                    'text-stone-900': '#1C1917',
                    'text-stone-700': '#44403C',
                    'text-stone-500': '#78716C',
                    'text-white': '#FFFFFF',
                    'text-[#204b34]': '#204b34',
                    'text-[#254536]': '#254536',
                    'text-emerald-700': '#047857'
                };
                if (map[presetText]) targetColor = map[presetText];
            }
            if (targetColor) {
                sec.style.setProperty('color', targetColor, 'important');
                const h = sec.querySelector('h1, h2, h3');
                if (h) h.style.setProperty('color', targetColor, 'important');
            }

            // Headline / Title
            const titleInput = document.querySelector('textarea[name="title"]');
            if (titleInput) {
                const h = sec.querySelector('h1, h2, h3');
                if (h) h.innerHTML = titleInput.value;
            }

            // Subtitle
            const subInput = document.querySelector('textarea[name="subtitle"]');
            if (subInput) {
                const p = sec.querySelector('p');
                if (p) p.innerText = subInput.value;
            }

            // Badge / Eyebrow
            const badgeInput = document.querySelector('input[name="content[badge]"]');
            if (badgeInput) {
                const b = sec.querySelector('.badge');
                if (b) b.innerText = badgeInput.value;
            }
        } catch(e) {
            // Silently ignore if iframe is reloading
        }
    }

    // 7. Open Section Editor Drawer
    function openSectionEditor(sectionId) {
        const section = currentSections.find(s => s.id === sectionId);
        if (!section) return;

        document.getElementById('edit-section-id').value = section.id;
        document.getElementById('edit-section-type').value = section.section_type;
        document.getElementById('drawer-section-title').innerText = 'Edit ' + capitalize(section.section_type.replace('_', ' '));
        document.getElementById('drawer-section-type').innerText = section.section_type;

        // Content & Settings
        let content = {};
        try {
            content = typeof section.content === 'string' ? JSON.parse(section.content) : (section.content || {});
        } catch(e) { content = {}; }
        const settings = content.settings || {};

        // Populate Spacing
        document.getElementById('edit-top-spacing').value = settings.top_spacing || '80px';
        document.getElementById('edit-bottom-spacing').value = settings.bottom_spacing || '80px';
        if (document.getElementById('edit-left-spacing')) document.getElementById('edit-left-spacing').value = settings.left_spacing || '0px';
        if (document.getElementById('edit-right-spacing')) document.getElementById('edit-right-spacing').value = settings.right_spacing || '0px';

        // Populate Background & Layout
        const bgVal = settings.bg_color || settings.bg_tone || '';
        const bgCustom = settings.bg_custom_color || (bgVal.startsWith('#') ? bgVal : '');
        const bgColorSelect = document.getElementById('edit-bg-color');
        if (bgColorSelect) {
            let found = false;
            for (let i = 0; i < bgColorSelect.options.length; i++) {
                if (bgColorSelect.options[i].value === bgVal) {
                    bgColorSelect.value = bgVal;
                    found = true;
                    break;
                }
            }
            if (!found) {
                const map = { 'cream': 'bg-[var(--cream)]', 'sand': 'bg-[var(--sand)]', 'ink': 'bg-[var(--ink)] text-white', 'white': 'bg-white' };
                if (map[bgVal]) bgColorSelect.value = map[bgVal];
                else bgColorSelect.value = '';
            }
        }
        if (document.getElementById('edit-bg-custom-color')) {
            document.getElementById('edit-bg-custom-color').value = (bgCustom && bgCustom.startsWith('#') && bgCustom.length === 7) ? bgCustom : '#ffffff';
            document.getElementById('edit-bg-custom-hex').value = bgCustom || '';
        }
        document.getElementById('edit-container-width').value = settings.container_width || 'standard';
        document.getElementById('edit-alignment').value = settings.alignment || 'left';
        document.getElementById('edit-anchor-id').value = settings.anchor_id || '';
        const isTruthy = val => val === 1 || val === '1' || val === true || val === 'true';
        document.getElementById('edit-hidden-desktop').checked = isTruthy(settings.hidden_desktop);
        document.getElementById('edit-hidden-tablet').checked = isTruthy(settings.hidden_tablet);
        document.getElementById('edit-hidden-mobile').checked = isTruthy(settings.hidden_mobile);

        // Populate Typography
        const textVal = settings.text_color || settings.title_font_color || '';
        const textCustom = settings.text_custom_color || (textVal.startsWith('#') ? textVal : '');
        document.getElementById('edit-font-family').value = settings.font_family || settings.title_font_family || '';
        document.getElementById('edit-font-size').value = settings.font_size || settings.title_font_size || '';
        document.getElementById('edit-font-weight').value = settings.font_weight || '';
        const textColorSelect = document.getElementById('edit-text-color');
        if (textColorSelect) {
            textColorSelect.value = textVal.startsWith('#') ? '' : textVal;
        }
        if (document.getElementById('edit-text-custom-color')) {
            document.getElementById('edit-text-custom-color').value = (textCustom && textCustom.startsWith('#') && textCustom.length === 7) ? textCustom : '#1c1917';
            document.getElementById('edit-text-custom-hex').value = textCustom || '';
        }

        // Populate Button Global Styling
        const btnBg = settings.button_bg_color || settings.button_primary_color || '';
        const btnText = settings.button_text_color || settings.button_primary_text_color || '';
        if (document.getElementById('edit-button-font')) document.getElementById('edit-button-font').value = settings.button_font || '';
        if (document.getElementById('edit-button-radius')) document.getElementById('edit-button-radius').value = settings.button_border_radius || '12px';
        if (document.getElementById('edit-button-bg-color')) {
            document.getElementById('edit-button-bg-color').value = (btnBg && btnBg.startsWith('#') && btnBg.length === 7) ? btnBg : '#204b34';
            document.getElementById('edit-button-bg-hex').value = btnBg || '';
        }
        if (document.getElementById('edit-button-text-color')) {
            document.getElementById('edit-button-text-color').value = (btnText && btnText.startsWith('#') && btnText.length === 7) ? btnText : '#ffffff';
            document.getElementById('edit-button-text-hex').value = btnText || '';
        }

        // Populate Media
        document.getElementById('edit-media-id').value = section.media_id || '';
        const previewBox = document.getElementById('drawer-media-preview-box');
        const clearMediaBtn = document.getElementById('btn-clear-media');
        if (section.media) {
            const mediaUrl = section.media.storage_path ? `/storage/${section.media.storage_path}` : section.media.url;
            previewBox.innerHTML = `<img src="${mediaUrl}" class="w-full h-full object-cover">`;
            clearMediaBtn.classList.remove('hidden');
        } else {
            previewBox.innerHTML = `<span class="text-[10px] text-stone-400">No Image</span>`;
            clearMediaBtn.classList.add('hidden');
        }

        // Render dynamic fields based on section type
        renderSectionTypeForm(section, content);

        // Open Drawer
        setDrawerTab('content');
        const drawer = document.getElementById('section-editor-drawer');
        drawer.classList.remove('translate-x-full');
        drawer.classList.add('open');
    }

    function closeSectionEditor() {
        const drawer = document.getElementById('section-editor-drawer');
        drawer.classList.remove('open');
        drawer.classList.add('translate-x-full');
    }

    function setDrawerTab(tab) {
        const contentTab = document.getElementById('drawer-tab-content');
        const designTab = document.getElementById('drawer-tab-design');
        const contentBtn = document.getElementById('tab-btn-content');
        const designBtn = document.getElementById('tab-btn-design');

        if (tab === 'content') {
            contentTab.classList.remove('hidden');
            designTab.classList.add('hidden');
            contentBtn.className = "px-4 py-2.5 text-xs font-semibold text-emerald-800 border-b-2 border-emerald-800 transition";
            designBtn.className = "px-4 py-2.5 text-xs font-semibold text-stone-400 hover:text-stone-700 border-b-2 border-transparent transition";
        } else {
            contentTab.classList.add('hidden');
            designTab.classList.remove('hidden');
            designBtn.className = "px-4 py-2.5 text-xs font-semibold text-emerald-800 border-b-2 border-emerald-800 transition";
            contentBtn.className = "px-4 py-2.5 text-xs font-semibold text-stone-400 hover:text-stone-700 border-b-2 border-transparent transition";
        }
    }

    // Helper: Render Image Position Radio Controls
    function renderImagePositionControl(currentPos) {
        const pos = currentPos || 'right';
        return `
            <div class="pt-3 pb-1 border-t border-stone-200/80">
                <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block mb-2">Image Position in Section</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="image_position" value="left" ${pos === 'left' ? 'checked' : ''} class="peer hidden">
                        <div class="peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-900 border border-stone-200 rounded-xl p-2.5 text-center text-xs font-semibold text-stone-600 transition hover:border-stone-400 flex flex-col items-center gap-1">
                            <span>◀ Left</span>
                            <span class="text-[10px] text-stone-400 font-normal">Image left, text right</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="image_position" value="right" ${pos === 'right' ? 'checked' : ''} class="peer hidden">
                        <div class="peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-900 border border-stone-200 rounded-xl p-2.5 text-center text-xs font-semibold text-stone-600 transition hover:border-stone-400 flex flex-col items-center gap-1">
                            <span>Right ▶</span>
                            <span class="text-[10px] text-stone-400 font-normal">Text left, image right</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="image_position" value="background" ${pos === 'background' ? 'checked' : ''} class="peer hidden">
                        <div class="peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-900 border border-stone-200 rounded-xl p-2.5 text-center text-xs font-semibold text-stone-600 transition hover:border-stone-400 flex flex-col items-center gap-1">
                            <span>⬛ Background</span>
                            <span class="text-[10px] text-stone-400 font-normal">Full image background</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="image_position" value="none" ${pos === 'none' ? 'checked' : ''} class="peer hidden">
                        <div class="peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-900 border border-stone-200 rounded-xl p-2.5 text-center text-xs font-semibold text-stone-600 transition hover:border-stone-400 flex flex-col items-center gap-1">
                            <span>🚫 None</span>
                            <span class="text-[10px] text-stone-400 font-normal">Hide image completely</span>
                        </div>
                    </label>
                </div>
            </div>
        `;
    }

    // Helper: Dynamic Bullet List Builder
    function renderDynamicBulletList(bullets) {
        bullets = Array.isArray(bullets) ? bullets : [];
        if (bullets.length === 0) {
            bullets = ['No clear starting point', 'Real-life disruptions get read as failure', 'The most recent moment becomes the impression'];
        }

        let html = `
            <div class="pt-3 border-t border-stone-200/80 space-y-3" id="bullet-list-wrapper">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block">Bullet Points</label>
                        <span class="text-[11px] text-stone-400" id="bullet-count-label">${bullets.length} bullet points (Recommended: 3 to 6)</span>
                    </div>
                    <button type="button" onclick="addBulletItem()" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                        + Add Bullet Point
                    </button>
                </div>

                <div id="bullet-warning-hint" class="${bullets.length > 6 ? '' : 'hidden'} p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-[11px] flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span><strong>Heads up:</strong> More than 6 bullet points might look crowded on standard screens.</span>
                </div>

                <div id="bullet-items-container" class="space-y-2">
        `;

        bullets.forEach((b, i) => {
            const val = typeof b === 'string' ? b : (b?.title || b?.text || '');
            html += `
                <div class="bullet-row flex items-center gap-2 bg-stone-50 p-2 rounded-xl border border-stone-200/90">
                    <span class="bullet-index w-5 h-5 rounded-full bg-stone-200 text-stone-700 text-[10px] font-bold flex items-center justify-center shrink-0">${i + 1}</span>
                    <input type="text" name="content[cards][${i}]" value="${escapeHtml(val)}" placeholder="Bullet point text..." class="flex-1 rounded-lg border border-stone-300 px-3 py-1.5 text-xs bg-white">
                    <button type="button" onclick="removeBulletItem(this)" class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Remove bullet">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;
        return html;
    }

    function addBulletItem() {
        const container = document.getElementById('bullet-items-container');
        if (!container) return;
        const count = container.querySelectorAll('.bullet-row').length;
        const newRow = document.createElement('div');
        newRow.className = "bullet-row flex items-center gap-2 bg-stone-50 p-2 rounded-xl border border-stone-200/90";
        newRow.innerHTML = `
            <span class="bullet-index w-5 h-5 rounded-full bg-stone-200 text-stone-700 text-[10px] font-bold flex items-center justify-center shrink-0">${count + 1}</span>
            <input type="text" name="content[cards][${count}]" value="" placeholder="New bullet point text..." class="flex-1 rounded-lg border border-stone-300 px-3 py-1.5 text-xs bg-white">
            <button type="button" onclick="removeBulletItem(this)" class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Remove bullet">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        `;
        container.appendChild(newRow);
        updateBulletIndices();
    }

    function removeBulletItem(btn) {
        const row = btn.closest('.bullet-row');
        if (row) {
            row.remove();
            updateBulletIndices();
        }
    }

    function updateBulletIndices() {
        const container = document.getElementById('bullet-items-container');
        if (!container) return;
        const rows = container.querySelectorAll('.bullet-row');
        rows.forEach((row, i) => {
            const idxBadge = row.querySelector('.bullet-index');
            if (idxBadge) idxBadge.innerText = i + 1;
            const input = row.querySelector('input');
            if (input) input.name = `content[cards][${i}]`;
        });
        const label = document.getElementById('bullet-count-label');
        if (label) label.innerText = `${rows.length} bullet points (Recommended: 3 to 6)`;
        const hint = document.getElementById('bullet-warning-hint');
        if (hint) {
            if (rows.length > 6) hint.classList.remove('hidden');
            else hint.classList.add('hidden');
        }
    }

    // Helper: Dynamic Button Builder (Up to 4 Buttons)
    function renderButtonBuilder(buttons, legacyContent) {
        let btnList = Array.isArray(buttons) ? [...buttons] : [];
        if (btnList.length === 0 && legacyContent) {
            if (legacyContent.button_1 || legacyContent.primary_button_text) {
                btnList.push({
                    text: legacyContent.button_1 || legacyContent.primary_button_text || 'Primary Button',
                    url: legacyContent.button_1_url || legacyContent.primary_button_url || '/shop',
                    style: 'primary',
                    target: '_self'
                });
            }
            if (legacyContent.button_2 || legacyContent.secondary_button_text) {
                btnList.push({
                    text: legacyContent.button_2 || legacyContent.secondary_button_text || 'Secondary Button',
                    url: legacyContent.button_2_url || legacyContent.secondary_button_url || '#fit',
                    style: 'secondary',
                    target: '_self'
                });
            }
        }

        let html = `
            <div class="pt-3 border-t border-stone-200/80 space-y-3" id="buttons-builder-wrapper">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block">Action Buttons</label>
                        <span class="text-[11px] text-stone-400" id="btn-count-label">${btnList.length} / 4 buttons configured</span>
                    </div>
                    <button type="button" id="btn-add-action-button" onclick="addButtonItem()"
                            class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-lg transition flex items-center gap-1 ${btnList.length >= 4 ? 'opacity-50 pointer-events-none' : ''}">
                        + Add Button
                    </button>
                </div>
                <div id="button-items-container" class="space-y-3">
        `;

        btnList.forEach((b, i) => {
            html += renderButtonItemMarkup(i, b);
        });

        html += `
                </div>
            </div>
        `;
        return html;
    }

    function renderButtonItemMarkup(i, b) {
        const text = b?.text || '';
        const url = b?.url || '';
        const style = b?.style || (i === 0 ? 'primary' : 'secondary');
        const target = b?.target || '_self';
        const bgColor = b?.bg_color || '';
        const textColor = b?.text_color || '';

        return `
            <div class="button-item-card bg-stone-50 p-3 rounded-xl border border-stone-200/90 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="button-num-badge text-[10px] font-bold uppercase tracking-wider text-stone-500 bg-stone-200/80 px-2 py-0.5 rounded-full">
                        Button ${i + 1}
                    </span>
                    <button type="button" onclick="removeButtonItem(this)" class="text-xs text-rose-500 hover:text-rose-700 font-medium">
                        ✕ Remove
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label class="text-[10px] font-semibold text-stone-500 block mb-0.5">Label Text</label>
                        <input type="text" name="content[buttons][${i}][text]" value="${escapeHtml(text)}" placeholder="e.g. Shop Gut Reset" class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white">
                    </div>
                    <div>
                        <label class="text-[10px] font-semibold text-stone-500 block mb-0.5">Destination URL</label>
                        <input type="text" name="content[buttons][${i}][url]" value="${escapeHtml(url)}" placeholder="e.g. /shop or #fit" class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1">
                    <div>
                        <label class="text-[10px] font-semibold text-stone-500 block mb-0.5">Style</label>
                        <select name="content[buttons][${i}][style]" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-xs bg-white">
                            <option value="primary" ${style === 'primary' ? 'selected' : ''}>Primary (Filled)</option>
                            <option value="secondary" ${style === 'secondary' ? 'selected' : ''}>Secondary (Light)</option>
                            <option value="outline" ${style === 'outline' ? 'selected' : ''}>Outline (Border)</option>
                            <option value="ghost" ${style === 'ghost' ? 'selected' : ''}>Ghost (Link)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-semibold text-stone-500 block mb-0.5">Open In</label>
                        <select name="content[buttons][${i}][target]" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-xs bg-white">
                            <option value="_self" ${target === '_self' ? 'selected' : ''}>Same Tab</option>
                            <option value="_blank" ${target === '_blank' ? 'selected' : ''}>New Tab (_blank)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-semibold text-stone-500 block mb-0.5">Custom BG</label>
                        <div class="flex items-center gap-1">
                            <input type="color" value="${bgColor || '#204b34'}" oninput="this.nextElementSibling.value = this.value" class="w-7 h-7 rounded border border-stone-300 cursor-pointer p-0 bg-white">
                            <input type="text" name="content[buttons][${i}][bg_color]" value="${escapeHtml(bgColor)}" placeholder="#hex" maxlength="7" class="w-full rounded border border-stone-300 px-1.5 py-1 text-[11px] font-mono bg-white">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-semibold text-stone-500 block mb-0.5">Custom Text</label>
                        <div class="flex items-center gap-1">
                            <input type="color" value="${textColor || '#ffffff'}" oninput="this.nextElementSibling.value = this.value" class="w-7 h-7 rounded border border-stone-300 cursor-pointer p-0 bg-white">
                            <input type="text" name="content[buttons][${i}][text_color]" value="${escapeHtml(textColor)}" placeholder="#hex" maxlength="7" class="w-full rounded border border-stone-300 px-1.5 py-1 text-[11px] font-mono bg-white">
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function addButtonItem() {
        const container = document.getElementById('button-items-container');
        if (!container) return;
        const count = container.querySelectorAll('.button-item-card').length;
        if (count >= 4) {
            alert("Maximum 4 buttons per section allowed.");
            return;
        }
        const temp = document.createElement('div');
        temp.innerHTML = renderButtonItemMarkup(count, { text: 'New Button', url: '/shop', style: count === 0 ? 'primary' : 'secondary', target: '_self' });
        container.appendChild(temp.firstElementChild);
        updateButtonIndices();
    }

    function removeButtonItem(btn) {
        const card = btn.closest('.button-item-card');
        if (card) {
            card.remove();
            updateButtonIndices();
        }
    }

    function updateButtonIndices() {
        const container = document.getElementById('button-items-container');
        if (!container) return;
        const cards = container.querySelectorAll('.button-item-card');
        cards.forEach((card, i) => {
            const badge = card.querySelector('.button-num-badge');
            if (badge) badge.innerText = `Button ${i + 1}`;
            card.querySelectorAll('input, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[buttons\]\[\d+\]/, `[buttons][${i}]`);
                }
            });
        });
        const label = document.getElementById('btn-count-label');
        if (label) label.innerText = `${cards.length} / 4 buttons configured`;
        const addBtn = document.getElementById('btn-add-action-button');
        if (addBtn) {
            if (cards.length >= 4) {
                addBtn.classList.add('opacity-50', 'pointer-events-none');
            } else {
                addBtn.classList.remove('opacity-50', 'pointer-events-none');
            }
        }
    }

    // Dynamic Cards Builder for Features, Standards, Learn, etc.
    function renderDynamicCardsList(cards, type) {
        cards = Array.isArray(cards) ? cards : [];
        if (cards.length === 0) {
            if (type === 'features') {
                cards = [
                    { title: 'The daily capsule', text: 'A gut-support supplement designed to support baseline gut health.' },
                    { title: 'The guided experience', text: 'Eight components that guide you through a 30-day process.' },
                    { title: 'Take it daily', text: 'Check in lightly. Know what changed.' }
                ];
            } else if (type === 'standards') {
                cards = [
                    { num: '01', title: 'Formulation', text: 'Every ingredient disclosed with intended role.' },
                    { num: '02', title: 'Quality', text: 'How quality is tested and certified.' },
                    { num: '03', title: 'Guidance', text: 'Safety information presented up-front.' }
                ];
            } else if (type === 'learn') {
                cards = [
                    { title: 'Methodology', text: 'Why supplements are easy to start and difficult to judge.', url: '/learn' },
                    { title: 'Medical Context', text: 'What digestive symptoms need medical attention.', url: '/learn' },
                    { title: 'Trial Design', text: 'What a fair 30-day product trial looks like.', url: '/learn' }
                ];
            }
        }

        let html = `
            <div class="pt-3 border-t border-stone-200/80 space-y-3" id="cards-builder-wrapper">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block">${capitalize(type)} Cards</label>
                        <span class="text-[11px] text-stone-400">${cards.length} cards in this section</span>
                    </div>
                    <button type="button" onclick="addCardItem('${type}')" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-lg transition">
                        + Add Card
                    </button>
                </div>
                <div id="card-items-container" class="space-y-3">
        `;

        cards.forEach((c, i) => {
            html += renderCardItemMarkup(i, c, type);
        });

        html += `
                </div>
            </div>
        `;
        return html;
    }

    function renderCardItemMarkup(i, c, type) {
        const title = typeof c === 'string' ? c : (c?.title || '');
        const text = typeof c === 'string' ? '' : (c?.text || c?.description || '');
        const num = c?.num || String(i + 1).padStart(2, '0');
        const url = c?.url || '';

        return `
            <div class="card-item-row bg-stone-50 p-3 rounded-xl border border-stone-200/90 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="card-num-badge text-[10px] font-bold text-stone-500 uppercase">Card ${i + 1}</span>
                    <button type="button" onclick="removeCardItem(this)" class="text-xs text-rose-500 hover:text-rose-700">✕ Remove</button>
                </div>
                ${type === 'standards' ? `
                    <div>
                        <label class="text-[10px] font-semibold text-stone-500 block mb-0.5">Step / Standard Number</label>
                        <input type="text" name="content[cards][${i}][num]" value="${escapeHtml(num)}" placeholder="01" class="w-20 rounded-lg border border-stone-300 px-2 py-1 text-xs bg-white font-mono">
                    </div>
                ` : ''}
                <div>
                    <label class="text-[10px] font-semibold text-stone-500 block mb-0.5">Title / Headline</label>
                    <input type="text" name="content[cards][${i}][title]" value="${escapeHtml(title)}" placeholder="Card title..." class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white font-medium">
                </div>
                <div>
                    <label class="text-[10px] font-semibold text-stone-500 block mb-0.5">Description / Body Text</label>
                    <textarea name="content[cards][${i}][text]" rows="2" placeholder="Card description..." class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white">${escapeHtml(text)}</textarea>
                </div>
                ${type === 'learn' ? `
                    <div>
                        <label class="text-[10px] font-semibold text-stone-500 block mb-0.5">Article Link URL</label>
                        <input type="text" name="content[cards][${i}][url]" value="${escapeHtml(url)}" placeholder="/learn" class="w-full rounded-lg border border-stone-300 px-2.5 py-1 text-xs bg-white font-mono">
                    </div>
                ` : ''}
            </div>
        `;
    }

    function addCardItem(type) {
        const container = document.getElementById('card-items-container');
        if (!container) return;
        const count = container.querySelectorAll('.card-item-row').length;
        const temp = document.createElement('div');
        temp.innerHTML = renderCardItemMarkup(count, { title: 'New Card', text: '' }, type);
        container.appendChild(temp.firstElementChild);
        updateCardIndices();
    }

    function removeCardItem(btn) {
        const row = btn.closest('.card-item-row');
        if (row) {
            row.remove();
            updateCardIndices();
        }
    }

    function updateCardIndices() {
        const container = document.getElementById('card-items-container');
        if (!container) return;
        const rows = container.querySelectorAll('.card-item-row');
        rows.forEach((row, i) => {
            const badge = row.querySelector('.card-num-badge');
            if (badge) badge.innerText = `Card ${i + 1}`;
            row.querySelectorAll('input, textarea').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[cards\]\[\d+\]/, `[cards][${i}]`);
                }
            });
        });
    }

    // Dynamic FAQ Builder
    function renderDynamicFaqList(items) {
        items = Array.isArray(items) ? items : [];
        if (items.length === 0) {
            items = [
                { title: 'Is this a probiotic?', description: 'The 30-Day Gut Reset is a gut-support capsule formulated with targeted botanicals.' },
                { title: 'How does the course last 30 days?', description: 'The bottle, course companion, and check-ins are designed to keep the trial simple.' }
            ];
        }

        let html = `
            <div class="pt-3 border-t border-stone-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block">FAQ Questions & Answers</label>
                        <span class="text-[11px] text-stone-400">${items.length} questions configured</span>
                    </div>
                    <button type="button" onclick="addFaqRow()" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-lg transition">
                        + Add Question
                    </button>
                </div>
                <div id="faq-items-container" class="space-y-3">
        `;

        items.forEach((item, i) => {
            html += renderFaqRowMarkup(i, item);
        });

        html += `
                </div>
            </div>
        `;
        return html;
    }

    function renderFaqRowMarkup(i, item) {
        return `
            <div class="faq-item-row bg-stone-50 p-3 rounded-xl border border-stone-200/90 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="faq-num-badge text-[10px] font-bold text-stone-500 uppercase">Question ${i + 1}</span>
                    <button type="button" onclick="removeFaqRow(this)" class="text-xs text-rose-500 hover:text-rose-700">✕ Remove</button>
                </div>
                <div>
                    <label class="text-[10px] font-semibold text-stone-500 block mb-0.5">Question / Title</label>
                    <input type="text" name="content[items][${i}][title]" value="${escapeHtml(item?.title || '')}" placeholder="e.g. Is this a probiotic?" class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white font-medium">
                </div>
                <div>
                    <label class="text-[10px] font-semibold text-stone-500 block mb-0.5">Answer / Explanation</label>
                    <textarea name="content[items][${i}][description]" rows="3" placeholder="Provide a clear, reassuring answer..." class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white leading-relaxed">${escapeHtml(item?.description || item?.text || '')}</textarea>
                </div>
            </div>
        `;
    }

    function addFaqRow() {
        const container = document.getElementById('faq-items-container');
        if (!container) return;
        const count = container.querySelectorAll('.faq-item-row').length;
        const temp = document.createElement('div');
        temp.innerHTML = renderFaqRowMarkup(count, { title: '', description: '' });
        container.appendChild(temp.firstElementChild);
        updateFaqIndices();
    }

    function removeFaqRow(btn) {
        const row = btn.closest('.faq-item-row');
        if (row) {
            row.remove();
            updateFaqIndices();
        }
    }

    function updateFaqIndices() {
        const container = document.getElementById('faq-items-container');
        if (!container) return;
        const rows = container.querySelectorAll('.faq-item-row');
        rows.forEach((row, i) => {
            const badge = row.querySelector('.faq-num-badge');
            if (badge) badge.innerText = `Question ${i + 1}`;
            row.querySelectorAll('input, textarea').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[items\]\[\d+\]/, `[items][${i}]`);
                }
            });
        });
    }

    // Dynamic Statistics Builder
    function renderDynamicStatsList(stats) {
        stats = Array.isArray(stats) ? stats : [];
        if (stats.length === 0) {
            stats = [
                { value: '30', unit: 'Days', label: 'Structured capsule course' },
                { value: '15', unit: 'Minutes', label: 'Total course check-in time' },
                { value: '100%', unit: 'Traceable', label: 'Clinical ingredient disclosure' },
                { value: '1:1', unit: 'Read', label: 'Personal Gut Response Brief' }
            ];
        }

        let html = `
            <div class="pt-3 border-t border-stone-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block">Metric Callouts</label>
                        <span class="text-[11px] text-stone-400">${stats.length} metrics configured</span>
                    </div>
                    <button type="button" onclick="addStatRow()" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-lg transition">
                        + Add Metric
                    </button>
                </div>
                <div id="stats-items-container" class="space-y-2.5">
        `;

        stats.forEach((s, i) => {
            html += renderStatRowMarkup(i, s);
        });

        html += `
                </div>
            </div>
        `;
        return html;
    }

    function renderStatRowMarkup(i, s) {
        return `
            <div class="stat-item-row bg-stone-50 p-2.5 rounded-xl border border-stone-200/90 space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="stat-num-badge text-[10px] font-bold text-stone-500 uppercase">Metric ${i + 1}</span>
                    <button type="button" onclick="removeStatRow(this)" class="text-xs text-rose-500 hover:text-rose-700">✕ Remove</button>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    <div>
                        <label class="text-[10px] text-stone-500 block mb-0.5">Value</label>
                        <input type="text" name="content[stats][${i}][value]" value="${escapeHtml(s?.value || '')}" placeholder="30" class="w-full rounded-lg border border-stone-300 px-2 py-1 text-xs bg-white font-bold">
                    </div>
                    <div>
                        <label class="text-[10px] text-stone-500 block mb-0.5">Unit / Suffix</label>
                        <input type="text" name="content[stats][${i}][unit]" value="${escapeHtml(s?.unit || '')}" placeholder="Days" class="w-full rounded-lg border border-stone-300 px-2 py-1 text-xs bg-white uppercase">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="text-[10px] text-stone-500 block mb-0.5">Label / Subtitle</label>
                        <input type="text" name="content[stats][${i}][label]" value="${escapeHtml(s?.label || '')}" placeholder="Structured capsule course" class="w-full rounded-lg border border-stone-300 px-2 py-1 text-xs bg-white">
                    </div>
                </div>
            </div>
        `;
    }

    function addStatRow() {
        const container = document.getElementById('stats-items-container');
        if (!container) return;
        const count = container.querySelectorAll('.stat-item-row').length;
        const temp = document.createElement('div');
        temp.innerHTML = renderStatRowMarkup(count, { value: '', unit: '', label: '' });
        container.appendChild(temp.firstElementChild);
        updateStatIndices();
    }

    function removeStatRow(btn) {
        const row = btn.closest('.stat-item-row');
        if (row) {
            row.remove();
            updateStatIndices();
        }
    }

    function updateStatIndices() {
        const container = document.getElementById('stats-items-container');
        if (!container) return;
        const rows = container.querySelectorAll('.stat-item-row');
        rows.forEach((row, i) => {
            const badge = row.querySelector('.stat-num-badge');
            if (badge) badge.innerText = `Metric ${i + 1}`;
            row.querySelectorAll('input').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[stats\]\[\d+\]/, `[stats][${i}]`);
                }
            });
        });
    }

    // Render Clean Typed Fields for All 15 Section Types
    function renderSectionTypeForm(section, content) {
        const container = document.getElementById('dynamic-content-fields');
        let html = '';

        const type = section.section_type;
        const title = section.title || '';
        const subtitle = section.subtitle || '';
        const imagePos = section.image_position || 'right';

        // 1. Standard Header Elements: Badge, Title, Subtitle
        if (type !== 'spacer') {
            html += `
                <div>
                    <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block mb-1">Badge / Eyebrow Text</label>
                    <input type="text" name="content[badge]" value="${escapeHtml(content.badge || content.eyebrow || '')}" oninput="triggerLivePreviewUpdate()" placeholder="e.g. 30-Day Guided Gut Reset" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs bg-white">
                </div>
                <div>
                    <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block mb-1">Main Headline / Title</label>
                    <textarea name="title" rows="2" oninput="triggerLivePreviewUpdate()" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm font-serif bg-white">${escapeHtml(title)}</textarea>
                </div>
                <div>
                    <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block mb-1">Subtitle / Paragraph Description</label>
                    <textarea name="subtitle" rows="3" oninput="triggerLivePreviewUpdate()" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs bg-white leading-relaxed">${escapeHtml(subtitle)}</textarea>
                </div>
            `;
        }

        // 2. Image Position Control (for image-capable sections)
        const imageCapable = ['hero', 'problem', 'showcase', 'testimonials', 'cta', 'features', 'standards', 'learn', 'video'];
        if (imageCapable.includes(type)) {
            html += renderImagePositionControl(imagePos);
        }

        // 3. Section-Type Specific Fields
        if (type === 'hero') {
            const pills = Array.isArray(content.pills) ? content.pills : ['30 capsules', '15 minutes total', 'Gut Response Brief'];
            html += `
                <div class="pt-3 border-t border-stone-200/80">
                    <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block mb-2">Key Feature Highlight Pills (3 Items)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <input type="text" name="content[pills][0]" value="${escapeHtml(pills[0] || '30 capsules')}" placeholder="Pill 1" class="rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white">
                        <input type="text" name="content[pills][1]" value="${escapeHtml(pills[1] || '15 minutes total')}" placeholder="Pill 2" class="rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white">
                        <input type="text" name="content[pills][2]" value="${escapeHtml(pills[2] || 'Gut Response Brief')}" placeholder="Pill 3" class="rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white">
                    </div>
                </div>
            `;
            html += renderButtonBuilder(content.buttons, content);

        } else if (type === 'problem') {
            html += renderDynamicBulletList(content.cards);
            html += renderButtonBuilder(content.buttons, content);

        } else if (type === 'features') {
            html += renderDynamicCardsList(content.cards, 'features');
            html += renderButtonBuilder(content.buttons, content);

        } else if (type === 'standards') {
            html += renderDynamicCardsList(content.cards, 'standards');
            html += renderButtonBuilder(content.buttons, content);

        } else if (type === 'showcase') {
            const items = Array.isArray(content.items) ? content.items : (
                subtitle ? subtitle.split("\n").filter(l => l.trim()) : ['The Capsule Bottle — discreetly labelled', 'Course Companion — concise activation card', 'Guided Course Access — permanent bottle QR']
            );
            html += `
                <div class="pt-3 border-t border-stone-200/80">
                    <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block mb-1">Package Items Checklist</label>
                    <div class="space-y-2" id="showcase-checklist-container">
            `;
            items.forEach((it, idx) => {
                const text = typeof it === 'string' ? it : (it?.title || '');
                html += `
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-stone-400 shrink-0"></span>
                        <input type="text" name="content[items][${idx}]" value="${escapeHtml(text)}" placeholder="Package item description..." class="flex-1 rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white">
                    </div>
                `;
            });
            html += `
                    </div>
                    <div class="mt-3">
                        <label class="text-xs font-semibold text-stone-600 block mb-1">Bottom Tagline Text</label>
                        <input type="text" name="content[primary_button_text]" value="${escapeHtml(content.primary_button_text || 'Take 30. Spend 15. Know your gut.')}" class="w-full rounded-lg border border-stone-300 px-3 py-1.5 text-xs bg-white">
                    </div>
                </div>
            `;
            html += renderButtonBuilder(content.buttons, content);

        } else if (type === 'testimonials') {
            const items = Array.isArray(content.items) ? content.items : [
                { title: 'What changed', description: 'Steadier after meals, improved baseline gut predictability.' },
                { title: 'Next step', description: 'Move smoothly to the maintenance phase.' }
            ];
            html += `
                <div class="pt-3 border-t border-stone-200/80 space-y-3">
                    <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block">Sample Brief Extract Cards (Extract 1 & 2)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-stone-50 p-3 rounded-xl border border-stone-200 space-y-2">
                            <span class="text-[10px] font-bold text-stone-400 uppercase">Extract Card 1</span>
                            <input type="text" name="content[items][0][title]" value="${escapeHtml(items[0]?.title || 'What changed')}" placeholder="Card 1 Header" class="w-full rounded-lg border border-stone-300 px-2.5 py-1 text-xs bg-white">
                            <textarea name="content[items][0][description]" rows="2" placeholder="Card 1 Text" class="w-full rounded-lg border border-stone-300 px-2.5 py-1 text-xs bg-white">${escapeHtml(items[0]?.description || '')}</textarea>
                        </div>
                        <div class="bg-stone-50 p-3 rounded-xl border border-stone-200 space-y-2">
                            <span class="text-[10px] font-bold text-stone-400 uppercase">Extract Card 2</span>
                            <input type="text" name="content[items][1][title]" value="${escapeHtml(items[1]?.title || 'Next step')}" placeholder="Card 2 Header" class="w-full rounded-lg border border-stone-300 px-2.5 py-1 text-xs bg-white">
                            <textarea name="content[items][1][description]" rows="2" placeholder="Card 2 Text" class="w-full rounded-lg border border-stone-300 px-2.5 py-1 text-xs bg-white">${escapeHtml(items[1]?.description || '')}</textarea>
                        </div>
                    </div>
                </div>
            `;
            html += renderButtonBuilder(content.buttons, content);

        } else if (type === 'feature_cards') {
            const items = Array.isArray(content.items) ? content.items : [
                {
                    title: 'May be relevant',
                    description: "Seeking structured gut-health support\nStruggle with supplement consistency\nWant a more informed read\nValue data-driven personal insight"
                },
                {
                    title: 'Speak to a doctor first',
                    description: "Pregnancy or breast-feeding\nDiagnosed gastrointestinal condition\nTaking prescription medications\nUnder medical supervision for chronic issues"
                }
            ];
            html += `
                <div class="pt-3 border-t border-stone-200/80 space-y-4">
                    <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block">Two-Column Fit & Caution Suitability</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-stone-50 p-3 rounded-xl border border-stone-200 space-y-2">
                            <label class="text-[10px] font-bold text-stone-500 uppercase block">Column 1 (Relevant Fit)</label>
                            <input type="text" name="content[items][0][title]" value="${escapeHtml(items[0]?.title || 'May be relevant')}" class="w-full rounded-lg border border-stone-300 px-2.5 py-1 text-xs bg-white font-semibold">
                            <label class="text-[10px] text-stone-400 block">List items (one per line):</label>
                            <textarea name="content[items][0][description]" rows="5" class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white font-sans leading-relaxed">${escapeHtml(items[0]?.description || '')}</textarea>
                        </div>
                        <div class="bg-stone-50 p-3 rounded-xl border border-stone-200 space-y-2">
                            <label class="text-[10px] font-bold text-stone-500 uppercase block">Column 2 (Medical Cautions)</label>
                            <input type="text" name="content[items][1][title]" value="${escapeHtml(items[1]?.title || 'Speak to a doctor first')}" class="w-full rounded-lg border border-stone-300 px-2.5 py-1 text-xs bg-white font-semibold">
                            <label class="text-[10px] text-stone-400 block">List items (one per line):</label>
                            <textarea name="content[items][1][description]" rows="5" class="w-full rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white font-sans leading-relaxed">${escapeHtml(items[1]?.description || '')}</textarea>
                        </div>
                    </div>
                </div>
            `;
            html += renderButtonBuilder(content.buttons, content);

        } else if (type === 'faq') {
            html += renderDynamicFaqList(content.items);
            html += renderButtonBuilder(content.buttons, content);

        } else if (type === 'cta') {
            html += renderButtonBuilder(content.buttons, content);

        } else if (type === 'learn') {
            html += renderDynamicCardsList(content.cards, 'learn');
            html += renderButtonBuilder(content.buttons, content);

        } else if (type === 'statistics') {
            html += renderDynamicStatsList(content.stats);

        } else if (type === 'video') {
            html += `
                <div class="pt-3 border-t border-stone-200/80">
                    <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block mb-1">YouTube, Vimeo or MP4 Video URL</label>
                    <input type="text" name="content[video_url]" value="${escapeHtml(content.video_url || '')}" placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/..." class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs font-mono bg-white">
                    <p class="text-[11px] text-stone-400 mt-1">Directly accepts standard YouTube links, Vimeo video URLs, or direct .mp4 links.</p>
                </div>
            `;

        } else if (type === 'gallery') {
            const images = Array.isArray(content.images) ? content.images : [];
            html += `
                <div class="pt-3 border-t border-stone-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block">Gallery Images (${images.length})</label>
                        <button type="button" onclick="addGalleryRow()" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 transition">
                            + Add Image URL
                        </button>
                    </div>
                    <div id="gallery-urls-container" class="space-y-2">
            `;
            images.forEach((img, idx) => {
                const url = typeof img === 'string' ? img : (img?.url || '');
                html += `
                    <div class="gallery-url-row flex items-center gap-2">
                        <input type="text" name="content[images][${idx}][url]" value="${escapeHtml(url)}" placeholder="https://..." class="flex-1 rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white font-mono">
                        <button type="button" onclick="this.closest('.gallery-url-row').remove()" class="p-1.5 text-stone-400 hover:text-rose-600">✕</button>
                    </div>
                `;
            });
            html += `
                    </div>
                </div>
            `;

        } else if (type === 'spacer') {
            html += `
                <div class="pt-3 space-y-4">
                    <div>
                        <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block mb-1">Spacer Vertical Height</label>
                        <select name="content[height]" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs bg-white">
                            <option value="20px" ${content.height === '20px' ? 'selected' : ''}>20px (Tiny)</option>
                            <option value="40px" ${content.height === '40px' ? 'selected' : ''}>40px (Small)</option>
                            <option value="60px" ${content.height === '60px' ? 'selected' : ''}>60px (Medium)</option>
                            <option value="80px" ${(!content.height || content.height === '80px') ? 'selected' : ''}>80px (Standard)</option>
                            <option value="120px" ${content.height === '120px' ? 'selected' : ''}>120px (Large)</option>
                            <option value="160px" ${content.height === '160px' ? 'selected' : ''}>160px (Extra Large)</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="content[show_line]" value="1" ${content.show_line ? 'checked' : ''} class="rounded border-stone-300 text-[#204b34] focus:ring-0">
                        <span class="text-xs font-semibold text-stone-700">Display subtle horizontal hairline divider line</span>
                    </label>
                </div>
            `;

        } else if (type === 'custom_html') {
            html += `
                <div class="pt-3 border-t border-stone-200/80">
                    <label class="text-xs font-bold text-stone-700 uppercase tracking-wider block mb-1">Custom HTML & Embed Code</label>
                    <textarea name="content[html]" rows="8" placeholder="<div class='my-custom-component'>...</div>" class="w-full rounded-lg border border-stone-300 p-3 text-xs font-mono bg-stone-50 text-stone-900 leading-relaxed">${escapeHtml(content.html || '')}</textarea>
                    <span class="text-[10px] text-stone-400 mt-1 block">Supports valid HTML, inline styles, embeds, and scripts.</span>
                </div>
            `;
        }

        container.innerHTML = html;
    }

    function addGalleryRow() {
        const container = document.getElementById('gallery-urls-container');
        if (!container) return;
        const count = container.querySelectorAll('.gallery-url-row').length;
        const row = document.createElement('div');
        row.className = "gallery-url-row flex items-center gap-2";
        row.innerHTML = `
            <input type="text" name="content[images][${count}][url]" value="" placeholder="https://..." class="flex-1 rounded-lg border border-stone-300 px-2.5 py-1.5 text-xs bg-white font-mono">
            <button type="button" onclick="this.closest('.gallery-url-row').remove()" class="p-1.5 text-stone-400 hover:text-rose-600">✕</button>
        `;
        container.appendChild(row);
    }

    // Submit Drawer Section Form via AJAX
    function handleSectionFormSubmit(e) {
        e.preventDefault();
        const sectionId = document.getElementById('edit-section-id').value;
        const form = document.getElementById('drawer-section-form');
        const formData = new FormData(form);

        const btn = document.getElementById('btn-save-drawer-section');
        btn.innerText = 'Saving...';
        btn.disabled = true;

        formData.append('_method', 'PUT');
        fetch(`${CMS_ROUTES.sectionBaseUrl}/${sectionId}`, {
            method: 'POST',
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: formData
        }).then(async res => {
            const data = await res.json().catch(() => null);
            if (!res.ok) {
                let msg = data?.message || `Server responded with status ${res.status}`;
                if (data?.errors) {
                    const firstErrors = Object.values(data.errors).flat().join('\n• ');
                    if (firstErrors) msg += '\n• ' + firstErrors;
                }
                throw new Error(msg);
            }
            return data;
        }).then(data => {
            btn.innerText = 'Save Section';
            btn.disabled = false;
            if (data && data.success) {
                showToast(data.message || "Section saved successfully");
                // Update local memory
                const idx = currentSections.findIndex(s => s.id == sectionId);
                if (idx > -1) {
                    currentSections[idx] = data.section;
                }
                // Update sidebar tree card title snippet
                const card = document.querySelector(`.section-tree-card[data-id="${sectionId}"]`);
                if (card && data.section) {
                    const titleText = data.section.title ? data.section.title.replace(/<[^>]*>?/gm, '').substring(0, 32) : 'Untitled Section';
                    const subtitleEl = card.querySelector('.section-card-subtitle') || card.querySelector('.truncate:last-child');
                    if (subtitleEl) subtitleEl.innerText = titleText;
                }
                closeSectionEditor();
                try {
                    refreshPreview();
                } catch(e) {
                    console.warn("Could not reload live preview:", e);
                }
            } else {
                alert("Error saving section: " + (data?.message || 'Unknown error'));
            }
        }).catch(err => {
            btn.innerText = 'Save Section';
            btn.disabled = false;
            console.error("Section save error:", err);
            alert("Error saving section:\n" + (err.message || 'Please check form values.'));
        });
    }

    // Confirm Add Section
    function confirmAddSection(type) {
        document.getElementById('modal-add-section').close();
        fetch("{{ route('admin.cms.sections.store', $page) }}", {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ section_type: type })
        }).then(res => res.json()).then(data => {
            if (data.success) {
                showToast("Section added");
                window.location.reload();
            }
        });
    }

    // Page Settings Modal
    function openPageSettingsModal() {
        document.getElementById('modal-page-settings').showModal();
    }

    function handlePageSettingsSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('form-page-settings');
        const formData = new FormData(form);

        const btn = document.getElementById('btn-save-page-settings');
        btn.innerText = 'Saving...';
        btn.disabled = true;

        formData.append('_method', 'PUT');
        fetch("{{ route('admin.cms.update', $page) }}", {
            method: 'POST',
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: formData
        }).then(res => res.json()).then(data => {
            btn.innerText = 'Save Page Details';
            btn.disabled = false;
            if (data.success) {
                showToast("Page settings saved");
                document.getElementById('top-bar-page-title').innerText = data.page.title;
                const statusBadge = document.getElementById('page-status-badge');
                statusBadge.innerText = data.page.status;
                document.getElementById('modal-page-settings').close();
                refreshPreview();
            }
        }).catch(() => {
            btn.innerText = 'Save Page Details';
            btn.disabled = false;
        });
    }

    function updateSeoPreview() {
        const titleInput = document.getElementById('page-meta-title-input');
        const descInput = document.getElementById('page-meta-desc-input');
        const titleCounter = document.getElementById('meta-title-counter');
        const descCounter = document.getElementById('meta-desc-counter');
        const serpTitle = document.getElementById('serp-title-preview');
        const serpDesc = document.getElementById('serp-desc-preview');

        if (titleInput && titleCounter && serpTitle) {
            const len = titleInput.value.length;
            titleCounter.innerText = `${len} / 60 chars`;
            titleCounter.className = len > 60 ? "text-[10px] text-rose-500 font-bold" : "text-[10px] text-stone-400";
            serpTitle.innerText = titleInput.value || (document.getElementById('page-title-input').value + ' | Guided Wellness');
        }

        if (descInput && descCounter && serpDesc) {
            const len = descInput.value.length;
            descCounter.innerText = `${len} / 160 chars`;
            descCounter.className = len > 160 ? "text-[10px] text-rose-500 font-bold" : "text-[10px] text-stone-400";
            serpDesc.innerText = descInput.value || 'Discover Guided Wellness 30-Day Gut Reset capsule course with personal response brief.';
        }
    }

    // Media Picker Integration
    function openMediaPickerModal() {
        document.getElementById('modal-media-picker').showModal();
    }

    function selectMediaItem(id, url, name) {
        document.getElementById('edit-media-id').value = id;
        const previewBox = document.getElementById('drawer-media-preview-box');
        previewBox.innerHTML = `<img src="${url}" class="w-full h-full object-cover">`;
        document.getElementById('btn-clear-media').classList.remove('hidden');
        document.getElementById('selected-media-info').innerText = `Selected: ${name}`;
        document.getElementById('modal-media-picker').close();
        showToast("Image selected");
    }

    function clearSelectedMedia() {
        document.getElementById('edit-media-id').value = '';
        document.getElementById('drawer-media-preview-box').innerHTML = `<span class="text-[10px] text-stone-400">No Image</span>`;
        document.getElementById('btn-clear-media').classList.add('hidden');
    }

    function uploadNewMediaFile(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const formData = new FormData();
        formData.append('file', file);

        showToast("Uploading image...");

        fetch("{{ route('admin.cms.media.upload') }}", {
            method: 'POST',
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: formData
        }).then(res => res.json()).then(data => {
            if (data.success) {
                showToast("Upload complete");
                selectMediaItem(data.media.id, data.media.url, data.media.file_name);
            }
        });
    }

    let searchTimer = null;
    function debounceMediaSearch() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            const query = document.getElementById('media-search-input').value;
            fetch(`${CMS_ROUTES.mediaPickerUrl}?search=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(res => {
                    const grid = document.getElementById('media-grid-container');
                    const empty = document.getElementById('media-empty-state');
                    if (res.data.length === 0) {
                        grid.innerHTML = '';
                        empty.classList.remove('hidden');
                    } else {
                        empty.classList.add('hidden');
                        grid.innerHTML = res.data.map(m => `
                            <div class="media-pick-item group relative aspect-square rounded-xl border-2 border-transparent hover:border-emerald-600 bg-stone-100 overflow-hidden cursor-pointer shadow-2xs"
                                 onclick="selectMediaItem(${m.id}, '${m.url}', '${m.file_name}')">
                                <img src="${m.url}" alt="${m.file_name}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center p-2 text-center">
                                    <span class="text-[10px] text-white font-medium truncate">${m.file_name}</span>
                                </div>
                            </div>
                        `).join('');
                    }
                });
        }, 300);
    }

    function openAddSectionModal() {
        document.getElementById('modal-add-section').showModal();
    }

    function showToast(msg) {
        const toast = document.getElementById('cms-toast');
        const text = document.getElementById('cms-toast-msg');
        text.innerText = msg;
        toast.classList.remove('translate-y-20', 'opacity-0');
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 2500);
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function applyTextColor(colorClass) {
        const sel = document.getElementById('edit-text-color');
        if (sel) {
            // Try to find a matching option, else add a temporary one
            let found = false;
            for (let i = 0; i < sel.options.length; i++) {
                if (sel.options[i].value === colorClass) {
                    sel.value = colorClass;
                    found = true;
                    break;
                }
            }
            if (!found) {
                const opt = document.createElement('option');
                opt.value = colorClass;
                opt.text = colorClass;
                sel.appendChild(opt);
                sel.value = colorClass;
            }
        }
        showToast('Colour applied — save to confirm');
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>
@endsection
