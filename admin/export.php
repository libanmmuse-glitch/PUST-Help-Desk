<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['admin']);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="pust_tickets_' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Ticket #', 'Subject', 'Status', 'Priority', 'Department', 'User', 'Created']);

$db = getDB();
$stmt = $db->query("SELECT t.ticket_number, t.subject, t.status, p.name AS priority, d.name AS dept,
    CONCAT(COALESCE(u.first_name,'Archived'),' ',COALESCE(u.last_name,'')) AS user_name, t.created_at
    FROM tickets t
    JOIN priorities p ON t.priority_id = p.id
    JOIN departments d ON t.department_id = d.id
    LEFT JOIN users u ON t.user_id = u.id AND u.deleted_at IS NULL
    WHERE t.deleted_at IS NULL
    ORDER BY t.created_at DESC");
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    fputcsv($out, $row);
}
fclose($out);
exit;
