<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$id=$_REQUEST['id'];
$status=$_REQUEST['status'];
$table=$_REQUEST['table'];

$update_status=$db->rp_update($table,array("status"=>$status),"id='".$id."'");
if($update_status)
{
	/*create customer*/
	/*if($status==3)
	{
		$where = " id='".$id."' AND isDelete=0";
		$inquiry_r = $db->rp_getData("no_order_inquiry","*",$where,"",0);
		while($inquiry_d = mysqli_fetch_assoc($inquiry_r))
		{

			$rows = array("type_of_executive","dealer_distributor_id","company_name","cname","phone","address","country","state","city");

			$values = array($inquiry_d['executive_type'],$inquiry_d['distributor_id'],$inquiry_d['company_name'],$inquiry_d['person_name'],$inquiry_d['mobile_number'],$inquiry_d['address'],$inquiry_d['country'],$inquiry_d['state'],$inquiry_d['city']);

			$insert = $db->rp_insert("executive",$values,$rows,0);

			
			if($inquiry_d['class_id']==0 && $inquiry_d['area_id']==0)
			{
				$class_id = $db->rp_getValue("class","id","name='".$inquiry_d['state']."'",0);
				$area_id = $db->rp_getValue("area","id","name='".$inquiry_d['city']."'",0);
			}

			$rows_insert = array("class_id","area_id","executive_id","executive_type","isDelete","isActive");
			$values_insert = array($class_id,$area_id,$insert,$inquiry_d['executive_type'],0,1);
			$inserted_id = $db->rp_insert("executive_map_area",$values_insert,$rows_insert,0);
			
		}
		
			$row = array("isDelete"=>1);
			$update = $db->rp_update("no_order_inquiry",$row,"id='".$id."'",0);
		
	}*/
	/*create customer*/
	$replay=array("ack"=>1,"ack_msg"=>"Status Updated Successfully");
}
else
{
	$replay=array("ack"=>0,"ack_msg"=>"Status Updated Failed");
}

require_once 'disconnect.php'; 
echo json_encode($replay);

?>