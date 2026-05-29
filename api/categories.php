<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';

header('Content-Type: application/json');
requireAuth();

$deptId = (int) ($_GET['department_id'] ?? 0);
$categories = getCategories($deptId ?: null);
jsonResponse(['categories' => $categories]);
