<?php $__env->startSection('title', __('messages.customers')); ?>

<?php $__env->startSection('main'); ?>
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1><?php echo e(__('messages.customers')); ?></h1>
            <p><?php echo e(__('messages.manage_customers')); ?></p>
        </div>
        <div style="display:flex;gap:0.5rem;">
            <a href="<?php echo e(route('print.customers')); ?>" target="_blank" class="btn btn-outline"><?php echo e(__('messages.print')); ?></a>
            <a href="<?php echo e(route('customers.create')); ?>" class="btn btn-primary"><?php echo e(__('messages.new_customer')); ?></a>
        </div>
    </div>
</div>

<?php if(session('status')): ?>
    <div class="alert alert-success"><?php echo e(session('status')); ?></div>
<?php endif; ?>

<div class="card" style="margin-bottom:1.5rem;">
    <form method="GET" action="<?php echo e(route('customers.index')); ?>">
        <div style="display:flex;gap:0.5rem;">
            <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="<?php echo e(__('messages.search_by_name_or_phone')); ?>" style="flex:1;padding:0.625rem 0.75rem;border:1px solid var(--color-border);border-radius:8px;font-family:var(--font-body);font-size:0.9rem;">
            <button type="submit" class="btn btn-outline"><?php echo e(__('messages.search')); ?></button>
            <?php if($search): ?>
                <a href="<?php echo e(route('customers.index')); ?>" class="btn btn-outline"><?php echo e(__('messages.clear')); ?></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><?php echo e(__('messages.name')); ?></th>
                    <th><?php echo e(__('messages.phone')); ?></th>
                    <th><?php echo e(__('messages.registration_date')); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><a href="<?php echo e(route('customers.show', $customer->id)); ?>" style="color:var(--color-primary);text-decoration:none;font-weight:500;"><?php echo e($customer->client_name); ?> <?php echo e($customer->client_last_name); ?></a></td>
                        <td><?php echo e($customer->client_phone_number); ?></td>
                        <td><?php echo e($customer->client_registration_date->format('M d, Y')); ?></td>
                        <td style="text-align:right;">
                            <a href="<?php echo e(route('customers.show', $customer->id)); ?>" class="btn btn-outline" style="padding:0.375rem 0.75rem;font-size:0.8rem;"><?php echo e(__('messages.view')); ?></a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--color-text-muted);padding:2rem;">
                            <?php if($search): ?>
                                <?php echo e(__('messages.no_customers_found', ['search' => $search])); ?>

                            <?php else: ?>
                                <?php echo e(__('messages.no_customers_yet')); ?> <a href="<?php echo e(route('customers.create')); ?>"><?php echo e(__('messages.create_first_customer')); ?></a>.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/customers/index.blade.php ENDPATH**/ ?>