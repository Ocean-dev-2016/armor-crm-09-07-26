<?php
include('connect.php');
echo "hi";exit;
$ctableR = $db->rp_getData("followup","*","isDelete=0 AND reference_table='no_order_inquiry'","",1);
$count = 0;
$ucnt = 0;
while($ctableD = mysqli_fetch_assoc($ctableR))
{
	$count++;
 
	$inquiry_created_by=$db->rp_getValue("no_order_inquiry","inquiry_created_by","id='".$ctableD['reference_id']."'",0);
	$inquiry_assign_to=$db->rp_getValue("no_order_inquiry","inquiry_assign_to","id='".$ctableD['reference_id']."'",0);

	$inquiry_created_by=($inquiry_created_by)?$inquiry_created_by:0;
	$inquiry_assign_to=($inquiry_assign_to)?$inquiry_assign_to:0;
	
// 	$updatedId = $db->rp_update("followup",array("inquiry_created_by"=>$inquiry_created_by,"inquiry_assign_to"=>$inquiry_assign_to),"id='".$ctableD['id']."'",0);
	if($updatedId)
	{
		$ucnt++;
	}
}
echo "total=".$count;
echo "<br/>updated=".$ucnt;
?>