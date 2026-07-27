<?php
$page_id = 671;
$page_slug = 'remark_wise_report';
$main_page = 'remark_analysis_report';
$page_title = "Remark Wise Report";
$page_hierarchy = array(
	array("link" => "", "title" => "Remark Analysis Report"),
	array("link" => "remark_wise_report.php", "title" => $page_title),
);
include("connect.php");
require_once("../include/class.remark_analysis_report.php");
$report = new RemarkAnalysisReport($db);
$employees = $report->getAccessibleEmployees(array(), $rights);
$customers = $report->getCustomerOptions();
$remarkLabels = $report->getRemarkLabels();
$reasonLabels = $report->getReasonLabels();
$hierarchy = $report->getHierarchy();
$defaultFrom = date("Y-m-01");
$defaultTo = date("Y-m-t");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
	<?php include("include_css.php"); ?>
	<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
	<style>
		.rar-filter-row { display:flex; flex-wrap:wrap; align-items:flex-end; gap:12px; }
		.rar-filter-field { min-width:160px; }
		.rar-filter-wide { min-width:260px; }
		#rar-results { min-height:120px; }
		.rar-summary-table th { background:#f5f5f5; }
		.rar-parent-row td { font-weight:700; background:#eef6ff; }
		.rar-child-row td:first-child { padding-left:28px; }
		.rar-code { font-weight:700; color:#1a7a3a; }
	</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="dashboard.php" class="primary"><i class="fa fa-arrow-circle-o-left" style="font-size:22px!important;"></i></a>
					&nbsp;<?php $db->pageBar($page_hierarchy); ?></h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption"><i class="fa fa-filter"></i> Filters</div>
					<div class="tools"><a href="javascript:;" class="collapse"></a></div>
				</div>
				<div class="portlet-body">
					<div class="rar-filter-row">
						<div class="rar-filter-field">
							<label>From Date</label>
							<input type="date" class="form-control" id="rar_from_date" value="<?php echo $defaultFrom; ?>">
						</div>
						<div class="rar-filter-field">
							<label>To Date</label>
							<input type="date" class="form-control" id="rar_to_date" value="<?php echo $defaultTo; ?>">
						</div>
						<div class="rar-filter-wide">
							<label>Sales Person</label>
							<select class="form-control" multiple="multiple" id="rar_employee_ids">
								<?php foreach ($employees as $employee) { ?>
									<option value="<?php echo (int) $employee['id']; ?>"><?php echo htmlspecialchars($employee['name']); ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="rar-filter-wide">
							<label>Customer</label>
							<select class="form-control" multiple="multiple" id="rar_customer_ids">
								<?php foreach ($customers as $customer) { ?>
									<option value="<?php echo (int) $customer['id']; ?>"><?php echo htmlspecialchars($customer['label']); ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="rar-filter-field">
							<label>Remark Code</label>
							<select class="form-control" id="rar_remark_code">
								<option value="">All Remarks</option>
								<?php foreach ($hierarchy as $parent => $children) { ?>
									<option value="<?php echo $parent; ?>"><?php echo $parent; ?> - <?php echo htmlspecialchars($remarkLabels[$parent]); ?></option>
									<?php foreach ($children as $child) { ?>
										<option value="<?php echo $child; ?>">&nbsp;&nbsp;<?php echo $child; ?> - <?php echo htmlspecialchars($reasonLabels[$child]); ?></option>
									<?php } ?>
								<?php } ?>
							</select>
						</div>
						<div>
							<button type="button" class="btn btn-success" id="rar_filter_btn"><i class="fa fa-search"></i> Show Report</button>
							<button type="button" class="btn btn-default" id="rar_clear_btn">Clear</button>
						</div>
					</div>
				</div>
			</div>
			<div class="portlet light">
				<div class="portlet-body">
					<div class="loading-div" style="display:none;text-align:center;padding:30px;">
						<img src="assets/admin/layout/img/ajax-loader.gif" alt="Loading">
					</div>
					<div id="rar-results"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="js/fSelect.js"></script>
<script type="text/javascript">
(function () {
	var currentPage = 1;
	var pageSize = 10;

	$("#rar_employee_ids").fSelect({placeholder: "All Sales Persons", numDisplayed: 2});
	$("#rar_customer_ids").fSelect({placeholder: "All Customers", numDisplayed: 2});

	function queryString(page) {
		var employees = $("#rar_employee_ids").val() || [];
		var customers = $("#rar_customer_ids").val() || [];
		return $.param({
			from_date: $("#rar_from_date").val(),
			to_date: $("#rar_to_date").val(),
			employee_ids: employees.join(","),
			customer_ids: customers.join(","),
			remark_code: $("#rar_remark_code").val(),
			show: pageSize,
			page: page || 1
		});
	}

	function loadReport(page) {
		currentPage = page || 1;
		$(".loading-div").show();
		$("#rar-results").html("");
		$("#rar-results").load("remark_wise_report_get_ajax.php?" + queryString(currentPage), function () {
			$(".loading-div").hide();
		});
	}

	$("#rar_filter_btn").on("click", function () {
		loadReport(1);
	});

	$("#rar_clear_btn").on("click", function () {
		$("#rar_from_date").val("<?php echo $defaultFrom; ?>");
		$("#rar_to_date").val("<?php echo $defaultTo; ?>");
		$("#rar_remark_code").val("");
		pageSize = 10;
		try {
			$("#rar_employee_ids").fSelect("reload");
			$("#rar_customer_ids").fSelect("reload");
		} catch (e) {}
		$("#rar_employee_ids").val([]);
		$("#rar_customer_ids").val([]);
		$(".fs-option.selected").removeClass("selected");
		$(".fs-label").each(function () {
			var ph = $(this).closest(".fs-wrap").find("select").attr("id") === "rar_customer_ids"
				? "All Customers" : "All Sales Persons";
			$(this).text(ph);
		});
		loadReport(1);
	});

	$("#rar-results").on("click", ".paging_simple_numbers a", function (e) {
		e.preventDefault();
		var page = $(this).attr("data-page");
		if (!page) {
			return;
		}
		loadReport(page);
	});

	$("#rar-results").on("change", "#rar_numRecords", function () {
		pageSize = parseInt($(this).val(), 10) || 10;
		loadReport(1);
	});

	loadReport(1);
})();
</script>
</body>
</html>
