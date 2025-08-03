<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => null, 'subtitle' => null, 'padding' => 'p-6']));

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

foreach (array_filter((['title' => null, 'subtitle' => null, 'padding' => 'p-6']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-all duration-200'])); ?>>
    <?php if($title || $subtitle): ?>
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
            <?php if($title): ?>
                <h3 class="text-lg font-semibold text-slate-900"><?php echo e($title); ?></h3>
            <?php endif; ?>
            <?php if($subtitle): ?>
                <p class="text-sm text-slate-600 mt-1"><?php echo e($subtitle); ?></p>
            <?php endif; ?>
        </div>
        <div class="<?php echo e($padding); ?>">
            <?php echo e($slot); ?>

        </div>
    <?php else: ?>
        <div class="<?php echo e($padding); ?>">
            <?php echo e($slot); ?>

        </div>
    <?php endif; ?>
</div>
<?php /**PATH F:\APPS\altezza\altezza\altezza\resources\views/components/card.blade.php ENDPATH**/ ?>