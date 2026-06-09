<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> - Daatio</title>
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <a href="/daatio/public/" class="navbar-brand">Daatio</a>
        <div class="navbar-actions">
            <form method="POST" action="<?php echo e(route('language.switch')); ?>" style="display:flex;gap:0.25rem;margin-right:0.5rem;">
                <?php echo csrf_field(); ?>
                <button type="submit" name="locale" value="en" class="btn btn-outline" style="padding:0.375rem 0.5rem;font-size:0.75rem;<?php echo e(app()->getLocale() === 'en' ? 'background:var(--color-active-nav-bg);' : ''); ?>">EN</button>
                <button type="submit" name="locale" value="es" class="btn btn-outline" style="padding:0.375rem 0.5rem;font-size:0.75rem;<?php echo e(app()->getLocale() === 'es' ? 'background:var(--color-active-nav-bg);' : ''); ?>">ES</button>
            </form>
            <span style="color:var(--color-text-muted);font-size:0.875rem;margin-right:0.5rem;"><?php echo e(auth()->user()->name); ?></span>
            <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-outline"><?php echo e(__('messages.logout')); ?></button>
            </form>
        </div>
    </nav>

    <div class="page-container">
        <aside class="sidebar">
            <div class="sidebar-section">
                <div class="sidebar-title"><?php echo e(__('messages.main')); ?></div>
                <ul class="sidebar-menu">
                    <li><a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>"><span class="icon">◉</span> <?php echo e(__('messages.dashboard')); ?></a></li>
                    <li><a href="<?php echo e(route('customers.index')); ?>" class="<?php echo e(request()->routeIs('customers.*') ? 'active' : ''); ?>"><span class="icon">◐</span> <?php echo e(__('messages.customers')); ?></a></li>
                    <li><a href="<?php echo e(route('transactions.index')); ?>" class="<?php echo e(request()->routeIs('transactions.*') ? 'active' : ''); ?>"><span class="icon">◎</span> <?php echo e(__('messages.transactions')); ?></a></li>
                    <li><a href="<?php echo e(route('products.index')); ?>" class="<?php echo e(request()->routeIs('products.*') ? 'active' : ''); ?>"><span class="icon">◆</span> <?php echo e(__('messages.products')); ?></a></li>
                    <li><a href="<?php echo e(route('reports.index')); ?>" class="<?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>"><span class="icon">▤</span> <?php echo e(__('messages.reports')); ?></a></li>
                </ul>
            </div>
            <div class="sidebar-section">
                <div class="sidebar-title"><?php echo e(__('messages.account')); ?></div>
                <ul class="sidebar-menu">
                    <li><a href="<?php echo e(route('account.profile.edit')); ?>" class="<?php echo e(request()->routeIs('account.*') ? 'active' : ''); ?>"><span class="icon">⚙</span> <?php echo e(__('messages.my_account')); ?></a></li>
                    <li><a href="<?php echo e(route('stores.index')); ?>" class="<?php echo e(request()->routeIs('stores.*') ? 'active' : ''); ?>"><span class="icon">⌂</span> <?php echo e(__('messages.stores')); ?></a></li>
                </ul>
            </div>
        </aside>

        <main class="main-content">
            <?php echo $__env->yieldContent('main'); ?>
        </main>
    </div>

    <footer style="text-align:center;padding:1.5rem;color:var(--color-text-muted);font-size:0.8rem;border-top:0.5px solid var(--color-border);">
        <?php echo e(__('messages.footer', ['year' => date('Y')])); ?>

    </footer>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\daatio\resources\views/layouts/dashboard.blade.php ENDPATH**/ ?>