<?php
include('connect.php');
$ProductR = $db->rp_getData("product","id,name","isDelete=0","",0);
$count = 0;
while($ProductD = mysqli_fetch_assoc($ProductR))
{
	$count ++;
	$productWeightR = $db->rp_getData("product_weight_price","*","product_id='".$ProductD['id']."' AND isDelete=0","",0);
	$pcount = 0;
	While($productWeightD = mysqli_fetch_array($productWeightR))
	{
		$pcount++;
		//stock 0
		$update = $db->rp_update("product_weight_price",array("stock_qty"=>0),"product_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."' AND  isDelete=0 AND id='".$productWeightD['id']."'",0);

		//inward stock add
		$new_stock = $db->rp_getValue("inward_stock","SUM(pro_qty)","pro_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."'",0);

		$old_stock = $db->rp_getValue("product_weight_price","stock_qty","product_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."'",0);

		$new_stock_add = $old_stock + $new_stock;

		$add_Stock = $db->rp_update("product_weight_price",array("stock_qty"=>$new_stock_add),"product_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."' AND  isDelete=0 AND id='".$productWeightD['id']."'",0);

		/*invoice minus*/

		$invoice_Qty = $db->rp_getValue("packing_slip_item","SUM(pro_qty)","pro_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."'",0);

		$old_stock_invoice = $db->rp_getValue("product_weight_price","stock_qty","product_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."' AND  isDelete=0 AND id='".$productWeightD['id']."'",0);

		$new_stock_minus = $old_stock_invoice - $invoice_Qty;

		$update_Stock = $db->rp_update("product_weight_price",array("stock_qty"=>$new_stock_minus),"product_id='".$productWeightD['product_id']."' AND weight_id='".$productWeightD['weight_id']."' AND  isDelete=0 AND id='".$productWeightD['id']."'",0);
	}
}
echo $count." <br/>".$pcount;
?>