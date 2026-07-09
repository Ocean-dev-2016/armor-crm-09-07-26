<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");

// $cid 		= $_POST["cid"];
$sales_id   = $_POST['sales_id'];

$selected_id   = $_POST['state_id'];

$state_id = $db->rp_getValue("class","name","id='".$selected_id."'",0);
$state_id = strtolower($state_id);

$class_id1 = $db->rp_getData("sales_executive_map_area","class_id","sales_executive_id IN(".$sales_id.") GROUP BY class_id","",0);

    $class_array = array();
    while($class_id_d=mysqli_fetch_assoc($class_id1))
    {
        
        $class_array[] = $class_id_d['class_id'];
    }

$calss_id = implode(",",$class_array);

$class_r = $db->rp_getData("class","*","id IN(".$calss_id.") AND isDelete=0","",0);

?>
<option value="">Select State</option>
<?php
while($class_d = mysqli_fetch_assoc($class_r))
{
    ?>
    <option <?= (strtolower($class_d['name'])==$state_id)?"selected":""; ?> value="<?php echo $class_d['name']; ?>" data-sales_id="<?=$sales_id;?>" data-state_id="<?php echo $class_d['id']; ?>"><?php echo $class_d['name']; ?></option>
    <?php
}
?>
<?php require_once 'disconnect.php';  ?>

