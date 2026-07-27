<?php
/**
 * Remark Wise Report Excel — one file, sheet-wise:
 * Summary | Visit Detail | C1 Consultant | C2 Consultant | High Rate Form
 */
$page_id = 671;
$page_slug = 'remark_wise_report';
include("connect.php");
require_once("../include/class.remark_analysis_report.php");
require_once("PHPExcel/IOFactory.php");

@set_time_limit(0);
@ini_set('max_execution_time', '0');
@ini_set('memory_limit', '512M');
@ini_set('display_errors', '0');
@ignore_user_abort(true);

function rar_excel_json_exit($payload)
{
	while (ob_get_level()) {
		@ob_end_clean();
	}
	if (!headers_sent()) {
		header('Content-Type: application/json; charset=utf-8');
	}
	echo json_encode($payload);
	exit;
}

register_shutdown_function(function () {
	$err = error_get_last();
	if (!$err) {
		return;
	}
	$fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
	if (!in_array($err['type'], $fatalTypes, true)) {
		return;
	}
	rar_excel_json_exit(array(
		'ack' => 0,
		'ack_msg' => 'Excel failed: ' . $err['message'] . ' (line ' . $err['line'] . ')',
	));
});

$canExport = (
	(isset($rights['export_excel_flag']) && (int) $rights['export_excel_flag'] === 1)
	|| (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0)
);
if (!$canExport) {
	rar_excel_json_exit(array('ack' => 0, 'ack_msg' => 'Excel export permission required.'));
}

function rar_excel_payment($value)
{
	if ($value === '0' || $value === 0) {
		return 'Advance';
	}
	if ($value === '1' || $value === 1) {
		return '30 Days';
	}
	return (string) $value;
}

function rar_excel_write_header($sheet, $headers, $row, $style)
{
	$col = 0;
	foreach ($headers as $header) {
		$cell = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
		$sheet->setCellValue($cell, $header);
		$sheet->getStyle($cell)->applyFromArray($style);
		$col++;
	}
}

function rar_excel_autosize($sheet, $colCount)
{
	for ($i = 0; $i < $colCount; $i++) {
		$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setAutoSize(true);
	}
}

$report = new RemarkAnalysisReport($db);
$data = $report->build(
	isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : "",
	isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : "",
	isset($_REQUEST['employee_ids']) ? $_REQUEST['employee_ids'] : "",
	isset($_REQUEST['customer_ids']) ? $_REQUEST['customer_ids'] : "",
	isset($_REQUEST['remark_code']) ? $_REQUEST['remark_code'] : "",
	$rights
);

$visits = $report->attachFormsToVisits($data['visits']);
$hierarchy = $data['hierarchy'];
$remarkLabels = $data['remark_labels'];
$reasonLabels = $data['reason_labels'];
$summary = $data['summary'];
$fromLabel = date("d/m/Y", strtotime($data['range']['from']));
$toLabel = date("d/m/Y", strtotime($data['range']['to']));

$headerStyle = array(
	"font" => array("bold" => true, "color" => array("rgb" => "FFFFFF")),
	"fill" => array("type" => PHPExcel_Style_Fill::FILL_SOLID, "color" => array("rgb" => "3598DC")),
	"alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
);
$titleStyle = array(
	"font" => array("bold" => true, "size" => 13),
	"fill" => array("type" => PHPExcel_Style_Fill::FILL_SOLID, "color" => array("rgb" => "DCE6F1")),
);
$parentStyle = array(
	"font" => array("bold" => true),
	"fill" => array("type" => PHPExcel_Style_Fill::FILL_SOLID, "color" => array("rgb" => "EEF6FF")),
);
$cStyle = array(
	"font" => array("bold" => true, "color" => array("rgb" => "FFFFFF")),
	"fill" => array("type" => PHPExcel_Style_Fill::FILL_SOLID, "color" => array("rgb" => "1A7A3A")),
	"alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
);
$eStyle = array(
	"font" => array("bold" => true, "color" => array("rgb" => "FFFFFF")),
	"fill" => array("type" => PHPExcel_Style_Fill::FILL_SOLID, "color" => array("rgb" => "C85A12")),
	"alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
);

$book = new PHPExcel();
$book->removeSheetByIndex(0);
$sheetIndex = 0;

/* ---------- Sheet 1: Summary ---------- */
$sheetSummary = new PHPExcel_Worksheet($book);
$sheetSummary->setTitle("Summary");
$book->addSheet($sheetSummary, $sheetIndex++);
$sheetSummary->setCellValue("A1", "Remark Wise Report - Summary");
$sheetSummary->mergeCells("A1:C1");
$sheetSummary->getStyle("A1")->applyFromArray($titleStyle);
$sheetSummary->setCellValue("A2", "Period: " . $fromLabel . " to " . $toLabel);
$sheetSummary->setCellValue("A3", "Total Visits: " . (int) $data['total_visits']);
rar_excel_write_header($sheetSummary, array("Code", "Description", "Count"), 5, $headerStyle);
$r = 6;
foreach ($hierarchy as $parent => $children) {
	$parentCount = isset($summary['parents'][$parent]) ? (int) $summary['parents'][$parent] : 0;
	$sheetSummary->setCellValue("A" . $r, $parent . " -");
	$sheetSummary->setCellValue("B" . $r, isset($remarkLabels[$parent]) ? $remarkLabels[$parent] : "");
	$sheetSummary->setCellValue("C" . $r, $parentCount);
	$sheetSummary->getStyle("A" . $r . ":C" . $r)->applyFromArray($parentStyle);
	$r++;
	foreach ($children as $child) {
		$childCount = isset($summary['children'][$child]) ? (int) $summary['children'][$child] : 0;
		$sheetSummary->setCellValue("A" . $r, $child);
		$sheetSummary->setCellValue("B" . $r, isset($reasonLabels[$child]) ? $reasonLabels[$child] : "");
		$sheetSummary->setCellValue("C" . $r, $childCount);
		$r++;
	}
}
if ((int) $summary['unknown'] > 0) {
	$sheetSummary->setCellValue("A" . $r, "-");
	$sheetSummary->setCellValue("B" . $r, "Unknown / No Remark Code");
	$sheetSummary->setCellValue("C" . $r, (int) $summary['unknown']);
}
rar_excel_autosize($sheetSummary, 3);

/* ---------- Sheet 2: Visit Detail ---------- */
$sheetVisits = new PHPExcel_Worksheet($book);
$sheetVisits->setTitle("Visit Detail");
$book->addSheet($sheetVisits, $sheetIndex++);
$sheetVisits->setCellValue("A1", "Remark Wise Visit Detail");
$sheetVisits->mergeCells("A1:J1");
$sheetVisits->getStyle("A1")->applyFromArray($titleStyle);
$sheetVisits->setCellValue("A2", "Period: " . $fromLabel . " to " . $toLabel);
$visitHeaders = array("#", "Date", "Sales Person", "Customer", "Customer Code", "Visit Duration", "Remark", "Reason", "Description", "Stop Remark", "Status", "Form Available");
rar_excel_write_header($sheetVisits, $visitHeaders, 4, $headerStyle);
$vr = 5;
$sr = 0;
foreach ($visits as $visit) {
	$sr++;
	$desc = "-";
	if ($visit['reason_code'] != "" && $visit['reason_label'] != "") {
		$desc = $visit['reason_code'] . ": " . $visit['reason_label'];
	} else if ($visit['remark_code'] != "" && $visit['remark_label'] != "") {
		$desc = $visit['remark_code'] . ": " . $visit['remark_label'];
	}
	$dateLabel = ($visit['visit_date'] != "") ? date("d/m/Y", strtotime($visit['visit_date'])) : "-";
	$formAvail = "-";
	if (!empty($visit['has_consultant_form']) && !empty($visit['has_high_rate_form'])) {
		$formAvail = "Consultant + High Rate";
	} else if (!empty($visit['has_consultant_form'])) {
		$formAvail = "Consultant Form";
	} else if (!empty($visit['has_high_rate_form'])) {
		$formAvail = "High Rate Form";
	}
	$durMins = isset($visit['duration_minutes']) ? $visit['duration_minutes'] : null;
	$durLabel = "-";
	if ($durMins !== null && $durMins !== "") {
		$durMins = (int) $durMins;
		$dh = (int) floor($durMins / 60);
		$dm = $durMins % 60;
		$durLabel = ($dh > 0) ? ($dh . "h " . $dm . "m") : ($dm . " min");
	}
	$sheetVisits->setCellValue("A" . $vr, $sr);
	$sheetVisits->setCellValue("B" . $vr, $dateLabel);
	$sheetVisits->setCellValue("C" . $vr, $visit['sales_person']);
	$sheetVisits->setCellValue("D" . $vr, $visit['customer_name']);
	$sheetVisits->setCellValue("E" . $vr, $visit['customer_code']);
	$sheetVisits->setCellValue("F" . $vr, $durLabel);
	$sheetVisits->setCellValue("G" . $vr, $visit['remark_code']);
	$sheetVisits->setCellValue("H" . $vr, $visit['reason_code']);
	$sheetVisits->setCellValue("I" . $vr, $desc);
	$sheetVisits->setCellValue("J" . $vr, $visit['stop_remark']);
	$sheetVisits->setCellValue("K" . $vr, $visit['is_completed'] ? "Completed" : "Open");
	$sheetVisits->setCellValue("L" . $vr, $formAvail);
	$vr++;
}
rar_excel_autosize($sheetVisits, 11);

/* ---------- Collect form rows ---------- */
$c1Rows = array();
$c2Rows = array();
$highRateRows = array();

foreach ($visits as $visit) {
	$dateLabel = ($visit['visit_date'] != "") ? date("d/m/Y", strtotime($visit['visit_date'])) : "-";
	$base = array(
		"visit_id" => $visit['id'],
		"visit_date" => $dateLabel,
		"sales_person" => $visit['sales_person'],
		"customer_name" => $visit['customer_name'],
		"customer_code" => $visit['customer_code'],
		"remark_code" => $visit['remark_code'],
		"reason_code" => $visit['reason_code'],
		"stop_remark" => $visit['stop_remark'],
	);

	if (!empty($visit['consultant_form']) && is_array($visit['consultant_form'])) {
		$vf = $visit['consultant_form'];
		$formReason = strtoupper(isset($vf['reason_code']) ? $vf['reason_code'] : $visit['reason_code']);
		$typeLabel = "Private Consultant Approval";
		if ($formReason == "C2" || (isset($vf['consultant_type']) && $vf['consultant_type'] == "government")) {
			$typeLabel = "Government Consultant Approval";
			$formReason = "C2";
		} else {
			$formReason = "C1";
		}
		$row = array_merge($base, array(
			"form_type" => $typeLabel,
			"form_reason" => $formReason,
			"firm_name" => isset($vf['firm_name']) ? $vf['firm_name'] : "",
			"address" => isset($vf['address']) ? $vf['address'] : "",
			"city" => isset($vf['city']) ? $vf['city'] : "",
			"state" => isset($vf['state']) ? $vf['state'] : "",
			"pincode" => isset($vf['pincode']) ? $vf['pincode'] : "",
			"contact_person" => isset($vf['contact_person']) ? $vf['contact_person'] : "",
			"mobile" => isset($vf['mobile']) ? $vf['mobile'] : "",
			"email" => isset($vf['email']) ? $vf['email'] : "",
		));
		if ($formReason == "C2") {
			$c2Rows[] = $row;
		} else {
			$c1Rows[] = $row;
		}
	}

	if (!empty($visit['high_rate_form']) && is_array($visit['high_rate_form'])) {
		$hf = $visit['high_rate_form'];
		$payLabel = isset($hf['payment_option']) ? rar_excel_payment($hf['payment_option']) : "";
		$items = !empty($visit['high_rate_items']) ? $visit['high_rate_items'] : array(array(
			"product_name" => "",
			"given_rate" => "",
			"qty" => "",
			"customer_rate" => "",
			"remark" => "",
		));
		foreach ($items as $hi) {
			$highRateRows[] = array_merge($base, array(
				"hr_customer_name" => isset($hf['customer_name']) ? $hf['customer_name'] : "",
				"payment" => $payLabel,
				"payment_remark" => isset($hf['payment_remark']) ? $hf['payment_remark'] : "",
				"product_name" => isset($hi['product_name']) ? $hi['product_name'] : "",
				"given_rate" => isset($hi['given_rate']) ? $hi['given_rate'] : "",
				"qty" => isset($hi['qty']) ? $hi['qty'] : "",
				"customer_rate" => isset($hi['customer_rate']) ? $hi['customer_rate'] : "",
				"item_remark" => isset($hi['remark']) ? $hi['remark'] : "",
			));
		}
	}
}

/* ---------- Sheet 3: C1 Private Consultant ---------- */
$sheetC1 = new PHPExcel_Worksheet($book);
$sheetC1->setTitle("C1 Private Consultant");
$book->addSheet($sheetC1, $sheetIndex++);
$sheetC1->setCellValue("A1", "C1 - Private Consultant Approval Forms");
$sheetC1->mergeCells("A1:N1");
$sheetC1->getStyle("A1")->applyFromArray($cStyle);
$sheetC1->setCellValue("A2", "Period: " . $fromLabel . " to " . $toLabel . " | Total Forms: " . count($c1Rows));
$cHeaders = array("#", "Visit ID", "Date", "Sales Person", "Customer", "Customer Code", "Form Type", "Firm Name", "Address", "City", "State", "Pincode", "Contact Person", "Mobile", "Email", "Stop Remark");
rar_excel_write_header($sheetC1, $cHeaders, 4, $headerStyle);
$cr = 5;
$csi = 0;
foreach ($c1Rows as $row) {
	$csi++;
	$sheetC1->setCellValue("A" . $cr, $csi);
	$sheetC1->setCellValue("B" . $cr, $row['visit_id']);
	$sheetC1->setCellValue("C" . $cr, $row['visit_date']);
	$sheetC1->setCellValue("D" . $cr, $row['sales_person']);
	$sheetC1->setCellValue("E" . $cr, $row['customer_name']);
	$sheetC1->setCellValue("F" . $cr, $row['customer_code']);
	$sheetC1->setCellValue("G" . $cr, $row['form_type']);
	$sheetC1->setCellValue("H" . $cr, $row['firm_name']);
	$sheetC1->setCellValue("I" . $cr, $row['address']);
	$sheetC1->setCellValue("J" . $cr, $row['city']);
	$sheetC1->setCellValue("K" . $cr, $row['state']);
	$sheetC1->setCellValue("L" . $cr, $row['pincode']);
	$sheetC1->setCellValue("M" . $cr, $row['contact_person']);
	$sheetC1->setCellValue("N" . $cr, $row['mobile']);
	$sheetC1->setCellValue("O" . $cr, $row['email']);
	$sheetC1->setCellValue("P" . $cr, $row['stop_remark']);
	$cr++;
}
if (empty($c1Rows)) {
	$sheetC1->setCellValue("A5", "No C1 Private Consultant forms found for selected filters.");
}
rar_excel_autosize($sheetC1, 16);

/* ---------- Sheet 4: C2 Government Consultant ---------- */
$sheetC2 = new PHPExcel_Worksheet($book);
$sheetC2->setTitle("C2 Govt Consultant");
$book->addSheet($sheetC2, $sheetIndex++);
$sheetC2->setCellValue("A1", "C2 - Government Consultant Approval Forms");
$sheetC2->mergeCells("A1:N1");
$sheetC2->getStyle("A1")->applyFromArray($cStyle);
$sheetC2->setCellValue("A2", "Period: " . $fromLabel . " to " . $toLabel . " | Total Forms: " . count($c2Rows));
rar_excel_write_header($sheetC2, $cHeaders, 4, $headerStyle);
$cr = 5;
$csi = 0;
foreach ($c2Rows as $row) {
	$csi++;
	$sheetC2->setCellValue("A" . $cr, $csi);
	$sheetC2->setCellValue("B" . $cr, $row['visit_id']);
	$sheetC2->setCellValue("C" . $cr, $row['visit_date']);
	$sheetC2->setCellValue("D" . $cr, $row['sales_person']);
	$sheetC2->setCellValue("E" . $cr, $row['customer_name']);
	$sheetC2->setCellValue("F" . $cr, $row['customer_code']);
	$sheetC2->setCellValue("G" . $cr, $row['form_type']);
	$sheetC2->setCellValue("H" . $cr, $row['firm_name']);
	$sheetC2->setCellValue("I" . $cr, $row['address']);
	$sheetC2->setCellValue("J" . $cr, $row['city']);
	$sheetC2->setCellValue("K" . $cr, $row['state']);
	$sheetC2->setCellValue("L" . $cr, $row['pincode']);
	$sheetC2->setCellValue("M" . $cr, $row['contact_person']);
	$sheetC2->setCellValue("N" . $cr, $row['mobile']);
	$sheetC2->setCellValue("O" . $cr, $row['email']);
	$sheetC2->setCellValue("P" . $cr, $row['stop_remark']);
	$cr++;
}
if (empty($c2Rows)) {
	$sheetC2->setCellValue("A5", "No C2 Government Consultant forms found for selected filters.");
}
rar_excel_autosize($sheetC2, 16);

/* ---------- Sheet 5: High Rate Form ---------- */
$sheetHR = new PHPExcel_Worksheet($book);
$sheetHR->setTitle("High Rate Form");
$book->addSheet($sheetHR, $sheetIndex++);
$sheetHR->setCellValue("A1", "E1 - High Rate Analysis Forms");
$sheetHR->mergeCells("A1:N1");
$sheetHR->getStyle("A1")->applyFromArray($eStyle);
$sheetHR->setCellValue("A2", "Period: " . $fromLabel . " to " . $toLabel . " | Total Rows: " . count($highRateRows));
$hrHeaders = array("#", "Visit ID", "Date", "Sales Person", "Customer", "Customer Code", "HR Customer Name", "Payment", "Payment Remark", "Product", "Given Rate", "Qty", "Customer Rate", "Item Remark", "Stop Remark");
rar_excel_write_header($sheetHR, $hrHeaders, 4, $headerStyle);
$hr = 5;
$hsi = 0;
foreach ($highRateRows as $row) {
	$hsi++;
	$sheetHR->setCellValue("A" . $hr, $hsi);
	$sheetHR->setCellValue("B" . $hr, $row['visit_id']);
	$sheetHR->setCellValue("C" . $hr, $row['visit_date']);
	$sheetHR->setCellValue("D" . $hr, $row['sales_person']);
	$sheetHR->setCellValue("E" . $hr, $row['customer_name']);
	$sheetHR->setCellValue("F" . $hr, $row['customer_code']);
	$sheetHR->setCellValue("G" . $hr, $row['hr_customer_name']);
	$sheetHR->setCellValue("H" . $hr, $row['payment']);
	$sheetHR->setCellValue("I" . $hr, $row['payment_remark']);
	$sheetHR->setCellValue("J" . $hr, $row['product_name']);
	$sheetHR->setCellValue("K" . $hr, $row['given_rate']);
	$sheetHR->setCellValue("L" . $hr, $row['qty']);
	$sheetHR->setCellValue("M" . $hr, $row['customer_rate']);
	$sheetHR->setCellValue("N" . $hr, $row['item_remark']);
	$sheetHR->setCellValue("O" . $hr, $row['stop_remark']);
	$hr++;
}
if (empty($highRateRows)) {
	$sheetHR->setCellValue("A5", "No High Rate forms found for selected filters.");
}
rar_excel_autosize($sheetHR, 15);

$book->setActiveSheetIndex(0);

$saveDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'inquiry_documents' . DIRECTORY_SEPARATOR;
if (!is_dir($saveDir)) {
	if (!@mkdir($saveDir, 0777, true) && !is_dir($saveDir)) {
		rar_excel_json_exit(array('ack' => 0, 'ack_msg' => 'Cannot create folder inquiry_documents. Check server write permission.'));
	}
}
if (!is_writable($saveDir)) {
	rar_excel_json_exit(array('ack' => 0, 'ack_msg' => 'inquiry_documents folder is not writable on server.'));
}

$fileName = "Remark_Wise_Report_" . date("Ymd_His") . ".xls";
$savePath = $saveDir . $fileName;

try {
	$writer = PHPExcel_IOFactory::createWriter($book, "Excel5");
	$writer->save($savePath);
} catch (Exception $e) {
	rar_excel_json_exit(array('ack' => 0, 'ack_msg' => 'Excel create failed: ' . $e->getMessage()));
}

if (!file_exists($savePath) || filesize($savePath) < 50) {
	rar_excel_json_exit(array('ack' => 0, 'ack_msg' => 'Excel file was not created. Check inquiry_documents permission.'));
}

rar_excel_json_exit(array(
	'ack' => 1,
	'ack_msg' => 'Excel ready',
	'file_path' => trim(ADMINFOLDER . "/inquiry_documents/" . $fileName),
	'file_name' => $fileName,
	'visit_count' => (int) $data['total_visits'],
	'c1_count' => count($c1Rows),
	'c2_count' => count($c2Rows),
	'high_rate_count' => count($highRateRows),
	'format' => 'xls',
));
