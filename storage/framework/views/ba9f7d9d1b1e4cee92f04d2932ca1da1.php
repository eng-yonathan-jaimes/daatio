<?php $__env->startSection('title', __('messages.my_account')); ?>

<?php $__env->startSection('main'); ?>
<div class="page-header">
    <h1><?php echo e(__('messages.my_account')); ?></h1>
    <p><?php echo e(auth()->user()->user_email); ?></p>
</div>

<?php if(session('status')): ?>
    <div class="alert alert-success"><?php echo e(session('status')); ?></div>
<?php endif; ?>

<?php if($errors->any() && !$errors->has('current_password') && !$errors->has('new_password')): ?>
    <div class="alert alert-error"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<div class="card" style="margin-bottom:1.5rem;padding:0;">
    <div style="display:flex;border-bottom:1px solid var(--color-border);">
        <a href="<?php echo e(route('account.profile.edit')); ?>" class="tab-link <?php echo e(request()->routeIs('account.profile.*') ? 'tab-active' : ''); ?>"><?php echo e(__('messages.profile')); ?></a>
        <a href="<?php echo e(route('account.security.index')); ?>" class="tab-link <?php echo e(request()->routeIs('account.security.*') ? 'tab-active' : ''); ?>"><?php echo e(__('messages.security_logs')); ?></a>
        <a href="<?php echo e(route('account.subscription.index')); ?>" class="tab-link <?php echo e(request()->routeIs('account.subscription.*') ? 'tab-active' : ''); ?>"><?php echo e(__('messages.subscription')); ?></a>
    </div>
    <div style="padding:1.5rem;">
        <?php echo $__env->yieldContent('tab-content'); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/tenant/layout.blade.php ENDPATH**/ ?>