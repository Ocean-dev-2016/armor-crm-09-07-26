<?php
/**
 * Live deploy verify — product edit 500 fix
 * URL: product_edit_check.php?key=armor_prod_check_2026&id=166
 * DELETE this file from live after verification.
 */
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['key']) || $_GET['key'] !== 'armor_prod_check_2026') {
	header('HTTP/1.1 403 Forbidden');
	die("Unauthorized. Use: product_edit_check.php?key=armor_prod_check_2026&id=166\n");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 166;

echo "=== Armor CRM Product Edit Check ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
echo "PHP: " . PHP_VERSION . "\n\n";

$files = array(
	'product_crud.php' => 'attr_count = isset($_REQUEST[\'attr_count\'])',
	'../include/product.class.php' => 'if (!$ctable_r)',
	'get_size_from_product_type.php' => 'if (!isset($min_selling_price)',
	'../include/function.class.php' => 'if ($imgInfo === false',
);

echo "--- Fix deployed on this server? ---\n";
$allOk = true;
foreach ($files as $path => $needle) {
	$full = __DIR__ . '/' . $path;
	if (!file_exists($full)) {
		echo "[MISSING FILE] $path\n";
		$allOk = false;
		continue;
	}
	$content = @file_get_contents($full);
	$ok = ($content !== false && strpos($content, $needle) !== false);
	echo ($ok ? '[OK] ' : '[OLD CODE] ') . $path . "\n";
	if (!$ok) {
		$allOk = false;
	}
}

echo "\n--- GetEditDataProduct test (id=$id) ---\n";
try {
	$_REQUEST['skip_security'] = 1224;
	$page_id = 559;
	include __DIR__ . '/connect.php';
	require_once __DIR__ . '/../include/product.class.php';
	$obj = new Product();
	$reply = $obj->GetEditDataProduct(array('id' => $id));
	if (!empty($reply['ack'])) {
		echo "ack=1 OK\n";
		if (!empty($reply['result']['name'])) {
			echo "product: " . $reply['result']['name'] . "\n";
		}
		if (isset($reply['result']['weight_ids'])) {
			echo "weight_ids: " . implode(',', $reply['result']['weight_ids']) . "\n";
		}
	} else {
		echo "ack=0 " . (isset($reply['ack_msg']) ? $reply['ack_msg'] : 'unknown') . "\n";
	}
} catch (Exception $e) {
	echo "EXCEPTION: " . $e->getMessage() . "\n";
	$allOk = false;
}

echo "\n--- Result ---\n";
if ($allOk) {
	echo "READY — fix files are on this server. Try product edit again (Ctrl+Shift+R).\n";
} else {
	echo "NOT READY — live server still has OLD files.\n";
	echo "Deploy: git pull origin main  OR  upload these 4 files via FTP:\n";
	echo "  bbsales_tracking/product_crud.php\n";
	echo "  bbsales_tracking/get_size_from_product_type.php\n";
	echo "  include/product.class.php\n";
	echo "  include/function.class.php\n";
}
