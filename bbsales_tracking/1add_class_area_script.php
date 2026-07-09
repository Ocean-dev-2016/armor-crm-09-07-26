<?php 
$page_id=400;$page_slug='dashboard';
include('connect.php');
$dispatch = $db->rp_getData("executive","*","isDelete=0");
while ($dispatchD = mysqli_fetch_assoc($dispatch)) 
{
	$class_id = $db->rp_getValue( "class", "id", "name LIKE '%".strtolower($dispatchD['state'])."%'", 0 );
	$area_id = $db->rp_getValue( "area", "id", "name LIKE '%".strtolower($dispatchD['city'])."%'", 0 );

	$mapping_id = $db->rp_insert("executive_map_area",array($dispatchD['id'],$dispatchD['type_of_executive'],$class_id,$area_id),array("executive_id","executive_type","class_id","area_id"),0);
}
?>