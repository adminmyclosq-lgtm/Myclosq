<?php $__env->startSection('content'); ?>

<?php if(isset($page) && $page->sections && $page->sections->count() > 0): ?>
    <!-- DYNAMIC CMS SECTION RENDERER -->
    <?php $__currentLoopData = $page->sections->sortBy('sort_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $settings = is_string($section->content) ? (json_decode($section->content, true)['settings'] ?? []) : [];
            if (!empty($settings['hidden_desktop']) && !empty($settings['hidden_mobile'])) continue; // simple skip
        ?>
        
        <?php if($section->section_type === 'hero'): ?>
            <?php if (isset($component)) { $__componentOriginal7d77bb759cf09fb7609ab7d50dcb0764 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7d77bb759cf09fb7609ab7d50dcb0764 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sections.hero','data' => ['section' => $section]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sections.hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($section)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7d77bb759cf09fb7609ab7d50dcb0764)): ?>
<?php $attributes = $__attributesOriginal7d77bb759cf09fb7609ab7d50dcb0764; ?>
<?php unset($__attributesOriginal7d77bb759cf09fb7609ab7d50dcb0764); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7d77bb759cf09fb7609ab7d50dcb0764)): ?>
<?php $component = $__componentOriginal7d77bb759cf09fb7609ab7d50dcb0764; ?>
<?php unset($__componentOriginal7d77bb759cf09fb7609ab7d50dcb0764); ?>
<?php endif; ?>
        <?php elseif($section->section_type === 'problem'): ?>
            <?php if (isset($component)) { $__componentOriginal2a2c15df1ee241e233f84393f0374459 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2a2c15df1ee241e233f84393f0374459 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sections.problem','data' => ['section' => $section]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sections.problem'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($section)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2a2c15df1ee241e233f84393f0374459)): ?>
<?php $attributes = $__attributesOriginal2a2c15df1ee241e233f84393f0374459; ?>
<?php unset($__attributesOriginal2a2c15df1ee241e233f84393f0374459); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2a2c15df1ee241e233f84393f0374459)): ?>
<?php $component = $__componentOriginal2a2c15df1ee241e233f84393f0374459; ?>
<?php unset($__componentOriginal2a2c15df1ee241e233f84393f0374459); ?>
<?php endif; ?>
        <?php elseif($section->section_type === 'features'): ?>
            <?php if (isset($component)) { $__componentOriginal6ba66857502b2c621a48ed5da8100e7b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ba66857502b2c621a48ed5da8100e7b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sections.features','data' => ['section' => $section]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sections.features'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($section)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ba66857502b2c621a48ed5da8100e7b)): ?>
<?php $attributes = $__attributesOriginal6ba66857502b2c621a48ed5da8100e7b; ?>
<?php unset($__attributesOriginal6ba66857502b2c621a48ed5da8100e7b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ba66857502b2c621a48ed5da8100e7b)): ?>
<?php $component = $__componentOriginal6ba66857502b2c621a48ed5da8100e7b; ?>
<?php unset($__componentOriginal6ba66857502b2c621a48ed5da8100e7b); ?>
<?php endif; ?>
        <?php elseif($section->section_type === 'standards'): ?>
            <?php if (isset($component)) { $__componentOriginal21c0b9be714bb280fc0aa13593fb07ed = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal21c0b9be714bb280fc0aa13593fb07ed = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sections.standards','data' => ['section' => $section]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sections.standards'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($section)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal21c0b9be714bb280fc0aa13593fb07ed)): ?>
<?php $attributes = $__attributesOriginal21c0b9be714bb280fc0aa13593fb07ed; ?>
<?php unset($__attributesOriginal21c0b9be714bb280fc0aa13593fb07ed); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal21c0b9be714bb280fc0aa13593fb07ed)): ?>
<?php $component = $__componentOriginal21c0b9be714bb280fc0aa13593fb07ed; ?>
<?php unset($__componentOriginal21c0b9be714bb280fc0aa13593fb07ed); ?>
<?php endif; ?>
        <?php elseif($section->section_type === 'showcase'): ?>
            <?php if (isset($component)) { $__componentOriginal83fe439523b0323b8291d342794cad0f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal83fe439523b0323b8291d342794cad0f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sections.showcase','data' => ['section' => $section]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sections.showcase'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($section)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal83fe439523b0323b8291d342794cad0f)): ?>
<?php $attributes = $__attributesOriginal83fe439523b0323b8291d342794cad0f; ?>
<?php unset($__attributesOriginal83fe439523b0323b8291d342794cad0f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal83fe439523b0323b8291d342794cad0f)): ?>
<?php $component = $__componentOriginal83fe439523b0323b8291d342794cad0f; ?>
<?php unset($__componentOriginal83fe439523b0323b8291d342794cad0f); ?>
<?php endif; ?>
        <?php elseif($section->section_type === 'testimonials'): ?>
            <?php if (isset($component)) { $__componentOriginalbc7b4f0c2c9047f644458dd33f7c67ae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbc7b4f0c2c9047f644458dd33f7c67ae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sections.testimonials','data' => ['section' => $section]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sections.testimonials'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($section)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbc7b4f0c2c9047f644458dd33f7c67ae)): ?>
<?php $attributes = $__attributesOriginalbc7b4f0c2c9047f644458dd33f7c67ae; ?>
<?php unset($__attributesOriginalbc7b4f0c2c9047f644458dd33f7c67ae); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbc7b4f0c2c9047f644458dd33f7c67ae)): ?>
<?php $component = $__componentOriginalbc7b4f0c2c9047f644458dd33f7c67ae; ?>
<?php unset($__componentOriginalbc7b4f0c2c9047f644458dd33f7c67ae); ?>
<?php endif; ?>
        <?php elseif($section->section_type === 'feature_cards'): ?>
            <?php if (isset($component)) { $__componentOriginal69d829e65cf8e181f65eb231e34bcc7b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69d829e65cf8e181f65eb231e34bcc7b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sections.feature_cards','data' => ['section' => $section]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sections.feature_cards'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($section)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69d829e65cf8e181f65eb231e34bcc7b)): ?>
<?php $attributes = $__attributesOriginal69d829e65cf8e181f65eb231e34bcc7b; ?>
<?php unset($__attributesOriginal69d829e65cf8e181f65eb231e34bcc7b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69d829e65cf8e181f65eb231e34bcc7b)): ?>
<?php $component = $__componentOriginal69d829e65cf8e181f65eb231e34bcc7b; ?>
<?php unset($__componentOriginal69d829e65cf8e181f65eb231e34bcc7b); ?>
<?php endif; ?>
        <?php elseif($section->section_type === 'learn'): ?>
            <?php if (isset($component)) { $__componentOriginalea13faeac394c70a34667d63accf737a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalea13faeac394c70a34667d63accf737a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sections.learn','data' => ['section' => $section]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sections.learn'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($section)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalea13faeac394c70a34667d63accf737a)): ?>
<?php $attributes = $__attributesOriginalea13faeac394c70a34667d63accf737a; ?>
<?php unset($__attributesOriginalea13faeac394c70a34667d63accf737a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalea13faeac394c70a34667d63accf737a)): ?>
<?php $component = $__componentOriginalea13faeac394c70a34667d63accf737a; ?>
<?php unset($__componentOriginalea13faeac394c70a34667d63accf737a); ?>
<?php endif; ?>
        <?php elseif($section->section_type === 'faq'): ?>
            <?php if (isset($component)) { $__componentOriginal871f819925592cac5093a9ea5abc64c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal871f819925592cac5093a9ea5abc64c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sections.faq','data' => ['section' => $section]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sections.faq'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($section)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal871f819925592cac5093a9ea5abc64c2)): ?>
<?php $attributes = $__attributesOriginal871f819925592cac5093a9ea5abc64c2; ?>
<?php unset($__attributesOriginal871f819925592cac5093a9ea5abc64c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal871f819925592cac5093a9ea5abc64c2)): ?>
<?php $component = $__componentOriginal871f819925592cac5093a9ea5abc64c2; ?>
<?php unset($__componentOriginal871f819925592cac5093a9ea5abc64c2); ?>
<?php endif; ?>
        <?php elseif($section->section_type === 'cta'): ?>
            <?php if (isset($component)) { $__componentOriginal3059bb6f234c7f6929934634de0e93cd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3059bb6f234c7f6929934634de0e93cd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sections.cta','data' => ['section' => $section]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sections.cta'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['section' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($section)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3059bb6f234c7f6929934634de0e93cd)): ?>
<?php $attributes = $__attributesOriginal3059bb6f234c7f6929934634de0e93cd; ?>
<?php unset($__attributesOriginal3059bb6f234c7f6929934634de0e93cd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3059bb6f234c7f6929934634de0e93cd)): ?>
<?php $component = $__componentOriginal3059bb6f234c7f6929934634de0e93cd; ?>
<?php unset($__componentOriginal3059bb6f234c7f6929934634de0e93cd); ?>
<?php endif; ?>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gutreset\resources\views/home.blade.php ENDPATH**/ ?>