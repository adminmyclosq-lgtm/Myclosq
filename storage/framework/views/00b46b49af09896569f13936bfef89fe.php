<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo e($title ?? 'Gut Reset Admin'); ?></title>
<?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css','resources/js/app.js']); ?>
</head>
<body class="bg-stone-50">
<div class="min-h-screen md:flex">
<aside class="w-full border-r bg-white md:w-64">
<div class="p-6 text-xl font-bold">Gut Reset Admin</div>
<nav class="space-y-1 px-4 pb-6 text-sm">
<?php $__currentLoopData = [
['Dashboard','admin.dashboard'],['Products','admin.products.index'],['Orders','admin.orders.index'],['Customers','admin.customers.index'],
['Inventory','admin.inventory.index'],['Shipping','admin.shipping.index'],['Coupons','admin.coupons.index'],['Media','admin.media.index'],['CMS','admin.cms.index'],['Payments','admin.payments.index'],['Fulfilment','admin.fulfilment.index'],['WhatsApp','admin.whatsapp.dashboard'],['Day 0–30','admin.reset.operations']
]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<a class="block rounded-xl px-4 py-3 hover:bg-stone-100" href="<?php echo e(route($item[1])); ?>"><?php echo e($item[0]); ?></a>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<a class="mt-5 block rounded-xl px-4 py-3 bg-stone-900 text-white" href="<?php echo e(route('home')); ?>">View storefront</a>
</nav>
</aside>
<main class="flex-1"><?php echo $__env->yieldContent('content'); ?></main>
</div>
</body>
</html>
<?php /**PATH C:\xampp83\htdocs\gutreset\resources\views/layouts/admin.blade.php ENDPATH**/ ?>