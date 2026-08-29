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
    
    $badge = $content['badge'] ?? 'The problem';
    $cards = $content['cards'] ?? ['No clear starting point', 'Real-life disruptions get read as failure', 'The most recent moment becomes the impression'];
    
    $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "padding-top: 80px;";
    $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "padding-bottom: 80px;";
    
    if (!empty($settings['hidden'])) return;
?>
<section class="section" style="<?php echo e($topStyle); ?> <?php echo e($bottomStyle); ?>">
    <div class="grid gap-12 md:grid-cols-2 md:items-start">
        <div>
            <span class="badge"><?php echo e($badge); ?></span>
            <h2 class="display-serif mt-4 text-4xl leading-tight"><?php echo $section->title ?? 'Most gut resets leave you guessing.'; ?></h2>
        </div>
        <div class="space-y-4 leading-7 text-stone-600">
            <p><?php echo e($section->subtitle ?? 'A good day feels encouraging. A bad day feels like the capsule is not working. Without a fair 30-day trial, most decisions are made on impression.'); ?></p>
            <div class="grid gap-3 sm:grid-cols-3">
                <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card text-sm"><?php echo e(is_array($card) ? ($card['title'] ?? '') : $card); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\gutreset\resources\views/components/sections/problem.blade.php ENDPATH**/ ?>