<?php
header('Content-Type: application/json');

const MIXCLOUD_PASSWORD = 'cutters44';
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
if ($password !== MIXCLOUD_PASSWORD) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$url = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL);
$index = filter_input(INPUT_POST, 'index', FILTER_VALIDATE_INT);
$artistId = filter_input(INPUT_POST, 'artistId', FILTER_SANITIZE_STRING);
$artistId = $artistId ? preg_replace('/[^a-zA-Z0-9_-]/', '', $artistId) : '';
$dataFile = __DIR__ . ($artistId ? "/archives_{$artistId}.json" : '/archives.json');

$archives = is_readable($dataFile)
    ? json_decode(file_get_contents($dataFile), true)
    : [];

// Delete by URL for DJ pages
if ($artistId && $url) {
    $archives = array_filter($archives, fn($a) => $a['url'] !== $url);
}
// Delete by index for main page
elseif ($index !== null) {
    if (!isset($archives[$index])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid index']);
        exit;
    }
    array_splice($archives, $index, 1);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

file_put_contents($dataFile, json_encode(array_values($archives), JSON_PRETTY_PRINT));
echo json_encode(array_values($archives));
