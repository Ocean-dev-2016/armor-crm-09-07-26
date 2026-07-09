<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$customer_id = $_POST["customer_id"];

$customer_r=$db->rp_getValue("executive","top_category_id","isDelete=0 AND isActive=1 AND id='".$customer_id."'",0);

$custoer_type_r=$db->rp_getValue("executive","type_of_executive","isDelete=0 AND isActive=1 AND id='".$customer_id."'",0);

if($custoer_type_r=="1" || $custoer_type_r=="2")
{
    $cat_r = $db->rp_getData("top_category_master","id,name","id IN (".$customer_r.") AND isDelete=0","",0);
}
else
{
    $cat_r = $db->rp_getData("top_category_master","id,name","isDelete=0","",0);

}       
?>
<option value="">Select category</option>
<?php

while($cat_d = mysqli_fetch_assoc($cat_r))
{
    ?>
    <option value="<?php echo $cat_d['id']; ?>" data-category_id="<?php echo $cat_d['id']; ?>"><?php echo $cat_d['name']; ?></option>
    <?php
}
?>
<?php require_once 'disconnect.php';  ?>