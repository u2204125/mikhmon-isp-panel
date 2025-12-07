<?php
/**
 * Routers API Endpoint
 * Handles CRUD operations for routers
 */

require_once __DIR__ . '/config.php';

// Require authentication
$user = requireAuth();

global $data;

// CSV path for routers
$csvPath = dirname(__DIR__) . '/../include/routers.csv';

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
/**
 * CSV helpers
 */
function readRoutersCsv() {
    global $csvPath;
    $routers = [];
    if (!file_exists($csvPath) || !is_readable($csvPath)) return $routers;
    if (($h = fopen($csvPath, 'r')) === false) return $routers;

    $row = 0;
    $headers = null;
    while (($r = fgetcsv($h)) !== false) {
        if ($row === 0) {
            $first = isset($r[0]) ? strtolower(trim($r[0])) : '';
            if (in_array($first, ['session','sessname','id'])) {
                $headers = array_map('strtolower', $r);
                $row++;
                continue;
            }
        }
        if (count($r) === 0) { $row++; continue; }

        $cols = array_map('trim', $r);
        $session = isset($cols[0]) ? $cols[0] : null;
        if (!$session) { $row++; continue; }

        $routers[$session] = [
            'session' => $session,
            'ipmik' => isset($cols[1]) ? $cols[1] : '',
            'usermik' => isset($cols[2]) ? $cols[2] : '',
            'passmik' => isset($cols[3]) ? $cols[3] : '',
            'hotspotname' => isset($cols[4]) ? $cols[4] : '',
            'dnsname' => isset($cols[5]) ? $cols[5] : '',
            'currency' => isset($cols[6]) ? $cols[6] : '',
            'areload' => isset($cols[7]) ? $cols[7] : '',
            'iface' => isset($cols[8]) ? $cols[8] : '',
            'infolp' => isset($cols[9]) ? $cols[9] : '',
            'idleto' => isset($cols[10]) ? $cols[10] : '',
            'livereport' => isset($cols[11]) && $cols[11] !== '' ? $cols[11] : 'disable'
        ];

        $row++;
    }
    fclose($h);
    return $routers;
}

function writeRoutersCsv($routers) {
    global $csvPath;
    $tmp = [];
    // header
    $tmp[] = ['session','ipmik','usermik','passmik','hotspotname','dnsname','currency','areload','iface','infolp','idleto','livereport'];
    foreach ($routers as $s => $r) {
        $tmp[] = [
            $r['session'],
            $r['ipmik'],
            $r['usermik'],
            $r['passmik'],
            $r['hotspotname'],
            $r['dnsname'],
            $r['currency'],
            $r['areload'],
            $r['iface'],
            $r['infolp'],
            $r['idleto'],
            $r['livereport']
        ];
    }

    if (($h = fopen($csvPath, 'w')) === false) return false;
    foreach ($tmp as $row) {
        fputcsv($h, $row);
    }
    fclose($h);
    return true;
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
            // empty(trim(...)) is not allowed on some PHP versions; use explicit check
            if (!isset($input[$field]) || trim($input[$field]) === '') {
                sendError("Field '{$field}' is required");
            }
        }
        
        // Set defaults
        $routerData = [
            'ipmik' => trim($input['ipmik']),
            'usermik' => trim($input['usermik']),
            // store encrypted password so existing code that calls decrypt() works
            'passmik' => encrypt(trim($input['passmik'])),
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
        
        // Check if router already exists (check CSV)
        $routers = readRoutersCsv();
        if (isset($routers[$sessionName]) || isset($data[$sessionName])) {
            sendError('Router with this session name already exists');
        }

        // add and persist
        $routers[$sessionName] = array_merge(['session' => $sessionName], $routerData);
        $routers[$sessionName]['session'] = $sessionName;

        if (writeRoutersCsv($routers)) {
            // update in-memory $data to reflect the new router using legacy format
            $arr = [];
            $arr[1] = $sessionName . '!' . $routerData['ipmik'];
            $arr[2] = $sessionName . '@|@' . $routerData['usermik'];
            $arr[3] = $sessionName . '#|#' . $routerData['passmik'];
            $arr[4] = $sessionName . '%' . $routerData['hotspotname'];
            $arr[5] = $sessionName . '^' . (isset($routerData['dnsname']) ? $routerData['dnsname'] : '');
            $arr[6] = $sessionName . '&' . (isset($routerData['currency']) ? $routerData['currency'] : '');
            $arr[7] = $sessionName . '*' . (isset($routerData['areload']) ? $routerData['areload'] : '');
            $arr[8] = $sessionName . '(' . $routerData['iface'];
            $arr[9] = $sessionName . ')' . (isset($routerData['infolp']) ? $routerData['infolp'] : '');
            $arr[10] = $sessionName . '=' . (isset($routerData['idleto']) ? $routerData['idleto'] : '');
            $arr[11] = $sessionName . '@!@' . (isset($routerData['livereport']) ? $routerData['livereport'] : 'disable');

            $data[$sessionName] = $arr;

            sendSuccess('Router added successfully', parseRouter($sessionName, $data[$sessionName]));
        } else {
            sendError('Failed to save router', 500);
        }
        break;
        
    default:
        sendError('Method not allowed', 405);
}
?>