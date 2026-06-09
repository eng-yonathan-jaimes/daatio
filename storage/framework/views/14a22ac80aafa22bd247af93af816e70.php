<?php $__env->startSection('title', __('messages.subscription')); ?>

<?php $__env->startSection('main'); ?>
<div class="page-header">
    <h1><?php echo e(__('messages.subscription')); ?></h1>
    <p><?php echo e(__('messages.manage_subscription')); ?></p>
</div>

<?php if(session('status')): ?>
    <div class="alert alert-success"><?php echo e(session('status')); ?></div>
<?php endif; ?>

<div class="stats-grid" style="margin-bottom:2rem;">
    <div class="stat-card">
        <div class="stat-label"><?php echo e(__('messages.current_plan')); ?></div>
        <div class="stat-value" style="font-size:1.5rem;">
            <?php echo e($activeSubscription?->subscription?->subscription_type ?? __('messages.none')); ?>

        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><?php echo e(__('messages.status')); ?></div>
        <div class="stat-value" style="font-size:1.25rem;">
            <span class="badge badge-<?php echo e(($activeSubscription?->user_subscription_status ?? 'Expired') === 'Active' ? 'success' : (($activeSubscription?->user_subscription_status ?? 'Expired') === 'Trial' ? 'info' : 'error')); ?>">
                <?php echo e(__($activeSubscription?->user_subscription_status ?? 'Expired')); ?>

            </span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><?php echo e(__('messages.days_remaining')); ?></div>
        <div class="stat-value" style="font-size:1.5rem;color:<?php echo e(($activeSubscription?->daysRemaining() ?? 0) <= 7 ? '#DC2626' : 'var(--color-text)'); ?>;">
            <?php echo e($activeSubscription?->daysRemaining() ?? 0); ?>

        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><?php echo e(__('messages.price')); ?></div>
        <div class="stat-value" style="font-size:1.25rem;">
            $<?php echo e(number_format($activeSubscription?->user_subscription_value ?? 0, 2)); ?>

        </div>
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

<?php if($activeSubscription->payments->count()): ?>
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
                    <th><?php echo e(__('messages.reference')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $activeSubscription->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($payment->subscription_payment_date->format('M d, Y')); ?></td>
                        <td>$<?php echo e(number_format($payment->subscription_payment_amount, 2)); ?></td>
                        <td><?php echo e($payment->subscription_payment_method); ?></td>
                        <td><span class="badge badge-<?php echo e($payment->subscription_payment_status === 'Completed' ? 'success' : 'warning'); ?>"><?php echo e(__($payment->subscription_payment_status)); ?></span></td>
                        <td style="font-family:var(--font-mono);font-size:0.8rem;"><?php echo e($payment->subscription_payment_reference); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<div class="card">
    <div class="card-title" style="margin-bottom:1rem;"><?php echo e(__('messages.available_plans')); ?></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;">
        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="border:1px solid var(--color-border);border-radius:8px;padding:1.5rem;text-align:center;">
                <h3 style="font-family:var(--font-heading);margin-bottom:0.5rem;"><?php echo e($plan->subscription_type); ?></h3>
                <?php if($plan->subscription_description): ?>
                    <p style="color:var(--color-text-muted);font-size:0.875rem;margin-bottom:1rem;"><?php echo e($plan->subscription_description); ?></p>
                <?php endif; ?>
                <div style="font-size:2rem;font-weight:700;font-family:var(--font-heading);margin-bottom:0.5rem;">$<?php echo e(number_format($plan->subscription_value, 2)); ?></div>
                <div style="color:var(--color-text-muted);font-size:0.875rem;margin-bottom:1rem;"><?php echo e(__('messages.per')); ?> <?php echo e($plan->subscription_period); ?> (<?php echo e($plan->subscription_days); ?> <?php echo e(__('messages.days')); ?>)</div>
                <div style="color:var(--color-text-muted);font-size:0.8rem;margin-bottom:1rem;"><?php echo e(__('messages.max_stores')); ?>: <?php echo e($plan->subscription_max_stores); ?></div>
                <form method="POST" action="<?php echo e(route('subscription.renew')); ?>" onsubmit="return confirm('<?php echo e(__('messages.activate_plan_confirm', ['plan' => $plan->subscription_type, 'price' => number_format($plan->subscription_value, 2)])); ?>')">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="plan_id" value="<?php echo e($plan->id); ?>">
                    <button type="submit" class="btn btn-primary" style="width:100%;"><?php echo e($activeSubscription?->subscription?->id === $plan->id ? __('messages.current_plan_btn') : __('messages.choose_plan')); ?></button>
                </form>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/subscription/index.blade.php ENDPATH**/ ?>