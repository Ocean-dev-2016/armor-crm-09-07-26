<?php
$page_id = 565;
$page_slug = 'page_order';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "orders";
$ctable1 	= "Orders";
$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : "";
$order_type = isset($_REQUEST['order_type']) ? $_REQUEST['order_type'] : "";
$type = "";
$ctable_where = "";
// echo "<pre>";
// print_r($_S);die;
// Get the total number of rows in the table
if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
	$ctable_where .= " (customer_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR company_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR order_no like '%" . $db->clean($_REQUEST['searchName']) . "%' OR client_code like '%" . $db->clean($_REQUEST['searchName']) . "%') AND ";
}

if (isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id'] != "") {
	$ctable_where .= "  sales_id = '" . $db->clean($_REQUEST['sales_executive_id']) . "' AND";
}
if (isset($_REQUEST['qid']) && $_REQUEST['qid'] != "" && $_REQUEST['qid'] != 'undefined') {
	$ctable_where .= "  quotation_id = '" . $db->clean($_REQUEST['qid']) . "' AND";
}

if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "") {
	$ctable_where .= "  customer_type = '" . $db->clean($_REQUEST['customer_type']) . "' AND";
}
if (isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company'] != "") {
	$ctable_where .= "  type_of_company = '" . $db->clean($_REQUEST['type_of_company']) . "' AND";
}
if (isset($_REQUEST['company_name']) && $_REQUEST['company_name'] != "") {
	$ctable_where .= "  customer_id = '" . $db->clean($_REQUEST['company_name']) . "' AND";
}

if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] != 0) {
	if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 2) //sales executive and its chain wise order
	{
		if ($rights['personal_flag'] == 1) {
			$ctable_where .= " isDelete=0 AND status!=-1 AND sales_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
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
					$ctable_where .= " isDelete=0 AND status!=-1 AND sales_id IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
				} else {
					$ctable_where .= " isDelete=0 AND status!=-1 AND sales_id IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
				}
			} else {
				$ctable_where .= " isDelete=0 AND status!=-1";
			}
		}
	} else if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 3) // customer and its chain wise order
	{
		// Channel Partner login: always own orders only
		if (function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db)) {
			$ctable_where .= " isDelete=0 AND status!=-1 AND customer_id='" . (int) $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
		} else if ($rights['personal_flag'] == 1) {
			$ctable_where .= " isDelete=0 AND status!=-1 AND customer_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
		} else {
			if ($rights['chain_vise_flag'] == 1) {
				$check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
				$get_customer_type = $db->rp_getValue("executive", "type_of_executive", "isDelete=0 AND id='" . $check_id . "'", 0);
				if ($get_customer_type == 1)  //super stockist
				{
					$cus_WhereCondition .= "super_stockist_id='" . $check_id . "' AND dealer_distributor_id=0";
				} else if ($get_customer_type == 2) //distributor 
				{
					$cus_WhereCondition .= "dealer_distributor_id='" . $check_id . "'";
				}

				$data = $db->rp_getData("executive", "id", $cus_WhereCondition, "", 0);

				$CUSIDS = array();
				if ($data) {
					while ($data_d = mysqli_fetch_assoc($data)) {
						$CUSIDS[] = $data_d['id'];
					}
				}
				if (!empty($CUSIDS)) {
					$CUSIDS = implode(",", $CUSIDS);
					$ctable_where .= " isDelete=0 AND status!=-1 AND customer_id IN (" . $CUSIDS . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
				} else {
					$ctable_where .= " isDelete=0 AND status!=-1 AND customer_id IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
				}
			} else {
				$ctable_where .= " isDelete=0 AND status!=-1";
			}
		}
	}
} else {
	$ctable_where .= " isDelete=0 AND status!=-1";
}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 100;

if (isset($_REQUEST["page"])) {
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if (!is_numeric($page_number)) {
		die('Invalid page number!');
	} //incase of invalid page number
} else {
	$page_number = 1; //if there's no page number, set it to 1
}


//status
if (isset($_REQUEST['status']) && $_REQUEST['status'] != "" && $_REQUEST['status'] != NULL && $_REQUEST['status'] != undefined) {
	$ctable_where .= " AND status = '" . $_REQUEST['status'] . "' ";
}

if (isset($_REQUEST['dispatch_status']) && $_REQUEST['dispatch_status'] != "" && $_REQUEST['dispatch_status'] != NULL && $_REQUEST['dispatch_status'] != undefined) {
	$ctable_where .= " AND dispatch_status = '" . $_REQUEST['dispatch_status'] . "' ";
}

// ///For ToDate & FromDate
// if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL && $_REQUEST['ToDate']!=undefined)
// {
//   $ctable_where .= " AND order_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
// }

// if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL && $_REQUEST['FromDate']!=undefined)
// {
//      $ctable_where .= " AND order_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
// }


if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined && $_REQUEST['todate'] != "01-01-1970") {
	$ctable_where .= " AND order_date >= '" . $_REQUEST['todate'] . "' ";
}


if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined && $_REQUEST['fromdate'] != "01-01-1970") {
	$ctable_where .= " AND order_date <= '" . $_REQUEST['fromdate'] . "' ";
}
if (isset($_REQUEST['df']) && $_REQUEST['df'] != "" && $_REQUEST['df'] != NULL && $_REQUEST['df'] != undefined) {
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode($_REQUEST['df']);

	$date_filter_query_ex = explode(" to ", $date_filter_query);

	$ctable_where .= " AND ( DATE(order_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(order_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
}
// if($_REQUEST['df']=="undefined")
// {
// 	$ctable_where .= " AND ( DATE(order_date)>='".date("Y-m-d")."' AND DATE(order_date)<='".date("Y-m-d")."')";
// }
if (isset($_REQUEST['order_month']) && $_REQUEST['order_month'] != "" && $_REQUEST['order_month'] != NULL) {
	$ctable_where .= " AND MONTH(order_date) = '" . $_REQUEST['order_month'] . "'";
}

if (isset($_REQUEST['order_year']) && $_REQUEST['order_year'] != "" && $_REQUEST['order_year'] != NULL) {
	$ctable_where .= " AND YEAR(order_date) = '" . $_REQUEST['order_year'] . "'";
}

if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != NULL) {
	$ctable_where .= " AND customer_id = '" . $_REQUEST['customer_id'] . "'";
}
// echo $_REQUEST['type']
$isPendingPaymentList = false;
$isCpPortalList = false;
$hasCpPortalCol = false;
$cpPortalColCheck = @mysqli_query($db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'cp_portal_order_flag'");
if ($cpPortalColCheck && mysqli_num_rows($cpPortalColCheck) > 0) {
	$hasCpPortalCol = true;
}
$is_cp_login_list = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);

if (isset($_REQUEST['type']) && $_REQUEST['type'] != "" && $_REQUEST['type'] != NULL && $_REQUEST['type'] != 'undefined') {
	if ($_REQUEST['type'] == "channel_partner_portal") {
		$isCpPortalList = true;
		$ctable_where .= " AND channel_partner_order_flag=1 ";
		if ($hasCpPortalCol) {
			$ctable_where .= " AND cp_portal_order_flag=1 ";
		} else {
			$ctable_where .= " AND 1=0 ";
		}
		$type = $_REQUEST['type'];
		$disabled = "disabled";
	} else if ($_REQUEST['type'] == "channel_partner") {
		$ctable_where .= " AND channel_partner_order_flag=1 ";
		/* Admin routine list: exclude portal-pending; CP My Orders: show all */
		if ($hasCpPortalCol && !$is_cp_login_list) {
			$ctable_where .= " AND (cp_portal_order_flag=0 OR cp_portal_order_flag IS NULL) ";
		}
		$type = $_REQUEST['type'];
		$disabled = "disabled";
	} else if ($_REQUEST['type'] == "pending_payment") {
		$isPendingPaymentList = true;
		$ctable_where .= " AND (payment_received_flag=0 OR payment_received_flag IS NULL) ";
		$ctable_where .= " AND DATE(order_date) <= DATE_SUB(CURDATE(), INTERVAL 45 DAY) ";
		$ctable_where .= " AND status NOT IN (-2,3) ";
		$type = $_REQUEST['type'];
	} else if ($_REQUEST['type'] != 100) {
		$ctable_where .= " AND customer_type = '" . $_REQUEST['type'] . "' AND (channel_partner_order_flag=0 OR channel_partner_order_flag IS NULL) ";
		$type = $_REQUEST['type'];
		$disabled = "disabled";
	}
}

if (isset($_REQUEST['sales_id']) && $_REQUEST['sales_id'] != "" && $_REQUEST['sales_id'] != NULL && $_REQUEST['sales_id'] != undefined) {
	$ctable_where .= " AND sales_id = '" . $_REQUEST['sales_id'] . "' ";
}

$get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows / $item_per_page);

//get starting position to fetch the records
$page_position = (($page_number - 1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable, "*", $ctable_where, ($isPendingPaymentList ? "company_name ASC, customer_name ASC, order_date ASC, id ASC" : "id DESC") . " limit $page_position, $item_per_page", 0);

$lr_right_r = $db->rp_getData("page_admin_right", "*", "page_id = 615 AND admin_id = '" . $_SESSION[SITE_SESS . '_ADMIN_TYPE'] . "'", "", 0);
// $lr_right = mysqli_fetch_array($lr_right_r);
$orders_status = array(/*"-1"=>"Add to Cart",*/"-2" => "Disapproved", "" => "Waiting For Approval", "1" => "Waiting For Account Approval", "3" => "Cancelled", "4" => "Account Approved", "5" => "Dispatch", "6" => "Order Complate");
// print_r($lr_right);die;
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

	.table_amount {
		width: auto;
		max-width: 100%;
		margin-bottom: 20px;
		text-align: right;
	}

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
	<!-- <div id="total-order-value" style="text-align: right;"></div>
	<div id="total-order-value-approve" style="text-align: right;"></div>
	<div id="total-order-value-pending" style="text-align: right;"></div>
	<div id="total-order-value-cancel" style="text-align: right;"></div>
	<div id="total-order-value-disapprove" style="text-align: right;"></div> -->

	<div style="text-align: -webkit-right;;" id="show-statics">
		<!-- <table class="table_amount table-striped table-bordered table-hover">
			
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Total Order </strong></td>
				<td style="padding-right: 10px;" id="total-order"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-order-value"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Approve order Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-order-value-approve"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Pending order Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-order-value-pending"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Cancel order Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-order-value-cancel"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Disapprove order Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-order-value-disapprove"></td>
			</tr>
		</table> -->
	</div>
	<span style="color: red;font-size: 14px;font-style: italic;"><?= CURRENT_DATA_INFO ?></span>
	<div class="table-scrollable">
		<table id="datatable_1" class="table table-striped table-bordered table-hover ">
			<thead class="fix-th">
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th>
						<select class="form-control qid" id="qid" name="qid">
							<option value="">Select Qutotation</option>
							<?php
							$q_r = $db->rp_getData("orders", "quotation_id", "isDelete=0 AND quotation_id!='' Group By quotation_id", "", 0);
							if ($q_r) {
								while ($q_d = mysqli_fetch_assoc($q_r)) {
									$q_no = $db->rp_getValue("quotation_detail", "quotation_no", "id='" . $q_d['quotation_id'] . "'");
							?>
									<option <?= ($_REQUEST['qid'] == $q_d['quotation_id']) ? "selected" : ""; ?> value="<?= $q_d['quotation_id']; ?>"><?= $q_no; ?></option>
							<?php
								}
							}
							?>
						</select>
					</th>
					<th style="width:150px;">
						<label>Filter By Date</label>
						<div class="input-group">
							<input style="width: 200px" class="form-control datetimerange-picker-input" id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
							<span class="input-group-addon datetimerange-picker-btn">
								<i class="fa fa-calendar"></i>
							</span>
							<span class="input-group-btn">
								<!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
							</span>
						</div>
						<!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
					</th>
					<th>

						<select class="form-control input-small" id="status">
							<option value="">Select Status</option>
							<option value="-2" <?= ("-2" == $_REQUEST['status']) ? "selected" : ""; ?>>Disapproved</option>
							<option value="0" <?= ("0" == $_REQUEST['status']) ? "selected" : ""; ?>>Waiting For Approval</option>
							<option value="1" <?= ("1" == $_REQUEST['status']) ? "selected" : ""; ?>>Waiting Account Approved</option>
							<option value="3" <?= ("3" == $_REQUEST['status']) ? "selected" : ""; ?>>Cancelled</option>
							<option value="4" <?= ("4" == $_REQUEST['status']) ? "selected" : ""; ?>>Account Approve</option>
							<option value="5" <?= ("5" == $_REQUEST['status']) ? "selected" : ""; ?>>Dispatch</option>
							<option value="6" <?= ("6" == $_REQUEST['status']) ? "selected" : ""; ?>>Order Complete</option>
							<?php

							// foreach ($orders_status as $key => $value) {
							?>
							<!-- <option value="<?= $key ?>" <?= ($key == $_REQUEST['status']) ? "selected" : ""; ?> ><?= $value ?></option> -->
							<?php
							//}
							?>
						</select>
						<!-- <select class="form-control input-small" id="status">
						<option value="">Select Status</option>
	            		<option value="-2" <?= ("-2" == $_REQUEST['status']) ? "selected" : ""; ?>>Disapproved</option>
	            		<option value="0" <?= ("0" == $_REQUEST['status']) ? "selected" : ""; ?>>Pending</option>
	            		<option value="1" <?= ("1" == $_REQUEST['status']) ? "selected" : ""; ?>>Approved</option> -->
						<!-- <option value="2" <?= ("2" == $_REQUEST['status']) ? "selected" : ""; ?>>Dispatch</option> -->
						<!-- <option value="3" <?= ("3" == $_REQUEST['status']) ? "selected" : ""; ?>>Cancelled</option>
	            		<option value="4" <?= ("4" == $_REQUEST['status']) ? "selected" : ""; ?>>Partially Dispatched</option>
	            		<option value="5" <?= ("5" == $_REQUEST['status']) ? "selected" : ""; ?>>Dispatched</option> -->
						<!-- <option value="7" <?= ("7" == $_REQUEST['status']) ? "selected" : ""; ?>>Pending LR</option> -->
						<!-- <option value="6" <?= ("6" == $_REQUEST['status']) ? "selected" : ""; ?>>Order Complate</option> -->
						</select>
					</th>
					<th>
						<select class="form-control input-small" id="type_of_company">
							<option value="">Select Company</option>
							<?php
							$company_r = $db->rp_getData("company_master", "*", "isDelete=0", "", 0);
							if ($company_r) {
								while ($company_d = mysqli_fetch_assoc($company_r)) {
							?>
									<option value="<?= $company_d['id'] ?>" <?= ($company_d['id'] == $_REQUEST['type_of_company']) ? "selected" : ""; ?>><?= $company_d['name']; ?></option>
							<?php
								}
							}
							?>
						</select>
					</th>
					<!--  <th>
					<select class="form-control input-small" id="dispatch_status">
						<option value="">Select Status</option>
		            	<?php $dispatch_status = $db->rp_getData("dispatch_order_status", "*", "isDelete=0", "", 0);
						if ($dispatch_status) {
							while ($statusD = mysqli_fetch_assoc($dispatch_status)) {
						?>
		            				<option value="<?= $statusD['id'] ?>" <?= ($statusD['id'] == $_REQUEST['dispatch_status']) ? "selected" : ""; ?>><?= $statusD['name']; ?></option>
		            			<?php
							}
						}
								?>
				    </select>
				</th> -->
					<th>
						<select class="form-control input-small" id="dispatch_status">
							<option value="">Select Status</option>
							<?php $dispatch_status = $db->rp_getData("dispatch_order_status", "*", "isDelete=0", "", 0);
							if ($dispatch_status) {
								while ($statusD = mysqli_fetch_assoc($dispatch_status)) {
							?>
									<option value="<?= $statusD['id'] ?>" <?= ($statusD['id'] == $_REQUEST['dispatch_status']) ? "selected" : ""; ?>><?= $statusD['name']; ?></option>
							<?php
								}
							}
							?>
						</select>
					</th>
					<th>
						<select class="form-control input-small" id="company_name" name="company_name">
							<option value="">Select Company</option>
							<?php $company_name_r = $db->rp_getData("executive", "company_name,cname,id", "isDelete=0", "", 0);
							if ($company_name_r) {
								while ($company_name_d = mysqli_fetch_assoc($company_name_r)) {
							?>
									<option value="<?= $company_name_d['id'] ?>" <?= ($company_name_d['id'] == $_REQUEST['company_name']) ? "selected" : ""; ?>><?= $company_name_d['company_name'] . "-" . $company_name_d['cname'] ?></option>
							<?php
								}
							}
							?>
						</select>
					</th>
					<th></th>
					<th></th>
					<th>
						<!-- Sales Person Name -->
						<?php
						if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
						?>
							<select class="form-control input-small" id="sales_id">
								<option value="">Select Sales Person Name</option>
								<?php
								$salesExe = $db->rp_getData("sales_executive", "*", "isDelete=0 AND isActive=1", "", 0);
								if ($salesExe) {
									while ($salesD = mysqli_fetch_assoc($salesExe)) {
								?>
										<option value="<?= $salesD['id'] ?>" <?= ($salesD['id'] == $_REQUEST['sales_id']) ? "selected" : ""; ?>><?= $salesD['name']; ?></option>
								<?php
									}
								}
								?>
							<?php
						}
							?>
							</select>
					</th>
					<th>
						<select <?= $disabled ?> class="form-control input-small" id="type">
							<option value="">Select Order Type</option>
							<?php
							$type_r = $db->rp_getData("customer_type", "id,name", "isDelete=0");
							if ($type_r) {
								while ($type_d = mysqli_fetch_assoc($type_r)) {
							?>
									<option value="<?= $type_d['id'] ?>" <?= ($type_d['id'] == $_REQUEST['type']) ? "selected" : ""; ?>><?= $type_d['name']; ?></option>
							<?php
								}
							}
							?>
						</select>
					</th>
					<th></th>
					<?php
					if ($lr_right['view_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
					?>
						<th></th>
						<th></th>
					<?php
					}
					?>
					<!-- <th></th>
				<th></th>
				<th></th>
				<th></th> -->
				</tr>
				<tr>
					<th class="fix-th1"></th>
					<th class="fix-th1">No.</th>
					<th class="fix-th1">Order No.</th>
					<th class="fix-th1">Approved By</th>
					<th class="fix-th1">Quotation No.</th>
					<th class="fix-th1" style="width:150px!important;">Order Date</th>
					<th class="fix-th1">Status</th>
					<th class="fix-th1">Type Of Company</th>
					<th class="fix-th1">Dispatch Order Status</th>
					<th class="fix-th1">Company Name</th>
					<th class="fix-th1">Customer Name</th>
					<th class="fix-th1">Client Code</th>
					<!-- <th class="fix-th1">Person Name</th> -->
					<th class="fix-th1">Sales Person Name</th>
					<th class="fix-th1">Order Type</th>
					<!-- <th>Brand Type</th> -->
					<th class="fix-th1" style="text-align:right;">Order Amount</th>
					<?php
					if ($lr_right['view_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
					?>
						<th class="fix-th1">LR Number</th>
						<th class="fix-th1">LR Attachment</th>
					<?php
					}
					?>
					<!-- <th class="fix-th1">Transport Name</th>
					<th class="fix-th1">Transport By</th> -->
					<!-- <th class="fix-th1">Dispatch Date</th> -->
					<!-- 	<th class="fix-th1">Entry Type</th>
					<th class="fix-th1">Update Entry Type</th> -->
				</tr>
			</thead>
			<tbody>
				<?php
				if ($ctable_r) {
					$count = 0;
					$sales_name = '';
					$lastCustomerKey = null;
					$colspanCount = 14;
					$paymentTypeLabels = array("1" => "By Cash", "2" => "By Cheque", "3" => "Online", "4" => "Other");
					$entry_type_status = array("1" => "Admin Panel", "2" => "customer", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
					$orders_status = array("-1" => "Add to Cart", "-2" => "Disapproved", "0" => "Waiting For Approval", "1" => "Waiting For Account Approval", "3" => "Cancelled", "4" => "Account Approved", "5" => "Dispatch", "6" => "Order Complate");
					while ($ctable_d = mysqli_fetch_array($ctable_r)) {
						// Customer-wise header for Pending Payment list
						if ($isPendingPaymentList) {
							$custKey = (int) $ctable_d['customer_id'] . '|' . $ctable_d['company_name'];
							if ($lastCustomerKey !== $custKey) {
								$lastCustomerKey = $custKey;
								$custLabel = trim($ctable_d['company_name']) != '' ? $ctable_d['company_name'] : 'Unknown Customer';
								if (trim($ctable_d['customer_name']) != '') {
									$custLabel .= ' — ' . $ctable_d['customer_name'];
								}
								if (!empty($ctable_d['client_code'])) {
									$custLabel .= ' (' . $ctable_d['client_code'] . ')';
								}
								?>
								<tr style="background-color:#f5d0d0;">
									<td colspan="<?php echo (int) $colspanCount; ?>" style="font-weight:700;color:#922b21;font-size:13px;padding:8px 10px;">
										<i class="fa fa-user"></i> <?php echo htmlspecialchars($custLabel, ENT_QUOTES, 'UTF-8'); ?>
									</td>
								</tr>
								<?php
							}
						}

						$transport_through_id = $db->rp_getValue("orders", "transport_through", "id='" . $ctable_d['id'] . "'", 0);
						$get_approved_by_name = $db->rp_getValue("dealer_distributor_network", "name", "id='" . $ctable_d['approve_by_id'] . "'", 0);
						$transport_through = $db->rp_getValue("transport_by", "name", "id='" . $transport_through_id . "'");
						$transport_name_id = $db->rp_getValue("orders", "transport_name", "id='" . $ctable_d['id'] . "'");
						$transport_name =  $db->rp_getValue("transport_master", "name", "transport_by_id='" . $transport_through_id . "' AND id='" . $transport_name_id . "'", 0);

						/*$customer=$db->rp_getValue('executive','isActive',"id=".$ctable_d['customer_id']."",0);
				if($customer==0)
				{
					continue;
				}*/

						/*if($ctable_d['status'] == -2)
				{
					$style = "style='background-color: #ffe167;'";
				}
				if($ctable_d['status'] == 4)
				{
					$style = "style='background-color: #FA9C5B;'";
				}
				else if ($ctable_d['status'] == 5) 
				{
					$style = "style='background-color: #f1acac;'";
				}
				else if ($ctable_d['status'] == 1) 
				{
					$style = "style='background-color: #7C9D96;'";
				}
				else if ($ctable_d['status'] == 0) 
				{
					$style = "style='background-color: #add8e6;'";
				}
				else if ($ctable_d['status'] == 6) 
				{
					$style = "style='background-color: #9fc1ff;'";
				}
				else if ($ctable_d['status'] == 3) 
				{
					$style = "style='background-color: #ec9b97;'";
				}*/

				$style = "";
				if ($isPendingPaymentList) {
					$style = "style='background-color:#fde8e8;color:#c0392b;'";
				} else if (isset($ctable_d['payment_received_flag']) && (int) $ctable_d['payment_received_flag'] === 0
					&& !empty($ctable_d['order_date']) && $ctable_d['order_date'] != '0000-00-00'
					&& strtotime($ctable_d['order_date']) <= strtotime('-45 days')) {
					// Soft highlight on All Orders when overdue without payment mark
					$style = "style='color:#c0392b;'";
				}

				?>
						<tr <?= $style ?>>
							<td>
								<?php $ctable_d['id'];
								/*if($rights['update_flag']==1)*/
								if ($rights['view_flag'] == 1) {
								?>
									<div class="btn-group">
										<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle"><i class="fa fa-gear"></i>
										</button>
										<ul role="menu" class="dropdown-menu">
											<?php
											$total_payment = $db->rp_getTotalRecord("payment", "receipt_type=2 AND invoice_id='" . $ctable_d['id'] . "' AND isDelete=0");
											if ($rights['update_flag'] == 1 && $ctable_d['status'] != 2 && $total_payment == 0) {
											?>
												<li>
													<a href="orders_crud.php?mode=edit&id=<?= $ctable_d['id'] ?>&c_type=<?= $order_type ?>" title="Edit">
														<span class="text-success">
															<i class="fa fa-pencil"></i>
															&nbsp;Edit
														</span>
													</a>
												</li>
												<?php

											}
											if ($rights['delete_flag'] == 1 && $ctable_d['id'] != -1 && $total_payment == 0) {
												$total_dispatch_record = $db->rp_getTotalRecord("dispatch_detail", "order_id='" . $ctable_d['id'] . "' AND isDelete=0");
												if ($total_dispatch_record > 0) {
												} else {
												?>
													<li>
														<a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete">
															<span class="text-danger">
																<i class="fa fa-times"></i>
																&nbsp;Delete
															</span>
														</a>
													</li>
											<?php
												}
											}
											$file_path = "order_viewer.php?order_id=" . $ctable_d['id'] . "&type=" . $_REQUEST['type'] . "";
											?>
											<li>
												<a class="" target="_blank" href="<?php echo $file_path; ?>" title="save">
													<i class="fa fa-file-pdf-o"></i>
													Download
												</a>
											</li>
											<li><a href="orders_crud.php?order_id=<?= $ctable_d['id'] ?>&mode=add&c_type=<?= $type ?>"><span class="text-warning"><i class="fa fa-refresh"></i> Repeat Order</span></a></li>
											<?php
											if (!$is_cp_login_list && !empty($ctable_d['cp_portal_order_flag']) && (int) $ctable_d['cp_portal_order_flag'] === 1) {
												$cpFromName = trim($ctable_d['company_name']);
												if ($cpFromName == '' && !empty($ctable_d['customer_name'])) {
													$cpFromName = $ctable_d['customer_name'];
												}
											?>
												<li>
													<a href="javascript:;" onClick="ConvertCpPortalOrder('<?php echo $ctable_d['id']; ?>');">
														<span class="text-primary"><i class="fa fa-exchange"></i> Convert to Regular Order (with Pricing)</span>
													</a>
												</li>
												<li>
													<a href="javascript:;" style="cursor:default;opacity:0.9;" title="Portal source">
														<span class="text-warning"><i class="fa fa-building"></i> Order from <?php echo htmlspecialchars($cpFromName); ?></span>
													</a>
												</li>
											<?php
											}
											if ($ctable_d['status'] == 1) {
											?>
												<li><a href="javascript:;" onClick="OrderStatus('<?php echo $ctable_d['id']; ?>','4');"><span><i class="fa fa-circle"></i>Account Approve</span></a></li>
											<?php
											}
											?>

											<?php
											if ($ctable_d['status'] == 4) {
												if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
													$get_dispatch_right = $db->rp_getValue("page_admin_right", "view_flag", "isDelete=0 AND page_id='569' AND admin_id='" . $_SESSION[SITE_SESS . '_ADMIN_TYPE'] . "'", 0);
													if ($get_dispatch_right == 1) {
											?>
														<!-- <li>
									<a class="" target="_blank" href="dispatch_crud_new.php?mode=add&order_id=<?php echo $ctable_d['id'] ?>" title="save"><i class="fa fa-file-pdf-o"></i>  Dispatch</a>
								</li> -->
													<?php
													}
												} else {
													?>
													<!-- <li>
											<a class="" target="_blank" href="dispatch_crud.php?mode=add&order_id=<?php echo $ctable_d['id'] ?>" title="save"><i class="fa fa-file-pdf-o"></i>  Dispatch</a>
										</li> -->
													<!-- <li>
											<a class="" target="_blank" href="dispatch_crud_new.php?mode=add&order_id=<?php echo $ctable_d['id'] ?>" title="save"><i class="fa fa-file-pdf-o"></i>  Dispatch</a>
										</li> -->
											<?php
												}
											}
											?>
											<!-- <li>
									<a class="" target="_blank" href="pending_order_view.php?order_id=<?php echo $ctable_d['id'] ?>" title="save">
										<i class="fa fa-file-pdf-o"></i>
										Pending Dispatch View
									</a>
								</li>  -->
											</li>
											<?php
											if ($lr_right['insert_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
												if ($ctable_d['status'] == 5) {
											?>
													<li>
														<a href="#lrattachment" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="Add LR Attachment">
															<span class="text-success">
																<i class="fa fa-paperclip" aria-hidden="true"></i>
																&nbsp;LR Attachment
															</span>
														</a>
													</li>
											<?php }
											} ?>
											<li>
												<a href="#pdfattachment" data-pdfid="<?= $ctable_d['pdf_attachment']; ?>" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="Add PDF Attachment">
													<span class="text-success">
														<i class="fa fa-paperclip" aria-hidden="true"></i>
														&nbsp;PDF Attachment
													</span>
												</a>
											</li>
											<?php
											$payReceived = isset($ctable_d['payment_received_flag']) ? (int) $ctable_d['payment_received_flag'] : 0;
											if ($payReceived === 1) {
												$payDateShow = (!empty($ctable_d['payment_received_date']) && $ctable_d['payment_received_date'] != '0000-00-00 00:00:00')
													? date('d-m-Y', strtotime($ctable_d['payment_received_date']))
													: '';
												$payAmtShow = isset($ctable_d['payment_received_amount']) ? (float) $ctable_d['payment_received_amount'] : 0;
												$payTypeShow = '';
												if (isset($ctable_d['payment_received_type']) && isset($paymentTypeLabels[$ctable_d['payment_received_type']])) {
													$payTypeShow = $paymentTypeLabels[$ctable_d['payment_received_type']];
												}
												$payExtra = array();
												if ($payAmtShow > 0) {
													$payExtra[] = number_format($payAmtShow, 2);
												}
												if ($payTypeShow != '') {
													$payExtra[] = $payTypeShow;
												}
												if ($payDateShow != '') {
													$payExtra[] = $payDateShow;
												}
												?>
												<li>
													<a href="javascript:;" title="Already marked" style="cursor:default;opacity:0.75;">
														<span class="text-muted">
															<i class="fa fa-check-circle"></i>
															&nbsp;Payment Received<?php echo !empty($payExtra) ? ' (' . implode(' | ', $payExtra) . ')' : ''; ?>
														</span>
													</a>
												</li>
												<?php
											} else if ($rights['update_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
												$orderGrand = isset($ctable_d['grand_total']) ? (float) $ctable_d['grand_total'] : 0;
												?>
												<li>
													<a href="#paymentReceivedModal"
														data-toggle="modal"
														data-order-id="<?php echo (int) $ctable_d['id']; ?>"
														data-order-no="<?php echo htmlspecialchars($ctable_d['order_no'], ENT_QUOTES, 'UTF-8'); ?>"
														data-grand-total="<?php echo $orderGrand; ?>"
														title="Mark Payment Received">
														<span class="text-success">
															<i class="fa fa-money"></i>
															&nbsp;Payment Received
														</span>
													</a>
												</li>
												<?php
											}
											?>
										</ul>
									</div>
								<?php
								}
								?>
							</td>
							<td><?php echo ++$count; ?></td>
							<td><span class="text-success"><a href="order_viewer.php?order_id=<?= $ctable_d['id'] ?>"><?php echo stripslashes($ctable_d['order_no']); ?></a></span><?php if (!empty($ctable_d['channel_partner_order_flag']) && $ctable_d['channel_partner_order_flag'] == 1) { ?> <span class="label label-info">Channel Partner</span><?php } ?><?php
							if (!empty($ctable_d['cp_portal_order_flag']) && (int) $ctable_d['cp_portal_order_flag'] === 1) {
								$cpFromLbl = trim($ctable_d['company_name']);
								if ($cpFromLbl == '' && !empty($ctable_d['customer_name'])) {
									$cpFromLbl = $ctable_d['customer_name'];
								}
								echo ' <span class="label label-warning">Portal</span>';
								echo '<div style="margin-top:4px;font-size:11px;color:#c87f0a;"><b>Order from:</b> ' . htmlspecialchars($cpFromLbl) . '</div>';
								if (!$is_cp_login_list) {
									echo '<div style="margin-top:2px;"><a href="javascript:;" class="btn btn-xs yellow" onClick="ConvertCpPortalOrder(\'' . (int) $ctable_d['id'] . '\');"><i class="fa fa-exchange"></i> Convert to Regular Order</a></div>';
								}
							}
							?></td>
							<td><?php echo $get_approved_by_name ?></td>
							<?php
							if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 11) {
							?>
								<td><?php if ($ctable_d['quotation_id'] != 0) {
										echo $db->rp_getValue("quotation_detail", "quotation_no", "id='" . $ctable_d['quotation_id'] . "'");
									} ?></td>
							<?php
							} else {
							?>
								<td><?php if ($ctable_d['quotation_id'] != 0) {
										echo "<a class='text-success' target='_blank' href='quotation_viewer.php?quotation_id=" . $ctable_d['quotation_id'] . "'>" . $db->rp_getValue("quotation_detail", "quotation_no", "id='" . $ctable_d['quotation_id'] . "'") . "</a>";
									} ?></td>
							<?php
							} ?>
							<td style="width:150px;"><?php echo date('d-m-Y', strtotime($ctable_d['order_date'])); ?></td>
							<td><?php echo $orders_status[$ctable_d['status']]; ?></td>
							<td><?php echo   $db->rp_getValue("company_master", "name", "id='" . $ctable_d['type_of_company'] . "'"); ?></td>
							<td>
								<select class="form-control" disabled="disabled" id="dispatch_status<?= $ctable_d['id'] ?>" style="width:120px;text-align:center;margin: auto;">
									<option>Select Status</option>
									<?php
									$dispatch_status = $db->rp_getData("dispatch_order_status", "*", "isDelete=0", "", 0);
									if ($dispatch_status) {
										while ($statusD = mysqli_fetch_assoc($dispatch_status)) {
									?>
											<option value="<?= $statusD['id'] ?>" <?= ($statusD['id'] == $ctable_d['dispatch_status']) ? "selected" : ""; ?>><?= $statusD['name']; ?></option>
									<?php
										}
									}
									?>
								</select>
								<a href="javascript:void(0);" id="editStatus_<?php echo $ctable_d['id']; ?>" onClick="editStatus('<?php echo $ctable_d['id']; ?>')">Edit</a>
								<span id="editStatus2_<?php echo $ctable_d['id']; ?>" style="display:none;">
									<a href="javascript:void(0);" id="saveEditStatus<?php echo $ctable_d['id']; ?>" onClick="saveEditStatus('<?php echo $ctable_d['id']; ?>')">Save</a> |
									<a href="javascript:void(0);" id="cancelEditStatus<?php echo $ctable_d['id']; ?>" onClick="cancelEditStatus('<?php echo $ctable_d['id']; ?>')">Cancel</a>
								</span>
							</td>
							<?php
							$customer_flag_text = "";
							if ($ctable_d['customer_flag'] == 1) {
								$customer_flag_text = " - P";
							} else if ($ctable_d['customer_flag'] == 0) {
								$customer_flag_text = " - C";
							}
							?>
							<td><b><?php echo $ctable_d['company_name'] . $customer_flag_text; ?></b></td>
							<td><?php echo stripslashes($ctable_d['customer_name']); ?></td>
							<td><?php echo stripslashes($ctable_d['client_code']); ?></td>
							<!-- <td></td> -->
							<?php
							$sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['sales_id'] . "'");
							if ($sales_name == "") {
								$sales_name = ($ctable_d['created_by_type'] == 0) ? "Admin" : "";
							}
							?>
							<td>
								<?= $sales_name; ?>
							</td>
							<!-- <td><?php if ($ctable_d['customer_type'] == '1') {
											$slug = "Super Stockist";
										} else if ($ctable_d['customer_type'] == '2') {
											$slug = "Distributor";
										} else if ($ctable_d['customer_type'] == '3') {
											$slug = "Dealer";
										} else if ($ctable_d['customer_type'] == '4') {
											$slug = "B2B Customer";
										} else if ($ctable_d['customer_type'] == '6') {
											$slug = "B2C Customer";
										} else if ($ctable_d['customer_type'] == 'normal_user') {
											$slug = "Normal Customer";
										}
										echo stripslashes($slug); ?>
					</td> -->
							<td><?php
								if (!empty($ctable_d['channel_partner_order_flag']) && $ctable_d['channel_partner_order_flag'] == 1) {
									echo "Channel Partner";
								} else {
									echo $customer_type = $db->rp_getValue("customer_type", "name", "id='" . $ctable_d['customer_type'] . "'");
								}
							?></td>
							<td align="right"><?php echo stripslashes(CURR . $db->rp_num(round($ctable_d['grand_total']))); ?></td>
							<?php
							if ($lr_right['view_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
							?>
								<td><?= $ctable_d['lr_number'] ?></td>
								<td>
									<?php
									if ($ctable_d['lr_image'] != "") {
										$lr_image = ADMINSITEURL . LRCOPY_A . $ctable_d['lr_image'];
									} else {
										$lr_image = "";
									}
									if ($ctable_d['lr_image'] != "") {
									?>
										<a href="<?= $lr_image ?>" download class="text-warning" title="View"><i class="fa fa-download" style="font-size: 15px;"></i></a>&nbsp;&nbsp;&nbsp;
										<a href="<?= $lr_image ?>" target="_blank" class="text-sucess" title="View"><i class="fa fa-eye" style="font-size: 15px;"></i></a>
									<?php
									}
									?>
								</td>
							<?php
							}
							?>
							<!-- <td><?php echo $transport_through; ?></td>
					<td><?php echo $transport_name; ?></td> -->
							<!-- <td><?php $dispatch_date =  $db->rp_getValue("dispatch_detail", "dispatch_date", "order_id='" . $ctable_d['id'] . "'", 0);
										if ($dispatch_date == "0000-00-00" || $dispatch_date == "1970-01-01" || $dispatch_date == "") {
											echo "";
										} else {
											echo date('d-m-Y', strtotime($dispatch_date));
										}
										?>
	        		</td> -->
							<!-- <td><?php echo $entry_type_status[$ctable_d['entry_flag']]; ?></td>
					<td><?php echo $entry_type_status[$ctable_d['update_entry_flag']]; ?></td> -->
						</tr>
				<?php
					}
					//$t_order_val += $ctable_d['grand_total']; 
					$t_orders = $db->rp_getTotalRecord("orders", $ctable_where, 0);
					$t_order_val = $db->rp_getValue("orders", "SUM(grand_total)", $ctable_where, 0);
					$t_order_val_approve = $db->rp_getValue("orders", "SUM(grand_total)", "isDelete=0 AND status=1 AND" . $ctable_where, 0);
					$t_order_val_pending = $db->rp_getValue("orders", "SUM(grand_total)", "isDelete=0 AND status=0 AND" . $ctable_where, 0);
					$t_order_val_cancel = $db->rp_getValue("orders", "SUM(grand_total)", "isDelete=0 AND status=3 AND" . $ctable_where, 0);
					$t_order_val_disapprove = $db->rp_getValue("orders", "SUM(grand_total)", "isDelete=0 AND status=-2 AND" . $ctable_where, 0);
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="50" <?php if ($_REQUEST["show"] == 50 || $_REQUEST["show"] == "") {
											echo ' selected="selected"';
										}  ?>>50</option>
					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "") {
											echo ' selected="selected"';
										}  ?>>100</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500) {
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
</form>

<script type="text/javascript">
	$("#qid").select2();
	$("#sales_id").select2();
	$("#type").select2();
	$("#status").select2();
	$("#brand_id").select2();
	$("#dispatch_status").select2();
	$("#type_of_company").select2();
	$("#company_name").select2();

	// $("#total-order").html("<strong><?php echo $t_orders; ?></strong>");

	// $("#total-order-value").html("<strong><?php echo CURR . $db->rp_number_format(stripslashes($t_order_val), 2); ?></strong>");
	// $("#total-order-value-approve").html("<strong><?php echo CURR . $db->rp_number_format(stripslashes($t_order_val_approve), 2); ?></strong>");
	// $("#total-order-value-pending").html("<strong><?php echo CURR . $db->rp_number_format(stripslashes($t_order_val_pending), 2); ?></strong>");
	// $("#total-order-value-cancel").html("<strong><?php echo CURR . $db->rp_number_format(stripslashes($t_order_val_cancel), 2); ?></strong>");
	// $("#total-order-value-disapprove").html("<strong><?php echo CURR . $db->rp_number_format(stripslashes($t_order_val_disapprove), 2); ?></strong>");

	function showStatics() {
		var where = "<?= $ctable_where ?>";
		$.ajax({
			type: 'POST',
			url: 'show_statics_ajax.php',
			data: {
				where: where,
				mode: 'order',
			},
			beforeSend: function() {
				// $('.preloader').fadeIn('slow');
			},
			success: function(result) {
				// $("#show-statics").html(result);
				// $('.preloader').fadeOut('slow');
				$("#show-statics").html(result);
			}
		});
	}
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

	function OrderStatus(oid, status) {
		if (status == 1) {
			var txt = "Approve";
		} else if (status == -2) {
			var txt = "Dispprove";
		} else if (status == 3) {
			var txt = "Cancel";
		} else if (status == 4) {
			var txt = "Account's Approve";
		}
		var r = confirm("Are You Sure you want to " + txt + " this Order??");
		if (r) {
			$.ajax({
				type: "POST",
				url: "update_order_status.php",
				data: 'order_id=' + oid + '&status=' + status,
				beforeSend: function() {
					$(".transCover").fadeIn(800);
				},
				success: function(result) {
					var result = $.parseJSON(result);
					if (result.ack == 1) {
						$(".hide-app-dis").addClass("hidden");
						setTimeout(function() {
							$(".transCover").fadeOut(100);
							toastr.success(result.ack_msg);

						}, 1500);
					} else {
						toastr.error(result.ack_msg);
					}

				}
			});
		}
	}

	// Payment Received uses #paymentReceivedModal in dealer_orders_manage.php
</script>
<?php require_once "disconnect.php"; ?>