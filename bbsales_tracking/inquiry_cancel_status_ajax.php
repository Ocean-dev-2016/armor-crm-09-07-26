<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$inq_reason_id = $_REQUEST['inq_reason_id'];
$reason_id = $_REQUEST['reason_id'];
$remark = $_REQUEST['remark'];

	$rows=array(
		"followup_reason_id"      => $reason_id,
		"status"      => '-2',
		"cancel_inq_remark"      => $remark,
	);
	$update=$db->rp_update("no_order_inquiry",$rows,"isDelete=0 AND id='".$inq_reason_id."' ",0);
	if($update)
	{
		$reply=array("ack"=>1,"developer_msg"=>"Status Update Successfully!!","ack_msg"=>"Data insert failed!!");
       	echo json_encode($reply);
	}
	else
	{
		$reply=array("ack"=>0,"developer_msg"=>"Status Update failed!!","ack_msg"=>"Data insert failed!!");
        echo json_encode($reply);
	}
include "disconnect.php";

?>