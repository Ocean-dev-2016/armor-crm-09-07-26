<?php 
$page_id=616;$page_slug='production_planning_page';
include("connect.php");
$ctable 	= "production_planning";
$mode=$_REQUEST['mode'];

$product_id=$_REQUEST['product_id'];
$quantity = $_REQUEST['quantity'];
$planning_date  = $_REQUEST['planning_date'];
$p_name  = $_REQUEST['p_name'];
$weight  = $_REQUEST['weight'];

if($mode=="add_production_planning")
{	
	$dup_where1 = "pro_id = '".$product_id."' AND  planning_date = '".date('Y-m-d',strtotime($planning_date))."' AND isDelete=0";
	$r1 = $db->rp_dupCheck("production_planning",$dup_where1,0);
	if($r1>0)
	{
		$ack=array("ack"=>0,"ack_msg"=>"This Product Are Already Added With This Planning Date");
	}
	else
	{
		$planning_date = date('Y-m-d',strtotime($planning_date));
		$insert = $db->rp_insert("production_planning",array($product_id,$weight,$p_name,$quantity,$planning_date),array("pro_id","weight_id","pro_name","pro_qty","planning_date"),0);
	}
		
	if($insert)
	{
		$ack=array("ack"=>1,"ack_msg"=>"Data Added Successfully");
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"Data Added Failed");
	}
	echo json_encode($ack);
}

if($mode=="delete_production_planning")
{
	$id=$_REQUEST['id'];
	$delete=$db->rp_update("production_planning",array("isDelete"=>1),"id='".$id."'");
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