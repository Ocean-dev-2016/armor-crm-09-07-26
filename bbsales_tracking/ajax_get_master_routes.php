<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");

$mr_id      = $_POST["mr_id"];

$sales_id1 = $db->rp_getValue("master_route","sales_id","isDelete=0 AND id='".$mr_id."'",0);

$where = "id='".$sales_id1."' AND isDelete=0";

$sales_r = $db->rp_getData("sales_executive","*",$where,"",0);
if($sales_r>0)
{

	?>
    <option value="">Select Sales Person</option>
    <?php
    while($sales_d = mysqli_fetch_array($sales_r))
    {
       ?>
        <option <?php if($sales_d['id']==$sales_id1){ echo "selected"; }  ?> value="<?php echo $sales_d['id']; ?>" ><?php echo $sales_d['name']; ?></option>

        <?php
    }
}
else
{
	?>
    <option value="">Select Sales Person</option>
    <?php
}
?>
<?php require_once 'disconnect.php';  ?>