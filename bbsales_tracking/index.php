<?php
// echo "SD";exit;
$page_id=402;
$page_slug="login";
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Kolkata');
include("../include/define.php");
include("../include/function.class.php");
	
$db = new Admin();
$conn = $db->connect();

$last_login = date('Y-m-d H:i:s');
$last_ip 	= $db->rp_get_client_ip();

$scheck_where = " ip='".$last_ip."' AND attempts>3 AND status='1' ";
$scheck_res = $db->rp_getData("security","*",$scheck_where);

if ($scheck_res && mysqli_num_rows($scheck_res) > 0) {
	//404
	$fail_data 	= mysqli_fetch_array($scheck_res);
	$attempts 	= $fail_data['attempts'];
	$attempts++;
	$rows 	= array(
			"attempts"=>$attempts,
			"ltime"=>$last_login
			);

	$where3	= "ip='".$last_ip."'";
	$db->rp_update("security",$rows,$where3);
	$db->rp_location(SITEURL."404/");
}
 if((isset($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS.'_ADMIN_SESS_ID']>0)){
	require_once("../include/master_activity_helper.php");
	if (armor_is_master_activity_user()) {
		$db->rp_location("master_activity_dashboard.php");
	} else {
		$db->rp_location("dashboard.php");
	}
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>Login | <?php echo SITETITLE; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="assets/admin/pages/css/login.css" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="assets/global/css/components-md.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="assets/global/css/plugins-md.css" rel="stylesheet" type="text/css"/>
<link href="assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-md login">
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGO -->
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content" style="background-color: #ffffff!important;">
	<div class="logo" style="display:flex;justify-content:center;align-items:center;padding:10px 0 5px;">
		<a href="<?php echo SITEURL."bbsales_tracking/index.php"; ?>">
			<img src="<?php echo CRM_LOGIN_LOGO_URL; ?>?v=<?php echo @filemtime(CRM_LOGIN_LOGO_PATH); ?>" alt="<?php echo SITETITLE; ?>" style="max-height:160px;max-width:100%;height:auto;display:block;" />
		</a>
	</div>
	<form class="login-form" action="password_admin.php" method="post" onSubmit="return check_form();">
		<h3 class="form-title">Login to your account</h3>
		<?php
		if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="0"){
		?>
			<div class="alert alert-danger">
				<button class="close" data-close="alert"></button>
				<span>Incorrect Username OR Password. </span>
			</div>
		<?php
		}else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="1"){
		?>
			<div class="alert alert-success">
				<button class="close" data-close="alert"></button>
				<span>Login detail has been sent successfully. Please check your mail box. </span>
			</div>
		<?php
		}else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="3"){
		?>
			<div class="alert alert-success">
				<button class="close" data-close="alert"></button>
				<span>Password updated successfully. </span>
			</div>    
		<?php
		}else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="4"){
		?>
			<div class="alert alert-success">
				<button class="close" data-close="alert"></button>
				<span>Something went wrong. Please try again. </span>
			</div>    
		<?php
		}
		else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="5"){
		?>
			<div class="alert alert-success">
				<button class="close" data-close="alert"></button>
				<span>Current Password is wrong </span>
			</div>    
		<?php
		}
		?>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9">Username</label>
			<div class="input-icon"> <i class="fa fa-envelope-o"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username" id="username" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Password</label>
			<div class="input-icon"> <i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" id="password"/>
			</div>
		</div>
		<div class="form-actions">
			<center><button type="submit" class="btn btn-success uppercase" type="submit" >Login</button></center>
			<!-- <a class="forget-password" id="forget-password" href="javascript:;">Forgot Password?</a><br/> -->
			<!-- <a class="forget-password" id="forget-password" href="index.php">Customer Login</a> -->
		</div>
	</form>
	<form class="forget-form" action="pass-recover.php" method="post" onSubmit="return check_form2();">
		<h3>Forget Password ?</h3>
		<?php
		if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="2"){
		?>
			<div class="alert alert-danger">
				<button class="close" data-close="alert"></button>
				<span>Invalid Email-Id. </span>
			</div>    
		<?php
		}
		?>
		<p class="text-center"> Enter your e-mail address below to reset password. </p>
		<div class="form-group">
			<div class="input-icon"> <i class="fa fa-envelope"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="admin_mail" id="admin_mail" />
			</div>
		</div>
		<div class="form-actions">
			<button type="button" id="back-btn" class="btn"> <i class="m-icon-swapleft"></i> Back </button>
			<button type="submit" class="btn green-haze pull-right"> Submit <i class="m-icon-swapright m-icon-white"></i> </button>
		</div>
	</form>
</div>
<div class="copyright">
	<?php echo date("Y"); ?> &copy; <?php echo SITENAME; ?> by <a href="<?= DESIGNBY_LINK ?>" target="_blank" title="Web, Mobile And Software Development Company" class="font-yellow"><?= DESIGNBY; ?></a>
</div>
<!--[if lt IE 9]>
<script src="assets/global/plugins/respond.min.js"></script>
<script src="assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="assets/admin/layout/scripts/demo.js" type="text/javascript"></script>
<script src="assets/admin/pages/scripts/login.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {     
	Metronic.init(); // init metronic core components
	Layout.init(); // init current layout
	Login.init();
	Demo.init();
});
</script>
<script>
<?php
if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="2"){
?>
	$(document).ready(function() {
		$("#forget-password").trigger("click");
	});
	$(document).ready(function() {
		$("#customer-login").trigger("click");
	});
<?php
}
?>
</script>
<script>
function check_form(){
	if($("#username").val()=="" || $("#username").val().split(" ")==""){
		alert("Please enter username.");
		$("#username").focus();
		return false;
	}
	if($("#password").val()=="" || $("#password").val().split(" ")==""){
		alert("Please enter password.");
		$("#password").focus();
		return false;
	}
}
function check_form2(){
	if($("#admin_mail").val()=="" || $("#admin_mail").val().split(" ")==""){
		alert("Please enter email.");
		$("#admin_mail").focus();
		return false;
	}else{
		if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($("#admin_mail").val())){  
			 
		}else{
			alert("Please enter valid email.");
			$("#admin_mail").focus();
			return false;
		}
	}
}
</script>
</body>
</html>