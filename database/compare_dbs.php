<?php
/**
 * Compare-only: dump DB (kxznassm_armorfire_crm_src) vs local (kxznassm_armorfire_crm)
 * Does NOT ALTER fields and does NOT write data.
 * Cut-off: 2026-08-14 (inclusive)
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
set_time_limit(0);

$host = '127.0.0.1';
$port = 3307;
$user = 'root';
$pass = '';
$localDb = 'kxznassm_armorfire_crm';
$srcDb = 'kxznassm_armorfire_crm_src';
$cutOff = '2026-08-14';
$outFile = __DIR__ . '/_compare_report.txt';

$conn = @mysqli_connect($host, $user, $pass, '', $port);
if (!$conn) {
	fwrite(STDERR, "DB connect failed: " . mysqli_connect_error() . PHP_EOL);
	exit(1);
}
mysqli_set_charset($conn, 'utf8');

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
		$cols[$row['COLUMN_NAME']] = $row;
	}
	return $cols;
}

function pickDateCol($cols)
{
	$preferred = array(
		'created_date', 'created_at', 'order_date', 'quotation_date', 'invoice_date',
		'expense_date', 'meeting_date', 'visit_date', 'date_time', 'start_date_time',
		'attendance_date', 'followup_date', 'dispatch_date', 'payment_date',
		'modified_date', 'updated_at', 'added_date', 'entry_date', 'tran_date'
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

function tableCount($conn, $schema, $table)
{
	$row = mysqli_fetch_assoc(q($conn, "SELECT COUNT(*) AS c FROM `{$schema}`.`{$table}`"));
	return (int) $row['c'];
}

function filteredCount($conn, $schema, $table, $dateCol, $cutOff)
{
	$col = '`' . str_replace('`', '``', $dateCol) . '`';
	$sql = "SELECT COUNT(*) AS c FROM `{$schema}`.`{$table}`
		WHERE DATE(COALESCE(NULLIF({$col}, '0000-00-00 00:00:00'), NULLIF({$col}, '0000-00-00'), {$col})) <= '{$cutOff}'
		OR {$col} IS NULL OR {$col} = '0000-00-00' OR {$col} = '0000-00-00 00:00:00'";
	$res = mysqli_query($conn, $sql);
	if ($res === false) {
		return null;
	}
	$row = mysqli_fetch_assoc($res);
	return (int) $row['c'];
}

$localTables = getTables($conn, $localDb);
$srcTables = getTables($conn, $srcDb);
$onlyLocal = array_values(array_diff($localTables, $srcTables));
$onlySrc = array_values(array_diff($srcTables, $localTables));
$commonTables = array_values(array_intersect($localTables, $srcTables));

$lines = array();
$lines[] = "DB COMPARE REPORT (NO CHANGES MADE)";
$lines[] = "Generated: " . date('Y-m-d H:i:s');
$lines[] = "Local DB : {$localDb}  (XAMPP local - schema KEEP)";
$lines[] = "Source DB: {$srcDb}  (from kxznassm_armorfire_crm.sql dump 15-Aug-2026)";
$lines[] = "Cut-off  : {$cutOff} inclusive";
$lines[] = "";
$lines[] = "TABLE COUNTS";
$lines[] = "Local tables : " . count($localTables);
$lines[] = "Dump tables  : " . count($srcTables);
$lines[] = "Common       : " . count($commonTables);
$lines[] = "Only in LOCAL: " . count($onlyLocal);
$lines[] = "Only in DUMP : " . count($onlySrc);
$lines[] = "";

$lines[] = "===== TABLES ONLY IN LOCAL (dump ma nathi) =====";
if (empty($onlyLocal)) {
	$lines[] = "(none)";
} else {
	foreach ($onlyLocal as $t) {
		$c = tableCount($conn, $localDb, $t);
		$lines[] = sprintf("%-50s local_rows=%d", $t, $c);
	}
}
$lines[] = "";
$lines[] = "===== TABLES ONLY IN DUMP (local ma nathi) =====";
if (empty($onlySrc)) {
	$lines[] = "(none)";
} else {
	foreach ($onlySrc as $t) {
		$c = tableCount($conn, $srcDb, $t);
		$lines[] = sprintf("%-50s dump_rows=%d", $t, $c);
	}
}
$lines[] = "";
$lines[] = "===== COLUMN / FIELD DIFFERENCES (common tables) =====";
$colDiffTables = 0;
foreach ($commonTables as $t) {
	$lc = getColumns($conn, $localDb, $t);
	$sc = getColumns($conn, $srcDb, $t);
	$onlyL = array_values(array_diff(array_keys($lc), array_keys($sc)));
	$onlyS = array_values(array_diff(array_keys($sc), array_keys($lc)));
	$typeDiff = array();
	foreach (array_intersect(array_keys($lc), array_keys($sc)) as $col) {
		if ($lc[$col]['COLUMN_TYPE'] !== $sc[$col]['COLUMN_TYPE']) {
			$typeDiff[] = $col . ' [local=' . $lc[$col]['COLUMN_TYPE'] . ' | dump=' . $sc[$col]['COLUMN_TYPE'] . ']';
		}
	}
	if (!empty($onlyL) || !empty($onlyS) || !empty($typeDiff)) {
		$colDiffTables++;
		$lines[] = "-- {$t}";
		if (!empty($onlyL)) {
			$lines[] = "   LOCAL-ONLY fields (keep, do not drop): " . implode(', ', $onlyL);
		}
		if (!empty($onlyS)) {
			$lines[] = "   DUMP-ONLY fields (ignore, do not add): " . implode(', ', $onlyS);
		}
		if (!empty($typeDiff)) {
			$lines[] = "   TYPE mismatch: " . implode(' ; ', $typeDiff);
		}
	}
}
if ($colDiffTables === 0) {
	$lines[] = "(no column name/type differences)";
}
$lines[] = "";
$lines[] = "===== ROW COUNT DIFFERENCES (common tables) =====";
$lines[] = sprintf("%-45s %12s %12s %12s %12s %s", "TABLE", "LOCAL", "DUMP", "DUMP<=14-08", "DIFF(dump-local)", "DATE_COL");
$diffCount = 0;
$sameCount = 0;
foreach ($commonTables as $t) {
	$localC = tableCount($conn, $localDb, $t);
	$srcC = tableCount($conn, $srcDb, $t);
	$sc = getColumns($conn, $srcDb, $t);
	$dateCol = pickDateCol($sc);
	$srcCut = '';
	if ($dateCol !== '') {
		$fc = filteredCount($conn, $srcDb, $t, $dateCol, $cutOff);
		$srcCut = ($fc === null) ? 'err' : (string) $fc;
	} else {
		$srcCut = '-';
	}
	$diff = $srcC - $localC;
	if ($diff !== 0) {
		$diffCount++;
		$flag = $diff > 0 ? 'DUMP_HAS_MORE' : 'LOCAL_HAS_MORE';
	} else {
		$sameCount++;
		$flag = 'SAME';
	}
	$lines[] = sprintf("%-45s %12d %12d %12s %12d  %s  date=%s", $t, $localC, $srcC, $srcCut, $diff, $flag, ($dateCol === '' ? '-' : $dateCol));
}
$lines[] = "";
$lines[] = "Tables with same row count: {$sameCount}";
$lines[] = "Tables with different row count: {$diffCount}";
$lines[] = "";
$lines[] = "NOTE: This report made ZERO schema/data changes.";
$lines[] = "Local fields will be kept as-is if data is later copied (common columns only).";

$text = implode("\n", $lines) . "\n";
file_put_contents($outFile, $text);
echo $text;
mysqli_close($conn);
