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
$dispatchOptionsData = $db->rp_getData("dispatch_detail","id,dispatch_no","customer_id = '".$_REQUEST['cid']."' AND isDelete=0");
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