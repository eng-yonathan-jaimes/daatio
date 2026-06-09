<?php $__env->startSection('tab-content'); ?>
<?php
$activeSubscription = \Modules\Subscriptions\app\Models\UserSubscription::where('user_subscription_user_id', auth()->id())
    ->with('subscription', 'payments')
    ->latest('user_subscription_start_date')
    ->first();
$plans = \Modules\Subscriptions\app\Models\Subscription::where('subscription_enabled', true)
    ->where('subscription_type', '!=', 'Trial')
    ->get();
?>

<div class="stats-grid" style="margin-bottom:2rem;">
    <div class="stat-card">
        <div class="stat-label"><?php echo e(__('messages.current_plan')); ?></div>
        <div class="stat-value" style="font-size:1.5rem;"><?php echo e($activeSubscription?->subscription?->subscription_type ?? __('messages.none')); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><?php echo e(__('messages.status')); ?></div>
        <div class="stat-value" style="font-size:1.25rem;">
            <span class="badge badge-<?php echo e(($activeSubscription?->user_subscription_status ?? 'Expired') === 'Active' ? 'success' : 'error'); ?>"><?php echo e($activeSubscription?->user_subscription_status ?? __('messages.expired')); ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><?php echo e(__('messages.days_remaining')); ?></div>
        <div class="stat-value" style="font-size:1.5rem;color:<?php echo e(($activeSubscription?->daysRemaining() ?? 0) <= 7 ? '#DC2626' : 'var(--color-text)'); ?>;"><?php echo e($activeSubscription?->daysRemaining() ?? 0); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><?php echo e(__('messages.price')); ?></div>
        <div class="stat-value" style="font-size:1.25rem;">$<?php echo e(number_format($activeSubscription?->user_subscription_value ?? 0, 2)); ?></div>
    </div>
</div>

<?php if($activeSubscription): ?>
<div class="card" style="margin-bottom:2rem;">
    <div class="card-title" style="margin-bottom:1rem;"><?php echo e(__('messages.plan_details')); ?></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;">
        <div><div class="stat-label"><?php echo e(__('messages.started')); ?></div><div style="font-weight:500;"><?php echo e($activeSubscription->user_subscription_start_date->format('M d, Y')); ?></div></div>
        <div><div class="stat-label"><?php echo e(__('messages.expires')); ?></div><div style="font-weight:500;"><?php echo e($activeSubscription->user_subscription_end_date->format('M d, Y')); ?></div></div>
        <div><div class="stat-label"><?php echo e(__('messages.max_stores')); ?></div><div style="font-weight:500;"><?php echo e($activeSubscription->subscription->subscription_max_stores ?? 1); ?></div></div>
    </div>
</div>
<?php endif; ?>

<?php if($activeSubscription?->payments->count()): ?>
<div class="card" style="margin-bottom:2rem;">
    <div class="card-title" style="margin-bottom:1rem;"><?php echo e(__('messages.payment_history')); ?></div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><?php echo e(__('messages.date')); ?></th>
                    <th><?php echo e(__('messages.amount')); ?></th>
                    <th><?php echo e(__('messages.method')); ?></th>
                    <th><?php echo e(__('messages.status')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $activeSubscription->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($payment->subscription_payment_date->format('M d, Y')); ?></td>
                        <td>$<?php echo e(number_format($payment->subscription_payment_amount, 2)); ?></td>
                        <td><?php echo e($payment->subscription_payment_method); ?></td>
                        <td><span class="badge badge-<?php echo e($payment->subscription_payment_status === 'Completed' ? 'success' : 'warning'); ?>"><?php echo e($payment->subscription_payment_status); ?></span></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tenant.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/tenant/subscription.blade.php ENDPATH**/ ?>