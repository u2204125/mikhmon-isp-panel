<?php
/**
 * API Directory Index
 * Prevents directory listing and provides API information
 */

http_response_code(403);
header('Content-Type: application/json');

echo json_encode([
    'error' => 'Direct access not allowed',
    'message' => 'Please use the appropriate API endpoints',
    'endpoints' => [
        'POST /api/login.php' => 'User authentication',
        'GET /api/routers.php' => 'List all routers (requires auth)',
        'POST /api/routers.php' => 'Add new router (requires auth)',
        'PUT /api/routers.php' => 'Update router (requires auth)',
        'DELETE /api/routers.php' => 'Delete router (requires auth)'
    ]
]);
?>