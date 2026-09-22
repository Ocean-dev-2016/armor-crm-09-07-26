<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
@set_time_limit(300);
@ini_set('memory_limit', '1024M');

register_shutdown_function(function () {
	$e = error_get_last();
	if ($e && in_array($e['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
		fwrite(STDERR, "FATAL: {$e['message']} in {$e['file']}:{$e['line']}\n");
	}
});

$page_id = 420;
$page_slug = 'page_customer';
require dirname(__FILE__) . '/connect_in.php';
require_once dirname(__FILE__) . '/../include/armor_pdf_export_helper.php';
if (isset($db) && is_object($db)) {
	$GLOBALS['db'] = $db;
}
if (isset($system) && is_object($system)) {
	$GLOBALS['system'] = $system;
}

$qid = isset($argv[1]) ? (int) $argv[1] : 2479;
echo "qid=$qid\n";

echo "STEP fetch html...\n";
$html = armor_pdf_export_fetch_view_html(
	'quotation_view_new_quotation_new_1.php',
	array('quotation_id' => $qid),
	array('quote-wrap', 'QUOTATION', 'quote-main-body')
);
echo 'html_len=' . strlen($html) . "\n";
if (trim($html) === '') {
	echo "EMPTY HTML\n";
	exit(1);
}

echo "STEP sanitize...\n";
$html2 = armor_pdf_export_sanitize_html($html);
echo 'sanitized_len=' . strlen($html2) . "\n";
echo 'vars=' . (isset($GLOBALS['armor_pdf_mpdf_vars']) ? count($GLOBALS['armor_pdf_mpdf_vars']) : 0) . "\n";

echo "STEP mpdf create...\n";
$mpdf = armor_pdf_export_create_mpdf();
if (!$mpdf) {
	echo "mpdf null\n";
	exit(1);
}
echo "STEP write html...\n";
try {
	armor_pdf_export_write_html($mpdf, $html2);
	echo "write ok pages=" . (property_exists($mpdf, 'page') ? $mpdf->page : '?') . "\n";
} catch (Exception $ex) {
	echo 'write exception: ' . $ex->getMessage() . "\n";
	exit(1);
}

$out = dirname(__FILE__) . '/pdf/orders/_cli_test_qt_' . $qid . '.pdf';
if (!is_dir(dirname($out))) {
	@mkdir(dirname($out), 0755, true);
}
echo "STEP output $out\n";
$mpdf->Output($out, 'F');
$bytes = is_file($out) ? filesize($out) : 0;
echo 'size_KB=' . round($bytes / 1024, 1) . "\n";
echo 'target_500KB=' . (($bytes > 0 && $bytes <= 512000) ? 'PASS' : 'FAIL') . "\n";
