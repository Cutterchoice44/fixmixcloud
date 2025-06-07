<?php
header('Content-Type: application/json');

const MIXCLOUD_PASSWORD = 'cutters44';
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
if ($password !== MIXCLOUD_PASSWORD) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$index = filter_input(INPUT_POST, 'index', FILTER_VALIDATE_INT);
$dataFile = __DIR__ . '/archives.json';

// Load existing archives
$archives = is_readable($dataFile)
    ? json_decode(file_get_contents($dataFile), true)
    : [];

// Validate index
if ($index === null || !isset($archives[$index])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid index']);
    exit;
}

// Remove the entry and save
array_splice($archives, $index, 1);
file_put_contents($dataFile, json_encode($archives, JSON_PRETTY_PRINT));

// Return updated list
echo json_encode($archives);
