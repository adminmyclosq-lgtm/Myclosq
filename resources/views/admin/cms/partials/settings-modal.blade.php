@php
    $contentData = json_decode($section->content, true);
    if (!is_array($contentData)) $contentData = [];
    $settings = $contentData['settings'] ?? ['top_spacing' => '80px', 'bottom_spacing' => '80px'];
@endphp
<dialog id="modal-settings-{{$section->id}}" class="backdrop:bg-stone-900/50 p-0 rounded-xl shadow-2xl w-full max-w-xl bg-white border-0 open:animate-[fadeIn_0.2s_ease-out]">
    <div class="p-6 border-b border-stone-100 flex justify-between items-center bg-stone-50 rounded-t-xl">
        <h3 class="text-lg font-bold text-stone-900">Section Settings</h3>
        <button type="button" onclick="this.closest('dialog').close()" class="text-stone-400 hover:text-stone-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
    </div>
    <form method="POST" action="{{ route('admin.cms.sections.update', $section) }}" class="p-6">
        @csrf @method('PUT')
        @foreach($contentData as $key => $val)
            @if($key !== 'settings' && !is_array($val))
                <input type="hidden" name="content[{{ $key }}]" value="{{ $val }}">
            @endif
        @endforeach
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
