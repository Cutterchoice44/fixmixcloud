<?php
header('Content-Type: application/json');
const MIXCLOUD_PASSWORD = 'cutters44';

// Must have password for any deletion
$pwd = filter_input(INPUT_POST,'password',FILTER_SANITIZE_STRING);
if ($pwd !== MIXCLOUD_PASSWORD) {
  http_response_code(403);
  echo json_encode(['error'=>'Forbidden']);
  exit;
}

// Which file
$artistId = filter_input(INPUT_POST,'artistId',FILTER_SANITIZE_STRING);
$artistId = $artistId ? preg_replace('/[^a-zA-Z0-9_-]/','',$artistId) : '';
$dataFile = __DIR__ . ($artistId ? "/archives_{$artistId}.json" : '/archives.json');

// Get index
$index = filter_input(INPUT_POST,'index',FILTER_VALIDATE_INT);
if ($index===null || !is_readable($dataFile)) {
  http_response_code(400);
  echo json_encode(['error'=>'Invalid parameters']);
  exit;
}

$archives = json_decode(file_get_contents($dataFile), true);
if (!isset($archives[$index])) {
  http_response_code(400);
  echo json_encode(['error'=>'Invalid index']);
  exit;
}

// Remove and save
array_splice($archives, $index, 1);
file_put_contents($dataFile, json_encode($archives, JSON_PRETTY_PRINT));

echo json_encode(array_values($archives));
