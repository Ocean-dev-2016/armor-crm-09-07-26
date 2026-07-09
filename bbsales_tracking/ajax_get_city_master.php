<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$sid 		= $_POST["sid"];


$state_id=$_POST["state_id"];

$city=$_POST["city"];

       
$city_r = $db->rp_getData("city","*","state_id = '".$state_id."' AND isDelete=0","",0);
?>
<option value="">Select City</option>
<?php

while($city_d = mysqli_fetch_assoc($city_r))
{
    ?>
    <option <?= (strtolower($city_d['name'])==strtolower($city))?"selected":""; ?> value="<?php echo $city_d['id']; ?>"><?php echo $city_d['name']; ?></option>
    <?php
}
require_once 'disconnect.php'; 
?>
