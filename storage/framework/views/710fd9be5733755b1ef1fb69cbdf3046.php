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
?>
<section class="section" style="padding-top: <?php echo e($topSpace); ?>; padding-bottom: <?php echo e($bottomSpace); ?>;" id="section-<?php echo e($section->id ?? 'new'); ?>">
    <div class="rounded-[2rem] bg-[linear-gradient(135deg,#18352c_0%,#284a3e_55%,#3c5c4e_100%)] px-6 py-12 text-white md:px-12 md:py-16">
        <div class="grid gap-8 md:grid-cols-[1.25fr_.75fr] md:items-end">
            <div>
                <span class="badge bg-white/10 text-white"><?php echo e($contentData['eyebrow'] ?? 'Start the course'); ?></span>
                <h2 class="display-serif mt-4 text-5xl leading-tight"><?php echo e($section->title ?? 'Give the capsule a fair 30-day trial.'); ?></h2>
                <p class="mt-4 max-w-2xl text-white/80"><?php echo e($section->subtitle ?? 'Start the course and receive your Gut Response Brief at Day 30.'); ?></p>
            </div>
            <div class="flex flex-wrap gap-3 md:justify-end">
                <a class="btn-primary !bg-white !text-[var(--ink)]" href="<?php echo e($contentData['primary_button_url'] ?? route('shop')); ?>"><?php echo e($contentData['primary_button_text'] ?? 'Shop Gut Reset'); ?></a>
                <a class="btn-secondary !border-white !text-white hover:bg-white/10" href="<?php echo e($contentData['secondary_button_url'] ?? '#fit'); ?>"><?php echo e($contentData['secondary_button_text'] ?? 'Check Your Fit'); ?></a>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\gutreset\resources\views/components/sections/cta.blade.php ENDPATH**/ ?>