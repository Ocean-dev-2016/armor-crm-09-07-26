<?php
$page_id=565;$page_slug='page_order';
include("connect_in.php");

$order_id = $_REQUEST['order_id'];
$flag = $_REQUEST['flag'];
$media_id = $_REQUEST['media_id'];
$media_id_array="";

if (isset($order_id) && isset($media_id) )
{ 
	if ($flag == 'delete_pdf_attachment') 
	{
		$rows = array("isDelete"=>1,);

		$db->rp_update("media",$rows,"reference_id='".$_REQUEST["order_id"]."'AND id='".$media_id."'",0);

		// Remove Media id from customer table
		$media_id_array=$db->rp_getValue("orders","pdf_attachment","id='".$_REQUEST["order_id"]."'",0);

		$media_id_array = explode(",", $media_id_array);

		$del_val=$media_id;
		if (($key = array_search($del_val, $media_id_array)) !== false) {
		    unset($media_id_array[$key]);
		}
		
		$media_id_array = implode(",", $media_id_array);
		$rows = array("pdf_attachment"=>$media_id_array,);

		$db->rp_update("orders",$rows,"id='".$_REQUEST["order_id"]."'",0);

		$reply = array("ack"=>1,"ack_msg"=>"PDF Attachment Delete successfully ");
		
	}
	else
	{
		$reply = array("ack"=>0,"ack_msg"=>"Required Flag not available Please check.....");
	}
}
else
{
	$reply = array("ack"=>0,"ack_msg"=>"Required order_id & media_id not available Please check.....");
}
echo json_encode($reply);
?>

