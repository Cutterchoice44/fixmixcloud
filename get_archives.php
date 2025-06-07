<?php
// Prevent any caching
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

header('Content-Type: application/json');

$artistId = filter_input(INPUT_GET,'artistId',FILTER_SANITIZE_STRING);
$artistId = $artistId ? preg_replace('/[^a-zA-Z0-9_-]/','',$artistId) : '';
$dataFile = __DIR__ . ($artistId
    ? "/archives_{$artistId}.json"
    : '/archives.json');

if (!file_exists($dataFile)) {
    echo json_encode([]);
    exit;
}

echo file_get_contents($dataFile);
