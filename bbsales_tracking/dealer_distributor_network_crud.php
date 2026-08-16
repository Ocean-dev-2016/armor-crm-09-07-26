<?php
$page_id=406;$page_slug='page_admin';
$ctable 	= "dealer_distributor_network";
$ctable1 	= "User";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
$name			= "";
$type			= "";
$user_id		= "";
$admin_type			= "";
$email			= "";
$username		= "";
$sales_executive_id		= "";
$customer_id			= "";
$password		= "";
$login_kind		= "";

if(isset($_REQUEST['submit'])){
	
	$name			        = $db->clean($_REQUEST['name']);	
	$type					= $db->clean($_REQUEST['type']);	
	$admin_type					= $db->clean($_REQUEST['admin_type']);	
		$user_id					= isset($_REQUEST['user_id'])?$db->clean($_REQUEST['user_id']):"";	
		$customer_id				= isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";
		$sales_executive_id		= isset($_REQUEST['sales_executive_id'])?$db->clean($_REQUEST['sales_executive_id']):"";
	$email					= $db->clean($_REQUEST['email']);	
	$username				= $db->clean($_REQUEST['username']);	
	$password				= $db->clean($_REQUEST['password']);		
	$isDelete				= 0;
	if($sales_executive_id!="")
	{

		$name   = $db->rp_getValue("sales_executive","name","id='".$sales_executive_id."'");
	}
	else if($customer_id!="")
	{
		$firm   = $db->rp_getValue("executive","company_name","id='".$customer_id."'");
		$person = $db->rp_getValue("executive","cname","id='".$customer_id."'");
		$name   = (trim($firm) != "") ? $firm : $person;
	}
	else if($user_id!="")
	{
		$firm   = $db->rp_getValue("executive","company_name","id='".$user_id."'");
		$person = $db->rp_getValue("executive","cname","id='".$user_id."'");
		$name   = (trim($firm) != "") ? $firm : $person;

	}
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$dup_where = "username = '".$username."' AND isDelete=0";
		$r = $db->rp_dupCheck($ctable,$dup_where);
		
		if($r){
			$db->rp_location($ctable."_crud.php?mode=add&msg=duplicate");
			die;
		}else{
			
			// $admin_type_id   = $db->rp_getValue("admin_type","id","slug='".$admin_type."'",0);
			$admin_type_id   = $admin_type;
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"name",						
						"type",						
						"admin_type",
						"user_id",
						"customer_id",
						"sales_executive_id",						
						"username",
						"phone",
						"email",						
						"password",						
						"isDelete",
						"adate"
					);
			$values = array(
						$name,									
						$type,									
						$admin_type_id,
						($type == 3 || $type == 4) ? 0 : $user_id,
						($type == 3 || $type == 4) ? $customer_id : 0,
						($type == 2) ? $sales_executive_id : 0,
						$username,
						$username,
						$email,									
						md5($password),									
						$isDelete,
						$adate
					);
					
			$tid = $db->rp_insert($ctable,$values,$rows,0);			
			$db->rp_location($ctable."_manage.php?msg=inserted");
		}
		
	}else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit"){
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$dup_where = "username = '".$username."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
		$r = $db->rp_dupCheck($ctable,$dup_where);
		if($r){
			$db->rp_location($ctable."_crud.php?mode=edit&id=".$_REQUEST['id']."&msg=duplicate");
			die;
		}else{
			$rows 	= array(
						"name"				=> $name,
						"type"				=> $type,
						"admin_type"				=> $admin_type,
						"username"				=> $username,
						"email"				=> $email,
						"sales_executive_id"				=> ($type == 2) ? $sales_executive_id : 0,
						"user_id"				=> ($type == 1) ? $user_id : 0,
						"customer_id"			=> ($type == 3 || $type == 4) ? $customer_id : 0,
						"phone"					=> $username,
						// "password"				=> md5($password),						
					
					);
			$where	= "id='".$_REQUEST['id']."'";
			$db->rp_update($ctable,$rows,$where);
			$db->rp_location($ctable."_manage.php?msg=updated");
		}
	}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
	if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
	$where = " id='".$_REQUEST['id']."' AND isDelete=0";
	$ctable_r = $db->rp_getData($ctable,"*",$where);
	$ctable_d = mysqli_fetch_array($ctable_r);
	$tid=$_REQUEST['id'];
	$name			= htmlentities($ctable_d['name']);	
	$type			= htmlentities($ctable_d['type']);	
	$admin_type			= htmlentities($ctable_d['admin_type']);	
	$username			= htmlentities($ctable_d['username']);	
	$email			= htmlentities($ctable_d['email']);	
	$password			= htmlentities($ctable_d['password']);	
	$user_id			= htmlentities($ctable_d['user_id']);	
	$customer_id		= htmlentities($ctable_d['customer_id']);
	$sales_executive_id			= htmlentities($ctable_d['sales_executive_id']);	

	$login_kind = $type;
	if (($type == 3 || $type == '3') && $customer_id != '') {
		$cpFlagEdit = $db->rp_getValue("executive", "channel_partner_flag", "id='".$customer_id."' AND isDelete=0", 0);
		if ((int) $cpFlagEdit === 1) {
			$login_kind = 4;
		}
	}

	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	if($rights['delete_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
	$rows 	= array(
				"isDelete"	=> "1"
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	
	$db->rp_location($ctable."_manage.php?msg=deleted");
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	$status = $_REQUEST['status'];
	$rows 	= array(
				"isActive"	=> $status
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location($ctable."_manage.php?msg=updated");
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
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
				
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
		<form role="form" action="" onSubmit="return check_form();" method="post">
			<div class="row">
				<div class="col-md-6 ">
					<?php if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="duplicate"){ ?>
					<div class="alert alert-danger alert-dismissable"> <i class="fa fa-ban"></i>
						<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
						<b>Error! This Name is Already Exist. Please Try Another Name.</b> </div>
					<?php } ?>
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
								<div class="row">
									<!-- <div class="col-md-6">
										<div class="form-group">
											<label>Name <code>*</code></label>
											<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>" autofocus>
										</div>
									</div> -->	
									<!-- onchange="GetSalesExecutive(this.value);" -->
									<div class="col-md-6">
										<div class="form-group">
											<label>Admin Type <code>*</code></label>
											<select type="text" class="form-control" name="admin_type" id="admin_type" value="<?php echo $name; ?>" >
											<option value="">Select Admin Type</option>
											<?php 
												$ctable_data=$db->rp_getData("admin_type","*","isDelete=0","",0);
												while($a=mysqli_fetch_assoc($ctable_data))
												{
													?>
													<option <?php echo ($admin_type==$a['id'])?"selected":""; ?> value="<?php echo $a['id']; ?>"><?php echo $a['name']?></option>
													<?php
												}
											?>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label> Type <code>*</code></label>
											<select type="text" class="form-control" name="type" id="type"   onchange="getTypeofUser(this.value)">
											<option value="">Select Type</option>
											<option value="3" <?= ($login_kind == 3 || ($login_kind == "" && $type == 3)) ? "selected" : ""; ?>>Customer</option>
											<option value="4" <?= ($login_kind == 4 || $type == 4) ? "selected" : ""; ?>>Channel Partner</option>
											<option value="2" <?= ($type == 2) ? "selected" : ""; ?>>Sales Officer</option>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6 se_user hidden">
										<div class="form-group">
											<label>Select Person<code>*</code></label>
											<select type="text" class="form-control" name="sales_executive_id" id="sales_executive_id" >
											<option value="">Select Person</option>
											</select>
										</div>
									</div>
									<div class="col-md-6 cust_user hidden">
										<div class="form-group">
											<label id="cust_user_label">Select Firm<code>*</code></label>
											<select type="text" class="form-control" name="customer_id" id="customer_id" >
											<option value="">Select Firm</option>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Email <code>*</code></label>
											<input type="text" class="form-control" name="email" id="email" value="<?php echo $email; ?>">
										</div>
									</div>											
								</div>
							
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Username <code>*</code></label>
											<input type="text" class="form-control" name="username" id="username" value="<?php echo $username; ?>">
										</div>
									</div>	
									<?php
									if($_REQUEST['mode']=="add")
									{
									?>
									<div class="col-md-6">
										<div class="form-group">
											<label>Passsword <code>*</code></label>
											<input type="password" class="form-control" name="password" id="password" value="<?php echo ""; ?>">
										</div>
									</div>										
									<?php
									}
									?>
								</div>
								<div class="row">
																
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
							</div>
						</div>
					</div>
				</div>			
			</div>
		</form>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript">
$("#checkAll").change(function () {
    $(".md-check").prop('checked', $(this).prop("checked"));
});
</script>
<script type="text/javascript">
	var mode="<?= $_REQUEST['mode']; ?>";
$(document).ready(function(){$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); } });
	
	if(mode=="edit")
	{
		var type="<?= ($login_kind != '' ? $login_kind : $type); ?>";
		getTypeofUser(type);
	}
 });
function check_form(){
	$(".form-body").children().removeClass("has-error");
	if($("#admin_type").val()=="" || $("#admin_type").val().split(" ").join("")==""){
		alert("Please Select Admin type.");
		$("#admin_type").focus().parent().addClass("has-error");
		return false;
	}
	if($("#type").val()=="" || $("#type").val().split(" ").join("")==""){
		alert("Please Select Type of User.");
		$("#type").focus().parent().addClass("has-error");
		return false;
	}
	var type=$("#type").val();
	if(type==3 || type==4)
	{
		if($("#customer_id").val()=="" || $("#customer_id").val().split(" ").join("")=="")
		{
			alert(type==4 ? "Please Select Channel Partner." : "Please Select Customer.");
			$("#customer_id").focus().parent().addClass("has-error");
			return false;
		}
	}
	else if(type==1)
	{
		if($("#customer_id").val()=="" || $("#customer_id").val().split(" ").join("")=="")
		{
			alert("Please Select Customer.");
			$("#customer_id").focus().parent().addClass("has-error");
			return false;
		}
	}
	else if(type==2)
	{
		if($("#sales_executive_id").val()=="" || $("#sales_executive_id").val().split(" ").join("")=="")
		{
			alert("Please Select Sales Officer.");
			$("#sales_executive_id").focus().parent().addClass("has-error");
			return false;
		}
	}
	if($("#email").val()=="" || $("#email").val().split(" ").join("")=="")
	{
		alert('Please enter valid email.');
			$("#email").focus().parent().addClass("has-error");
			return false;
	}
	else
	{
		if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($("#email").val())){  
			
		}else{
			alert('Please enter valid email.');
			$("#email").focus().parent().addClass("has-error");
			return false;
		}
	}
	if($("#username").val()=="" || $("#username").val().split(" ").join("")==""){
		alert("Please enter username.");
		$("#name").focus().parent().addClass("has-error");
		return false;
	}	
	if(mode=="add")
	{
		if($("#password").val()=="" || $("#password").val().split(" ").join("")==""){
			alert("Please enter password.");
			$("#password").focus().parent().addClass("has-error");
			return false;
		}
	}
	
	
}
</script>

<script type="text/javascript">
	function getTypeofUser(type_id)
	{

		if(type_id==2)
		{
			if(mode=="edit")
			{
				var se_id="<?= $sales_executive_id?>";
			}
			else
			{
				var se_id="";
			}
			GetSalesExecutive(se_id);
			$(".se_user").removeClass("hidden");
			$(".cust_user").addClass("hidden");
		}
		else if(type_id==3 || type_id==4 || type_id==1)
		{
			if(mode=="edit")
			{
				var customer_id="<?= $customer_id ?>";
			}
			else
			{
				var customer_id="";
			}
			var kind = (type_id==4) ? "channel_partner" : "customer";
			if(type_id==4)
			{
				$("#cust_user_label").html("Select Channel Partner<code>*</code>");
			}
			else
			{
				$("#cust_user_label").html("Select Customer<code>*</code>");
			}
			GetUser(customer_id, kind);
			$(".se_user").addClass("hidden");
			$(".cust_user").removeClass("hidden");
		}
		else
		{
			$(".se_user").addClass("hidden");
			$(".cust_user").addClass("hidden");
		}
	}
	function GetSalesExecutive(se_id)
	{
		$.ajax({
        	type: "POST",
        	url: "ajax_get_executive.php",
        	data:'se_id='+se_id,
        	beforeSend:function(){
            },
        	success: function(data){
	            $("#sales_executive_id").select2("destroy");
	            $("#sales_executive_id").html(data);
	            $("#sales_executive_id").select2();
       		}
   	 	});
	}
	function GetUser(customer_id, kind)
	{
		
		$.ajax({
        	type: "POST",
        	url: "ajax_get_user.php",
        	data:'user_id='+customer_id+'&kind='+(kind || 'customer'),
        	beforeSend:function(){
            },
        	success: function(data){
	            $("#customer_id").select2("destroy");
	            $("#customer_id").html(data);
	            $("#customer_id").select2();
       		}
   	 	});
	}
   	 	
</script>

</body>
</html>