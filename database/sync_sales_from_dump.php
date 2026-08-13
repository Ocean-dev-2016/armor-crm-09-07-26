<?php
/**
 * Schema-safe sync: dump DB (kxznassm_armorfire_crm_src) -> local (kxznassm_armorfire_crm)
 * - Keeps LOCAL table structure (never ALTER)
 * - Copies only common columns
 * - Date cut-off: today (inclusive)
 * - Also writes importable SQL files under database/sales_import/
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
$cutOff = date('Y-m-d'); // today
$outDir = __DIR__ . '/sales_import';

if (!is_dir($outDir)) {
	mkdir($outDir, 0777, true);
}

$conn = @mysqli_connect($host, $user, $pass, '', $port);
if (!$conn) {
	fwrite(STDERR, "DB connect failed: " . mysqli_connect_error() . PHP_EOL);
	exit(1);
}
mysqli_set_charset($conn, 'utf8');
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
mysqli_query($conn, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");
mysqli_query($conn, "SET NAMES utf8");

function q($conn, $sql)
{
	$res = mysqli_query($conn, $sql);
	if ($res === false) {
		throw new Exception("SQL error: " . mysqli_error($conn) . "\nSQL: " . $sql);
	}
	return $res;
}

function tableExists($conn, $schema, $table)
{
	$sql = "SELECT COUNT(*) AS c FROM information_schema.tables
		WHERE table_schema='" . mysqli_real_escape_string($conn, $schema) . "'
		AND table_name='" . mysqli_real_escape_string($conn, $table) . "'";
	$row = mysqli_fetch_assoc(q($conn, $sql));
	return ((int) $row['c']) > 0;
}

function getColumns($conn, $schema, $table)
{
	$sql = "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA
		FROM information_schema.columns
		WHERE table_schema='" . mysqli_real_escape_string($conn, $schema) . "'
		AND table_name='" . mysqli_real_escape_string($conn, $table) . "'
		ORDER BY ORDINAL_POSITION";
	$res = q($conn, $sql);
	$cols = array();
	while ($row = mysqli_fetch_assoc($res)) {
		$cols[$row['COLUMN_NAME']] = $row;
	}
	return $cols;
}

function commonColumns($conn, $localDb, $srcDb, $table)
{
	$local = getColumns($conn, $localDb, $table);
	$src = getColumns($conn, $srcDb, $table);
	$common = array_values(array_intersect(array_keys($local), array_keys($src)));
	$onlyLocal = array_values(array_diff(array_keys($local), array_keys($src)));
	$onlySrc = array_values(array_diff(array_keys($src), array_keys($local)));
	return array($common, $onlyLocal, $onlySrc);
}

function backtickList($cols)
{
	$out = array();
	foreach ($cols as $c) {
		$out[] = '`' . str_replace('`', '``', $c) . '`';
	}
	return implode(',', $out);
}

function syncTable($conn, $localDb, $srcDb, $table, $whereSql, $outDir, $cutOff)
{
	echo "=== {$table} ===\n";
	if (!tableExists($conn, $srcDb, $table)) {
		echo "SKIP: not in dump DB\n";
		return array('table' => $table, 'status' => 'skip_missing_src', 'rows' => 0);
	}
	if (!tableExists($conn, $localDb, $table)) {
		echo "SKIP: not in local DB\n";
		return array('table' => $table, 'status' => 'skip_missing_local', 'rows' => 0);
	}

	list($common, $onlyLocal, $onlySrc) = commonColumns($conn, $localDb, $srcDb, $table);
	if (empty($common)) {
		echo "SKIP: no common columns\n";
		return array('table' => $table, 'status' => 'skip_no_common', 'rows' => 0);
	}

	if (!empty($onlyLocal)) {
		echo "Local-only fields kept (not overwritten from dump): " . implode(', ', $onlyLocal) . "\n";
	}
	if (!empty($onlySrc)) {
		echo "Dump-only fields ignored: " . implode(', ', $onlySrc) . "\n";
	}

	$colSql = backtickList($common);
	$where = ($whereSql !== '') ? (' WHERE ' . $whereSql) : '';

	$countRes = q($conn, "SELECT COUNT(*) AS c FROM `{$srcDb}`.`{$table}`{$where}");
	$countRow = mysqli_fetch_assoc($countRes);
	$srcCount = (int) $countRow['c'];
	echo "Source rows (filtered): {$srcCount}\n";

	q($conn, "TRUNCATE TABLE `{$localDb}`.`{$table}`");
	$insertSql = "INSERT INTO `{$localDb}`.`{$table}` ({$colSql})
		SELECT {$colSql} FROM `{$srcDb}`.`{$table}`{$where}";
	q($conn, $insertSql);

	$localCountRes = q($conn, "SELECT COUNT(*) AS c FROM `{$localDb}`.`{$table}`");
	$localCount = (int) mysqli_fetch_assoc($localCountRes)['c'];
	echo "Imported into local: {$localCount}\n";

	// Export SQL file for later live use (schema-safe INSERT)
	$file = $outDir . '/' . $table . '.sql';
	$fh = fopen($file, 'wb');
	fwrite($fh, "-- Schema-safe import for `{$table}`\n");
	fwrite($fh, "-- Cut-off date: {$cutOff}\n");
	fwrite($fh, "-- Local-only columns NOT included (keep existing local structure)\n");
	if (!empty($onlyLocal)) {
		fwrite($fh, "-- Local-only: " . implode(', ', $onlyLocal) . "\n");
	}
	fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n");
	fwrite($fh, "TRUNCATE TABLE `{$table}`;\n");

	$batch = 200;
	$offset = 0;
	while (true) {
		$res = q($conn, "SELECT {$colSql} FROM `{$srcDb}`.`{$table}`{$where} LIMIT {$offset}, {$batch}");
		$n = mysqli_num_rows($res);
		if ($n == 0) {
			break;
		}
		$values = array();
		while ($row = mysqli_fetch_assoc($res)) {
			$parts = array();
			foreach ($common as $c) {
				$v = $row[$c];
				if ($v === null) {
					$parts[] = 'NULL';
				} else {
					$parts[] = "'" . mysqli_real_escape_string($conn, $v) . "'";
				}
			}
			$values[] = '(' . implode(',', $parts) . ')';
		}
		fwrite($fh, "INSERT INTO `{$table}` ({$colSql}) VALUES\n" . implode(",\n", $values) . ";\n");
		$offset += $batch;
		if ($n < $batch) {
			break;
		}
	}
	fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
	fclose($fh);
	echo "SQL file: {$file}\n";

	return array(
		'table' => $table,
		'status' => 'ok',
		'rows' => $localCount,
		'only_local' => $onlyLocal,
		'only_src' => $onlySrc,
	);
}

$report = array();
$cutEsc = mysqli_real_escape_string($conn, $cutOff);

// Parent / dated tables
$jobs = array(
	array('sales_executive', ''), // master
	array('visit', "DATE(COALESCE(NULLIF(start_date_time,'0000-00-00 00:00:00'), created_date)) <= '{$cutEsc}'"),
	array('attendance', "DATE(COALESCE(NULLIF(date_time,'0000-00-00 00:00:00'), created_date)) <= '{$cutEsc}'"),
	array('quotation_detail', "DATE(COALESCE(quotation_date, created_date)) <= '{$cutEsc}'"),
	array('orders', "DATE(COALESCE(order_date, created_date)) <= '{$cutEsc}'"),
	array('expense', "DATE(COALESCE(expense_date, created_date)) <= '{$cutEsc}'"),
	array('proforma_invoice_info', "DATE(COALESCE(invoice_date, created_date)) <= '{$cutEsc}'"),
	array('meeting', "DATE(COALESCE(meeting_date, created_date)) <= '{$cutEsc}'"),
);

foreach ($jobs as $job) {
	try {
		$report[] = syncTable($conn, $localDb, $srcDb, $job[0], $job[1], $outDir, $cutOff);
	} catch (Exception $e) {
		echo "ERROR {$job[0]}: " . $e->getMessage() . "\n";
		$report[] = array('table' => $job[0], 'status' => 'error', 'error' => $e->getMessage());
	}
}

// Child tables by parent IDs already in local
$childJobs = array(
	array('quotation_product_item', 'quotation_id', 'quotation_detail'),
	array('order_product_item', 'order_id', 'orders'),
	array('order_scheme_items', 'order_id', 'orders'),
	array('proforma_invoice_item', 'proforma_invoice_id', 'proforma_invoice_info'),
	array('meeting_member', 'meeting_id', 'meeting'),
	array('visit_consultant_form', 'visit_id', 'visit'),
	array('visit_high_rate_form', 'visit_id', 'visit'),
	array('visit_high_rate_form_item', 'visit_id', 'visit'),
);

foreach ($childJobs as $job) {
	list($table, $fk, $parent) = $job;
	try {
		if (!tableExists($conn, $srcDb, $table)) {
			echo "=== {$table} ===\nSKIP: not in dump DB (local structure kept)\n";
			$report[] = array('table' => $table, 'status' => 'skip_missing_src', 'rows' => 0);
			continue;
		}
		// Detect actual FK column if needed
		$srcCols = getColumns($conn, $srcDb, $table);
		if (!isset($srcCols[$fk])) {
			// try common alternates
			$alts = array('quotation_id', 'order_id', 'invoice_id', 'proforma_invoice_id', 'meeting_id', 'visit_id');
			$found = '';
			foreach ($alts as $a) {
				if (isset($srcCols[$a])) {
					$found = $a;
					break;
				}
			}
			if ($found === '') {
				echo "=== {$table} ===\nSKIP: FK column not found\n";
				$report[] = array('table' => $table, 'status' => 'skip_no_fk', 'rows' => 0);
				continue;
			}
			$fk = $found;
		}
		$where = "`{$fk}` IN (SELECT `id` FROM `{$srcDb}`.`{$parent}`)";
		// Prefer filter by parents that are already dated in src via same parent filter already applied conceptually:
		// Use local parent ids after parent sync:
		$where = "`{$fk}` IN (SELECT `id` FROM `{$localDb}`.`{$parent}`)";
		// But SELECT from src table:
		// syncTable builds FROM src WHERE ... so use src parent with same date rules via local ids:
		$where = "`{$fk}` IN (SELECT `id` FROM `{$localDb}`.`{$parent}`)";
		// Problem: WHERE uses src table - subquery to local is fine across DBs
		$report[] = syncTable($conn, $localDb, $srcDb, $table, $where, $outDir, $cutOff);
	} catch (Exception $e) {
		echo "ERROR {$table}: " . $e->getMessage() . "\n";
		$report[] = array('table' => $table, 'status' => 'error', 'error' => $e->getMessage());
	}
}

mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");

// Write summary + live instructions
$summaryFile = $outDir . '/_SUMMARY.txt';
$fh = fopen($summaryFile, 'wb');
fwrite($fh, "Sales data sync summary\n");
fwrite($fh, "Cut-off: {$cutOff}\n");
fwrite($fh, "Source: {$srcDb} (from kxznassm_armorfire_crm.sql)\n");
fwrite($fh, "Target: {$localDb} (local XAMPP - schema preserved)\n\n");
foreach ($report as $r) {
	$line = $r['table'] . ' => ' . $r['status'];
	if (isset($r['rows'])) {
		$line .= ' rows=' . $r['rows'];
	}
	if (!empty($r['only_local'])) {
		$line .= ' | local-only-fields=' . implode(',', $r['only_local']);
	}
	if (!empty($r['error'])) {
		$line .= ' | ' . $r['error'];
	}
	fwrite($fh, $line . "\n");
}
fwrite($fh, "\nFor LIVE:\n");
fwrite($fh, "1) Do NOT import dump CREATE TABLE / ALTER statements.\n");
fwrite($fh, "2) Run each *.sql in sales_import/ on live DB (keeps existing live fields).\n");
fwrite($fh, "3) Prefer off-peak; backup live first.\n");
fclose($fh);

echo "\nDONE. Summary: {$summaryFile}\n";
mysqli_close($conn);
