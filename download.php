<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/middleware/auth.php';
requireAuth();

$id = (int) input('id');
$db = getDB();
$stmt = $db->prepare('SELECT a.*, t.user_id, t.department_id FROM attachments a LEFT JOIN tickets t ON a.ticket_id = t.id WHERE a.id = ?');
$stmt->execute([$id]);
$att = $stmt->fetch();

if (!$att) {
    http_response_code(404);
    die('File not found.');
}

$allowed = false;
if ((int)$att['user_id'] === userId()) $allowed = true;
if (userRole() === 'admin') $allowed = true;
if (userRole() === 'staff' && (int)$att['department_id'] === (int)(currentUser()['department_id'] ?? 0)) $allowed = true;
if ((int)$att['user_id'] === userId()) $allowed = true;

if (!$allowed) {
    http_response_code(403);
    die('Access denied.');
}

$path = UPLOAD_PATH . '/' . $att['file_path'];
if (!file_exists($path)) {
    http_response_code(404);
    die('File missing on server.');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($att['file_name']) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
