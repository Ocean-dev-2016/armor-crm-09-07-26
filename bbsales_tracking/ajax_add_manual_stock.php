<?php 
$page_id=633;$page_slug='manual_stock_page';
include("connect.php");
$ctable 	= "inward_stock";
$mode=$_REQUEST['mode'];

$product_id=$_REQUEST['product_id'];
$quantity = $_REQUEST['quantity'];
$planning_date  = $_REQUEST['planning_date'];
$p_name  = $db->clean($_REQUEST['p_name']);
$weight  = $_REQUEST['weight'];

$remark  = $db->clean($_REQUEST['remark']);

$invoice_no  = $db->clean($_REQUEST['invoice_no']);
// $invoice_date  = $db->clean($_REQUEST['invoice_date']);
$invoice_date = $_REQUEST['invoice_date'] != "" ? date('Y-m-d',strtotime($_REQUEST['invoice_date'])) : "";
$warehouse_id  = $db->clean($_REQUEST['warehouse_id']);
$to_warehouse_id  = $db->clean($_REQUEST['to_warehouse_id']);
$stock_movement = $_REQUEST['stock_movement'];
// $warehouse_id_new = (explode(",",$warehouse_id));
// echo "<pre>";
// print_r($_REQUEST); exit;
if($mode=="insert_stock")
{	
	// print_r($_REQUEST);die;
	if($stock_movement == 1){
		$planning_date = date('Y-m-d',strtotime($planning_date));
		$form_where_house_stock_qty = $db->rp_getValue("inward_stock","SUM(pro_qty)","isActive = 1 AND isDelete = 0 AND pro_id = '".$product_id."' AND weight_id = '".$weight."' AND warehouse_id = '".$warehouse_id."'",0);
		// echo "form_where_house_stock_qty =".$form_where_house_stock_qty."<br>";
		// echo "quantity =".$quantity."<br>";die;
		if($form_where_house_stock_qty >= $quantity ){
			$from_wherehouse_name = $db->rp_getValue("warehouse","name","id = ".$warehouse_id,0);
			$to_wherehouse_name = $db->rp_getValue("warehouse","name","id = ".$to_warehouse_id,0);
			
			$from_wherehouse_remark = "Deducted from ".$from_wherehouse_name." and added to ".$to_wherehouse_name." on ".date("d-m-Y",strtotime($planning_date));
			$to_wherehouse_remark=  "Added to ".$to_wherehouse_name." and deducted from ".$from_wherehouse_name." on ".date("d-m-Y",strtotime($planning_date)); 
			
			$from_wherehouse_qty = "-".$quantity;
 
			$from_wherehouse_insert = $db->rp_insert("inward_stock",array($product_id,$weight,$p_name,$from_wherehouse_qty,$planning_date,$from_wherehouse_remark,$invoice_no,$invoice_date,$warehouse_id,$stock_movement),array("pro_id","weight_id","pro_name","pro_qty","planning_date","remark","invoice_no","invoice_date","warehouse_id","stock_movement"),0);
			// print_r(array($product_id,$weight,$p_name,$quantity,$planning_date,$to_wherehouse_remark,$invoice_no,$invoice_date,$to_warehouse_id,$stock_movement));die;
			$to_wherehouse_insert = $db->rp_insert("inward_stock",array($product_id,$weight,$p_name,$quantity,$planning_date,$to_wherehouse_remark,$invoice_no,$invoice_date,$to_warehouse_id,$stock_movement),array("pro_id","weight_id","pro_name","pro_qty","planning_date","remark","invoice_no","invoice_date","warehouse_id","stock_movement"),0);
			if($from_wherehouse_insert && $to_wherehouse_insert){
				$ack=array("ack"=>1,"ack_msg"=>"Stock Transfer Successfully");
			}else{
				$ack=array("ack"=>0,"ack_msg"=>"Stock Transfer Failed");
			}
		}else{
			$ack=array("ack"=>0,"ack_msg"=>"You cannot transfer QTY to another warehouse more than your current available stock.");
		}
	}else{
		$planning_date = date('Y-m-d',strtotime($planning_date));
		$insert = $db->rp_insert("inward_stock",array($product_id,$weight,$p_name,$quantity,$planning_date,$remark,$invoice_no,$invoice_date,$warehouse_id,$stock_movement),array("pro_id","weight_id","pro_name","pro_qty","planning_date","remark","invoice_no","invoice_date","warehouse_id","stock_movement"),0);
		if($insert)
		{
			/*update main stock*/
			// $get_old_stock_qty = $db->rp_getValue("product_weight_price","stock_qty","product_id='".$product_id."' AND weight_id='".$weight."' AND isDelete=0");
			// $new_stock_qty = $get_old_stock_qty+$quantity;
			// $update = $db->rp_update("product_weight_price",array("stock_qty"=>$new_stock_qty),"product_id='".$product_id."' AND weight_id='".$weight."'");		
			/*update main stock*/
			$ack=array("ack"=>1,"ack_msg"=>"Stock Added Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Stock Added Failed");
		}
	}
	echo json_encode($ack);
}

if($mode=="delete_production_planning")
{
	$id=$_REQUEST['id'];
	$delete=$db->rp_update("inward_stock",array("isDelete"=>1),"id='".$id."'");
	if($delete)
	{
		/*update main stock*/
		$getdata = $db->rp_getData("inward_stock","*","id='".$id."'");
		$getdataD = mysqli_fetch_array($getdata);

		$get_old_stock_qty = $db->rp_getValue("product_weight_price","stock_qty","product_id='".$getdataD['pro_id']."' AND weight_id='".$getdataD['weight_id']."' AND isDelete=0");
		
		$new_update_stock = $get_old_stock_qty - $getdataD['pro_qty'];
		
		$update = $db->rp_update("product_weight_price",array("stock_qty"=>$new_update_stock),"product_id='".$getdataD['pro_id']."' AND weight_id='".$getdataD['weight_id']."'",0);	
		
		/*update main stock*/
		$ack=array("ack"=>1,"ack_msg"=>"Data Delete Successfully");
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"Data Delete Failed");
	}
	echo json_encode($ack);
}
require_once "disconnect.php";
?>
