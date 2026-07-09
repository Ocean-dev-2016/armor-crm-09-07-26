<?php
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Kolkata');
include("../include/define.php");
include("../include/function.class.php");
include("../include/class.system.php");
$db = new Admin();
$conn = $db->connect();

define('DO_NOT_CHANGE', $db->rp_getValue("licence_key", "licence_key_date", "id=1"));
// echo DO_NOT_CHANGE;exit;
$system = new System();
include("../include/security.php");
// echo $_SESSION[SITE_SESS.'_ADMIN_TYPE'];exit;
