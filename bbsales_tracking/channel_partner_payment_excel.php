<?php
/**
 * Channel Partner — Receive Payment list (Export Excel).
 * Uses Excel5 (.xls) — no ZipArchive required on server.
 */
$page_id = 565;
$page_slug = 'channel_partner_payment';
include("connect.php");
include('PHPExcel/IOFactory.php');

if (!function_exists('cp_is_channel_partner_login') || !cp_is_channel_partner_login($db)) {
	die("Access denied. Channel Partner login required.");
}

$cp_id = (int) cp_get_login_channel_partner_id();
$cp_name = $db->rp_getValue("executive", "company_name", "id='" . $cp_id . "'", 0);
$party_id = isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : 0;

if ($party_id <= 0) {
	die("Please select a Party first.");
}

$party = $db->rp_getData(
	"channel_partner_customer",
	"id,company_name,person_name,mobile_no",
	"id='" . $party_id . "' AND channel_partner_id='" . $cp_id . "' AND isDelete=0",
	"",
	0
);
$partyRow = ($party) ? mysqli_fetch_assoc($party) : null;
if (!$partyRow) {
	die("Invalid Party.");
}

$orders = array();
$or = $db->rp_getData(
	"orders",
	"id,order_no,order_date,grand_total,payment_received_flag,payment_received_amount,status",
	"customer_id='" . $cp_id . "' AND channel_partner_order_flag=1 AND channel_partner_customer_id='" . $party_id . "' AND isDelete=0 AND status NOT IN (-2,3)",
	"id DESC",
	0
);
if ($or) {
	while ($o = mysqli_fetch_assoc($or)) {
		$orders[] = $o;
	}
}

$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->setActiveSheetIndex(0);
$sheet->setTitle('Receive Payment');

$sheet->setCellValue('A1', 'Receive Payment — Against Order');
$sheet->mergeCells('A1:F1');
$sheet->setCellValue('A2', 'Channel Partner');
$sheet->setCellValue('B2', $cp_name);
$sheet->setCellValue('A3', 'Party');
$sheet->setCellValue('B3', $partyRow['company_name'] . (!empty($partyRow['person_name']) ? ' / ' . $partyRow['person_name'] : '') . (!empty($partyRow['mobile_no']) ? ' (' . $partyRow['mobile_no'] . ')' : ''));
$sheet->setCellValue('A4', 'Exported On');
$sheet->setCellValue('B4', date('d-m-Y h:i A'));

$headers = array('Sr No', 'Order No', 'Date', 'Order Amount', 'Payment Status', 'Received Amount');
$col = 'A';
foreach ($headers as $h) {
	$sheet->setCellValue($col . '6', $h);
	$col++;
}
$sheet->getStyle('A6:F6')->getFont()->setBold(true);

$rowCount = 7;
$sr = 0;
$totalOrder = 0;
$totalPaid = 0;
foreach ($orders as $o) {
	$sr++;
	$paidFlag = isset($o['payment_received_flag']) ? (int) $o['payment_received_flag'] : 0;
	$orderAmt = (float) $o['grand_total'];
	$paidAmt = ($paidFlag === 1) ? (float) $o['payment_received_amount'] : 0;
	$totalOrder += $orderAmt;
	$totalPaid += $paidAmt;

	$sheet->setCellValue('A' . $rowCount, $sr);
	$sheet->setCellValue('B' . $rowCount, $o['order_no']);
	$sheet->setCellValue('C' . $rowCount, date('d-m-Y', strtotime($o['order_date'])));
	$sheet->setCellValue('D' . $rowCount, number_format($orderAmt, 2, '.', ''));
	$sheet->setCellValue('E' . $rowCount, ($paidFlag === 1) ? 'RECEIVED' : 'PENDING');
	$sheet->setCellValue('F' . $rowCount, number_format($paidAmt, 2, '.', ''));
	$rowCount++;
}

$sheet->setCellValue('A' . $rowCount, '');
$sheet->setCellValue('B' . $rowCount, '');
$sheet->setCellValue('C' . $rowCount, 'Total');
$sheet->setCellValue('D' . $rowCount, number_format($totalOrder, 2, '.', ''));
$sheet->setCellValue('E' . $rowCount, '');
$sheet->setCellValue('F' . $rowCount, number_format($totalPaid, 2, '.', ''));
$sheet->getStyle('C' . $rowCount . ':F' . $rowCount)->getFont()->setBold(true);

foreach (range('A', 'F') as $c) {
	$sheet->getColumnDimension($c)->setAutoSize(true);
}

$safeParty = preg_replace('/[^A-Za-z0-9_\-]/', '_', $partyRow['company_name']);
$file_name = "CP_Receive_Payment_" . $safeParty . "_" . date("d-m-Y") . ".xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment;filename=\"" . $file_name . "\"");
header("Cache-Control: max-age=0");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
require_once 'disconnect.php';
exit;
?>
