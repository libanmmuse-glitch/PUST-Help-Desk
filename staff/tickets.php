<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['staff']);

$user = currentUser();
$deptId = (int) ($user['department_id'] ?? 0);
$isLecturer = isLecturerAccount($user);
$page = max(1, (int) input('page', 1));
$filters = staffTicketScopeFilters($user);
$activeStatus = activeStatusFilter();
if ($activeStatus !== '') {
    $filters['status'] = $activeStatus;
}
if ($q = input('search')) $filters['search'] = $q;
if (input('assigned') === 'me') {
    unset($filters['staff_scope_user_id'], $filters['staff_scope_department_id'], $filters['staff_scope_faculty_id']);
    $filters['assigned_to'] = userId();
}

$statusCounts = getStatusCountsForFilters($filters);
$result = getTickets($filters, $page, 10);
$tickets = $result['tickets'];
$pagination = $result['pagination'];

$pageTitle = 'Tickets';
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>
<div class="dashboard-card rounded-xl p-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h2 class="text-xl font-semibold dash-text">Tickets</h2>
        <a href="<?= appUrl('staff/submit-ticket.php') ?>" class="px-4 py-2 pust-btn-primary font-semibold rounded-lg">+ New Ticket</a>
    </div>
    <?php
    $baseUrl = appUrl('staff/tickets.php');
    $currentStatus = $activeStatus;
    require dirname(__DIR__) . '/includes/templates/status-filter-bar.php';
    ?>
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <?php if ($activeStatus !== ''): ?><input type="hidden" name="status" value="<?= e($activeStatus) ?>"><?php endif; ?>
        <input type="text" name="search" value="<?= e(input('search', '')) ?>" placeholder="Search..." class="flex-1 min-w-[200px] px-4 py-2 border rounded-lg">
        <select name="assigned" class="px-4 py-2 border rounded-lg">
            <option value="">All Assignments</option>
            <option value="me" <?= input('assigned')==='me'?'selected':'' ?>>Assigned to me</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-pust-navy text-white rounded-lg">Filter</button>
    </form>
    <?php $detailsBase = 'staff/ticket-details.php'; require dirname(__DIR__) . '/includes/templates/ticket-table.php'; ?>
    <?php $baseUrl = appUrl('staff/tickets.php'); require dirname(__DIR__) . '/includes/templates/pagination.php'; ?>
</div>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
