<?php
$page_id=408;$page_slug='page_vendor';
$page_slug="manage_vendor";
$ctable 	= "vendor";
$ctable1 	= "Vendor";
$main_page 	= $ctable;
include("connect.php");
require_once("../include/vendor.class.php");
$page 		= "add_".$ctable;
$id="";
$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
$vendor_name=$db->rp_getValue("vendor","cname","id='".$id."'");
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1." ".$vendor_name;

$objVendor= new Vendor();

$company_type	= "";
$cname			= "";
$email			= "";
$tin			= "";
$pan			= "";
$gst			= "";
$vat			= "";
$phone 			= "";
$address		= "";
$zip 			= "";
$country		= "";
$state			= "";
$city			= "";
$bank_name			= "";
$account_no			= "";
$ifsc_code			= "";
$isActive		= 0;
//$unique="S/".FINANCIAL_YEAR."/".(intval($db->rp_getValue($ctable,"max(`id`)","1=1"))+1);
if(isset($_REQUEST['submit'])){
		
	$detail['company_type']		= $db->clean($_REQUEST['company_type']);	
	$detail['cname']			= $db->clean($_REQUEST['cname']);
	$detail['email']			= $db->clean($_REQUEST['email']);
	$detail['tin']				= $db->clean($_REQUEST['tin']);
	$detail['pan']				= $db->clean($_REQUEST['pan']);
	$detail['gst']				= $db->clean($_REQUEST['gst']);
	$detail['vat']				= $db->clean($_REQUEST['vat']);
	$detail['phone']			= $db->clean($_REQUEST['phone']);
	$detail['address']			= $db->clean($_REQUEST['address']);
	$detail['zip']				= $db->clean($_REQUEST['zip']);
	$detail['country']			= $db->clean($_REQUEST['country']);
	 $detail['state']			= $db->clean($_REQUEST['state']);
	 $detail['city']				= $db->clean($_REQUEST['city']);
	 $detail['bank_name']		= $db->clean($_REQUEST['bank_name']);
	$detail['ifsc_code']		= $db->clean($_REQUEST['ifsc_code']);
	$detail['account_no']		= $db->clean($_REQUEST['account_no']);
	$detail['isActive']			= 1;
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=insert_access_denied');
		}
		$reply=$objVendor->InsertVendor($detail);
			if($reply['ack']==1){
				$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=inserted");
			}
			else{
				 $db->addErrorMessage($reply['ack_msg']);				 
			}
		
	}else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit"){		
		
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=update_access_denied');
		}
		$reply=$objVendor->UpdateVendor($detail);
		if($reply['ack']==1){
				$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location("vendor_manage.php?msg=updated");
		}
		else{
				$db->addErrorMessage($reply['ack_msg']);		
			}
	}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
	
	if($rights['update_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=update_access_denied');
	}
	$where = " id='".$_REQUEST['id']."' AND isDelete=0";
	$ctable_r = $db->rp_getData($ctable,"*",$where);
	$detail['id']=$_REQUEST['id'];	
	$reply=$objVendor->EditVendor($detail);
	if($reply['ack']==1){
		$result=$reply['result'];
		extract($result);	
	}
	else{
		$db->addErrorMessage($reply['ack_msg']);		
	}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){		
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id']=$_REQUEST['id'];
	$reply=$objVendor->DeleteVendor($detail);
	if($reply['ack']==1){
	$db->addSuccessMessage($reply['ack_msg']);
	$db->rp_location($ctable."_manage.php?msg=inserted");
	}
	else{
		$db->addErrorMessage($reply['ack_msg']);		
	}
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
				<h1><a href="<?php echo  $ctable;?>_manage.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
		<div class="row">
		<div class="col-sm-12">
			<?php $db->printErrorMessage(); ?>
			 <?php $db->printSuccessMessage(); ?>
		</div>
		</div>
		<form role="form" action="" onSubmit="return check_form();" method="post">
			<div class="row">
					<div class="col-md-12">						
						<div class="portlet box blue">
							<div class="portlet-body form">
								<div class="row">	
									<div class="col-sm-12">
										<div class="tabbable-custom nav-justified">
											<ul class="nav nav-tabs ">
												<li class="active">
													<a href="#tab_customer_info" data-toggle="tab" aria-expanded="false"> Company Information </a>
												</li>
												<li>
													<a href="#tab_branch_info" data-toggle="tab" aria-expanded="false"> Unit Information </a>
												</li>
												<li>
													<a href="#tab_contact_info" data-toggle="tab" aria-expanded="false"> Unit Contact Information </a>
												</li>														
											</ul>
											<div class="tab-content">
												<div class="tab-pane active" id="tab_customer_info">
													<div class="row">
														<div class="col-sm-6">
										<div class="col-md-12 col-sm-12 col-xs-12 bg-grey">								
											<h4><i class="fa fa-user"></i> &nbsp; Vendor Information</h4>														
										</div>
										<div class="row">
										<div class="col-md-12">
										<div class="form-body">
										<div class="form-group">
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
															<label>Vendor Name <code>*</code></label>
															<input type="text" class="form-control" name="cname" id="cname" value="<?php echo $cname; ?>" autofocus>
																<p class="help-block"></p>	
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
															<label for="phone">Phone </label>
															<input type="text" class="form-control" name="phone" id="phone" value="<?php echo $phone; ?>" maxlength="10">
																<p class="help-block"></p>		
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label>Email </label>
																<input type="text" class="form-control" name="email" id="email" value="<?php echo $email; ?>">
																<p class="help-block"></p>	
															</div>
														</div>														
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label>TIN Number </label>
																<input type="text" class="form-control" name="tin" id="tin" value="<?php echo $tin; ?>">
																<p class="help-block"></p>	
															</div>
														</div>														
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label>VAT Number </label>
																<input type="text" class="form-control" name="vat" id="vat" value="<?php echo $vat; ?>">
																<p class="help-block"></p>	
															</div>
														</div>														
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label>GST Number </label>
																<input type="text" class="form-control" name="gst" id="gst" value="<?php echo $gst; ?>" maxlength="15">
																<p class="help-block"></p>	
															</div>
														</div>														
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label>PAN Number </label>
																<input type="text" class="form-control" name="pan" id="pan" value="<?php echo $pan; ?>">
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
									</div>						
									<div class="col-sm-6">
										<div class="col-md-12 col-sm-12 col-xs-12 bg-grey">											
											<h4><i class="fa fa-building"></i>&nbsp;&nbsp;Address Information</h4>														
										</div>
										<div class="row">
																						
												<div class="col-md-12">
										<div class="form-body">										
										<div class="form-group">
											<label for="address">Company Type </label>
											<select class="form-control" name="company_type" id="company_type" >
															<option value="">-- Select Company List--</option>
															<?php 
																$company_list_d=$db->rp_getData('company_type',"*","1=1","",0);
																while($company_list_r=mysqli_fetch_assoc($company_list_d))
																{
																	?>
																	<option <?php echo ($company_type==$company_list_r['id'])?"selected":"" ; ?> value="<?php echo $company_list_r['id']?>">
																	<?php echo $company_list_r['name'];?>
																	</option>
																	<?php
																}
															?>
															</select>
										</div>
										
										<div class="form-group">
											<label for="address">Address </label>
											<input type="text" class="form-control" name="address" id="address" value="<?php echo $address; ?>">
										</div>
										
										<div class="form-group">
											<div class="row">
												<div class="col-md-6">
													<label for="zip">Pincode </label>
													<input type="text" class="form-control" name="zip" id="zip" value="<?php echo $zip; ?>" maxlength="6">
													<p class="help-block"></p>	
												</div>
												
											</div>
										</div>
										
										<div class="form-group">
											<div class="row">
												<div class="col-md-4">
													<label for="country">Country</label>
													<select name="country" id="country" class="form-control" onChange="aj.fetchState(this.value,'#state',function(mode,result){callbackState(mode,result)});">
														<option val=""> Select Country</option>
															<?php
															$country_r = $db->rp_getData("country","*");
															if(mysqli_num_rows($country_r)>0)
															{
																while($country_d = mysqli_fetch_array($country_r))
																{
															?>
														<option value="<?php echo $country_d['name']; ?>" <?php if($country_d['name']==$country){?> selected <?php } ?>><?php echo $country_d['name']; ?></option>
															<?php
																}
															}
															?>
													</select>
												</div>
												<div class="col-md-4">
													<label for="state">State</label>
													<select name="state" id="state" class="form-control" onChange="aj.fetchCity(this.value,'#city',function(mode,result){callbackCity(mode,result)});">
														<option val=""> Select State</option>
															<?php
															if($_REQUEST['mode']=='edit')
															{
															$state_name=$db->rp_getValue("state","name","id='".$state."'",0);
															?>
															<option value="<?php echo $state; ?>" "<?php echo $state_name ?>" selected > <?php echo $state_name; ?>  </option>
															<?php
															}
															?>
													</select>
												</div>
												<div class="col-md-4">
													<label for="city">City</label>
													<select name="city" id="city" class="form-control" >
														<option value=""> Select City </option>
														<?php
														if($_REQUEST['mode']=='edit')
														{
														$city_name=$db->rp_getValue("city","name","id='".$city."'",0);
														?>
														<option value="<?php echo $city; ?>" "<?php echo $city_name ?>" selected > <?php echo $city_name; ?>  </option>
														<?php
														}
														?>
														</select>
												</div>
												</br>
												
												<div class="col-md-12">
													<div class="form-group">
														<label for="bank_name">Bank Name </label>
														<input type="text" class="form-control" name="bank_name" id="bank_name" value="<?php echo $bank_name; ?>">
													</div>
												</div>
												
												<div class="col-md-12">
													<div class="form-group">
														<label for="account_no">Bank Account No </label>
														<input type="text" class="form-control" name="account_no" id="account_no" value="<?php echo $account_no; ?>">
													</div>
												</div>
												<div class="col-md-12 ">
													<div class="form-group">
														<label for="ifsc_code">IFSC Code </label>
														<input type="text" class="form-control" name="ifsc_code" id="ifsc_code" value="<?php echo $ifsc_code; ?>">
													</div>
												</div>
												<br/>
												<br/>
												
											</div>
										</div>
									</div>
									
										
									</div>
									</div>
									</div>
									<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
										<button type="submit" name="submit" class="btn green pull-right"><i class="fa fa-floppy-o"></i> Save</button>								
									</div>
								</div>
								
							</div>
							
						<div class="tab-pane" id="tab_branch_info">
						<div class="row">
						<div class="col-sm-12">
							<div class="col-md-12 col-sm-12 col-xs-12 bg-grey">								
								<h4><i class="fa fa-sitemap"></i>&nbsp;Branches</h4> 												
							</div>							
							<div class="row">
								<div class="col-md-12">
								<div class="form-body">
									<div class="row">									
										<div class="col-md-3">
											<div class="form-group">											
												<input  id="branch_name" name="branch_name" type="text" class="form-control" placeholder="Branch name."/>
												<p class="help-block"></p>												
											</div>
										</div>										
										<div class="col-md-1">
											<div class="form-group">											
													<button type="button" name="add-branch" id="add-branch" class="btn pull-right green"><i class="fa fa-plus"></i>&nbsp;Add</button>						
											</div>
										</div>
										<div class="col-md-12">
											<div id="results">
											</div>
										</div>	
									</div>	
								</div>	
								</div>	
							</div>	
						</div>	
						</div>	
						<br><br><br>
					</div>	
		<div class="tab-pane" id="tab_contact_info">
		<div class="row">
						<div class="col-sm-12">
							<div class="col-md-12 col-sm-12 col-xs-12 bg-grey">								
								<h4><i class="fa fa-user-plus"></i>&nbsp; Contact Persons</h4> 												
							</div>							
							<div class="row">
								<div class="col-md-12 col-xs-12 col-sm-12">
								<div class="form-body">
									<div class="row">									
										<div class="col-md-3 col-sm-12 col-xs-12">
										<div class="col-md-12">
											<div class="form-group">											
												<input  id="contact_name" name="contact_name" type="text" class="form-control" placeholder="Name."/>
												<p class="help-block"></p>												
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">											
												<input  id="contact_designation" name="contact_designation" type="text" class="form-control" placeholder="Designation"/>
												<p class="help-block"></p>												
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">											
												
												<select id="contact_branch" name="contact_branch" class="form-control">
												<?php 
												$vid=isset($_REQUEST['id'])?$_REQUEST['id']:"";
												if($vid!="")
												{
													$branches=$objVendor->getVendorBranches($vid);	
													if(!empty($branches))
													{
														?>
														<option value="">--Select Branch --</option>
														<?php
														foreach($branches as $b)
														{
															?>
															<option value="<?php echo $b['id'];?>"><?php echo $b['branch_name'];?></option>
															<?php
														}
													}
													else
													{
														?>
														<option value="">--Select Branch --</option>
														<?php
													}
												}
												else
												{
													?>
													<option value="">--Select Branch --</option>
													<?php 
												}
											?>	
												</select>
												<p class="help-block"></p>												
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">											
												<input  id="contact_phone" name="contact_phone" type="text" class="form-control" placeholder="Phone" maxlength="10"/>
												<p class="help-block"></p>												
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">											
												<input  id="contact_email" name="contact_email" type="text" class="form-control" placeholder="Email"/>
												<p class="help-block"></p>												
											</div>
										</div>										
										<div class="col-md-12">
											<div class="form-group">											
													<button type="button" data-mode="add_contact" name="add-contact-person" id="add-contact-person" class="btn  green"><i class="fa fa-user-plus"></i>&nbsp;Add</button>						
											</div>											
										</div>
										</div>
										
										<div class="col-md-9 col-sm-12 col-xs-12">
											<div id="results2">
											</div>
										</div>	
									</div>	
								</div>	
								</div>	
							</div>	
						</div>	
						</div>	
					</div>	
				</div>	
			</div>
			</div>
		</div>
	</div>
	</div>
	
</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript">
/*$("#country").change(function () {
 if($("#country option:selected").val() == 1){
	$('#state').removeAttr('disabled');
	$('#city').removeAttr('disabled');
 }  else {
	$('#state').attr('disabled', 'disabled');
	$('#city').attr('disabled', 'disabled');
 }
});*/
$("#phone").numeric();
$("#zip").numeric();
$("#contact_phone").numeric();
$(document).ready(function(){$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) {$(this).parent().find('.help-block').html(""); $(this).parent().removeClass("has-error"); } }); });
function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;
	if($("#cname").val()=="" || $("#cname").val().split(" ").join("")==""){		
		vd=aj.error('cname',"Please enter vendor name.","add_error");
		isValid=false;
	}
	var alphanumers = /^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])*$/;
	if(!alphanumers.test($("#cname").val())){
		aj.error('cname','Name can have only alphabets and numbers!!','add_error');
		isValid=false;
	}
	if($("#tin").val()!="" || $("#tin").val().split(" ").join("")!=""){
		if($("#tin").val().length!=11){
			aj.error('tin','Please enter valid 11 Digits TIN no!!','add_error');
			isValid=false;
		}
	}
	if($("#phone").val()!="" || $("#phone").val().split(" ").join("")!=""){
		if($("#phone").val().length!=10){
			aj.error('phone','Please enter valid mobile no!!','add_error');
			isValid=false;
		}
	}
	if($("#gst").val()!="" || $("#gst").val().split(" ").join("")!=""){
		if($("#gst").val().length!=15){
			aj.error('gst','Please enter valid 11 Digits GST no!!','add_error');
			isValid=false;
		}
		if(!alphanumers.test($("#gst").val())){
			aj.error('gst','GST no can have only alphabets and numbers!!','add_error');
			isValid=false;
		}
	}
	
	if($("#vat").val()!="" || $("#vat").val().split(" ").join("")!=""){
		if($("#vat").val().length!=11){
			aj.error('vat','Please enter valid 11 Digits VAT no!!','add_error');
			isValid=false;
		}
	}
	if($("#pan").val()!="" || $("#pan").val().split(" ").join("")!=""){
		if($("#pan").val().length!=11){
			aj.error('pan','Please enter valid 11 Digits PAN no!!','add_error');
			isValid=false;
		}
	}
	if($("#zip").val()!="" || $("#zip").val().split(" ").join("")!=""){
		if($("#zip").val().length!=6){
			aj.error('zip','Please enter valid 6 Digits pincode no!!','add_error');
			isValid=false;
		}
	}
	 if($("#email").val()!="")
    {
        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($("#email").val())){

        }else{
           	aj.error('email','Please enter valid email!!','add_error');
        	isValid= false;
        }
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
var searchName="";
var data_url = "vendor_branch_data_get_ajax.php";
var data_cotact_person_url = "vendor_contact_get_ajax.php";
var data_vendor_branch_url="vendor_branch_get_ajax.php";
<?php 
	if(isset($_REQUEST['mode'])  && $_REQUEST['mode']=='edit' && isset($_REQUEST['id']) && $_REQUEST['id']!='')
	{
		echo "var vid=".$_REQUEST['id'].";";
	}
	else
	{
		echo "var vid=0;";
	}
?>
$(document).ready(function(){
	displayRecords(100,1);	
	displayContactRecords(100,1);	
	$("#searchName").keyup(function(event)
	{
		if(event.keyCode == 13){
			$("#searchByName").click();
		}
	});
	
	$('#add-branch').on('click',function()
	{
		
		var isVendorInformationAvailable=check_form();
		if(!isVendorInformationAvailable || vid=="")
		{
			toastr.error('Please Save Vendor Information First');
		}
		else
		{
			
			if(checkBranchInfo())
			{ 
				var branch_name=$('#branch_name').val();							
				$.ajax({
					url:"vendor_branch_ajax_function.php",
					type:"POST",
					data:{
						mode:'add_branch',
						branch_name:branch_name,
						vid:vid,
						
					},
					 success:function(json, textStatus, jqXHR) 
					{
						json=$.parseJSON(json);
						msg=json.ack_msg;
						if(json.ack==1)
						{
							
							toastr.success(msg,"Success!!");
							$('#branch_name').val("");
							displayRecords(10);
							
						}
						else
						{
							toastr.error(msg, 'Error!!')
						}
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						toastr.error('Sorry, Server Error!!.', 'Error!!')
					}
					
				})
			}
						
			
		}
	});
	$('#add-contact-person').on('click',function()
	{
		
		var isVendorInformationAvailable=check_form();
		if(!isVendorInformationAvailable || vid=="")
		{
			toastr.error('Please Save Vendor Information First');
		}
		else
		{
			
			if(checkContactInfo())
			{ 
				var contact_name=$('#contact_name').val();							
				var contact_branch=$('#contact_branch').val();							
				var contact_designation=$('#contact_designation').val();							
				var contact_phone=$('#contact_phone').val();							
				var contact_email=$('#contact_email').val();
				var mode=$(this).attr('data-mode');	
				var cpid=$(this).attr('data-id');	
				$.ajax({
					url:"vendor_contact_ajax_function.php",
					type:"POST",
					data:{
						mode:mode,
						contact_branch:contact_branch,
						contact_name:contact_name,
						contact_designation:contact_designation,
						contact_phone:contact_phone,
						contact_email:contact_email,
						vid:vid,
						cpid:cpid
						
					},
					 success:function(json, textStatus, jqXHR) 
					{
						json=$.parseJSON(json);
						msg=json.ack_msg;
						if(json.ack==1)
						{
							
							toastr.success(msg,"Success!!");
							$('#contact_name').val("");
							$('#contact_branch').select2("val","");
							$('#contact_designation').val("");
							$('#contact_phone').val("");
							$('#contact_email').val("");
							$('#add-contact-person').attr('data-mode','add_contact');
							$('#add-contact-person').html('<i class="fa fa-user-plus"></i> &nbsp; Add Contact');
							displayContactRecords(100);
							
						}
						else
						{
							toastr.error(msg, 'Error!!')
						}
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						toastr.error('Sorry, Server Error!!.', 'Error!!')
					}
					
				})
			}
						
			
		}
	});
	$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
});
	
	function checkBranchInfo()
	{
		var isValid=true;
		if($("#branch_name").val()=="" || $("#branch_name").val().split(" ").join("")==""){				
			aj.error('branch_name','Please enter branch name!!','add_error');
			isValid=false;
		}
		else
		{
			aj.error('branch_name','','remove_error');
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
	function checkContactInfo()
	{
		var isValid=true;
		if($("#contact_name").val()=="" || $("#contact_name").val().split(" ").join("")==""){				
			aj.error('contact_name','Please enter contact name!!','add_error');
			isValid=false;
		}
		else
		{
			aj.error('contact_name','','remove_error');
		}
		if($("#contact_designation").val()=="" || $("#contact_designation").val().split(" ").join("")==""){				
			aj.error('contact_designation','Please enter designation!!','add_error');
			isValid=false;
		}
		else
		{
			aj.error('contact_designation','','remove_error');
		}
		
		if($("#contact_phone").val()=="" || $("#contact_phone").val().split(" ").join("")=="" || $("#contact_phone").val().length!=10){				
			aj.error('contact_phone','Please enter contact number!!','add_error');
			isValid=false;
		}
		else
		{
			aj.error('contact_phone','','remove_error');
		}
		if($("#contact_branch").val()=="" || $("#contact_branch").val().split(" ").join("")==""){				
			aj.error('contact_branch','Please enter contact branch!!','add_error');
			isValid=false;
		}
		else
		{
			aj.error('contact_branch','','remove_error');
		}
		if($("#contact_email").val()=="" || $("#contact_email").val().split(" ").join("")=="")
		{
			aj.error('contact_email','Please enter Contact Email!!','add_error');
			isValid=false;
		}
		else
		{
			if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($("#contact_email").val())){  
				
			}else{
				aj.error("contact_email","Please enter valid contact email.","add_error");
				isValid=false;
			}
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
	function del_conf(id)
	{
		var r = confirm("Are you sure you want to delete?");
		if(r){
			var isVendorInformationAvailable=check_form();
			if(!isVendorInformationAvailable && id=="" && vid=="")
			{
				toastr.error('Please save Vendor inforamtion first.')
			}
			else
			{
		
				$.ajax({
					url:"vendor_branch_ajax_function.php",
					type:"POST",
					data:{
						mode:'delete_branch',
						vid:vid,
						vbid:id,
						
					},
					 success:function(json, textStatus, jqXHR) 
					{
						json=$.parseJSON(json);
						msg=json.ack_msg;
						if(json.ack==1)
						{						
							toastr.success(msg,"Success!!");
							displayRecords();
							
						}
						else
						{
							toastr.error(msg, 'Error!!')
						}
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						toastr.error('Sorry, Server Error!!.', 'Error!!')
					}
					
				})
			}
		}

	}
	function del_conf_contact(id)
	{
		var r = confirm("Are you sure you want to delete?");
		if(r){
			var isVendorInformationAvailable=check_form();
			if(!isVendorInformationAvailable && id=="" && vid=="")
			{
				toastr.error('Please save Vendor inforamtion first.')
			}
			else
			{
		
				$.ajax({
					url:"vendor_contact_ajax_function.php",
					type:"POST",
					data:{
						mode:'delete_contact',
						cpid:id,
						vid:vid,
						
					},
					 success:function(json, textStatus, jqXHR) 
					{
						json=$.parseJSON(json);
						msg=json.ack_msg;
						if(json.ack==1)
						{						
							toastr.success(msg,"Success!!");
							displayContactRecords();
							
						}
						else
						{
							toastr.error(msg, 'Error!!')
						}
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						toastr.error('Sorry, Server Error!!.', 'Error!!')
					}
					
				})
			}
		}

	}
	function  editContact(edit_id)
	{
		$.ajax({
			type: "POST",
			url: "vendor_contact_ajax_function.php",
			data: {
				vid:vid,			
				cpid:edit_id,			
				mode:"get_contact",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json) {
				json=$.parseJSON(json);
				msg=json.ack_msg;
				if(json.ack==1)
				{						
					toastr.success(msg,"Success!!");
					detail=json.result;
					$('#contact_branch').select2("val",detail.branch);
					$('#contact_name').val(detail.name);
					$('#contact_designation').val(detail.designation);
					$('#contact_email').val(detail.email);
					$('#contact_phone').val(detail.phone);
					branches=detail.branches;
					container=$('#contact_branch');
					container.html("");
					container.append('<option value="">-- Select Branch --</option>');
					$.each(branches,function(index,value){						
						container.append(aj.createFormElement('spinner',value.id,'contact_branch_class','contact_branch_class','',value.branch_name,'',value.selected));
					});
					$('#add-contact-person').attr('data-mode','edit_contact');
					$('#add-contact-person').attr('data-id',edit_id);
					$('#add-contact-person').html('<i class="fa fa-refresh"></i> &nbsp; Update Contact');
					
				}
				else
				{
					toastr.error(msg, 'Error!!')
				}
			}
		});
		
	}
	function updateBranchInfo()
	{
		
		if(vid!="")
		{
			$("#contact_branch").attr('disabled','disabled');
			$("#contact_branch").load( data_vendor_branch_url+"?vid=" + vid,function(){
			$("#contact_branch").removeAttr('disabled');		
			});
		}
		else
		{
			$("#contact_branch").html("");
			$("#contact_branch").attr('disabled','disabled');
		}
	}
	// used when user change row limit
	function changeDisplayRowCount(numRecords) 
	{
		displayRecords(numRecords, 1);
	}
	// used when user change row limit
	function changeDisplayRowCountContact(numRecords) 
	{
		displayContactRecords(numRecords, 1);
	}
	function displayRecords(numRecords) 
	{
	var searchName 	= ($("#searchName").val()==undefined)?"":$("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?vid="+vid+"&show=" + numRecords + "&searchName=" + searchName,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?vid="+vid+"&show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?jid="+jid+"&show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	
}
function displayContactRecords(numRecords) 
	{
	var searchName 	= ($("#searchContactName").val()==undefined)?"":$("#searchContactName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results2" ).html("");
	$("#results2" ).load( data_cotact_person_url+"?vid="+vid+"&show=" + numRecords + "&searchName=" + searchName,function(){
		loadContactDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results2").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords2").val();
		$(".loading-div2").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results2").load(data_cotact_person_url+"?vid="+vid+"&show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div2").hide(); //once done, hide loading element
			loadContactDataTable();
		});
		
	});
	$("#results2").on( "change", "#numRecords2", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords2").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results2").load(data_cotact_person_url+"?jid="+jid+"&show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div2").hide(); //once done, hide loading element
			loadContactDataTable();
		});
		
	});
}
	function loadDataTable()
	{
		$('#datatable_1').dataTable({
			"bPaginate": false,
			"bFilter": false,
			"bInfo": false,
			"bAutoWidth": false, 
			"aoColumns": [
				  { "sWidth": "1%" }, 
				  { "sWidth": "15%" },
				  { "sWidth": "10%" },			
				  { "sWidth": "10%","bSortable": false }
				],
			 "oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-sitemap '></i> &nbsp; No Branch Found"},
		});
		updateBranchInfo();
    }
	function loadContactDataTable()
	{
		$('#datatable_2').dataTable({
			"bPaginate": false,
			"bFilter": false,
			"bInfo": false,
			"bAutoWidth": false, 
			"aoColumns": [
				  { "sWidth": "0.4%" }, 
				  { "sWidth": "10%" },
				  { "sWidth": "5%" },
				  { "sWidth": "5%" },			
				  { "sWidth": "8%" },							  		
				  { "sWidth": "10%","bSortable": false }
				],
			 "oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-user-plus '></i> &nbsp; No Contact Found"},
		});
    }
	function searchByName()
	{
	searchName = $("#searchName").val();
	displayRecords(100,1);
	return false;
	}
	function clearSearchByName()
	{
		searchName = "";
		state = "";
		city = "";
		$("#searchName").val("");
		
		displayRecords(100,1);
	}
	function searchByContactName()
	{
	searchName = $("#searchContactName").val();
	displayContactRecords(100,1);
	return false;
	}
	function clearSearchByContactName()
	{
		searchName = "";
		$("#searchContactName").val("");
		displayContactRecords(100,1);
	}
	
</script>
<script type="text/javascript">
function hideQuickButton(id)
{
	$("#"+id).hide();
}
function showQuickButton(id)
{
	$("#"+id).show();
}
function quickEdit(pid){
	
	$(".lblQk").show(200);
	$(".btnQuickEdit").show(200);
	$(".txtQk").hide();
	$(".btnQk").hide();
	$(".btnEdit").show(200);
	$("#btnQuickEdit"+pid).hide();
	$("#btnSave"+pid).show(200);
	$("#btnCancel"+pid).show(200);
	$("#lblName"+pid).hide();
	$("#txtName"+pid).show(400);
	$("#name"+pid).focus();
	$("#lblCat"+pid).hide();
	$("#ddCat"+pid).show(400);
}
function cancelQuickEdit(pid){
	
	$("#txtName"+pid).hide();
	$("#lblName"+pid).show(200);
	$("#ddCat"+pid).hide();
	$("#lblCat"+pid).show(200);
	$("#btnSave"+pid).hide();
	$("#btnCancel"+pid).hide();
	$("#btnQuickEdit"+pid).show(200);
}
function saveQuickEdit(pid){
	var name 	= $("#name"+pid).val();
	var vbid 	= pid;
	if(vbid!=""){
		$.ajax({
			type: "POST",
			url: "vendor_branch_ajax_function.php",
			data: {
				vid:vid,
				vbid:vbid,
				branch_name:name,
				mode:"edit_branch",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json) {
				json=$.parseJSON(json);
				msg=json.ack_msg;
				if(json.ack==1)
				{						
					toastr.success(msg,"Success!!");
					displayRecords();
					
					
				}
				else
				{
					toastr.error(msg, 'Error!!')
				}
			}
		});
	}else{
		alert("Category is not selected.");
	}
}
/*function getCity(val){
	
	 $.ajax({
        type: "POST",
        url: "find_city.php",
        data:'state_id='+val,
        success: function(data){
        $("#city").html(data);
		$('#city').select2("val","");
	
}
	 });
}
function getState(val) {
	$.ajax({
	type: "POST",
	url: "findstate.php",
	data:'country_id='+val,
	success: function(data){
		$("#state").html(data);
	}
	});
}*/
function getCityName(cid){
	state=$('#state').val();
	city=cid;
	displayRecords(100,1);
	
}
function callbackState(mode,result){
	if(mode==0){
		$('#state').html('<option value="">Select State</option>');
		$('#state').select2("val","");
		$('#city').html('<option value="">Select City</option>');
		$('#city').select2("val","");
		}
}
function callbackCity(mode,result){
	if(mode==0){
		$('#city').html('<option value="">Select City</option>');
		$('#city').select2("val","");
			}
}
</script>

</body>
</html>