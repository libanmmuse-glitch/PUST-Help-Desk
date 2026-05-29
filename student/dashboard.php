<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['student']);

$statusCounts = getStatusCounts(null, userId());
$tickets = getTickets(['user_id' => userId()], 1, 5)['tickets'];

$pageTitle = 'Student Dashboard';
$breadcrumbs = [['label' => 'Dashboard', 'url' => '']];
$ticketsUrl = appUrl('student/tickets.php');
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>

<?php require dirname(__DIR__) . '/includes/templates/dashboard-stat-cards.php'; ?>

<div class="flex flex-wrap gap-3 mb-6">
    <a href="<?= appUrl('student/submit-ticket.php') ?>" class="px-4 py-2.5 pust-btn-primary font-semibold rounded-lg transition">+ New Ticket</a>
    <a href="<?= e($ticketsUrl) ?>" class="px-4 py-2.5 dashboard-card border dash-border rounded-lg font-medium dash-text hover:border-pust-primary transition">View All Tickets</a>
</div>

<div class="dashboard-card rounded-xl p-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <h2 class="text-lg font-semibold dash-text">Recent Tickets</h2>
        <a href="<?= e($ticketsUrl) ?>" class="text-sm font-medium text-pust-primary hover:underline">View all &rarr;</a>
    </div>
    <?php $detailsBase = 'student/ticket-details.php'; require dirname(__DIR__) . '/includes/templates/ticket-table.php'; ?>
</div>

<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
