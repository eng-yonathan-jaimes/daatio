<?php $__env->startSection('title', __('messages.stores')); ?>

<?php $__env->startSection('main'); ?>
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1><?php echo e(__('messages.stores')); ?></h1>
            <p><?php echo e(__('messages.manage_stores')); ?></p>
        </div>
        <a href="<?php echo e(route('stores.create')); ?>" class="btn btn-primary"><?php echo e(__('messages.new_store')); ?></a>
    </div>
</div>

<?php if(session('status')): ?>
    <div class="alert alert-success"><?php echo e(session('status')); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><?php echo e(__('messages.store_name')); ?></th>
                    <th><?php echo e(__('messages.store_type')); ?></th>
                    <th><?php echo e(__('messages.location_type')); ?></th>
                    <th><?php echo e(__('messages.status')); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="font-weight:500;"><?php echo e($store->store_name); ?></td>
                        <td><?php echo e($store->type?->store_type_description ?? __('messages.n_a')); ?></td>
                        <td><?php echo e($store->store_location === 'Physical' ? __('messages.physical') : ($store->store_location === 'Online' ? __('messages.online') : __('messages.both'))); ?></td>
                        <td><span class="badge badge-<?php echo e($store->store_active ? 'success' : 'error'); ?>"><?php echo e($store->store_active ? __('messages.active') : __('messages.inactive')); ?></span></td>
                        <td style="text-align:right;">
                            <a href="<?php echo e(route('stores.edit', $store->id)); ?>" class="btn btn-outline" style="padding:0.375rem 0.75rem;font-size:0.8rem;"><?php echo e(__('messages.edit')); ?></a>
                            <form method="POST" action="<?php echo e(route('stores.toggle', $store->id)); ?>" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-outline" style="padding:0.375rem 0.75rem;font-size:0.8rem;"><?php echo e($store->store_active ? __('messages.deactivate') : __('messages.activate')); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--color-text-muted);padding:2rem;"><?php echo e(__('messages.create_first_store')); ?>.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-title" style="margin-bottom:1rem;"><?php echo e(__('messages.store_types')); ?></div>
    <form method="POST" action="<?php echo e(route('stores.types.store')); ?>" style="display:flex;gap:0.5rem;margin-bottom:1rem;">
        <?php echo csrf_field(); ?>
        <input type="text" name="store_type_description" placeholder="e.g. Jewelry Shop" required style="flex:1;padding:0.625rem 0.75rem;border:1px solid var(--color-border);border-radius:8px;font-family:var(--font-body);font-size:0.9rem;">
        <button type="submit" class="btn btn-primary" style="font-size:0.85rem;"><?php echo e(__('messages.add_type')); ?></button>
    </form>
    <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="badge badge-info" style="font-size:0.85rem;padding:0.375rem 0.75rem;"><?php echo e($type->store_type_description); ?></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/stores/index.blade.php ENDPATH**/ ?>