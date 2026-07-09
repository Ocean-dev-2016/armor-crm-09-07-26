<?php
include('connect.php');
$ProductR = $db->rp_getData("product","id,name","isDelete=0","",0);
$count = 0;
$pcount = 0;
while($ProductD = mysqli_fetch_assoc($ProductR))
{	
	$productWeightR = $db->rp_getData("product_weight_price","*","product_id='".$ProductD['id']."' AND isDelete=0","",0);
	
	While($productWeightD = mysqli_fetch_array($productWeightR))
	{			
		$packing_Qty = $db->rp_getValue("packing_slip_item","SUM(pro_qty)","pro_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."' AND isDelete=0 AND created_date <'2022-06-20 16:00:00'",0);

		$update_stock = $db->rp_update("product_weight_price",array("stock_qty"=>0,"opening_stock_qty"=>$packing_Qty),"product_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."' AND isDelete=0",0);

		$pcount++;		
	}
	$count ++;
}
echo "procnt=".$count;
echo "proweightcnt=".$pcount;
?>