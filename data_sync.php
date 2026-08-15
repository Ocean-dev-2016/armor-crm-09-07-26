<?php
/**
 * Schema-safe DATA import for live (jrosvllq_armor_crm_09_07).
 * Does NOT ALTER tables. Inserts only columns that already exist.
 * Delete this file after import.
 *
 * URL: https://armor-crm.oceanhub.co.in/data_sync.php?key=armor_cp_sync_2026&action=ping
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
set_time_limit(300);

define('DATA_SYNC_KEY', 'armor_cp_sync_2026');

$key = isset($_REQUEST['key']) ? $_REQUEST['key'] : '';
if ($key !== DATA_SYNC_KEY) {
	header('HTTP/1.1 403 Forbidden');
	header('Content-Type: application/json');
	echo '{"ok":0,"error":"unauthorized"}';
	exit;
}

require_once __DIR__ . '/include/app.config.loader.php';
require_once __DIR__ . '/include/json_polyfill.php';
$config = armor_get_app_config();

$host = isset($config['db_host']) ? $config['db_host'] : 'localhost';
$user = isset($config['db_user']) ? $config['db_user'] : '';
$pass = isset($config['db_pass']) ? $config['db_pass'] : '';
$name = isset($config['db_name']) ? $config['db_name'] : '';
$ports = isset($config['db_ports']) ? $config['db_ports'] : array(3306);

$conn = null;
$lastError = '';
foreach ($ports as $port) {
	$conn = @mysqli_connect($host, $user, $pass, $name, (int) $port);
	if ($conn) {
		break;
	}
	$lastError = mysqli_connect_error();
}
if (!$conn) {
	header('Content-Type: application/json');
	echo json_encode(array('ok' => 0, 'error' => 'DB connection failed: ' . $lastError));
	exit;
}
mysqli_set_charset($conn, 'utf8');
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
mysqli_query($conn, "SET UNIQUE_CHECKS=0");
mysqli_query($conn, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");

function ds_out($arr)
{
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($arr);
	exit;
}

function ds_table_ok($table)
{
	return (bool) preg_match('/^[a-zA-Z0-9_]+$/', $table);
}

function ds_columns($conn, $schema, $table)
{
	$sql = "SELECT COLUMN_NAME FROM information_schema.columns
		WHERE table_schema='" . mysqli_real_escape_string($conn, $schema) . "'
		AND table_name='" . mysqli_real_escape_string($conn, $table) . "'
		AND EXTRA NOT LIKE '%GENERATED%' AND EXTRA NOT LIKE '%VIRTUAL%'
		ORDER BY ORDINAL_POSITION";
	$res = mysqli_query($conn, $sql);
	$cols = array();
	if ($res) {
		while ($row = mysqli_fetch_assoc($res)) {
			$cols[] = $row['COLUMN_NAME'];
		}
	}
	return $cols;
}

function ds_table_exists($conn, $schema, $table)
{
	$t = mysqli_real_escape_string($conn, $table);
	$s = mysqli_real_escape_string($conn, $schema);
	$res = mysqli_query($conn, "SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema='{$s}' AND table_name='{$t}' AND TABLE_TYPE='BASE TABLE'");
	$row = $res ? mysqli_fetch_assoc($res) : null;
	return $row && (int) $row['c'] > 0;
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'ping';
$raw = file_get_contents('php://input');
$body = array();
if ($raw !== '' && $raw !== false) {
	$decoded = json_decode($raw, true);
	if (is_array($decoded)) {
		$body = $decoded;
		if (isset($body['action'])) {
			$action = $body['action'];
		}
	}
}

if ($action === 'ping') {
	$cntRes = mysqli_query($conn, "SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema='" . mysqli_real_escape_string($conn, $name) . "' AND TABLE_TYPE='BASE TABLE'");
	$cntRow = $cntRes ? mysqli_fetch_assoc($cntRes) : array('c' => 0);
	ds_out(array(
		'ok' => 1,
		'db' => $name,
		'environment' => isset($config['environment']) ? $config['environment'] : '',
		'tables' => (int) $cntRow['c'],
	));
}

$table = isset($body['table']) ? $body['table'] : (isset($_REQUEST['table']) ? $_REQUEST['table'] : '');
if (!ds_table_ok($table)) {
	ds_out(array('ok' => 0, 'error' => 'invalid table'));
}
if (!ds_table_exists($conn, $name, $table)) {
	ds_out(array('ok' => 0, 'error' => 'table not found: ' . $table));
}

if ($action === 'count') {
	$res = mysqli_query($conn, "SELECT COUNT(*) c FROM `{$table}`");
	$row = $res ? mysqli_fetch_assoc($res) : array('c' => -1);
	ds_out(array('ok' => 1, 'table' => $table, 'count' => (int) $row['c']));
}

if ($action === 'truncate') {
	$ok = mysqli_query($conn, "TRUNCATE TABLE `{$table}`");
	ds_out(array('ok' => $ok ? 1 : 0, 'table' => $table, 'error' => $ok ? '' : mysqli_error($conn)));
}

if ($action === 'insert' || $action === 'merge') {
	$rows = isset($body['rows']) && is_array($body['rows']) ? $body['rows'] : array();
	if (empty($rows)) {
		ds_out(array('ok' => 1, 'table' => $table, 'inserted' => 0));
	}
	$liveCols = ds_columns($conn, $name, $table);
	$liveMap = array();
	foreach ($liveCols as $c) {
		$liveMap[$c] = 1;
	}
	$first = $rows[0];
	$useCols = array();
	foreach ($first as $k => $v) {
		if (isset($liveMap[$k])) {
			$useCols[] = $k;
		}
	}
	if (empty($useCols)) {
		ds_out(array('ok' => 0, 'error' => 'no common columns for ' . $table));
	}
	$colSql = array();
	foreach ($useCols as $c) {
		$colSql[] = '`' . str_replace('`', '``', $c) . '`';
	}
	$valuesSql = array();
	foreach ($rows as $row) {
		$parts = array();
		foreach ($useCols as $c) {
			if (!isset($row[$c]) || $row[$c] === null) {
				$parts[] = 'NULL';
			} else {
				$parts[] = "'" . mysqli_real_escape_string($conn, $row[$c]) . "'";
			}
		}
		$valuesSql[] = '(' . implode(',', $parts) . ')';
	}
	$sql = "INSERT INTO `{$table}` (" . implode(',', $colSql) . ") VALUES " . implode(',', $valuesSql);
	if ($action === 'merge') {
		$updates = array();
		foreach ($useCols as $c) {
			$bq = '`' . str_replace('`', '``', $c) . '`';
			$updates[] = "{$bq}=VALUES({$bq})";
		}
		$sql .= ' ON DUPLICATE KEY UPDATE ' . implode(',', $updates);
	}
	$ok = mysqli_query($conn, $sql);
	ds_out(array(
		'ok' => $ok ? 1 : 0,
		'table' => $table,
		'inserted' => $ok ? count($rows) : 0,
		'error' => $ok ? '' : mysqli_error($conn),
	));
}

ds_out(array('ok' => 0, 'error' => 'unknown action'));
