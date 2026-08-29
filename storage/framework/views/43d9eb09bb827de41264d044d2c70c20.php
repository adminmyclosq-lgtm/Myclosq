<?php $__env->startSection('content'); ?>
<div class="p-6 md:p-10">
<div class="flex flex-wrap items-end justify-between"><div><span class="badge">Management</span><h1 class="serif mt-4 text-5xl">Dashboard</h1></div></div>
<div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
<?php $__currentLoopData = [['Customers',number_format($stats['customers'])],['Orders',number_format($stats['orders'])],['Paid orders',number_format($stats['paid_orders'])],['Sales','₹'.number_format($stats['gross_sales'],2)],['AOV','₹'.number_format($stats['average_order_value']??0,2)]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="card"><div class="text-sm text-stone-500"><?php echo e($s[0]); ?></div><div class="mt-2 text-2xl font-bold"><?php echo e($s[1]); ?></div></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<div class="mt-8 grid gap-6 lg:grid-cols-2">
<div class="card"><h2 class="text-xl font-bold">Sales analytics</h2><div class="mt-5"><?php $__empty_1 = true; $__currentLoopData = $stats['sales_by_day']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><div class="flex justify-between border-b py-3 text-sm"><span><?php echo e($row->day); ?></span><span>₹<?php echo e(number_format($row->sales,2)); ?> · <?php echo e($row->orders); ?></span></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><p class="text-stone-500">No paid sales yet.</p><?php endif; ?></div></div>
<div class="card"><h2 class="text-xl font-bold">Operations</h2><div class="mt-5 grid grid-cols-2 gap-3 text-sm"><a class="rounded-xl bg-stone-100 p-4" href="<?php echo e(route('admin.products.index')); ?>">Catalogue</a><a class="rounded-xl bg-stone-100 p-4" href="<?php echo e(route('admin.orders.index')); ?>">Fulfilment</a><a class="rounded-xl bg-stone-100 p-4" href="<?php echo e(route('admin.inventory.index')); ?>">Inventory</a><a class="rounded-xl bg-stone-100 p-4" href="<?php echo e(route('admin.media.index')); ?>">Media</a></div></div>
</div>
<div class="mt-8 grid gap-6 lg:grid-cols-4">
<a class="card hover:bg-white" href="<?php echo e(route('admin.payments.index')); ?>"><div class="text-xs uppercase text-stone-500">Finance</div><div class="mt-2 text-xl font-bold">Payments & reconciliation</div><p class="mt-2 text-sm text-stone-500">Gateway transactions, pending payments and reconciliation.</p></a>
<a class="card hover:bg-white" href="<?php echo e(route('admin.fulfilment.index')); ?>"><div class="text-xs uppercase text-stone-500">Operations</div><div class="mt-2 text-xl font-bold">Shipment fulfilment</div><p class="mt-2 text-sm text-stone-500">Shipment status, tracking and delivery operations.</p></a>
<a class="card hover:bg-white" href="<?php echo e(route('admin.whatsapp.dashboard')); ?>"><div class="text-xs uppercase text-stone-500">Engagement</div><div class="mt-2 text-xl font-bold">WhatsApp workflow</div><p class="mt-2 text-sm text-stone-500">Message funnel, templates and delivery status.</p></a>
<a class="card hover:bg-white" href="<?php echo e(route('admin.reset.operations')); ?>"><div class="text-xs uppercase text-stone-500">Programme</div><div class="mt-2 text-xl font-bold">Day 0–30 operations</div><p class="mt-2 text-sm text-stone-500">Adherence, milestones, safety and active reset days.</p></a>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp83\htdocs\gutreset\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>