<?php
$page_id = 599;
$page_slug = 'visit_report_page';
include("connect.php");
require_once("../include/class.employee_visit_kra_report.php");

header("Content-Type: application/json; charset=utf-8");

$report = new EmployeeVisitKraReport($db);
$data = $report->getApprovedExpenseBreakdown(
	isset($_REQUEST['employee_id']) ? $_REQUEST['employee_id'] : 0,
	isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : "",
	isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : "",
	$rights
);

if (!empty($data['ack'])) {
	$rowsOut = array();
	foreach ($data['rows'] as $row) {
		$rowsOut[] = array(
			"category_id" => $row['category_id'],
			"subcategory_id" => isset($row['subcategory_id']) ? (int) $row['subcategory_id'] : 0,
			"category_name" => $row['category_name'],
			"subcategory_name" => isset($row['subcategory_name']) ? $row['subcategory_name'] : "-",
			"expense_type" => isset($row['expense_type']) ? (int) $row['expense_type'] : 0,
			"is_km" => !empty($row['is_km']) ? 1 : 0,
			"expense_count" => $row['expense_count'],
			"approved_amount" => $row['approved_amount'],
			"approved_amount_label" => CURR . " " . $db->rp_number_format((float) $row['approved_amount'], 2),
			"total_km" => isset($row['total_km']) ? (float) $row['total_km'] : 0,
			"master_rate" => isset($row['master_rate']) ? (float) $row['master_rate'] : 0,
			"master_rate_label" => CURR . " " . $db->rp_number_format((float) (isset($row['master_rate']) ? $row['master_rate'] : 0), 2),
			"km_calc_amount" => isset($row['km_calc_amount']) ? (float) $row['km_calc_amount'] : 0,
			"km_calc_amount_label" => CURR . " " . $db->rp_number_format((float) (isset($row['km_calc_amount']) ? $row['km_calc_amount'] : 0), 2),
		);
	}
	echo json_encode(array(
		"ack" => 1,
		"employee_id" => !empty($data['employee']) ? (int) $data['employee']['id'] : 0,
		"employee_name" => !empty($data['employee']) ? $data['employee']['name'] : "",
		"from_date" => $data['range']['from'],
		"to_date" => $data['range']['to'],
		"from_label" => date("d/m/Y", strtotime($data['range']['from'])),
		"to_label" => date("d/m/Y", strtotime($data['range']['to'])),
		"rows" => $rowsOut,
		"total_count" => (int) $data['total_count'],
		"total_amount" => (float) $data['total_amount'],
		"total_amount_label" => CURR . " " . $db->rp_number_format((float) $data['total_amount'], 2),
	));
} else {
	echo json_encode(array(
		"ack" => 0,
		"ack_msg" => isset($data['ack_msg']) ? $data['ack_msg'] : "Failed to load expense breakdown.",
		"rows" => array(),
	));
}
include("disconnect.php");
