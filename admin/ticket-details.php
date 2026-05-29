<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['admin']);

$id = (int) input('id');
$ticket = getTicketById($id);
if (!$ticket) {
    flash('error', 'Ticket not found.');
    redirect(appUrl('admin/tickets.php'));
}

if (isPost()) {
    requireCsrf();
    $action = input('action');

    if ($action === 'reply') {
        $result = addTicketReply($id, userId(), (string) input('message', ''), !empty($_POST['is_internal']), $_FILES['attachment'] ?? null);
        flashTicketAction($result['success'], 'Reply sent.', $result, 'Could not send reply.');
    } elseif ($action === 'quick_status') {
        $st = trim((string) input('status', ''));
        if ($st !== '' && isValidTicketStatus($st)) {
            $result = updateTicket($id, ['status' => $st], userId());
            flashTicketAction($result['success'], 'Status updated to ' . getStatusLabel($st) . '.', $result, 'Could not update status.');
        } else {
            flash('error', 'Invalid status.');
        }
    } elseif ($action === 'update') {
        $result = updateTicket($id, buildTicketUpdateFromPost($_POST, true), userId());
        flashTicketAction($result['success'], 'Ticket updated successfully.', $result, 'Update failed.');
    } elseif ($action === 'assign_me') {
        $result = updateTicket($id, ['assigned_to' => userId()], userId());
        flashTicketAction($result['success'], 'Ticket assigned to you.', $result, 'Could not assign ticket.');
    } elseif ($action === 'delete') {
        deleteTicket($id, userId());
        flash('success', 'Ticket deleted.');
        redirect(appUrl('admin/tickets.php'));
    }

    redirect(appUrl('admin/ticket-details.php?id=' . $id));
}

$replies = getTicketReplies($id, true);
$replyAttachments = getReplyAttachmentsByTicket($id);
$attachments = getTicketAttachments($id);
$canManage = true;
$backUrl = appUrl('admin/tickets.php');
$pageTitle = $ticket['ticket_number'];
$breadcrumbs = [
    ['label' => 'Tickets', 'url' => appUrl('admin/tickets.php')],
    ['label' => $ticket['ticket_number'], 'url' => ''],
];
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
require dirname(__DIR__) . '/includes/templates/ticket-details.php';
require dirname(__DIR__) . '/includes/templates/dashboard-footer.php';
