<?php
$user = currentUser();
$pageTitle = $pageTitle ?? 'Dashboard';
$role = userRole();
$basePath = $role === 'admin' ? 'admin' : ($role === 'staff' ? 'staff' : 'student');
$unreadCount = countUnreadNotifications(userId());
$navItems = match ($role) {
    'admin' => [
        ['icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard', 'url' => 'dashboard.php'],
        ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'label' => 'Tickets', 'url' => 'tickets.php'],
        ['icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'label' => 'Users', 'url' => 'users.php'],
        ['icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'label' => 'Departments', 'url' => 'departments.php'],
        ['icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'label' => 'Categories', 'url' => 'categories.php'],
        ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'label' => 'Reports', 'url' => 'reports.php'],
        ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'Profile', 'url' => 'profile.php'],
        ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Settings', 'url' => 'settings.php'],
    ],
    'staff' => [
        ['icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard', 'url' => 'dashboard.php'],
        ['icon' => 'M12 4v16m8-8H4', 'label' => 'New Ticket', 'url' => 'submit-ticket.php'],
        ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'label' => 'Tickets', 'url' => 'tickets.php'],
        ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'label' => 'Reports', 'url' => 'reports.php'],
        ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'Profile', 'url' => 'profile.php'],
        ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Settings', 'url' => 'settings.php'],
    ],
    default => [
        ['icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard', 'url' => 'dashboard.php'],
        ['icon' => 'M12 4v16m8-8H4', 'label' => 'New Ticket', 'url' => 'submit-ticket.php'],
        ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'label' => 'My Tickets', 'url' => 'tickets.php'],
        ['icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'label' => 'Notifications', 'url' => 'notifications.php'],
        ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'Profile', 'url' => 'profile.php'],
        ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Settings', 'url' => 'settings.php'],
    ],
};
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= PUST_DEFAULT_THEME ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <?php require __DIR__ . '/theme-init.php'; ?>
    <script>
    (function(){
      if(localStorage.getItem('pust-sidebar-collapsed')==='1'){
        document.documentElement.classList.add('sidebar-collapsed');
      }
    })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php require __DIR__ . '/tailwind-brand.php'; ?>
    <?php require __DIR__ . '/brand-styles.php'; ?>
    <link rel="stylesheet" href="<?= assetUrl('css/custom.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/head-branding.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="dashboard-layout antialiased" data-notif-url="<?= appUrl('api/notifications-count.php') ?>">
<div id="page-loader"><?= renderLogo('lg') ?><div class="spinner w-10 h-10"></div></div>
<div class="toast-container"></div>
<?php if ($msg = flash('success')): ?><div data-toast="<?= e($msg) ?>" data-toast-type="success" class="hidden"></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div data-toast="<?= e($msg) ?>" data-toast-type="error" class="hidden"></div><?php endif; ?>

<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden sidebar-overlay"></div>

<aside id="sidebar" class="sidebar pust-sidebar fixed top-0 left-0 z-50 h-full text-white -translate-x-full lg:translate-x-0">
    <div class="sidebar-header p-4 border-b border-white/10 flex items-center justify-between gap-2">
        <a href="<?= appUrl("$basePath/dashboard.php") ?>" class="sidebar-brand flex items-center gap-2 font-bold min-w-0 flex-1" title="PUST Help Desk">
            <?= renderLogo('icon', 'PUST') ?>
            <span class="sidebar-label truncate text-sm">PUST Help Desk</span>
        </a>
        <button type="button" id="sidebar-collapse" class="sidebar-collapse-btn hidden lg:flex p-1.5 hover:bg-white/10 rounded-lg flex-shrink-0" title="Collapse sidebar" aria-label="Collapse sidebar">
            <svg class="collapse-icon-open w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
            <svg class="collapse-icon-close w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
        </button>
    </div>
    <nav class="sidebar-nav p-3 space-y-1 overflow-y-auto overflow-x-hidden">
        <?php foreach ($navItems as $item): ?>
        <a href="<?= appUrl("$basePath/{$item['url']}") ?>"
           title="<?= e($item['label']) ?>"
           class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg transition <?= $currentPage === $item['url'] ? 'bg-white text-pust-primary font-semibold' : 'hover:bg-white/10' ?>">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $item['icon'] ?>"/></svg>
            <span class="sidebar-label"><?= e($item['label']) ?></span>
        </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer absolute bottom-0 left-0 right-0 p-3 border-t border-white/10">
        <a href="<?= appUrl('logout.php') ?>" title="Logout" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-red-500/20 text-red-300">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span class="sidebar-label">Logout</span>
        </a>
    </div>
</aside>

<div class="main-content min-h-screen flex flex-col">
    <header class="dashboard-card sticky top-0 z-30 px-4 py-3 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <button type="button" id="sidebar-toggle" class="dash-icon-btn lg:hidden p-2 rounded-lg" aria-label="Open menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <button type="button" id="sidebar-collapse-header" class="dash-icon-btn hidden lg:flex p-2 rounded-lg" title="Toggle sidebar" aria-label="Toggle sidebar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <?php if (!empty($breadcrumbs)): ?>
            <nav class="dash-muted text-sm hidden sm:block">
                <?php foreach ($breadcrumbs as $i => $crumb): ?>
                    <?php if ($i > 0): ?><span class="mx-1">/</span><?php endif; ?>
                    <?php if (!empty($crumb['url'])): ?>
                        <a href="<?= e($crumb['url']) ?>" class="text-pust-primary hover:underline"><?= e($crumb['label']) ?></a>
                    <?php else: ?>
                        <span class="font-medium dash-text"><?= e($crumb['label']) ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
            <?php else: ?>
            <h1 class="text-lg font-semibold"><?= e($pageTitle) ?></h1>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-3">
            <button id="theme-toggle" type="button" data-theme-toggle class="dash-icon-btn p-2 rounded-lg" title="Toggle theme" aria-label="Toggle theme">
                <svg id="theme-icon-moon" class="w-5 h-5 theme-icon-moon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg id="theme-icon-sun" class="w-5 h-5 theme-icon-sun" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </button>
            <a href="<?= appUrl("$basePath/notifications.php") ?>" class="dash-icon-btn relative p-2 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span id="notif-badge" class="<?= $unreadCount ? '' : 'hidden' ?> absolute -top-0.5 -right-0.5 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center"><?= $unreadCount > 99 ? '99+' : $unreadCount ?></span>
            </a>
            <a href="<?= appUrl("$basePath/profile.php") ?>" class="flex items-center gap-2 pl-2 border-l dash-border hover:opacity-90 transition">
                <?= renderAvatar($user, 'md') ?>
                <div class="hidden sm:block">
                    <p class="text-sm font-medium dash-text"><?= e($user['first_name'] . ' ' . $user['last_name']) ?></p>
                    <p class="text-xs dash-muted"><?= e(roleLabel($user['role'])) ?></p>
                </div>
            </a>
        </div>
    </header>
    <div class="flex-1 p-4 md:p-6">
