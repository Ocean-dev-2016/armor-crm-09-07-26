<?php
$page_id = 607;
$page_slug = 'quotation';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "quotation_detail";
$ctable1 	= "Quotation";
$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : "";
$quotation_type = isset($_REQUEST['quotation_type']) ? $_REQUEST['quotation_type'] : "";
$ctable_where = "";
// Get the total number of rows in the table
if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
	$ctable_where .= " (customer_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR company_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR quotation_no like '%" . $db->clean($_REQUEST['searchName']) . "%'  OR client_code like '%" . $db->clean($_REQUEST['searchName']) . "%' ) AND ";
}

if (isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id'] != "") {
	$ctable_where .= "  sales_id = '" . $db->clean($_REQUEST['sales_executive_id']) . "' AND";
}

if (isset($_REQUEST['inquiry_id']) && $_REQUEST['inquiry_id'] != "") {
	$ctable_where .= "  inquiry_id = '" . $db->clean($_REQUEST['inquiry_id']) . "' AND";
}

if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "") {
	$ctable_where .= "  customer_type = '" . $db->clean($_REQUEST['customer_type']) . "' AND";
}
if (isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company'] != "") {
	$ctable_where .= "  type_of_company = '" . $db->clean($_REQUEST['type_of_company']) . "' AND";
}

if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
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
} else {
	$ctable_where .= " isDelete=0 AND status!=-1";
}

// if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0 || $rights['all_data_flag'] == 1)
// {
// 	$ctable_where .= " isDelete=0 AND status!=-1";
// } 
// else if($rights['chain_vise_flag'] == 1)
// {	
//     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

//     $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
//     if ($get_sales_type== "sales_manager") 
//     {
//         $sales_executive_type = "Regional Sales Manager";
//         $key="sm_id";
//         $WhereCondition.=' ' .$key.'='.$check_id;
//     }

//     else if ($get_sales_type == "area_sales_manager") 
//     {
//         $sales_executive_type = "National Sales Manager";//Business Development Manager
//         $key="asm_id";
//         $WhereCondition.=' ' .$key.'='.$check_id;
//     }

//     else if ($get_sales_type == "sales_officer") 
//     {
//         $sales_executive_type = "Area Sales Manager";//Area Sales Manager
//         $key="so_id";
//         $WhereCondition.=' ' .$key.'='.$check_id;
//     }
//     else if ($get_sales_type == "sales_executive") 
//     {
//         $sales_executive_type = "Sales Officer";
//         $key="se_id";
//         $WhereCondition.=' ' .$key.'='.$check_id;
//     }
//     else
//     {
//     	$WhereCondition.=' type = "service_engineer"';
//     }

//     $data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);

//     $SALEID1=array();
// 	if($data)
// 	{
// 		while($data_d=mysqli_fetch_assoc($data))
// 		{
// 			$SALEID1[]=$data_d['id'];
// 		}
// 	}
// 	if(!empty($SALEID1))
// 	{
// 		$SALEID1=implode(",", $SALEID1);

// 			$ctable_where .= " isDelete=0 AND status!=-1 AND sales_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	


// 	}
// 	else
// 	{
// 			$ctable_where .= " isDelete=0 AND status!=-1 AND sales_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
// 	}
// }
// else 
// {
// 	/*$ctable_where .= " isDelete=0 AND status!=-1 AND created_by='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "'";*/
// 	$ctable_where .= " isDelete=0 AND status!=-1 AND sales_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
// }
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
if (isset($_REQUEST['status']) && $_REQUEST['status'] != "" && $_REQUEST['status'] != NULL) {
	$ctable_where .= " AND status = '" . $_REQUEST['status'] . "' ";
}
///For ToDate & FromDate
// if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL && $_REQUEST['ToDate'] != undefined) {
// 	$ctable_where .= " AND quotation_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
// }

// if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL && $_REQUEST['FromDate'] != undefined) {
// 	$ctable_where .= " AND quotation_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
// }

if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined && $_REQUEST['todate'] != "01-01-1970") {
	// echo $_REQUEST['todate'];die;
	$ctable_where .= " AND (DATE(quotation_date) >= '" . $_REQUEST['todate'] . "' ";
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined && $_REQUEST['fromdate'] != "01-01-1970") {
	$ctable_where .= " AND DATE(quotation_date) <= '" . $_REQUEST['fromdate'] . "') ";
}


if (isset($_REQUEST['df']) && $_REQUEST['df'] != "" && $_REQUEST['df'] != NULL && $_REQUEST['df'] != undefined) {
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode($_REQUEST['df']);

	$date_filter_query_ex = explode(" to ", $date_filter_query);

	$ctable_where .= " AND ( DATE(quotation_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(quotation_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
}

if (isset($_REQUEST['quotation_month']) && $_REQUEST['quotation_month'] != "" && $_REQUEST['quotation_month'] != NULL) {
	$ctable_where .= " AND MONTH(quotation_date) = '" . $_REQUEST['quotation_month'] . "'";
}

if (isset($_REQUEST['quotation_year']) && $_REQUEST['quotation_year'] != "" && $_REQUEST['quotation_year'] != NULL) {
	$ctable_where .= " AND YEAR(quotation_date) = '" . $_REQUEST['quotation_year'] . "'";
}

if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != NULL) {
	$ctable_where .= " AND customer_id = '" . $_REQUEST['customer_id'] . "'";
}

if (isset($_REQUEST['type']) && $_REQUEST['type'] != "" && $_REQUEST['type'] != NULL) {
	$ctable_where .= " AND customer_type = '" . $_REQUEST['type'] . "' ";
}
if (isset($_REQUEST['sales_id']) && $_REQUEST['sales_id'] != "" && $_REQUEST['sales_id'] != NULL  && $_REQUEST['sales_id'] != undefined) {
	$ctable_where .= " AND sales_id = '" . $_REQUEST['sales_id'] . "' ";
}
$get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where); //hold total records in variable
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

	.table_amount {
		width: 40%;
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
	<div class="table-scrollable">

		<!-- <div style="text-align: right;" id="total-quotation-value"></div> -->
		<!-- <div style="text-align: right;" id="total-quotation-value-approve"></div> -->
		<!-- <div style="text-align: right;" id="total-quotation-value-pending"></div> -->
		<!-- <div style="text-align: right;" id="total-quotation-value-cancel"></div> -->
		<!-- <div style="text-align: right;" id="total-quotation-value-lost"></div> -->
		<!-- <div style="text-align: right;" id="total-quotation-value-disapprove"></div> -->
		<!-- <div style="text-align: right;" id="total-quotation-value-order"></div> -->
		<div style="text-align: -webkit-right;" id="show-statics">

		</div>
		<span style="color: red;font-size: 14px;font-style: italic;"><?= CURRENT_DATA_INFO ?></span>

		<table id="datatable_1" class="table table-striped table-bordered table-hover">
			<thead class="fix-th">
				<!-- <tr>

        			<th colspan="15" style="text-align: right;" id="total-quotation-value">
        			Total Amount: 
        			</th>
        		</tr> -->
				<tr>
					<!-- <th></th> -->
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>
						<select class="form-control input-small" id="inquiry_id">
							<option value="">Select Inquiry</option>
							<?php
							$inq_r = $db->rp_getData("no_order_inquiry", "id", "isDelete=0", "", 0);
							if ($inq_r) {
								while ($inq_d = mysqli_fetch_assoc($inq_r)) {
							?>
									<option value="<?= $inq_d['id'] ?>" <?= ($inq_d['id'] == $_REQUEST['inquiry_id']) ? "selected" : ""; ?>><?= "#INQ/" . $inq_d['id']; ?></option>
							<?php
								}
							}
							?>
						</select>
					</th>
					<th width=100%>
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
							<option value="0" <?= ("0" == $_REQUEST['status']) ? "selected" : ""; ?>>Pending</option>
							<option value="1" <?= ("1" == $_REQUEST['status']) ? "selected" : ""; ?>>Approved</option>
							<option value="3" <?= ("3" == $_REQUEST['status']) ? "selected" : ""; ?>>Cancelled</option>
							<!-- <option value="-1" <?= ("-1" == $_REQUEST['status']) ? "selected" : ""; ?>>Add to Cart</option> -->
							<option value="4" <?= ("4" == $_REQUEST['status']) ? "selected" : ""; ?>>Order Generated</option>
							<option value="5" <?= ("5" == $_REQUEST['status']) ? "selected" : ""; ?>>Lost</option>
						</select>
					</th>
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
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<!-- <th></th> -->
					<th>
						<!-- Sales Person Name -->
						<?php
						if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
						?>
							<select class="form-control input-small" id="sales_id">
								<option value="">Select Sales Person Name</option>
								<?php
								$salesExe = $db->rp_getData("sales_executive", "*", "isDelete=0 AND isActive=1 ", "", 0);
								if ($salesExe) {
									while ($salesD = mysqli_fetch_assoc($salesExe)) {
								?>
										<option value="<?= $salesD['id'] ?>" <?= ($salesD['id'] == $_REQUEST['sales_id']) ? "selected" : ""; ?>><?= $salesD['name']; ?></option>
								<?php
									}
								}
								?>
							</select>
						<?php
						}
						?>
					</th>
					<th>
						<select class="form-control input-small" id="type">
							<option value="">Select Quotation Type</option>
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
					<th></th>
					<th></th>
					<!-- <th></th>
					<th></th> -->
				</tr>
				<tr>
					<th class="fix-th1"></th>
					<th class="fix-th1">No.</th>
					<th class="fix-th1">Followup</th>
					<th class="fix-th1">Quotation No.</th>
					<!-- <th class="fix-th1">Revised From <br/> Quotation No.</th> -->
					<th class="fix-th1">Inquiry No.</th>
					<th class="fix-th1">Quotation Date</th>
					<th class="fix-th1">Status</th>
					<th class="fix-th1">Approved By</th>
					<th class="fix-th1">Type Of Company</th>
					<th class="fix-th1">Firm Name</th>
					<th class="fix-th1">Person Name</th>
					<th class="fix-th1">Client Code</th>
					<th class="fix-th1">Company Mobile No</th>
					<!-- <th class="fix-th1">Person Name</th> -->
					<th class="fix-th1">Sales Person Name</th>
					<th class="fix-th1">Quotation Type</th>
					<th class="fix-th1" style="text-align:right;">Quotation Amount</th>
					<th class="fix-th1">Lost Reason</th>
					<th class="fix-th1">Quotation Attachment</th>
					<!-- <th class="fix-th1">Entry Type</th> -->
					<!-- <th class="fix-th1">Update Entry Type</th> -->
				</tr>
			</thead>
			<tbody>
				<?php
				if ($ctable_r) {
					$count = 0;
					$sales_name = '';
					while ($ctable_d = mysqli_fetch_array($ctable_r)) {
						if ($ctable_d["attachment"] != "") {
							$quotation_img = ADMINSITEURL . QUOTATION_ATTACHMENT_A . $ctable_d["attachment"];
						} else {
							$quotation_img = "";
						}


						$entry_type_status = array("1" => "Admin Panel", "2" => "customer", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
						/*$customer = $db->rp_getValue('executive', 'isActive', "id=" . $ctable_d['customer_id'] . "", 0);
						if ($customer == 0) {
							continue;
						}*/

						if ($ctable_d['status'] == 4) {
							$style = "style='background-color: #cef5c7;'";
						} else if ($ctable_d['status'] == 5) {
							$style = "style='background-color: #f1acac;'";
						} else if ($ctable_d['status'] == 1) {
							$style = "style='background-color: #FFFF99;'";
						} else if ($ctable_d['status'] == 0) {
							$style = "style='background-color: #add8e6;'";
						} else if ($ctable_d['status'] == 3) {
							$style = "style='background-color: #ec9b97;'";
						} else {
							$style = "";
						}

				?>
						<tr <?= $style ?>>
							<td>
								<?php $ctable_d['id'];
								if ($rights['update_flag'] == 1) {
								?>
									<div class="btn-group">
										<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
											<i class="fa fa-gear"></i>
										</button>
										<ul role="menu" class="dropdown-menu">
											<?php
											$revisedCount = $db->rp_getTotalRecord("quotation_detail", "refrence_id='" . $ctable_d['id'] . "' AND isDelete=0");
											if ($revisedCount <= 0) {
												if ($ctable_d['status'] == 1) {
													$order_id = $db->rp_getValue("orders", "id", "quotation_id='" . $ctable_d['id'] . "' AND isDelete=0");
											?>
													<li>
														<a onclick="GenerateOrder('<?= $ctable_d['id'] ?>')" title="Generate Order">
															<span class="text-warning">
																<i class="fa fa-shopping-cart"></i>
																&nbsp;Generate Order
															</span>
														</a>
													</li>
												<?php
												}
											}
											if ($rights['update_flag'] == 1 && $ctable_d['status'] != 4) {
												/*if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
												{*/
												$revisedCount = $db->rp_getTotalRecord("quotation_detail", "refrence_id='" . $ctable_d['id'] . "' AND isDelete=0");
												if ($revisedCount <= 0) {
												?>
													<li>
														<a href="quotation_crud.php?mode=edit&id=<?= $ctable_d['id'] ?>" title="Edit">
															<span class="text-success">
																<i class="fa fa-pencil"></i>
																&nbsp;Edit
															</span>
														</a>
													</li>
												<?php
												}
												/*}*/
											}
											// if ($rights['update_flag'] == 1) 
											// {
											/*if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
												{*/
											/*$revisedCount = $db->rp_getTotalRecord("quotation_detail","refrence_id='".$ctable_d['id']."' AND isDelete=0");
													if($revisedCount<=0)
													{
														?>
														<li>
															 <a href="followup.php?mode=quotation_followup&quotation_id=<?php echo $ctable_d['id']?>&sales_id=<?php echo $ctable_d['sales_id']?>" target="_blank"  title="track">
																<span class="text-success">
																	<i class="fa fa-file fa-lg fa-fw"></i>
																Follow Up
																</span>
															</a>
														</li>
														<?php
													}*/
											/*}*/
											// }
											if ($rights['delete_flag'] == 1 && $ctable_d['id'] != -1 && $ctable_d['status'] == 0) {
												$total_dispatch_record = $db->rp_getTotalRecord("dispatch_map_order", "quotation_id='" . $ctable_d['id'] . "' AND isDelete=0");
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
											$file_path = "quotation_viewer.php?quotation_id=" . $ctable_d['id'] . "";
											?>
											<li>
												<a class="" target="_blank" href="<?php echo $file_path; ?>" title="save">
													<i class="fa fa-file-pdf-o"></i>
													Download
												</a>
											</li>

											<?php if ($ctable_d['status'] != 4 && $ctable_d['status'] != 5) { ?>
												<li>
													<a class="" data-toggle="modal" data-target="#lostModal" onclick="LostAdd(<?= $ctable_d['id'] ?>);">
														<span class="text-success">
															<i class="fa fa-chain-broken"></i>
															&nbsp;Lost
														</span>
													</a>
												</li>
											<?php } ?>

											<?php
											if ($ctable_d['status'] == 1) {
												$revisedCount = $db->rp_getTotalRecord("quotation_detail", "refrence_id='" . $ctable_d['id'] . "' AND isDelete=0");
												if ($revisedCount <= 0) {
											?>
													<!-- <li>
														<a class="" target="_blank" href="quotation_crud.php?mode=add&quotation_id=<?= $ctable_d['id'] ?>" title="save"><i class="fa fa-file-pdf-o"></i> Revised Quotation</a>
													</li -->
											<?php
												}
											}
											?>
										</ul>
									</div>
								<?php

								}
								?>
							</td>
							<td><?php echo ++$count; ?></td>
							<td style="text-align: center;">
								<?php
								$SEID = $db->rp_getvalue("dealer_distributor_network", "sales_executive_id", "id='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "' ", 0);
								$revisedCount = $db->rp_getTotalRecord("quotation_detail", "refrence_id='" . $ctable_d['id'] . "' AND isDelete=0");
								if ($revisedCount <= 0) {
								?><a href="followup.php?mode=quotation_followup&quotation_id=<?php echo $ctable_d['id'] ?>&sales_id=<?php echo $SEID ?>&visitor_id=<?php echo $ctable_d['customer_id'] ?>" target="_blank" title="track">
										<span class="text-success">
											<i class="fa fa-eye"></i>
										</span>
									</a>
								<?php
								}
								?>
							</td>
							<td><a href="quotation_viewer.php?quotation_id=<?= $ctable_d['id'] ?>"><span class="text-success"><?php echo stripslashes($ctable_d['quotation_no']); ?></span></a></td>
							<!-- <td><a href="quotation_viewer.php?quotation_id=<?= $ctable_d['refrence_id'] ?>"><span class="text-success"><?php echo $db->rp_getValue("quotation_detail", "quotation_no", "id='" . $ctable_d['refrence_id'] . "'"); ?></span></a></td> -->
							<td><?= "#INQ/" . $ctable_d['inquiry_id']; ?></td>
							<td>
								<?php
								if ($ctable_d['quotation_date'] != "0000-00-00") {
									echo date('d-m-Y', strtotime($ctable_d['quotation_date']));
								} else {
									echo "";
								}

								?>

							</td>
							<?php
							if ($ctable_d['status'] == -2) {
								$status = "Disapproved";
							} else if ($ctable_d['status'] == 0) {
								$status = "Pending";
							} else if ($ctable_d['status'] == 1) {
								// $status = "Order Generated";
								$status = "Approved";
							} else if ($ctable_d['status'] == 3) {
								// $status="Cancelled <br><b>Reason For Cancel</b><br/><span class='text-danger'>".$ctable_d['reason_of_cancel_order']."</span>";
								$status = "Cancelled";
							} else if ($ctable_d['status'] == -1) {
								$status = "Add to Cart";
							} else if ($ctable_d['status'] == 4) {
								$status = "Order Generated";
							} else if ($ctable_d['status'] == 5) {
								$status = "Lost";
							}
							$customer_flag = "";
							if ($ctable_d['customer_flag'] == 1) {
								$customer_flag = " - P";
							} else if ($ctable_d['customer_flag'] == 0) {
								$customer_flag = " - C";
							}
							?>
							<td><?php $ctable_d['status']; ?><?php echo stripslashes($status); ?></td>
							<td>
								<?php
								$approvedByName = "";
								if (!empty($ctable_d['approve_by_id'])) {
									$approvedByName = $db->rp_getValue("dealer_distributor_network", "name", "id='" . (int) $ctable_d['approve_by_id'] . "'", 0);
								}
								echo ($approvedByName != "") ? stripslashes($approvedByName) : "-";
								?>
							</td>
							<td>
								<?php echo $db->rp_getValue("company_master", "name", "id='" . $ctable_d['type_of_company'] . "'"); ?>
							</td>
							<td>
								<?= stripslashes($ctable_d['company_name']) . $customer_flag; ?>
							</td>
							<td>
								<?= stripslashes($ctable_d['customer_name']) ?>
							</td>
							<td>
								<?= stripslashes($ctable_d['client_code']) ?>
							</td>
							<td>
								<a
									target="_blank"
									href="https://api.whatsapp.com/send?phone=91<?php echo stripslashes($ctable_d['contact_number']); ?>&text=<?= $sms; ?>"><?php echo stripslashes($ctable_d['mobile_no']); ?>
									<?php echo $ctable_d['contact_number']; ?>
								</a>
							</td>
							<!-- <td><?php echo stripslashes($ctable_d['customer_name']); ?></td> -->
							<?php
							$sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['sales_id'] . "'");
							?>
							<td>
								<?php if ($sales_name == "") {
									echo "Admin";
								} else {
									echo $sales_name;
								}
								?>
							</td>
							<td>
								<?php
								/*if($ctable_d['customer_type']=='1')
									{
										$slug="Super Stockist";
									}
									else if($ctable_d['customer_type']=='2')
									{
										$slug="Distributor";
									}
									else if($ctable_d['customer_type']=='3')
									{
										$slug="Dealer";
									}
									else if($ctable_d['customer_type']=='normal_user')
									{
										$slug="Normal Customer";
									}*/
								echo stripslashes($db->rp_getValue("customer_type", "name", "id='" . $ctable_d['customer_type'] . "'"));
								?>
							</td>
							<td align="right"><?php echo stripslashes(CURR . $db->rp_num(round($ctable_d['grand_total']))); ?></td>
							<td><?php echo $ctable_d['lost_reason']; ?></td>
							<td>
								<?php
								if ($quotation_img != "") {
								?>
									<a href="<?= $quotation_img ?>" download class="text-warning" title="View"><i class="fa fa-download" style="font-size: 15px;"></i></a>&nbsp;&nbsp;&nbsp;
									<a href="<?= $quotation_img ?>" target="_blank" class="text-sucess" title="View"><i class="fa fa-eye" style="font-size: 15px;"></i></a>
								<?php
								}
								?>
							</td>
							<!-- <td><?php echo $entry_type_status[$ctable_d['entry_flag']]; ?></td>
							<td><?php echo $entry_type_status[$ctable_d['update_entry_flag']]; ?></td> -->
						</tr>
				<?php
						//$t_quotation_val += $ctable_d['grand_total']; 
					}
					// $t_quotation = $db->rp_getTotalRecord("quotation_detail",$ctable_where,0);
					// $t_quotation_val = $db->rp_getValue("quotation_detail","SUM(grand_total)",$ctable_where,0); 
					// $t_quotation_val_approve = $db->rp_getValue("quotation_detail","SUM(grand_total)","isDelete=0 AND status=1 AND ".$ctable_where,0); 
					// $t_quotation_val_pending = $db->rp_getValue("quotation_detail","SUM(grand_total)","isDelete=0 AND status=0 AND ".$ctable_where,0); 
					// $t_quotation_val_cancel = $db->rp_getValue("quotation_detail","SUM(grand_total)","isDelete=0 AND status=3 AND ".$ctable_where,0); 
					// $t_quotation_val_lost = $db->rp_getValue("quotation_detail","SUM(grand_total)","isDelete=0 AND status=5 AND ".$ctable_where,0); 
					// $t_quotation_val_disapprove = $db->rp_getValue("quotation_detail","SUM(grand_total)","isDelete=0 AND status=-2 AND ".$ctable_where,0); 
					// $t_quotation_val_order = $db->rp_getValue("quotation_detail","SUM(grand_total)","isDelete=0 AND status=4 AND ".$ctable_where,0); 
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
	$("#inquiry_id").select2();
	$("#sales_id").select2();
	$("#type").select2();
	$("#status").select2();
	$("#type_of_company").select2();

	// $("#total-quotation").html("<strong><?php echo $t_quotation; ?></strong>");

	// $("#total-quotation-value").html("<strong><?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val), 2); ?></strong>");

	// $("#total-quotation-value-approve").html("<strong><?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val_approve), 2); ?></strong>");

	// $("#total-quotation-value-pending").html("<strong><?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val_pending), 2); ?></strong>");

	// $("#total-quotation-value-cancel").html("<strong><?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val_cancel), 2); ?></strong>");

	// $("#total-quotation-value-lost").html("<strong><?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val_lost), 2); ?></strong>");

	// $("#total-quotation-value-disapprove").html("<strong><?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val_disapprove), 2); ?></strong>");

	// $("#total-quotation-value-order").html("<strong><?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val_order), 2); ?></strong>");



	// $("#total-quotation-value").html("<strong>Total Amount : <?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val), 2); ?></strong>");

	// $("#total-quotation-value-approve").html("<strong>Approve Quotation Total Amount : <?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val_approve), 2); ?></strong>");

	// $("#total-quotation-value-pending").html("<strong>Pendind Quotation Total Amount : <?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val_pending), 2); ?></strong>");

	// $("#total-quotation-value-cancel").html("<strong>Cancel Quotation Total Amount : <?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val_cancel), 2); ?></strong>");

	// $("#total-quotation-value-lost").html("<strong>Lost Quotation Total Amount : <?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val_lost), 2); ?></strong>");

	// $("#total-quotation-value-disapprove").html("<strong>Disapprove Quotation Total Amount : <?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val_disapprove), 2); ?></strong>");

	// $("#total-quotation-value-order").html("<strong>Order Generated Quotation Total Amount : <?php echo CURR . $db->rp_number_format(stripslashes($t_quotation_val_order), 2); ?></strong>");
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

	function GenerateOrder(qid) {
		var r = confirm("Are you sure to Generate Order???");
		if (r) {
			$.ajax({
				type: "post",
				url: "ajax_create_order.php",
				data: "qid=" + qid,
				beforeSend: function() {
					$(".transCover").fadeIn(800);
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					// $("#loading-modal").modal('hide');
					$('.preloader').fadeOut('slow');
					result = $.parseJSON(result);
					if (result.ack == 0) {
						toastr.error(result.ack_msg);
					} else {
						toastr.success(result.ack_msg);
						window.location.href = "orders_crud.php?mode=edit&id=" + result.order_id + "&c_type=" + result.c_type;
					}
				}
			})
		}
	}

	function showStatics() {
		var where = "<?= $ctable_where ?>";
		$.ajax({
			type: 'POST',
			url: 'show_statics_ajax.php',
			data: {
				where: where,
				mode: 'quotation',
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
<?php require_once "disconnect.php"; ?>