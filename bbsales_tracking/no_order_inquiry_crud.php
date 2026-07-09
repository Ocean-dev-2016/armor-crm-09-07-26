<?php
if($_REQUEST['type']=="-1")
{
	$page_id=621;$page_slug='prospect_inquiry';
}
else if($_REQUEST['type']=="0")
{
	$page_id        = 572;
	$page_slug      = 'no_order_inquiry';
}
else
{
	$page_id=620;$page_slug='lead_page';
}
// $page_id        = 572;
$ctable 	= "no_order_inquiry";
$ctable1 	= "Inquiry";
$page 		= $ctable."_manage";

if($_REQUEST['type']=="-1")
{	
	$page_title     = "Raw Data";
	$ctable1     = "Raw Data";
	$link     = "no_order_inquiry_manage.php?type=".$_REQUEST['type'];
}
else if ($_REQUEST['type']=="0")
{
	$page_title     = "Inquiry";
	$ctable1     = "Inquiry";
	$link     = "no_order_inquiry_manage.php?type=".$_REQUEST['type'];
}
else
{
	$page_title     = "Leads";
	$ctable1     = "Leads";
	$link     = "no_order_inquiry_manage.php?type=".$_REQUEST['type'];
}

$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>$link,"title"=>$ctable1),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit ".$ctable1));

include("connect.php");
require_once("../include/class.no_order_inquiry.php");
$objWeight= new NoOrderInquiry();
$name			= "";
$code			= "";
if(isset($_REQUEST['submit'])){
	
	if ($_REQUEST['m'] == 'change_to_inquiry') {
		$convertInqToLead = file_get_contents(ADMINSITEURL."customer_ajax_function.php?m=change_to_inquiry&inquiry_id=".$_REQUEST['id']);
		// echo ADMINSITEURL."customer_ajax_function.php?m=change_to_inquiry&inquiry_id=".$_REQUEST['id'];
		// print_r($convertInqToLead);exit();
	}
	$detail['input_image_id']   = $db->clean($_REQUEST['input_image_id']);
	$detail['source_of_inquiry']   = $db->clean($_REQUEST['source_of_inquiry']);
	$detail['executive_type']      = $db->clean($_REQUEST['executive_type']);
	$detail['company_name']        = $db->clean($_REQUEST['company_name']);
	$detail['address']             = $db->clean($_REQUEST['address']);
	$detail['contact_person']      = $db->clean($_REQUEST['contact_person']);
	$detail['person_name']         = $db->clean($_REQUEST['contact_person']);
	$detail['designation']         = $db->clean($_REQUEST['designation']);
	$detail['email_address']       = $db->clean($_REQUEST['email_address']);
	//$detail['mobile_no']       	   = $db->clean($_REQUEST['mobile_no']);
	$detail['mobile_number']       = $db->clean($_REQUEST['mobile_number']);
	$detail['phone']       = $db->clean($_REQUEST['phone']);
	$phone = implode(",", $_REQUEST['phone']);
	$customer_name = implode(",", $_REQUEST['customer_name']);
	if ($_REQUEST['phone'] == "") 
	{
		$phone ="";
	}
	if ($_REQUEST['customer_name'] == "") 
	{
		$customer_name ="";
	}

	$detail['country']             = $db->clean($_REQUEST['country']);
	$detail['state']               = $db->clean($_REQUEST['state']);
	$detail['city']                = $db->clean($_REQUEST['city']);
	$detail['zone']                = $db->clean($_REQUEST['zone']);
	$detail['description']         = $db->clean($_REQUEST['description']);
	$detail['inquiry_created_by']  = $db->clean($_REQUEST['inquiry_created_by']);
	$detail['inquiry_assign_to']   = $_REQUEST['inquiry_assign_to'] == "" && isset($_REQUEST['inquiry_assign_to']) ? $detail['inquiry_created_by'] : $db->clean($_REQUEST['inquiry_assign_to']);
	$detail['inquiry_date']        = $db->clean($_REQUEST['inquiry_date']);
	$detail['first_followup_date'] = $db->clean($_REQUEST['first_followup_date']);
	$detail['followup_detail']     = $db->clean($_REQUEST['followup_detail']);
	$detail['other_mobile_no']     = $db->clean($_REQUEST['other_mobile_no']);
	//$detail['country']             = "India";
	$detail['distributor_id']	   = $db->clean($_REQUEST['distributor_id']);
	$detail['sales_id']       	   = $db->clean($_REQUEST['sales_id']);
	$detail['image_path']          = $db->clean($_REQUEST['image_path']);
	$detail['old_image_path']      = $db->clean($_REQUEST['old_image_path']);
	$detail['birth_date']          = $db->clean($_REQUEST['birth_date']);

	$dealer_id=$db->rp_getValue("executive","id","mobile_no1='".$detail['mobile_number']."'");
	$detail['dealer_id']           = (isset($dealer_id))?$db->clean($dealer_id):"0";
	// echo $detail['dealer_id'];exit;
	$detail['date_of_call']        = $db->clean($_REQUEST['date_of_call']);
	$detail['class_id']            = $db->clean($_REQUEST['class_id']);
	$detail['area_id']             = $db->clean($_REQUEST['area_id']);
	$detail['city_id']             = $db->clean($_REQUEST['city_id']);
	$detail['inquiry_type']        = $db->clean($_REQUEST['type']);
	$detail['inq_status']        = $db->clean($_REQUEST['type']);
	$detail['gst_no']        	   = $db->clean($_REQUEST['gst_no']);
	$detail['shipping_address']    = $db->clean($_REQUEST['shipping_address']);
	$detail['billing_address']     = $db->clean($_REQUEST['billing_address']);
	$detail['industry_type']     = $db->clean($_REQUEST['industry_type']);
	$detail['main_city']     = $db->clean($_REQUEST['main_city']);
    $detail['type_of_company']     = $db->clean($_REQUEST['type_of_company']);
    $detail['purchasing_from']     = $db->clean($_REQUEST['purchasing_from']);
    $detail['pincode']     		   = $db->clean($_REQUEST['pincode']);
	$detail['top_category_id']  = isset($_REQUEST['top_category_id'])?$db->clean($_REQUEST['top_category_id']):0;
	if (sizeof($_REQUEST['top_category_id']) != 0) 
	{
	  $detail['top_category_id']=implode(',',$_REQUEST['top_category_id']);
	}
	else
	{
	  $detail['top_category_id'] = "";
	}
	$detail['entry_flag']=1;
	$detail['update_entry_flag']=1;
// print_r($detail);exit;


	$product_id = $_REQUEST['product_id'];
	$weight_id = $_REQUEST['weight_id'];
	$quantity = $_REQUEST['quantity'];
	$remark = $_REQUEST['remark'];
	$pro_name = $_REQUEST['pro_name'];

	$size[]=sizeof($product_id);
	$size[]=sizeof($weight_id);
	$size[]=sizeof($quantity);
	$size[]=sizeof($remark);
	$size[]=sizeof($pro_name);
	$value_check=sizeof($quantity);

	if(in_array($value_check,$size))
	{
		$isValidArray=true;
	}
	else
	{
		$isValidArray=false;
	}
	if($isValidArray && !empty($quantity))
	{
		for($i=0;$i<sizeof($quantity);$i++)
		{
			$item[]=array("quantity"=>$quantity[$i],"pid"=>$product_id[$i],"remark"=>$remark[$i],"pro_name"=>$pro_name[$i],"weight_id"=>$weight_id[$i]);
		}
	}
	
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
	{
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objWeight->InsertNoOrderInquiry($detail,$_FILES,$item);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?type=".$_REQUEST['type']."&msg=inserted");
		}
		else
		{
			$db->addErrorMessage($reply['ack_msg']);
		}
	}
		
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$detail['dealer_id'] = $db->rp_getValue("no_order_inquiry","dealer_id","id='".$_REQUEST['id']."'",0);
		// $detail['class_id'] = $db->rp_getValue("no_order_inquiry","class_id","id='".$_REQUEST['id']."'",0);
		// $detail['area_id'] = $db->rp_getValue("no_order_inquiry","area_id","id='".$_REQUEST['id']."'",0);
		// print_r($_FILES);exit;
		$reply=$objWeight->UpdateNoOrderInquiry($detail,$_FILES,$item);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    /*$db->rp_location($ctable."_grid.php?msg=updated");*/
		    $db->rp_location($ctable."_manage.php?type=".$_REQUEST['type']."&msg=updated");
		}
		else
		{
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
		$reply=$objWeight->GetEditDataNoOrderInquiry($detail);
		$item_info=$objWeight->GetInquiryItems($detail);
		if($reply['ack']==1){
			//$SuccessMsg = $reply['ack_msg'];
			$result=$reply['result'];
			//print_r($result);exit();
			extract($result);
		}else{
			$db->addErrorMessage($reply['ack_msg']);
		}

		if($item_info['ack']==1){
		$store_inward_id=$_REQUEST['id'];
		$item_info=$item_info['result'];
		}
		else{
			$item_info=array();
		}
	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}	
	$detail['id']=$_REQUEST['id'];
	$reply=$objWeight->DeleteNoOrderInquiry($detail);
	if($reply['ack']==1){
	$db->addSuccessMessage($reply['ack_msg']);
	// $db->rp_location($ctable."_grid.php?msg=inserted");
	// print_r($_REQUEST);exit;
	$db->rp_location($ctable."_manage.php?type=".$_REQUEST['type']."&msg=delete");
	}
	else{
		$db->addErrorMessage($reply['ack_msg']);
	}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="cancel"){
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}	
	$detail['id']=$_REQUEST['id'];
	$reply=$objWeight->CancelNoOrderInquiry($detail);
	if($reply['ack']==1){
	$db->addSuccessMessage($reply['ack_msg']);
	/*$db->rp_location($ctable."_grid.php?msg=inserted");*/
	$db->rp_location($ctable."_manage.php?type=".$_REQUEST['type']."&msg=delete");
	}
	else{
		$db->addErrorMessage($reply['ack_msg']);
	}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="followup"){
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}	
	$detail['id']=$_REQUEST['id'];
	$reply=$objWeight->followupNoOrderInquiry($detail);
	if($reply['ack']==1){
	$db->addSuccessMessage($reply['ack_msg']);
	$db->rp_location($ctable."_manage.php?type=".$_REQUEST['type']."&msg=inserted");
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
	/*$db->rp_location($ctable."_grid.php?msg=updated");*/
	$db->rp_location($ctable."_manage.php?type=".$_REQUEST['type']."&msg=updated");
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
<link href="assets/global/plugins/bootstrap-datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet" type="text/css" /> 

<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
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
	ul.ui-autocomplete.ui-widget-content {
		width: 20% !important;
		background: #eee !important;
		color: #333 !important;
		padding-left: 0 !important;
		border: 1px solid #ddd !important;
	}
	ul.ui-autocomplete.ui-widget-content li.ui-menu-item {
		list-style: none !important;
		padding: 5px 5px 5px 10px !important;
		cursor: pointer !important;
		font-size: 14px !important;
	}
	ul.ui-autocomplete.ui-widget-content li.ui-menu-item:hover{
		border: 1px solid #fbcb09;
		background: #fdf5ce;
		color: #c77405;
	}
	#dataTable_past_inquiry {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
	}
</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable."_manage.php?type=".$_REQUEST['type'];?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
				<input type="hidden" name="input_image_id" id="input_image_id" value="">
				<div class="row">
					<div class="col-md-6">					
						<div class="portlet box blue">
							<div class="portlet-body form">
								<div class="form-body">
									<!-- <h4><b>Leads</b></h4> -->
									<?php
									if($_REQUEST['type']=="-1"){
										$title = "Raw Data";
									} 
									elseif ($_REQUEST['type']=="0") {
										$title = "Inquiry";
									}
									else{
										$title = "Leads";	
									}
									?>
									<h4><b><?= $title; ?></b></h4>
									<p style="font-size: 20px;color: #ed2b2be6;"><strong><?php echo $_REQUEST['m'] == "change_to_inquiry" ? "Submit to convert Raw data to inquiry" : ""; ?></strong></p>
									<hr/>
									<div class="row">
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Select Company<code>*</code></label>
													<select class="form-control b-3" id="type_of_company" name="type_of_company" autofocus>
														<option value="">Select Company</option>
								                        <?php
								                        $company_r = $db->rp_getData("company_master","*","isDelete=0","id DESC",0);
								                        if(mysqli_num_rows($company_r)>0)
								                        {
								                            while($company_d = mysqli_fetch_array($company_r))
								                            {
								                                ?>
								                                <option value="<?php echo $company_d['id']; ?>" <?=($type_of_company == $company_d['id'])?"selected":"";?>><?php echo $company_d['name']; ?></option>

								                                


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
													<label class="test">Source of Inquiry<?php echo $_REQUEST['type'] != "-1" ? "<code>*</code>" : ''; ?></label>
													<select class="form-control b-3" id="source_of_inquiry" name="source_of_inquiry" autofocus>
														<option value="">Select Source of Inquiry</option>
								                        <?php
								                        $source_of_inquiry_r = $db->rp_getData("source_of_inquiry","*","isDelete=0","display_order",0);
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
										</div>
										<div class="col-md-12">


											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Type of Inquiry<?php echo $_REQUEST['type'] != "-1" ? "<code>*</code>" : ''; ?></label>
													<select class="form-control b-3" id="executive_type" name="executive_type">
								                        <option value="">Select Type of Inquiry</option>
								                        <?php
								                        $customer_type = $db->rp_getData("customer_type","*","isDelete=0");
								                        if($customer_type)
								                        {
								                            while($customer_type_d = mysqli_fetch_assoc($customer_type))
								                            { ?>
						                                <option value="<?=$customer_type_d['id']?>" <?=($executive_type == $customer_type_d['id'])?"selected":"";?>><?=$customer_type_d['name']?></option>
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
													<label class="test">Type of Industry</label>
													<select class="form-control b-3" id="industry_type" name="industry_type">
								                        <option value="">Select Type of Industry</option>
								                        <?php
								                        $customer_type = $db->rp_getData("industry_type","*","isDelete=0");
								                        if($customer_type)
								                        {
								                            while($customer_type_d = mysqli_fetch_assoc($customer_type))
								                            { ?>
								                                <option value="<?=$customer_type_d['id']?>" <?=($industry_type == $customer_type_d['id'])?"selected":"";?>><?=$customer_type_d['name']?></option>
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
													<label class="test">Firm Name<code>*</code></label>
													<input type="text" class="form-control b-3" name="company_name" id="company_name" value="<?php echo $company_name; ?>">
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">GST NO</label>
													<input type="text" class="form-control b-3" name="gst_no" id="gst_no" maxlength="15" value="<?php echo $gst_no; ?>">
													<p class="help-block"></p>

										</div>
									</div>
										</div>

										
											<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Mobile Number<code>*</code></label>
													<input type="text" class="form-control b-3" name="mobile_number" id="mobile_number" maxlength="10" value="<?php echo $mobile_number; ?>">
													<p class="help-block"></p>

										
										</div>
										</div>
										<div class="col-md-6">
												<div class="form-group">
													<label class="test">Person Name<?php echo $_REQUEST['type'] != "-1" ? "<code>*</code>" : ''; ?></label>
													<input type="text" class="form-control b-3" name="contact_person" id="contact_person" value="<?php echo $person_name; ?>">
													<p class="help-block"></p>

										
										</div>
										</div>
										</div>
										<div class="col-md-12">
											<div class="form-group multiple-entry">
											<h4><strong>Phone</strong> </h4>
											<br>
										<?php if($_REQUEST['mode'] != 'edit')
										{ 
										?>
											<div class="col-md-6">
											
													<label class="test">Phone</label>
													<input type="text" class="form-control b-3 multiple-mobile-number" name="phone[]" id="phone" maxlength="15" value="<?php echo $phone; ?>">
												
											</div>
											<div class="col-md-6">
												
													<label class="test">Contact Person Name</label>
													<input type="text" class="form-control b-3" name="customer_name[]" id="customer_name" value="<?php echo $person_name; ?>">
													<p class="help-block"></p>
												
											</div>
											<div id="new_mobile_number">
											</div>	
										<?php
										 }
										 else
										 { ?>
											<div id="new_mobile_number">
											</div>	
										 <?php 
											$count = 0;
										 	$no_order_inquiry_vs_mobile_number_r = $db->rp_getData("customer_vs_phone_no","*","customer_id='".$_REQUEST['id']."' AND isDelete=0 AND ref_table='no_order_inquiry'","",0);
												while($inquiry_d = mysqli_fetch_array($no_order_inquiry_vs_mobile_number_r))
								           		 {
								            		$count++;
											 ?>
											  <?php if($count==1)
											  { ?>
											 	<div class="form-group">
											 		<div class="col-md-6 phone-m">
											 			<label>Phone</label>
											 			<input type="text" class="form-control b-3 multiple-mobile-number" name="phone[]" id="phone" maxlength="15" value="<?php echo $inquiry_d['phone_no']; ?>">
											 		</div>
											 		<div class="col-md-6">
													<label class="test">Contact Person</label>
													<input type="text" class="form-control b-3" name="customer_name[]" id="customer_name" value="<?php echo $inquiry_d['name']; ?>">
													<p class="help-block"></p>
													</div>
												</div>
											 
											 <?php } else{ ?>
											 	<div class="col-md-12">
													 <div class="form-group" id="removeClass<?=$count;?>">
											 			<button type="button" onclick="Remove_add(<?=$count;?>)" class="remove-this-first text-danger" id="BtnDel"><i class="fa fa-trash"></i></button>
											 			<div class="col-md-6">
											 			<label>Phone</label>
														<input type="text" class="form-control b-3 multiple-mobile-number" name="phone[]" id="phone" maxlength="15" value="<?php echo $inquiry_d['phone_no']; ?>">
														</div>
														<div class="col-md-6">
														<label>Contact Person</label>
													 	<input type="text" class="form-control" name="customer_name[]" id="customer_name" value="<?php echo $inquiry_d['name']; ?>">
														</div>
													</div>
													</div>
											
											<?php }} ?>
										
										<?php } ?>
										
									
									<div class="form-group">
										<button class="btn btn-primary" type="button" id="add_new">ADD</button>
										<p class="help-block"></p>
									</div>
									
								</div>
								</div>

<!-- 
													<p class="help-block"></p>
												</div>
											</div> -->
                                  
										<!-- 	<div class="col-md-6">
												<div class="form-group">
													<label class="test">Whatsapp Number</label>
													<input type="text" class="form-control b-3" name="other_mobile_no" id="other_mobile_no" maxlength="10" value="<?php echo $other_mobile_no; ?>">
													<p class="help-block"></p>
												</div>
											</div> -->
										
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Country<code>*</code></label>
													<select class="form-control" name="country" id="country" onChange="filter_country(this.value);">
								                        <option value="">Select Country </option>
								                        <?php
								                        $country_r = $db->rp_getData("country","*",0);
								                        if(mysqli_num_rows($country_r)>0)
								                        {
								                            while($country_d = mysqli_fetch_array($country_r))
								                            {
								                            	if ($_REQUEST['mode'] == 'add') {
								                            		

								                                ?>
											                    <option value="<?php echo $country_d['name']; ?>" <?=('India' == $country_d['name'])?"selected":"";?>><?php echo $country_d['name']; ?></option>
								                                <?php
								                            	} 
								                            	else
								                            	{
								                            		?>
											            		<option value="<?php echo $country_d['name']; ?>" <?=($country == $country_d['name'])?"selected":"";?>><?php echo $country_d['name']; ?></option>
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
													<label class="test">State<code>*</code></label>
													<select class="form-control" name="state" id="state" onChange="filter_state(this.value);"filter_state>
								                        <option value="">Select State</option>
								                    </select> 
								                    <input type="hidden" name="class_id" id="class_id" value="">
													<p class="help-block"></p>
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Select City<?php echo $_REQUEST['type'] != "-1" ? "<code>*</code>" : ''; ?></label>
													<select class="form-control" name="main_city" id="main_city" onChange="filter_city(this.value);"filter_state>
								                        <option value="">Select City</option>
								                    </select> 
								                    <input type="hidden" name="city_id" id="city_id" value="">
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Route<!-- <code>*</code> --></label>
													<select class="form-control b-3" name="city" id="city" onChange="get_id(this.value);">
								                        <option value="">Select Route</option>
								                    </select>  
								                    <input type="hidden" name="area_id" id="area_id" value="<?= $area_id ?>">
													<p class="help-block"></p>
												</div>
											</div>
										</div>

										<div class="col-md-12"> 
											<!-- <div class="col-md-6">
												<div class="form-group">
													<label class="test">Zone</label>

													<select class="form-control" name="zone" id="zone" >
								                        <option value="">Select Zone </option>
								                        <?php
								                        $zone_r = $db->rp_getData("zone","*","isDelete=0",0);
								                        if(mysqli_num_rows($zone_r)>0)
								                        {
								                            while($zone_d = mysqli_fetch_array($zone_r))
								                            {

								                                ?>
								                                <option value="<?php echo $zone_d['id']; ?>" <?=($zone == $zone_d['id'])?"selected":"";?>><?php echo $zone_d['name']; ?></option>
								                                <?php
								                            }
								                        }
								                        ?>
								                    </select> -->

													<!-- <input type="text" class="form-control b-3" name="zone" id="zone" value="<?php echo $zone; ?>">
													<p class="help-block"></p> -->
											<!-- 	</div> -->
											<!-- </div> -->
											
										</div>

										<div class="col-md-12">
											   <div class="col-md-6">
												<div class="form-group">
													<label class="test">Zone</label>

													<select class="form-control" name="zone" id="zone" >
								                        <option value="">Select Zone </option>
								                        <?php
								                        $zone_r = $db->rp_getData("zone","*","isDelete=0",0);
								                        if(mysqli_num_rows($zone_r)>0)
								                        {
								                            while($zone_d = mysqli_fetch_array($zone_r))
								                            {

								                                ?>
								                                <option value="<?php echo $zone_d['id']; ?>" <?=($zone == $zone_d['id'])?"selected":"";?>><?php echo $zone_d['name']; ?></option>
								                                <?php
								                            }
								                        }
								                        ?>
								                    </select>

													<!-- <input type="text" class="form-control b-3" name="zone" id="zone" value="<?php echo $zone; ?>">
													<p class="help-block"></p> -->
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
													<label class="test">Inquiry Created by <code>*</code> </label>
													<select class="form-control b-3" id="inquiry_created_by" <?=$cls?> >
								                        <option value="">Select Inquiry Created by</option>
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
													<?php
													if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0 && !isset($inquiry_assign_to) && ($inquiry_assign_to=="" || $inquiry_assign_to==0))
								                        {
								                        	$inquiry_assign_to=$_SESSION[SITE_SESS.'REFERANCE_ID'];
								                        }
								                        ?>
													<label class="test">Inquiry Assigned to <code>*</code> </label>
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
													<label class="test">Whatsapp Number</label>
													<input type="text" class="form-control b-3" name="other_mobile_no" id="other_mobile_no" maxlength="10" value="<?php echo $other_mobile_no; ?>">
													<p class="help-block"></p>
												</div>
											</div>
										</div>
											<div class="col-md-12">
											<div class="col-md-6">
											<div class="form-group">
												<label class="">Category<?php echo $_REQUEST['type'] != "-1" ? "<code>*</code>" : ''; ?></label>
												<select name="top_category_id[]" id="top_category_id" class="form-control top_category_id" multiple >
													<option value="">Select Category</option>
													<?php
													$category_data_r=$db->rp_getData("top_category_master","id,name","isDelete=0","",0);

													if ($_REQUEST['mode'] == 'add') {
														$all_category_selected = "selected";
													} else {
														$all_category_selected = "";
													}

													$top_category_id=explode(',', $top_category_id);

													while ($category_data_d=mysqli_fetch_assoc($category_data_r)) 
													{
													?>
													<option <?=(in_array($category_data_d['id'], $top_category_id))?"selected":$all_category_selected?> value="<?=$category_data_d['id']?>"><?= $category_data_d['name']?>	
													</option>
													<?php
													}
													?>
												</select>
												<p class="help-block"></p>
											</div>
										</div>
										
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Email Address</label>
													<input type="text" class="form-control b-3" name="email_address" id="email_address" value="<?php echo $email_address; ?>">
													<p class="help-block"></p>
												</div>
											</div>
										</div>
											<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Address</label>
													<input type="text" class="form-control b-3" name="address" id="address" value="<?php echo $address; ?>">
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
											<div class="form-group">
													<label class="test">Purchasing From</label>
													<input type="text" class="form-control b-3" name="purchasing_from" id="purchasing_from" value="<?php echo $purchasing_from; ?>">
													<p class="help-block"></p>
											</div>
											</div>
										</div>
										<!-- 	<div class="col-md-6">
												<div class="form-group">
													<label class="test">Designation</label>
													<input type="text" class="form-control b-3" name="designation" id="designation" value="<?php echo $designation; ?>">
													<p class="help-block"></p>
												</div>
											</div> -->

										<div class="col-md-12">
											
										<!-- 	<div class="col-md-6">
												<div class="form-group">
													<label class="test">Remark</label>
													<textarea class="form-control b-3" name="description" id="description"><?= $description; ?></textarea>
													<p class="help-block"></p>
												</div>
											</div> -->
										</div>

										

										<?php
										if($_REQUEST['mode']=="add") 
										{
											?>
											<div class="col-md-12">
												<div class="col-md-6">
													<div class="form-group">
														<label class="test">First Follow-up Date</label>
														<input class="form-control b-3 datepicker" type="text" name="first_followup_date" id="first_followup_date" value="">
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
													<label class="test">Date Of Call</label>
													<input type="text" class="form-control b-3 datepicker" name="date_of_call" id="date_of_call" 
													value="<?php if($_REQUEST['mode']=="add"){ echo date('d-m-Y');} else{ echo $date_of_call;}?>">
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Date on which the Inquiry Assigned</label>
													<input type="text" class="form-control b-3 datepicker" name="inquiry_date" id="inquiry_date" value="<?php if($_REQUEST['mode']=="add"){ echo date('d-m-Y');} else{ echo $inquiry_date;}?>">
													<p class="help-block"></p>
												</div>
											</div>
										</div>

										<div class="col-md-12">
											<!-- <div class="col-md-6">
												<div class="form-group">
													<label class="test">Birth Date</label>
													<input class="form-control b-3 datepicker" type="text" name="birth_date" id="birth_date" value="<?php if($birth_date=="01-01-1970"){ echo $birth_date = "";} else{echo  $birth_date;}?>">
													<p class="help-block"></p>
												</div>
											</div> -->
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Remark</label>
													<textarea class="form-control b-3" name="description" id="description"><?= $description; ?></textarea>
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
	                                                <!-- <lable>Select Blog Image <code>*</code></lable> -->
	                                                <input data-image="<?php echo ($image_path!="" && file_exists(INQUIRY_IMAGE.$image_path))?INQUIRY_IMAGE.$image_path:"";?>" type="file" accept="image/*" name="image_path[]" id="image_path" data-old-image-dom="old_image_path"  data-old-image-path="<?php echo $image_path ?>" value="" multiple>
	                                                <input type="hidden" name="old_image_path" id="old_image_path" value="<?php echo $image_path;?>">
	                                             </div>
	                                             <?php 
	                                            if($_REQUEST['mode']=="edit") 
	                                            {

							                    if($image_path!="")
							                    {
							                        $img = explode(",", $image_path);
							                        $imgpath = array();
							                        for ($i=0; $i < sizeof($img); $i++)
							                        { 
							                            $imgpath[] = "../resource/image/".$db->rp_getValue("media","url","reference_id='".$_REQUEST["id"]."' AND id='".$img[$i]."'",0);
							                        }

							                        for ($i=0; $i < sizeof($imgpath); $i++)
							                        {
							                            if($i==0){
							                            ?>
							                                <a href="<?=$imgpath[$i]?>" data-lightbox="Inquiry<?=$count?>" data-title="Inquiry <?=$_REQUEST['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
							                            <?php 
							                            }else{
							                            ?>
							                                <div class="hidden">
							                                    <a href="<?=$imgpath[$i]?>" data-lightbox="Inquiry<?=$count?>" data-title="Inquiry <?=$LeadD['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
							                                </div>
							                            <?php
							                            }
							                        }
							                    }
							                    else
							                    {
							                        $img = $image_path = DEFAULTIMG;
						                        ?>
							                        <!-- <a href="<?=$img?>" data-lightbox="Inquiry<?=$count?>" data-title="Inquiry <?=$LeadD['id']?>"><img src="<?=$img?>" style="height: 80px;"></a> -->
							                        <?php
							                    }
							                	}
							                    ?>

                                            </div>
                                         
											
										</div>

									<!-- 	<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Shipping Address</label>
													<textarea class="form-control b-3" name="shipping_address" id="shipping_address"><?= $shipping_address; ?></textarea>
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Billing Address</label>
													<textarea class="form-control b-3" name="billing_address" id="billing_address"><?= $billing_address; ?></textarea>
													<p class="help-block"></p>
												</div>
											</div>
										</div> -->
									</div>
								</div>
							</div>
						</div>
						<div class="form-actions">
							<button type="submit" name="submit" class="btn green">Submit</button>
							<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable."_manage.php?type=".$_REQUEST['type'];?>'">Back</button>
						</div>
					</div> 

					<div class="col-md-6">
						<!-- <div class="form-group">
								<br/><br/><button class="btn btn-primary" type="button" id="other_details">Other Details</button>
													<p class="help-block"></p>
												</div> -->
						<div class="portlet box blue" id="other_details1" >
							<div class="portlet-body form">
							
								<div class="form-body">
										<a data-toggle="collapse" class="<?= $a_class; ?>" data-target="#collapseOne" aria-expanded="<?= $aria_expanded; ?>" aria-controls="collapseOne" style="color:#fff;">
	                                 <div class="portlet grey-cascade box">
	 	                             <div class="portlet-title">
		                           	<div class="caption" style="padding: 11px 0px 9px 10px;font-size: 18px;line-height: 18px;float: left;">Other Details<span style="float: right; "><i class="fa fa-angle-down"></i></span>
		                           	</div>
  		                             </div>
  	                               </div>
	                               </a>
	                        	<hr/>

									<?php
									/*if($_REQUEST['type']=="-1")
									{
										$title1 = "Raw Data Product";
									} 
									elseif ($_REQUEST['type']=="0") {
										$title1 = "Inquiry Product";
									}
									else{
										$title1 = "Leads Product";	
									}*/
									?>
									
									<!--  <div class="portlet grey-cascade box overflow-auto" style="box-shadow: none;" id="please-scroll">
  	                                <div id="collapseOne" class="portlet-body collapse <?= $in_class; ?>">
									<h4><b><?= $title1?></b></h4>
									<hr/>

									<div class="row">
										<div class="col-md-12">
											 <div class="col-md-9">
												<div class="form-group">
													<label class="test">Product Name</label>
													<select class="form-control b-3" id="product_ids" name="product_id">
								                        <option value="">Select Product</option>
								                        <?php
								                        $product_r = $db->rp_getData("product","*","isDelete=0 AND isActive=1");
								                        if($product_r)
								                        {
								                            while($product_d = mysqli_fetch_assoc($product_r))
								                            {
								                            	$product_weight = $db->rp_getData("product_weight_price","weight_id,catno","product_id='".$product_d['id']."' AND isDelete=0");
																while($product_weight_d = mysqli_fetch_assoc($product_weight))
																{
																	$weight_name = $db->rp_getValue("weight","name","id='".$product_weight_d['weight_id']."' AND isDelete=0 AND id!='-1'",0);

																	$name = $db->rp_getValue("weight","name","id='".$product_weight_d['weight_id']."'");

																	$pro_name  = $product_d['name'];

																	$name1= htmlentities($name." ".$pro_name." ");
								                            		?>
								                                	<option class="pids_<?=$product_d['id']."_".$product_weight_d['weight_id']; ?>" data-weight-id="<?php echo $product_weight_d['weight_id']?>" data-name="<?php echo $name1; ?>"  data-pid="<?php echo $product_d['id']?>" data-cat_no="<?= $product_weight_d['catno'] ?>" value="<?=$product_d['id']."_".$product_weight_d['weight_id']; ?>" <?=($product_id == $product_d['id'])?"selected":"";?>><?=($weight_name!="")?$product_d['name'] ." - ". $weight_name:$product_d['name']." - ".$product_weight_d['catno']?></option>
								                            		<?php
								                        		}
								                            }
								                        } 
								                        ?>
								                    </select> 
													<p class="help-block"></p>
												</div>
											</div> 

											<div class="col-md-4">
												<div class="form-group">
													<label>Category </label>
													<select class="form-control" name="category_id[]" id="category_id" multiple="multiple">
														<option value="">select Category</option>
														<?php
														$cat_r=$db->rp_getData("top_category_master","*","isDelete=0 AND isActive=1",0);
														while($cat_d=mysqli_fetch_assoc($cat_r))
														{
														?>
														<option value="<?= $cat_d['id'] ?>"><?= $cat_d['name'] ?></option>
														<?php
														}
														?>
													</select>
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-5">
												<div class="form-group">
													<label>Products </label>
													<select class="form-control" name="product_id" id="product_ids">
														<option value="">select product</option>
													</select>
													<p class="help-block"></p>
												</div>
											</div>

											<div class="col-md-3">
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
													<label class="test">Customer Requirement Detail Note</label>
													<textarea class="form-control b-3" name="customer_requirement" id="customer_requirement"><?= $customer_requirement; ?></textarea>
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6 text-center">
												<div class="form-group">
													<br/><br/><button class="btn btn-primary" type="button" id="add">ADD</button>
													<p class="help-block"></p>
												</div>
											</div>
										</div>
									</div>  -->
									<!-- <hr> -->
									<?php
										/*if($_REQUEST['type']=="-1")
										{
											$title2 = "Raw Data Product Grid";
										} 
										else if ($_REQUEST['type']=="0") {
											$title2 = "Inquiry Product Grid";
										}
										else {
											$title2 = "Lead Product Grid";
										}*/
										?>
										<!-- <h4><b><?= $title2 ?></b></h4>
										<hr/> -->
										<!-- <div class="row">
											<div class="col-md-12">
												<table id="datatable_1" class="table table-striped table-bordered table-hover">
													<thead>
														<tr>
															<th>Product Name</th>
															<th>Qty</th>
															<th>Remark</th>
															<th>Action</th>
														</tr>
													</thead>
													<tbody>
														<?php
														//print_r($item_info); exit;
														if(!empty($item_info))
														{
															foreach($item_info as $i)
															{ 

																?>
																	<tr>
																		<td style="width: 300px;text-align: center;">
																		<input type='hidden' class='product_id' name='product_id1' id='product_id' value='<?php echo $i['product_id']."_".$i['weight_id'] ?>'><input type='hidden' name='product_id[]' value='<?php echo $i['product_id']; ?>' id='product_id'/><input type='hidden' name='pro_name[]' value='<?php echo $i['product_name']; ?>' id='pro_name'><input type='hidden' name='weight_id[]' value='<?php echo $i['weight_id'] ?>' id='pro_name'><?php echo $i['product_name']." - ".$i['cat_no']; ?>
																	</td>	
																	<td class='text-center'><input type='hidden' name='quantity[]' class='form-control positive  quantity' style='text-align:right;' value='<?php echo $i['quantity']; ?>' id='quantity'/><?php echo $i['quantity']; ?></td>
																	<td class='text-center'><input type='text' name='remark[]' class='form-control' value='<?php echo $i['item_remark']; ?>' id='remark'/></td>
																	<td  class="text-center">
																	<?php
																		$total_dispatch_record=$db->rp_getTotalRecord("inquiry_item","inquiry_id='".$i['inquiry_id']."' AND isDelete=0",0);
																		if($total_dispatch_record>0)
																		{
																		}
																		else
																		{
																			?>
																			<a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></a>
																			<?php
																		}
																		?>
																	</td>
																	</tr>
																<?php
															}
														}
														else
														{
															?>
															<tr>
																<td colspan="4" class="text-center remove-this-first">No Data Found!!</td>
															</tr>
															<?php
														}
														?>
													</tbody>
												</table>
											</div>
										</div>
									<hr> -->
									<div class="row">

										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Birth Date</label>
													<input class="form-control b-3 datepicker" type="text" name="birth_date" id="birth_date" value="<?php if($birth_date=="01-01-1970"){ echo $birth_date = "";} else{echo  $birth_date;}?>">
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Pincode<code>*</code></label>
													<input type="text" class="form-control b-3" name="pincode" id="pincode" value="<?php echo $pincode; ?>" maxlength="6">
													<p class="help-block"></p>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Shipping Address</label>
													<textarea class="form-control b-3" name="shipping_address" id="shipping_address"><?= $shipping_address; ?></textarea>
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Billing Address</label>
													<textarea class="form-control b-3" name="billing_address" id="billing_address"><?= $billing_address; ?></textarea>
													<p class="help-block"></p>
												</div>
											</div>
										</div>
										<!-- <div class="col-md-12">
											
										</div> -->
									</div>
									<hr>
									<?php
									if($_REQUEST['type']=="-1")
									{
										$title3 = "Past Raw Data(s)";
									} 
									else if($_REQUEST['type']=="0")
									{
										$title3 = "Past Inquiry(s)";
									} 
									else
									{
										$title3 = "Past Lead(s)";
									} 
									?>
									<h4><b><?= $title3 ?></b></h4>
									<hr/>
									<div class="row">
										<div class="col-md-12">
											<table class="table table-bordered table-striped dataTable" id="dataTable_past_inquiry">
												<thead>
													<tr>
														<th>Inquiry No</th>
														<th>Name</th>
														<th>Company Name</th>
														<th>Person Mobile No</th>
														<th>Inquiry Date</th>
														<th>Inquiry Status</th>
														<th>Inquiry Created By</th>
														<th>Inquiry Assign By</th>
													</tr>
												</thead>
												<tbody class="past-data">
													<tr>
														<th class="inq_no"></th>
														<th class="name"></th>
														<th class="inq_cname"></th>
														<th class="inq_mobile_no"></th>
														<th class="inq_date"></th>
														<th class="inq_status"></th>
														<th class="inq_created_by"></th>
														<th class="inquiry_assign_to"></th>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
									<?php
									if($_REQUEST['mode']!='edit')
									{
										?>
										</div>
							</div>
						</div>
					</div>
				</div>
			</div>
									<?php } 
									?>
								
					<?php
					if($_REQUEST['mode']=="edit")
					{
						if($_REQUEST['type']=="-1")
						{
							$title4 = "Raw Data Attachment";
						} 
						else if($_REQUEST['type']=="0")
						{
							$title4 = "Inquiry Attachment";
						}
						else
						{
							$title4 = "Lead Attachment";
						}
						?>
						<!-- <div class="col-md-6">
							<div class="portlet box blue">
								<div class="portlet-body form">
									<div class="form-body"> -->
										<h4><b><?= $title4 ?></b></h4>
										<hr/>
										<div class="row">
											<div class="col-md-12">
												<div class="col-md-6">
													<div class="form-group">
		                                                <!-- <lable>Select Blog Image <code>*</code></lable> -->
		                                                <input data-image="<?php echo ($file_path!="" && file_exists(INQUIRY_ATTACH_IMAGE.$file_path))?INQUIRY_ATTACH_IMAGE.$file_path:"";?>" type="file" accept="image/*" name="file_path" id="file_path" 
		                                                 value="">
		                                             </div>
	                                            </div>
												<div class="col-md-6">
													<div class="form-group">
														<br/><button class="btn btn-primary" type="button" id="add_attachment" style="margin-top: 7px;">ADD</button>
														<p class="help-block"></p>
													</div>
												</div>
											</div>
										</div>
										<div class="quick_notes"></div>
									</div>
								</div>
							</div>
						</div>
						</div>
						</div>
						
						<?php
					}
					?> 
				</div>
			</form>
		</div>
	</div>
</div>

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>

<script type="text/javascript" src="assets/js/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datetimepicker/jquery.datetimepicker.full.js"></script>
<script type="text/javascript" src="assets/js/jquery.numeric.min.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script> 

<script type="text/javascript">
		$("#category_id").fSelect({
		    numDisplayed: 1,
		});

	</script>

<script type="text/javascript">
	$("#other_mobile_no").numeric();
	$("#quantity").numeric();
	$("#mobile_number").numeric();
	$("#phone").numeric();
	$(".multiple-mobile-number").numeric();

	$(document).ready(function() {

		$("#mobile_number").keyup(function(event) {
			if (event.keyCode == 46 || event.keyCode == 8) {
				// let it happen, don't do anything
			} else if (/\D/g.test(this.value)) {
				alert("sorry!! Only Digits Allowed");
				this.value = this.value.replace(/\D/g, '');
			}
		});
		$("#mobile_no").keyup(function(event) {
			if (event.keyCode == 46 || event.keyCode == 8) {
				// let it happen, don't do anything
			} else if (/\D/g.test(this.value)) {
				alert("sorry!! Only Digits Allowed");
				this.value = this.value.replace(/\D/g, '');
			}
		});

		$("#other_mobile_no").keyup(function(event) {
			if (event.keyCode == 46 || event.keyCode == 8) {
				// let it happen, don't do anything
			} else if (/\D/g.test(this.value)) {
				alert("sorry!! Only Digits Allowed");
				this.value = this.value.replace(/\D/g, '');
			}
		});
	});

	var date = new Date();
	date.setDate(date.getDate());

	$('#inquiry_date').datepicker({
	  format: "dd-mm-yyyy",
	  orientation: "auto",
	  startDate: "",
	  endDate: '+0d',
	  clearBtn: false
	});

	$('#first_followup_date').datetimepicker({
	  // format: "dd-mm-yyyy",
	  // orientation: "auto",
	  // startDate: date,
	  // clearBtn: false
	  formatTime:'H:i',
			formatDate:'dd-mm-yyyy',
			minDate:'0',
			timepickerScrollbar:false,
	});
	$('#birth_date').datepicker({
	  format: "dd-mm-yyyy",
	  orientation: "auto",
	  startDate: "",
	  clearBtn: false
	});
	$('#date_of_call').datepicker({
	  format: "dd-mm-yyyy",
	  orientation: "auto",
	  startDate: "",
	  endDate: '+0d',
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

	// var isImageThumbnailLoaded=false;
	// var isImageThumbnailValid=false;

	// $(function()
	// {
	// 	aj.imageHolder($("input[id=file_path]"),"","",
	// 	function(isImageThumbnailLoadedReply,isImageThumbnailValidReply)
	// 	{
 //     		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
 //     		isImageThumbnailValid=isImageThumbnailValidReply;
 //     		toastr.success("Old Image Found!!");
 //     	},
 //     	function(file,img)
 //     	{
 //     		if(!file)
 //     		{
 //     			toastr.error("File may be corrupted or missing. Try again!!");
 //     		}
 //     	},
 //     	function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height)
 //     	{
 //     		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
 //     		isImageThumbnailValid=isImageThumbnailValidReply;
 //     		toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
 //     	},
 //     	function(data)
 //     	{
 //     		isImageThumbnailLoadedReply
 //     	}
 //     	);
 //    })


</script>

<script type="text/javascript">

		var mode = '<?php echo $_REQUEST['mode']; ?>';
		$( document ).ready(function() {
		var country_id = '<?php  echo $country; ?>';
		var state_id = '<?php  echo $state; ?>';
		var city = '<?php  echo $city; ?>';
		var main_city = '<?php  echo $main_city; ?>';
    		filter_country('India',state_id,city,main_city);
		if(mode=='edit')
		{
    		filter_state(state_id);	
    	}
    		// filter_country('India',state="",city="",main_city="");

		GetInquiryAttachment('<?php echo $_REQUEST['id'] ?>','get_attachment')
    	// $('select[readonly="readonly"]').select2('distroy');
    	$('select[readonly="readonly"]').select2('destroy');
		$('select[readonly="readonly"]').prop('disabled', true);
		$('select[readonly="readonly"]').select2();

		$("#inquiry_created_by").change(function() {
			$("input[name='inquiry_created_by']").val($(this).val());
		});
	});

	/*function filter_state(state_id,city="")
	{
		// state_id = encodeURI(state_id);
	    $.ajax({
	        type: "POST",
	        url: "find_city.php",
	        data:'state_id='+encodeURIComponent(state_id)+"&city="+city,
	        beforeSend:function(){
	            // $("#loading-modal").modal('show');  
	        },
	        success: function(data){
	        	if(data.trim()=="")
	        	{
	        		data = "<option value=''>Select City</option>";
	        	}
	        	var class_id = $("#state").find(':selected').attr('data-state_id');
				$("#class_id").val(class_id);
	            $("#city").select2("destroy");
	            $("#city").html(data);
	            $("#city").select2();
	            // $("#loading-modal").modal('hide');
	        }
	    });
	}*/

		function filter_state(state_id,main_city="")
	{
		// alert("test")
		// state_id = encodeURI(state_id);
	    $.ajax({
	        type: "POST",
	        url: "find_city.php",
	        data:'state_id='+encodeURIComponent(state_id)+"&city="+main_city,
	        beforeSend:function(){
	            // $("#loading-modal").modal('show');  
	        },
	        success: function(data){
	        	if(data.trim()=="")
	        	{
	        		data = "<option value=''>Select City</option>";
	        	}
	        	var class_id = $("#state").find(':selected').attr('data-state_id');
				$("#class_id").val(class_id);
	            $("#main_city").select2("destroy");
	            $("#main_city").html(data);
	            $("#main_city").select2();
	            // $("#loading-modal").modal('hide');
	        }
	    });
	}


	function filter_city(main_city,city="")
	{
		// state_id = encodeURI(state_id);
	    $.ajax({
	        type: "POST",
	        url: "find_city.php",
	        data:'main_city='+encodeURIComponent(main_city)+"&city="+city,
	        beforeSend:function(){
	            // $("#loading-modal").modal('show');  
	        },
	        success: function(data){
	        	if(data.trim()=="")
	        	{
	        		data = "<option value=''>Select Route</option>";
	        	}
	        	// var class_id = $("#state").find(':selected').attr('data-state_id');
				// $("#class_id").val(class_id);
	            $("#city").select2("destroy");
	            $("#city").html(data);
	            $("#city").select2();
	            // $("#loading-modal").modal('hide');

	            getCityId();
	        }
	    });
	}

	function get_id(){
		var area_id = $("#city").find(':selected').attr('data-city_id');
		$("#area_id").val(area_id);
	}

	function getCityId(){
		var main_city_id = $("#main_city").find(':selected').attr('data-main_city_id');
		$("#city_id").val(main_city_id);
	}

	 function filter_country(country_id,state="",city="",main_city=""){
	 	// alert(country_id);
        $.ajax({
            type: "POST",
            url: "find_city.php",
            data:'country_id='+country_id+'&state='+state,
            beforeSend:function(){
                // $("#loading-modal").modal('show');
                $('.preloader').fadeIn('slow');  
            },
           success: function(data){
           		if(data.trim()=="")
           		{
           			data = "<option value=''>Select State</option>";
           		}
                $("#state").select2("destroy");
                $("#state").html(data);
                $("#state").select2();

                $("#city").select2("destroy");
                $("#city").html("<option value=''>Select City</option>");
                $("#city").select2();

                // $("#loading-modal").modal('hide');
                $('.preloader').fadeOut('slow');
                if (state!="" && city!="")
                {
                    // var main_city=$("#main_city");
                    filter_state(state,main_city);
                    filter_city(main_city,city);
                    $("#class_id").val(class_id);
                }
            }
        });
    }
function check_form()
{
	$(".form-body").children().removeClass("has-error");
	var isValid=true;
	var type = '<?= $_REQUEST['type'] ?>';

	if (type != "-1")
	 {

		if($("#contact_person").val()=="" || $("#contact_person").val().split(" ").join("")=="")
		{
			vd=aj.error('contact_person',"Please Enter Contact Person Name.","add_error");
			isValid=false;
		}


		if($("#executive_type").val()=="" || $("#executive_type").val().split(" ").join("")=="")
		{
			vd=aj.error('executive_type',"Please Select Type Of Inquiry","add_error");
			isValid=false;
		}
			


		if ($("#top_category_id").val() == null) 
		{ 
			vd = aj.error('top_category_id', "Please select category.", "add_error");
			toastr.error("Please select atleast one category!!");
			isValid = false;
	    }

		if($("#source_of_inquiry").val()=="" || $("#source_of_inquiry").val().split(" ").join("")=="")
		{
			vd=aj.error('source_of_inquiry',"Please Select Source Of Inquiry.","add_error");
			isValid=false;
		}
		if($("#main_city").val()=="" || $("#main_city").val().split(" ").join("")=="")
		{
			vd=aj.error('main_city',"Please Select City.","add_error");
			isValid=false;
		}
	}
	if($("#company_name").val()=="" || $("#company_name").val().split(" ").join("")=="")
	{
		vd=aj.error('company_name',"Please Enter  Firm Name.","add_error");
		isValid=false;
	}

	if($("#pincode").val()=="" || $("#pincode").val().split(" ").join("")=="")
	{
		vd=aj.error('pincode',"Please Enter  Pincode.","add_error");
		isValid=false;
	}

	if($("#mobile_number").val()=="" || $("#mobile_number").val().split(" ").join("")=="")
	{
		vd=aj.error('mobile_number',"Please Enter Contact Number.","add_error");
		isValid=false;
	}
	// if($("#mobile_no").val()=="" || $("#mobile_no").val().split(" ").join("")=="")
	// {
	// 	vd=aj.error('mobile_no',"Please Enter Contact Number.","add_error");
	// 	isValid=false;
	// }


	/*if($("#mobile_number").val()=="" || $("#mobile_number").val().split(" ").join("")=="")
	{
		vd=aj.error('mobile_number',"Please Enter Contact Number.","add_error");
		isValid=false;
	}*/

	if($("#country").val()=="" || $("#country").val().split(" ").join("")=="")
	{
		vd=aj.error('country',"Please Select Country.","add_error");
		isValid=false;
	}

	if($("#state").val()=="" || $("#state").val().split(" ").join("")=="")
	{
		vd=aj.error('state',"Please Select State.","add_error");
		isValid=false;
	}

	if($("#inquiry_created_by").val()=="" || $("#inquiry_created_by").val().split(" ").join("")=="")
	{
		vd=aj.error('inquiry_created_by',"Please Select Inquiry Created By.","add_error");
		isValid=false;
	}

	if($("#inquiry_assign_to").val()=="" || $("#inquiry_assign_to").val().split(" ").join("")=="")
	{
		vd=aj.error('inquiry_assign_to',"Please Select Inquiry Assigned By.","add_error");
		isValid=false;
	}



	// if($("#city").val()=="" || $("#city").val().split(" ").join("")=="")
	// {
	// 	vd=aj.error('city',"Please Select Route.","add_error");
	// 	isValid=false;
	// }
	/*if($("#zone").val()=="" || $("#zone").val().split(" ").join("")=="")
	{
		vd=aj.error('zone',"Please Select Zone.","add_error");
		isValid=false;
	}*/
	if($("#type_of_company").val()=="" || $("#type_of_company").val().split(" ").join("")=="")
	{
		vd=aj.error('type_of_company',"Please Select Type Of Company.","add_error");
		isValid=false;
	}


	if(isValid)
	{
		// var type = '<?= $_REQUEST['type'] ?>';
		if(type=="-1")
		{
			var txt = "Raw Data"
		}
		else if(type=="0")
		{
			var txt = "Inquiry"
		}
		else
		{
			var txt = "Lead"
		}

		var r=confirm("Are You sure want to Save this "+ txt +" ??");
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
</script>

<script type="text/javascript">
	$(".top_category_id").fSelect();
	$(".multiple-mobile-number").numeric();
</script>

<script type="text/javascript">
	function hasValue(elem) {
    return $(elem).filter(function() { return $(this).val(); }).length > 0;
}

$("#other_details").click(function()
{
	$("#other_details1").show();
})

$("#add").click(function()
{
	var check_product=$("#product_ids").val();
	var quantity=$("#quantity").val();
	// if(check_product=="")
	// {
	// 	toastr.error('Please Select product!!');
	// }
	// else if(quantity=="" || quantity==0)
	// {
	// 	toastr.error('Please Enter At least one Quantity!!');
	// }
	// else
	// {	
		// var weight_id=$(".pids_"+check_product).data('weight-id');
		// var product_id = $("#product_id").val();

		// var product_id = $("#product_ids").val();

		var product_id = $("#product_ids").find('option:selected').data('pro_id');
		var weight_id = $("#product_ids").find('option:selected').data('weight-id');

		var quotation_remark=$("#customer_requirement").val();
		var p_name=$("#product_ids").find('option:selected').data('name');
		// var cat_no=$("#product_ids").find('option:selected').data('cat_no');
		var cat_no = $("#product_ids").find('option:selected').data('catno');
		// alert(cat_no);
		//var duplicate = hasValue($("input.product_id[value='"+product_id+"_"+weight_id+"']"));
		var duplicate = 0;
		if(duplicate==0)
		{
			var new_row="<tr><td width='300px;' class='text-center'><input type='hidden' class='product_id' name='product_id1' id='product_id' value='"+product_id+'_'+weight_id+"'><input type='hidden' name='product_id[]' value='"+product_id+"' id='product_id'/><input type='hidden' name='pro_name[]' value='"+p_name+"' id='pro_name'><input type='hidden' name='weight_id[]' value='"+weight_id+"' id='pro_name'>"+p_name+" - "+cat_no+"</td>"+
			"<td class='text-center'><input type='hidden' name='quantity[]' class='form-control positive  quantity' style='text-align:right;' value='"+quantity+"' id='quantity'/>"+quantity+"</td>"+
			"<td class='text-center'><input type='text' name='remark[]' class='form-control' value='"+quotation_remark+"' id='remark'/></td>"+
			"<td  class='text-center'><a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></td></td></tr>";
			// $("#datatable_1").find('tbody').prepend(new_row);
			$(".remove-this-first").remove();
			$("#datatable_1").find('tbody').append(new_row);
			//$("#product_ids").select2("val","");
			$("#product_ids").val("").trigger('change');
			$("#quantity").val("");
		}
		else
		{
			toastr.error("Product already added!!");
		}
	//}
})

$(document).ready(function(){
	$("#datatable_1").on('click','.delete',function()
	{
		var r = confirm("Are you sure you want to delete?");
		if(r){
			$(this).closest('tr').remove();
			recalculateFinalValues();
		}
	});
	
});

$("#mobile_number").autocomplete({
    source:"search_inquiry.php?flag=mobile",
    select:function(event,ui)
    {
	    GetcustomerInfo(ui.item.value,1);
    }
})

$("#company_name").autocomplete({
    source:"search_inquiry.php?flag=customer",
    select:function(event,ui)
    {
    	GetcustomerInfo(ui.item.value,2);
    	//GetcustomerInfo(mobile_number);
    	// var company_name = $("#company_name").val();
    	// GetcustomerInfo(company_name);
    }
})

function GetcustomerInfo(mobile_number,mobile_or_name = 1)
{
	var type = '<?= $_REQUEST['type'] ?>';
	$.ajax({
    	type: "POST",
    	url: "ajax_get_customer_info_ajax.php",
    	headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    	data: {
    		mobile_number:mobile_number,
    		mobile_or_name:mobile_or_name,
    		type:type,
    	},
    	beforeSend:function(){
        },
    	success: function(data){
    		var data =$.parseJSON(data);
    		// alert(JSON.stringify(data));
    		$("#company_name").val(data.company_name);
    		$("#mobile_number").val(data.mobile_number);
    		$("#gst_no").val(data.gst_no);
    		// $("#contact_person").val(data.person_name);
    		$("#contact_person").val(data.cname);
    		// $("#email_address").val(data.email_address);
    		$("#email_address").val(data.email);
    		// $("#other_mobile_no").val(data.whatsapp_no);
    		$("#other_mobile_no").val(data.other_mobile_no);
    		// $("#other_mobile_no").val(data.whatsapp_no);
    		$("#description").val(data.description);
    		$("#address").val(data.address);
    		$("#designation").val(data.designation);
    		$("#zone").val(data.zone);
    		$("#country").select2("destroy");
    		$('#country').val(data.country);
            $("#country").select2();
            filter_country(data.country,data.state,"");
            filter_state(data.state,data.city);
            $("#area_id").val(data.area_id);
            $("#executive_type").select2("destroy");
    		$('#executive_type').val(data.executive_type);
            $("#executive_type").select2();
            $(".past-data").html(data.past_data);
            // $("#state").select2("destroy");
    		// $('#state').val(data.state);
    		// $("#state").select2();
    	}
   	});
}

$("#add_attachment").click(function()
{
	var inquiry_id = '<?php echo $_REQUEST['id'] ?>';
	var file_path = $("#file_path").val();
	var myFormData = new FormData();
	myFormData.append("inquiry_id",inquiry_id);
	myFormData.append("file_path",$('#file_path')[0].files[0]);
	myFormData.append("mode","add_inquiry_attachment");
	$.ajax({
      	url:"ajax_add_inquiry_attachment.php",
      	type:"POST",
      	data:myFormData,
      	processData: false,
      	contentType: false,
      	beforeSend:function(){
      	},
      	success:function(result)
      	{
      		if(result!="")
      		{
	      		var imagefileold = $("#input_image_id").val();
	      		var items = imagefileold.split(',');
	      		items.push(result);
	      		items = items.join();
	      		items = items.replace(/^,|,$/g,'');
	      		$("#input_image_id").val(items);
      		}
        	toastr.success("Insert Successful...");
        	GetInquiryAttachment('<?php echo $_REQUEST['id'] ?>','get_attachment')
        }
    });
});


function GetInquiryAttachment(cid,mode)
{
	$.ajax({
      	url:"ajax_add_inquiry_attachment.php",
      	method:"POST",
      	data:{
          	id:cid,
          	mode:mode,
      	},
      	beforeSend:function(){
          	$(".quick_notes").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
      	},
      	success:function(result)
      	{
            $(".quick_notes").html(result);
      	}
  	});
}

 $("#category_id").on('change', function() {
				var tcid = $("#category_id").val();
				getProductList(tcid);
			});

		function getProductList(tcid) {

			// var tcid = $("#category_id").val();
			var cid = $("#customer_id").val();

			$.ajax({
				type: "post",
				url: "ajax_get_product.php",

				data: "cid=" + cid+"&tcid="+tcid,
				beforeSend: function() {
					$(".transCover").fadeIn(800);
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {

					/*var cd=$("#customer_id").find("option:selected").data("cash-discount");
					$("#cash_discount").val(cd);
					var ad=$("#customer_id").find("option:selected").data("add-discount");
					$("#additional_discount").val(ad);*/
					setTimeout(function() {
						$('#product_ids').html(result);
						// $("#loading-modal").modal('hide');
						$('.preloader').fadeOut('slow');
					});
				}

			})

			// if (mode == "add") {
			// 	var l = $("#datatable_1").find('tbody').find('tr').length;
			// 	if (l > 0) {
			// 		alert("You lost all added Product");
			// 		$("#datatable_1").find('tbody').html("");
			// 		recalculateRow();
			// 		recalculateFinalValues();
			// 	}
			// }
		}

		var sc_count1 = 0; 

	$("#add_new").click(function()
	{var check_mobile_number=$("#phone").val();
		if(check_mobile_number=="")
		{
			toastr.error('Please Enter Mobile Number!!');
		}
		else
		{	
			var duplicate = 0;
			if(duplicate==0)
			{
				sc_count1++;
				// sc_count++;
				// alert(sc_count);
				// alert(sc_count1);
				var new_row = '<div class="form-group" id="removeClass'+sc_count1+'"><button type="button" onclick="Remove_add('+sc_count1+')" class="remove-this-first text-danger" id="BtnDel"><i class="fa fa-trash"></i></button><br><div class="col-md-6"><label>Phone</label><input type="text" class="form-control b-3 multiple-mobile-number" onChange="checkInputLength(this.value)" name="phone[]" id="phone" maxlength="15"> </div><div class="col-md-6"><label>Contact Person Name</label><input type="text" class="form-control" name="customer_name[]" id="customer_name"></div>';

				// $("#new_shipping_address").prepend(new_row);
				$("#new_mobile_number").prepend(new_row);
				$(".multiple-mobile-number").numeric();

			}
		}
	})


	function Remove_add(del)
	{	
		var r = confirm("Are you sure you want to delete?");
		if(r)
		{
		 	$("#removeClass"+del).remove();
		}
	}
	function checkInputLength(value1) 
	{  
	    if (value1.length <= 10 ) 
	    {
	    	$(this.value).val("");
	       // alert("Please Enter 10 Digit Mobile No!!!");
	      
	    }
	    
	}

	

</script>

</body>
</html>