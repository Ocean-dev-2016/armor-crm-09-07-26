<?php
$page_id=565;$page_slug='page_order';
include("connect.php");
$pro_qty	= $_REQUEST['pro_qty'];
$id	= $_REQUEST['id'];



if($_REQUEST['mode'] == "edit")
{

	$update = $db->rp_update("order_scheme_items",array("pro_qty"=>$pro_qty,"is_edited"=>1),"id='".$id."'",0);


	if($update)
	{
		$reply=array("ack"=>1,"ack_msg"=>"Update Successfully");
	}
	else
	{	
		$reply=array("ack"=>0,"ack_msg"=>"Update Failed");
	}
}

elseif ($_REQUEST['mode'] == "delete") 
{
	$update = $db->rp_update("order_scheme_items",array("isDelete"=>1,"is_edited"=>1),"id='".$id."'",0);


	if($update)
	{
		$reply=array("ack"=>1,"ack_msg"=>"Delete Successfully");
	}
	else
	{
		$reply=array("ack"=>0,"ack_msg"=>"Delete Failed");
	}
}

echo json_encode($reply);
require_once 'disconnect.php'; 
?>