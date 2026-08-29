<?php
    $contentData = json_decode($section->content, true);
    if (!is_array($contentData)) $contentData = [];
    $settings = $contentData['settings'] ?? ['top_spacing' => '80px', 'bottom_spacing' => '80px'];
?>
<dialog id="modal-settings-<?php echo e($section->id); ?>" class="backdrop:bg-stone-900/50 p-0 rounded-xl shadow-2xl w-full max-w-xl bg-white border-0 open:animate-[fadeIn_0.2s_ease-out]">
    <div class="p-6 border-b border-stone-100 flex justify-between items-center bg-stone-50 rounded-t-xl">
        <h3 class="text-lg font-bold text-stone-900">Section Settings</h3>
        <button type="button" onclick="this.closest('dialog').close()" class="text-stone-400 hover:text-stone-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
    </div>
    <form method="POST" action="<?php echo e(route('admin.cms.sections.update', $section)); ?>" class="p-6">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <?php $__currentLoopData = $contentData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($key !== 'settings' && !is_array($val)): ?>
                <input type="hidden" name="content[<?php echo e($key); ?>]" value="<?php echo e($val); ?>">
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <input type="hidden" name="sort_order" value="<?php echo e($section->sort_order); ?>">
        <input type="hidden" name="title" value="<?php echo e($section->title); ?>">
        <input type="hidden" name="subtitle" value="<?php echo e($section->subtitle); ?>">
        
        <div class="space-y-6">
            <div>
                <h4 class="text-sm font-bold text-stone-800 border-b pb-2 mb-4">Spacing</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase">Top Spacing</label>
                        <select name="content[settings][top_spacing]" class="mt-1 w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="0px" <?php if(($settings['top_spacing']??'') == '0px'): echo 'selected'; endif; ?>>0px (None)</option>
                            <option value="40px" <?php if(($settings['top_spacing']??'') == '40px'): echo 'selected'; endif; ?>>40px (Small)</option>
                            <option value="80px" <?php if(($settings['top_spacing']??'80px') == '80px'): echo 'selected'; endif; ?>>80px (Standard)</option>
                            <option value="120px" <?php if(($settings['top_spacing']??'') == '120px'): echo 'selected'; endif; ?>>120px (Large)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase">Bottom Spacing</label>
                        <select name="content[settings][bottom_spacing]" class="mt-1 w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="0px" <?php if(($settings['bottom_spacing']??'') == '0px'): echo 'selected'; endif; ?>>0px (None)</option>
                            <option value="40px" <?php if(($settings['bottom_spacing']??'') == '40px'): echo 'selected'; endif; ?>>40px (Small)</option>
                            <option value="80px" <?php if(($settings['bottom_spacing']??'80px') == '80px'): echo 'selected'; endif; ?>>80px (Standard)</option>
                            <option value="120px" <?php if(($settings['bottom_spacing']??'') == '120px'): echo 'selected'; endif; ?>>120px (Large)</option>
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
                            <option value="bg-white" <?php if(($settings['bg_color']??'') == 'bg-white'): echo 'selected'; endif; ?>>White</option>
                            <option value="bg-[var(--cream)]" <?php if(($settings['bg_color']??'') == 'bg-[var(--cream)]'): echo 'selected'; endif; ?>>Cream</option>
                            <option value="bg-[var(--ink)]" <?php if(($settings['bg_color']??'') == 'bg-[var(--ink)]'): echo 'selected'; endif; ?>>Ink (Dark)</option>
                            <option value="bg-stone-50" <?php if(($settings['bg_color']??'') == 'bg-stone-50'): echo 'selected'; endif; ?>>Light Gray</option>
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
                            <option value="standard" <?php if(($settings['container_width']??'') == 'standard'): echo 'selected'; endif; ?>>Standard</option>
                            <option value="wide" <?php if(($settings['container_width']??'') == 'wide'): echo 'selected'; endif; ?>>Wide</option>
                            <option value="full" <?php if(($settings['container_width']??'') == 'full'): echo 'selected'; endif; ?>>Full Width</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-stone-500 uppercase">Alignment</label>
                        <select name="content[settings][alignment]" class="mt-1 w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                            <option value="left" <?php if(($settings['alignment']??'') == 'left'): echo 'selected'; endif; ?>>Left</option>
                            <option value="center" <?php if(($settings['alignment']??'') == 'center'): echo 'selected'; endif; ?>>Center</option>
                            <option value="right" <?php if(($settings['alignment']??'') == 'right'): echo 'selected'; endif; ?>>Right</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-bold text-stone-800 border-b pb-2 mb-4">Visibility</h4>
                <div class="space-y-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="content[settings][hidden_desktop]" value="0">
                        <input type="checkbox" name="content[settings][hidden_desktop]" value="1" class="rounded border-stone-300 text-stone-900" <?php if(!empty($settings['hidden_desktop'])): echo 'checked'; endif; ?>>
                        <span class="text-sm font-medium text-stone-700">Hide on Desktop</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="content[settings][hidden_tablet]" value="0">
                        <input type="checkbox" name="content[settings][hidden_tablet]" value="1" class="rounded border-stone-300 text-stone-900" <?php if(!empty($settings['hidden_tablet'])): echo 'checked'; endif; ?>>
                        <span class="text-sm font-medium text-stone-700">Hide on Tablet</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="content[settings][hidden_mobile]" value="0">
                        <input type="checkbox" name="content[settings][hidden_mobile]" value="1" class="rounded border-stone-300 text-stone-900" <?php if(!empty($settings['hidden_mobile'])): echo 'checked'; endif; ?>>
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
<?php /**PATH C:\xampp\htdocs\gutreset\resources\views/admin/cms/partials/settings-modal.blade.php ENDPATH**/ ?>