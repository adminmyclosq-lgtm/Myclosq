<?php $__env->startSection('content'); ?>
<div class="section max-w-3xl">
    <span class="badge">Payment confirmed</span>
    <h1 class="serif mt-4 text-5xl">Your order is confirmed.</h1>
    <div class="card mt-8">
        <div class="flex justify-between border-b pb-4"><span>Order</span><b><?php echo e($order->order_number); ?></b></div>
        <div class="flex justify-between py-4"><span>Payment</span><span class="font-semibold text-green-700">Paid</span></div>
        <div class="flex justify-between border-t pt-4"><span>Total</span><b>₹<?php echo e(number_format($order->grand_total,2)); ?></b></div>
        <a class="btn-primary mt-7 inline-block" href="<?php echo e(route('account.order',$order)); ?>">Track my order</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp83\htdocs\gutreset\resources\views\payment-success.blade.php ENDPATH**/ ?>