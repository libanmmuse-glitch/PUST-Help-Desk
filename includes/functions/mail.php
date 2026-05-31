<?php
/**
 * Application email helpers.
 */

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

function getContactRecipientEmail(): string
{
    $fromDb = getSetting('contact_email', '');
    if ($fromDb && validateEmail($fromDb)) {
        return strtolower(trim($fromDb));
    }
    return CONTACT_FORM_EMAIL;
}

function getMailFromAddress(): string
{
    if (isSmtpConfigured() && defined('MAIL_FROM_EMAIL') && validateEmail(MAIL_FROM_EMAIL)) {
        return strtolower(trim(MAIL_FROM_EMAIL));
    }

    $fromDb = getSetting('mail_from', '');
    if ($fromDb && validateEmail($fromDb)) {
        return strtolower(trim($fromDb));
    }
    return MAIL_FROM_EMAIL;
}

function isSmtpConfigured(): bool
{
    if (!defined('MAIL_SMTP_HOST') || !defined('MAIL_SMTP_USER') || !defined('MAIL_SMTP_PASS')) {
        return false;
    }
    $host = trim((string) MAIL_SMTP_HOST);
    $user = trim((string) MAIL_SMTP_USER);
    $pass = trim((string) MAIL_SMTP_PASS);

    // Reject totally empty values
    if ($host === '' || $user === '' || $pass === '') return false;

    return true;
}

/**
 * Send an application email via PHPMailer/SMTP.
 *
 * @param string      $to           Recipient email address
 * @param string      $subject      Email subject
 * @param string      $body         HTML body (when $isHtml=true) or plain text
 * @param string|null $replyToEmail Optional Reply-To address
 * @param string|null $replyToName  Optional Reply-To name
 * @param bool        $isHtml       Set true to send as HTML email
 * @param string      $altBody      Plain-text fallback (used when $isHtml=true)
 */
function sendAppEmail(
    string $to,
    string $subject,
    string $body,
    ?string $replyToEmail = null,
    ?string $replyToName  = null,
    bool   $isHtml        = false,
    string $altBody       = ''
): bool {
    if (!validateEmail($to)) {
        return false;
    }

    $from = getMailFromAddress();

    if (!class_exists(PHPMailer::class)) {
        logMailError('PHPMailer is not installed. Run: composer install');
        return false;
    }

    if (!isSmtpConfigured()) {
        logMailError(
            'SMTP not configured. Fill in real credentials in ' .
            'includes/config/mail.local.php (MAIL_SMTP_HOST, MAIL_SMTP_USER, MAIL_SMTP_PASS).'
        );
        return false;
    }

    try {
        $mail = new PHPMailer(true);
        $mail->CharSet  = PHPMailer::CHARSET_UTF8;
        $mail->Encoding = 'base64';

        // ── SMTP transport ──────────────────────────────────────────────
        $mail->isSMTP();
        $mail->Host     = MAIL_SMTP_HOST;
        $mail->Port     = defined('MAIL_SMTP_PORT') ? (int) MAIL_SMTP_PORT : 587;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_SMTP_USER;
        $mail->Password = MAIL_SMTP_PASS;

        if (defined('MAIL_SMTP_DEBUG') && MAIL_SMTP_DEBUG) {
            $mail->SMTPDebug = 2; // DEBUG_SERVER
            $mail->Debugoutput = function($str, $level) {
                logMailError('SMTP: ' . trim($str));
            };
        }

        $secure = defined('MAIL_SMTP_SECURE') ? strtolower((string) MAIL_SMTP_SECURE) : 'tls';
        if ($secure === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            if ($mail->Port === 587) {
                $mail->Port = 465; // correct port for SSL
            }
        } elseif ($secure === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;
        }

        // On localhost: disable SSL peer verification to avoid self-signed cert errors
        if (in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'], true)
            || str_starts_with($_SERVER['HTTP_HOST'] ?? '', 'localhost:')
        ) {
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];
        }

        // ── Addressing ──────────────────────────────────────────────────
        $mail->setFrom($from, defined('APP_NAME') ? APP_NAME : 'Help Desk');
        $mail->addAddress($to);
        if ($replyToEmail && validateEmail($replyToEmail)) {
            $mail->addReplyTo($replyToEmail, $replyToName ?? '');
        }

        // ── Content ─────────────────────────────────────────────────────
        $mail->Subject = $subject;
        if ($isHtml) {
            $mail->isHTML(true);
            $mail->Body    = $body;
            $mail->AltBody = $altBody !== '' ? $altBody : strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));
        } else {
            $mail->Body    = $body;
            $mail->AltBody = $body;
        }

        return $mail->send();

    } catch (\Throwable $e) {
        $message = $e->getMessage();
        if (isset($mail) && $mail->ErrorInfo !== '') {
            $message .= ' | PHPMailer: ' . $mail->ErrorInfo;
        }
        logMailError('Email failed to [' . $to . ']: ' . $message);
        return false;
    }
}

function logMailError(string $message): void
{
    error_log('[MailError] ' . $message);

    $dir = ROOT_PATH . '/logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    @file_put_contents(
        $dir . '/mail-errors.log',
        date('c') . "\t" . $message . "\n",
        FILE_APPEND | LOCK_EX
    );
}

// ───────────────────────────────────────────────────────────────────────────
// Password Reset Email
// ───────────────────────────────────────────────────────────────────────────

/**
 * Build a branded HTML password-reset email body.
 */
function buildPasswordResetHtml(string $name, string $resetUrl, string $appName): string
{
    $year     = date('Y');
    $safeName = htmlspecialchars($name,    ENT_QUOTES, 'UTF-8');
    $safeUrl  = htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8');
    $safeApp  = htmlspecialchars($appName, ENT_QUOTES, 'UTF-8');

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Reset Your Password — {$safeApp}</title>
</head>
<body style="margin:0;padding:0;background-color:#F1F5F9;font-family:'Segoe UI',Arial,Helvetica,sans-serif;-webkit-font-smoothing:antialiased;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#F1F5F9;padding:48px 20px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" border="0"
           style="max-width:600px;width:100%;border-radius:20px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,.12);">

      <!-- ── Header ─────────────────────────────────── -->
      <tr>
        <td style="background:linear-gradient(135deg,#1B3A6B 0%,#2F6FDB 60%,#5B9BD5 100%);padding:44px 40px 36px;text-align:center;">
          <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr><td align="center" style="padding-bottom:18px;">
              <div style="display:inline-block;width:72px;height:72px;background:rgba(255,255,255,.2);border-radius:50%;border:2px solid rgba(255,255,255,.35);line-height:72px;text-align:center;font-size:32px;">
                🔐
              </div>
            </td></tr>
            <tr><td align="center">
              <h1 style="color:#ffffff;margin:0;font-size:22px;font-weight:700;letter-spacing:-0.4px;">{$safeApp}</h1>
              <p style="color:rgba(255,255,255,.75);margin:6px 0 0;font-size:13px;letter-spacing:.5px;text-transform:uppercase;">Password Reset Request</p>
            </td></tr>
          </table>
        </td>
      </tr>

      <!-- ── Body ───────────────────────────────────── -->
      <tr>
        <td style="background:#ffffff;padding:44px 40px;">

          <p style="color:#0F172A;font-size:16px;font-weight:600;margin:0 0 10px;">Hello, {$safeName}!</p>
          <p style="color:#475569;font-size:15px;line-height:1.7;margin:0 0 28px;">
            We received a request to reset the password for your <strong style="color:#1B3A6B;">{$safeApp}</strong> account.
            Click the button below to create a new secure password. This link is valid for <strong>1 hour</strong>.
          </p>

          <!-- CTA Button -->
          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:32px 0;">
            <tr>
              <td align="center">
                <a href="{$safeUrl}"
                   style="display:inline-block;background:linear-gradient(135deg,#1B3A6B 0%,#2F6FDB 100%);color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;padding:16px 44px;border-radius:12px;letter-spacing:.3px;box-shadow:0 4px 14px rgba(47,111,219,.4);">
                  &#x1F511;&nbsp; Reset My Password
                </a>
              </td>
            </tr>
          </table>

          <!-- Warning box -->
          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:28px 0;">
            <tr>
              <td style="background:#FFFBEB;border:1px solid #FCD34D;border-radius:10px;padding:16px 20px;">
                <p style="color:#92400E;font-size:13px;margin:0;line-height:1.6;">
                  &#x23F0; <strong>This link expires in 1 hour.</strong><br>
                  If you did not request a password reset, you can safely ignore this email — your password will remain unchanged.
                </p>
              </td>
            </tr>
          </table>

          <!-- Divider -->
          <hr style="border:none;border-top:1px solid #E2E8F0;margin:32px 0;">

          <!-- Fallback URL -->
          <p style="color:#94A3B8;font-size:12px;line-height:1.7;margin:0;">
            <strong>Button not working?</strong> Copy and paste this link into your browser:<br>
            <a href="{$safeUrl}" style="color:#2F6FDB;word-break:break-all;font-size:11px;">{$safeUrl}</a>
          </p>

        </td>
      </tr>

      <!-- ── Footer ─────────────────────────────────── -->
      <tr>
        <td style="background:#F8FAFC;padding:24px 40px;text-align:center;border-top:1px solid #E2E8F0;">
          <p style="color:#94A3B8;font-size:12px;margin:0;">
            &copy; {$year} {$safeApp} &mdash; Puntland University of Science &amp; Technology
          </p>
          <p style="color:#CBD5E1;font-size:11px;margin:6px 0 0;">
            This is an automated email. Please do not reply to this message.
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
}

/**
 * Send a password-reset email with a branded HTML template.
 *
 * @param string $to       Recipient email
 * @param string $resetUrl Full reset URL (token + email in query string)
 * @param string $name     User's first name for personalisation
 */
function sendPasswordResetEmail(string $to, string $resetUrl, string $name = 'User'): bool
{
    $appName  = defined('APP_NAME') ? APP_NAME : 'PUST Help Desk';
    $subject  = 'Reset Your Password — ' . $appName;
    $htmlBody = buildPasswordResetHtml($name, $resetUrl, $appName);

    $plainBody = "Hello {$name},\r\n\r\n"
        . "We received a request to reset your {$appName} account password.\r\n\r\n"
        . "Reset your password here (link expires in 1 hour):\r\n"
        . $resetUrl . "\r\n\r\n"
        . "If you did not request this, you can safely ignore this email.\r\n\r\n"
        . "Regards,\r\n{$appName} Team\r\n";

    return sendAppEmail($to, $subject, $htmlBody, null, null, true, $plainBody);
}

// ───────────────────────────────────────────────────────────────────────────
// Contact Form
// ───────────────────────────────────────────────────────────────────────────

function ensureContactSubmissionsTable(): void
{
    ensureHelpDeskDatabaseSchema();
}

function saveContactSubmission(string $name, string $email, string $subject, string $message, bool $emailSent): int
{
    ensureContactSubmissionsTable();
    $db   = getDB();
    $stmt = $db->prepare(
        'INSERT INTO contact_submissions (name, email, subject, message, email_sent) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $name,
        strtolower(trim($email)),
        $subject !== '' ? $subject : null,
        $message,
        $emailSent ? 1 : 0,
    ]);
    return (int) $db->lastInsertId();
}

function processContactFormSubmission(string $name, string $fromEmail, string $subjectLine, string $messageBody): array
{
    $sent = false;

    if (defined('CONTACT_FORM_SEND_EMAIL') && CONTACT_FORM_SEND_EMAIL) {
        $subject = $subjectLine !== ''
            ? '[' . APP_NAME . '] ' . $subjectLine
            : '[' . APP_NAME . '] New contact form message';

        $body  = "You have received a new message from the help desk contact form.\r\n\r\n";
        $body .= "Name: {$name}\r\n";
        $body .= "Email: {$fromEmail}\r\n";
        if ($subjectLine !== '') {
            $body .= "Subject: {$subjectLine}\r\n";
        }
        $body .= "\r\nMessage:\r\n{$messageBody}\r\n";

        $sent = sendAppEmail(getContactRecipientEmail(), $subject, $body, $fromEmail, $name);
    }

    $submissionId = saveContactSubmission($name, $fromEmail, $subjectLine, $messageBody, $sent);

    return [
        'email_sent'    => $sent,
        'submission_id' => $submissionId,
    ];
}
