<?php $__env->startSection('title', __('messages.products')); ?>

<?php $__env->startSection('main'); ?>
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1><?php echo e(__('messages.products')); ?></h1>
            <p><?php echo e(__('messages.manage_products')); ?></p>
        </div>
        <a href="<?php echo e(route('products.create')); ?>" class="btn btn-primary"><?php echo e(__('messages.new_product')); ?></a>
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
                    <th><?php echo e(__('messages.name')); ?></th>
                    <th><?php echo e(__('messages.metal_weight')); ?></th>
                    <th><?php echo e(__('messages.initial_value')); ?></th>
                    <th><?php echo e(__('messages.product_state')); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="font-weight:500;"><?php echo e($product->product_name); ?></td>
                        <td><?php echo e($product->product_weight > 0 ? rtrim(rtrim(number_format($product->product_weight, 4), '0'), '.') . 'g' : __('messages.none')); ?></td>
                        <td><?php echo e($product->product_value > 0 ? '$' . number_format($product->product_value, 2) . ' ' . ($product->product_currency ?? 'USD') : __('messages.none')); ?></td>
                        <td>
                            <span class="badge badge-<?php echo e($product->product_state === 'In Stock' ? 'success' : 'warning'); ?>">
                                <?php echo e($product->product_state === 'In Stock' ? __('messages.in_stock') : __('messages.out_of_stock')); ?>

                            </span>
                        </td>
                        <td style="text-align:right;">
                            <a href="<?php echo e(route('products.edit', $product->id)); ?>" class="btn btn-outline" style="padding:0.375rem 0.75rem;font-size:0.8rem;"><?php echo e(__('messages.edit')); ?></a>
                            <form method="POST" action="<?php echo e(route('products.destroy', $product->id)); ?>" style="display:inline;" onsubmit="return confirm('<?php echo e(__('messages.delete_confirm')); ?>')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-outline" style="padding:0.375rem 0.75rem;font-size:0.8rem;color:#DC2626;"><?php echo e(__('messages.delete')); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--color-text-muted);padding:2rem;">
                            <?php echo e(__('messages.add_first_product')); ?>.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/products/index.blade.php ENDPATH**/ ?>