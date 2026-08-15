<?php
/**
 * Push local kxznassm_armorfire_crm -> live via data_sync.php
 * Live structure is not altered (common columns only).
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '1024M');
set_time_limit(0);

$host = '127.0.0.1';
$port = 3307;
$user = 'root';
$pass = '';
$localDb = 'kxznassm_armorfire_crm';
$liveUrl = 'https://armor-crm.oceanhub.co.in/data_sync.php?key=armor_cp_sync_2026';
$batch = 80;
$logFile = __DIR__ . '/_push_live_log.txt';

$mergeTables = array(
	'dealer_distributor_network' => 1,
	'page_admin_right' => 1,
	'page_table' => 1,
	'api_table' => 1,
);
$skipTables = array(
	'licence_key' => 1,
);

$conn = @mysqli_connect($host, $user, $pass, $localDb, $port);
if (!$conn) {
	fwrite(STDERR, "Local DB connect failed: " . mysqli_connect_error() . PHP_EOL);
	exit(1);
}
mysqli_set_charset($conn, 'utf8');

function logmsg($msg)
{
	global $logFile;
	$line = date('H:i:s') . ' ' . $msg . "\n";
	echo $line;
	file_put_contents($logFile, $line, FILE_APPEND);
}

function live_post($url, $payload)
{
	$json = json_encode($payload);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 180);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$resp = curl_exec($ch);
	$err = curl_error($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($resp === false) {
		return array('ok' => 0, 'error' => $err, 'http' => $code);
	}
	$data = json_decode($resp, true);
	if (!is_array($data)) {
		return array('ok' => 0, 'error' => 'bad json http=' . $code . ' body=' . substr($resp, 0, 300), 'http' => $code);
	}
	$data['http'] = $code;
	return $data;
}

function tableCount($conn, $table)
{
	$res = mysqli_query($conn, "SELECT COUNT(*) c FROM `{$table}`");
	$row = mysqli_fetch_assoc($res);
	return (int) $row['c'];
}

file_put_contents($logFile, "LIVE PUSH START " . date('Y-m-d H:i:s') . "\n");

$ping = live_post($liveUrl, array('action' => 'ping'));
if (empty($ping['ok'])) {
	logmsg('LIVE PING FAIL: ' . json_encode($ping));
	fwrite(STDERR, "Upload data_sync.php to live first.\n");
	exit(2);
}
logmsg('LIVE PING OK db=' . $ping['db'] . ' tables=' . $ping['tables']);

$res = mysqli_query($conn, "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='{$localDb}' AND TABLE_TYPE='BASE TABLE' ORDER BY TABLE_NAME");
$tables = array();
while ($row = mysqli_fetch_assoc($res)) {
	$tables[] = $row['TABLE_NAME'];
}

foreach ($tables as $table) {
	if (isset($skipTables[$table])) {
		logmsg("SKIP {$table} (keep live)");
		continue;
	}
	$localN = tableCount($conn, $table);
	if ($localN === 0) {
		logmsg("SKIP {$table} local empty (keep live rows)");
		continue;
	}

	$mode = isset($mergeTables[$table]) ? 'merge' : 'insert';
	logmsg("START {$table} local={$localN} mode={$mode}");

	if ($mode === 'insert') {
		$tr = live_post($liveUrl, array('action' => 'truncate', 'table' => $table));
		if (empty($tr['ok'])) {
			logmsg("TRUNCATE FAIL {$table}: " . json_encode($tr));
			continue;
		}
	}

	$offset = 0;
	$sent = 0;
	while ($offset < $localN) {
		$q = mysqli_query($conn, "SELECT * FROM `{$table}` LIMIT {$offset}, {$batch}");
		if ($q === false) {
			logmsg("SELECT FAIL {$table}: " . mysqli_error($conn));
			break;
		}
		$rows = array();
		while ($r = mysqli_fetch_assoc($q)) {
			$rows[] = $r;
		}
		$n = count($rows);
		if ($n === 0) {
			break;
		}
		$resp = live_post($liveUrl, array('action' => $mode, 'table' => $table, 'rows' => $rows));
		if (empty($resp['ok'])) {
			logmsg("INSERT FAIL {$table} offset={$offset}: " . json_encode($resp));
			break;
		}
		$sent += $n;
		$offset += $n;
		if ($offset % 800 === 0 || $offset >= $localN) {
			logmsg("  {$table} {$sent}/{$localN}");
		}
	}
	logmsg("DONE {$table} sent={$sent}");
}

$check = array('visit', 'attendance', 'orders', 'quotation_detail', 'expense', 'followup', 'sales_executive', 'executive');
foreach ($check as $t) {
	$c = live_post($liveUrl, array('action' => 'count', 'table' => $t));
	logmsg('LIVE COUNT ' . $t . '=' . (isset($c['count']) ? $c['count'] : json_encode($c)));
}
logmsg('PUSH COMPLETE');
