<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$sid 		= $_POST["sid"];
$sales_id   = $_POST['sales_id'];

$class_id1 = $db->rp_getData("sales_executive_map_area","city_id","sales_executive_id IN(".$sales_id.") AND class_id = '".$sid."' GROUP BY class_id,city_id","",0);

$class_array = array();
    while($class_id_d=mysqli_fetch_assoc($class_id1))
    {
        
        $class_array[] = $class_id_d['city_id'];
    }

$city_ids = implode(",",$class_array);


// $state_id=$db->rp_getValue("class","id","name = '".$city_id ."'",0);

// $city=$_POST["city"];        
// $city_r = $db->rp_getData("area","*","city_id = '".$city_id."' AND isDelete=0","",1);

$city_r = $db->rp_getData("city","*","id IN(".$city_ids.") AND isDelete=0","",0);


?>
<option value="">Select City</option>
<?php

while($city_d = mysqli_fetch_assoc($city_r))
{
    ?>
    <option <?= (strtolower($city_d['name'])==strtolower($city))?"selected":""; ?> value="<?php echo $city_d['id']; ?>" data-city_id="<?php echo $city_d['id']; ?>"><?php echo $city_d['name']; ?></option>
    <?php
}
?>
<?php require_once 'disconnect.php';  ?>
