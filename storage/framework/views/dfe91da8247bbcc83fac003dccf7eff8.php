<?php $__env->startSection('content'); ?>
<div class="p-6 md:p-10 max-w-5xl">
<a class="text-sm underline" href="<?php echo e(route('admin.products.index')); ?>">← Products</a>
<h1 class="serif mt-4 text-4xl"><?php echo e($product->exists?'Edit product':'Create product'); ?></h1>
<?php if($errors->any()): ?><div class="mt-5 rounded-xl bg-red-50 p-4 text-red-700"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php endif; ?>
<form method="POST" action="<?php echo e($product->exists?route('admin.products.update',$product):route('admin.products.store')); ?>" class="mt-8 space-y-6">
<?php echo csrf_field(); ?> <?php if($product->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
<div class="card grid gap-5 md:grid-cols-2">
<div><label>Name</label><input name="name" value="<?php echo e(old('name',$product->name)); ?>" required class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Base SKU</label><input name="base_sku" value="<?php echo e(old('base_sku',$product->base_sku)); ?>" required class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Slug</label><input name="slug" value="<?php echo e(old('slug',$product->slug)); ?>" class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Category</label><select name="category_id" class="mt-2 w-full rounded-xl border p-3"><option value="">None</option><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(old('category_id',$product->category_id)==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="md:col-span-2"><label>Short description</label><textarea name="short_description" class="mt-2 w-full rounded-xl border p-3"><?php echo e(old('short_description',$product->short_description)); ?></textarea></div>
<div class="md:col-span-2"><label>Description</label><textarea name="description" rows="5" class="mt-2 w-full rounded-xl border p-3"><?php echo e(old('description',$product->description)); ?></textarea></div>
<div><label>Status</label><select name="status" class="mt-2 w-full rounded-xl border p-3"><option value="draft">draft</option><option value="active" <?php if($product->status==='active'): echo 'selected'; endif; ?>>active</option><option value="inactive">inactive</option></select></div>
<label class="flex items-center gap-3 pt-7"><input type="checkbox" name="is_featured" value="1" <?php if(old('is_featured',$product->is_featured)): echo 'checked'; endif; ?>> Featured product</label>
</div>
<div class="card"><h2 class="text-xl font-bold">Primary variant</h2><div class="mt-5 grid gap-5 md:grid-cols-3">
<?php ($variant=$product->variants->first() ?? new \App\Models\ProductVariant()); ?>
<div><label>Variant name</label><input name="variant[name]" value="<?php echo e(old('variant.name',$variant->name)); ?>" required class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Variant SKU</label><input name="variant[sku]" value="<?php echo e(old('variant.sku',$variant->sku)); ?>" required class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Unit label</label><input name="variant[unit_label]" value="<?php echo e(old('variant.unit_label',$variant->unit_label)); ?>" class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Weight grams</label><input name="variant[weight_grams]" value="<?php echo e(old('variant.weight_grams',$variant->weight_grams)); ?>" class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Variant status</label><select name="variant[status]" class="mt-2 w-full rounded-xl border p-3"><option value="active">active</option><option value="inactive">inactive</option></select></div>
</div></div>
<div class="card"><h2 class="text-xl font-bold">Price</h2><div class="mt-5 grid gap-5 md:grid-cols-3">
<?php ($price=$variant->prices->first() ?? new \App\Models\ProductPrice()); ?>
<div><label>MRP</label><input type="number" step="0.01" name="price[mrp]" value="<?php echo e(old('price.mrp',$price->mrp)); ?>" required class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Selling price</label><input type="number" step="0.01" name="price[selling_price]" value="<?php echo e(old('price.selling_price',$price->selling_price)); ?>" required class="mt-2 w-full rounded-xl border p-3"></div>
<div><label>Tax %</label><input type="number" step="0.01" name="price[tax_percentage]" value="<?php echo e(old('price.tax_percentage',$price->tax_percentage)); ?>" class="mt-2 w-full rounded-xl border p-3"></div>
</div></div>
<button class="btn-primary"><?php echo e($product->exists?'Update product':'Create product'); ?></button>
</form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gutreset\resources\views/admin/products/form.blade.php ENDPATH**/ ?>