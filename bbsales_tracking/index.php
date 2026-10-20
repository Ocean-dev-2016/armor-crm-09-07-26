<?php
$page_id = 402;
$page_slug = "login";
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Kolkata');
include("../include/define.php");
include("../include/function.class.php");

$db = new Admin();
$conn = $db->connect();

$last_login = date('Y-m-d H:i:s');
$last_ip = $db->rp_get_client_ip();

$scheck_where = " ip='" . $last_ip . "' AND attempts>3 AND status='1' ";
$scheck_res = $db->rp_getData("security", "*", $scheck_where);

if ($scheck_res && mysqli_num_rows($scheck_res) > 0) {
	$fail_data = mysqli_fetch_array($scheck_res);
	$attempts = $fail_data['attempts'];
	$attempts++;
	$rows = array(
		"attempts" => $attempts,
		"ltime" => $last_login
	);
	$where3 = "ip='" . $last_ip . "'";
	$db->rp_update("security", $rows, $where3);
	$db->rp_location(SITEURL . "404/");
}
if ((isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] > 0)) {
	require_once("../include/master_activity_helper.php");
	if (armor_is_master_activity_user()) {
		$db->rp_location("master_activity_dashboard.php");
	} else {
		$db->rp_location("dashboard.php");
	}
}

$logoUrl = CRM_LOGIN_LOGO_URL . '?v=' . @filemtime(CRM_LOGIN_LOGO_PATH);
$alertHtml = '';
if (isset($_REQUEST['msg'])) {
	if ($_REQUEST['msg'] == "0") {
		$alertHtml = '<div class="armor-alert armor-alert-danger">Incorrect Username OR Password.</div>';
	} else if ($_REQUEST['msg'] == "1") {
		$alertHtml = '<div class="armor-alert armor-alert-success">Login detail has been sent successfully. Please check your mail box.</div>';
	} else if ($_REQUEST['msg'] == "3") {
		$alertHtml = '<div class="armor-alert armor-alert-success">Password updated successfully.</div>';
	} else if ($_REQUEST['msg'] == "4") {
		$alertHtml = '<div class="armor-alert armor-alert-danger">Something went wrong. Please try again.</div>';
	} else if ($_REQUEST['msg'] == "5") {
		$alertHtml = '<div class="armor-alert armor-alert-danger">Current Password is wrong.</div>';
	} else if ($_REQUEST['msg'] == "2") {
		$alertHtml = '<div class="armor-alert armor-alert-danger">Invalid Email-Id.</div>';
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Login | <?php echo SITETITLE; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta name="description" content="Armor Fire CRM — Command Hub Login"/>
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<link href="assets/admin/pages/css/armor-login.css" rel="stylesheet" type="text/css"/>
<link href="assets/admin/layout/css/armor-theme.css" rel="stylesheet" type="text/css"/>
<link rel="shortcut icon" href="<?php echo htmlspecialchars($logoUrl); ?>"/>
<style>
body, .login-container, .form-input, .login-btn, h1, p, button, input {
	font-family: "Open Sans", sans-serif !important;
}
.armor-alert {
	margin: 0 0 14px;
	padding: 12px 14px;
	border-radius: 12px;
	font-size: 13px;
	font-weight: 600;
	text-align: left;
}
.armor-alert-danger {
	background: rgba(227, 28, 35, 0.1);
	color: #b91c1c;
	border: 1px solid rgba(227, 28, 35, 0.25);
}
.armor-alert-success {
	background: rgba(22, 163, 74, 0.1);
	color: #166534;
	border: 1px solid rgba(22, 163, 74, 0.25);
}
.logo-section img {
	max-width: 168px;
	width: 100%;
	height: auto;
	display: block;
	margin: 0 auto;
}
</style>
</head>
<body>
<?php include __DIR__ . '/assets/admin/pages/partials/armor_login_bg_scene.html'; ?>

<div class="login-container">
	<div class="corner-top-left"></div>
	<div class="corner-bottom-right"></div>
	<div class="command-hub-badge">Command Hub</div>

	<div class="logo-section">
		<span class="since-text">SINCE 2010</span>
		<img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Armor Fire Logo">
	</div>

	<div class="title-section">
		<h1>CRM Portal</h1>
		<p>Engineered for Safety | Precision CRM Node</p>
	</div>

	<?php echo $alertHtml; ?>

	<form class="login-form" action="password_admin.php" method="post" onsubmit="return armorLoginSubmit(event);">
		<div class="form-group">
			<i class="ri-shield-user-line"></i>
			<input type="text" name="username" id="username" class="form-input" placeholder="Credential ID" required autocomplete="off">
		</div>

		<div class="form-group">
			<i class="ri-lock-password-line"></i>
			<input type="password" name="password" id="password" class="form-input" placeholder="Encryption Key" required autocomplete="off">
		</div>

		<button type="submit" class="login-btn" id="loginBtn">
			Validate &amp; Initiate Session
			<i class="ri-flashlight-fill"></i>
		</button>
	</form>

	<div class="footer-section">
		<p class="developed-by">Designed &amp; Developed By</p>
		<p class="company-name">
			<a href="<?php echo htmlspecialchars(DESIGNBY_LINK); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars(strtoupper(DESIGNBY)); ?></a>
		</p>
	</div>
</div>

<div class="login-loader" id="loginLoader" role="status" aria-live="polite" aria-hidden="true">
	<div class="loader-emblem">
		<span class="loader-ring"></span>
		<span class="loader-ring inner"></span>
		<span class="loader-ring dash"></span>
		<div class="loader-core">
			<img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Armor Fire">
		</div>
		<span class="loader-ember"></span><span class="loader-ember e2"></span><span class="loader-ember e3"></span><span class="loader-ember e4"></span>
	</div>
	<div>
		<div class="loader-title">Initiating Secure Session</div>
		<div class="loader-step" id="loaderStep">Verifying credentials…</div>
	</div>
	<div class="loader-bar"></div>
	<div class="loader-brand">Armor Fire · CRM Portal</div>
</div>

<script>
function armorLoginSubmit(e) {
	var u = document.getElementById('username');
	var p = document.getElementById('password');
	if (!u.value || !u.value.trim()) {
		alert('Please enter username.');
		u.focus();
		e.preventDefault();
		return false;
	}
	if (!p.value || !p.value.trim()) {
		alert('Please enter password.');
		p.focus();
		e.preventDefault();
		return false;
	}

	var loader = document.getElementById('loginLoader');
	var stepEl = document.getElementById('loaderStep');
	var btn = document.getElementById('loginBtn');
	var steps = [
		'Verifying credentials…',
		'Checking role & permissions…',
		'Loading CRM modules…',
		'Preparing your dashboard…'
	];
	var i = 0;
	if (btn) btn.disabled = true;
	if (loader) {
		loader.classList.add('show');
		loader.setAttribute('aria-hidden', 'false');
	}
	if (stepEl) stepEl.textContent = steps[0];
	setInterval(function () {
		i = (i + 1) % steps.length;
		if (!stepEl) return;
		stepEl.style.opacity = 0;
		setTimeout(function () {
			stepEl.textContent = steps[i];
			stepEl.style.opacity = 1;
		}, 200);
	}, 900);
	return true;
}
</script>
</body>
</html>
