<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/middleware/auth.php';
guestOnly();

if (isPost()) {
    requireCsrf();
    $user = attemptLogin(input('email'), input('password'));
    if ($user) {
        loginUser($user, !empty($_POST['remember']));
        flash('success', 'Welcome back, ' . $user['first_name'] . '!');
        redirectToDashboard();
    }
    flash('error', 'Invalid email or password.');
    setOld($_POST);
}

$pageTitle = 'Login';
require __DIR__ . '/includes/templates/public-header.php';
?>
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8 border">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4"><?= renderLogo('xl', 'PUST University') ?></div>
                <h1 class="text-2xl font-bold text-pust-navy">Sign In</h1>
                <p class="text-slate-500 text-sm mt-1">Access your PUST Help Desk account</p>
            </div>
            <?php if (isset($_GET['timeout'])): ?>
            <div class="mb-4 p-3 bg-amber-50 text-amber-800 rounded-lg text-sm">Session expired. Please log in again.</div>
            <?php endif; ?>
            <form method="POST" data-validate class="space-y-4">
                <?= csrfField() ?>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" required value="<?= old('email') ?>" class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-pust-amber/50" placeholder="you@student.pust.edu">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-pust-amber/50" placeholder="••••••••">
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-pust-amber">
                        Remember me
                    </label>
                    <a href="<?= appUrl('forgot-password.php') ?>" class="text-pust-navy hover:text-pust-amber">Forgot password?</a>
                </div>
                <button type="submit" class="w-full py-3 pust-btn-primary font-semibold rounded-lg transition">Sign In</button>
            </form>
            <p class="text-center text-sm text-slate-500 mt-6">Don't have an account? <a href="<?= appUrl('register.php') ?>" class="text-pust-amber font-medium hover:underline">Register</a></p>
            <div class="mt-6 p-3 bg-slate-50 rounded-lg text-xs text-slate-500">
                <strong>Admin login:</strong> admin@pust.edu / password
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/templates/public-footer.php'; ?>
