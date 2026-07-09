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
$buyerName = $complainComplainCustomer_d['cname'];
$buyerCompanyName = $complainComplainCustomer_d['company_name'];
$contact_no = $complainComplainCustomer_d['phone'];
$buyer_address = $complainComplainCustomer_d['address'];
$client_code = $complainComplainCustomer_d['client_code'];
$buyerState = $complainComplainCustomer_d['state'];
$buyerCity = $complainComplainCustomer_d['main_city'];
$buyerContactNo = $complainComplainCustomer_d['mobile_no1'];
$contactPersonId = $complainComplainCustomer_d['seid'];
$contactPersonName = $db->rp_getValue("sales_executive", "name", "isDelete=0 AND id='" . $contactPersonId . "'");
/* Customer Data Get */

$Servicemen = $db->rp_getValue("sales_executive", "GROUP_CONCAT(name)", "id IN(" . $complain_assign_to . ") AND isDelete=0", 0);

/* Service Data Get */
$GetService_R = $db->rp_getData("complain_service", "*", "complain_id='" . $_REQUEST['complain_id'] . "' AND isDelete=0", "", 0);
$GetService_D = mysqli_fetch_assoc($GetService_R);

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

$mt_fire_hydrant 		= ($GetService_D['mt_fire_hydrant']) ? "checked" : "";
$mt_rrl 						= ($GetService_D['mt_rrl']) ? "checked" : "";
$mt_hose_reel_drum 	= ($GetService_D['mt_hose_reel_drum']) ? "checked" : "";
$mt_branch_pipe 		= ($GetService_D['mt_branch_pipe']) ? "checked" : "";
$mt_inlet 					= ($GetService_D['mt_inlet']) ? "checked" : "";
$mt_new 						= ($GetService_D['mt_new']) ? "checked" : "";
$lastMaDate 				= ($GetService_D['mt_new']) ? "disabled" : "";
/* Company Master Data Get */
?>
<style>
	.mainDiv,
	table {
		border: 1px solid #595959;
		border-collapse: collapse;
		font-size: 13px;
		font-family: system-ui;
		width: 250mm !important;
		background-color: #FFF;
		margin: auto;
		padding: auto;
	}

	table,
	td,
	th {
		border: 1px solid #595959;
	}

	td,
	th {
		padding: 5px;
		height: 15px;
	}


	.text-center {
		text-align: center !important;
	}

	.text-right {
		text-align: right !important;
	}

	.no-border-left {
		border-left: hidden;
	}

	.no-border-right {
		border-right: hidden;
	}

	.no-border-bottom {
		border-bottom: hidden !important;
	}

	.no-border-top {
		border-top: hidden !important;
	}

	.border td {
		border-bottom: hidden !important;
	}

	.color {
		background: #fffff;
	}

	/*.font-size td
{
	font-size: 15px!important;
}*/
	.font-size {
		font-size: 15px;
	}

	.image-width {
		width: 10% !important;
		min-width: 10% !important;
		max-width: 10% !important;
	}

	.border-r-width {
		border-right-width: 5px;
	}

	.border-gray {
		border-right-color: #E5E5E5;
	}

	.border-blue {
		border-right-color: <?= VIEW_COLOR ?>;
	}

	.vertical-top {
		vertical-align: top;
	}

	.height-5 {
		height: 5px;
	}

	.bg-gray {
		background-color: #E5E5E5 !important;
	}

	.font-13 {
		font-size: 13px !important;
	}

	.headerBorder {
		border: 22px solid #eb268f;
		border-bottom: none;
		border-right: none;
		border-left: none;
	}

	.bolde-style {
		font-weight: bold;
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
</style>
<table>
	<tbody>

		<tr>
			<td colspan="16" class="font-size" style="text-align: center;">
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
			<td colspan="16" class="main-title color font-size" align="center" style="height: 60px; font-size: 30px;">
				<span><strong>Product Inspection</strong></span>
			</td>
		</tr>
		<tr>
			<td colspan="4" class="color font-size" align="center"><strong>Sr. No. </strong></td>
			<td colspan="4" class="color font-size" align="center">
				<?= $GetService_D['sr_no']; ?>
			</td>
			<td colspan="4" class="color font-size" align="center"><strong>Date</strong></td>
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
			<td colspan="4" class="color font-size" align="center">
				<?= $service_date ?>
			</td>
		</tr>


		<!-- Row 1 -->
		<tr>
			<!-- Buyer Name -->
			<td colspan="4" class="color font-size" align="center"><strong>Buyer Name </strong></td>
			<td colspan="4" class="color font-size" align="center">
				<?= $buyerName . " - " . $buyerCompanyName . " - " . $client_code ?>
			</td>
			<!-- Type of Product -->
			<td colspan="4" class="color font-size" align="center"><strong>Type of Product </strong></td>
			<td colspan="4" class="color" align="left">
				<ul>
					<?php
					for ($psc = 0; $psc < sizeof($productSubCatIdsArr); $psc++) {
					?>
						<li><?= $db->rp_getValue("category_master", "name", "isDelete=0 AND id='" . $productSubCatIdsArr[$psc] . "'"); ?></li>
					<?php
					}
					?>
				</ul>
			</td>
		</tr>

		<!-- Row 2 -->
		<tr>
			<!-- Address -->
			<td colspan="4" class="color font-size" align="center"><strong>Address</strong></td>
			<td colspan="4" class="color font-size" align="center">
				<?= $buyer_address; ?>
			</td>
			<!-- Product -->
			<td colspan="4" class="color font-size" align="center"><strong>Product</strong></td>
			<td colspan="4" class="color" align="left">
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
			</td>
		</tr>


		<!-- Row 3 -->
		<tr>
			<!-- Contact Person -->
			<td colspan="4" class="color font-size" align="center"><strong>Contact Person </strong></td>
			<td colspan="4" class="color font-size" align="center">
				<?= $contactPersonName; ?>
			</td>
			<!-- Invoice No./Date -->
			<td colspan="4" class="color font-size" align="center"><strong>Invoice No./Date</strong></td>
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
				<span><?= $GetService_D['invoice_no']; ?></span>
				<br>
				<span><?= $invoice_date; ?></span>
			</td>
		</tr>

		<!-- Row 4 -->
		<tr>
			<!-- State/City -->
			<td colspan="4" class="color font-size" align="center"><strong>State/City </strong></td>
			<td colspan="4" class="color font-size" align="center">
				<?= $buyerState . " / " . $buyerCity ?>
			</td>

			<!-- Sales Person -->
			<td colspan="4" class="color font-size" align="center"><strong>Sales Person </strong></td>
			<td colspan="4" class="color font-size" align="center">
				<?= $Servicemen ?>
			</td>
		</tr>


		<!-- Row 5 -->
		<tr>
			<!-- Contact No. -->
			<td colspan="4" class="color font-size" align="center"><strong>Contact No. </strong></td>
			<td colspan="4" class="color font-size" align="center">
				<?= $buyerContactNo ?>
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
			<td colspan="16" class="color font-size" align="center" style="height: 40px; background-color: #E5E5E5;"><strong>Test Details</strong></td>
		</tr>

		<!-- Row 7 -->
		<tr>
			<!-- Site Name -->
			<td colspan="4" class="color font-size" align="center"><strong>Site Name</strong></td>
			<td colspan="12" class="color font-size" align="left">
				<?= $GetService_D['site_name'] ?>
			</td>
		</tr>

		<!-- Row 8 -->
		<tr>
			<!-- Site Address -->
			<td colspan="4" class="color font-size" align="center"><strong>Site Address</strong></td>
			<td colspan="12" class="color font-size" align="left">
				<?= $GetService_D['site_address'] ?>
			</td>
		</tr>

		<!-- Row 9 -->
		<tr>
			<!-- Contractor -->
			<td colspan="4" class="color font-size" align="center"><strong>Government Office</strong></td>
			<td colspan="12" class="color font-size" align="left">
				<?= $GetService_D['contractor'] ?>
			</td>
		</tr>

		<!-- Row 10 -->
		<tr>
			<!-- Test Details -->
			<td colspan="4" class="color font-size" align="center"><strong>Test Details</strong></td>
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
			<td colspan="4" class="color font-size" align="left">
				<!-- Test Date -->
				<div><strong>Test Date: </strong>
					<?= $test_date ?>
				</div>
				<br>
				<!-- Tested Pressure -->
				<div><strong>Tested Pressure KGF/CM<sup>2</sup>:</strong>
					<?= $GetService_D['tested_pressure'] ?>
				</div>
				<br>
				<!-- Issues in Testing -->
				<?php
				if ($GetService_D['is_issues_testing'] == 1) {
					$is_issues_testing_checked = "YES";
				} else {
					$is_issues_testing_checked = "NO";
				}
				?>
				<div>
					<strong>Issues in Testing: </strong>

					<strong><?= $is_issues_testing_checked; ?></strong>
				</div>
			</td>
			<!-- Maintenance Test -->
			<td colspan="2" class="color font-size" align="center"><strong>Maintenance Test: </strong></td>

			<td colspan="6" class="color font-size" align="left">
				<!-- Maintenance Test Options -->
				<div style="display: flex; justify-content: space-evenly;">
					<div class="section">
						<h2>Annual</h2>
						<label class="checkbox-label">
							Fire Hydrant <input disabled type="checkbox" name="mt_fire_hydrant" id="mt_fire_hydrant" value="1" class="align-checkbox-css-input" <?= $mt_fire_hydrant; ?>>
						</label>
						<label class="checkbox-label">
							RRL <input disabled type="checkbox" name="mt_rrl" value="1" id="mt_rrl" class="align-checkbox-css-input" <?= $mt_rrl; ?>>
						</label>
						<label class="checkbox-label">
							Hose Reel Drum <input disabled type="checkbox" name="mt_hose_reel_drum" id="mt_hose_reel_drum" value="1" class="align-checkbox-css-input" <?= $mt_hose_reel_drum; ?>>
						</label>
						<label class="checkbox-label">
							Branch Pipe <input disabled type="checkbox" name="mt_branch_pipe" id="mt_branch_pipe" value="1" class="align-checkbox-css-input" <?= $mt_branch_pipe; ?>>
						</label>
						<label class="checkbox-label">
							Inlet <input disabled type="checkbox" name="mt_inlet" id="mt_inlet" value="1" class="align-checkbox-css-input" <?= $mt_inlet; ?>>
						</label>
					</div>

					<div class="section" style="text-align: center;">
						<h2>New</h2>
						<label class="checkbox-label">
							<input disabled type="checkbox" name="mt_new" value="1" id="mt_new" class="align-checkbox-css-input" <?= $mt_new; ?>>
						</label>
					</div>
				</div>
			</td>
		</tr>

		<!-- Row 11 -->
		<tr>
			<!-- Remarks -->
			<td colspan="4" class="color font-size" align="center"><strong>Remarks: </strong></td>
			<td colspan="4" class="color font-size" align="left">
				<?= $GetService_D['remark'] ?>
			</td>
			<!-- Last Maintenance Date -->
			<td colspan="2" class="color font-size" align="center"><strong>Last Maintenance Date: - </strong></td>
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
				<?= $last_maintenance_date ?>
			</td>
		</tr>

		<!-- Row 12 -->
		<tr>
			<td colspan="16" class="color font-size" align="center" style="height: 40px; background-color: #E5E5E5;"><strong>Current Date Observation</strong></td>
		</tr>

		<!-- Row 13 -->
		<tr>
			<!-- Product Type -->
			<td colspan="4" class="color font-size" align="center"><strong>Product Type</strong></td>
			<td colspan="12" class="color font-size" align="left">
				<?= $GetService_D['product_type'] ?>
			</td>
		</tr>

		<!-- Row 14 -->
		<tr>
			<!-- Specifications -->
			<td colspan="4" class="color font-size" align="center"><strong>Specifications</strong></td>
			<td colspan="12" class="color font-size" align="left">
				<?= $GetService_D['specifications'] ?>
			</td>
		</tr>


		<!-- Row 15 -->
		<tr>
			<!-- Root of Issue -->
			<td colspan="4" class="color font-size" align="center"><strong>Root of Issue</strong></td>
			<td colspan="12" class="color font-size" align="left">
				<?= $GetService_D['root_of_issue'] ?>
			</td>
		</tr>

		<!-- Row 16 -->
		<tr>
			<!-- Current Scenario -->
			<td colspan="4" class="color font-size" align="center"><strong>Current Scenario: -</strong></td>
			<td colspan="12" class="color font-size" align="left">
				<?= $GetService_D['current_scenario'] ?>
			</td>
		</tr>

		<!-- Row 17 -->
		<tr>
			<!-- Conclusion -->
			<td colspan="4" class="color font-size" align="center"><strong>Conclusion:</strong></td>
			<td colspan="12" class="color font-size" align="left">
				<?= $GetService_D['conclusion'] ?>
			</td>
		</tr>

		<!-- Row 18 -->
		<tr>
			<td colspan="16" class="color font-size" align="left">
				<!-- Resolution -->
				<div>
					<strong>Resolution: </strong>

					<?php
					if ($GetService_D['resolution'] == 1) {
						$MaintenanceChecked = "Maintenance";
						$ReplacementChecked = "";
					} else if ($GetService_D['resolution'] == 2) {
						$MaintenanceChecked = "";
						$ReplacementChecked = "Replacement";
					} else {
						$MaintenanceChecked = "";
						$ReplacementChecked = "";
					}
					?>


					<label class="form-check-label" for="inlineCheckbox1"><?= $MaintenanceChecked ?></label>


					<label class="form-check-label" for="inlineCheckbox2"><?= $ReplacementChecked ?></label>

				</div>
			</td>
		</tr>
	</tbody>
</table>

<table style="font-family: math;">
	<tbody>
		<tr height="80px;">
			<!-- Observer Section -->
			<td colspan="8" class="font-size">
				<div style="margin: 15px;"><strong>Observer Name: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</strong> </div>
				<br>
				<div style="margin: 15px;"><strong>Company Person Sign: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</strong> </div>
				<br>
				<div style="margin: 15px;"><strong>Date: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</strong> </div>
			</td>

			<!-- Client Section -->
			<td colspan="8" align="right" class=" no-border-left font-size">
				<div style="margin: 15px;"><strong>Client Name: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</strong> </div>
				<br>
				<div style="margin: 15px;"><strong>Client Seal / Sign: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</strong> </div>
				<br>
				<div style="margin: 15px;"><strong>Date: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</strong> </div>
			</td>
		</tr>
	</tbody>
</table>