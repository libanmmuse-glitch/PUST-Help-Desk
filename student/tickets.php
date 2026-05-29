<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['student']);

$page = max(1, (int) input('page', 1));
$activeStatus = activeStatusFilter();
$filters = ['user_id' => userId()];
if ($activeStatus !== '') {
    $filters['status'] = $activeStatus;
}
if ($q = input('search')) $filters['search'] = $q;

$statusCounts = getStatusCountsForFilters($filters);
$result = getTickets($filters, $page, (int) getSetting('tickets_per_page', 10));
$tickets = $result['tickets'];
$pagination = $result['pagination'];

$pageTitle = 'My Tickets';
$breadcrumbs = [['label' => 'Dashboard', 'url' => appUrl('student/dashboard.php')], ['label' => 'Tickets', 'url' => '']];
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>
<div class="dashboard-card rounded-xl p-6">
    <?php
    $baseUrl = appUrl('student/tickets.php');
    $currentStatus = $activeStatus;
    require dirname(__DIR__) . '/includes/templates/status-filter-bar.php';
    ?>
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <?php if ($activeStatus !== ''): ?><input type="hidden" name="status" value="<?= e($activeStatus) ?>"><?php endif; ?>
        <input type="text" name="search" value="<?= e(input('search', '')) ?>" placeholder="Search tickets..." class="flex-1 min-w-[200px] px-4 py-2 border rounded-lg">
        <button type="submit" class="px-4 py-2 bg-pust-navy text-white rounded-lg">Search</button>
        <a href="<?= appUrl('student/submit-ticket.php') ?>" class="px-4 py-2 pust-btn-primary font-semibold rounded-lg ml-auto">+ New Ticket</a>
    </form>
    <?php $detailsBase = 'student/ticket-details.php'; require dirname(__DIR__) . '/includes/templates/ticket-table.php'; ?>
    <?php $baseUrl = appUrl('student/tickets.php'); require dirname(__DIR__) . '/includes/templates/pagination.php'; ?>
</div>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
