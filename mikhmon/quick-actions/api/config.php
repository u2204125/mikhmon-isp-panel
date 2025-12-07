<?php
/**
 * Quick Actions API Configuration
 * Session management and security settings
 */

// Prevent direct access
if (basename($_SERVER['PHP_SELF']) === 'config.php') {
    http_response_code(403);
    exit('Direct access not allowed');
}

// Load .env file
require_once dirname(__DIR__) . '/../lib/env_loader.php';
$envPath = dirname(__DIR__) . '/../../../.env';
loadEnv($envPath);

// API Configuration from environment or defaults
define('QA_SECRET_KEY', getenv('QA_SECRET_KEY') ?: 'quickaction_mikhmon_secure_key_2025');
define('QA_TOKEN_EXPIRY', getenv('QA_TOKEN_EXPIRY') ?: (30 * 24 * 60 * 60)); // 30 days in seconds

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load parent config for router management
$parentConfigPath = dirname(__DIR__) . '/../include/config.php';
if (file_exists($parentConfigPath)) {
    require_once $parentConfigPath;
}

// Load encryption/decryption functions
$routerosApiPath = dirname(__DIR__) . '/../lib/routeros_api.class.php';
if (file_exists($routerosApiPath)) {
    require_once $routerosApiPath;
}

// Polyfill for getallheaders() for environments that don't provide it (e.g., non-Apache)
if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                // Convert HTTP_HEADER_NAME to Header-Name
                $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }
}

/**
 * Generate JWT-like token
 */
function generateToken($username) {
    $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
    $payload = base64_encode(json_encode([
        'username' => $username,
        'exp' => time() + QA_TOKEN_EXPIRY,
        'iat' => time()
    ]));
    
    $signature = hash_hmac('sha256', "$header.$payload", QA_SECRET_KEY);
    
    return "$header.$payload.$signature";
}

/**
 * Verify JWT-like token
 */
function verifyToken($token) {
    if (!$token) {
        return false;
    }
    
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return false;
    }
    
    list($header, $payload, $signature) = $parts;
    
    // Verify signature
    $expectedSignature = hash_hmac('sha256', "$header.$payload", QA_SECRET_KEY);
    if ($signature !== $expectedSignature) {
        return false;
    }
    
    // Decode payload
    $payloadData = json_decode(base64_decode($payload), true);
    
    // Check expiration
    if (!isset($payloadData['exp']) || $payloadData['exp'] < time()) {
        return false;
    }
    
    return $payloadData;
}

/**
 * Get token from Authorization header
 */
function getAuthToken() {
    $headers = getallheaders();
    
    if (isset($headers['Authorization'])) {
        $auth = $headers['Authorization'];
        if (strpos($auth, 'Bearer ') === 0) {
            return substr($auth, 7);
        }
    }
    
    return null;
}

/**
 * Require authentication
 */
function requireAuth() {
    $token = getAuthToken();
    $payload = verifyToken($token);
    
    if (!$payload) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized. Please login again.'
        ]);
        exit();
    }
    
    return $payload;
}

/**
 * Send JSON response
 */
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

/**
 * Send error response
 */
function sendError($message, $statusCode = 400) {
    sendResponse([
        'success' => false,
        'message' => $message
    ], $statusCode);
}

/**
 * Send success response
 */
function sendSuccess($message, $data = null) {
    $response = [
        'success' => true,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    sendResponse($response);
}
?>