<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$sid        = $_POST["sid"];
$sales_id   = $_POST['sales_id'];
$city_id   = $_POST['main_city'];

$class_id1 = $db->rp_getData("sales_executive_map_area","area_id","sales_executive_id IN(".$sales_id.") AND class_id = '".$sid."' AND city_id='".$city_id."' GROUP BY class_id,area_id,city_id","",0);

$class_array = array();
    while($class_id_d=mysqli_fetch_assoc($class_id1))
    {
        
        $class_array[] = $class_id_d['area_id'];
    }

$area_ids = implode(",",$class_array);


// $state_id=$db->rp_getValue("class","id","name = '".$area_id ."'",0);

// $city=$_POST["city"];        
// $city_r = $db->rp_getData("area","*","area_id = '".$area_id."' AND isDelete=0","",1);

$city_r = $db->rp_getData("area","*","id IN(".$area_ids.") AND isDelete=0","",0);


?>
<option value="">Select City</option>
<?php

while($city_d = mysqli_fetch_assoc($city_r))
{
    ?>
    <option <?= (strtolower($city_d['name'])==strtolower($city))?"selected":""; ?> value="<?php echo $city_d['name']; ?>" data-city_id="<?php echo $city_d['id']; ?>"><?php echo $city_d['name']; ?></option>
    <?php
}
?>
<?php require_once 'disconnect.php';  ?>
