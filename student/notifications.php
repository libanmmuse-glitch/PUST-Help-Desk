<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['student']);

if (isset($_GET['read_all'])) {
    markAllNotificationsRead(userId());
    redirect(appUrl('student/notifications.php'));
}
if ($nid = (int) input('read')) {
    markNotificationRead($nid, userId());
}

$notifications = getNotifications(userId(), 50);
$pageTitle = 'Notifications';
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>
<div class="dashboard-card rounded-xl p-6 max-w-3xl">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold">Notifications</h2>
        <a href="?read_all=1" class="text-sm text-pust-amber hover:underline">Mark all as read</a>
    </div>
    <div class="divide-y">
        <?php foreach ($notifications as $n): ?>
        <div class="py-4 flex gap-4 <?= $n['is_read'] ? 'opacity-60' : '' ?>">
            <div class="w-2 h-2 mt-2 rounded-full flex-shrink-0 <?= $n['is_read'] ? 'bg-transparent' : 'bg-pust-amber' ?>"></div>
            <div class="flex-1">
                <p class="font-medium"><?= e($n['title']) ?></p>
                <p class="text-sm text-slate-500 mt-1"><?= e($n['message']) ?></p>
                <p class="text-xs text-slate-400 mt-2"><?= formatDate($n['created_at']) ?></p>
                <?php if ($n['link']): ?><a href="<?= e($n['link']) ?>" class="text-sm text-pust-amber hover:underline mt-1 inline-block">View &rarr;</a><?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($notifications)): ?><p class="text-slate-500 py-8 text-center">No notifications.</p><?php endif; ?>
    </div>
</div>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
