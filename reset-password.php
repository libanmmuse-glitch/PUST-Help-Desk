<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/middleware/auth.php';
guestOnly();

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

if (isPost()) {
    requireCsrf();
    $result = resetPassword(input('token'), input('email'), input('password'), input('password_confirm'));
    if ($result['success']) {
        flash('success', 'Your password has been reset successfully. Please log in with your new password.');
        redirect(appUrl('login.php'));
    }
    flash('error', implode(' ', $result['errors']));
}

$pageTitle = 'Reset Password';
require __DIR__ . '/includes/templates/public-header.php';
?>
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border">
        <h1 class="text-2xl font-bold text-pust-navy mb-2">Reset Password</h1>
        <p class="text-slate-500 text-sm mb-6">Enter and confirm your new password below.</p>
        <form method="POST" data-validate class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="token" value="<?= e($token ?: old('token')) ?>">
            <input type="hidden" name="email" value="<?= e($email ?: old('email')) ?>">
            <div>
                <label class="block text-sm font-medium mb-1">New Password</label>
                <input type="password" name="password" required data-strength="true" class="w-full px-4 py-2.5 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirm Password</label>
                <input type="password" name="password_confirm" required class="w-full px-4 py-2.5 border rounded-lg">
            </div>
            <button type="submit" class="w-full py-3 bg-pust-navy text-white font-semibold rounded-lg">Reset Password</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/includes/templates/public-footer.php'; ?>
