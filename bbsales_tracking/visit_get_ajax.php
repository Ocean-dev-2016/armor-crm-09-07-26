<?php
$page_id = 577;
$page_slug = 'visit_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "visit";
$ctable1 	= "User";

$ctable_where = "";

// print_r($_REQUEST);exit();
// Get the total number of rows in the table

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {

	// $sales_id = $db->rp_getData("sales_executive","*","name LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	// if($sales_id)
	// {		
	// 	while($K=mysqli_fetch_assoc($sales_id))
	// 	{
	// 		$USER_IDS[]=$K['id'];
	// 	}
	// 	$USER_IDS=implode(",",$USER_IDS);
	// 	$ctable_where .="user_id IN (".$USER_IDS.") ";
	// }
	// else
	// {
	// 	$ctable_where .="user_id IN (0) ";
	// }

	$customer_id = $db->rp_getData("executive", "*", " ( cname LIKE '%" . $_REQUEST['searchName'] . "%' OR phone LIKE '%" . $_REQUEST['searchName'] . "%' OR company_name LIKE '%" . $_REQUEST['searchName'] . "%' OR client_code LIKE '%" . $_REQUEST['searchName'] . "%') AND isDelete=0", "", 0);
	if ($customer_id) {
		while ($K1 = mysqli_fetch_assoc($customer_id)) {
			$CUSTOMER_IDS[] = $K1['id'];
		}
		$CUSTOMER_IDS = implode(",", $CUSTOMER_IDS);
		$ctable_where .= "  customer_id IN (" . $CUSTOMER_IDS . ") ";
	} else {
		$ctable_where .= "  customer_id IN (0)  ";
	}

	$inquiry_id = $db->rp_getData("no_order_inquiry", "*", "person_name LIKE '%" . $_REQUEST['searchName'] . "%' OR mobile_number LIKE '%" . $_REQUEST['searchName'] . "%' OR company_name LIKE '%" . $_REQUEST['searchName'] . "%' AND isDelete=0", "", 0);
	if ($inquiry_id) {
		while ($D1 = mysqli_fetch_assoc($inquiry_id)) {
			$INQID[] = $D1['id'];
		}
		$INQID = implode(",", $INQID);
		$ctable_where .= " AND  inquiry_id IN (" . $INQID . ") ";
	} else {
		$ctable_where .= "  AND  inquiry_id IN (0)  ";
	}

	$ctable_where .= " AND ";
	/*$ctable_where .= " (
							name like '%".$db->clean($_REQUEST['searchName'])."%'
							OR company_name like '%".$db->clean($_REQUEST['searchName'])."%'
							OR email like '%".$db->clean($_REQUEST['searchName'])."%'
							OR phone  LIKE '%".$db->clean($_REQUEST['searchName'])."%'
						) AND ";*/
}


$ctable_where .= " isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 100;

if (isset($_REQUEST["page"])) {
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if (!is_numeric($page_number)) {
		die('Invalid page number!');
	} //incase of invalid page number
} else {
	$page_number = 1; //if there's no page number, set it to 1
}
// print_r($_REQUEST["sales_executive"]);exit;
if (isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"] != "" && $_REQUEST["sales_executive"] != undefined) {
	$ctable_where .= " AND user_id='" . $_REQUEST["sales_executive"] . "'";
	$sid = $_REQUEST["sales_executive"];
}
if (isset($_REQUEST["visit_type"]) && $_REQUEST["visit_type"] != "" && $_REQUEST["visit_type"] != undefined) {
	$ctable_where .= " AND visit_type='" . $_REQUEST["visit_type"] . "'";
	$visit_type = $_REQUEST["visit_type"];
}
if (isset($_REQUEST["customer_id"]) && $_REQUEST["customer_id"] != "" && $_REQUEST["customer_id"] != undefined) {
	$ctable_where .= " AND customer_id='" . $_REQUEST["customer_id"] . "'";
	$cid = $_REQUEST["customer_id"];
}

if (isset($_REQUEST["company_id"]) && $_REQUEST["company_id"] != "" && $_REQUEST["company_id"] != undefined) {
	$ctable_where .= " AND type_of_company='" . $_REQUEST["company_id"] . "' ";
	$company_ids = $_REQUEST["company_id"];
}

if (isset($_REQUEST['df']) && $_REQUEST['df'] != "") {
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode($_REQUEST['df']);

	$date_filter_query_ex = explode(" to ", $date_filter_query);

	$ctable_where .= " AND ( DATE(created_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(created_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
}

if (isset($_REQUEST['visit_month']) && $_REQUEST['visit_month'] != "" && $_REQUEST['visit_month'] != NULL) {
	$ctable_where .= " AND MONTH(created_date) = '" . $_REQUEST['visit_month'] . "'";
}

if (isset($_REQUEST['visit_year']) && $_REQUEST['visit_year'] != "" && $_REQUEST['visit_year'] != NULL) {
	$ctable_where .= " AND YEAR(created_date) = '" . $_REQUEST['visit_year'] . "'";
}
if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined  && $_REQUEST['todate'] != '01-01-1970') {
	$ctable_where .= " AND DATE(created_date) >= '" . $_REQUEST['todate'] . "' ";
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined && $_REQUEST['fromdate'] != '01-01-1970') {
	$ctable_where .= " AND DATE(created_date) <= '" . $_REQUEST['fromdate'] . "' ";
}

if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
	if ($rights['personal_flag'] == 1) {
		$ctable_where .= " AND user_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
	} else {
		if ($rights['chain_vise_flag'] == 1) {
			$check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];

			$get_sales_type = $db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='" . $check_id . "'", 0);
			if ($get_sales_type == "sales_manager") {
				$sales_executive_type = "Regional Sales Manager";
				$key = "sm_id";
				$WhereCondition .= ' ' . $key . '=' . $check_id;
			} else if ($get_sales_type == "area_sales_manager") {
				$sales_executive_type = "National Sales Manager"; //Business Development Manager
				$key = "asm_id";
				$WhereCondition .= ' ' . $key . '=' . $check_id;
			} else if ($get_sales_type == "sales_officer") {
				$sales_executive_type = "Area Sales Manager"; //Area Sales Manager
				$key = "so_id";
				$WhereCondition .= ' ' . $key . '=' . $check_id;
			} else if ($get_sales_type == "sales_executive") {
				$sales_executive_type = "Sales Officer";
				$key = "se_id";
				$WhereCondition .= ' ' . $key . '=' . $check_id;
			} else {
				$WhereCondition .= ' type = "service_engineer"';
			}

			$data = $db->rp_getData("sales_executive", "id", $WhereCondition, "", 0);

			$SALEID1 = array();
			if ($data) {
				while ($data_d = mysqli_fetch_assoc($data)) {
					$SALEID1[] = $data_d['id'];
				}
			}
			if (!empty($SALEID1)) {
				$SALEID1 = implode(",", $SALEID1);
				$ctable_where .= " AND user_id IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
			} else {
				$ctable_where .= " AND user_id IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
			}
		}
	}
}

$get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where, 0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows / $item_per_page);

//get starting position to fetch the records
$page_position = (($page_number - 1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "id DESC limit $page_position, $item_per_page", 0);
?>
<style>
	.table-scrollable {
		width: auto;
		height: 600px;
		overflow-x: scroll;
		overflow-y: scroll;
		border: 1px solid #e7ecf1;
		margin: 10px 0 !important;
	}
</style>
<style type="text/css">
	.fix-th {
		background-color: #f5f5f5 !important;
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.fix-th1 {
		background-color: #e5e5e5 !important;
		position: sticky;
		top: 0;
		z-index: 1;
	}
</style>
<form action="" name="frm" id="frm" method="post">
	<div class="table-scrollable">
		<table id="datatable_1" class="table table-striped table-bordered table-hover">
			<thead class="fix-th">
				<tr>
					<th style="width: 5%;"></th>
					<th>
						<select class="form-control input-small" name="sales_executive" id="sales_executive">
							<option value="">Select Sales Person</option>
							<!-- <?php
									$D_r = $db->rp_getData("sales_executive", "id,name", "", "", 0);
									while ($D = mysqli_fetch_assoc($D_r)) {
									?>
	                				<option value="<?= $D['id'] ?>" <?= ($sid == $D['id']) ? "selected" : ""; ?>><?= $D['name'] ?></option>
	                				<?php
									}
									?> -->
							<?php
							if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
								$whereCustom = "";
								$whereCustom = "isDelete=0 AND isActive=1";

								if ($rights['personal_flag'] == 1) {
									$whereCustom .= " AND id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
								} else {
									if ($rights['chain_vise_flag'] == 1) {
										$exeType = $db->rp_getValue("sales_executive", "type", "id = '" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'");
										// echo $exeType;
										if ($exeType == 'sales_manager') {
											$whereCustom .= " AND (sm_id = '" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "' OR id = '" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "')";
										} else if ($exeType == 'area_sales_manager' || $exeType == 'dispatch_sales_manager') {
											$whereCustom .= " AND (asm_id = '" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "' OR id = '" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "')";
										} else if ($exeType == 'sales_officer') {
											$whereCustom .= " AND (so_id = '" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "' OR id = '" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "')";
										} else {
											$whereCustom .= " AND id = '" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
										}
									}
								}
							}

							$D_r = $db->rp_getData('sales_executive', "*", $whereCustom, "", 0);
							while ($D = mysqli_fetch_assoc($D_r)) {
							?>
								<option
									<?= ($D['id'] == $_SESSION[SITE_SESS . 'REFERANCE_ID']) ? "selected" : ""; ?>
									<?php echo ($sid == $D['id']) ? "selected" : ""; ?> value="<?php echo $D['id'] ?>">
									<?php echo $D['name']; ?>
								</option>
							<?php
							}
							?>
						</select>
					</th>
					<th>
						<select class="form-control input-small" name="company_id" id="company_id">
							<option value="">Select Company</option>
							<?php
							$company_r = $db->rp_getData("company_master", "id,name", "isDelete=0", "", 0);
							while ($company_d = mysqli_fetch_assoc($company_r)) {
							?>
								<option value="<?= $company_d['id'] ?>" <?= ($company_ids == $company_d['id']) ? "selected" : ""; ?>>
									<?= $company_d['name'] ?>
								</option>
							<?php
							}
							?>
						</select>
					</th>
					<th>
						<select class="form-control input-small" name="customer_id" id="customer_id">
							<option value="">Select Customer</option>
							<?php
							$E_r = $db->rp_getData("executive", "id,cname,company_name", "isDelete=0", "cname ASC", 0);
							while ($E = mysqli_fetch_assoc($E_r)) {
							?>
								<option value="<?= $E['id'] ?>" <?= ($cid == $E['id']) ? "selected" : ""; ?>><?= $E['company_name'] ?> - <?= $E['cname'] ?></option>
							<?php
							}
							?>
						</select>
					</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>
						<!-- <label>Filter By Date</label> -->
						<div class="input-group">
							<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
							<span class="input-group-addon datetimerange-picker-btn">
								<i class="fa fa-calendar"></i>
							</span>

							<span class="input-group-btn">
								<!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
							</span>
						</div>
						<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
					</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>
						<select class="form-control input-small" name="visit_type" id="visit_type">
							<option value="">Select Visit Type</option>
							<option <?= $visit_type == 1 ? "selected" : "" ?> value="1">Existing Customer</option>
							<option <?= $visit_type == 3 ? "selected" : "" ?> value="3">Inquiry</option>
							<option <?= $visit_type == 4 ? "selected" : "" ?> value="4">New Customer</option>
						</select>
					</th>
					<th></th>
				</tr>
				<tr>
					<th>No.</th>
					<th>Sales<br /> Person Name</th>
					<th>Company</th>
					<th>Company Name</th>
					<th>Person Name</th>
					<th>Client Code</th>
					<th>Customer<br /> Mobile No.</th>
					<th>Customer<br /> Email</th>
					<th>Customer<br /> Gst</th>
					<th>Customer<br /> Turn Over</th>
					<th>Customer<br /> Turn Year</th>
					<th>Date and Time</th>
					<th>Visit Purpose</th>
					<th>Customer<br /> Person Name</th>
					<th>Customer <br />Person Mobile No. </th>
					<th>Customer <br />Person Email ID </th>
					<th>Customer <br />Person Designation </th>
					<th>Visit Start <br /> Address</th>
					<th>Visit Stop <br /> Address</th>
					<th>Customer<br /> Address</th>
					<th>Visit Start <br /> Remark</th>
					<th>Visit Start <br /> Location Map</th>
					<th>Visit Start <br /> Image</th>
					<th>Visit Start <br /> Time</th>
					<th>Visit Stop <br /> Remark / Reason</th>
					<th>Visit Stop <br /> Location Map</th>
					<th>Visit Stop <br /> Image</th>
					<th>Visit Stop <br /> Time</th>
					<th>Purchasing From</th>
					<th>Total Time</th>
					<th>Visit Type</th>
					<th>Visit Stop<br />Flag</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if (mysqli_num_rows($ctable_r) > 0) {
					$count = 0;
					while ($ctable_d = mysqli_fetch_array($ctable_r)) {
						$datetime1 = new DateTime($ctable_d['stop_date_time']);
						$datetime2 = new DateTime($ctable_d['start_date_time']);
						$interval = $datetime1->diff($datetime2);
						$elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
						$customer_email = "";
						$customer_turn_over = "";
						$customer_turn_year = "";
						$customer_gst = "";
						$customer_address = "";
						$company_name = "";
						$customer_flag = "";
						$cname = "";
						$client_code = "";
						$mobile_no1 = "";

						if ($ctable_d['visit_stop_flag'] == 4) {
						} else if ($ctable_d['customer_id'] == 0 && $ctable_d['inquiry_id'] != "0") {
						} else {
							$customer_detail_get = $db->rp_getData("executive", "gst,email,turnover,turnover_year,address,company_name,customer_flag,cname,client_code,mobile_no1", "isDelete=0 AND id = '" . $ctable_d['customer_id'] . "' ");
							$customer_detail_get_d = mysqli_fetch_assoc($customer_detail_get);
							$customer_email = $customer_detail_get_d['email'];
							$customer_turn_over = $customer_detail_get_d['turnover'];
							$customer_turn_year = $customer_detail_get_d['turnover_year'];
							$customer_gst = $customer_detail_get_d['gst'];
							$customer_address = $customer_detail_get_d['address'];
							$company_name = $customer_detail_get_d['company_name'];
							$customer_flag = $customer_detail_get_d['customer_flag'];
							$cname = $customer_detail_get_d['cname'];
							$client_code = $customer_detail_get_d['client_code'];
							$mobile_no1 = $customer_detail_get_d['mobile_no1'];
						}
				?>
						<tr>
							<td><?php echo ++$count; ?></td>
							<td>
								<span class="<?php echo ($ctable_d['isActive'] == 0) ? "text-danger" : "text-success"; ?>">
									<?php echo $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['user_id'] . "'") ?>
								</span>
							</td>
							<td>
								<?php

								echo $db->rp_getValue("company_master", "name", "id='" . $ctable_d['type_of_company'] . "'", 0);
								?>
							</td>
							<td>
								<span class="<?php echo ($ctable_d['isActive'] == 0) ? "text-danger" : "text-success"; ?>">
									<?php
									if ($ctable_d['visit_stop_flag'] == 4) {
										echo $ctable_d['firm_name'];
									} else if ($ctable_d['customer_id'] == 0 && $ctable_d['inquiry_id'] != "0") {
										echo $db->rp_getValue("no_order_inquiry", "company_name", "id='" . $ctable_d['inquiry_id'] . "'", 0);
									} else {

										$customer_flag_text = "";
										if ($customer_flag == 1) {
											$customer_flag_text = " - P";
										} else if ($customer_flag == 0) {
											$customer_flag_text = " - C";
										}
										echo $company_name . $customer_flag_text;
									}
									?>
								</span>
							</td>
							<td>
								<span class="<?php echo ($ctable_d['isActive'] == 0) ? "text-danger" : "text-success"; ?>">
									<?php
									if ($ctable_d['visit_stop_flag'] == 4) {
										echo $ctable_d['contact_person'] ?:  '-'; // Assuming 'contact_person' field exists; replace with actual field if different
									} else if ($ctable_d['customer_id'] == 0 && $ctable_d['inquiry_id'] != "0") {
										echo $db->rp_getValue("no_order_inquiry", "person_name", "id='" . $ctable_d['inquiry_id'] . "'", 0);
									} else {
										echo $cname;
									}
									?>
								</span>
							</td>
							<td>
								<span class="<?php echo ($ctable_d['isActive'] == 0) ? "text-danger" : "text-success"; ?>">
									<?php
									if ($ctable_d['visit_stop_flag'] == 4) {
									} else if ($ctable_d['customer_id'] == 0 && $ctable_d['inquiry_id'] != "0") {
									} else {
										echo $client_code;
									}
									?>
								</span>
							</td>
							<td>
								<?php
								if ($ctable_d['visit_stop_flag'] == 4) {
									echo $ctable_d['contact_number'];
								} else if ($ctable_d['customer_id'] == 0 && $ctable_d['inquiry_id'] != "0") {
									echo $db->rp_getValue("no_order_inquiry", "mobile_number", "id='" . $ctable_d['inquiry_id'] . "'");
								} else {
									echo $mobile_no1;
								}
								?>
							</td>
							<td><?= $customer_email ?></td>
							<td><?= $customer_gst ?></td>
							<td><?= $customer_turn_over ?></td>
							<td><?= $customer_turn_year ?></td>
							<td><?php echo date("d-m-Y H:i:s", strtotime($ctable_d['created_date'])); ?></td>
							<td><?php echo $db->rp_getValue("purpose_master", "name", "isDelete=0 AND id=" . $ctable_d['purpose_id'], 0); ?></td>
							<td><?php echo $ctable_d['name']; ?></td>
							<td><?php echo $ctable_d['mobile_no']; ?></td>
							<td><?php echo $ctable_d['email_id']; ?></td>
							<td><?php echo $db->rp_getValue("visit_designation", "name", "isDelete=0 AND id = '" . $ctable_d['designation'] . "' "); ?></td>
							<td><?php echo $ctable_d['app_address']; ?></td>
							<td><?php echo $ctable_d['stop_app_address']; ?></td>
							<td>
								<?php
								if ($ctable_d['visit_stop_flag'] == 4) {
								} else if ($ctable_d['customer_id'] == 0 && $ctable_d['inquiry_id'] != "0") {
								} else {
									echo $customer_address;
								}
								?>
							</td>
							<td><?php echo stripslashes($ctable_d['remark']); ?></td>
							<td>
								<!-- Trigger the modal with a button -->
								<a class="mapbtn" data-app_address="<?php echo stripslashes($ctable_d['app_address']); ?>" data-lat="<?php echo stripslashes($ctable_d['latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['longitude']); ?>" data-date="<?= date("d M H:i", strtotime($ctable_d['created_date'])); ?>" data-salesexename="<?= $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d["user_id"] . "'", 0); ?>" data-toggle="modal" data-target="#OpenMap">
									<img src="<?= SITEURL ?>resource/map.png" style="height: 80px;">
								</a>
							</td>
							<td>
								<?php
								$img = explode(",", $ctable_d['image_path']);
								$imgpath = array();
								for ($i = 0; $i < sizeof($img); $i++) {
									$imgpath[] = SITEURL . "resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $ctable_d["id"] . "' AND id='" . $img[$i] . "'", 0);
								}
								for ($i = 0; $i < sizeof($imgpath); $i++) {
									if ($i == 0) {
								?>
										<a href="<?= $imgpath[$i] ?>" data-lightbox="visit<?= $count ?>" data-title="visit <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 80px;"></a>
									<?php
									} else {
									?>
										<div class="hidden">
											<a href="<?= $imgpath[$i] ?>" data-lightbox="visit<?= $count ?>" data-title="visit <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 80px;"></a>
										</div>
								<?php
									}
								}
								?>
							</td>
							<td><?php if ($ctable_d['start_date_time'] != "0000-00-00 00:00:00") {
									echo date('d-m-Y h:i A', strtotime($ctable_d['start_date_time']));
								} else {
									echo "";
								} ?></td>
							<td><?php echo stripslashes($ctable_d['stop_remark']);
								/* Show Consultant Detail form under Visit Stop Remark */
								if (!empty($ctable_d['consultant_form_id']) || (!empty($ctable_d['remark_code']) && $ctable_d['remark_code'] == 'C')) {
									$vcf = $db->rp_getData("visit_consultant_form", "*", "visit_id='" . $ctable_d['id'] . "' AND isDelete=0", "id DESC", 0);
									if ($vcf) {
										$vf = mysqli_fetch_assoc($vcf);
										$typeLabel = (isset($vf['consultant_type']) && $vf['consultant_type'] == "government") ? "Government Consultant" : "Private Consultant";
										echo '<div style="margin-top:6px;padding:6px;border:1px solid #ddd;background:#f9f9f9;font-size:12px;line-height:1.5;">';
										echo '<b>Consultant Detail (' . htmlspecialchars($typeLabel) . ')</b><br>';
										echo '<b>Firm Name:</b> ' . htmlspecialchars($vf['firm_name']) . '<br>';
										echo '<b>Address:</b> ' . nl2br(htmlspecialchars($vf['address'])) . '<br>';
										echo '<b>City:</b> ' . htmlspecialchars($vf['city']) . ' &nbsp; <b>State:</b> ' . htmlspecialchars($vf['state']) . ' &nbsp; <b>Pincode:</b> ' . htmlspecialchars($vf['pincode']) . '<br>';
										echo '<b>Contact Person:</b> ' . htmlspecialchars($vf['contact_person']) . '<br>';
										echo '<b>Mo:</b> ' . htmlspecialchars($vf['mobile']);
										if (!empty($vf['email'])) {
											echo ' &nbsp; <b>Mail ID:</b> ' . htmlspecialchars($vf['email']);
										}
										if (!empty($ctable_d['visit_followup_id']) && $ctable_d['visit_followup_id'] != '0') {
											echo '<br><span class="label label-info">Follow-up #' . htmlspecialchars($ctable_d['visit_followup_id']) . '</span>';
										}
										echo '</div>';
									}
								}
							?></td>
							<td>
								<!-- Trigger the modal with a button -->
								<?php if ($ctable_d['stop_longitude'] != "") {
								?>
									<a class="mapbtn1" data-app_address="<?php echo stripslashes($ctable_d['stop_app_address']); ?>" data-lat="<?php echo stripslashes($ctable_d['stop_latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['stop_longitude']); ?>" data-date="<?= date("d M H:i", strtotime($ctable_d['created_date'])); ?>" data-salesexename="<?= $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d["user_id"] . "'", 0); ?>" data-toggle="modal" data-target="#OpenMap1">
										<img src="<?= SITEURL ?>resource/map.png" style="height: 80px;">
									</a>
								<?php
								} ?>

							</td>
							<td>
								<?php
								if ($ctable_d['stop_date_time'] != "0000-00-00 00:00:00") {
									$img = explode(",", $ctable_d['stop_image_path']);
									$imgpath = array();
									for ($i = 0; $i < sizeof($img); $i++) {
										$imgpath[] = SITEURL . "resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $ctable_d["id"] . "' AND id='" . $img[$i] . "'", 0);
									}
									for ($i = 0; $i < sizeof($imgpath); $i++) {
										if ($i == 0) {
								?>
											<a href="<?= $imgpath[$i] ?>" data-lightbox="visit<?= $count ?>" data-title="visit <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 80px;"></a>
										<?php
										} else {
										?>
											<div class="hidden">
												<a href="<?= $imgpath[$i] ?>" data-lightbox="visit<?= $count ?>" data-title="visit <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 80px;"></a>
											</div>
								<?php
										}
									}
								}
								?>
							</td>
							<td><?php if ($ctable_d['stop_date_time'] != "0000-00-00 00:00:00") {
									echo date('d-m-Y h:i A', strtotime($ctable_d['stop_date_time']));
								} else {
									echo "";
								} ?></td>
							<td><?php echo $ctable_d['product_name']; ?></td>
							<td>
								<?php
								if ($ctable_d['stop_date_time'] != "0000-00-00 00:00:00") {
									echo $elapsed;
								}
								?>
							</td>

							<td>
								<?php
								if ($ctable_d['visit_type'] == "1") {
									echo "Existing Customer";
								} else if ($ctable_d['visit_type'] == "3") {
									echo "Inquiry";
								} else if ($ctable_d['visit_type'] == "4") {
									echo "New Customer";
								} else {
									echo " ";
								}
								?>
							</td>
							<?php
							if ($ctable_d['visit_stop_flag'] == "1") {
								$order_no = $db->rp_getValue("orders", "order_no", "customer_id='" . $ctable_d['customer_id'] . "' AND DATE(created_date)='" . date('Y-m-d', strtotime($ctable_d['stop_date_time'])) . "' AND sales_id=" . $ctable_d['user_id']);
							}
							if ($order_no == "" && $ctable_d['visit_stop_flag'] == "1") {
								$style = "style='background-color: #f1acac;'";
							}


							if ($ctable_d['visit_stop_flag'] == "3") {
								if (isset($ctable_d['visit_followup_id']) && $ctable_d['visit_followup_id'] != "" && $ctable_d['visit_followup_id'] != "0") {
									$followp = $ctable_d['visit_followup_id'];
								} else {
									$followp = $db->rp_getValue("followup", "id", "visitor_id='" . $ctable_d['customer_id'] . "' AND reference_id='" . $ctable_d['inquiry_id'] . "' AND DATE(created_date)='" . date('Y-m-d', strtotime($ctable_d['stop_date_time'])) . "' AND user_id=" . $ctable_d['user_id'], 0);
									if ($followp == "" && $ctable_d['customer_id'] != "" && $ctable_d['customer_id'] != "0") {
										$followp = $db->rp_getValue("followup", "id", "(visitor_id='" . $ctable_d['customer_id'] . "' OR reference_id='" . $ctable_d['customer_id'] . "') AND DATE(created_date)='" . date('Y-m-d', strtotime($ctable_d['stop_date_time'])) . "' AND user_id='" . $ctable_d['user_id'] . "' AND isDelete=0", 0);
									}
								}
							}
							if ($followp == "" && $ctable_d['visit_stop_flag'] == "3") {
								$style = "style='background-color: #f1acac;'";
							}
							?>
							<td <?php
								if ($ctable_d['visit_stop_flag'] == "1" && $order_no == "") {
									echo $style;
								} else if ($ctable_d['visit_stop_flag'] == "3" && $followp == "") {
									echo $style;
								}
								?>>
								<?php
								if ($ctable_d['visit_stop_flag'] == "1") {
									echo "Create Order<br/>" . $order_no;
								} else if ($ctable_d['visit_stop_flag'] == "2") {
									echo "Stop Visit With Edit Inquiry";
								} else if ($ctable_d['visit_stop_flag'] == "3") {
									echo "Create Followup";
								}
								?>
							</td>
						</tr>
				<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<!-- Modal -->
	<div id="OpenMap" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width: 970px;">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Visit</h4>
				</div>
				<div class="modal-body">
					<div id="map_canvas"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>

		</div>
	</div>
	<!-- Modal -->
	<!-- End Modal -->
	<div id="OpenMap1" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width: 970px;">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Visit</h4>
				</div>
				<div class="modal-body">
					<div id="map_canvas1"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>

		</div>
	</div>
	<!-- end Modal -->
</form>
<div class="row">
	<div class="col-md-6">
		<div class="dataTables_info"> Rows Limit:
			<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
				<option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {
										echo ' selected="selected"';
									}  ?>>500</option>
				<option value="1000" <?php if ($_REQUEST["show"] == 1000) {
											echo ' selected="selected"';
										}  ?>>1000</option>
				<option value="2000" <?php if ($_REQUEST["show"] == 2000) {
											echo ' selected="selected"';
										}  ?>>2000</option>
				<option value="5000" <?php if ($_REQUEST["show"] == 5000) {
											echo ' selected="selected"';
										}  ?>>5000</option>
			</select>
		</div>
	</div>
	<div class="col-md-6">
		<div class="dataTables_paginate paging_simple_numbers">
			<ul class="pagination">
				<?php
				echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
				?>
			</ul>
		</div>
	</div>
</div>

<div id="myModal" class="modal">
	<span class="close1" onclick='$("#myModal").css("display","none");'>&times;</span>
	<img class="modal-content" style="height: 80%;width: auto;" id="img01">
</div>
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>
<script async defer
	src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4&callback=initMap">
</script>
<script type="text/javascript">
	$(".mapbtn").click(function() {
		var date = $(this).data("date");
		var salesexename = $(this).data("salesexename");
		var lat = $(this).data("lat");
		var lng = $(this).data("long");
		var app_address = $(this).data("app_address");
		//alert(app_address);
		$.ajax({
			url: "get_visit_map.php",
			data: {
				lat: lat,
				lng: lng,
				date: date,
				app_address: app_address,
				salesexename: salesexename,
			},
			beforeSend: function() {
				$("#map_canvas").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
			},
			success: function(result) {
				$("#map_canvas").html(result);
			}
		});
	});
</script>

<script type="text/javascript">
	$(".mapbtn1").click(function() {
		var date = $(this).data("date");
		var salesexename = $(this).data("salesexename");
		var lat = $(this).data("lat");
		var lng = $(this).data("long");
		var app_address = $(this).data("app_address");
		$.ajax({
			url: "get_visit_map_end.php",
			data: {
				lat: lat,
				lng: lng,
				date: date,
				app_address: app_address,
				salesexename: salesexename,
			},
			beforeSend: function() {
				$("#map_canvas1").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
			},
			success: function(result) {
				$("#map_canvas1").html(result);
			}
		});
	});
</script>
<script type="text/javascript">
	$("#sales_executive").select2();
	$("#customer_id").select2();
	$("#company_id").select2();
	$("#visit_type").select2();
</script>
<script type="text/javascript">
	$(".filterBtn").on("click", function() {
		sales_executive = $("#sales_executive").val();
		customer_id = $("#customer_id").val();
		df1 = $("#material_request_filter_input").val();
		df1 = encodeURI(df1)
		displayRecords(100, 1);
	})
	$(".datetimerange-picker-btn").on("click", function() {
		$(".datetimerange-picker-input", $(this).closest(".date")).focus();
	});
	$(".datetimerange-picker-input").daterangepicker({
		"format": "dd-mm-yy ",
		autoUpdateInput: false,
		timePicker: false,
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		}
	});
	$('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
		$(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY') + " to " + picker.endDate.format('DD-MM-YYYY'));
	});
</script>