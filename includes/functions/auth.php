<?php
/**
 * Authentication functions
 */

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function userId(): ?int
{
    return currentUser()['id'] ?? null;
}

function userRole(): ?string
{
    return currentUser()['role'] ?? null;
}

function loginUser(array $user, bool $remember = false): void
{
    unset($user['password']);
    $_SESSION['user'] = $user;
    $_SESSION['last_activity'] = time();

    $db = getDB();
    $stmt = $db->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ? AND deleted_at IS NULL');
    $stmt->execute([$user['id']]);

    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $stmt = $db->prepare('UPDATE users SET remember_token = ? WHERE id = ? AND deleted_at IS NULL');
        $stmt->execute([hash('sha256', $token), $user['id']]);
        setcookie('remember_token', $token, time() + (REMEMBER_DAYS * 86400), '/', '', false, true);
        setcookie('remember_user', (string) $user['id'], time() + (REMEMBER_DAYS * 86400), '/', '', false, true);
    }

    logActivity($user['id'], 'login', 'user', $user['id'], 'User logged in successfully');
}

function logoutUser(): void
{
    if (isLoggedIn()) {
        logActivity(userId(), 'logout', 'user', userId(), 'User logged out');
    }
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
        setcookie('remember_user', '', time() - 3600, '/');
    }
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function attemptLogin(string $email, string $password): ?array
{
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ? AND is_active = 1 AND deleted_at IS NULL LIMIT 1');
    $stmt->execute([strtolower(trim($email))]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return null;
}

function checkRememberMe(): void
{
    if (isLoggedIn() || empty($_COOKIE['remember_token']) || empty($_COOKIE['remember_user'])) {
        return;
    }
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ? AND is_active = 1 AND deleted_at IS NULL AND remember_token = ?');
    $stmt->execute([(int) $_COOKIE['remember_user'], hash('sha256', $_COOKIE['remember_token'])]);
    $user = $stmt->fetch();
    if ($user) {
        loginUser($user, false);
    }
}

function registerUser(array $data): array
{
    $errors = [];
    $accountType = ($data['account_type'] ?? 'student') === 'staff' ? 'staff' : 'student';

    if (empty($data['first_name'])) $errors[] = 'First name is required.';
    if (empty($data['last_name'])) $errors[] = 'Last name is required.';
    if (!validateEmail($data['email'] ?? '')) $errors[] = 'Valid email is required.';
    $pwErrors = validatePassword($data['password'] ?? '');
    $errors = array_merge($errors, $pwErrors);
    if (($data['password'] ?? '') !== ($data['password_confirm'] ?? '')) {
        $errors[] = 'Passwords do not match.';
    }

    $facultyId = null;
    $departmentId = null;
    $staffCategory = null;

    if ($accountType === 'student') {
        $facultyId = (int) ($data['faculty_id'] ?? 0);
        if ($facultyId < 1 || !facultyExists($facultyId)) {
            $errors[] = 'Please select your faculty.';
        }
    } else {
        $staffCategory = $data['staff_category'] ?? '';
        if (!isValidStaffCategory($staffCategory)) {
            $errors[] = 'Please select a staff category.';
        } elseif ($staffCategory === 'lecturer') {
            $facultyId = (int) ($data['faculty_id'] ?? 0);
            if ($facultyId < 1 || !facultyExists($facultyId)) {
                $errors[] = 'Please select your faculty.';
            }
        } else {
            $departmentId = (int) ($data['department_id'] ?? 0);
            if ($departmentId < 1 || !departmentExists($departmentId)) {
                $errors[] = 'Please select a valid department.';
            }
        }
    }

    if ($accountType === 'student' && empty(trim($data['student_id'] ?? ''))) {
        $errors[] = 'Student ID is required.';
    }

    if ($accountType === 'staff' && getSetting('allow_staff_registration', '1') !== '1') {
        $errors[] = 'Staff registration is currently disabled.';
    }

    $db = getDB();
    $stmt = $db->prepare('SELECT id FROM users WHERE email = ? AND deleted_at IS NULL');
    $stmt->execute([strtolower(trim($data['email']))]);
    if ($stmt->fetch()) {
        $errors[] = 'Email is already registered.';
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $memberId = trim($data['student_id'] ?? '') ?: null;

    try {
        $stmt = $db->prepare('INSERT INTO users (student_id, first_name, last_name, email, password, phone, role, staff_category, faculty_id, department_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $memberId,
            sanitizeString($data['first_name']),
            sanitizeString($data['last_name']),
            strtolower(trim($data['email'])),
            password_hash($data['password'], PASSWORD_DEFAULT),
            sanitizeString($data['phone'] ?? ''),
            $accountType,
            $staffCategory,
            $facultyId,
            $departmentId,
        ]);
    } catch (PDOException $e) {
        return ['success' => false, 'errors' => ['Registration failed. Please contact support or try again later.']];
    }

    $userId = (int) $db->lastInsertId();
    $roleLabel = $accountType === 'staff' ? 'staff' : 'student';
    logActivity($userId, 'register', 'user', $userId, "New {$roleLabel} account registered");

    $dashboardUrl = $accountType === 'staff'
        ? appUrl('staff/dashboard.php')
        : appUrl('student/dashboard.php');

    createNotification(
        $userId,
        'system',
        'Welcome!',
        'Welcome to PUST Help Desk. You can now submit and track support tickets.',
        $dashboardUrl
    );

    return ['success' => true, 'user_id' => $userId, 'role' => $accountType];
}

function ensurePasswordResetsTable(): void
{
    ensureHelpDeskDatabaseSchema();
}

function createPasswordResetToken(string $email): bool
{
    $email = strtolower(trim($email));
    if (!validateEmail($email)) {
        return true; // Don't reveal anything
    }

    // Auto-create table if missing (resilience for fresh installs)
    ensurePasswordResetsTable();

    $db   = getDB();
    $stmt = $db->prepare('SELECT id, first_name FROM users WHERE email = ? AND is_active = 1 AND deleted_at IS NULL LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user) {
        return true; // Don't reveal if email exists (timing-safe)
    }

    // ── Rate limiting: max 1 request per 60 seconds per email ──────────────
    $rateStmt = $db->prepare(
        'SELECT created_at FROM password_resets WHERE email = ? ORDER BY created_at DESC LIMIT 1'
    );
    $rateStmt->execute([$email]);
    $lastReset = $rateStmt->fetch();
    if ($lastReset && (time() - strtotime($lastReset['created_at'])) < 60) {
        return true; // Silently skip — too soon since last request
    }

    // ── Generate a secure token ─────────────────────────────────────────────
    $token = bin2hex(random_bytes(32));
    $db->prepare('DELETE FROM password_resets WHERE email = ?')->execute([$email]);
    $db->prepare(
        'INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))'
    )->execute([$email, hash('sha256', $token)]);

    // ── Build reset link and send branded HTML email ────────────────────────
    $resetLink = rtrim(appUrl('reset-password.php'), '/')
        . '?token=' . urlencode($token)
        . '&email=' . urlencode($email);

    $name = $user['first_name'] ?? 'User';
    $sent = sendPasswordResetEmail($email, $resetLink, $name);

    if (!$sent) {
        logMailError("Password reset email could not be delivered to: {$email}");
    }

    return true; // Always true — never reveal email existence
}

function resetPassword(string $token, string $email, string $password, string $passwordConfirm = ''): array
{
    $email = strtolower(trim($email));
    $token = trim($token);

    if ($email === '' || $token === '') {
        return ['success' => false, 'errors' => ['Invalid or expired reset link.']];
    }

    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires_at > NOW()');
    $stmt->execute([$email, hash('sha256', $token)]);
    if (!$stmt->fetch()) {
        return ['success' => false, 'errors' => ['Invalid or expired reset link.']];
    }

    $errors = validatePassword($password);
    if ($password !== $passwordConfirm) {
        $errors[] = 'Passwords do not match.';
    }
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $stmt = $db->prepare('UPDATE users SET password = ? WHERE email = ? AND is_active = 1 AND deleted_at IS NULL');
    $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $email]);
    $db->prepare('DELETE FROM password_resets WHERE email = ?')->execute([$email]);

    return ['success' => true];
}

function changePassword(int $userId, string $current, string $new): array
{
    $db = getDB();
    $stmt = $db->prepare('SELECT password FROM users WHERE id = ? AND deleted_at IS NULL');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($current, $user['password'])) {
        return ['success' => false, 'errors' => ['Current password is incorrect.']];
    }
    $errors = validatePassword($new);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    $stmt = $db->prepare('UPDATE users SET password = ? WHERE id = ? AND deleted_at IS NULL');
    $stmt->execute([password_hash($new, PASSWORD_DEFAULT), $userId]);
    logActivity($userId, 'password_change', 'user', $userId, 'Password changed');
    return ['success' => true];
}

function adminCreateUser(array $data): array
{
    $errors = [];
    $role = $data['role'] ?? 'student';
    if (!in_array($role, ['student', 'staff', 'admin'], true)) {
        $errors[] = 'Invalid user role.';
    }

    if (empty($data['first_name'])) $errors[] = 'First name is required.';
    if (empty($data['last_name'])) $errors[] = 'Last name is required.';
    if (!validateEmail($data['email'] ?? '')) $errors[] = 'Valid email is required.';
    
    // Password validation
    $pwErrors = validatePassword($data['password'] ?? '');
    $errors = array_merge($errors, $pwErrors);

    $facultyId = null;
    $departmentId = null;
    $staffCategory = null;

    if ($role === 'student') {
        $facultyId = (int) ($data['faculty_id'] ?? 0);
        if ($facultyId < 1 || !facultyExists($facultyId)) {
            $errors[] = 'Please select a faculty for the student.';
        }
        if (empty(trim($data['student_id'] ?? ''))) {
            $errors[] = 'Student ID is required.';
        }
    } elseif ($role === 'staff') {
        $staffCategory = $data['staff_category'] ?? 'general';
        if ($staffCategory === 'lecturer') {
            $facultyId = (int) ($data['faculty_id'] ?? 0);
            if ($facultyId < 1 || !facultyExists($facultyId)) {
                $errors[] = 'Please select a faculty for the lecturer.';
            }
        } else {
            $departmentId = (int) ($data['department_id'] ?? 0);
            if ($departmentId < 1 || !departmentExists($departmentId)) {
                $errors[] = 'Please select a department.';
            }
        }
    } else { // admin
        $departmentId = (int) ($data['department_id'] ?? 0);
        if ($departmentId < 1) {
            $departmentId = null;
        }
    }

    $db = getDB();
    $stmt = $db->prepare('SELECT id FROM users WHERE email = ? AND deleted_at IS NULL');
    $stmt->execute([strtolower(trim($data['email']))]);
    if ($stmt->fetch()) {
        $errors[] = 'Email is already registered.';
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $memberId = trim($data['student_id'] ?? '') ?: null;
    $isActive = isset($data['is_active']) ? (int) $data['is_active'] : 1;

    try {
        $stmt = $db->prepare('INSERT INTO users (student_id, first_name, last_name, email, password, phone, role, staff_category, faculty_id, department_id, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $memberId,
            sanitizeString($data['first_name']),
            sanitizeString($data['last_name']),
            strtolower(trim($data['email'])),
            password_hash($data['password'], PASSWORD_DEFAULT),
            sanitizeString($data['phone'] ?? ''),
            $role,
            $staffCategory,
            $facultyId,
            $departmentId,
            $isActive,
        ]);
        $newUserId = (int) $db->lastInsertId();
        logActivity(userId(), 'user_create', 'user', $newUserId, "Admin created new {$role} account: {$data['email']}");
        return ['success' => true, 'user_id' => $newUserId];
    } catch (PDOException $e) {
        return ['success' => false, 'errors' => ['Failed to create user.']];
    }
}

function adminUpdateUser(int $id, array $data): array
{
    $errors = [];
    $db = getDB();
    
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ? AND deleted_at IS NULL');
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if (!$user) {
        return ['success' => false, 'errors' => ['User not found.']];
    }

    $role = $data['role'] ?? $user['role'];
    if (!in_array($role, ['student', 'staff', 'admin'], true)) {
        $errors[] = 'Invalid user role.';
    }

    if (empty($data['first_name'])) $errors[] = 'First name is required.';
    if (empty($data['last_name'])) $errors[] = 'Last name is required.';
    if (!validateEmail($data['email'] ?? '')) $errors[] = 'Valid email is required.';
    
    if (!empty($data['password'])) {
        $pwErrors = validatePassword($data['password']);
        $errors = array_merge($errors, $pwErrors);
    }

    $facultyId = null;
    $departmentId = null;
    $staffCategory = null;

    if ($role === 'student') {
        $facultyId = (int) ($data['faculty_id'] ?? 0);
        if ($facultyId < 1 || !facultyExists($facultyId)) {
            $errors[] = 'Please select a faculty for the student.';
        }
        if (empty(trim($data['student_id'] ?? ''))) {
            $errors[] = 'Student ID is required.';
        }
    } elseif ($role === 'staff') {
        $staffCategory = $data['staff_category'] ?? 'general';
        if ($staffCategory === 'lecturer') {
            $facultyId = (int) ($data['faculty_id'] ?? 0);
            if ($facultyId < 1 || !facultyExists($facultyId)) {
                $errors[] = 'Please select a faculty for the lecturer.';
            }
        } else {
            $departmentId = (int) ($data['department_id'] ?? 0);
            if ($departmentId < 1 || !departmentExists($departmentId)) {
                $errors[] = 'Please select a department.';
            }
        }
    } else { // admin
        $departmentId = (int) ($data['department_id'] ?? 0);
        if ($departmentId < 1) {
            $departmentId = null;
        }
    }

    $stmt = $db->prepare('SELECT id FROM users WHERE email = ? AND id != ? AND deleted_at IS NULL');
    $stmt->execute([strtolower(trim($data['email'])), $id]);
    if ($stmt->fetch()) {
        $errors[] = 'Email is already in use by another account.';
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $memberId = trim($data['student_id'] ?? '') ?: null;
    $isActive = isset($data['is_active']) ? (int) $data['is_active'] : $user['is_active'];

    try {
        $sql = 'UPDATE users SET student_id = ?, first_name = ?, last_name = ?, email = ?, phone = ?, role = ?, staff_category = ?, faculty_id = ?, department_id = ?, is_active = ?, deleted_at = CASE WHEN ? = 0 THEN COALESCE(deleted_at, NOW()) ELSE NULL END';
        $params = [
            $memberId,
            sanitizeString($data['first_name']),
            sanitizeString($data['last_name']),
            strtolower(trim($data['email'])),
            sanitizeString($data['phone'] ?? ''),
            $role,
            $staffCategory,
            $facultyId,
            $departmentId,
            $isActive,
            $isActive
        ];

        if (!empty($data['password'])) {
            $sql .= ', password = ?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= ' WHERE id = ?';
        $params[] = $id;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        logActivity(userId(), 'user_update', 'user', $id, "Admin updated user account: {$data['email']}");
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'errors' => ['Failed to update user.']];
    }
}

function adminDeleteUser(int $id): bool
{
    if ($id === userId()) {
        return false;
    }
    $db = getDB();
    $stmt = $db->prepare('SELECT email FROM users WHERE id = ? AND deleted_at IS NULL');
    $stmt->execute([$id]);
    $email = $stmt->fetchColumn();
    if (!$email) {
        return false;
    }

    $db->prepare('UPDATE users SET is_active = 0, remember_token = NULL, deleted_at = NOW() WHERE id = ? AND deleted_at IS NULL')->execute([$id]);
    logActivity(userId(), 'user_delete', 'user', $id, "Admin archived user account: {$email}");
    return true;
}
