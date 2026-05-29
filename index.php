<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (isLoggedIn()) {
    redirectToDashboard();
}
$pageTitle = 'Home';
require __DIR__ . '/includes/templates/public-header.php';
?>
<section class="bg-gradient-pust-hero text-white">
    <div class="max-w-7xl mx-auto px-4 py-20 md:py-28 grid md:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-block px-3 py-1 bg-white/20 text-white rounded-full text-sm font-medium mb-4">PUST University Support</span>
            <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6">Your Campus Support, <span class="text-pust-amber">Simplified</span></h1>
            <p class="text-slate-300 text-lg mb-8">Submit complaints, track requests, and get help from ICT, Finance, Registrar, Library, and more — all in one professional help desk platform.</p>
            <div class="flex flex-wrap gap-4">
                <a href="<?= appUrl('register.php') ?>" class="px-6 py-3 pust-btn-accent font-semibold rounded-lg transition">Get Started</a>
                <a href="<?= appUrl('login.php') ?>" class="px-6 py-3 pust-btn-outline-white font-semibold rounded-lg transition">Sign In</a>
            </div>
        </div>
        <div class="hidden md:flex flex-col items-center justify-center">
            <div class="dashboard-card bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/20 w-full max-w-md">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/10 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold text-pust-amber">24/7</p>
                        <p class="text-sm text-slate-300 mt-1">Ticket Tracking</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold text-pust-amber">6</p>
                        <p class="text-sm text-slate-300 mt-1">Departments</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold text-pust-amber">Fast</p>
                        <p class="text-sm text-slate-300 mt-1">Response Times</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold text-pust-amber">Secure</p>
                        <p class="text-sm text-slate-300 mt-1">Platform</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="how-it-works max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="text-center max-w-2xl mx-auto mb-14">
        <p class="text-sm font-semibold uppercase tracking-wider text-pust-amber mb-2">Simple process</p>
        <h2 class="text-3xl md:text-4xl font-bold text-pust-navy mb-4">How It Works</h2>
        <p class="text-slate-600 leading-relaxed">From sign-up to resolution — three clear steps to get the support you need across campus.</p>
    </div>
    <div class="grid md:grid-cols-3 gap-8 lg:gap-10">
        <article class="how-it-works-step group relative bg-white rounded-2xl p-8 border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-pust-amber/30 transition-all duration-300 text-center">
            <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-pust-primary text-white text-xs font-bold">Step 1</span>
            <div class="w-16 h-16 rounded-2xl pust-icon-box flex items-center justify-center mx-auto mb-6 mt-2 group-hover:scale-105 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"/>
                </svg>
            </div>
            <h3 class="font-bold text-xl text-pust-navy mb-3">Register &amp; Sign In</h3>
            <p class="text-slate-600 text-sm leading-relaxed mb-4">Create your account as a student or staff member. Students select their faculty; staff link to their help desk department. Use your university email and a secure password.</p>
            <ul class="text-left text-sm text-slate-500 space-y-2 max-w-xs mx-auto">
                <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber mt-1.5 flex-shrink-0"></span>Student or staff registration</li>
                <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber mt-1.5 flex-shrink-0"></span>Faculty &amp; department selection</li>
                <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber mt-1.5 flex-shrink-0"></span>One login for all services</li>
            </ul>
        </article>
        <article class="how-it-works-step group relative bg-white rounded-2xl p-8 border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-pust-amber/30 transition-all duration-300 text-center">
            <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-pust-primary text-white text-xs font-bold">Step 2</span>
            <div class="w-16 h-16 rounded-2xl pust-icon-box flex items-center justify-center mx-auto mb-6 mt-2 group-hover:scale-105 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c1.01.005 1.955.144 2.84.346M9.75 3.104v.008c0 .414.336.75.75.75h.75m-4.125 0a.75.75 0 01-.75-.75v-.008c0-.414.336-.75.75-.75h.375m6 0a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75h-.375m-6 7.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </div>
            <h3 class="font-bold text-xl text-pust-navy mb-3">Submit a Support Ticket</h3>
            <p class="text-slate-600 text-sm leading-relaxed mb-4">Open a ticket for ICT, Finance, Registrar, Library, or other campus offices. Pick the right department and category, describe your issue clearly, and attach documents if needed.</p>
            <ul class="text-left text-sm text-slate-500 space-y-2 max-w-xs mx-auto">
                <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber mt-1.5 flex-shrink-0"></span>Department &amp; category routing</li>
                <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber mt-1.5 flex-shrink-0"></span>File attachments supported</li>
                <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber mt-1.5 flex-shrink-0"></span>Priority levels available</li>
            </ul>
        </article>
        <article class="how-it-works-step group relative bg-white rounded-2xl p-8 border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-pust-amber/30 transition-all duration-300 text-center">
            <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-pust-primary text-white text-xs font-bold">Step 3</span>
            <div class="w-16 h-16 rounded-2xl pust-icon-box flex items-center justify-center mx-auto mb-6 mt-2 group-hover:scale-105 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
            </div>
            <h3 class="font-bold text-xl text-pust-navy mb-3">Track &amp; Get Resolved</h3>
            <p class="text-slate-600 text-sm leading-relaxed mb-4">Follow your ticket through every stage — from pending to resolved. Get email-style notifications, read staff replies, and see a visual status tracker until your request is closed.</p>
            <ul class="text-left text-sm text-slate-500 space-y-2 max-w-xs mx-auto">
                <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber mt-1.5 flex-shrink-0"></span>Real-time status updates</li>
                <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber mt-1.5 flex-shrink-0"></span>In-app notifications</li>
                <li class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber mt-1.5 flex-shrink-0"></span>Two-way conversation thread</li>
            </ul>
        </article>
    </div>
</section>

<section class="helpdesk-supported pust-section-muted py-20 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <p class="text-sm font-semibold uppercase tracking-wider text-pust-amber mb-2">Who we serve</p>
            <h2 class="text-3xl md:text-4xl font-bold text-pust-navy mb-4">Help Desk Supported</h2>
            <p class="text-slate-600 leading-relaxed">One platform for every member of the PUST community — submit requests, track progress, and connect with the right teams from a single, secure help desk.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8 lg:gap-10">
            <article class="helpdesk-supported-card group bg-white rounded-2xl p-8 shadow-sm border border-slate-200/80 hover:shadow-xl hover:border-pust-amber/30 transition-all duration-300">
                <div class="helpdesk-supported-icon w-14 h-14 rounded-2xl pust-icon-box flex items-center justify-center mb-6 group-hover:scale-105 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.405a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-pust-navy mb-3">Students</h3>
                <p class="text-slate-600 text-sm leading-relaxed mb-5">Register with your university email, select your faculty, open tickets for ICT, finance, registrar, and library services, and follow every update until resolved.</p>
                <ul class="space-y-2 text-sm text-slate-500">
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber"></span>Submit & track support tickets</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber"></span>Attach files and receive notifications</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber"></span>View status from open to closed</li>
                </ul>
            </article>
            <article class="helpdesk-supported-card group bg-white rounded-2xl p-8 shadow-sm border border-slate-200/80 hover:shadow-xl hover:border-pust-amber/30 transition-all duration-300">
                <div class="helpdesk-supported-icon w-14 h-14 rounded-2xl pust-icon-box flex items-center justify-center mb-6 group-hover:scale-105 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-pust-navy mb-3">Lecturers</h3>
                <p class="text-slate-600 text-sm leading-relaxed mb-5">Faculty and teaching staff can request departmental support, report technical or administrative issues, and collaborate with assigned teams through structured ticket workflows.</p>
                <ul class="space-y-2 text-sm text-slate-500">
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber"></span>Department-based ticket routing</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber"></span>Priority handling for academic needs</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber"></span>Threaded replies with staff teams</li>
                </ul>
            </article>
            <article class="helpdesk-supported-card group bg-white rounded-2xl p-8 shadow-sm border border-slate-200/80 hover:shadow-xl hover:border-pust-amber/30 transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-pust-amber/5 rounded-bl-full -mr-4 -mt-4 pointer-events-none" aria-hidden="true"></div>
                <div class="helpdesk-supported-icon w-14 h-14 rounded-2xl pust-icon-box flex items-center justify-center mb-6 group-hover:scale-105 transition-transform relative">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-pust-navy mb-3">PUST Management</h3>
                <p class="text-slate-600 text-sm leading-relaxed mb-5">Administrators and department leads oversee the full help desk — assign tickets, manage users, monitor performance, and keep campus services running smoothly.</p>
                <ul class="space-y-2 text-sm text-slate-500">
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber"></span>Dashboard analytics & reporting</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber"></span>User, role & department control</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pust-amber"></span>Assign, escalate & close tickets</li>
                </ul>
            </article>
        </div>
        <p class="text-center text-sm text-slate-500 mt-12 max-w-xl mx-auto">Ready to get started? <a href="<?= appUrl('register.php') ?>" class="text-pust-navy font-semibold hover:text-pust-amber transition">Create your account</a> or <a href="<?= appUrl('login.php') ?>" class="text-pust-navy font-semibold hover:text-pust-amber transition">sign in</a> if you already have one.</p>
    </div>
</section>

<section class="bg-white py-20 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <p class="text-sm font-semibold uppercase tracking-wider text-pust-amber mb-2">Get started</p>
            <h2 class="text-3xl md:text-4xl font-bold text-pust-navy mb-4">Registration &amp; Support Categories</h2>
            <p class="text-slate-600 leading-relaxed">Students register under their faculty; staff link to a help desk department. Submit tickets under the right category when you need support.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-6 max-w-3xl mx-auto mb-16">
            <a href="<?= appUrl('register.php?type=student') ?>" class="register-category-card group flex gap-5 p-6 rounded-2xl border-2 border-slate-200 hover:border-pust-amber hover:shadow-lg transition-all bg-white">
                <div class="w-14 h-14 flex-shrink-0 rounded-2xl bg-pust-primary text-white flex items-center justify-center group-hover:scale-105 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.405a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-pust-navy mb-1">Student Registration</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">For enrolled students — register with Student ID, select your faculty, and open tickets across ICT, Finance, Registrar, Library, and more.</p>
                    <span class="inline-flex items-center gap-1 mt-3 text-sm font-semibold text-pust-navy group-hover:text-pust-amber transition">Register as student →</span>
                </div>
            </a>
            <a href="<?= appUrl('register.php?type=staff') ?>" class="register-category-card group flex gap-5 p-6 rounded-2xl border-2 border-slate-200 hover:border-pust-amber hover:shadow-lg transition-all bg-white">
                <div class="w-14 h-14 flex-shrink-0 rounded-2xl bg-pust-primary text-white flex items-center justify-center group-hover:scale-105 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-pust-navy mb-1">Staff Registration</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">For lecturers and university staff — link to your help desk department, handle assigned tickets, and collaborate with campus support teams.</p>
                    <span class="inline-flex items-center gap-1 mt-3 text-sm font-semibold text-pust-navy group-hover:text-pust-amber transition">Register as staff →</span>
                </div>
            </a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/templates/public-footer.php'; ?>
