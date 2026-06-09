<?php $__env->startSection('title', $store->store_name); ?>

<?php $__env->startSection('main'); ?>
<div class="page-header">
    <a href="<?php echo e(route('stores.index')); ?>" class="back-link">← <?php echo e(__('messages.stores')); ?></a>
    <h1><?php echo e($store->store_name); ?></h1>
</div>

<?php if($errors->any()): ?>
    <div class="alert alert-error"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<div class="card" style="max-width:560px;">
    <form method="POST" action="<?php echo e(route('stores.update', $store->id)); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="store_name"><?php echo e(__('messages.store_name')); ?> <span style="color:#DC2626;">*</span></label>
            <input type="text" id="store_name" name="store_name" value="<?php echo e(old('store_name', $store->store_name)); ?>" required autofocus>
        </div>
        <div class="form-group">
            <label for="store_address"><?php echo e(__('messages.address')); ?></label>
            <input type="text" id="store_address" name="store_address" value="<?php echo e(old('store_address', $store->store_address)); ?>" placeholder="123 Main St">
        </div>
        <div class="form-group">
            <label for="store_type_id"><?php echo e(__('messages.store_type')); ?> <span style="color:#DC2626;">*</span></label>
            <select id="store_type_id" name="store_type_id" style="width:100%;padding:0.75rem 1rem;border:1px solid var(--color-border);border-radius:8px;font-size:1rem;font-family:var(--font-body);color:var(--color-text);background:var(--color-card);">
                <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type->id); ?>" <?php echo e(old('store_type_id', $store->store_type_id) == $type->id ? 'selected' : ''); ?>><?php echo e($type->store_type_description); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group">
            <label for="store_location"><?php echo e(__('messages.location_type')); ?> <span style="color:#DC2626;">*</span></label>
            <select id="store_location" name="store_location" style="width:100%;padding:0.75rem 1rem;border:1px solid var(--color-border);border-radius:8px;font-size:1rem;font-family:var(--font-body);color:var(--color-text);background:var(--color-card);">
                <option value="Physical" <?php echo e(old('store_location', $store->store_location) == 'Physical' ? 'selected' : ''); ?>><?php echo e(__('messages.physical')); ?></option>
                <option value="Online" <?php echo e(old('store_location', $store->store_location) == 'Online' ? 'selected' : ''); ?>><?php echo e(__('messages.online')); ?></option>
                <option value="Both" <?php echo e(old('store_location', $store->store_location) == 'Both' ? 'selected' : ''); ?>><?php echo e(__('messages.both')); ?></option>
            </select>
        </div>
        <div style="display:flex;gap:1rem;margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary"><?php echo e(__('messages.save_changes')); ?></button>
            <a href="<?php echo e(route('stores.index')); ?>" class="btn btn-outline"><?php echo e(__('messages.cancel')); ?></a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/stores/edit.blade.php ENDPATH**/ ?>