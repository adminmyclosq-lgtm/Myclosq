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
    <div class="section">
        <div class="max-w-3xl">
            <?php if($contentData['eyebrow'] ?? ''): ?>
                <span class="badge"><?php echo e($contentData['eyebrow']); ?></span>
            <?php else: ?>
                <span class="badge">Questions</span>
            <?php endif; ?>
            <h2 class="display-serif mt-4 text-4xl leading-tight"><?php echo e($section->title ?? 'Questions you might have.'); ?></h2>
            <?php if($section && $section->subtitle): ?><p class="mt-4 leading-7 text-stone-600"><?php echo e($section->subtitle); ?></p><?php endif; ?>
        </div>
        <div class="mt-10 grid gap-4 md:grid-cols-2">
            <?php if(count($items) > 0): ?>
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card">
                        <div class="font-semibold"><?php echo e($item['title'] ?? ''); ?></div>
                        <p class="mt-3 text-sm leading-6 text-stone-600"><?php echo e($item['description'] ?? ''); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="card">
                    <div class="font-semibold">Is this a probiotic?</div>
                    <p class="mt-3 text-sm leading-6 text-stone-600">The 30-Day Gut Reset is a gut-support capsule formulated with targeted botanicals and fibre-focused ingredients. We focus on strengthening the response, not naming a category.</p>
                </div>
                <div class="card">
                    <div class="font-semibold">How does the course last 30 days?</div>
                    <p class="mt-3 text-sm leading-6 text-stone-600">The bottle, course companion, and short guided moments are designed to keep the trial simple enough to repeat every day.</p>
                </div>
                <div class="card">
                    <div class="font-semibold">Can I start if I am already taking supplements?</div>
                    <p class="mt-3 text-sm leading-6 text-stone-600">You can compare your current routine with the fair-trial format, but the fit section should guide any medical caution first.</p>
                </div>
                <div class="card">
                    <div class="font-semibold">What happens at Day 30?</div>
                    <p class="mt-3 text-sm leading-6 text-stone-600">Your check-ins are summarised into a personal Gut Response Brief, which highlights what changed and what to consider next.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\gutreset\resources\views/components/sections/faq.blade.php ENDPATH**/ ?>