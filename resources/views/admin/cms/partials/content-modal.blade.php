@php
    $contentData = json_decode($section->content, true);
    if (!is_array($contentData)) $contentData = [];
    $headerLogo = !empty($contentData['header_logo_media_id'] ?? null) ? \App\Models\Media::find($contentData['header_logo_media_id']) : null;
@endphp
<dialog id="modal-content-{{$section->id}}" class="backdrop:bg-stone-900/50 p-0 rounded-xl shadow-2xl w-full max-w-2xl bg-white border-0 open:animate-[fadeIn_0.2s_ease-out]">
    <div class="p-6 border-b border-stone-100 flex justify-between items-center bg-stone-50 rounded-t-xl">
        <h3 class="text-lg font-bold text-stone-900">Edit Content: {{ ucwords(str_replace('_', ' ', $section->section_type)) }}</h3>
        <button type="button" onclick="this.closest('dialog').close()" class="text-stone-400 hover:text-stone-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
    </div>
    <form method="POST" action="{{ route('admin.cms.sections.update', $section) }}" enctype="multipart/form-data" class="p-6">
        @csrf @method('PUT')
        <input type="hidden" name="sort_order" value="{{ $section->sort_order }}">
        <input type="hidden" name="section_type" value="{{ $section->section_type }}">
        
        <div class="space-y-5">
            @if($section->section_type === 'hero' || $section->section_type === 'text_image' || $section->section_type === 'showcase' || $section->section_type === 'cta')
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Eyebrow / Badge Text</label>
                    <input name="content[eyebrow]" value="{{ $contentData['eyebrow'] ?? '' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Heading</label>
                    <input name="title" value="{{ $section->title }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm font-serif">
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Description</label>
                    <textarea name="subtitle" rows="3" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">{{ $section->subtitle }}</textarea>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Primary Button Text</label>
                        <input name="content[primary_button_text]" value="{{ $contentData['primary_button_text'] ?? '' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Primary Button URL</label>
                        <input name="content[primary_button_url]" value="{{ $contentData['primary_button_url'] ?? '' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Primary Button Size</label>
                        <select name="content[primary_button_size]" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="">Default</option>
                            <option value="btn-lg" @selected(($contentData['primary_button_size'] ?? '') === 'btn-lg')>Large</option>
                            <option value="btn-sm" @selected(($contentData['primary_button_size'] ?? '') === 'btn-sm')>Small</option>
                        </select>
                    </div>
                </div>
                @if($section->section_type === 'hero' || $section->section_type === 'cta')
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Secondary Button Text</label>
                        <input name="content[secondary_button_text]" value="{{ $contentData['secondary_button_text'] ?? '' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Secondary Button URL</label>
                        <input name="content[secondary_button_url]" value="{{ $contentData['secondary_button_url'] ?? '' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Secondary Button Size</label>
                        <select name="content[secondary_button_size]" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="">Default</option>
                            <option value="btn-lg" @selected(($contentData['secondary_button_size'] ?? '') === 'btn-lg')>Large</option>
                            <option value="btn-sm" @selected(($contentData['secondary_button_size'] ?? '') === 'btn-sm')>Small</option>
                        </select>
                    </div>
                </div>
                @endif
                @if($section->section_type === 'hero')
                <div class="border-t border-stone-100 pt-5">
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-2">Header Logo</label>
                    <p class="mb-3 text-[11px] text-stone-400">Shown in the homepage menu bar. Upload a square PNG, JPG, or WebP logo.</p>
                    <div class="flex items-center gap-4">
                        @if($headerLogo)
                            <img src="{{ $headerLogo->url }}" alt="Current header logo" class="h-12 w-12 rounded-full border border-stone-200 object-cover">
                        @endif
                        <input type="file" name="header_logo" accept="image/png,image/jpeg,image/webp" class="text-sm">
                    </div>
                </div>
                @endif
                
                @if($section->section_type === 'text_image')
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Image Position</label>
                    <select name="content[image_position]" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                        <option value="left" @selected(($contentData['image_position'] ?? '') === 'left')>Left</option>
                        <option value="right" @selected(($contentData['image_position'] ?? '') === 'right')>Right</option>
                    </select>
                </div>
                @endif
                
            @elseif(in_array($section->section_type, ['feature_cards', 'standards', 'faq', 'testimonials']))
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Section Title</label>
                    <input name="title" value="{{ $section->title }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm font-serif">
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Section Description</label>
                    <textarea name="subtitle" rows="3" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">{{ $section->subtitle }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Items Data (JSON Array)</label>
                    <textarea name="content" rows="6" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm font-mono bg-stone-50">{{ is_string($section->content) && str_starts_with(trim($section->content), '[') ? $section->content : (isset($contentData['items']) ? json_encode($contentData['items'], JSON_PRETTY_PRINT) : '[]') }}</textarea>
                    <div class="mt-1 text-xs text-stone-500">Enter a JSON array of items: [{"title":"...","description":"..."}]</div>
                </div>

            @elseif($section->section_type === 'custom_content' || $section->section_type === 'rich_text')
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Rich Content (HTML/JSON)</label>
                    <textarea name="content" rows="10" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm font-mono bg-stone-50">{{ is_string($section->content) ? $section->content : '' }}</textarea>
                </div>

            @else
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Title</label>
                    <input name="title" value="{{ $section->title }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Description / Subtitle</label>
                    <textarea name="subtitle" rows="3" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">{{ $section->subtitle }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Content Data (JSON)</label>
                    <textarea name="content" rows="4" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm font-mono bg-stone-50">{{ is_string($section->content) && str_starts_with(trim($section->content), '{') ? $section->content : '' }}</textarea>
                </div>
            @endif

            @if(!in_array($section->section_type, ['rich_text', 'spacer', 'faq']))
            <div class="border-t border-stone-100 pt-5 mt-5">
                <label class="text-xs font-semibold text-stone-600 uppercase block mb-4">Media / Image</label>
                <div class="flex gap-6 items-start">
                    @if($section->media)
                        <div class="w-24 h-24 rounded border overflow-hidden shrink-0">
                            <img src="{{ $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="flex-1">
                        <input type="file" name="image" accept="image/*" class="text-sm">
                        <p class="text-[11px] text-stone-400 mt-2 tracking-wide">Upload a new image to replace the current media.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-stone-100">
            <button type="button" onclick="this.closest('dialog').close()" class="rounded-md border border-stone-300 bg-white px-5 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50 transition">Cancel</button>
            <button type="submit" class="rounded-md bg-stone-900 px-5 py-2 text-sm font-semibold text-white hover:bg-stone-800 transition">Save Content</button>
        </div>
    </form>
</dialog>
