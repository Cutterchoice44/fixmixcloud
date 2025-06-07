<?php
header('Content-Type: application/json');

$artistId = isset($_GET['artistId'])
    ? preg_replace('/[^a-zA-Z0-9_-]/','',$_GET['artistId'])
    : '';
$dataFile = __DIR__ . ($artistId ? "/archives_{$artistId}.json" : '/archives.json');
if (!file_exists($dataFile)) {
    echo json_encode([]);
    exit;
}
echo file_get_contents($dataFile);
