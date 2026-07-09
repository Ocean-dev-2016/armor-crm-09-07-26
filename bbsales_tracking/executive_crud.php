<?php 
//echo $_REQUEST['mode'];exit();
if(isset($_REQUEST['flag']) && $_REQUEST['flag']=="prospect")
{
	$page_id=616;$page_slug='prospect_customer';
}
else if(isset($_REQUEST['flag']) && $_REQUEST['flag']=="channel_partner")
{
	$page_id=555;$page_slug='channel_partner_customer';
}
else
{
	$page_id=555;$page_slug='page_executive';
}
$ctable 	= "executive";
if(isset($_REQUEST['flag']) && $_REQUEST['flag']=="prospect")
{
	$ctable1 	= "Prospect Customer";	
}
else if(isset($_REQUEST['flag']) && $_REQUEST['flag']=="channel_partner")
{
	$ctable1 	= "Channel Partner";
}
else
{
	$ctable1 	= "Customer";
}

$ctable 	= "executive";
$main_page 	= $ctable;
$page 		= "add_" . $ctable;
$page_title = ucwords($_REQUEST['mode']) . "  " . 'Customer';
include("connect.php");
$page_hierarchy = array(array("link" => "", "title" => "Sales & Marketing"), array("link" => $ctable . "_manage.php?flag=".$_REQUEST['flag'], "title" => "Manage " . $ctable1), array("link" => $ctable . "_crud.php?flag=".$_REQUEST['flag'], "title" => "Add/Edit " . $ctable1));
require_once("../include/class.executive.php");
$objClass = new Executive();
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "add";
$flag = isset($_REQUEST['flag']) ? $_REQUEST['flag'] : "";
$type_of_inquiry = isset($_REQUEST['type']) ? $_REQUEST['type'] : "super_stockist";
//variable Declaration..........//
$error                 = 0;
$disabled              = false;
$super_stockist_id     = "";
$dealer_distributor_id = "";
$company_name          = "";
$unit_name             = "";
$company_type          = "";
$industry_type          = "";
$cname                 = "";
$cpname                = "";
$email                 = "";
$email_cc              = "";
$cst                   = "";
$excise                = "";
$pan                   = "";
$gst                   = "";
$vat                   = "";
$phone                 = "";
$address               = "";
$address2               = "";
$zip                   = "";
$country               = "";
$state                 = "";
$city                  = "";
$zone                  = "";
$image_path            = "";
$vendor_desk           = "";
$office_supplier       = "";
$gst_detail            = "";
$other_image           = "";
$mobile_no1            = "";
$class_id              = "";
$area_id               = "";
$shipping_address      = "";
$billing_address       = "";
$channel_partner_flag  = 0;

if ($_SESSION['detail'] != "") {
	$detail = array();
	$detail = $_SESSION['detail'];
	extract($detail);
	unset($_SESSION['detail']);
}
//$unique="S/".FINANCIAL_YEAR."/".(intval($db->rp_getValue($ctable,"max(`id`)","1=1"))+1);
if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "edit") {
	// var_dump($_REQUEST);exit;
	// echo '<pre>';
	// print_r($_REQUEST);
	// echo '</pre>';
	// exit;
	if ($rights['update_flag'] != 1) {
		$db->rp_location('access_denied.php?msg=update_access_denied');
	}
    $where      = " id='" . $_REQUEST['id'] . "' AND isDelete=0";
    $detail['id']       = $_REQUEST['id'];
    
    $reply      = $objClass->EditExecutive($detail);
	if ($reply['ack'] == 1) {

		$result = $reply['result'];
		$areas = $reply['area_id'];
		extract($result);
		/*print_r($result);exit;*/
 
        $page_title = ucwords($_REQUEST['mode']) . '&nbsp' . "Customer" . "- " . ucwords(htmlentities($cname)) . '&nbsp'; 
	} else {
		$db->addErrorMessage($reply['ack_msg']);
	}
}
else
{
	$price_list_id =1;			
}
if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "delete") {
	if ($rights['delete_flag'] != 1) {
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id'] = $_REQUEST['id'];
	$reply = $objClass->ExecutiveDelete($detail);
	if ($reply['ack'] == 1) {
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable . "_manage.php?msg=inserted&flag=".$_REQUEST['flag']);
	} else {
		$db->addErrorMessage($reply['ack_msg']);
	}
}

if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "change_to_customer") {
	
	$detail['id'] = $_REQUEST['id'];
	$reply = $objClass->changetocustomer($detail);
	if ($reply['ack'] == 1) {
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable . "_manage.php?msg=inserted&flag=".$_REQUEST['flag']);
	} else {
		$db->addErrorMessage($reply['ack_msg']);
	}
}
if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "isActive" && isset($_REQUEST['status'])  && $_REQUEST['status'] != "") {
	$status = $_REQUEST['status'];
	$rows 	= array(
		"isActive"	=> $status
	);
	$where	= "id='" . $_REQUEST['id'] . "'";
	$db->rp_update($ctable, $rows, $where);
	if($_REQUEST['flag']=="prospect")
	{
		$db->rp_location("executive_manage.php?flag=prospect");
	}
	else
	{
		$db->rp_location("executive_manage.php");
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
	<meta charset="utf-8" />
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>

	<?php include("include_css.php"); ?>
	<link href="assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
</head>

<body class="page-md">
	<?php include("header.php"); ?>
	<div class="page-container">
		<div class="page-head bg-grey">
			<div class="container">
				<div class="page-title">				
					<h1><a href="<?php echo  $ctable; ?>_manage.php?flag=<?= $_REQUEST['flag']; ?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy); ?> </h1>

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
				<div class="row">
					<div class="col-md-12">
						<div class="portlet box blue">
							<div class="portlet-body form">
								<div class="row">
									<div class="col-sm-12">
										<div class="tabbable-custom nav-justified">
											<ul class="nav nav-tabs ">
												<li class="active">
													<a href="#tab_super_stockist_info" data-toggle="tab" aria-expanded="false"> Basic Information </a>
												</li>
												<!--li>
												<a href="#tab_branch_info" data-toggle="tab" aria-expanded="false"> Unit Information </a>
											</li>
											<li>
												<a href="#tab_contact_info" data-toggle="tab" aria-expanded="false"> Unit Contact Information </a>
											</li-->
											</ul>
											<div class="tab-content">
												<!--TAB 1 START BASIC INFO------------>
												<div class="tab-pane active" id="tab_super_stockist_info">
													<br />
													<?php
													if ($error != 1) {
														if ($type_of_inquiry == '1') {
															include('form/super_stockist_form.php');
														} else if ($type_of_inquiry == '2') {
															include('form/dealer_distributor_form.php');
														} else if ($type_of_inquiry == '3') {
															include('form/outlets_form.php');
														} else if ($type_of_inquiry == '4') {
															include('form/project_form.php');
														} else if ($type_of_inquiry == '5') {
															include('form/oem_form.php');
														} else if($type_of_inquiry == '6') {
															// echo "string"; exit;
														include('form/c2c_customer_form.php');
														} 
														else if($type_of_inquiry == '7') {
															include('form/promotional_customer_form.php');
														}
														else if($type_of_inquiry == '8') {
															include('form/merchant_exports_form.php');
														}
														else if($type_of_inquiry == '9') {
															include('form/corporate_customer_form.php');
														}
														else if($type_of_inquiry == '10') {
															include('form/builder_form.php');
														}
														else if($type_of_inquiry == '11') {
															include('form/brand_approval_visit_form.php');
														}
														else {
															$error = 1;
															$error_msg = "Something went wrong with page!! Try Again :("
													?>
															<h1 class="text-center">
																<?php echo $error_msg; ?>
																<br>
																<br>
																<a class="btn btn-lg btn-primary">
																	<i class="fa fa-refresh"></i>&nbsp; Try Again!!
																</a>
															</h1>

														<?php
														}
													} else {
														?>
														<h1 class="text-center">
															<?php echo $error_msg; ?>
															<br>
															<br>
															<a class="btn btn-lg btn-primary">
																<i class="fa fa-refresh"></i>&nbsp; Try Again!!
															</a>
														</h1>

													<?php
													}

													?>
												</div>
												<!-- TAB 1 OVER-------------------------->
												<!-- TAB 2 UNIT INFO START-------------------------->

												<div class="tab-pane" id="tab_branch_info">
													<br>
													<div class="row">
														<div class="col-md-12 col-sm-12">
															<div class="portlet grey-cascade box">
																<div class="portlet-title">
																	<div class="caption">
																		<i class="fa fa-sitemap"></i>&nbsp;Branches
																	</div>

																</div>
																<div class="portlet-body">
																	<div class="form-body">
																		<div class="row">
																			<div class="col-md-3">
																				<div class="form-group">
																					<input id="branch_name" name="branch_name" type="text" class="form-control" placeholder="Branch name." />
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
												<!-- TAB 2 OVER-------------------------->
												<!-- TAB 3 UNIT CONTACT INFO START-------------------------->
												<div class="tab-pane" id="tab_contact_info">
													<br>
													<div class="row">
														<div class="col-md-12 col-sm-12">
															<div class="portlet grey-cascade box">
																<div class="portlet-title">
																	<div class="caption">
																		<i class="fa fa-user-plus"></i>&nbsp; Contact Persons
																	</div>

																</div>
																<div class="portlet-body">
																	<div class="form-body">
																		<div class="row">
																			<div class="col-md-3 col-sm-12 col-xs-12">
																				<div class="col-md-12">
																					<div class="form-group">
																						<input id="contact_name" name="contact_name" type="text" class="form-control" placeholder="Name." />
																						<p class="help-block"></p>
																					</div>
																				</div>
																				<div class="col-md-12">
																					<div class="form-group">
																						<input id="contact_designation" name="contact_designation" type="text" class="form-control" placeholder="Designation" />
																						<p class="help-block"></p>
																					</div>
																				</div>
																				<div class="col-md-12">
																					<div class="form-group">

																						<select id="contact_branch" name="contact_branch" class="form-control">
																							<?php
																							$cid = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
																							var_dump($_REQUEST);
																							if ($cid != "") {
																								$branches = $db->getExecutiveBranches($cid, 0);
																							?>

																								<option value="">--Select Branch --</option>
																								<?php
																								//print_r($branches);
																								if (!empty($branches)) {
																									echo "hi";

																								?>

																									<?php
																									foreach ($branches as $b) {
																									?>
																										<option <?php echo ($contact_branch == $b['id']) ? "selected" : ""; ?>value="<?php echo contact_branch; ?>"><?php echo $b['branch_name']; ?></option>
																									<?php
																									}
																								} else {
																									?>
																									<option value="">--Select Branch --</option>
																								<?php
																								}
																							} else {
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
																						<input id="contact_phone" name="contact_phone" type="text" class="form-control" placeholder="Phone" />
																						<p class="help-block"></p>
																					</div>
																				</div>
																				<div class="col-md-12">
																					<div class="form-group">
																						<input id="contact_email" name="contact_email" type="text" class="form-control" placeholder="Email" />
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
													<!-- TAB 3 OVER-------------------------->
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
	<script src="assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
	<script src="assets/global/plugins/jquery.quicksearch.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
	<script type="text/javascript" src="js/fSelect.js"></script>
	<script type="text/javascript">
	// $(".category_id").fSelect();
	$(".top_category_id").fSelect();
</script> 
	<script type="text/javascript">
		$("#phone").numeric();
		$("#contact_phone").numeric();
		// $("#discount").numeric();
		$("#zip").numeric();
		$("#mobile_no1").numeric();
		$(".multiple-phone-number").numeric();
		/*xyz();
function xyz(data){
	if(data=="")
	{
		$('#area_id').multiSelect();		
	}
	else
	{
		$('#area_id').html(data);
		$("#area_id").multiSelect("refresh");	
		
		
	}
	 
}*/
		// $('#area_id').multiSelect();
		// $('#select-all').click(function() {
		// 	$('#area_id').multiSelect('select_all');
		// 	return false;
		// });
		// $('#deselect-all').click(function() {
		// 	$('#area_id').multiSelect('deselect_all');
		// 	return false;
		// });
		// $('#area_id').multiSelect({
		// 	selectableHeader: "<input type='text' class='search-input form-control' autocomplete='off' placeholder='Search Area'>",
		// 	selectionHeader: "<input type='text' class='search-input form-control' autocomplete='off' placeholder='Search Area'>",
		// 	afterInit: function(ms) {

		// 		var that = this,
		// 			$selectableSearch = that.$selectableUl.prev(),
		// 			$selectionSearch = that.$selectionUl.prev(),
		// 			selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
		// 			selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

		// 		that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
		// 			.on('keydown', function(e) {
		// 				if (e.which === 40) {
		// 					that.$selectableUl.focus();
		// 					return false;
		// 				}
		// 			});

		// 		that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
		// 			.on('keydown', function(e) {
		// 				if (e.which == 40) {
		// 					that.$selectionUl.focus();
		// 					return false;
		// 				}
		// 			});
		// 	},
		// 	afterSelect: function() {
		// 		this.qs1.cache();
		// 		this.qs2.cache();
		// 	},
		// 	afterDeselect: function() {
		// 		this.qs1.cache();
		// 		this.qs2.cache();
		// 	}
		// });
		//for get class	
		/*	
		function getDealerDistributor(val)
		{
				var super_stockist_id=$("#super_stockist_id").val();
		        $.ajax({
		        type: "POST",
		        url: "find_class.php",
		        data:
				{
					class_id:class_id,
					super_stockist_id:super_stockist_id,
					dealer_distributor_id:dealer_distributor_id,
				},
		        success: function(data){
		        $("#class_id").html(data);
				getArea($("#class_id").val());
				
			   }
		     
		    });
		}*/
		function getDealerDistributor(val) {
			var super_stockist_id = val;
			var dealer_distributor_id = "<?= $dealer_distributor_id; ?>";
			$.ajax({
				type: "POST",
				url: "find_dealer_distributor.php",
				data: 'super_stockist_id=' + super_stockist_id + '&dealer_distributor_id=' + dealer_distributor_id,
				success: function(data) {
					// $("#dealer_distributor_id").select2("val", "");
					$('#class_id').select2("val", "");
					//displayRecords(100,1);
					$('#dealer_distributor_id').select2("destroy");
					$("#dealer_distributor_id").html(data);
					$('#dealer_distributor_id').select2();
				}
			});

			
		}


		// function getClass(val) {
		// 	var super_stockist_id = $("#super_stockist_id").val();
		// 	var dealer_distributor_id = $("#dealer_distributor_id").val();
		// 	var class_id = $("#class_id").val();
		// 	$.ajax({
		// 		type: "POST",
		// 		url: "find_class.php",
		// 		data: {
		// 			class_id: class_id,
		// 			super_stockist_id: super_stockist_id,
		// 			dealer_distributor_id: dealer_distributor_id,
		// 		},
		// 		success: function(data) {
		// 			$("#class_id").html(data);
		// 			getArea($("#class_id").val());

		// 		}

		// 	});
		// 	$.ajax({
		// 		type: "POST",
		// 		url: "get_price_list.php",
		// 		data: {
		// 			super_stockist_id: super_stockist_id,
		// 		},
		// 		success: function(data) {
		// 			$('#price_list_id').select2("val", data);
		// 		}
		// 	});
		// }


		// function getArea(val) {
		// 	var super_stockist_id = $("#super_stockist_id").val();
		// 	var dealer_distributor_id = $("#dealer_distributor_id").val();
		// 	var class_id = $("#class_id").val();
		// 	$.ajax({
		// 		type: "POST",
		// 		url: "find_area.php",
		// 		data: {
		// 			class_id: class_id,
		// 			super_stockist_id: super_stockist_id,
		// 			dealer_distributor_id: dealer_distributor_id,
		// 		},
		// 		success: function(data) {
		// 			//xyz(data)
		// 			$('#area_id').html(data);
		// 			$("#area_id").multiSelect("refresh");
		// 		}
		// 	});
		// }
		$(document).ready(function() {
			$(".form-control").bind("keyup change", function() {
				if ($(this).parent().hasClass("has-error")) {
					$(this).parent().find('.help-block').html("");
					$(this).parent().removeClass("has-error");
				}
			});
			$(".form-control").bind("keyup change", function() {
				if ($(this).parent().hasClass("has-error")) {
					$(this).parent().find('.help-block').html("");
					$(this).parent().removeClassMobile("has-error");
				}
			});
			var super_stockist_id = "<?= $super_stockist_id; ?>";
			getDealerDistributor(super_stockist_id);

		});

		function encodeHtmlEntities(str) {
		  return str.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
		    return '&#'+i.charCodeAt(0)+';';
		  });
		}

		function check_form() {
			// debugger;
			$(".form-body").children().removeClass("has-error");
			// var top_category_id_check = encodeHtmlEntities($("#top_category_idddd").val());
			var isValid = true;
			if ($("#cname").val() == "" || $("#cname").val().split(" ").join("") == "") {

				vd = aj.error('cname', "Please enter name.", "add_error");
				isValid = false;
			}
			if ($("#gst").val() == "" || $("#gst").val().split(" ").join("") == "") {

				vd = aj.error('gst', "Please enter gst", "add_error");
				isValid = false;
			}
			// alert(cname);
			if ($("#address").val() == "" || $("#address").val().split(" ").join("") == "") {

				vd = aj.error('address', "Please enter address.", "add_error");
				isValid = false;
			}
			if ($("#zip").val() == "" || $("#zip").val().split(" ").join("") == "") {
				// debugger;
				vd = aj.error('zip', "Please enter Pincode.", "add_error");
				isValid = false;
			}
			
			// if ($(".pincode-chek").val() != "" || $(".pincode-chek").val().split(" ").join("") != "") {
			// 	debugger;
			// 	// if ($(".pincode-chek").val().length != 6) {
			// 	// 	aj.error('pincode-chek', 'Please enter valid pincode no!!', 'add_error');
			// 	// 	isValid = false;
			// 	// }
			// 	vd = aj.error('pincode-chek', "Please enter Pincode.", "add_error");
			// 	isValid = false;
			// }
			// debugger;
			// if ($("#password").val() == "" || $("#password").val().split(" ").join("") == "") {

			// 	vd = aj.error('password', "Please enter password.", "add_error");
			// 	isValid = false;
			// }
			
			if ($("#company_name").val() == "" || $("#company_name").val().split(" ").join("") == "") {

				vd = aj.error('company_name', "Please Enter Firm Name.", "add_error");
				isValid = false;
			}  
			// if ($("#mobile_no1").val() == "" || $("#mobile_no1").val().split(" ").join("") == "") {

			// 	vd = aj.error('mobile_no1', "Please enter Mobile No.", "add_error");
			// 	isValid = false;
			// }

			if ($("#country").val() == "" || $("#country").val().split(" ").join("") == "") {

				vd = aj.error('country', "Please enter Country name.", "add_error");
				isValid = false;
			}
			if ($("#type_of_company").val() == "" || $("#type_of_company").val().split(" ").join("") == "") {

				vd = aj.error('type_of_company', "Please Select Company name.", "add_error");
				isValid = false;
			}
			 
			
			if ($("#state").val() == "" || $("#state").val().split(" ").join("") == "") {

				vd = aj.error('state', "Please enter State name.", "add_error");
				isValid = false;
			}
			// if ($("#city").val() == "" || $("#city").val().split(" ").join("") == "") {

			// 	vd = aj.error('city', "Please enter route name.", "add_error");
			// 	isValid = false;
			// }
			if ($("#main_city").val() == "" || $("#main_city").val().split(" ").join("") == "") {

				vd = aj.error('main_city', "Please enter City name.", "add_error");
				isValid = false;
			}
			//alert(isValid);
			/*if ($("#zone").val() == "" || $("#zone").val().split(" ").join("") == "") {

				vd = aj.error('zone', "Please enter Zone name.", "add_error");
				isValid = false;
			}*/
			if ($("#client_code").val() == "" || $("#client_code").val().split(" ").join("") == "") {
				vd = aj.error('client_code', "Please enter Client Code.", "add_error");
				isValid = false;
			}
			// var alphanumers = /^[a-zA-Z0-9-_,]+(\s{0,1}[a-zA-Z0-9-_,])*$/;
			// if (!alphanumers.test($("#client_code").val())) {
			// 	aj.error('client_code', 'Client Code can have only Alphabets , Numbers And "-","_" !!', 'add_error');
			// 	isValid = false;

		 //    noSpace: true;
		  
			// } 
			<?php

			if ($_REQUEST['type'] == 2) {
			?>
			if ($("#super_stockist_id").val() == null || $("#super_stockist_id").val() == "" || $("#super_stockist_id").val().split(" ").join("") == "") {

				vd = aj.error('super_stockist_id', "Please Select Super Stockist.", "add_error");
				isValid = false;
			}
			<?php
			}
			?>
			<?php
			if ($_REQUEST['type'] == 3) {
			?>
			if ($("#super_stockist").val() == null || $("#super_stockist").val() == "" || $("#super_stockist").val().split(" ").join("") == "") {

				vd = aj.error('super_stockist', "Please Select Super Stockist.", "add_error");
				isValid = false;
			}
			if ($("#dealer_distributor_id").val() == null || $("#dealer_distributor_id").val() == "" || $("#dealer_distributor_id").val().split(" ").join("") == "") {

				vd = aj.error('dealer_distributor_id', "Please Select Delear Distributor.", "add_error");
				isValid = false;
			}
			<?php
			}
			?>
			if ($("#mobile_no1").val() == "" || $("#mobile_no1").val().split(" ").join("") != "") {

				if ($("#mobile_no1").val().length < 10 || $("#mobile_no1").val().length > 15) 
				{
					aj.error('mobile_no1', 'Please enter valid Mobile Number!!', 'add_error');
					isValid = false;
				}
			}
			$('.phone_valid').each(function() {
				phone_valid_r = parseFloat($(this).val());
				if(phone_valid_r.length < 10 || phone_valid_r.length > 15)
				{

					aj.error('phone', 'Please enter 15 digit phone Number!!', 'add_error');
					isValid = false;
				}
			});


			// if ($(".phone_data").val()!="") {


			// 	if ($("#phone").val().length < 10 || $("#phone_data").val().length > 15) 
			// 	{

			// 		aj.error('phone', 'Please enter valid phone Number!!', 'add_error');
			// 		isValid = false;
			// 	}
			// }
			


			
			// if ($("#password").val() == "" || $("#password").val().split(" ").join("") == "") {

			// 	vd = aj.error('password', "Please enter password", "add_error");
			// 	isValid = false;
			// }

			/*if ($("#email").val() == "" || $("#email").val().split(" ").join("") == "") {

				vd = aj.error('email', "Please enter Email", "add_error");
				isValid = false;
			}

			if ($("#email").val() != "") {
				if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($("#email").val())) {

				} else {
					//alert("email");
					aj.error('email', 'Please enter valid email!!', 'add_error');
					isValid = false;
				}
			}*/

			if ($("#mobile_no1").val() != "") {
				if ($("#mobile_no1").val().length != 10) {
					aj.error('mobile_no1', 'Please enter valid mobile no!!', 'add_error');
					isValid = false;
				}
			}
			if($("#top_category_id").val()==null)
			{
				isValid=false;
				toastr.error("Please select atleast one category!!");
			}
			// if ($("#top_category_id").val() == null || $("#top_category_id").val()=="" || $("#top_category_id").val().split(" ").join("")=="") { 
			// 	// alert($("#top_category_id").val());
			// 	vd = aj.error('top_category_id', "Please select category.", "add_error");
			// 	toastr.error("Please select atleast one category!!");
			// 	isValid = false;
			// }


			
			if (isValid) {
				return true;
			} else {
				//alert(isValid);
				return false;
			}
		}
		//enter Only Number not('-' OR '.')//
		$(document).ready(function() {
			$("#phone").keyup(function(event) {
				if (event.keyCode == 46 || event.keyCode == 8) {
					// let it happen, don't do anything
				} else if (/\D/g.test(this.value)) {
					alert("sorry!! Only Digits Allowed");
					this.value = this.value.replace(/\D/g, '');
				}
			});

			$("#mobile_no1").keyup(function(event) {
				if (event.keyCode == 46 || event.keyCode == 8) {
					// let it happen, don't do anything
				} else if (/\D/g.test(this.value)) {
					alert("sorry!! Only Digits Allowed");
					this.value = this.value.replace(/\D/g, '');
				}
			});
			$("#client_code").keyup(function(event) {
				if (event.keyCode != 32) {
					// let it happen, don't do anything
				} 
				else if (/\D/g.test(this.value)) 
				{
					alert("Sorry!! No space Allowed In Client Code ");
					this.value = this.value.replace(/\D/g, '');
				}
			});

		});
		//------------------------------------------//
		var searchName = "";
		var data_url = "executive_branch_data_get_ajax.php";
		var data_cotact_person_url = "executive_contact_get_ajax.php";
		var data_super_stockist_branch_url = "super_stockist_branch_get_ajax.php";
		<?php
		if (isset($_REQUEST['mode'])  && $_REQUEST['mode'] == 'edit' && isset($_REQUEST['id']) && $_REQUEST['id'] != '') {
			echo "var cid=" . $_REQUEST['id'] . ";";
		} else {
			echo "var cid=0;";
		}
		?>
		$(document).ready(function() {
			displayRecords(100, 1);
			displayContactRecords(100, 1);
			$("#searchName").keyup(function(event) {
				if (event.keyCode == 13) {
					$("#searchByName").click();
				}
			});

			<?php
				if ($_REQUEST['mode'] == 'edit') {
					?>
					getTransportname('<?= $transport_by_id ?>','<?= $transporter_id; ?>');

					<?php
				}
			?>
		});
		$('#add-branch').on('click', function() {
			var isSuperStockistInformationAvailable = check_form();
			if (!isSuperStockistInformationAvailable || cid == "") {
				alert('Please Save Super Stockist Information First');
			} else {
				if (checkBranchInfo()) {
					var branch_name = $('#branch_name').val();
					$.ajax({
						url: "executive_branch_ajax_function.php",
						type: "POST",
						data: {
							mode: 'add_branch',
							branch_name: branch_name,
							cid: cid,

						},
						success: function(json, textStatus, jqXHR) {
							json = $.parseJSON(json);
							msg = json.ack_msg;
							if (json.ack == 1) {

								toastr.success(msg, "Success!!");
								$('#branch_name').val("");
								displayRecords(10);

							} else {
								toastr.error(msg, 'Error!!')
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							toastr.error('Sorry, Server Error!!.', 'Error!!')
						}

					})
				}


			}
		});
		$('#add-contact-person').on('click', function() {
			var isSuperStockistInformationAvailable = check_form();
			if (!isSuperStockistInformationAvailable || cid == "") {
				alert('Please Save Super Stockist Information First');
			} else {

				if (checkContactInfo()) {
					var contact_name = $('#contact_name').val();
					var contact_branch = $('#contact_branch').val();
					var contact_designation = $('#contact_designation').val();
					var contact_phone = $('#contact_phone').val();
					var contact_email = $('#contact_email').val();
					var mode = $(this).attr('data-mode');
					var cpid = $(this).attr('data-id');
					$.ajax({
						url: "executive_contact_ajax_function.php",
						type: "POST",
						data: {
							mode: mode,
							contact_branch: contact_branch,
							contact_name: contact_name,
							contact_designation: contact_designation,
							contact_phone: contact_phone,
							contact_email: contact_email,
							cid: cid,
							cpid: cpid

						},
						success: function(json, textStatus, jqXHR) {
							//print_r(json);
							json = $.parseJSON(json);
							msg = json.ack_msg;
							if (json.ack == 1) {

								toastr.success(msg, "Success!!");
								$('#contact_name').val("");
								$('#contact_branch').select2("val", "");
								$('#contact_designation').val("");
								$('#contact_phone').val("");
								$('#contact_email').val("");
								$('#add-contact-person').attr('data-mode', 'add_contact');
								$('#add-contact-person').html('<i class="fa fa-user-plus"></i> &nbsp; Add Contact');
								displayContactRecords(10);

							} else {
								toastr.error(msg, 'Error!!')
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							toastr.error('Sorry, Server Error!!.', 'Error!!')
						}

					})
				}


			}
		});

		$(".form-control").bind("keyup change", function() {
			if ($(this).parent().hasClass("has-error")) {
				$(this).parent().removeClass("has-error");
				$(this).parent().find('p.help-block').html("");
			}
		});

		function checkBranchInfo() {
			var isValid = true;

			if ($("#branch_name").val() == "" || $("#branch_name").val().split(" ").join("") == "") {
				aj.error('branch_name', 'Please enter branch name!!', 'add_error');
				isValid = false;
			} else {
				aj.error('branch_name', '', 'remove_error');
			}
			if (isValid) {
				return true;
			} else {
				return false;
			}

		}

		function checkContactInfo() {
			var isValid = true;
			if ($("#contact_name").val() == "" || $("#contact_name").val().split(" ").join("") == "") {
				aj.error('contact_name', 'Please enter contact name!!', 'add_error');
				isValid = false;
			} else {
				aj.error('contact_name', '', 'remove_error');
			}
			if ($("#contact_designation").val() == "" || $("#contact_designation").val().split(" ").join("") == "") {
				aj.error('contact_designation', 'Please enter designation!!', 'add_error');
				isValid = false;
			} else {
				aj.error('contact_designation', '', 'remove_error');
			}

			if ($("#contact_phone").val() == "" || $("#contact_phone").val().split(" ").join("") != "") {
				if ($("#contact_phone").val().length != 10) {
					aj.error('contact_phone', 'Please enter valid contact number!!', 'add_error');
					isValid = false;
				}
			} else {
				aj.error('contact_phone', '', 'remove_error');
			}
			if ($("#contact_branch").val() == "" || $("#contact_branch").val().split(" ").join("") == "") {
				aj.error('contact_branch', 'Please Select contact branch!!', 'add_error');
				isValid = false;
			} else {
				aj.error('contact_branch', '', 'remove_error');
			}
			if ($("#contact_email").val() == "" || $("#contact_email").val().split(" ").join("") == "") {
				aj.error('contact_email', 'Please enter Contact Email !!', 'add_error');
				isValid = false;
			} else {
				if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($("#contact_email").val())) {

				} else {
					aj.error("contact_email", "Please enter valid contact email.", "add_error");
					isValid = false;
				}
			}
			if (isValid) {
				return true;
			} else {
				return false;
			}

		}

		function del_conf(id) {
			var r = confirm("Are you sure you want to delete?");
			if (r) {
				var isSuperStockistInformationAvailable = check_form();
				if (!isSuperStockistInformationAvailable && id == "" && cid == "") {
					toastr.error('Please save super stockist inforamtion first.')
				} else {

					$.ajax({
						url: "executive_branch_ajax_function.php",
						type: "POST",
						data: {
							mode: 'delete_branch',
							cid: cid,
							cbid: id,

						},
						success: function(json, textStatus, jqXHR) {
							json = $.parseJSON(json);
							msg = json.ack_msg;
							if (json.ack == 1) {
								toastr.success(msg, "Success!!");
								displayRecords();

							} else {
								toastr.error(msg, 'Error!!')
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							toastr.error('Sorry, Server Error!!.', 'Error!!')
						}

					})
				}
			}

		}

		function del_conf_contact(id) {
			var r = confirm("Are you sure you want to delete?");
			if (r) {
				var isSuperStockistInformationAvailable = check_form();
				if (!isSuperStockistInformationAvailable && id == "" && cid == "") {
					toastr.error('Please save super stockist inforamtion first.')
				} else {

					$.ajax({
						url: "executive_contact_ajax_function.php",
						type: "POST",
						data: {
							mode: 'delete_contact',
							cpid: id,
							cid: cid,

						},
						success: function(json, textStatus, jqXHR) {
							json = $.parseJSON(json);
							msg = json.ack_msg;
							if (json.ack == 1) {
								toastr.success(msg, "Success!!");
								displayContactRecords();

							} else {
								toastr.error(msg, 'Error!!')
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							toastr.error('Sorry, Server Error!!.', 'Error!!')
						}

					})
				}
			}

		}

		function editContact(edit_id) {
			$.ajax({
				type: "POST",
				url: "executive_contact_ajax_function.php",
				data: {
					cid: cid,
					cpid: edit_id,
					mode: "get_contact",
				},
				cache: false,
				beforeSend: function() {

				},
				success: function(json) {
					json = $.parseJSON(json);
					msg = json.ack_msg;
					if (json.ack == 1) {
						toastr.success(msg, "Success!!");
						detail = json.result;
						$('#contact_branch').select2("val", detail.branch);
						$('#contact_name').val(detail.name);
						$('#contact_designation').val(detail.designation);
						$('#contact_email').val(detail.email);
						$('#contact_phone').val(detail.phone);
						branches = detail.branches;

						container = $('#contact_branch');
						container.html("");
						container.append('<option value="">-- Select Branch --</option>');
						$.each(branches, function(index, value) {
							container.append(aj.createFormElement('spinner', value.id, 'contact_branch_class', 'contact_branch_class', '', value.branch_name, '', value.selected));
						});
						$('#add-contact-person').attr('data-mode', 'edit_contact');
						$('#add-contact-person').attr('data-id', edit_id);
						$('#add-contact-person').html('<i class="fa fa-refresh"></i> &nbsp; Update Contact');

					} else {
						toastr.error(msg, 'Error!!')
					}
				}
			});

		}

		function updateBranchInfo() {

			if (cid != "") {
				$("#contact_branch").attr('disabled', 'disabled');
				$("#contact_branch").load(data_super_stockist_branch_url + "?cid=" + cid, function() {
					$("#contact_branch").removeAttr('disabled');
				});
			} else {
				$("#contact_branch").html("");
				$("#contact_branch").attr('disabled', 'disabled');
			}
		}

		function changeDisplayRowCount(numRecords) {
			displayRecords(numRecords, 1);
		}

		function changeDisplayRowCountContact(numRecords) {
			displayContactRecords(numRecords, 1);
		}

		function displayRecords(numRecords) {
			var searchName = ($("#searchName").val() == undefined) ? "" : $("#searchName").val();
			searchName = encodeURIComponent(searchName.trim());
			$("#results").html("");
			$("#results").load(data_url + "?cid=" + cid + "&show=" + numRecords + "&searchName=" + searchName, function() {
				loadDataTable();
			});
			$("#results").on("click", ".paging_simple_numbers a", function(e) {
				e.preventDefault();
				var numRecords = $("#numRecords").val();
				$(".loading-div").show(); //show loading element
				var page = $(this).attr("data-page"); //get page number from link
				$("#results").load(data_url + "?cid=" + cid + "&show=" + numRecords + "&searchName=" + searchName, {
					"page": page
				}, function() { //get content from PHP page
					$(".loading-div").hide(); //once done, hide loading element
					loadDataTable();
				});

			});
			$("#results").on("change", "#numRecords", function(e) {
				e.preventDefault();
				var numRecords = $("#numRecords").val();
				$(".loading-div").show(); //show loading element
				var page = $(this).attr("data-page"); //get page number from link
				$("#results").load(data_url + "?jid=" + jid + "&show=" + numRecords + "&searchName=" + searchName, {
					"page": page
				}, function() { //get content from PHP page
					$(".loading-div").hide(); //once done, hide loading element
					loadDataTable();
				});

			});

		}

		function displayContactRecords(numRecords) {
			var searchName = ($("#searchContactName").val() == undefined) ? "" : $("#searchContactName").val();
			searchName = encodeURIComponent(searchName.trim());
			$("#results2").html("");
			$("#results2").load(data_cotact_person_url + "?cid=" + cid + "&show=" + numRecords + "&searchName=" + searchName, function() {
				loadContactDataTable();
			});
			$("#results2").on("click", ".paging_simple_numbers a", function(e) {
				e.preventDefault();
				var numRecords = $("#numRecords2").val();
				$(".loading-div2").show(); //show loading element
				var page = $(this).attr("data-page"); //get page number from link
				$("#results2").load(data_cotact_person_url + "?cid=" + cid + "&show=" + numRecords + "&searchName=" + searchName, {
					"page": page
				}, function() { //get content from PHP page
					$(".loading-div2").hide(); //once done, hide loading element
					loadContactDataTable();
				});

			});
			$("#results2").on("change", "#numRecords2", function(e) {
				e.preventDefault();
				var numRecords = $("#numRecords2").val();
				$(".loading-div").show(); //show loading element
				var page = $(this).attr("data-page"); //get page number from link
				$("#results2").load(data_cotact_person_url + "?jid=" + jid + "&show=" + numRecords + "&searchName=" + searchName, {
					"page": page
				}, function() { //get content from PHP page
					$(".loading-div2").hide(); //once done, hide loading element
					loadContactDataTable();
				});

			});
		}

		function loadDataTable() {
			$('#datatable_1').dataTable({
				"bPaginate": false,
				"bFilter": false,
				"bInfo": false,
				"bAutoWidth": false,
				"aoColumns": [{
						"sWidth": "1%"
					},
					{
						"sWidth": "15%"
					},
					{
						"sWidth": "10%"
					},
					{
						"sWidth": "10%",
						"bSortable": false
					}
				],
				"oLanguage": {
					"sEmptyTable": "<i class='fa fa- fa-sitemap '></i> &nbsp; No Branch Found"
				},
			});
			updateBranchInfo();
		}

		function loadContactDataTable() {
			$('#datatable_2').dataTable({
				"bPaginate": false,
				"bFilter": false,
				"bInfo": false,
				"bAutoWidth": false,
				"aoColumns": [{
						"sWidth": "0.4%"
					},
					{
						"sWidth": "10%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "8%"
					},
					{
						"sWidth": "10%",
						"bSortable": false
					}
				],
				"oLanguage": {
					"sEmptyTable": "<i class='fa fa- fa-user-plus '></i> &nbsp; No Contact Found"
				},
			});
		}

		function searchByName() {
			searchName = $("#searchName").val();
			displayRecords(100, 1);
			return false;
		}

		function clearSearchByName() {
			searchName = "";
			$("#searchName").val("");
			displayRecords(100, 1);
		}

		function searchByContactName() {
			searchName = $("#searchContactName").val();
			displayContactRecords(100, 1);
			return false;
		}

		function clearSearchByContactName() {
			searchName = "";
			$("#searchContactName").val("");
			displayContactRecords(100, 1);
		}

		function hideQuickButton(id) {
			$("#" + id).hide();
		}

		function showQuickButton(id) {
			$("#" + id).show();
		}

		function quickEdit(pid) {

			$(".lblQk").show(200);
			$(".btnQuickEdit").show(200);
			$(".txtQk").hide();
			$(".btnQk").hide();
			$(".btnEdit").show(200);
			$("#btnQuickEdit" + pid).hide();
			$("#btnSave" + pid).show(200);
			$("#btnCancel" + pid).show(200);
			$("#lblName" + pid).hide();
			$("#txtName" + pid).show(400);
			$("#name" + pid).focus();
			$("#lblCat" + pid).hide();
			$("#ddCat" + pid).show(400);
		}

		function cancelQuickEdit(pid) {

			$("#txtName" + pid).hide();
			$("#lblName" + pid).show(200);
			$("#ddCat" + pid).hide();
			$("#lblCat" + pid).show(200);
			$("#btnSave" + pid).hide();
			$("#btnCancel" + pid).hide();
			$("#btnQuickEdit" + pid).show(200);
		}

		function saveQuickEdit(pid) {
			var name = $("#name" + pid).val();
			var cbid = pid;
			if (cbid != "") {
				$.ajax({
					type: "POST",
					url: "executive_branch_ajax_function.php",
					data: {
						cid: cid,
						cbid: cbid,
						branch_name: name,
						mode: "edit_branch",
					},
					cache: false,
					beforeSend: function() {

					},
					success: function(json) {
						json = $.parseJSON(json);
						msg = json.ack_msg;
						if (json.ack == 1) {
							toastr.success(msg, "Success!!");
							displayRecords();
						} else {
							toastr.error(msg, 'Error!!')
						}
					}
				});
			} else {
				alert("Category is not selected.");
			}
		}

		function callbackState(mode, result) {
			if (mode == 0) {
				$('#state').html('<option value="">Select State</option>');
				$('#state').select2("val", "");
				$('#city').html('<option value="">Select City</option>');
				$('#city').select2("val", "");
			}
		}

		function callbackCity(mode, result) {
			if (mode == 0) {
				$('#city').html('<option value="">Select City</option>');
				$('#city').select2("val", "");
			}
		}
	</script>

	<script type="text/javascript">
	// file path
	$(function(){
		aj.imageHolder($("input[name=image_path]"),"","",
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
			//toastr.success("Old Image Found!!");
		},
		function(file,img)
		{
			if(!file)
			{
				toastr.error("File may be corrupted or missing. Try again!!");
			}
		},
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
				//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
			},
		function(data){
			isImageThumbnailLoadedReply
		},
		["png","PNG","jpeg","JPEG","jpg","JPG","gif","GIF"]
		);
	})
		$(function() {
			aj.imageHolder($("input[name=vendor_desk]"), "", "",
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValidT = isImageThumbnailValidReply;
					//toastr.success("Old Image Found!!");
				},
				function(file, img) {
					if (!file) {
						toastr.error("File may be corrupted or missing. Try again!!");
					}
				},
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply, image_width, image_height) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValidT = isImageThumbnailValidReply;
					//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
				},
				function(data) {
					isImageThumbnailLoadedReply
				},
				["png", "PNG", "jpeg", "JPEG", "jpg", "JPG", "gif", "GIF", "pdf", "PDF"]
			);
		})
		$(function() {
			aj.imageHolder($("input[name=office_supplier]"), "", "",
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValidT = isImageThumbnailValidReply;
					//toastr.success("Old Image Found!!");
				},
				function(file, img) {
					if (!file) {
						toastr.error("File may be corrupted or missing. Try again!!");
					}
				},
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply, image_width, image_height) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValidT = isImageThumbnailValidReply;
					//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
				},
				function(data) {
					isImageThumbnailLoadedReply
				},
				["png", "PNG", "jpeg", "JPEG", "jpg", "JPG", "gif", "GIF", "pdf", "PDF"]
			);
		})
		$(function() {
			aj.imageHolder($("input[name=gst_detail]"), "", "",
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValidT = isImageThumbnailValidReply;
					//toastr.success("Old Image Found!!");
				},
				function(file, img) {
					if (!file) {
						toastr.error("File may be corrupted or missing. Try again!!");
					}
				},
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply, image_width, image_height) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValidT = isImageThumbnailValidReply;
					//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
				},
				function(data) {
					isImageThumbnailLoadedReply
				},
				["png", "PNG", "jpeg", "JPEG", "jpg", "JPG", "gif", "GIF", "pdf", "PDF"]
			);
		})
		$(function() {
			aj.imageHolder($("input[name=other_image]"), "", "",
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValidT = isImageThumbnailValidReply;
					//toastr.success("Old Image Found!!");
				},
				function(file, img) {
					if (!file) {
						toastr.error("File may be corrupted or missing. Try again!!");
					}
				},
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply, image_width, image_height) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValidT = isImageThumbnailValidReply;
					//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
				},
				function(data) {
					isImageThumbnailLoadedReply
				},
				["png", "PNG", "jpeg", "JPEG", "jpg", "JPG", "gif", "GIF", "pdf", "PDF"]
			);
		})
	</script>
 
	<script type="text/javascript">
		$(document).ready(function() {
			var mode = "<?= isset($_REQUEST['mode']) ? $_REQUEST['mode'] : ""; ?>";
			if(mode=="add")
			{
				client_code_data("");
			}
			if (mode == "edit") {
				/*var state_data = $("#country").val();
				State(state_data);*/

				var city_data = $("#state").val();
				var main_city_data = $("#main_city").val();
				var city_name="<?=$city?>"
				var main_city_name="<?=$main_city?>";
				city_data(main_city_name,city_name);;
				City(city_data,main_city_name);

			}
			if (mode == 'add') 
			{
				State('India');
			}
		});
	</script>

	<script type="text/javascript">
		function State(val) {
			$.ajax({
				type: "POST",
				url: "ajax_get_state.php",
				data: 'cid=' + val,
				success: function(result) {
					$("#state").html(result);
					
				}
			});
		}

		function city_data(val,city_name="") 
		{
			$.ajax({
				type: "POST",
				url: "ajax_get_main_city.php",
				data: 'sid=' + val + '&city='+city_name,
				success: function(result) {
					$("#main_city").html(result);
					
					$("#main_city").select2("destroy");
	            	$("#main_city").html(result);
	           	 	$("#main_city").select2();

				}
			});
		}
		
		function City(val,city_name="") {
			$.ajax({
				type: "POST",
				url: "ajax_get_city.php",
				data: 'sid=' + val + '&city='+city_name,
				success: function(result) {
					$("#city").html(result);
					var class_id = $("#state").find(':selected').attr('data-state_id');
					$("#class_id").val(class_id);
					$("#city").select2("destroy");
	            $("#city").html(result);
	            $("#city").select2();

				}
			});
		}
		function client_code_data(val)
		{  
			var zone_id=$("#zone").val();
			var company_id=$("#type_of_company").val();
			var mode="<?php echo $_REQUEST['mode'];?>";
			if(mode=='edit')
			{
				var id="<?php echo $_REQUEST['id'];?>";
			}
			else
			{
				var id="";
			}
			var type_of_executive="<?php echo $_REQUEST['type'];?>";
			$.ajax({
				type: "POST",
				url: "ajax_get_client_code.php",
				data: 'zone_id=' + zone_id +'&mode='+mode+'&type_of_executive='+type_of_executive+'&id='+id+'&company_id='+company_id,
				success: function(result) { 
					var data =$.parseJSON(result);
	           		// alert(data.client_code);
	           		// added by shivani
	           		$("#client_code").val("");
	           		// $("#client_code").val(data.client_code);
	           		// added by shivani
	           		$("#client_code_sr_by_type").val(data.client_code_sr_by_type); 
				}
			});
		}

		function get_id(){
			var area_id = $("#city").find(':selected').attr('data-city_id');
			$("#area_id").val(area_id);
		}
	</script>


	<script type="text/javascript">
	

	var sc_count1 = 0; 

	$("#add_new").click(function()
	{

		// var rowCount = $("#shipping_address").length;
		// var sc_count = rowCount; 

		var check_shipping_address=$("#shipping_address").val();
		if(check_shipping_address=="")
		{
			toastr.error('Please Enter Shipping Address!!');
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

				var new_row = '<div class="form-group" id="removeClass'+sc_count1+'"><button type="button" onclick="Remove_add('+sc_count1+')" class="remove-this-first text-danger" id="BtnDel"><i class="fa fa-trash"></i></button><textarea class="form-control" name="shipping_address[]" id="shipping_address"></textarea></div>';

				// $("#new_shipping_address").prepend(new_row);
				$("#new_shipping_address").append(new_row);

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
	var sc_count_m = 0; 

	$("#add_new_phone").click(function()
	{
		// var rowCount = $("#shipping_address").length;
		// var sc_count = rowCount; 

		var check_phone_no=$("#phone").val();
		if(check_phone_no=="")
		{
			toastr.error('Please Enter Phone No!!');
		}
		else
		{	
			var duplicate = 0;
			if(duplicate==0)
			{
				sc_count_m++;
			
				// var new_row = '<div class="form-group" id="removeClassPhone'+sc_count_m+'"><button type="button" onclick="Remove_add_phone('+sc_count_m+')" class="remove-this-first-phone-no text-danger" id="BtnDelPhone"><i class="fa fa-trash"></i></button><input type="text" maxlength="10" size="10" minlength="10" class="form-control " name="phone[]" id="phone" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');"></div>';
				// var new_row = '<div class="col-md-12"><div class="form-group" id="removeClassPhone'+sc_count_m+'"><button type="button" onclick="Remove_add_phone('+sc_count_m+')" class="remove-this-first-phone-no text-danger" id="BtnDelPhone"><i class="fa fa-trash"></i></button></div><div class="col-md-6"><label>Phone</label><input type="number" maxlength="10" size="10" minlength="10" class="form-control" name="phone[]" id="phone" onChange="checkInputLength(this.value)"></div><div class="col-md-6"><label>Customer Name</label><input type="text" class="form-control" name="customer_name[]" id="customer_name"></div>';	
				// const last_child = document.querySelector("div phone-m:last-child");
				//console.log(last_child);
				var new_row = '<div class="form-group" id="removeClassPhone'+sc_count_m+'"><button type="button" onclick="Remove_add_phone('+sc_count_m+')" class="remove-this-first-phone-no text-danger" id="BtnDelPhone"><i class="fa fa-trash"></i></button><br><div class="col-md-6"><label>Phone</label><input  type="text" class="form-control multiple-phone-number" name="phone[]" id="phone"  maxlength="15" size="10" onChange="checkInputLength(this.value)"></div><div class="col-md-6"><label>Contact Person Name</label><input type="text" class="form-control" name="customer_name[]" id="customer_name"></div>';
				$("#new_phone_no").append(new_row);
				$(".multiple-phone-number").numeric();
				// $("#new_phone_no").prepend(new_row);
			}
		}
	})
	function Remove_add_phone(del1)
	{	
		var r = confirm("Are you sure you want to delete?");
		if(r)
		{
		 	$("#removeClassPhone"+del1).remove();
		}
	}
	function checkInputLength(value1) 
	{  
	    if (value1.length < 10 || value1.length > 15) 
	    {
	    	$(this.value).val("");
	        alert("Please Enter 15 Digit Phone No!!!");
	      
	    }
	    
	}

	$("#whatsapp_no").numeric();
	$("#other_details").click(function()
	{
	$("#other_details1").show();
	})
	</script>

	<script type="text/javascript">
		function getTransportname(id,transport_name_selected_id=""){	
			$.ajax({
				type: "post",
				url: "ajax_get_transport_detail.php",
				data: "id=" + id+"&selected_id="+transport_name_selected_id,
				beforeSend: function() {
					$(".transCover").fadeIn(800);
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					setTimeout(function() {
					$("#transporter_id").select2("destroy");
					$('#transporter_id').html(result);
					$("#transporter_id").select2();
					// $("#loading-modal").modal('hide');
					$('.preloader').fadeOut('slow');
					});
				}
			})
		}

		function checkcode() {
			var old = $("#client_code").data('clientcode');
			var newcode = $("#client_code").val();
			if (old != newcode) {
				$("#client_code_sr_by_type").val(0);
			}
			// alert(old);
			// alert(newcode);
		}
	</script>
</body>

</html>