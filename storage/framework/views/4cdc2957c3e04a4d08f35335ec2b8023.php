<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('main'); ?>
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <p style="font-size:1.25rem;color:var(--color-text);margin-bottom:0.25rem;"><?php echo e(__('messages.welcome_back_user', ['name' => auth()->user()->name])); ?></p>
        </div>
        <a href="<?php echo e(route('transactions.quick')); ?>" class="btn btn-primary" style="font-size:1rem;padding:0.75rem 1.5rem;"><?php echo e(__('messages.new_transaction')); ?></a>
    </div>
</div>

<?php if($subscription): ?>
<div class="alert alert-<?php echo e($subscription->isExpired() ? 'error' : ($subscription->daysRemaining() <= 7 ? 'error' : 'info')); ?>" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
    <div>
        <strong><?php echo e($subscription->subscription->subscription_type ?? 'N/A'); ?></strong> plan
        &mdash;
        <?php if($subscription->isExpired()): ?>
            <span style="color:#991B1B;">Expired</span>
        <?php else: ?>
            <?php echo e($subscription->daysRemaining()); ?> day<?php echo e($subscription->daysRemaining() !== 1 ? 's' : ''); ?> remaining
        <?php endif; ?>
    </div>
    <a href="<?php echo e(route('subscription.index')); ?>" class="btn <?php echo e($subscription->isExpired() || $subscription->daysRemaining() <= 7 ? 'btn-primary' : 'btn-outline'); ?>" style="font-size:0.85rem;">
        <?php echo e($subscription->isExpired() ? 'Renew Now' : 'Manage'); ?>

    </a>
</div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label"><?php echo e(__('messages.total_customers')); ?></div>
        <div class="stat-value"><?php echo e($totalCustomers); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><?php echo e(__('messages.total_debt')); ?></div>
        <div class="stat-value">$<?php echo e(number_format($totalDebt, 2)); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><?php echo e(__('messages.total_paid')); ?></div>
        <div class="stat-value">$<?php echo e(number_format($totalPaid, 2)); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><?php echo e(__('messages.outstanding_balance')); ?></div>
        <div class="stat-value">$<?php echo e(number_format($outstandingBalance, 2)); ?></div>
    </div>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <span class="card-title"><?php echo e(__('messages.customers_outstanding_debt')); ?></span>
    </div>
    <form method="GET" action="<?php echo e(route('dashboard')); ?>" style="margin-bottom:1rem;">
        <div style="display:flex;gap:0.5rem;">
            <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="<?php echo e(__('messages.search_by_name_or_phone')); ?>" style="flex:1;padding:0.625rem 0.75rem;border:1px solid var(--color-border);border-radius:8px;font-family:var(--font-body);font-size:0.9rem;">
            <button type="submit" class="btn btn-outline"><?php echo e(__('messages.search')); ?></button>
            <?php if($search): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline"><?php echo e(__('messages.clear')); ?></a>
            <?php endif; ?>
        </div>
    </form>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><?php echo e(__('messages.name')); ?></th>
                    <th><?php echo e(__('messages.phone')); ?></th>
                    <th><?php echo e(__('messages.amount_owed')); ?></th>
                    <th><?php echo e(__('messages.last_transaction')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $debtors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $debtState = $client->states->first(); ?>
                    <tr>
                        <td><?php echo e($client->client_name); ?> <?php echo e($client->client_last_name); ?></td>
                        <td><?php echo e($client->client_phone_number); ?></td>
                        <td>$<?php echo e(number_format(abs($debtState->client_state_amount ?? 0), 2)); ?></td>
                        <td><?php echo e($debtState->client_state_last_transaction_date?->format('M d, Y') ?? __('messages.none')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--color-text-muted);padding:2rem;">
                            <?php echo e($search ? __('messages.no_customers_found', ['search' => $search]) : __('messages.no_customers_debt')); ?>

                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/dashboard/index.blade.php ENDPATH**/ ?>