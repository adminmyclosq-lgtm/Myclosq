<?php $__env->startSection('content'); ?>
<div class="section grid gap-12 md:grid-cols-2">
    <div class="aspect-square rounded-[2rem] bg-stone-100">
        <?php if($product->productImages->first()?->media): ?>
            <img src="<?php echo e($product->productImages->first()?->media->url); ?>" class="h-full w-full rounded-[2rem] object-cover" alt="<?php echo e($product->name); ?>">
        <?php endif; ?>
    </div>
    <div class="py-5">
        <span class="badge">30-Day Guided Gut Reset</span>
        <h1 class="serif mt-5 text-5xl"><?php echo e($product->name); ?></h1>
        <p class="mt-5 text-lg leading-8 text-stone-600"><?php echo e($product->description); ?></p>
        <div class="mt-7 space-y-3">
            <div>✓ 30 days of support</div>
            <div>✓ Guided 30-day course</div>
            <div>✓ Gut Response Brief</div>
        </div>
        <?php if($product->variants->first()): ?>
        <button data-add-cart="<?php echo e($product->variants->first()->id); ?>" class="btn-primary mt-8">Add to cart</button>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp83\htdocs\gutreset\resources\views/shop/show.blade.php ENDPATH**/ ?>