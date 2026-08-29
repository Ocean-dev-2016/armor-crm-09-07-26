<?php
$page_id = 607;
$page_slug = 'quotation_pi_suggest';
include('connect.php');
require_once('../include/quotation_pi_suggest_products_helper.php');

header('Content-Type: application/json');

$customerId = isset($_REQUEST['customer_id']) ? (int) $_REQUEST['customer_id'] : 0;
$exclude = array();
if (isset($_REQUEST['exclude_ids']) && $_REQUEST['exclude_ids'] !== '') {
	$exclude = array_filter(array_map('intval', explode(',', $_REQUEST['exclude_ids'])));
}

if ($customerId <= 0) {
	echo json_encode(array('ack' => 0, 'html' => '<div class="alert alert-warning">Please select customer first.</div>'));
	exit;
}

$items = armor_quotation_pi_get_suggest_products($db, $customerId, $exclude);
$html = armor_quotation_pi_render_suggest_grid($items, true);
echo json_encode(array('ack' => 1, 'html' => $html, 'count' => count($items)));
