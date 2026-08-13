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
$employeeCount = count($employees);
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
		.kra-overview-note {
			background:#eef6fb; border:1px solid #c5d9e8; color:#31708f;
			padding:10px 14px; margin-bottom:16px; font-size:13px;
		}
		.kra-emp-card {
			border:1px solid #d9e2ea; margin-bottom:12px; background:#fff;
		}
		.kra-emp-card-title {
			background:#2f6f44; color:#fff; padding:10px 14px; font-size:16px; font-weight:700;
		}
		.kra-emp-card-sub {
			font-size:12px; font-weight:400; margin-top:3px; opacity:0.95;
		}
		.kra-emp-card-body {
			padding:10px 14px; background:#f5f7f9; font-size:13px; color:#555;
		}
		.kra-emp-card-body .label-muted { color:#888; margin-right:4px; }
		#kraExpenseModal .modal-header { background:#2f6f44; color:#fff; }
		#kraExpenseModal .modal-header .close { color:#fff; opacity:0.9; }
		#kraExpenseModal .table th { background:#f5f7f9; }
		#kraExpenseModal .kra-exp-total { font-weight:700; background:#f0f7f2; }
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
				</div>
			</div>
			<div class="portlet light">
				<div class="portlet-body">
					<div class="loading-div" style="display:none;text-align:center;padding:30px;">
						<img src="assets/admin/layout/img/ajax-loader.gif" alt="Loading">
					</div>
					<div id="kra-results">
						<div id="kra-employee-overview">
							<div class="kra-overview-note">
								<i class="fa fa-users"></i>
								<strong>Employee List (<?php echo (int) $employeeCount; ?>)</strong>
								— Select employee above and click <b>Show Report</b> for assigned customers + visit details.
							</div>
							<?php if (empty($employees)) { ?>
								<div class="alert alert-warning">No accessible sales employees found.</div>
							<?php } else {
								foreach ($employees as $employee) {
									$typeLabel = isset($employee['type']) ? str_replace('_', ' ', $employee['type']) : '';
									$meta = array();
									if (!empty($employee['phone'])) {
										$meta[] = htmlspecialchars($employee['phone']);
									}
									if ($typeLabel != '') {
										$meta[] = htmlspecialchars(ucwords($typeLabel));
									}
									if (!empty($employee['state'])) {
										$meta[] = htmlspecialchars($employee['state']);
									}
									if (!empty($employee['city'])) {
										$meta[] = htmlspecialchars($employee['city']);
									}
							?>
								<div class="kra-emp-card">
									<div class="kra-emp-card-title">
										KEY RESULT AREA - <?php echo htmlspecialchars(strtoupper($employee['name'])); ?>
										<div class="kra-emp-card-sub">
											<?php echo !empty($meta) ? implode(' | ', $meta) : 'Select this employee from filter to load report'; ?>
										</div>
									</div>
									<div class="kra-emp-card-body">
										<span class="label-muted">Status:</span> Select employee + Show Report to load assigned customers and visits.
									</div>
								</div>
							<?php
								}
							} ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="kraExpenseModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><i class="fa fa-inr"></i> Approved Expense — Category Wise</h4>
			</div>
			<div class="modal-body">
				<p id="kra_expense_modal_meta" style="margin-bottom:12px;color:#555;"></p>
				<div id="kra_expense_modal_loading" style="display:none;text-align:center;padding:20px;">
					<img src="assets/admin/layout/img/ajax-loader.gif" alt="Loading">
				</div>
				<div id="kra_expense_modal_body"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="js/fSelect.js"></script>
<script type="text/javascript">
	(function () {
		$("#kra_employee_ids").fSelect({placeholder: "Select Sales Employee", numDisplayed: 2});

		var overviewHtml = $("#kra-employee-overview").prop("outerHTML");

		function queryString() {
			var selected = $("#kra_employee_ids").val() || [];
			return $.param({
				from_date: $("#kra_from_date").val(),
				to_date: $("#kra_to_date").val(),
				employee_ids: selected.join(",")
			});
		}

		function showEmployeeOverview() {
			$("#kra-results").html(overviewHtml);
		}

		function loadReport() {
			var selected = $("#kra_employee_ids").val() || [];
			if (!selected.length) {
				toastr.error("Please select at least one Sales Employee.");
				showEmployeeOverview();
				return;
			}
			$(".loading-div").show();
			$("#kra-results").html("");
			$("#kra-results").load("employee_visit_kra_report_get_ajax.php?" + queryString(), function () {
				$(".loading-div").hide();
			});
		}

		function openExpenseBreakdown(employeeId, employeeName) {
			$("#kra_expense_modal_meta").text(
				(employeeName || "Employee") + " | " +
				$("#kra_from_date").val() + " to " + $("#kra_to_date").val()
			);
			$("#kra_expense_modal_body").html("");
			$("#kra_expense_modal_loading").show();
			$("#kraExpenseModal").modal("show");

			$.ajax({
				url: "employee_visit_kra_expense_ajax.php",
				type: "GET",
				dataType: "json",
				data: {
					employee_id: employeeId,
					from_date: $("#kra_from_date").val(),
					to_date: $("#kra_to_date").val()
				},
				success: function (res) {
					$("#kra_expense_modal_loading").hide();
					if (!res || res.ack != 1) {
						$("#kra_expense_modal_body").html(
							'<div class="alert alert-danger">' +
							((res && res.ack_msg) ? res.ack_msg : "Failed to load expense breakdown.") +
							"</div>"
						);
						return;
					}
					$("#kra_expense_modal_meta").text(
						(res.employee_name || employeeName || "Employee") +
						" | " + (res.from_label || "") + " to " + (res.to_label || "")
					);
					if (!res.rows || !res.rows.length) {
						$("#kra_expense_modal_body").html(
							'<div class="alert alert-info">No approved expense found for this period.</div>'
						);
						return;
					}
					var html = '<div class="table-responsive"><table class="table table-bordered table-striped" style="margin-bottom:0;">';
					html += "<thead><tr><th>Sr.</th><th>Category</th><th>Travel / Sub Category</th><th class=\"text-center\">Count</th><th class=\"text-center\">KM</th><th class=\"text-right\">Rate / KM</th><th class=\"text-right\">KM Calc</th><th class=\"text-right\">Approved Amount</th></tr></thead><tbody>";
					$.each(res.rows, function (i, row) {
						var km = row.is_km ? (row.total_km || 0) : "-";
						var rate = row.is_km ? (row.master_rate_label || "-") : "-";
						var kmCalc = row.is_km ? (row.km_calc_amount_label || "-") : "-";
						var subName = row.subcategory_name || "-";
						if (row.is_km) {
							subName = subName + " (KM)";
						}
						html += "<tr>";
						html += "<td>" + (i + 1) + "</td>";
						html += "<td>" + $("<div>").text(row.category_name || "Other").html() + "</td>";
						html += "<td>" + $("<div>").text(subName).html() + "</td>";
						html += "<td class=\"text-center\">" + (row.expense_count || 0) + "</td>";
						html += "<td class=\"text-center\">" + km + "</td>";
						html += "<td class=\"text-right\">" + rate + "</td>";
						html += "<td class=\"text-right\">" + kmCalc + "</td>";
						html += "<td class=\"text-right\">" + (row.approved_amount_label || "") + "</td>";
						html += "</tr>";
					});
					html += "<tr class=\"kra-exp-total\"><td colspan=\"3\"><strong>Total</strong></td>";
					html += "<td class=\"text-center\"><strong>" + (res.total_count || 0) + "</strong></td>";
					html += "<td colspan=\"3\"></td>";
					html += "<td class=\"text-right\"><strong>" + (res.total_amount_label || "") + "</strong></td></tr>";
					html += "</tbody></table></div>";
					html += '<p style="margin-top:10px;font-size:12px;color:#666;"><i class="fa fa-info-circle"></i> Category = Expense Master category (Hotel / Travelling / Food / Other…). Travel/Sub = Bike, Car, Auto/Train… KM Calc = KM × master <b>fix_amount</b>.</p>';
					$("#kra_expense_modal_body").html(html);
				},
				error: function () {
					$("#kra_expense_modal_loading").hide();
					$("#kra_expense_modal_body").html(
						'<div class="alert alert-danger">Failed to load expense breakdown.</div>'
					);
				}
			});
		}

		$(document).on("click", ".kra-approved-expense", function () {
			var empId = $(this).data("employee-id");
			var empName = $(this).data("employee-name") || "";
			if (!empId) {
				toastr.error("Employee not found.");
				return;
			}
			openExpenseBreakdown(empId, empName);
		});

		$("#kra_filter_btn").on("click", loadReport);
		$("#kra_excel_btn").on("click", function () {
			var empCount = ($("#kra_employee_ids").val() || []).length;
			if (empCount === 0) {
				toastr.error("Please select at least one Sales Employee for Excel export.");
				return;
			}
			$.ajax({
				method: "POST",
				url: "employee_visit_kra_report_excel.php",
				data: {
					from_date: $("#kra_from_date").val(),
					to_date: $("#kra_to_date").val(),
					employee_ids: ($("#kra_employee_ids").val() || []).join(",")
				},
				dataType: "text",
				timeout: 600000,
				beforeSend: function () {
					$(".loading-div").show();
					if (!$("#kra_export_status").length) {
						$(".loading-div").append('<div id="kra_export_status" style="margin-top:10px;font-weight:600;"></div>');
					}
					$("#kra_export_status").text("Exporting Excel… please wait.");
				},
				success: function (raw) {
					$(".loading-div").hide();
					$("#kra_export_status").text("");
					var result = null;
					try { result = $.parseJSON(raw); } catch (e) { result = null; }
					if (!result || result.ack == 0 || !result.file_path) {
						var msg = (result && result.ack_msg) ? result.ack_msg : "Excel download failed";
						if (!result && raw) {
							msg += "\n" + String(raw).replace(/<[^>]+>/g, " ").substring(0, 250);
						}
						alert(msg);
						return;
					}
					window.location.href = "<?= SITEURL ?>" + result.file_path;
				},
				error: function (xhr, status) {
					$(".loading-div").hide();
					$("#kra_export_status").text("");
					var msg = "Excel download failed";
					if (status === "timeout") {
						msg = "Excel export timed out. Please try again with fewer employees.";
					} else if (xhr && xhr.responseText) {
						try {
							var parsed = $.parseJSON(xhr.responseText);
							if (parsed && parsed.ack_msg) msg = parsed.ack_msg;
							else msg += " (HTTP " + xhr.status + ")";
						} catch (e) {
							msg += " (HTTP " + xhr.status + ")";
						}
					}
					alert(msg);
				}
			});
		});
	})();
</script>
</body>
</html>
