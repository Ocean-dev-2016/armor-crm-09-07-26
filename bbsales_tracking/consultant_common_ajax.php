<?php
$page_id = 668;
$page_slug = 'consultant_apptoval_process_report';
include("connect.php");

$mode   = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "";
$id     = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

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
