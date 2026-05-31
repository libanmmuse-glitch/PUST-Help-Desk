<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['staff', 'admin']);

$base = userRole() === 'admin' ? 'admin' : 'staff';
$profileUrl = appUrl($base . '/profile.php');

if (isPost()) {
    $stmt = getDB()->prepare('SELECT * FROM users WHERE id = ? AND deleted_at IS NULL');
    $stmt->execute([userId()]);
    processProfilePost($stmt->fetch(), $profileUrl);
}

$stmt = getDB()->prepare('SELECT * FROM users WHERE id = ? AND deleted_at IS NULL');
$stmt->execute([userId()]);
$user = $stmt->fetch();
refreshSessionUser(userId());

$pageTitle = 'Profile';
$breadcrumbs = [['label' => 'Dashboard', 'url' => appUrl($base . '/dashboard.php')], ['label' => 'Profile', 'url' => '']];
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
$showStudentId = false;
require dirname(__DIR__) . '/includes/templates/profile-page.php';
require dirname(__DIR__) . '/includes/templates/dashboard-footer.php';
