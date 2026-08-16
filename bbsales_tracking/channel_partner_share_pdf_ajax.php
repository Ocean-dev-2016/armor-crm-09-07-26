<?php
/**
 * CP Share PDF — generate PDF (mPDF), store in inquiry_documents (temp), return JSON.
 * Pattern matches working reports (expense_genReport_ajax / productStockReport_gen_ajax).
 * PHP 5.6 compatible.
 */
$page_id = 565;
$page_slug = 'channel_partner_payment';
error_reporting(0);
@ini_set('display_errors', 0);

/* Skip page-rights redirect; we verify login ourselves below */
$_REQUEST['skip_security'] = 1224;

include("connect.php");
include("include/channel_partner_ledger_data.php");

if (!headers_sent()) {
	header('Content-Type: application/json; charset=utf-8');
	header('Cache-Control: no-store, no-cache, must-revalidate');
}

function cp_share_json($arr)
{
	echo json_encode($arr);
	exit;
}

/* Must be logged in */
if (!isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) || $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] == '') {
	cp_share_json(array('ack' => 0, 'ack_msg' => 'Please login again.'));
}

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

$css = 'table{width:100%;border-collapse:collapse;font-size:11px;font-family:dejavusans,Arial,sans-serif;}
td,th{border:1px solid #595959;padding:5px 6px;vertical-align:top;}
.th{background:#1a6b8a;color:#fff;text-align:center;}
.title{background:#A9A9A9;text-align:center;font-weight:bold;font-size:14px;}
.tr{text-align:right;}.tc{text-align:center;}
.ok{color:#1e7e34;font-weight:bold;}.pend{color:#c0392b;font-weight:bold;}
.gray{background:#f0f0f0;}';

if ($type == 'payment') {
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
		$pay = cp_order_payment_state($o['grand_total'], $o['payment_received_amount']);
		$orderAmt = $pay['order_amount'];
		$paidAmt = $pay['paid_amount'];
		$totalOrder += $orderAmt;
		$totalPaid += $paidAmt;
		$totalPending += $pay['remaining_amount'];
		$statusTxt = $pay['status_short'];
		$statusCls = $pay['is_paid'] ? 'ok' : 'pend';
		$rowsHtml .= '<tr><td class="tc">' . $sr . '</td><td><b>' . htmlspecialchars($o['order_no']) . '</b></td>'
			. '<td class="tc">' . date('d-m-Y', strtotime($o['order_date'])) . '</td>'
			. '<td class="tr">' . number_format($orderAmt, 2) . '</td>'
			. '<td class="tc ' . $statusCls . '">' . $statusTxt . '</td>'
			. '<td class="tr">' . number_format($paidAmt, 2) . '</td>'
			. '<td class="tr pend">' . number_format($pay['remaining_amount'], 2) . '</td></tr>';
	}
	if ($rowsHtml == '') {
		$rowsHtml = '<tr><td colspan="7" class="tc">No orders found.</td></tr>';
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
	$html .= '<table><tr><td colspan="7" class="title">PAYMENT RECEIVE STATEMENT</td></tr><tr>'
		. '<td colspan="4"><b>Party / Buyer</b><br><b>' . htmlspecialchars($partyLabel) . '</b><br>'
		. htmlspecialchars($partyAddr)
		. ($partyMobile != '' ? '<br>Mobile: ' . htmlspecialchars($partyMobile) : '')
		. ($partyGst != '' ? '<br>GSTIN: ' . htmlspecialchars($partyGst) : '') . '</td>'
		. '<td colspan="3"><b>From:</b> ' . htmlspecialchars($pi_company) . '<br>'
		. ($pi_gst != '' ? '<b>GSTIN:</b> ' . htmlspecialchars($pi_gst) . '<br>' : '')
		. '<b>Print Date:</b> ' . date('d-M-Y') . '</td></tr></table>';
	$html .= '<table><tr class="th"><th>Sr</th><th>Order No</th><th>Date</th><th>Order Amount</th><th>Status</th><th>Received</th><th>Pending</th></tr>'
		. $rowsHtml
		. '<tr class="gray"><td colspan="3" class="tr"><b>Total Order</b></td><td class="tr"><b>' . number_format($totalOrder, 2) . '</b></td>'
		. '<td class="tr"><b>Total Received</b></td><td class="tr"><b>' . number_format($totalPaid, 2) . '</b></td>'
		. '<td class="tr pend"><b>' . number_format($totalPending, 2) . '</b></td></tr></table>';
	$html .= '</body></html>';
	$fileBase = 'CP_Payment_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $partyLabel) . '_' . date('Ymd_His');
	$shareTitle = 'Payment Receive Statement - ' . $partyLabel;
	$shareText = "Payment Receive Statement\nParty: " . $partyLabel . "\nFrom: " . $pi_company;
} else {
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
	$html .= '<table><tr><td colspan="6" class="title">PARTY LEDGER</td></tr><tr>'
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

/* Same folder used by other CRM PDF reports */
$saveDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'inquiry_documents' . DIRECTORY_SEPARATOR;
if (!is_dir($saveDir)) {
	@mkdir($saveDir, 0777, true);
}
if (!is_dir($saveDir) || !is_writable($saveDir)) {
	cp_share_json(array('ack' => 0, 'ack_msg' => 'inquiry_documents folder missing or not writable.'));
}

/* Cleanup old share PDFs (24 hours) */
$now = time();
$oldFiles = @glob($saveDir . 'CP_*.pdf');
if ($oldFiles) {
	foreach ($oldFiles as $old) {
		if (is_file($old) && ($now - @filemtime($old)) > 86400) {
			@unlink($old);
		}
	}
}

$fileName = $fileBase . '_' . substr(md5(uniqid((string) mt_rand(), true)), 0, 8) . '.pdf';
$savePath = $saveDir . $fileName;

if (!file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mpdf60' . DIRECTORY_SEPARATOR . 'mpdf.php')) {
	cp_share_json(array('ack' => 0, 'ack_msg' => 'mPDF library missing (mpdf60).'));
}

/* Live host may not have php_mbstring — polyfill before mPDF */
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'mbstring_polyfill.php';

/* Same require style as working expense / stock report ajax */
require('mpdf60/mpdf.php');

$mpdf = new mPDF(
	'',
	'A4',
	10,
	'sans-serif',
	8,
	8,
	8,
	8,
	0,
	0,
	'P'
);
$mpdf->WriteHTML($html);
$mpdf->Output($savePath, 'F');

if (!file_exists($savePath) || @filesize($savePath) < 50) {
	cp_share_json(array('ack' => 0, 'ack_msg' => 'PDF file was not created. Check folder permissions.'));
}

$fileUrl = rtrim(ADMINSITEURL, '/') . '/inquiry_documents/' . $fileName;

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
