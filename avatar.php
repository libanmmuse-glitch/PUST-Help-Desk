<?php
/**
 * Serve user profile pictures
 */
require_once __DIR__ . '/includes/bootstrap.php';

$userId = (int) ($_GET['u'] ?? 0);
if ($userId < 1) {
    http_response_code(404);
    exit;
}

$db = getDB();
$stmt = $db->prepare('SELECT id, first_name, last_name, avatar FROM users WHERE id = ? AND is_active = 1');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    exit;
}

// Logged-in users can view avatars (tickets, dashboard, etc.)
if (!isLoggedIn()) {
    http_response_code(403);
    exit;
}

if (userHasCustomAvatar($user)) {
    $path = UPLOAD_PATH . '/' . ltrim($user['avatar'], '/');
    $mime = match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
        'png'  => 'image/png',
        'webp' => 'image/webp',
        default => 'image/jpeg',
    };
    header('Content-Type: ' . $mime);
    header('Cache-Control: private, max-age=86400');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

// SVG initials fallback
$initials = userInitials($user);
header('Content-Type: image/svg+xml');
header('Cache-Control: private, max-age=3600');
echo '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 128 128">
  <defs><linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="' . PUST_COLOR_BLUE . '"/><stop offset="100%" stop-color="' . PUST_COLOR_BLUE_LIGHT . '"/></linearGradient></defs>
  <rect width="128" height="128" fill="url(#g)"/>
  <text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="44" font-weight="700" fill="' . PUST_COLOR_WHITE . '">' . htmlspecialchars($initials) . '</text>
</svg>';
