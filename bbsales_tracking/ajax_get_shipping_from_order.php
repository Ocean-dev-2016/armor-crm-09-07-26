<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");

$dispatch_detailR = $db->rp_getData("dispatch_detail","*","id='".$_REQUEST['dispatch_id']."'","1",0);
$dispatch_detail = mysqli_fetch_assoc($dispatch_detailR);

$ordersR = $db->rp_getData("orders","billing_address,shipping_address,transport_name,transport_through,vendor_code,tendor_code","id='".$dispatch_detail['order_id']."'","1",0);
while($ordersD = mysqli_fetch_assoc($ordersR))
{
	if($_REQUEST['mode']=="edit")
	{
		$ordersD['billing_address'] = $db->rp_getValue("invoice_new","billing_address","id='".$_REQUEST['id']."'",0);
		$ordersD['shipping_address'] = $db->rp_getValue("invoice_new","shipping_address","id='".$_REQUEST['id']."'",0);
		$ordersD['vendor_code'] = $db->rp_getValue("invoice_new","vendor_code","id='".$_REQUEST['id']."'",0);
		$ordersD['tendor_code'] = $db->rp_getValue("invoice_new","tendor_code","id='".$_REQUEST['id']."'",0);
		$ordersD['transport_through'] = $db->rp_getValue("invoice_new","transport_through","id='".$_REQUEST['id']."'",0);
		$ordersD['transport_name'] = $db->rp_getValue("invoice_new","transport_name","id='".$_REQUEST['id']."'",0);
		$ordersD['warehouse_id'] = $db->rp_getValue("invoice_new","warehouse_id","id='".$_REQUEST['id']."'",0);
		$ordersD['way_bill_no'] = $db->rp_getValue("invoice_new","way_bill_no","id='".$_REQUEST['id']."'",0);
	}
	else
	{
		$ordersD['billing_address'] = $ordersD['billing_address'];	
		$ordersD['shipping_address'] = $ordersD['shipping_address'];
		$ordersD['vendor_code'] = $ordersD['vendor_code'];
		$ordersD['tendor_code'] = $ordersD['tendor_code'];	
		$ordersD['transport_through'] = $ordersD['transport_through'];	
		$ordersD['transport_name'] = $ordersD['transport_name'];	
		$ordersD['warehouse_id'] = $dispatch_detail['warehouse_id'];
		$ordersD['way_bill_no'] = "";
	}
	$ordersD['shipping_address'] = $ordersD['shipping_address'];
	$ordersD['transport_name'] = $ordersD['transport_name'];
	$ordersD['transport_through'] = $ordersD['transport_through'];
	$ordersD['warehouse_id'] = explode(",", $ordersD['warehouse_id']);
	$ordersD['vendor_code'] = $ordersD['vendor_code'];
	$ordersD['tendor_code'] = $ordersD['tendor_code'];
	$result = $ordersD;
}
echo json_encode($result);
?>
<?php require_once 'disconnect.php';  ?>