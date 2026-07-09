<?php
$page_id=5909;$page_slug='manage_notification';
include("connect.php");

$ctable 	= "notification";
$ctable1 	= "Notification";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;

$name			= "";
$notification_title = "";
$notification_type = "";
$notification_extra = "";
$notification_description = "";
$display_order	= 0;
$image_path="";
$image_path_inside="";
$type_sluf="";
$sales_executive = array();
$customer = array();
if(isset($_REQUEST['submit'])){
	
	if(in_array("all",$_REQUEST['sales_executive']))
	{
		$sales    = $db->rp_getData('sales_executive',"*","","",0);
		$sales_id =array();
		while($row = mysqli_fetch_assoc($sales))
			{
				$sales_id[] = $row['id'];	
			}
			$sales_executive = implode(",",$sales_id);
	}
	else
	{	
		//$sales_id= implode(",",$_REQUEST['sales_executive']);
		$sales_id= $_REQUEST['sales_executive'];
	}
	if(in_array("all",$_REQUEST['customer']))
	{
		$customers    = $db->rp_getData('customer',"*","","",0);
		$customer_id =array();
		while($row = mysqli_fetch_assoc($customers))
			{
				$customer_id[] = $row['id'];	
			}
			$customer = implode(",",$customer_id);
	}
	else
	{	
		//$sales_id= implode(",",$_REQUEST['sales_executive']);
		$customer= $_REQUEST['customer'];
	}
	$notification_title		= addslashes(trim($_REQUEST['notification_title']));
	$notification_description		= addslashes(trim($_REQUEST['notification_description']));
	$notification_type		= addslashes(trim($_REQUEST['notification_type']));
	$notification_extra		= addslashes(trim($_REQUEST['notification_extra']));
	$respective_date=date('Y-m-d H:i:s');
	if($notification_type==1)
	{
		$type_slug='Expense Message';
	}
	else if($notification_type==2)
	{
		$type_slug='Admin Message';
	}
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		$dup_where = "notification_msg = '".$notification_msg."' AND isDelete=0";
		$r = $db->rp_dupCheck($ctable,$dup_where);
		if($r){
			$db->rp_location("notification_crud.php?mode=add&msg=duplicate");
		}else{
		//print_r($sales_id);exit;
			$refreshTokens=array();
			//echo sizeof($sales_id);exit;
			for($i=0;$i<sizeof($sales_id);$i++)
			{
					$sales_executive = $sales_id[$i];
					$rows 	= array(
						"type_slug",
						"notification_title",
						"notification_description",
						"notification_type",
						"notification_extra",					
						"user_id",
						"respective_date",									
					);
					$values = array(
						$type_slug,
						$notification_title,
						$notification_description,
						$notification_type,
						$notification_extra,
						$sales_executive,	
						$respective_date
					);
					$db->rp_insert($ctable,$values,$rows,0);
					$refreshToken=$db->rp_getValue("sales_executive","refreshToken","refreshToken!='' AND id='".$sales_executive."' AND isDelete=0");
					$refreshTokens[]=$refreshToken;
			}			
			$msg = array(
					"type"		 => $notification_type,
					"type_slug"	 => $notification_slug,
					"title"		 => $notification_title,
					"description"=> $notification_description,					
					"extra"		 =>	$notification_extra,
					"respective_date"		 =>	$respective_date,
					);
			$result=$db->send_notification($msg,$refreshTokens);

			/////////// For Customers
			for($i=0;$i<sizeof($customer);$i++)
			{
					$customers = $customer[$i];


					$rows 	= array(
						"type_slug",
						"notification_title",
						"notification_description",
						"notification_type",
						"notification_extra",					
						"user_id",
						"respective_date",									
					);
					$values = array(
						$type_slug,
						$notification_title,
						$notification_description,
						$notification_type,
						$notification_extra,
						$customers,	
						$respective_date
					);
					$db->rp_insert($ctable,$values,$rows,0);
					$refreshToken=$db->rp_getValue("customer","gcmid","id='".$customers."' AND isDelete=0");
					$refreshTokens_customer[]=$refreshToken;

					/*$refreshTokens=$db->rp_getData("refresh_token","refresh_token","user_id='".$customers."'","",0);
					
					if($refreshTokens){
						while ($refreshToken=mysqli_fetch_assoc($refreshTokens)) {
							$refreshToken[]=$refreshToken['refresh_token'];
						}
						$msg = array(
								"type"		 => $notification_type,
								"type_slug"	 => $notification_slug,
								"title"		 => $notification_title,
								"description"=> $notification_description,					
								"extra"		 =>	$notification_extra,
								"respective_date"		 =>	$respective_date,
								);
						$result=$db->send_notification($msg,$refreshToken);
					}*/
					
			}	
			$msg = array(
							"type"		 => $notification_type,
							"type_slug"	 => $notification_slug,
							"title"		 => $notification_title,
							"description"=> $notification_description,					
							"extra"		 =>	$notification_extra,
							"respective_date"		 =>	$respective_date,
							);
					$result=$db->send_notification($msg,$refreshTokens_customer,2);
						
			if($result)
			{
				$db->rp_location("notification_manage.php?msg=1");
			}
			else
			{
				$db->rp_location("notification_manage.php?msg=2");
			}
		}
		
	}else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit"){
		
		$sales_id= implode(",",$_REQUEST['sales_executive']);
		$dup_where = "notification_msg = '".$notification_msg."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
		$r = $db->rp_dupCheck($ctable,$dup_where);
		if($r){
			$db->rp_location("notification_crud.php?mode=edit&id=".$_REQUEST['id']."&msg=duplicate");
			die;
		}else{
			if($notification_type==1)
			{
				$type_slug='Expense Message';
			}
			else if($notification_type==2)
			{
				$type_slug='Admin Message';
			}
			$respective_date=date('Y-m-d H:i:s');		
			$rows 	= array(
					"notification_title"		    => $notification_title,
					"notification_description"		=> $notification_description,
					"notification_extra"			=> $notification_extra,
					"notification_type"				=> $notification_type,
					"type_slug"				=> $type_slug,
					"user_id" 						=> $sales_id,	
					"respective_date" 				=> $respective_date,	
				);
			$where	= "id=".$_REQUEST['id'];
			$update=$db->rp_update($ctable,$rows,$where,0);
			if($update)
			{
				$db->rp_location("notification_manage.php?msg=3");
			}
			else{
				$db->rp_location("notification_manage.php?msg=4");
			}
		}
	}
	
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
	$where = " id='".$_REQUEST['id']."' AND isDelete=0";
	$ctable_r 	= $db->rp_getData($ctable,"*",$where);
	$ctable_d 	= mysqli_fetch_array($ctable_r);
	$notification_title		= stripslashes($ctable_d['notification_title']);
	$notification_extra		= stripslashes($ctable_d['notification_extra']);
	$notification_type	= stripslashes($ctable_d['notification_type']);	
	$notification_description	= stripslashes($ctable_d['notification_description']);
	$sales_executive = explode(",",$ctable_d['user_id']);
	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	$rows 	= array("isDelete" => "1");
	$where	= "id=".$_REQUEST['id'];
	$delete=$db->rp_update($ctable,$rows,$where);
	if($delete)
	{
		$db->rp_location("notification_manage.php?msg=5");
	}
	else
	{
		$db->rp_location("notification_manage.php?msg=6");
	}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="resend"){
	$where = " id='".$_REQUEST['id']."' AND isDelete=0";
	$ctable_r 	= $db->rp_getData($ctable,"*",$where);
	$ctable_d 	= mysqli_fetch_array($ctable_r);
	$notification_title		= stripslashes($ctable_d['notification_title']);
	$notification_type		= stripslashes($ctable_d['notification_type']);
	$notification_extra		= stripslashes($ctable_d['notification_extra']);	
	$notification_description	= stripslashes($ctable_d['notification_description']);
	$sales_executive	= stripslashes($ctable_d['sales_id']);
	$msg = array(
			"type"		 => $notification_type,
			"type_slug"		 => $type_slug,
			"title"		 => $notification_title,
			"description"=> $notification_description,			
			"extra"		 =>	$notification_extra,
			);
	$refreshTokens=array();
	$d=$db->rp_getValue("sales_executive","refreshToken","refreshToken!='' AND id='".$sales_executive."' AND isDelete=0");
	$refreshTokens[]=$d;				
	$respective_date=date('Y-m-d H:i:s');		
	$result=$db->send_notification($msg,$refreshTokens);
	$rows 	= array(
			
			"respective_date"			            => $respective_date,
			
		);
	
	$resend=$db->rp_update($ctable,$rows,$where);
	if($resend)	
	{
		$db->rp_location("notification_manage.php?msg=7");
	}
	else{
		$db->rp_location("notification_manage.php?msg=8");
	}
	
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $page_title; ?> | <?php echo ADMINTITLE; ?></title>
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
<?php include("include_css.php"); ?>
<link href="assets/css/demo.html5imageupload.css?v1.3" rel="stylesheet">
<link rel="stylesheet" href="tag/bootstrap-tagsinput.css">
<link rel="stylesheet" href="tag/app.css">
<link href="js/select2/select2.css" rel="stylesheet" />

<style>
.lable-check {
	padding-right:10px !important;
}

</style>


</head>
<body class="skin-black">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
				
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
		
		<form role="form" action="" onSubmit="return check_form();" method="post">
			<div class="row">
				<div class="col-md-6">
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
								<div class="form-group">
									<div class="row">
										<div class="col-md-12">
										
										<div class="row">
											<div class="col-md-12">
												<label for="notification_title">Notification Title <code>*</code></label>
												<input type="text" class="form-control" name="notification_title" id="notification_title" value="<?php echo $notification_title; ?>">
											   <p class="help-block"></p>
											</div>
										</div>
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
											<label for="notification_type">Notification Type <code>*</code></label>
										<select  name="notification_type" id="notification_type" class="form-control">
												<option>Select Notification Type</option>
												<option  <?php echo ($notification_type==1)?"selected":"";?> value="1">Expense Notification</option>
											    <option <?php echo ($notification_type==2)?"selected":"";?> value="2">Admin Message</option>
										   </select>
											<p class="help-block"></p>
											 </div>	
										</div>
									</div>
									
									<div class="row">
											<div class="col-md-12">
												
												<label for="notification_extra">Extra </label>
												<input type="text" class="form-control" name="notification_extra" id="notification_extra" value="<?php echo $notification_extra; ?>">
											   <p class="help-block"></p>
											</div>
									</div>
									
									
								</div>
									</div>
								</div>
								
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="row">
						<div class="portlet box blue">
							<div class="portlet-body form">
								<div class="form-body">
									<div class="form-group">
										<div class="row">
											<div class="col-md-12">
													<label for="sales_executive">Sales Officer </label>
													<select name="sales_executive[]" id="sales_executive" class="form-control" multiple size="10" placeholder="Select Sales Officer">
												<option id="select_all" name="select_all" value="all">Select All</option>
												<?php
												$cid_r = $db->rp_getData("sales_executive","*","isDelete=0");
													if(mysqli_num_rows($cid_r)>0){
														while($cid_d = mysqli_fetch_array($cid_r)){
														?>
													<option value="<?php echo $cid_d['id']; ?>" <?php if(in_array($cid_d['id'],$sales_executive)){?> selected <?php } ?>><?php echo $cid_d['name']; ?></option>
												<?php
													}
												}
											?>
												</select>
												<p class="help-block" autofocus></p>
											</div>
											<div class="col-md-12">
													<label for="customer">Customer </label>
													<select name="customer[]" id="customer" class="form-control" multiple size="10" placeholder="Select Customer">
												<option id="select_all" name="select_all" value="all">Select All</option>
												<?php
												$cid_r = $db->rp_getData("customer","*","isDelete=0");
													if(mysqli_num_rows($cid_r)>0){
														while($cid_d = mysqli_fetch_array($cid_r)){
														?>
													<option value="<?php echo $cid_d['id']; ?>" <?php if(in_array($cid_d['id'],$customer)){?> selected <?php } ?>><?php echo $cid_d['name']; ?></option>
												<?php
													}
												}
											?>
												</select>
												<p class="help-block" autofocus></p>
											</div>
											<div class="col-md-12">
												<label for="notification_description">Description<code>*</code></label>
												<textarea id="notification_description" name="notification_description" rows="10" cols="80">
												<?php echo $notification_description; ?>
												</textarea>
											<p class="help-block"></p>
											</div>
											
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</form>
		</div>
	</div>
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> </h1>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php');?>
<?php include("include_js.php"); ?>
<script src="assets/js/pro_html5imageupload.js?v1.3.4"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script src="js/plugins/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="js/select2/select2.min.js"></script>
<script type="text/javascript">

  //$('#sales_executive').select2();

	$('.dropzone').html5imageupload({
		onAfterProcessImage: function() {
			var imgName = $('#filename').val($(this.element).data('imageFileName'));
		},
		onAfterCancel: function() {
			$('#filename').val('');
		}
	});
	$(document).ready(function(){
		CKEDITOR.replace('notification_description');
	});
	$('#select_all').click(function() {
    $('#sales_executive option').prop('selected', true);
});
</script>
<script type="text/javascript">
function check_form(){  
	var isValid=true;
	$(".form-body").children().removeClass("has-error");
	if($("#notification_title").val()==""){
		aj.error('notification_title','Please enter title!!','add_error');
		isValid=false;
	}
	if($("#notification_type").val()==null || $("#notification_type").val()==""){
		aj.error('notification_type','Please select notification type!!','add_error');
		isValid=false;
	}
	if($($("#notification_description").val()==null || "#notification_description").val()==""){
		aj.error('notification_description','Please Enter notification Description!!','add_error');
		isValid=false;
	}
	if(isValid)
	{
		return true;
	}			
	else
	{
		return false;
	}
}


</script>



</body>
</html>
