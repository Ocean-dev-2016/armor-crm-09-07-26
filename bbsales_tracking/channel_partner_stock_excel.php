<?php
/**
 * Channel Partner — My Stock Excel export (.xls / Excel5).
 * view=inout  Inward / Outward ledger
 * view=main   Main stock (product & code)
 */
$page_id = 650;
$page_slug = 'channel_partner_stock';
include("connect.php");
require_once dirname(__FILE__) . '/../include/class.channel_partner_stock.php';
include('PHPExcel/IOFactory.php');

$is_cp = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
$cp_login_id = $is_cp ? (int) cp_get_login_channel_partner_id() : 0;
$is_admin = (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0);

if (!$is_cp && !$is_admin) {
	die("Access denied.");
}

$cpFilter = $is_cp ? $cp_login_id : (isset($_REQUEST['cp_id']) ? (int) $_REQUEST['cp_id'] : 0);
$view = isset($_REQUEST['view']) ? strtolower(trim($_REQUEST['view'])) : 'inout';
if ($view !== 'main') {
	$view = 'inout';
}

if ($cpFilter <= 0) {
	die("Select Channel Partner.");
}

$cp_name = $db->rp_getValue("executive", "company_name", "id='" . $cpFilter . "'", 0);
$stockObj = new ChannelPartnerStock($db);
$stockObj->backfillMissingOutward($cpFilter);

$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->setActiveSheetIndex(0);

$sheet->setCellValue('A2', 'Channel Partner');
$sheet->setCellValue('B2', $cp_name ? $cp_name : '');
$sheet->setCellValue('A3', 'Exported On');
$sheet->setCellValue('B3', date('d-m-Y h:i A'));

if ($view === 'main') {
	$sheet->setTitle('Main Stock');
	$sheet->setCellValue('A1', 'My Stock — Main Stock (Product & Code)');
	$sheet->mergeCells('A1:C1');
	$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

	$headers = array('Sr.', 'Product', 'Available Qty');
	$col = 'A';
	foreach ($headers as $h) {
		$sheet->setCellValue($col . '5', $h);
		$col++;
	}
	$sheet->getStyle('A5:C5')->getFont()->setBold(true);

	$rows = $stockObj->getMainStockByProductCode($cpFilter);
	$rowCount = 6;
	$sr = 0;
	foreach ($rows as $r) {
		$sr++;
		$code = (isset($r['catno']) && $r['catno'] !== '' && $r['catno'] !== '-') ? $r['catno'] : '';
		$label = $code !== '' ? ($code . ' - ' . $r['pro_name']) : $r['pro_name'];
		$sheet->setCellValue('A' . $rowCount, $sr);
		$sheet->setCellValue('B' . $rowCount, $label);
		$sheet->setCellValue('C' . $rowCount, $r['total_qty']);
		$rowCount++;
	}
	if ($sr === 0) {
		$sheet->setCellValue('A6', 'No stock found.');
		$sheet->mergeCells('A6:C6');
	}
	foreach (range('A', 'C') as $c) {
		$sheet->getColumnDimension($c)->setAutoSize(true);
	}
	$file_name = "CP_Main_Stock_" . date("d-m-Y") . ".xls";
} else {
	$sheet->setTitle('Inward Outward');
	$sheet->setCellValue('A1', 'My Stock — Inward / Outward stock ledger (Bill No, Date)');
	$sheet->mergeCells('A1:I1');
	$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

	$headers = array('Sr.', 'Date', 'Bill No', 'Type', 'Product', 'Inward', 'Outward', 'Balance', 'Remark');
	$col = 'A';
	foreach ($headers as $h) {
		$sheet->setCellValue($col . '5', $h);
		$col++;
	}
	$sheet->getStyle('A5:I5')->getFont()->setBold(true);

	$moves = $stockObj->getStockMovements($cpFilter);
	$rowCount = 6;
	$sr = 0;
	$running = 0;
	foreach ($moves as $m) {
		$sr++;
		$running += (float) $m['qty'];
		$isIn = ($m['txn_type'] === 'in' || (float) $m['qty_in'] > 0);
		$typeLbl = $isIn ? 'INWARD' : 'OUTWARD';
		$dateShow = '';
		if (!empty($m['date']) && $m['date'] != '0000-00-00') {
			$dateShow = date('d-m-Y', strtotime($m['date']));
		}
		$billShow = $m['bill_no'] !== '' ? $m['bill_no'] : '-';
		$code = (isset($m['catno']) && $m['catno'] !== '' && $m['catno'] !== '-') ? $m['catno'] : '';
		$label = $code !== '' ? ($code . ' - ' . $m['pro_name']) : $m['pro_name'];

		$sheet->setCellValue('A' . $rowCount, $sr);
		$sheet->setCellValue('B' . $rowCount, $dateShow);
		$sheet->setCellValue('C' . $rowCount, $billShow);
		$sheet->setCellValue('D' . $rowCount, $typeLbl);
		$sheet->setCellValue('E' . $rowCount, $label);
		$sheet->setCellValue('F' . $rowCount, $m['qty_in'] > 0 ? $m['qty_in'] : '');
		$sheet->setCellValue('G' . $rowCount, $m['qty_out'] > 0 ? $m['qty_out'] : '');
		$sheet->setCellValue('H' . $rowCount, $running);
		$sheet->setCellValue('I' . $rowCount, $m['remark']);
		$rowCount++;
	}

	if ($sr === 0) {
		$sheet->setCellValue('A6', 'No inward / outward entries found.');
		$sheet->mergeCells('A6:I6');
	} else {
		$sheet->setCellValue('A' . $rowCount, '');
		$sheet->setCellValue('G' . $rowCount, 'Closing Available Qty');
		$sheet->setCellValue('H' . $rowCount, $running);
		$sheet->getStyle('G' . $rowCount . ':H' . $rowCount)->getFont()->setBold(true);
	}

	foreach (range('A', 'I') as $c) {
		$sheet->getColumnDimension($c)->setAutoSize(true);
	}
	$file_name = "CP_Inward_Outward_Stock_" . date("d-m-Y") . ".xls";
}

$safeCp = preg_replace('/[^A-Za-z0-9_\-]/', '_', $cp_name ? $cp_name : 'CP');
$file_name = $safeCp . '_' . $file_name;

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment;filename=\"" . $file_name . "\"");
header("Cache-Control: max-age=0");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
require_once 'disconnect.php';
exit;
?>
