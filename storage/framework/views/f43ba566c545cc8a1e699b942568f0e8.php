<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['section']));

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

foreach (array_filter((['section']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $content = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
    if (!is_array($content)) $content = [];
    $settings = $content['settings'] ?? [];
    
    $badge = $content['badge'] ?? 'Transparency';
    $cards = $content['cards'] ?? [
        ['num' => '01', 'title' => 'Formulation', 'text' => 'Every ingredient is disclosed with its intended role and rationale.'],
        ['num' => '02', 'title' => 'Quality', 'text' => 'How quality is tested and where the limits of testing are.'],
        ['num' => '03', 'title' => 'Guidance', 'text' => 'Safety information is presented up-front so the product is used well.']
    ];
    
    $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "padding-top: 80px;";
    $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "padding-bottom: 80px;";
    
    if (!empty($settings['hidden'])) return;
?>
<section class="section" id="standards" style="<?php echo e($topStyle); ?> <?php echo e($bottomStyle); ?>">
    <div class="max-w-3xl">
        <span class="badge"><?php echo e($badge); ?></span>
        <h2 class="display-serif mt-4 text-4xl leading-tight"><?php echo $section->title ?? 'Standards you should be able to inspect.'; ?></h2>
        <p class="mt-4 text-stone-600"><?php echo e($section->subtitle ?? 'Transparency is not a feature; it is the foundation of trust.'); ?></p>
        <a class="mt-6 inline-flex text-sm font-semibold text-[var(--ink)] underline decoration-stone-400 underline-offset-4" href="#learn">Explore standards</a>
    </div>
    <div class="mt-10 grid gap-5 md:grid-cols-3">
        <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border-t border-stone-300 pt-5">
                <div class="text-sm text-stone-500"><?php echo e($item['num'] ?? '01'); ?></div>
                <h3 class="mt-2 text-xl font-bold"><?php echo e($item['title'] ?? ''); ?></h3>
                <p class="mt-3 text-sm leading-6 text-stone-600"><?php echo e($item['text'] ?? ''); ?></p>
                <div class="mt-4 text-sm font-semibold text-[var(--ink)]">Learn More →</div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\gutreset\resources\views/components/sections/standards.blade.php ENDPATH**/ ?>