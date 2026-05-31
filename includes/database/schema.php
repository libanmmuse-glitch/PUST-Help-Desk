<?php
/**
 * Central database schema module.
 *
 * Keeps every table definition, seed set, and low-risk upgrade in one place
 * so the app does not have to scatter CREATE/ALTER logic across helpers.
 */

function dbColumnExists(PDO $db, string $table, string $column): bool
{
    $stmt = $db->prepare(
        'SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1'
    );
    $stmt->execute([$table, $column]);
    return (bool) $stmt->fetchColumn();
}

function dbIndexExists(PDO $db, string $table, string $index): bool
{
    $stmt = $db->prepare(
        'SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1'
    );
    $stmt->execute([$table, $index]);
    return (bool) $stmt->fetchColumn();
}

function ensureColumn(PDO $db, string $table, string $columnSql, string $columnName, ?string $after = null): void
{
    if (dbColumnExists($db, $table, $columnName)) {
        return;
    }
    $sql = 'ALTER TABLE `' . $table . '` ADD COLUMN `' . $columnName . '` ' . $columnSql;
    if ($after !== null) {
        $sql .= ' AFTER `' . $after . '`';
    }
    $db->exec($sql);
}

function ensureIndex(PDO $db, string $table, string $indexName, string $indexSql): void
{
    if (dbIndexExists($db, $table, $indexName)) {
        return;
    }
    $db->exec('ALTER TABLE `' . $table . '` ADD INDEX `' . $indexName . '` ' . $indexSql);
}

function ensureUniqueIndex(PDO $db, string $table, string $indexName, string $indexSql): void
{
    if (dbIndexExists($db, $table, $indexName)) {
        return;
    }
    $db->exec('ALTER TABLE `' . $table . '` ADD UNIQUE INDEX `' . $indexName . '` ' . $indexSql);
}

function ensureForeignKey(PDO $db, string $table, string $constraintName, string $constraintSql): void
{
    $stmt = $db->prepare(
        'SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? LIMIT 1'
    );
    $stmt->execute([$table, $constraintName]);
    if ($stmt->fetchColumn()) {
        return;
    }
    $db->exec('ALTER TABLE `' . $table . '` ADD CONSTRAINT `' . $constraintName . '` ' . $constraintSql);
}

function ensureHelpDeskDatabaseSchema(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $db = getDB();
    $db->exec('SET FOREIGN_KEY_CHECKS = 0');

    $db->exec("CREATE TABLE IF NOT EXISTS faculties (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        code VARCHAR(20) NOT NULL,
        description TEXT NULL,
        sort_order INT NOT NULL DEFAULT 0,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        UNIQUE KEY uq_faculties_code (code),
        KEY idx_faculties_active (is_active, deleted_at, sort_order, name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS departments (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        code VARCHAR(20) NOT NULL,
        description TEXT NULL,
        email VARCHAR(255) NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        UNIQUE KEY uq_departments_code (code),
        KEY idx_departments_active (is_active, deleted_at, name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS priorities (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        slug VARCHAR(50) NOT NULL,
        color VARCHAR(20) NOT NULL DEFAULT '#6b7280',
        sort_order INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        UNIQUE KEY uq_priorities_slug (slug),
        KEY idx_priorities_sort (sort_order, deleted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        department_id INT UNSIGNED NULL,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL,
        description TEXT NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        UNIQUE KEY uq_categories_slug (slug),
        KEY idx_categories_department_active (department_id, is_active, deleted_at, name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(50) NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NULL,
        role ENUM('student', 'staff', 'admin') NOT NULL DEFAULT 'student',
        staff_category VARCHAR(30) NULL,
        faculty_id INT UNSIGNED NULL,
        department_id INT UNSIGNED NULL,
        avatar VARCHAR(255) NOT NULL DEFAULT 'default-avatar.png',
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        email_verified_at DATETIME NULL,
        remember_token VARCHAR(100) NULL,
        last_login_at DATETIME NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        UNIQUE KEY uq_users_email (email),
        UNIQUE KEY uq_users_student_id (student_id),
        KEY idx_users_role_active (role, is_active, deleted_at),
        KEY idx_users_faculty_active (faculty_id, role, is_active, deleted_at),
        KEY idx_users_department_active (department_id, role, is_active, deleted_at),
        KEY idx_users_staff_category (staff_category, deleted_at),
        KEY idx_users_last_login (last_login_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS tickets (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        ticket_number VARCHAR(20) NOT NULL,
        user_id INT UNSIGNED NOT NULL,
        department_id INT UNSIGNED NOT NULL,
        category_id INT UNSIGNED NULL,
        priority_id INT UNSIGNED NOT NULL DEFAULT 2,
        assigned_to INT UNSIGNED NULL,
        subject VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        status ENUM('pending', 'open', 'in_progress', 'resolved', 'closed') NOT NULL DEFAULT 'pending',
        is_internal TINYINT(1) NOT NULL DEFAULT 0,
        resolved_at DATETIME NULL,
        closed_at DATETIME NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        UNIQUE KEY uq_tickets_number (ticket_number),
        KEY idx_tickets_user_created (user_id, deleted_at, created_at),
        KEY idx_tickets_department_status (department_id, status, deleted_at, created_at),
        KEY idx_tickets_assigned_status (assigned_to, status, deleted_at, created_at),
        KEY idx_tickets_priority_status (priority_id, status, deleted_at, created_at),
        KEY idx_tickets_status_created (status, deleted_at, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS ticket_replies (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        ticket_id INT UNSIGNED NOT NULL,
        user_id INT UNSIGNED NOT NULL,
        message TEXT NOT NULL,
        is_internal TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        KEY idx_replies_ticket_created (ticket_id, deleted_at, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS attachments (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        ticket_id INT UNSIGNED NULL,
        reply_id INT UNSIGNED NULL,
        user_id INT UNSIGNED NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        file_type VARCHAR(100) NOT NULL,
        file_size INT UNSIGNED NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        KEY idx_attachments_ticket_reply (ticket_id, reply_id, deleted_at, created_at),
        KEY idx_attachments_user_created (user_id, deleted_at, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        type VARCHAR(50) NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        link VARCHAR(500) NULL,
        is_read TINYINT(1) NOT NULL DEFAULT 0,
        read_at DATETIME NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        KEY idx_notifications_user_read (user_id, is_read, deleted_at, created_at),
        KEY idx_notifications_type (type, deleted_at, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS activity_logs (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NULL,
        action VARCHAR(100) NOT NULL,
        entity_type VARCHAR(50) NULL,
        entity_id INT UNSIGNED NULL,
        description TEXT NOT NULL,
        ip_address VARCHAR(45) NULL,
        user_agent VARCHAR(500) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        KEY idx_activity_user_created (user_id, deleted_at, created_at),
        KEY idx_activity_created (created_at, deleted_at),
        KEY idx_activity_entity (entity_type, entity_id, deleted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL,
        setting_value TEXT NULL,
        setting_group VARCHAR(50) NOT NULL DEFAULT 'general',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        UNIQUE KEY uq_settings_key (setting_key),
        KEY idx_settings_group (setting_group, deleted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS contact_submissions (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NULL,
        message TEXT NOT NULL,
        email_sent TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        KEY idx_contact_created (created_at, deleted_at),
        KEY idx_contact_email (email, deleted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS password_resets (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        token VARCHAR(255) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at DATETIME NULL,
        KEY idx_password_resets_email (email, deleted_at, created_at),
        KEY idx_password_resets_token (token, deleted_at),
        KEY idx_password_resets_expires (expires_at, deleted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    ensureForeignKey($db, 'users', 'fk_users_faculty', 'FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE SET NULL');
    ensureForeignKey($db, 'users', 'fk_users_department', 'FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL');
    ensureForeignKey($db, 'categories', 'fk_categories_department', 'FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL');
    ensureForeignKey($db, 'tickets', 'fk_tickets_user', 'FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
    ensureForeignKey($db, 'tickets', 'fk_tickets_department', 'FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE RESTRICT');
    ensureForeignKey($db, 'tickets', 'fk_tickets_category', 'FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL');
    ensureForeignKey($db, 'tickets', 'fk_tickets_priority', 'FOREIGN KEY (priority_id) REFERENCES priorities(id) ON DELETE RESTRICT');
    ensureForeignKey($db, 'tickets', 'fk_tickets_assigned_to', 'FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL');
    ensureForeignKey($db, 'ticket_replies', 'fk_ticket_replies_ticket', 'FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE');
    ensureForeignKey($db, 'ticket_replies', 'fk_ticket_replies_user', 'FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
    ensureForeignKey($db, 'attachments', 'fk_attachments_ticket', 'FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE');
    ensureForeignKey($db, 'attachments', 'fk_attachments_reply', 'FOREIGN KEY (reply_id) REFERENCES ticket_replies(id) ON DELETE CASCADE');
    ensureForeignKey($db, 'attachments', 'fk_attachments_user', 'FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
    ensureForeignKey($db, 'notifications', 'fk_notifications_user', 'FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
    ensureForeignKey($db, 'activity_logs', 'fk_activity_logs_user', 'FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL');

    ensureColumn($db, 'users', "VARCHAR(50) NULL", 'student_id', 'id');
    ensureColumn($db, 'users', "VARCHAR(30) NULL", 'staff_category', 'role');
    ensureColumn($db, 'users', "INT UNSIGNED NULL", 'faculty_id', 'staff_category');
    ensureColumn($db, 'users', "INT UNSIGNED NULL", 'department_id', 'faculty_id');
    ensureColumn($db, 'users', "VARCHAR(255) NOT NULL DEFAULT 'default-avatar.png'", 'avatar', 'department_id');
    ensureColumn($db, 'users', "TINYINT(1) NOT NULL DEFAULT 1", 'is_active', 'avatar');
    ensureColumn($db, 'users', "DATETIME NULL", 'email_verified_at', 'is_active');
    ensureColumn($db, 'users', "VARCHAR(100) NULL", 'remember_token', 'email_verified_at');
    ensureColumn($db, 'users', "DATETIME NULL", 'last_login_at', 'remember_token');
    ensureColumn($db, 'users', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", 'created_at', 'last_login_at');
    ensureColumn($db, 'users', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", 'updated_at', 'created_at');
    ensureColumn($db, 'users', "DATETIME NULL", 'deleted_at', 'updated_at');

    ensureColumn($db, 'departments', "VARCHAR(100) NOT NULL", 'name', 'id');
    ensureColumn($db, 'departments', "VARCHAR(20) NOT NULL", 'code', 'name');
    ensureColumn($db, 'departments', "TEXT NULL", 'description', 'code');
    ensureColumn($db, 'departments', "VARCHAR(255) NULL", 'email', 'description');
    ensureColumn($db, 'departments', "TINYINT(1) NOT NULL DEFAULT 1", 'is_active', 'email');
    ensureColumn($db, 'departments', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", 'created_at', 'is_active');
    ensureColumn($db, 'departments', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", 'updated_at', 'created_at');
    ensureColumn($db, 'departments', "DATETIME NULL", 'deleted_at', 'updated_at');

    ensureColumn($db, 'faculties', "VARCHAR(150) NOT NULL", 'name', 'id');
    ensureColumn($db, 'faculties', "VARCHAR(20) NOT NULL", 'code', 'name');
    ensureColumn($db, 'faculties', "TEXT NULL", 'description', 'code');
    ensureColumn($db, 'faculties', "INT NOT NULL DEFAULT 0", 'sort_order', 'description');
    ensureColumn($db, 'faculties', "TINYINT(1) NOT NULL DEFAULT 1", 'is_active', 'sort_order');
    ensureColumn($db, 'faculties', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", 'created_at', 'is_active');
    ensureColumn($db, 'faculties', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", 'updated_at', 'created_at');
    ensureColumn($db, 'faculties', "DATETIME NULL", 'deleted_at', 'updated_at');

    ensureColumn($db, 'priorities', "VARCHAR(50) NOT NULL", 'name', 'id');
    ensureColumn($db, 'priorities', "VARCHAR(50) NOT NULL", 'slug', 'name');
    ensureColumn($db, 'priorities', "VARCHAR(20) NOT NULL DEFAULT '#6b7280'", 'color', 'slug');
    ensureColumn($db, 'priorities', "INT NOT NULL DEFAULT 0", 'sort_order', 'color');
    ensureColumn($db, 'priorities', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", 'created_at', 'sort_order');
    ensureColumn($db, 'priorities', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", 'updated_at', 'created_at');
    ensureColumn($db, 'priorities', "DATETIME NULL", 'deleted_at', 'updated_at');

    ensureColumn($db, 'categories', "INT UNSIGNED NULL", 'department_id', 'id');
    ensureColumn($db, 'categories', "VARCHAR(100) NOT NULL", 'name', 'department_id');
    ensureColumn($db, 'categories', "VARCHAR(100) NOT NULL", 'slug', 'name');
    ensureColumn($db, 'categories', "TEXT NULL", 'description', 'slug');
    ensureColumn($db, 'categories', "TINYINT(1) NOT NULL DEFAULT 1", 'is_active', 'description');
    ensureColumn($db, 'categories', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", 'created_at', 'is_active');
    ensureColumn($db, 'categories', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", 'updated_at', 'created_at');
    ensureColumn($db, 'categories', "DATETIME NULL", 'deleted_at', 'updated_at');

    ensureColumn($db, 'tickets', "VARCHAR(20) NOT NULL", 'ticket_number', 'id');
    ensureColumn($db, 'tickets', "INT UNSIGNED NOT NULL", 'user_id', 'ticket_number');
    ensureColumn($db, 'tickets', "INT UNSIGNED NOT NULL", 'department_id', 'user_id');
    ensureColumn($db, 'tickets', "INT UNSIGNED NULL", 'category_id', 'department_id');
    ensureColumn($db, 'tickets', "INT UNSIGNED NOT NULL DEFAULT 2", 'priority_id', 'category_id');
    ensureColumn($db, 'tickets', "INT UNSIGNED NULL", 'assigned_to', 'priority_id');
    ensureColumn($db, 'tickets', "VARCHAR(255) NOT NULL", 'subject', 'assigned_to');
    ensureColumn($db, 'tickets', "TEXT NOT NULL", 'description', 'subject');
    ensureColumn($db, 'tickets', "ENUM('pending', 'open', 'in_progress', 'resolved', 'closed') NOT NULL DEFAULT 'pending'", 'status', 'description');
    ensureColumn($db, 'tickets', "TINYINT(1) NOT NULL DEFAULT 0", 'is_internal', 'status');
    ensureColumn($db, 'tickets', "DATETIME NULL", 'resolved_at', 'is_internal');
    ensureColumn($db, 'tickets', "DATETIME NULL", 'closed_at', 'resolved_at');
    ensureColumn($db, 'tickets', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", 'created_at', 'closed_at');
    ensureColumn($db, 'tickets', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", 'updated_at', 'created_at');
    ensureColumn($db, 'tickets', "DATETIME NULL", 'deleted_at', 'updated_at');

    ensureColumn($db, 'ticket_replies', "INT UNSIGNED NOT NULL", 'ticket_id', 'id');
    ensureColumn($db, 'ticket_replies', "INT UNSIGNED NOT NULL", 'user_id', 'ticket_id');
    ensureColumn($db, 'ticket_replies', "TEXT NOT NULL", 'message', 'user_id');
    ensureColumn($db, 'ticket_replies', "TINYINT(1) NOT NULL DEFAULT 0", 'is_internal', 'message');
    ensureColumn($db, 'ticket_replies', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", 'created_at', 'is_internal');
    ensureColumn($db, 'ticket_replies', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", 'updated_at', 'created_at');
    ensureColumn($db, 'ticket_replies', "DATETIME NULL", 'deleted_at', 'updated_at');

    ensureColumn($db, 'attachments', "INT UNSIGNED NULL", 'ticket_id', 'id');
    ensureColumn($db, 'attachments', "INT UNSIGNED NULL", 'reply_id', 'ticket_id');
    ensureColumn($db, 'attachments', "INT UNSIGNED NOT NULL", 'user_id', 'reply_id');
    ensureColumn($db, 'attachments', "VARCHAR(255) NOT NULL", 'file_name', 'user_id');
    ensureColumn($db, 'attachments', "VARCHAR(500) NOT NULL", 'file_path', 'file_name');
    ensureColumn($db, 'attachments', "VARCHAR(100) NOT NULL", 'file_type', 'file_path');
    ensureColumn($db, 'attachments', "INT UNSIGNED NOT NULL", 'file_size', 'file_type');
    ensureColumn($db, 'attachments', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", 'created_at', 'file_size');
    ensureColumn($db, 'attachments', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", 'updated_at', 'created_at');
    ensureColumn($db, 'attachments', "DATETIME NULL", 'deleted_at', 'updated_at');

    ensureColumn($db, 'notifications', "INT UNSIGNED NOT NULL", 'user_id', 'id');
    ensureColumn($db, 'notifications', "VARCHAR(50) NOT NULL", 'type', 'user_id');
    ensureColumn($db, 'notifications', "VARCHAR(255) NOT NULL", 'title', 'type');
    ensureColumn($db, 'notifications', "TEXT NOT NULL", 'message', 'title');
    ensureColumn($db, 'notifications', "VARCHAR(500) NULL", 'link', 'message');
    ensureColumn($db, 'notifications', "TINYINT(1) NOT NULL DEFAULT 0", 'is_read', 'link');
    ensureColumn($db, 'notifications', "DATETIME NULL", 'read_at', 'is_read');
    ensureColumn($db, 'notifications', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", 'created_at', 'read_at');
    ensureColumn($db, 'notifications', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", 'updated_at', 'created_at');
    ensureColumn($db, 'notifications', "DATETIME NULL", 'deleted_at', 'updated_at');

    ensureColumn($db, 'activity_logs', "INT UNSIGNED NULL", 'user_id', 'id');
    ensureColumn($db, 'activity_logs', "VARCHAR(100) NOT NULL", 'action', 'user_id');
    ensureColumn($db, 'activity_logs', "VARCHAR(50) NULL", 'entity_type', 'action');
    ensureColumn($db, 'activity_logs', "INT UNSIGNED NULL", 'entity_id', 'entity_type');
    ensureColumn($db, 'activity_logs', "TEXT NOT NULL", 'description', 'entity_id');
    ensureColumn($db, 'activity_logs', "VARCHAR(45) NULL", 'ip_address', 'description');
    ensureColumn($db, 'activity_logs', "VARCHAR(500) NULL", 'user_agent', 'ip_address');
    ensureColumn($db, 'activity_logs', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", 'created_at', 'user_agent');
    ensureColumn($db, 'activity_logs', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", 'updated_at', 'created_at');
    ensureColumn($db, 'activity_logs', "DATETIME NULL", 'deleted_at', 'updated_at');

    ensureColumn($db, 'settings', "VARCHAR(100) NOT NULL", 'setting_key', 'id');
    ensureColumn($db, 'settings', "TEXT NULL", 'setting_value', 'setting_key');
    ensureColumn($db, 'settings', "VARCHAR(50) NOT NULL DEFAULT 'general'", 'setting_group', 'setting_value');
    ensureColumn($db, 'settings', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", 'created_at', 'setting_group');
    ensureColumn($db, 'settings', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", 'updated_at', 'created_at');
    ensureColumn($db, 'settings', "DATETIME NULL", 'deleted_at', 'updated_at');

    ensureColumn($db, 'contact_submissions', "VARCHAR(100) NOT NULL", 'name', 'id');
    ensureColumn($db, 'contact_submissions', "VARCHAR(255) NOT NULL", 'email', 'name');
    ensureColumn($db, 'contact_submissions', "VARCHAR(255) NULL", 'subject', 'email');
    ensureColumn($db, 'contact_submissions', "TEXT NOT NULL", 'message', 'subject');
    ensureColumn($db, 'contact_submissions', "TINYINT(1) NOT NULL DEFAULT 0", 'email_sent', 'message');
    ensureColumn($db, 'contact_submissions', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", 'created_at', 'email_sent');
    ensureColumn($db, 'contact_submissions', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", 'updated_at', 'created_at');
    ensureColumn($db, 'contact_submissions', "DATETIME NULL", 'deleted_at', 'updated_at');

    ensureColumn($db, 'password_resets', "VARCHAR(255) NOT NULL", 'email', 'id');
    ensureColumn($db, 'password_resets', "VARCHAR(255) NOT NULL", 'token', 'email');
    ensureColumn($db, 'password_resets', "DATETIME NOT NULL", 'expires_at', 'token');
    ensureColumn($db, 'password_resets', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", 'created_at', 'expires_at');
    ensureColumn($db, 'password_resets', "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", 'updated_at', 'created_at');
    ensureColumn($db, 'password_resets', "DATETIME NULL", 'deleted_at', 'updated_at');

    ensureIndex($db, 'users', 'idx_users_role_active', '(role, is_active, deleted_at)');
    ensureIndex($db, 'users', 'idx_users_faculty_active', '(faculty_id, role, is_active, deleted_at)');
    ensureIndex($db, 'users', 'idx_users_department_active', '(department_id, role, is_active, deleted_at)');
    ensureIndex($db, 'tickets', 'idx_tickets_user_created', '(user_id, deleted_at, created_at)');
    ensureIndex($db, 'tickets', 'idx_tickets_department_status', '(department_id, status, deleted_at, created_at)');
    ensureIndex($db, 'tickets', 'idx_tickets_assigned_status', '(assigned_to, status, deleted_at, created_at)');
    ensureIndex($db, 'notifications', 'idx_notifications_user_read', '(user_id, is_read, deleted_at, created_at)');
    ensureIndex($db, 'activity_logs', 'idx_activity_user_created', '(user_id, deleted_at, created_at)');
    ensureIndex($db, 'contact_submissions', 'idx_contact_created', '(created_at, deleted_at)');
    ensureIndex($db, 'password_resets', 'idx_password_resets_email', '(email, deleted_at, created_at)');

    $db->exec('SET FOREIGN_KEY_CHECKS = 1');

    $seedTables = [
        'faculties' => [
            ['Faculty of Computer Science', 'FCS', 'Computing, software, and information technology programs', 1],
            ['Faculty of Education', 'FED', 'Teacher education and pedagogical studies', 2],
            ['Faculty of Business & Economics', 'FBE', 'Business administration, economics, and management', 3],
            ['Faculty of Health Science', 'FHS', 'Health sciences and related professional programs', 4],
            ['Faculty of Engineering', 'FEN', 'Engineering disciplines and applied technology', 5],
            ['Faculty of Agriculture', 'FAG', 'Agricultural sciences and rural development', 6],
            ['Faculty of Islamic Studies', 'FIS', 'Islamic studies and related humanities', 7],
            ['Faculty of Social Science', 'FSS', 'Social sciences, humanities, and community studies', 8],
        ],
        'departments' => [
            ['Information & Communication Technology', 'ICT', 'IT support, network, and software issues', 'ict@pust.edu'],
            ['Finance Office', 'FIN', 'Fees, payments, and financial inquiries', 'finance@pust.edu'],
            ['Registrar Office', 'REG', 'Registration, transcripts, and records', 'registrar@pust.edu'],
            ['Library Services', 'LIB', 'Library resources and access', 'library@pust.edu'],
            ['Academic Affairs', 'ACA', 'Academic programs and curriculum', 'academic@pust.edu'],
            ['Administration', 'ADM', 'General administrative matters', 'admin@pust.edu'],
        ],
        'priorities' => [
            ['Low', 'low', '#22c55e', 1],
            ['Medium', 'medium', '#3b82f6', 2],
            ['High', 'high', '#f59e0b', 3],
            ['Urgent', 'urgent', '#ef4444', 4],
        ],
        'categories' => [
            ['Network Issue', 'network-issue', 'Internet and network connectivity', 1],
            ['Software Problem', 'software-problem', 'Application and software errors', 1],
            ['Email & Account', 'email-account', 'University email and login access', 1],
            ['Hardware Support', 'hardware-support', 'Computers, printers, and lab equipment', 1],
            ['Fee Payment', 'fee-payment', 'Tuition and fee related', 2],
            ['Scholarship', 'scholarship', 'Scholarship and financial aid', 2],
            ['Registration', 'registration', 'Course registration issues', 3],
            ['Transcript Request', 'transcript-request', 'Official transcripts and records', 3],
            ['Book Access', 'book-access', 'Library book and resource access', 4],
            ['Digital Resources', 'digital-resources', 'E-books and online library access', 4],
        ],
    ];

    $counts = [
        'faculties' => (int) $db->query('SELECT COUNT(*) FROM faculties')->fetchColumn(),
        'departments' => (int) $db->query('SELECT COUNT(*) FROM departments')->fetchColumn(),
        'priorities' => (int) $db->query('SELECT COUNT(*) FROM priorities')->fetchColumn(),
        'categories' => (int) $db->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
    ];

    if ($counts['faculties'] === 0) {
        $stmt = $db->prepare('INSERT INTO faculties (name, code, description, sort_order) VALUES (?, ?, ?, ?)');
        foreach ($seedTables['faculties'] as $row) {
            $stmt->execute($row);
        }
    }
    if ($counts['departments'] === 0) {
        $stmt = $db->prepare('INSERT INTO departments (name, code, description, email) VALUES (?, ?, ?, ?)');
        foreach ($seedTables['departments'] as $row) {
            $stmt->execute($row);
        }
    }
    if ($counts['priorities'] === 0) {
        $stmt = $db->prepare('INSERT INTO priorities (name, slug, color, sort_order) VALUES (?, ?, ?, ?)');
        foreach ($seedTables['priorities'] as $row) {
            $stmt->execute($row);
        }
    }
    if ($counts['categories'] === 0) {
        $stmt = $db->prepare('INSERT INTO categories (name, slug, description, department_id) VALUES (?, ?, ?, ?)');
        foreach ($seedTables['categories'] as $row) {
            $stmt->execute($row);
        }
    }
}
