<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$user_id      = $_POST["user_id"];
$where = "isDelete=0";
// $where = "type = '$type_id' AND isDelete=0";

$compalin_subcate_r = $db->rp_getData("executive","*",$where,"",0);
if(mysqli_num_rows($compalin_subcate_r)>0)
{
	?>
    <option value="">Select User</option>
    <?php
    while($compalin_subcate_d = mysqli_fetch_array($compalin_subcate_r))
    {
       ?>
        <option <?php if($compalin_subcate_d['id']==$user_id && $user_id!=""){ echo "selected"; }  ?> value="<?php echo $compalin_subcate_d['id']; ?>"><?php echo $compalin_subcate_d['cname']; ?></option>
        <?php
    }
}
else
{
	?>
    <option value="">Select User</option>
    <?php
}
?>
<?php require_once 'disconnect.php';  ?>