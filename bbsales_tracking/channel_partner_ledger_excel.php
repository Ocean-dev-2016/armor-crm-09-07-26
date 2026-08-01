<?php
/**
 * Channel Partner — Party Ledger Export Excel (.xls / Excel5).
 */
$page_id = 565;
$page_slug = 'channel_partner_ledger';
include("connect.php");
include("include/channel_partner_ledger_data.php");
include('PHPExcel/IOFactory.php');

$is_cp = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
$cp_login_id = $is_cp ? (int) cp_get_login_channel_partner_id() : 0;
$is_admin = (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0);

if (!$is_cp && !$is_admin) {
	die("Access denied.");
}

$cpFilter = $is_cp ? $cp_login_id : (isset($_REQUEST['cp_id']) ? (int) $_REQUEST['cp_id'] : 0);
$partyFilter = isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : 0;

if ($cpFilter <= 0) {
	die("Select Channel Partner.");
}

$cp_name = $db->rp_getValue("executive", "company_name", "id='" . $cpFilter . "'", 0);
$partyLabel = 'All Parties';
if ($partyFilter > 0) {
	$pname = $db->rp_getValue(
		"channel_partner_customer",
		"company_name",
		"id='" . $partyFilter . "' AND channel_partner_id='" . $cpFilter . "' AND isDelete=0",
		0
	);
	if ($pname != '') {
		$partyLabel = $pname;
	}
}

list($ledger, $opening) = cp_build_customer_ledger($db, $cpFilter, $partyFilter);

$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->setActiveSheetIndex(0);
$sheet->setTitle('Party Ledger');

$sheet->setCellValue('A1', 'CP Customer Ledger — Party Ledger');
$sheet->mergeCells('A1:F1');
$sheet->setCellValue('A2', 'Channel Partner');
$sheet->setCellValue('B2', $cp_name);
$sheet->setCellValue('A3', 'Party');
$sheet->setCellValue('B3', $partyLabel);
$sheet->setCellValue('A4', 'Exported On');
$sheet->setCellValue('B4', date('d-m-Y h:i A'));

$headers = array('Date', 'Particulars', 'Voucher', 'Debit', 'Credit', 'Balance');
$col = 'A';
foreach ($headers as $h) {
	$sheet->setCellValue($col . '6', $h);
	$col++;
}
$sheet->getStyle('A6:F6')->getFont()->setBold(true);

$rowCount = 7;
$bal = $opening;
$totalDr = 0;
$totalCr = 0;
foreach ($ledger as $row) {
	$bal += $row['debit'] - $row['credit'];
	$totalDr += $row['debit'];
	$totalCr += $row['credit'];
	$balLbl = number_format(abs($bal), 2, '.', '') . ($bal >= 0 ? ' Dr' : ' Cr');

	$sheet->setCellValue('A' . $rowCount, date('d-m-Y', strtotime($row['date'])));
	$sheet->setCellValue('B' . $rowCount, $row['particular']);
	$sheet->setCellValue('C' . $rowCount, $row['vch']);
	$sheet->setCellValue('D' . $rowCount, $row['debit'] > 0 ? number_format($row['debit'], 2, '.', '') : '');
	$sheet->setCellValue('E' . $rowCount, $row['credit'] > 0 ? number_format($row['credit'], 2, '.', '') : '');
	$sheet->setCellValue('F' . $rowCount, $balLbl);
	$rowCount++;
}

$sheet->setCellValue('A' . $rowCount, '');
$sheet->setCellValue('B' . $rowCount, '');
$sheet->setCellValue('C' . $rowCount, 'Total');
$sheet->setCellValue('D' . $rowCount, number_format($totalDr, 2, '.', ''));
$sheet->setCellValue('E' . $rowCount, number_format($totalCr, 2, '.', ''));
$sheet->setCellValue('F' . $rowCount, '');
$sheet->getStyle('C' . $rowCount . ':E' . $rowCount)->getFont()->setBold(true);
$rowCount++;

$sheet->setCellValue('C' . $rowCount, 'Closing Balance');
$sheet->setCellValue('F' . $rowCount, number_format(abs($bal), 2, '.', '') . ($bal >= 0 ? ' Dr' : ' Cr'));
$sheet->getStyle('C' . $rowCount . ':F' . $rowCount)->getFont()->setBold(true);

foreach (range('A', 'F') as $c) {
	$sheet->getColumnDimension($c)->setAutoSize(true);
}

$safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $partyLabel);
$file_name = "CP_Party_Ledger_" . $safeName . "_" . date("d-m-Y") . ".xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment;filename=\"" . $file_name . "\"");
header("Cache-Control: max-age=0");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
require_once 'disconnect.php';
exit;
?>
