<?php
/**
 * @var array $statusCounts
 * @var string $baseUrl list page URL
 * @var string $currentStatus active status slug or empty
 */
$statuses = getTicketStatuses();
$current = $currentStatus ?? activeStatusFilter();
?>
<div class="status-filters mb-4" role="tablist" aria-label="Filter by status">
    <a href="<?= e(ticketFilterUrl($baseUrl, null)) ?>"
       class="status-filter-chip <?= $current === '' ? 'active' : '' ?>"
       role="tab"
       aria-selected="<?= $current === '' ? 'true' : 'false' ?>">
        All <span class="count"><?= (int) ($statusCounts['total'] ?? 0) ?></span>
    </a>
    <?php foreach ($statuses as $slug => $info): ?>
    <a href="<?= e(ticketFilterUrl($baseUrl, $slug)) ?>"
       class="status-filter-chip <?= $current === $slug ? 'active' : '' ?>"
       role="tab"
       aria-selected="<?= $current === $slug ? 'true' : 'false' ?>">
        <?= e($info['label']) ?>
        <span class="count"><?= (int) ($statusCounts[$slug] ?? 0) ?></span>
    </a>
    <?php endforeach; ?>
</div>
