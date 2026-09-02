<?php
/**
 * Bootstrap for bbsales_tracking views.
 * When included from API/PDF export ($db already exists), skip re-loading classes.
 */
if (isset($db) && is_object($db)) {
	if (!isset($system) || !is_object($system)) {
		require_once dirname(__FILE__) . '/../include/class.system.php';
		$system = new System();
	}
	if (!defined('DO_NOT_CHANGE')) {
		define('DO_NOT_CHANGE', $db->rp_getValue('licence_key', 'licence_key_date', 'id=1'));
	}
	return;
}

error_reporting(0);
if (!isset($_SESSION)) {
	@session_start();
}
date_default_timezone_set('Asia/Kolkata');
require_once dirname(__FILE__) . '/../include/define.php';
require_once dirname(__FILE__) . '/../include/function.class.php';
require_once dirname(__FILE__) . '/../include/class.system.php';
$db = new Admin();
$conn = $db->connect();
$system = new System();
// include("../include/security.php");

// added by shivani
if (!defined('DO_NOT_CHANGE')) {
	define('DO_NOT_CHANGE', $db->rp_getValue('licence_key', 'licence_key_date', 'id=1'));
}
?>