<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ?? 'Gut Reset'); ?></title>
    <script>
        window.AppRoutes = {
            cartItems: <?php echo json_encode(url('/api/v1/cart/items'), 15, 512) ?>,
            login: <?php echo json_encode(route('login'), 15, 512) ?>,
        };
    </script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css','resources/js/app.js']); ?>
</head>
<body>
<header class="sticky top-0 z-40 border-b border-stone-200 bg-white/85 backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 py-4 md:px-8">
        <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3 text-lg font-semibold tracking-tight text-[var(--ink)]">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[var(--ink)] text-sm font-bold text-white">GW</span>
            <span>Guided Wellness</span>
        </a>
        <nav class="hidden items-center gap-7 text-sm md:flex">
            <a href="<?php echo e(route('shop')); ?>">Shop</a>
            <a href="<?php echo e(url('/#how-it-works')); ?>">How It Works</a>
            <a href="<?php echo e(url('/#standards')); ?>">Our Standards</a>
            <a href="<?php echo e(url('/#learn')); ?>">Learn</a>
            <a href="<?php echo e(url('/#faq')); ?>">Support</a>
        </nav>
        <div class="flex items-center gap-3 text-sm">
            <a href="<?php echo e(route('cart')); ?>" class="hidden md:inline-flex">Cart</a>
            <?php if(auth()->guard()->check()): ?>
                <?php
                    $authUser = auth()->user();
                    $primaryRole = $authUser?->roles()->first();
                    $accountRoute = $authUser?->hasRole('SUPER_ADMIN') ? 'admin.dashboard' : 'account';
                    $roleLabel = $primaryRole
                        ? ucwords(strtolower(str_replace('_', ' ', (string) $primaryRole->code)))
                        : 'User';
                ?>
                <a href="<?php echo e(route($accountRoute)); ?>" class="hidden max-w-[14rem] rounded-full border border-stone-200 bg-white px-3 py-2 text-left leading-tight md:block">
                    <span class="block truncate font-semibold text-stone-800"><?php echo e($authUser?->name ?? 'Account'); ?></span>
                    <span class="block truncate text-xs text-stone-500"><?php echo e($roleLabel); ?></span>
                </a>
                <a href="<?php echo e(route($accountRoute)); ?>" class="inline-flex md:hidden">Account</a>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="hidden md:inline-flex">Account</a>
            <?php endif; ?>
            <a href="<?php echo e(route('shop')); ?>" class="btn-primary !px-5 !py-2.5">Shop Gut Reset</a>
        </div>
    </div>
</header>

<main>
    <?php echo $__env->yieldContent('content'); ?>
</main>

<footer class="border-t border-stone-200 bg-[var(--cream)]">
    <div class="section grid gap-10 md:grid-cols-4">
        <div>
            <div class="text-lg font-semibold">Guided Wellness</div>
            <p class="mt-3 max-w-sm text-sm leading-7 text-stone-600">Redefining the supplement experience through transparency, guidance, and respect for biological complexity.</p>
        </div>
        <div>
            <div class="font-semibold">Shop</div>
            <a class="mt-3 block text-sm text-stone-600" href="<?php echo e(route('shop')); ?>">30-Day Gut Reset</a>
            <a class="mt-2 block text-sm text-stone-600" href="<?php echo e(url('/#how-it-works')); ?>">How It Works</a>
        </div>
        <div>
            <div class="font-semibold">Learn</div>
            <a class="mt-3 block text-sm text-stone-600" href="<?php echo e(url('/#learn')); ?>">Guides</a>
            <a class="mt-2 block text-sm text-stone-600" href="<?php echo e(url('/#standards')); ?>">Standards</a>
            <a class="mt-2 block text-sm text-stone-600" href="<?php echo e(url('/#faq')); ?>">About</a>
        </div>
        <div>
            <div class="font-semibold">Support</div>
            <a class="mt-3 block text-sm text-stone-600" href="<?php echo e(route('account')); ?>">Account</a>
            <a class="mt-2 block text-sm text-stone-600" href="<?php echo e(url('/#faq')); ?>">Help Centre</a>
        </div>
    </div>
    <div class="border-t border-stone-200 py-6 text-center text-sm text-stone-500">© 2026 Kurate Wellness Private Limited. All rights reserved.</div>
</footer>
</body>
</html>
<?php /**PATH C:\xampp83\htdocs\gutreset\resources\views\layouts\app.blade.php ENDPATH**/ ?>