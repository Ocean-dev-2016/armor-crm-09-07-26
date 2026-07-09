<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$tcid 		= $_POST["tcid"];

$where = " tcid = '$tcid' AND isDelete=0";
// $unit_id=$db->rp_getValue("top_category_master","unit_id","isDelete=0 AND id='".$tcid."'",0);
//$customer_unit_id=$db->rp_getValue("top_category_master","customer_unit_id","isDelete=0 AND id='".$tcid."'");
$sub_cat_r = $db->rp_getData("category_master","*",$where);
if(mysqli_num_rows($sub_cat_r)>0){
	?>
    <option value="">Select Top Category</option>
    <?php
	while($sub_cat_d = mysqli_fetch_array($sub_cat_r)){
	?>
    <option value="<?php echo $sub_cat_d['id']; ?>" ><?php echo $sub_cat_d['name']; ?></option>
    <?php
	}
}else{
	?>
    <option value="">Select Top Category</option>
    <?php
}
?>
<?php require_once 'disconnect.php';  ?>