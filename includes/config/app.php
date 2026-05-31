<?php
/**
 * Application configuration
 */

require_once __DIR__ . '/brand.php';

define('APP_NAME', 'PUST Help Desk App');
define('APP_VERSION', '1.0.0');

define('ROOT_PATH', dirname(dirname(__DIR__)));
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads');

$appHost = $_SERVER['HTTP_HOST'] ?? '';
$isProductionHost = is_string($appHost) && (
    str_contains($appHost, 'infinityfreeapp.com') ||
    str_contains($appHost, 'ftpupload.net')
);

define('APP_URL', $isProductionHost
    ? 'http://pust-helpdesk.infinityfreeapp.com'
    : 'http://localhost/Help-Desk-App'
);

define('UPLOAD_URL', APP_URL . '/assets/uploads');

define('SESSION_LIFETIME', 7200); // 2 hours
define('REMEMBER_DAYS', 30);
define('CSRF_TOKEN_NAME', '_csrf_token');

if (is_file(__DIR__ . '/mail.local.php')) {
    require_once __DIR__ . '/mail.local.php';
}

if (!defined('CONTACT_FORM_SEND_EMAIL')) define('CONTACT_FORM_SEND_EMAIL', false);
if (!defined('CONTACT_FORM_EMAIL')) define('CONTACT_FORM_EMAIL', 'support@pust.edu.so');
if (!defined('MAIL_FROM_EMAIL')) define('MAIL_FROM_EMAIL', 'noreply@pust.edu.so');

date_default_timezone_set('Africa/Kampala');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
