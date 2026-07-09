<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$selected_value1 = $_REQUEST['selected_value'];
$mode = $_REQUEST['mode'];
if($selected_value1!="" && $selected_value1!=undefined)
{
	$selected_value = explode(",", $selected_value1);
}
else
{
	$selected_value = array();
}
/*$dispatch_ids = $db->rp_getValue("packing_slip","dispatch_id","id = '".$_REQUEST['packing_slip_id']."' AND isDelete=0",0);*/
$dispatch_ids = array();
$dispatch_idR = $db->rp_getData("packing_slip","dispatch_id","id IN (".$_REQUEST['packing_slip_id'].") AND isDelete=0","",0);
while($dispatch_idD = mysqli_fetch_array($dispatch_idR))
{
	$dispatch_ids[] = $dispatch_idD['dispatch_id'];
}
$dispatch_ids = implode(",",$dispatch_ids);
//$dispatchOptionsData = $db->rp_getData("dispatch_detail","id,dispatch_no","customer_id = '".$_REQUEST['cid']."' AND isDelete=0 AND status = 0");


if($selected_value1!="" && $selected_value1!=undefined)
{ 
	$dispatchOptionsData = $db->rp_getData("dispatch_detail","id,dispatch_no","id IN (".$selected_value1.") AND isDelete=0","",0);
}
else
{ 
	// $dispatchOptionsData = $db->rp_getData("dispatch_detail","id,dispatch_no","id IN (".$dispatch_ids.") AND isDelete=0 AND status = 0","",0);
	$dispatchOptionsData = $db->rp_getData("dispatch_detail","id,dispatch_no","id IN (".$dispatch_ids.") AND isDelete=0","",0);
}

?>
<option value="">Select Dispatch</option>
<?php
while ($dispatchOptions = mysqli_fetch_assoc($dispatchOptionsData))
{
	?>
	<option <?=( in_array($dispatchOptions['id'], $selected_value) )?"selected":""?> value="<?=$dispatchOptions['id']?>"><?=$dispatchOptions['dispatch_no']?></option>
	<?php
}
require_once 'disconnect.php'; 
?>