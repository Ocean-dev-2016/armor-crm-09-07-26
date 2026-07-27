<?php
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Kolkata');
include("../include/define.php");
include("../include/function.class.php");
	
$db = new Functions();
$conn = $db->connect();

$where = " forgot_pass_string='".$db->clean($_REQUEST['f'])."'";
$ctable_r 	= $db->rp_getData(CTABLE_ADMIN,"*",$where);
	
$ctable_t = mysqli_num_rows($ctable_r);
if($ctable_t>0){
	$ctable_d 	= mysqli_fetch_array($ctable_r);
	$forgot_pass_string = $ctable_d['forgot_pass_string'];
	$_SESSION['forgot_pass_string'] = $forgot_pass_string;
}else{
	$db->rp_location(ADMINSITEURL."?msg=4");
}

if(isset($_REQUEST['submit']) && $_REQUEST['f']==$_SESSION['forgot_pass_string']){

	$password	= md5(trim($_REQUEST['password']));
	$rows 		= array("password"=>$password,"forgot_pass_string"=>"000");
	
	$where		= "forgot_pass_string='".$_SESSION['forgot_pass_string']."'";
	$db->rp_update(CTABLE_ADMIN,$rows,$where,0);
	$db->rp_location(ADMINSITEURL."login_admin.php?msg=3");

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
<title>Forgot Password | <?php echo SITETITLE; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
<meta name="robots" content="noindex">
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="assets/admin/pages/css/login3.css" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="assets/global/css/components-md.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="assets/global/css/plugins-md.css" rel="stylesheet" type="text/css"/>
<link href="assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="assets/admin/layout/css/themes/darkblue.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<!--<link rel="shortcut icon" href="favicon.ico"/>-->
</head>
<body class="page-md login">
<div class="logo"> <a href="<?php echo SITEURL; ?>"> <img src="assets/admin/layout/img/logo-keshav.png" alt=""/> </a> </div>
<div class="menu-toggler sidebar-toggler"> </div>
<div class="content">
	<!-- BEGIN LOGIN FORM -->
	<form class="login-form" action="forgot-password.php" method="post" onSubmit="return check_form();">
		<h3 class="form-title">Password Recovery</h3>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">New Password</label>
			<div class="input-icon"> <i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" id="password"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Confirm New Password</label>
			<div class="input-icon"> <i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="cpassword" id="cpassword"/>
				<input type="hidden" name="f" id="f" value="<?php echo $_REQUEST['f']; ?>">
			</div>
		</div>
		<div class="form-actions">
			<label class="checkbox">
			<!--<input type="checkbox" name="remember" value="1"/>
			Remember me--> </label>
			<button type="submit" name="submit" class="btn green-haze pull-right"> Update <i class="m-icon-swapright m-icon-white"></i> </button>
		</div>
		<div class="forget-password">
			<h4>Sign In ?</h4>
			<p> click <a href="<?php echo SITEURL_EMAIL; ?>"> here </a>. </p>
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
<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
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
<script type="text/javascript">
function check_form(){
	if($("#password").val()=="" || $("#password").val().split(" ").join("")==""){
		alert("Please enter password.");
		$("#password").focus();
		return false;
	}
	if($("#cpassword").val()=="" || $("#cpassword").val().split(" ").join("")==""){
		alert("Please enter confirm password.");
		$("#cpassword").focus();
		return false;
	}
	if($("#cpassword").val()!=$("#cpassword").val()){
		alert("New Password and Confirm New Password should be the same.");
		$("#cpassword").focus();
		return false;
	}
}
</script>
</body>
</html>