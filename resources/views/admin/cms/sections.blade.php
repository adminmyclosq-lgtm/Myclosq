@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10 max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('admin.cms.index') }}" class="text-sm font-semibold text-stone-500 hover:text-stone-900 transition-colors flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Pages</a>
            <h1 class="serif mt-2 text-4xl">Manage Homepage</h1>
        </div>
        <a href="{{ url($page->slug == 'home' ? '/' : $page->slug) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-stone-100 px-4 py-2 text-sm font-semibold text-stone-800 hover:bg-stone-200 transition">
            Preview Original
        </a>
    </div>
    
    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-50 px-5 py-4 text-sm font-medium text-green-800">{{ session('success') }}</div>
    @endif

    <div class="rounded-xl bg-white border border-stone-200 shadow-sm p-6 mb-10">
        <h2 class="text-lg font-bold text-stone-800 mb-4 uppercase tracking-widest text-[11px]">Page Level Details</h2>
        <form method="POST" action="{{ route('admin.cms.update', $page) }}">
            @csrf @method('PUT')
            <div class="grid gap-x-6 gap-y-4 md:grid-cols-2">
                <div>
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-widest block mb-1">Page Title</label>
                    <input name="title" value="{{ $page->title }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-stone-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-widest block mb-1">URL / Slug</label>
                    <input name="slug" value="{{ $page->slug }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-stone-500 focus:outline-none bg-stone-50" readonly>
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-widest block mb-1">Meta Title</label>
                    <input name="meta_title" value="{{ $page->meta_title }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-stone-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-widest block mb-1">Meta Description</label>
                    <input name="meta_description" value="{{ $page->meta_description }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-stone-500 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-widest block mb-1">Status</label>
                        <select name="status" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-stone-500 focus:outline-none bg-white">
                            <option value="draft" @selected($page->status==='draft')>Draft</option>
                            <option value="published" @selected($page->status==='published')>Published</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-widest block mb-1">Published At</label>
                        <input type="datetime-local" name="published_at" value="{{ optional($page->published_at)->format('Y-m-d\TH:i') }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-stone-500 focus:outline-none">
                    </div>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3 items-center">
                <span id="save-page-details-indicator" class="text-sm text-stone-500 hidden animate-pulse">Saving...</span>
                <button type="submit" onclick="document.getElementById('save-page-details-indicator').classList.remove('hidden'); this.innerText = 'Saving...'" class="rounded-md bg-stone-900 px-5 py-2 text-sm font-semibold text-white hover:bg-stone-800 transition">Save Page Details</button>
            </div>
        </form>
    </div>
    
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-stone-800 uppercase tracking-widest text-[11px]">Homepage Sections</h2>
    </div>

    <!-- SECTIONS DRAG & DROP CONTAINER -->
    <div class="space-y-3" id="sections-container">
        @foreach($page->sections->sortBy('sort_order') as $section)
            @php
                $contentData = json_decode($section->content, true);
                if (!is_array($contentData)) $contentData = [];
                $settings = $contentData['settings'] ?? [];
            @endphp
            <div class="group flex items-center justify-between rounded-lg border border-stone-200 bg-white px-5 py-4 shadow-sm hover:shadow-md transition section-card" data-id="{{ $section->id }}">
                <div class="flex items-center gap-4">
                    <div class="text-stone-300 cursor-move hover:text-stone-500 transition drag-handle">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-stone-900">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} {{ ucwords(str_replace('_', ' ', $section->section_type)) }}</span>
                            @if(isset($settings['hidden_desktop']) && $settings['hidden_desktop'] && isset($settings['hidden_mobile']) && $settings['hidden_mobile'])
                                <span class="rounded bg-stone-100 px-2 py-0.5 text-[10px] uppercase tracking-wider text-stone-500 font-bold">Hidden</span>
                            @else
                                <span class="rounded bg-green-50 px-2 py-0.5 text-[10px] uppercase tracking-wider text-green-700 font-bold">Published</span>
                            @endif
                        </div>
                        <div class="mt-1 flex items-center gap-3 text-[11px] text-stone-400 font-mono">
                            <span>Top: {{ $settings['top_spacing'] ?? '80px' }}</span>
                            <span>Bottom: {{ $settings['bottom_spacing'] ?? '80px' }}</span>
                            <span>BG: {{ isset($settings['bg_color']) && $settings['bg_color'] != 'bg-white' ? '✓' : 'None' }}</span>
                            <span>Font: {{ isset($settings['title_font_size']) || isset($settings['body_font_size']) ? '✓' : 'Default' }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <button onclick="document.getElementById('modal-content-{{$section->id}}').showModal()" class="rounded border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition shadow-sm">Edit Content</button>
                    <button onclick="document.getElementById('modal-settings-{{$section->id}}').showModal()" class="rounded border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition shadow-sm">Edit Settings</button>
                    
                    <form method="POST" action="{{ route('admin.cms.sections.duplicate', $section) }}" class="inline">
                        @csrf
                        <button type="submit" class="rounded border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition shadow-sm">Duplicate</button>
                    </form>
                    
                    <a href="{{ url($page->slug == 'home' ? '/' : $page->slug) }}#section-{{$section->id}}" target="_blank" class="rounded border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition shadow-sm">Preview</a>

                    <form method="POST" action="{{ route('admin.cms.sections.destroy', $section) }}" class="inline" onsubmit="return confirm('Delete this section?\nThis action cannot be undone.');">
                        @csrf @method('DELETE')
                        <button type="submit" class="rounded border border-red-200 bg-red-50 text-red-600 px-3 py-1.5 text-xs font-semibold hover:bg-red-100 transition shadow-sm">Delete</button>
                    </form>
                </div>
            </div>

            @include('admin.cms.partials.content-modal', ['section' => $section])
            @include('admin.cms.partials.settings-modal', ['section' => $section])
            
        @endforeach
    </div>

    <!-- ADD SECTION FORM AT BOTTOM -->
    <div class="mt-8 rounded-xl bg-stone-50 border border-stone-200 p-6 shadow-sm">
        <h3 class="text-lg font-bold text-stone-800 uppercase tracking-widest text-[11px] mb-4">Add New Section</h3>
        <form method="POST" action="{{ route('admin.cms.sections.store', $page) }}" class="flex flex-col sm:flex-row items-end gap-4">
            @csrf
            <div class="flex-1 w-full">
                <label class="text-xs font-semibold text-stone-500 uppercase block mb-1">Section Type</label>
                <select name="section_type" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm bg-white shadow-sm focus:border-stone-500 focus:outline-none">
                    <option value="hero">Hero Header</option>
                    <option value="text_image">Text + Image</option>
                    <option value="feature_cards">Feature Cards</option>
                    <option value="statistics">Statistics</option>
                    <option value="standards">Standards / Three Columns</option>
                    <option value="showcase">Product Showcase</option>
                    <option value="cta">CTA Banner</option>
                    <option value="faq">FAQ</option>
                    <option value="testimonials">Testimonials</option>
                    <option value="custom_content">Custom Content (Rich Text)</option>
                    <option value="gallery">Image Gallery</option>
                    <option value="video">Video</option>
                    <option value="spacer">Spacer / Divider</option>
                </select>
            </div>
            <div class="w-full sm:w-32">
                <label class="text-xs font-semibold text-stone-500 uppercase block mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ ($page->sections->max('sort_order') ?? 0) + 10 }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm bg-white shadow-sm focus:border-stone-500 focus:outline-none">
            </div>
            <button type="submit" class="w-full sm:w-auto rounded-md bg-stone-900 px-6 py-2 text-sm font-semibold text-white hover:bg-stone-800 transition shadow-sm h-[38px] flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Add Section
            </button>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .sortable-ghost { opacity: 0.4; }
    .sortable-drag { cursor: grabbing !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const container = document.getElementById('sections-container');
        if (container) {
            new Sortable(container, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                onEnd: function (evt) {
                    let order = [];
                    container.querySelectorAll('.section-card').forEach(function(el) {
                        order.push(el.getAttribute('data-id'));
                    });
                    
                    fetch("{{ route('admin.cms.sections.reorder', $page) }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ order: order })
                    }).then(response => response.json()).then(data => {
                        if(data.success) {
                            // Automatically re-number the badges in the DOM
                            container.querySelectorAll('.section-card').forEach(function(el, index) {
                                let numBadge = el.querySelector('span.font-bold.text-stone-900');
                                if (numBadge) {
                                    let numStr = String(index + 1).padStart(2, '0');
                                    let typeTxt = numBadge.innerText.replace(/^\d+\s/, '');
                                    numBadge.innerText = numStr + ' ' + typeTxt;
                                }
                            });
                        }
                    });
                },
            });
        }
    });
</script>
@endsection
