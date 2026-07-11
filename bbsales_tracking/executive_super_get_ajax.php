<?php
if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "") {
	$page_id = 555;
	$page_slug = 'page_executive';
} else if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "prospect") {
	$page_id = 616;
	$page_slug = 'prospect_customer';
} else if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "channel_partner") {
	$page_id = 555;
	$page_slug = 'channel_partner_customer';
}
include("connect.php");
$ctable 	= "executive";
$ctable1 	= "Executive";
$ctable_where = "";
$where = "";
$isFillter = filter_var($_REQUEST['isFillter'], FILTER_VALIDATE_BOOLEAN);
$uid = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
// print_r($_REQUEST);exit();

if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "") {
	$ctable_where .= "customer_flag=0 AND channel_partner_flag=0 AND ";
} else if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "prospect") {
	$ctable_where .= "customer_flag=1 AND ";
} else if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "channel_partner") {
	$ctable_where .= "customer_flag=0 AND channel_partner_flag=1 AND ";
	$isFillter = true;
}

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
	//$ctable_where .= " (cname like '%" . $db->clean($_REQUEST['searchName']) . "%' OR company_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR phone  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR mobile_no1  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%'  OR gst  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR client_code  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR zip  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' ) AND ";
	$ctable_where .= " (cname like '%" . $db->clean($_REQUEST['searchName']) . "%' OR company_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR mobile_no1  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%'  OR gst  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR client_code  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR zip  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' ) AND ";
	$isFillter = true;
}

$ctable_where .= " isDelete=0 AND id!=-1 ";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 50;

if (isset($_REQUEST["page"])) {
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if (!is_numeric($page_number)) {
		die('Invalid page number!');
	}
} else {
	$page_number = 1; //if there's no page number, set it to 1
}

if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
	if ($rights['personal_flag'] == 1) {
		$sales_id = $db->rp_getValue("dealer_distributor_network", "sales_executive_id", "isDelete=0 AND id='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "'", 0);

		// $ctable_where .=" AND created_by ='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."' ";
		$ctable_where .= " AND (created_by ='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "' OR seid='" . $sales_id . "')";
		$customer_type = $db->rp_getValue("sales_executive", "customer_type", "id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'", 0);
		$filter_where .= " AND id IN (" . $customer_type . ") ";
		// print_r($ctable_where);exit;
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

				$ctable_where .= " AND seid IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
			} else {
				$ctable_where .= " AND seid IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
			}
		}
		// else
		// {
		// 	// $ctable_where .= " isDelete=0";
		// 	// $customer_type=$db->rp_getValue("sales_executive","customer_type","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
		// 	// //$CustomerType = implode(",", $customer_type);
		//  //    $ctable_where .=" AND type_of_executive IN (".$customer_type.")  ";
		//  //    $filter_where .=" AND id IN (".$customer_type.") ";
		// }	

		// else
		// {
		// 	$ctable_where .= " isDelete=0 ";
		// }
	}
}

if (isset($_REQUEST['class_name']) && $_REQUEST['class_name'] != "" && $_REQUEST['class_name'] != NULL) {
	$ctable_where .= " AND class_id = '" . $_REQUEST['class_name'] . "'";
	$isFillter = true;
}

if (isset($_REQUEST['price_list']) && $_REQUEST['price_list'] != "" && $_REQUEST['price_list'] != NULL) {
	$ctable_where .= " AND price_list_id = '" . $_REQUEST['price_list'] . "'";
	$price_list = $_REQUEST['price_list'];
	$isFillter = true;
}


if (isset($_REQUEST['seid']) && $_REQUEST['seid'] != "" && $_REQUEST['seid'] != NULL) {
	$ctable_where .= " AND seid = '" . $_REQUEST['seid'] . "'";
	$seid = $_REQUEST['seid'];
	$isFillter = true;
}


// if (isset($_REQUEST['zone']) && $_REQUEST['zone'] != "" && $_REQUEST['zone'] != NULL) 
// {
// 	$ctable_where .= " AND zone = '" . $_REQUEST['zone'] . "'";
// 	$zone = $_REQUEST['zone'];
// 	$isFillter=true;
// }

if (isset($_REQUEST['area']) && $_REQUEST['area'] != "" && $_REQUEST['area'] != NULL) {
	$executive_id = array();
	$ctable_area = "class_id=" . $_REQUEST['class_name'] . " AND area_id = '" . $_REQUEST['area'] . "' AND executive_type='1' AND isDelete=0";
	$area_list = $db->rp_getData("executive_map_area", "*", $ctable_area, "", 0);
	while ($area_list_d = mysqli_fetch_assoc($area_list)) {
		$executive_id[] = $area_list_d['executive_id'];
	}
	$ids = implode(",", $executive_id);
	$ctable_where .= " AND id IN (" . $ids . ")";
	$isFillter = true;
}

if (isset($_REQUEST['state']) && $_REQUEST['state'] != "" && $_REQUEST['state'] != NULL) {
	$ctable_where .= " AND state = '" . $_REQUEST['state'] . "' ";
	$state = $_REQUEST['state'];
	$isFillter = true;
}

if (isset($_REQUEST['city']) && $_REQUEST['city'] != "" && $_REQUEST['city'] != NULL) {
	$ctable_where .= " AND city = '" . $_REQUEST['city'] . "'";
	// $city = $_REQUEST['city'];
	$isFillter = true;
}
if (isset($_REQUEST['main_city']) && $_REQUEST['main_city'] != "" && $_REQUEST['main_city'] != NULL) {
	$ctable_where .= " AND main_city = '" . $_REQUEST['main_city'] . "'";
	$city = $_REQUEST['main_city'];
	$isFillter = true;
}

if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "" && $_REQUEST['customer_type'] != NULL) {
	$ctable_where .= " AND type_of_executive = '" . $_REQUEST['customer_type'] . "' ";
	$customer_type = $_REQUEST['customer_type'];
	$isFillter = true;
}
if (isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company'] != "" && $_REQUEST['type_of_company'] != NULL) {
	$ctable_where .= " AND type_of_company = '" . $_REQUEST['type_of_company'] . "' ";
	$type_of_company = $_REQUEST['type_of_company'];
	$isFillter = true;
}
if (isset($_REQUEST['dashboard_customer_type']) && $_REQUEST['dashboard_customer_type'] != "" && $_REQUEST['dashboard_customer_type'] != NULL) {
	$ctable_where .= " AND type_of_executive = '" . $_REQUEST['dashboard_customer_type'] . "' ";
	$customer_type = $_REQUEST['dashboard_customer_type'];
}
if (isset($_REQUEST['category_id']) && $_REQUEST['category_id'] != "" && $_REQUEST['category_id'] != "undefined" && $_REQUEST['category_id'] != "null") {
	$ctable_where .= " AND category_id IN (" . $_REQUEST['category_id'] . ")  ";
	$category_id = $_REQUEST['category_id'];
	$isFillter = true;
}

if (isset($_REQUEST['top_category_id']) && $_REQUEST['top_category_id'] != "" && $_REQUEST['top_category_id'] != "undefined" && $_REQUEST['top_category_id'] != "null") {
	$ctable_where .= " AND top_category_id IN (" . $_REQUEST['top_category_id'] . ")  ";
	$top_category_id = $_REQUEST['top_category_id'];
	$isFillter = true;
}

if ($isFillter) {
	$get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where, 0); //hold total records in variable

	//break records into pages
	$total_pages = ceil($get_total_rows / $item_per_page);

	//get starting position to fetch the records
	$page_position = (($page_number - 1) * $item_per_page);

	$ctable_r = $db->rp_getData($ctable, "*", $ctable_where . $where, "id DESC limit $page_position, $item_per_page", 0);
}
// $ctable_r = $db->rp_getData($ctable, "*", $ctable_where . $where, "id DESC",0,10);
?>
<style>
	.table-scrollable {
		width: auto;
		height: 450px;
		overflow-x: scroll;
		overflow-y: scroll;
		border: 1px solid #e7ecf1;
		margin: 10px 0 !important;
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
	<div style="text-align: -webkit-right;;" id="show-statics">

	</div>
	<div class="table-scrollable">
		<table id="datatable_super" class="table table-striped table-bordered table-hover">
			<thead class="fix-th">
				<tr>
					<th width="3%"></th>
					<th></th>
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
					<th>
						<select class="form-control" name="customer_type" id="customer_type" onChange="CustomerType(this.value);" autofocus>
							<option value="">Select Customer Type</option>
							<?php
							$customer_type_r = $db->rp_getData("customer_type", "*", "isDelete=0", 0);
							if (mysqli_num_rows($customer_type_r) > 0) {
								while ($customer_type_d = mysqli_fetch_array($customer_type_r)) {
							?>
									<option value="<?php echo $customer_type_d['id']; ?>" <?= ($customer_type == $customer_type_d['id']) ? "selected" : ""; ?>><?php echo $customer_type_d['name']; ?></option>
							<?php
								}
							}
							?>
						</select>
					</th>
					<th>
						<select class="form-control" name="price_list" id="price_list" onChange="price_list(this.value);" autofocus>
							<option value="">Select Price List</option>
							<?php
							$price_list_r = $db->rp_getData("price_list", "*", 0);
							if (mysqli_num_rows($price_list_r) > 0) {
								while ($price_list_d = mysqli_fetch_array($price_list_r)) {
							?>
									<option value="<?php echo $price_list_d['id']; ?>" <?= ($price_list == $price_list_d['id']) ? "selected" : ""; ?>><?php echo $price_list_d['pricelist_name']; ?></option>
							<?php
								}
							}
							?>
						</select>
					</th>
					<th>
						<select class="form-control" name="seid" id="seid" autofocus>
							<option value="">Select Sales Excutive</option>
							<?php
							$sales_person_r = $db->rp_getData("sales_executive", "*", "isDelete=0", "", 0);
							if (mysqli_num_rows($sales_person_r) > 0) {
								while ($sales_person_d = mysqli_fetch_array($sales_person_r)) {
							?>
									<option value="<?php echo $sales_person_d['id']; ?>" <?= ($seid == $sales_person_d['id']) ? "selected" : ""; ?>>
										<?php echo $sales_person_d['name']; ?>
									</option>
							<?php
								}
							}
							?>
						</select>
					</th>
					<th></th>
					<th></th>

					<th></th>

					<th></th>
					<th></th>

					<th>
						<select class="form-control status" style="width:118px;" name="state" id="state" onChange="filter_state(this.value);" autofocus>
							<option value="">Select State</option>
							<?php
							$state_r = $db->rp_getData("state", "*", 0);
							if (mysqli_num_rows($state_r) > 0) {
								while ($state_d = mysqli_fetch_array($state_r)) {
							?>
									<option value="<?php echo $state_d['name']; ?>" <?= ($state == $state_d['name']) ? "selected" : ""; ?>><?php echo $state_d['name']; ?></option>
							<?php
								}
							}
							?>
						</select>
					</th>
					<th>
						<select class="form-control status" style="width:118px;" name="main_city" id="main_city" onChange="filter_city(this.value);" autofocus>
							<option selected value="<?= $_REQUEST['main_city'] ?>">Select City</option>
							<!-- <?php
									if (isset($_REQUEST['main_city']) && $_REQUEST['main_city'] != "" && $_REQUEST['main_city'] != NULL) {
									}
									?> -->
						</select>
					</th>
					<th>
						<select class="form-control status" name="city" id="city" autofocus>
							<option selected value="<?= $_REQUEST['city'] ?>">Select Route</option>
						</select>
					</th>
					<th></th>
					<th></th>
					<th></th>
					<th>
						<!-- <label class="">Category</label> -->
						<select name="top_category_id" id="top_category_id" class="form-control top_category_id" multiple>
							<option value="">Select Category</option>
							<?php
							$top_category_id = explode(',', $top_category_id);
							$category_data_r = $db->rp_getData("top_category_master", "id,name", "isDelete=0", "", 0);
							while ($category_data_d = mysqli_fetch_array($category_data_r)) {

							?>

								<option <?= (in_array($category_data_d['id'], $top_category_id)) ? "selected" : "" ?> value="<?= $category_data_d['id'] ?>"><?= $category_data_d['name'] ?>
								</option>

							<?php
							}

							?>
						</select>
					</th>
					<!-- <th>
						<select class="form-control" name="zone" id="zone" >
	                        <option value="">Select Zone </option>
	                        <?php
							$zone_r = $db->rp_getData("zone", "*", "isDelete=0", 0);
							if (mysqli_num_rows($zone_r) > 0) {
								while ($zone_d = mysqli_fetch_array($zone_r)) {

							?>
	                                <option value="<?php echo $zone_d['id']; ?>" <?= ($zone == $zone_d['id']) ? "selected" : ""; ?>><?php echo $zone_d['name']; ?></option>
	                                <?php
								}
							}
									?>
						</select>
					</th> -->
					<th></th>

					<th></th>
					<th></th>
					<!-- <th></th>
					<th></th> -->
				</tr>
				<tr>
					<th class="fix-th1" width="3%"></th>
					<th class="fix-th1">No.</th>
					<th class="fix-th1">Type Of Company</th>
					<th class="fix-th1">Customer Type</th>
					<th class="fix-th1">Price List</th>
					<th class="fix-th1">Sales Person</th>
					<th class="fix-th1">Client Code</th>
					<th class="fix-th1">Firm Name</th>
					<th class="fix-th1">Person Name</th>
					<th class="fix-th1">Gst Number</th>
					<th class="fix-th1">Phone</th>
					<!-- <th class="fix-th1">Mobile</th> -->
					<!-- <th class="fix-th1">WhatsApp</th> -->
					<!-- <th class="fix-th1">Credit Limit</th>
					<th class="fix-th1">Credit Days</th> -->
					<th class="fix-th1">State</th>
					<th class="fix-th1">City</th>
					<th class="fix-th1">Route</th>
					<th class="fix-th1">Pincode</th>
					<th class="fix-th1">Turnover</th>
					<th class="fix-th1">Turnover Year</th>
					<th class="fix-th1">Category</th>
					<!-- <th class="fix-th1">Zone</th> -->
					<th class="fix-th1">Image path</th>

					<th class="fix-th1">Customer Entry</th>
					<th class="fix-th1">Customer<br> Create Date</th>
					<!-- <th class="fix-th1">Entry Type</th> -->
					<!-- <th class="fix-th1">Update Entry Type</th> -->

					<!-- <th>Customer From</th> -->
					<!-- <th>Order Customer</th> -->
					<!-- <th>Action</th> -->
				</tr>
			</thead>
			<tbody>
				<?php
				if ($isFillter) {
					if (mysqli_num_rows($ctable_r) > 0) {
						$entry_type_status = array("1" => "Admin Panel", "2" => "customer", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
						$count = 0;
						$CustomerArray = array("0" => "Normal Customer", "1" => "Inquiry Customer");
						$OrderCustomerArray = array("0" => "", "1" => "Order Customer");
						while ($ctable_d = mysqli_fetch_array($ctable_r)) {
							$class_id = $db->rp_getValue("executive_map_area", "class_id", "executive_id='" . $ctable_d['id'] . "' AND isDelete=0");
							$class_name = $db->rp_getValue("class", "name", "id='" . $class_id . "' AND isDelete=0");

							$area_id = $db->rp_getValue("executive_map_area", "area_id", "executive_id='" . $ctable_d['id'] . "' AND isDelete=0");
							$area_name = $db->rp_getValue("area", "name", "id='" . $area_id . "' AND isDelete=0");

							$is_create_system_user = $db->rp_getValue("dealer_distributor_network", "customer_id", "customer_id='" . $ctable_d['id'] . "' AND isDelete=0", 0);

							$is_quotation = $db->rp_getTotalRecord("quotation_detail", "customer_id='" . $ctable_d['id'] . "' AND isDelete=0", 0);
							$is_order = $db->rp_getTotalRecord("orders", "customer_id='" . $ctable_d['id'] . "' AND isDelete=0", 0);

							// echo "is_quotation=".$is_quotation;
							// echo "is_order=".$is_order;

							$is_order_account_approved = $db->rp_getTotalRecord("orders", "customer_id='" . $ctable_d['id'] . "' AND isDelete=0 AND status=4", 0);
							$total_customer_order = $db->rp_getTotalRecord("orders", "customer_id='" . $ctable_d['id'] . "' AND isDelete=0", 0);

							$is_order_waiting_for_acc_approve = $db->rp_getTotalRecord("orders", "customer_id='" . $ctable_d['id'] . "' AND isDelete=0 AND status=1", 0);

							if ($is_quotation == 0 && $is_order == 0 && $ctable_d['customer_flag'] == 1) {
								// light pink
								$style = "style='background-color: #FFB6C1;'";
							} else if ($is_quotation == 0 || $is_order == 0) {
								// sky blue
								$style = "style='background-color: #ADD8E6;'";
							} else if ($total_customer_order = $is_order_account_approved) {
								// light green
								$style = "style='background-color: #AEDCAE;'";
							} else if ($is_order_waiting_for_acc_approve > 0) {
								// light maroon
								$style = "style='background-color: #FF9377;'";
							}
							// if($is_order_account_approved>0 && $is_order_waiting_for_acc_approve>0)
							else {
								$style = "style='background-color: #fff;'";
							}
				?>
							<tr <?= $style ?>>
								<?php
								if ($_REQUEST['type'] == "1" || $_REQUEST['type'] == "2") {
								?>
									<td>
										<div class="btn-group">
											<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle">More</button>
											<ul role="menu" class="dropdown-menu">
												<!-- <li>
											<a href="#myModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Super Stockist">
												<span class="text-success">
													<i class="fa fa-circle"></i>&nbsp; View Information
												</span>
											</a>
										</li> -->

												<li>
													<a href="customer_view.php?id=<?php echo $ctable_d['id'] ?>" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Super Stockist">
														<span class="text-success">
															<i class="fa fa-circle"></i>&nbsp; View Customer
														</span>
													</a>
												</li>
												<!-- <li>
											<a style="margin-top: 5px;" href="add_executive_class_area.php?executive_id=<?php echo $ctable_d['id'] ?>&type=<?php echo $ctable_d['type_of_executive'] ?>" class="btn btn-success btn-sm" title="track">Add Class Area</a>
										</li> -->
											</ul>
										</div>
									</td>
								<?php
								} else {
								?>
									<td>
										<!-- <?php
												if ($rights['update_flag'] == 1) {
													$type_of_executive = $ctable_d['type_of_executive'];
													if ($type_of_executive == '1') {
												?>
									<a class="btn btn-info btn-sm" onClick="window.location.href='executive_crud.php?mode=edit&type=1&id=<?php echo $ctable_d['id']; ?>'" title="Edit">
										<i class="fa fa-pencil"></i>
									</a>
									<?php
													} else if ($type_of_executive == '2') {
									?>
									<a class="btn btn-info btn-sm" onClick="window.location.href='executive_crud.php?mode=edit&type=2&id=<?php echo $ctable_d['id']; ?>'" title="Edit">
										<i class="fa fa-pencil"></i>
									</a>
									<?php
													} else {
									?>
									<a class="btn btn-info btn-sm" onClick="window.location.href='executive_crud.php?mode=edit&type=3&id=<?php echo $ctable_d['id']; ?>'" title="Edit">
										<i class="fa fa-pencil"></i>
									</a>
									<?php
													}
												}
									?> -->
										<div class="btn-group">
											<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
												<i class="fa fa-gear"></i>
											</button>
											<ul role="menu" class="dropdown-menu">
												<?php
												if ($rights['update_flag'] == 1) {
												?>
													<li>
														<?php
														$type_of_executive = $ctable_d['type_of_executive'];

														/*if($type_of_executive=='1')
																					{
																						?>
																						<a onClick="window.location.href='executive_crud.php?mode=edit&type=1&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><span class="text-success"><i class="fa fa-pencil"></i></span>&nbsp;Edit</a>
																						<?php 
																					} 
																					else if($type_of_executive=='2')
																					{
																						?>
																						<a onClick="window.location.href='executive_crud.php?mode=edit&type=2&id=<?php echo $ctable_d['id']; ?>'"  title="Edit"><span class="text-success"><i class="fa fa-pencil"></i></span>&nbsp;Edit</a>
																						<?php 
																					} 
																					else
																					{
																						?>
																						<a onClick="window.location.href='executive_crud.php?mode=edit&type=3&id=<?php echo $ctable_d['id']; ?>'" target="_blank" title="Edit"><span class="text-success"><i class="fa fa-pencil"></i></span>&nbsp;Edit</a>
																						<?php 
																					}*/
														?>
														<?php
														if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "prospect") {
															$edit_url = "mode=edit&flag=prospect";
														} else {
															$edit_url = "mode=edit";
														}
														?>
														<a href='executive_crud.php?type=<?= $type_of_executive; ?>&id=<?php echo $ctable_d['id']; ?>&<?= $edit_url ?>' target="_blank" title="Edit"><span class="text-success"><i class="fa fa-pencil"></i></span>&nbsp;Edit</a>
													</li>


													<li>
														<?php
														$type_of_executive = $ctable_d['type_of_executive'];
														?>
														<?php
														if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "prospect") {
															$edit_url = "mode=edit&flag=prospect";
														} else {
															$edit_url = "mode=edit";
														}
														?>

														<a href='quotation_crud.php?mode=add&customer_id=<?php echo $ctable_d['id']; ?>' target="_blank" title="Quotation"><span class="text-info"><i class="fa fa-file"></i></span>&nbsp;Quotation</a>
													</li>

													<li>
														<?php
														if ($rights['delete_flag'] == 1) {
														?>
															<a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete">
																<span class="text-danger">
																	<i class="fa fa-times"></i>
																	&nbsp;Delete
																</span>
															</a>
														<?php
														}
														?>
													</li>
													<!-- <li>
																							<a   data-toggle="modal" data-target="#viewCustomerModal" onclick="getCustomerModuleCount('<?php echo $ctable_d['id']; ?>');"><i  class="fa fa-eye"></i> View</a>
																						</li> -->
													<li>
														<?php
														if ($ctable_d['isActive'] == 0) {
														?>
															<a href="<?php echo $ctable; ?>_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=1&flag=<?= $_REQUEST['flag'] ?>" title="Activate">
																<span class="text-success">
																	<i class="fa fa-circle"></i>
																	&nbsp;Activate
																</span>
															</a>
														<?php
														} else {
														?>
															<a href="<?php echo $ctable; ?>_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=0&flag=<?= $_REQUEST['flag'] ?>" title="Deactivate">
																<span class="text-danger">
																	<i class="fa fa-circle-o"></i>
																	&nbsp; Deactivate
																</span>
															</a>
														<?php
														}
														?>
													</li>
													<!-- <li>
																							<a href="#myModal" data-type="<?php echo $type_of_executive; ?>" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Super Stockist">
																								<span class="text-success">
																									<i class="fa fa-circle"></i>
																									&nbsp; View Information
																								</span>
																							</a>
																						</li> -->
													<li>
														<a class="" href="#changePasswordModal" data-id="<?php echo $ctable_d['id']; ?>" class="btn sbold blue-ebonyclay" data-toggle="modal" title="Change Password"> <i class="fa fa-gear"></i>&nbsp;Change Password</a>
													</li>
													<!-- <li>
																							<a href="customer_view.php?id=<?php echo $ctable_d['id'] ?>" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Super Stockist">
																								<span class="text-success">
																									<i class="fa fa-circle"></i>&nbsp; View Customer
																								</span>
																							</a>
																						</li> -->
													<li>
														<a href="add_executive_class_area.php?executive_id=<?php echo $ctable_d['id'] ?>&type=<?php echo $ctable_d['type_of_executive'] ?>" class="" title="track">
															<span class="text-warning">
																<i class="fa fa-circle"></i>
																&nbsp; Add Class Area
															</span>
														</a>
													</li>
													<?php
													if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "prospect") {
													?>

														<li>
															<a onClick="change_to_customer('<?php echo $ctable_d['id']; ?>');" title="change to customer">
																<span class="text-success">
																	<i class="fa fa-circle"></i>
																	&nbsp;Change To Customer
																</span>
															</a>
														</li>
													<?php
													}
													if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "") {
														$SEID = $db->rp_getvalue("dealer_distributor_network", "sales_executive_id", "id='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "' ", 0);
														/*if($SEID==0)
																							{
																								$SEID=1;
																							}*/
													?>
														<li>
															<a href="followup.php?mode=customer_followup&executive_id=<?php echo $ctable_d['id'] ?>&sales_id=<?= $SEID; ?>" class="" title="Followup"><span class="text-success"><i class="fa fa-circle"></i>&nbsp; Followup</span></a>
														</li>
														<?php
														if ($db->checkUserPermission(669, $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {

														?>
															<li>
																<a href="payment_followup_manage.php?mode=customer_payment_followup&executive_id=<?php echo $ctable_d['id'] ?>&sales_id=<?= $SEID; ?>"
																	title="Followup"
																	style="color: #28a745; text-decoration: none;">

																	<i class="fa fa-circle"></i> Payment Followup
																</a>
															</li>
														<?php
														}
													}
													if ($ctable_d['isActive'] == 1 && $ctable_d['isDelete'] == 0) {
														if ($is_create_system_user == '' || $is_create_system_user == 0 || $is_create_system_user == -1) {
														?>

															<li>
																<a onClick="CustomerUserCreate('<?php echo $ctable_d['id']; ?>');" title="Create System User">
																	<span class="text-success">
																		<i class="fa fa-circle"></i>
																		&nbsp;Create System User
																	</span>
																</a>
															</li>
												<?php
														}
													}
												}
												?>
												<li>
													<a href='visit_manage.php?redirecttocustomer=1&customer_id=<?php echo $ctable_d['id']; ?>' target="_blank" title="Show visit"><span class="text-info"><i class="fa fa-map-marker"></i></span>&nbsp;Show Visit</a>
												</li>
											</ul>
										</div>
									</td>
								<?php
								}
								?>


								<td><?php echo ++$count; ?></td>
								<td><?php echo   $db->rp_getValue("company_master", "name", "id='" . $ctable_d['type_of_company'] . "'"); ?></td>
								<td><?php echo $customer_type = $db->rp_getValue("customer_type", "name", "id='" . $ctable_d['type_of_executive'] . "'", 0); ?></td>
								<td><?= $db->rp_getValue("price_list", "pricelist_name", "id='" . $ctable_d['price_list_id'] . "' AND isDelete=0"); ?></td>
								<td>
									<?= $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['seid'] . "' AND isDelete=0", 0); ?>
								</td>

								<td><?= $ctable_d['client_code']; ?></td>

								<td><span <?php echo ($ctable_d['isActive'] == 0) ? "class='text-danger' style='font-size:17px;'" : "style=' font-size:17px;'"; ?>><strong><?php echo stripslashes($ctable_d['company_name']); ?></strong>

								</td>
								<td><?php echo stripslashes($ctable_d['cname']); ?></td>
								<td><?php
									if ($ctable_d['gst'] != "") {
									?>
									<?php
										echo stripslashes($ctable_d['gst']);
									}
									?></td>

								<!-- <td><?php echo stripslashes($ctable_d['cname']); ?></td> -->

								<td>
									<?php echo stripslashes($ctable_d['mobile_no1']); ?>
									<?php echo $ctable_d['email'] ? "<br><b>Email: " . stripslashes($ctable_d['email']) . "</b>" : ''; ?>
								</td>
								<!-- <td><?php echo stripslashes($ctable_d['mobile_no1']); ?></td> -->
								<!-- <td><?php echo stripslashes($ctable_d['whatsapp_no']); ?></td> -->
								<!-- <td><?php echo stripslashes($ctable_d['credit_limit']); ?></td>
							<td><?php echo stripslashes($ctable_d['credit_day']); ?></td> -->
								<td><?php echo $ctable_d['state'] . " <br/><p style='color:green;'>" . $class_name . "</p>"; ?></td>
								<td><?php echo $ctable_d['main_city']; ?></td>
								<td><?php echo $ctable_d['city'] . " <br/><p style='color:green;'>" . $area_name . "</p>"; ?></td>
								<td><?php echo $ctable_d['zip']; ?></td>
								<td><?= $ctable_d['turnover'] ?></td>
								<td><?= $ctable_d['turnover_year'] ?></td>
								<td><?php
									$catid = explode(",", $ctable_d['top_category_id']);
									$cat_name = array();
									for ($j = 0; $j < sizeof($catid); $j++) {

										$cat_name[] = $db->rp_getValue("top_category_master", "name", "isDelete=0 AND id='" . $catid[$j] . "'", 0);
									}
									echo implode($cat_name, ", ");

									?>
								</td>
								<!-- <td><?= $db->rp_getValue("zone", "name", "id='" . $ctable_d['zone'] . "' AND isDelete=0"); ?></td> -->

								<td>
									<?php
									if ($ctable_d['image_path'] != "") {
										//echo $ctable_d['image_path'];exit();
										$img = explode(",", $ctable_d['image_path']);
										$imgpath = array();
										for ($i = 0; $i < sizeof($img); $i++) {
											$imgpath[] = "../resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $ctable_d["id"] . "' AND id='" . $img[$i] . "'", 0);
										}

										for ($i = 0; $i < sizeof($imgpath); $i++) {
											if ($i == 0) {
									?>
												<a href="<?= $imgpath[$i] ?>" data-lightbox="Customer<?= $count ?>" data-title="Customer <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 80px;"></a>
											<?php
											} else {
											?>
												<div class="hidden">
													<a href="<?= $imgpath[$i] ?>" data-lightbox="Customer<?= $count ?>" data-title="Customer <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 80px;"></a>
												</div>
										<?php
											}
										}
									} else {
										$img = $ctable_d['image_path'] = DEFAULTIMG;
										?>
										<a href="<?= $img ?>" data-lightbox="Customer<?= $count ?>" data-title="Customer <?= $ctable_d['id'] ?>"><img src="<?= $img ?>" style="height: 80px;"></a>
									<?php
									}
									?>
								</td>

								<td>
									<?php
									$lead_time = $db->rp_getValue("no_order_inquiry", "created_date", "isDelete=0 AND dealer_id='" . $ctable_d['id'] . "'", 0);
									$date = date('Y-m-d', strtotime($ctable_d['created_date']));
									$customer_order = $db->rp_getValue("orders", "customer_id", "customer_id='" . $ctable_d['id'] . "' AND isDelete=0 AND isActive=1", 0);

									if ($customer_order) {
										echo "Prospect Order Convert to Customer";
									} else if ($lead_time) {
										echo "Inquiry To Lead";
									} else if ($ctable_d['entry_flag'] == "1") {
										echo "Direct Customer";
									} else if ($ctable_d['entry_flag'] == "5") {
										echo "Sales App";
									}
									?>
								</td>
								<td><?= date('d-m-Y h:i:s a', strtotime($ctable_d['created_date'])) ?></td>
								<!-- <td><?php echo $entry_type_status[$ctable_d['entry_flag']]; ?></td>
								<td><?php echo $entry_type_status[$ctable_d['update_entry_flag']]; ?></td> -->

								<!-- <td><?php echo $CustomerArray[$ctable_d['customer_from']]; ?></td> -->
								<!-- <td><?php echo $OrderCustomerArray[$ctable_d['order_customer_flag']]; ?></td> -->
								<!-- <td><a style="margin-top: 5px;" href="add_executive_class_area.php?executive_id=<?php echo $ctable_d['id'] ?>&type=<?php echo $ctable_d['type_of_executive'] ?>" class="btn btn-success btn-sm" title="track">Add Class Area</a></td> -->

								<?php
								if ($_REQUEST['type'] == "1" || $_REQUEST['type'] == "2") {
								?>
									<td>
										<div class="btn-group">
											<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle">
												More
											</button>
											<ul role="menu" class="dropdown-menu">
												<li>
													<a href="#myModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Super Stockist"><span class="text-success"><i class="fa fa-circle"></i>&nbsp; View Information</span></a>
												</li>

											</ul>
										</div>
									</td>
								<?php
								} else {
								?>

								<?php
								}
								?>


							</tr>
						<?php
						}
					} else {
						?>
						<tr>
							<td colspan="17">
								<p style="text-align:center;">No data available in table</p>
							</td>
						</tr>
					<?php
					}
				} else {
					?>
					<tr>
						<td colspan="23">
							<h2 style="text-align:center;"><strong>Use Filter For Find Customer</strong></h2>
						</td>
					</tr>
				<?php
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
	$("#state").select2();
	$("#city").select2();
	$("#customer_type").select2();
	$("#price_list").select2();
	$("#zone").select2();
	$("#main_city").select2();
	$("#type_of_company").select2();
	$("#seid").select2();

	function filter_state(state_id, city = "") {
		// alert(state_id+" "+ city);
		if (state_id != "") {
			$("#main_city").select2('val', "");
			$("#city").select2('val', "");
		}
		$.ajax({
			type: "POST",
			url: "find_city.php",
			data: 'state_id=' + state_id + "&city=" + city,
			beforeSend: function() {
				$("#loading-modal").modal('show');
				// $('.preloader').fadeIn('slow');
			},
			success: function(data) {
				// $("#main_city").select2("destroy");
				// alert(data)
				$("#main_city").html(data);
				$("#main_city").select2();
				$("#loading-modal").modal('hide');
				// $('.preloader').fadeOut('slow');
			}
		});
	}

	function filter_city(main_city, route = "") {
		// alert(main_city+" "+ route);
		$.ajax({
			type: "POST",
			url: "find_city.php",
			data: 'main_city=' + main_city + "&city=" + route,
			beforeSend: function() {
				$("#loading-modal").modal('show');
				// $('.preloader').fadeIn('slow');
			},
			success: function(data) {
				// $("#city").select2("destroy");
				$("#city").html(data);
				$("#city").select2();
				$("#loading-modal").modal('hide');
				// $('.preloader').fadeOut('slow');
			}
		});
	}

	function showStatics() {
		//alert("hello");
		//var where = "<?= $ctable_where ?>";
		$.ajax({
			type: 'POST',
			url: 'show_statics_ajax.php',
			data: {
				//where:where,
				mode: 'customer',
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
	$(".top_category_id").fSelect();
</script>
<?php require_once 'disconnect.php';  ?>