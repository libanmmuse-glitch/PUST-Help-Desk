<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['admin']);

$page = max(1, (int) input('page', 1));
$activeStatus = activeStatusFilter();

$filters = [];
if ($activeStatus !== '') {
    $filters['status'] = $activeStatus;
}
if ($d = input('department_id')) {
    $filters['department_id'] = (int) $d;
}
if ($q = trim((string) input('search', ''))) {
    $filters['search'] = $q;
}

$statusCounts = getStatusCountsForFilters($filters);
$result = getTickets($filters, $page, 10);
$tickets = $result['tickets'];
$pagination = $result['pagination'];
$departments = getDepartments();

$pageTitle = 'Manage Tickets';
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>
<div class="dashboard-card rounded-xl p-6">
    <?php
    $baseUrl = appUrl('admin/tickets.php');
    $currentStatus = $activeStatus;
    require dirname(__DIR__) . '/includes/templates/status-filter-bar.php';
    ?>
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <?php if ($activeStatus !== ''): ?><input type="hidden" name="status" value="<?= e($activeStatus) ?>"><?php endif; ?>
        <input type="text" name="search" value="<?= e(input('search', '')) ?>" placeholder="Search tickets..." class="flex-1 min-w-[180px] px-4 py-2 border rounded-lg dash-border">
        <select name="department_id" class="px-4 py-2 border rounded-lg dash-border">
            <option value="">All Departments</option>
            <?php foreach ($departments as $d): ?>
            <option value="<?= $d['id'] ?>" <?= (string) input('department_id') === (string) $d['id'] ? 'selected' : '' ?>><?= e($d['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="px-4 py-2 pust-btn-primary font-semibold rounded-lg">Filter</button>
        <?php if ($activeStatus !== '' || input('search') || input('department_id')): ?>
        <a href="<?= e(appUrl('admin/tickets.php')) ?>" class="px-4 py-2 border dash-border rounded-lg hover:border-pust-primary transition">Clear all</a>
        <?php endif; ?>
        <a href="<?= appUrl('admin/export.php') ?>" class="px-4 py-2 border dash-border rounded-lg hover:border-pust-primary transition">Export CSV</a>
    </form>
    <?php if ($activeStatus !== ''): ?>
    <p class="text-sm dash-muted mb-4">
        Showing <strong class="dash-text"><?= e(getStatusLabel($activeStatus)) ?></strong> tickets
        (<?= (int) $pagination['total'] ?> of <?= (int) $statusCounts['total'] ?> total)
    </p>
    <?php endif; ?>
    <?php $detailsBase = 'admin/ticket-details.php'; require dirname(__DIR__) . '/includes/templates/ticket-table.php'; ?>
    <?php $baseUrl = appUrl('admin/tickets.php'); require dirname(__DIR__) . '/includes/templates/pagination.php'; ?>
</div>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
