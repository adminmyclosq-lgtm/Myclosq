<?php $__env->startSection('content'); ?>
<div class="section">
    <span class="badge">Checkout</span><h1 class="serif mt-4 text-5xl">Complete your order.</h1>
    <?php if($errors->any()): ?> <div class="mt-5 rounded-xl bg-red-50 p-4 text-sm text-red-700"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div> <?php endif; ?>
    <form method="POST" action="<?php echo e(route('checkout.store')); ?>" class="mt-10 grid gap-8 lg:grid-cols-3">
        <?php echo csrf_field(); ?>
        <div class="lg:col-span-2 card space-y-5">
            <h2 class="text-xl font-bold">Delivery address</h2>
            <input name="shipping_address[recipient_name]" required placeholder="Recipient name" class="w-full rounded-xl border p-3">
            <input name="shipping_address[phone]" required placeholder="Phone" class="w-full rounded-xl border p-3">
            <input name="shipping_address[address_line1]" required placeholder="Address line 1" class="w-full rounded-xl border p-3">
            <input name="shipping_address[address_line2]" placeholder="Address line 2" class="w-full rounded-xl border p-3">
            <div class="grid gap-4 md:grid-cols-3"><input name="shipping_address[city]" required placeholder="City" class="rounded-xl border p-3"><input name="shipping_address[state]" required placeholder="State" class="rounded-xl border p-3"><input name="shipping_address[postal_code]" required placeholder="PIN code" class="rounded-xl border p-3"></div>
            <input name="shipping_address[country]" value="India" placeholder="Country" class="w-full rounded-xl border p-3">

            <h2 class="pt-5 text-xl font-bold">Shipping method</h2>
            <?php $__currentLoopData = $methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="flex cursor-pointer items-center justify-between rounded-2xl border p-4">
                    <span><input type="radio" name="shipping_method_id" value="<?php echo e($method->id); ?>" class="mr-3" <?php echo e($loop->first?'checked':''); ?>><?php echo e($method->name); ?></span>
                    <span class="text-sm text-stone-500"><?php echo e($method->estimated_min_days); ?>–<?php echo e($method->estimated_max_days); ?> days</span>
                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <h2 class="pt-5 text-xl font-bold">Coupon</h2>
            <input name="coupon_code" placeholder="Coupon code (optional)" class="w-full rounded-xl border p-3">

            <button class="btn-primary mt-5">Place order</button>
        </div>
        <aside class="card h-fit">
            <h2 class="text-xl font-bold">Order summary</h2>
            <div class="mt-5 space-y-4">
                <?php $__currentLoopData = $cart->cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between text-sm"><span><?php echo e($item->productVariant->product->name); ?> × <?php echo e($item->quantity); ?></span><span>₹<?php echo e(number_format($item->unit_price_snapshot*$item->quantity,2)); ?></span></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="mt-6 border-t pt-5 flex justify-between font-bold"><span>Subtotal</span><span>₹<?php echo e(number_format($cart->cartItems->sum(fn($i)=>$i->unit_price_snapshot*$i->quantity),2)); ?></span></div>
        </aside>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp83\htdocs\gutreset\resources\views\checkout.blade.php ENDPATH**/ ?>