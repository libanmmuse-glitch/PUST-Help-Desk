<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (isLoggedIn()) {
    redirectToDashboard();
}

if (isPost()) {
    requireCsrf();
    $name = sanitizeString((string) input('name', ''));
    $email = strtolower(trim((string) input('email', '')));
    $subject = sanitizeString((string) input('subject', ''));
    $message = trim((string) input('message', ''));

    if ($name === '' || !validateEmail($email) || $subject === '' || $message === '') {
        flash('error', 'Please complete all required fields with a valid email address.');
    } else {
        processContactFormSubmission($name, $email, $subject, $message);
        flash('success', 'Thank you. Your message has been received.');
        redirect(appUrl('contact.php'));
    }
}

$pageTitle = 'Contact';
require __DIR__ . '/includes/templates/public-header.php';

$contactCards = [
    [
        'label' => 'Email Support',
        'value' => 'support@pust.edu.so',
        'href' => 'mailto:support@pust.edu.so',
        'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'note' => 'Best for general inquiries and follow-up',
        'accent' => 'bg-pust-primary/10 text-pust-primary',
    ],
    [
        'label' => 'Phone Support',
        'value' => '+252 907 789 916',
        'href' => 'tel:+252907789916',
        'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
        'note' => 'Use for urgent assistance during office hours',
        'accent' => 'bg-pust-amber/10 text-pust-amber',
    ],
    [
        'label' => 'Campus Location',
        'value' => 'PUST Campus, Galkacyo',
        'href' => '',
        'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
        'note' => 'Israac-Siinay-Biixi Road',
        'accent' => 'bg-pust-emerald/10 text-pust-emerald',
    ],
    [
        'label' => 'Office Hours',
        'value' => '8:00 AM - 5:30 PM',
        'href' => '',
        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'note' => 'Saturday - Thursday',
        'accent' => 'bg-slate-100 text-slate-700',
    ],
];

$guidance = [
    [
        'title' => 'Use the dashboard for ticketed support',
        'text' => 'If you are registered, sign in and submit a ticket so your request can be tracked, prioritized, and assigned to the right team.',
        'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    ],
    [
        'title' => 'Include complete details',
        'text' => 'For faster handling, mention your name, department, student ID, or staff ID when it is relevant to your request.',
        'icon' => 'M9 12h6m-6 4h6M9 8h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z',
    ],
    [
        'title' => 'Protect sensitive information',
        'text' => 'Please avoid sharing passwords, access codes, or other confidential credentials in the contact form.',
        'icon' => 'M12 11c0-1.105.895-2 2-2s2 .895 2 2v2h1a1 1 0 011 1v6a1 1 0 01-1 1H7a1 1 0 01-1-1v-6a1 1 0 011-1h1v-2a4 4 0 018 0v2',
    ],
];
?>

<section class="relative overflow-hidden bg-gradient-pust-hero text-white">
    <div class="absolute inset-0 opacity-30">
        <div class="absolute -top-20 right-0 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-28 left-[-4rem] h-80 w-80 rounded-full bg-pust-amber/20 blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/15 border border-white/20 rounded-full text-sm font-medium mb-6 backdrop-blur">
                    <svg class="w-4 h-4 text-pust-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
                    Contact Support
                </span>
                <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-6">Connect with the help desk team</h1>
                <p class="text-slate-200 text-lg md:text-xl leading-relaxed max-w-2xl">
                    Reach out for general support, office guidance, or service questions. If you already have an account, sign in and submit a ticket for tracked assistance.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="<?= appUrl('login.php') ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-white text-pust-navy font-semibold shadow-lg shadow-black/10 hover:-translate-y-0.5 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Sign In
                    </a>
                    <a href="<?= appUrl('register.php') ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg border border-white/25 text-white font-semibold hover:bg-white/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v6m3-3h-6M5 20h14a1 1 0 001-1v-1a4 4 0 00-4-4H8a4 4 0 00-4 4v1a1 1 0 001 1zm9-12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Create Account
                    </a>
                </div>
            </div>

            <div class="grid gap-4">
                <?php foreach ($contactCards as $card): ?>
                    <article class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur-md p-5 shadow-2xl shadow-black/10">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl <?= e($card['accent']) ?> flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e($card['icon']) ?>"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm uppercase tracking-wider text-slate-300 font-semibold mb-1"><?= e($card['label']) ?></p>
                                <?php if ($card['href']): ?>
                                    <a href="<?= e($card['href']) ?>" class="text-white text-base md:text-lg font-semibold hover:text-pust-amber transition-colors"><?= e($card['value']) ?></a>
                                <?php else: ?>
                                    <p class="text-white text-base md:text-lg font-semibold"><?= e($card['value']) ?></p>
                                <?php endif; ?>
                                <p class="text-sm text-slate-300 mt-1"><?= e($card['note']) ?></p>
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
        <div class="grid lg:grid-cols-3 gap-4">
            <?php foreach ($guidance as $item): ?>
                <article class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-pust-primary/10 text-pust-primary flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e($item['icon']) ?>"/></svg>
                    </div>
                    <h2 class="text-lg font-bold text-pust-navy mb-2"><?= e($item['title']) ?></h2>
                    <p class="text-sm text-slate-600 leading-relaxed"><?= e($item['text']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid lg:grid-cols-5 gap-8 items-start">
        <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 md:p-8">
            <div class="mb-7">
                <p class="text-sm font-semibold uppercase tracking-wider text-pust-amber mb-2">Send a Message</p>
                <h2 class="text-2xl md:text-3xl font-bold text-pust-navy mb-3">Contact the support team</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Use this form for general inquiries, service questions, or campus support requests that are not yet tied to an existing ticket.
                </p>
            </div>

            <form method="POST" data-validate class="space-y-5">
                <?= csrfField() ?>
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            <input type="text" name="name" required placeholder="Your full name" class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pust-primary/20 focus:border-pust-primary">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <input type="email" name="email" required placeholder="you@example.com" class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pust-primary/20 focus:border-pust-primary">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Subject *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M9 8h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                        </span>
                        <input type="text" name="subject" required maxlength="255" placeholder="How can we help?" class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pust-primary/20 focus:border-pust-primary">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Message *</label>
                    <div class="relative">
                        <span class="absolute top-4 left-0 pl-4 text-slate-400 pointer-events-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m-8 8h10a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <textarea name="message" required rows="7" placeholder="Describe your request clearly, including any relevant details or deadlines." class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pust-primary/20 focus:border-pust-primary"></textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 pust-btn-primary font-semibold rounded-xl shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
                        Send Message
                    </button>
                    <a href="<?= appUrl('login.php') ?>" class="text-sm font-semibold text-pust-navy hover:text-pust-amber transition-colors">
                        Sign in to submit a tracked ticket
                    </a>
                </div>
            </form>
        </div>

        <aside class="lg:col-span-2 space-y-5">
            <div class="bg-pust-navy text-white rounded-2xl p-6 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-white/10 text-pust-amber flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-xl font-bold mb-3">Tracked support works best from your account</h2>
                <p class="text-sm text-slate-300 leading-relaxed mb-5">
                    Create an account or sign in to submit a ticket, upload attachments, and monitor progress from your dashboard.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="<?= appUrl('login.php') ?>" class="px-4 py-2 bg-white text-pust-navy rounded-lg text-sm font-semibold hover:-translate-y-0.5 transition-transform">Sign In</a>
                    <a href="<?= appUrl('register.php') ?>" class="px-4 py-2 border border-white/30 rounded-lg text-sm font-semibold hover:bg-white/10 transition-colors">Register</a>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="font-bold text-pust-navy mb-4">Quick Tips</h3>
                <ul class="space-y-3 text-sm text-slate-600">
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-pust-amber flex-shrink-0"></span>
                        Use the dashboard for issues that need a response or tracking number.
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-pust-amber flex-shrink-0"></span>
                        Include your department, faculty, or ID number when it helps identify your request.
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-pust-amber flex-shrink-0"></span>
                        Do not share passwords, OTPs, or sensitive credentials through this form.
                    </li>
                </ul>
            </div>
        </aside>
    </div>
</section>

<?php require __DIR__ . '/includes/templates/public-footer.php'; ?>
