<?php
$page_id = 565;
$page_slug = 'channel_partner_order';
$ctable = "orders";
$ctable1 = "Customer Order";
$main_page = "channel_partner";
$page = "channel_partner_order";
$page_title = "Manage " . $ctable1;
$page_hierarchy = array(
	array("link" => "", "title" => "Sales & Marketing"),
	array("link" => "channel_partner_customer_manage.php", "title" => "Channel Partner"),
	array("link" => "channel_partner_order_manage.php", "title" => $page_title)
);
include("connect.php");

if (!function_exists('cp_is_channel_partner_login') || !cp_is_channel_partner_login($db)) {
	$db->addErrorMessage("This page is only for Channel Partner login.");
	$db->rp_location("dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="channel_partner_customer_manage.php" class="primary"><i class="fa fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy); ?></h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<?php $db->printErrorMessage(); ?>
					<?php $db->printSuccessMessage(); ?>
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption"><i class="fa fa-filter"></i>Filters</div>
							<div class="tools"><a href="javascript:;" class="collapse"></a></div>
						</div>
						<div class="portlet-body">
							<form class="form-inline" role="form" onsubmit="return searchOrders();">
								<div class="form-group">
									<label>Search: &nbsp;</label>
									<input type="text" class="form-control input-medium" name="searchName" id="searchName" placeholder="Order No / Customer" />
								</div>
								<div class="form-group">
									<input class="btn btn-danger btn-sm" type="submit" value="Search">
									<input class="btn btn-success btn-sm" type="button" value="Clear" onclick="clearSearchOrders();">
								</div>
							</form>
						</div>
					</div>
					<div class="portlet light">
						<div class="table-toolbar">
							<div class="row">
								<div class="col-md-6">
									<a href="channel_partner_order_simple.php?cp_mode=customer" class="btn sbold green">
										Add Customer Order <i class="fa fa-shopping-cart"></i>
									</a>
								</div>
							</div>
						</div>
						<div class="portlet-body">
							<div class="loading-div" style="display:none;">
								<img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin:10% auto;display:block;">
							</div>
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
<script type="text/javascript">
var searchName = "";
var data_url = "channel_partner_order_get_ajax.php";

function searchOrders() {
	searchName = $("#searchName").val();
	displayRecords(100, 1);
	return false;
}
function clearSearchOrders() {
	searchName = "";
	$("#searchName").val("");
	displayRecords(100, 1);
}
function loadDataTable() {
	if (!$('#datatable_1').length) {
		return;
	}
	if ($('#datatable_1 tbody tr td[colspan]').length > 0) {
		return;
	}
	var colCount = $('#datatable_1 thead th').length;
	var rowCols = $('#datatable_1 tbody tr:first td').length;
	if (!colCount || colCount !== rowCols) {
		return;
	}
	if ($.fn.DataTable && $.fn.DataTable.fnIsDataTable && $.fn.DataTable.fnIsDataTable('#datatable_1')) {
		try { $('#datatable_1').dataTable().fnDestroy(); } catch (e) {}
	}
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
		"bDestroy": true,
		"aaSorting": [[1, "desc"]],
		"aoColumnDefs": [
			{ "bSortable": false, "aTargets": [0, 7] }
		]
	});
}
function displayRecords(numRecords) {
	searchName = encodeURIComponent(($("#searchName").val() || "").trim());
	$("#results").html("");
	$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName, function(response, status) {
		if (status === "error") {
			$("#results").html('<div class="alert alert-danger">Failed to load order listing. Please refresh.</div>');
			return;
		}
		loadDataTable();
	});
	$("#results").off("click", ".paging_simple_numbers a").on("click", ".paging_simple_numbers a", function(e) {
		e.preventDefault();
		var numRecords = $("#numRecords").val();
		$(".loading-div").show();
		var page = $(this).attr("data-page");
		$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName, { "page": page }, function() {
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
