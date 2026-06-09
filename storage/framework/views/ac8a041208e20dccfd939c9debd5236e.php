<?php $__env->startSection('content'); ?>
<nav class="navbar">
    <a href="/daatio/public/" class="navbar-brand">Daatio</a>
    <div class="navbar-menu">
        <a href="/daatio/public/" class="active"><?php echo e(__('messages.home')); ?></a>
        <a href="#"><?php echo e(__('messages.features')); ?></a>
        <a href="#"><?php echo e(__('messages.pricing')); ?></a>
        <a href="#"><?php echo e(__('messages.about')); ?></a>
    </div>
    <div class="navbar-actions">
        <a href="/daatio/public/login" class="btn btn-outline"><?php echo e(__('messages.sign_in')); ?></a>
        <a href="/daatio/public/register" class="btn btn-primary"><?php echo e(__('messages.get_started_free')); ?></a>
    </div>
</nav>

<div class="main-content" style="padding: 0; display: block;">
    <div class="landing-hero">
        <h1><?php echo e(__('messages.welcome_title')); ?></h1>
        <p><?php echo e(__('messages.welcome_subtitle')); ?></p>
        <a href="/daatio/public/register" class="btn btn-primary"><?php echo e(__('messages.get_started_free')); ?></a>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">◉</div>
                <h3><?php echo e(__('messages.feature_dashboard')); ?></h3>
                <p><?php echo e(__('messages.feature_dashboard_desc')); ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">◎</div>
                <h3><?php echo e(__('messages.feature_cashbook')); ?></h3>
                <p><?php echo e(__('messages.feature_cashbook_desc')); ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">◐</div>
                <h3><?php echo e(__('messages.feature_customers')); ?></h3>
                <p><?php echo e(__('messages.feature_customers_desc')); ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">◑</div>
                <h3><?php echo e(__('messages.feature_suppliers')); ?></h3>
                <p><?php echo e(__('messages.feature_suppliers_desc')); ?></p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/welcome.blade.php ENDPATH**/ ?>