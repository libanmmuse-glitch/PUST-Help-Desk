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
    return defined('MAIL_SMTP_HOST')
        && trim((string) MAIL_SMTP_HOST) !== ''
        && defined('MAIL_SMTP_USER')
        && trim((string) MAIL_SMTP_USER) !== ''
        && defined('MAIL_SMTP_PASS')
        && trim((string) MAIL_SMTP_PASS) !== '';
}

function sendAppEmail(string $to, string $subject, string $body, ?string $replyToEmail = null, ?string $replyToName = null): bool
{
    if (!validateEmail($to)) {
        return false;
    }

    $from = getMailFromAddress();

    if (!class_exists(PHPMailer::class)) {
        logMailError('PHPMailer is not installed. Run composer install for this project.');
        return false;
    }

    if (!isSmtpConfigured()) {
        logMailError('SMTP is not configured. Create includes/config/mail.local.php with MAIL_SMTP_HOST, MAIL_SMTP_USER, and MAIL_SMTP_PASS.');
        return false;
    }

    try {
        $mail = new PHPMailer(true);
        $mail->CharSet = PHPMailer::CHARSET_UTF8;

        $mail->isSMTP();
        $mail->Host = MAIL_SMTP_HOST;
        $mail->Port = defined('MAIL_SMTP_PORT') ? (int) MAIL_SMTP_PORT : 587;
        $mail->SMTPAuth = defined('MAIL_SMTP_USER') && MAIL_SMTP_USER !== '';
        $mail->Username = defined('MAIL_SMTP_USER') ? MAIL_SMTP_USER : '';
        $mail->Password = defined('MAIL_SMTP_PASS') ? MAIL_SMTP_PASS : '';

        $secure = defined('MAIL_SMTP_SECURE') ? strtolower((string) MAIL_SMTP_SECURE) : 'tls';
        if ($secure === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($secure === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->setFrom($from, APP_NAME);
        $mail->addAddress($to);
        if ($replyToEmail && validateEmail($replyToEmail)) {
            $mail->addReplyTo($replyToEmail, $replyToName ?? '');
        }
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $body;

        return $mail->send();
    } catch (Throwable $e) {
        $message = $e->getMessage();
        if (isset($mail) && $mail->ErrorInfo !== '') {
            $message .= ' PHPMailer: ' . $mail->ErrorInfo;
        }
        logMailError('Email could not be sent to ' . $to . ': ' . $message);
        return false;
    }
}

function logMailError(string $message): void
{
    error_log($message);

    $dir = ROOT_PATH . '/logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    @file_put_contents($dir . '/mail-errors.log', date('c') . "\t" . $message . "\n", FILE_APPEND | LOCK_EX);
}

function sendPasswordResetEmail(string $to, string $resetUrl): bool
{
    $subject = 'Reset your ' . APP_NAME . ' password';

    $body = "Hello,\r\n\r\n";
    $body .= "We received a request to reset the password for your " . APP_NAME . " account.\r\n\r\n";
    $body .= "Please click the secure link below to create a new password:\r\n";
    $body .= $resetUrl . "\r\n\r\n";
    $body .= "This link will expire in 1 hour. If you did not request a password reset, you can safely ignore this email and your password will remain unchanged.\r\n\r\n";
    $body .= "Thank you,\r\n";
    $body .= APP_NAME . " Team\r\n";

    return sendAppEmail($to, $subject, $body);
}

function ensureContactSubmissionsTable(): void
{
    static $done = false;
    if ($done) {
        return;
    }

    $done = true;
    getDB()->exec("CREATE TABLE IF NOT EXISTS contact_submissions (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NULL,
        message TEXT NOT NULL,
        email_sent TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_contact_created (created_at)
    ) ENGINE=InnoDB");
}

function saveContactSubmission(string $name, string $email, string $subject, string $message, bool $emailSent): int
{
    ensureContactSubmissionsTable();
    $db = getDB();
    $stmt = $db->prepare('INSERT INTO contact_submissions (name, email, subject, message, email_sent) VALUES (?, ?, ?, ?, ?)');
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

        $body = "You have received a new message from the help desk contact form.\r\n\r\n";
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
        'email_sent' => $sent,
        'submission_id' => $submissionId,
    ];
}
