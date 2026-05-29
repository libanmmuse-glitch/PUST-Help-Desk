<?php
/**
 * Security: CSRF, validation, session
 */

function initSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
    if (!isset($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
}

function csrfToken(): string
{
    return $_SESSION['_csrf_token'] ?? '';
}

function csrfField(): string
{
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . e(csrfToken()) . '">';
}

function verifyCsrf(?string $token = null): bool
{
    $token = $token ?? ($_POST[CSRF_TOKEN_NAME] ?? '');
    return hash_equals(csrfToken(), $token ?? '');
}

function requireCsrf(): void
{
    if (!verifyCsrf()) {
        http_response_code(403);
        die('Invalid CSRF token. Please refresh and try again.');
    }
}

function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePassword(string $password): array
{
    $errors = [];
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain an uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain a lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain a number.';
    }
    return $errors;
}

function sanitizeString(string $value): string
{
    return trim(strip_tags($value));
}

function allowedUploadTypes(): array
{
    return ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
}

function maxUploadSize(): int
{
    return 5 * 1024 * 1024; // 5MB
}

function validateUpload(array $file): array
{
    $errors = [];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) return [];
        $errors[] = 'File upload failed.';
        return $errors;
    }
    if ($file['size'] > maxUploadSize()) {
        $errors[] = 'File exceeds maximum size of 5MB.';
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, allowedUploadTypes(), true)) {
        $errors[] = 'File type not allowed.';
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowedMimes = [
        'image/jpeg', 'image/png', 'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    if (!in_array($mime, $allowedMimes, true)) {
        $errors[] = 'Invalid file content.';
    }
    return $errors;
}

function storeUpload(array $file, string $subdir = 'tickets'): ?array
{
    $errors = validateUpload($file);
    if (!empty($errors)) return null;

    $dir = UPLOAD_PATH . '/' . $subdir;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $stored = uniqid('att_', true) . '.' . $ext;
    $path = $dir . '/' . $stored;
    if (!move_uploaded_file($file['tmp_name'], $path)) {
        return null;
    }
    return [
        'file_name' => basename($file['name']),
        'file_path' => $subdir . '/' . $stored,
        'file_type' => $ext,
        'file_size' => (int) $file['size'],
    ];
}
