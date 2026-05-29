<?php
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= PUST_DEFAULT_THEME ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <?php require __DIR__ . '/theme-init.php'; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php require __DIR__ . '/tailwind-brand.php'; ?>
    <?php require __DIR__ . '/brand-styles.php'; ?>
    <link rel="stylesheet" href="<?= assetUrl('css/custom.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/head-branding.php'; ?>
</head>
<body class="pust-public-body pust-public-layout antialiased">
<div id="page-loader"><?= renderLogo('lg') ?><div class="spinner w-10 h-10"></div></div>
<div class="toast-container"></div>
<?php if ($msg = flash('success')): ?><div data-toast="<?= e($msg) ?>" data-toast-type="success" class="hidden"></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div data-toast="<?= e($msg) ?>" data-toast-type="error" class="hidden"></div><?php endif; ?>

<header class="fixed top-0 left-0 right-0 z-50 px-4 py-4 md:px-8 pointer-events-none transition-all duration-300">
    <div class="max-w-7xl mx-auto w-full pointer-events-auto bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-100/40 dark:shadow-none rounded-2xl md:rounded-full px-4 sm:px-6 py-3 transition-all duration-300">
        <div class="flex justify-between items-center h-12">
            <!-- Branding: PUST Help Desk with official university crest -->
            <a href="<?= appUrl() ?>" class="flex items-center gap-3 min-w-0 flex-1 md:flex-none">
                <span class="shrink-0"><?= renderLogo('sm', 'PUST Help Desk') ?></span>
                <div class="flex flex-col min-w-0">
                    <span class="font-extrabold text-base tracking-tight text-slate-900 dark:text-white leading-none truncate">PUST Help Desk</span>
                    <span class="hidden sm:block text-[9px] font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400 mt-0.5 truncate">Puntland University of Science &amp; Technology</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <div class="hidden lg:flex items-center gap-8">
                <a href="<?= appUrl() ?>" class="text-sm font-medium text-slate-600 hover:text-pust-primary dark:text-slate-300 dark:hover:text-blue-500 transition-colors">Home</a>
                <a href="<?= appUrl('about.php') ?>" class="text-sm font-medium text-slate-600 hover:text-pust-primary dark:text-slate-300 dark:hover:text-blue-500 transition-colors">About</a>
                <a href="<?= appUrl('contact.php') ?>" class="text-sm font-medium text-slate-600 hover:text-pust-primary dark:text-slate-300 dark:hover:text-blue-500 transition-colors">Contact</a>
            </div>

            <!-- Actions -->
            <div class="hidden md:flex items-center gap-4">
                <!-- Theme Toggle Button -->
                <button id="theme-toggle" type="button" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors" title="Toggle theme" aria-label="Toggle theme">
                    <svg id="theme-icon-moon" class="w-5 h-5 theme-icon-moon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg id="theme-icon-sun" class="w-5 h-5 theme-icon-sun" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                
                <?php if (isLoggedIn()): ?>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Hi, <?= e(currentUser()['first_name']) ?></span>
                    <a href="<?= appUrl(userRole() . '/dashboard.php') ?>" class="px-4 py-2 text-sm font-semibold bg-slate-900 dark:bg-white text-white dark:text-slate-950 rounded-full hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors">Dashboard</a>
                    <a href="<?= appUrl('logout.php') ?>" class="text-sm text-slate-500 hover:text-red-500 transition-colors">Logout</a>
                <?php else: ?>
                    <a href="<?= appUrl('login.php') ?>" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-950 dark:text-slate-300 dark:hover:text-white transition-colors">Login</a>
                    <a href="<?= appUrl('register.php') ?>" class="px-5 py-2.5 text-sm font-semibold bg-slate-950 hover:bg-slate-800 text-white dark:bg-white dark:hover:bg-slate-100 dark:text-slate-950 rounded-full shadow-sm hover:shadow transition-all duration-200">Get Started</a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu and Toggle Buttons -->
            <div class="flex items-center gap-2 sm:gap-3 md:hidden shrink-0">
                <button id="theme-toggle-mobile" type="button" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors" title="Toggle theme" aria-label="Toggle theme">
                    <svg class="w-5 h-5 theme-icon-moon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg class="w-5 h-5 theme-icon-sun" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                <button id="mobile-menu-btn" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors" aria-label="Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div id="mobile-menu" class="hidden lg:hidden mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
            <a href="<?= appUrl() ?>" class="block py-2 px-3 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white transition-all">Home</a>
            <a href="<?= appUrl('about.php') ?>" class="block py-2 px-3 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white transition-all">About</a>
            <a href="<?= appUrl('contact.php') ?>" class="block py-2 px-3 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white transition-all">Contact</a>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col gap-2 md:hidden">
                <?php if (isLoggedIn()): ?>
                    <a href="<?= appUrl(userRole() . '/dashboard.php') ?>" class="w-full text-center py-2.5 rounded-full bg-slate-900 text-white dark:bg-white dark:text-slate-950 font-semibold transition-all">Dashboard</a>
                    <a href="<?= appUrl('logout.php') ?>" class="w-full text-center py-2.5 rounded-full border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-semibold transition-all">Logout</a>
                <?php else: ?>
                    <a href="<?= appUrl('login.php') ?>" class="w-full text-center py-2.5 rounded-full border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-semibold transition-all">Login</a>
                    <a href="<?= appUrl('register.php') ?>" class="w-full text-center py-2.5 rounded-full bg-slate-950 text-white dark:bg-white dark:text-slate-950 font-semibold transition-all">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const mobileToggle = document.getElementById('theme-toggle-mobile');
    const mainToggle = document.getElementById('theme-toggle');
    if (mobileToggle && mainToggle) {
        mobileToggle.addEventListener('click', () => {
            mainToggle.click();
        });
    }
});
</script>

<main class="pt-28 md:pt-32 flex-grow">
