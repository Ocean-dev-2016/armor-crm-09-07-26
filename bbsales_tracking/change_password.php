<?php
$page_id=402;
$page_slug="login";
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Kolkata');
include("../include/define.php");
include("../include/function.class.php");
$phone=$_REQUEST['phone'];	
$db = new Admin();
$conn = $db->connect();
	if(isset($_REQUEST['submit']))
	{
		$new_password=md5($db->clean($_REQUEST['new_password']));
		$where="phone=".$phone." AND isDelete=0";
		$rows=array("password"=>$new_password);
		$update_password=$db->rp_update("executive",$rows,$where,0);
		if($update_password)
		{
			$db->rp_location("index.php?msg=3");
		}
		else
		{
			$db->rp_location("change_password.php?msg=9");
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
<div class="logo"> <a href="<?php echo SITEURL."bbkeshav/admin_login.php"; ?>"> <img src="assets/admin/layout/img/logo-keshav.png" style="height:120px;" alt=""/> </a> </div>

<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">

	<form class="recover-form" action="" method="post" onSubmit="return check_form2();">
		<h3>Choose a new password</h3>
		<?php
		if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="9"){
		?>
			<div class="alert alert-success">
				<button class="close" data-close="alert"></button>
				<span>Something went wrong. Please try again. </span>
			</div>    
		<?php
		}
		?>
	
		<div class="form-group">
			<div class="input-icon"><label><b>New password</b></label> 
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="" name="new_password" id="new_password" />
			</div>
		</div>
		<div class="form-actions">
			<a href="index.php" id="back-btn" class="btn"> <i class="m-icon-swapleft"></i> Skip </a>
			<button type="submit" name="submit" class="btn green-haze pull-right"> Continue <i class="m-icon-swapright m-icon-white"></i> </button>
		</div>
	</form>
</div>
<div class="copyright">
	<?php echo date("Y"); ?> &copy; <?php echo SITENAME; ?> by <a href="https://www.oceaninfotech.co.in/" target="_blank" title="Web, Mobile And Software Development Company" class="font-yellow">Ocean Infotech</a>
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
function check_form2(){
	if($("#new_password").val()=="" || $("#new_password").val().split(" ")==""){
		alert("Please enter New Password.");
		$("#new_password").focus();
		return false;
	}
}
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

</script>
</body>
</html>