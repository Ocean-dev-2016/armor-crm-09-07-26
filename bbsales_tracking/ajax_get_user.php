<?php
$page_id = 400;
$page_slug = 'dashboard';
require_once("connect.php");
$user_id = isset($_POST["user_id"]) ? $_POST["user_id"] : "";
$kind = isset($_POST["kind"]) ? $_POST["kind"] : "";

$where = "isDelete=0";
$placeholder = "Select Firm";
if ($kind == "channel_partner") {
	$where .= " AND channel_partner_flag=1 AND (customer_flag=0 OR customer_flag IS NULL OR customer_flag='')";
	$placeholder = "Select Channel Partner";
} else if ($kind == "customer") {
	$where .= " AND (channel_partner_flag=0 OR channel_partner_flag IS NULL OR channel_partner_flag='')";
	$placeholder = "Select Customer";
}

echo '<option value="">' . htmlspecialchars($placeholder) . '</option>';
$compalin_subcate_r = $db->rp_getData("executive", "id,company_name,cname,client_code", $where, "company_name ASC", 0);
if ($compalin_subcate_r && mysqli_num_rows($compalin_subcate_r) > 0) {
	while ($compalin_subcate_d = mysqli_fetch_array($compalin_subcate_r)) {
		$firm = trim($compalin_subcate_d['company_name']);
		$person = trim($compalin_subcate_d['cname']);
		$label = ($firm != '') ? $firm : $person;
		if ($firm != '' && $person != '' && strcasecmp($firm, $person) != 0) {
			$label = $firm . ' - ' . $person;
		}
		if ($label == '') {
			$label = 'ID #' . $compalin_subcate_d['id'];
		}
		$sel = ($compalin_subcate_d['id'] == $user_id && $user_id != "") ? ' selected' : '';
		echo '<option value="' . (int) $compalin_subcate_d['id'] . '"' . $sel . '>' . htmlspecialchars($label) . '</option>';
	}
}
require_once 'disconnect.php';
?>
