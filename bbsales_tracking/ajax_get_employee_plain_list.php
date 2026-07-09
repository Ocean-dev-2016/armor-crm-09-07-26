<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$customer_type 		= $_POST["sales_type"];
$where = "type = '".$customer_type."' AND isDelete=0";
$customer_r = $db->rp_getData("sales_executive","*",$where,"",0);
if(mysqli_num_rows($customer_r)>0)
{
?>
    <option value="">Select Employee</option>
    <?php
    while($customer_d = mysqli_fetch_array($customer_r))
    {
?>
        <option value="<?=$customer_d['id'];?>"><?=$customer_d['name']." - ".$customer_d['username'];?></option>
<?php
    }
}
else
{
	?>
    <option value="">Select Employee</option>
    <?php
}
?>
<?php require_once 'disconnect.php';  ?>