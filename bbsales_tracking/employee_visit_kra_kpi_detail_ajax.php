<?php
$page_id = 599;
$page_slug = 'visit_report_page';
include("connect.php");
require_once("../include/class.employee_visit_kra_report.php");

header("Content-Type: application/json; charset=utf-8");

$report = new EmployeeVisitKraReport($db);
$data = $report->getKpiDetail(
	isset($_REQUEST['employee_id']) ? $_REQUEST['employee_id'] : 0,
	isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : "",
	isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : "",
	isset($_REQUEST['kpi_type']) ? $_REQUEST['kpi_type'] : "",
	$rights
);

if (!empty($data['ack'])) {
	echo json_encode(array(
		"ack" => 1,
		"title" => isset($data['title']) ? $data['title'] : "KPI Detail",
		"employee_id" => !empty($data['employee']) ? (int) $data['employee']['id'] : 0,
		"employee_name" => !empty($data['employee']) ? $data['employee']['name'] : "",
		"from_date" => $data['range']['from'],
		"to_date" => $data['range']['to'],
		"from_label" => date("d/m/Y", strtotime($data['range']['from'])),
		"to_label" => date("d/m/Y", strtotime($data['range']['to'])),
		"columns" => isset($data['columns']) ? $data['columns'] : array(),
		"rows" => isset($data['rows']) ? $data['rows'] : array(),
		"total_label" => isset($data['total_label']) ? $data['total_label'] : "",
		"footer_note" => isset($data['footer_note']) ? $data['footer_note'] : "",
	));
} else {
	echo json_encode(array(
		"ack" => 0,
		"ack_msg" => isset($data['ack_msg']) ? $data['ack_msg'] : "Failed to load KPI detail.",
		"title" => isset($data['title']) ? $data['title'] : "KPI Detail",
		"columns" => array(),
		"rows" => array(),
	));
}
include("disconnect.php");
