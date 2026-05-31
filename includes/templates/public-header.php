<?php
$pageTitle = $pageTitle ?? APP_NAME;

// Resolve best logo file
$_logoFile = 'pust-logo-transparent.png';
if (!is_file(ROOT_PATH . '/assets/images/pust-logo-transparent.png')) {
    $_logoFile = is_file(ROOT_PATH . '/assets/images/pust-logo.jpg') ? 'pust-logo.jpg' : 'pust-logo.png';
}
$_logoUrl = assetUrl('images/' . $_logoFile);
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/head-branding.php'; ?>

    <style>
    /* ═══════════════════════════════════════════════════
       NAVBAR — premium redesign
    ═══════════════════════════════════════════════════ */
    :root {
        --nav-h: 76px;
        --nav-bg-light: rgba(255,255,255,0.92);
        --nav-bg-dark:  rgba(10,15,30,0.88);
        --nav-border-light: rgba(203,213,225,0.7);
        --nav-border-dark:  rgba(255,255,255,0.08);
        --nav-shadow: 0 8px 32px rgba(15,23,42,0.10), 0 2px 8px rgba(15,23,42,0.06);
        --brand-blue: #1B3A6B;
        --brand-blue2: #2F6FDB;
    }

    /* ── Fixed wrapper ───────────────────────────────── */
    #site-header {
        position: fixed;
        top: 0; left: 0; right: 0;
        z-index: 1000;
        padding: 10px 1rem 0;
        transition: padding .3s ease;
    }
    #site-header.scrolled { padding-top: 6px; }

    /* ── Pill container ──────────────────────────────── */
    .nav-pill {
        max-width: 1200px;
        margin: 0 auto;
        background: var(--nav-bg-light);
        border: 1px solid var(--nav-border-light);
        border-radius: 20px;
        box-shadow: var(--nav-shadow);
        backdrop-filter: blur(20px) saturate(1.6);
        -webkit-backdrop-filter: blur(20px) saturate(1.6);
        transition: box-shadow .3s, border-color .3s, border-radius .3s;
    }
    #site-header.scrolled .nav-pill {
        border-radius: 16px;
        box-shadow: 0 16px 48px rgba(15,23,42,.14), 0 2px 8px rgba(15,23,42,.06);
    }
    html[data-theme="dark"] .nav-pill {
        background: var(--nav-bg-dark);
        border-color: var(--nav-border-dark);
    }

    /* ── Inner flex row ──────────────────────────────── */
    .nav-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0 1.25rem;
        height: 64px;
    }

    /* ── Brand / Logo ────────────────────────────────── */
    .nav-brand {
        display: flex;
        align-items: center;
        gap: .75rem;
        text-decoration: none;
        flex-shrink: 0;
    }
    .nav-logo-wrap {
        width: 44px; height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
        border: 1.5px solid rgba(47,111,219,.18);
        display: flex; align-items: center; justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
        transition: box-shadow .2s;
    }
    .nav-brand:hover .nav-logo-wrap {
        box-shadow: 0 0 0 4px rgba(47,111,219,.12);
    }
    html[data-theme="dark"] .nav-logo-wrap {
        background: rgba(47,111,219,.15);
        border-color: rgba(47,111,219,.25);
    }
    .nav-logo-wrap img {
        width: 34px; height: 34px;
        object-fit: contain;
    }
    .nav-brand-text { display: flex; flex-direction: column; line-height: 1; }
    .nav-brand-title {
        font-size: .9375rem;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: -.025em;
        transition: color .2s;
    }
    html[data-theme="dark"] .nav-brand-title { color: #F1F5F9; }
    .nav-brand-sub {
        font-size: .625rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #64748B;
        margin-top: 2px;
    }
    html[data-theme="dark"] .nav-brand-sub { color: #94A3B8; }

    /* ── Desktop nav links ───────────────────────────── */
    .nav-links {
        display: flex;
        align-items: center;
        gap: .25rem;
    }
    .nav-link {
        display: inline-flex;
        align-items: center;
        padding: .45rem .875rem;
        border-radius: 10px;
        font-size: .875rem;
        font-weight: 500;
        color: #475569;
        text-decoration: none;
        transition: background .18s, color .18s;
        position: relative;
    }
    .nav-link:hover {
        background: rgba(47,111,219,.08);
        color: #2F6FDB;
    }
    html[data-theme="dark"] .nav-link { color: #CBD5E1; }
    html[data-theme="dark"] .nav-link:hover { background: rgba(255,255,255,.07); color: #fff; }
    .nav-link.active {
        color: #2F6FDB;
        background: rgba(47,111,219,.1);
        font-weight: 600;
    }
    html[data-theme="dark"] .nav-link.active { color: #7DD3FC; background: rgba(47,111,219,.15); }

    /* ── Right actions ───────────────────────────────── */
    .nav-actions {
        display: flex;
        align-items: center;
        gap: .625rem;
        flex-shrink: 0;
    }

    /* Theme toggle */
    .nav-theme-btn {
        width: 38px; height: 38px;
        border-radius: 10px;
        border: 1px solid rgba(203,213,225,.7);
        background: rgba(248,250,252,.9);
        color: #64748B;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: background .18s, color .18s, border-color .18s, box-shadow .18s;
    }
    .nav-theme-btn:hover {
        background: rgba(47,111,219,.08);
        color: #2F6FDB;
        border-color: rgba(47,111,219,.3);
    }
    html[data-theme="dark"] .nav-theme-btn {
        background: rgba(255,255,255,.06);
        border-color: rgba(255,255,255,.1);
        color: #94A3B8;
    }
    html[data-theme="dark"] .nav-theme-btn:hover {
        background: rgba(255,255,255,.12);
        color: #fff;
    }

    /* Hi chip */
    .nav-hi-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .3rem .75rem;
        background: rgba(47,111,219,.1);
        color: #2F6FDB;
        font-size: .8rem;
        font-weight: 600;
        border-radius: 999px;
    }
    html[data-theme="dark"] .nav-hi-chip { background: rgba(47,111,219,.18); color: #93C5FD; }

    /* Login button */
    .nav-btn-login {
        padding: .45rem .9rem;
        border-radius: 10px;
        font-size: .875rem;
        font-weight: 600;
        color: #374151;
        text-decoration: none;
        border: 1px solid rgba(203,213,225,.8);
        background: rgba(248,250,252,.9);
        transition: background .18s, color .18s, border-color .18s;
    }
    .nav-btn-login:hover {
        background: rgba(47,111,219,.07);
        color: #2F6FDB;
        border-color: rgba(47,111,219,.3);
    }
    html[data-theme="dark"] .nav-btn-login {
        background: rgba(255,255,255,.06);
        border-color: rgba(255,255,255,.1);
        color: #CBD5E1;
    }
    html[data-theme="dark"] .nav-btn-login:hover { background: rgba(255,255,255,.1); color: #fff; }

    /* CTA / Get Started / Dashboard */
    .nav-btn-cta {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .5rem 1.1rem;
        border-radius: 12px;
        font-size: .875rem;
        font-weight: 700;
        color: #fff;
        text-decoration: none;
        background: linear-gradient(135deg, #1B3A6B 0%, #2F6FDB 100%);
        box-shadow: 0 4px 14px rgba(47,111,219,.32);
        transition: filter .18s, transform .18s, box-shadow .18s;
        border: none;
        cursor: pointer;
        letter-spacing: -.01em;
    }
    .nav-btn-cta:hover {
        filter: brightness(1.1);
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(47,111,219,.4);
    }
    .nav-btn-cta:active { transform: translateY(0); }

    /* Logout link */
    .nav-logout-link {
        font-size: .8rem;
        font-weight: 500;
        color: #94A3B8;
        text-decoration: none;
        transition: color .18s;
    }
    .nav-logout-link:hover { color: #EF4444; }

    /* ── Mobile hamburger ────────────────────────────── */
    .nav-hamburger {
        width: 38px; height: 38px;
        border-radius: 10px;
        border: 1px solid rgba(203,213,225,.7);
        background: rgba(248,250,252,.9);
        color: #64748B;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: background .18s, color .18s;
    }
    .nav-hamburger:hover { background: rgba(47,111,219,.08); color: #2F6FDB; }
    html[data-theme="dark"] .nav-hamburger {
        background: rgba(255,255,255,.06);
        border-color: rgba(255,255,255,.1);
        color: #94A3B8;
    }
    html[data-theme="dark"] .nav-hamburger:hover { background: rgba(255,255,255,.1); color: #fff; }

    /* ── Mobile drawer ───────────────────────────────── */
    .nav-drawer {
        display: none;
        padding: .75rem 1.25rem 1.25rem;
        border-top: 1px solid rgba(203,213,225,.5);
        animation: drawerIn .22s ease both;
    }
    html[data-theme="dark"] .nav-drawer { border-top-color: rgba(255,255,255,.07); }
    @keyframes drawerIn {
        from { opacity:0; transform:translateY(-8px); }
        to   { opacity:1; transform:none; }
    }
    .nav-drawer.open { display: block; }

    .drawer-link {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .65rem .875rem;
        border-radius: 10px;
        font-size: .9375rem;
        font-weight: 500;
        color: #374151;
        text-decoration: none;
        transition: background .15s, color .15s;
    }
    .drawer-link:hover { background: rgba(47,111,219,.08); color: #2F6FDB; }
    html[data-theme="dark"] .drawer-link { color: #CBD5E1; }
    html[data-theme="dark"] .drawer-link:hover { background: rgba(255,255,255,.07); color: #fff; }

    .drawer-divider {
        height: 1px;
        background: rgba(203,213,225,.5);
        margin: .75rem 0;
    }
    html[data-theme="dark"] .drawer-divider { background: rgba(255,255,255,.07); }

    .drawer-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: .7rem 1rem;
        border-radius: 12px;
        font-size: .9375rem;
        font-weight: 700;
        text-decoration: none;
        transition: filter .18s, transform .18s;
        margin-bottom: .5rem;
    }
    .drawer-btn:last-child { margin-bottom: 0; }
    .drawer-btn-primary {
        background: linear-gradient(135deg, #1B3A6B 0%, #2F6FDB 100%);
        color: #fff;
        box-shadow: 0 4px 14px rgba(47,111,219,.28);
    }
    .drawer-btn-primary:hover { filter: brightness(1.08); }
    .drawer-btn-outline {
        background: transparent;
        color: #374151;
        border: 1.5px solid rgba(203,213,225,.8);
    }
    .drawer-btn-outline:hover { background: rgba(47,111,219,.07); color: #2F6FDB; }
    html[data-theme="dark"] .drawer-btn-outline { color: #CBD5E1; border-color: rgba(255,255,255,.1); }
    html[data-theme="dark"] .drawer-btn-outline:hover { background: rgba(255,255,255,.07); color: #fff; }

    /* ── Hide / show breakpoints ─────────────────────── */
    .nav-desktop { display: none; }
    .nav-mobile  { display: flex; }
    @media (min-width: 768px) {
        .nav-desktop { display: flex; }
        .nav-mobile  { display: none; }
    }
    .nav-links-group { display: none; }
    @media (min-width: 1024px) {
        .nav-links-group { display: flex; }
    }

    /* ── Main content offset ─────────────────────────── */
    .public-main { padding-top: calc(var(--nav-h) + 10px); }

    /* ── Progress bar ────────────────────────────────── */
    #scroll-progress {
        position: fixed;
        top: 0; left: 0;
        height: 3px;
        background: linear-gradient(90deg, #1B3A6B, #2F6FDB, #60A5FA);
        width: 0%;
        z-index: 1001;
        transition: width .1s linear;
        border-radius: 0 3px 3px 0;
    }
    </style>
</head>
<body class="pust-public-body pust-public-layout antialiased">

<!-- Scroll progress bar -->
<div id="scroll-progress" aria-hidden="true"></div>

<!-- Page loader -->
<div id="page-loader">
    <?= renderLogo('lg') ?>
    <div class="spinner w-10 h-10"></div>
</div>

<!-- Toast container -->
<div class="toast-container"></div>
<?php if ($msg = flash('success')): ?><div data-toast="<?= e($msg) ?>" data-toast-type="success" class="hidden"></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div data-toast="<?= e($msg) ?>" data-toast-type="error" class="hidden"></div><?php endif; ?>

<!-- ═══════════════════════════════ HEADER ══════════════════════════════ -->
<div id="site-header" role="banner">
    <nav class="nav-pill" aria-label="Main navigation">
        <div class="nav-inner">

            <!-- Brand -->
            <a href="<?= appUrl() ?>" class="nav-brand" aria-label="PUST Help Desk – Home">
                <span class="nav-logo-wrap">
                    <img src="<?= e($_logoUrl) ?>" alt="PUST Crest" width="34" height="34">
                </span>
                <span class="nav-brand-text">
                    <span class="nav-brand-title">PUST Help Desk</span>
                    <span class="nav-brand-sub">Puntland University</span>
                </span>
            </a>

            <!-- Desktop nav links (lg+) -->
            <div class="nav-links-group nav-links" role="list">
                <?php
                $currentPage = basename($_SERVER['PHP_SELF']);
                $navItems = [
                    ['href' => appUrl(),              'label' => 'Home',    'page' => 'index.php'],
                    ['href' => appUrl('about.php'),   'label' => 'About',   'page' => 'about.php'],
                    ['href' => appUrl('contact.php'), 'label' => 'Contact', 'page' => 'contact.php'],
                ];
                foreach ($navItems as $item):
                    $active = ($currentPage === $item['page']) ? ' active' : '';
                ?>
                <a href="<?= $item['href'] ?>" class="nav-link<?= $active ?>" role="listitem"><?= $item['label'] ?></a>
                <?php endforeach; ?>
            </div>

            <!-- Desktop actions (md+) -->
            <div class="nav-actions nav-desktop">
                <!-- Theme toggle -->
                <button id="theme-toggle" type="button" data-theme-toggle class="nav-theme-btn" title="Toggle theme" aria-label="Toggle light/dark theme">
                    <svg id="theme-icon-moon" class="w-4 h-4 theme-icon-moon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg id="theme-icon-sun" class="w-4 h-4 theme-icon-sun" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                <?php if (isLoggedIn()): ?>
                    <span class="nav-hi-chip">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0"/></svg>
                        <?= e(currentUser()['first_name']) ?>
                    </span>
                    <a href="<?= appUrl(userRole() . '/dashboard.php') ?>" class="nav-btn-cta">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                        Dashboard
                    </a>
                    <a href="<?= appUrl('logout.php') ?>" class="nav-logout-link">Logout</a>
                <?php else: ?>
                    <a href="<?= appUrl('login.php') ?>" class="nav-btn-login">Login</a>
                    <a href="<?= appUrl('register.php') ?>" class="nav-btn-cta">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                        Get Started
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile actions -->
            <div class="nav-actions nav-mobile">
                <button id="theme-toggle-mobile" type="button" data-theme-toggle class="nav-theme-btn" title="Toggle theme" aria-label="Toggle theme">
                    <svg class="w-4 h-4 theme-icon-moon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg class="w-4 h-4 theme-icon-sun" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                <button id="mobile-menu-btn" class="nav-hamburger" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-drawer">
                    <svg id="hamburger-icon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg id="close-icon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile drawer -->
        <div id="mobile-drawer" class="nav-drawer" role="navigation" aria-label="Mobile navigation">
            <a href="<?= appUrl() ?>" class="drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                Home
            </a>
            <a href="<?= appUrl('about.php') ?>" class="drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
                About
            </a>
            <a href="<?= appUrl('contact.php') ?>" class="drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                Contact
            </a>
            <div class="drawer-divider"></div>
            <?php if (isLoggedIn()): ?>
                <a href="<?= appUrl(userRole() . '/dashboard.php') ?>" class="drawer-btn drawer-btn-primary">
                    Dashboard
                </a>
                <a href="<?= appUrl('logout.php') ?>" class="drawer-btn drawer-btn-outline">
                    Logout
                </a>
            <?php else: ?>
                <a href="<?= appUrl('login.php') ?>" class="drawer-btn drawer-btn-outline">
                    Login
                </a>
                <a href="<?= appUrl('register.php') ?>" class="drawer-btn drawer-btn-primary">
                    Get Started
                </a>
            <?php endif; ?>
        </div>
    </nav>
</div>
<!-- ══════════════════════════════════════════════════════════════════════ -->

<main class="public-main flex-grow">

<script>
/* ── Mobile menu toggle ───────────────────────────── */
(function () {
    const btn     = document.getElementById('mobile-menu-btn');
    const drawer  = document.getElementById('mobile-drawer');
    const hamIcon = document.getElementById('hamburger-icon');
    const xIcon   = document.getElementById('close-icon');
    if (!btn || !drawer) return;

    btn.addEventListener('click', () => {
        const open = drawer.classList.toggle('open');
        btn.setAttribute('aria-expanded', open);
        hamIcon.style.display = open ? 'none' : '';
        xIcon.style.display   = open ? '' : 'none';
    });

    /* Close drawer on outside click */
    document.addEventListener('click', (e) => {
        if (!btn.contains(e.target) && !drawer.contains(e.target)) {
            drawer.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
            hamIcon.style.display = '';
            xIcon.style.display   = 'none';
        }
    });

    /* Sync mobile theme toggle → desktop toggle */
    const mobileTheme = document.getElementById('theme-toggle-mobile');
    const desktopTheme = document.getElementById('theme-toggle');
    if (mobileTheme && desktopTheme) {
        mobileTheme.addEventListener('click', () => desktopTheme.click());
    }
})();

/* ── Scroll effects ────────────────────────────────── */
(function () {
    const header   = document.getElementById('site-header');
    const progress = document.getElementById('scroll-progress');
    if (!header) return;

    function onScroll() {
        const scrolled = window.scrollY > 20;
        header.classList.toggle('scrolled', scrolled);

        if (progress) {
            const total  = document.documentElement.scrollHeight - window.innerHeight;
            const pct    = total > 0 ? (window.scrollY / total * 100) : 0;
            progress.style.width = pct + '%';
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
})();
</script>
