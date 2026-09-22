<?php
/**
 * Local CLI/browser test: generate Quotation PDF and report size (target ≤500KB).
 * Usage:
 *   php _test_quotation_pdf_size.php
 *   php _test_quotation_pdf_size.php 2629
 *   http://localhost:8080/armor_crm_08_07/202526/bbsales_tracking/_test_quotation_pdf_size.php?quotation_id=2629
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
@set_time_limit(300);
@ini_set('memory_limit', '1024M');

$page_id = 420;
$page_slug = 'page_customer';
require_once dirname(__FILE__) . '/connect_in.php';
require_once dirname(__FILE__) . '/../include/armor_pdf_export_helper.php';

// Ensure PDF embed include() can see $db (function scope).
if (isset($db) && is_object($db)) {
	$GLOBALS['db'] = $db;
}
if (isset($system) && is_object($system)) {
	$GLOBALS['system'] = $system;
}

$quotationId = 0;
if (isset($argv[1]) && (int) $argv[1] > 0) {
	$quotationId = (int) $argv[1];
} elseif (!empty($_REQUEST['quotation_id'])) {
	$quotationId = (int) $_REQUEST['quotation_id'];
}

if ($quotationId <= 0) {
	$row = $db->rp_getData('quotation_detail', 'id,quotation_no', 'isDelete=0', 'id DESC', 1);
	if ($row) {
		$d = mysqli_fetch_assoc($row);
		$quotationId = isset($d['id']) ? (int) $d['id'] : 0;
	}
}

$isCli = (php_sapi_name() === 'cli');
$out = function ($msg) use ($isCli) {
	if ($isCli) {
		echo $msg . PHP_EOL;
	} else {
		echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . "<br>\n";
	}
};

if ($quotationId <= 0) {
	$out('ERROR: No quotation_id found.');
	exit(1);
}

$qNo = $db->rp_getValue('quotation_detail', 'quotation_no', "id='" . $quotationId . "' AND isDelete=0", 0);
$fileName = 'TEST_compress_' . date('Ymd_His') . '_QT_' . preg_replace('/[^A-Za-z0-9_-]/', '-', (string) $qNo);
$saveRelative = $fileName . '/' . $fileName . '.pdf';

$out('Generating Quotation PDF...');
$out('quotation_id=' . $quotationId . ' quotation_no=' . $qNo);

$t0 = microtime(true);
$gen = armor_pdf_export_generate(
	'quotation_view_new_quotation_new_1.php',
	array('quotation_id' => $quotationId),
	array('quote-wrap', 'QUOTATION', 'quote-main-body'),
	$saveRelative
);
$ms = round((microtime(true) - $t0) * 1000);

if (empty($gen['ok'])) {
	$out('FAIL: ' . (isset($gen['error']) ? $gen['error'] : 'unknown'));
	exit(1);
}

$bytes = isset($gen['size']) ? (int) $gen['size'] : (is_file($gen['path']) ? filesize($gen['path']) : 0);
$kb = round($bytes / 1024, 1);
$targetOk = ($bytes >= 300 * 1024 && $bytes <= 500 * 1024);
$underOk = ($bytes > 0 && $bytes <= 500 * 1024);

$out('OK');
$out('path=' . $gen['path']);
$out('url=' . (isset($gen['url']) ? $gen['url'] : ''));
$out('pages=' . (isset($gen['pages']) ? $gen['pages'] : '?'));
$out('size_bytes=' . $bytes);
$out('size_KB=' . $kb);
if ($targetOk) {
	$out('target_300_500KB=PASS');
} elseif ($underOk) {
	$out('target_300_500KB=UNDER (ok but smaller than 300KB)');
} else {
	$out('target_300_500KB=FAIL (over 500KB)');
}
$out('time_ms=' . $ms);

if (!$isCli && !empty($gen['url'])) {
	echo '<p><a href="' . htmlspecialchars($gen['url'], ENT_QUOTES, 'UTF-8') . '" target="_blank">Open PDF</a></p>';
}

exit($targetOk ? 0 : 2);
