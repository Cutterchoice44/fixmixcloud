<?php
header('Content-Type: application/json');
$url = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL);
if (!$url) {
    http_response_code(400);
    echo json_encode(['error'=>'Invalid URL']);
    exit;
}
$dataFile = __DIR__ . '/archives.json';
$archives = is_readable($dataFile)
    ? json_decode(file_get_contents($dataFile), true)
    : [];
$archives[] = ['url'=>$url, 'added'=>date('c')];
file_put_contents($dataFile, json_encode($archives, JSON_PRETTY_PRINT));
echo json_encode($archives);
?>