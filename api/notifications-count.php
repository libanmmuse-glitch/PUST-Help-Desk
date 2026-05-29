<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    jsonResponse(['count' => 0]);
}

jsonResponse(['count' => countUnreadNotifications(userId())]);
