<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");

$inquiry_id = $_REQUEST['inquiry_id'];
$status = $_REQUEST['status'];

// if($status != "")
// {
	$rows=array(
		"status"      => $status,
	);
	$update=$db->rp_update("no_order_inquiry",$rows,"isDelete=0 AND id='".$inquiry_id."' ",0);
	if($update)
	{
		$db->addStatusTimelineEntry($inquiry_id,$status);
		$reply=array("ack"=>1,"developer_msg"=>"Status Update Successfully!!","ack_msg"=>"Data insert failed!!");
            echo json_encode($reply);
	}
	else
	{
		$reply=array("ack"=>0,"developer_msg"=>"Status Update failed!!","ack_msg"=>"Data insert failed!!");
            echo json_encode($reply);
	}

	// $reply=array("ack"=>0,"developer_msg"=>"You not select any option!!","ack_msg"=>"Data insert failed!!");
 //            echo json_encode($reply);
// }
include "disconnect.php";

?>