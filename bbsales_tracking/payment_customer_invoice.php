<?php
$page_id=400;$page_slug='dashboard';
include('connect.php');
$customer_id = $_REQUEST['customer_id'];
$invoice_id = $_REQUEST['invoice_id'];
?>
<option value="">Select Order</option>
<?php
$invoice_r = $db->rp_getData("orders","*","isDelete=0 AND remaining_amount>0 AND customer_id='".$customer_id."' AND status=1","id ASC",0); 
while($invoice_d = mysqli_fetch_assoc($invoice_r))
{
?> 
<option data-remaining-amt="<?= $invoice_d['remaining_amount']; ?>" <?=($invoice_id == $invoice_d['id'])?"selected":""; ?> value="<?= $invoice_d['id'] ?>"><?php echo stripslashes($invoice_d['order_no']."-(".date('d-m-Y',strtotime($invoice_d['order_date'])).")-".$invoice_d['remaining_amount']); ?></option> 
<?php 
}
?>  
