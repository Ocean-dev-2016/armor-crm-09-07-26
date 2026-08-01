<?php
/**
 * Generate CP Payment / Ledger PDF for WhatsApp share (actual PDF file, not link).
 * Returns JSON: ack, file_url, file_name, phone (CP number), title
 */
$page_id = 565;
$page_slug = 'channel_partner_payment';
include("connect.php");
include("include/channel_partner_ledger_data.php");
header('Content-Type: application/json; charset=utf-8');

$type = isset($_REQUEST['type']) ? strtolower(trim($_REQUEST['type'])) : '';
if ($type !== 'payment' && $type !== 'ledger') {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Invalid type.'));
	exit;
}

$is_cp = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
$cp_login_id = $is_cp ? (int) cp_get_login_channel_partner_id() : 0;
$is_admin = (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0);

if ($type === 'payment') {
	if (!$is_cp) {
		echo json_encode(array('ack' => 0, 'ack_msg' => 'Channel Partner login required.'));
		exit;
	}
	$cpFilter = $cp_login_id;
} else {
	if (!$is_cp && !$is_admin) {
		echo json_encode(array('ack' => 0, 'ack_msg' => 'Access denied.'));
		exit;
	}
	$cpFilter = $is_cp ? $cp_login_id : (isset($_REQUEST['cp_id']) ? (int) $_REQUEST['cp_id'] : 0);
}

$partyFilter = isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : 0;
if ($type === 'payment' && $partyFilter <= 0) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Select Party first.'));
	exit;
}
if ($cpFilter <= 0) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Channel Partner required.'));
	exit;
}

/* CP WhatsApp number (own number — not party) */
$cpPhone = '';
$ddn = $db->rp_getData(
	"dealer_distributor_network",
	"phone,username",
	"customer_id='" . $cpFilter . "' AND type='3' AND isDelete=0",
	"id DESC",
	0
);
if ($ddn && $dd = mysqli_fetch_assoc($ddn)) {
	$cpPhone = !empty($dd['phone']) ? $dd['phone'] : $dd['username'];
}
if ($cpPhone == '') {
	$cpPhone = $db->rp_getValue("executive", "mobile_no1", "id='" . $cpFilter . "'", 0);
}
if ($cpPhone == '') {
	$cpPhone = $db->rp_getValue("executive", "phone", "id='" . $cpFilter . "'", 0);
}
$cpPhoneDigits = preg_replace('/\D+/', '', (string) $cpPhone);
if (strlen($cpPhoneDigits) === 10) {
	$cpPhoneDigits = '91' . $cpPhoneDigits;
} else if (strlen($cpPhoneDigits) === 12 && substr($cpPhoneDigits, 0, 2) === '91') {
	/* ok */
} else if (strlen($cpPhoneDigits) < 10) {
	$cpPhoneDigits = '';
}

$cp_r = $db->rp_getData("executive", "*", "id='" . $cpFilter . "' AND isDelete=0", "", 0);
$cp = $cp_r ? mysqli_fetch_assoc($cp_r) : array();
$cp_name = isset($cp['company_name']) ? $cp['company_name'] : '';
$pi_company = !empty($cp['cp_print_company_name']) ? $cp['cp_print_company_name'] : $cp_name;
$pi_gst = !empty($cp['cp_print_gst']) ? $cp['cp_print_gst'] : (isset($cp['gst']) ? $cp['gst'] : '');
$headerImgFile = isset($cp['cp_print_header_image']) ? $cp['cp_print_header_image'] : '';
$footerImgFile = isset($cp['cp_print_footer_image']) ? $cp['cp_print_footer_image'] : '';
$headerHtml = '';
$footerHtml = '';
$headerFs = '';
$footerFs = '';
if ($headerImgFile != '' && defined('HEADER_A')) {
	$headerFs = realpath(dirname(__FILE__) . '/' . HEADER_A . $headerImgFile);
	if (!$headerFs && file_exists(HEADER_A . $headerImgFile)) {
		$headerFs = realpath(HEADER_A . $headerImgFile);
	}
	if ($headerFs && file_exists($headerFs)) {
		$headerHtml = '<img src="' . str_replace('\\', '/', $headerFs) . '" style="width:100%;" />';
	}
}
if ($footerImgFile != '' && defined('HEADER_A')) {
	$footerFs = realpath(dirname(__FILE__) . '/' . HEADER_A . $footerImgFile);
	if (!$footerFs && file_exists(HEADER_A . $footerImgFile)) {
		$footerFs = realpath(HEADER_A . $footerImgFile);
	}
	if ($footerFs && file_exists($footerFs)) {
		$footerHtml = '<img src="' . str_replace('\\', '/', $footerFs) . '" style="width:100%;" />';
	}
}

$partyLabel = 'All Parties';
$partyRow = null;
if ($partyFilter > 0) {
	$pr = $db->rp_getData(
		"channel_partner_customer",
		"*",
		"id='" . $partyFilter . "' AND channel_partner_id='" . $cpFilter . "' AND isDelete=0",
		"",
		0
	);
	$partyRow = $pr ? mysqli_fetch_assoc($pr) : null;
	if ($partyRow) {
		$partyLabel = $partyRow['company_name'];
	}
}

$currency = defined('CURR') ? CURR : 'Rs.';
$css = 'table{width:100%;border-collapse:collapse;font-size:11px;font-family:Arial,sans-serif;}
td,th{border:1px solid #595959;padding:5px 6px;vertical-align:top;}
.th{background:#1a6b8a;color:#fff;text-align:center;}
.title{background:#A9A9A9;text-align:center;font-weight:bold;font-size:14px;}
.tr{text-align:right;}.tc{text-align:center;}
.ok{color:#1e7e34;font-weight:bold;}.pend{color:#c0392b;font-weight:bold;}
.gray{background:#f0f0f0;}';

if ($type === 'payment') {
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
		$rowsHtml .= '<tr>
			<td class="tc">' . $sr . '</td>
			<td><b>' . htmlspecialchars($o['order_no']) . '</b></td>
			<td class="tc">' . date('d-m-Y', strtotime($o['order_date'])) . '</td>
			<td class="tr">' . number_format($orderAmt, 2) . '</td>
			<td class="tc ' . ($paidFlag === 1 ? 'ok' : 'pend') . '">' . ($paidFlag === 1 ? 'RECEIVED' : 'PENDING') . '</td>
			<td class="tr">' . number_format($paidAmt, 2) . '</td>
		</tr>';
	}
	if ($rowsHtml === '') {
		$rowsHtml = '<tr><td colspan="6" class="tc">No orders found.</td></tr>';
	}
	$partyAddr = '';
	$partyMobile = '';
	$partyGst = '';
	if ($partyRow) {
		$partyAddr = implode(', ', array_filter(array(
			isset($partyRow['address']) ? $partyRow['address'] : '',
			isset($partyRow['city']) ? $partyRow['city'] : '',
			isset($partyRow['state']) ? $partyRow['state'] : '',
			isset($partyRow['pincode']) ? $partyRow['pincode'] : '',
		)));
		$partyMobile = isset($partyRow['mobile_no']) ? $partyRow['mobile_no'] : '';
		$partyGst = isset($partyRow['gst']) ? $partyRow['gst'] : '';
	}
	$html = '<html><head><style>' . $css . '</style></head><body>';
	$html .= $headerHtml;
	$html .= '<table>
		<tr><td colspan="6" class="title">' . $docTitle . '</td></tr>
		<tr>
			<td colspan="3"><b>Party / Buyer</b><br><b style="font-size:13px;">' . htmlspecialchars($partyLabel) . '</b><br>'
			. htmlspecialchars($partyAddr) . ($partyMobile != '' ? '<br>Mobile: ' . htmlspecialchars($partyMobile) : '')
			. ($partyGst != '' ? '<br>GSTIN: ' . htmlspecialchars($partyGst) : '') . '</td>
			<td colspan="3"><b>From:</b> ' . htmlspecialchars($pi_company) . '<br>'
			. ($pi_gst != '' ? '<b>GSTIN:</b> ' . htmlspecialchars($pi_gst) . '<br>' : '')
			. '<b>Print Date:</b> ' . date('d-M-Y') . '</td>
		</tr>
	</table>
	<table style="margin-top:-1px;">
		<tr class="th">
			<th width="6%">Sr</th><th width="20%">Order No</th><th width="14%">Date</th>
			<th width="18%">Order Amount</th><th width="20%">Status</th><th width="22%">Received</th>
		</tr>' . $rowsHtml . '
		<tr class="gray">
			<td colspan="3" class="tr"><b>Total Order</b></td>
			<td class="tr"><b>' . number_format($totalOrder, 2) . '</b></td>
			<td class="tr"><b>Total Received</b></td>
			<td class="tr"><b>' . number_format($totalPaid, 2) . '</b></td>
		</tr>
		<tr class="gray">
			<td colspan="5" class="tr"><b>Pending</b></td>
			<td class="tr pend"><b>' . number_format($totalPending, 2) . '</b></td>
		</tr>
	</table>';
	$html .= $footerHtml;
	$html .= '</body></html>';
	$fileBase = 'CP_Payment_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $partyLabel) . '_' . date('Ymd_His');
	$shareTitle = 'Payment Receive Statement — ' . $partyLabel;
	$shareText = "Payment Receive Statement\nParty: " . $partyLabel . "\nFrom: " . $pi_company . "\n(Please find PDF attached)";
} else {
	$docTitle = 'PARTY LEDGER';
	list($ledger, $opening) = cp_build_customer_ledger($db, $cpFilter, $partyFilter);
	$bal = $opening;
	$totalDr = 0;
	$totalCr = 0;
	$rowsHtml = '';
	$sr = 0;
	foreach ($ledger as $row) {
		$sr++;
		$bal += $row['debit'] - $row['credit'];
		$totalDr += $row['debit'];
		$totalCr += $row['credit'];
		$balLbl = number_format(abs($bal), 2) . ($bal >= 0 ? ' Dr' : ' Cr');
		$rowsHtml .= '<tr>
			<td class="tc">' . date('d-m-Y', strtotime($row['date'])) . '</td>
			<td>' . htmlspecialchars($row['particular']) . '</td>
			<td class="tc">' . htmlspecialchars($row['vch']) . '</td>
			<td class="tr">' . ($row['debit'] > 0 ? number_format($row['debit'], 2) : '') . '</td>
			<td class="tr">' . ($row['credit'] > 0 ? number_format($row['credit'], 2) : '') . '</td>
			<td class="tr">' . $balLbl . '</td>
		</tr>';
	}
	if ($rowsHtml === '') {
		$rowsHtml = '<tr><td colspan="6" class="tc">No ledger entries.</td></tr>';
	}
	$closingAbs = abs($bal);
	$closingSide = ($bal >= 0) ? 'Dr' : 'Cr';
	$html = '<html><head><style>' . $css . '</style></head><body>';
	$html .= $headerHtml;
	$html .= '<table>
		<tr><td colspan="6" class="title">' . $docTitle . '</td></tr>
		<tr>
			<td colspan="3"><b>Party</b><br><b style="font-size:13px;">' . htmlspecialchars($partyLabel) . '</b></td>
			<td colspan="3"><b>From:</b> ' . htmlspecialchars($pi_company) . '<br>'
			. ($pi_gst != '' ? '<b>GSTIN:</b> ' . htmlspecialchars($pi_gst) . '<br>' : '')
			. '<b>Print Date:</b> ' . date('d-M-Y') . '</td>
		</tr>
	</table>
	<table style="margin-top:-1px;">
		<tr class="th">
			<th width="12%">Date</th><th width="36%">Particulars</th><th width="14%">Voucher</th>
			<th width="12%">Debit</th><th width="12%">Credit</th><th width="14%">Balance</th>
		</tr>' . $rowsHtml . '
		<tr class="gray">
			<td colspan="3" class="tr"><b>Total</b></td>
			<td class="tr"><b>' . number_format($totalDr, 2) . '</b></td>
			<td class="tr"><b>' . number_format($totalCr, 2) . '</b></td>
			<td></td>
		</tr>
		<tr class="gray">
			<td colspan="5" class="tr"><b>Closing Balance</b></td>
			<td class="tr"><b>' . number_format($closingAbs, 2) . ' ' . $closingSide . '</b></td>
		</tr>
	</table>';
	$html .= $footerHtml;
	$html .= '</body></html>';
	$fileBase = 'CP_Ledger_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $partyLabel) . '_' . date('Ymd_His');
	$shareTitle = 'Party Ledger — ' . $partyLabel;
	$shareText = "Party Ledger\nParty: " . $partyLabel . "\nFrom: " . $pi_company . "\n(Please find PDF attached)";
}

$saveDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'inquiry_documents' . DIRECTORY_SEPARATOR;
if (!is_dir($saveDir)) {
	@mkdir($saveDir, 0777, true);
}
if (!is_writable($saveDir)) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'inquiry_documents folder not writable.'));
	exit;
}

$fileName = $fileBase . '.pdf';
$savePath = $saveDir . $fileName;

try {
	require_once dirname(__FILE__) . '/mpdf60/mpdf.php';
	$mpdf = new mPDF('', 'A4', 10, 'Arial', 8, 8, 8, 8, 0, 0, 'P');
	$mpdf->WriteHTML($html);
	$mpdf->Output($savePath, 'F');
} catch (Exception $e) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'PDF create failed: ' . $e->getMessage()));
	exit;
}

if (!file_exists($savePath) || filesize($savePath) < 50) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'PDF file was not created.'));
	exit;
}

$fileUrl = rtrim(ADMINSITEURL, '/') . '/inquiry_documents/' . $fileName;

echo json_encode(array(
	'ack' => 1,
	'ack_msg' => 'PDF ready',
	'file_url' => $fileUrl,
	'file_name' => $fileName,
	'phone' => $cpPhoneDigits,
	'title' => $shareTitle,
	'text' => $shareText,
));
exit;
?>
