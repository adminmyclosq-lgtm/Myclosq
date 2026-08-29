<?php $__env->startSection('content'); ?>
<div class="p-6 md:p-10">
    <h1 class="serif text-4xl">CMS & Pages</h1>
    <?php if(session('success')): ?>
        <div class="mt-5 rounded-xl bg-green-50 p-4 text-green-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    
    <div class="mt-8 overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm text-stone-600">
            <thead class="bg-stone-50 text-stone-900 border-b border-stone-200">
                <tr>
                    <th class="px-6 py-4 font-semibold">S.No.</th>
                    <th class="px-6 py-4 font-semibold">Page Title</th>
                    <th class="px-6 py-4 font-semibold">Meta Title</th>
                    <th class="px-6 py-4 font-semibold">URL</th>
                    <th class="px-6 py-4 font-semibold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200">
                <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-stone-50">
                        <td class="px-6 py-4"><?php echo e($index + 1); ?></td>
                        <td class="px-6 py-4 font-medium text-stone-900"><?php echo e($page->title); ?></td>
                        <td class="px-6 py-4"><?php echo e($page->meta_title ?? '-'); ?></td>
                        <td class="px-6 py-4">
                            <a href="<?php echo e(url($page->slug == 'home' ? '/' : $page->slug)); ?>" target="_blank" class="text-stone-500 hover:text-stone-900 underline decoration-stone-300 underline-offset-4">
                                <?php echo e(url($page->slug == 'home' ? '/' : $page->slug)); ?>

                            </a>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="<?php echo e(route('admin.cms.sections', $page)); ?>" class="inline-flex items-center rounded-lg bg-stone-900 px-4 py-2 text-xs font-semibold text-white hover:bg-stone-800 transition">
                                Manage
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    
    <div class="mt-6"><?php echo e($pages->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gutreset\resources\views/admin/cms/index.blade.php ENDPATH**/ ?>