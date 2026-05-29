<?php
/** @var array $user */
/** @var bool $showStudentId */
$showStudentId = $showStudentId ?? ($user['role'] === 'student');
?>
<div class="grid md:grid-cols-2 gap-6 max-w-4xl">
    <div class="dashboard-card rounded-xl p-6">
        <h2 class="font-semibold mb-4 dash-text">Profile Information</h2>

        <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 mb-6 border-b dash-border">
            <?= renderAvatar($user, '2xl') ?>
            <div class="flex-1 w-full text-center sm:text-left">
                <p class="font-semibold dash-text text-lg"><?= e($user['first_name'] . ' ' . $user['last_name']) ?></p>
                <p class="text-sm dash-muted"><?= e($user['email']) ?></p>
                <p class="text-xs dash-muted capitalize mt-1"><?= e($user['role']) ?></p>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data" data-validate class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="profile">
            <div>
                <label class="block text-sm font-medium dash-text mb-1">Change profile picture</label>
                <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                       class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-pust-navy file:text-white">
                <p class="text-xs dash-muted mt-1">JPG, PNG or WebP. Max 2MB.</p>
            </div>
            <?php if ($showStudentId): ?>
            <div>
                <label class="text-sm font-medium dash-text">Student ID</label>
                <input name="student_id" value="<?= e($user['student_id'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg mt-1">
            </div>
            <div>
                <label class="text-sm font-medium dash-text">Faculty</label>
                <p class="w-full px-4 py-2 border rounded-lg mt-1 bg-slate-50 dash-muted text-sm"><?= e(getFacultyName($user['faculty_id'] ?? null)) ?></p>
            </div>
            <?php elseif (($user['role'] ?? '') === 'staff' && ($user['staff_category'] ?? '') === 'lecturer'): ?>
            <div>
                <label class="text-sm font-medium dash-text">Staff category</label>
                <p class="w-full px-4 py-2 border rounded-lg mt-1 bg-slate-50 dash-muted text-sm"><?= e(staffCategoryLabel($user['staff_category'] ?? null)) ?></p>
            </div>
            <div>
                <label class="text-sm font-medium dash-text">Faculty</label>
                <p class="w-full px-4 py-2 border rounded-lg mt-1 bg-slate-50 dash-muted text-sm"><?= e(getFacultyName($user['faculty_id'] ?? null)) ?></p>
            </div>
            <?php elseif (($user['role'] ?? '') === 'staff'): ?>
            <div>
                <label class="text-sm font-medium dash-text">Staff category</label>
                <p class="w-full px-4 py-2 border rounded-lg mt-1 bg-slate-50 dash-muted text-sm"><?= e(staffCategoryLabel($user['staff_category'] ?? null)) ?></p>
            </div>
            <?php endif; ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium dash-text">First Name *</label>
                    <input name="first_name" required value="<?= e($user['first_name']) ?>" class="w-full px-4 py-2 border rounded-lg mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium dash-text">Last Name *</label>
                    <input name="last_name" required value="<?= e($user['last_name']) ?>" class="w-full px-4 py-2 border rounded-lg mt-1">
                </div>
            </div>
            <div>
                <label class="text-sm font-medium dash-text">Phone</label>
                <input name="phone" value="<?= e($user['phone'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg mt-1">
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="submit" class="px-6 py-2.5 bg-pust-navy text-white font-semibold rounded-lg">Save Profile</button>
            </div>
        </form>
        <?php if (userHasCustomAvatar($user)): ?>
        <form method="POST" class="mt-3">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="avatar_remove">
            <button type="submit" class="text-sm text-red-600 hover:underline">Remove profile picture</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="dashboard-card rounded-xl p-6">
        <h2 class="font-semibold mb-4 dash-text">Change Password</h2>
        <form method="POST" data-validate class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="password">
            <div>
                <label class="text-sm font-medium dash-text">Current Password</label>
                <input type="password" name="current_password" required class="w-full px-4 py-2 border rounded-lg mt-1">
            </div>
            <div>
                <label class="text-sm font-medium dash-text">New Password</label>
                <input type="password" name="new_password" required data-strength="true" class="w-full px-4 py-2 border rounded-lg mt-1">
            </div>
            <button type="submit" class="px-6 py-2.5 pust-btn-primary font-semibold rounded-lg">Change Password</button>
        </form>
    </div>
</div>
