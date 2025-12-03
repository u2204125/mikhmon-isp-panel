<?php
/**
 * Login API Endpoint
 * Handles user authentication
 */

require_once __DIR__ . '/config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['username']) || !isset($input['password'])) {
    sendError('Username and password are required');
}

$username = trim($input['username']);
$password = trim($input['password']);

// Validate credentials against parent config
global $data;

if (!isset($data['mikhmon'])) {
    sendError('Configuration not found', 500);
}

// Extract admin credentials
$useradm = explode('<|<', $data['mikhmon'][1])[1];
$passadm = explode('>|>', $data['mikhmon'][2])[1];

// Decrypt password and compare
$decryptedPass = decrypt($passadm);

if ($username === $useradm && $password === $decryptedPass) {
    // Generate token
    $token = generateToken($username);
    
    sendSuccess('Login successful', [
        'token' => $token,
        'username' => $username,
        'expiresIn' => QA_TOKEN_EXPIRY
    ]);
} else {
    sendError('Invalid username or password', 401);
}
?>