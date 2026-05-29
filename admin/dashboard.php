<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['admin']);

$statusCounts = getStatusCounts();
$stats = getDashboardStats();
$chartData = getChartData();
$activities = getRecentActivities(10);
$tickets = getTickets([], 1, 5)['tickets'];

$pageTitle = 'Admin Dashboard';
$loadCharts = true;
$ticketsUrl = appUrl('admin/tickets.php');
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>

<?php require dirname(__DIR__) . '/includes/templates/dashboard-stat-cards.php'; ?>

<div class="grid sm:grid-cols-2 gap-4 mb-6">
    <div class="dashboard-card rounded-xl p-5 flex items-center justify-between">
        <div>
            <p class="dash-muted text-sm">Registered Students</p>
            <p class="text-2xl font-bold dash-text mt-1"><?= (int) $stats['total_students'] ?></p>
        </div>
        <span class="w-12 h-12 rounded-xl bg-pust-primary/10 text-pust-primary flex items-center justify-center text-xl" aria-hidden="true">🎓</span>
    </div>
    <div class="dashboard-card rounded-xl p-5 flex items-center justify-between">
        <div>
            <p class="dash-muted text-sm">Staff Members</p>
            <p class="text-2xl font-bold dash-text mt-1"><?= (int) $stats['total_staff'] ?></p>
        </div>
        <span class="w-12 h-12 rounded-xl bg-pust-primary/10 text-pust-primary flex items-center justify-center text-xl" aria-hidden="true">👥</span>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6 mb-6">
    <div class="dashboard-card rounded-xl p-6">
        <h3 class="font-semibold dash-text mb-4">By Department</h3>
        <div class="h-56 relative"><canvas id="chart-departments"></canvas></div>
    </div>
    <div class="dashboard-card rounded-xl p-6">
        <h3 class="font-semibold dash-text mb-4">By Status</h3>
        <div class="h-56 relative"><canvas id="chart-status"></canvas></div>
    </div>
    <div class="dashboard-card rounded-xl p-6">
        <h3 class="font-semibold dash-text mb-4">Monthly Volume</h3>
        <div class="h-56 relative"><canvas id="chart-monthly"></canvas></div>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 dashboard-card rounded-xl p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
            <h2 class="font-semibold dash-text">Recent Tickets</h2>
            <a href="<?= e($ticketsUrl) ?>" class="text-sm font-medium text-pust-primary hover:underline">View all &rarr;</a>
        </div>
        <?php
        $baseUrl = $ticketsUrl;
        $currentStatus = '';
        require dirname(__DIR__) . '/includes/templates/status-filter-bar.php';
        ?>
        <?php $detailsBase = 'admin/ticket-details.php'; require dirname(__DIR__) . '/includes/templates/ticket-table.php'; ?>
    </div>
    <div class="dashboard-card rounded-xl p-6">
        <h2 class="font-semibold dash-text mb-4">Activity Log</h2>
        <?php require dirname(__DIR__) . '/includes/templates/dashboard-activity-feed.php'; ?>
    </div>
</div>

<script>document.addEventListener('DOMContentLoaded', function () {
  if (typeof initDashboardCharts === 'function') {
    initDashboardCharts(<?= json_encode($chartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>);
  }
});</script>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
