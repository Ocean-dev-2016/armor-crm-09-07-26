<?php 
$page_id=400;$page_slug='dashboard';
include('connect.php');
/*$ctableR = $db->rp_getData("executive","*","isDelete=0 AND city=''","",0);
$cnt=0;
$updated_cnt=0;
while ($ctableD = mysqli_fetch_assoc($ctableR)) 
{ 
	$cnt++;
	$area_id = $db->rp_getValue("executive_map_area","area_id","executive_id='".$ctableD['id']."' ORDER BY id ASC LIMIT 1",0);
	$area_name = $db->rp_getValue("area","name","id='".$area_id."'",0);

	$mapping_id = $db->rp_update("executive",array("city"=>$area_name,"area_id"=>$area_id),"id='".$ctableD['id']."'",0);
	if($mapping_id)
	{
		$updated_cnt++;
	}
}
echo "Total Cnt=".$cnt;
echo "Updated Cnt=".$updated_cnt;*/


// ---------------------------------------------------------------------------
// update city id in map area
/*$ctableR = $db->rp_getData("executive_map_area","*","isDelete=0","",0);
$cnt=0;
$updated_cnt=0;
while ($ctableD = mysqli_fetch_assoc($ctableR)) 
{ 
	$cnt++; 
	$city_id = $db->rp_getValue("area","city_id","id='".$ctableD['area_id']."'",0);

	$mapping_id = $db->rp_update("executive_map_area",array("city_id"=>$city_id),"id='".$ctableD['id']."'",0);
	if($mapping_id)
	{
		$updated_cnt++;
	}
}
echo "Total Cnt=".$cnt;
echo "Updated Cnt=".$updated_cnt;*/
?>