<?php
header('Content-Type: application/json');
error_reporting(1);
session_start();
date_default_timezone_set('Asia/Kolkata');
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
