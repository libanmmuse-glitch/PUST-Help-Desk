<?php
/**
 * Application bootstrap - load all dependencies
 */

require_once __DIR__ . '/config/app.php';
if (is_file(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';
require_once __DIR__ . '/functions/security.php';
require_once __DIR__ . '/functions/auth.php';
require_once __DIR__ . '/functions/tickets.php';
require_once __DIR__ . '/functions/notifications.php';
require_once __DIR__ . '/functions/status.php';
require_once __DIR__ . '/functions/avatar.php';
require_once __DIR__ . '/functions/logo-image.php';
require_once __DIR__ . '/functions/faculties.php';
require_once __DIR__ . '/functions/mail.php';

initSession();
checkRememberMe();

// Session timeout
if (isLoggedIn() && isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > SESSION_LIFETIME) {
        logoutUser();
        redirect(appUrl('login.php?timeout=1'));
    }
    $_SESSION['last_activity'] = time();
}
