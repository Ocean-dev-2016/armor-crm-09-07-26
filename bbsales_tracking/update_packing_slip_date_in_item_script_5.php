<?php 
include('connect.php');

$sales_r = $db->rp_getData("packing_slip","*","","",0);
while($sales_d = mysqli_fetch_assoc($sales_r))
{
	$db->rp_update("packing_slip_item",array("created_date"=>$sales_d['packing_slip_date']),"packing_slip_id='".$sales_d['id']."'",0);
}