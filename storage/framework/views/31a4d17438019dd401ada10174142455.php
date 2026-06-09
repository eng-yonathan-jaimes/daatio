<?php $__env->startSection('title', __('messages.transactions')); ?>

<?php $__env->startSection('main'); ?>
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1><?php echo e(__('messages.transactions')); ?></h1>
            <p><?php echo e(__('messages.all_operations')); ?></p>
        </div>
        <a href="<?php echo e(route('print.transactions', $type ? ['type' => $type] : [])); ?>" target="_blank" class="btn btn-outline"><?php echo e(__('messages.print')); ?></a>
    </div>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        <a href="<?php echo e(route('transactions.index')); ?>" class="btn <?php echo e(!$type ? 'btn-primary' : 'btn-outline'); ?>" style="font-size:0.85rem;"><?php echo e(__('messages.all')); ?></a>
        <a href="<?php echo e(route('transactions.index', ['type' => 'Selling'])); ?>" class="btn <?php echo e($type === 'Selling' ? 'btn-primary' : 'btn-outline'); ?>" style="font-size:0.85rem;"><?php echo e(__('messages.purchases')); ?></a>
        <a href="<?php echo e(route('transactions.index', ['type' => 'Paying'])); ?>" class="btn <?php echo e($type === 'Paying' ? 'btn-primary' : 'btn-outline'); ?>" style="font-size:0.85rem;"><?php echo e(__('messages.payments')); ?></a>
        <a href="<?php echo e(route('transactions.index', ['type' => 'Buying'])); ?>" class="btn <?php echo e($type === 'Buying' ? 'btn-primary' : 'btn-outline'); ?>" style="font-size:0.85rem;"><?php echo e(__('messages.debts')); ?></a>
        <a href="<?php echo e(route('transactions.index', ['type' => 'Retriving'])); ?>" class="btn <?php echo e($type === 'Retriving' ? 'btn-primary' : 'btn-outline'); ?>" style="font-size:0.85rem;"><?php echo e(__('messages.deliveries')); ?></a>
        <a href="<?php echo e(route('transactions.index', ['type' => 'Settle'])); ?>" class="btn <?php echo e($type === 'Settle' ? 'btn-primary' : 'btn-outline'); ?>" style="font-size:0.85rem;"><?php echo e(__('messages.settlements')); ?></a>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><?php echo e(__('messages.date')); ?></th>
                    <th><?php echo e(__('messages.description')); ?></th>
                    <th><?php echo e(__('messages.customers')); ?></th>
                    <th><?php echo e(__('messages.type')); ?></th>
                    <th><?php echo e(__('messages.amount')); ?></th>
                    <th><?php echo e(__('messages.state')); ?></th>
                    <th><?php echo e(__('messages.by')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($tx->transaction_registration_date->format('M d, Y h:i A')); ?></td>
                        <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($tx->transaction_description ?? __('messages.none')); ?></td>
                        <td><a href="<?php echo e(route('customers.show', $tx->transaction_client_id)); ?>" style="color:var(--color-primary);text-decoration:none;"><?php echo e($tx->client?->client_name); ?> <?php echo e($tx->client?->client_last_name); ?></a></td>
                        <td><span class="badge badge-<?php echo e($tx->transaction_transaction === 'Paying' || $tx->transaction_transaction === 'Settle' ? 'success' : ($tx->transaction_transaction === 'Selling' ? 'error' : ($tx->transaction_transaction === 'Retriving' ? 'warning' : 'info'))); ?>"><?php echo e(__('messages.tx_' . strtolower($tx->transaction_transaction))); ?></span></td>
                        <td>$<?php echo e(number_format($tx->transaction_amount, 2)); ?></td>
                        <td><?php echo e(__('messages.' . strtolower($tx->transaction_state))); ?></td>
                        <td style="color:var(--color-text-muted);font-size:0.85rem;"><?php echo e($tx->user?->user_name ?? __('messages.none')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--color-text-muted);padding:2rem;"><?php echo e(__('messages.no_transactions_found')); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/transactions/index.blade.php ENDPATH**/ ?>