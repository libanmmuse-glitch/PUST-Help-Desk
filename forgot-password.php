<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/middleware/auth.php';
guestOnly();

$emailSent   = false;
$submittedTo = '';

if (isPost()) {
    requireCsrf();
    $rawEmail    = trim(input('email', ''));
    $submittedTo = $rawEmail;
    createPasswordResetToken($rawEmail);   // always returns true (no email enumeration)
    $emailSent = true;
}

$pageTitle = 'Forgot Password';
require __DIR__ . '/includes/templates/public-header.php';
?>
<style>
/* ── Shell ──────────────────────────────────────────────────────────── */
.auth-shell {
    min-height: calc(100vh - 8rem);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
}

/* ── Card ───────────────────────────────────────────────────────────── */
.auth-single-card {
    width: 100%;
    max-width: 480px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,.12), 0 4px 16px rgba(0,0,0,.06);
    overflow: hidden;
    animation: cardIn .55s cubic-bezier(.22,.68,0,1.2) both;
}
@keyframes cardIn {
    from { opacity:0; transform:translateY(32px) scale(.97); }
    to   { opacity:1; transform:none; }
}

/* ── Header gradient ────────────────────────────────────────────────── */
.auth-card-top {
    background: linear-gradient(145deg, #1B3A6B 0%, #2F6FDB 60%, #5B9BD5 100%);
    padding: 2.5rem 2.5rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.auth-card-top::before {
    content: '';
    position: absolute;
    inset: -60px;
    background: radial-gradient(circle at 30% 20%, rgba(255,255,255,.12) 0%, transparent 60%),
                radial-gradient(circle at 70% 80%, rgba(255,255,255,.08) 0%, transparent 50%);
    pointer-events: none;
}
.auth-card-top .icon-wrap {
    width: 72px; height: 72px;
    background: rgba(255,255,255,.2);
    border: 2px solid rgba(255,255,255,.35);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    margin-bottom: 1rem;
    animation: iconPop .6s cubic-bezier(.22,.68,0,1.4) .15s both;
    position: relative;
    z-index: 1;
}
@keyframes iconPop {
    from { transform:scale(0) rotate(-20deg); opacity:0; }
    to   { transform:none; opacity:1; }
}
.auth-card-top h1 {
    color: #fff;
    font-size: 1.4rem;
    font-weight: 800;
    letter-spacing: -.02em;
    position: relative;
    z-index: 1;
}
.auth-card-top p {
    color: rgba(255,255,255,.8);
    font-size: .875rem;
    margin-top: .35rem;
    line-height: 1.55;
    position: relative;
    z-index: 1;
}

/* ── Card body ──────────────────────────────────────────────────────── */
.auth-card-body {
    padding: 2rem 2.5rem 2.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* ── Input ──────────────────────────────────────────────────────────── */
.input-group { display:flex; flex-direction:column; gap:.35rem; }
.input-group label { font-size:.8125rem; font-weight:600; color:#374151; letter-spacing:.01em; }
.input-wrap { position:relative; }
.input-wrap .input-icon {
    position:absolute; left:.875rem; top:50%; transform:translateY(-50%);
    color:#94A3B8; width:18px; height:18px; pointer-events:none; transition:color .2s;
}
.input-wrap input {
    width:100%; padding:.72rem 1rem .72rem 2.75rem;
    border:1.5px solid #E2E8F0; border-radius:12px;
    font-size:.9rem; background:#F8FAFC; color:#0F172A;
    transition:border-color .2s, box-shadow .2s, background .2s; outline:none;
    box-sizing: border-box;
}
.input-wrap input:focus {
    border-color:#2F6FDB; background:#fff;
    box-shadow:0 0 0 3px rgba(47,111,219,.12);
}
.input-wrap:focus-within .input-icon { color:#2F6FDB; }

/* ── Button ─────────────────────────────────────────────────────────── */
.auth-btn {
    width:100%; padding:.8rem 1.5rem;
    background:linear-gradient(135deg, #1B3A6B 0%, #2F6FDB 100%);
    color:#fff; font-size:.9375rem; font-weight:700; letter-spacing:.01em;
    border:none; border-radius:12px; cursor:pointer;
    transition:transform .18s, box-shadow .18s, filter .18s;
    box-shadow:0 4px 14px rgba(47,111,219,.35);
    display:flex; align-items:center; justify-content:center; gap:.5rem;
}
.auth-btn:hover { filter:brightness(1.08); transform:translateY(-1px); box-shadow:0 8px 20px rgba(47,111,219,.4); }
.auth-btn:active { transform:translateY(0); }
.auth-btn:disabled { opacity:.6; cursor:not-allowed; transform:none; }

/* ── Alerts ─────────────────────────────────────────────────────────── */
.auth-alert {
    padding:.75rem 1rem; border-radius:10px; font-size:.875rem; font-weight:500;
    display:flex; align-items:flex-start; gap:.6rem;
    animation:slideIn .3s ease both;
}
@keyframes slideIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:none} }
.auth-alert.success { background:#F0FDF4; color:#15803D; border:1px solid #BBF7D0; }
.auth-alert.error   { background:#FEF2F2; color:#B91C1C; border:1px solid #FECACA; }
.auth-alert.info    { background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE; }

/* ── Check-inbox success state ──────────────────────────────────────── */
.inbox-state {
    text-align: center;
    padding: .5rem 0;
    animation: fadeUp .5s ease both;
}
@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
.inbox-icon-wrap {
    width: 88px; height: 88px;
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
    animation: bumpIn .6s cubic-bezier(.22,.68,0,1.4) .1s both;
}
@keyframes bumpIn { from{transform:scale(0);opacity:0} to{transform:scale(1);opacity:1} }
.inbox-state h2 { font-size:1.2rem; font-weight:800; color:#0F172A; margin:0 0 .5rem; }
.inbox-state p { font-size:.875rem; color:#475569; line-height:1.65; margin:0; }
.inbox-state .email-chip {
    display: inline-block;
    margin-top: .75rem;
    background: #EFF6FF;
    color: #1D4ED8;
    font-size: .8rem;
    font-weight: 600;
    padding: .3rem .85rem;
    border-radius: 999px;
    border: 1px solid #BFDBFE;
    word-break: break-all;
}
.inbox-steps {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    text-align: left;
    margin-top: 1rem;
    font-size: .8125rem;
    color: #475569;
    line-height: 1.75;
}
.inbox-steps li { padding-left: .25rem; }
.resend-row {
    text-align: center;
    font-size: .8125rem;
    color: #64748B;
}
.resend-row a { color: #2F6FDB; font-weight: 600; cursor: pointer; }
.resend-row a:hover { text-decoration: underline; }
#resend-countdown { font-weight: 600; color: #2F6FDB; }

/* ── Back link ──────────────────────────────────────────────────────── */
.back-link {
    text-align:center; font-size:.8125rem; color:#64748B;
    display:flex; align-items:center; justify-content:center; gap:.35rem;
}
.back-link a { color:#2F6FDB; font-weight:600; }
.back-link a:hover { text-decoration:underline; }

/* ── SMTP config notice ─────────────────────────────────────────────── */
.smtp-notice {
    background: #FFFBEB;
    border: 1px solid #FCD34D;
    border-radius: 10px;
    padding: .75rem 1rem;
    font-size: .775rem;
    color: #92400E;
    line-height: 1.55;
}
.smtp-notice strong { display:block; margin-bottom:.2rem; }

/* ── Dark mode ──────────────────────────────────────────────────────── */
html[data-theme="dark"] .auth-single-card { background:#1E293B; }
html[data-theme="dark"] .input-group label { color:#CBD5E1; }
html[data-theme="dark"] .input-wrap input { background:#0F172A; border-color:#334155; color:#F1F5F9; }
html[data-theme="dark"] .input-wrap input:focus { border-color:#2F6FDB; background:#1E293B; }
html[data-theme="dark"] .auth-alert.success { background:#052E16; color:#86EFAC; border-color:#14532D; }
html[data-theme="dark"] .auth-alert.error   { background:#450A0A; color:#FCA5A5; border-color:#7F1D1D; }
html[data-theme="dark"] .auth-alert.info    { background:#0C1A2E; color:#93C5FD; border-color:#1E3A5F; }
html[data-theme="dark"] .inbox-steps { background:#0F172A; border-color:#334155; color:#94A3B8; }
html[data-theme="dark"] .inbox-state h2 { color:#F1F5F9; }
html[data-theme="dark"] .inbox-state p { color:#94A3B8; }
html[data-theme="dark"] .back-link { color:#94A3B8; }
html[data-theme="dark"] .smtp-notice { background:#1C1408; border-color:#78350F; color:#FCD34D; }

@media (max-width:480px) {
    .auth-single-card { border-radius:18px; }
    .auth-card-top { padding:2rem 1.5rem 1.5rem; }
    .auth-card-body { padding:1.5rem; }
}
</style>

<section class="auth-shell">
    <div class="auth-single-card">

        <?php if ($emailSent): ?>
        <!-- ── SUCCESS: Check Inbox State ───────────────────────── -->
        <div class="auth-card-top">
            <div class="icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.661-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.615a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V8.844a2.25 2.25 0 0 1 1.183-1.981l7.5-4.039a2.25 2.25 0 0 1 2.134 0l7.5 4.039a2.25 2.25 0 0 1 1.183 1.98V19.5Z" /></svg>
            </div>
            <h1>Check Your Email</h1>
            <p>We've sent a secure reset link to your inbox.</p>
        </div>

        <div class="auth-card-body">
            <div class="inbox-state">
                <div class="inbox-icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#16A34A"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                </div>
                <h2>Reset link sent!</h2>
                <p>If <strong><?= e($submittedTo) ?></strong> is registered, you'll receive a password reset email shortly.</p>
                <?php if ($submittedTo !== ''): ?>
                <span class="email-chip"><?= e($submittedTo) ?></span>
                <?php endif; ?>
            </div>

            <div class="inbox-steps">
                <ol style="margin:0;padding-left:1.2rem;">
                    <li>Open your email inbox</li>
                    <li>Look for an email from <strong><?= e(defined('MAIL_FROM_EMAIL') ? MAIL_FROM_EMAIL : 'noreply@pust.edu.so') ?></strong></li>
                    <li>Click <strong>"Reset My Password"</strong> in the email</li>
                    <li>Check your <strong>Spam / Junk</strong> folder if you don't see it</li>
                </ol>
            </div>

            <?php if (!isSmtpConfigured()): ?>
            <div class="smtp-notice">
                <strong>⚠️ SMTP not configured (admin notice)</strong>
                Email delivery is disabled. Configure SMTP credentials in
                <code>includes/config/mail.local.php</code> to enable password reset emails.
            </div>
            <?php endif; ?>

            <div class="resend-row">
                Didn't receive it?
                <a id="resend-link" href="<?= appUrl('forgot-password.php') ?>">Resend email</a>
                <span id="resend-countdown"></span>
            </div>

            <div class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                <a href="<?= appUrl('login.php') ?>">Back to Login</a>
            </div>
        </div>

        <?php else: ?>
        <!-- ── FORM STATE ────────────────────────────────────────── -->
        <div class="auth-card-top">
            <div class="icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/></svg>
            </div>
            <h1>Forgot Password?</h1>
            <p>Enter your registered email address and we'll send you a secure reset link.</p>
        </div>

        <div class="auth-card-body">

            <?php if ($msg = flash('error')): ?>
            <div class="auth-alert error">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink:0;margin-top:1px"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/></svg>
                <?= e($msg) ?>
            </div>
            <?php endif; ?>

            <?php if (!isSmtpConfigured()): ?>
            <div class="smtp-notice">
                <strong>⚠️ Email delivery not configured</strong>
                SMTP credentials are missing or still set to placeholder values. Reset emails cannot be sent until
                <code>includes/config/mail.local.php</code> is configured with real SMTP details.
            </div>
            <?php endif; ?>

            <form method="POST" id="forgot-form" novalidate style="display:flex;flex-direction:column;gap:1rem;">
                <?= csrfField() ?>
                <div class="input-group">
                    <label for="forgot-email">Email Address</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        <input type="email" id="forgot-email" name="email" required
                               autocomplete="email" autocapitalize="none" spellcheck="false"
                               placeholder="you@student.pust.edu"
                               value="<?= old('email') ?>">
                    </div>
                    <span id="email-err" style="font-size:.75rem;color:#B91C1C;display:none;margin-top:2px;"></span>
                </div>

                <button type="submit" id="submit-btn" class="auth-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                    <span id="btn-text">Send Reset Link</span>
                </button>
            </form>

            <div class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                <a href="<?= appUrl('login.php') ?>">Back to Login</a>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<script>
/* ── Resend countdown (60 s) ─────────────────────────────────────────── */
(function () {
    const link = document.getElementById('resend-link');
    const count = document.getElementById('resend-countdown');
    if (!link || !count) return;

    let seconds = 59;
    link.style.pointerEvents = 'none';
    link.style.opacity = '.45';
    count.textContent = ' (' + seconds + 's)';

    const tick = setInterval(() => {
        seconds--;
        if (seconds <= 0) {
            clearInterval(tick);
            link.style.pointerEvents = '';
            link.style.opacity = '';
            count.textContent = '';
        } else {
            count.textContent = ' (' + seconds + 's)';
        }
    }, 1000);
})();

/* ── Form: client-side email validation + loading state ─────────────── */
(function () {
    const form = document.getElementById('forgot-form');
    if (!form) return;

    const emailInput = document.getElementById('forgot-email');
    const emailErr   = document.getElementById('email-err');
    const btn        = document.getElementById('submit-btn');
    const btnText    = document.getElementById('btn-text');

    function showErr(el, msg) {
        if (!el) return;
        el.textContent = msg;
        el.style.display = 'block';
    }
    function clearErr(el) {
        if (!el) return;
        el.textContent = '';
        el.style.display = 'none';
    }

    emailInput && emailInput.addEventListener('input', () => clearErr(emailErr));

    form.addEventListener('submit', (e) => {
        clearErr(emailErr);
        const val = emailInput ? emailInput.value.trim() : '';
        const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);

        if (!emailOk) {
            e.preventDefault();
            showErr(emailErr, 'Please enter a valid email address.');
            emailInput && emailInput.focus();
            return;
        }

        // Loading state
        if (btn) {
            btn.disabled = true;
            if (btnText) btnText.textContent = 'Sending…';
        }
    });
})();
</script>

<?php require __DIR__ . '/includes/templates/public-footer.php'; ?>
