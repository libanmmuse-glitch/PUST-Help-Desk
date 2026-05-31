<?php
/**
 * User profile picture (avatar) helpers
 */

function userInitials(array $user): string
{
    $f = strtoupper(substr($user['first_name'] ?? 'U', 0, 1));
    $l = strtoupper(substr($user['last_name'] ?? '', 0, 1));
    return $f . $l;
}

function userHasCustomAvatar(array $user): bool
{
    if (empty($user['avatar']) || $user['avatar'] === 'default-avatar.png') {
        return false;
    }
    $path = UPLOAD_PATH . '/' . ltrim($user['avatar'], '/');
    return is_file($path);
}

function avatarUrl(int $userId): string
{
    return appUrl('avatar.php?u=' . $userId);
}

function avatarSizeClasses(string $size): string
{
    return match ($size) {
        'xs'  => 'w-7 h-7 text-[10px]',
        'sm'  => 'w-8 h-8 text-xs',
        'md'  => 'w-10 h-10 text-sm',
        'lg'  => 'w-16 h-16 text-xl',
        'xl'  => 'w-24 h-24 text-3xl',
        '2xl' => 'w-32 h-32 text-4xl',
        default => 'w-10 h-10 text-sm',
    };
}

/**
 * Render avatar HTML (image or initials fallback).
 */
function renderAvatar(array $user, string $size = 'md', string $extraClass = ''): string
{
    $sizeClass = avatarSizeClasses($size);
    $initials = e(userInitials($user));
    $base = 'user-avatar inline-flex flex-shrink-0 ' . $sizeClass . ' ' . $extraClass;

    if (userHasCustomAvatar($user)) {
        $url = e(avatarUrl((int) $user['id']));
        return '<img src="' . $url . '" alt="' . $initials . '" class="' . $base . ' rounded-full object-cover ring-2 ring-white/10" width="96" height="96" loading="lazy">';
    }

    return '<span class="' . $base . ' rounded-full bg-pust-primary text-white items-center justify-center font-bold" role="img" aria-label="' . $initials . '">' . $initials . '</span>';
}

function validateAvatarUpload(array $file): array
{
    $errors = [];
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return [];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Profile picture upload failed.';
        return $errors;
    }
    $maxAvatar = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxAvatar) {
        $errors[] = 'Image must be 2MB or smaller.';
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
        $errors[] = 'Only JPG, PNG, or WebP images are allowed.';
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
        $errors[] = 'Invalid image file.';
    }
    return $errors;
}

function uploadUserAvatar(int $userId, array $file): array
{
    $errors = validateAvatarUpload($file);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'skipped' => true];
    }

    $dir = UPLOAD_PATH . '/avatars';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext === 'jpeg') {
        $ext = 'jpg';
    }
    $filename = 'user_' . $userId . '_' . time() . '.' . $ext;
    $relative = 'avatars/' . $filename;
    $fullPath = UPLOAD_PATH . '/' . $relative;

    if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
        return ['success' => false, 'errors' => ['Could not save profile picture.']];
    }

    $db = getDB();
    $stmt = $db->prepare('SELECT avatar FROM users WHERE id = ? AND deleted_at IS NULL');
    $stmt->execute([$userId]);
    $old = $stmt->fetchColumn();
    if ($old && $old !== 'default-avatar.png') {
        $oldPath = UPLOAD_PATH . '/' . ltrim($old, '/');
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }

    $db->prepare('UPDATE users SET avatar = ? WHERE id = ? AND deleted_at IS NULL')->execute([$relative, $userId]);

    if (isLoggedIn() && userId() === $userId) {
        $_SESSION['user']['avatar'] = $relative;
    }

    logActivity($userId, 'avatar_updated', 'user', $userId, 'Profile picture updated');

    return ['success' => true, 'path' => $relative];
}

function removeUserAvatar(int $userId): void
{
    $db = getDB();
    $stmt = $db->prepare('SELECT avatar FROM users WHERE id = ? AND deleted_at IS NULL');
    $stmt->execute([$userId]);
    $old = $stmt->fetchColumn();
    if ($old && $old !== 'default-avatar.png') {
        $oldPath = UPLOAD_PATH . '/' . ltrim($old, '/');
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }
    $db->prepare('UPDATE users SET avatar = ? WHERE id = ? AND deleted_at IS NULL')->execute(['default-avatar.png', $userId]);
    if (isLoggedIn() && userId() === $userId) {
        $_SESSION['user']['avatar'] = 'default-avatar.png';
    }
}

/**
 * Process profile form POST (shared by student/staff/admin).
 */
function processProfilePost(array $user, string $redirectUrl): void
{
    requireCsrf();
    $action = input('action');
    $db = getDB();
    $uid = (int) $user['id'];

    if ($action === 'profile') {
        if ($user['role'] === 'student') {
            $db->prepare('UPDATE users SET first_name=?, last_name=?, phone=?, student_id=? WHERE id=? AND deleted_at IS NULL')->execute([
                sanitizeString(input('first_name')),
                sanitizeString(input('last_name')),
                sanitizeString(input('phone')),
                sanitizeString(input('student_id')),
                $uid,
            ]);
        } else {
            $db->prepare('UPDATE users SET first_name=?, last_name=?, phone=? WHERE id=? AND deleted_at IS NULL')->execute([
                sanitizeString(input('first_name')),
                sanitizeString(input('last_name')),
                sanitizeString(input('phone')),
                $uid,
            ]);
        }
        $_SESSION['user']['first_name'] = input('first_name');
        $_SESSION['user']['last_name'] = input('last_name');

        if (!empty($_FILES['avatar']['name'])) {
            $result = uploadUserAvatar($uid, $_FILES['avatar']);
            if (!$result['success']) {
                flash('error', implode(' ', $result['errors']));
                redirect($redirectUrl);
            }
        }

        flash('success', 'Profile updated successfully.');
    } elseif ($action === 'avatar_remove') {
        removeUserAvatar($uid);
        flash('success', 'Profile picture removed.');
    } elseif ($action === 'password') {
        $result = changePassword($uid, input('current_password'), input('new_password'));
        flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Password changed.' : implode(' ', $result['errors']));
    }

    redirect($redirectUrl);
}

function refreshSessionUser(int $userId): void
{
    $db = getDB();
    $stmt = $db->prepare('SELECT id, student_id, first_name, last_name, email, phone, role, staff_category, faculty_id, department_id, avatar FROM users WHERE id = ? AND deleted_at IS NULL');
    $stmt->execute([$userId]);
    $u = $stmt->fetch();
    if ($u && isLoggedIn() && userId() === $userId) {
        $_SESSION['user'] = array_merge($_SESSION['user'] ?? [], $u);
    }
}
