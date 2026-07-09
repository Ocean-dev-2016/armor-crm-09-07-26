<?php 
$page_id=400;$page_slug='dashboard';
include("connect.php");


$InquiryR = $db->rp_getData("no_order_inquiry","*","date(created_date) = '2021-04-02'");
while ($Inquiry = mysqli_fetch_assoc($InquiryR))
{
	$class_name = $Inquiry['state'];
	$area_name = $Inquiry['city'];
	$class_id = 0;
	$area_id = 0;
	if($class_name!="")
	{
		$CheckClassAvailable = $db->rp_getValue("class","COUNT(*)","LOWER(name) = '".trim(strtolower($class_name))."' AND isDelete = 0 AND isActive = 1");
		if($CheckClassAvailable>0)
		{
			$class_id = $db->rp_getValue("class","id","LOWER(name) = '".trim(strtolower($class_name))."' AND isDelete = 0 AND isActive = 1");
		}
		else
		{
			$InsertData = array(
				"name" => trim($class_name),
				"slug" => $db->rp_createSlug($class_name),
				"isDelete" => 0,
				"isActive" => 1,
			);
			$class_id = $db->rp_insert("class",array_values($InsertData),array_keys($InsertData),0);
		}

		if($area_name!="")
		{
			$CheckAreaAvailable = $db->rp_getValue("area","COUNT(*)","LOWER(name) = '".trim(strtolower($area_name))."'  AND class_id='".$class_id."' AND isDelete = 0 AND isActive = 1");
			if($CheckAreaAvailable>0)
			{
				$area_id = $db->rp_getValue("area","id","LOWER(name) = '".trim(strtolower($area_name))."' AND class_id='".$class_id."' AND isDelete = 0 AND isActive = 1");
			}
			else
			{
				$InsertData = array(
					"name" => trim($area_name),
					"area_name" => $db->rp_createSlug($area_name),
					"class_id" => $class_id,
					"isDelete" => 0,
					"isActive" => 1,
				);
				$area_id = $db->rp_insert("area",array_values($InsertData),array_keys($InsertData),0);
			}
		}

		$db->rp_update("no_order_inquiry",array("class_id"=>$class_id,"area_id"=>$area_id),"id='".$Inquiry['id']."'");
	}
}
require_once 'disconnect.php'; 
?>
