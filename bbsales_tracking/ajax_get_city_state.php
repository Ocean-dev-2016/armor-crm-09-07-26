<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$stateData = '<select class="form-control status" name="state" id="state" onChange="filter_state(this.value);" autofocus ><option value="">Select State</option>';

$state_r = $db->rp_getData("class", "*", 0);
if (mysqli_num_rows($state_r) > 0) {
	while ($state_d = mysqli_fetch_array($state_r)) {
		$selectedVar = ($_REQUEST['state_id'] == $state_d['name']) ? "selected" : "";
		$stateData .= '<option value="'.$state_d['name'].'" '.$selectedVar.' >'.$state_d['name'].'</option>';
	}
}
$stateData .= '</select>';



$cityData = '<select class="form-control status" name="city" id="city" autofocus ><option value="">Select City</option>';
if($_REQUEST['state_id']!="")
{
	$state_id=$db->rp_getValue("state","id","name = '".$_REQUEST["state_id"]."'",0);
	$city_r = $db->rp_getData("area","*","class_id = '".$state_id."' AND isDelete=0","",0);
	if (mysqli_num_rows($city_r) > 0) {
		while ($city_d = mysqli_fetch_array($city_r)) {
			$selectedVar = ($_REQUEST['city_id'] == $city_d['name']) ? "selected" : "";
			$cityData .= '<option value="'.$city_d['name'].'" '.$selectedVar.' >'.$city_d['name'].'</option>';
		}
	}
}
$cityData .= '</select>';

echo json_encode(array("state_select"=>$stateData,"city_select"=>$cityData));
require_once 'disconnect.php'; 
?>
