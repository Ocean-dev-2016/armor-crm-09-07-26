<?php
$page_id=519;$page_slug='page_inward_store_page';
include('connect.php');
$customer_order_id=$_REQUEST['customer_order_id'];
///get item list
$place_of_supply=$db->rp_getValue("customer_order_request_info","state","id='".$customer_order_id."'");
 $ctable_where = "request_id='".$customer_order_id."' AND isDelete=0 AND pending_qty!=0"; 
 $orders=$db->rp_getData("customer_order_request_item","*",$ctable_where,"",0);
$items=array();
if($orders){
	while($order=mysqli_fetch_assoc($orders))
	{
			$ctable_r = $db->rp_getData("product","*","id='".$order['item_id']."'","",0);
			if($ctable_r){
				$product=mysqli_fetch_assoc($ctable_r);
			}
			$price=$db->rp_getValue("product_weight_price","price","product_id='".$order['item_id']."' AND weight_id='".$order['weight_id']."'");	
			$row= array("order_request_item_id"=>$order['id'],"item_id"=>$order['item_id'], "item_name"=>$order['item_name'],"pending_qty"=>$order['pending_qty'],"cgst"=>$product['cgst'],"sgst"=>$product['sgst'],"igst"=>$product['igst'],"price"=>$price,"weight_id"=>$order['weight_id']);
			$items[]=$row;
	}
}

echo json_encode(array("result"=>array("items"=>$items,"place_of_supply"=>$place_of_supply)));
?>