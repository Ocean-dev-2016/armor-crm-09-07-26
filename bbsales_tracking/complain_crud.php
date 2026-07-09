<?php
$page_id=581;$page_slug='manage_complain';
$ctable 	= "complain";
$ctable1 	= "complain";
$page 		= $ctable."_manage";
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"manage_complain.php","title"=>"Manage ".$ctable1),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit ".$ctable1));

include("connect.php");

include('../include/class.complain.php');
$objComplain=new Complain();

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
	
	// print_r($_REQUEST);exit;
	$detail['id']           = isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : '';

	$detail['complain_type']           = isset($_REQUEST['complain_type']) ? $db->clean($_REQUEST['complain_type']) : '';

	$detail['complain_cat_id']         = isset($_REQUEST['complain_cat_id']) ? $db->clean($_REQUEST['complain_cat_id']) : '';

	$detail['complain_subcat_id']      = isset($_REQUEST['complain_subcat_id']) ? $db->clean($_REQUEST['complain_subcat_id']) : '';

	$detail['executive_type']         = isset($_REQUEST['executive_type']) ? $db->clean($_REQUEST['executive_type']) : '';

	$detail['customer_id']            = isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : '';

	$detail['address']                = isset($_REQUEST['address']) ? $db->clean($_REQUEST['address']) : '';

	$detail['contact_person']         = isset($_REQUEST['contact_person']) ? $db->clean($_REQUEST['contact_person']) : '';

	$detail['state']                  = isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : '';

	$detail['city']                   = isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : '';

	$detail['zone']                   = isset($_REQUEST['zone']) ? $db->clean($_REQUEST['zone']) : '';

	$detail['remark']                 = isset($_REQUEST['remark']) ? $db->clean($_REQUEST['remark']) : '';

	$detail['complain_created_by']    = isset($_REQUEST['complain_created_by']) ? $db->clean($_REQUEST['complain_created_by']) : '';

	$detail['complain_assign_to']     = isset($_REQUEST['complain_assign_to']) ? $db->clean($_REQUEST['complain_assign_to']) : '';

	// $detail['complain_assign_to']   = $db->clean(implode(",", $_REQUEST['complain_assign_to']));

	$detail['complain_date']          = isset($_REQUEST['complain_date']) ? $db->clean($_REQUEST['complain_date']) : '';

	$detail['entry_flag']             = '3';

	$detail['image_path']             = isset($_REQUEST['image_path']) ? $db->clean($_REQUEST['image_path']) : '';

	$detail['old_image_path']         = isset($_REQUEST['old_image_path']) ? $db->clean($_REQUEST['old_image_path']) : '';

	$detail['product_id']             = (is_array($_REQUEST['product_id'])) ? implode(",", $_REQUEST['product_id']) : '';

	$detail['product_sub_category']   = (is_array($_REQUEST['product_sub_category'])) ? implode(",", $_REQUEST['product_sub_category']) : '';

	$detail['quantity']               = isset($_REQUEST['quantity']) ? $db->clean($_REQUEST['quantity']) : '';

	$detail['u_w_flag']               = isset($_REQUEST['u_w_flag']) ? $db->clean($_REQUEST['u_w_flag']) : '';

	$detail['u_w_remark']             = isset($_REQUEST['u_w_remark']) ? $db->clean($_REQUEST['u_w_remark']) : '';

	$detail['quotation_flag']         = isset($_REQUEST['quotation_flag']) ? $db->clean($_REQUEST['quotation_flag']) : '';

	$detail['quotation_remark']       = isset($_REQUEST['quotation_remark']) ? $db->clean($_REQUEST['quotation_remark']) : '';

	$detail['customer_requirement']   = isset($_REQUEST['customer_requirement']) ? $db->clean($_REQUEST['customer_requirement']) : '';

	$detail['company_type']           = isset($_REQUEST['company_type']) ? $db->clean($_REQUEST['company_type']) : '';

	$detail['executive_type']         = isset($_REQUEST['executive_type']) ? $db->clean($_REQUEST['executive_type']) : '';

	// print_r($detail);exit;


	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objComplain->AddComplainPanel($detail,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("manage_complain.php?msg=inserted");
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

		$reply=$objComplain->UpdateComplainPanel($detail,$_FILES);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location("manage_complain.php?msg=updated");
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
		$reply=$objComplain->GetEditDataComplain($detail);
		if($reply['ack']==1){
			//$SuccessMsg = $reply['ack_msg'];
			$result=$reply['result'];
			// echo "<pre>";
			// print_r($result);
			// echo "</pre>";

			//Product Sub Category
			extract($result);

			$productSubCatIds = $product_sub_category;
			if ($productSubCatIds != "" && isset($productSubCatIds) && !empty($productSubCatIds)) {
			    $productSubCatIdsArr = explode(",", $productSubCatIds);
			    $productSubCatIdsArr = is_array($productSubCatIdsArr) ? $productSubCatIdsArr : array();
			}

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
		$reply=$objComplain->DeleteComplain($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location("manage_complain.php?msg=inserted");
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
	$db->rp_location("manage_complain.php?msg=updated");
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
<link rel="stylesheet" href="assets/global/plugins/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" />
<link href="assets/global/plugins/bootstrap-datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
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
									<h4><b>Complain</b></h4>
									<hr/>
									<div class="row">
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label>Source of complain<code>*</code></label>
													<select class="form-control" id="complain_type" name="complain_type">
														<option value="">Select Source of complain</option>
														<?php
								                        $source_of_complain_r = $db->rp_getData("source_of_inquiry","*","isDelete=0",0);
								                        if(mysqli_num_rows($source_of_complain_r)>0)
								                        {
								                            while($source_of_complain_d = mysqli_fetch_array($source_of_complain_r))
								                            {
								                                ?>
								                                <option value="<?php echo $source_of_complain_d['id']; ?>" <?=($complain_type == $source_of_complain_d['id'])?"selected":"";?>><?php echo $source_of_complain_d['name']; ?></option>
								                                <?php
								                            }
								                        }
								                        ?>
								                        <!-- <option value="">Select Source of complain</option>
								                        <option value="1">Email</option>
								                        <option value="2">Call</option>
								                        <option value="3">Whatsapp</option> -->
								                    </select> 
													<p class="help-block"></p>
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
													<label>Complain Category<code>*</code></label>
													<select class="form-control" id="complain_cat_id" name="complain_cat_id" onchange="Getsubcategory(this.value);">
								                        <option value="">Select Complain Category</option>
								                        <?php 
								                        $complain_categoty_r = $db->rp_getData("complain_category","*","isDelete=0");
								                        if($complain_categoty_r){
								                        	while($complain_categoty_d = mysqli_fetch_assoc($complain_categoty_r))
								                        	{?>
								                        		<option value="<?=$complain_categoty_d['id']?>" <?=($complain_cat_id == $complain_categoty_d['id'])?"selected":"";?>><?=$complain_categoty_d['name']?></option>
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
													<label>Complain SubCategory<code>*</code></label>
													<select class="form-control" id="complain_subcat_id" name="complain_subcat_id">
								                        <option value="">Select Complain SubCategory</option>
								                    </select> 
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Type Of Customer<code>*</code></label>
													<select class="form-control" id="executive_type" name="executive_type" onchange="Getcustomer();">
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
										</div>

										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label>Name Type Company<code>*</code></label>
													<select onchange="Getcustomer();" class="form-control" id="company_type" name="company_type">
								                        <option value="">Select Company Type</option>
								                        <?php
								                        	$company_type_r = $db->rp_getData("company_master","*","isDelete=0");
								                        	while($company_type_d = mysqli_fetch_assoc($company_type_r)){
								                        ?>
								                        		<option <?= ($type_of_company == $company_type_d['id']) ? "selected" : ''; ?> value="<?=$company_type_d['id']?>"><?=$company_type_d['name']?></option>
								                        <?php
								                        	}
								                        ?>
								                    </select> 
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Name of Company<code>*</code></label>
													<select class="form-control" id="customer_id" name="customer_id" onchange="GetcustomerInfo(this.value);">>
								                        <option value="">Select Name of Company</option>
								                    </select> 
													<p class="help-block"></p>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label>Address<code>*</code></label>
													<textarea readonly class="form-control" name="address" id="address"><?= $address; ?></textarea>
													<p class="help-block"></p>
												</div>
											</div>															
											<div class="col-md-6">
												<div class="form-group">
													<label>Contact Person<code>*</code></label>
													<input readonly class="form-control" name="contact_person" id="contact_person" value="<?php echo $contact_person?>">
													<p class="help-block"></p>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label>State<code>*</code></label>
													<input readonly class="form-control" name="state" id="state" value="<?php echo $state?>">
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>City<code>*</code></label>
													<input readonly class="form-control" name="city" id="city" value="<?php echo $city?>">
													<p class="help-block"></p>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label>Zone</label>
													<input class="form-control" name="zone" id="zone" value="<?= $zone; ?>">
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Remark<code>*</code></label>
													<textarea class="form-control" name="remark" id="remark"><?= $remark; ?></textarea>
													<p class="help-block"></p>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label>Complain Created by<code>*</code></label>
													<select class="form-control" id="complain_created_by" name="complain_created_by">
								                        <option value="">Complain Created by</option>
								                        <?php

								                        if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
														{
															  
														  	if($rights['personal_flag']==1)
															{
																$ctable_where_sales1 .=" AND id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";
															}
															else
															{ 
																if($rights['chain_vise_flag'] == 1)
															 	{
																	$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
																	$get_sales_typer=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
																    if ($get_sales_typer== "sales_manager") 
																    {
																        $sales_executive_type = "Regional Sales Manager";
																        $key="sm_id";
																        $WhereConditionr.=' ' .$key.'='.$check_id;
																    }
																    else if ($get_sales_typer == "area_sales_manager") 
																    {
																        $sales_executive_type = "National Sales Manager";//Business Development Manager
																        $key="asm_id";
																        $WhereConditionr.=' ' .$key.'='.$check_id;
																    }
																    else if ($get_sales_typer == "sales_officer") 
																    {
																        $sales_executive_type = "Area Sales Manager";//Area Sales Manager
																        $key="so_id";
																        $WhereConditionr.=' ' .$key.'='.$check_id;
																    }
																    else if ($get_sales_typer == "sales_executive") 
																    {
																        $sales_executive_type = "Sales Officer";
																        $key="se_id";
																        $WhereConditionr.=' ' .$key.'='.$check_id;
																    }
																    else
																    {
																    	$WhereConditionr.=' type = "service_engineer"';
																    }

																    $data_r = $db->rp_getData("sales_executive","id",$WhereConditionr,"",0);

																    $SALEID2=array();
																	if($data_r)
																	{
																		while($data_dd=mysqli_fetch_assoc($data_r))
																		{
																			$SALEID2[]=$data_dd['id'];
																		}
																	}
																	if(!empty($SALEID2))
																	{
																		$SALEID2=implode(",", $SALEID2);
																		$ctable_where_sales1 .= " AND id IN (".$SALEID2.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
																	}
																	else
																	{
																		$ctable_where_sales1 .= " AND id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
																	}
																}
																else
																{
																	// $ctable_where_sales1 .= " isDelete=0 ";
																}
															}
													    }
								                        $sales_id_r = $db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1".$ctable_where_sales1);

								                        // echo $complain_created_by;exit;
								                        $complain_created_by = ($complain_created_by == "" || $complain_created_by == NULL || empty($complain_created_by))?$_SESSION[SITE_SESS.'REFERANCE_ID']:$complain_created_by;
								                        if($sales_id_r)
								                        {
								                            while($sales_id_d = mysqli_fetch_assoc($sales_id_r))
								                            {?>
								                                <option value="<?=$sales_id_d['id']?>" <?=($complain_created_by == $sales_id_d['id'])?"selected":"";?>><?=$sales_id_d['name']?></option>
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
													<label>Complain Assigned to<code>*</code></label>
													<select class="form-control" id="complain_assign_to" name="complain_assign_to">
								                        <option value="">Complain Assigned to</option>
								                        <?php
								                        if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
														{
															  
														  	if($rights['personal_flag']==1)
															{
																$ctable_where_sales .=" AND id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";
															}
															else
															{ 
																if($rights['chain_vise_flag'] == 1)
															 	{
																	$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
																	$get_sales_typer=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
																    if ($get_sales_typer== "sales_manager") 
																    {
																        $sales_executive_type = "Regional Sales Manager";
																        $key="sm_id";
																        $WhereConditionr2.=' ' .$key.'='.$check_id;
																    }
																    else if ($get_sales_typer == "area_sales_manager") 
																    {
																        $sales_executive_type = "National Sales Manager";//Business Development Manager
																        $key="asm_id";
																        $WhereConditionr2.=' ' .$key.'='.$check_id;
																    }
																    else if ($get_sales_typer == "sales_officer") 
																    {
																        $sales_executive_type = "Area Sales Manager";//Area Sales Manager
																        $key="so_id";
																        $WhereConditionr2.=' ' .$key.'='.$check_id;
																    }
																    else if ($get_sales_typer == "sales_executive") 
																    {
																        $sales_executive_type = "Sales Officer";
																        $key="se_id";
																        $WhereConditionr2.=' ' .$key.'='.$check_id;
																    }
																    else
																    {
																    	$WhereConditionr2.=' type = "service_engineer"';
																    }

																    $data_rr = $db->rp_getData("sales_executive","id",$WhereConditionr2,"",0);

																    $SALEID2=array();
																	if($data_rr)
																	{
																		while($data_dd=mysqli_fetch_assoc($data_rr))
																		{
																			$SALEID2[]=$data_dd['id'];
																		}
																	}
																	if(!empty($SALEID2))
																	{
																		$SALEID2=implode(",", $SALEID2);
																		$ctable_where_sales .= " AND id IN (".$SALEID2.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
																	}
																	else
																	{
																		$ctable_where_sales .= " AND id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
																	}
																}
																else
																{
																	// $ctable_where_sales .= " isDelete=0 ";
																}
															}
													    }

													    
													    $complain_assign_to = ($complain_assign_to == "" || $complain_assign_to == NULL || empty($complain_assign_to))?$_SESSION[SITE_SESS.'REFERANCE_ID']:$complain_assign_to;
								                        $sales_id_r = $db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1".$ctable_where_sales,"",0);
								                        if($sales_id_r)
								                        {
								                            while($sales_id_d = mysqli_fetch_assoc($sales_id_r))
								                            {?>
								                                <option value="<?=$sales_id_d['id']?>" <?=($complain_assign_to == $sales_id_d['id'])?"selected":"";?> ><?=$sales_id_d['name']?></option>
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
													<label>Date on which the complain <code>*</code></label>
													<input type="text" class="form-control datepicker" name="complain_date" id="complain_date" value="<?php if($_REQUEST['mode']=="add") { echo date('Y-m-d');}else{echo $complain_date;}?>">
													<p class="help-block"></p>
												</div>
											</div>									
											<div class="col-md-6">
												<div class="form-group">
                                               		<input data-image="<?php echo ($image_path!="" && file_exists(INQUIRY_IMAGE.$image_path))?INQUIRY_IMAGE.$image_path:"";?>" type="file" accept="image/*" name="image_path[]" id="image_path" data-old-image-dom="old_image_path"  data-old-image-path="<?php echo $image_path ?>" value="" multiple>
                                            	</div>
                                            	<input type="hidden" name="image_path_old" value="<?= $image_path ?>">
                                            	<table class="table table-bordered" id="image_grid">
                                            	    <?php 
													if($_REQUEST['mode']=="edit") 
													{

														if($image_path!="")
														{

															$img = explode(",", $image_path);
															$imagepath="";
															for ($i=0; $i < sizeof($img); $i++)
															{ 
																$imagepath="../resource/image/".$db->rp_getValue("media","url","reference_id='".$_REQUEST["id"]."' AND id='".$img[$i]."'",0);

															?>
																<tr>
																	<td><img src=<?= $imagepath;?> style="height: 80px;width: 200px;"></td>
																	<td><a class='delete btn btn-danger btn-sm'  title='Delete' onclick="DeleteImage(<?= $_REQUEST['id']; ?>,'delete_complain_image',<?=$img[$i];?>)"><i class='fa fa-times'></i></a></td>
																</tr>

															<?php
															}
														}
													}
												
				                                	?>		
												</table>	
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-actions">
							<button type="submit" name="submit" class="btn green">Submit</button>
							<button type="button" class="btn btn-default" onClick="window.location.href='manage_complain.php'">Back</button>
						</div>
					</div>
					<div class="col-md-6">
						<div class="portlet box blue">
							<div class="portlet-body form">
								<div class="form-body">
									<h4><b>Product Complain</b></h4>
									<hr/>
									<div class="row">
										<div class="col-md-12">
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Product Sub Category</label>
													<select class="form-control b-3" id="product_sub_category" name="product_sub_category[]" multiple>
								                        <option value="">Select Sub Category</option>
								                        <?php
								                        $category_master_r = $db->rp_getData("category_master","*","isDelete=0 AND isActive=1");
								                        if($category_master_r)
								                        {
								                            while($category_master_d = mysqli_fetch_assoc($category_master_r))
								                            {
							                            		?>
							                                	<option value="<?=$category_master_d['id']?>" <?= in_array($category_master_d['id'],$productSubCatIdsArr) ? "selected" : ""; ?> ><?=$category_master_d['name']?></option>
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
													<label class="test">Product Name</label>
													<select class="form-control b-3" id="product_id" name="product_id[]" multiple>
								                        <option value="">Select Product</option>
								                       
								                    </select> 
													<p class="help-block"></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="test">Customer Requirement Detail Note</label>
													<textarea class="form-control b-3" name="customer_requirement" id="customer_requirement"><?= $customer_requirement; ?></textarea>
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
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>


<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.numeric.min.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script>

<script type="text/javascript">
	var add = "<?= $_REQUEST['mode'] ?>";
	$(document).ready(function(){
		
		// alert(<?= $complain_subcat_id ?>);
		var complain_cat_id = '<?= $complain_cat_id ?>';
		var complain_subcat_id = '<?= $complain_subcat_id ?>';
   		Getsubcategory(complain_cat_id,complain_subcat_id);


   		var customer_type = '<?= $executive_type ?>';
		var company_name = '<?= $customer_id ?>';
   		Getcustomer();
   		GetcustomerInfo(company_name);
   		$("#product_sub_category").trigger("change");
	});


	/*$("#complain_assign_to").fSelect();*/

	// $('#complain_date').datepicker({ format: "dd-mm-yyyy",orientation: "auto",startDate: "",clearBtn: false,});
	$('#complain_date').datepicker({dateFormat: 'dd-mm-yy',orientation: "auto",startDate: "",clearBtn: false});
	$("#product_sub_category").fSelect();
	$("#product_id").fSelect();

</script>
<script type="text/javascript">
	$("#product_sub_category").change(function(){
		var product_sub_category = String($("#product_sub_category").val());
		var product_id = '<?= $product_id; ?>';

		$.ajax({
			type:'POST',
			url:'sub_category_wise_product_ajax.php',
			data:{
				psc_id:product_sub_category,
				product:product_id
			},
			beforeSend:function(){
				$("#loading-modal").modal('show');
			},
			success:function(result){
				$("#product_id").fSelect("destroy");
				$("#product_id").val("");
				$("#product_id").html(result);
				$("#product_id").fSelect("create");
				$("#loading-modal").modal('hide');
			}
		});

	});
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

	function Getcustomer()
	{
		var customer_type = $("#executive_type").val();
		var company_type = $("#company_type").val();
		var customer_id   = '<?= $customer_id; ?>';

		$.ajax({
        	type: "POST",
        	url: "ajax_get_customer.php",
        	data:'customer_type='+customer_type+"&companytype="+company_type+"&customer_id="+customer_id,
        	beforeSend:function(){
            },
        	success: function(data){
	            $("#customer_id").select2("destroy");
	            $("#customer_id").html(data);
	            $("#customer_id").select2();
       		}
   	 	});
	}

	function Getsubcategory(category_id,complain_subcat_id="")
	{
		// alert(category_id+"-"+complain_subcat_id);
		$.ajax({
        	type: "POST",
        	url: "ajax_get_subcategory.php",
        	data:{

        		category_id:category_id,
        		compalin_subcate_id:complain_subcat_id
        	},
        	beforeSend:function(){
            },
        	success: function(data){
	            $("#complain_subcat_id").select2("destroy");
	            $("#complain_subcat_id").html(data);
	            $("#complain_subcat_id").select2();
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
        		// $("#zone").val(data.zip);
        		if (add == 'add') {
	        		$("#zone").val(data.zip);
        		}
        	}
   	 	});
	}

	$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
  
	function check_form()
	{
		$(".form-body").children().removeClass("has-error");
		var isValid=true;	
		
		if($("#complain_type").val()=="" || $("#complain_type").val().split(" ").join("")=="")
		{
			vd=aj.error('complain_type',"Please Select Source Of Complain.","add_error");
			isValid=false;
		}

		if($("#complain_cat_id").val()=="" || $("#complain_cat_id").val().split(" ").join("")=="")
		{
			vd=aj.error('complain_cat_id',"Please Select Complain Category.","add_error");
			isValid=false;
		}

		if($("#complain_subcat_id").val()=="" || $("#complain_subcat_id").val().split(" ").join("")=="")
		{
			vd=aj.error('complain_subcat_id',"Please Select Complain SubCategory.","add_error");
			isValid=false;
		}

		if($("#executive_type").val()=="" || $("#executive_type").val().split(" ").join("")=="")
		{
			vd=aj.error('executive_type',"Please Select Type Of Customer.","add_error");
			isValid=false;
		}

		if($("#customer_id").val()=="" || $("#customer_id").val().split(" ").join("")=="")
		{
			vd=aj.error('customer_id',"Please Select Name Of Company.","add_error");
			isValid=false;
		}

		if($("#address").val()=="" || $("#address").val().split(" ").join("")=="")
		{
			vd=aj.error('address',"Please Enter Address.","add_error");
			isValid=false;
		}

		if($("#contact_person").val()=="" || $("#contact_person").val().split(" ").join("")=="")
		{
			vd=aj.error('contact_person',"Please Enter Contact Person.","add_error");
			isValid=false;
		}

		if($("#state").val()=="" || $("#state").val().split(" ").join("")=="")
		{
			vd=aj.error('state',"Please Enter State.","add_error");
			isValid=false;
		}

		if($("#city").val()=="" || $("#city").val().split(" ").join("")=="")
		{
			vd=aj.error('city',"Please Enter City.","add_error");
			isValid=false;
		}

		/*if($("#zone").val()=="" || $("#zone").val().split(" ").join("")=="")
		{
			vd=aj.error('zone',"Please Enter Zone.","add_error");
			isValid=false;
		}*/
		if($("#remark").val()=="" || $("#remark").val().split(" ").join("")=="")
		{
			vd=aj.error('remark',"Please Enter Remark.","add_error");
			isValid=false;
		}
		
		if($("#complain_created_by").val()=="" || $("#complain_created_by").val().split(" ").join("")=="")
		{
			vd=aj.error('complain_created_by',"Please Select Complain Created By.","add_error");
			isValid=false;
		}

		if($("#complain_assign_to").val()=="" || $("#complain_assign_to").val().split(" ").join("")=="")
		{
			vd=aj.error('complain_assign_to',"Please Select Complain Assign To.","add_error");
			isValid=false;
		}

		if($("#complain_date").val()=="" || $("#complain_date").val().split(" ").join("")=="")
		{
			vd=aj.error('complain_date',"Please Select Complain Date.","add_error");
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
<script type="text/javascript">
	function DeleteImage(complain_id,flag,media_id)
	{
		var r = confirm("Are you sure you want to delete?");
	 	if(r)
        {
            $.ajax({
                url:"delete_multiple_attchment_ajax.php",
                type:"POST",
                data:{
                   
                    complain_id:complain_id,                
                    flag:flag,                
                    media_id:media_id,
                },
                beforeSend: function() {
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
                success:function(result) 
                {
                	// console.log(result);
                    var result=JSON.parse(result);
                    $('.preloader').fadeOut('slow');
                    
                    if(result.ack==1)
                    {                       
                        toastr.success(result.ack_msg,"Success!!");
                        // $('#image_grid').load('ajax/complain_crud.php #image_grid');
                        location.reload();
                    }
                    else
                    {
                        toastr.error(result.ack_msg, 'Error!!');
                        // $('#image_grid').load('ajax/complain_crud.php #image_grid');
                    }

                },            
            });
        }
	}
</script>
</body>
</html>