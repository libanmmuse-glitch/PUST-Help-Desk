<?php
/**
 * General helper functions
 */

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function appUrl(string $path = ''): string
{
    $path = trim($path);

    if ($path !== '') {
        $normalizedPath = str_replace('\\', '/', $path);
        $normalizedRoot = str_replace('\\', '/', ROOT_PATH);

        // If a filesystem path slips in, convert it back to an app-relative route.
        if (preg_match('~^[A-Za-z]:/~', $normalizedPath) === 1) {
            if (str_starts_with($normalizedPath, $normalizedRoot . '/')) {
                $normalizedPath = substr($normalizedPath, strlen($normalizedRoot) + 1);
            } else {
                $normalizedPath = basename($normalizedPath);
            }
        }

        $path = ltrim($normalizedPath, '/');

        [$route, $query] = array_pad(explode('?', $path, 2), 2, '');
        $route = preg_replace('/\.php$/i', '', $route);
        $path = $route . ($query !== '' ? '?' . $query : '');
    }

    return rtrim(APP_URL, '/') . ($path !== '' ? '/' . $path : '');
}

/** Build ticket list URL preserving current filters (search, department, etc.). */
function ticketDetailsUrl(int $ticketId, ?string $role = null): string
{
    $role = $role ?? (currentUser()['role'] ?? 'student');

    return match ($role) {
        'admin' => appUrl('admin/ticket-details.php?id=' . $ticketId),
        'staff' => appUrl('staff/ticket-details.php?id=' . $ticketId),
        default => appUrl('student/ticket-details.php?id=' . $ticketId),
    };
}

function ticketDetailsUrlForUser(int $userId, int $ticketId): string
{
    $db = getDB();
    $stmt = $db->prepare('SELECT role FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1');
    $stmt->execute([$userId]);
    $role = $stmt->fetchColumn() ?: 'student';

    return ticketDetailsUrl($ticketId, (string) $role);
}

function flashTicketAction(bool $success, string $successMsg, array $result = [], string $fallbackError = 'Action failed.'): void
{
    if ($success) {
        flash('success', $successMsg);
        return;
    }
    $errors = $result['errors'] ?? [];
    flash('error', $errors ? implode(' ', $errors) : $fallbackError);
}

function ticketFilterUrl(string $baseUrl, ?string $status = null): string
{
    $query = $_GET;
    unset($query['page']);
    unset($query['status']);
    if ($status !== null && $status !== '') {
        $query['status'] = $status;
    }
    $qs = http_build_query($query);

    return $baseUrl . ($qs !== '' ? '?' . $qs : '');
}

function assetUrl(string $path): string
{
    return appUrl('assets/' . ltrim($path, '/'));
}

function logoUrl(): string
{
    // Prefer the generated official transparent version, then the cached PNG/JPG assets.
    $candidates = [
        ROOT_PATH . '/assets/images/pust-logo-transparent.png' => assetUrl('images/pust-logo-transparent.png'),
        ROOT_PATH . '/assets/images/pust-logo.png'             => assetUrl('images/pust-logo.png'),
        ROOT_PATH . '/assets/images/pust-logo-official.jpg'    => assetUrl('images/pust-logo-official.jpg'),
        ROOT_PATH . '/assets/images/pust-logo.jpg'             => assetUrl('images/pust-logo.jpg'),
        ROOT_PATH . '/assets/images/pust-logo-source.png'      => assetUrl('images/pust-logo-source.png'),
    ];
    foreach ($candidates as $path => $url) {
        if (is_file($path)) {
            return $url . '?v=' . filemtime($path);
        }
    }
    return appUrl('logo.php');
}

/**
 * PUST logo image (responsive sizes via CSS classes/inline SVG).
 * @param string $size icon|sm|md|lg|xl
 */
function renderLogo(string $size = 'sm', string $alt = 'PUST Help Desk'): string
{
    $sizes = [
        'icon' => ['class' => 'w-8 h-8',   'px' => 32],
        'sm'   => ['class' => 'w-10 h-10',  'px' => 40],
        'md'   => ['class' => 'w-12 h-12',  'px' => 48],
        'lg'   => ['class' => 'w-16 h-16',  'px' => 64],
        'xl'   => ['class' => 'w-24 h-24',  'px' => 96],
        'hero' => ['class' => 'w-32 h-32',  'px' => 128],
    ];
    $info  = $sizes[$size] ?? $sizes['sm'];
    $class = $info['class'];
    $px    = $info['px'];

    // Prefer the generated official transparent version, then the cached PNG/JPG assets.
    $candidates = [
        ROOT_PATH . '/assets/images/pust-logo-transparent.png' => assetUrl('images/pust-logo-transparent.png'),
        ROOT_PATH . '/assets/images/pust-logo.png'             => assetUrl('images/pust-logo.png'),
        ROOT_PATH . '/assets/images/pust-logo-official.jpg'    => assetUrl('images/pust-logo-official.jpg'),
        ROOT_PATH . '/assets/images/pust-logo.jpg'             => assetUrl('images/pust-logo.jpg'),
        ROOT_PATH . '/assets/images/pust-logo-source.png'      => assetUrl('images/pust-logo-source.png'),
    ];
    foreach ($candidates as $filePath => $fileUrl) {
        if (is_file($filePath)) {
            $url = $fileUrl . '?v=' . filemtime($filePath);
            return '<img src="' . e($url) . '" alt="' . e($alt) . '" width="' . $px . '" height="' . $px . '"'
                 . ' class="pust-logo pust-logo--' . e($size) . ' ' . $class . ' object-contain flex-shrink-0"'
                 . ' style="filter: drop-shadow(0 1px 3px rgba(0,0,0,.18));">';
        }
    }

    // Fallback SVG shield when no image file is found
    return '
    <svg class="pust-logo pust-logo--' . e($size) . ' ' . $class . ' text-pust-primary dark:text-pust-primary-light transition-transform duration-300 hover:rotate-6 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-label="' . e($alt) . '">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2"/>
        <path d="M12 15c0-1.5 1-2.5 3-2.5s3 1 3 2.5v.5H12v-.5z" fill="currentColor" fill-opacity="0.2"/>
        <path d="M12 15c0-1.5-1-2.5-3-2.5S6 13.5 6 15v.5h6v-.5z" fill="currentColor" fill-opacity="0.2"/>
        <path d="M12 16V12.5c0-1.2 1-1.8 2.5-1.8s2.5.6 2.5 1.8V16" stroke="currentColor" stroke-width="1.5"/>
        <path d="M12 16V12.5c0-1.2-1-1.8-2.5-1.8S7 11.3 7 12.5V16" stroke="currentColor" stroke-width="1.5"/>
        <path d="M12 10.5v5.5" stroke="currentColor" stroke-width="2"/>
        <path d="M12 5l.6 1.2 1.4.2-1 1 .2 1.4-1.2-.7-1.2.7.2-1.4-1-1 1.4-.2L12 5z" fill="currentColor" stroke="currentColor" stroke-width="0.5"/>
    </svg>';
}

function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function input(string $key, $default = null)
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function old(string $key, string $default = ''): string
{
    return e($_SESSION['_old'][$key] ?? $default);
}

function flash(string $key, ?string $value = null)
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }
    $val = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $val;
}

function setOld(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clearOld(): void
{
    unset($_SESSION['_old']);
}

function formatDate(?string $date, string $format = 'M d, Y H:i'): string
{
    if (!$date) return '—';
    return date($format, strtotime($date));
}

function statusBadge(string $status): string
{
    $statuses = getTicketStatuses();
    $info = $statuses[$status] ?? ['label' => ucwords(str_replace('_', ' ', $status)), 'color' => '#94a3b8'];
    $color = $info['color'];
    $label = $info['label'];
    return '<span class="status-badge px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color:' . e($color) . '22;color:' . e($color) . '">' . e($label) . '</span>';
}

function priorityBadge(string $name, string $color): string
{
    return '<span class="px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color:' . e($color) . '20;color:' . e($color) . '">' . e($name) . '</span>';
}

function roleLabel(string $role): string
{
    return ucfirst($role);
}

function generateTicketNumber(): string
{
    return 'TKT-' . date('Y') . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
}

function paginate(int $total, int $page, int $perPage): array
{
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;
    return [
        'total'       => $total,
        'per_page'    => $perPage,
        'current'     => $page,
        'total_pages' => $totalPages,
        'offset'      => $offset,
    ];
}

function jsonResponse(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function getClientIp(): string
{
    return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function truncate(string $text, int $length = 100): string
{
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

function formatFileSize(int $bytes): string
{
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}
