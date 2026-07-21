<?php
$page_id = 599;
$page_slug = 'visit_report_page';
include("connect.php");
require_once("../include/class.employee_visit_kra_report.php");
require_once("PHPExcel/IOFactory.php");

if (!($rights['export_excel_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0)) {
	header("HTTP/1.1 403 Forbidden");
	die("Excel export permission required.");
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

function kra_excel_account_visits($account)
{
	$visits = array();
	foreach ($account['dates'] as $dateRows) {
		foreach ($dateRows as $visit) {
			$visits[] = $visit;
		}
	}
	usort($visits, "kra_excel_sort_visits");
	return $visits;
}

function kra_excel_sort_visits($a, $b)
{
	return strcmp($a['start_date_time'], $b['start_date_time']);
}

$builder = new EmployeeVisitKraReport($db);
$data = $builder->build(
	isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : "",
	isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : "",
	isset($_REQUEST['employee_ids']) ? $_REQUEST['employee_ids'] : "",
	$rights
);

if (empty($data['employees'])) {
	die("No accessible employee data found for export.");
}

$book = new PHPExcel();
$book->removeSheetByIndex(0);
$usedTitles = array();
$sheetIndex = 0;

foreach ($data['employees'] as $employee) {
	$sheet = new PHPExcel_Worksheet($book);
	$title = kra_excel_safe_title($employee['name'], $usedTitles);
	$usedTitles[] = strtolower($title);
	$sheet->setTitle($title);
	$book->addSheet($sheet, $sheetIndex++);

	$dateCount = count($data['range']['dates']);
	$lastColumnIndex = 7 + $dateCount;
	$lastColumn = PHPExcel_Cell::stringFromColumnIndex($lastColumnIndex);
	$sheet->mergeCells("A1:" . $lastColumn . "1");
	$sheet->setCellValue("A1", "KEY RESULT AREA - " . strtoupper($employee['name']));
	$sheet->mergeCells("A2:" . $lastColumn . "2");
	$sheet->setCellValue("A2", date("d/m/Y", strtotime($data['range']['from'])) . " TO " . date("d/m/Y", strtotime($data['range']['to'])));

	$kpiLabels = array("Approved Expense", "Salary", "Expense + Salary", "Total Sales", "Total Visit", "Completed / Open", "Total Quotation", "Total PI Approved");
	$kpiValues = array(
		(float) $employee['kpi']['approved_expense'],
		"N/A",
		$db->rp_number_format((float) $employee['kpi']['approved_expense'], 2) . " + N/A",
		(float) $employee['kpi']['total_sales'],
		(int) $employee['kpi']['total_visits'],
		(int) $employee['kpi']['completed_visits'] . " / " . (int) $employee['kpi']['open_visits'],
		(int) $employee['kpi']['total_quotations'],
		(int) $employee['kpi']['approved_pi'],
	);
	for ($i = 0; $i < count($kpiLabels); $i++) {
		$column = PHPExcel_Cell::stringFromColumnIndex($i);
		$sheet->setCellValue($column . "3", $kpiLabels[$i]);
		$sheet->setCellValue($column . "4", $kpiValues[$i]);
	}

	$headerRow = 6;
	$headers = array("Sr.", "Code", "Account Name", "Turnover", "GST No.", "Address", "City", "Pincode");
	foreach ($data['range']['dates'] as $date) {
		$headers[] = date("d/m/Y", strtotime($date));
	}
	foreach ($headers as $index => $header) {
		$column = PHPExcel_Cell::stringFromColumnIndex($index);
		$sheet->setCellValue($column . $headerRow, $header);
	}

	$summaryRow = 7;
	$sheet->mergeCells("A" . $summaryRow . ":H" . $summaryRow);
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
		$column = PHPExcel_Cell::stringFromColumnIndex(8 + $dateIndex);
		$sheet->setCellValue($column . $summaryRow, implode("\n", $parts));
	}

	$rowNumber = 8;
	$sr = 0;
	foreach ($employee['accounts'] as $account) {
		$values = array(
			++$sr,
			$account['code'],
			$account['company'] . (($account['person'] != "") ? "\n" . $account['person'] : ""),
			$account['turnover'],
			$account['gst'],
			$account['address'],
			$account['city'],
			$account['pincode'],
		);
		foreach ($values as $index => $value) {
			$column = PHPExcel_Cell::stringFromColumnIndex($index);
			$sheet->setCellValue($column . $rowNumber, $value);
		}
		foreach ($data['range']['dates'] as $dateIndex => $date) {
			$codes = array();
			if (!empty($account['dates'][$date])) {
				foreach ($account['dates'][$date] as $visit) {
					$codes[] = kra_excel_visit_code($visit);
				}
			}
			$column = PHPExcel_Cell::stringFromColumnIndex(8 + $dateIndex);
			$sheet->setCellValue($column . $rowNumber, implode("\n", $codes));
		}
		$rowNumber++;
	}

	$rowNumber += 2;
	$detailTitleRow = $rowNumber;
	$detailHeaders = array(
		"Visit Date", "Visit ID", "Account", "Person", "Visit Type", "Purpose",
		"Start Time", "Stop Time", "Duration Minutes", "Remark Code", "Reason Code",
		"Outcome", "Full Stop Remark", "Purchasing From", "Contact Name", "Contact Mobile",
		"Start Address", "Stop Address", "Follow-up ID"
	);
	$detailLastColumn = PHPExcel_Cell::stringFromColumnIndex(count($detailHeaders) - 1);
	$sheet->mergeCells("A" . $detailTitleRow . ":" . $detailLastColumn . $detailTitleRow);
	$sheet->setCellValue("A" . $detailTitleRow, "VISIT DETAILS");
	$rowNumber++;
	foreach ($detailHeaders as $index => $header) {
		$column = PHPExcel_Cell::stringFromColumnIndex($index);
		$sheet->setCellValue($column . $rowNumber, $header);
	}
	$detailHeaderRow = $rowNumber;
	$rowNumber++;

	foreach ($employee['accounts'] as $account) {
		foreach (kra_excel_account_visits($account) as $visit) {
			$visitType = "";
			if ($visit['visit_type'] == "1") {
				$visitType = "Existing Customer";
			} else if ($visit['visit_type'] == "3") {
				$visitType = "Inquiry";
			} else if ($visit['visit_type'] == "4") {
				$visitType = "New Customer";
			}
			$detailValues = array(
				date("d/m/Y", strtotime($visit['visit_date'])),
				$visit['id'],
				$account['company'],
				$account['person'],
				$visitType,
				$visit['purpose_name'],
				$visit['start_date_time'],
				$visit['is_completed'] ? $visit['stop_date_time'] : "Open",
				$visit['duration_minutes'] === null ? "" : $visit['duration_minutes'],
				$visit['normalized_remark_code'],
				$visit['normalized_reason_code'],
				trim($visit['remark_label'] . (($visit['reason_label'] != "") ? " - " . $visit['reason_label'] : "")),
				$visit['stop_remark'],
				$visit['product_name'],
				$visit['name'],
				$visit['mobile_no'],
				$visit['app_address'],
				$visit['stop_app_address'],
				$visit['visit_followup_id'],
			);
			foreach ($detailValues as $index => $value) {
				$column = PHPExcel_Cell::stringFromColumnIndex($index);
				$sheet->setCellValue($column . $rowNumber, $value);
			}
			$rowNumber++;
		}
	}

	$headerStyle = array(
		"font" => array("bold" => true, "color" => array("rgb" => "FFFFFF")),
		"fill" => array("type" => PHPExcel_Style_Fill::FILL_SOLID, "color" => array("rgb" => "E9782E")),
		"alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, "vertical" => PHPExcel_Style_Alignment::VERTICAL_CENTER, "wrap" => true),
		"borders" => array("allborders" => array("style" => PHPExcel_Style_Border::BORDER_THIN)),
	);
	$titleStyle = array(
		"font" => array("bold" => true, "size" => 14, "color" => array("rgb" => "FFFFFF")),
		"fill" => array("type" => PHPExcel_Style_Fill::FILL_SOLID, "color" => array("rgb" => "2F6F44")),
		"alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
	);
	$sheet->getStyle("A1:" . $lastColumn . "1")->applyFromArray($titleStyle);
	$sheet->getStyle("A2:" . $lastColumn . "2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->getStyle("A3:H3")->getFont()->setBold(true);
	$sheet->getStyle("A3:H4")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$sheet->getStyle("A" . $headerRow . ":" . $lastColumn . $headerRow)->applyFromArray($headerStyle);
	$sheet->getStyle("A" . $detailHeaderRow . ":" . $detailLastColumn . $detailHeaderRow)->applyFromArray($headerStyle);
	$sheet->getStyle("A" . $detailTitleRow . ":" . $detailLastColumn . $detailTitleRow)->applyFromArray($titleStyle);
	$sheet->getStyle("A1:" . $detailLastColumn . ($rowNumber - 1))->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
	$sheet->freezePane("I7");
	$sheet->setAutoFilter("A" . $detailHeaderRow . ":" . $detailLastColumn . $detailHeaderRow);
	$sheet->getColumnDimension("A")->setWidth(11);
	$sheet->getColumnDimension("B")->setWidth(14);
	$sheet->getColumnDimension("C")->setWidth(28);
	$sheet->getColumnDimension("D")->setWidth(16);
	$sheet->getColumnDimension("E")->setWidth(20);
	$sheet->getColumnDimension("F")->setWidth(36);
	$sheet->getColumnDimension("G")->setWidth(16);
	$sheet->getColumnDimension("H")->setWidth(12);
	for ($index = 8; $index <= $lastColumnIndex; $index++) {
		$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($index))->setWidth(13);
	}
}

$book->setActiveSheetIndex(0);
$fileName = "Employee_Visit_KRA_" . date("Ymd_His") . ".xlsx";
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=\"" . $fileName . "\"");
header("Cache-Control: max-age=0");
$writer = PHPExcel_IOFactory::createWriter($book, "Excel2007");
$writer->save("php://output");
exit;
?>
