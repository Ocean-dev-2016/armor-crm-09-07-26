<?php 
$page_id=400;$page_slug='dashboard';
include('connect.php');
$dispatch = $db->rp_getData("executive","*","isDelete=0","",0);
while ($dispatchD = mysqli_fetch_assoc($dispatch)) 
{
	$Count = $db->rp_getTotalRecord("executive_map_area","executive_id='".$dispatchD['id']."' AND isDelete=0",0);
	if($Count<=0)
	{
	    ///echo "hello";exit;

		$class_id = $db->rp_getValue( "class", "id", "name LIKE '%".strtolower(trim($dispatchD['state']))."%'", 0 );
		$area_id = $db->rp_getValue( "area", "id", "name LIKE '%".strtolower(trim($dispatchD['city']))."%'", 0 );
		/*var_dump($area_id);*/
        
       // echo $class_id." ".$area_id; exit;
		if($area_id!="" && $class_id!="")
		{
			$mapping_id = $db->rp_insert("executive_map_area",array($dispatchD['id'],$dispatchD['type_of_executive'],$class_id,$area_id),array("executive_id","executive_type","class_id","area_id"),0);
		}
	}
}
if($mapping_id)
{
	$ack = array("ack"=>"1","ack_msg"=>"success");
	return $ack;
}
?>