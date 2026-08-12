<?php
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Kolkata');
include("../include/define.php");
require_once("../include/function.class.php");
include("../include/class.system.php");
include("../include/channel_partner_helper.php");
$db = new Admin();
$conn = $db->connect();

if (!defined('DO_NOT_CHANGE')) {
	define('DO_NOT_CHANGE', $db->rp_getValue("licence_key", "licence_key_date", "id=1"));
}
// echo DO_NOT_CHANGE;exit;
$system = new System();
include("../include/security.php");
// echo $_SESSION[SITE_SESS.'_ADMIN_TYPE'];exit;
