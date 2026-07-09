<?php
$page_id        = 572;
$ctable         = "no_order_inquiry";
include("connect.php");
$id=$_REQUEST['id'];
$where="id='".$id."'";
$get_customer_type=$db->rp_getValue("no_order_inquiry","executive_type",$where);
if($get_customer_type==1)
{
	$customer=$db->rp_getValue("no_order_inquiry","sales_executive_id",$where);
}
else
{
	$customer=$db->rp_getValue("no_order_inquiry","dealer_id",$where);
}

$check_customer=$db->rp_getTotalRecord("executive","id='".$customer."'");
if($check_customer>0)
{
	$replay=array("ack"=>1,"ack_msg"=>"Customer Found");
}
else
{
	$replay=array("ack"=>0,"ack_msg"=>"Customer  Not Found");
}
echo json_encode($replay);
?>