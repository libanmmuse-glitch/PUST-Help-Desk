<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['student']);

$id = (int) input('id');
$ticket = getTicketById($id);
if (!$ticket || (int)$ticket['user_id'] !== userId()) {
    flash('error', 'Ticket not found.');
    redirect(appUrl('student/tickets.php'));
}

if (isPost()) {
    requireCsrf();
    if (input('action') === 'reply') {
        $result = addTicketReply($id, userId(), (string) input('message', ''), false, $_FILES['attachment'] ?? null);
        flashTicketAction($result['success'], 'Reply sent.', $result, 'Could not send reply.');
    }
    redirect(appUrl('student/ticket-details.php?id=' . $id));
}

$replies = getTicketReplies($id);
$replyAttachments = getReplyAttachmentsByTicket($id);
$attachments = getTicketAttachments($id);
$canManage = false;
$backUrl = appUrl('student/tickets.php');

$pageTitle = $ticket['ticket_number'];
$breadcrumbs = [['label' => 'Tickets', 'url' => appUrl('student/tickets.php')], ['label' => $ticket['ticket_number'], 'url' => '']];
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
require dirname(__DIR__) . '/includes/templates/ticket-details.php';
require dirname(__DIR__) . '/includes/templates/dashboard-footer.php';
