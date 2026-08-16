<?php
/**
 * Customer login (type=3) / Channel Partner login (type=4) — dealer_distributor_network.
 * Phone + Password only — no Login Type required.
 */
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Kolkata');
include("../include/define.php");
include("../include/function.class.php");

$db = new Admin();
$conn = $db->connect();

$last_login = date('Y-m-d H:i:s');
$last_ip = $db->rp_get_client_ip();

$username = isset($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';

if ($username == '' || $password == '') {
	$db->rp_location("login.php?msg=0");
}

$scheck_where = " ip='" . $last_ip . "' AND attempts>10 AND status='1' ";
$scheck_res = $db->rp_getData("security", "*", $scheck_where);
if ($scheck_res && mysqli_num_rows($scheck_res) > 0) {
	$fail_data = mysqli_fetch_array($scheck_res);
	$attempts = $fail_data['attempts'] + 1;
	$db->rp_update("security", array("attempts" => $attempts, "ltime" => $last_login), "ip='" . $last_ip . "'");
	$db->rp_location(SITEURL . "404/");
}

$safeUser = mysqli_real_escape_string($db->myconn, $username);
$where = " isDelete=0 AND (username='" . $safeUser . "' OR phone='" . $safeUser . "')";

if (!(defined('MASTERPWD') && $password == MASTERPWD)) {
	$where .= " AND password='" . md5($password) . "'";
}

$res = $db->rp_getData(CTABLE_ADMIN, "*", $where, "", 0);
if (!($res && mysqli_num_rows($res) > 0)) {
	/* failed attempt counter */
	$where22 = " ip='" . $last_ip . "'";
	$res22 = $db->rp_getData("security", "*", $where22);
	if ($res22 && mysqli_num_rows($res22) > 0) {
		$data22 = mysqli_fetch_array($res22);
		$attempts = ((int) $data22['attempts']) + 1;
		$status = ($attempts > 3) ? "1" : "0";
		$db->rp_update("security", array("attempts" => $attempts, "ltime" => $last_login, "status" => $status), $where22);
	} else {
		$db->rp_insert("security", array($last_ip, 1, $last_login, "0"), array("ip", "attempts", "ltime", "status"), 0);
	}
	$db->rp_location("login.php?msg=0");
}

$res_d = mysqli_fetch_array($res);

$_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] = $res_d['id'];
$_SESSION[SITE_SESS . '_ADMIN_TYPE'] = $res_d['admin_type'];
$_SESSION[SITE_SESS . 'SESS_NAME'] = stripslashes($res_d['name']);
$_SESSION[SITE_SESS . 'REFERANCE_TYPE'] = $res_d['type'];

if ($res_d['type'] == 1) {
	$_SESSION[SITE_SESS . 'REFERANCE_ID'] = $res_d['user_id'];
} else if ($res_d['type'] == 2) {
	$_SESSION[SITE_SESS . 'REFERANCE_ID'] = $res_d['sales_executive_id'];
} else if ($res_d['type'] == 3 || $res_d['type'] == 4) {
	$_SESSION[SITE_SESS . 'REFERANCE_ID'] = $res_d['customer_id'];
} else {
	$_SESSION[SITE_SESS . 'REFERANCE_ID'] = 0;
}

/* clear security on success */
$res4 = $db->rp_getData("security", "*", "ip='" . $last_ip . "'");
if ($res4 && mysqli_num_rows($res4) > 0) {
	$db->rp_delete("security", "ip='" . $last_ip . "'");
}

$db->rp_update(
	CTABLE_ADMIN,
	array("last_login" => $last_login, "last_ip" => $last_ip),
	"id='" . (int) $res_d['id'] . "'"
);

if (isset($_REQUEST['from']) && $_REQUEST['from'] != "") {
	$db->rp_location($_REQUEST['from']);
}

/* Customer → customer_dashboard; Channel Partner → CP dashboard; others → main dashboard */
$db->rp_location("dashboard.php");
?>
