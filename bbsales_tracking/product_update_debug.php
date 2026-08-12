<?php
/**
 * Step-by-step UpdateProduct debug for live
 * URL: product_update_debug.php?key=armor_prod_check_2026&id=166
 */
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['key']) || $_GET['key'] !== 'armor_prod_check_2026') {
	die("Unauthorized\n");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 166;

function dbg($msg) {
	echo $msg . "\n";
	@flush();
}

dbg("=== UpdateProduct step debug id=$id ===");

$_REQUEST['skip_security'] = 1224;
$page_id = 559;
include __DIR__ . '/connect.php';
require_once __DIR__ . '/../include/product.class.php';
$obj = new Product();

$reply = $obj->GetEditDataProduct(array('id' => $id));
if (empty($reply['ack'])) {
	dbg('FAIL load: ' . $reply['ack_msg']);
	exit;
}
$r = $reply['result'];
dbg('1 load OK name=' . $r['name']);
dbg('   image_path=' . (isset($r['image_path']) ? $r['image_path'] : '(empty)'));
if (!empty($r['image_path'])) {
	$imgPath = PRODUCT_A . $r['image_path'];
	dbg('   image file_exists=' . (file_exists($imgPath) ? 'yes' : 'no') . ' path=' . $imgPath);
}

$update = array(
	'id' => $id,
	'image_path' => '',
	'old_image_path' => isset($r['image_path']) ? $r['image_path'] : '',
	'product_type' => $r['product_type'],
	'name' => html_entity_decode($r['name'], ENT_QUOTES, 'UTF-8'),
	'weights' => array(-1 => array(
		'id' => -1, 'price' => '650', 'stock' => '0', 'current_stock' => '0',
		'inner' => '1', 'outer' => '1', 'inner_unit' => '100', 'outer_unit' => '100',
		'catno' => '2717', 'pro_weight' => '0', 'inner_size' => '', 'inner_cft' => '',
		'inner_cbm' => '', 'outer_size' => '', 'outer_cft' => '', 'outer_cbm' => '',
		'min_stock_qty' => '', 'max_stock_qty' => '',
	)),
	'tcid' => $r['tcid'], 'brand_id' => 0, 'cid' => $r['cid'],
	'unit_id' => $r['unit_id'], 'customer_unit_id' => $r['customer_unit_id'],
	'display_unit' => $r['display_unit'], 'is_free' => $r['is_free'],
	'hsn_code' => $r['hsn_code'], 'product_code' => $r['product_code'],
	'max_price' => $r['max_price'], 'sell_price' => $r['sell_price'],
	'pro_tax' => $r['pro_tax'], 'igst' => $r['igst'], 'cgst' => $r['cgst'],
	'sgst' => $r['sgst'], 'status' => 1, 'isDelete' => 0, 'slug' => $r['slug'],
	'descr' => $r['descr'], 'attr' => '',
);

dbg('2 calling UpdateProduct...');
$_SESSION[SITE_SESS . 'SESS_NAME'] = 'DiagTest';
$upd = $obj->UpdateProduct($update, array());
dbg('3 done ack=' . $upd['ack'] . ' msg=' . $upd['ack_msg']);
