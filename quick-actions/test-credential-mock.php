<?php
// JSON endpoint wrapper around test credential data.
// Usage: fetch('test-credential-mock.php')
// Optional: add ?reveal=1 to show full password, allowed only from localhost for safety.

$dataFile = __DIR__ . '/test-credential-data.php';
if (!file_exists($dataFile)) {
    header('Content-Type: application/json; charset=utf-8', true, 500);
    echo json_encode(['ok' => false, 'error' => 'Data file missing.']);
    exit;
}

$data = include $dataFile;

// Safety: only reveal the actual password if requested from localhost.
$allowReveal = (isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1'));
$reveal = (isset($_GET['reveal']) && $_GET['reveal'] === '1' && $allowReveal);

$export = $data;
if (!$reveal) {
    $export['password'] = '****';
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['ok' => true, 'credential' => $export], JSON_PRETTY_PRINT);
?>