<?php $__env->startSection('content'); ?>
<div class="p-6 md:p-10">
<div class="flex flex-wrap items-center justify-between gap-4"><div><span class="badge">Ecommerce</span><h1 class="serif mt-3 text-4xl">Products</h1></div><a class="btn-primary" href="<?php echo e(route('admin.products.create')); ?>">+ Add product</a></div>
<?php if(session('success')): ?><div class="mt-5 rounded-xl bg-green-50 p-4 text-green-800"><?php echo e(session('success')); ?></div><?php endif; ?>
<form class="mt-6"><input name="search" value="<?php echo e(request('search')); ?>" placeholder="Search product" class="rounded-xl border p-3"><button class="btn-secondary ml-2">Search</button></form>
<div class="mt-6 overflow-x-auto rounded-2xl border bg-white"><table class="w-full text-sm"><thead class="bg-stone-50 text-left"><tr><th class="p-4">Product</th><th>SKU</th><th>Price</th><th>Status</th><th></th></tr></thead><tbody>
<?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr class="border-t"><td class="p-4"><b><?php echo e($p->name); ?></b><div class="text-xs text-stone-500"><?php echo e($p->category->name ?? 'Uncategorised'); ?></div></td><td><?php echo e($p->base_sku); ?></td><td>₹<?php echo e(number_format($p->variants->first()?->currentPrice?->selling_price ?? 0,2)); ?></td><td><?php echo e($p->status); ?></td><td><a class="underline" href="<?php echo e(route('admin.products.edit',$p)); ?>">Edit</a></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody></table></div><div class="mt-6"><?php echo e($products->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gutreset\resources\views/admin/products/index.blade.php ENDPATH**/ ?>