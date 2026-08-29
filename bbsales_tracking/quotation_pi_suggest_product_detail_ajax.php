<?php
$page_id = 607;
$page_slug = 'quotation_pi_suggest_detail';
include('connect.php');
require_once('../include/quotation_pi_suggest_products_helper.php');

header('Content-Type: application/json');

$customerId = isset($_REQUEST['customer_id']) ? (int) $_REQUEST['customer_id'] : 0;
$productId = isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : 0;
$weightId = isset($_REQUEST['weight_id']) ? (int) $_REQUEST['weight_id'] : 0;

if ($customerId <= 0 || $productId <= 0 || $weightId <= 0) {
	echo json_encode(array('ack' => 0, 'msg' => 'Invalid product or customer.'));
	exit;
}

$optionHtml = armor_quotation_pi_build_option_html($db, $customerId, $productId, $weightId);
if ($optionHtml === '') {
	echo json_encode(array('ack' => 0, 'msg' => 'Product not available for this customer.'));
	exit;
}

preg_match('/value="([0-9]+)"/', $optionHtml, $m);
$optionValue = isset($m[1]) ? $m[1] : '';
preg_match('/data-pro_id="([0-9]+)"/', $optionHtml, $m2);
$proId = isset($m2[1]) ? $m2[1] : $productId;
preg_match('/data-item_order_unit="([^"]*)"/', $optionHtml, $m3);
$itemOrderUnit = isset($m3[1]) ? $m3[1] : '';

echo json_encode(array(
	'ack' => 1,
	'option_html' => $optionHtml,
	'option_value' => $optionValue,
	'pro_id' => $proId,
	'item_order_unit' => $itemOrderUnit,
));
