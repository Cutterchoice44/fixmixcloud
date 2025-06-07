<?php
header('Content-Type: application/json');
const MIXCLOUD_PASSWORD = 'cutters44';

// Determine which data file: per‑DJ or global
$artistId = filter_input(INPUT_POST,'artistId',FILTER_SANITIZE_STRING);
$artistId = $artistId ? preg_replace('/[^a-zA-Z0-9_-]/','',$artistId) : '';
$dataFile = __DIR__ . ($artistId ? "/archives_{$artistId}.json" : '/archives.json');

// For per‑DJ saves, enforce password
if ($artistId) {
  $pwd = filter_input(INPUT_POST,'password',FILTER_SANITIZE_STRING);
  if ($pwd !== MIXCLOUD_PASSWORD) {
    http_response_code(403);
    echo json_encode(['error'=>'Forbidden']);
    exit;
  }
}

// Validate URL
$url = filter_input(INPUT_POST,'url',FILTER_VALIDATE_URL);
if (!$url) {
  http_response_code(400);
  echo json_encode(['error'=>'Invalid URL']);
  exit;
}

// Load, append, save
$archives = is_readable($dataFile)
    ? json_decode(file_get_contents($dataFile), true)
    : [];
$archives[] = ['url'=>$url,'added'=>date('c')];
file_put_contents($dataFile, json_encode($archives, JSON_PRETTY_PRINT));

echo json_encode($archives);
