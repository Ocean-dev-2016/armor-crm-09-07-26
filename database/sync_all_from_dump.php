<?php
/**
 * Schema-safe FULL data sync:
 * dump (kxznassm_armorfire_crm_src) -> local (kxznassm_armorfire_crm)
 *
 * - NEVER ALTER local structure
 * - Copy only common columns
 * - Local-only tables/fields kept
 * - Cut-off: 2026-08-14 inclusive
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
$srcDb = 'kxznassm_armorfire_crm_src';
$cutOff = '2026-08-14';
$cutOffDt = $cutOff . ' 23:59:59';
$logFile = __DIR__ . '/_sync_all_log.txt';
$summaryFile = __DIR__ . '/_sync_all_summary.txt';

$mergeTables = array(
	'dealer_distributor_network' => 1,
	'page_admin_right' => 1,
	'page_table' => 1,
	'api_table' => 1,
);
$skipTables = array(
	'licence_key' => 1,
);

$conn = @mysqli_connect($host, $user, $pass, '', $port);
if (!$conn) {
	fwrite(STDERR, "DB connect failed: " . mysqli_connect_error() . PHP_EOL);
	exit(1);
}
mysqli_set_charset($conn, 'utf8');
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
mysqli_query($conn, "SET UNIQUE_CHECKS=0");
mysqli_query($conn, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");
mysqli_query($conn, "SET NAMES utf8");
mysqli_query($conn, "SET SESSION innodb_lock_wait_timeout=7200");
mysqli_query($conn, "SET SESSION wait_timeout=28800");
mysqli_query($conn, "SET SESSION net_read_timeout=600");
mysqli_query($conn, "SET SESSION net_write_timeout=600");

function logmsg($msg)
{
	global $logFile;
	$line = date('H:i:s') . ' ' . $msg . "\n";
	echo $line;
	file_put_contents($logFile, $line, FILE_APPEND);
}

function q($conn, $sql)
{
	$res = mysqli_query($conn, $sql);
	if ($res === false) {
		throw new Exception("SQL error: " . mysqli_error($conn) . "\nSQL: " . $sql);
	}
	return $res;
}

function getTables($conn, $schema)
{
	$res = q($conn, "SELECT TABLE_NAME FROM information_schema.tables
		WHERE table_schema='" . mysqli_real_escape_string($conn, $schema) . "'
		AND TABLE_TYPE='BASE TABLE'
		ORDER BY TABLE_NAME");
	$out = array();
	while ($row = mysqli_fetch_assoc($res)) {
		$out[] = $row['TABLE_NAME'];
	}
	return $out;
}

function getColumns($conn, $schema, $table)
{
	$sql = "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA, COLUMN_KEY
		FROM information_schema.columns
		WHERE table_schema='" . mysqli_real_escape_string($conn, $schema) . "'
		AND table_name='" . mysqli_real_escape_string($conn, $table) . "'
		ORDER BY ORDINAL_POSITION";
	$res = q($conn, $sql);
	$cols = array();
	while ($row = mysqli_fetch_assoc($res)) {
		if (stripos($row['EXTRA'], 'GENERATED') !== false || stripos($row['EXTRA'], 'VIRTUAL') !== false) {
			continue;
		}
		$cols[$row['COLUMN_NAME']] = $row;
	}
	return $cols;
}

function tableCount($conn, $schema, $table)
{
	$row = mysqli_fetch_assoc(q($conn, "SELECT COUNT(*) AS c FROM `{$schema}`.`{$table}`"));
	return (int) $row['c'];
}

function backtickList($cols)
{
	$out = array();
	foreach ($cols as $c) {
		$out[] = '`' . str_replace('`', '``', $c) . '`';
	}
	return implode(',', $out);
}

function getPkCols($cols)
{
	$pk = array();
	foreach ($cols as $name => $meta) {
		if ($meta['COLUMN_KEY'] === 'PRI') {
			$pk[] = $name;
		}
	}
	return $pk;
}

function pickDateCol($cols)
{
	$preferred = array(
		'start_date_time', 'date_time', 'order_date', 'quotation_date', 'invoice_date',
		'expense_date', 'meeting_date', 'visit_date', 'attendance_date',
		'dispatch_date', 'payment_date', 'followup_date', 'created_date', 'created_at',
		'added_date', 'entry_date', 'tran_date', 'modified_date', 'updated_at',
	);
	foreach ($preferred as $c) {
		if (isset($cols[$c])) {
			return $c;
		}
	}
	foreach ($cols as $name => $meta) {
		$type = strtolower($meta['COLUMN_TYPE']);
		if (strpos($type, 'date') !== false || strpos($type, 'timestamp') !== false) {
			return $name;
		}
	}
	return '';
}

function buildWhere($table, $cols, $cutOff, $cutOffDt)
{
	$special = array(
		'visit' => array('start_date_time', 'created_date'),
		'attendance' => array('date_time', 'created_date'),
		'quotation_detail' => array('quotation_date', 'created_date'),
		'orders' => array('order_date', 'created_date'),
		'expense' => array('expense_date', 'created_date'),
		'proforma_invoice_info' => array('invoice_date', 'created_date'),
		'meeting' => array('meeting_date', 'created_date'),
	);
	$use = array();
	if (isset($special[$table])) {
		foreach ($special[$table] as $c) {
			if (isset($cols[$c])) {
				$use[] = $c;
			}
		}
	}
	if (empty($use)) {
		$d = pickDateCol($cols);
		if ($d !== '') {
			$use[] = $d;
		}
	}
	if (empty($use)) {
		return '';
	}

	$parts = array();
	foreach ($use as $c) {
		$bq = '`' . str_replace('`', '``', $c) . '`';
		$type = strtolower($cols[$c]['COLUMN_TYPE']);
		$isDateOnly = (strpos($type, 'datetime') === false && strpos($type, 'timestamp') === false && strpos($type, 'date') !== false);
		$limit = $isDateOnly ? $cutOff : $cutOffDt;
		$parts[] = "({$bq} IS NOT NULL AND {$bq} <> '0000-00-00' AND {$bq} <> '0000-00-00 00:00:00' AND {$bq} <= '{$limit}')";
	}
	// Keep blank/zero dates (master / old rows)
	$blank = array();
	foreach ($use as $c) {
		$bq = '`' . str_replace('`', '``', $c) . '`';
		$blank[] = "({$bq} IS NULL OR {$bq} = '0000-00-00' OR {$bq} = '0000-00-00 00:00:00' OR {$bq} = '')";
	}
	return '(' . implode(' OR ', $parts) . ' OR (' . implode(' AND ', $blank) . '))';
}

function syncReplace($conn, $localDb, $srcDb, $table, $common, $whereSql)
{
	$colSql = backtickList($common);
	q($conn, "TRUNCATE TABLE `{$localDb}`.`{$table}`");
	$where = ($whereSql !== '') ? (' WHERE ' . $whereSql) : '';
	q($conn, "INSERT INTO `{$localDb}`.`{$table}` ({$colSql}) SELECT {$colSql} FROM `{$srcDb}`.`{$table}`{$where}");
	return tableCount($conn, $localDb, $table);
}

function syncMerge($conn, $localDb, $srcDb, $table, $common, $pkCols, $whereSql)
{
	if (empty($pkCols)) {
		return syncReplace($conn, $localDb, $srcDb, $table, $common, $whereSql);
	}
	$colSql = backtickList($common);
	$updates = array();
	foreach ($common as $c) {
		if (!in_array($c, $pkCols, true)) {
			$bq = '`' . str_replace('`', '``', $c) . '`';
			$updates[] = "{$bq}=VALUES({$bq})";
		}
	}
	$where = ($whereSql !== '') ? (' WHERE ' . $whereSql) : '';
	$sql = "INSERT INTO `{$localDb}`.`{$table}` ({$colSql}) SELECT {$colSql} FROM `{$srcDb}`.`{$table}`{$where}";
	if (!empty($updates)) {
		$sql .= ' ON DUPLICATE KEY UPDATE ' . implode(',', $updates);
	}
	q($conn, $sql);
	return tableCount($conn, $localDb, $table);
}

file_put_contents($logFile, "FULL SYNC START " . date('Y-m-d H:i:s') . " cutoff={$cutOff}\n");
logmsg("Connected. Local={$localDb} Src={$srcDb}");

$localTables = getTables($conn, $localDb);
$srcTables = getTables($conn, $srcDb);
$commonTables = array_values(array_intersect($localTables, $srcTables));
$onlyLocal = array_values(array_diff($localTables, $srcTables));

logmsg('Local tables=' . count($localTables) . ' Dump tables=' . count($srcTables) . ' Common=' . count($commonTables));
logmsg('Local-only kept: ' . implode(', ', $onlyLocal));

$report = array();

foreach ($commonTables as $table) {
	try {
		$localCols = getColumns($conn, $localDb, $table);
		$srcCols = getColumns($conn, $srcDb, $table);
		$common = array_values(array_intersect(array_keys($localCols), array_keys($srcCols)));
		$onlyLocalCols = array_values(array_diff(array_keys($localCols), array_keys($srcCols)));
		$onlySrcCols = array_values(array_diff(array_keys($srcCols), array_keys($localCols)));

		if (empty($common)) {
			logmsg("SKIP {$table}: no common columns");
			$report[] = array($table, 'skip_no_common', 0, 0, 0, '');
			continue;
		}

		if (isset($skipTables[$table])) {
			$lc = tableCount($conn, $localDb, $table);
			logmsg("SKIP {$table}: keep local as-is (rows={$lc})");
			$report[] = array($table, 'skip_keep_local', $lc, tableCount($conn, $srcDb, $table), $lc, implode(',', $onlyLocalCols));
			continue;
		}

		$srcN = tableCount($conn, $srcDb, $table);
		$localBefore = tableCount($conn, $localDb, $table);

		if ($srcN === 0 && $localBefore > 0) {
			logmsg("KEEP {$table}: dump empty, local has {$localBefore} rows");
			$report[] = array($table, 'keep_local_dump_empty', $localBefore, 0, $localBefore, implode(',', $onlyLocalCols));
			continue;
		}
		if ($srcN === 0 && $localBefore === 0) {
			$report[] = array($table, 'empty_both', 0, 0, 0, implode(',', $onlyLocalCols));
			continue;
		}

		$whereSql = buildWhere($table, $srcCols, $cutOff, $cutOffDt);
		$mode = isset($mergeTables[$table]) ? 'merge' : 'replace';
		logmsg("START {$table} mode={$mode} src={$srcN} local_before={$localBefore} extra_local_fields=" . implode(',', $onlyLocalCols));

		if ($mode === 'merge') {
			$pk = getPkCols($localCols);
			$after = syncMerge($conn, $localDb, $srcDb, $table, $common, $pk, $whereSql);
		} else {
			$after = syncReplace($conn, $localDb, $srcDb, $table, $common, $whereSql);
		}

		$note = '';
		if (!empty($onlyLocalCols)) {
			$note = 'kept_local_fields=' . implode(',', $onlyLocalCols);
		}
		if (!empty($onlySrcCols)) {
			$note .= ($note !== '' ? '; ' : '') . 'ignored_dump_fields=' . implode(',', $onlySrcCols);
		}
		logmsg("DONE {$table} local_after={$after} {$note}");
		$report[] = array($table, $mode, $localBefore, $srcN, $after, $note);
	} catch (Exception $e) {
		logmsg("ERROR {$table}: " . $e->getMessage());
		$report[] = array($table, 'error', 0, 0, 0, $e->getMessage());
	}
}

mysqli_query($conn, "SET UNIQUE_CHECKS=1");
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");

$fh = fopen($summaryFile, 'wb');
fwrite($fh, "Full schema-safe sync\n");
fwrite($fh, "Cut-off: {$cutOff}\n");
fwrite($fh, "Source: {$srcDb}\n");
fwrite($fh, "Target: {$localDb} (structure preserved)\n");
fwrite($fh, "Finished: " . date('Y-m-d H:i:s') . "\n\n");
fwrite($fh, sprintf("%-45s %-28s %10s %10s %10s %s\n", 'TABLE', 'STATUS', 'BEFORE', 'DUMP', 'AFTER', 'NOTE'));
foreach ($report as $r) {
	fwrite($fh, sprintf("%-45s %-28s %10s %10s %10s %s\n", $r[0], $r[1], $r[2], $r[3], $r[4], $r[5]));
}
fwrite($fh, "\nLocal-only tables kept: " . implode(', ', $onlyLocal) . "\n");
fclose($fh);

logmsg('DONE. Summary: ' . $summaryFile);
mysqli_close($conn);
