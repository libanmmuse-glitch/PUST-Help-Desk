<?php
/**
 * Serve transparent PUST logo (regenerates cache when source changes).
 */
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/functions/logo-image.php';

$source = ROOT_PATH . '/assets/images/pust-logo-source.png';
if (!is_file($source)) {
    $source = ROOT_PATH . '/assets/images/pust-logo.png';
}
$output = ROOT_PATH . '/assets/images/pust-logo.png';

if (!is_file($source)) {
    http_response_code(404);
    exit;
}

if (!is_file($output) || filemtime($output) < filemtime($source)) {
    cleanPustLogoBackground($source, $output);
}

header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');
readfile(is_file($output) ? $output : $source);
exit;
