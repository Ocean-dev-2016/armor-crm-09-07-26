<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$selected_value = $_REQUEST['selected_value'];
$mode = $_REQUEST['mode'];
if($selected_value!="")
{
	$selected_value = explode(",", $selected_value);
}
else
{
	$selected_value = array();
}
if($mode=="edit")
{
	$PackingOptionsData = $db->rp_getData("packing_slip","id,packing_slip_no","customer_id = '".$_REQUEST['cid']."' AND isDelete=0",0);
}
else
{
	$PackingOptionsData = $db->rp_getData("packing_slip","id,packing_slip_no","customer_id = '".$_REQUEST['cid']."' AND isDelete=0 AND status=0",0);
}
?>
<option value="">Select Packing Slip</option>
<?php
while ($packingOptions = mysqli_fetch_assoc($PackingOptionsData))
{
	?>
	<option <?=( in_array($packingOptions['id'], $selected_value) )?"selected":""?> value="<?=$packingOptions['id']?>"><?=$packingOptions['packing_slip_no']?></option>
	<?php
}
?>
<?php require_once 'disconnect.php';  ?>