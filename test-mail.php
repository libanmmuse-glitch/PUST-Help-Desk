<?php
/**
 * PUST Help Desk — SMTP Delivery Test
 * ─────────────────────────────────────────────────────────────────────────
 * ACCESS: http://localhost/Help-Desk-App/test-mail.php
 * DELETE this file before deploying to production!
 * ─────────────────────────────────────────────────────────────────────────
 */

// ── Bootstrap (loads config, PHPMailer, all helpers) ─────────────────────
require_once __DIR__ . '/includes/bootstrap.php';

// ── Simple IP guard: localhost only ──────────────────────────────────────
$allowedIps = ['127.0.0.1', '::1', 'localhost'];
$clientIp   = $_SERVER['REMOTE_ADDR'] ?? '';
$isCli      = (php_sapi_name() === 'cli');
if (!$isCli && !in_array($clientIp, $allowedIps, true)) {
    http_response_code(403);
    die('403 Forbidden — this script is for local use only.');
}

// ── Run test if button clicked ────────────────────────────────────────────
$result   = null;
$testEmail = trim($_POST['email'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $testEmail !== '') {
    $smtpOk = isSmtpConfigured();
    if (!$smtpOk) {
        $result = ['ok' => false, 'msg' => 'SMTP is not configured. Fill in mail.local.php first.'];
    } else {
        $subject = '[PUST Help Desk] SMTP Test — ' . date('H:i:s');
        $resetUrl = 'http://localhost/Help-Desk-App/reset-password.php?token=TESTTOKEN123&email=' . urlencode($testEmail);
        $sent = sendPasswordResetEmail($testEmail, $resetUrl, 'Test User');
        if ($sent) {
            $result = ['ok' => true,  'msg' => 'Email sent successfully! Check your Mailtrap inbox (or Gmail).'];
        } else {
            // Read the last line from mail-errors.log
            $logFile = ROOT_PATH . '/logs/mail-errors.log';
            $lastErr = '';
            if (is_file($logFile)) {
                $lines = array_filter(explode("\n", file_get_contents($logFile)));
                $lastErr = end($lines);
            }
            $result = ['ok' => false, 'msg' => 'Send failed. Last error: ' . ($lastErr ?: 'Check logs/mail-errors.log')];
        }
    }
}

// ── Read current config values for display ────────────────────────────────
$cfgHost   = defined('MAIL_SMTP_HOST')   ? MAIL_SMTP_HOST   : '— not set —';
$cfgPort   = defined('MAIL_SMTP_PORT')   ? MAIL_SMTP_PORT   : '— not set —';
$cfgSecure = defined('MAIL_SMTP_SECURE') ? MAIL_SMTP_SECURE : '— not set —';
$cfgUser   = defined('MAIL_SMTP_USER')   ? MAIL_SMTP_USER   : '— not set —';
$cfgPass   = defined('MAIL_SMTP_PASS')   ? str_repeat('●', min(strlen(MAIL_SMTP_PASS), 20)) : '— not set —';
$cfgFrom   = defined('MAIL_FROM_EMAIL')  ? MAIL_FROM_EMAIL  : '— not set —';
$smtpReady = isSmtpConfigured();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SMTP Test — PUST Help Desk</title>
<style>
  *, *::before, *::after { box-sizing: border-box; }
  body {
    margin: 0; padding: 2rem 1rem;
    background: #0F172A;
    font-family: 'Segoe UI', system-ui, sans-serif;
    color: #F1F5F9;
    min-height: 100vh;
  }
  .card {
    max-width: 640px; margin: 0 auto;
    background: #1E293B;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,.4);
    overflow: hidden;
  }
  .card-header {
    background: linear-gradient(135deg,#1B3A6B,#2F6FDB);
    padding: 2rem 2rem 1.75rem;
  }
  .card-header h1 { margin: 0; font-size: 1.35rem; font-weight: 800; }
  .card-header p  { margin: .4rem 0 0; font-size:.875rem; color:rgba(255,255,255,.7); }
  .danger-pill {
    display: inline-block;
    background: rgba(239,68,68,.25);
    color: #FCA5A5;
    border: 1px solid rgba(239,68,68,.4);
    font-size: .7rem; font-weight: 700;
    letter-spacing: .05em; text-transform: uppercase;
    padding: .2rem .65rem; border-radius: 999px;
    margin-top: .6rem;
  }
  .card-body { padding: 2rem; display: flex; flex-direction: column; gap: 1.5rem; }

  /* Config table */
  .cfg-table { width: 100%; border-collapse: collapse; font-size: .8375rem; }
  .cfg-table th, .cfg-table td { padding: .6rem .75rem; text-align: left; }
  .cfg-table thead th { color: #64748B; font-size: .7rem; letter-spacing: .06em; text-transform: uppercase; border-bottom: 1px solid #334155; }
  .cfg-table tbody tr:not(:last-child) td { border-bottom: 1px solid #1E293B; }
  .cfg-table td:first-child { color: #94A3B8; width: 42%; }
  .cfg-table td:last-child  { color: #F1F5F9; font-family: 'Consolas', monospace; }
  .cfg-table tbody tr { background: #0F172A; }
  .cfg-table tbody tr:nth-child(odd) { background: #162032; }
  .cfg-table tbody tr { border-radius: 8px; }

  .status-badge {
    display: inline-flex; align-items: center; gap: .4rem;
    font-size: .8rem; font-weight: 600;
    padding: .3rem .85rem; border-radius: 999px;
  }
  .status-badge.ok   { background: #052E16; color: #86EFAC; border: 1px solid #14532D; }
  .status-badge.fail { background: #450A0A; color: #FCA5A5; border: 1px solid #7F1D1D; }

  /* Alert */
  .alert {
    padding: .9rem 1.1rem; border-radius: 12px;
    font-size: .875rem; font-weight: 500;
    display: flex; gap: .6rem; align-items: flex-start;
  }
  .alert.ok   { background: #052E16; color: #86EFAC; border: 1px solid #14532D; }
  .alert.fail { background: #450A0A; color: #FCA5A5; border: 1px solid #7F1D1D; }

  /* Form */
  label { display: block; font-size: .8125rem; font-weight: 600; color: #CBD5E1; margin-bottom: .4rem; }
  input[type="email"] {
    width: 100%; padding: .7rem 1rem;
    background: #0F172A; border: 1.5px solid #334155;
    border-radius: 10px; color: #F1F5F9; font-size: .9rem;
    outline: none; transition: border-color .2s;
  }
  input[type="email"]:focus { border-color: #2F6FDB; box-shadow: 0 0 0 3px rgba(47,111,219,.15); }
  button[type="submit"] {
    width: 100%; padding: .8rem;
    background: linear-gradient(135deg,#1B3A6B,#2F6FDB);
    color: #fff; font-size: .9375rem; font-weight: 700;
    border: none; border-radius: 12px; cursor: pointer;
    transition: filter .18s, transform .18s;
    box-shadow: 0 4px 14px rgba(47,111,219,.35);
    margin-top: .25rem;
  }
  button[type="submit"]:hover { filter: brightness(1.1); transform: translateY(-1px); }
  button[type="submit"]:disabled { opacity: .5; cursor: not-allowed; }

  .tip-box {
    background: #0C1A2E;
    border: 1px solid #1E3A5F;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    font-size: .8rem;
    color: #93C5FD;
    line-height: 1.7;
  }
  .tip-box strong { color: #BFDBFE; }
  .tip-box a { color: #60A5FA; }
  .tip-box code {
    background: rgba(96,165,250,.15); border-radius: 4px;
    padding: .1rem .35rem; font-size: .78rem;
  }
  hr { border: none; border-top: 1px solid #334155; }
</style>
</head>
<body>
<div class="card">
  <div class="card-header">
    <h1>📧 SMTP Delivery Test</h1>
    <p>Verify PHPMailer + SMTP is working correctly for the Forgot Password system.</p>
    <span class="danger-pill">⚠ Delete this file before going live</span>
  </div>

  <div class="card-body">

    <!-- ── SMTP Status ───────────────────────────────────────── -->
    <div>
      <p style="margin:0 0 .75rem;font-size:.8125rem;font-weight:600;color:#94A3B8;letter-spacing:.04em;text-transform:uppercase;">Current SMTP Configuration</p>
      <table class="cfg-table">
        <thead><tr><th>Setting</th><th>Value</th></tr></thead>
        <tbody>
          <tr><td>Host</td>    <td><?= htmlspecialchars($cfgHost) ?></td></tr>
          <tr><td>Port</td>    <td><?= htmlspecialchars((string)$cfgPort) ?></td></tr>
          <tr><td>Secure</td>  <td><?= htmlspecialchars($cfgSecure) ?></td></tr>
          <tr><td>Username</td><td><?= htmlspecialchars($cfgUser) ?></td></tr>
          <tr><td>Password</td><td><?= htmlspecialchars($cfgPass) ?></td></tr>
          <tr><td>From Email</td><td><?= htmlspecialchars($cfgFrom) ?></td></tr>
        </tbody>
      </table>
      <div style="margin-top:.85rem;">
        <?php if ($smtpReady): ?>
        <span class="status-badge ok">✓ SMTP configured — ready to send</span>
        <?php else: ?>
        <span class="status-badge fail">✕ SMTP not configured — update mail.local.php</span>
        <?php endif; ?>
      </div>
    </div>

    <hr>

    <?php if ($result !== null): ?>
    <!-- ── Send Result ───────────────────────────────────────── -->
    <div class="alert <?= $result['ok'] ? 'ok' : 'fail' ?>">
      <span style="flex-shrink:0;font-size:1.1rem;"><?= $result['ok'] ? '✅' : '❌' ?></span>
      <span><?= htmlspecialchars($result['msg']) ?></span>
    </div>
    <?php endif; ?>

    <!-- ── Send Test Email ──────────────────────────────────── -->
    <form method="POST">
      <label for="test-email">Send a test password-reset email to:</label>
      <input type="email" id="test-email" name="email" required
             placeholder="your@email.com"
             value="<?= htmlspecialchars($testEmail) ?>"
             <?= $smtpReady ? '' : 'disabled' ?>>
      <button type="submit" <?= $smtpReady ? '' : 'disabled' ?>>
        <?= $smtpReady ? '▶ Send Test Email' : '✕ Configure SMTP First' ?>
      </button>
    </form>

    <hr>

    <!-- ── Instructions ─────────────────────────────────────── -->
    <div class="tip-box">
      <strong>📋 How to get Mailtrap credentials (takes 2 min)</strong><br>
      1. Sign up free at <a href="https://mailtrap.io" target="_blank">mailtrap.io</a> — no credit card<br>
      2. Go to <strong>Email Testing → Inboxes → My Inbox</strong><br>
      3. Click the inbox → <strong>Show Credentials</strong> or pick <strong>"PHPMailer"</strong> from the integrations list<br>
      4. Copy <code>Username</code> and <code>Password</code><br>
      5. Open <code>includes/config/mail.local.php</code> and paste them into<br>
         &nbsp;&nbsp;<code>MAIL_SMTP_USER</code> and <code>MAIL_SMTP_PASS</code><br>
      6. Refresh this page and click <strong>Send Test Email</strong><br><br>

      <strong>📧 After the test succeeds:</strong><br>
      The reset email will appear in your <a href="https://mailtrap.io" target="_blank">Mailtrap inbox</a>.
      Click the reset link inside it — it should open the Reset Password page and let you set a new password.<br><br>

      <strong>🚀 When going live — switch to Gmail:</strong><br>
      Create a Gmail App Password at <a href="https://myaccount.google.com/apppasswords" target="_blank">myaccount.google.com/apppasswords</a>
      and update <code>mail.local.php</code> with Gmail settings (instructions inside that file).
    </div>

  </div><!-- /.card-body -->
</div><!-- /.card -->
</body>
</html>
