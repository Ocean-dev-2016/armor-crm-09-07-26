<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$customer_id 		= $_POST["customer_id"];
$selected_value      = $_POST["invoice_id"];
$where = "customer_id = '$customer_id' AND isDelete=0 AND status=1";
$invoice_r = $db->rp_getData("invoice_new","*",$where,"",0);
if(mysqli_num_rows($invoice_r)>0)
{
	?>
    <option value="">Select Invoice</option>
    <?php

    $selected_value = explode(",",$selected_value);
    while($invoice_d = mysqli_fetch_array($invoice_r))
    {
        
        ?>
        <option <?=( in_array($invoice_d['id'], $selected_value) )?"selected":""?> value="<?php echo $invoice_d['id']; ?>" ><?php echo $invoice_d['invoice_no']; ?></option>
        <?php
    }
}
else
{
	?>
    <option value="">Select Invoice</option>
    <?php
}
?>
<?php require_once 'disconnect.php';  ?>