<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['student']);
$pageTitle = 'Settings';
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>
<div class="dashboard-card rounded-xl p-6 max-w-lg">
    <h2 class="font-semibold mb-2 dash-text">Appearance</h2>
    <p class="text-sm dash-muted mb-4">Choose how the dashboard looks. Your preference is saved automatically.</p>
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
    <p class="text-xs dash-muted mt-4">You can also use the sun/moon icon in the top header bar.</p>
    <hr class="my-6 dash-border">
    <h3 class="font-semibold text-sm dash-text mb-2">Other</h3>
    <ul class="text-sm dash-muted space-y-1">
        <li>Email notifications: Enabled (system default)</li>
        <li>Language: English</li>
    </ul>
    <a href="<?= appUrl('student/profile.php') ?>" class="inline-block mt-4 text-pust-amber hover:underline text-sm">Edit profile &rarr;</a>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>{if(window.PUST)PUST.updateThemeUI(PUST.getTheme());});</script>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
