-- PUST Help Desk App - Database Schema
-- Database: pust_helpdesk

CREATE DATABASE IF NOT EXISTS pust_helpdesk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pust_helpdesk;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS attachments;
DROP TABLE IF EXISTS ticket_replies;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS contact_submissions;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS priorities;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS faculties;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- Users
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    role ENUM('student', 'staff', 'admin') NOT NULL DEFAULT 'student',
    staff_category VARCHAR(30) NULL,
    faculty_id INT UNSIGNED NULL,
    department_id INT UNSIGNED NULL,
    avatar VARCHAR(255) DEFAULT 'default-avatar.png',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    email_verified_at DATETIME NULL,
    remember_token VARCHAR(100) NULL,
    last_login_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role (role),
    INDEX idx_users_faculty (faculty_id),
    INDEX idx_users_department (department_id)
) ENGINE=InnoDB;

-- Faculties (academic — student registration)
CREATE TABLE faculties (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Departments
CREATE TABLE departments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT NULL,
    email VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE users ADD CONSTRAINT fk_users_faculty FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE SET NULL;
ALTER TABLE users ADD CONSTRAINT fk_users_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL;

-- Priorities
CREATE TABLE priorities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    color VARCHAR(20) NOT NULL DEFAULT '#6b7280',
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Categories
CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    department_id INT UNSIGNED NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tickets
CREATE TABLE tickets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_number VARCHAR(20) NOT NULL UNIQUE,
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE RESTRICT,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (priority_id) REFERENCES priorities(id) ON DELETE RESTRICT,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_tickets_status (status),
    INDEX idx_tickets_user (user_id),
    INDEX idx_tickets_department (department_id),
    INDEX idx_tickets_assigned (assigned_to)
) ENGINE=InnoDB;

-- Ticket Replies
CREATE TABLE ticket_replies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    is_internal TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_replies_ticket (ticket_id)
) ENGINE=InnoDB;

-- Attachments
CREATE TABLE attachments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT UNSIGNED NULL,
    reply_id INT UNSIGNED NULL,
    user_id INT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (reply_id) REFERENCES ticket_replies(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Notifications
CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(500) NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notifications_user (user_id),
    INDEX idx_notifications_read (is_read)
) ENGINE=InnoDB;

-- Activity Logs
CREATE TABLE activity_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NULL,
    entity_id INT UNSIGNED NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_activity_user (user_id),
    INDEX idx_activity_created (created_at)
) ENGINE=InnoDB;

-- Settings
CREATE TABLE settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    setting_group VARCHAR(50) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Contact Submissions
CREATE TABLE contact_submissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NULL,
    message TEXT NOT NULL,
    email_sent TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contact_created (created_at)
) ENGINE=InnoDB;

-- Password Resets
CREATE TABLE password_resets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_password_resets_email (email),
    INDEX idx_password_resets_token (token)
) ENGINE=InnoDB;

-- Faculties
INSERT INTO faculties (name, code, description, sort_order) VALUES
('Faculty of Computer Science', 'FCS', 'Computing, software, and information technology programs', 1),
('Faculty of Education', 'FED', 'Teacher education and pedagogical studies', 2),
('Faculty of Business & Economics', 'FBE', 'Business administration, economics, and management', 3),
('Faculty of Health Science', 'FHS', 'Health sciences and related professional programs', 4),
('Faculty of Engineering', 'FEN', 'Engineering disciplines and applied technology', 5),
('Faculty of Agriculture', 'FAG', 'Agricultural sciences and rural development', 6),
('Faculty of Islamic Studies', 'FIS', 'Islamic studies and related humanities', 7),
('Faculty of Social Science', 'FSS', 'Social sciences, humanities, and community studies', 8);

-- Sample Data: Departments
INSERT INTO departments (name, code, description, email) VALUES
('Information & Communication Technology', 'ICT', 'IT support, network, and software issues', 'ict@pust.edu'),
('Finance Office', 'FIN', 'Fees, payments, and financial inquiries', 'finance@pust.edu'),
('Registrar Office', 'REG', 'Registration, transcripts, and records', 'registrar@pust.edu'),
('Library Services', 'LIB', 'Library resources and access', 'library@pust.edu'),
('Academic Affairs', 'ACA', 'Academic programs and curriculum', 'academic@pust.edu'),
('Administration', 'ADM', 'General administrative matters', 'admin@pust.edu');

-- Priorities
INSERT INTO priorities (name, slug, color, sort_order) VALUES
('Low', 'low', '#22c55e', 1),
('Medium', 'medium', '#3b82f6', 2),
('High', 'high', '#f59e0b', 3),
('Urgent', 'urgent', '#ef4444', 4);

-- Categories
INSERT INTO categories (name, slug, description, department_id) VALUES
('Network Issue', 'network-issue', 'Internet and network connectivity', 1),
('Software Problem', 'software-problem', 'Application and software errors', 1),
('Email & Account', 'email-account', 'University email and login access', 1),
('Hardware Support', 'hardware-support', 'Computers, printers, and lab equipment', 1),
('Fee Payment', 'fee-payment', 'Tuition and fee related', 2),
('Scholarship', 'scholarship', 'Scholarship and financial aid', 2),
('Registration', 'registration', 'Course registration issues', 3),
('Transcript Request', 'transcript-request', 'Official transcripts and records', 3),
('Book Access', 'book-access', 'Library book and resource access', 4),
('Digital Resources', 'digital-resources', 'E-books and online library access', 4),
('Grade Inquiry', 'grade-inquiry', 'Academic grade questions', 5),
('Course Enrollment', 'course-enrollment', 'Add/drop courses and schedules', 5),
('General Inquiry', 'general-inquiry', 'General administrative questions', 6),
('ID Card', 'id-card', 'Student and staff ID cards', 6);

-- Users (default password hash is for: password)
INSERT INTO users (student_id, first_name, last_name, email, password, phone, role, department_id, avatar) VALUES
(NULL, 'System', 'Administrator', 'admin@pust.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0770000001', 'admin', 6, 'default-avatar.png');

-- Settings
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('site_name', 'PUST Help Desk App', 'general'),
('site_email', 'support@pust.edu.so', 'general'),
('contact_email', 'support@pust.edu.so', 'general'),
('mail_from', 'noreply@pust.edu.so', 'general'),
('tickets_per_page', '10', 'general'),
('allow_registration', '1', 'auth'),
('allow_staff_registration', '1', 'auth'),
('max_upload_size', '5242880', 'upload'),
('allowed_file_types', 'jpg,jpeg,png,pdf,doc,docx', 'upload');

-- Sample notification for admin (optional)
INSERT INTO notifications (user_id, type, title, message, link, is_read) VALUES
(1, 'system', 'Welcome', 'PUST Help Desk is ready. Register students and staff to get started.', '/admin/dashboard.php', 0);
