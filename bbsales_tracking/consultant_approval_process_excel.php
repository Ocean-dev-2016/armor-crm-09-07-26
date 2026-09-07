<?php
/**
 * Consultant Approval Process Report — Excel export
 */
$page_id = 668;
$page_slug = 'consultant_apptoval_process_report';
include("connect.php");
require_once dirname(__DIR__) . '/include/consultant_approval_process_helper.php';
include('PHPExcel/IOFactory.php');

$ctable = "sales_vs_consultant_approval_process";
$ctable_where = "isDelete=0";

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
	$USER_IDS = array();
	$sales_id = $db->rp_getData("sales_executive", "*", "name LIKE '%" . $_REQUEST['searchName'] . "%' OR phone LIKE '%" . $_REQUEST['searchName'] . "%'  AND isDelete=0", "", 0);
	if ($sales_id) {
		while ($K = mysqli_fetch_assoc($sales_id)) {
			$USER_IDS[] = $K['id'];
		}
		$USER_IDS = implode(",", $USER_IDS);
		$ctable_where .= " AND sales_id IN (" . $USER_IDS . ") ";
	} else {
		$ctable_where .= " AND sales_id IN (0) ";
	}
}

if (
	isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL &&
	isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL
) {
	$ctable_where = "process_four_purchase_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND process_four_purchase_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "'";
} else if (
	isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive'] != "" && $_REQUEST['sales_executive'] != "null"
) {
	$ctable_where .= " AND process_one_sales_executive_id = " . (int) $_REQUEST['sales_executive'];
}

if (isset($_REQUEST['approval_type']) && $_REQUEST['approval_type'] != "" && $_REQUEST['approval_type'] != "null") {
	$approval_type = (int) $_REQUEST['approval_type'];
	if ($approval_type > 0) {
		$ctable_where .= " AND process_one_approval_type = '" . $approval_type . "'";
	}
}

if (
	(empty($_REQUEST['sales_executive']) || $_REQUEST['sales_executive'] == "null") &&
	(empty($_REQUEST['FromDate']) || empty($_REQUEST['ToDate']))
) {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Please select Sales Person filter first.'));
	include("disconnect.php");
	exit();
}

if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
	if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 2) {
		if ($rights['personal_flag'] == 1) {
			$check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
			$ctable_where .= " AND process_one_sales_executive_id='" . $check_id . "' ";
		} else if ($rights['chain_vise_flag'] == 1) {
			$check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
			$WhereCondition = '';
			$get_sales_type = $db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='" . $check_id . "'", 0);
			if ($get_sales_type == "sales_manager") {
				$WhereCondition .= ' sm_id=' . $check_id;
			} else if ($get_sales_type == "area_sales_manager") {
				$WhereCondition .= ' asm_id=' . $check_id;
			} else if ($get_sales_type == "sales_officer") {
				$WhereCondition .= ' so_id=' . $check_id;
			} else if ($get_sales_type == "sales_executive") {
				$WhereCondition .= ' se_id=' . $check_id;
			} else {
				$WhereCondition .= ' type = "service_engineer"';
			}
			$data = $db->rp_getData("sales_executive", "id", $WhereCondition, "", 0);
			$SALEID1 = array();
			if ($data) {
				while ($data_d = mysqli_fetch_assoc($data)) {
					$SALEID1[] = $data_d['id'];
				}
			}
			if (!empty($SALEID1)) {
				$ctable_where .= "  AND sales_id IN (" . implode(",", $SALEID1) . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
			} else {
				$ctable_where .= "  AND sales_id IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
			}
		}
	}
}

$sales_name_get = '';
if (!empty($_REQUEST['sales_executive']) && $_REQUEST['sales_executive'] != "null") {
	$sales_name_get = $db->rp_getValue("sales_executive", "name", "id='" . (int) $_REQUEST['sales_executive'] . "'", 0);
}

$book = new PHPExcel();
$sheet = $book->setActiveSheetIndex(0);
$sheet->setTitle('Consultant Approval');

$sheet->setCellValue('A1', 'Consultant Approval Report');
$sheet->mergeCells('A1:P1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->setCellValue('A2', 'Person Name: ' . $sales_name_get);
$sheet->mergeCells('A2:P2');
$sheet->setCellValue('A3', 'Exported On: ' . date('d-m-Y h:i A'));
$sheet->mergeCells('A3:P3');

$headers = array(
	'Sr No',
	'Date',
	'Total Visit',
	'Customer Wise Visit Count',
	'Approval Type',
	'Customer Name',
	'Name',
	'Number',
	'Mail ID',
	'Project Name',
	'Project Location',
	'Product Name',
	'Contractor Office Name',
	'Contractor Office Mobile',
	'Contractor Office Email',
	'Purchase Date',
);
$colIndex = 0;
foreach ($headers as $header) {
	$cell = PHPExcel_Cell::stringFromColumnIndex($colIndex) . '5';
	$sheet->setCellValue($cell, $header);
	$colIndex++;
}
$sheet->getStyle('A5:P5')->getFont()->setBold(true);
$sheet->getStyle('A5:P5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('3598DC');
$sheet->getStyle('A5:P5')->getFont()->getColor()->setRGB('FFFFFF');
$sheet->getStyle('A5:P5')->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$rowNum = 6;
$sr = 1;
$result = $db->rp_getData($ctable, "*", "isDelete=0 AND isActive=1 AND $ctable_where", "", 0);
if ($result) {
	while ($d = mysqli_fetch_assoc($result)) {
		$entry_date = '';
		if (!empty($d['created_date']) && $d['created_date'] != '0000-00-00 00:00:00') {
			$entry_date = date('d-m-Y', strtotime($d['created_date']));
		} elseif (!empty($d['modified_date']) && $d['modified_date'] != '0000-00-00 00:00:00') {
			$entry_date = date('d-m-Y', strtotime($d['modified_date']));
		}

		$customer_id = (int) $d['process_one_executive_id'];
		$sales_id = (int) $d['process_one_sales_executive_id'];
		$customer_wise_visit_count = 0;
		$total_visit = 0;
		if ($customer_id > 0) {
			$customer_wise_visit_count = (int) $db->rp_getTotalRecord("visit", "isDelete=0 AND customer_id='" . $customer_id . "'", 0);
			if ($sales_id > 0) {
				$total_visit = (int) $db->rp_getTotalRecord("visit", "isDelete=0 AND customer_id='" . $customer_id . "' AND user_id='" . $sales_id . "'", 0);
			}
		}

		$excutive_name_data = $db->rp_getValue("executive", "company_name", "isDelete=0 AND id='" . $d['process_one_executive_id'] . "'");
		$approval_type_label = '';
		if (isset($db->approval_type_arr[$d['process_one_approval_type']])) {
			$approval_type_label = $db->approval_type_arr[$d['process_one_approval_type']];
		}

		$projectName = consultant_approval_format_project_text(isset($d['process_three_project_name']) ? $d['process_three_project_name'] : '');
		$projectLocation = consultant_approval_format_project_text(isset($d['process_three_project_location']) ? $d['process_three_project_location'] : '');
		$productName = consultant_approval_format_product_text(isset($d['process_four_product_name']) ? $d['process_four_product_name'] : '');

		$purchase_date = '';
		if (!empty($d['process_four_purchase_date']) && $d['process_four_purchase_date'] != '0000-00-00') {
			$purchase_date = date('d-m-Y', strtotime($d['process_four_purchase_date']));
		}

		$values = array(
			$sr++,
			$entry_date,
			$total_visit,
			$customer_wise_visit_count,
			$approval_type_label,
			$excutive_name_data ? $excutive_name_data : '',
			isset($d['process_two_consultant_name']) ? $d['process_two_consultant_name'] : '',
			isset($d['process_two_consultant_mobile']) ? $d['process_two_consultant_mobile'] : '',
			isset($d['process_two_consultant_email']) ? $d['process_two_consultant_email'] : '',
			$projectName,
			$projectLocation,
			$productName,
			isset($d['process_four_contractor_name']) ? $d['process_four_contractor_name'] : '',
			isset($d['process_four_contractor_mobile']) ? $d['process_four_contractor_mobile'] : '',
			isset($d['process_four_contractor_email']) ? $d['process_four_contractor_email'] : '',
			$purchase_date,
		);

		$colIndex = 0;
		foreach ($values as $value) {
			$cell = PHPExcel_Cell::stringFromColumnIndex($colIndex) . $rowNum;
			$sheet->setCellValueExplicit($cell, (string) $value, PHPExcel_Cell_DataType::TYPE_STRING);
			$colIndex++;
		}

		$sheet->getStyle('A' . $rowNum . ':P' . $rowNum)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$rowNum++;
	}
}

$widths = array(8, 12, 10, 14, 28, 18, 14, 14, 24, 22, 22, 32, 20, 16, 24, 12);
foreach ($widths as $i => $width) {
	$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth($width);
}

$saveDir = dirname(__FILE__) . '/inquiry_documents/';
if (!is_dir($saveDir)) {
	@mkdir($saveDir, 0755, true);
}
$fileName = 'Consultant_Approval_Report_' . date('Ymd_His') . '.xls';
$savePath = $saveDir . $fileName;

$writer = PHPExcel_IOFactory::createWriter($book, 'Excel5');
$writer->save($savePath);

header('Content-Type: application/json; charset=utf-8');
echo json_encode(array(
	'ack' => 1,
	'ack_msg' => 'Excel ready',
	'file_path' => trim(ADMINFOLDER . '/inquiry_documents/' . $fileName),
	'file_name' => $fileName,
));

include("disconnect.php");
