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
		$new_stock = $db->rp_getValue("inward_stock","SUM(pro_qty)","pro_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."' AND isDelete=0 AND created_date > '2022-06-20 16:00:00'",0);
		//echo "new_stock ".$new_stock." -- "; 
		$old_stock = $db->rp_getValue("product_weight_price","stock_qty","product_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."' AND  isDelete=0 AND id='".$productWeightD['id']."'",0);
		//echo "old stock ".$old_stock." -- ";
		$new_stock_add = ($old_stock) + ($new_stock);

		$add_Stock = $db->rp_update("product_weight_price",array("stock_qty"=>$new_stock_add),"product_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."' AND  isDelete=0 AND id='".$productWeightD['id']."'",0);
//echo "add_Stock ".$add_Stock." -- ";
 
		$pcount++;
	}
	$count ++;
}
echo "procnt=".$count;
echo "opening_stock_entry=".$pcount;
?>