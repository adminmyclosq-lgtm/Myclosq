@php
    $contentData = json_decode($section->content, true);
    if (!is_array($contentData)) $contentData = [];
    $settings = $contentData['settings'] ?? ['top_spacing' => '80px', 'bottom_spacing' => '80px'];

    $swatches = [
        ['name' => 'Deep Ink', 'hex' => '#0b0f14'],
        ['name' => 'Signal Teal', 'hex' => '#28b5a6'],
        ['name' => 'Clarity Aqua', 'hex' => '#d9ebe8'],
        ['name' => 'Warm Sand', 'hex' => '#e7e3dc'],
        ['name' => 'Pure White', 'hex' => '#fafaf8'],
        ['name' => 'Gut Green', 'hex' => '#1e402b'],
        ['name' => 'Ink', 'hex' => '#254536'],
        ['name' => 'Sage', 'hex' => '#829a87'],
        ['name' => 'Cream', 'hex' => '#f5f1e8'],
        ['name' => 'Sand', 'hex' => '#e5ddcd'],
        ['name' => 'Muted', 'hex' => '#66736d'],
    ];
@endphp
<dialog id="modal-settings-{{$section->id}}" class="backdrop:bg-stone-900/50 m-auto p-0 shadow-2xl w-full max-w-xl bg-white border-0 open:animate-[fadeIn_0.2s_ease-out]">
    <div class="p-6 border-b border-stone-100 flex justify-between items-center bg-stone-50">
        <h3 class="text-lg font-bold text-stone-900">Section Settings</h3>
        <button type="button" onclick="this.closest('dialog').close()" class="text-stone-400 hover:text-stone-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
    </div>
    <form method="POST" action="{{ route('admin.cms.sections.update', $section) }}" class="p-6">
        @csrf @method('PUT')
        <input type="hidden" name="_is_settings_form" value="1">
        <input type="hidden" name="sort_order" value="{{ $section->sort_order }}">
        <input type="hidden" name="title" value="{{ $section->title }}">
        <input type="hidden" name="subtitle" value="{{ $section->subtitle }}">
        
        <div class="space-y-6">
            <div>
                <h4 class="text-sm font-bold text-stone-800 border-b pb-2 mb-4">Spacing</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase">Top Spacing</label>
                        <select name="content[settings][top_spacing]" class="mt-1 w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="0px" @selected(($settings['top_spacing']??'') == '0px')>0px (None)</option>
                            <option value="40px" @selected(($settings['top_spacing']??'') == '40px')>40px (Small)</option>
                            <option value="80px" @selected(($settings['top_spacing']??'80px') == '80px')>80px (Standard)</option>
                            <option value="120px" @selected(($settings['top_spacing']??'') == '120px')>120px (Large)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase">Bottom Spacing</label>
                        <select name="content[settings][bottom_spacing]" class="mt-1 w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="0px" @selected(($settings['bottom_spacing']??'') == '0px')>0px (None)</option>
                            <option value="40px" @selected(($settings['bottom_spacing']??'') == '40px')>40px (Small)</option>
                            <option value="80px" @selected(($settings['bottom_spacing']??'80px') == '80px')>80px (Standard)</option>
                            <option value="120px" @selected(($settings['bottom_spacing']??'') == '120px')>120px (Large)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-bold text-stone-800 border-b pb-2 mb-4">Background</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase">Background Color</label>
                        <select name="content[settings][bg_color]" class="mt-1 w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="bg-white" @selected(($settings['bg_color']??'') == 'bg-white')>White</option>
                            <option value="bg-[var(--cream)]" @selected(($settings['bg_color']??'') == 'bg-[var(--cream)]')>Cream</option>
                            <option value="bg-[var(--ink)]" @selected(($settings['bg_color']??'') == 'bg-[var(--ink)]')>Ink (Dark)</option>
                            <option value="bg-stone-50" @selected(($settings['bg_color']??'') == 'bg-stone-50')>Light Gray</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-bold text-stone-800 border-b pb-2 mb-4">Layout</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase">Container Width</label>
                        <select name="content[settings][container_width]" class="mt-1 w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="standard" @selected(($settings['container_width']??'') == 'standard')>Standard</option>
                            <option value="wide" @selected(($settings['container_width']??'') == 'wide')>Wide</option>
                            <option value="full" @selected(($settings['container_width']??'') == 'full')>Full Width</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase">Alignment</label>
                        <select name="content[settings][alignment]" class="mt-1 w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="left" @selected(($settings['alignment']??'') == 'left')>Left</option>
                            <option value="center" @selected(($settings['alignment']??'') == 'center')>Center</option>
                            <option value="right" @selected(($settings['alignment']??'') == 'right')>Right</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-bold text-stone-800 border-b pb-2 mb-4">Typography</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase">Title Font Style</label>
                        <select name="content[settings][title_font_family]" class="mt-1 w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="" @selected(empty($settings['title_font_family']))>Default for Section</option>
                            <option value="font-['Human_Sans',sans-serif]" @selected(($settings['title_font_family']??'') == "font-['Human_Sans',sans-serif]")>Human Sans (Primary Brand)</option>
                            <option value="font-['Data_Mono',monospace]" @selected(($settings['title_font_family']??'') == "font-['Data_Mono',monospace]")>Data Mono (Technical/Data)</option>
                            <option value="font-sans" @selected(($settings['title_font_family']??'') == 'font-sans')>Inter (Sans-Serif)</option>
                            <option value="font-serif" @selected(($settings['title_font_family']??'') == 'font-serif')>Cormorant Garamond (Serif)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase">Subtitle/Body Font Style</label>
                        <select name="content[settings][body_font_family]" class="mt-1 w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="" @selected(empty($settings['body_font_family']))>Default for Section</option>
                            <option value="font-['Human_Sans',sans-serif]" @selected(($settings['body_font_family']??'') == "font-['Human_Sans',sans-serif]")>Human Sans (Primary Brand)</option>
                            <option value="font-['Data_Mono',monospace]" @selected(($settings['body_font_family']??'') == "font-['Data_Mono',monospace]")>Data Mono (Technical/Data)</option>
                            <option value="font-sans" @selected(($settings['body_font_family']??'') == 'font-sans')>Inter (Sans-Serif)</option>
                            <option value="font-serif" @selected(($settings['body_font_family']??'') == 'font-serif')>Cormorant Garamond (Serif)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase">Title Font Size</label>
                        <select name="content[settings][title_font_size]" class="mt-1 w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="" @selected(empty($settings['title_font_size']))>Default for Section</option>
                            <option value="text-[16pt]" @selected(($settings['title_font_size']??'') == 'text-[16pt]')>16pt</option>
                            <option value="text-[20pt]" @selected(($settings['title_font_size']??'') == 'text-[20pt]')>20pt</option>
                            <option value="text-[24pt]" @selected(($settings['title_font_size']??'') == 'text-[24pt]')>24pt</option>
                            <option value="text-[32pt]" @selected(($settings['title_font_size']??'') == 'text-[32pt]')>32pt</option>
                            <option value="text-[40pt]" @selected(($settings['title_font_size']??'') == 'text-[40pt]')>40pt</option>
                            <option value="text-[48pt]" @selected(($settings['title_font_size']??'') == 'text-[48pt]')>48pt</option>
                            <option value="text-[64pt]" @selected(($settings['title_font_size']??'') == 'text-[64pt]')>64pt</option>
                            <option value="text-[80pt]" @selected(($settings['title_font_size']??'') == 'text-[80pt]')>80pt</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase">Subtitle/Body Font Size</label>
                        <select name="content[settings][body_font_size]" class="mt-1 w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="" @selected(empty($settings['body_font_size']))>Default for Section</option>
                            <option value="text-[10pt]" @selected(($settings['body_font_size']??'') == 'text-[10pt]')>10pt</option>
                            <option value="text-[12pt]" @selected(($settings['body_font_size']??'') == 'text-[12pt]')>12pt</option>
                            <option value="text-[14pt]" @selected(($settings['body_font_size']??'') == 'text-[14pt]')>14pt</option>
                            <option value="text-[16pt]" @selected(($settings['body_font_size']??'') == 'text-[16pt]')>16pt</option>
                            <option value="text-[18pt]" @selected(($settings['body_font_size']??'') == 'text-[18pt]')>18pt</option>
                            <option value="text-[20pt]" @selected(($settings['body_font_size']??'') == 'text-[20pt]')>20pt</option>
                            <option value="text-[24pt]" @selected(($settings['body_font_size']??'') == 'text-[24pt]')>24pt</option>
                        </select>
                    </div>

                    <div class="col-span-2 pt-2 border-t border-stone-100">
                        <label class="text-xs font-semibold text-stone-500 uppercase">Title Text Color</label>
                        <div class="mt-2 flex items-center gap-3">
                            <input type="color" id="picker_title_color_{{$section->id}}" value="{{ $settings['title_font_color'] ?? '#000000' }}" oninput="document.getElementById('input_title_color_{{$section->id}}').value = this.value" class="h-9 w-16 cursor-pointer rounded border border-stone-300 p-0.5 bg-white">
                            <input type="text" id="input_title_color_{{$section->id}}" name="content[settings][title_font_color]" value="{{ $settings['title_font_color'] ?? '' }}" onchange="document.getElementById('picker_title_color_{{$section->id}}').value = this.value || '#000000'" class="w-full max-w-[150px] h-9 rounded-md border border-stone-300 px-3 text-sm font-mono uppercase" placeholder="Default or #HEX">
                            <button type="button" onclick="document.getElementById('input_title_color_{{$section->id}}').value = ''; document.getElementById('picker_title_color_{{$section->id}}').value = '#000000';" class="text-xs font-medium text-stone-500 hover:text-stone-800">Clear</button>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($swatches as $swatch)
                                <button type="button" onclick="document.getElementById('picker_title_color_{{$section->id}}').value='{{$swatch['hex']}}'; document.getElementById('input_title_color_{{$section->id}}').value='{{$swatch['hex']}}';" class="flex items-center gap-1.5 rounded-full border border-stone-200 bg-white px-2 py-1 hover:bg-stone-50 transition" title="{{ $swatch['name'] }} ({{ strtoupper($swatch['hex']) }})">
                                    <div class="h-3 w-3 rounded-full border border-black/10 shadow-inner" style="background-color: {{ $swatch['hex'] }}"></div>
                                    <span class="text-xs text-stone-600">{{ $swatch['name'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-span-2 pt-2 border-t border-stone-100">
                        <label class="text-xs font-semibold text-stone-500 uppercase">Subtitle/Body Text Color</label>
                        <div class="mt-2 flex items-center gap-3">
                            <input type="color" id="picker_body_color_{{$section->id}}" value="{{ $settings['body_font_color'] ?? '#000000' }}" oninput="document.getElementById('input_body_color_{{$section->id}}').value = this.value" class="h-9 w-16 cursor-pointer rounded border border-stone-300 p-0.5 bg-white">
                            <input type="text" id="input_body_color_{{$section->id}}" name="content[settings][body_font_color]" value="{{ $settings['body_font_color'] ?? '' }}" onchange="document.getElementById('picker_body_color_{{$section->id}}').value = this.value || '#000000'" class="w-full max-w-[150px] h-9 rounded-md border border-stone-300 px-3 text-sm font-mono uppercase" placeholder="Default or #HEX">
                            <button type="button" onclick="document.getElementById('input_body_color_{{$section->id}}').value = ''; document.getElementById('picker_body_color_{{$section->id}}').value = '#000000';" class="text-xs font-medium text-stone-500 hover:text-stone-800">Clear</button>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($swatches as $swatch)
                                <button type="button" onclick="document.getElementById('picker_body_color_{{$section->id}}').value='{{$swatch['hex']}}'; document.getElementById('input_body_color_{{$section->id}}').value='{{$swatch['hex']}}';" class="flex items-center gap-1.5 rounded-full border border-stone-200 bg-white px-2 py-1 hover:bg-stone-50 transition" title="{{ $swatch['name'] }} ({{ strtoupper($swatch['hex']) }})">
                                    <div class="h-3 w-3 rounded-full border border-black/10 shadow-inner" style="background-color: {{ $swatch['hex'] }}"></div>
                                    <span class="text-xs text-stone-600">{{ $swatch['name'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-bold text-stone-800 border-b pb-2 mb-4">Visibility</h4>
                <div class="space-y-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="content[settings][hidden_desktop]" value="0">
                        <input type="checkbox" name="content[settings][hidden_desktop]" value="1" class="rounded border-stone-300 text-stone-900" @checked(!empty($settings['hidden_desktop']))>
                        <span class="text-sm font-medium text-stone-700">Hide on Desktop</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="content[settings][hidden_tablet]" value="0">
                        <input type="checkbox" name="content[settings][hidden_tablet]" value="1" class="rounded border-stone-300 text-stone-900" @checked(!empty($settings['hidden_tablet']))>
                        <span class="text-sm font-medium text-stone-700">Hide on Tablet</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="content[settings][hidden_mobile]" value="0">
                        <input type="checkbox" name="content[settings][hidden_mobile]" value="1" class="rounded border-stone-300 text-stone-900" @checked(!empty($settings['hidden_mobile']))>
                        <span class="text-sm font-medium text-stone-700">Hide on Mobile</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-stone-100">
            <button type="button" onclick="this.closest('dialog').close()" class="rounded-md border border-stone-300 bg-white px-5 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50 transition">Cancel</button>
            <button type="submit" class="rounded-md bg-stone-900 px-5 py-2 text-sm font-semibold text-white hover:bg-stone-800 transition">Save Settings</button>
        </div>
    </form>
</dialog>
