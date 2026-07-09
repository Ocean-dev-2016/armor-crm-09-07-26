<?php
$page_id=588;$page_slug='add_invoice';
include("connect.php");
$ctable=$_REQUEST['ctable'];
$typeid=$_REQUEST['typeid'];
$prefix=$_REQUEST['prefix'];
$invoice_id=$_REQUEST['invoice_id'];
$prefixR=$db->rp_getData("prefix_master","*","id='".$typeid."'","",0);
$prefixData = mysqli_fetch_assoc($prefixR);
$order_no=0;
if($invoice_id!="")
{
	$update = $db->rp_update($ctable,array("type_id"=>$typeid),"id='".$invoice_id."'",0);
	if($update)
	{
		$invoice_no     = $db->rp_getValue($ctable,"MAX(`invoice_sr_no`)","isDelete=0 AND type_id='".$typeid."'",0);
		if($invoice_no!="")
		{	
			if($typeid==1)
			{
				$prefix =  "DOM/PT/";
			}
			else if($typeid==2)
			{
				$prefix =  "DOM/RT/";
			}
			else if($typeid==3)
			{
				$prefix =  "EXP/PT/";
			}
			else
			{
				$prefix =  "EXP/RT/";
			}
			$invoice_no2    = $invoice_no+1;
			$final_invoice_no  = $prefix . str_pad($invoice_no2, 2, '0', STR_PAD_LEFT);
			$reply = array("order_no"=>($final_invoice_no));
		}
	}
}
require_once 'disconnect.php'; 
echo json_encode($reply);

?>