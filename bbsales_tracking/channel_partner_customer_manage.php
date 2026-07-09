<?php
$page_id = 555;
$page_slug = 'channel_partner_customer';
$ctable = "channel_partner_customer";
$ctable1 = "Channel Partner Customer";
$main_page = "channel_partner";
$page = "channel_partner_customer";
$page_title = "Manage " . $ctable1;
$page_hierarchy = array(
	array("link" => "", "title" => "Sales & Marketing"),
	array("link" => "channel_partner_customer_manage.php", "title" => "Channel Partner"),
	array("link" => "channel_partner_customer_manage.php", "title" => "Manage " . $ctable1)
);
include("connect.php");
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
				<h1><a href="<?php echo "dashboard.php"; ?>" class="primary"><i class="fa fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy); ?> </h1>
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
							<form class="form-inline" role="form" onSubmit="return searchByName();">
								<div class="form-group">
									<label>Search: &nbsp;</label>
									<input type="text" class="form-control input-medium" name="searchName" id="searchName" placeholder="Name / Mobile / Email" />
								</div>
								<div class="form-group">
									<input class="btn btn-danger btn-sm" type="submit" value="Search">
									<input class="btn btn-success btn-sm" type="button" value="Clear" onClick="clearSearchByName();">
								</div>
							</form>
						</div>
					</div>
					<div class="portlet light">
						<div class="table-toolbar">
							<div class="row">
								<div class="col-md-6">
									<?php if ($rights['insert_flag'] == 1) { ?>
										<a href="channel_partner_customer_crud.php?mode=add" class="btn sbold blue">
											Add Channel Partner Customer <i class="fa fa-plus"></i>
										</a>
									<?php } ?>
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
var data_url = "<?php echo $ctable; ?>_get_ajax.php";

function searchByName() {
	searchName = $("#searchName").val();
	displayRecords(100, 1);
	return false;
}
function clearSearchByName() {
	searchName = "";
	$("#searchName").val("");
	displayRecords(100, 1);
}
function loadDataTable() {
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
		"aoColumns": [
			{ "sWidth": "5%" },
			{ "sWidth": "14%" },
			{ "sWidth": "14%" },
			{ "sWidth": "12%" },
			{ "sWidth": "10%" },
			{ "sWidth": "10%" },
			{ "sWidth": "9%" },
			{ "sWidth": "9%" },
			{ "sWidth": "8%" },
			{ "sWidth": "9%", "bSortable": false }
		]
	});
}
function displayRecords(numRecords) {
	searchName = encodeURIComponent(($("#searchName").val() || "").trim());
	$("#results").html("");
	$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName, function() {
		loadDataTable();
	});
	$("#results").on("click", ".paging_simple_numbers a", function(e) {
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
function del_conf(id) {
	if (confirm("Are you sure you want to delete this Channel Partner Customer?")) {
		window.location.href = "channel_partner_customer_crud.php?mode=delete&id=" + id;
	}
}
$(document).ready(function() {
	displayRecords(100, 1);
});
</script>
</body>
</html>
