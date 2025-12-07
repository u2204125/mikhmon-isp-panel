<?php 
if(substr($_SERVER["REQUEST_URI"], -10) == "config.php"){header("Location:./");};


// Load .env file
require_once dirname(__DIR__) . '/lib/env_loader.php';
$envPath = dirname(__DIR__) . '/../.env';
loadEnv($envPath);

$username = getenv('DEFAULT_ADMIN_USERNAME') ?: 'marzan';
$password = getenv('DEFAULT_ADMIN_PASSWORD') ?: '11121112';

# Encrypt the password
require_once dirname(__DIR__) . '/lib/routeros_api.class.php';
$password = encrypt($password);

// Keep admin credentials in config (used by login routines)
$data['mikhmon'] = array ('1'=>"mikhmon<|<$username","mikhmon>|>$password");

// Routers are stored in CSV (one row per router). This file will be read and
// converted into the legacy $data[$session][n] structure so the rest of the
// application does not need to change.
$routersCsv = __DIR__ . '/routers.csv';

/* Expected CSV columns (header is optional but recommended):
   session,ipmik,usermik,passmik,hotspotname,dnsname,currency,areload,iface,infolp,idleto,livereport
*/

if (file_exists($routersCsv) && is_readable($routersCsv)) {
	if (($handle = fopen($routersCsv, 'r')) !== false) {
		$row = 0;
		$headers = [];
		while (($dataCsv = fgetcsv($handle)) !== false) {
			// skip empty lines
			if (count($dataCsv) === 0) continue;
			// detect header row by common header names
			if ($row === 0) {
				$first = isset($dataCsv[0]) ? strtolower(trim($dataCsv[0])) : '';
				if (in_array($first, ['session','sessname','id'])) {
					$headers = array_map('strtolower', $dataCsv);
					$row++;
					continue; // skip header
				}
			}

			// normalize columns to expected indexes
			$cols = array_map(function($v){ return trim($v); }, $dataCsv);

			// map by position
			$session = isset($cols[0]) && $cols[0] !== '' ? $cols[0] : null;
			if (!$session) { $row++; continue; }

			$ipmik = isset($cols[1]) ? $cols[1] : '';
			$usermik = isset($cols[2]) ? $cols[2] : '';
			$passmik = isset($cols[3]) ? $cols[3] : '';
			$hotspotname = isset($cols[4]) ? $cols[4] : '';
			$dnsname = isset($cols[5]) ? $cols[5] : '';
			$currency = isset($cols[6]) ? $cols[6] : '';
			$areload = isset($cols[7]) ? $cols[7] : '';
			$iface = isset($cols[8]) ? $cols[8] : '';
			$infolp = isset($cols[9]) ? $cols[9] : '';
			$idleto = isset($cols[10]) ? $cols[10] : '';
			$livereport = isset($cols[11]) ? $cols[11] : 'disable';

			// keep the legacy string format used elsewhere in the app so no other
			// file changes are required.
			$arr = [];
			$arr[1] = $session . '!' . $ipmik;
			$arr[2] = $session . '@|@' . $usermik;
			$arr[3] = $session . '#|#' . $passmik;
			$arr[4] = $session . '%' . $hotspotname;
			$arr[5] = $session . '^' . $dnsname;
			$arr[6] = $session . '&' . $currency;
			$arr[7] = $session . '*' . $areload;
			$arr[8] = $session . '(' . $iface;
			$arr[9] = $session . ')' . $infolp;
			$arr[10] = $session . '=' . $idleto;
			$arr[11] = $session . '@!@' . $livereport;

			$data[$session] = $arr;

			$row++;
		}
		fclose($handle);
	}
}
