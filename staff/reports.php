<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['staff']);

$user = currentUser();
$deptId = (int)($user['department_id'] ?? 0);
$db = getDB();
$chartData = ['by_department' => [], 'by_status' => [], 'monthly' => []];
$stmt = $db->prepare("SELECT status, COUNT(*) AS count FROM tickets WHERE department_id = ? GROUP BY status");
$stmt->execute([$deptId]);
$chartData['by_status'] = $stmt->fetchAll();
$stmt = $db->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count FROM tickets WHERE department_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month");
$stmt->execute([$deptId]);
$chartData['monthly'] = $stmt->fetchAll();
$stmt = $db->prepare("SELECT d.name, COUNT(t.id) AS count FROM departments d LEFT JOIN tickets t ON d.id = t.department_id WHERE d.id = ? GROUP BY d.id, d.name");
$stmt->execute([$deptId]);
$chartData['by_department'] = $stmt->fetchAll();

$pageTitle = 'Reports';
$loadCharts = true;
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>
<div class="grid md:grid-cols-2 gap-6">
    <div class="dashboard-card rounded-xl p-6"><h3 class="font-semibold mb-4">By Status</h3><div class="h-64"><canvas id="chart-status"></canvas></div></div>
    <div class="dashboard-card rounded-xl p-6"><h3 class="font-semibold mb-4">Monthly Tickets</h3><div class="h-64"><canvas id="chart-monthly"></canvas></div></div>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>initDashboardCharts(<?= json_encode($chartData) ?>));</script>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
