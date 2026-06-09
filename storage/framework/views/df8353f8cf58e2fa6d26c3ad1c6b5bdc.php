<?php $__env->startSection('tab-content'); ?>
<div style="max-width:560px;">
    <form method="POST" action="<?php echo e(route('account.profile.update')); ?>">
        <?php echo csrf_field(); ?>

        <div style="display:flex;gap:0.75rem;">
            <div class="form-group" style="flex:1;">
                <label for="user_name"><?php echo e(__('messages.first_name')); ?></label>
                <input type="text" id="user_name" name="user_name" value="<?php echo e(old('user_name', $user->user_name)); ?>" required>
            </div>
            <div class="form-group" style="flex:1;">
                <label for="user_lastName"><?php echo e(__('messages.last_name')); ?></label>
                <input type="text" id="user_lastName" name="user_lastName" value="<?php echo e(old('user_lastName', $user->user_lastName)); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="user_email"><?php echo e(__('messages.email_address')); ?></label>
            <input type="email" id="user_email" name="user_email" value="<?php echo e(old('user_email', $user->user_email)); ?>" required>
        </div>

        <div class="form-group">
            <label for="user_phone_number"><?php echo e(__('messages.phone_number')); ?></label>
            <input type="text" id="user_phone_number" name="user_phone_number" value="<?php echo e(old('user_phone_number', $user->user_phone_number)); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary"><?php echo e(__('messages.save_changes')); ?></button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tenant.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\daatio\resources\views/tenant/profile.blade.php ENDPATH**/ ?>