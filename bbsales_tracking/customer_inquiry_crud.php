<?php
$page_id=605;$page_slug='customer_inquiry';
$ctable 	= "customer_inquiry";
$ctable1 	= "Inquiry";
$ctable2 	= "Lead";
// $main_page 	= "product_mgmt";
$page 		= $ctable."_manage";
$page_title = ucwords($_REQUEST['mode'])." ".$ctable2;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>"customer_inquiry_manage.php","title"=>"Manage ".$ctable2),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit ".$ctable2));
include("connect.php");
require_once("../include/class.customer_inquiry.php");
$objCustomerIquiry= new CustomerInquiry();
$name			= "";
$code			= "";
if(isset($_REQUEST['submit'])){
	
	$detail['source_of_inquiry'] = $db->clean($_REQUEST['source_of_inquiry']);
	$detail['executive_type'] = $db->clean($_REQUEST['executive_type']);
	$detail['company_name'] = $db->clean($_REQUEST['company_name']);
	$detail['contact_person'] = $db->clean($_REQUEST['contact_person']);
	$detail['person_name'] = $db->clean($_REQUEST['contact_person']);
	$detail['mobile_number'] = $db->clean($_REQUEST['mobile_number']);
	$detail['whatsapp_number'] = $db->clean($_REQUEST['whatsapp_number']);
	$detail['country'] = $db->clean($_REQUEST['country']);
	$detail['state'] = $db->clean($_REQUEST['state']);
	$detail['city'] = $db->clean($_REQUEST['city']);
	$detail['inquiry_created_by'] = $db->clean($_REQUEST['inquiry_created_by']);
	$detail['inquiry_assign_to'] = $db->clean($_REQUEST['inquiry_assign_to']);
	$detail['inquiry_date'] = $db->clean($_REQUEST['inquiry_date']);
	$detail['first_followup_date'] = $db->clean($_REQUEST['first_followup_date']);
	$detail['followup_detail'] = $db->clean($_REQUEST['followup_detail']);
	$detail['date_of_call'] = $db->clean($_REQUEST['date_of_call']);
	$detail['email_address'] = $db->clean($_REQUEST['email_address']);
	$detail['address'] = $db->clean($_REQUEST['address']);
	
	//$detail['country'] = $db->clean($_REQUEST['country']);
	$detail['country'] = "India";
	$detail['distributor_id'] = $db->clean($_REQUEST['distributor_id']);
	$detail['sales_id'] = $db->clean($_REQUEST['sales_id']);
	
	$detail['image_path'] = $db->clean($_REQUEST['image_path']);
	$detail['old_image_path'] = $db->clean($_REQUEST['old_image_path']);
	
	$detail['product_id'] = $db->clean($_REQUEST['product_id']);
	$detail['quantity'] = $db->clean($_REQUEST['quantity']);
	$detail['remark'] = $db->clean($_REQUEST['remark']);
	$detail['quotation_flag'] = $db->clean($_REQUEST['quotation_flag']);
	$detail['customer_requirement'] = $db->clean($_REQUEST['customer_requirement']);
	
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objCustomerIquiry->InsertCustomerInquiry($detail,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_grid.php?msg=inserted");
		}else{
				$db->addErrorMessage($reply['ack_msg']);
			}
		}
		
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objCustomerIquiry->UpdateCustomerInquiry($detail,$_FILES);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location($ctable."_grid.php?msg=updated");
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
		$reply=$objCustomerIquiry->GetEditDataCustomerInquiry($detail);
		if($reply['ack']==1){
			//$SuccessMsg = $reply['ack_msg'];
			$result=$reply['result'];
			// print_r($result);exit;
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
		$reply=$objCustomerIquiry->DeleteCustomerInquiry($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		// $db->rp_location($ctable."_grid.php?msg=inserted");
		$db->rp_location($ctable."_grid.php?msg=inserted");
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
	$db->rp_location($ctable."_grid.php?msg=updated");
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
<style type="text/css">
	.test{
		font-weight: 800;
	}
	/*.b-3{
		border:3px solid;
	}*/
</style>
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
									<!-- <h4><b>Inquiry</b></h4> -->
									<h4><b>Lead</b></h4>
									<hr/>
									<div class="row">
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Source Medium</label>
													<select class="form-control b-3" id="source_of_inquiry" name="source_of_inquiry" autofocus>
														<option value="">Select source medium</option>
								                        <?php
								                        $source_of_inquiry_r = $db->rp_getData("source_of_inquiry","*","isDelete=0",0);
								                        if(mysqli_num_rows($source_of_inquiry_r)>0)
								                        {
								                            while($source_of_inquiry_d = mysqli_fetch_array($source_of_inquiry_r))
								                            {
								                                ?>
								                                <option value="<?php echo $source_of_inquiry_d['id']; ?>" <?=($source_of_inquiry == $source_of_inquiry_d['id'])?"selected":"";?>><?php echo $source_of_inquiry_d['name']; ?></option>
								                                <?php
								                            }
								                        }
								                        ?>
								                    </select> 
													<p class="help-block"></p>
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Inquiry Type</label>
													<select class="form-control b-3" id="executive_type" name="executive_type" onchange="get_distributor(this.value);">
								                        <option value="">Select Type of Inquiry</option>
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
										</div>

										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Company Name<code>*</code></label>
													<input type="text" class="form-control b-3" name="company_name" id="company_name" value="<?php echo $company_name; ?>">
													<p class="help-block"></p>
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Person Name<code>*</code></label>
													<input type="text" class="form-control b-3" name="contact_person" id="contact_person" value="<?php echo $person_name; ?>">
													<p class="help-block"></p>
												</div>
											</div>
										</div>

										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Contact Number<code>*</code></label>
													<input type="text" class="form-control b-3" name="mobile_number" id="mobile_number" maxlength="10" value="<?php echo $mobile_number; ?>">
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Whatsapp Number</label>
													<input type="text" class="form-control b-3" name="whatsapp_number" id="whatsapp_number" maxlength="10" value="<?php echo $whatsapp_number; ?>">
													<p class="help-block"></p>
												</div>
											</div>
										</div>

										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Email Address</label>
													<input type="text" class="form-control b-3" name="email_address" id="email_address" value="<?php echo $email_address; ?>">
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Address</label>
													<textarea type="text" class="form-control b-3" name="address" id="address" value="<?php echo $address; ?>"><?php echo $address; ?></textarea>
													<p class="help-block"></p>
												</div>
											</div>
										</div>


										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Country<code>*</code></label>
													<select class="form-control" name="country" id="country" onChange="filter_country(this.value);">
								                        <option value="">Select Country</option>
								                        <?php
								                        $country_r = $db->rp_getData("country","*",0);
								                        if(mysqli_num_rows($country_r)>0)
								                        {
								                            while($country_d = mysqli_fetch_array($country_r))
								                            {
								                                ?>
								                                <option value="<?php echo $country_d['name']; ?>" <?=(strtolower($country) == strtolower($country_d['name']))?"selected":"";?>><?php echo $country_d['name']; ?></option>
								                                <?php
								                            }
								                        }
								                        ?>
								                    </select>      
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">State</label>
													 <select class="form-control" name="state" id="state" autofocus onChange="filter_state(this.value);">
								                        <option value="">Select State</option>
								                    </select> 
													<p class="help-block"></p>
												</div>
											</div>
										</div>

										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">City</label>
													<select class="form-control" name="city" id="city">
								                        <option value="">Select City</option>
								                    </select>   
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<?php
														$cls = "";
								                        if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
								                        {
								                        	$cls = "readonly='readonly'";
								                        }

								                        if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0 && !isset($inquiry_created_by) && ($inquiry_created_by=="" || $inquiry_created_by==0))
								                        {
								                        	$inquiry_created_by=$_SESSION[SITE_SESS.'REFERANCE_ID'];
								                        }
								                    ?>
													<label class="test">Created by <code>*</code></label>
													<select class="form-control b-3" id="inquiry_created_by" <?=$cls?> >
								                        <option value="">Select Created by</option>
								                        <?php
								                        
								                        $sales_id_r = $db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1");
								                        if($sales_id_r)
								                        {
								                            while($sales_id_d = mysqli_fetch_assoc($sales_id_r))
								                            {?>
								                                <option value="<?=$sales_id_d['id']?>" <?=($inquiry_created_by == $sales_id_d['id'])?"selected":"";?>><?=$sales_id_d['name']?></option>
								                            <?php
								                            }
								                        } 
								                        ?>
								                    </select> 
								                    <input type="hidden" name="inquiry_created_by" value="<?=$inquiry_created_by;?>"/>
													<p class="help-block"></p>
												</div>
											</div>
										</div>
										
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Assigned to</label>
													<select class="form-control b-3" id="inquiry_assign_to" name="inquiry_assign_to">
								                        <option value="">Select Inquiry Assigned to</option>
								                        <?php
								                        $sales_id_r = $db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1");
								                        if($sales_id_r)
								                        {
								                            while($sales_id_d = mysqli_fetch_assoc($sales_id_r))
								                            {?>
								                                <option value="<?=$sales_id_d['id']?>" <?=($inquiry_assign_to == $sales_id_d['id'])?"selected":"";?>><?=$sales_id_d['name']?></option>
								                            <?php
								                            }
								                        } 
								                        ?>
								                    </select> 
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Date Of Call</label>
													<input type="text" class="form-control b-3 datepicker" name="date_of_call" id="date_of_call" value="<?php if($_REQUEST['mode']=="add"){ echo date('d-m-Y');} else{ echo $date_of_call;}?>">
													<p class="help-block"></p>
												</div>
											</div>
										</div>
										
										<?php
										if($_REQUEST['mode']=="add") 
										{
											?>
											<div class="col-md-12">
												<div class="col-md-6">
													<div class="form-group">
														<label class="test">First Follow-up Date</label>
														<input class="form-control b-3 datepicker" type="text" name="first_followup_date" id="first_followup_date">
															<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label class="test">First Follow-up Detail</label>
														<textarea class="form-control b-3" name="followup_detail" id="followup_detail"><?= $followup_detail; ?></textarea>
														<p class="help-block"></p>
													</div>
												</div>
											</div>
											<?php
										}
										?>
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
                                                <!-- <lable>Select Blog Image <code>*</code></lable> -->
                                                <input data-image="<?php echo ($image_path!="" && file_exists(CUSTOMER_INQUIRY_IMAGE.$image_path))?CUSTOMER_INQUIRY_IMAGE.$image_path:"";?>" type="file" accept="image/*" name="image_path[]" id="image_path" data-old-image-dom="old_image_path"  data-old-image-path="<?php echo $image_path ?>" value="" multiple>
                                             </div>
                                            </div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-actions">
							<button type="submit" name="submit" class="btn green">Submit</button>
							<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
						</div>
					</div>
					<div class="col-md-6">
						<div class="portlet box blue">
							<div class="portlet-body form">
								<div class="form-body">
									<!-- <h4><b>Product Inquiry</b></h4> -->
									<h4><b>Product Lead</b></h4>
									<hr/>
									<div class="row">
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Product Name</label>
													<select class="form-control b-3" id="product_id" name="product_id">
								                        <option value="">Select Product</option>
								                        <?php
								                        $product_r = $db->rp_getData("product","*","isDelete=0 AND isActive=1");
								                        if($product_r)
								                        {
								                            while($product_d = mysqli_fetch_assoc($product_r))
								                            {
								                            	$product_weight = $db->rp_getData("product_weight_price","weight_id","product_id='".$product_d['id']."' AND isDelete=0");
																while($product_weight_d = mysqli_fetch_assoc($product_weight))
																{
																	$weight_name = $db->rp_getValue("weight","name","id='".$product_weight_d['weight_id']."' AND isDelete=0 AND id!='-1'",0);
								                            		?>
								                                	<option value="<?=$product_d['id']?>" <?=($product_id == $product_d['id'])?"selected":"";?>><?=($weight_name!="")?$product_d['name'] ." - ". $weight_name:$product_d['name']?></option>
								                            		<?php
								                        		}
								                            }
								                        } 
								                        ?>
								                    </select> 
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Quantity</label>
													<input type="text" class="form-control b-3" name="quantity" id="quantity" value="<?php echo $quantity; ?>">
													<p class="help-block"></p>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Quotation</label>
													<select class="form-control b-3" id="quotation_flag" name="quotation_flag">
								                        <option value="">Select Quotation</option>
								                        <option <?= ($quotation_flag=="1")?"selected":"" ?> value="1">Yes</option>
								                        <option <?= ($quotation_flag=="2")?"selected":"" ?> value="2">No</option>
								                    </select> 
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Remark</label>
													<textarea class="form-control b-3" name="remark" id="remark"><?= $remark; ?></textarea>
													<p class="help-block"></p>
												</div>
											</div>
											
										</div>

										<div class="col-md-12">
											<div class="form-group">
												<label class="test">Customer Requirement Details</label>
												<textarea rows="18" class="form-control b-3" name="customer_requirement" id="customer_requirement"><?= $customer_requirement; ?></textarea>
												<p class="help-block"></p>
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
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>

<script type="text/javascript" src="assets/js/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.numeric.min.js"></script>

<script type="text/javascript">
	$("#mobile_number").numeric();
	$("#whatsapp_number").numeric();
	$("#quantity").numeric();

	var date = new Date();
	date.setDate(date.getDate());

	$('#inquiry_date').datepicker({
	  format: "dd-mm-yyyy",
	  orientation: "auto",
	  startDate: date,
	  clearBtn: false
	});
	$('#date_of_call').datepicker({
	  format: "dd-mm-yyyy",
	  orientation: "auto",
	  startDate: date,
	  clearBtn: false
	});
	$('#first_followup_date').datepicker({
	  format: "dd-mm-yyyy",
	  orientation: "auto",
	  startDate: date,
	  clearBtn: false
	});
	$('#birth_date').datepicker({
	  format: "dd-mm-yyyy",
	  orientation: "auto",
	  startDate: "",
	  clearBtn: false
	});
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

		var mode = '<?php echo $_REQUEST['mode']; ?>';
	$( document ).ready(function() {
		var country_id = '<?php  echo $country; ?>';
		var state_id = '<?php  echo $state; ?>';
		var city = '<?php  echo $city; ?>';
		if(mode=='edit')
		{
    		// filter_state(state_id,city);
    		// alert(country_id);
    		// alert(state_id);
    		// alert(city);
    		filter_country(country_id,state_id,city);
    	}

    	// $('select[readonly="readonly"]').select2('distroy');
    	$('select[readonly="readonly"]').select2('destroy');
		$('select[readonly="readonly"]').prop('disabled', true);
		$('select[readonly="readonly"]').select2();

		$("#inquiry_created_by").change(function() {
			$("input[name='inquiry_created_by']").val($(this).val());
		});
	});

	function filter_state(state_id,city=""){		
    $.ajax({
        type: "POST",
        url: "find_city.php",
        data:'state_id='+state_id+"&city="+city,
        beforeSend:function(){
            // $("#loading-modal").modal('show');  
        },
        success: function(data){
            $("#city").select2("destroy");
            $("#city").html(data);
            $("#city").select2();
            // $("#loading-modal").modal('hide');
        }
    });
}
function filter_country(country_id,state="",city=""){

        $.ajax({
            type: "POST",
            url: "find_city.php",
            data:'country_id='+country_id+'&state='+state,
            beforeSend:function(){
                // $("#loading-modal").modal('show');  
                $('.preloader').fadeIn('slow');
            },
           success: function(data){
                $("#state").select2("destroy");
                $("#state").html(data);
                $("#state").select2();
                // $("#loading-modal").modal('hide');
                $('.preloader').fadeOut('slow');
                if (state!="" && city!="")
                {
                    filter_state(state,city);
                }
            }
        });
    }
function check_form()
{
	$(".form-body").children().removeClass("has-error");
	var isValid=true;
	if($("#company_name").val()=="" || $("#company_name").val().split(" ").join("")=="")
	{
		vd=aj.error('company_name',"Please Enter Name of Company.","add_error");
		isValid=false;
	}

	if($("#contact_person").val()=="" || $("#contact_person").val().split(" ").join("")=="")
	{
		vd=aj.error('contact_person',"Please Enter Contact Person.","add_error");
		isValid=false;
	}

	if($("#mobile_number").val()=="" || $("#mobile_number").val().split(" ").join("")=="")
	{
		vd=aj.error('mobile_number',"Please Enter Contact Number.","add_error");
		isValid=false;
	}
	if($("#email_address").val()!="")
	{
		if (/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test($("#email_address").val().toLowerCase())){
		}
		else
		{
			vd=aj.error('email_address',"Please Enter Valid Email Address","add_error");
			isValid=false;
			
		}
	}


	if($("#country").val()=="" || $("#country").val().split(" ").join("")=="")
	{
		vd=aj.error('country',"Please Select country.","add_error");
		isValid=false;
	}

	/*if($("#state").val()=="" || $("#state").val().split(" ").join("")=="")
	{
		vd=aj.error('state',"Please Select State.","add_error");
		isValid=false;
	}

	if($("#city").val()=="" || $("#city").val().split(" ").join("")=="")
	{
		vd=aj.error('city',"Please Select City.","add_error");
		isValid=false;
	}*/
	if($("#inquiry_created_by").val()=="" || $("#inquiry_created_by").val().split(" ").join("")=="")
	{
		vd=aj.error('inquiry_created_by',"Please Select Inquiry Created By.","add_error");
		isValid=false;
	}


	if(isValid)
	{
		var r=confirm("Are You sure want to Save this Inquiry??");
		if(r)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}
/*$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
  
function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	
	if($("#source_of_inquiry").val()=="" || $("#source_of_inquiry").val().split(" ").join("")=="")
	{
		vd=aj.error('source_of_inquiry',"Please Select Source Of Inquiry.","add_error");
		isValid=false;
	}

	if($("#executive_type").val()=="" || $("#executive_type").val().split(" ").join("")=="")
	{
		vd=aj.error('executive_type',"Please Select Type Of Inquiry.","add_error");
		isValid=false;
	}

	if($("#company_name").val()=="" || $("#company_name").val().split(" ").join("")=="")
	{
		vd=aj.error('company_name',"Please Enter Company Name.","add_error");
		isValid=false;
	}

	if($("#person_name").val()=="" || $("#person_name").val().split(" ").join("")=="")
	{
		vd=aj.error('person_name',"Please Enter Person Name.","add_error");
		isValid=false;
	}

	if($("#contact_person").val()=="" || $("#contact_person").val().split(" ").join("")=="")
	{
		vd=aj.error('contact_person',"Please Enter Contact Person.","add_error");
		isValid=false;
	}

	if($("#mobile_number").val()=="" || $("#mobile_number").val().split(" ").join("")=="")
	{
		vd=aj.error('mobile_number',"Please Enter Mobile Number.","add_error");
		isValid=false;
	}

	if($("#designation").val()=="" || $("#designation").val().split(" ").join("")=="")
	{
		vd=aj.error('designation',"Please Enter Designation.","add_error");
		isValid=false;
	}

	if($("#other_mobile_no").val()=="" || $("#other_mobile_no").val().split(" ").join("")=="")
	{
		vd=aj.error('other_mobile_no',"Please Enter Alternate Number.","add_error");
		isValid=false;
	}

	if($("#address").val()=="" || $("#address").val().split(" ").join("")=="")
	{
		vd=aj.error('address',"Please Enter Address.","add_error");
		isValid=false;
	}

	if($("#state").val()=="" || $("#state").val().split(" ").join("")=="")
	{
		vd=aj.error('state',"Please Select State.","add_error");
		isValid=false;
	}

	if($("#city").val()=="" || $("#city").val().split(" ").join("")=="")
	{
		vd=aj.error('city',"Please Select City.","add_error");
		isValid=false;
	}

	if($("#zone").val()=="" || $("#zone").val().split(" ").join("")=="")
	{
		vd=aj.error('zone',"Please Enter Zone.","add_error");
		isValid=false;
	}
	if($("#description").val()=="" || $("#description").val().split(" ").join("")=="")
	{
		vd=aj.error('description',"Please Enter Remark.","add_error");
		isValid=false;
	}
	if($("#inquiry_created_by").val()=="" || $("#inquiry_created_by").val().split(" ").join("")=="")
	{
		vd=aj.error('inquiry_created_by',"Please Select Inquiry Created By.","add_error");
		isValid=false;
	}
	if($("#inquiry_assign_to").val()=="" || $("#inquiry_assign_to").val().split(" ").join("")=="")
	{
		vd=aj.error('inquiry_assign_to',"Please Select Inquiry Assigned To.","add_error");
		isValid=false;
	}
	if($("#inquiry_date").val()=="" || $("#inquiry_date").val().split(" ").join("")=="")
	{
		vd=aj.error('inquiry_date',"Please Select Inquiry Date.","add_error");
		isValid=false;
	}
	if($("#first_followup_date").val()=="" || $("#first_followup_date").val().split(" ").join("")=="")
	{
		vd=aj.error('first_followup_date',"Please Follow-up Date.","add_error");
		isValid=false;
	}

	if($("#followup_detail").val()=="" || $("#followup_detail").val().split(" ").join("")=="")
	{
		vd=aj.error('followup_detail',"Please Follow-up Detail.","add_error");
		isValid=false;
	}

	if(isValid)
	{
		var r=confirm("Are You sure want to Save this Inquiry??");
		if(r)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
	
}*/

</script>
</body>
</html>