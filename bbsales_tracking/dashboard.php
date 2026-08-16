<?php
$page_id = 400;
$page_slug = 'dashboard';
include("connect.php");

$query = $_GET;
$is_cp_dash = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
if ($is_cp_dash) {
	$target = "channel_partner_customer_manage.php";
} else if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 3) {
	$target = "customer_dashboard.php";
} else {
	$target = "main_dashboard.php";
}
if (!empty($query)) {
	$target .= '?' . http_build_query($query);
}
$db->rp_location($target);
exit;