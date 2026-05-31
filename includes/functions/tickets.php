<?php
/**
 * Ticket management functions
 */

function getTicketById(int $id): ?array
{
    $db = getDB();
    $sql = "SELECT t.*, d.name AS department_name, c.name AS category_name,
            p.name AS priority_name, p.color AS priority_color, p.slug AS priority_slug,
            u.first_name AS user_first, u.last_name AS user_last, u.email AS user_email, u.faculty_id AS user_faculty_id,
            a.first_name AS assignee_first, a.last_name AS assignee_last
            FROM tickets t
            JOIN departments d ON t.department_id = d.id
            LEFT JOIN categories c ON t.category_id = c.id
            JOIN priorities p ON t.priority_id = p.id
            LEFT JOIN users u ON t.user_id = u.id AND u.deleted_at IS NULL
            LEFT JOIN users a ON t.assigned_to = a.id AND a.deleted_at IS NULL
            WHERE t.id = ? AND t.deleted_at IS NULL";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

/**
 * Build SQL WHERE clause for ticket queries (shared by list + status counts).
 *
 * @param array $filters same keys as getTickets()
 * @param array $options exclude_status: bool
 */
function buildTicketFilterWhere(array $filters, array &$params, array $options = []): string
{
    $where = ['t.deleted_at IS NULL'];
    $excludeStatus = !empty($options['exclude_status']);

    if (!empty($filters['staff_scope_user_id'])) {
        $staffId = (int) $filters['staff_scope_user_id'];
        $deptId = (int) ($filters['staff_scope_department_id'] ?? 0);
        $facultyId = (int) ($filters['staff_scope_faculty_id'] ?? 0);
        if ($deptId > 0) {
            $where[] = '(t.department_id = ? OR t.assigned_to = ?)';
            $params[] = $deptId;
            $params[] = $staffId;
        } elseif ($facultyId > 0) {
            $where[] = '(EXISTS (SELECT 1 FROM users requester WHERE requester.id = t.user_id AND requester.faculty_id = ? AND requester.deleted_at IS NULL) OR t.assigned_to = ?)';
            $params[] = $facultyId;
            $params[] = $staffId;
        } else {
            $where[] = 't.assigned_to = ?';
            $params[] = $staffId;
        }
    }

    if (!empty($filters['user_id'])) {
        $where[] = 't.user_id = ?';
        $params[] = $filters['user_id'];
    }
    if (!empty($filters['assigned_to'])) {
        $where[] = 't.assigned_to = ?';
        $params[] = $filters['assigned_to'];
    }
    if (!empty($filters['department_id'])) {
        $where[] = 't.department_id = ?';
        $params[] = $filters['department_id'];
    }
    if (!empty($filters['staff_department_id'])) {
        $where[] = 't.department_id = ?';
        $params[] = $filters['staff_department_id'];
    }
    if (!empty($filters['staff_faculty_id'])) {
        $where[] = 'EXISTS (SELECT 1 FROM users requester WHERE requester.id = t.user_id AND requester.faculty_id = ? AND requester.deleted_at IS NULL)';
        $params[] = $filters['staff_faculty_id'];
    }
    if (!$excludeStatus && !empty($filters['status'])) {
        $where[] = 't.status = ?';
        $params[] = $filters['status'];
    }
    if (!$excludeStatus && !empty($filters['status_in']) && is_array($filters['status_in'])) {
        $placeholders = implode(',', array_fill(0, count($filters['status_in']), '?'));
        $where[] = "t.status IN ($placeholders)";
        foreach ($filters['status_in'] as $st) {
            $params[] = $st;
        }
    }
    if (!empty($filters['priority_id'])) {
        $where[] = 't.priority_id = ?';
        $params[] = $filters['priority_id'];
    }
    if (!empty($filters['search'])) {
        $where[] = '(t.subject LIKE ? OR t.ticket_number LIKE ? OR t.description LIKE ?)';
        $s = '%' . $filters['search'] . '%';
        $params[] = $s;
        $params[] = $s;
        $params[] = $s;
    }

    return implode(' AND ', $where);
}

function isLecturerAccount(array $user): bool
{
    return ($user['role'] ?? '') === 'staff' && ($user['staff_category'] ?? '') === 'lecturer';
}

function staffTicketScopeFilters(array $user): array
{
    $filters = ['staff_scope_user_id' => (int) ($user['id'] ?? userId())];
    $departmentId = (int) ($user['department_id'] ?? 0);
    $facultyId = (int) ($user['faculty_id'] ?? 0);

    if ($departmentId > 0) {
        $filters['staff_scope_department_id'] = $departmentId;
    } elseif (isLecturerAccount($user) && $facultyId > 0) {
        $filters['staff_scope_faculty_id'] = $facultyId;
    }

    return $filters;
}

function getTickets(array $filters = [], int $page = 1, int $perPage = 10): array
{
    $db = getDB();
    $params = [];
    $whereSql = buildTicketFilterWhere($filters, $params);

    $countStmt = $db->prepare("SELECT COUNT(*) FROM tickets t WHERE $whereSql");
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $pagination = paginate($total, $page, $perPage);

    $sql = "SELECT t.*, d.name AS department_name, p.name AS priority_name, p.color AS priority_color,
            u.first_name, u.last_name
            FROM tickets t
            JOIN departments d ON t.department_id = d.id
            JOIN priorities p ON t.priority_id = p.id
            LEFT JOIN users u ON t.user_id = u.id AND u.deleted_at IS NULL
            WHERE $whereSql
            ORDER BY t.created_at DESC
            LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll();

    return ['tickets' => $tickets, 'pagination' => $pagination];
}

function createTicket(array $data, int $userId, ?array $file = null): array
{
    $errors = [];
    if (empty($data['subject'])) $errors[] = 'Subject is required.';
    if (empty($data['description'])) $errors[] = 'Description is required.';
    if (empty($data['department_id'])) $errors[] = 'Department is required.';

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $db = getDB();
    $ticketNumber = generateTicketNumber();

    // Ensure unique ticket number
    $check = $db->prepare('SELECT id FROM tickets WHERE ticket_number = ? AND deleted_at IS NULL');
    while (true) {
        $check->execute([$ticketNumber]);
        if (!$check->fetch()) break;
        $ticketNumber = generateTicketNumber();
    }

    $stmt = $db->prepare('INSERT INTO tickets (ticket_number, user_id, department_id, category_id, priority_id, subject, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $ticketNumber,
        $userId,
        (int) $data['department_id'],
        !empty($data['category_id']) ? (int) $data['category_id'] : null,
        !empty($data['priority_id']) ? (int) $data['priority_id'] : 2,
        sanitizeString($data['subject']),
        sanitizeString($data['description']),
        'pending',
    ]);

    $ticketId = (int) $db->lastInsertId();

    if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = storeUpload($file);
        if ($upload) {
            $stmt = $db->prepare('INSERT INTO attachments (ticket_id, user_id, file_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$ticketId, $userId, $upload['file_name'], $upload['file_path'], $upload['file_type'], $upload['file_size']]);
        }
    }

    logActivity($userId, 'ticket_created', 'ticket', $ticketId, "Created ticket $ticketNumber");
    createNotification($userId, 'ticket_created', 'Ticket Created', "Your ticket $ticketNumber has been submitted.", ticketDetailsUrlForUser($userId, $ticketId));

    // Notify staff in department
    $staffStmt = $db->prepare('SELECT id FROM users WHERE role = ? AND department_id = ? AND is_active = 1 AND deleted_at IS NULL');
    $staffStmt->execute(['staff', (int) $data['department_id']]);
    while ($staff = $staffStmt->fetch()) {
        createNotification($staff['id'], 'ticket_created', 'New Ticket', "New ticket $ticketNumber requires attention.", appUrl('staff/ticket-details.php?id=' . $ticketId));
    }

    // Notify admin
    $adminStmt = $db->query("SELECT id FROM users WHERE role = 'admin' AND is_active = 1 AND deleted_at IS NULL");
    while ($admin = $adminStmt->fetch()) {
        createNotification($admin['id'], 'ticket_created', 'New Ticket', "New ticket $ticketNumber submitted.", appUrl('admin/ticket-details.php?id=' . $ticketId));
    }

    return ['success' => true, 'ticket_id' => $ticketId, 'ticket_number' => $ticketNumber];
}

/**
 * Build safe update payload from POST (avoids department_id=0 etc.).
 */
function buildTicketUpdateFromPost(array $post, bool $includeAssign = true): array
{
    $data = [];

    if (!empty($post['status']) && isValidTicketStatus($post['status'])) {
        $data['status'] = $post['status'];
    }
    if (!empty($post['priority_id'])) {
        $data['priority_id'] = (int) $post['priority_id'];
    }
    if (!empty($post['department_id'])) {
        $data['department_id'] = (int) $post['department_id'];
    }
    if ($includeAssign && array_key_exists('assigned_to', $post)) {
        $data['assigned_to'] = ($post['assigned_to'] !== '' && $post['assigned_to'] !== null)
            ? (int) $post['assigned_to']
            : null;
    }

    return $data;
}

function updateTicket(int $ticketId, array $data, int $actorId): array
{
    $ticket = getTicketById($ticketId);
    if (!$ticket) {
        return ['success' => false, 'errors' => ['Ticket not found.']];
    }

    if (isset($data['status']) && !isValidTicketStatus($data['status'])) {
        return ['success' => false, 'errors' => ['Invalid ticket status.']];
    }

    // Auto-advance status when assigning staff to a pending ticket
    if (!empty($data['assigned_to']) && empty($data['status'])) {
        $suggested = suggestStatusAfterAssign($ticket['status']);
        if ($suggested) {
            $data['status'] = $suggested;
        }
    }

    $db = getDB();
    $fields = [];
    $params = [];
    $oldStatus = $ticket['status'];

    $scalarFields = ['status', 'priority_id', 'department_id', 'category_id', 'subject', 'description'];
    foreach ($scalarFields as $field) {
        if (!array_key_exists($field, $data)) {
            continue;
        }
        $val = $data[$field];
        if ($val === null || $val === '') {
            continue;
        }
        if (in_array($field, ['priority_id', 'department_id', 'category_id'], true) && (int) $val < 1) {
            continue;
        }
        $fields[] = "$field = ?";
        $params[] = $val;
    }

    if (array_key_exists('assigned_to', $data)) {
        $assignId = $data['assigned_to'];
        if ($assignId === null || $assignId === '' || (int) $assignId < 1) {
            $fields[] = 'assigned_to = NULL';
        } else {
            $assignId = (int) $assignId;
            $check = $db->prepare("SELECT id FROM users WHERE id = ? AND role IN ('staff','admin') AND is_active = 1 AND deleted_at IS NULL");
            $check->execute([$assignId]);
            if (!$check->fetch()) {
                return ['success' => false, 'errors' => ['Selected assignee is not valid. Add staff users or pick another assignee.']];
            }
            $fields[] = 'assigned_to = ?';
            $params[] = $assignId;
        }
    }

    if (isset($data['status'])) {
        foreach (applyStatusSideEffects($data['status'], $oldStatus) as $sqlFrag) {
            $fields[] = $sqlFrag;
        }
    }

    if (empty($fields)) {
        return ['success' => false, 'errors' => ['No fields to update.']];
    }

    $params[] = $ticketId;
    $sql = 'UPDATE tickets SET ' . implode(', ', $fields) . ', updated_at = NOW() WHERE id = ?';

    try {
        $db->prepare($sql)->execute($params);
    } catch (PDOException $e) {
        return ['success' => false, 'errors' => ['Could not save ticket. Please check your selections and try again.']];
    }

    $newStatus = $data['status'] ?? $oldStatus;
    $logMsg = 'Ticket ' . $ticket['ticket_number'] . ' updated';
    if (isset($data['status']) && $data['status'] !== $oldStatus) {
        $logMsg = 'Ticket ' . $ticket['ticket_number'] . ' status: ' . getStatusLabel($oldStatus) . ' → ' . getStatusLabel($data['status']);
    }
    logActivity($actorId, 'ticket_updated', 'ticket', $ticketId, $logMsg);

    $msg = 'Your ticket ' . $ticket['ticket_number'] . ' has been updated.';
    if (isset($data['status']) && $data['status'] !== $oldStatus) {
        $msg = 'Ticket ' . $ticket['ticket_number'] . ' is now: ' . getStatusLabel($data['status']);
        createNotification((int) $ticket['user_id'], 'ticket_updated', 'Status Updated', $msg, appUrl('student/ticket-details.php?id=' . $ticketId));

        if ($data['status'] === 'resolved') {
            createNotification((int) $ticket['user_id'], 'ticket_resolved', 'Ticket Resolved', 'Your ticket ' . $ticket['ticket_number'] . ' has been resolved.', appUrl('student/ticket-details.php?id=' . $ticketId));
        }
    } else {
        createNotification((int) $ticket['user_id'], 'ticket_updated', 'Ticket Updated', $msg, appUrl('student/ticket-details.php?id=' . $ticketId));
    }

    if (!empty($data['assigned_to']) && (int) $data['assigned_to'] !== (int) ($ticket['assigned_to'] ?? 0)) {
        createNotification((int) $data['assigned_to'], 'ticket_assigned', 'Ticket Assigned', 'Ticket ' . $ticket['ticket_number'] . ' assigned to you.', appUrl('staff/ticket-details.php?id=' . $ticketId));
    }

    return ['success' => true, 'status' => $newStatus];
}

function deleteTicket(int $ticketId, int $actorId): bool
{
    $ticket = getTicketById($ticketId);
    if (!$ticket) return false;
    $db = getDB();
    $db->prepare('UPDATE tickets SET deleted_at = NOW(), updated_at = NOW() WHERE id = ? AND deleted_at IS NULL')->execute([$ticketId]);
    logActivity($actorId, 'ticket_deleted', 'ticket', $ticketId, 'Archived ticket ' . $ticket['ticket_number']);
    return true;
}

function addTicketReply(int $ticketId, int $userId, string $message, bool $isInternal = false, ?array $file = null): array
{
    if (empty(trim($message))) {
        return ['success' => false, 'errors' => ['Message is required.']];
    }

    $ticket = getTicketById($ticketId);
    if (!$ticket) {
        return ['success' => false, 'errors' => ['Ticket not found.']];
    }

    $db = getDB();
    $stmt = $db->prepare('INSERT INTO ticket_replies (ticket_id, user_id, message, is_internal) VALUES (?, ?, ?, ?)');
    $stmt->execute([$ticketId, $userId, sanitizeString($message), $isInternal ? 1 : 0]);
    $replyId = (int) $db->lastInsertId();

    if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = storeUpload($file);
        if (!$upload) {
            return ['success' => false, 'errors' => ['Could not upload attachment. Check file type and size.']];
        }
        $stmt = $db->prepare('INSERT INTO attachments (ticket_id, reply_id, user_id, file_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$ticketId, $replyId, $userId, $upload['file_name'], $upload['file_path'], $upload['file_type'], $upload['file_size']]);
    }

    logActivity($userId, 'ticket_reply', 'ticket', $ticketId, 'Replied to ticket ' . $ticket['ticket_number']);

    // Auto-move to In Progress when staff/admin posts a public reply
    $actor = currentUser();
    if (!$isInternal && $actor && in_array($actor['role'], ['staff', 'admin'], true)) {
        $nextStatus = suggestStatusAfterStaffReply($ticket['status']);
        if ($nextStatus) {
            updateTicket($ticketId, ['status' => $nextStatus], $userId);
        }
    }

    if (!$isInternal) {
        $studentId = (int) $ticket['user_id'];
        $assigneeId = (int) ($ticket['assigned_to'] ?? 0);

        if ($userId !== $studentId) {
            createNotification(
                $studentId,
                'new_reply',
                'New Reply',
                'Staff replied to your ticket ' . $ticket['ticket_number'],
                ticketDetailsUrlForUser($studentId, $ticketId)
            );
        }
        if ($assigneeId > 0 && $userId !== $assigneeId) {
            createNotification(
                $assigneeId,
                'new_reply',
                'New Reply',
                'New reply on ticket ' . $ticket['ticket_number'],
                ticketDetailsUrlForUser($assigneeId, $ticketId)
            );
        }
    }

    return ['success' => true, 'reply_id' => $replyId];
}

function getTicketReplies(int $ticketId, bool $includeInternal = false): array
{
    $db = getDB();
    $sql = "SELECT r.*, u.first_name, u.last_name, u.role, u.avatar
            FROM ticket_replies r
            LEFT JOIN users u ON r.user_id = u.id AND u.deleted_at IS NULL
            WHERE r.ticket_id = ? AND r.deleted_at IS NULL";
    if (!$includeInternal) {
        $sql .= ' AND r.is_internal = 0';
    }
    $sql .= ' ORDER BY r.created_at ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute([$ticketId]);
    return $stmt->fetchAll();
}

function getTicketAttachments(int $ticketId): array
{
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM attachments WHERE ticket_id = ? AND reply_id IS NULL AND deleted_at IS NULL ORDER BY created_at');
    $stmt->execute([$ticketId]);
    return $stmt->fetchAll();
}

/** @return array<int, list<array>> Attachments grouped by reply_id */
function getReplyAttachmentsByTicket(int $ticketId): array
{
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM attachments WHERE ticket_id = ? AND reply_id IS NOT NULL AND deleted_at IS NULL ORDER BY created_at');
    $stmt->execute([$ticketId]);
    $grouped = [];
    foreach ($stmt->fetchAll() as $row) {
        $grouped[(int) $row['reply_id']][] = $row;
    }

    return $grouped;
}

/**
 * Recent tickets visible on staff dashboard (department + assigned, deduplicated).
 */
function getStaffRecentTickets(int $staffUserId, int $departmentId, int $limit = 5, int $facultyId = 0): array
{
    $db = getDB();
    $limit = max(1, min($limit, 20));

    if ($departmentId > 0) {
        $where = '(t.department_id = ? OR t.assigned_to = ?)';
        $params = [$departmentId, $staffUserId];
    } elseif ($facultyId > 0) {
        $where = '(u.faculty_id = ? OR t.assigned_to = ?)';
        $params = [$facultyId, $staffUserId];
    } else {
        $where = 't.assigned_to = ?';
        $params = [$staffUserId];
    }

    $sql = "SELECT t.*, d.name AS department_name, p.name AS priority_name, p.color AS priority_color,
            u.first_name, u.last_name
            FROM tickets t
            JOIN departments d ON t.department_id = d.id
            JOIN priorities p ON t.priority_id = p.id
            LEFT JOIN users u ON t.user_id = u.id AND u.deleted_at IS NULL
            WHERE $where
            ORDER BY t.created_at DESC
            LIMIT $limit";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getDashboardStats(?int $departmentId = null, ?int $userId = null, ?int $staffUserId = null): array
{
    $db = getDB();
    $statusCounts = getStatusCounts($departmentId, $userId, $staffUserId);

    $stats = [];
    $stats['total_tickets'] = $statusCounts['total'];
    $stats['pending_tickets'] = $statusCounts['pending'];
    $stats['open_tickets'] = $statusCounts['open'];
    $stats['in_progress_tickets'] = $statusCounts['in_progress'];
    $stats['resolved_tickets'] = $statusCounts['resolved'];
    $stats['closed_tickets'] = $statusCounts['closed'];
    $stats['active_tickets'] = $statusCounts['active'];
    $stats['total_students'] = (int) $db->query("SELECT COUNT(*) FROM users WHERE role = 'student' AND deleted_at IS NULL")->fetchColumn();
    $stats['total_staff'] = (int) $db->query("SELECT COUNT(*) FROM users WHERE role = 'staff' AND deleted_at IS NULL")->fetchColumn();

    return $stats;
}

function normalizeStatusChartRows(array $rows): array
{
    $map = [];
    foreach ($rows as $row) {
        $map[$row['status']] = (int) $row['count'];
    }
    $normalized = [];
    foreach (getTicketStatuses() as $slug => $info) {
        $normalized[] = [
            'status' => $slug,
            'label'  => $info['label'],
            'count'  => $map[$slug] ?? 0,
        ];
    }
    return $normalized;
}

function getChartData(array $filters = []): array
{
    $db = getDB();
    $params = [];
    $whereSql = buildTicketFilterWhere($filters, $params);

    $byDept = $db->query(
        'SELECT d.name, COUNT(t.id) AS count FROM departments d LEFT JOIN tickets t ON d.id = t.department_id AND t.deleted_at IS NULL GROUP BY d.id, d.name ORDER BY d.name'
    )->fetchAll();

    $statusSql = "SELECT t.status, COUNT(*) AS count FROM tickets t WHERE $whereSql GROUP BY t.status";
    $statusStmt = $db->prepare($statusSql);
    $statusStmt->execute($params);
    $byStatus = normalizeStatusChartRows($statusStmt->fetchAll());

    $monthlySql = "SELECT DATE_FORMAT(t.created_at, '%Y-%m') AS month, COUNT(*) AS count
        FROM tickets t WHERE $whereSql AND t.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY month ORDER BY month";
    $monthlyStmt = $db->prepare($monthlySql);
    $monthlyStmt->execute($params);
    $monthly = $monthlyStmt->fetchAll();

    return [
        'by_department' => $byDept,
        'by_status'     => $byStatus,
        'monthly'       => $monthly,
    ];
}
