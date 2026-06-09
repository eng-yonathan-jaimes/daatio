<?php $__env->startSection('tab-content'); ?>
<div class="card" style="margin-bottom:1.5rem;max-width:560px;">
    <div class="card-title" style="margin-bottom:1rem;"><?php echo e(__('messages.change_password')); ?></div>
    <?php if($errors->has('current_password') || $errors->has('new_password')): ?>
        <div class="alert alert-error"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>
    <form method="POST" action="<?php echo e(route('account.security.password')); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="current_password"><?php echo e(__('messages.current_password_label')); ?></label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        <div class="form-group">
            <label for="new_password"><?php echo e(__('messages.new_password')); ?></label>
            <input type="password" id="new_password" name="new_password" required minlength="8">
        </div>
        <div class="form-group">
            <label for="new_password_confirmation"><?php echo e(__('messages.confirm_new_password')); ?></label>
            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo e(__('messages.update_password')); ?></button>
    </form>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:1rem;"><?php echo e(__('messages.login_history')); ?></div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><?php echo e(__('messages.date')); ?></th>
                    <th><?php echo e(__('messages.ip_address')); ?></th>
                    <th><?php echo e(__('messages.device')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $loginHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($log->user_login_history_login_date->format('M d, Y h:i A')); ?></td>
                        <td style="font-family:var(--font-mono);font-size:0.85rem;"><?php echo e($log->user_login_history_ip); ?></td>
                        <td style="font-size:0.8rem;max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo e($log->user_login_history_device); ?>"><?php echo e($log->user_login_history_device); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="3" style="text-align:center;color:var(--color-text-muted);padding:2rem;"><?php echo e(__('messages.no_data')); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-title" style="margin-bottom:1rem;"><?php echo e(__('messages.recovery_history')); ?></div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><?php echo e(__('messages.date')); ?></th>
                    <th><?php echo e(__('messages.method')); ?></th>
                    <th><?php echo e(__('messages.status')); ?></th>
                    <th><?php echo e(__('messages.ip_address')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $recoveryHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($log->user_recovery_history_intent_date->format('M d, Y h:i A')); ?></td>
                        <td><?php echo e($log->user_recovery_history_method_used); ?></td>
                        <td>
                            <span class="badge badge-<?php echo e($log->user_recovery_history_recovered_success ? 'success' : 'error'); ?>">
                                <?php echo e($log->user_recovery_history_recovered_success ? __('messages.success') : __('messages.failed')); ?>

                            </span>
                        </td>
                        <td style="font-family:var(--font-mono);font-size:0.85rem;"><?php echo e($log->user_recovery_history_ip); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--color-text-muted);padding:2rem;"><?php echo e(__('messages.no_data')); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tenant.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/tenant/security.blade.php ENDPATH**/ ?>