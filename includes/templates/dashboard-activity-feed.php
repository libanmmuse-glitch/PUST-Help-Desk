<?php
/**
 * @var array $activities
 * @var int $limit optional max height items
 */
?>
<div class="space-y-3 text-sm max-h-80 overflow-y-auto dash-activity-feed">
    <?php if (empty($activities)): ?>
    <p class="dash-muted text-center py-6">No recent activity yet.</p>
    <?php else: foreach ($activities as $a): ?>
    <div class="dash-activity-item border-l-2 border-pust-primary pl-3 py-0.5">
        <p class="dash-text leading-snug"><?= e(truncate($a['description'] ?? '', 60)) ?></p>
        <p class="text-xs dash-muted mt-1">
            <?= e(trim(($a['first_name'] ?? 'System') . ' ' . ($a['last_name'] ?? ''))) ?>
            &bull; <?= formatDate($a['created_at'] ?? '', 'M d, H:i') ?>
        </p>
    </div>
    <?php endforeach; endif; ?>
</div>
