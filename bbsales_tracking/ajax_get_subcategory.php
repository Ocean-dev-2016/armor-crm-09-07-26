<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$category_id      = $_POST["category_id"];
$compalin_subcate_id      = $_POST["compalin_subcate_id"];
$where = "complain_category_id = '$category_id' AND isDelete=0";
$compalin_subcate_r = $db->rp_getData("complain_sub_category","*",$where,"",0);
if(mysqli_num_rows($compalin_subcate_r)>0)
{
   ?>
    <option value="">Select subcategory</option>
    <?php
    while($compalin_subcate_d = mysqli_fetch_array($compalin_subcate_r))
    {
       ?>
        <option <?php if($compalin_subcate_d['id']==$compalin_subcate_id){ echo "selected"; }  ?> value="<?php echo $compalin_subcate_d['id']; ?>"><?php echo $compalin_subcate_d['name']; ?></option>
        <?php
    }
}
else
{
   ?>
    <option value="">Select compalin_subcate</option>
    <?php
}
?>
<?php require_once 'disconnect.php';  ?>