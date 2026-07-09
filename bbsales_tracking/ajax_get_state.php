<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$cid 		= $_POST["cid"];
$state_id        = $_POST["state_id"];

$country_id=$db->rp_getValue("country","id","name LIKE '".$cid."'",0);

$class_r = $db->rp_getData("class","*","country_id = '".$country_id."' AND isDelete=0","",0);

?>
<option value="">Select State</option>
<?php
while($class_d = mysqli_fetch_assoc($class_r))
{
    ?>
    <option <?= (strtolower($class_d['name'])==strtolower($state_id))?"selected":""; ?> value="<?php echo $class_d['name']; ?>" data-state_id="<?php echo $class_d['id']; ?>"><?php echo $class_d['name']; ?></option>
    <?php
}
?>
<!-- <?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$cid        = $_POST["cid"];

$id = $db->rp_getValue("country","id","LOWER(name)='".strtolower($cid)."'",0);

$where = " country_id = '$id' AND isDelete=0";
$sub_cat_r = $db->rp_getData("state","*",$where,"",0);
if(mysqli_num_rows($sub_cat_r)>0){
    ?>
    <option value="">Select State</option>
    <?php
    while($sub_cat_d = mysqli_fetch_array($sub_cat_r)){
    ?>
    <option <?php if($sub_cat_d['name']==$state_id){ echo "selected"; }  ?> value="<?php echo $sub_cat_d['name']; ?>"><?php echo $sub_cat_d['name']; ?></option>
    <?php
    }
}else{
    ?>
    <option value="">Select State</option>
    <?php
}
?> -->
<?php require_once 'disconnect.php';  ?>

