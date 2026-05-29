<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/middleware/auth.php';
guestOnly();

if (isPost()) {
    requireCsrf();
    createPasswordResetToken(input('email'));
    flash('success', 'If your email exists, reset instructions have been sent.');
}

$pageTitle = 'Forgot Password';
require __DIR__ . '/includes/templates/public-header.php';
?>
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border">
        <h1 class="text-2xl font-bold text-pust-navy mb-2">Forgot Password</h1>
        <p class="text-slate-500 text-sm mb-6">Enter your email to receive a password reset link.</p>
        <form method="POST" data-validate class="space-y-4">
            <?= csrfField() ?>
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 border rounded-lg">
            </div>
            <button type="submit" class="w-full py-3 bg-pust-navy text-white font-semibold rounded-lg">Send Reset Link</button>
        </form>
        <p class="text-center text-sm mt-6"><a href="<?= appUrl('login.php') ?>" class="text-pust-amber">Back to Login</a></p>
    </div>
</div>
<?php require __DIR__ . '/includes/templates/public-footer.php'; ?>
