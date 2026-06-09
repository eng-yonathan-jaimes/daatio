<?php $__env->startSection('title', $product->product_name); ?>

<?php $__env->startSection('main'); ?>
<div class="page-header">
    <a href="<?php echo e(route('products.index')); ?>" class="back-link">← <?php echo e(__('messages.products')); ?></a>
    <h1><?php echo e($product->product_name); ?></h1>
</div>

<?php if($errors->any()): ?>
    <div class="alert alert-error"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<div class="card" style="max-width:560px;">
    <form method="POST" action="<?php echo e(route('products.update', $product->id)); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="product_name"><?php echo e(__('messages.product_name')); ?> <span style="color:#DC2626;">*</span></label>
            <input type="text" id="product_name" name="product_name" value="<?php echo e(old('product_name', $product->product_name)); ?>" required autofocus>
        </div>
        <div class="form-group">
            <label for="product_weight"><?php echo e(__('messages.metal_weight')); ?></label>
            <input type="number" step="0.0001" id="product_weight" name="product_weight" value="<?php echo e(old('product_weight', $product->product_weight)); ?>" placeholder="0.0000">
        </div>
        <div class="form-group">
            <label for="product_value"><?php echo e(__('messages.initial_value')); ?></label>
            <div style="display:flex;gap:0.5rem;">
                <input type="number" step="0.0001" id="product_value" name="product_value" value="<?php echo e(old('product_value', $product->product_value)); ?>" placeholder="0.0000" style="flex:1;">
                <select name="product_currency" class="country-select" style="width:120px;">
                    <?php $__currentLoopData = ['USD', 'COP', 'EUR', 'VES', 'MXN', 'PEN', 'ARS', 'CLP', 'BRL']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cur); ?>" <?php echo e(old('product_currency', $product->product_currency ?? 'USD') === $cur ? 'selected' : ''); ?>><?php echo e($cur); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="product_state"><?php echo e(__('messages.product_state')); ?></label>
            <select id="product_state" name="product_state" style="width:100%;padding:0.75rem 1rem;border:1px solid var(--color-border);border-radius:8px;font-size:1rem;font-family:var(--font-body);color:var(--color-text);background:var(--color-card);">
                <option value="In Stock" <?php echo e(old('product_state', $product->product_state) === 'In Stock' ? 'selected' : ''); ?>><?php echo e(__('messages.in_stock')); ?></option>
                <option value="Out of Stock" <?php echo e(old('product_state', $product->product_state) === 'Out of Stock' ? 'selected' : ''); ?>><?php echo e(__('messages.out_of_stock')); ?></option>
            </select>
        </div>
        <div style="display:flex;gap:1rem;margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary"><?php echo e(__('messages.save_changes')); ?></button>
            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-outline"><?php echo e(__('messages.cancel')); ?></a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/products/edit.blade.php ENDPATH**/ ?>