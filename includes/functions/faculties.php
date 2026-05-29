<?php
/**
 * Academic faculties (student registration).
 */

function ensureFacultiesSchema(): void
{
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $db = getDB();

    $db->exec("CREATE TABLE IF NOT EXISTS faculties (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        code VARCHAR(20) NOT NULL UNIQUE,
        description TEXT NULL,
        sort_order INT NOT NULL DEFAULT 0,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    $cols = $db->query("SHOW COLUMNS FROM users LIKE 'faculty_id'")->fetchAll();
    if (empty($cols)) {
        $db->exec('ALTER TABLE users ADD COLUMN faculty_id INT UNSIGNED NULL AFTER role');
        $db->exec('ALTER TABLE users ADD INDEX idx_users_faculty (faculty_id)');
    }

    $staffCatCol = $db->query("SHOW COLUMNS FROM users LIKE 'staff_category'")->fetchAll();
    if (empty($staffCatCol)) {
        $db->exec("ALTER TABLE users ADD COLUMN staff_category VARCHAR(30) NULL AFTER role");
    }

    $fk = $db->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND CONSTRAINT_NAME = 'fk_users_faculty'")->fetch();
    if (!$fk) {
        try {
            $db->exec('ALTER TABLE users ADD CONSTRAINT fk_users_faculty FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE SET NULL');
        } catch (PDOException $e) {
            // FK may fail if incompatible data; column still usable
        }
    }

    $count = (int) $db->query('SELECT COUNT(*) FROM faculties')->fetchColumn();
    if ($count === 0) {
        $seed = [
            ['Faculty of Computer Science', 'FCS', 'Computing, software, and information technology programs', 1],
            ['Faculty of Education', 'FED', 'Teacher education and pedagogical studies', 2],
            ['Faculty of Business & Economics', 'FBE', 'Business administration, economics, and management', 3],
            ['Faculty of Health Science', 'FHS', 'Health sciences and related professional programs', 4],
            ['Faculty of Engineering', 'FEN', 'Engineering disciplines and applied technology', 5],
            ['Faculty of Agriculture', 'FAG', 'Agricultural sciences and rural development', 6],
            ['Faculty of Islamic Studies', 'FIS', 'Islamic studies and related humanities', 7],
            ['Faculty of Social Science', 'FSS', 'Social sciences, humanities, and community studies', 8],
        ];
        $stmt = $db->prepare('INSERT INTO faculties (name, code, description, sort_order) VALUES (?, ?, ?, ?)');
        foreach ($seed as $row) {
            $stmt->execute($row);
        }
    }
}

function getFaculties(bool $activeOnly = true): array
{
    ensureFacultiesSchema();
    $db = getDB();
    $sql = 'SELECT * FROM faculties';
    if ($activeOnly) {
        $sql .= ' WHERE is_active = 1';
    }
    $sql .= ' ORDER BY sort_order ASC, name ASC';
    return $db->query($sql)->fetchAll();
}

function facultyExists(int $facultyId): bool
{
    ensureFacultiesSchema();
    $db = getDB();
    $stmt = $db->prepare('SELECT id FROM faculties WHERE id = ? AND is_active = 1');
    $stmt->execute([$facultyId]);
    return (bool) $stmt->fetch();
}

/** Staff registration categories (staff role only). */
function getStaffCategories(): array
{
    return [
        'lecturer' => 'Lecturer',
        'general'  => 'Administrative / Help Desk Staff',
    ];
}

function isValidStaffCategory(string $category): bool
{
    return array_key_exists($category, getStaffCategories());
}

function staffCategoryLabel(?string $category): string
{
    if (!$category) {
        return '—';
    }
    return getStaffCategories()[$category] ?? ucfirst($category);
}

function getFacultyName(?int $facultyId): string
{
    if (!$facultyId) {
        return '—';
    }
    try {
        ensureFacultiesSchema();
        $db = getDB();
        $stmt = $db->prepare('SELECT name FROM faculties WHERE id = ?');
        $stmt->execute([$facultyId]);
        $row = $stmt->fetch();
        return $row ? $row['name'] : '—';
    } catch (PDOException $e) {
        return '—';
    }
}
