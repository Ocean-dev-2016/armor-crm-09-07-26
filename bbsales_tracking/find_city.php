<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");	
?>
<?php
if(!empty($_POST["country_id"]))
{
	// $country_id=$db->rp_getValue("country","id","name LIKE '".$_POST["country_id"]."'",0);
	$country_id=$db->rp_getValue("country","id","name LIKE '".addslashes($_POST["country_id"])."'",0);

	$class_r = $db->rp_getData("class","*","country_id = '".$country_id."'","",0);

?>
<option value="">Select State</option>
<?php
	while($class_d = mysqli_fetch_assoc($class_r))
	{
?> 
<option <?= (strtolower($class_d['name'])==strtolower($_REQUEST['state']))?"selected":""; ?> value="<?php echo $class_d['name']; ?>" data-state_id="<?php echo $class_d['id']; ?>"><?php echo $class_d['name']; ?></option>
<?php
	}
}
else
{
?>
<?php
}
?>
<?php
if(!empty($_POST["state_id"]))
{
	// $state_id=$db->rp_getValue("class","id","name = '".$_POST["state_id"]."'",0);
	$state_id=$db->rp_getValue("class","id","name LIKE '".addslashes($_POST["state_id"])."'",0);

	//$city_r = $db->rp_getData("city","*","state_id = '".$state_id."' AND isDelete=0","",1);
	$city_r = $db->rp_getData("city","*","state_id = '".$state_id."' AND isDelete=0","",0);
?>
<option value="">Select City</option>
<?php 
	while($city_d = mysqli_fetch_assoc($city_r))
	{
		// print_r(strtolower($_REQUEST['main_city']));exit;
?>
<option <?= (strtolower($city_d['name'])==strtolower($_REQUEST['city']))?"selected":""; ?> value="<?php echo $city_d['name']; ?>" data-main_city_id="<?php echo $city_d['id']; ?>"><?php echo $city_d['name']; ?></option>
<?php
	}
}
else
{
?>
<?php
} 
if(!empty($_POST["main_city"]) && $_POST['state_id']=="")
{
	$city = $_REQUEST['city'];

	// /echo "ds";exit;
	// $state_id=$db->rp_getValue("class","id","name = '".$_POST["state_id"]."'",0);
	$main_city_id=$db->rp_getValue("city","id","name LIKE '".addslashes($_POST["main_city"])."'",0);

	if ($city == "" || empty($city)) {
		// $city = $_POST['main_city']; //comment by shivani
	}
			// print_r($_POST["main_city"]);exit();
	//$city_r = $db->rp_getData("city","*","state_id = '".$state_id."' AND isDelete=0","",1);
	$area_r = $db->rp_getData("area","*","city_id = '".$main_city_id."' AND isDelete=0","",0);
?>
<option value="">Select Route</option>
<?php
	while($area_d = mysqli_fetch_assoc($area_r))
	{
?>
<option <?= (strtolower($area_d['name'])==strtolower($city))?"selected":""; ?> value="<?php echo $area_d['name']; ?>" data-city_id="<?php echo $area_d['id']; ?>"><?php echo $area_d['name']; ?></option>
<?php
	}
}
else
{
?>
<?php
}
if(!empty($_POST["class_id"]))
{
	$area_r = $db->rp_getData("area","*","class_id = '".$_POST["class_id"]."' AND isDelete=0","","");
?>
<option value="">Select Area</option>
<?php
	while($area_d = mysqli_fetch_assoc($area_r))
	{
?>
<option value="<?php echo $area_d['id']; ?>" ><?php echo $area_d['name']; ?></option>
<?php
	}
}
else
{
?>
<?php
}
?>