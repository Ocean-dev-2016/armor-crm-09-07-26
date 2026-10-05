<?php
$page_id = 674;
$page_slug = 'approval_entry';
$ctable = 'approval_entry';
$ctable1 = 'Approval';
$main_page = 'approval_entry';
$page = 'unlock_approval_entry';
$page_title = 'Approval Access';
$page_hierarchy = array(
	array('link' => '', 'title' => 'Approval'),
	array('link' => 'approval_unlock.php', 'title' => $page_title),
);
include('connect.php');
require_once('../include/approval_entry_helper.php');

$return = 'approval_manage.php';
if (isset($_REQUEST['return']) && $_REQUEST['return'] != '') {
	$ret = $_REQUEST['return'];
	$retPath = parse_url($ret, PHP_URL_PATH);
	$retBase = $retPath ? basename($retPath) : basename($ret);
	if ($retBase !== '' && preg_match('/^approval_[a-z0-9_\-\.]+\.php$/i', $retBase)) {
		$return = $retBase;
		$qs = parse_url($ret, PHP_URL_QUERY);
		if ($qs) {
			$return .= '?' . $qs;
		}
	}
}

if (armor_approval_is_unlocked()) {
	$db->rp_location($return);
}

$error = '';
if (isset($_REQUEST['submit'])) {
	$pwd = isset($_REQUEST['module_password']) ? (string)$_REQUEST['module_password'] : '';
	if (armor_approval_verify_password($pwd, $db)) {
		armor_approval_set_unlocked(true);
		$db->rp_location($return);
	}
	$error = 'Incorrect password. Please try again.';
	/* Do not echo or log the submitted password */
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include('include_css.php'); ?>
<style type="text/css">
.approval-unlock-box {
	max-width: 420px;
	margin: 40px auto 20px;
}
.approval-unlock-box .portlet-body {
	padding: 25px 30px;
}
.approval-unlock-hint {
	color: #888;
	font-size: 12px;
	margin-top: 8px;
}
</style>
</head>
<body class="page-md">
<?php include('header.php'); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1>
					<a href="dashboard.php" class="primary"><i class="fa fa-arrow-circle-o-left" style="font-size:22px!important;"></i></a>
					&nbsp;<?php $db->pageBar($page_hierarchy); ?>
				</h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="approval-unlock-box">
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption"><i class="fa fa-lock"></i> Approval Module Password</div>
					</div>
					<div class="portlet-body">
						<?php if ($error != '') { ?>
							<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
						<?php } ?>
						<p>Enter the special password to open the Approval module.</p>
						<form method="post" action="" autocomplete="off">
							<input type="hidden" name="return" value="<?php echo htmlspecialchars($return); ?>">
							<div class="form-group">
								<label>Password</label>
								<input type="password" class="form-control" name="module_password" id="module_password" autocomplete="off" autofocus required>
							</div>
							<button type="submit" name="submit" value="1" class="btn blue btn-block">Unlock</button>
							<a href="dashboard.php" class="btn default btn-block" style="margin-top:8px;">Cancel</a>
						</form>
						<div class="approval-unlock-hint">Access is for this login session only.</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>
<?php include('include_js.php'); ?>
</body>
</html>
