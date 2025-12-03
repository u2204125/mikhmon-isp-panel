<?php
// Mock quick-action endpoint that simulates using the test credential.
// Returns JSON describing the (simulated) result. Does NOT make external connections.

$dataFile = __DIR__ . '/test-credential-data.php';
if (!file_exists($dataFile)) {
    header('Content-Type: application/json; charset=utf-8', true, 500);
    echo json_encode(['ok' => false, 'error' => 'Data file missing.']);
    exit;
}

$data = include $dataFile;

$masked = $data;
$masked['password'] = str_repeat('*', max(4, strlen($masked['password'])));

$response = [
    'ok' => true,
    'action' => 'mock_connect',
    'used_credential' => $masked,
    'result' => 'Simulated quick action executed successfully. (No external connections made)'
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_PRETTY_PRINT);
?>