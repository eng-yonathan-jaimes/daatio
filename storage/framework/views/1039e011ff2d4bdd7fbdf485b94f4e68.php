<?php $__env->startSection('title', $client->client_name . ' ' . $client->client_last_name); ?>

<?php $__env->startSection('main'); ?>
<div class="page-header">
    <a href="<?php echo e(route('customers.index')); ?>" class="back-link">← <?php echo e(__('messages.customers')); ?></a>
    <h1><?php echo e($client->client_name); ?> <?php echo e($client->client_last_name); ?></h1>
</div>

<?php if(session('status')): ?>
    <div class="alert alert-success"><?php echo e(session('status')); ?></div>
<?php endif; ?>

<?php
$balanceAmount = $balance->client_state_amount ?? 0;
$balanceColor = $balanceAmount > 0 ? 'var(--color-primary)' : ($balanceAmount < 0 ? '#DC2626' : 'var(--color-text)');
$balanceLabel = $balanceAmount > 0 ? __('messages.favor') : ($balanceAmount < 0 ? __('messages.debit') : __('messages.settled'));
$balanceBadge = $balanceAmount > 0 ? 'info' : ($balanceAmount < 0 ? 'error' : 'success');
?>

<div class="stats-grid" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-label"><?php echo e(__('messages.current_balance')); ?></div>
        <div class="stat-value" style="color:<?php echo e($balanceColor); ?>;">
            $<?php echo e(number_format(abs($balanceAmount), 2)); ?>

        </div>
        <div class="stat-change">
            <span class="badge badge-<?php echo e($balanceBadge); ?>"><?php echo e($balanceLabel); ?></span>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div style="display:flex;gap:2rem;flex-wrap:wrap;">
        <div>
            <div class="stat-label" style="margin-bottom:0.25rem;"><?php echo e(__('messages.phone')); ?></div>
            <div style="font-weight:500;"><?php echo e($client->client_phone_number); ?></div>
        </div>
        <div>
            <div class="stat-label" style="margin-bottom:0.25rem;"><?php echo e(__('messages.email_address')); ?></div>
            <div style="font-weight:500;"><?php echo e($client->client_email ?: __('messages.none')); ?></div>
        </div>
        <div>
            <div class="stat-label" style="margin-bottom:0.25rem;"><?php echo e(__('messages.document')); ?></div>
            <div style="font-weight:500;">
                <?php if($client->client_document_number): ?>
                    <?php echo e($client->client_document_type); ?>: <?php echo e($client->client_document_number); ?>

                <?php else: ?>
                    <?php echo e(__('messages.none')); ?>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:1rem;"><?php echo e(__('messages.actions')); ?></div>
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        <a href="<?php echo e(route('transactions.create', ['customer' => $client->id, 'type' => 'Buying'])); ?>" class="btn btn-outline"><?php echo e(__('messages.add_debt')); ?></a>
        <a href="<?php echo e(route('transactions.create', ['customer' => $client->id, 'type' => 'Paying'])); ?>" class="btn btn-outline"><?php echo e(__('messages.register_payment')); ?></a>
        <a href="<?php echo e(route('transactions.create', ['customer' => $client->id, 'type' => 'Selling'])); ?>" class="btn btn-outline"><?php echo e(__('messages.metal_purchase')); ?></a>
        <a href="<?php echo e(route('transactions.create', ['customer' => $client->id, 'type' => 'Retriving'])); ?>" class="btn btn-outline"><?php echo e(__('messages.deliver_money')); ?></a>
        <?php if($balanceAmount != 0): ?>
            <a href="<?php echo e(route('transactions.create', ['customer' => $client->id, 'type' => 'Settle'])); ?>" class="btn btn-outline" style="color:#DC2626;border-color:#DC2626;"><?php echo e(__('messages.settle_account')); ?></a>
        <?php endif; ?>
        <a href="<?php echo e(route('print.customer', $client->id)); ?>" target="_blank" class="btn btn-outline" style="margin-left:auto;"><?php echo e(__('messages.print_history')); ?></a>
    </div>
</div>

<?php if($client->orders->count()): ?>
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:1rem;"><?php echo e(__('messages.orders')); ?></div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><?php echo e(__('messages.date')); ?></th>
                    <th><?php echo e(__('messages.state')); ?></th>
                    <th><?php echo e(__('messages.value')); ?></th>
                    <th><?php echo e(__('messages.items')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $client->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($order->client_order_registration_date->format('M d, Y')); ?></td>
                        <td>
                            <span class="badge badge-<?php echo e($order->client_order_state === 'Debit' ? 'error' : ($order->client_order_state === 'Favor' ? 'info' : 'success')); ?>">
                                <?php echo e($order->client_order_state === 'Debit' ? __('messages.debit') : ($order->client_order_state === 'Favor' ? __('messages.favor') : __('messages.settled'))); ?>

                            </span>
                        </td>
                        <td>$<?php echo e(number_format($order->client_order_value, 2)); ?></td>
                        <td>
                            <?php if($order->items->count()): ?>
                                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div style="font-size:0.85rem;color:var(--color-text-muted);">
                                        <?php echo e($item->product?->product_name ?? __('messages.unknown')); ?>

                                        <?php if($item->client_list_order_weight > 0): ?>
                                            — <?php echo e(rtrim(rtrim(number_format($item->client_list_order_weight, 4), '0'), '.')); ?>g
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <span style="color:var(--color-text-muted);"><?php echo e(__('messages.none')); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <span class="card-title"><?php echo e(__('messages.transaction_history')); ?></span>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><?php echo e(__('messages.date')); ?></th>
                    <th><?php echo e(__('messages.description')); ?></th>
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
                        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($tx->transaction_description ?? __('messages.none')); ?></td>
                        <td>
                            <span class="badge badge-<?php echo e($tx->transaction_transaction === 'Paying' || $tx->transaction_transaction === 'Settle' ? 'success' : ($tx->transaction_transaction === 'Selling' ? 'error' : ($tx->transaction_transaction === 'Retriving' ? 'warning' : 'info'))); ?>">
                                <?php echo e(__('messages.tx_' . strtolower($tx->transaction_transaction))); ?>

                            </span>
                        </td>
                        <td>$<?php echo e(number_format($tx->transaction_amount, 2)); ?></td>
                        <td><?php echo e(__('messages.' . strtolower($tx->transaction_state))); ?></td>
                        <td style="color:var(--color-text-muted);font-size:0.85rem;"><?php echo e($tx->user?->user_name ?? __('messages.none')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--color-text-muted);padding:2rem;">
                            <?php echo e(__('messages.no_transactions')); ?>

                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/customers/show.blade.php ENDPATH**/ ?>