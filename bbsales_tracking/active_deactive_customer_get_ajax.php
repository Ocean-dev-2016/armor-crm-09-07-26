<?php
$page_id = 604;
$page_slug = 'active_deactive_customer_report_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "executive";
$ctable1 	= "Executive";
$ctable_where = "";
$where = "";
$isFillter = filter_var($_REQUEST['isFillter'], FILTER_VALIDATE_BOOLEAN);
// echo $isFillter;exit();
// $today=date("Y-m-d");
$uid = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
// Get the total number of rows in the tabl
if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
	$ctable_where .= " (
							cname like '%" . $db->clean($_REQUEST['searchName']) . "%'
							OR company_name like '%" . $db->clean($_REQUEST['searchName']) . "%'
							OR phone  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%'
						) AND ";
	$isFillter = true;
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

$date_select = "";

/*if(isset($_REQUEST['customer_status']) && $_REQUEST['customer_status']!="" && $_REQUEST['customer_status']!=NULL && $_REQUEST['customer_status']!=null && $_REQUEST['customer_status']!="NULL" && $_REQUEST['customer_status']!="null" && $_REQUEST['customer_status']!=UNDEFINED && $_REQUEST['customer_status']!=undefined && $_REQUEST['customer_status']!="UNDEFINED" && $_REQUEST['customer_status']!="undefined")
{
	$ctable_where .= " AND isActive IN (".$_REQUEST['customer_status'].")";
}*/

if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != NULL && $_REQUEST['customer_id'] != null && $_REQUEST['customer_id'] != "NULL" && $_REQUEST['customer_id'] != "null" && $_REQUEST['customer_id'] != UNDEFINED && $_REQUEST['customer_id'] != undefined && $_REQUEST['customer_id'] != "UNDEFINED" && $_REQUEST['customer_id'] != "undefined") {
	$ctable_where .= " AND id IN(" . $_REQUEST['customer_id'] . ")";
	$isFillter = true;
}
if (isset($_REQUEST['state']) && $_REQUEST['state'] != "" && $_REQUEST['state'] != NULL && $_REQUEST['state'] != null && $_REQUEST['state'] != "NULL" && $_REQUEST['state'] != "null" && $_REQUEST['state'] != UNDEFINED && $_REQUEST['state'] != undefined && $_REQUEST['state'] != "UNDEFINED" && $_REQUEST['state'] != "undefined") {


	$stringA = $_REQUEST['state'];
	$stringAArray = explode(",", $stringA);
	$stringiO = "(";
	foreach ($stringAArray as $key => $value) {
		$stringiO .= "'" . $value . "',";
	}
	$stringiO = rtrim($stringiO, ",");
	$stringiO .= ")";
	$ctable_where .= " AND state IN " . $stringiO . " ";
	$state = $_REQUEST['state'];
	$isFillter = true;
}


if (isset($_REQUEST['city']) && $_REQUEST['city'] != "" && $_REQUEST['city'] != NULL && $_REQUEST['city'] != null && $_REQUEST['city'] != "NULL" && $_REQUEST['city'] != "null" && $_REQUEST['city'] != UNDEFINED && $_REQUEST['city'] != undefined && $_REQUEST['city'] != "UNDEFINED" && $_REQUEST['city'] != "undefined") {
	$stringD = $_REQUEST['city'];
	$stringDArray = explode(",", $stringD);
	$stringio = "(";
	foreach ($stringDArray as $key => $value) {
		$stringio .= "'" . $value . "',";
	}
	$stringio = rtrim($stringio, ",");
	$stringio .= ")";
	$ctable_where .= " AND main_city IN " . $stringio . " ";
	$city = $_REQUEST['city'];
	$isFillter = true;
}

if (isset($_REQUEST['route']) && $_REQUEST['route'] != "" && $_REQUEST['route'] != NULL && $_REQUEST['route'] != null && $_REQUEST['route'] != "NULL" && $_REQUEST['route'] != "null" && $_REQUEST['route'] != UNDEFINED && $_REQUEST['route'] != undefined && $_REQUEST['route'] != "UNDEFINED" && $_REQUEST['route'] != "undefined") {
	$stringD = $_REQUEST['route'];
	$stringDArray = explode(",", $stringD);
	$stringio = "(";
	foreach ($stringDArray as $key => $value) {
		$stringio .= "'" . $value . "',";
	}
	$stringio = rtrim($stringio, ",");
	$stringio .= ")";
	$ctable_where .= " AND city IN " . $stringio . " ";
	$route = $_REQUEST['route'];
	$isFillter = true;
}

// $get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); 
//hold total records in variable

/*if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
 $ctable_where .= " AND type_of_executive = '".$_REQUEST['status']."' ";
}*/

if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "" && $_REQUEST['customer_type'] != NULL && $_REQUEST['customer_type'] != null && $_REQUEST['customer_type'] != "NULL" && $_REQUEST['customer_type'] != "null" && $_REQUEST['customer_type'] != UNDEFINED && $_REQUEST['customer_type'] != undefined && $_REQUEST['customer_type'] != "UNDEFINED" && $_REQUEST['customer_type'] != "undefined") {
	$ctable_where .= " AND type_of_executive IN ( " . $_REQUEST['customer_type'] . ") ";
	$customer_type = $_REQUEST['customer_type'];
	$isFillter = true;
}

if (isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company'] != "" && $_REQUEST['type_of_company'] != NULL && $_REQUEST['type_of_company'] != null && $_REQUEST['type_of_company'] != "NULL" && $_REQUEST['type_of_company'] != "null" && $_REQUEST['type_of_company'] != UNDEFINED && $_REQUEST['type_of_company'] != undefined && $_REQUEST['type_of_company'] != "UNDEFINED" && $_REQUEST['type_of_company'] != "undefined") {
	$ctable_where .= " AND type_of_company IN ( " . $_REQUEST['type_of_company'] . ") ";
	$type_of_company = $_REQUEST['type_of_company'];
	$isFillter = true;
}
if (isset($_REQUEST['days']) && $_REQUEST['days'] != "" && $_REQUEST['days'] != NULL) {
	$date1 = date('Y-m-d');
	$prev_days_date = date('Y-m-d', strtotime($date1 . ' - ' . $_REQUEST['days'] . ' days'));
	$order_Where = " AND order_date>='" . $prev_days_date . "'";
	$isFillter = true;
}

$order = $db->rp_getData("orders", "customer_id", "isDelete=0" . $order_Where . " GROUP BY customer_id", "", 0);
$customer_ids = array();
while ($order_d = mysqli_fetch_array($order)) {
	$customer_ids[] = $order_d['customer_id'];
}
$custome_id = implode(",", $customer_ids);
// echo ($custome_id);exit();

if (isset($_REQUEST['customer_status']) && $_REQUEST['customer_status'] != "" && $_REQUEST['customer_status'] != NULL && $_REQUEST['customer_status'] != null && $_REQUEST['customer_status'] != "NULL" && $_REQUEST['customer_status'] != "null" && $_REQUEST['customer_status'] != UNDEFINED && $_REQUEST['customer_status'] != undefined && $_REQUEST['customer_status'] != "UNDEFINED" && $_REQUEST['customer_status'] != "undefined") {
	if ($_REQUEST['customer_status'] == 0) {

		$ctable_where .= " AND id NOT IN (" . $custome_id . ")";
	} else {
		$ctable_where .= " AND id  IN (" . $custome_id . ")";
	}
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
<form action="" name="frm" id="frm" method="post">
	<div class="table-scrollable">
		<table id="datatable_super" class="table table-striped table-bordered table-hover">
			<thead class="fix-th">
				<?php
				if ($date_select != "") {
				?>
					<tr>
						<th colspan="11" class="text-center" style="border-bottom: 1px solid #ddd;">
							<h2> Report From <?= $date_select; ?></h2>
						</th>
					</tr>
				<?php
				}
				?>
				<tr>
					<th class="fix-th1">No.</th>
					<th class="fix-th1">Customer Type</th>
					<!-- 	<th>Company</th> -->
					<th class="fix-th1">Company Name</th>
					<th class="fix-th1">Person Name</th>
					<th class="fix-th1">Client Code</th>
					<th class="fix-th1">Phone</th>
					<th class="fix-th1">Mobile</th>
					<!-- <th>WhatsApp</th>	 -->
					<th class="fix-th1">State</th>
					<th class="fix-th1">City</th>
					<th class="fix-th1">Route</th>
					<th class="fix-th1">Last Order Detail</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ($isFillter) {
					if (mysqli_num_rows($ctable_r) > 0) {
						$count = 0;

						while ($ctable_d = mysqli_fetch_array($ctable_r)) {
							$customer_flag_text = "";
							if ($ctable_d['customer_flag'] == 1) {
								$customer_flag_text = " - P";
							} else if ($ctable_d['customer_flag'] == 0) {
								$customer_flag_text = " - C";
							}
							if ($ctable_d['type_of_executive'] == "1") {
								$type = "Super Stockist";
							} else if ($ctable_d['type_of_executive'] == "2") {
								$type = "Distributor";
							} else if ($ctable_d['type_of_executive'] == "3") {
								$type = "Dealer";
							} else if ($ctable_d['type_of_executive'] == "4") {
								$type = "B2B Customer";
							} else if ($ctable_d['type_of_executive'] == "5") {
								$type = "OEM";
							} else if ($ctable_d['type_of_executive'] == "6") {
								$type = "B2C Customer";
							} else if ($ctable_d['type_of_executive'] == "7") {
								$type = "Promotional Customer";
							} else if ($ctable_d['type_of_executive'] == "8") {
								$type = "Merchant Exports";
							} else if ($ctable_d['type_of_executive'] == "9") {
								$type = "Corporate Customer";
							}
				?>
							<tr>
								<td><?php echo ++$count; ?></td>

								<td><?php echo stripslashes($type); ?></td>
								<!-- <td><?= $db->rp_getValue("brand", "name", "id='" . $_REQUEST['type_of_company'] . "'"); ?></td> -->
								<td>
									<span class="<?php echo ($ctable_d['isActive'] == 0) ? "text-danger" : "text-success"; ?>">
										<?php echo stripslashes($ctable_d['company_name']) . $customer_flag_text; ?>
									</span>
								</td>

								<td><?php echo stripslashes($ctable_d['cname']); ?></td>
								<td><?php echo stripslashes($ctable_d['client_code']); ?></td>
								<td><?php echo stripslashes($ctable_d['phone']); ?></td>
								<td><?php echo stripslashes($ctable_d['mobile_no1']); ?></td>
								<!-- 	<td><?php echo stripslashes($ctable_d['whatsapp_no']); ?></td> -->
								<td><?php echo $ctable_d['state']; ?></td>
								<td><?php echo $ctable_d['main_city']; ?></td>
								<td><?php echo $ctable_d['city']; ?></td>

								<?php
								if (isset($_REQUEST['days']) && $_REQUEST['days'] != "" && $_REQUEST['days'] != NULL) {
									$date1 = date('Y-m-d');
									$prev_days_date = date('Y-m-d', strtotime($date1 . ' - ' . $_REQUEST['days'] . ' days'));
									$order_Where = " AND order_date>='" . $prev_days_date . "'";
								}
								$order_r = $db->rp_getData("orders", "id,order_no,order_date,grand_total", "customer_id='" . $ctable_d['id'] . "' AND isDelete=0" . $order_Where, "id DESC", "0", 1);
								if ($order_r) {
									$order_d = mysqli_fetch_assoc($order_r);
									$link = "order_viewer.php?order_id=" . $order_d['id'];
								?>
									<td>
										<a target='_blank' href="<?= $link; ?>">
											<div>
												<?php
												$order_dt = date("d-m-Y", strtotime($order_d['order_date']));
												echo "#" . $order_d['order_no'] . "<br/>" . $order_dt . "<br/><b>" . $db->rp_num($order_d['grand_total']) . "</b>";
												?>
											</div>
										</a>
									</td>
								<?php
								} else {
								?><td><?php echo "No Order Found" ?></td><?php
																		}
																			?>
							</tr>
						<?php
						}
					} else {
						?>
						<tr>
							<td colspan="10">
								<p style="text-align:center;">No data available in table</p>
							</td>
						</tr>
					<?php
					}
				} else {
					?>
					<tr>
						<td colspan="10" class="text-center">
							<h3><strong><?= FILTER_INFO ?></strong></h3>
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

					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "") {
											echo ' selected="selected"';
										}  ?>>100</option>

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
</form>
<?php require_once("disconnect.php"); ?>
<!-- <script type="text/javascript">
	$("#state").select2();
    $("#city").select2();
    $("#customer_type").select2();
    $("#price_list").select2();

	function filter_state(state_id,city=""){
	    $.ajax({
	        type: "POST",
	        url: "find_city.php",
	        data:'state_id='+state_id+"&city="+city,
	        beforeSend:function(){
	            $('.preloader').fadeIn('slow');  
	        },
	        success: function(data){
	            $("#city").select2("destroy");
	            $("#city").html(data);
	            $("#city").select2();
	            $('.preloader').fadeOut('slow');
	        }
	    });
	}
</script> -->