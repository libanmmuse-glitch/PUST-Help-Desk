<?php
/**
 * Database configuration - PDO connection
 */

define('DB_CHARSET', 'utf8mb4');

$dbHost = 'localhost';
$dbName = 'pust_helpdesk';
$dbUser = 'root';
$dbPass = '';

$dbHostName = $_SERVER['HTTP_HOST'] ?? '';
$isProductionHost = is_string($dbHostName) && (
    str_contains($dbHostName, 'infinityfreeapp.com') ||
    str_contains($dbHostName, 'ftpupload.net')
);

if ($isProductionHost) {
    $dbHost = 'sql211.infinityfree.com';
    $dbName = 'if0_42050846_pust_helpdesk';
    $dbUser = 'if0_42050846';
    $dbPass = 'Bootaan12345';
}

define('DB_HOST', $dbHost);
define('DB_NAME', $dbName);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}
