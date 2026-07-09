<?php
$page_id=569;$page_slug='dispatch_pages';
include("connect.php");
$dispatchid = $_REQUEST['dispatchid'];
$ctable 	= "dispatch_detail";
$dispatch_r = $db->rp_getData($ctable, "*", "id='" . $dispatchid . "' AND isDelete=0", "", 0);
if ($dispatch_d = mysqli_fetch_assoc($dispatch_r)) 
{
	$packingSlipArray['customer_id']       = $dispatch_d['customer_id'];
	$packingSlipArray['customer_type']     = $dispatch_d['order_type'];
	$packingSlipArray['dispatch_id']       = $dispatchid;
	$packing_slip_no                       = $db->getLastInsertId('packing_slip');
	$packingSlipArray['packing_slip_no']   = PACKING_SLIP_NO . str_pad($packing_slip_no, 2, '0', STR_PAD_LEFT);
	$packingSlipArray['packing_slip_date'] = date('Y-m-d');
	$packingSlipArray['isDelete']          = 0;
	$packingSlipArray['isActive']          = 1;

	$PackingSlipId = $db->rp_insert("packing_slip",array_values($packingSlipArray),array_keys($packingSlipArray),0);
	if(PackingSlipId)
	{
		$reply1 = array("ack" => 1, "ack_msg" => "PackingSlip Generated Successfully", "packing_slip_id" =>$PackingSlipId);
	}
	else 
	{
		$reply1 = array("ack" => 0, "ack_msg" => "Something Went Wrong.Please Check Again..");
	}
	echo json_encode($reply1);
}
require_once 'disconnect.php'; 
?>