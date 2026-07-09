<?php 
$page_id=400;$page_slug='dashboard';
include('connect.php');
$ordersR = $db->rp_getData("orders","*","isDelete=0","",0);
while ($ordersD = mysqli_fetch_assoc($ordersR)) 
{
	$igst_amount = $db->rp_getValue("dispatch_detail","igst_amount","order_id='".$ordersD['id']."' AND isDelete=0",0);
	if($igst_amount==0)
	{
		$update_rows = array("igst_amount"=>$ordersD['igst_amount']);
		$update = $db->rp_update("dispatch_detail",$update_rows,"order_id='".$ordersD['id']."'",0);
	}
}
?>