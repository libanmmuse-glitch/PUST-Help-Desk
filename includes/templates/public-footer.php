</main>
<footer class="pust-footer mt-auto" role="contentinfo">
    <div class="pust-footer__glow" aria-hidden="true"></div>

    <div class="pust-footer__cta">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="pust-footer__cta-inner">
                <div>
                    <p class="pust-footer__cta-label">Need assistance?</p>
                    <h2 class="pust-footer__cta-title">Get support from PUST Help Desk</h2>
                    <p class="pust-footer__cta-text">Submit tickets, track progress, and connect with the right department — all in one place.</p>
                </div>
                <div class="pust-footer__cta-actions">
                    <a href="<?= appUrl('register.php') ?>" class="pust-btn-accent px-6 py-3 rounded-lg font-semibold text-sm">Create Account</a>
                    <a href="<?= appUrl('login.php') ?>" class="pust-footer__cta-outline px-6 py-3 rounded-lg font-semibold text-sm">Sign In</a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
        <div class="pust-footer__grid">
            <div class="pust-footer__brand">
                <a href="<?= appUrl() ?>" class="pust-footer__logo-link">
                    <?= renderLogo('sm', 'PUST Help Desk') ?>
                    <span class="pust-footer__brand-name">PUST Help Desk</span>
                </a>
                <p class="pust-footer__tagline">
                    Professional support ticket management for Puntland University of Science &amp; Technology students, lecturers, and staff.
                </p>
                <p class="pust-footer__badge">Official university support portal</p>
            </div>

            <div>
                <h3 class="pust-footer__heading">Quick Links</h3>
                <ul class="pust-footer__links">
                    <li><a href="<?= appUrl() ?>">Home</a></li>
                    <li><a href="<?= appUrl('about.php') ?>">About Us</a></li>
                    <li><a href="<?= appUrl('contact.php') ?>">Contact</a></li>
                    <li><a href="<?= appUrl('login.php') ?>">Login</a></li>
                    <li><a href="<?= appUrl('register.php') ?>">Register</a></li>
                </ul>
            </div>

            <div>
                <h3 class="pust-footer__heading">Support</h3>
                <ul class="pust-footer__links">
                    <li><a href="<?= appUrl('register.php?type=student') ?>">Student Registration</a></li>
                    <li><a href="<?= appUrl('register.php?type=staff') ?>">Staff Registration</a></li>
                    <li><a href="<?= appUrl('contact.php') ?>">Report an Issue</a></li>
                </ul>
            </div>

            <div>
                <h3 class="pust-footer__heading">Contact Us</h3>
                <ul class="pust-footer__contact">
                    <li>
                        <span class="pust-footer__icon" aria-hidden="true">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <a href="mailto:support@pust.edu.so">support@pust.edu.so</a>
                    </li>
                    <li>
                        <span class="pust-footer__icon" aria-hidden="true">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </span>
                        <a href="tel:+252907789916">+252 907 789 916</a>
                    </li>
                    <li>
                        <span class="pust-footer__icon" aria-hidden="true">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <span>Israac-Siinay-Biixi Road<br>PUST Campus, Galkacyo</span>
                    </li>
                </ul>
                <p class="pust-footer__hours">
                    <strong>Office hours:</strong> 8:00 AM - 5:30 PM, Saturday - Thursday
                </p>
            </div>
        </div>
    </div>

    <div class="pust-footer__bottom">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
            <p class="pust-footer__copy">&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.</p>
            <p class="pust-footer__meta">Puntland University of Science &amp; Technology</p>
        </div>
    </div>
</footer>
<script src="<?= assetUrl('js/app.js') ?>"></script>
</body>
</html>
