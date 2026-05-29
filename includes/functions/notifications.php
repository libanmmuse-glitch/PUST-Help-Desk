<?php
/**
 * Notifications and activity logs
 */

function createNotification(int $userId, string $type, string $title, string $message, ?string $link = null): void
{
    $db = getDB();
    $stmt = $db->prepare('INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$userId, $type, $title, $message, $link]);
}

function getNotifications(int $userId, int $limit = 20, bool $unreadOnly = false): array
{
    $db = getDB();
    $sql = 'SELECT * FROM notifications WHERE user_id = ?';
    if ($unreadOnly) $sql .= ' AND is_read = 0';
    $sql .= ' ORDER BY created_at DESC LIMIT ' . (int) $limit;
    $stmt = $db->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function countUnreadNotifications(int $userId): int
{
    $db = getDB();
    $stmt = $db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}

function markNotificationRead(int $id, int $userId): void
{
    $db = getDB();
    $stmt = $db->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);
}

function markAllNotificationsRead(int $userId): void
{
    $db = getDB();
    $stmt = $db->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
    $stmt->execute([$userId]);
}

function logActivity(?int $userId, string $action, ?string $entityType, ?int $entityId, string $description): void
{
    $db = getDB();
    $stmt = $db->prepare('INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $userId,
        $action,
        $entityType,
        $entityId,
        $description,
        getClientIp(),
        substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
    ]);
}

function getRecentActivities(int $limit = 10): array
{
    $db = getDB();
    $stmt = $db->prepare("SELECT a.*, u.first_name, u.last_name FROM activity_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getDepartments(bool $activeOnly = true): array
{
    $db = getDB();
    $sql = 'SELECT * FROM departments';
    if ($activeOnly) $sql .= ' WHERE is_active = 1';
    $sql .= ' ORDER BY name';
    return $db->query($sql)->fetchAll();
}

function getCategories(?int $departmentId = null): array
{
    $db = getDB();
    if ($departmentId) {
        $stmt = $db->prepare('SELECT * FROM categories WHERE is_active = 1 AND (department_id = ? OR department_id IS NULL) ORDER BY name');
        $stmt->execute([$departmentId]);
        return $stmt->fetchAll();
    }
    return $db->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY name')->fetchAll();
}

/** Departments with nested ticket categories (for landing page, registration). */
function getDepartmentsWithCategories(): array
{
    $departments = getDepartments();
    $categories = getCategories();
    $byDept = [];
    foreach ($categories as $cat) {
        $deptId = (int) ($cat['department_id'] ?? 0);
        $byDept[$deptId][] = $cat;
    }
    foreach ($departments as &$dept) {
        $dept['categories'] = $byDept[(int) $dept['id']] ?? [];
    }
    unset($dept);
    return $departments;
}

function departmentExists(int $departmentId): bool
{
    $db = getDB();
    $stmt = $db->prepare('SELECT id FROM departments WHERE id = ? AND is_active = 1');
    $stmt->execute([$departmentId]);
    return (bool) $stmt->fetch();
}

function getPriorities(): array
{
    $db = getDB();
    return $db->query('SELECT * FROM priorities ORDER BY sort_order')->fetchAll();
}

function getSetting(string $key, $default = null)
{
    $db = getDB();
    $stmt = $db->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : $default;
}

function getStaffByDepartment(int $departmentId): array
{
    return getAssignableUsers($departmentId, false);
}

/**
 * Users who can be assigned to tickets (staff + admins).
 */
function getAssignableUsers(int $departmentId, bool $allDepartments = false): array
{
    $db = getDB();

    if ($allDepartments || userRole() === 'admin') {
        $stmt = $db->query("SELECT id, first_name, last_name, email, role, department_id
            FROM users WHERE role IN ('staff','admin') AND is_active = 1
            ORDER BY FIELD(role,'admin','staff'), first_name, last_name");
        return $stmt->fetchAll();
    }

    $stmt = $db->prepare("SELECT id, first_name, last_name, email, role, department_id
        FROM users WHERE role IN ('staff','admin') AND department_id = ? AND is_active = 1
        ORDER BY first_name, last_name");
    $stmt->execute([$departmentId]);
    $users = $stmt->fetchAll();

    // Fallback when no department staff exist yet
    if (empty($users)) {
        $stmt = $db->query("SELECT id, first_name, last_name, email, role, department_id
            FROM users WHERE role IN ('staff','admin') AND is_active = 1
            ORDER BY first_name, last_name");
        return $stmt->fetchAll();
    }

    return $users;
}

function getAllUsers(string $role = '', int $page = 1, int $perPage = 15): array
{
    $db = getDB();
    $where = '1=1';
    $params = [];
    if ($role) {
        $where .= ' AND role = ?';
        $params[] = $role;
    }
    $countStmt = $db->prepare("SELECT COUNT(*) FROM users WHERE $where");
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();
    $pagination = paginate($total, $page, $perPage);

    $sql = "SELECT u.id, u.student_id, u.first_name, u.last_name, u.email, u.phone, u.role,
            u.staff_category, u.faculty_id, u.department_id, u.avatar, u.is_active, u.created_at,
            f.name AS faculty_name, d.name AS department_name
            FROM users u
            LEFT JOIN faculties f ON u.faculty_id = f.id
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE $where ORDER BY u.created_at DESC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return ['users' => $stmt->fetchAll(), 'pagination' => $pagination];
}
