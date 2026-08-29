<?php
$page_id = 673;
$page_slug = 'quotation_pi_suggest_product';
include('connect.php');
require_once('../include/quotation_pi_suggest_products_helper.php');

header('Content-Type: application/json');

if (!isset($_SESSION['rights']['update_flag']) || (int) $_SESSION['rights']['update_flag'] !== 1) {
	if (!isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) || (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] !== 0) {
		echo json_encode(array('ack' => 0, 'ack_msg' => 'Access denied.'));
		exit;
	}
}

$catnos = array();
if (isset($_REQUEST['catnos']) && $_REQUEST['catnos'] !== '') {
	$catnos = array_filter(array_map('trim', explode(',', $_REQUEST['catnos'])));
}

$result = armor_quotation_pi_save_suggest_products($db, $catnos);
echo json_encode($result);
