<?php
include('connect.php');
$ProductR = $db->rp_getData("area","id,city_id","isDelete=0","",0);
$count = 0;
while($ProductD = mysqli_fetch_assoc($ProductR))
{
	$count ++;
	
	
		//echo "string";exit;
		$pcount++;
		//stock 0
		
		$country_id = $db->rp_getValue("city","country_id","isDelete=0 AND id='".$ProductD['city_id']."'",0);

		if($country_id!=101){

		$update_Stock = $db->rp_update("area",array("isDelete"=>1),"id='".$ProductD['id']."'",0);
		}
		

		

}
echo $count." <br/>".$pcount;
?>