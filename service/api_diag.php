<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$steps = array();
$fail = null;

function diag_step($label, $callback)
{
	global $steps, $fail;
	if ($fail !== null) {
		return;
	}
	try {
		$result = $callback();
		$steps[] = array('step' => $label, 'ok' => 1, 'detail' => $result);
	} catch (Exception $e) {
		$fail = array('step' => $label, 'ok' => 0, 'error' => $e->getMessage());
	} catch (Error $e) {
		$fail = array('step' => $label, 'ok' => 0, 'error' => $e->getMessage());
	}
}

diag_step('php_version', function () {
	return PHP_VERSION;
});

diag_step('define.php', function () {
	include_once('../include/define.php');
	return 'loaded';
});

diag_step('function.class.php', function () {
	include_once('../include/function.class.php');
	return 'loaded';
});

diag_step('admin_connect', function () {
	$db = new Admin();
	$conn = $db->connect();
	return $conn ? 'connected' : 'connect_failed';
});

diag_step('check_api_key_1226', function () {
	global $db;
	return $db->checkAPIKey('1226') ? 'valid' : 'invalid';
});

diag_step('product.class.php', function () {
	require_once('../include/product.class.php');
	return 'loaded';
});

diag_step('class.executive.php', function () {
	include_once('../include/class.executive.php');
	return class_exists('Executive') ? 'Executive class ok' : 'class missing';
});

diag_step('class.sales_executive.php', function () {
	include_once('../include/class.sales_executive.php');
	return class_exists('SalesExecutive') ? 'SalesExecutive class ok' : 'class missing';
});

diag_step('followup.class.php', function () {
	require_once('../include/followup.class.php');
	$obj = new Followup();
	return 'Followup ok';
});

diag_step('push_notification.class.php', function () {
	require_once('../include/push_notification.class.php');
	$obj = new PushNotification();
	return 'PushNotification ok';
});

diag_step('class.channel_partner_customer.php', function () {
	require_once('../include/class.channel_partner_customer.php');
	$obj = new ChannelPartnerCustomer();
	return 'ChannelPartnerCustomer ok';
});

diag_step('mysql_real_escape_string', function () {
	if (!function_exists('mysql_real_escape_string')) {
		return 'mysql extension NOT available';
	}
	return 'mysql extension available';
});

echo json_encode(array(
	'ack' => $fail === null ? 1 : 0,
	'fail' => $fail,
	'steps' => $steps,
));
