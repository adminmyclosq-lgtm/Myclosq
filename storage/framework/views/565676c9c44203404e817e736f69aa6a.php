<?php $__env->startSection('content'); ?>
<div class="section">
    <div class="flex flex-wrap items-end justify-between gap-4"><div><span class="badge">Order tracking</span><h1 class="serif mt-4 text-5xl"><?php echo e($order->order_number); ?></h1></div><span class="rounded-full bg-stone-100 px-4 py-2 text-sm"><?php echo e(ucfirst(str_replace('_',' ',$order->shipment_status))); ?></span></div>
    <div class="mt-10 grid gap-6 lg:grid-cols-3">
        <div class="card"><div class="text-sm text-stone-500">Payment</div><div class="mt-2 text-xl font-bold"><?php echo e(ucfirst($order->payment_status)); ?></div><div class="mt-2">₹<?php echo e(number_format($order->grand_total,2)); ?></div></div>
        <div class="card"><div class="text-sm text-stone-500">Fulfilment</div><div class="mt-2 text-xl font-bold"><?php echo e(ucfirst($order->fulfilment_status)); ?></div></div>
        <div class="card"><div class="text-sm text-stone-500">Shipment</div><div class="mt-2 text-xl font-bold"><?php echo e($order->shipments->first()?->tracking_number ?: 'Preparing'); ?></div></div>
    </div>
    <div class="card mt-6">
        <h2 class="text-xl font-bold">Tracking timeline</h2>
        <div class="mt-6 space-y-5">
        <?php $__empty_1 = true; $__currentLoopData = $order->shipments->flatMap->trackingEvents->sortByDesc('event_time'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex gap-4"><div class="mt-2 h-3 w-3 rounded-full bg-stone-800"></div><div><div class="font-semibold"><?php echo e(ucfirst(str_replace('_',' ',$event->status))); ?></div><div class="text-sm text-stone-500"><?php echo e($event->event_time?->format('d M Y, h:i A')); ?> <?php if($event->location): ?> · <?php echo e($event->location); ?> <?php endif; ?></div><div class="mt-1 text-sm"><?php echo e($event->description); ?></div></div></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?> <p class="text-stone-500">Tracking updates will appear here after dispatch.</p><?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp83\htdocs\gutreset\resources\views\account\order.blade.php ENDPATH**/ ?>