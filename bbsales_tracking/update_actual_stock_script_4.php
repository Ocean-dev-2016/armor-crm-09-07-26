<?php
include('connect.php');
$ProductR = $db->rp_getData("product","id,name","isDelete=0","",0);
$count = 0;
$pcount = 0;
while($ProductD = mysqli_fetch_assoc($ProductR))
{ 
	$productWeightR = $db->rp_getData("product_weight_price","*","product_id='".$ProductD['id']."' AND isDelete=0","",0);
	//$productWeightR = $db->rp_getData("product_weight_price","*","id=1000 AND isDelete=0","",0);
	 
	While($productWeightD = mysqli_fetch_array($productWeightR))
	{ 	 
		/*Pakcing Slip minus*/

		$packing_Qty = $db->rp_getValue("packing_slip_item","SUM(pro_qty)","pro_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."' AND isDelete=0 AND created_date >'2022-06-20 16:00:00'",0);

		//echo "packing_Qty  ".$packing_Qty." -- ";


		$old_stock_packing = $db->rp_getValue("product_weight_price","stock_qty","product_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."' AND  isDelete=0 AND id='".$productWeightD['id']."'",0);
		//echo "old_stock_packing  ".$old_stock_packing." -- ";

		$new_stock_minus = ($old_stock_packing) - ($packing_Qty);

		$update_Stock = $db->rp_update("product_weight_price",array("stock_qty"=>$new_stock_minus),"product_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."' AND  isDelete=0 AND id='".$productWeightD['id']."'",0);

		$pcount++;
	}
	$count ++;
}
echo "procnt=".$count;
echo "opening_stock_entry=".$pcount;
?>