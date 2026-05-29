<?php
/** @var array $pagination */
/** @var string $baseUrl */
if (($pagination['total_pages'] ?? 1) <= 1) return;
$p = $pagination;
$query = $_GET;
?>
<nav class="flex items-center justify-between mt-4" aria-label="Pagination">
    <p class="text-sm dash-muted">
        Showing <?= ($p['current'] - 1) * $p['per_page'] + 1 ?>–<?= min($p['current'] * $p['per_page'], $p['total']) ?> of <?= $p['total'] ?>
    </p>
    <div class="flex gap-1">
        <?php if ($p['current'] > 1):
            $query['page'] = $p['current'] - 1;
        ?>
        <a href="<?= e($baseUrl . '?' . http_build_query($query)) ?>" class="px-3 py-1.5 rounded-lg border dash-border hover:opacity-80 text-sm">Prev</a>
        <?php endif; ?>
        <?php for ($i = max(1, $p['current'] - 2); $i <= min($p['total_pages'], $p['current'] + 2); $i++):
            $query['page'] = $i;
        ?>
        <a href="<?= e($baseUrl . '?' . http_build_query($query)) ?>"
           class="px-3 py-1.5 rounded-lg text-sm <?= $i === $p['current'] ? 'bg-pust-navy text-white' : 'border dash-border hover:opacity-80' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($p['current'] < $p['total_pages']):
            $query['page'] = $p['current'] + 1;
        ?>
        <a href="<?= e($baseUrl . '?' . http_build_query($query)) ?>" class="px-3 py-1.5 rounded-lg border dash-border hover:opacity-80 text-sm">Next</a>
        <?php endif; ?>
    </div>
</nav>
