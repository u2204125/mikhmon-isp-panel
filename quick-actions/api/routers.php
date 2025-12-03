<?php
/**
 * Routers API Endpoint
 * Handles CRUD operations for routers
 */

require_once __DIR__ . '/config.php';

// Require authentication
$user = requireAuth();

global $data;

// Get config file path
$configPath = dirname(__DIR__, 2) . '/mikhmon/include/config.php';

/**
 * Parse all routers from config
 */
function getAllRouters() {
    global $data;
    
    $routers = [];
    
    if (!isset($data) || !is_array($data)) {
        return $routers;
    }
    
    foreach ($data as $key => $value) {
        if ($key === 'mikhmon' || !is_array($value)) {
            continue;
        }
        
        $routers[] = parseRouter($key, $value);
    }
    
    return $routers;
}

/**
 * Parse router data
 */
function parseRouter($sessionName, $routerData) {
    return [
        'id' => $sessionName,
        'sessname' => $sessionName,
        'ipmik' => isset($routerData[1]) ? explode('!', $routerData[1])[1] : '',
        'usermik' => isset($routerData[2]) ? explode('@|@', $routerData[2])[1] : '',
        'passmik' => isset($routerData[3]) ? explode('#|#', $routerData[3])[1] : '',
        'hotspotname' => isset($routerData[4]) ? explode('%', $routerData[4])[1] : '',
        'dnsname' => isset($routerData[5]) ? explode('^', $routerData[5])[1] : '',
        'currency' => isset($routerData[6]) ? explode('&', $routerData[6])[1] : '',
        'areload' => isset($routerData[7]) ? explode('*', $routerData[7])[1] : '',
        'iface' => isset($routerData[8]) ? explode('(', $routerData[8])[1] : '',
        'infolp' => isset($routerData[9]) ? explode(')', $routerData[9])[1] : '',
        'idleto' => isset($routerData[10]) ? explode('=', $routerData[10])[1] : '',
        'livereport' => isset($routerData[11]) ? explode('@!@', $routerData[11])[1] : 'disable',
        'status' => 'active' // Can be enhanced with actual connectivity check
    ];
}

/**
 * Format router data for config file
 */
function formatRouterForConfig($sessionName, $routerData) {
    $formatted = [];
    
    $formatted[] = "'1'=>'{$sessionName}!{$routerData['ipmik']}'";
    $formatted[] = "'{$sessionName}@|@{$routerData['usermik']}'";
    $formatted[] = "'{$sessionName}#|#{$routerData['passmik']}'";
    $formatted[] = "'{$sessionName}%{$routerData['hotspotname']}'";
    $formatted[] = "'{$sessionName}^{$routerData['dnsname']}'";
    $formatted[] = "'{$sessionName}&{$routerData['currency']}'";
    $formatted[] = "'{$sessionName}*{$routerData['areload']}'";
    $formatted[] = "'{$sessionName}({$routerData['iface']}'";
    $formatted[] = "'{$sessionName}){$routerData['infolp']}'";
    $formatted[] = "'{$sessionName}={$routerData['idleto']}'";
    $formatted[] = "'{$sessionName}@!@{$routerData['livereport']}'";
    
    return implode(',', $formatted);
}

/**
 * Save router to config file
 */
function saveRouterToConfig($sessionName, $routerData) {
    global $configPath;
    
    if (!file_exists($configPath)) {
        return false;
    }
    
    $configContent = file_get_contents($configPath);
    
    // Format router array
    $routerArray = formatRouterForConfig($sessionName, $routerData);
    $newRouterLine = "\$data['{$sessionName}'] = array ({$routerArray});\n";
    
    // Find the position to insert (before the closing PHP tag)
    $insertPos = strrpos($configContent, '?>');
    
    if ($insertPos === false) {
        // No closing tag, append at the end
        $configContent .= "\n" . $newRouterLine;
    } else {
        // Insert before closing tag
        $configContent = substr_replace($configContent, $newRouterLine, $insertPos, 0);
    }
    
    return file_put_contents($configPath, $configContent) !== false;
}

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // List all routers
        $routers = getAllRouters();
        sendSuccess('Routers retrieved successfully', $routers);
        break;
        
    case 'POST':
        // Add new router
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['sessname', 'ipmik', 'usermik', 'passmik', 'hotspotname', 'iface'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || empty(trim($input[$field]))) {
                sendError("Field '{$field}' is required");
            }
        }
        
        // Set defaults
        $routerData = [
            'ipmik' => trim($input['ipmik']),
            'usermik' => trim($input['usermik']),
            'passmik' => trim($input['passmik']),
            'hotspotname' => trim($input['hotspotname']),
            'dnsname' => isset($input['dnsname']) ? trim($input['dnsname']) : '',
            'currency' => isset($input['currency']) ? trim($input['currency']) : 'USD',
            'areload' => isset($input['areload']) ? trim($input['areload']) : '5',
            'iface' => trim($input['iface']),
            'infolp' => isset($input['infolp']) ? trim($input['infolp']) : '',
            'idleto' => isset($input['idleto']) ? trim($input['idleto']) : '0',
            'livereport' => isset($input['livereport']) && $input['livereport'] ? 'enable' : 'disable'
        ];
        
        $sessionName = trim($input['sessname']);
        
        // Check if router already exists
        if (isset($data[$sessionName])) {
            sendError('Router with this session name already exists');
        }
        
        // Save to config
        if (saveRouterToConfig($sessionName, $routerData)) {
            sendSuccess('Router added successfully', parseRouter($sessionName, $routerData));
        } else {
            sendError('Failed to save router', 500);
        }
        break;
        
    default:
        sendError('Method not allowed', 405);
}
?>