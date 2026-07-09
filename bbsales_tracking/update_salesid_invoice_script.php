<?php
include('connect.php');
$ctable_r = $db->rp_getData("invoice_new","*","","",0);
$count = 0;
 
while($ctable_d = mysqli_fetch_assoc($ctable_r))
{ 
	$count++;
	$sales_executive_id = $db->rp_getValue("dealer_distributor_network","sales_executive_id","id='".$ctable_d['created_by']."'",0); 
// echo $sales_executive_id;exit;
	$db->rp_update("invoice_new",array("sales_id"=>$sales_executive_id),"id='".$ctable_d['id']."'","",0); 	 
}
echo "procnt=".$count; 
?>