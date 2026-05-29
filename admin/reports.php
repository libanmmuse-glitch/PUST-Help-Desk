<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['admin']);

$chartData = getChartData();
$db = getDB();
$activityReport = $db->query("SELECT u.first_name, u.last_name, u.role, COUNT(a.id) AS actions FROM activity_logs a JOIN users u ON a.user_id = u.id WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY u.id ORDER BY actions DESC LIMIT 10")->fetchAll();

$pageTitle = 'Reports & Analytics';
$loadCharts = true;
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>
<div class="grid md:grid-cols-3 gap-6 mb-6">
    <div class="dashboard-card rounded-xl p-6"><h3 class="font-semibold mb-4">By Department</h3><div class="h-64"><canvas id="chart-departments"></canvas></div></div>
    <div class="dashboard-card rounded-xl p-6"><h3 class="font-semibold mb-4">By Status</h3><div class="h-64"><canvas id="chart-status"></canvas></div></div>
    <div class="dashboard-card rounded-xl p-6"><h3 class="font-semibold mb-4">Monthly Trend</h3><div class="h-64"><canvas id="chart-monthly"></canvas></div></div>
</div>
<div class="dashboard-card rounded-xl p-6">
    <h3 class="font-semibold mb-4">User Activity (30 days)</h3>
    <table class="w-full text-sm"><thead><tr><th class="text-left py-2">User</th><th>Role</th><th>Actions</th></tr></thead>
    <tbody><?php foreach ($activityReport as $r): ?><tr class="border-t"><td class="py-2"><?= e($r['first_name'].' '.$r['last_name']) ?></td><td><?= e($r['role']) ?></td><td><?= $r['actions'] ?></td></tr><?php endforeach; ?></tbody>
    </table>
    <a href="<?= appUrl('admin/export.php') ?>" class="inline-block mt-4 px-4 py-2 bg-pust-navy text-white rounded-lg text-sm">Export Tickets CSV</a>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>initDashboardCharts(<?= json_encode($chartData) ?>));</script>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
