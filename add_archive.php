<?php
// Prevent caching
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

header('Content-Type: application/json');

const MIXCLOUD_PASSWORD = 'cutters44';

$artistId = filter_input(INPUT_POST,'artistId',FILTER_SANITIZE_STRING);
$artistId = $artistId ? preg_replace('/[^a-zA-Z0-9_-]/','',$artistId) : '';

if ($artistId) {
    $pwd = filter_input(INPUT_POST,'password',FILTER_SANITIZE_STRING);
    if ($pwd !== MIXCLOUD_PASSWORD) {
        http_response_code(403);
        echo json_encode(['error'=>'Forbidden']);
        exit;
    }
}

$url = filter_input(INPUT_POST,'url',FILTER_VALIDATE_URL);
if (!$url) {
    http_response_code(400);
    echo json_encode(['error'=>'Invalid URL']);
    exit;
}

$dataFile = __DIR__ . ($artistId
    ? "/archives_{$artistId}.json"
    : '/archives.json');

$archives = is_readable($dataFile)
    ? json_decode(file_get_contents($dataFile), true)
    : [];

$archives[] = ['url'=>$url,'added'=>date('c')];
file_put_contents($dataFile, json_encode($archives, JSON_PRETTY_PRINT));

echo json_encode($archives);
