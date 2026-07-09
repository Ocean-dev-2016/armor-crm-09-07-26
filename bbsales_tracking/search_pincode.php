<?php
$page_id=526;$page_slug='page_pincode';
include('connect.php');
$suggestion_zip=array();
///get suggestion zip details

$where = "pincode='".$_REQUEST['zip']."' AND isDelete=0"; 
 $zip_id_r=$db->rp_getData("delivery_pincode","*",$where,"",0);
 
if($zip_id_r){
	while($row_zip = mysqli_fetch_assoc($zip_id_r) )
	{
		$data=array();	
		
		$country=$db->rp_getValue("country","name","id='".$row_zip['country']."'",0);
		$state=$db->rp_getValue("state","name","id='".$row_zip['state']."'",0);
		$city=$db->rp_getValue("city","name","id='".$row_zip['city']."'",0);
		
		$row= array("zip"=>$row_zip['pincode'],"country"=>$row_zip['country'],"country_name"=>$country, "state"=>$row_zip['state'],"state_name"=>$state,"city"=>$row_zip['city'],"city_name"=>$city);
		$suggestion_zip=$row;
		
	}
}

echo json_encode(array("result"=>array("suggestion_zip"=>$suggestion_zip)),true);
?>