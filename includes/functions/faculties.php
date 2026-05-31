<?php
/**
 * Academic faculties (student registration).
 */

function ensureFacultiesSchema(): void
{
    ensureHelpDeskDatabaseSchema();
}

function getFaculties(bool $activeOnly = true): array
{
    ensureFacultiesSchema();
    $db = getDB();
    $sql = 'SELECT * FROM faculties';
    if ($activeOnly) {
        $sql .= ' WHERE is_active = 1 AND deleted_at IS NULL';
    }
    $sql .= ' ORDER BY sort_order ASC, name ASC';
    return $db->query($sql)->fetchAll();
}

function facultyExists(int $facultyId): bool
{
    ensureFacultiesSchema();
    $db = getDB();
    $stmt = $db->prepare('SELECT id FROM faculties WHERE id = ? AND is_active = 1 AND deleted_at IS NULL');
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
        $stmt = $db->prepare('SELECT name FROM faculties WHERE id = ? AND deleted_at IS NULL');
        $stmt->execute([$facultyId]);
        $row = $stmt->fetch();
        return $row ? $row['name'] : '—';
    } catch (PDOException $e) {
        return '—';
    }
}
