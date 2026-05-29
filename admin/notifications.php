<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['admin']);
if (isset($_GET['read_all'])) { markAllNotificationsRead(userId()); redirect(appUrl('admin/notifications.php')); }
$notifications = getNotifications(userId(), 50);
$pageTitle = 'Notifications';
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>
<div class="dashboard-card rounded-xl p-6 max-w-3xl">
    <a href="?read_all=1" class="text-sm text-pust-amber float-right">Mark all read</a>
    <h2 class="font-semibold mb-4">Notifications</h2>
    <?php foreach ($notifications as $n): ?>
    <div class="py-3 border-b <?= $n['is_read']?'opacity-60':'' ?>">
        <p class="font-medium"><?= e($n['title']) ?></p><p class="text-sm text-slate-500"><?= e($n['message']) ?></p>
        <?php if ($n['link']): ?><a href="<?= e($n['link']) ?>" class="text-sm text-pust-amber">View</a><?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
