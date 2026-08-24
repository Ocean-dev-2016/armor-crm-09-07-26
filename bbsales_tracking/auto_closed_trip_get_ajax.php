<?php
$page_id = 404;
$page_slug = 'auto_closed_trip_page';
include("connect.php");
require_once("../include/expense.class.php");

if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
	die('<div class="alert alert-danger">Access denied. Admin only.</div>');
}

$expenseObj = new Expense();
$expenseObj->ensureAutoCloseTripColumns();

$ctable = "expense_tmp";
$ctable_where = "isDelete=0 AND auto_closed=1";

if (isset($_REQUEST['trip_no']) && $_REQUEST['trip_no'] != "") {
	$tripNo = intval($_REQUEST['trip_no']);
	if ($tripNo > 0) {
		$ctable_where .= " AND id='" . $tripNo . "'";
	}
}

if (isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id'] != "" && $_REQUEST['sales_executive_id'] != "0" && $_REQUEST['sales_executive_id'] != NULL) {
	$ctable_where .= " AND sales_executive_id='" . intval($_REQUEST['sales_executive_id']) . "'";
}

if (isset($_REQUEST['df']) && $_REQUEST['df'] != "") {
	$date_filter_query = urldecode($_REQUEST['df']);
	$date_filter_query_ex = explode(" to ", $date_filter_query);
	if (count($date_filter_query_ex) >= 2) {
		$fromDate = date("Y-m-d", strtotime(trim($date_filter_query_ex[0])));
		$toDate = date("Y-m-d", strtotime(trim($date_filter_query_ex[1])));
		$ctable_where .= " AND (DATE(auto_closed_at)>='" . $fromDate . "' AND DATE(auto_closed_at)<='" . $toDate . "')";
	}
} else {
	$ctable_where .= " AND MONTH(auto_closed_at)='" . date('m') . "' AND YEAR(auto_closed_at)='" . date('Y') . "'";
}

$item_per_page = (isset($_REQUEST["show"]) && $_REQUEST["show"] != "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 100;

if (isset($_REQUEST["page"])) {
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
	if (!is_numeric($page_number)) {
		die('Invalid page number!');
	}
} else {
	$page_number = 1;
}

$get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where, 0);
$total_pages = ($item_per_page > 0) ? ceil($get_total_rows / $item_per_page) : 1;
$page_position = (($page_number - 1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "id DESC LIMIT " . $page_position . ", " . $item_per_page, 0);
?>
<form action="" id="print_info_auto_closed" name="frm" method="post">
	<div class="table-toolbar">
		<div class="row">
			<div class="col-md-6">
				<label>Show
					<select name="numRecords" id="numRecords" class="form-control input-sm input-xsmall input-inline">
						<?php
						$showArr = array(10, 25, 50, 100, 500);
						foreach ($showArr as $showVal) {
						?>
						<option value="<?php echo $showVal; ?>" <?php echo ($item_per_page == $showVal) ? "selected" : ""; ?>><?php echo $showVal; ?></option>
						<?php } ?>
					</select> records</label>
			</div>
			<div class="col-md-6 text-right">
				<span class="badge badge-auto-closed">Total Auto Closed: <?php echo $get_total_rows; ?></span>
			</div>
		</div>
	</div>
	<div class="table-scrollable">
		<table class="table table-striped table-bordered table-hover" id="datatable_auto_closed">
			<thead>
				<tr>
					<th>Trip No</th>
					<th>Employee</th>
					<th>Mobile</th>
					<th>Vehicle Type</th>
					<th>Start Date/Time</th>
					<th>Start KM</th>
					<th>Auto End Date/Time</th>
					<th>End KM</th>
					<th>Auto Closed At</th>
					<th>Remark</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($ctable_r && mysqli_num_rows($ctable_r) > 0) {
				while ($row = mysqli_fetch_assoc($ctable_r)) {
					$empName = $db->rp_getValue("sales_executive", "name", "id='" . $row['sales_executive_id'] . "'", 0);
					$empPhone = $db->rp_getValue("sales_executive", "phone", "id='" . $row['sales_executive_id'] . "'", 0);
					$vehicleType = $db->rp_getValue("expence_sub_category", "slug", "id='" . $row['subcategory_id'] . "'", 0);
					if ($vehicleType == "") {
						$vehicleType = $db->rp_getValue("expence_sub_category", "name", "id='" . $row['subcategory_id'] . "'", 0);
					}
					$startDt = ($row['start_date_time'] != "" && $row['start_date_time'] != "0000-00-00 00:00:00") ? date('d-m-Y h:i A', strtotime($row['start_date_time'])) : "-";
					$endDt = ($row['end_date_time'] != "" && $row['end_date_time'] != "0000-00-00 00:00:00") ? date('d-m-Y h:i A', strtotime($row['end_date_time'])) : "-";
					$closedAt = (isset($row['auto_closed_at']) && $row['auto_closed_at'] != "" && $row['auto_closed_at'] != "0000-00-00 00:00:00") ? date('d-m-Y h:i A', strtotime($row['auto_closed_at'])) : "-";
					$remark = isset($row['auto_closed_remark']) && $row['auto_closed_remark'] != "" ? $row['auto_closed_remark'] : "Auto closed - End KM not entered";
			?>
				<tr>
					<td class="trip-no">#<?php echo $row['id']; ?></td>
					<td><?php echo htmlspecialchars($empName); ?></td>
					<td><?php echo htmlspecialchars($empPhone); ?></td>
					<td><?php echo htmlspecialchars($vehicleType); ?></td>
					<td><?php echo $startDt; ?></td>
					<td><?php echo $row['start_km']; ?></td>
					<td><?php echo $endDt; ?></td>
					<td><?php echo $row['end_km']; ?></td>
					<td><?php echo $closedAt; ?></td>
					<td><span class="label label-sm label-danger"><?php echo htmlspecialchars($remark); ?></span></td>
				</tr>
			<?php
				}
			} else {
			?>
				<tr>
					<td colspan="10" class="text-center">No auto-closed trips found for selected filters.</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<?php
	if ($total_pages > 1) {
	?>
	<div class="row">
		<div class="col-md-12">
			<ul class="pagination paging_simple_numbers pull-right">
				<?php
				for ($i = 1; $i <= $total_pages; $i++) {
					$active = ($i == $page_number) ? "active" : "";
				?>
				<li class="<?php echo $active; ?>"><a href="javascript:;" data-page="<?php echo $i; ?>"><?php echo $i; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<?php } ?>
</form>
