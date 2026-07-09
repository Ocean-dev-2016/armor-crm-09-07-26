<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
// var_dump($_REQUEST);exit;
if($_REQUEST['id']!="" && $_REQUEST['id']!=0)
{
	$id=$_REQUEST['id'];
	$count=$db->rp_getTotalRecord("no_order_inquiry","id IN (".$id.") AND isDelete=0",0);
	
	if($count != 0)
	{ 
		$rows=array("isDelete"=>1); 
		$where	= "id IN (".$id.")";
		$isUpdated = $db->rp_update("no_order_inquiry",$rows,$where,0);  
	}  
	if($isUpdated)
	{ 
		$response=array('ack'=>1,'ack_msg'=>'Data Deleted Successfully');
		echo json_encode($response);
	}
	else
	{
		$response=array('ack'=>0,'ack_msg'=>'Data Update Failed');
		echo json_encode($response);
	}
}   
?>