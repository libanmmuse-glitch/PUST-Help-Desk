<?php
/**
 * Authentication middleware
 */

function requireAuth(): void
{
    if (!isLoggedIn()) {
        flash('error', 'Please log in to continue.');
        redirect(appUrl('login.php'));
    }
}

function requireRole(array $roles): void
{
    requireAuth();
    if (!in_array(userRole(), $roles, true)) {
        http_response_code(403);
        flash('error', 'You do not have permission to access this page.');
        $dashboard = match (userRole()) {
            'admin'  => 'admin/dashboard.php',
            'staff'  => 'staff/dashboard.php',
            default  => 'student/dashboard.php',
        };
        redirect(appUrl($dashboard));
    }
}

function guestOnly(): void
{
    if (isLoggedIn()) {
        $dashboard = match (userRole()) {
            'admin'  => 'admin/dashboard.php',
            'staff'  => 'staff/dashboard.php',
            default  => 'student/dashboard.php',
        };
        redirect(appUrl($dashboard));
    }
}

function redirectToDashboard(): void
{
    if (!isLoggedIn()) {
        redirect(appUrl('login.php'));
    }
    $dashboard = match (userRole()) {
        'admin'  => 'admin/dashboard.php',
        'staff'  => 'staff/dashboard.php',
        default  => 'student/dashboard.php',
    };
    redirect(appUrl($dashboard));
}
