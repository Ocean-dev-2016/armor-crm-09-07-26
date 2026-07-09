<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$product_id = $_REQUEST['product_id'];
$count = $db->rp_getTotalRecord("product_weight_price","product_id='".$product_id."'");
if($count>0)
{
	$ack=array("ack"=>0,"ack_msg"=>"You can not Chnage Type.If you want to change type then delete added varient for this product.");
}
echo json_encode($ack);
require_once 'disconnect.php'; 
?>