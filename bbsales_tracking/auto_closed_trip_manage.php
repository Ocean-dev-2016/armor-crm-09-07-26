<?php
$page_id = 404;
$page_slug = 'auto_closed_trip_page';
$ctable = "expense_tmp";
$ctable1 = "Auto Closed Trip";
$main_page = "product_mgmt";
$page = "manage_auto_closed_trip";
$page_title = "Auto Closed Trip Report";
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
$date_filter_query = date("Y-m-01") . " to " . date("Y-m-t");
$page_hierarchy = array(
	array("link" => "", "title" => "HR"),
	array("link" => "expense_manage.php", "title" => "Expense"),
	array("link" => "auto_closed_trip_manage.php", "title" => $page_title)
);
include("connect.php");
require_once("../include/expense.class.php");

if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
	$db->rp_location("access_denied.php?msg=access_denied");
	exit;
}

$expenseObj = new Expense();
$expenseObj->ensureAutoCloseTripColumns();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<style type="text/css">
	.badge-auto-closed { background: #e7505a; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 11px; }
	.trip-no { font-weight: bold; color: #3598dc; }
</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="expense_manage.php" class="primary"><i class="fa fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy); ?></h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-xl-12">
					<?php $db->printErrorMessage(); ?>
					<?php $db->printSuccessMessage(); ?>
					<div class="note note-warning">
						<p><strong>Note:</strong> Trips auto-closed at 11:30 PM when employee forgot End KM. These trips are <strong>not</strong> added to Expense list (0 km claim). Employee can start a new trip next day.</p>
					</div>
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption"><i class="fa fa-filter"></i>Filters</div>
							<div class="tools"><a href="javascript:;" class="collapse"></a></div>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="col-md-3 col-sm-3" style="margin-top:10px">
									<label>Filter By Sales Person</label>
									<select class="form-control" name="sales_executive_id" id="sales_executive_id">
										<option value="">All Sales Person</option>
										<option value="0">All</option>
										<?php
										$product_list_d = $db->rp_getData('sales_executive', "*", "isDelete=0 AND isActive=1 AND type!='service_engineer'", "", 0);
										while ($product_list_r = mysqli_fetch_assoc($product_list_d)) {
										?>
										<option value="<?php echo $product_list_r['id']; ?>"><?php echo $product_list_r['name']; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-md-4 col-sm-4" style="margin-top:10px">
									<label>Filter By Trip / Auto-Close Date</label>
									<div class="input-group">
										<input class="form-control datetimerange-picker-input" id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
										<span class="input-group-addon datetimerange-picker-btn"><i class="fa fa-calendar"></i></span>
										<span class="input-group-btn">
											<button class="btn btn-success filterBtn" type="button">Filter</button>
										</span>
									</div>
								</div>
								<div class="col-md-3 col-sm-3" style="margin-top:10px">
									<label>Search Trip No</label>
									<input type="text" class="form-control" name="trip_no" id="trip_no" placeholder="Enter Trip No" />
								</div>
								<div class="col-md-2 col-sm-2" style="margin-top:30px">
									<button class="btn btn-danger btn-sm" type="button" onClick="searchByName();">Search</button>
									<button class="btn btn-success btn-sm" type="button" onClick="clearSearchByName();">Clear</button>
								</div>
							</div>
						</div>
					</div>
					<div class="portlet light">
						<div class="portlet-body">
							<div class="loading-div" style="display:none;"><img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;margin-top:10%;padding-left:48%;"></div>
							<div id="results"></div>
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
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
var sales_executive_id = "";
var trip_no = "";
var df1 = "<?php echo $date_filter_query; ?>";
var data_url = "auto_closed_trip_get_ajax.php";

function searchByName() {
	sales_executive_id = $("#sales_executive_id").val();
	trip_no = $("#trip_no").val();
	displayRecords(100, 1);
	return false;
}

$(".filterBtn").on("click", function() {
	df1 = $("#material_request_filter_input").val();
	df1 = encodeURI(df1);
	displayRecords(100, 1);
});

function clearSearchByName() {
	df1 = "<?php echo $date_filter_query; ?>";
	sales_executive_id = "";
	trip_no = "";
	$("#material_request_filter_input").val(df1);
	$("#sales_executive_id").val("");
	$("#trip_no").val("");
	displayRecords(100, 1);
}

function loadDataTable() {
	$.fn.dataTable.ext.errMode = 'none';
	if ($.fn.DataTable.isDataTable('#datatable_auto_closed')) {
		$('#datatable_auto_closed').DataTable().destroy();
	}
	$('#datatable_auto_closed').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
		"order": [[0, "desc"]]
	});
}

function displayRecords(numRecords) {
	df1 = encodeURI($("#material_request_filter_input").val() || df1);
	sales_executive_id = $("#sales_executive_id").val();
	trip_no = encodeURIComponent($("#trip_no").val().trim());
	$("#results").html("");
	$("#results").load(data_url + "?show=" + numRecords + "&df=" + df1 + "&sales_executive_id=" + sales_executive_id + "&trip_no=" + trip_no, function() {
		loadDataTable();
	});

	$("#results").on("click", ".paging_simple_numbers a", function(e) {
		e.preventDefault();
		var numRecords = $("#numRecords").val();
		var page = $(this).attr("data-page");
		$(".loading-div").show();
		$("#results").load(data_url + "?show=" + numRecords + "&df=" + df1 + "&sales_executive_id=" + sales_executive_id + "&trip_no=" + trip_no, { "page": page }, function() {
			$(".loading-div").hide();
			loadDataTable();
		});
	});

	$("#results").on("change", "#numRecords", function(e) {
		e.preventDefault();
		var numRecords = $("#numRecords").val();
		$(".loading-div").show();
		$("#results").load(data_url + "?show=" + numRecords + "&df=" + df1 + "&sales_executive_id=" + sales_executive_id + "&trip_no=" + trip_no, function() {
			$(".loading-div").hide();
			loadDataTable();
		});
	});
}

$(document).ready(function() {
	displayRecords(100, 1);
});
</script>
</body>
</html>
