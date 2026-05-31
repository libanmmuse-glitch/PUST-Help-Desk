<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/middleware/auth.php';
guestOnly();

$token = trim($_GET['token'] ?? '');
$email = strtolower(trim($_GET['email'] ?? ''));

// ── Guard: missing parameters ─────────────────────────────────────────────
if ($token === '' || $email === '') {
    flash('error', 'Invalid or missing password reset link. Please request a new one.');
    redirect(appUrl('forgot-password.php'));
}

// ── Guard: validate token upfront on GET (before showing the form) ────────
ensurePasswordResetsTable();
$db       = getDB();
$chkStmt  = $db->prepare(
    'SELECT id FROM password_resets WHERE email = ? AND token = ? AND expires_at > NOW() LIMIT 1'
);
$chkStmt->execute([$email, hash('sha256', $token)]);
$validRow = $chkStmt->fetch();

if (!$validRow) {
    flash('error', 'This password reset link is invalid or has expired. Please request a new one.');
    redirect(appUrl('forgot-password.php'));
}

// ── Handle POST ───────────────────────────────────────────────────────────
if (isPost()) {
    requireCsrf();
    $result = resetPassword(
        input('token'),
        input('email'),
        input('password'),
        input('password_confirm')
    );
    if ($result['success']) {
        flash('success', 'Your password has been reset successfully. Please log in with your new password.');
        redirect(appUrl('login.php'));
    }
    flash('error', implode(' ', $result['errors']));
    // Keep GET params so the form reloads with token/email still in URL
}

$pageTitle = 'Reset Password';
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

/* ── Header ─────────────────────────────────────────────────────────── */
.auth-card-top {
    background: linear-gradient(145deg, #14532D 0%, #16A34A 60%, #4ADE80 100%);
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
    position: relative; z-index: 1;
}
@keyframes iconPop {
    from { transform:scale(0) rotate(-20deg); opacity:0; }
    to   { transform:none; opacity:1; }
}
.auth-card-top h1 { color:#fff; font-size:1.4rem; font-weight:800; letter-spacing:-.02em; position:relative;z-index:1; }
.auth-card-top p  { color:rgba(255,255,255,.85); font-size:.875rem; margin-top:.35rem; line-height:1.55; position:relative;z-index:1; }

/* ── Body ───────────────────────────────────────────────────────────── */
.auth-card-body { padding:2rem 2.5rem 2.5rem; display:flex; flex-direction:column; gap:1.25rem; }

/* ── Input ──────────────────────────────────────────────────────────── */
.input-group { display:flex; flex-direction:column; gap:.35rem; }
.input-group label { font-size:.8125rem; font-weight:600; color:#374151; letter-spacing:.01em; }
.input-wrap { position:relative; }
.input-wrap .input-icon {
    position:absolute; left:.875rem; top:50%; transform:translateY(-50%);
    color:#94A3B8; width:18px; height:18px; pointer-events:none; transition:color .2s;
}
.input-wrap input {
    width:100%; padding:.72rem 2.75rem .72rem 2.75rem;
    border:1.5px solid #E2E8F0; border-radius:12px;
    font-size:.9rem; background:#F8FAFC; color:#0F172A;
    transition:border-color .2s, box-shadow .2s, background .2s; outline:none;
    box-sizing: border-box;
}
.input-wrap input:focus {
    border-color:#16A34A; background:#fff;
    box-shadow:0 0 0 3px rgba(22,163,74,.12);
}
.input-wrap input.input-error { border-color:#EF4444; }
.input-wrap input.input-ok    { border-color:#22C55E; }
.input-wrap:focus-within .input-icon { color:#16A34A; }
.input-wrap .pw-toggle {
    position:absolute; right:.875rem; top:50%; transform:translateY(-50%);
    cursor:pointer; color:#94A3B8; background:none; border:none; padding:0;
    display:flex; align-items:center; transition:color .2s;
}
.input-wrap .pw-toggle:hover { color:#16A34A; }

/* ── Strength bar ────────────────────────────────────────────────────── */
.strength-bar { height:5px; border-radius:4px; background:#E2E8F0; margin-top:.45rem; overflow:hidden; }
.strength-fill { height:100%; width:0%; border-radius:4px; transition:width .35s, background .35s; }

/* ── Requirements list ──────────────────────────────────────────────── */
.req-list {
    background:#F8FAFC;
    border:1px solid #E2E8F0;
    border-radius:10px;
    padding:.75rem 1rem;
    font-size:.775rem;
    display:flex;
    flex-direction:column;
    gap:.3rem;
    list-style: none;
    margin:0;
}
.req-item {
    display: flex;
    align-items: center;
    gap: .45rem;
    color: #94A3B8;
    transition: color .2s;
}
.req-item.met { color:#16A34A; }
.req-item.fail { color:#EF4444; }
.req-dot {
    width: 14px; height: 14px;
    border-radius: 50%;
    border: 1.5px solid currentColor;
    display:inline-flex; align-items:center; justify-content:center;
    flex-shrink:0;
    font-size: 9px;
    font-weight:700;
    transition: background .2s;
}
.req-item.met .req-dot { background:#16A34A; border-color:#16A34A; color:#fff; }
.req-item.fail .req-dot { background:#EF4444; border-color:#EF4444; color:#fff; }

/* ── Match indicator ─────────────────────────────────────────────────── */
.match-hint {
    font-size:.75rem;
    margin-top:.25rem;
    display:flex;
    align-items:center;
    gap:.35rem;
    min-height: 1rem;
}
.match-hint.match-ok   { color:#16A34A; }
.match-hint.match-fail { color:#EF4444; }

/* ── Button ─────────────────────────────────────────────────────────── */
.auth-btn {
    width:100%; padding:.8rem 1.5rem;
    background:linear-gradient(135deg, #14532D 0%, #16A34A 100%);
    color:#fff; font-size:.9375rem; font-weight:700; letter-spacing:.01em;
    border:none; border-radius:12px; cursor:pointer;
    transition:transform .18s, box-shadow .18s, filter .18s;
    box-shadow:0 4px 14px rgba(22,163,74,.35);
    display:flex; align-items:center; justify-content:center; gap:.5rem;
}
.auth-btn:hover { filter:brightness(1.08); transform:translateY(-1px); box-shadow:0 8px 20px rgba(22,163,74,.4); }
.auth-btn:active { transform:translateY(0); }
.auth-btn:disabled { opacity:.6; cursor:not-allowed; transform:none; }

/* ── Alerts ─────────────────────────────────────────────────────────── */
.auth-alert {
    padding:.75rem 1rem; border-radius:10px; font-size:.875rem; font-weight:500;
    display:flex; align-items:flex-start; gap:.6rem;
    animation:slideIn .3s ease both;
}
@keyframes slideIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:none} }
.auth-alert.error { background:#FEF2F2; color:#B91C1C; border:1px solid #FECACA; }

/* ── Back link ──────────────────────────────────────────────────────── */
.back-link {
    text-align:center; font-size:.8125rem; color:#64748B;
    display:flex; align-items:center; justify-content:center; gap:.35rem;
}
.back-link a { color:#16A34A; font-weight:600; }
.back-link a:hover { text-decoration:underline; }

/* ── Dark mode ──────────────────────────────────────────────────────── */
html[data-theme="dark"] .auth-single-card { background:#1E293B; }
html[data-theme="dark"] .input-group label { color:#CBD5E1; }
html[data-theme="dark"] .input-wrap input { background:#0F172A; border-color:#334155; color:#F1F5F9; }
html[data-theme="dark"] .input-wrap input:focus { border-color:#16A34A; background:#1E293B; }
html[data-theme="dark"] .auth-alert.error { background:#450A0A; color:#FCA5A5; border-color:#7F1D1D; }
html[data-theme="dark"] .back-link { color:#94A3B8; }
html[data-theme="dark"] .req-list { background:#0F172A; border-color:#334155; }

@media (max-width:480px) {
    .auth-single-card { border-radius:18px; }
    .auth-card-top { padding:2rem 1.5rem 1.5rem; }
    .auth-card-body { padding:1.5rem; }
}
</style>

<section class="auth-shell">
    <div class="auth-single-card">

        <div class="auth-card-top">
            <div class="icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z"/></svg>
            </div>
            <h1>Reset Your Password</h1>
            <p>Create a strong new password for your PUST account.</p>
        </div>

        <div class="auth-card-body">

            <?php if ($msg = flash('error')): ?>
            <div class="auth-alert error">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink:0;margin-top:1px"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/></svg>
                <?= e($msg) ?>
            </div>
            <?php endif; ?>

            <form method="POST" id="reset-form" novalidate style="display:flex;flex-direction:column;gap:1.1rem;">
                <?= csrfField() ?>
                <input type="hidden" name="token" value="<?= e($token) ?>">
                <input type="hidden" name="email" value="<?= e($email) ?>">

                <!-- New Password -->
                <div class="input-group">
                    <label for="new-password">New Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/></svg>
                        <input type="password" id="new-password" name="password" required
                               minlength="8" autocomplete="new-password"
                               placeholder="Min 8 characters"
                               oninput="onPasswordInput()">
                        <button type="button" class="pw-toggle" onclick="togglePw('new-password',this)" aria-label="Show password">
                            <?= eyeIcon() ?>
                        </button>
                    </div>
                    <!-- Strength bar -->
                    <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                    <span id="strength-label" style="font-size:.72rem;color:#94A3B8;margin-top:1px;min-height:1rem;display:block;"></span>
                </div>

                <!-- Requirements checklist -->
                <ul class="req-list" id="req-list" aria-label="Password requirements">
                    <li class="req-item" id="req-len">
                        <span class="req-dot">✓</span> At least 8 characters
                    </li>
                    <li class="req-item" id="req-upper">
                        <span class="req-dot">✓</span> One uppercase letter (A–Z)
                    </li>
                    <li class="req-item" id="req-lower">
                        <span class="req-dot">✓</span> One lowercase letter (a–z)
                    </li>
                    <li class="req-item" id="req-num">
                        <span class="req-dot">✓</span> One number (0–9)
                    </li>
                </ul>

                <!-- Confirm Password -->
                <div class="input-group">
                    <label for="confirm-password">Confirm New Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                        <input type="password" id="confirm-password" name="password_confirm" required
                               minlength="8" autocomplete="new-password"
                               placeholder="Repeat new password"
                               oninput="onConfirmInput()">
                        <button type="button" class="pw-toggle" onclick="togglePw('confirm-password',this)" aria-label="Show password">
                            <?= eyeIcon() ?>
                        </button>
                    </div>
                    <div class="match-hint" id="match-hint"></div>
                </div>

                <button type="submit" id="reset-btn" class="auth-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    <span id="reset-btn-text">Reset Password</span>
                </button>
            </form>

            <div class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                <a href="<?= appUrl('login.php') ?>">Back to Login</a>
            </div>
        </div>
    </div>
</section>

<?php
function eyeIcon(): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" /></svg>';
}
?>

<script>
/* ── Password toggle ─────────────────────────────────────────────────── */
const eyeOn  = `<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" /></svg>`;
const eyeOff = `<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>`;

function togglePw(id, btn) {
    const inp = document.getElementById(id);
    const isText = inp.type === 'text';
    inp.type = isText ? 'password' : 'text';
    btn.innerHTML = isText ? eyeOn : eyeOff;
}

/* ── Requirements helpers ────────────────────────────────────────────── */
const rules = [
    { id: 'req-len',   test: v => v.length >= 8 },
    { id: 'req-upper', test: v => /[A-Z]/.test(v) },
    { id: 'req-lower', test: v => /[a-z]/.test(v) },
    { id: 'req-num',   test: v => /[0-9]/.test(v) },
];

function evaluateRules(val) {
    let passed = 0;
    rules.forEach(r => {
        const el = document.getElementById(r.id);
        if (!el) return;
        const ok = r.test(val);
        if (ok) passed++;
        el.classList.toggle('met',  ok  && val !== '');
        el.classList.toggle('fail', !ok && val !== '');
    });
    return passed;
}

/* ── Strength bar ────────────────────────────────────────────────────── */
const strengthMap = [
    { pct:'0%',   bg:'transparent', txt:'' },
    { pct:'25%',  bg:'#EF4444',     txt:'Weak' },
    { pct:'50%',  bg:'#F59E0B',     txt:'Fair' },
    { pct:'75%',  bg:'#3B82F6',     txt:'Good' },
    { pct:'100%', bg:'#22C55E',     txt:'Strong' },
];

function onPasswordInput() {
    const val   = document.getElementById('new-password').value;
    const score = val === '' ? 0 : evaluateRules(val);
    const fill  = document.getElementById('strength-fill');
    const label = document.getElementById('strength-label');
    if (fill)  { fill.style.width = strengthMap[score].pct; fill.style.background = strengthMap[score].bg; }
    if (label) { label.textContent = strengthMap[score].txt; label.style.color = strengthMap[score].bg || '#94A3B8'; }
    onConfirmInput(); // re-check match
}

/* ── Confirm match indicator ─────────────────────────────────────────── */
function onConfirmInput() {
    const pw  = document.getElementById('new-password').value;
    const cfm = document.getElementById('confirm-password').value;
    const hint = document.getElementById('match-hint');
    const cfmInp = document.getElementById('confirm-password');
    if (!hint || cfm === '') { hint && (hint.innerHTML = ''); cfmInp && cfmInp.classList.remove('input-ok','input-error'); return; }

    if (pw === cfm) {
        hint.innerHTML = '✓ Passwords match';
        hint.className = 'match-hint match-ok';
        cfmInp && cfmInp.classList.replace('input-error','input-ok') || cfmInp.classList.add('input-ok');
    } else {
        hint.innerHTML = '✕ Passwords do not match';
        hint.className = 'match-hint match-fail';
        cfmInp && cfmInp.classList.replace('input-ok','input-error') || cfmInp.classList.add('input-error');
    }
}

/* ── Form submit guard ───────────────────────────────────────────────── */
document.getElementById('reset-form').addEventListener('submit', function(e) {
    const pw  = document.getElementById('new-password').value;
    const cfm = document.getElementById('confirm-password').value;
    const score = evaluateRules(pw);

    if (score < 4) {
        e.preventDefault();
        alert('Please ensure your password meets all requirements.');
        return;
    }
    if (pw !== cfm) {
        e.preventDefault();
        document.getElementById('confirm-password').focus();
        onConfirmInput();
        return;
    }

    // Loading state
    const btn  = document.getElementById('reset-btn');
    const txt  = document.getElementById('reset-btn-text');
    if (btn)  btn.disabled = true;
    if (txt)  txt.textContent = 'Resetting…';
});
</script>

<?php require __DIR__ . '/includes/templates/public-footer.php'; ?>
