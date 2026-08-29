<?php $__env->startSection('content'); ?>
<div class="section max-w-xl">
    <span class="badge">Customer login</span>
    <h1 class="serif mt-4 text-5xl">Welcome back.</h1>
    <?php if($errors->any()): ?> <div class="mt-5 rounded-xl bg-red-50 p-4 text-sm text-red-700"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div> <?php endif; ?>
    <form method="POST" action="<?php echo e(route('login.post')); ?>" class="card mt-8 space-y-5">
        <?php echo csrf_field(); ?>
        <div><label class="text-sm font-semibold">Email or mobile</label><input name="identifier" value="<?php echo e(old('identifier')); ?>" required class="mt-2 w-full rounded-xl border p-3"></div>
        <div><label class="text-sm font-semibold">Password</label><input name="password" type="password" required class="mt-2 w-full rounded-xl border p-3"></div>
        <button class="btn-primary w-full">Sign in</button>
        <p class="text-center text-sm text-stone-600">New customer? <a class="underline" href="<?php echo e(route('register')); ?>">Create an account</a></p>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gutreset\resources\views/auth/login.blade.php ENDPATH**/ ?>