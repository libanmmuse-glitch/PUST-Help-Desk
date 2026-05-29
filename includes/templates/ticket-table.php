<?php
/** @var array $tickets */
/** @var string $detailsBase - e.g. student/ticket-details.php */
$tickets = $tickets ?? $recentTickets ?? [];
$isStudent = userRole() === 'student';
$colspan = $isStudent ? 7 : 8;
?>
<div class="overflow-x-auto rounded-xl border dash-border">
    <table id="tickets-table" class="w-full text-sm tickets-table">
        <thead>
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Ticket #</th>
                <th class="px-4 py-3 text-left font-semibold">Subject</th>
                <?php if (!$isStudent): ?>
                <th class="px-4 py-3 text-left font-semibold">Submitted By</th>
                <?php endif; ?>
                <th class="px-4 py-3 text-left font-semibold">Department</th>
                <th class="px-4 py-3 text-left font-semibold">Priority</th>
                <th class="px-4 py-3 text-left font-semibold">Status</th>
                <th class="px-4 py-3 text-left font-semibold">Date</th>
                <th class="px-4 py-3 text-right font-semibold">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php if (empty($tickets)): ?>
            <tr><td colspan="<?= $colspan ?>" class="px-4 py-10 text-center dash-muted">No tickets found.</td></tr>
            <?php else: foreach ($tickets as $t): ?>
            <tr>
                <td class="px-4 py-3 font-mono text-xs"><?= e($t['ticket_number']) ?></td>
                <td class="px-4 py-3 max-w-xs truncate"><?= e($t['subject']) ?></td>
                <?php if (!$isStudent): ?>
                <td class="px-4 py-3"><?= e(($t['first_name'] ?? '') . ' ' . ($t['last_name'] ?? '')) ?></td>
                <?php endif; ?>
                <td class="px-4 py-3"><?= e($t['department_name']) ?></td>
                <td class="px-4 py-3"><?= priorityBadge($t['priority_name'], $t['priority_color']) ?></td>
                <td class="px-4 py-3"><?= statusBadge($t['status']) ?></td>
                <td class="px-4 py-3 dash-muted"><?= formatDate($t['created_at'], 'M d, Y') ?></td>
                <td class="px-4 py-3 text-right">
                    <a href="<?= appUrl($detailsBase . '?id=' . $t['id']) ?>" class="text-pust-primary hover:underline font-medium">View</a>
                </td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
