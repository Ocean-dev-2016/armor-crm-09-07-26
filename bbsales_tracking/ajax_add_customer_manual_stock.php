<?php 
$page_id=650;$page_slug='customer_manual_stock';
include("connect.php");
$ctable 	= "inward_stock";
$mode=$_REQUEST['mode'];

$product_id=$_REQUEST['product_id'];
$quantity = $_REQUEST['quantity'];
$planning_date  = $_REQUEST['planning_date'];
$p_name  = $db->clean($_REQUEST['p_name']);
$weight  = $_REQUEST['weight'];
$customer_id  = $_REQUEST['customer_id'];
$sales_id  = $_REQUEST['sales_id'];
$remark  = $db->clean($_REQUEST['remark']);
$expiry_date = date('Y-m-d',strtotime($_REQUEST['expiry_date']));
if($mode=="insert_stock")
{	
	$planning_date = date('Y-m-d',strtotime($planning_date));
	$insert = $db->rp_insert("customer_inward_stock",array($product_id,$weight,$p_name,$quantity,$planning_date,$remark,$expiry_date,$customer_id,$sales_id),array("pro_id","weight_id","pro_name","pro_qty","planning_date","remark","expiry_date","customer_id","sales_id"),0);
	if($insert)
	{
		$ack=array("ack"=>1,"ack_msg"=>"Data Added Successfully");
		/*update main stock*/
		// echo $get_current_stock;exit;
		// $get_old_stock_qty=$get_current_stock;
		// $get_old_stock_qty = $db->rp_getValue("product_weight_price","stock_qty","product_id='".$product_id."' AND weight_id='".$weight."' AND isDelete=0");
		// $new_stock_qty = $get_old_stock_qty+$quantity;
		// $new_stock_qty = $get_current_stock;
		// $update = $db->rp_update("product_weight_price",array("stock_qty"=>$new_stock_qty),"product_id='".$product_id."' AND weight_id='".$weight."'");		
		/*update main stock*/
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
	$delete=$db->rp_update("customer_inward_stock",array("isDelete"=>1),"id='".$id."'");
	if($delete)
	{
		

		// $get_old_stock_qty = $db->rp_getValue("product_weight_price","stock_qty","product_id='".$getdataD['pro_id']."' AND weight_id='".$getdataD['weight_id']."' AND isDelete=0");

		/*update main stock*/
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