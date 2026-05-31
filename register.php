<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/middleware/auth.php';
guestOnly();

try {
    $faculties = getFaculties();
} catch (Throwable $e) {
    $faculties = [];
}

if (isPost()) {
    requireCsrf();
    if (getSetting('allow_registration', '1') !== '1') {
        flash('error', 'Registration is currently disabled.');
    } else {
        $_POST['account_type'] = 'student';
        $result = registerUser($_POST);
        if ($result['success']) {
            flash('success', 'Student registration successful! Please log in.');
            redirect(appUrl('login.php'));
        }
        flash('error', implode(' ', $result['errors']));
        setOld($_POST);
    }
}

$pageTitle = 'Register';
require __DIR__ . '/includes/templates/public-header.php';
?>
<style>
/* Shared auth styles are also in login.php — duplicate only what's needed */
.auth-shell {
    min-height: calc(100vh - 8rem);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
}
.auth-card {
    width: 100%;
    max-width: 960px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,.12), 0 4px 16px rgba(0,0,0,.06);
    overflow: hidden;
    display: grid;
    grid-template-columns: 340px 1fr;
    animation: cardIn .55s cubic-bezier(.22,.68,0,1.2) both;
}
@keyframes cardIn {
    from { opacity:0; transform:translateY(32px) scale(.97); }
    to   { opacity:1; transform:none; }
}
.auth-panel-left {
    background: linear-gradient(145deg, #1B3A6B 0%, #2F6FDB 50%, #5B9BD5 100%);
    padding: 3rem 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 1rem;
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
    width: 90px; height: 90px;
    object-fit: contain;
    filter: drop-shadow(0 4px 16px rgba(0,0,0,.3));
    animation: logoPulse 3s ease-in-out infinite;
}
@keyframes logoPulse {
    0%,100% { transform:scale(1); }
    50%      { transform:scale(1.04); }
}
.auth-panel-left h2 { color:#fff; font-size:1.2rem; font-weight:800; letter-spacing:-.02em; line-height:1.25; }
.auth-panel-left p  { color:rgba(255,255,255,.8); font-size:.825rem; line-height:1.6; }
.auth-panel-left .step { background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25); border-radius:12px; padding:.6rem 1rem; text-align:left; color:#fff; font-size:.8rem; display:flex; align-items:center; gap:.6rem; width:100%; }
.auth-panel-left .step svg { flex-shrink:0; color:rgba(255,255,255,.75); }

.auth-panel-right {
    padding: 2.5rem 2.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    overflow-y: auto;
    max-height: 90vh;
}
.auth-panel-right h1 { font-size:1.5rem; font-weight:800; color:#0F172A; letter-spacing:-.03em; }
.auth-panel-right .subtitle { font-size:.875rem; color:#64748B; margin-top:-.5rem; }

/* Input groups */
.input-group { display:flex; flex-direction:column; gap:.35rem; }
.input-group label { font-size:.8rem; font-weight:600; color:#374151; letter-spacing:.01em; }
.input-wrap { position:relative; }
.input-wrap .input-icon {
    position:absolute; left:.875rem; top:50%; transform:translateY(-50%);
    color:#94A3B8; width:17px; height:17px; pointer-events:none; transition:color .2s;
}
.input-wrap input,
.input-wrap select {
    width:100%; padding:.65rem 1rem .65rem 2.65rem;
    border:1.5px solid #E2E8F0; border-radius:10px;
    font-size:.875rem; background:#F8FAFC; color:#0F172A;
    transition:border-color .2s, box-shadow .2s, background .2s; outline:none;
    appearance:none; -webkit-appearance:none;
}
.input-wrap input:focus,
.input-wrap select:focus {
    border-color:#2F6FDB; background:#fff;
    box-shadow:0 0 0 3px rgba(47,111,219,.12);
}
.input-wrap:focus-within .input-icon { color:#2F6FDB; }
.input-wrap .pw-toggle {
    position:absolute; right:.875rem; top:50%; transform:translateY(-50%);
    cursor:pointer; color:#94A3B8; background:none; border:none; padding:0;
    display:flex; align-items:center; transition:color .2s;
}
.input-wrap .pw-toggle:hover { color:#2F6FDB; }
/* chevron for select */
.input-wrap .select-icon {
    position:absolute; right:.875rem; top:50%; transform:translateY(-50%);
    color:#94A3B8; pointer-events:none; width:16px; height:16px;
}

/* 2-col grid */
.form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:.875rem; }

/* Button */
.auth-btn {
    width:100%; padding:.75rem 1.5rem;
    background:linear-gradient(135deg, #1B3A6B 0%, #2F6FDB 100%);
    color:#fff; font-size:.9375rem; font-weight:700;
    border:none; border-radius:12px; cursor:pointer;
    transition:transform .18s, box-shadow .18s, filter .18s;
    box-shadow:0 4px 14px rgba(47,111,219,.35);
    display:flex; align-items:center; justify-content:center; gap:.5rem;
}
.auth-btn:hover { filter:brightness(1.08); transform:translateY(-1px); box-shadow:0 8px 20px rgba(47,111,219,.4); }
.auth-btn:active { transform:translateY(0); }

/* Alert */
.auth-alert {
    padding:.7rem .9rem; border-radius:10px; font-size:.85rem; font-weight:500;
    display:flex; align-items:flex-start; gap:.55rem;
    animation:slideIn .3s ease both;
}
@keyframes slideIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:none} }
.auth-alert.error   { background:#FEF2F2; color:#B91C1C; border:1px solid #FECACA; }
.auth-alert.warning { background:#FFFBEB; color:#92400E; border:1px solid #FDE68A; }
.auth-alert.success { background:#F0FDF4; color:#15803D; border:1px solid #BBF7D0; }

/* Divider */
.auth-divider { text-align:center; font-size:.8125rem; color:#94A3B8; }
.auth-divider a { color:#2F6FDB; font-weight:600; }
.auth-divider a:hover { text-decoration:underline; }

/* Dark mode */
html[data-theme="dark"] .auth-card { background:#1E293B; }
html[data-theme="dark"] .auth-panel-right h1 { color:#F1F5F9; }
html[data-theme="dark"] .auth-panel-right .subtitle { color:#94A3B8; }
html[data-theme="dark"] .input-group label { color:#CBD5E1; }
html[data-theme="dark"] .input-wrap input,
html[data-theme="dark"] .input-wrap select { background:#0F172A; border-color:#334155; color:#F1F5F9; }
html[data-theme="dark"] .input-wrap input:focus,
html[data-theme="dark"] .input-wrap select:focus { border-color:#2F6FDB; background:#1E293B; }
html[data-theme="dark"] .auth-alert.error   { background:#450A0A; color:#FCA5A5; border-color:#7F1D1D; }
html[data-theme="dark"] .auth-alert.warning { background:#451A03; color:#FCD34D; border-color:#78350F; }

/* Responsive */
@media (max-width:760px) {
    .auth-card { grid-template-columns:1fr; border-radius:20px; }
    .auth-panel-left { padding:2rem 1.5rem; border-radius:20px 20px 0 0; }
    .auth-panel-left .brand-logo { width:70px; height:70px; }
    .auth-panel-left .step { display:none; }
    .auth-panel-right { padding:2rem 1.5rem; max-height:none; }
    .form-grid-2 { grid-template-columns:1fr; }
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
            <h2>Create Your<br>PUST Account</h2>
            <p>Join the Puntland University of Science &amp; Technology support system</p>
            <div class="step">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                Select your faculty
            </div>
            <div class="step">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                Fill in your personal details
            </div>
            <div class="step">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                Set a secure password
            </div>
        </div>

        <!-- Right form panel -->
        <div class="auth-panel-right">
            <div>
                <h1>Student Registration</h1>
                <p class="subtitle">Register as a PUST University student</p>
            </div>

            <?php if (empty($faculties)): ?>
            <div class="auth-alert warning">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink:0;margin-top:1px"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/></svg>
                Faculty list unavailable. Please contact the administrator to complete faculty setup before registering.
            </div>
            <?php endif; ?>

            <?php if ($msg = flash('error')): ?>
            <div class="auth-alert error">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink:0;margin-top:1px"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/></svg>
                <?= e($msg) ?>
            </div>
            <?php endif; ?>

            <form method="POST" data-validate id="register-form" novalidate <?= empty($faculties) ? 'data-registration-blocked' : '' ?>>
                <?= csrfField() ?>
                <input type="hidden" name="account_type" value="student">

                <div class="input-group">
                    <label for="faculty_id">Faculty <span style="color:#EF4444">*</span></label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
                        <select name="faculty_id" id="faculty_id" required aria-label="Select your faculty">
                            <option value="" disabled <?= old('faculty_id') === '' ? 'selected' : '' ?>>Select your faculty</option>
                            <?php foreach ($faculties as $f): ?>
                            <option value="<?= (int)$f['id'] ?>" <?= (string)old('faculty_id') === (string)$f['id'] ? 'selected' : '' ?>><?= e($f['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="select-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="input-group">
                        <label for="first_name">First Name <span style="color:#EF4444">*</span></label>
                        <div class="input-wrap">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                            <input type="text" id="first_name" name="first_name" required minlength="2"
                                   autocomplete="given-name" value="<?= old('first_name') ?>" placeholder="First name">
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="last_name">Last Name <span style="color:#EF4444">*</span></label>
                        <div class="input-wrap">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                            <input type="text" id="last_name" name="last_name" required minlength="2"
                                   autocomplete="family-name" value="<?= old('last_name') ?>" placeholder="Last name">
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label for="student_id">Student ID <span style="color:#EF4444">*</span></label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z"/></svg>
                        <input type="text" id="student_id" name="student_id" required minlength="4"
                               autocomplete="off" value="<?= old('student_id') ?>" placeholder="e.g. PUST2024001">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="input-group">
                        <label for="register-email">Email <span style="color:#EF4444">*</span></label>
                        <div class="input-wrap">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                            <input type="email" id="register-email" name="email" required
                                   autocomplete="email" autocapitalize="none" spellcheck="false"
                                   value="<?= old('email') ?>" placeholder="you@student.pust.edu">
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="phone">Phone <span style="color:#EF4444">*</span></label>
                        <div class="input-wrap">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                            <input type="tel" id="phone" name="phone" required
                                   inputmode="tel" autocomplete="tel"
                                   value="<?= old('phone') ?>" placeholder="+252 907 789 916">
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="input-group">
                        <label for="reg-password">Password <span style="color:#EF4444">*</span></label>
                        <div class="input-wrap">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/></svg>
                            <input type="password" id="reg-password" name="password" required
                                   data-strength="true" minlength="8"
                                   autocomplete="new-password" placeholder="Min 8 characters">
                            <button type="button" class="pw-toggle" onclick="togglePw('reg-password', this)" aria-label="Show password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" /></svg>
                            </button>
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="password_confirm">Confirm Password <span style="color:#EF4444">*</span></label>
                        <div class="input-wrap">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                            <input type="password" id="password_confirm" name="password_confirm" required
                                   minlength="8" autocomplete="new-password" placeholder="Repeat password">
                            <button type="button" class="pw-toggle" onclick="togglePw('password_confirm', this)" aria-label="Show password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="auth-btn" style="margin-top:.25rem">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
                    Create Account
                </button>
            </form>

            <div class="auth-divider">Already have an account? <a href="<?= appUrl('login.php') ?>">Sign In</a></div>
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
(function () {
    const form = document.getElementById('register-form');
    if (form && form.dataset.registrationBlocked) {
        form.addEventListener('submit', e => e.preventDefault());
    }
})();
</script>
<?php require __DIR__ . '/includes/templates/public-footer.php'; ?>
