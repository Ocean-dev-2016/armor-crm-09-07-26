<?php
$page_id = 675;
$page_slug = 'customer_order_report';
$main_page = "Customer Order Report";
$page_title = "Customer Order Report";
$page_hierarchy = array(
	array("link" => "", "title" => "Sales Team Reports"),
	array("link" => "customer_order_report.php", "title" => $page_title),
);
include("connect.php");
require_once("../include/class.customer_order_report.php");

function cor_attr($value)
{
	return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$corReport = new CustomerOrderReport($db);
$employees = $corReport->getAccessibleEmployees($rights);
$customerTypes = $corReport->getCustomerTypes();
$intervals = $corReport->getIntervals();
$sortOptions = $corReport->getSortOptions();
$canExport = (isset($rights['export_excel_flag']) && $rights['export_excel_flag'] == 1) || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
	<?php include("include_css.php"); ?>
	<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
	<link rel="stylesheet" type="text/css" href="css/order_report.css"/>
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
			<div class="orp-help">
				<span><i class="fa fa-info-circle"></i> <b>Which customer ordered and who did not</b> in the selected period.</span>
				<span><span class="orp-dot orp-dot-green"></span>Ordered in period</span>
				<span><span class="orp-dot orp-dot-orange"></span>Ordered before, not in period</span>
				<span><span class="orp-dot orp-dot-red"></span>Never ordered</span>
				<span>Only <b>approved PI / orders</b> are counted (Pending, Draft, Disapproved, Cancelled are not).</span>
			</div>

			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption"><i class="fa fa-filter"></i> Filters</div>
					<div class="tools"><a href="javascript:;" class="collapse"></a></div>
				</div>
				<div class="portlet-body">
					<div class="orp-filter-row">
						<div class="orp-btn-group">
							<span class="orp-label">1. Select Order Period</span>
							<?php foreach ($intervals as $key => $label) { ?>
								<button type="button" class="btn btn-sm default cor-interval<?php echo $key === "6" ? " active" : ""; ?>" data-interval="<?php echo cor_attr($key); ?>"><?php echo htmlspecialchars($label); ?></button>
							<?php } ?>
							<div class="orp-period-text" id="cor_period_text"></div>
						</div>
						<div class="orp-field orp-custom-dates">
							<label>From Date</label>
							<input type="date" class="form-control" id="cor_from_date" value="<?php echo date("Y-m-d", strtotime("-6 months +1 day")); ?>">
						</div>
						<div class="orp-field orp-custom-dates">
							<label>To Date</label>
							<input type="date" class="form-control" id="cor_to_date" value="<?php echo date("Y-m-d"); ?>">
						</div>
					</div>
					<div class="orp-filter-row">
						<div class="orp-field orp-field-wide">
							<label>2. Search Customer</label>
							<input type="text" class="form-control" id="cor_search" placeholder="Customer name / code / city / mobile — press Enter">
						</div>
						<div class="orp-field" style="min-width:260px;">
							<label>Sales Person</label>
							<select class="form-control" multiple="multiple" id="cor_employee_ids">
								<?php foreach ($employees as $employee) { ?>
									<option value="<?php echo (int) $employee['id']; ?>"><?php echo htmlspecialchars($employee['name']); ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="orp-field">
							<label>Customer Type</label>
							<select class="form-control" id="cor_customer_type">
								<option value="0">All Types</option>
								<?php foreach ($customerTypes as $id => $name) { ?>
									<option value="<?php echo (int) $id; ?>"><?php echo htmlspecialchars($name); ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="orp-field">
							<label>Sort By</label>
							<select class="form-control" id="cor_sort">
								<?php foreach ($sortOptions as $key => $label) { ?>
									<option value="<?php echo cor_attr($key); ?>"><?php echo htmlspecialchars($label); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="orp-filter-row">
						<label style="font-weight:normal;margin:0 12px 0 0;"><input type="checkbox" id="cor_include_leads" value="1"> Include Lead customers</label>
						<div class="orp-actions">
							<button type="button" class="btn btn-success" id="cor_filter_btn"><i class="fa fa-search"></i> Show Report</button>
							<button type="button" class="btn default" id="cor_reset_btn"><i class="fa fa-refresh"></i> Reset</button>
							<?php if ($canExport) { ?>
								<button type="button" class="btn green-haze" id="cor_excel_btn"><i class="fa fa-file-excel-o"></i> Excel</button>
							<?php } ?>
						</div>
					</div>
					<input type="hidden" id="cor_order_status" value="all">
				</div>
			</div>
			<div class="portlet light">
				<div class="portlet-body">
					<div class="loading-div" style="display:none;text-align:center;padding:30px;">
						<img src="assets/admin/layout/img/ajax-loader.gif" alt="Loading">
					</div>
					<div id="cor-results"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade orp-modal" id="corDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="cor_detail_title"><i class="fa fa-list"></i> Customer Orders</h4>
			</div>
			<div class="modal-body">
				<p id="cor_detail_meta" style="margin-bottom:12px;color:#555;"></p>
				<div id="cor_detail_loading" style="display:none;text-align:center;padding:20px;">
					<img src="assets/admin/layout/img/ajax-loader.gif" alt="Loading">
				</div>
				<div id="cor_detail_body" style="max-height:65vh;overflow:auto;"></div>
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
		$("#cor_employee_ids").fSelect({placeholder: "All Sales Persons", numDisplayed: 2});

		var currentInterval = "6";
		var currentPage = 1;

		function esc(v) {
			return $("<div>").text(v == null ? "" : String(v)).html();
		}

		function fmt(d) {
			return ("0" + d.getDate()).slice(-2) + "/" + ("0" + (d.getMonth() + 1)).slice(-2) + "/" + d.getFullYear();
		}

		function updatePeriodText() {
			var text;
			if (currentInterval === "custom") {
				var f = $("#cor_from_date").val(), t = $("#cor_to_date").val();
				text = (f && t) ? fmt(new Date(f)) + " to " + fmt(new Date(t)) : "Select From and To date";
			} else {
				var to = new Date();
				var from = new Date();
				from.setMonth(from.getMonth() - parseInt(currentInterval, 10));
				from.setDate(from.getDate() + 1);
				text = fmt(from) + " to " + fmt(to);
			}
			$("#cor_period_text").html('<i class="fa fa-calendar"></i> ' + esc(text));
		}

		function params(extra) {
			var p = {
				interval: currentInterval,
				from_date: $("#cor_from_date").val(),
				to_date: $("#cor_to_date").val(),
				employee_ids: ($("#cor_employee_ids").val() || []).join(","),
				order_status: $("#cor_order_status").val(),
				customer_type: $("#cor_customer_type").val(),
				sort: $("#cor_sort").val(),
				search: $("#cor_search").val(),
				include_leads: $("#cor_include_leads").is(":checked") ? 1 : 0,
				page: currentPage
			};
			return $.extend(p, extra || {});
		}

		function loadReport(page) {
			currentPage = page || 1;
			$(".loading-div").show();
			$("#cor-results").html("");
			$("#cor-results").load("customer_order_report_ajax.php?" + $.param(params({action: "list"})), function () {
				$(".loading-div").hide();
			});
		}

		$(".cor-interval").on("click", function () {
			$(".cor-interval").removeClass("active");
			$(this).addClass("active");
			currentInterval = String($(this).data("interval"));
			$(".orp-custom-dates").toggle(currentInterval === "custom");
			updatePeriodText();
			if (currentInterval !== "custom") {
				loadReport(1);
			}
		});
		$("#cor_from_date, #cor_to_date").on("change", updatePeriodText);

		$("#cor_filter_btn").on("click", function () { loadReport(1); });
		$("#cor_search").on("keypress", function (e) {
			if (e.which === 13) { loadReport(1); }
		});
		$("#cor_customer_type, #cor_sort, #cor_include_leads").on("change", function () { loadReport(1); });

		$("#cor_reset_btn").on("click", function () {
			$("#cor_search").val("");
			$("#cor_customer_type").val("0");
			$("#cor_sort").val("last_order_asc");
			$("#cor_include_leads").prop("checked", false);
			$("#cor_order_status").val("all");
			$("#cor_employee_ids").val([]);
			$("#cor_employee_ids").fSelect("reload");
			$('.cor-interval[data-interval="6"]').trigger("click");
		});

		$(document).on("click", ".cor-page", function () {
			if (!$(this).is(":disabled")) {
				loadReport(parseInt($(this).data("page"), 10) || 1);
			}
		});

		$(document).on("click", ".orp-card-click", function () {
			$("#cor_order_status").val($(this).data("status"));
			loadReport(1);
		});

		$(document).on("click", ".cor-detail", function () {
			var customerId = $(this).data("id");
			var scope = $(this).data("scope") || "period";
			$("#cor_detail_title").html('<i class="fa fa-list"></i> Customer Orders');
			$("#cor_detail_meta").text("");
			$("#cor_detail_body").html("");
			$("#cor_detail_loading").show();
			$("#corDetailModal").modal("show");
			$.ajax({
				url: "customer_order_report_ajax.php",
				type: "GET",
				dataType: "json",
				data: params({action: "detail", customer_id: customerId, scope: scope}),
				success: function (res) {
					$("#cor_detail_loading").hide();
					if (!res || res.ack != 1) {
						$("#cor_detail_body").html('<div class="alert alert-danger">' + esc(res && res.ack_msg ? res.ack_msg : "Failed to load orders.") + "</div>");
						return;
					}
					$("#cor_detail_title").html('<i class="fa fa-list"></i> ' + esc(res.customer));
					$("#cor_detail_meta").text(res.period_label);
					var html = "";
					if (res.in_period != 1 && res.lifetime != 1) {
						html += '<div class="alert alert-warning" style="margin-bottom:10px;">No approved order in selected period. Showing latest orders.</div>';
					}
					if (!res.rows || !res.rows.length) {
						$("#cor_detail_body").html(html + '<div class="alert alert-info">No approved orders found.</div>');
						return;
					}
					html += '<div class="table-responsive"><table class="table table-bordered table-striped table-condensed" style="font-size:12px;margin-bottom:8px;"><thead><tr><th>Sr.</th>';
					$.each(res.columns, function (i, col) { html += "<th>" + esc(col) + "</th>"; });
					html += "</tr></thead><tbody>";
					$.each(res.rows, function (i, row) {
						html += "<tr><td>" + (i + 1) + "</td>";
						$.each(row, function (j, cell) {
							html += '<td' + (j === row.length - 1 ? ' class="text-right"' : '') + '>' + esc(cell) + "</td>";
						});
						html += "</tr>";
					});
					html += "</tbody></table></div>";
					html += "<p><strong>" + esc(res.total_label) + "</strong></p>";
					$("#cor_detail_body").html(html);
				},
				error: function () {
					$("#cor_detail_loading").hide();
					$("#cor_detail_body").html('<div class="alert alert-danger">Failed to load orders.</div>');
				}
			});
		});

		$("#cor_excel_btn").on("click", function () {
			window.location.href = "customer_order_report_ajax.php?" + $.param(params({action: "export"}));
		});

		updatePeriodText();
		loadReport(1);
	})();
</script>
</body>
</html>
