<?php $__env->startSection('content'); ?>
<div class="section">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><span class="badge">Account</span><h1 class="serif mt-4 text-5xl">Hello, <?php echo e($user->customerProfile->first_name ?? $user->email); ?>.</h1></div>
        <form method="POST" action="<?php echo e(route('logout')); ?>"><?php echo csrf_field(); ?><button class="btn-secondary">Sign out</button></form>
    </div>
    <?php if(session('success')): ?><div class="mt-6 rounded-xl bg-green-50 p-4 text-green-800"><?php echo e(session('success')); ?></div><?php endif; ?>
    <div class="mt-10 grid gap-6 md:grid-cols-3">
        <div class="card"><div class="text-xs uppercase text-stone-500">Course</div><div class="mt-2 text-2xl font-bold">30 days</div><p class="mt-3 text-sm text-stone-600">Daily check-ins and milestone moments.</p></div>
        <div class="card"><div class="text-xs uppercase text-stone-500">Orders</div><div class="mt-2 text-2xl font-bold"><?php echo e($user->orders->count()); ?></div><a class="mt-3 inline-block underline" href="<?php echo e($user->orders->first()?route('account.order',$user->orders->first()):route('shop')); ?>">View latest order</a></div>
        <div class="card"><div class="text-xs uppercase text-stone-500">WhatsApp</div><div class="mt-2 text-2xl font-bold"><?php echo e(($user->customerProfile->whatsapp_opt_in ?? false)?'Enabled':'Not enabled'); ?></div></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp83\htdocs\gutreset\resources\views/account.blade.php ENDPATH**/ ?>