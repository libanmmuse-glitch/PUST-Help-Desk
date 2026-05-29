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
?>
<section class="bg-gradient-pust-hero text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-24">
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/15 border border-white/20 rounded-full text-sm font-medium mb-5">
                <svg class="w-4 h-4 text-pust-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
                Contact Support
            </span>
            <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6">Get in touch with the help desk team</h1>
            <p class="text-slate-300 text-lg leading-relaxed max-w-2xl">Send us a message or sign in to submit a ticket. Our support team will route your request to the right department.</p>
        </div>
    </div>
</section>

<section class="bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php
            $contacts = [
                ['label' => 'Email', 'value' => 'support@pust.edu.so', 'href' => 'mailto:support@pust.edu.so', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['label' => 'Phone', 'value' => '+252 907 789 916', 'href' => 'tel:+252907789916', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
                ['label' => 'Location', 'value' => 'PUST Campus, Galkacyo', 'href' => '', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Support Hours', 'value' => 'Saturday - Thursday', 'href' => '', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
            foreach ($contacts as $item):
            ?>
            <article class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                <div class="w-11 h-11 rounded-lg bg-pust-primary/10 text-pust-primary flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e($item['icon']) ?>"/></svg>
                </div>
                <p class="text-xs uppercase tracking-wider font-semibold text-slate-500 mb-1"><?= e($item['label']) ?></p>
                <?php if ($item['href']): ?>
                <a href="<?= e($item['href']) ?>" class="text-sm font-semibold text-pust-navy hover:text-pust-amber"><?= e($item['value']) ?></a>
                <?php else: ?>
                <p class="text-sm font-semibold text-pust-navy"><?= e($item['value']) ?></p>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid lg:grid-cols-5 gap-8 items-start">
        <div class="lg:col-span-3 bg-white rounded-xl border border-slate-200 shadow-sm p-6 md:p-8">
            <div class="mb-7">
                <p class="text-sm font-semibold uppercase tracking-wider text-pust-amber mb-2">Message Form</p>
                <h2 class="text-2xl md:text-3xl font-bold text-pust-navy mb-3">Send a support inquiry</h2>
                <p class="text-sm text-slate-600">Registered users should submit a ticket from the dashboard for tracking and faster routing.</p>
            </div>
            <form method="POST" data-validate class="space-y-5">
                <?= csrfField() ?>
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Name *</label>
                        <input type="text" name="name" required placeholder="Your full name" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-pust-primary/20 focus:border-pust-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email *</label>
                        <input type="email" name="email" required placeholder="you@example.com" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-pust-primary/20 focus:border-pust-primary">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Subject *</label>
                    <input type="text" name="subject" required maxlength="255" placeholder="How can we help?" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-pust-primary/20 focus:border-pust-primary">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Message *</label>
                    <textarea name="message" required rows="6" placeholder="Describe your request clearly..." class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-pust-primary/20 focus:border-pust-primary"></textarea>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <button type="submit" class="px-6 py-3 pust-btn-primary font-semibold rounded-lg">Send Message</button>
                    <a href="<?= appUrl('login.php') ?>" class="text-sm font-semibold text-pust-navy hover:text-pust-amber">Sign in to submit a ticket</a>
                </div>
            </form>
        </div>

        <aside class="lg:col-span-2 space-y-5">
            <div class="bg-pust-navy text-white rounded-xl p-6 shadow-sm">
                <div class="w-12 h-12 rounded-lg bg-white/10 text-pust-amber flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M5.636 5.636l3.536 3.536m0 5.656l-3.536 3.536M12 2v4m0 12v4m10-10h-4M6 12H2"/></svg>
                </div>
                <h2 class="text-xl font-bold mb-3">Need tracked support?</h2>
                <p class="text-sm text-slate-300 leading-relaxed mb-5">Create an account or sign in to submit a ticket, upload attachments, and track progress through your dashboard.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="<?= appUrl('login.php') ?>" class="px-4 py-2 bg-white text-pust-navy rounded-lg text-sm font-semibold">Sign In</a>
                    <a href="<?= appUrl('register.php') ?>" class="px-4 py-2 border border-white/30 rounded-lg text-sm font-semibold hover:bg-white/10">Register</a>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h3 class="font-bold text-pust-navy mb-4">Response Guidance</h3>
                <ul class="space-y-3 text-sm text-slate-600">
                    <li class="flex gap-3"><span class="mt-1 w-2 h-2 rounded-full bg-pust-amber flex-shrink-0"></span>Use your dashboard for ticket-specific issues.</li>
                    <li class="flex gap-3"><span class="mt-1 w-2 h-2 rounded-full bg-pust-amber flex-shrink-0"></span>Include your department, faculty, or student ID when relevant.</li>
                    <li class="flex gap-3"><span class="mt-1 w-2 h-2 rounded-full bg-pust-amber flex-shrink-0"></span>Do not include passwords or sensitive credentials.</li>
                </ul>
            </div>
        </aside>
    </div>
</section>
<?php require __DIR__ . '/includes/templates/public-footer.php'; ?>
