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
		$get_opening_stock = $db->rp_getValue("opening_stock_entry","stock","code='".$productWeightD['catno']."'",0);
		if($get_opening_stock=="")
		{
			$get_opening_stock = 0;
		} 
		$values=array("pro_id","weight_id","pro_name","pro_qty","planning_date","remark","invoice_no","invoice_date","warehouse_id","isActive","created_date");

		$rows=array($productWeightD['product_id'],$productWeightD['weight_id'],$ProductD['name'],$get_opening_stock,"2022-06-20","","stock Adjustment","2022-06-20","1",1,"2022-06-20 16:01:00");

		$is_insert=$db->rp_insert("inward_stock",$rows,$values,0);
		$pcount++;
	}
	$count ++;
}
echo "procnt=".$count;
echo "proweightcnt=".$pcount;
?>