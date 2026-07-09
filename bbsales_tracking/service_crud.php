<?php
$page_id = 581;
$page_slug = 'manage_complain';
$ctable 	= "complain";
$ctable1 	= "Service";
$page 		= $ctable . "_manage";
//$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
// Assuming $ctable1 contains the title of the page.
$page_title = $ctable1;
// Assuming $ctable1 contains the title of the page.
$page_hierarchy = array(
	array("link" => "", "title" => "Master"),
	array("link" => "manage_complain.php", "title" => "Manage " . $ctable1),
	array("link" => $ctable1 . "_crud.php", "title" => "Add/Edit " . $ctable1)
);

// Include necessary files
include("connect.php");
include('../include/class.complain.php');

// Create a new Complain object
$objComplain = new Complain();

// Initialize variables
$complain_type = "";
$complain_cat_id = "";
$complain_subcat_id = "";
$executive_type = "";
$customer_id = "";
$app_address = "";
$contact_person = "";
$state = "";
$city = "";
$zone = "";
$remark = "";
$complain_created_by = "";
$complain_assign_to = "";
$complain_date = "";
$productSubCatIdsArr = "";
$productIdsArr = "";


if (isset($_REQUEST['submit'])) {

	$detail['complain_id']           = isset($_REQUEST['complain_id']) ? $db->clean($_REQUEST['complain_id']) : '';

	$detail['service_no']           = isset($_REQUEST['service_no']) ? $db->clean($_REQUEST['service_no']) : '';

	// $detail['complain_date']         = isset($_REQUEST['complain_date']) ? date("Y-m-d", strtotime($db->clean($_REQUEST['complain_date']))) : '';

	$detail['customer_name']         = isset($_REQUEST['buyer_name']) ? $db->clean($_REQUEST['buyer_name']) : '';

	$detail['address']               = isset($_REQUEST['buyer_address']) ? stripslashes($db->clean($_REQUEST['buyer_address'])) : '';

	$detail['contact_person_name']   = isset($_REQUEST['contact_person']) ? $db->clean($_REQUEST['contact_person']) : '';

	$detail['contact_no']            = isset($_REQUEST['contact_no']) ? $db->clean($_REQUEST['contact_no']) : '';

	$detail['service_start_time']    = isset($_REQUEST['service_start_time']) ? $db->clean($_REQUEST['service_start_time']) : '';

	$detail['service_end_time']      = isset($_REQUEST['service_end_time']) ? $db->clean($_REQUEST['service_end_time']) : '';

	$detail['servicemen']            = isset($_REQUEST['servicemen']) ? $db->clean($_REQUEST['servicemen']) : '';

	$detail['remark']                = isset($_REQUEST['remark']) ? stripslashes($db->clean($_REQUEST['remark'])) : '';

	$detail['type_of_product']       = isset($_REQUEST['type_of_product']) ? $db->clean($_REQUEST['type_of_product']) : '';

	$detail['product']               = isset($_REQUEST['product']) ? $db->clean($_REQUEST['product']) : '';

	$detail['state_city']            = isset($_REQUEST['state_city']) ? stripslashes($db->clean($_REQUEST['state_city'])) : '';

	$detail['site_name']             = isset($_REQUEST['site_name']) ? $db->clean($_REQUEST['site_name']) : '';

	$detail['site_address']          = isset($_REQUEST['site_address']) ? stripslashes($db->clean($_REQUEST['site_address'])) : '';

	$detail['contractor']            = isset($_REQUEST['contractor']) ? $db->clean($_REQUEST['contractor']) : '';

	$detail['test_date']             = isset($_REQUEST['test_date']) ? date("Y-m-d", strtotime($db->clean($_REQUEST['test_date']))) : '';

	$detail['tested_pressure']       = isset($_REQUEST['tested_pressure']) ? $db->clean($_REQUEST['tested_pressure']) : '';

	$detail['is_issues_testing']     = isset($_REQUEST['is_issues_testing']) ? $db->clean($_REQUEST['is_issues_testing']) : 0;

	$detail['last_maintenance_date'] = isset($_REQUEST['last_maintenance_date']) ? date("Y-m-d", strtotime($db->clean($_REQUEST['last_maintenance_date']))) : '';

	$detail['product_type']        = isset($_REQUEST['product_type']) ? $db->clean($_REQUEST['product_type']) : '';

	$detail['specifications']        = isset($_REQUEST['specifications']) ? $db->clean($_REQUEST['specifications']) : '';

	$detail['root_of_issue']         = isset($_REQUEST['root_of_issue']) ? $db->clean($_REQUEST['root_of_issue']) : '';

	$detail['current_scenario']      = isset($_REQUEST['current_scenario']) ? stripslashes($db->clean($_REQUEST['current_scenario'])) : '';

	$detail['conclusion']            = isset($_REQUEST['conclusion']) ? stripslashes($db->clean($_REQUEST['conclusion'])) : '';

	$detail['resolution']            = isset($_REQUEST['resolution']) ? $db->clean($_REQUEST['resolution']) : 0;

	$detail['invoice_no']            = isset($_REQUEST['invoice_no']) ? $db->clean($_REQUEST['invoice_no']) : 0;

	$detail['invoice_date'] 				 = isset($_REQUEST['invoice_date']) ? date("Y-m-d", strtotime($db->clean($_REQUEST['invoice_date']))) : '';

	$detail['mt_fire_hydrant']       = isset($_REQUEST['mt_fire_hydrant']) ? $db->clean($_REQUEST['mt_fire_hydrant']) : '';

	$detail['mt_rrl']       				 = isset($_REQUEST['mt_rrl']) ? $db->clean($_REQUEST['mt_rrl']) : '';

	$detail['mt_hose_reel_drum']     = isset($_REQUEST['mt_hose_reel_drum']) ? $db->clean($_REQUEST['mt_hose_reel_drum']) : '';

	$detail['mt_branch_pipe']        = isset($_REQUEST['mt_branch_pipe']) ? $db->clean($_REQUEST['mt_branch_pipe']) : '';

	$detail['mt_inlet']       			 = isset($_REQUEST['mt_inlet']) ? $db->clean($_REQUEST['mt_inlet']) : '';

	$detail['mt_new']       				 = isset($_REQUEST['mt_new']) ? $db->clean($_REQUEST['mt_new']) : '';

	$detail['sr_no']       				 	 = isset($_REQUEST['sr_no']) ? $db->clean($_REQUEST['sr_no']) : '';




	// echo "<pre>";
	// print_r($detail);exit;

	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "add") {

		if ($rights['insert_flag'] != 1) {
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply = $objComplain->AddComplainService($detail);
		if ($reply['ack'] == 1) {
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("manage_complain.php?msg=inserted");
		} else {
			$db->addErrorMessage($reply['ack_msg']);
		}
	}
}

/* Complain Data Get */
$GetOutlet_R = $db->rp_getData("complain", "*", "id='" . $_REQUEST['complain_id'] . "' AND isDelete=0", "", 0);
$GetOutlet_D = mysqli_fetch_assoc($GetOutlet_R);
$complain_assign_to = $db->rp_getValue("complain", "complain_assign_to", "id='" . $GetOutlet_D['id'] . "' AND isDelete=0", 0);

//Product Sub Category
$productSubCatIds = $GetOutlet_D['product_sub_category'];
if ($productSubCatIds != "" && $productSubCatIds != NULL && $productSubCatIds != nulll && isset($productSubCatIds) && !empty($productSubCatIds)) {
	$productSubCatIdsArr = explode(",", $productSubCatIds);
	$productSubCatIdsArr = is_array($productSubCatIdsArr) ? $productSubCatIdsArr : 0;
}

//Product
$productIds = $GetOutlet_D['product_id'];
if ($productIds != "" && $productIds != NULL && $productIds != nulll && isset($productIds) && !empty($productIds)) {
	$productIdsArr = explode(",", $productIds);
	$productIdsArr = is_array($productIdsArr) ? $productIdsArr : 0;
}

/* Complain Data Get */

/* Customer Data Get */
$complainComplainCustomer_r = $db->rp_getData("executive", "*", "isDelete=0 AND isActive=1 AND id='" . $GetOutlet_D['customer_id'] . "'");
$complainComplainCustomer_d = mysqli_fetch_assoc($complainComplainCustomer_r);
// $buyerName = $complainComplainCustomer_d['cname'];
$buyerCompanyName = $complainComplainCustomer_d['company_name'];
$contact_no = $complainComplainCustomer_d['phone'];
$buyer_address = $complainComplainCustomer_d['address'];
$client_code = $complainComplainCustomer_d['client_code'];
$buyerState = $complainComplainCustomer_d['state'];
$buyerCity = $complainComplainCustomer_d['main_city'];
$buyerContactNo = $complainComplainCustomer_d['mobile_no1'];
$contactPersonId = $complainComplainCustomer_d['seid'];
$contactPersonName = $complainComplainCustomer_d['cname'];
/* Customer Data Get */

$Servicemen = $db->rp_getValue("sales_executive", "GROUP_CONCAT(name)", "id IN(" . $complain_assign_to . ") AND isDelete=0", 0);

/* Service Data Get */
$GetService_R = $db->rp_getData("complain_service", "*", "complain_id='" . $_REQUEST['complain_id'] . "' AND isDelete=0", "", 0);
$GetService_D = mysqli_fetch_assoc($GetService_R);

$mt_fire_hydrant 		= ($GetService_D['mt_fire_hydrant']) ? "checked" : "";
$mt_rrl 						= ($GetService_D['mt_rrl']) ? "checked" : "";
$mt_hose_reel_drum 	= ($GetService_D['mt_hose_reel_drum']) ? "checked" : "";
$mt_branch_pipe 		= ($GetService_D['mt_branch_pipe']) ? "checked" : "";
$mt_inlet 					= ($GetService_D['mt_inlet']) ? "checked" : "";
$mt_new 						= ($GetService_D['mt_new']) ? "checked" : "";
$lastMaDate 				= ($GetService_D['mt_new']) ? "disabled" : "";



// if (!isset($company_detail_d['sr_no']) || empty($company_detail_d['sr_no']) || $company_detail_d['sr_no'] == "" || $company_detail_d['sr_no'] == NULL || $company_detail_d['sr_no'] == null && $company_detail_d['sr_no'] == 0) {
//     $maxId = $db->rp_getValue("complain_service", "MAX(`id`)", "isDelete=0 AND complain_id='" . $_REQUEST['complain_id'] . "'", 0);
//     $value = $db->rp_getValue("complain_service", "sr_no", "id='" . $maxId . "'");
//     $value += 1;
//     $GetService_D['sr_no'] = $value;
// }
/* Service Data Get */

/* Company Master Data Get */
$company_detail_r = $db->rp_getData("company_master", "*", "id='" . $GetOutlet_D['type_of_company'] . "' AND isDelete=0", "", 0);
$company_detail_d = mysqli_fetch_assoc($company_detail_r);
/* Company Master Data Get */

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<link rel="stylesheet" type="text/css" href="assets/js/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" />
<link rel="stylesheet" href="assets/global/plugins/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" />
<link href="assets/global/plugins/bootstrap-datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/fSelect.css" />

<head>
	<meta charset="utf-8" />
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
	<?php include("include_css.php"); ?>


	<style type="text/css">
		.mainDiv,
		table {
			table-layout: fixed;
			/* Ensure equal width for all cells */
			border: 1px solid #595959;
			border-collapse: collapse;
			font-size: 13px;
			width: 250mm !important;
			/* Width of the elements is set to 250 millimeters */
			background-color: #FFF;
			margin: auto;
			/* Center the element horizontally */
			padding: auto;
			/* NOT a valid CSS property. It should be removed. */
		}

		table,
		td,
		th {
			border: 1px solid #595959;
		}

		td,
		th {
			padding: 5px;
			height: 25px;
		}

		/* The following styles define classes to hide certain borders of elements */
		.no-border-left {
			border-left: hidden;
		}

		.no-border-right {
			border-right: hidden;
		}

		.no-border-bottom {
			border-bottom: hidden !important;
			/* Important rule to override other styles */
		}

		.no-border-top {
			border-top: hidden !important;
			/* Important rule to override other styles */
		}

		.bootstrap-timepicker-widget table {
			width: 100% !important;
			/* Set the width of the Bootstrap timepicker widget table to 100% */
		}

		.ui-datepicker table {
			width: 100% !important;
			/* Set the width of the jQuery UI datepicker widget table to 100% */
		}

		.main-title>span>input {
			border: 0;
			outline: 0;
			background: transparent;
			border-bottom: 1px solid black;
			/* Remove borders and apply a black bottom border to the input element */
		}

		.section {
			margin-bottom: 20px !important;
			/* Add a bottom margin of 20 pixels to elements with class "section" */
		}

		h2 {
			margin-top: 0;
			/* Remove the top margin of h2 elements */
		}

		.checkbox-label {
			display: block !important;
			/* Set the display property of the element with class "checkbox-label" to block */
			margin-bottom: 10px !important;
			/* Add a bottom margin of 10 pixels to elements with class "checkbox-label" */
		}

		.aling-check-box-css-input {
			margin-right: 5px !important;
			/* Add a right margin of 5 pixels to elements with class "aling-check-box-css-input" */
		}

		/* To increase the width of radio buttons */
		input[type="radio"] {
			width: 20px !important;
			/* You can adjust the width to your preference */
			height: 20px !important;
			/* Set the height to match the width for a symmetrical look */
		}

		/* To increase the width of checkboxes */
		input[type="checkbox"] {
			width: 20px !important;
			/* You can adjust the width to your preference */
			height: 20px !important;
			/* Set the height to match the width for a symmetrical look */
		}

		.mt-0 {
			margin-top: 0 !important;
		}
	</style>

</head>

<body class="page-md">
	<?php include("header.php"); ?>



	<div class="page-container">
		<div class="page-head bg-grey">
			<div class="container">
				<div class="page-title">
					<h1><a href="<?php echo "manage_complain.php"; ?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy); ?> </h1>
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
				<div class="page-head bg-grey">
					<div class="container">
						<div class="page-title">
							<h2><?php echo $page_title; ?></h2>
						</div>
						<div class="page-toolbar">
							<?php
							if ($flag_d['print_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
							?>
								<div class="btn-group btn-theme-panel">
									<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="printReport('<?php echo $_REQUEST['complain_id']; ?>');" title="Print">Print</a>
								</div>
							<?php
							}
							?>
						</div>
					</div>
				</div>
				<form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
					<input type="hidden" name="mode" value="add">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet box blue">
								<div class="portlet-body form">
									<div class="form-body">
										<div class="mainDiv">
											<table>
												<tbody>

													<tr>
														<td colspan="16" class="" style="text-align: center;">
															<?php
															if (isset($company_detail_d['image_path']) && $company_detail_d['image_path'] != "") {
															?>
																<img style="width: 100%; padding: 0px !important;" src="<?= HEADER_A . $company_detail_d['image_path'] ?>">
															<?php
															} else {
															?>
																<img style="width: 100%; padding: 0px !important;" src="../images/craftbox_header.jpg">
															<?php
															}
															?>
														</td>
													</tr>

													<tr>
														<td colspan="16" class="main-title color" align="center" style="height: 60px; font-size: 20px;">
															<span><strong>Product Inspection</strong></span>
														</td>
													</tr>
													<tr>
														<td colspan="4" class="color" align="center"><strong>Sr. No. <code>*</code> </strong></td>
														<td colspan="4" class="color" align="center">
															<input type="text" name="sr_no" class="form-control" id="sr_no" value="<?= $GetService_D['sr_no']; ?>">
														</td>
														<td colspan="4" class="color" align="center"><strong>Date</strong></td>
														<?php
														if (
															$GetService_D['service_date'] != "1970-01-01" &&
															$GetService_D['service_date'] != "0000-00-00" &&
															$GetService_D['service_date'] != "" &&
															$GetService_D['service_date'] != "0003-01-01"
														) {
															$service_date = date("d-m-Y", strtotime($GetService_D['service_date']));
														} else {
															$service_date = date("d-m-Y");
														}
														?>
														<td colspan="4" class="color" align="center">
															<input readonly="" type="text" name="service_date" class="form-control" id="service_date" value="<?= $service_date ?>">
														</td>
													</tr>


													<!-- Row 1 -->
													<tr>
														<!-- Buyer Name -->
														<td colspan="4" class="color" align="center"><strong>Buyer Name <code>*</code> </strong></td>
														<td colspan="4" class="color" align="center">
															<input readonly type="text" name="buyer_name" class="form-control" id="buyer_name" value="<?= $buyerCompanyName . " - " . $client_code ?>">
														</td>
														<!-- Type of Product -->
														<td colspan="4" class="color" align="center"><strong>Type of Product <code>*</code> </strong></td>
														<td colspan="4" class="color" align="left">
															<input type="hidden" name="type_of_product" class="form-control" id="type_of_product" value="<?= $productSubCatIds; ?>">
															<ul>
																<?php
																for ($psc = 0; $psc < sizeof($productSubCatIdsArr); $psc++) {
																?>
																	<li><?= $db->rp_getValue("category_master", "name", "isDelete=0 AND id='" . $productSubCatIdsArr[$psc] . "'"); ?></li>
																<?php
																}
																?>
															</ul>
															<div class="btn-group btn-theme-panel mt-0">
																<a class="btn dropdown-toggle blue-ebonyclay" title="Add Type Of Product" href='#type-of-product' data-mode="type_of_product" data-toggle='modal'>ADD</a>
															</div>
														</td>
													</tr>

													<!-- Row 2 -->
													<tr>
														<!-- Address -->
														<td colspan="4" class="color" align="center"><strong>Address</strong></td>
														<td colspan="4" class="color" align="center">
															<textarea readonly name="buyer_address" class="form-control" id="buyer_address"><?= $buyer_address; ?></textarea>
														</td>
														<!-- Product -->
														<td colspan="4" class="color" align="center"><strong>Product</strong></td>
														<td colspan="4" class="color" align="left">
															<input type="hidden" name="product" class="form-control" id="product" value="<?= $productIds; ?>">
															<ul>
																<?php
																for ($p = 0; $p < sizeof($productIdsArr); $p++) {

																	$product_weight = $db->rp_getValue("product_weight_price", "weight_id", "id='" . $productIdsArr[$p] . "' AND isDelete=0", 0);

																	$product_id = $db->rp_getValue("product_weight_price", "product_id", "id='" . $productIdsArr[$p] . "' AND isDelete=0");

																	$product_name = $db->rp_getValue("product", "name", "isDelete=0 AND isActive=1 AND id='" . $product_id . "'", "", 0);

																	$weight_name = $db->rp_getValue("weight", "name", "id='" . $product_weight . "' AND isDelete=0 AND id!='-1'", 0);

																?>
																	<li><?= ($weight_name != "") ? $product_name . " - " . $weight_name : $product_name ?></li>
																<?php
																}
																?>
															</ul>
															<div class="btn-group btn-theme-panel mt-0">
																<a class="btn dropdown-toggle blue-ebonyclay" title="Add Type Of Product" href='#type-of-product' data-mode="type_of_product" data-toggle='modal'>ADD</a>
															</div>
														</td>
														</td>
													</tr>


													<!-- Row 3 -->
													<tr>
														<!-- Contact Person -->
														<td colspan="4" class="color" align="center"><strong>Contact Person <code>*</code> </strong></td>
														<td colspan="4" class="color" align="center">
															<input readonly type="text" name="contact_person" class="form-control" id="contact_person" value="<?= $contactPersonName; ?>">
														</td>
														<!-- Invoice No./Date -->
														<td colspan="4" class="color" align="center"><strong>Invoice No./Date</strong></td>
														<?php
														if (
															$GetService_D['invoice_date'] != "1970-01-01" &&
															$GetService_D['invoice_date'] != "0000-00-00" &&
															$GetService_D['invoice_date'] != "" &&
															$GetService_D['invoice_date'] != "0003-01-01"
														) {
															$invoice_date = date("d-m-Y", strtotime($GetService_D['invoice_date']));
														} else {
															$invoice_date = "";
														}
														?>
														<td colspan="4" class="color" align="center">
															<input type="text" name="invoice_no" class="form-control" id="invoice_no" value="<?= $GetService_D['invoice_no']; ?>" placeholder='Invoice No.'>
															<br>
															<input type="text" name="invoice_date" class="form-control" id="invoice_date" value="<?= $invoice_date ?>" placeholder='Invoice Date'>
														</td>
													</tr>

													<!-- Row 4 -->
													<tr>
														<!-- State/City -->
														<td colspan="4" class="color" align="center"><strong>State/City <code>*</code> </strong></td>
														<td colspan="4" class="color" align="center">
															<input readonly type="text" name="state_city" class="form-control" id="state_city" value="<?= $buyerState . " / " . $buyerCity ?>">
														</td>

														<!-- Sales Person -->
														<td colspan="4" class="color" align="center"><strong>Sales Person <code>*</code> </strong></td>
														<td colspan="4" class="color" align="center">
															<input readonly type="text" name="servicemen" class="form-control" id="servicemen" value="<?= $Servicemen ?>">
														</td>
													</tr>


													<!-- Row 5 -->
													<tr>
														<!-- Contact No. -->
														<td colspan="4" class="color" align="center"><strong>Contact No. <code>*</code> </strong></td>
														<td colspan="4" class="color" align="center">
															<input readonly type="text" name="contact_no" class="form-control" id="contact_no" value="<?= $buyerContactNo ?>">
														</td>
														<!-- Note: The next two columns are commented out. Uncomment if needed. -->
														<!-- Contact No. -->
														<!-- <td colspan="4" class="color" align="center"><strong>Contact No.</strong></td> -->
														<!-- <td colspan="4" class="color" align="center">
														<input type="text" name="contact_no" class="form-control" id="contact_no" value="">
													</td> -->
													</tr>

													<!-- Row 6 -->
													<tr>
														<td colspan="16" class="color" align="center" style="height: 40px; background-color: #E5E5E5;"><strong>Test Details</strong></td>
													</tr>

													<!-- Row 7 -->
													<tr>
														<!-- Site Name -->
														<td colspan="4" class="color" align="center"><strong>Site Name</strong></td>
														<td colspan="12" class="color" align="center">
															<input type="text" name="site_name" class="form-control" id="site_name" value="<?= $GetService_D['site_name'] ?>">
														</td>
													</tr>

													<!-- Row 8 -->
													<tr>
														<!-- Site Address -->
														<td colspan="4" class="color" align="center"><strong>Site Address</strong></td>
														<td colspan="12" class="color" align="center">
															<textarea name="site_address" class="form-control" id="site_address"><?= $GetService_D['site_address'] ?></textarea>
														</td>
													</tr>

													<!-- Row 9 -->
													<tr>
														<!-- Contractor -->
														<td colspan="4" class="color" align="center"><strong>Government Office</strong></td>
														<td colspan="12" class="color" align="center">
															<input type="text" name="contractor" class="form-control" id="contractor" value="<?= $GetService_D['contractor'] ?>">
														</td>
													</tr>

													<!-- Row 10 -->
													<tr>
														<!-- Test Details -->
														<td colspan="4" class="color" align="center"><strong>Test Details</strong></td>
														<?php
														if (
															$GetService_D['test_date'] != "1970-01-01" &&
															$GetService_D['test_date'] != "0000-00-00" &&
															$GetService_D['test_date'] != "" &&
															$GetService_D['test_date'] != "0003-01-01"
														) {
															$test_date = date("d-m-Y", strtotime($GetService_D['test_date']));
														} else {
															$test_date = "";
														}
														?>
														<td colspan="4" class="color" align="left">
															<!-- Test Date -->
															<div><strong>Test Date: </strong>
																<input type="text" name="test_date" class="form-control" id="test_date" value="<?= $test_date ?>">
															</div>
															<br>
															<!-- Tested Pressure -->
															<div><strong>Tested Pressure: KGF/CM<sup>2</sup></strong>
																<input type="text" name="tested_pressure" class="form-control" id="tested_pressure" value="<?= $GetService_D['tested_pressure'] ?>">
															</div>
															<br>
															<!-- Issues in Testing -->
															<?php
															if ($GetService_D['is_issues_testing'] == 1) {
																$is_issues_testing_checked = "checked";
															} else {
																$is_issues_testing_checked = "";
															}
															?>
															<div>
																<strong>Issues in Testing: </strong>
																<input type="checkbox" name="is_issues_testing" class="form-control" value="1" id="is_issues_testing" <?= $is_issues_testing_checked; ?>>
																<strong>YES / NO</strong>
															</div>
														</td>
														<!-- Maintenance Test -->
														<td colspan="2" class="color" align="center"><strong>Maintenance Test: </strong></td>

														<td colspan="6" class="color" align="left">
															<!-- Maintenance Test Options -->
															<div style="display: flex; justify-content: space-evenly;">
																<div class="section">
																	<h2>Annual</h2>
																	<label class="checkbox-label">
																		Fire Hydrant <input type="checkbox" name="mt_fire_hydrant" id="mt_fire_hydrant" value="1" class="align-checkbox-css-input" <?= $mt_fire_hydrant; ?>>
																	</label>
																	<label class="checkbox-label">
																		RRL <input type="checkbox" name="mt_rrl" value="1" id="mt_rrl" class="align-checkbox-css-input" <?= $mt_rrl; ?>>
																	</label>
																	<label class="checkbox-label">
																		Hose Reel Drum <input type="checkbox" name="mt_hose_reel_drum" id="mt_hose_reel_drum" value="1" class="align-checkbox-css-input" <?= $mt_hose_reel_drum; ?>>
																	</label>
																	<label class="checkbox-label">
																		Branch Pipe <input type="checkbox" name="mt_branch_pipe" id="mt_branch_pipe" value="1" class="align-checkbox-css-input" <?= $mt_branch_pipe; ?>>
																	</label>
																	<label class="checkbox-label">
																		Inlet <input type="checkbox" name="mt_inlet" id="mt_inlet" value="1" class="align-checkbox-css-input" <?= $mt_inlet; ?>>
																	</label>
																</div>

																<div class="section" style="text-align: center;">
																	<h2>New</h2>
																	<label class="checkbox-label">
																		<input type="checkbox" name="mt_new" value="1" id="mt_new" class="align-checkbox-css-input" <?= $mt_new; ?>>
																	</label>
																</div>
															</div>
														</td>
													</tr>

													<!-- Row 11 -->
													<tr>
														<!-- Remarks -->
														<td colspan="4" class="color" align="center"><strong>Remarks: </strong></td>
														<td colspan="4" class="color" align="left">
															<textarea class="form-control" name="remark" id="remark" rows="3">
															<?= $GetService_D['remark'] ?>
														</textarea>
														</td>
														<!-- Last Maintenance Date -->
														<td colspan="2" class="color" align="center"><strong>Last Maintenance Date: - </strong></td>
														<?php
														if (
															$GetService_D['last_maintenance_date'] != "1970-01-01" &&
															$GetService_D['last_maintenance_date'] != "0000-00-00" &&
															$GetService_D['last_maintenance_date'] != "" &&
															$GetService_D['last_maintenance_date'] != "0003-01-01"
														) {
															$last_maintenance_date = date("d-m-Y", strtotime($GetService_D['last_maintenance_date']));
														} else {
															$last_maintenance_date = "";
														}
														?>
														<td colspan="6" class="color" align="left">
															<input type="text" name="last_maintenance_date" class="form-control" id="last_maintenance_date" value="<?= $last_maintenance_date ?>" <?= $lastMaDate; ?>>
														</td>
													</tr>

													<!-- Row 12 -->
													<tr>
														<td colspan="16" class="color" align="center" style="height: 40px; background-color: #E5E5E5;"><strong>Current Date Observation</strong></td>
													</tr>

													<!-- Row 13 -->
													<!-- left -->
													<!-- <tr>
													<td colspan="4" class="color" align="center"><strong>Product Type</strong></td>
													<td colspan="12" class="color" align="left">
														<input type="text" name="product_type" class="form-control" id="product_type" value="<?= $GetService_D['product_type'] ?>">
													</td>
												</tr> -->

													<!-- Row 14 -->
													<tr>
														<!-- Specifications -->
														<td colspan="4" class="color" align="center"><strong>Specifications</strong></td>
														<td colspan="12" class="color" align="left">
															<input type="text" name="specifications" class="form-control" id="specifications" value="<?= $GetService_D['specifications'] ?>">
														</td>
													</tr>


													<!-- Row 15 -->
													<tr>
														<!-- Root of Issue -->
														<td colspan="4" class="color" align="center"><strong>Root of Issue</strong></td>
														<td colspan="12" class="color" align="left">
															<input type="text" name="root_of_issue" class="form-control" id="root_of_issue" value="<?= $GetService_D['root_of_issue'] ?>">
														</td>
													</tr>

													<!-- Row 16 -->
													<tr>
														<!-- Current Scenario -->
														<td colspan="4" class="color" align="center"><strong>Current Scenario: -</strong></td>
														<td colspan="12" class="color" align="left">
															<textarea class="form-control" name="current_scenario" id="current_scenario" rows="3"><?= $GetService_D['current_scenario'] ?></textarea>
														</td>
													</tr>

													<!-- Row 17 -->
													<tr>
														<!-- Conclusion -->
														<td colspan="4" class="color" align="center"><strong>Conclusion:</strong></td>
														<td colspan="12" class="color" align="left">
															<textarea class="form-control" name="conclusion" id="conclusion" rows="3"><?= $GetService_D['conclusion'] ?></textarea>
														</td>
													</tr>

													<!-- Row 18 -->
													<tr>
														<td colspan="16" class="color" align="left">
															<!-- Resolution -->
															<div>
																<strong>Resolution: </strong>

																<?php
																if ($GetService_D['resolution'] == 1) {
																	$MaintenanceChecked = "checked";
																	$ReplacementChecked = "";
																} else if ($GetService_D['resolution'] == 2) {
																	$MaintenanceChecked = "";
																	$ReplacementChecked = "checked";
																} else {
																	$MaintenanceChecked = "";
																	$ReplacementChecked = "";
																}
																?>

																<input type="radio" class="custom-checkbox" name="resolution" id="maintenance" value="1" <?= $MaintenanceChecked ?>>
																<label class="form-check-label" for="inlineCheckbox1">Maintenance</label>

																<input type="radio" class="custom-checkbox" name="resolution" id="replacement" value="2" <?= $ReplacementChecked ?>>
																<label class="form-check-label" for="inlineCheckbox2">Replacement</label>

															</div>
														</td>
													</tr>
												</tbody>
											</table>

											<table>
												<tbody>
													<tr height="80px;">
														<!-- Observer Section -->
														<td colspan="8" class="no-border-bottom">
															<div style="margin: 15px;"><strong>Observer Name: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</strong> </div>
															<br>
															<div style="margin: 15px;"><strong>Company Person Sign: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</strong> </div>
															<br>
															<div style="margin: 15px;"><strong>Date: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</strong> </div>
														</td>

														<!-- Client Section -->
														<td colspan="8" align="right" class="no-border-bottom no-border-left">
															<div style="margin: 15px;"><strong>Client Name: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</strong> </div>
															<br>
															<div style="margin: 15px;"><strong>Client Seal / Sign: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</strong> </div>
															<br>
															<div style="margin: 15px;"><strong>Date: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</strong> </div>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='manage_complain.php'">Back</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div id="type-of-product" class="modal fade" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-add"></i><span class="add-service-item-title">Add Service Item</span>
						</div>
						<div class="tools">

							<a href="javascript:;" id="requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

							<a href="javascript:;" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
						</div>
					</div>
					<div class="portlet-body portlet-empty" style="">
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include("footer.php"); ?>
	<?php include("include_js.php"); ?>


	<!-- <script type="text/javascript" src="assets/js/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script> -->
	<script type="text/javascript" src="assets/js/jquery.numeric.min.js"></script>
	<!-- <script type="text/javascript" src="assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script> -->


	<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
	<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/fSelect.js"></script>

	<script type="text/javascript">
		$('#service_date').datepicker({
			datepicker: true,
			autoclose: true,
			dateFormat: "dd-mm-yy"
		});
		$('#invoice_date').datepicker({
			dateFormat: "dd-mm-yy",
			orientation: "auto",
			startDate: "",
			clearBtn: false
		});
		$('#test_date').datepicker({
			dateFormat: "dd-mm-yy",
			orientation: "auto",
			startDate: "",
			clearBtn: false
		});
		$('#last_maintenance_date').datepicker({
			dateFormat: "dd-mm-yy",
			orientation: "auto",
			startDate: "",
			clearBtn: false
		});
		$('#tested_pressure').numeric();
	</script>

	<script type="text/javascript">
		$('#type-of-product').on('show.bs.modal', function(event) {
			var button = $(event.relatedTarget) // Button that triggered the modal
			var mode = button.data("mode");
			var complain_id = '<?= $_REQUEST['complain_id'] ?>';

			$("#requesting_ajax").attr("data-url", "service_add_ajax.php?mode=" + mode + "&complain_id=" + complain_id);
			$("#requesting_ajax").click();
		});
	</script>

	<script type="text/javascript">
		// function Getcustomer(customer_type)
		// {
		// 	$.ajax({
		//       	type: "POST",
		//       	url: "ajax_get_customer.php",
		//       	data:'customer_type='+customer_type,
		//       	beforeSend:function(){
		//           },
		//       	success: function(data){
		//             $("#customer_id").select2("destroy");
		//             $("#customer_id").html(data);
		//             $("#customer_id").select2();
		//      		}
		//  	 	});
		// }

		// function Getsubcategory(category_id)
		// {
		// 	$.ajax({
		//       	type: "POST",
		//       	url: "ajax_get_subcategory.php",
		//       	data:'category_id='+category_id,
		//       	beforeSend:function(){
		//           },
		//       	success: function(data){
		//             $("#complain_subcat_id").select2("destroy");
		//             $("#complain_subcat_id").html(data);
		//             $("#complain_subcat_id").select2();
		//      		}
		//  	 	});
		// }

		// function GetcustomerInfo(customer_id)
		// {
		// 	$.ajax({
		//       	type: "POST",
		//       	url: "ajax_get_customer_info.php",
		//       	data:'customer_id='+customer_id,
		//       	beforeSend:function(){
		//           },
		//       	success: function(data){
		//       		var data =$.parseJSON(data);
		//       		$("#address").html(data.address);
		//       		$("#contact_person").val(data.cname);
		//       		$("#state").val(data.state);
		//       		$("#city").val(data.city);
		//       		$("#zone").val(data.zip);
		//       	}
		//  	 	});
		// }
	</script>

	<script type="text/javascript">
		// function hasValue(elem) {
		//   	return $(elem).filter(function() { return $(this).val(); }).length > 0;
		// }

		// $("#add").click(function()
		// {
		// 	var check_product=$("#product_ids").val();
		// 	var make=$("#make").val();
		// 	var sell_date=$("#sell_date").val();
		// 	var warranty=$("#warranty").val();

		// 	if(check_product=="" || check_product=="Select Product")
		// 	{
		// 		toastr.error('Please Select product!!');
		// 	}
		// 	else
		// 	{	
		// 		var product_id=$(".pids"+check_product).data('pid');
		// 		var p_name=$("#product_ids").find('option:selected').data('name');
		// 		var duplicate = hasValue($("input.product_id[value='"+product_id+"']"));
		// 		var makeflagname = $("#make").find('option:selected').data('make_name');
		// 		var warrantyflagname = $("#warranty").find('option:selected').data('warranty_name');
		// 		if(duplicate==0)
		// 		{
		// 			var new_row="<tr><td class='text-center'><input type='hidden' name='product_id[]' class='form-control' style='text-align:right;' value='"+check_product+"' id='product_id'/><input type='hidden' name='pro_name[]' value='"+p_name+"' id='pro_name'>"+p_name+"</td>" +

		// 			"<td class='text-center'><input type='hidden' name='make[]' class='form-control positive  quantity' style='text-align:right;' value='"+make+"' id='make'/>"+makeflagname+"</td>" +

		// 			"<td class='text-center'><input type='hidden' name='sell_date[]' class='form-control' style='text-align:right;' value='"+sell_date+"' id='sell_date'/>"+sell_date+"</td>" +

		// 			"<td class='text-center'><input type='hidden' name='warranty[]' class='warrantyble-single-amount form-control' style='text-align:right;' value='"+warranty+"' id='warranty'/>"+warrantyflagname+"</td>" +

		// 			"<td  class='text-center'><a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></td></tr>" ;

		// 			$("#datatable_1").find('tbody').append(new_row);
		// 		}
		// 		else
		// 		{
		// 			toastr.error("Product already added!!");
		// 		}
		// 	}
		// })

		$(document).ready(function() {
			// $("#datatable_1").on('click','.delete',function(){
			// var r = confirm("Are you sure you want to delete?");
			// 	if(r){
			// 		$(this).closest('tr').remove();
			// 		recalculateFinalValues();
			// 	}
			// });
		});
	</script>
	<script type="text/javascript">
		// $(function(){
		// 	aj.imageHolder($("input[name=lr_attachment]"),"","",
		// 	function(isImageThumbnailLoadedReply,isImageThumbnailValidReply){
		// 		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
		// 		isImageThumbnailValidT=isImageThumbnailValidReply;
		// 		//toastr.success("Old Image Found!!");
		// 	},
		// 	function(file,img)
		// 	{
		// 		if(!file)
		// 		{
		// 			toastr.error("File may be corrupted or missing. Try again!!");
		// 		}
		// 	},
		// 	function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height){
		// 		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
		// 		isImageThumbnailValidT=isImageThumbnailValidReply;
		// 			//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
		// 		},
		// 	function(data){
		// 		isImageThumbnailLoadedReply
		// 	},
		// 	["png","PNG","jpeg","JPEG","jpg","JPG","gif","GIF"]
		// 	);
		// })
	</script>

	<script type="text/javascript">
		function check_form() {
			var isValid = true;

			if ($("#buyer_name").val() == "" || $("#buyer_name").val().split(" ").join("") == "") {
				toastr.error("Enter Buyer Name !!");
				isValid = false;
			}

			if ($("#type_of_product").val() == "" || $("#type_of_product").val().split(" ").join("") == "") {
				toastr.error("Enter Type Of Product Name !!");
				isValid = false;
			}

			if ($("#contact_person").val() == "" || $("#contact_person").val().split(" ").join("") == "") {
				toastr.error("Enter Contact Person !!");
				isValid = false;
			}

			if ($("#state_city").val() == "" || $("#state_city").val().split(" ").join("") == "") {
				toastr.error("Enter State City !!");
				isValid = false;
			}

			if ($("#contact_no").val() == "" || $("#contact_no").val().split(" ").join("") == "") {
				toastr.error("Enter Contact No !!");
				isValid = false;
			}

			if ($("#servicemen").val() == "" || $("#servicemen").val().split(" ").join("") == "") {
				toastr.error("Enter Sales Person !!");
				isValid = false;
			}

			if (isValid) {
				var r = confirm("Are You sure want to Save This Service Form ?");
				if (r) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	</script>
	<script type="text/javascript">
		function printReport(complain_id) {
			var myWindow = window.open('print_complain_service.php?complain_id=' + complain_id, '', 'width=500,height=800');
			myWindow.print();
		}
	</script>
	<script type="text/javascript">
		// When the new checkbox is clicked, disable the last maintenance date if it is selected.
		$("#mt_new").on('click', function() {
			$("#last_maintenance_date").prop("disabled", $(this).prop("checked"));
		});
	</script>
</body>

</html>