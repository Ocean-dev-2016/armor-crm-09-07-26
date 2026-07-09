<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
// print_r($_REQUEST);exit();
$inq_company_id = $_REQUEST['inq_company_id'];
$company_id = $_REQUEST['company_id'];
$customer_type = $_REQUEST['customer_type'];
$customer_id = $_REQUEST['customer_id'];

	$rows=array(
		"type_of_company"      => $company_id,
		"executive_type"      => $customer_type,
		"dealer_id"      => $customer_id,
	);
	$update=$db->rp_update("no_order_inquiry",$rows,"isDelete=0 AND id='".$inq_company_id."' ",0);
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