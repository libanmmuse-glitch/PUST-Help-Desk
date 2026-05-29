<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/middleware/auth.php';
guestOnly();

try {
    $faculties = getFaculties();
} catch (Throwable $e) {
    $faculties = [];
}

if (isPost()) {
    requireCsrf();
    if (getSetting('allow_registration', '1') !== '1') {
        flash('error', 'Registration is currently disabled.');
    } else {
        // Enforce account_type as student for public registration
        $_POST['account_type'] = 'student';
        $result = registerUser($_POST);
        if ($result['success']) {
            flash('success', 'Student registration successful! Please log in.');
            redirect(appUrl('login.php'));
        }
        flash('error', implode(' ', $result['errors']));
        setOld($_POST);
    }
}

$pageTitle = 'Register';
require __DIR__ . '/includes/templates/public-header.php';
?>
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-lg">
        <div class="bg-white rounded-2xl shadow-xl p-8 border">
            <div class="text-center mb-6">
                <div class="flex justify-center mb-4"><?= renderLogo('xl', 'PUST University') ?></div>
                <h1 class="text-2xl font-bold text-pust-navy">Create Account</h1>
                <p class="text-slate-500 text-sm mt-1">Register as a student of PUST University</p>
            </div>

            <?php if (empty($faculties)): ?>
            <div class="mb-4 p-3 bg-amber-50 text-amber-900 rounded-lg text-sm border border-amber-200">
                Faculty list is not available. Please contact the administrator to complete the faculty setup.
            </div>
            <?php endif; ?>

            <form method="POST" data-validate id="register-form" class="space-y-4" <?= empty($faculties) ? 'data-registration-blocked' : '' ?>>
                <?= csrfField() ?>
                <input type="hidden" name="account_type" value="student">

                <div>
                    <label class="block text-sm font-medium mb-1" for="faculty_id">Faculty *</label>
                    <select name="faculty_id" id="faculty_id" class="w-full px-4 py-2.5 border rounded-lg bg-white" required>
                        <option value="">Select your faculty</option>
                        <?php foreach ($faculties as $f): ?>
                        <option value="<?= (int) $f['id'] ?>" <?= (string) old('faculty_id') === (string) $f['id'] ? 'selected' : '' ?>><?= e($f['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Your academic faculty at PUST.</p>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">First Name *</label>
                        <input type="text" name="first_name" required value="<?= old('first_name') ?>" class="w-full px-4 py-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Last Name *</label>
                        <input type="text" name="last_name" required value="<?= old('last_name') ?>" class="w-full px-4 py-2.5 border rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" id="member-id-label">Student ID *</label>
                    <input type="text" name="student_id" id="student_id" required value="<?= old('student_id') ?>" class="w-full px-4 py-2.5 border rounded-lg" placeholder="PUST2024001">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Email *</label>
                    <input type="email" name="email" id="register-email" required value="<?= old('email') ?>" class="w-full px-4 py-2.5 border rounded-lg" placeholder="you@student.pust.edu">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <input type="tel" name="phone" value="<?= old('phone') ?>" class="w-full px-4 py-2.5 border rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Password *</label>
                    <input type="password" name="password" required data-strength="true" class="w-full px-4 py-2.5 border rounded-lg" placeholder="Min 8 chars, upper, lower, number">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Confirm Password *</label>
                    <input type="password" name="password_confirm" required class="w-full px-4 py-2.5 border rounded-lg">
                </div>

                <button type="submit" class="w-full py-3 pust-btn-primary font-semibold rounded-lg transition">Create Account</button>
            </form>

            <p class="text-center text-sm text-slate-500 mt-6">Already registered? <a href="<?= appUrl('login.php') ?>" class="font-medium text-pust-navy hover:text-pust-amber">Sign In</a></p>
        </div>
    </div>
</div>
<script>
(function () {
    const form = document.getElementById('register-form');
    if (form && form.dataset.registrationBlocked) {
        form.addEventListener('submit', e => e.preventDefault());
    }
})();
</script>
<?php require __DIR__ . '/includes/templates/public-footer.php'; ?>
