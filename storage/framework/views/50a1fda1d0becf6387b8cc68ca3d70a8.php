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
    
    $badge = $content['badge'] ?? 'Learn';
    $cards = $content['cards'] ?? [
        ['title' => 'Methodology', 'text' => 'Why supplements are easy to start and difficult to judge.'],
        ['title' => 'Medical Context', 'text' => 'What digestive symptoms need medical attention.'],
        ['title' => 'Trial Design', 'text' => 'What a fair 30-day product trial looks like.']
    ];
    
    $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "";
    $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "";
    
    if (!empty($settings['hidden'])) return;
?>
<section class="section <?php echo e($settings['bg_color'] ?? ''); ?>" id="learn" style="<?php echo e($topStyle); ?> <?php echo e($bottomStyle); ?>">
    <div class="max-w-3xl">
        <span class="badge"><?php echo e($badge); ?></span>
        <h2 class="display-serif mt-4 text-4xl leading-tight"><?php echo $section->title ?? 'Understand your gut — and how to judge a trial.'; ?></h2>
    </div>
    <div class="mt-10 grid gap-5 md:grid-cols-3">
        <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('shop')); ?>" class="group overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <div class="aspect-[4/3] overflow-hidden bg-stone-100">
                    <img src="https://guided-gut-reset-lovable-app.lovable.app/assets/bottle-capsules-COsDFlLa.jpg" alt="Guided wellness article image" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                </div>
                <div class="p-5">
                    <div class="text-xs uppercase tracking-[.26em] text-stone-400"><?php echo e(is_array($item) ? ($item['title'] ?? '') : $item); ?></div>
                    <div class="mt-2 text-lg font-semibold"><?php echo e(is_array($item) ? ($item['text'] ?? '') : ''); ?></div>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\gutreset\resources\views/components/sections/learn.blade.php ENDPATH**/ ?>