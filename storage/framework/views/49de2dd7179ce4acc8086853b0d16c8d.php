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
    $bgColor = $settings['bg_color'] ?? 'bg-[var(--cream)]';
    $items = isset($contentData['items']) && is_array($contentData['items']) ? $contentData['items'] : [];
?>
<section class="<?php echo e($bgColor); ?>" style="padding-top: <?php echo e($topSpace); ?>; padding-bottom: <?php echo e($bottomSpace); ?>;" id="section-<?php echo e($section->id ?? 'new'); ?>">
    <div class="section">
        <?php if($contentData['eyebrow'] ?? ''): ?>
            <span class="badge"><?php echo e($contentData['eyebrow']); ?></span>
        <?php else: ?>
            <span class="badge">Check your fit</span>
        <?php endif; ?>
        <h2 class="display-serif mt-4 text-4xl leading-tight"><?php echo e($section->title ?? 'Is this likely to be right for you?'); ?></h2>
        <div class="mt-10 grid gap-6 md:grid-cols-2">
            <?php if(count($items) > 0): ?>
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card">
                        <h3 class="font-bold"><?php echo e($item['title'] ?? ''); ?></h3>
                        <?php $lines = explode("\n", $item['description'] ?? ''); ?>
                        <ul class="mt-4 space-y-3 text-stone-600">
                            <?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(trim($line)): ?> <li>· <?php echo e($line); ?></li> <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="card">
                    <h3 class="font-bold">May be relevant</h3>
                    <ul class="mt-4 space-y-3 text-stone-600">
                        <li>· Seeking structured gut-health support</li>
                        <li>· Struggle with supplement consistency</li>
                        <li>· Want a more informed read</li>
                        <li>· Value data-driven personal insight</li>
                    </ul>
                </div>
                <div class="card">
                    <h3 class="font-bold">Speak to a doctor first</h3>
                    <ul class="mt-4 space-y-3 text-stone-600">
                        <li>· Pregnancy or breast-feeding</li>
                        <li>· Diagnosed gastrointestinal condition</li>
                        <li>· Currently taking prescription medication</li>
                        <li>· Severe, sudden or new abdominal symptoms</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\gutreset\resources\views/components/sections/feature_cards.blade.php ENDPATH**/ ?>