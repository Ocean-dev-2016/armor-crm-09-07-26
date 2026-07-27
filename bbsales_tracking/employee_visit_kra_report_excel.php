<?php
/**
 * Employee Visit KRA Excel — supports ALL employees in one file.
 * One sheet per employee (KRA grid + Total Visit + visit codes).
 */
$page_id = 599;
$page_slug = 'visit_report_page';
include("connect.php");
require_once("../include/class.employee_visit_kra_report.php");
require_once("PHPExcel/IOFactory.php");

@set_time_limit(0);
@ini_set('max_execution_time', '0');
@ini_set('memory_limit', '1024M');
@ini_set('display_errors', '0');
@ignore_user_abort(true);

function kra_excel_json_exit($payload)
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
	kra_excel_json_exit(array(
		'ack' => 0,
		'ack_msg' => 'Excel failed: ' . $err['message'] . ' (line ' . $err['line'] . ')',
	));
});

$canExport = (
	(isset($rights['export_excel_flag']) && (int) $rights['export_excel_flag'] === 1)
	|| (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0)
);
if (!$canExport) {
	kra_excel_json_exit(array('ack' => 0, 'ack_msg' => 'Excel export permission required.'));
}

function kra_excel_safe_title($name, $used)
{
	$name = preg_replace('/[\\\\\\/\\?\\*\\[\\]\\:]/', '', (string) $name);
	$name = trim(substr($name, 0, 25));
	if ($name == "") {
		$name = "Employee";
	}
	$base = $name;
	$counter = 2;
	while (in_array(strtolower($name), $used)) {
		$name = substr($base, 0, 21) . " " . $counter;
		$counter++;
	}
	return $name;
}

function kra_excel_visit_code($visit)
{
	$code = $visit['normalized_reason_code'] != "" ? $visit['normalized_reason_code'] : $visit['normalized_remark_code'];
	return ($code != "") ? $code : ($visit['is_completed'] ? "Done" : "Open");
}

$builder = new EmployeeVisitKraReport($db);
$data = $builder->build(
	isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : "",
	isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : "",
	isset($_REQUEST['employee_ids']) ? $_REQUEST['employee_ids'] : "",
	$rights
);

if (empty($data['employees'])) {
	kra_excel_json_exit(array('ack' => 0, 'ack_msg' => 'No accessible employee data found for export.'));
}

$employeeCount = count($data['employees']);
// Bulk (all / many employees): keep KRA grid, skip heavy VISIT DETAILS for speed on Live
$includeVisitDetails = ($employeeCount <= 3);

$book = new PHPExcel();
$book->removeSheetByIndex(0);
$usedTitles = array();
$sheetIndex = 0;
$masterColCount = 10;
$firstDateColIndex = 10;

$headerStyle = array(
	"font" => array("bold" => true, "color" => array("rgb" => "FFFFFF")),
	"fill" => array("type" => PHPExcel_Style_Fill::FILL_SOLID, "color" => array("rgb" => "E9782E")),
	"alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
);
$titleStyle = array(
	"font" => array("bold" => true, "size" => 13, "color" => array("rgb" => "FFFFFF")),
	"fill" => array("type" => PHPExcel_Style_Fill::FILL_SOLID, "color" => array("rgb" => "2F6F44")),
	"alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
);

foreach ($data['employees'] as $employee) {
	$sheet = new PHPExcel_Worksheet($book);
	$title = kra_excel_safe_title($employee['name'], $usedTitles);
	$usedTitles[] = strtolower($title);
	$sheet->setTitle($title);
	$book->addSheet($sheet, $sheetIndex++);

	$dateCount = count($data['range']['dates']);
	$lastColumnIndex = ($masterColCount - 1) + max(1, $dateCount);
	$lastColumn = PHPExcel_Cell::stringFromColumnIndex($lastColumnIndex);

	$sheet->mergeCells("A1:" . $lastColumn . "1");
	$sheet->setCellValue("A1", "KEY RESULT AREA - " . strtoupper($employee['name']));
	$sheet->mergeCells("A2:" . $lastColumn . "2");
	$sheet->setCellValue("A2", date("d/m/Y", strtotime($data['range']['from'])) . " TO " . date("d/m/Y", strtotime($data['range']['to'])));
	$sheet->getStyle("A1")->applyFromArray($titleStyle);

	$kpiLabels = array("Approved Expense", "Salary", "Expense + Salary", "Total Sales", "Total Visit", "Total Duration", "Completed / Open", "Total Quotation", "Total PI Approved");
	$totalDurationMins = isset($employee['kpi']['total_duration_minutes']) ? (int) $employee['kpi']['total_duration_minutes'] : 0;
	$durH = (int) floor($totalDurationMins / 60);
	$durM = $totalDurationMins % 60;
	$totalDurationLabel = ($durH > 0) ? ($durH . "h " . $durM . "m") : ($durM . " min");
	$kpiValues = array(
		(float) $employee['kpi']['approved_expense'],
		"N/A",
		$db->rp_number_format((float) $employee['kpi']['approved_expense'], 2) . " + N/A",
		(float) $employee['kpi']['total_sales'],
		(int) $employee['kpi']['total_visits'],
		$totalDurationLabel,
		(int) $employee['kpi']['completed_visits'] . " / " . (int) $employee['kpi']['open_visits'],
		(int) $employee['kpi']['total_quotations'],
		(int) $employee['kpi']['approved_pi'],
	);
	for ($i = 0; $i < count($kpiLabels); $i++) {
		$column = PHPExcel_Cell::stringFromColumnIndex($i);
		$sheet->setCellValue($column . "3", $kpiLabels[$i]);
		$sheet->setCellValue($column . "4", $kpiValues[$i]);
	}
	$sheet->getStyle("A3:I3")->getFont()->setBold(true);

	$headerRow = 6;
	$headers = array("Sr.", "Code", "Account Name", "Turnover", "GST No.", "Address", "City", "Pincode", "Total Visit", "Visit Duration");
	foreach ($data['range']['dates'] as $date) {
		$headers[] = date("d/m/Y", strtotime($date));
	}
	foreach ($headers as $index => $header) {
		$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($index) . $headerRow, $header);
	}
	$sheet->getStyle("A" . $headerRow . ":" . $lastColumn . $headerRow)->applyFromArray($headerStyle);
	$sheet->getStyle("I" . $headerRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("C85A12");
	$sheet->getStyle("J" . $headerRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("A84B0F");

	$summaryRow = 7;
	$sheet->mergeCells("A" . $summaryRow . ":J" . $summaryRow);
	$sheet->setCellValue("A" . $summaryRow, "Daily Visit Count / Outcome");
	foreach ($data['range']['dates'] as $dateIndex => $date) {
		$daily = $employee['daily'][$date];
		$parts = array($daily['total'] . " Visit");
		foreach ($daily['codes'] as $code => $count) {
			if ($count > 0) {
				$parts[] = $code . ":" . $count;
			}
		}
		if ($daily['open'] > 0) {
			$parts[] = "Open:" . $daily['open'];
		}
		$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($firstDateColIndex + $dateIndex) . $summaryRow, implode(" | ", $parts));
	}

	$rowNumber = 8;
	$accountStartRow = $rowNumber;
	$sr = 0;
	foreach ($employee['accounts'] as $account) {
		$acctVisits = isset($account['total_visits']) ? (int) $account['total_visits'] : 0;
		$acctDur = isset($account['total_duration_minutes']) ? (int) $account['total_duration_minutes'] : 0;
		$ah = (int) floor($acctDur / 60);
		$am = $acctDur % 60;
		$acctDurLabel = ($ah > 0) ? ($ah . "h " . $am . "m") : ($am . " min");
		$values = array(
			++$sr,
			$account['code'],
			$account['company'] . (($account['person'] != "") ? " / " . $account['person'] : ""),
			$account['turnover'],
			$account['gst'],
			$account['address'],
			$account['city'],
			$account['pincode'],
			$acctVisits,
			$acctDurLabel,
		);
		foreach ($values as $index => $value) {
			$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($index) . $rowNumber, $value);
		}
		foreach ($data['range']['dates'] as $dateIndex => $date) {
			$codes = array();
			if (!empty($account['dates'][$date])) {
				foreach ($account['dates'][$date] as $visit) {
					$codes[] = kra_excel_visit_code($visit);
				}
			}
			$sheet->setCellValue(
				PHPExcel_Cell::stringFromColumnIndex($firstDateColIndex + $dateIndex) . $rowNumber,
				implode(", ", $codes)
			);
		}
		$rowNumber++;
	}
	$accountEndRow = $rowNumber - 1;

	if ($accountEndRow >= $accountStartRow) {
		$sheet->getStyle("I" . $accountStartRow . ":J" . $accountEndRow)->getFont()->setBold(true)->setSize(14);
		$dateStartCol = PHPExcel_Cell::stringFromColumnIndex($firstDateColIndex);
		$sheet->getStyle($dateStartCol . $accountStartRow . ":" . $lastColumn . $accountEndRow)->getFont()->setBold(true)->setSize(14);
		$sheet->getStyle($dateStartCol . $accountStartRow . ":" . $lastColumn . $accountEndRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}

	$rowNumber += 1;
	$sheet->setCellValue("A" . $rowNumber, "Visit Code Chart:");
	$col = 1;
	foreach ($data['remark_labels'] as $code => $label) {
		$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($col) . $rowNumber, $code . " = " . $label);
		$col++;
	}

	if ($includeVisitDetails) {
		$rowNumber += 2;
		$sheet->setCellValue("A" . $rowNumber, "VISIT DETAILS");
		$sheet->getStyle("A" . $rowNumber)->applyFromArray($titleStyle);
		$rowNumber++;
		$detailHeaders = array("Visit Date", "Visit ID", "Account", "Code", "Purpose", "Start", "Stop", "Remark", "Reason", "Outcome", "Stop Remark", "Consultant Firm", "Consultant Contact", "Consultant Mobile", "HR Customer", "HR Payment", "HR Products");
		foreach ($detailHeaders as $index => $header) {
			$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($index) . $rowNumber, $header);
		}
		$sheet->getStyle("A" . $rowNumber . ":Q" . $rowNumber)->applyFromArray($headerStyle);
		$rowNumber++;
		foreach ($employee['accounts'] as $account) {
			foreach ($account['dates'] as $dateRows) {
				foreach ($dateRows as $visit) {
					$cfFirm = "";
					$cfContact = "";
					$cfMobile = "";
					if (!empty($visit['consultant_form']) && is_array($visit['consultant_form'])) {
						$cfFirm = $visit['consultant_form']['firm_name'];
						$cfContact = $visit['consultant_form']['contact_person'];
						$cfMobile = $visit['consultant_form']['mobile'];
					}
					$hrCust = "";
					$hrPay = "";
					$hrProducts = "";
					if (!empty($visit['high_rate_form']) && is_array($visit['high_rate_form'])) {
						$hrCust = $visit['high_rate_form']['customer_name'];
						$hrPay = $visit['high_rate_form']['payment_option'];
						if ($hrPay === '0' || $hrPay === 0) {
							$hrPay = 'Advance';
						} else if ($hrPay === '1' || $hrPay === 1) {
							$hrPay = '30 Days';
						}
						if (!empty($visit['high_rate_items'])) {
							$bits = array();
							foreach ($visit['high_rate_items'] as $hi) {
								$bits[] = $hi['product_name'] . " (G:" . $hi['given_rate'] . "/Q:" . $hi['qty'] . "/C:" . $hi['customer_rate'] . ")";
							}
							$hrProducts = implode("; ", $bits);
						}
					}
					$sheet->setCellValue("A" . $rowNumber, date("d/m/Y", strtotime($visit['visit_date'])));
					$sheet->setCellValue("B" . $rowNumber, $visit['id']);
					$sheet->setCellValue("C" . $rowNumber, $account['company']);
					$sheet->setCellValue("D" . $rowNumber, kra_excel_visit_code($visit));
					$sheet->setCellValue("E" . $rowNumber, $visit['purpose_name']);
					$sheet->setCellValue("F" . $rowNumber, $visit['start_date_time']);
					$sheet->setCellValue("G" . $rowNumber, $visit['is_completed'] ? $visit['stop_date_time'] : "Open");
					$sheet->setCellValue("H" . $rowNumber, $visit['normalized_remark_code']);
					$sheet->setCellValue("I" . $rowNumber, $visit['normalized_reason_code']);
					$sheet->setCellValue("J" . $rowNumber, trim($visit['remark_label'] . (($visit['reason_label'] != "") ? " - " . $visit['reason_label'] : "")));
					$sheet->setCellValue("K" . $rowNumber, $visit['stop_remark']);
					$sheet->setCellValue("L" . $rowNumber, $cfFirm);
					$sheet->setCellValue("M" . $rowNumber, $cfContact);
					$sheet->setCellValue("N" . $rowNumber, $cfMobile);
					$sheet->setCellValue("O" . $rowNumber, $hrCust);
					$sheet->setCellValue("P" . $rowNumber, $hrPay);
					$sheet->setCellValue("Q" . $rowNumber, $hrProducts);
					$rowNumber++;
				}
			}
		}
	}

	$sheet->freezePane("K8");
	$sheet->getColumnDimension("A")->setWidth(8);
	$sheet->getColumnDimension("B")->setWidth(12);
	$sheet->getColumnDimension("C")->setWidth(28);
	$sheet->getColumnDimension("D")->setWidth(14);
	$sheet->getColumnDimension("E")->setWidth(16);
	$sheet->getColumnDimension("F")->setWidth(24);
	$sheet->getColumnDimension("G")->setWidth(12);
	$sheet->getColumnDimension("H")->setWidth(10);
	$sheet->getColumnDimension("I")->setWidth(12);
	$sheet->getColumnDimension("J")->setWidth(14);
	for ($index = $firstDateColIndex; $index <= $lastColumnIndex; $index++) {
		$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($index))->setWidth(11);
	}
}

$book->setActiveSheetIndex(0);

$saveDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'inquiry_documents' . DIRECTORY_SEPARATOR;
if (!is_dir($saveDir)) {
	if (!@mkdir($saveDir, 0777, true) && !is_dir($saveDir)) {
		kra_excel_json_exit(array('ack' => 0, 'ack_msg' => 'Cannot create folder inquiry_documents. Check server write permission.'));
	}
}
if (!is_writable($saveDir)) {
	kra_excel_json_exit(array('ack' => 0, 'ack_msg' => 'inquiry_documents folder is not writable on server.'));
}

// Live PHP has no ZipArchive — NEVER use Excel2007 (.xlsx).
// Always export Excel5 (.xls) so Zip is not required.
$fileName = "Employee_Visit_KRA_ALL_" . date("Ymd_His") . ".xls";
$savePath = $saveDir . $fileName;

try {
	$writer = PHPExcel_IOFactory::createWriter($book, "Excel5");
	$writer->save($savePath);
} catch (Exception $e) {
	kra_excel_json_exit(array('ack' => 0, 'ack_msg' => 'Excel create failed: ' . $e->getMessage()));
}

if (!file_exists($savePath) || filesize($savePath) < 50) {
	kra_excel_json_exit(array('ack' => 0, 'ack_msg' => 'Excel file was not created. Check inquiry_documents permission.'));
}

kra_excel_json_exit(array(
	'ack' => 1,
	'ack_msg' => 'Excel ready (' . $employeeCount . ' employees)',
	'file_path' => trim(ADMINFOLDER . "/inquiry_documents/" . $fileName),
	'file_name' => $fileName,
	'employee_count' => $employeeCount,
	'format' => 'xls',
));
