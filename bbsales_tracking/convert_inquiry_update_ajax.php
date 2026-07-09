<?php 
$page_id=400;$page_slug='dashboard';
include("connect.php");
if($_REQUEST['id']!="" && $_REQUEST['id']!=0)
{
	$id=$_REQUEST['id'];

	$count=$db->rp_getTotalRecord("no_order_inquiry","id IN (".$id.") AND isDelete=0",0);
	if($count != 0)
	{
		$rows=array("inquiry_assign_to"=>$_REQUEST['inquiry_assign_to']); 
		// $rows=array("inquiry_lead_flag"=>0,"inquiry_type"=>0,"inquiry_date"=>date('Y-m-d'),"sales_executive_id"=>$_REQUEST['inquiry_assign_to'],"inquiry_assign_to"=>$_REQUEST['inquiry_assign_to'],"inquiry_created_by"=>$_REQUEST['inquiry_assign_to'],"inq_status"=>2,"entry_flag"=>1); 
		$where	= "id IN (".$id.")";
		$isUpdated = $db->rp_update("no_order_inquiry",$rows,$where,0);  
	}

	if($isUpdated)
	{
		$response=array('ack'=>1,'ack_msg'=>'Data Update Successfully');
		echo json_encode($response);
	}
	else
	{
		$response=array('ack'=>0,'ack_msg'=>'Data Update Failed');
		echo json_encode($response);
	}
} 
?>