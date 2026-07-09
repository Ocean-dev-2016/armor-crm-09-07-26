<?php
$page_id = 595;
$page_slug = 'order_report_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
include("../include/no_to_word.php");
$ctable 	= "orders";
$ctable1 	= "Orders";
$ctable_where = "";
$area = $_REQUEST['area'];
// $ctable_where .= " isDelete=0 AND";	

// Get the total number of rows in the table
// print_r($_SESSION);exit;
if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
	$ctable_where .= " (
		customer_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR client_code like '%" . $db->clean($_REQUEST['searchName']) . "%' OR
		order_no like '%" . $db->clean($_REQUEST['searchName']) . "%' OR company_name like '%" . $db->clean($_REQUEST['searchName']) . "%'
	) AND ";
}

// if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="" && $_REQUEST['class_id']!=NULL && $_REQUEST['class_id']!="null")
// {
// 	$state_r = $db->rp_getData("class","name","id in (".$_REQUEST['class_id'].")","",0);
// 	while($state_d = mysqli_fetch_array($state_r)) 
// 	{
// 		$state_str[] = "'".$state_d['name']."'";
// 	}
// 	$class_str = implode(",",$state_str);
// 	$ctable_where .= "   state IN (".$class_str.") AND ";
// }
//for area----//
// if(isset($_REQUEST['area']) && $_REQUEST['area']!="" && $_REQUEST['area']!=NULL && $_REQUEST['area']!="null")
// {
// 	$city_r = $db->rp_getData("city","name","id in (".$_REQUEST['area'].")","",0);
// 	while($city_d = mysqli_fetch_array($city_r)) 
// 	{
// 		$city_str[] = "'".$city_d['name']."'";
// 	}
// 	// echo implode(",",$city_str);exit;
// 	$ctable_where .= "  city IN (".implode(",",$city_str).") AND";

// }


if (isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id'] != "" && $_REQUEST['sales_executive_id'] != "null" && $_REQUEST['sales_executive_id'] != undefined) {
	$ctable_where .= "  sales_id IN (" . $_REQUEST['sales_executive_id'] . ") AND";
}

if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "" && $_REQUEST['customer_type'] != "null" &&  $_REQUEST['customer_type'] != "0") {
	if ($_REQUEST['customer_id'] == "") {
		$ctable_where .= " customer_type=" . $_REQUEST['customer_type'] . " AND ";
	}
}

if (isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company'] != "" && $_REQUEST['type_of_company'] != "null" &&  $_REQUEST['type_of_company'] != "0") {
	if ($_REQUEST['customer_id'] == "") {
		$ctable_where .= " type_of_company=" . $_REQUEST['type_of_company'] . " AND ";
	}
}
if (isset($_REQUEST['state']) && $_REQUEST['state'] != "" && $_REQUEST['state'] != NULL && $_REQUEST['state'] != "null") {
	$state_r = $db->rp_getData("state", "name", "id in (" . $_REQUEST['state'] . ")", "", 0);
	while ($state_d = mysqli_fetch_array($state_r)) {
		$state_str[] = "'" . $state_d['name'] . "'";
	}
	$class_str = implode(",", $state_str);
	$ctable_where .= " state IN (" . $class_str . ") AND ";
}
//for area----//
if (isset($_REQUEST['city']) && $_REQUEST['city'] != "" && $_REQUEST['city'] != NULL && $_REQUEST['city'] != "null") {
	$city_r = $db->rp_getData("city", "name", "id in (" . $_REQUEST['city'] . ")", "", 0);
	while ($city_d = mysqli_fetch_array($city_r)) {
		$city_str[] = "'" . $city_d['name'] . "'";
	}
	// echo implode(",",$city_str);exit;
	$ctable_where .= " main_city IN (" . implode(",", $city_str) . ") AND ";
}

if (isset($_REQUEST['route']) && $_REQUEST['route'] != "" && $_REQUEST['route'] != NULL && $_REQUEST['route'] != "null") {
	$area_r = $db->rp_getData("area", "name", "id in (" . $_REQUEST['route'] . ")", "", 0);
	while ($area_d = mysqli_fetch_array($area_r)) {
		$area_str[] = "'" . $area_d['name'] . "'";
	}
	// echo implode(",",$area_str);exit;
	$ctable_where .= " city IN (" . implode(",", $area_str) . ") AND ";
}


if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != "null") {
	/*$get_customer_type=$db->rp_getValue("executive","type_of_executive","isDelete=0 AND id='".$_REQUEST['customer_id']."'");

	if($get_customer_type == 2)
	{
		$get_retailer_customer=$db->rp_getData("executive","*","isDelete=0 AND dealer_distributor_id='".$_REQUEST['customer_id']."'","",0);

		$retailer_id=array();
		while($retailer_ids_d=mysqli_fetch_assoc($get_retailer_customer))
		{
			$retailer_id[]=$retailer_ids_d['id'];

		}

		// print_r($retailer_id);exit;
		$retailer_id=implode(",",$retailer_id);
 
		$ctable_where.=" customer_id IN(".$retailer_id.") AND ";

		$total_retailer_count=$db->rp_getTotalRecord("executive","isDelete=0 AND dealer_distributor_id='".$_REQUEST['customer_id']."'",0);

		$total_visit_count=$db->rp_getTotalRecord("visit","isDelete=0 AND customer_id IN(".$retailer_id.")");
		$total_order_count=$db->rp_getTotalRecord("orders","isDelete=0 AND customer_id IN(".$retailer_id.") GROUP BY customer_id");

	}else if($get_customer_type == 1)
	{
		$get_retailer_customer=$db->rp_getData("executive","*","isDelete=0 AND super_stockist_id='".$_REQUEST['customer_id']."' AND type_of_executive=2","",0);

		$distributor_id=array();
		while($distributor_ids_d=mysqli_fetch_assoc($get_retailer_customer))
		{
			$distributor_id[]=$distributor_ids_d['id'];

		}

		// print_r($retailer_id);exit;
		$distributor_id=implode(",",$distributor_id);

		$ctable_where.=" customer_id IN(".$distributor_id.") AND ";

		$total_retailer_count=$db->rp_getTotalRecord("executive","isDelete=0 AND super_stockist_id='".$_REQUEST['customer_id']."' AND type_of_executive=2",0);

		$total_visit_count=$db->rp_getTotalRecord("visit","isDelete=0 AND customer_id IN(".$distributor_id.")");
		$total_order_count=$db->rp_getTotalRecord("orders","isDelete=0 AND customer_id IN(".$distributor_id.") GROUP BY customer_id");

	}
	else
	{
		$ctable_where.=" customer_id=".$_REQUEST['customer_id']." AND ";
	}*/
	$ctable_where .= " customer_id=" . $_REQUEST['customer_id'] . " AND ";
}

if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
	$ctable_where .= " status!=-1 AND ";
} else if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 2) {
	if ($rights['all_data_flag'] != 1) {
		$ctable_where .= " status!=-1 AND sales_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "' AND ";
	}
} else if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 3) // customer and its chain wise order
{
	if ($rights['personal_flag'] == 1) {
		$ctable_where .= " status!=-1 AND customer_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "' AND ";
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
				$ctable_where .= "  status!=-1 AND customer_id IN (" . $CUSIDS . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") AND ";
			} else {
				$ctable_where .= " status!=-1 AND customer_id IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") AND ";
			}
		} else {
			$ctable_where .= " status!=-1 AND";
		}
	}
}

$ctable_where .= " isDelete=0 ";

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

// if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="" && $_REQUEST['class_id']!=NULL && $_REQUEST['class_id']!="null")
// {

// 	$ctable_where .= " AND  state IN (".$_REQUEST['class_id'].") ";
// }
// //for area----//
// if(isset($_REQUEST['area']) && $_REQUEST['area']!="" && $_REQUEST['area']!=NULL && $_REQUEST['area']!="null")
// {

// 	$ctable_where .= " AND city IN (".$_REQUEST['area'].") ";

// }

if (isset($_REQUEST['df']) && $_REQUEST['df'] != "") {

	$date_filter_query = urldecode($_REQUEST['df']);

	$date_filter_query_ex = explode(" to ", $date_filter_query);

	$ctable_where .= " AND ( DATE(order_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(order_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
}

$get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows / $item_per_page);


$page_position = (($page_number - 1) * $item_per_page);



// $total_order_count=$db->rp_getTotalRecord("orders",$ctable_where." GROUP BY customer_id");
?>
<style type="text/css">
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
<div class="table-responsive">
	<form action="" name="frm" id="print_info" method="post">
		<?php
		if ($_REQUEST['customer_id'] != "" ||  $_REQUEST['customer_type'] == "0" ||  $_REQUEST['customer_type'] == "1" ||  $_REQUEST['customer_type'] == "2" || $_REQUEST['customer_type'] == "3" ||  $_REQUEST['customer_type'] == "4" ||   $_REQUEST['customer_type'] == "6") {
			$ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "id DESC", 0);
		?>
			<div class="table-scrollable">
				<table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll!important;">
					<thead class="fix-th">
						<tr>
							<td class="header" align="center" colspan="10">
								<h3><b> Order Report <?php echo $date; ?></b></h3>
							</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<!-- <td></td> -->
							<td class="total-value" align="right"></td>
							<!-- <td></td> -->
						</tr>
						<tr class="tr">
							<th class="th fix-th1">Sr.<br> No.</th>
							<th class="th fix-th1">Order No.</th>
							<th class="th fix-th1">Company Name</th>
							<th class="th fix-th1">Person Name</th>
							<th class="th fix-th1">Client Code</th>
							<th class="th fix-th1">Sales Person Name</th>
							<th class="th fix-th1">Customer Type</th>
							<th class="th fix-th1">Order Date</th>
							<th class="th fix-th1">Order Amount</th>
							<th class="th fix-th1">Product</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if (mysqli_num_rows($ctable_r) > 0) {
							$count = 1;
							$sales_name = '';
							while ($ctable_d = mysqli_fetch_array($ctable_r)) {

								$customer = $db->rp_getValue('executive', 'isActive', "id=" . $ctable_d['customer_id'] . "", 0);
								if ($customer == 0) {
									continue;
								}

								$customer_flag_text = "";
								if ($ctable_d['customer_flag'] == 1) {
									$customer_flag_text = " - P";
								} else if ($ctable_d['customer_flag'] == 0) {
									$customer_flag_text = " - C";
								}

						?>
								<tr class="tr">
									<td class="td" style="width:5px;"><?php echo $count++; ?></td>
									<td class="td"><?php echo stripslashes($ctable_d['order_no']); ?></td>
									<td><?php echo $db->rp_getValue('executive', 'company_name', "id=" . $ctable_d['customer_id'] . "", 0) . $customer_flag_text; ?></td>
									<td class="td"><?php echo stripslashes($ctable_d['customer_name']); ?></td>
									<td class="td"><?php echo stripslashes($ctable_d['client_code']); ?></td>
									<?php
									$sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['sales_id'] . "'");
									?>
									<td class="td"><?php if ($sales_name == "") {
														echo "--";
													} else {
														echo $sales_name;
													}
													?>
									</td>
									<td class="td"> <?php
													if ($ctable_d['customer_type'] == '1') {
														$slug = "Super Stockist";
													} else if ($ctable_d['customer_type'] == '2') {
														$slug = "Distributor";
													} else if ($ctable_d['customer_type'] == '3') {
														$slug = "Dealer";
													} else if ($ctable_d['customer_type'] == '4') {
														$slug = "B2B Customer";
													} else if ($ctable_d['customer_type'] == '6') {
														$slug = "B2C Customer";
													}
													echo stripslashes($slug); ?></td>
									<td class="td"><?php echo date('d-m-Y', strtotime($ctable_d['order_date'])); ?></td>
									<td align="right" class="td"><?php echo stripslashes('₹' . $db->rp_num($ctable_d['grand_total'])); ?></td>
									<?php
									$total_value += $ctable_d['grand_total'];
									?>
									<td class="td">
										<?php
										$ctable_where_p	= "order_id='" . $ctable_d['id'] . "' AND isDelete=0";
										$ctable_p = $db->rp_getData("order_product_item", "*", $ctable_where_p, "", 0);
										?>
										<table style="width:100%;vertical-align: top;" class="table1 table-bordered table-striped dataTable">
											<thead>
												<?php
												if ($ctable_p) {
												?>
													<tr class="tr1">
														<th class="th1" style="width: 60%!important;">Product Name</th>
														<th class="th1" style="width: 20%!important;text-align: center;">QTY</th>
														<th class="th1" style="width: 20%!important;text-align: center;">Price</th>
													</tr>
											</thead>
											<tbody>
												<?php
													$total = 0;
													while ($ctable_pro = mysqli_fetch_array($ctable_p)) {
												?>
													<tr class="tr1">
														<td class="td1"><?php
																		$pro_name = $db->rp_getValue("product", "name", "isDelete=0 AND id='" . $ctable_pro['pro_id'] . "'", 0);
																		$weight_name = $db->rp_getValue("weight", "name", "isDelete=0 AND id='" . $ctable_pro['weight_id'] . "'");
																		$product_code = $db->rp_getValue("product_weight_price", "catno", "product_id='" . $ctable_pro['pro_id'] . "' AND weight_id='" . $ctable_pro['weight_id'] . "'", 0);

																		echo "<b>#".$product_code."</b>-".$pro_name . "(" . $weight_name . ")";  ?></td>
														<td class="td1" align="right">
															<?php
															/*$get_weight=$db->rp_getValue("product_weight_price","weight_in_kg","isDelete=0 AND product_id='".$ctable_pro['pro_id']."' AND weight_id='".$ctable_pro['weight_id']."'",0);*/
															echo $total_weight_count =/*=$get_weight**/ $ctable_pro['pro_qty'];

															?>
														</td>
														<td class="td1" align="right"><?php echo "Rs. " . $db->rp_num($ctable_pro['unitprice']);  ?></td>
													</tr>
												<?php
														$total += $ctable_pro['totalprice'];
														$total_unitprice += $ctable_pro['unitprice'];

														$total_qty += $total_weight_count;
													}
												?>
												<tr>
													<td><strong>Total</strong></td>
													<td align="right"><strong><?= $total_qty ?></strong></td>
													<!-- <td align="right"><strong><?= $total ?></strong></td> -->
													<td align="right"><strong><?= $db->rp_num($total_unitprice); ?></strong></td>
												</tr>
											<?php
													$total_qty = 0;
													$total = 0;
													$total_unitprice = 0;
												} else {
											?>
												<tr>
													<td class="tr1" colspan="3" style="text-align:center;">No Product Order</td>
												</tr>
											<?php
												}
											?>
											</tbody>
										</table>
									</td>
								</tr>
						<?php
							}
						}
						?>
					</tbody>
				</table>
			</div>

			<br><br>
			<center>
				<h2><strong>Product Wise Sales Summary</strong></h2>
			</center>
			<?php
			$get_order_data_search = $db->rp_getData("orders", "*", $ctable_where, "", 0);

			$orders_ids = array();
			while ($get_order_data_search_d = mysqli_fetch_assoc($get_order_data_search)) {

				$orders_ids[] = $get_order_data_search_d['id'];
			}
			// print_r($orders_ids);exit();
			$orders_ids = implode(',', $orders_ids);

			//$order_id=$_REQUEST['id'];
			$ctable_where_sales	= " order_id IN(" . $orders_ids . ") AND isDelete=0 Group By pro_id,weight_id";
			$ctable_p_sales = $db->rp_getData("order_product_item", "*", $ctable_where_sales, "", 0);

			?>
			<div class="table-scrollable">
				<table class="table1 table-bordered table-striped dataTable">
					<thead class="fix-th">
						<?php
						if ($ctable_p_sales) {
						?>
							<tr class="tr">
								<th class="th fix-th1">Category</th>
								<th class="th fix-th1">Product Name</th>
								<th class="th fix-th1" align="center" style="text-align: center;">Total Sale QTY</th>

							</tr>
					</thead>


					<tbody>
						<?php
							$total = 0;
							while ($ctable_pro_sales = mysqli_fetch_array($ctable_p_sales)) {
						?>
							<tr class="tr1">
								<td class="td"><?php

												$pro_cat = $db->rp_getValue("category_master", "name", "id='" . $ctable_pro_sales['cat_id'] . "'", 0);
												$pro_name = $db->rp_getValue("product", "name", "isDelete=0 AND id='" . $ctable_pro_sales['pro_id'] . "'", 0);
												$weight_name = $db->rp_getValue("weight", "name", "isDelete=0 AND id='" . $ctable_pro_sales['weight_id'] . "'");


												echo $pro_cat;

												// echo $pro_name."(".$weight_name.")";  
												?></td>
								<td class="td" align="left">
									<?php


									$pro_name = $db->rp_getValue("product", "name", "isDelete=0 AND id='" . $ctable_pro_sales['pro_id'] . "'", 0);
									$weight_name = $db->rp_getValue("weight", "name", "isDelete=0 AND id='" . $ctable_pro_sales['weight_id'] . "'");
										$product_code = $db->rp_getValue("product_weight_price", "catno", "product_id='" . $ctable_pro_sales['pro_id'] . "' AND weight_id='" . $ctable_pro_sales['weight_id'] . "'", 0);

																		

									echo "<b>#".$product_code."</b>-".$pro_name . "(" . $weight_name . ")";





									/*echo  $ctable_pro_sales['pro_qty'];*/ ?></td>
								<td class="td" align="center"><?php


																$get_total_qty = $db->rp_getValue("order_product_item", "SUM(pro_qty)", "isDelete=0 AND pro_id='" . $ctable_pro_sales['pro_id'] . "' AND weight_id=" . $ctable_pro_sales['weight_id'] . " AND (order_id IN(" . $orders_ids . ")) GROUP By pro_id,weight_id", 0);

																// echo $get_total_qty;

																$get_total_kg = $get_total_qty
																	/**$ctable_pro_sales['weight']*/
																;

																echo $get_total_kg;

																?></td>

							</tr>

						<?php


								// $total+=$ctable_pro['totalprice'];
							}
						?>

					<?php
						} else {

					?>
						<tr>
							<td class="tr1" colspan="3" style="text-align:center;">No Product Order</td>

						</tr>
					<?php
						}
					?>

					</tbody>
				</table>



			<?php

		} else {
			?>

				<h2>
					<center>Please Select Customer Type And Customer To See Result</center>
				</h2>
			<?php
		}

			?>
	</form>
</div>
<div id="loading" class="modal fade" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box white">
				<div class="portlet-title" style="color:black;">
					<div class="caption">Loading.......
						<img src="../images/loading-spinner-blue.gif">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- <button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i> &nbsp PDF</button>
<button type="button" class="btn green-haze export" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i>&nbsp; Excel</button> -->

<!-- --- milan --- 22-06-2021 --- -->
<script>
	$(".total-value").html("<?php echo $total_value; ?>")
</script>
<!-- --- milan --- 22-06-2021 --- -->

<script>
	function genReport(cid) {
		if ($("#datatable_1").find("tbody").find("tr").length >= 2) {
			var rc = encodeURIComponent($("#print_info").html());

			$.ajax({
				type: "POST",
				url: "ordersReport_gen_ajax.php",
				data: '&rc=' + rc,
				beforeSend: function() {
					$(".transCover").fadeIn(800);
					$("#loading").modal('show');
				},
				success: function(result) {
					//alert(result);
					setTimeout(function() {
						$(".transCover").fadeOut(100);
						alert("Report file generated!!");
						$("#loading").modal('hide');
						window.location.href = result;
					}, 1500);
				}
			});
		} else {
			toastr.error("Report Can't generated");
		}

	}
</script>
<?php require_once "disconnect.php"; ?>