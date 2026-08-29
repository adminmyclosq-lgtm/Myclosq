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
    $eyebrow = $contentData['eyebrow'] ?? 'What you receive';
    $title = $section->title ?? 'Everything needed to start correctly and return easily.';
    $subtitle = $section->subtitle ?? "The Capsule Bottle — discreetly labelled, tightly designed for one capsule daily.\nCourse Companion — a concise activation card and course guide.\nGuided Course Access — permanent bottle QR to start or return to your guided course.";
    $listItems = explode("\n", $subtitle);
?>
<section class="<?php echo e($bgColor); ?>" style="padding-top: <?php echo e($topSpace); ?>; padding-bottom: <?php echo e($bottomSpace); ?>;" id="section-<?php echo e($section->id ?? 'new'); ?>">
    <div class="section grid gap-10 md:grid-cols-2 md:items-center">
        <div>
            <?php if($eyebrow): ?><span class="badge"><?php echo e($eyebrow); ?></span><?php endif; ?>
            <h2 class="display-serif mt-4 text-4xl leading-tight"><?php echo e($title); ?></h2>
            <?php if(count($listItems) > 0): ?>
            <ul class="mt-7 space-y-4 text-stone-700">
                <?php $__currentLoopData = $listItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(trim($item)): ?> <li><?php echo e($item); ?></li> <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php endif; ?>
            <?php if(isset($contentData['primary_button_text']) && $contentData['primary_button_text']): ?>
                <div class="mt-6 text-sm uppercase tracking-[.22em] text-stone-500"><?php echo e($contentData['primary_button_text']); ?></div>
            <?php else: ?>
                <div class="mt-6 text-sm uppercase tracking-[.22em] text-stone-500">Take 30. Spend 15. Know your gut.</div>
            <?php endif; ?>
        </div>
        <div class="grid gap-4">
            <?php if($section && $section->media): ?>
                <div class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                    <img src="<?php echo e($section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url); ?>" alt="<?php echo e($section->media->file_name); ?>" class="h-full w-full object-cover">
                </div>
            <?php else: ?>
                <div class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                    <img src="https://guided-gut-reset-lovable-app.lovable.app/assets/course-kit-DtVPc2tT.jpg" alt="Open 30-day course kit" class="h-full w-full object-cover">
                </div>
                <div class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                    <img src="https://guided-gut-reset-lovable-app.lovable.app/assets/hero-product-CftTmpl1.jpg" alt="A person taking their capsule" class="h-full w-full object-cover">
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\gutreset\resources\views/components/sections/showcase.blade.php ENDPATH**/ ?>