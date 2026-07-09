<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$sid        = $_POST["sid"];

$stateArr = explode(",",$sid);
foreach ($stateArr as $value) {
    // code...
    // $city_id[]=$db->rp_getValue("city","id","name = '".$value ."'",0);
    $state_id[]=$db->rp_getValue("class","id","name = '".$value ."' AND isDelete=0 ",0);
}


$city=$_POST["city"];        
$city_r = $db->rp_getData("city","*","state_id IN (".implode(",",$state_id).") AND isDelete=0","",0);
// $city_r = $db->rp_getData("city","*","state_id = '".$state_id."' AND isDelete=0","",0);
?>
<option value="">Select City</option>
<?php

while($city_d = mysqli_fetch_assoc($city_r))
{
    ?>
    <option <?= (strtolower($city_d['name'])==strtolower($city))?"selected":""; ?> value="<?php echo $city_d['name']; ?>" ><?php echo $city_d['name']; ?></option>
    <?php
}
?>
 <?php require_once("disconnect.php"); ?>

