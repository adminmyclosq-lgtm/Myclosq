@php
    $contentData = json_decode($section->content, true);
    if (!is_array($contentData)) $contentData = [];
    $headerLogo = !empty($contentData['header_logo_media_id'] ?? null) ? \App\Models\Media::find($contentData['header_logo_media_id']) : null;
@endphp
<dialog id="modal-content-{{$section->id}}" class="backdrop:bg-stone-900/50 m-auto p-0 shadow-2xl w-full max-w-2xl bg-white border-0 open:animate-[fadeIn_0.2s_ease-out]">
    <div class="p-6 border-b border-stone-100 flex justify-between items-center bg-stone-50">
        <h3 class="text-lg font-bold text-stone-900">Edit Content: {{ ucwords(str_replace('_', ' ', $section->section_type)) }}</h3>
        <button type="button" onclick="this.closest('dialog').close()" class="text-stone-400 hover:text-stone-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
    </div>
    <form method="POST" action="{{ route('admin.cms.sections.update', $section) }}" enctype="multipart/form-data" class="p-6">
        @csrf @method('PUT')
        <input type="hidden" name="sort_order" value="{{ $section->sort_order }}">
        <input type="hidden" name="section_type" value="{{ $section->section_type }}">
        
        <div class="space-y-5">
            @if($section->section_type === 'hero')
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Badge Text</label>
                    <input name="content[badge]" value="{{ $contentData['badge'] ?? '30-Day Guided Gut Reset' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Heading</label>
                    <textarea name="title" rows="2" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm font-serif">{{ $section->title }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Description</label>
                    <textarea name="subtitle" rows="3" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">{{ $section->subtitle }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Primary Button</label>
                        <input name="content[button_1]" value="{{ $contentData['button_1'] ?? 'Explore the Reset' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Secondary Button</label>
                        <input name="content[button_2]" value="{{ $contentData['button_2'] ?? 'Check Your Fit' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                    </div>
                </div>
            @elseif($section->section_type === 'problem')
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Badge Text</label>
                    <input name="content[badge]" value="{{ $contentData['badge'] ?? 'The problem' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Heading</label>
                    <textarea name="title" rows="2" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm font-serif">{{ $section->title }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Description</label>
                    <textarea name="subtitle" rows="3" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">{{ $section->subtitle }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase mb-2 flex items-center justify-between">
                        <span>Bullet Points</span>
                        <button type="button" class="text-xs font-bold text-[var(--ink)] hover:underline" onclick="cloneFieldRow(this)">+ Add Bullet</button>
                    </label>
                    <div class="space-y-2 clone-container">
                        @php $items = !empty($contentData['cards']) ? $contentData['cards'] : ['', '', '']; @endphp
                        @foreach($items as $i => $item)
                        <div class="flex gap-2 items-center clone-row">
                            <input name="content[cards][]" value="{{ is_array($item) ? ($item['title'] ?? '') : $item }}" class="w-full rounded-md border border-stone-300 px-3 py-1.5 text-sm" placeholder="Bullet">
                            <button type="button" class="text-stone-400 hover:text-red-500 px-1" onclick="if(this.closest('.clone-container').children.length > 1) this.closest('.clone-row').remove()">&times;</button>
                        </div>
                        @endforeach
                    </div>
                </div>
            @elseif($section->section_type === 'features')
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Badge Text</label>
                    <input name="content[badge]" value="{{ $contentData['badge'] ?? 'Standout experience' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Heading</label>
                    <textarea name="title" rows="2" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm font-serif">{{ $section->title }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-2">Feature Cards (3 items)</label>
                    <div class="space-y-4">
                        @for($i=0; $i<3; $i++)
                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-md">
                            <input name="content[cards][{{$i}}][title]" value="{{ $contentData['cards'][$i]['title'] ?? '' }}" class="w-full rounded-md border border-stone-300 px-3 py-1.5 text-sm font-bold mb-2" placeholder="Card {{ $i+1 }} Title">
                            <textarea name="content[cards][{{$i}}][text]" rows="2" class="w-full rounded-md border border-stone-300 px-3 py-1.5 text-sm" placeholder="Card {{ $i+1 }} Description">{{ $contentData['cards'][$i]['text'] ?? '' }}</textarea>
                        </div>
                        @endfor
                    </div>
                </div>
            @elseif($section->section_type === 'standards')
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Badge Text</label>
                    <input name="content[badge]" value="{{ $contentData['badge'] ?? 'Transparency' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Heading</label>
                    <textarea name="title" rows="2" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm font-serif">{{ $section->title }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Description</label>
                    <textarea name="subtitle" rows="2" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">{{ $section->subtitle }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-2">Standard Cards (3 items)</label>
                    <div class="space-y-4">
                        @for($i=0; $i<3; $i++)
                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-md">
                            <div class="flex gap-2 mb-2">
                                <input name="content[cards][{{$i}}][num]" value="{{ $contentData['cards'][$i]['num'] ?? '0'.($i+1) }}" class="w-16 rounded-md border border-stone-300 px-2 py-1.5 text-sm font-bold" placeholder="Num">
                                <input name="content[cards][{{$i}}][title]" value="{{ $contentData['cards'][$i]['title'] ?? '' }}" class="w-full rounded-md border border-stone-300 px-3 py-1.5 text-sm font-bold" placeholder="Card {{ $i+1 }} Title">
                            </div>
                            <textarea name="content[cards][{{$i}}][text]" rows="2" class="w-full rounded-md border border-stone-300 px-3 py-1.5 text-sm" placeholder="Card {{ $i+1 }} Description">{{ $contentData['cards'][$i]['text'] ?? '' }}</textarea>
                        </div>
                        @endfor
                    </div>
                </div>
            @elseif($section->section_type === 'showcase')
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Eyebrow Text</label>
                    <input name="content[eyebrow]" value="{{ $contentData['eyebrow'] ?? 'What you receive' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Heading</label>
                    <textarea name="title" rows="2" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm font-serif">{{ $section->title }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">List Items (One per line)</label>
                    <textarea name="subtitle" rows="4" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">{{ $section->subtitle }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Footer / Button Text</label>
                    <input name="content[primary_button_text]" value="{{ $contentData['primary_button_text'] ?? 'Take 30. Spend 15. Know your gut.' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                </div>
            @elseif(str_starts_with($section->section_type, 'support_') || str_starts_with($section->section_type, 'standards_') || str_starts_with($section->section_type, 'learn_') || str_starts_with($section->section_type, 'product_'))
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Title</label>
                    <textarea name="title" rows="2" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">{{ $section->title }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Subtitle</label>
                    <textarea name="subtitle" rows="3" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">{{ $section->subtitle }}</textarea>
                </div>
                
                @foreach($contentData as $key => $val)
                    @if(in_array($key, ['settings', 'badge', 'image'])) @continue @endif
                    
                    @if(is_array($val))
                        <div class="mt-4 p-4 border border-stone-200 bg-stone-50 rounded-lg">
                            <label class="text-xs font-bold text-stone-600 uppercase block mb-3">{{ ucwords(str_replace('_', ' ', $key)) }} Array</label>
                            <div class="space-y-3">
                                @foreach($val as $i => $item)
                                    <div class="grid gap-2 border-l-2 border-stone-300 pl-3">
                                        @if(is_array($item))
                                            @foreach($item as $subK => $subV)
                                                @if(is_array($subV))
                                                    <div class="text-xs font-semibold text-stone-500 mt-2">{{ ucwords($subK) }}:</div>
                                                    @foreach($subV as $j => $arrVal)
                                                        <input type="text" name="content[{{$key}}][{{$i}}][{{$subK}}][{{$j}}]" value="{{ $arrVal }}" class="w-full rounded-md border border-stone-300 px-3 py-1.5 text-xs text-stone-600" placeholder="{{ $subK }} {{$j+1}}">
                                                    @endforeach
                                                @else
                                                    <div class="grid grid-cols-[100px_1fr] items-center">
                                                        <span class="text-xs font-medium text-stone-600">{{ ucwords(str_replace('_', ' ', $subK)) }}</span>
                                                        @if(str_contains($subK, 'text') || str_contains($subK, 'description'))
                                                            <textarea name="content[{{$key}}][{{$i}}][{{$subK}}]" rows="2" class="w-full rounded-md border border-stone-300 px-3 py-1.5 text-sm">{{ $subV }}</textarea>
                                                        @else
                                                            <input type="text" name="content[{{$key}}][{{$i}}][{{$subK}}]" value="{{ $subV }}" class="w-full rounded-md border border-stone-300 px-3 py-1.5 text-sm">
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <input type="text" name="content[{{$key}}][{{$i}}]" value="{{ $item }}" class="w-full rounded-md border border-stone-300 px-3 py-1.5 text-sm">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-4">
                            <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                            @if(str_contains($key, 'text') || str_contains($key, 'description'))
                                <textarea name="content[{{$key}}]" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">{{ $val }}</textarea>
                            @else
                                <input name="content[{{$key}}]" value="{{ $val }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            @endif
                        </div>
                    @endif
                @endforeach
            @elseif(str_starts_with($section->section_type, 'hiw_'))
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Title</label>
                    <textarea name="title" rows="2" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">{{ $section->title }}</textarea>
                </div>
                
                @if(in_array($section->section_type, ['hiw_hero', 'hiw_timeline', 'hiw_cards_grid', 'hiw_personal_brief']))
                    <div>
                        <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Subtitle</label>
                        <textarea name="subtitle" rows="2" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">{{ $section->subtitle }}</textarea>
                    </div>
                @endif
                
                @if(in_array($section->section_type, ['hiw_hero', 'hiw_cta']))
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div><label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Primary Button Text</label><input type="text" name="content[button_1]" value="{{ $contentData['button_1'] ?? '' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm"></div>
                        <div><label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Primary Button URL</label><input type="text" name="content[button_1_url]" value="{{ $contentData['button_1_url'] ?? '' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div><label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Secondary Button Text</label><input type="text" name="content[button_2]" value="{{ $contentData['button_2'] ?? '' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm"></div>
                        <div><label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Secondary Button URL</label><input type="text" name="content[button_2_url]" value="{{ $contentData['button_2_url'] ?? '' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm"></div>
                    </div>
                @endif
                @if($section->section_type === 'hiw_help')
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div><label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Left Button Text</label><input type="text" name="content[button]" value="{{ $contentData['button'] ?? '' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm"></div>
                        <div><label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Left Button URL</label><input type="text" name="content[button_url]" value="{{ $contentData['button_url'] ?? '' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm"></div>
                    </div>
                @endif
                
                @if(in_array($section->section_type, ['hiw_timeline', 'hiw_cards_grid', 'hiw_cards_grid_flat', 'hiw_help']))
                    @php $limit = $section->section_type === 'hiw_timeline' ? 6 : 4; @endphp
                    <div class="mt-4">
                        <label class="text-xs font-semibold text-stone-600 uppercase block mb-2">{{ $limit }} Data Cards</label>
                        <div class="space-y-3">
                            @for($i = 0; $i < $limit; $i++)
                                <div class="grid grid-cols-[1fr_2fr] gap-3 bg-stone-50 p-2 rounded-md border border-stone-200">
                                    <input type="text" name="content[cards][{{$i}}][{{ $section->section_type === 'hiw_timeline' ? 'day' : 'title' }}]" value="{{ $contentData['cards'][$i][$section->section_type === 'hiw_timeline' ? 'day' : 'title'] ?? '' }}" placeholder="{{ $section->section_type === 'hiw_timeline' ? 'Day' : 'Card Title' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                                    <input type="text" name="content[cards][{{$i}}][{{ $section->section_type === 'hiw_timeline' ? 'label' : 'text' }}]" value="{{ $contentData['cards'][$i][$section->section_type === 'hiw_timeline' ? 'label' : 'text'] ?? '' }}" placeholder="{{ $section->section_type === 'hiw_timeline' ? 'Label Text' : 'Description text' }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                                </div>
                            @endfor
                        </div>
                    </div>
                @endif
                @if(in_array($section->section_type, ['hiw_honest_read', 'hiw_physical_pack']))
                    @php $limit = $section->section_type === 'hiw_honest_read' ? 5 : 3; @endphp
                    <div class="mt-4">
                        <label class="text-xs font-semibold text-stone-600 uppercase mb-2 flex items-center justify-between">
                            <span>Bullet Points</span>
                            <button type="button" class="text-xs font-bold text-[var(--ink)] hover:underline" onclick="cloneFieldRow(this)">+ Add Bullet</button>
                        </label>
                        <div class="space-y-2 clone-container">
                            @php $items = !empty($contentData['cards']) ? $contentData['cards'] : array_fill(0, $limit, ''); @endphp
                            @foreach($items as $i => $item)
                            <div class="flex gap-2 items-center clone-row">
                                <input type="text" name="content[cards][]" value="{{ is_array($item) ? ($item['title'] ?? '') : $item }}" placeholder="Bullet line" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                                <button type="button" class="text-stone-400 hover:text-red-500 px-1" onclick="if(this.closest('.clone-container').children.length > 1) this.closest('.clone-row').remove()">&times;</button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if($section->section_type === 'hiw_personal_brief')
                    <div class="mt-4">
                        <label class="text-xs font-semibold text-stone-600 uppercase block mb-2">Outcome Tags (Up to 5)</label>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                            @for($i = 0; $i < 5; $i++)
                                <input type="text" name="content[tags][{{$i}}]" value="{{ (is_array($contentData['tags'][$i] ?? '')) ? ($contentData['tags'][$i]['title'] ?? '') : ($contentData['tags'][$i] ?? '') }}" placeholder="Outcome" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            @endfor
                        </div>
                    </div>
                @endif
                @if(in_array($section->section_type, ['hiw_product_layer', 'hiw_ready']))
                    @php $titleKey = $section->section_type === 'hiw_ready' ? 'badge' : 'title'; @endphp
                    <div class="grid lg:grid-cols-2 gap-6 bg-stone-50 p-4 rounded-xl border border-stone-200 mt-4">
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <input type="text" name="content[col1_{{$titleKey}}]" value="{{ $contentData["col1_{$titleKey}"] ?? '' }}" placeholder="Column 1 Title/Badge" class="w-full max-w-[200px] rounded-md border border-stone-300 px-3 py-2 text-sm font-bold">
                                <button type="button" class="text-xs font-bold text-[var(--ink)] hover:underline ml-2" onclick="cloneFieldRow(this)">+ Add Line</button>
                            </div>
                            <div class="space-y-2 clone-container">
                                @php $items1 = !empty($contentData['col1_items']) ? $contentData['col1_items'] : ['', '', '']; @endphp
                                @foreach($items1 as $item)
                                <div class="flex gap-2 items-center clone-row">
                                    <input type="text" name="content[col1_items][]" value="{{ $item }}" placeholder="Line item" class="w-full rounded-md border border-stone-300 px-3 py-2 text-xs">
                                    <button type="button" class="text-stone-400 hover:text-red-500 px-1" onclick="if(this.closest('.clone-container').children.length > 1) this.closest('.clone-row').remove()">&times;</button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <input type="text" name="content[col2_{{$titleKey}}]" value="{{ $contentData["col2_{$titleKey}"] ?? '' }}" placeholder="Column 2 Title/Badge" class="w-full max-w-[200px] rounded-md border border-stone-300 px-3 py-2 text-sm font-bold">
                                <button type="button" class="text-xs font-bold text-[var(--ink)] hover:underline ml-2" onclick="cloneFieldRow(this)">+ Add Line</button>
                            </div>
                            <div class="space-y-2 clone-container">
                                @php $items2 = !empty($contentData['col2_items']) ? $contentData['col2_items'] : ['', '', '']; @endphp
                                @foreach($items2 as $item)
                                <div class="flex gap-2 items-center clone-row">
                                    <input type="text" name="content[col2_items][]" value="{{ $item }}" placeholder="Line item" class="w-full rounded-md border border-stone-300 px-3 py-2 text-xs">
                                    <button type="button" class="text-stone-400 hover:text-red-500 px-1" onclick="if(this.closest('.clone-container').children.length > 1) this.closest('.clone-row').remove()">&times;</button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Title</label>
                    <input name="title" value="{{ $section->title }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm font-serif">
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Subtitle / Description</label>
                    <textarea name="subtitle" rows="3" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">{{ $section->subtitle }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-stone-600 uppercase block mb-1">Content Data (JSON)</label>
                    <textarea name="content" rows="4" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm font-mono bg-stone-50">{{ is_string($section->content) && str_starts_with(trim($section->content), '{') ? $section->content : (is_array($contentData) ? json_encode($contentData, JSON_PRETTY_PRINT) : '{}') }}</textarea>
                </div>
            @endif
            @if(!in_array($section->section_type, ['rich_text', 'spacer', 'faq', 'features', 'standards']))
            <div class="border-t border-stone-200 pt-6 mt-6">
                <label class="text-xs font-semibold text-stone-600 uppercase block mb-4">Media / Image Upload</label>
                <div class="flex flex-col sm:flex-row gap-6 items-start">
                    @if($section->media)
                        <div class="w-32 h-32 rounded-xl border border-stone-200 overflow-hidden shrink-0 shadow-sm relative group bg-stone-100 flex items-center justify-center">
                            <img src="{{ $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="flex-1 w-full relative">
                        <input type="file" name="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="this.nextElementSibling.querySelector('span.filename').innerText = this.files[0].name">
                        <div class="border-2 border-dashed border-stone-300 rounded-xl bg-stone-50/50 hover:bg-stone-50 transition p-6 flex flex-col items-center justify-center text-center h-auto min-h-[8rem]">
                            <svg class="h-6 w-6 text-stone-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            <span class="text-sm font-semibold text-stone-700 filename">Click or drag image here</span>
                            <span class="text-xs text-stone-500 mt-1">PNG, JPG, WEBP up to 5MB</span>
                            <div class="mt-3 text-xs text-red-500 font-medium border-t border-red-500/30 pt-2 w-full">Note: Please maintain a 4:3 aspect ratio (e.g., 1200x900px) across all sections to ensure visual consistency.</div>
                        </div>
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
<script>
    if (typeof window.cloneFieldRow === 'undefined') {
        window.cloneFieldRow = function(btn) {
            const container = btn.closest('div').parentElement.querySelector('.clone-container');
            if (!container || container.children.length === 0) return;
            const clone = container.lastElementChild.cloneNode(true);
            const inputs = clone.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.value = '';
                if (input.name.match(/\[(\d+)\]/)) {
                    input.name = input.name.replace(/\[(\d+)\]/, function(match, p1) {
                        return '[' + (parseInt(p1) + 1) + ']';
                    });
                }
            });
            container.appendChild(clone);
        }
    }
</script>
