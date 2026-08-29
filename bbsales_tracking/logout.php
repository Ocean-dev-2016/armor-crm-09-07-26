<?php
/**
 * Web CRM Logout — clears all login session keys + cookie, then redirect to login.
 */
$page_id = 403;
$page_slug = "logout";
include("connect.php");

// Clear web refresh token (safe if row does not exist)
if (isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] != '') {
	$adminId = (int) $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
	@$db->rp_update("dealer_distributor_network", array("refresh_token_web" => ""), "id='" . $adminId . "'", 0);
}

// Must match keys set in password_admin.php / password.php
$sessionKeys = array(
	SITE_SESS . '_ADMIN_SESS_ID',
	SITE_SESS . '_ADMIN_TYPE',
	SITE_SESS . '_ADMIN_SESS_TYPE', // legacy wrong key (if present)
	SITE_SESS . 'SESS_NAME',
	SITE_SESS . 'REFERANCE_TYPE',
	SITE_SESS . 'REFERANCE_ID',
	SITE_SESS . '_MASTER_ACTIVITY_VIEW',
	'rights',
	'SESS_NAME',
);

foreach ($sessionKeys as $key) {
	if (isset($_SESSION[$key])) {
		unset($_SESSION[$key]);
	}
}

$_SESSION = array();

if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(
		session_name(),
		'',
		time() - 42000,
		isset($params["path"]) ? $params["path"] : '/',
		isset($params["domain"]) ? $params["domain"] : '',
		!empty($params["secure"]),
		!empty($params["httponly"])
	);
}

@session_destroy();

header("Location: index.php");
exit;
