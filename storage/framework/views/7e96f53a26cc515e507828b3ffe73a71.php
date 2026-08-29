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
    
    $badge = $content['badge'] ?? 'Standout experience';
    $cards = $content['cards'] ?? [
        ['title' => 'The daily capsule', 'text' => 'A gut-support supplement designed to support baseline gut health, taken once daily.'],
        ['title' => 'The guided experience', 'text' => 'Eight components that guide you through a 30-day process, observing signals and coming to an honest read.'],
        ['title' => 'Take it daily.', 'text' => 'Check in lightly. Know what changed.']
    ];
    
    $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "padding-top: 80px;";
    $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "padding-bottom: 80px;";
    
    if (!empty($settings['hidden'])) return;
?>
<section class="bg-[var(--cream)]" id="how-it-works" style="<?php echo e($topStyle); ?> <?php echo e($bottomStyle); ?>">
    <div class="section">
        <div class="max-w-3xl">
            <span class="badge"><?php echo e($badge); ?></span>
            <h2 class="display-serif mt-4 text-4xl leading-tight"><?php echo $section->title ?? 'The capsule creates the response.<br>The guided course helps make it readable.'; ?></h2>
        </div>
        <div class="mt-12 grid gap-6 md:grid-cols-3">
            <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card">
                    <div class="text-sm font-semibold"><?php echo e($card['title'] ?? ''); ?></div>
                    <p class="mt-3 text-sm leading-6 text-stone-600"><?php echo e($card['text'] ?? ''); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\gutreset\resources\views/components/sections/features.blade.php ENDPATH**/ ?>