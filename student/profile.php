<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['student']);

$profileUrl = appUrl('student/profile.php');

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
$breadcrumbs = [['label' => 'Dashboard', 'url' => appUrl('student/dashboard.php')], ['label' => 'Profile', 'url' => '']];
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
$showStudentId = true;
require dirname(__DIR__) . '/includes/templates/profile-page.php';
require dirname(__DIR__) . '/includes/templates/dashboard-footer.php';
