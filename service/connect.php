<?php
header('Content-Type: application/json');
error_reporting(0);
session_start();
if (function_exists('session_write_close')) {
	@session_write_close();
}
date_default_timezone_set('Asia/Kolkata');

register_shutdown_function(function() {
	$error = error_get_last();
	if ($error && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
		if (!headers_sent()) {
			header('Content-Type: application/json');
		}
		$msg = isset($error['message']) ? $error['message'] : 'Fatal error occurred';
		$file = isset($error['file']) ? basename($error['file']) : '';
		$line = isset($error['line']) ? $error['line'] : 0;
		echo json_encode(array(
			"ack" => 0,
			"ack_msg" => "Internal Server Error",
			"developer_msg" => $msg . " (" . $file . ":" . $line . ")",
			"extra" => array("requested_params" => $_REQUEST)
		));
	}
});

include("../include/define.php");

include("../include/function.class.php");
require_once("../include/channel_partner_helper.php");

include("../include/notification.class.php");
$db = new Admin();
$conn = $db->connect();
include("../include/class.system.php");
// $db->logMysqlError("__API_REQUEST__", false);

if (isset($_REQUEST[API_PARAM]) && $_REQUEST[API_PARAM] != "") {
	$is_valid_api_key = $db->checkAPIKey($db->clean($_REQUEST[API_PARAM]));
	if (isset($_REQUEST[SERVICE_PARAM]) && $_REQUEST[SERVICE_PARAM] != "") {
		$is_valid_service = $db->checkAPI($db->clean($_REQUEST[SERVICE_PARAM]));
		if ($is_valid_service) {
			$is_valid_service = true;
			$service = $_REQUEST[SERVICE_PARAM];
		} else {
			$is_valid_service = false;
			$service = "";
		}
	} else {
		$is_valid_service = false;
		$service = "";
	}
} else {
	$is_valid_api_key = false;
}
