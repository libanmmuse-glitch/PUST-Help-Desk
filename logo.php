<?php
/**
 * Serve transparent PUST logo (regenerates cache when source changes).
 */
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/functions/logo-image.php';

$sourceCandidates = [
    ROOT_PATH . '/assets/images/pust-logo-official.jpg',
    ROOT_PATH . '/assets/images/pust-logo-source.png',
    ROOT_PATH . '/assets/images/pust-logo.png',
];
$source = null;
foreach ($sourceCandidates as $candidate) {
    if (is_file($candidate)) {
        $source = $candidate;
        break;
    }
}
$output = ROOT_PATH . '/assets/images/pust-logo.png';

if (!$source || !is_file($source)) {
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
