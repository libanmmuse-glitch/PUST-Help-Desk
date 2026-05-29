<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['admin']);

$db = getDB();
if (isPost()) {
    requireCsrf();
    foreach (['site_name','site_email','tickets_per_page','allow_registration'] as $key) {
        if (isset($_POST[$key])) {
            $db->prepare('UPDATE settings SET setting_value = ? WHERE setting_key = ?')->execute([sanitizeString($_POST[$key]), $key]);
        }
    }
    flash('success', 'Settings saved.');
    redirect(appUrl('admin/settings.php'));
}

$settings = [];
foreach ($db->query('SELECT * FROM settings')->fetchAll() as $s) {
    $settings[$s['setting_key']] = $s['setting_value'];
}

$pageTitle = 'Settings';
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>
<div class="dashboard-card rounded-xl p-6 max-w-lg mb-6">
    <h2 class="font-semibold mb-2 dash-text">Appearance</h2>
    <p class="text-sm dash-muted mb-4">Choose light or dark mode for the admin dashboard.</p>
    <div class="flex gap-3">
        <button type="button" data-set-theme="light" class="theme-option flex-1 flex flex-col items-center gap-2 p-4 rounded-xl">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span class="font-medium text-sm">Light</span>
        </button>
        <button type="button" data-set-theme="dark" class="theme-option flex-1 flex flex-col items-center gap-2 p-4 rounded-xl">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <span class="font-medium text-sm">Dark</span>
        </button>
    </div>
</div>
<div class="dashboard-card rounded-xl p-6 max-w-xl">
    <form method="POST" class="space-y-4"><?= csrfField() ?>
        <div><label class="text-sm font-medium">Site Name</label><input name="site_name" value="<?= e($settings['site_name']??'') ?>" class="w-full px-4 py-2 border rounded-lg mt-1"></div>
        <div><label class="text-sm font-medium">Site Email</label><input name="site_email" type="email" value="<?= e($settings['site_email']??'') ?>" class="w-full px-4 py-2 border rounded-lg mt-1"></div>
        <div><label class="text-sm font-medium">Tickets Per Page</label><input name="tickets_per_page" type="number" value="<?= e($settings['tickets_per_page']??'10') ?>" class="w-full px-4 py-2 border rounded-lg mt-1"></div>
        <div><label class="text-sm font-medium">Allow Registration</label>
            <select name="allow_registration" class="w-full px-4 py-2 border rounded-lg mt-1">
                <option value="1" <?= ($settings['allow_registration']??'1')==='1'?'selected':'' ?>>Yes</option>
                <option value="0" <?= ($settings['allow_registration']??'')==='0'?'selected':'' ?>>No</option>
            </select>
        </div>
        <button class="px-6 py-2 pust-btn-primary font-semibold rounded-lg">Save Settings</button>
    </form>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>{if(window.PUST)PUST.updateThemeUI(PUST.getTheme());});</script>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
