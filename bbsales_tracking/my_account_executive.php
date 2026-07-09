<?php
$page_id=401;
include("connect.php");

$main_page 	= "my_account";
$page 		= "my_account";
$page_title	= "My Account";

$name		= "";
$phone		= "";
$email			= "";

if(isset($_REQUEST['submit'])){

	$name		= addslashes(trim($_REQUEST['name']));
	$phone	= addslashes(trim($_REQUEST['phone']));
	$email		= trim($_REQUEST['email']);
	$email_password	= isset($_REQUEST['email_password']) ? $db->clean($_REQUEST['email_password']) : "";
	
	if(isset($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=""){
		$uname = $_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
		//$rows 	= array("cname"=>$name,"phone"=>$phone,"email"=>$email,"email_password"=>$email_password);
		$rows 	= array("name"=>$name,"phone"=>$phone,"email"=>$email,"email_password"=>$email_password);
	}
	$where	= "id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'";
	//$db->rp_update('executive',$rows,$where,0);
	$db->rp_update('dealer_distributor_network',$rows,$where,0);
	$_SESSION[SITE_SESS.'SESS_NAME']=$name;
	$_SESSION['SESS_NAME'] = $name;
	$db->rp_location("dashboard.php?msg=1");	
}
if(isset($_REQUEST['submit1'])){

	$where = " id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
	$admin_r = $db->rp_getData('executive',"*",$where);
	$admin_d = mysqli_fetch_array($admin_r);
		
	$old_password		= $admin_d['password'];
	
	$opassword		= md5(trim($_REQUEST['opassword']));
	$password		= md5(trim($_REQUEST['password']));
	if($admin_d['password']!=md5(trim($_REQUEST['opassword'])))
	{
		$db->rp_location("my_account_executive.php?msg=3");
	}
	if($old_password!=$opassword){
		$db->rp_location("my_account_executive.php?msg=2");
	}else{
		$rows 	= array("password"=>$password);
		
		$where	= "id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'";
		$db->rp_update('executive',$rows,$where,0);
		$db->rp_location("dashboard.php?msg=1");
	}
		
}

$where = " id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'";
//$admin_r = $db->rp_getData('executive',"*",$where);
$admin_r = $db->rp_getData('dealer_distributor_network',"*",$where,"",0);
$admin_d = mysqli_fetch_array($admin_r);

$id				= $admin_d['id'];
$name			= $admin_d['name'];
$phone		= $admin_d['phone'];
$email 			= $admin_d['email'];
$email_password = $admin_d['email_password'];
//$name=$_SESSION['SESS_NAME'];
					
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
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><?php echo $page_title; ?></h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
		<?php 
			if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="1"){
			?>			
			<div class="alert alert-success alert-dismissable"> <i class="fa fa-check"></i>
				<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
				<b>Success! </b>Account details has been updated.
			</div>
			<?php
			}else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="2"){
			?>
			<div class="alert alert-danger alert-dismissable"> <i class="fa fa-ban"></i>
				<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
				<b>Error! </b>There is an error in admin account updation process. Please try again.
			</div>
			<div class="row">
			<?php
			}
			else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="3"){
			?>
			<div class="alert alert-danger alert-dismissable"> <i class="fa fa-ban"></i>
				<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
				<b>Error! </b>Current Password Not Match.Please try again.
			</div>
			<div class="row">
			<?php
			}
			?>
			<div class="row">
				<div class="col-md-6 ">
					<div class="portlet box">
						<div class="portlet-title">
							<div class="caption font-grey-gallery">
								<i class="icon-user font-grey-gallery"></i>
								<span class="caption-subject bold uppercase"> Personal Information</span>
							</div>
							
						</div>
						<div class="portlet-body form">
							<form role="form" action="" onSubmit="return check_form();" method="post">
							<div class="form-body">
								<div class="form-group">
									<label for="name">Name</label>
									<input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="<?php echo $name; ?>">
								</div>
								<div class="form-group">
									<label for="phone">Phone Number</label>
									<input type="text" class="form-control" name="phone" id="phone" placeholder="Enter User Name" value="<?php echo $phone; ?>">
								</div>
								<div class="form-group">
									<label for="email">Email</label>
									<input type="text" class="form-control" name="email" id="email" placeholder="Enter Email" value="<?php echo $email; ?>">
								</div>
								<div class="form-group">
									<label for="email_password">Email Password</label>
									<input type="password" class="form-control" name="email_password" id="email_password" placeholder="Enter Password" value="<?php echo $email_password; ?>">
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">save Profile</button>
								<button onclick="window.location.href='dashboard.php'" class="btn btn-default" type="button">Cancel</button>
							</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-6 ">
					<div class="portlet box">
						<div class="portlet-title">
							<div class="caption font-grey-gallery">
								<i class="icon-settings font-grey-gallery"></i>
								<span class="caption-subject bold uppercase"> Change Password</span>
							</div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="" onSubmit="return check_form2();" method="post">
							<div class="form-body">
								<div class="form-group">
									<label for="opassword">Current Password</label>
									<input type="password" class="form-control" name="opassword" id="opassword" placeholder="Enter Current Password" value="" autocomplete="off">
								</div>
								<div class="form-group">
									<label for="password">New Password</label>
									<input type="password" class="form-control" name="password" id="password" placeholder="Enter New Password" value="" autocomplete="off">
								</div>
								<div class="form-group">
									<label for="cpassword">Confirm New Password</label>
									<input type="password" class="form-control" name="cpassword" id="cpassword" placeholder="Enter New Password" value="<?php //echo $password; ?>" autocomplete="off">
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" name="submit1" class="btn green">Change Password</button>
								<button onclick="window.location.href='dashboard.php'" class="btn btn-default" type="button">Cancel</button>
							</div>
							</form>
						</div>
					</div>
				</div>
				<!-- notification setting tab -->
				<div class="col-md-6">
					<div class="portlet box">
						<div class="portlet-title">
							<div class="caption font-grey-gallery">
								<i class="icon-user font-grey-gallery"></i>
								<span class="caption-subject bold uppercase">Notification Setting</span>
							</div>
						</div>
						<div class="portlet-body form">
							<form action="#">
	                            <table class="table table-light table-hover">
	                                <tr>
	                                    <td>
	                                        <div class="mt-checkbox-list" style="margin-left:11px;">
	                                            <button type="button" class="btn btn-primary" onclick="GenerateFcmToken()">Notification Web</button>

	                                            <button type="button" class="btn btn-primary" onclick="GenerateFcmTokenMobile()">Notification Web Mobile</button>
	                                        </div>
	                                    </td>
	                                </tr>
	                            </table>                                            
                            </form>
						</div>
					</div>
				</div>
				<!-- notification setting tab -->
			</div>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript">
function check_form(){
	$(".form-body").children().removeClass("has-error");
	if($("#name").val()=="" || $("#name").val().split(" ").join("")==""){
		alert("Please enter admin name.");
		$("#name").focus().parent().addClass("has-error");
		return false;
	}
	if($("#username").val()=="" || $("#username").val().split(" ").join("")==""){
		alert("Please enter admin username.");
		$("#username").focus();
		return false;
	}
	if($("#email").val()=="" || $("#email").val().split(" ").join("")==""){
		alert("Please enter admin email.");
		$("#email").focus().parent().addClass("has-error");
		return false;
	}else{
		if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($("#email").val())){    
		}else{
			alert("Please enter valid admin email.");
			$("#email").focus().parent().addClass("has-error");
			return false;
		}
	}
}
function check_form2(){
	if($("#opassword").val()=="" || $("#opassword").val().split(" ").join("")==""){
		alert("Please enter current password.");
		$("#opassword").focus().parent().addClass("has-error");
		return false;
	}
	if($("#password").val()=="" || $("#password").val().split(" ").join("")==""){
		alert("Please enter new password.");
		$("#password").focus().parent().addClass("has-error");
		return false;
	}
	if($("#cpassword").val()=="" || $("#cpassword").val().split(" ").join("")==""){
		alert("Please enter confirm password.");
		$("#password").focus().parent().addClass("has-error");
		return false;
	}
	if($("#password").val()!=$("#cpassword").val()){
		alert("New password and Confirm password not matched.");
		$("#password").focus().parent().addClass("has-error");
		return false;
	}
}
</script>

<script type="text/javascript">
function GenerateFcmToken()
{
    var config = {
	    apiKey: "AIzaSyCrGaViP8w_D8hzkxSoFuO_fzs-fEH7Dfg",
	    authDomain: "cmk-crm.firebaseapp.com",
	    projectId: "cmk-crm",
	    storageBucket: "cmk-crm.appspot.com",
	    messagingSenderId: "345899882377",
	    appId: "1:345899882377:web:5efbbdfd36a1f23671f358",
	    measurementId: "G-2TS49WRQ29",
	    // databaseURL: "https://craftbox-5d2bb.firebaseio.com",
    };
    if (!firebase.apps.length) 
    {
			firebase.initializeApp({});
	}
	else 
	{
			firebase.app(); // if already initialized, use that one
	}
    // Retrieve Firebase Messaging object.
  	// const messaging = firebase.messaging();
    const messaging = firebase.messaging();
    messaging.requestPermission().then(function() {
        getRegToken();
    }).catch(function(err) {
        console.log('Unable to get permission to notify.', err);
    })
}

function getRegToken(argument) 
{     
	const messaging = firebase.messaging();
    messaging
   	.requestPermission()
   	.then(function () 
   	{
     	//MsgElem.innerHTML = "Notification permission granted." 
    	console.log("Notification permission granted.");
    	// get the token in the form of promise
    	return messaging.getToken()
   	})
   	.then(function(token) {
    	console.log( token);
    	if(token){
    		saveToken(token);
    	}
    	// print the token on the HTML page
    	// TokenElem.innerHTML = "Device token is : <br>" + token
   	})
   	.catch(function (err) {
   		//ErrElem.innerHTML = ErrElem.innerHTML + "; " + err
   		console.log("Unable to get permission to notify.", err);
 	});
}

function saveToken(currentToken) 
{
	var UpdateTo = "refresh_token_web";
    $.ajax({
        url: 'ajax_update_token.php',
        method: 'post',
        data: {token : currentToken,update_to:UpdateTo},
    }).done(function(result){
        console.log(result);
        result = $.parseJSON(result);
    if (result.ack == 0) {
			toastr.error(result.ack_msg);
		} else {
			toastr.success(result.ack_msg);
			// window.location.href = "orders_crud.php?mode=edit&id=" + result.order_id;
		}
    })
}
</script>

<script type="text/javascript">
function GenerateFcmTokenMobile()
{
    var config = {
	    apiKey: "AIzaSyCrGaViP8w_D8hzkxSoFuO_fzs-fEH7Dfg",
	    authDomain: "cmk-crm.firebaseapp.com",
	    projectId: "cmk-crm",
	    storageBucket: "cmk-crm.appspot.com",
	    messagingSenderId: "345899882377",
	    appId: "1:345899882377:web:5efbbdfd36a1f23671f358",
	    measurementId: "G-2TS49WRQ29",
	    // databaseURL: "https://craftbox-5d2bb.firebaseio.com",
    };
    if (!firebase.apps.length) 
    {
			firebase.initializeApp({});
	}
	else 
	{
			firebase.app(); // if already initialized, use that one
	}
    // Retrieve Firebase Messaging object.
  	// const messaging = firebase.messaging();
    const messaging = firebase.messaging();
    messaging.requestPermission().then(function() {
        getRegTokenMobile();
    }).catch(function(err) {
        console.log('Unable to get permission to notify.', err);
    })
}

function getRegTokenMobile(argument) 
{     
	const messaging = firebase.messaging();
    messaging
   	.requestPermission()
   	.then(function () 
   	{
     	//MsgElem.innerHTML = "Notification permission granted." 
    	console.log("Notification permission granted.");
    	// get the token in the form of promise
    	return messaging.getToken()
   	})
   	.then(function(token) {
    	console.log( token);
    	if(token){
    		saveTokenMobile(token);
    	}
    	// print the token on the HTML page
    	// TokenElem.innerHTML = "Device token is : <br>" + token
   	})
   	.catch(function (err) {
   		//ErrElem.innerHTML = ErrElem.innerHTML + "; " + err
   		console.log("Unable to get permission to notify.", err);
 	});
}

function saveTokenMobile(currentToken) 
{
	var UpdateTo = "refresh_token_mobile_web";
    $.ajax({
        url: 'ajax_update_token.php',
        method: 'post',
        data: {token : currentToken,update_to:UpdateTo},
    }).done(function(result){
        console.log(result);
        result = $.parseJSON(result);
    if (result.ack == 0) {
			toastr.error(result.ack_msg);
		} else {
			toastr.success(result.ack_msg);
			// window.location.href = "orders_crud.php?mode=edit&id=" + result.order_id;
		}
    })
}
</script>

</body>
</html>