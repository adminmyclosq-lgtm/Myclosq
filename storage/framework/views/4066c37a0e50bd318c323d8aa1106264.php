<?php $__env->startSection('content'); ?>
<div class="p-6 md:p-10"><span class="badge">WhatsApp</span><h1 class="serif mt-3 text-4xl">Message templates</h1>
<?php if(session('success')): ?><div class="mt-5 rounded-xl bg-green-50 p-4 text-green-800"><?php echo e(session('success')); ?></div><?php endif; ?>
<div class="mt-8 grid gap-6 lg:grid-cols-3">
<div class="card"><h2 class="font-bold">Add template</h2><form method="POST" action="<?php echo e(route('admin.whatsapp.templates.store')); ?>" class="mt-4 space-y-3"><?php echo csrf_field(); ?>
<input name="name" required placeholder="Internal name" class="w-full rounded-xl border p-3">
<input name="meta_template_name" required placeholder="Meta template name" class="w-full rounded-xl border p-3">
<input name="language_code" value="en" required class="w-full rounded-xl border p-3">
<select name="category" class="w-full rounded-xl border p-3"><option>UTILITY</option><option>MARKETING</option><option>AUTHENTICATION</option></select>
<textarea name="body" rows="5" placeholder="Template reference body" class="w-full rounded-xl border p-3"></textarea>
<select name="status" class="w-full rounded-xl border p-3"><option value="draft">draft</option><option value="approved">approved</option><option value="rejected">rejected</option></select>
<button class="btn-primary w-full">Save</button></form></div>
<div class="lg:col-span-2 card"><h2 class="font-bold">Configured templates</h2><div class="mt-5 space-y-4"><?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="border-b pb-4"><div class="flex justify-between"><b><?php echo e($t->name); ?></b><span><?php echo e($t->status); ?></span></div><div class="text-sm text-stone-500"><?php echo e($t->meta_template_name); ?> · <?php echo e($t->language_code); ?></div><div class="mt-2 text-sm"><?php echo e($t->body); ?></div></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp83\htdocs\gutreset\resources\views\admin\whatsapp\templates.blade.php ENDPATH**/ ?>