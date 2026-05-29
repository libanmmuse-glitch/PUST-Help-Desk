<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['staff']);

$user = currentUser();
$deptId = (int) ($user['department_id'] ?? 0);
$facultyId = (int) ($user['faculty_id'] ?? 0);
$isLecturer = isLecturerAccount($user);
$staffId = userId();
$scopeFilters = staffTicketScopeFilters($user);

$statusCounts = getStatusCountsForFilters($scopeFilters);
$tickets = getStaffRecentTickets($staffId, $deptId, 5, $isLecturer ? $facultyId : 0);
$activities = getRecentActivities(8);

$pageTitle = 'Staff Dashboard';
$ticketsUrl = appUrl('staff/tickets.php');
$newTicketUrl = appUrl('staff/submit-ticket.php');
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>

<?php if (!$isLecturer && $deptId < 1): ?>
<div class="mb-4 px-4 py-3 rounded-lg border border-amber-300/50 bg-amber-500/10 text-sm dash-text">
    <strong class="text-pust-amber">Note:</strong> No department is assigned to your account. You will only see tickets assigned directly to you.
</div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/templates/dashboard-stat-cards.php'; ?>

<div class="flex justify-end mb-6">
    <a href="<?= e($newTicketUrl) ?>" class="px-4 py-2.5 pust-btn-primary font-semibold rounded-lg transition">+ New Ticket</a>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 dashboard-card rounded-xl p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="font-semibold dash-text"><?= $isLecturer ? 'Recent Faculty Tickets' : 'Recent Department Tickets' ?></h2>
            <a href="<?= e($ticketsUrl) ?>" class="text-sm font-medium text-pust-primary hover:underline">View all &rarr;</a>
        </div>
        <?php $detailsBase = 'staff/ticket-details.php'; require dirname(__DIR__) . '/includes/templates/ticket-table.php'; ?>
    </div>
    <div class="dashboard-card rounded-xl p-6">
        <h2 class="font-semibold dash-text mb-4">Recent Activity</h2>
        <?php require dirname(__DIR__) . '/includes/templates/dashboard-activity-feed.php'; ?>
    </div>
</div>

<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
