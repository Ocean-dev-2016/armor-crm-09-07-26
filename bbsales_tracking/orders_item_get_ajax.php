<?php
	$page_id=400;$page_slug='dashboard';
	include('connect.php');
	$ctable='purchase_order_item';

	$ctable_where = "order_id='".$_REQUEST['order_id']."' AND (status=0 OR status=4) AND isDelete=0"; 
	$ctable_r = $db->rp_getData("order_product_item","*",$ctable_where,"",0);

	$transport_through = $db->rp_getValue("orders","transport_through","id='".$_REQUEST['order_id']."'");
	$transport_name = $db->rp_getValue("orders","transport_name","id='".$_REQUEST['order_id']."'",0);

	/*if($transport_through!="")
	{
		$transport_through = $db->rp_getValue("transport_by","name","id='".$transport_by."'",0);
	}
	if($transport_name!="")
	{
		$transport_name = $db->rp_getValue("transport_master","name","id='".$transport_through."'");
	}*/

	while($row_po = mysqli_fetch_assoc($ctable_r) ){
	$row_po['stock_qty']=$db->rp_getValue("product_weight_price","stock_qty","product_id='".$row_po['pro_id']."' AND weight_id='".$row_po['weight_id']."'",0);
	$unit_id = $db->rp_getValue("product","unit_id","id='". $row_po['pro_id']."'");
	$row_po['unit_name'] = $db->rp_getValue("unit","name","id='". $unit_id."'");
	$row_po['pro_description'] = $row_po['pro_description'];

	// $row_po['top_cat_id'] = $db->rp_getValue("product","tcid","id='". $row_po['pro_id']."'");

	$row_po['box_qty'] = $row_po['box_qty'];
	$row_po['cartoon_qty'] = $row_po['cartoon_qty'];
	$row_po['loose_qty'] = $row_po['loose_qty'];
	
	$items[]=$row_po;
	
	}
	echo json_encode(array("result"=>array("items"=>$items,"transport_through"=>$transport_through,"transport_by"=>$transport_name)),true);
	require_once "disconnect.php";
?>