<?php
require_once("connect.php");
$se_id 		= $_POST["se_id"];
$where = "isDelete=0 AND isActive=1";

// $where = "type = '$type_id' AND isDelete=0";
$compalin_subcate_r = $db->rp_getData("sales_executive","*",$where,"",0);
if(mysqli_num_rows($compalin_subcate_r)>0)
{
	?>
    <option value="">Select Person</option>
    <?php
    while($compalin_subcate_d = mysqli_fetch_array($compalin_subcate_r))
    {
       ?>
        <option <?php if($compalin_subcate_d['id']==$se_id && $se_id!=""){ echo "selected"; }  ?> value="<?php echo $compalin_subcate_d['id']; ?>"><?php echo $compalin_subcate_d['name']; ?></option>
        <?php
    }
}
else
{
	?>
    <option value="">Select Person</option>
    <?php
}
?>
<?php require_once 'disconnect.php';  ?>