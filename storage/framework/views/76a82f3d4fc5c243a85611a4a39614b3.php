<?php $__env->startSection('content'); ?>
<div class="section">
    <span class="badge">Cart</span><h1 class="serif mt-4 text-5xl">Your cart</h1>
    <?php if(auth()->guard()->guest()): ?>
        <div class="mt-8 card"><p>Please sign in to manage your cart.</p><a class="btn-primary mt-5" href="<?php echo e(route('login')); ?>">Sign in</a></div>
    <?php else: ?>
        <?php if(!$cart || $cart->cartItems->isEmpty()): ?>
            <div class="mt-8 text-stone-600">Your cart is empty. <a class="underline" href="<?php echo e(route('shop')); ?>">Explore the reset.</a></div>
        <?php else: ?>
            <div class="mt-10 space-y-4">
                <?php $__currentLoopData = $cart->cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card flex items-center justify-between">
                    <div><div class="font-bold"><?php echo e($item->productVariant->product->name); ?></div><div class="text-sm text-stone-500"><?php echo e($item->productVariant->sku); ?></div></div>
                    <div>Qty <?php echo e($item->quantity); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp83\htdocs\gutreset\resources\views/cart.blade.php ENDPATH**/ ?>