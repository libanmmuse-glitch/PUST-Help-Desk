<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/middleware/auth.php';
guestOnly();

if (isPost()) {
    requireCsrf();
    $user = attemptLogin(input('email'), input('password'));
    if ($user) {
        loginUser($user, !empty($_POST['remember']));
        flash('success', 'Welcome back, ' . $user['first_name'] . '!');
        redirectToDashboard();
    }
    flash('error', 'Invalid email or password.');
    setOld($_POST);
}

$pageTitle = 'Login';
require __DIR__ . '/includes/templates/public-header.php';
?>
<style>
/* ── Auth page shell ───────────────────────────────────────────── */
.auth-shell {
    min-height: calc(100vh - 8rem);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
}

/* ── Split card ────────────────────────────────────────────────── */
.auth-card {
    width: 100%;
    max-width: 900px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,.12), 0 4px 16px rgba(0,0,0,.06);
    overflow: hidden;
    display: grid;
    grid-template-columns: 1fr 1fr;
    animation: cardIn .55s cubic-bezier(.22,.68,0,1.2) both;
}
@keyframes cardIn {
    from { opacity:0; transform:translateY(32px) scale(.97); }
    to   { opacity:1; transform:none; }
}

/* ── Left panel (branding) ──────────────────────────────────────── */
.auth-panel-left {
    background: linear-gradient(145deg, #1B3A6B 0%, #2F6FDB 50%, #5B9BD5 100%);
    padding: 3rem 2.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 1.25rem;
    position: relative;
    overflow: hidden;
}
.auth-panel-left::before {
    content: '';
    position: absolute;
    inset: -60px;
    background: radial-gradient(circle at 30% 20%, rgba(255,255,255,.12) 0%, transparent 60%),
                radial-gradient(circle at 70% 80%, rgba(255,255,255,.08) 0%, transparent 50%);
    pointer-events: none;
}
.auth-panel-left .brand-logo {
    width: 100px;
    height: 100px;
    object-fit: contain;
    filter: drop-shadow(0 4px 16px rgba(0,0,0,.3));
    animation: logoPulse 3s ease-in-out infinite;
}
@keyframes logoPulse {
    0%,100% { transform: scale(1);     }
    50%      { transform: scale(1.04); }
}
.auth-panel-left h2 {
    color: #fff;
    font-size: 1.35rem;
    font-weight: 800;
    letter-spacing: -.02em;
    line-height: 1.25;
}
.auth-panel-left p {
    color: rgba(255,255,255,.8);
    font-size: .875rem;
    line-height: 1.6;
}
.auth-panel-left .badge-row {
    display: flex;
    gap: .6rem;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: .5rem;
}
.auth-panel-left .badge {
    background: rgba(255,255,255,.18);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    font-size: .7rem;
    font-weight: 600;
    letter-spacing: .04em;
    padding: .3rem .75rem;
    border-radius: 999px;
}

/* ── Right panel (form) ─────────────────────────────────────────── */
.auth-panel-right {
    padding: 3rem 2.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 1.5rem;
}
.auth-panel-right h1 {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0F172A;
    letter-spacing: -.03em;
}
.auth-panel-right .subtitle {
    font-size: .875rem;
    color: #64748B;
    margin-top: -.75rem;
}

/* ── Input groups ─────────────────────────────────────────────── */
.input-group {
    display: flex;
    flex-direction: column;
    gap: .375rem;
}
.input-group label {
    font-size: .8125rem;
    font-weight: 600;
    color: #374151;
    letter-spacing: .01em;
}
.input-wrap {
    position: relative;
}
.input-wrap .input-icon {
    position: absolute;
    left: .875rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94A3B8;
    width: 18px;
    height: 18px;
    pointer-events: none;
    transition: color .2s;
}
.input-wrap input {
    width: 100%;
    padding: .7rem 1rem .7rem 2.75rem;
    border: 1.5px solid #E2E8F0;
    border-radius: 12px;
    font-size: .9rem;
    background: #F8FAFC;
    color: #0F172A;
    transition: border-color .2s, box-shadow .2s, background .2s;
    outline: none;
}
.input-wrap input:focus {
    border-color: #2F6FDB;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(47,111,219,.12);
}
.input-wrap input:focus ~ .input-icon,
.input-wrap:focus-within .input-icon {
    color: #2F6FDB;
}
.input-wrap .pw-toggle {
    position: absolute;
    right: .875rem;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #94A3B8;
    background: none;
    border: none;
    padding: 0;
    display: flex;
    align-items: center;
    transition: color .2s;
}
.input-wrap .pw-toggle:hover { color: #2F6FDB; }

/* ── Button ──────────────────────────────────────────────────── */
.auth-btn {
    width: 100%;
    padding: .8rem 1.5rem;
    background: linear-gradient(135deg, #1B3A6B 0%, #2F6FDB 100%);
    color: #fff;
    font-size: .9375rem;
    font-weight: 700;
    letter-spacing: .01em;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: transform .18s, box-shadow .18s, filter .18s;
    box-shadow: 0 4px 14px rgba(47,111,219,.35);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
}
.auth-btn:hover {
    filter: brightness(1.08);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(47,111,219,.4);
}
.auth-btn:active { transform: translateY(0); }

/* ── Dark mode ────────────────────────────────────────────────── */
html[data-theme="dark"] .auth-card { background: #1E293B; }
html[data-theme="dark"] .auth-panel-right h1 { color: #F1F5F9; }
html[data-theme="dark"] .auth-panel-right .subtitle { color: #94A3B8; }
html[data-theme="dark"] .input-group label { color: #CBD5E1; }
html[data-theme="dark"] .input-wrap input {
    background: #0F172A; border-color: #334155; color: #F1F5F9;
}
html[data-theme="dark"] .input-wrap input:focus {
    border-color: #2F6FDB; background: #1E293B;
}
html[data-theme="dark"] .remember-label { color: #CBD5E1; }
html[data-theme="dark"] .forgot-link { color: #7DD3FC; }

/* ── Remember / Forgot row ─────────────────────────────────────── */
.form-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: .8125rem;
}
.remember-label {
    display: flex;
    align-items: center;
    gap: .45rem;
    color: #475569;
    cursor: pointer;
}
.remember-label input[type=checkbox] {
    width: 15px; height: 15px; accent-color: #2F6FDB; cursor: pointer;
}
.forgot-link { color: #2F6FDB; font-weight: 500; }
.forgot-link:hover { text-decoration: underline; }

/* ── Alert / Error box ─────────────────────────────────────────── */
.auth-alert {
    padding: .75rem 1rem;
    border-radius: 10px;
    font-size: .875rem;
    font-weight: 500;
    display: flex;
    align-items: flex-start;
    gap: .6rem;
    animation: slideIn .3s ease both;
}
@keyframes slideIn {
    from { opacity:0; transform:translateY(-6px); }
    to   { opacity:1; transform:none; }
}
.auth-alert.error   { background:#FEF2F2; color:#B91C1C; border:1px solid #FECACA; }
.auth-alert.success { background:#F0FDF4; color:#15803D; border:1px solid #BBF7D0; }
.auth-alert.warning { background:#FFFBEB; color:#92400E; border:1px solid #FDE68A; }
html[data-theme="dark"] .auth-alert.error   { background:#450A0A; color:#FCA5A5; border-color:#7F1D1D; }
html[data-theme="dark"] .auth-alert.success { background:#052E16; color:#86EFAC; border-color:#14532D; }

/* ── Divider ────────────────────────────────────────────────────── */
.auth-divider {
    text-align: center;
    font-size: .8125rem;
    color: #94A3B8;
}
.auth-divider a { color: #2F6FDB; font-weight: 600; }
.auth-divider a:hover { text-decoration: underline; }

/* ── Responsive collapse ────────────────────────────────────────── */
@media (max-width: 680px) {
    .auth-card { grid-template-columns: 1fr; border-radius: 20px; }
    .auth-panel-left { padding: 2rem 1.5rem; border-radius: 20px 20px 0 0; }
    .auth-panel-left .brand-logo { width: 72px; height: 72px; }
    .auth-panel-right { padding: 2rem 1.5rem; }
}
</style>

<section class="auth-shell">
    <div class="auth-card">

        <!-- Left branding panel -->
        <div class="auth-panel-left">
            <?php
            $logo = assetUrl('images/pust-logo-transparent.png');
            if (!is_file(ROOT_PATH . '/assets/images/pust-logo-transparent.png')) {
                $logo = assetUrl('images/pust-logo.jpg');
            }
            ?>
            <img src="<?= $logo ?>" alt="PUST Logo" class="brand-logo">
            <h2>Puntland University<br>of Science &amp; Technology</h2>
            <p>Welcome to the official student &amp; staff support portal. Sign in to submit and track your requests.</p>
            <div class="badge-row">
                <span class="badge">EST 2004</span>
                <span class="badge">Help Desk</span>
                <span class="badge">24/7 Support</span>
            </div>
        </div>

        <!-- Right form panel -->
        <div class="auth-panel-right">
            <div>
                <h1>Sign In</h1>
                <p class="subtitle">Access your PUST Help Desk account</p>
            </div>

            <?php if (isset($_GET['timeout'])): ?>
            <div class="auth-alert warning">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink:0;margin-top:1px"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>
                Session expired. Please log in again.
            </div>
            <?php endif; ?>

            <?php if ($msg = flash('error')): ?>
            <div class="auth-alert error">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink:0;margin-top:1px"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/></svg>
                <?= e($msg) ?>
            </div>
            <?php endif; ?>

            <form method="POST" data-validate class="space-y-4" novalidate>
                <?= csrfField() ?>

                <div class="input-group">
                    <label for="login-email">Email Address</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                        <input type="email" id="login-email" name="email" required autocomplete="username"
                               autocapitalize="none" spellcheck="false"
                               value="<?= old('email') ?>"
                               placeholder="you@student.pust.edu">
                    </div>
                </div>

                <div class="input-group">
                    <label for="login-password">Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z" /></svg>
                        <input type="password" id="login-password" name="password" required
                               autocomplete="current-password" placeholder="Enter your password">
                        <button type="button" class="pw-toggle" onclick="togglePw('login-password', this)" aria-label="Show password">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" /></svg>
                        </button>
                    </div>
                </div>

                <div class="form-meta">
                    <label class="remember-label">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="<?= appUrl('forgot-password.php') ?>" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="auth-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                    Sign In
                </button>
            </form>

            <div class="auth-divider">
                Don't have an account? <a href="<?= appUrl('register.php') ?>">Register here</a>
            </div>


        </div>

    </div>
</section>

<script>
function togglePw(inputId, btn) {
    const inp = document.getElementById(inputId);
    const isText = inp.type === 'text';
    inp.type = isText ? 'password' : 'text';
    btn.innerHTML = isText
        ? '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" /></svg>'
        : '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>';
}
</script>
<?php require __DIR__ . '/includes/templates/public-footer.php'; ?>
