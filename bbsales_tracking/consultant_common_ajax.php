<?php
$page_id = 668;
$page_slug = 'consultant_apptoval_process_report';
include("connect.php");
require_once dirname(__DIR__) . '/include/consultant_approval_process_helper.php';

$mode   = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "";
$id     = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

if ($mode == 'fire_purchase_reminders') {
	header('Content-Type: application/json; charset=utf-8');
	$fired = consultant_approval_fire_purchase_reminders($db);
	echo json_encode(array(
		'success' => true,
		'count' => count($fired),
		'reminders' => $fired,
	));
	include("disconnect.php");
	exit;
}

if ($mode == 'list_active_purchase_reminders') {
	header('Content-Type: application/json; charset=utf-8');
	/* Also persist any newly due reminders into notification bell */
	$fired = consultant_approval_fire_purchase_reminders($db);
	$active = consultant_approval_list_active_purchase_reminders($db, 20);
	echo json_encode(array(
		'success' => true,
		'count' => count($active),
		'fired_count' => count($fired),
		'reminders' => $active,
	));
	include("disconnect.php");
	exit;
}

if ($mode == 'delete' && $id > 0) {
    $where = 'id = "' . $id . '"';
    $row = array("isDelete" => "1");
    $isUpdated = $db->rp_update("sales_vs_consultant_approval_process", $row, $where);

    if ($isUpdated) {
        $response = array('ack' => 1, 'ack_msg' => 'Data Deleted Successfully');
    } else {
        $response = array('ack' => 0, 'ack_msg' => 'Data Update Failed');
    }

    echo json_encode($response);
}

if ($mode == 'status' && $id > 0) {
    $where = 'id = "' . $id . '"';
    $rows = array("status" => "1");  

   $status_update = $db->rp_update("sales_vs_consultant_approval_process", $rows, $where);

   if($status_update){
    $response = array('ack' => 1,'ack_msg' => 'Status Update SuccessFully');
   }else{
    $response = array('ack' => 0 , 'ack_msg' => 'Status Update Failed !!');
   }
    echo json_encode($response);
}

include("disconnect.php");
?>
