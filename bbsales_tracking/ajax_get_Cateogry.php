<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$top_cat_id 		= $_POST["top_cat_id"];


$top_cat_id1 = $db->rp_getValue("top_category_master","id","id = '".$top_cat_id ."'",0);

        
$category_r = $db->rp_getData("category_master","*","tcid = '".$top_cat_id1."' AND isDelete=0","",0);
?>
<option value="">Select category</option>
<?php

while($category_d = mysqli_fetch_assoc($category_r))
{
    ?>
    <option <?= (strtolower($category_d['name'])==$city)?"selected":""; ?> value="<?php echo $category_d['id']; ?>" data-category_id="<?php echo $category_d['id']; ?>"><?php echo $category_d['name']; ?></option>
    <?php
}
 require_once 'disconnect.php'; 
?>