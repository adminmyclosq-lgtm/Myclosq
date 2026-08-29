<?php $__env->startSection('content'); ?>
<div class="section">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><span class="badge">Shop</span><h1 class="serif mt-4 text-5xl">30-Day Gut Reset</h1></div>
        <form><input name="search" value="<?php echo e(request('search')); ?>" class="rounded-full border px-5 py-3" placeholder="Search products"><button class="btn-primary ml-2">Search</button></form>
    </div>
    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <article class="card overflow-hidden p-0">
            <a href="<?php echo e(route('product.show',$product)); ?>" class="block aspect-square bg-stone-100">
                <?php if($product->productImages->first()?->media): ?>
                    <img src="<?php echo e($product->productImages->first()?->media->url); ?>" class="h-full w-full object-cover" alt="<?php echo e($product->name); ?>">
                <?php endif; ?>
            </a>
            <div class="p-6">
                <div class="text-xs uppercase tracking-widest text-stone-500"><?php echo e($product->category->name ?? 'Gut Reset'); ?></div>
                <h2 class="mt-2 text-xl font-bold"><?php echo e($product->name); ?></h2>
                <p class="mt-2 text-sm text-stone-600"><?php echo e($product->short_description); ?></p>
                <div class="mt-5 flex items-center justify-between">
                    <a href="<?php echo e(route('product.show',$product)); ?>" class="font-semibold">View details →</a>
                    <?php if($product->variants->first()): ?>
                    <button data-add-cart="<?php echo e($product->variants->first()->id); ?>" class="btn-primary px-4 py-2 text-sm">Add to cart</button>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="mt-8"><?php echo e($products->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gutreset\resources\views/shop/index.blade.php ENDPATH**/ ?>