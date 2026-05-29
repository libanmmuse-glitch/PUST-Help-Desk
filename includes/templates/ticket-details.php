<?php
/** @var array $ticket */
/** @var array $replies */
/** @var array $attachments */
/** @var array $replyAttachments grouped by reply id */
/** @var string $backUrl */
/** @var bool $canManage */
$replyAttachments = $replyAttachments ?? [];
$role = userRole();
?>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="dashboard-card rounded-xl p-6">
            <h3 class="text-sm font-semibold dash-muted mb-2">Ticket Progress</h3>
            <?= renderStatusTracker($ticket['status']) ?>
            <?php if ($ticket['resolved_at']): ?>
            <p class="text-xs dash-muted mt-2">Resolved: <?= formatDate($ticket['resolved_at']) ?></p>
            <?php endif; ?>
            <?php if ($ticket['closed_at']): ?>
            <p class="text-xs dash-muted">Closed: <?= formatDate($ticket['closed_at']) ?></p>
            <?php endif; ?>
        </div>
        <div class="dashboard-card rounded-xl p-6">
            <div class="flex flex-wrap justify-between gap-4 mb-4">
                <div>
                    <span class="font-mono text-sm text-slate-500"><?= e($ticket['ticket_number']) ?></span>
                    <h2 class="text-xl font-bold mt-1"><?= e($ticket['subject']) ?></h2>
                </div>
                <div class="flex gap-2"><?= statusBadge($ticket['status']) ?> <?= priorityBadge($ticket['priority_name'], $ticket['priority_color']) ?></div>
            </div>
            <p class="dash-muted whitespace-pre-wrap"><?= e($ticket['description']) ?></p>
            <?php if ($attachments): ?>
            <div class="mt-4 pt-4 border-t">
                <p class="text-sm font-medium mb-2">Attachments</p>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($attachments as $att): ?>
                    <a href="<?= appUrl('download.php?id=' . $att['id']) ?>" class="text-sm px-3 py-1 bg-slate-100 rounded-lg hover:bg-pust-amber/20"><?= e($att['file_name']) ?> (<?= formatFileSize($att['file_size']) ?>)</a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="dashboard-card rounded-xl p-6">
            <h3 class="font-semibold dash-text mb-4">Conversation</h3>
            <div class="space-y-4 mb-6 max-h-[32rem] overflow-y-auto pr-1">
                <?php foreach ($replies as $r):
                    $replyFiles = $replyAttachments[(int) $r['id']] ?? [];
                ?>
                <div class="flex gap-3 p-3 rounded-lg <?= $r['is_internal'] ? 'bg-amber-500/10 border border-amber-400/40' : 'bg-[var(--dash-bg-alt,var(--pust-bg-secondary))] border dash-border' ?>">
                    <?php
                    $replyUser = [
                        'id' => $r['user_id'],
                        'first_name' => $r['first_name'],
                        'last_name' => $r['last_name'],
                        'avatar' => $r['avatar'] ?? 'default-avatar.png',
                    ];
                    echo renderAvatar($replyUser, 'md');
                    ?>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 text-sm">
                            <span class="font-medium dash-text"><?= e($r['first_name'] . ' ' . $r['last_name']) ?></span>
                            <span class="text-xs dash-muted"><?= e(ucfirst($r['role'] ?? '')) ?></span>
                            <span class="dash-muted text-xs"><?= formatDate($r['created_at']) ?></span>
                            <?php if ($r['is_internal']): ?><span class="text-xs bg-amber-500/20 text-amber-800 dark:text-amber-200 px-2 py-0.5 rounded-full font-medium">Internal note</span><?php endif; ?>
                        </div>
                        <p class="mt-2 dash-text whitespace-pre-wrap break-words"><?= e($r['message']) ?></p>
                        <?php if ($replyFiles): ?>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <?php foreach ($replyFiles as $att): ?>
                            <a href="<?= appUrl('download.php?id=' . $att['id']) ?>" class="text-xs px-2 py-1 rounded-lg border dash-border hover:border-pust-primary transition"><?= e($att['file_name']) ?></a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($replies)): ?><p class="dash-muted text-sm text-center py-6">No replies yet. Start the conversation below.</p><?php endif; ?>
            </div>
            <?php if ($ticket['status'] !== 'closed'): ?>
            <form method="POST" enctype="multipart/form-data" data-validate class="space-y-3 border-t dash-border pt-4">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="reply">
                <label class="block text-sm font-medium dash-text" for="reply-message">Your reply</label>
                <textarea id="reply-message" name="message" required rows="4" class="w-full px-4 py-2 border dash-border rounded-lg mt-1" placeholder="Write your reply to the student or staff..."></textarea>
                <?php if ($canManage): ?>
                <label class="flex items-center gap-2 text-sm dash-muted cursor-pointer">
                    <input type="checkbox" name="is_internal" value="1" class="rounded">
                    Internal note (visible to staff/admin only)
                </label>
                <?php endif; ?>
                <div class="flex flex-wrap gap-3 items-center">
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="text-sm dash-muted">
                    <button type="submit" class="px-4 py-2.5 pust-btn-primary font-semibold rounded-lg">Send Reply</button>
                </div>
            </form>
            <?php else: ?>
            <p class="text-sm dash-muted border-t dash-border pt-4">This ticket is closed. Reopen it from the status panel to add more replies.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="space-y-4">
        <div class="dashboard-card rounded-xl p-5 space-y-3 text-sm">
            <h3 class="font-semibold">Details</h3>
            <p><span class="dash-muted">Department:</span> <?= e($ticket['department_name']) ?></p>
            <p><span class="dash-muted">Category:</span> <?= e($ticket['category_name'] ?? '—') ?></p>
            <p><span class="dash-muted">Submitted:</span> <?= formatDate($ticket['created_at']) ?></p>
            <p><span class="dash-muted">Submitted by:</span> <?= e($ticket['user_first'] . ' ' . $ticket['user_last']) ?></p>
            <?php if ($ticket['assignee_first']): ?>
            <p><span class="dash-muted">Assigned to:</span> <?= e($ticket['assignee_first'] . ' ' . $ticket['assignee_last']) ?></p>
            <?php endif; ?>
        </div>

        <?php if ($canManage): ?>
        <div class="dashboard-card rounded-xl p-5">
            <h3 class="font-semibold mb-3">Update Status</h3>
            <div class="quick-status-btns mb-4">
                <?php foreach (getTicketStatuses() as $slug => $info): ?>
                <form method="POST" class="inline">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="quick_status">
                    <input type="hidden" name="status" value="<?= e($slug) ?>">
                    <button type="submit" class="quick-status-btn <?= $ticket['status'] === $slug ? 'is-active' : '' ?>" style="--step-color:<?= e($info['color']) ?>">
                        <?= e($info['label']) ?>
                    </button>
                </form>
                <?php endforeach; ?>
            </div>
            <h3 class="font-semibold mb-3 text-sm dash-muted">Manage Ticket</h3>
            <form method="POST" class="space-y-3">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="update">
                <div>
                    <label class="text-xs dash-muted">Status</label>
                    <select name="status" class="w-full px-3 py-2 border rounded-lg text-sm mt-1">
                        <?php foreach (getTicketStatuses() as $slug => $info): ?>
                        <option value="<?= e($slug) ?>" <?= $ticket['status'] === $slug ? 'selected' : '' ?>><?= e($info['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-xs dash-muted">Priority</label>
                    <select name="priority_id" class="w-full px-3 py-2 border rounded-lg text-sm mt-1">
                        <?php foreach (getPriorities() as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $ticket['priority_id'] == $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($role !== 'student'):
                    $assignable = getAssignableUsers((int)$ticket['department_id'], $role === 'admin');
                ?>
                <div>
                    <label class="text-xs dash-muted">Assign To</label>
                    <select name="assigned_to" class="w-full px-3 py-2 border rounded-lg text-sm mt-1">
                        <option value="">— Unassigned —</option>
                        <?php foreach ($assignable as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (int)($ticket['assigned_to'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>>
                            <?= e($s['first_name'] . ' ' . $s['last_name']) ?> (<?= e(ucfirst($s['role'])) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (empty($assignable)): ?>
                    <p class="text-xs text-amber-600 mt-1">No staff users yet. <a href="<?= appUrl('admin/users.php') ?>" class="underline">Add staff</a> or use Assign to me below.</p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <button type="submit" class="w-full py-2 pust-btn-primary font-semibold rounded-lg text-sm mt-3">Update Ticket</button>
            </form>
            <?php if ($role !== 'student' && in_array($role, ['staff', 'admin'], true)): ?>
            <form method="POST" class="mt-3">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="assign_me">
                <button type="submit" class="w-full py-2 border border-pust-amber text-pust-amber rounded-lg text-sm font-medium hover:bg-pust-amber/10">
                    Assign to me
                </button>
            </form>
            <?php endif; ?>
            <?php if ($role === 'admin'): ?>
            <form method="POST" class="mt-3" onsubmit="return confirm('Delete this ticket?')">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="w-full py-2 border border-red-300 text-red-600 rounded-lg text-sm hover:bg-red-50">Delete Ticket</button>
            </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <a href="<?= e($backUrl) ?>" class="block text-center text-sm text-pust-amber hover:underline">&larr; Back to tickets</a>
    </div>
</div>
