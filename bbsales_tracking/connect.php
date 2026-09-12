<?php
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Kolkata');
require_once("../include/define.php");
require_once("../include/function.class.php");
require_once("../include/class.system.php");
require_once("../include/channel_partner_helper.php");
$db = new Admin();
$conn = $db->connect();

if (!defined('DO_NOT_CHANGE')) {
	define('DO_NOT_CHANGE', $db->rp_getValue("licence_key", "licence_key_date", "id=1"));
}
// echo DO_NOT_CHANGE;exit;
$system = new System();
require_once("../include/master_activity_helper.php");
include("../include/security.php");

if (armor_is_master_activity_user()) {
	$currentPage = basename($_SERVER['PHP_SELF']);
	if (!in_array($currentPage, armor_master_activity_allowed_pages(), true)) {
		$db->rp_location("master_activity_dashboard.php");
		exit;
	}
}
// echo $_SESSION[SITE_SESS.'_ADMIN_TYPE'];exit;
