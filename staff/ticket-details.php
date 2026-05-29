<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['staff']);

$user = currentUser();
$id = (int) input('id');
$ticket = getTicketById($id);
$deptId = (int) ($user['department_id'] ?? 0);
$facultyId = (int) ($user['faculty_id'] ?? 0);
$isLecturer = isLecturerAccount($user);
$canAccess = $ticket && (
    ($deptId > 0 && (int) $ticket['department_id'] === $deptId)
    || ($isLecturer && $facultyId > 0 && (int) ($ticket['user_faculty_id'] ?? 0) === $facultyId)
    || (int) ($ticket['assigned_to'] ?? 0) === userId()
);

if (!$canAccess) {
    flash('error', 'Ticket not found.');
    redirect(appUrl('staff/tickets.php'));
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
    }

    redirect(appUrl('staff/ticket-details.php?id=' . $id));
}

$replies = getTicketReplies($id, true);
$replyAttachments = getReplyAttachmentsByTicket($id);
$attachments = getTicketAttachments($id);
$canManage = true;
$backUrl = appUrl('staff/tickets.php');
$pageTitle = $ticket['ticket_number'];
$breadcrumbs = [
    ['label' => 'Tickets', 'url' => appUrl('staff/tickets.php')],
    ['label' => $ticket['ticket_number'], 'url' => ''],
];
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
require dirname(__DIR__) . '/includes/templates/ticket-details.php';
require dirname(__DIR__) . '/includes/templates/dashboard-footer.php';
