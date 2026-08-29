<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['section' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['section' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $contentData = $section ? json_decode($section->content, true) : [];
    $settings = $contentData['settings'] ?? [];
    $topSpace = $settings['top_spacing'] ?? '80px';
    $bottomSpace = $settings['bottom_spacing'] ?? '80px';
    $bgColor = $settings['bg_color'] ?? 'bg-white';
    $items = isset($contentData['items']) && is_array($contentData['items']) ? $contentData['items'] : [];
?>
<section class="<?php echo e($bgColor); ?>" style="padding-top: <?php echo e($topSpace); ?>; padding-bottom: <?php echo e($bottomSpace); ?>;" id="section-<?php echo e($section->id ?? 'new'); ?>">
    <div class="section grid gap-10 md:grid-cols-2 md:items-center">
        <div>
            <?php if($contentData['eyebrow'] ?? ''): ?>
                <span class="badge"><?php echo e($contentData['eyebrow']); ?></span>
            <?php else: ?>
                <span class="badge">Day 30</span>
            <?php endif; ?>
            <h2 class="display-serif mt-4 text-4xl leading-tight"><?php echo e($section->title ?? 'Your Gut Response Brief at Day 30.'); ?></h2>
            <p class="mt-4 leading-7 text-stone-600"><?php echo e($section->subtitle ?? 'At the end of the 30 days you receive a personal Gut Response Brief. It is the read after your capsule course and a few short course moments.'); ?></p>
            <?php if($contentData['primary_button_text'] ?? ''): ?>
                <a class="mt-6 inline-flex text-sm font-semibold text-[var(--ink)] underline decoration-stone-400 underline-offset-4" href="<?php echo e($contentData['primary_button_url'] ?? '#'); ?>"><?php echo e($contentData['primary_button_text']); ?></a>
            <?php else: ?>
                <a class="mt-6 inline-flex text-sm font-semibold text-[var(--ink)] underline decoration-stone-400 underline-offset-4" href="<?php echo e(route('shop')); ?>">See what you receive</a>
            <?php endif; ?>
        </div>
        <div class="card">
            <div class="text-sm font-semibold">Example brief</div>
            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <?php if(count($items) > 0): ?>
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <div class="text-xs text-stone-500"><?php echo e($item['title'] ?? ''); ?></div>
                            <div class="mt-1 font-semibold"><?php echo e($item['description'] ?? ''); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div>
                        <div class="text-xs text-stone-500">What changed</div>
                        <div class="mt-1 font-semibold">Steadier after meals</div>
                    </div>
                    <div>
                        <div class="text-xs text-stone-500">Next step</div>
                        <div class="mt-1 font-semibold">Move to maintenance</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\gutreset\resources\views/components/sections/testimonials.blade.php ENDPATH**/ ?>