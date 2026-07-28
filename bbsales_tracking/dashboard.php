<?php
$page_id = 400;
$page_slug = 'dashboard';
include("connect.php");

$query = $_GET;
$target = ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 3) ? "customer_dashboard.php" : "main_dashboard.php";
if (!empty($query)) {
	$target .= '?' . http_build_query($query);
}
$db->rp_location($target);
exit;