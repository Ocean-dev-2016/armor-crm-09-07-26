<?php
$page_id = 673;
$page_slug = 'quotation_pi_suggest_product';
include('connect.php');
require_once('../include/quotation_pi_suggest_products_helper.php');

header('Content-Type: application/json');

if (!isset($_SESSION['rights']['view_flag']) || (int) $_SESSION['rights']['view_flag'] !== 1) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Access denied.'));
	exit;
}

$extra = array();
if (isset($_REQUEST['extra']) && $_REQUEST['extra'] !== '') {
	$extra = array_filter(array_map('trim', explode(',', $_REQUEST['extra'])));
}

$rows = armor_quotation_pi_get_admin_suggest_rows($db);

if (!empty($extra)) {
	$existing = array();
	foreach ($rows as $row) {
		$existing[$row['catno']] = 1;
	}
	foreach ($extra as $catno) {
		if (isset($existing[$catno])) {
			continue;
		}
		$info = armor_quotation_pi_lookup_product_by_catno($db, $catno);
		$rows[] = array(
			'catno' => $catno,
			'name' => $info ? $info['name'] : '',
			'image' => $info ? $info['image'] : armor_quotation_pi_product_image_url(''),
			'product_id' => $info ? (int) $info['product_id'] : 0,
			'is_selected' => 1,
			'display_order' => 99999,
			'found' => $info ? 1 : 0,
		);
	}
}

echo json_encode(array('ack' => 1, 'rows' => $rows, 'count' => count($rows)));
