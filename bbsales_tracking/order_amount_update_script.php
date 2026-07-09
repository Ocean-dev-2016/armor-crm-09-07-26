<?php 
$page_id=565;$page_slug='page_order';
include("connect.php");
echo "hi";exit;
$orderR=$db->rp_getData("orders","*","","id DESC","0");
$cnt=0;
while($orderD=mysqli_fetch_assoc($orderR))
{
	$cnt++;
	$sub_total=0; 
	$tot_gst_amount=0; 
	$gst_amount=0; 
	$grand_total=0; 
	$tcs_amount=0; 
	$sub_total1=0; 

	$sub_total=$db->rp_getValue("order_product_item","SUM(pro_qty*unitprice)","order_id='".$orderD['id']."'");
	$tot_gst_amount=$db->rp_getValue("order_product_item","SUM(igst_amount)","order_id='".$orderD['id']."'");
	
	// echo $sub_total;
	// echo "-".$tot_gst_amount;exit;
	
	if($orderD['cash_discount']!="" || ($orderD['cash_discount_amount']!="" && $orderD['cash_discount_amount']!=0))
	{  
		$cash_discount_amount=$orderD['cash_discount_amount']; 
		if($sub_total>$cash_discount_amount)
		{
			$sub_total=$db->rp_num($sub_total-$cash_discount_amount);
		}
		else
		{
			$sub_total=$db->rp_num($cash_discount_amount-$sub_total);
		}
	}  
 
	if($orderD['additional_discount']!="" || ($orderD['additional_discount_amount']!="" && $orderD['additional_discount_amount']!=0))
	{
		$additional_discount_amount=$orderD['additional_discount_amount'];  
		if($sub_total>$additional_discount_amount)
		{
			$sub_total=$db->rp_num($sub_total-$additional_discount_amount);
		}
		else
		{
			$sub_total=$db->rp_num($additional_discount_amount-$sub_total);
		}
	} 
	$sub_total1=$sub_total+$orderD['transport_charge']+$orderD['packing_charge'];
	$gst_amount = $tot_gst_amount; 
	$grand_total = $db->rp_num($sub_total1+$gst_amount);

	$tcs_amount = $db->rp_num($orderD['tcs_amount']);
	$grand_total = $db->rp_num($grand_total+$tcs_amount);

	$whole = floor($grand_total);      // 1
	$fraction = $grand_total - $whole;
	$round_off =  $db->rp_number_format((float)$fraction, 2, '.', '');
	  // echo "<br/>";
	// $isUpdated=$db->rp_update("orders",array("subtotal"=>$sub_total1,"grand_total"=>$grand_total,"remaining_amount"=>round($grand_total),"igst_amount"=>$gst_amount,"roundoff"=>$round_off,"grand_total_rounded"=>round($grand_total),"tcs_amount"=>$tcs_amount),"id='".$orderD['id']."'",1);

	if($isUpdated)
	{	
		$updatedCnt++;
	}
	else
	{ 
		$nupdatedCnt++;
	}
}
echo "Total=".$cnt;		
echo "<br/>Updated=".$updatedCnt;		
echo "<br/>Not Updated=".$nupdatedCnt;	
?>