<?php 
$page_id=652;$page_slug='scheme_master';
include("connect.php");
$ctable 	= "scheme_master_item";

extract($_REQUEST);


if($mode=="add_scheme_item")
{	

	$values=array($product_id,$weight_id,$qty,$product_id_2,$weight_id_2,$free_qty,$scheme_type,$scheme_id);
	$rows=array("product_id","weight_id","qty","product_id_2","weight_id_2","free_qty","scheme_type","scheme_id");

	$is_insert=$db->rp_insert("scheme_master_item",$values,$rows,0);
	if($is_insert)
	{
		$ack=array("ack"=>1,"ack_msg"=>"Item Added Successfully");
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"Item Added Failed");
	}
	
	echo json_encode($ack);
}

if($mode=="delete_scheme_items")
{
	$id=$_REQUEST['id'];
	$delete=$db->rp_update("scheme_master_item",array("isDelete"=>1),"id='".$id."'");
	if($delete)
	{
		$ack=array("ack"=>1,"ack_msg"=>"Data Delete Successfully");
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"Data Delete Failed");
	}
	echo json_encode($ack);
}
require_once 'disconnect.php'; 
?>