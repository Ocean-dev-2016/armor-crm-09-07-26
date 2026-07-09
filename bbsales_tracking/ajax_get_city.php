<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$sid        = $_POST["sid"];

$cityArr = explode(",",$sid);
// print_r($cityArr);exit;
foreach ($cityArr as $value) {
    // code...
    $city_id[]=$db->rp_getValue("city","id","name = '".$value ."' AND isDelete=0",0);
}

$city=$_POST["city"]; 
if ($city == "" || empty($city)) {
    $city = $sid;
}

$city_r = $db->rp_getData("area","*","city_id IN (".implode(",",$city_id).") AND isDelete=0","",0);
// $city_r = $db->rp_getData("area","*","city_id = '".$city_id."' AND isDelete=0","",0);
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
 <?php require_once("disconnect.php"); ?>
<!-- <?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$sid        = $_POST["sid"];

$id = $db->rp_getValue("state","id","LOWER(name)='".trim(strtolower($sid))."'",0);

$where = "state_id = '$id' AND isDelete=0";
$sub_cat_r = $db->rp_getData("city","*",$where,"",1);
if(mysqli_num_rows($sub_cat_r)>0){
    ?>
    <option value="">Select City</option>
    <?php
    while($sub_cat_d = mysqli_fetch_array($sub_cat_r)){
    ?>
    <option <?php if($sub_cat_d['name']==$city){ echo "selected"; }  ?> value="<?php echo $sub_cat_d['name']; ?>"><?php echo $sub_cat_d['name']; ?></option>
    <?php
    }
}else{
    ?>
    <option value="">Select City</option>
    <?php
}
?> -->
