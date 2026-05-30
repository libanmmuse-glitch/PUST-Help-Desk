<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (isLoggedIn()) {
    redirectToDashboard();
}

$pageTitle = 'About';
require __DIR__ . '/includes/templates/public-header.php';

$highlights = [
    [
        'label' => 'Unified support',
        'value' => 'One platform for all campus service requests',
        'icon' => 'M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm3 2h10M7 12h10M7 16h6',
    ],
    [
        'label' => 'Clear accountability',
        'value' => 'Every ticket is tracked, assigned, and resolved',
        'icon' => 'M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z',
    ],
    [
        'label' => 'Responsive service',
        'value' => 'Faster follow-up for students, staff, and lecturers',
        'icon' => 'M12 8v4l3 3m6 1a9 9 0 11-18 0 9 9 0 0118 0z',
    ],
];

$serviceCards = [
    [
        'title' => 'Student Support',
        'text' => 'Manage academic, account, and facility-related requests in one place with a transparent status trail.',
        'icon' => 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    ],
    [
        'title' => 'Staff Coordination',
        'text' => 'Route issues to the right department quickly so teams can collaborate without manual follow-up.',
        'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 11a3 3 0 11-6 0 3 3 0 016 0zm6 2a3 3 0 100-6 3 3 0 000 6zm-6-7a3 3 0 100-6 3 3 0 000 6z',
    ],
    [
        'title' => 'Transparent Workflow',
        'text' => 'Track progress from submission to closure, including replies, updates, and attached evidence.',
        'icon' => 'M9 12h6m-6 4h6M9 8h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z',
    ],
    [
        'title' => 'Department Oversight',
        'text' => 'Help administrators monitor ticket volume, service trends, and workload distribution across the campus.',
        'icon' => 'M9 19V6l12-2v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-2c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z',
    ],
];

$pillars = [
    [
        'title' => 'Mission',
        'text' => 'Deliver a reliable help desk that gives the university a professional, organized, and measurable support experience.',
        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
        'badgeClass' => 'bg-pust-amber/15 text-pust-amber',
    ],
    [
        'title' => 'Vision',
        'text' => 'Become the trusted digital service desk that improves response times, visibility, and user confidence.',
        'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z',
        'badgeClass' => 'bg-pust-primary/10 text-pust-primary',
    ],
    [
        'title' => 'Commitment',
        'text' => 'Keep support work secure, accountable, and easy to understand for every user and department.',
        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'badgeClass' => 'bg-pust-emerald/10 text-pust-emerald',
    ],
];

$values = [
    [
        'title' => 'Accountability',
        'text' => 'Each ticket has a responsible owner, a visible status, and a full history of action taken.',
        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    ],
    [
        'title' => 'Clarity',
        'text' => 'Users can see what is happening without chasing updates or relying on informal messages.',
        'icon' => 'M4 6h16M4 12h16M4 18h10',
    ],
    [
        'title' => 'Security',
        'text' => 'Role-based access helps protect sensitive information and keeps workflows controlled.',
        'icon' => 'M12 11c0-1.105.895-2 2-2s2 .895 2 2v2h1a1 1 0 011 1v6a1 1 0 01-1 1H7a1 1 0 01-1-1v-6a1 1 0 011-1h1v-2a4 4 0 018 0v2',
    ],
    [
        'title' => 'Service',
        'text' => 'The platform is designed to help teams respond faster and improve the overall support experience.',
        'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 11a3 3 0 11-6 0 3 3 0 016 0z',
    ],
];
?>

<section class="relative overflow-hidden bg-gradient-pust-hero text-white">
    <div class="absolute inset-0 opacity-30">
        <div class="absolute -top-24 right-0 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-28 left-[-4rem] h-80 w-80 rounded-full bg-pust-amber/20 blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/15 border border-white/20 rounded-full text-sm font-medium mb-6 backdrop-blur">
                    <svg class="w-4 h-4 text-pust-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    About PUST Help Desk
                </span>
                <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-6">Professional campus support, organized in one place</h1>
                <p class="text-slate-200 text-lg md:text-xl leading-relaxed max-w-2xl">
                    PUST Help Desk is a secure and streamlined service platform built to help students, lecturers, staff, and administrators submit, manage, and resolve support requests with clarity and accountability.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="<?= appUrl('contact.php') ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-white text-pust-navy font-semibold shadow-lg shadow-black/10 hover:-translate-y-0.5 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
                        Contact Support
                    </a>
                    <a href="<?= appUrl('login.php') ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg border border-white/25 text-white font-semibold hover:bg-white/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Sign In
                    </a>
                </div>
            </div>
            <div class="grid gap-4">
                <?php foreach ($highlights as $highlight): ?>
                    <article class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur-md p-5 shadow-2xl shadow-black/10">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-white/15 text-pust-amber flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e($highlight['icon']) ?>"/></svg>
                            </div>
                            <div>
                                <p class="text-sm uppercase tracking-wider text-slate-300 font-semibold mb-1"><?= e($highlight['label']) ?></p>
                                <p class="text-white text-base leading-relaxed"><?= e($highlight['value']) ?></p>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-end justify-between gap-6 mb-6">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-wider text-pust-amber mb-2">Platform Snapshot</p>
                <h2 class="text-2xl md:text-3xl font-bold text-pust-navy">A quick look at the support experience</h2>
            </div>
            <p class="hidden md:block text-sm text-slate-500 max-w-md text-right">
                These metrics highlight the coverage, visibility, and security built into the help desk workflow.
            </p>
        </div>
        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <?php
            $stats = [
                [
                    'value' => '4',
                    'label' => 'User roles supported',
                    'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 11a3 3 0 11-6 0 3 3 0 016 0zm6 2a3 3 0 100-6 3 3 0 000 6zm-6-7a3 3 0 100-6 3 3 0 000 6z',
                    'accent' => 'text-pust-primary',
                    'badge' => 'bg-pust-primary/10 ring-pust-primary/10',
                    'delay' => '0s',
                    'note' => 'Students, staff, lecturers, and admins',
                ],
                [
                    'value' => '24/7',
                    'label' => 'Ticket visibility',
                    'icon' => 'M12 8v4l3 3m6 1a9 9 0 11-18 0 9 9 0 0118 0z',
                    'accent' => 'text-pust-amber',
                    'badge' => 'bg-pust-amber/10 ring-pust-amber/10',
                    'delay' => '0.12s',
                    'note' => 'Always available for progress tracking',
                ],
                [
                    'value' => '6+',
                    'label' => 'Campus departments',
                    'icon' => 'M3 21h18M5 21V7l8-4 8 4v14M9 21v-8h6v8M11 9h2',
                    'accent' => 'text-pust-emerald',
                    'badge' => 'bg-pust-emerald/10 ring-pust-emerald/10',
                    'delay' => '0.24s',
                    'note' => 'Cross-department support routing',
                ],
                [
                    'value' => 'Secure',
                    'label' => 'Role-based access control',
                    'icon' => 'M12 11c0-1.105.895-2 2-2s2 .895 2 2v2h1a1 1 0 011 1v6a1 1 0 01-1 1H7a1 1 0 01-1-1v-6a1 1 0 011-1h1v-2a4 4 0 018 0v2',
                    'accent' => 'text-slate-700',
                    'badge' => 'bg-slate-100 ring-slate-200',
                    'delay' => '0.36s',
                    'note' => 'Controlled access for protected data',
                ],
            ];
            foreach ($stats as $stat):
            ?>
                <article class="about-stat-card group bg-white border border-slate-200 rounded-3xl p-6 shadow-sm overflow-hidden" style="animation-delay: <?= e($stat['delay']) ?>;">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl <?= e($stat['badge']) ?> ring-1 flex items-center justify-center shadow-sm transition-transform duration-300 group-hover:scale-105 group-hover:-rotate-3">
                            <svg class="w-6 h-6 <?= e($stat['accent']) ?> about-stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e($stat['icon']) ?>"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 mb-2">Key Metric</p>
                            <p class="text-3xl font-bold text-pust-navy leading-none about-stat-value"><?= e($stat['value']) ?></p>
                            <p class="text-sm font-medium text-slate-700 mt-3"><?= e($stat['label']) ?></p>
                            <p class="text-sm text-slate-500 mt-1 leading-relaxed"><?= e($stat['note']) ?></p>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid lg:grid-cols-2 gap-12 items-start">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-pust-amber mb-3">Who It Serves</p>
            <h2 class="text-3xl md:text-4xl font-bold text-pust-navy mb-5">Built for clear, accountable support</h2>
            <p class="text-slate-600 leading-relaxed mb-4">
                The platform centralizes university support requests so every issue has an owner, a status, a response history, and a documented resolution path.
            </p>
            <p class="text-slate-600 leading-relaxed">
                Instead of scattered messages and manual follow-ups, users can submit tickets, attach files, receive updates, and monitor progress from a clean dashboard experience.
            </p>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <?php foreach ($serviceCards as $card): ?>
                <article class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-pust-hover transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-pust-primary/10 text-pust-primary flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e($card['icon']) ?>"/></svg>
                    </div>
                    <h3 class="font-bold text-pust-navy mb-2"><?= e($card['title']) ?></h3>
                    <p class="text-sm text-slate-600 leading-relaxed"><?= e($card['text']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="bg-white border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid lg:grid-cols-3 gap-6">
            <?php foreach ($pillars as $pillar): ?>
                <article class="rounded-2xl border border-slate-200 p-6 bg-slate-50">
                    <div class="w-12 h-12 rounded-xl <?= e($pillar['badgeClass']) ?> flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e($pillar['icon']) ?>"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-pust-navy mb-3"><?= e($pillar['title']) ?></h2>
                    <p class="text-sm text-slate-600 leading-relaxed"><?= e($pillar['text']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="max-w-2xl mb-10">
        <p class="text-sm font-semibold uppercase tracking-wider text-pust-amber mb-2">Core Values</p>
        <h2 class="text-3xl md:text-4xl font-bold text-pust-navy">Principles behind the platform</h2>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <?php foreach ($values as $value): ?>
            <article class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="w-11 h-11 rounded-xl bg-pust-navy text-white flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e($value['icon']) ?>"/></svg>
                </div>
                <h3 class="font-bold text-pust-navy mb-2"><?= e($value['title']) ?></h3>
                <p class="text-sm text-slate-600 leading-relaxed"><?= e($value['text']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
    <div class="rounded-3xl bg-gradient-pust-hero text-white overflow-hidden relative">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-0 right-0 h-56 w-56 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 h-56 w-56 rounded-full bg-pust-amber/20 blur-3xl"></div>
        </div>
        <div class="relative px-6 py-10 md:px-10 md:py-12 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="max-w-2xl">
                <p class="text-sm uppercase tracking-wider text-slate-300 font-semibold mb-2">Ready for campus use</p>
                <h2 class="text-2xl md:text-3xl font-bold mb-3">A professional help desk experience for the whole university community</h2>
                <p class="text-slate-200 leading-relaxed">
                    From request submission to final resolution, PUST Help Desk is designed to keep support efficient, visible, and easy to trust.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="<?= appUrl('register.php') ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-white text-pust-navy font-semibold hover:-translate-y-0.5 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v6m3-3h-6M5 20h14a1 1 0 001-1v-1a4 4 0 00-4-4H8a4 4 0 00-4 4v1a1 1 0 001 1zm9-12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Create Account
                </a>
                <a href="<?= appUrl('contact.php') ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg border border-white/25 text-white font-semibold hover:bg-white/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
                    Get in Touch
                </a>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/templates/public-footer.php'; ?>
