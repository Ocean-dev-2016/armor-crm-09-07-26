<?php
$page_id = 599;
$page_slug = 'visit_report_page';
$page_title = "Employee Visit KRA Report";
$page_hierarchy = array(
	array("link" => "", "title" => "Reports"),
	array("link" => "employee_visit_kra_report.php", "title" => $page_title),
);
include("connect.php");
require_once("../include/class.employee_visit_kra_report.php");
$kraReport = new EmployeeVisitKraReport($db);
$employees = $kraReport->getAccessibleEmployees(array(), $rights);
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
		.kra-filter-row { display:flex; flex-wrap:wrap; align-items:flex-end; gap:12px; }
		.kra-filter-field { min-width:180px; }
		.kra-filter-employee { min-width:320px; }
		#kra-results { min-height:120px; }
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
					<div class="kra-filter-row">
						<div class="kra-filter-field">
							<label>From Date</label>
							<input type="date" class="form-control" id="kra_from_date" value="<?php echo $defaultFrom; ?>">
						</div>
						<div class="kra-filter-field">
							<label>To Date</label>
							<input type="date" class="form-control" id="kra_to_date" value="<?php echo $defaultTo; ?>">
						</div>
						<div class="kra-filter-employee">
							<label>Sales Employee</label>
							<select class="form-control" multiple="multiple" id="kra_employee_ids">
								<?php foreach ($employees as $employee) { ?>
									<option value="<?php echo (int) $employee['id']; ?>"><?php echo htmlspecialchars($employee['name']); ?></option>
								<?php } ?>
							</select>
						</div>
						<div>
							<button type="button" class="btn btn-success" id="kra_filter_btn"><i class="fa fa-search"></i> Show Report</button>
							<?php if ($rights['export_excel_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) { ?>
								<button type="button" class="btn green-haze" id="kra_excel_btn"><i class="fa fa-file-excel-o"></i> Excel</button>
							<?php } ?>
						</div>
					</div>
					<p class="help-block" style="margin-top:10px;">Maximum 62 days are shown at a time. Salary remains N/A until an employee-to-sales-person mapping is configured.</p>
				</div>
			</div>
			<div class="portlet light">
				<div class="portlet-body">
					<div class="loading-div" style="display:none;text-align:center;padding:30px;">
						<img src="assets/admin/layout/img/ajax-loader.gif" alt="Loading">
					</div>
					<div id="kra-results"></div>
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
		$("#kra_employee_ids").fSelect({placeholder: "All accessible employees", numDisplayed: 2});

		function queryString() {
			var selected = $("#kra_employee_ids").val() || [];
			return $.param({
				from_date: $("#kra_from_date").val(),
				to_date: $("#kra_to_date").val(),
				employee_ids: selected.join(",")
			});
		}

		function loadReport() {
			$(".loading-div").show();
			$("#kra-results").html("");
			$("#kra-results").load("employee_visit_kra_report_get_ajax.php?" + queryString(), function () {
				$(".loading-div").hide();
			});
		}

		$("#kra_filter_btn").on("click", loadReport);
		$("#kra_excel_btn").on("click", function () {
			$.ajax({
				method: "POST",
				url: "employee_visit_kra_report_excel.php",
				data: {
					from_date: $("#kra_from_date").val(),
					to_date: $("#kra_to_date").val(),
					employee_ids: ($("#kra_employee_ids").val() || []).join(",")
				},
				dataType: "json",
				beforeSend: function () {
					$(".loading-div").show();
				},
				success: function (result) {
					$(".loading-div").hide();
					if (!result || result.ack == 0 || !result.file_path) {
						alert((result && result.ack_msg) ? result.ack_msg : "Excel download failed");
						return;
					}
					window.location.href = "<?= SITEURL ?>" + result.file_path;
				},
				error: function (xhr) {
					$(".loading-div").hide();
					var msg = "Excel download failed";
					if (xhr && xhr.responseText) {
						try {
							var parsed = $.parseJSON(xhr.responseText);
							if (parsed && parsed.ack_msg) msg = parsed.ack_msg;
						} catch (e) {}
					}
					alert(msg);
				}
			});
		});
		$(document).ready(loadReport);
	})();
</script>
</body>
</html>
