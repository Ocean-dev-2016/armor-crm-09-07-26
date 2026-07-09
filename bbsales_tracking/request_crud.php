<?php
$page_id=591;$page_slug='request_page';
$ctable 	= "request";
$ctable1 	= "Request";
$page 		= $ctable."_manage";
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>"request_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit ".$ctable1));

include("connect.php");

include('../include/class.request.php');
$objRequest=new Request();

$complain_type			= "";
$complain_cat_id		= "";
$complain_subcat_id		= "";
$executive_type			= "";
$customer_id			= "";
$address			    = "";
$contact_person			= "";
$state			        = "";
$city			        = "";
$zone			        = "";
$remark			        = "";
$complain_created_by	= "";
$complain_assign_to		= "";
$complain_date			= "";


if(isset($_REQUEST['submit'])){
	
	$detail['request_type'] 	    = $db->clean($_REQUEST['request_type']);
	$detail['request_cat_id']      = $db->clean($_REQUEST['request_cat_id']);
	$detail['request_subcat_id']   = $db->clean($_REQUEST['request_subcat_id']);
	$detail['executive_type']       = $db->clean($_REQUEST['executive_type']);
	$detail['customer_id']          = $db->clean($_REQUEST['customer_id']);
	$detail['address']              = $db->clean($_REQUEST['address']);
	$detail['contact_person']       = $db->clean($_REQUEST['contact_person']);
	$detail['state']                = $db->clean($_REQUEST['state']);
	$detail['city']                 = $db->clean($_REQUEST['city']);
	$detail['zone']                 = $db->clean($_REQUEST['zone']);
	$detail['remark']               = $db->clean($_REQUEST['remark']);
	$detail['request_created_by']  = $db->clean($_REQUEST['request_created_by']);
	$detail['request_assign_to']   = $db->clean($_REQUEST['request_assign_to']);
	$detail['request_date']        = $db->clean($_REQUEST['request_date']);
	$detail['entry_flag']           ='3';
	$detail['image_path']           = $db->clean($_REQUEST['image_path']);
	$detail['old_image_path']       = $db->clean($_REQUEST['old_image_path']);


	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objRequest->AddRequestPanel($detail,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("request_manage.php?msg=inserted");
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
		}
	}
		
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}

		$reply=$objRequest->UpdateNoOrderInquiry($detail);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location("request_manage.php?msg=updated");
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
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
		$detail['id']=$_REQUEST['id'];	
		$reply=$objRequest->GetEditDataNoOrderInquiry($detail);
		if($reply['ack']==1){
			//$SuccessMsg = $reply['ack_msg'];
			$result=$reply['result'];
			//print_r($result);
			extract($result);
		}else{
			$db->addErrorMessage($reply['ack_msg']);
		}
	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	if($rights['delete_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}	
		$detail['id']=$_REQUEST['id'];
		$reply=$objRequest->DeleteNoOrderInquiry($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location("request_manage.php?msg=inserted");
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
	$db->rp_location("request_manage.php?msg=updated");
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<link rel="stylesheet" type="text/css" href="assets/js/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
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
			<div class="row">
				<div class="col-sm-12">
					 <?php $db->printErrorMessage(); ?>
					 <?php $db->printSuccessMessage(); ?>		 
				</div>
			</div>
			<form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-6">					
						<div class="portlet box blue">
							<div class="portlet-body form">
								<div class="form-body">
									<div class="row">

										<div class="col-md-12">
											<div class="form-group">
												<label>Source of Request<code>*</code></label>
												<select class="form-control" id="request_type" name="request_type">
							                        <option value="">Select Source of Request</option>
							                        <option value="1">Email</option>
							                        <option value="2">Call</option>
							                        <option value="3">Whatsapp</option>
							                    </select> 
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label>Request Category<code>*</code></label>
												<select class="form-control" id="request_cat_id" name="request_cat_id" onchange="Getsubcategory(this.value);">
							                        <option value="">Select Request Category</option>
							                        <?php 
							                        $complain_categoty_r = $db->rp_getData("complain_category","*","isDelete=0");
							                        if($complain_categoty_r){
							                        	while($complain_categoty_d = mysqli_fetch_assoc($complain_categoty_r))
							                        	{?>
							                        		<option value="<?=$complain_categoty_d['id']?>" <?=($request_cat_id == $complain_categoty_d['id'])?"selected":"";?>><?=$complain_categoty_d['name']?></option>
							                            <?php
							                        	}
							                        }
							                        ?>
							                    </select> 
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label>Request SubCategory<code>*</code></label>
												<select class="form-control" id="request_subcat_id" name="request_subcat_id">
							                        <option value="">Select Request SubCategory</option>
							                    </select> 
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label>Type Of Customer<code>*</code></label>
												<select class="form-control" id="executive_type" name="executive_type" onchange="Getcustomer(this.value);">
							                        <option value="">Select Customer Type</option>
							                        <?php
							                        $customer_type = $db->rp_getData("customer_type","*","isDelete=0");
							                        if($customer_type)
							                        {
							                            while($customer_type_d = mysqli_fetch_assoc($customer_type))
							                            {?>
							                                <option value="<?=$customer_type_d['id']?>" <?=($executive_type == $customer_type_d['id'])?"selected":"";?>><?=$customer_type_d['name']?></option>
							                            <?php
							                            }
							                        } 
							                        ?>
							                    </select> 
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label>Name of Company<code>*</code></label>
												<select class="form-control" id="customer_id" name="customer_id" onchange="GetcustomerInfo(this.value);">>
							                        <option value="">Select Name of Company</option>
							                    </select> 
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label>Address<code>*</code></label>
												<textarea class="form-control" name="address" id="address"><?= $address; ?></textarea>
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label>Contact Person<code>*</code></label>
												<input class="form-control" name="contact_person" id="contact_person" value="<?php echo $contact_person?>">
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label>State<code>*</code></label>
												<input class="form-control" name="state" id="state" value="<?php echo $state?>">
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label>City<code>*</code></label>
												<input class="form-control" name="city" id="city" value="<?php echo $city?>">
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label>Zone<code>*</code></label>
												<input class="form-control" name="zone" id="zone" value="<?php echo $zone?>">
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label>Remark<code>*</code></label>
												<textarea class="form-control" name="remark" id="remark"><?= $remark; ?></textarea>
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label>Request Created by<code>*</code></label>
												<select class="form-control" id="request_created_by" name="request_created_by">
							                        <option value="">Request Created by</option>
							                        <?php
							                        $sales_id_r = $db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1");
							                        if($sales_id_r)
							                        {
							                            while($sales_id_d = mysqli_fetch_assoc($sales_id_r))
							                            {?>
							                                <option value="<?=$sales_id_d['id']?>" <?=($request_created_by == $sales_id_d['id'])?"selected":"";?>><?=$sales_id_d['name']?></option>
							                            <?php
							                            }
							                        } 
							                        ?>
							                    </select> 
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label>Request Assigned to<code>*</code></label>
												<select class="form-control" id="request_assign_to" name="request_assign_to">
							                        <option value="">Request Assigned to</option>
							                        <?php
							                        $sales_id_r = $db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1");
							                        if($sales_id_r)
							                        {
							                            while($sales_id_d = mysqli_fetch_assoc($sales_id_r))
							                            {?>
							                                <option value="<?=$sales_id_d['id']?>" <?=($request_assign_to == $sales_id_d['id'])?"selected":"";?>><?=$sales_id_d['name']?></option>
							                            <?php
							                            }
							                        } 
							                        ?>
							                    </select> 
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label>Date on which the Request assigned</label>
												<input type="text" class="form-control datepicker" name="request_date" id="request_date" value="<?php echo $request_date; ?>">
												<p class="help-block"></p>
											</div>
										</div>

										<div class="col-md-12">
											<div class="form-group">
                                               <input data-image="<?php echo ($image_path!="" && file_exists(INQUIRY_IMAGE.$image_path))?INQUIRY_IMAGE.$image_path:"";?>" type="file" accept="image/*" name="image_path[]" id="image_path" data-old-image-dom="old_image_path"  data-old-image-path="<?php echo $image_path ?>" value="" multiple>
                                            </div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-actions">
							<button type="submit" name="submit" class="btn green">Submit</button>
							<button type="button" class="btn btn-default" onClick="window.location.href='request_manage.php'">Back</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>


<script type="text/javascript" src="assets/js/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.numeric.min.js"></script>

<script type="text/javascript">
	$('#request_date').datepicker({ datepicker: true, autoclose: true, format: 'yyyy-mm-dd'});
</script>

<script type="text/javascript">
	var isImageThumbnailLoaded=false;
	var isImageThumbnailValid=false;

	$(function()
	{
		aj.imageHolder($("input[id=image_path]"),"","",
			function(isImageThumbnailLoadedReply,isImageThumbnailValidReply)
			{
         		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
         		isImageThumbnailValid=isImageThumbnailValidReply;
         		toastr.success("Old Image Found!!");
         	},
         	function(file,img)
         	{
         		if(!file)
         		{
         			toastr.error("File may be corrupted or missing. Try again!!");
         		}
         	},
         	function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height)
         	{
         		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
         		isImageThumbnailValid=isImageThumbnailValidReply;
         		toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
         	},
         	function(data)
         	{
         		isImageThumbnailLoadedReply
         	}
         	);
        })
</script>

<script type="text/javascript">

	function Getcustomer(customer_type)
	{
		$.ajax({
        	type: "POST",
        	url: "ajax_get_customer.php",
        	data:'customer_type='+customer_type,
        	beforeSend:function(){
            },
        	success: function(data){
	            $("#customer_id").select2("destroy");
	            $("#customer_id").html(data);
	            $("#customer_id").select2();
       		}
   	 	});
	}

	function Getsubcategory(category_id)
	{
		$.ajax({
        	type: "POST",
        	url: "ajax_get_subcategory.php",
        	data:'category_id='+category_id,
        	beforeSend:function(){
            },
        	success: function(data){
	            $("#request_subcat_id").select2("destroy");
	            $("#request_subcat_id").html(data);
	            $("#request_subcat_id").select2();
       		}
   	 	});
	}

	function GetcustomerInfo(customer_id)
	{
		$.ajax({
        	type: "POST",
        	url: "ajax_get_customer_info.php",
        	data:'customer_id='+customer_id,
        	beforeSend:function(){
            },
        	success: function(data){
        		var data =$.parseJSON(data);
        		$("#address").html(data.address);
        		$("#contact_person").val(data.cname);
        		$("#state").val(data.state);
        		$("#city").val(data.city);
        	}
   	 	});
	}

	$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
  
	function check_form()
	{
		$(".form-body").children().removeClass("has-error");
		var isValid=true;	
		
		if($("#request_type").val()=="" || $("#request_type").val().split(" ").join("")=="")
		{
			vd=aj.error('request_type',"Please Select Source of Request.","add_error");
			isValid=false;
		}

		if($("#request_cat_id").val()=="" || $("#request_cat_id").val().split(" ").join("")=="")
		{
			vd=aj.error('request_cat_id',"Please Select Request Category.","add_error");
			isValid=false;
		}

		if($("#request_subcat_id").val()=="" || $("#request_subcat_id").val().split(" ").join("")=="")
		{
			vd=aj.error('request_subcat_id',"Please Select Request SubCategory.","add_error");
			isValid=false;
		}

		if($("#executive_type").val()=="" || $("#executive_type").val().split(" ").join("")=="")
		{
			vd=aj.error('executive_type',"Please Select Type Of Custome.","add_error");
			isValid=false;
		}

		if($("#customer_id").val()=="" || $("#customer_id").val().split(" ").join("")=="")
		{
			vd=aj.error('customer_id',"Please Select Name of Company.","add_error");
			isValid=false;
		}

		if($("#address").val()=="" || $("#address").val().split(" ").join("")=="")
		{
			vd=aj.error('address',"Please Enter Address.","add_error");
			isValid=false;
		}
		if($("#contact_person").val()=="" || $("#contact_person").val().split(" ").join("")=="")
		{
			vd=aj.error('contact_person',"Please Enter Contact person.","add_error");
			isValid=false;
		}
		if($("#state").val()=="" || $("#state").val().split(" ").join("")=="")
		{
			vd=aj.error('state',"Please Enter state","add_error");
			isValid=false;
		}
		if($("#city").val()=="" || $("#city").val().split(" ").join("")=="")
		{
			vd=aj.error('city',"Please Enter city.","add_error");
			isValid=false;
		}

		if($("#request_created_by").val()=="" || $("#request_created_by").val().split(" ").join("")=="")
		{
			vd=aj.error('request_created_by',"Please selected Request Created By.","add_error");
			isValid=false;
		}

		if($("#request_assign_to").val()=="" || $("#request_assign_to").val().split(" ").join("")=="")
		{
			vd=aj.error('request_assign_to',"Please selected Request Assigned To.","add_error");
			isValid=false;
		}

		if($("#request_date").val()=="" || $("#request_date").val().split(" ").join("")=="")
		{
			vd=aj.error('request_date',"Please selected Request Date","add_error");
			isValid=false;
		}

		if($("#remark").val()=="" || $("#remark").val().split(" ").join("")=="")
		{
			vd=aj.error('remark',"Please Enter Remark.","add_error");
			isValid=false;
		}

		if($("#zone").val()=="" || $("#zone").val().split(" ").join("")=="")
		{
			vd=aj.error('zone',"Please Enter Zone.","add_error");
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