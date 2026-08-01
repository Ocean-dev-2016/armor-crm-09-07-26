<?php
/**
 * CP Share PDF — generate real PDF, store temporarily, return WhatsApp share data.
 * PHP 5.6 compatible. Always returns clean JSON.
 */
$page_id = 565;
$page_slug = 'channel_partner_payment';
error_reporting(0);
@ini_set('display_errors', 0);
@ini_set('log_errors', 1);
ob_start();

$GLOBALS['cp_share_json_sent'] = 0;

function cp_share_json($arr)
{
	if (!empty($GLOBALS['cp_share_json_sent'])) {
		return;
	}
	$GLOBALS['cp_share_json_sent'] = 1;
	while (ob_get_level()) {
		ob_end_clean();
	}
	if (!headers_sent()) {
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-store, no-cache, must-revalidate');
	}
	echo json_encode($arr);
	exit;
}

function cp_share_shutdown_handler()
{
	if (!empty($GLOBALS['cp_share_json_sent'])) {
		return;
	}
	$err = error_get_last();
	/* Only real fatals — ignore notices like "Constant already defined" */
	$fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
	if ($err && isset($err['type']) && in_array((int) $err['type'], $fatalTypes, true)) {
		$msg = 'PDF generation failed on server.';
		if (!empty($err['message'])) {
			$msg .= ' ' . $err['message'];
		}
		cp_share_json(array('ack' => 0, 'ack_msg' => $msg));
		return;
	}
	/* Script ended without JSON and without fatal — still return JSON */
	cp_share_json(array('ack' => 0, 'ack_msg' => 'PDF generation stopped unexpectedly. Check mPDF and temp folder permissions.'));
}

register_shutdown_function('cp_share_shutdown_handler');

include("connect.php");
include("include/channel_partner_ledger_data.php");

while (ob_get_level() > 1) {
	ob_end_clean();
}
ob_clean();

$type = isset($_REQUEST['type']) ? strtolower(trim($_REQUEST['type'])) : '';
if ($type != 'payment' && $type != 'ledger') {
	cp_share_json(array('ack' => 0, 'ack_msg' => 'Invalid type.'));
}

$is_cp = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
$cp_login_id = $is_cp ? (int) cp_get_login_channel_partner_id() : 0;
$is_admin = (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0);

if ($type == 'payment') {
	if (!$is_cp) {
		cp_share_json(array('ack' => 0, 'ack_msg' => 'Channel Partner login required.'));
	}
	$cpFilter = $cp_login_id;
} else {
	if (!$is_cp && !$is_admin) {
		cp_share_json(array('ack' => 0, 'ack_msg' => 'Access denied.'));
	}
	$cpFilter = $is_cp ? $cp_login_id : (isset($_REQUEST['cp_id']) ? (int) $_REQUEST['cp_id'] : 0);
}

$partyFilter = isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : 0;
if ($type == 'payment' && $partyFilter <= 0) {
	cp_share_json(array('ack' => 0, 'ack_msg' => 'Select Party first.'));
}
if ($cpFilter <= 0) {
	cp_share_json(array('ack' => 0, 'ack_msg' => 'Channel Partner required.'));
}

$cpPhoneDigits = function_exists('cp_whatsapp_phone_digits')
	? cp_whatsapp_phone_digits($db, $cpFilter)
	: '';

$cp_r = $db->rp_getData("executive", "company_name,cp_print_company_name,cp_print_gst,gst", "id='" . $cpFilter . "' AND isDelete=0", "", 0);
$cp = $cp_r ? mysqli_fetch_assoc($cp_r) : array();
$cp_name = isset($cp['company_name']) ? $cp['company_name'] : '';
$pi_company = !empty($cp['cp_print_company_name']) ? $cp['cp_print_company_name'] : $cp_name;
$pi_gst = !empty($cp['cp_print_gst']) ? $cp['cp_print_gst'] : (isset($cp['gst']) ? $cp['gst'] : '');

$partyLabel = 'All Parties';
$partyRow = null;
if ($partyFilter > 0) {
	$pr = $db->rp_getData(
		"channel_partner_customer",
		"id,company_name,person_name,mobile_no,address,city,state,pincode,gst",
		"id='" . $partyFilter . "' AND channel_partner_id='" . $cpFilter . "' AND isDelete=0",
		"",
		0
	);
	$partyRow = $pr ? mysqli_fetch_assoc($pr) : null;
	if ($partyRow) {
		$partyLabel = $partyRow['company_name'];
	}
}

$baseAdmin = rtrim(ADMINSITEURL, '/');
$css = 'table{width:100%;border-collapse:collapse;font-size:11px;font-family:Arial,sans-serif;}
td,th{border:1px solid #595959;padding:5px 6px;vertical-align:top;}
.th{background:#1a6b8a;color:#fff;text-align:center;}
.title{background:#A9A9A9;text-align:center;font-weight:bold;font-size:14px;}
.tr{text-align:right;}.tc{text-align:center;}
.ok{color:#1e7e34;font-weight:bold;}.pend{color:#c0392b;font-weight:bold;}
.gray{background:#f0f0f0;}';

if ($type == 'payment') {
	$docTitle = 'PAYMENT RECEIVE STATEMENT';
	$orders = array();
	$or = $db->rp_getData(
		"orders",
		"id,order_no,order_date,grand_total,payment_received_flag,payment_received_amount,status",
		"customer_id='" . $cpFilter . "' AND channel_partner_order_flag=1 AND channel_partner_customer_id='" . $partyFilter . "' AND isDelete=0 AND status NOT IN (-2,3)",
		"order_date ASC, id ASC",
		0
	);
	if ($or) {
		while ($o = mysqli_fetch_assoc($or)) {
			$orders[] = $o;
		}
	}
	$totalOrder = 0;
	$totalPaid = 0;
	$totalPending = 0;
	$rowsHtml = '';
	$sr = 0;
	foreach ($orders as $o) {
		$sr++;
		$paidFlag = isset($o['payment_received_flag']) ? (int) $o['payment_received_flag'] : 0;
		$orderAmt = (float) $o['grand_total'];
		$paidAmt = ($paidFlag === 1) ? (float) $o['payment_received_amount'] : 0;
		$totalOrder += $orderAmt;
		$totalPaid += $paidAmt;
		$totalPending += ($paidFlag === 1) ? 0 : $orderAmt;
		$statusTxt = ($paidFlag === 1) ? 'RECEIVED' : 'PENDING';
		$statusCls = ($paidFlag === 1) ? 'ok' : 'pend';
		$rowsHtml .= '<tr><td class="tc">' . $sr . '</td><td><b>' . htmlspecialchars($o['order_no']) . '</b></td>'
			. '<td class="tc">' . date('d-m-Y', strtotime($o['order_date'])) . '</td>'
			. '<td class="tr">' . number_format($orderAmt, 2) . '</td>'
			. '<td class="tc ' . $statusCls . '">' . $statusTxt . '</td>'
			. '<td class="tr">' . number_format($paidAmt, 2) . '</td></tr>';
	}
	if ($rowsHtml == '') {
		$rowsHtml = '<tr><td colspan="6" class="tc">No orders found.</td></tr>';
	}
	$partyAddr = '';
	$partyMobile = '';
	$partyGst = '';
	if ($partyRow) {
		$parts = array();
		if (!empty($partyRow['address'])) { $parts[] = $partyRow['address']; }
		if (!empty($partyRow['city'])) { $parts[] = $partyRow['city']; }
		if (!empty($partyRow['state'])) { $parts[] = $partyRow['state']; }
		if (!empty($partyRow['pincode'])) { $parts[] = $partyRow['pincode']; }
		$partyAddr = implode(', ', $parts);
		$partyMobile = isset($partyRow['mobile_no']) ? $partyRow['mobile_no'] : '';
		$partyGst = isset($partyRow['gst']) ? $partyRow['gst'] : '';
	}
	$html = '<html><head><style>' . $css . '</style></head><body>';
	$html .= '<table><tr><td colspan="6" class="title">' . $docTitle . '</td></tr><tr>'
		. '<td colspan="3"><b>Party / Buyer</b><br><b>' . htmlspecialchars($partyLabel) . '</b><br>'
		. htmlspecialchars($partyAddr)
		. ($partyMobile != '' ? '<br>Mobile: ' . htmlspecialchars($partyMobile) : '')
		. ($partyGst != '' ? '<br>GSTIN: ' . htmlspecialchars($partyGst) : '') . '</td>'
		. '<td colspan="3"><b>From:</b> ' . htmlspecialchars($pi_company) . '<br>'
		. ($pi_gst != '' ? '<b>GSTIN:</b> ' . htmlspecialchars($pi_gst) . '<br>' : '')
		. '<b>Print Date:</b> ' . date('d-M-Y') . '</td></tr></table>';
	$html .= '<table><tr class="th"><th>Sr</th><th>Order No</th><th>Date</th><th>Order Amount</th><th>Status</th><th>Received</th></tr>'
		. $rowsHtml
		. '<tr class="gray"><td colspan="3" class="tr"><b>Total Order</b></td><td class="tr"><b>' . number_format($totalOrder, 2) . '</b></td>'
		. '<td class="tr"><b>Total Received</b></td><td class="tr"><b>' . number_format($totalPaid, 2) . '</b></td></tr>'
		. '<tr class="gray"><td colspan="5" class="tr"><b>Pending</b></td><td class="tr pend"><b>' . number_format($totalPending, 2) . '</b></td></tr></table>';
	$html .= '</body></html>';
	$fileBase = 'CP_Payment_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $partyLabel) . '_' . date('Ymd_His');
	$shareTitle = 'Payment Receive Statement - ' . $partyLabel;
	$shareText = "Payment Receive Statement\nParty: " . $partyLabel . "\nFrom: " . $pi_company;
} else {
	$docTitle = 'PARTY LEDGER';
	$ledgerData = cp_build_customer_ledger($db, $cpFilter, $partyFilter);
	$ledger = isset($ledgerData[0]) ? $ledgerData[0] : array();
	$opening = isset($ledgerData[1]) ? $ledgerData[1] : 0;
	$bal = $opening;
	$totalDr = 0;
	$totalCr = 0;
	$rowsHtml = '';
	foreach ($ledger as $row) {
		$bal += $row['debit'] - $row['credit'];
		$totalDr += $row['debit'];
		$totalCr += $row['credit'];
		$balLbl = number_format(abs($bal), 2) . ($bal >= 0 ? ' Dr' : ' Cr');
		$rowsHtml .= '<tr><td class="tc">' . date('d-m-Y', strtotime($row['date'])) . '</td>'
			. '<td>' . htmlspecialchars($row['particular']) . '</td>'
			. '<td class="tc">' . htmlspecialchars($row['vch']) . '</td>'
			. '<td class="tr">' . ($row['debit'] > 0 ? number_format($row['debit'], 2) : '') . '</td>'
			. '<td class="tr">' . ($row['credit'] > 0 ? number_format($row['credit'], 2) : '') . '</td>'
			. '<td class="tr">' . $balLbl . '</td></tr>';
	}
	if ($rowsHtml == '') {
		$rowsHtml = '<tr><td colspan="6" class="tc">No ledger entries.</td></tr>';
	}
	$closingAbs = abs($bal);
	$closingSide = ($bal >= 0) ? 'Dr' : 'Cr';
	$html = '<html><head><style>' . $css . '</style></head><body>';
	$html .= '<table><tr><td colspan="6" class="title">' . $docTitle . '</td></tr><tr>'
		. '<td colspan="3"><b>Party</b><br><b>' . htmlspecialchars($partyLabel) . '</b></td>'
		. '<td colspan="3"><b>From:</b> ' . htmlspecialchars($pi_company) . '<br>'
		. ($pi_gst != '' ? '<b>GSTIN:</b> ' . htmlspecialchars($pi_gst) . '<br>' : '')
		. '<b>Print Date:</b> ' . date('d-M-Y') . '</td></tr></table>';
	$html .= '<table><tr class="th"><th>Date</th><th>Particulars</th><th>Voucher</th><th>Debit</th><th>Credit</th><th>Balance</th></tr>'
		. $rowsHtml
		. '<tr class="gray"><td colspan="3" class="tr"><b>Total</b></td><td class="tr"><b>' . number_format($totalDr, 2) . '</b></td>'
		. '<td class="tr"><b>' . number_format($totalCr, 2) . '</b></td><td></td></tr>'
		. '<tr class="gray"><td colspan="5" class="tr"><b>Closing Balance</b></td><td class="tr"><b>' . number_format($closingAbs, 2) . ' ' . $closingSide . '</b></td></tr></table>';
	$html .= '</body></html>';
	$fileBase = 'CP_Ledger_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $partyLabel) . '_' . date('Ymd_His');
	$shareTitle = 'Party Ledger - ' . $partyLabel;
	$shareText = "Party Ledger\nParty: " . $partyLabel . "\nFrom: " . $pi_company;
}

/* Temporary PDF folder */
$tmpRel = 'inquiry_documents/cp_share_tmp';
$saveDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $tmpRel) . DIRECTORY_SEPARATOR;
if (!is_dir($saveDir)) {
	if (!@mkdir($saveDir, 0777, true)) {
		cp_share_json(array('ack' => 0, 'ack_msg' => 'Cannot create temp folder on server.'));
	}
}
if (!is_writable($saveDir)) {
	cp_share_json(array('ack' => 0, 'ack_msg' => 'Temp folder not writable on server.'));
}

/* Cleanup temp PDFs older than 24 hours */
$now = time();
$oldFiles = @glob($saveDir . '*.pdf');
if ($oldFiles) {
	foreach ($oldFiles as $old) {
		if (is_file($old) && ($now - @filemtime($old)) > 86400) {
			@unlink($old);
		}
	}
}

$fileName = $fileBase . '_' . substr(md5(uniqid((string) mt_rand(), true)), 0, 8) . '.pdf';
$savePath = $saveDir . $fileName;
$mpdfFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mpdf60' . DIRECTORY_SEPARATOR . 'mpdf.php';

if (!file_exists($mpdfFile)) {
	cp_share_json(array('ack' => 0, 'ack_msg' => 'mPDF library missing on server (mpdf60).'));
}

ob_start();
include_once $mpdfFile;
$junk = ob_get_clean();

if (!class_exists('mPDF')) {
	cp_share_json(array('ack' => 0, 'ack_msg' => 'mPDF class not loaded.'));
}

ob_start();
$mpdf = new mPDF('', 'A4', 10, 'Arial', 8, 8, 8, 8, 0, 0, 'P');
$mpdf->WriteHTML($html);
$mpdf->Output($savePath, 'F');
$junk2 = ob_get_clean();

if (!file_exists($savePath) || filesize($savePath) < 50) {
	cp_share_json(array('ack' => 0, 'ack_msg' => 'PDF file was not created. Check mPDF / folder permissions.'));
}

$fileUrl = $baseAdmin . '/' . $tmpRel . '/' . rawurlencode($fileName);

cp_share_json(array(
	'ack' => 1,
	'ack_msg' => 'PDF ready',
	'file_url' => $fileUrl,
	'file_name' => $fileName,
	'phone' => $cpPhoneDigits,
	'title' => $shareTitle,
	'text' => $shareText,
	'pdf_ok' => 1,
));
