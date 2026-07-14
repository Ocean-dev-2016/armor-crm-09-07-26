<?php
/**
 * Temporary live API health check for Get Dealer (#78).
 * Open: https://armor-crm.oceanhub.co.in/service/api_78_check.php?key=armor_cp_sync_2026&sales_id=76&class_id=12&type=7
 * DELETE this file after testing.
 */
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_REQUEST['key']) || $_REQUEST['key'] !== 'armor_cp_sync_2026') {
	header('HTTP/1.1 403 Forbidden');
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Unauthorized'));
	exit;
}

$out = array(
	'php' => PHP_VERSION,
	'json_encode' => function_exists('json_encode') ? 1 : 0,
	'mb_strtoupper' => function_exists('mb_strtoupper') ? 1 : 0,
	'mysqli_fetch_assoc' => function_exists('mysqli_fetch_assoc') ? 1 : 0,
	'mysql_fetch_assoc' => function_exists('mysql_fetch_assoc') ? 1 : 0,
);

try {
	include __DIR__ . '/connect.php';
	require_once __DIR__ . '/../include/class.executive.php';
	$executive = new Executive();
	$detail = array(
		'cid' => '',
		'type' => isset($_REQUEST['type']) ? $_REQUEST['type'] : '7',
		'class_id' => isset($_REQUEST['class_id']) ? $_REQUEST['class_id'] : '12',
		'area_id' => isset($_REQUEST['area_id']) ? $_REQUEST['area_id'] : '',
		'city_id' => isset($_REQUEST['city_id']) ? $_REQUEST['city_id'] : '',
		'sales_id' => isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : '76',
		'dealer_id' => '',
		'superstokist_id' => '',
		'customer_flag' => isset($_REQUEST['customer_flag']) ? $_REQUEST['customer_flag'] : '0',
		'type_of_company' => '',
		'is_class_area_filter' => '',
	);
	$salesType = $db->rp_getValue('sales_executive', 'type', "isDelete=0 AND id='" . $db->clean($detail['sales_id']) . "'", 0);
	$out['sales_id'] = $detail['sales_id'];
	$out['sales_type'] = $salesType;
	$reply = $executive->GetDealer($detail);
	$out['ack'] = isset($reply['ack']) ? $reply['ack'] : 0;
	$out['ack_msg'] = isset($reply['ack_msg']) ? $reply['ack_msg'] : '';
	$out['count'] = (isset($reply['result']) && is_array($reply['result'])) ? count($reply['result']) : 0;
	if ($out['count'] > 0) {
		$first = $reply['result'][0];
		$out['sample'] = array(
			'id' => isset($first['id']) ? $first['id'] : '',
			'company_name' => isset($first['company_name']) ? $first['company_name'] : '',
			'type_of_executive' => isset($first['type_of_executive']) ? $first['type_of_executive'] : '',
		);
	}
} catch (Exception $e) {
	$out['ack'] = 0;
	$out['error'] = $e->getMessage();
}

echo json_encode($out);
