<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (isLoggedIn()) {
    redirectToDashboard();
}

$pageTitle = 'About';
require __DIR__ . '/includes/templates/public-header.php';
?>
<section class="bg-gradient-pust-hero text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-24">
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/15 border border-white/20 rounded-full text-sm font-medium mb-5">
                <svg class="w-4 h-4 text-pust-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                About PUST Help Desk
            </span>
            <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6">Professional campus support, organized in one place</h1>
            <p class="text-slate-300 text-lg leading-relaxed max-w-2xl">PUST Help Desk helps students, lecturers, staff, and administrators manage support requests through a secure, transparent, and department-driven ticketing system.</p>
        </div>
    </div>
</section>

<section class="bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                <p class="text-2xl font-bold text-pust-navy">4</p>
                <p class="text-sm text-slate-500 mt-1">User roles supported</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                <p class="text-2xl font-bold text-pust-navy">6+</p>
                <p class="text-sm text-slate-500 mt-1">Campus departments</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                <p class="text-2xl font-bold text-pust-navy">24/7</p>
                <p class="text-sm text-slate-500 mt-1">Ticket visibility</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                <p class="text-2xl font-bold text-pust-navy">Secure</p>
                <p class="text-sm text-slate-500 mt-1">Role-based access</p>
            </div>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid lg:grid-cols-2 gap-10 items-start">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-pust-amber mb-2">Company Overview</p>
            <h2 class="text-3xl font-bold text-pust-navy mb-5">Built for clear, accountable support</h2>
            <p class="text-slate-600 leading-relaxed mb-4">The Help Desk platform centralizes requests across university services so every issue has an owner, status, conversation history, and resolution path.</p>
            <p class="text-slate-600 leading-relaxed">Instead of scattered messages and manual follow-ups, users can submit tickets, attach files, receive notifications, and monitor progress from their dashboard.</p>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <?php
            $overviewCards = [
                ['title' => 'Ticket Management', 'text' => 'Submit, assign, reply, and resolve requests with a clear workflow.', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2'],
                ['title' => 'Smart Routing', 'text' => 'Departments and categories help requests reach the right team.', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4M4 17h12m0 0l-4-4m4 4l-4 4'],
                ['title' => 'Notifications', 'text' => 'Users stay informed when tickets change status or receive replies.', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5'],
                ['title' => 'Admin Control', 'text' => 'Administrators manage users, reports, departments, and categories.', 'icon' => 'M12 6V4m0 16v-2m8-6h2M2 12h2m13.657-5.657l1.414-1.414M4.929 19.071l1.414-1.414m0-11.314L4.929 4.929m14.142 14.142l-1.414-1.414M12 15a3 3 0 100-6 3 3 0 000 6z'],
            ];
            foreach ($overviewCards as $card):
            ?>
            <article class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm hover:shadow-pust-hover transition">
                <div class="w-11 h-11 rounded-lg bg-pust-primary/10 text-pust-primary flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e($card['icon']) ?>"/></svg>
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
            <article class="rounded-xl border border-slate-200 p-6 bg-slate-50">
                <div class="w-12 h-12 rounded-lg bg-pust-amber/15 text-pust-amber flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-pust-navy mb-3">Mission</h2>
                <p class="text-sm text-slate-600 leading-relaxed">To provide a reliable, easy-to-use help desk that improves service delivery, reduces delays, and keeps every support request visible from start to finish.</p>
            </article>
            <article class="rounded-xl border border-slate-200 p-6 bg-slate-50">
                <div class="w-12 h-12 rounded-lg bg-pust-primary/10 text-pust-primary flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-pust-navy mb-3">Vision</h2>
                <p class="text-sm text-slate-600 leading-relaxed">To become the university’s trusted digital service desk, enabling faster decisions, stronger accountability, and better support experiences.</p>
            </article>
            <article class="rounded-xl border border-slate-200 p-6 bg-slate-50">
                <div class="w-12 h-12 rounded-lg bg-pust-emerald/10 text-pust-emerald flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-pust-navy mb-3">Commitment</h2>
                <p class="text-sm text-slate-600 leading-relaxed">To keep support work professional, secure, organized, and measurable for every department and user group.</p>
            </article>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="max-w-2xl mb-10">
        <p class="text-sm font-semibold uppercase tracking-wider text-pust-amber mb-2">Core Values</p>
        <h2 class="text-3xl font-bold text-pust-navy">Principles behind the platform</h2>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <?php
        $values = [
            ['title' => 'Accountability', 'text' => 'Every ticket has a clear status and responsible team.', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['title' => 'Clarity', 'text' => 'Users can understand progress without repeated follow-up.', 'icon' => 'M4 6h16M4 12h16M4 18h7'],
            ['title' => 'Security', 'text' => 'Role-based access protects users, tickets, and system data.', 'icon' => 'M12 11c0-1.105.895-2 2-2s2 .895 2 2v2h1a1 1 0 011 1v6a1 1 0 01-1 1H7a1 1 0 01-1-1v-6a1 1 0 011-1h1v-2a4 4 0 018 0v2'],
            ['title' => 'Service', 'text' => 'The system is built to help departments respond faster.', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
        ];
        foreach ($values as $value):
        ?>
        <article class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-pust-navy text-white flex items-center justify-center mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e($value['icon']) ?>"/></svg>
            </div>
            <h3 class="font-bold text-pust-navy mb-2"><?= e($value['title']) ?></h3>
            <p class="text-sm text-slate-600 leading-relaxed"><?= e($value['text']) ?></p>
        </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/templates/public-footer.php'; ?>
