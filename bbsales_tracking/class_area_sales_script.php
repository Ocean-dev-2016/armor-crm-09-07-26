<?php 
$page_id=400;$page_slug='dashboard';
include('connect.php');
$dispatch = $db->rp_getData("sales_executive","*","isDelete=0","",0);
while ($dispatchD = mysqli_fetch_assoc($dispatch)) 
{
	$Count = $db->rp_getTotalRecord("sales_executive_map_area","sales_executive_id='".$dispatchD['id']."' AND isDelete=0",0);
	if($Count<=0)
	{
		$class_id = $db->rp_getValue( "class", "id", "LOWER(name) LIKE '%".strtolower(trim($dispatchD['state']))."%'", 0 );
	    if($class_id!="" && $class_id!=NULL)
		{   
		    
		    $area_id = $db->rp_getValue( "area", "id", "class_id = '".$class_id."' AND LOWER(name) LIKE '%".strtolower(trim($dispatchD['city']))."%'",0);
		
        }
		if($area_id!="" && $class_id!="")
		{

			$type=$dispatchD['type'];


			if($type=="sales_manager")
			{
			    $type_slug="GeneralManager";
			}

			if($type=="area_sales_manager")
			{
			    $type_slug="RegionalManager";
			}

			if($type=="dispatch_sales_manager")
			{
			    $type_slug="DispatchManager";
			}

			if($type=="sales_officer")
			{
			    $type_slug="SalesOfficer";
			}

			if($type=="sales_executive")
			{
			    $type_slug="SalesExecutive";
			}
			if($type=="service_executive")
			{
			    $type_slug="ServiceExecutive";
			}
			$insert = $db->rp_insert("sales_executive_map_area",array($dispatchD['id'],$type,$type_slug,$class_id,$area_id),array("sales_executive_id","executive_type","type_slug","class_id","area_id"),0);

			//$mapping_id = $db->rp_insert("executive_map_area",array($dispatchD['id'],$dispatchD['type_of_executive'],$class_id,$area_id),array("executive_id","executive_type","class_id","area_id"),0);
		}
	}
}
if($mapping_id)
{
	$ack = array("ack"=>"1","ack_msg"=>"success");
	return $ack;
}
?>