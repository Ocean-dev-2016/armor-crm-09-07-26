<?php
/**
 * Live product edit diagnostics
 * URL: product_edit_check.php?key=armor_prod_check_2026&id=166
 * Optional: &step=all  |  &step=update
 * DELETE from live after use.
 */
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['key']) || $_GET['key'] !== 'armor_prod_check_2026') {
	header('HTTP/1.1 403 Forbidden');
	die("Unauthorized. Use: product_edit_check.php?key=armor_prod_check_2026&id=166\n");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 166;
$step = isset($_GET['step']) ? $_GET['step'] : 'all';

echo "=== Armor CRM Product Edit Check ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
echo "PHP: " . PHP_VERSION . "\n\n";

$files = array(
	'product_crud.php' => 'if($cat_r && mysqli_num_rows($cat_r)',
	'../include/product.class.php' => 'existingWeightId',
	'get_size_from_product_type.php' => 'if($pro_r1 && ($pro_d',
	'../include/function.class.php' => 'if ($imgInfo === false',
);

echo "--- Latest fix on server? ---\n";
$allOk = true;
foreach ($files as $path => $needle) {
	$full = __DIR__ . '/' . $path;
	if (!file_exists($full)) {
		echo "[MISSING] $path\n";
		$allOk = false;
		continue;
	}
	$content = @file_get_contents($full);
	$ok = ($content !== false && strpos($content, $needle) !== false);
	echo ($ok ? '[OK] ' : '[OLD] ') . $path . "\n";
	if (!$ok) {
		$allOk = false;
	}
}

$_REQUEST['skip_security'] = 1224;
$page_id = 559;
include __DIR__ . '/connect.php';
require_once __DIR__ . '/../include/product.class.php';
$obj = new Product();

echo "\n--- DB column check ---\n";
$colCheck = @mysqli_query($db->myconn, "SHOW COLUMNS FROM product_weight_price LIKE 'minimum_selling_price'");
if ($colCheck && mysqli_num_rows($colCheck) > 0) {
	echo "OK: minimum_selling_price column exists\n";
} else {
	echo "MISSING: product_weight_price.minimum_selling_price — run alter on live DB\n";
	echo "SQL: ALTER TABLE product_weight_price ADD minimum_selling_price DOUBLE NOT NULL DEFAULT 0 AFTER outer_unit;\n";
	$allOk = false;
}

echo "\n--- GetEditDataProduct (id=$id) ---\n";
$reply = $obj->GetEditDataProduct(array('id' => $id));
if (empty($reply['ack'])) {
	echo "FAIL: " . (isset($reply['ack_msg']) ? $reply['ack_msg'] : 'unknown') . "\n";
	$allOk = false;
} else {
	echo "OK: " . $reply['result']['name'] . " | weights=" . implode(',', $reply['result']['weight_ids']) . "\n";
}

if ($step === 'all' || $step === 'size') {
	echo "\n--- get_size_from_product_type (type=2 edit) ---\n";
	$_REQUEST['type'] = 2;
	$_REQUEST['mode'] = 'edit';
	$_REQUEST['id'] = $id;
	ob_start();
	include __DIR__ . '/get_size_from_product_type.php';
	$sizeHtml = ob_get_clean();
	echo (strlen($sizeHtml) > 500 ? 'OK len=' . strlen($sizeHtml) : 'FAIL len=' . strlen($sizeHtml)) . "\n";
	if (strlen($sizeHtml) < 500) {
		echo substr($sizeHtml, 0, 500) . "\n";
		$allOk = false;
	}
}

if ($step === 'all' || $step === 'page') {
	echo "\n--- product_crud page render (edit GET) ---\n";
	$_REQUEST['mode'] = 'edit';
	$_REQUEST['id'] = $id;
	unset($_REQUEST['submit']);
	ob_start();
	include __DIR__ . '/product_crud.php';
	$pageHtml = ob_get_clean();
	echo (strlen($pageHtml) > 5000 ? 'OK len=' . strlen($pageHtml) : 'FAIL len=' . strlen($pageHtml)) . "\n";
	if (strlen($pageHtml) < 5000) {
		echo substr($pageHtml, 0, 1000) . "\n";
		$allOk = false;
	}
}

if ($step === 'all' || $step === 'update') {
	echo "\n--- UpdateProduct test ---\n";
	@flush();
	if (!empty($reply['ack'])) {
		$r = $reply['result'];
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
		$_SESSION[SITE_SESS . 'SESS_NAME'] = 'DiagTest';
		$upd = $obj->UpdateProduct($update, array());
		@flush();
		echo ($upd['ack'] == 1 ? 'OK: ' : 'FAIL: ') . $upd['ack_msg'] . "\n";
		if ($upd['ack'] != 1) {
			$allOk = false;
		}
	}
}

echo "\n--- Result ---\n";
echo $allOk ? "ALL TESTS PASSED on this server.\n" : "SOME TESTS FAILED — upload latest files again.\n";
