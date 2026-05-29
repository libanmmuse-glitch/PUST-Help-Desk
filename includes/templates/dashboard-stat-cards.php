<?php
/**
 * Status stat cards row for dashboards.
 *
 * @var array $statusCounts from getStatusCounts()
 * @var string $ticketsUrl base list URL
 */
$statCards = [
    ['label' => 'Total', 'key' => 'total', 'status' => '', 'class' => 'stat-card--dark'],
    ['label' => 'Pending', 'key' => 'pending', 'status' => 'pending', 'class' => 'stat-card--slate'],
    ['label' => 'Open', 'key' => 'open', 'status' => 'open', 'class' => 'stat-card--blue'],
    ['label' => 'In Progress', 'key' => 'in_progress', 'status' => 'in_progress', 'class' => 'stat-card--amber'],
    ['label' => 'Resolved', 'key' => 'resolved', 'status' => 'resolved', 'class' => 'stat-card--emerald'],
    ['label' => 'Closed', 'key' => 'closed', 'status' => 'closed', 'class' => 'stat-card--slate'],
];
?>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <?php foreach ($statCards as $card):
        $val = $card['key'] === 'total' ? ($statusCounts['total'] ?? 0) : ($statusCounts[$card['key']] ?? 0);
        $href = ticketFilterUrl($ticketsUrl, $card['status'] !== '' ? $card['status'] : null);
    ?>
    <a href="<?= e($href) ?>" class="stat-card rounded-xl p-4 <?= e($card['class']) ?> block transition">
        <p class="text-xs font-medium opacity-90"><?= e($card['label']) ?></p>
        <p class="text-2xl font-bold mt-1 tabular-nums"><?= (int) $val ?></p>
    </a>
    <?php endforeach; ?>
</div>
