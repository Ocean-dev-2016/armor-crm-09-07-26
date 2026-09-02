<?php

/*
 * @author Ravi Patel
 * Quotation PDF — same template as print view (suggested products) + compressed images.
 */

$staic = isset($staic) ? $staic : (isset($_REQUEST['staic']) ? $_REQUEST['staic'] : 0);
if ($staic == 2 && !isset($db)) {
	$page_id = 420;
	$page_slug = 'page_customer';
	require_once("connect_in.php");
}

$quotation_id = isset($quotation_id) ? (int) $quotation_id : (isset($_REQUEST['quotation_id']) ? (int) $_REQUEST['quotation_id'] : 0);
$file_path = '';

if ($quotation_id > 0) {
	require_once dirname(__FILE__) . '/../include/armor_pdf_export_helper.php';

	if ($staic == 2 && isset($db)) {
		$quotation_no = str_replace("/", "-", stripslashes($db->rp_getValue("quotation_detail", "quotation_no", "id='" . $quotation_id . "'", 0)));
	} else {
		$quotation_no = $quotation_id;
	}

	$fileName = date('d_m_Y') . "_" . "Quotation_" . $quotation_no . 'pdf';
	$saveRelative = $fileName . '/' . $fileName . '.pdf';

	$gen = armor_pdf_export_generate(
		'quotation_view_new_quotation_new_1.php',
		array('quotation_id' => $quotation_id),
		array('quote-wrap', 'QUOTATION', 'quote-main-body'),
		$saveRelative
	);

	if ($gen['ok']) {
		if ($staic == 2 && isset($db)) {
			$quotation_no_log = $db->rp_getValue("quotation_detail", "quotation_no", "id='" . $quotation_id . "'");
			$flag = "Web";
			$ctable = "quotation_detail";
			$module_name = "Quotation";
			$log_description = $module_name . " " . $quotation_no_log . " PDF Download By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
			$db->insertLog($ctable, $quotation_id, "insert", "", array(), 0, $log_description, $flag, $module_name, "", "");
			echo $gen['url'];
			exit;
		}
		$file_path = $gen['path'];
	} elseif ($staic == 2) {
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(array('ack' => 0, 'ack_msg' => isset($gen['error']) ? $gen['error'] : 'Quotation PDF Not Generate!!'));
		exit;
	}
}

?>
