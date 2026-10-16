<?php
$page_id = 676;
$page_slug = 'pincode_order_report';
$main_page = "Pincode Wise Order Report";
$page_title = "Pincode / State Wise Order Report";
$page_hierarchy = array(
	array("link" => "", "title" => "Sales Team Reports"),
	array("link" => "pincode_order_report.php", "title" => $page_title),
);
include("connect.php");
require_once("../include/class.customer_order_report.php");

function por_attr($value)
{
	return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$porReport = new CustomerOrderReport($db);
$porFilters = $porReport->normalizeAreaFilters(array(), $rights);
$employees = $porReport->getAccessibleEmployees($rights);
$customerTypes = $porReport->getCustomerTypes();
$states = $porReport->getStates($porFilters);
$intervals = $porReport->getIntervals();
$areaSorts = $porReport->getAreaSortOptions();
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
				<span><i class="fa fa-info-circle"></i> <b>Which State / Pincode is giving orders</b> and where customers are not ordering.</span>
				<span><span class="orp-dot orp-dot-green"></span>50%+ customers ordered</span>
				<span><span class="orp-dot orp-dot-orange"></span>Some customers ordered</span>
				<span><span class="orp-dot orp-dot-red"></span>No order from this area</span>
				<span>Start with <b>State Wise</b>, then click <b>Pincodes</b> to go deeper.</span>
			</div>

			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption"><i class="fa fa-filter"></i> Filters</div>
					<div class="tools"><a href="javascript:;" class="collapse"></a></div>
				</div>
				<div class="portlet-body">
					<div class="orp-filter-row">
						<div class="orp-btn-group">
							<span class="orp-label">1. Report Type</span>
							<button type="button" class="btn btn-sm default por-group active" data-group="state"><i class="fa fa-globe"></i> State Wise</button>
							<button type="button" class="btn btn-sm default por-group" data-group="pincode"><i class="fa fa-map-marker"></i> Pincode Wise</button>
						</div>
						<div class="orp-btn-group">
							<span class="orp-label">2. Order Period</span>
							<?php foreach ($intervals as $key => $label) { ?>
								<button type="button" class="btn btn-sm default cor-interval<?php echo $key === "6" ? " active" : ""; ?>" data-interval="<?php echo por_attr($key); ?>"><?php echo htmlspecialchars($label); ?></button>
							<?php } ?>
							<div class="orp-period-text" id="por_period_text"></div>
						</div>
						<div class="orp-field orp-custom-dates">
							<label>From Date</label>
							<input type="date" class="form-control" id="por_from_date" value="<?php echo date("Y-m-d", strtotime("-6 months +1 day")); ?>">
						</div>
						<div class="orp-field orp-custom-dates">
							<label>To Date</label>
							<input type="date" class="form-control" id="por_to_date" value="<?php echo date("Y-m-d"); ?>">
						</div>
					</div>
					<div class="orp-filter-row">
						<div class="orp-field">
							<label>State</label>
							<select class="form-control" id="por_state">
								<option value="">All States</option>
								<?php foreach ($states as $stateName) { ?>
									<option value="<?php echo por_attr($stateName); ?>"><?php echo htmlspecialchars($stateName); ?></option>
								<?php } ?>
								<option value="<?php echo CustomerOrderReport::AREA_NONE; ?>">Not Set</option>
							</select>
						</div>
						<div class="orp-field" style="min-width:150px;">
							<label>Pincode</label>
							<input type="text" class="form-control" id="por_pincode" placeholder="e.g. 380 or 380015" maxlength="10">
						</div>
						<div class="orp-field" style="min-width:260px;">
							<label>Sales Person</label>
							<select class="form-control" multiple="multiple" id="por_employee_ids">
								<?php foreach ($employees as $employee) { ?>
									<option value="<?php echo (int) $employee['id']; ?>"><?php echo htmlspecialchars($employee['name']); ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="orp-field">
							<label>Customer Type</label>
							<select class="form-control" id="por_customer_type">
								<option value="0">All Types</option>
								<?php foreach ($customerTypes as $id => $name) { ?>
									<option value="<?php echo (int) $id; ?>"><?php echo htmlspecialchars($name); ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="orp-field">
							<label>Sort By</label>
							<select class="form-control" id="por_area_sort">
								<?php foreach ($areaSorts as $key => $label) { ?>
									<option value="<?php echo por_attr($key); ?>"><?php echo htmlspecialchars($label); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="orp-filter-row">
						<label style="font-weight:normal;margin:0 12px 0 0;"><input type="checkbox" id="por_include_leads" value="1"> Include Lead customers</label>
						<div class="orp-actions">
							<button type="button" class="btn btn-success" id="por_filter_btn"><i class="fa fa-search"></i> Show Report</button>
							<button type="button" class="btn default" id="por_reset_btn"><i class="fa fa-refresh"></i> Reset</button>
							<?php if ($canExport) { ?>
								<button type="button" class="btn green-haze" id="por_excel_btn"><i class="fa fa-file-excel-o"></i> Excel</button>
							<?php } ?>
						</div>
					</div>
					<input type="hidden" id="por_area_status" value="all">
				</div>
			</div>
			<div class="portlet light">
				<div class="portlet-body">
					<div class="loading-div" style="display:none;text-align:center;padding:30px;">
						<img src="assets/admin/layout/img/ajax-loader.gif" alt="Loading">
					</div>
					<div id="por-results"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade orp-modal" id="porCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="por_modal_title"><i class="fa fa-users"></i> Customers</h4>
			</div>
			<div class="modal-body">
				<div id="por_modal_meta" class="orp-modal-meta" style="display:none;"></div>
				<div class="orp-modal-filters">
					<button type="button" class="btn btn-xs default por-modal-filter" data-state="all">All <span class="badge" data-count="all">0</span></button>
					<button type="button" class="btn btn-xs default por-modal-filter" data-state="ordered">Ordered <span class="badge" data-count="ordered">0</span></button>
					<button type="button" class="btn btn-xs default por-modal-filter" data-state="not_ordered">Not Ordered <span class="badge" data-count="not_ordered">0</span></button>
					<button type="button" class="btn btn-xs default por-modal-filter" data-state="never">Never Ordered <span class="badge" data-count="never">0</span></button>
				</div>
				<div id="por_modal_loading" style="display:none;text-align:center;padding:20px;">
					<img src="assets/admin/layout/img/ajax-loader.gif" alt="Loading">
				</div>
				<div id="por_modal_body" class="orp-modal-body-wrap"></div>
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
		$("#por_employee_ids").fSelect({placeholder: "All Sales Persons", numDisplayed: 2});

		var currentGroup = "state";
		var currentInterval = "6";
		var currentPage = 1;
		var modalRows = [];
		var modalColumns = [];

		function esc(v) {
			return $("<div>").text(v == null ? "" : String(v)).html();
		}

		function fmt(d) {
			return ("0" + d.getDate()).slice(-2) + "/" + ("0" + (d.getMonth() + 1)).slice(-2) + "/" + d.getFullYear();
		}

		function updatePeriodText() {
			var text;
			if (currentInterval === "custom") {
				var f = $("#por_from_date").val(), t = $("#por_to_date").val();
				text = (f && t) ? fmt(new Date(f)) + " to " + fmt(new Date(t)) : "Select From and To date";
			} else {
				var to = new Date();
				var from = new Date();
				from.setMonth(from.getMonth() - parseInt(currentInterval, 10));
				from.setDate(from.getDate() + 1);
				text = fmt(from) + " to " + fmt(to);
			}
			$("#por_period_text").html('<i class="fa fa-calendar"></i> ' + esc(text));
		}

		function params(extra) {
			var p = {
				group_by: currentGroup,
				interval: currentInterval,
				from_date: $("#por_from_date").val(),
				to_date: $("#por_to_date").val(),
				state: $("#por_state").val(),
				pincode: $("#por_pincode").val(),
				employee_ids: ($("#por_employee_ids").val() || []).join(","),
				customer_type: $("#por_customer_type").val(),
				area_status: $("#por_area_status").val(),
				area_sort: $("#por_area_sort").val(),
				include_leads: $("#por_include_leads").is(":checked") ? 1 : 0,
				page: currentPage
			};
			return $.extend(p, extra || {});
		}

		function loadReport(page) {
			currentPage = page || 1;
			$(".loading-div").show();
			$("#por-results").html("");
			$("#por-results").load("pincode_order_report_ajax.php?" + $.param(params({action: "list"})), function () {
				$(".loading-div").hide();
			});
		}

		function setGroup(group) {
			currentGroup = group;
			$(".por-group").removeClass("active");
			$('.por-group[data-group="' + group + '"]').addClass("active");
		}

		$(".por-group").on("click", function () {
			setGroup(String($(this).data("group")));
			$("#por_area_status").val("all");
			loadReport(1);
		});

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
		$("#por_from_date, #por_to_date").on("change", updatePeriodText);

		$("#por_filter_btn").on("click", function () { loadReport(1); });
		$("#por_pincode").on("keypress", function (e) {
			if (e.which === 13) { loadReport(1); }
		});
		$("#por_state, #por_customer_type, #por_area_sort, #por_include_leads").on("change", function () { loadReport(1); });

		$("#por_reset_btn").on("click", function () {
			$("#por_state").val("");
			$("#por_pincode").val("");
			$("#por_customer_type").val("0");
			$("#por_area_sort").val("not_ordered_desc");
			$("#por_area_status").val("all");
			$("#por_include_leads").prop("checked", false);
			$("#por_employee_ids").val([]);
			$("#por_employee_ids").fSelect("reload");
			setGroup("state");
			$('.cor-interval[data-interval="6"]').trigger("click");
		});

		$(document).on("click", ".cor-page", function () {
			if (!$(this).is(":disabled")) {
				loadReport(parseInt($(this).data("page"), 10) || 1);
			}
		});

		$(document).on("click", ".orp-card-click", function () {
			$("#por_area_status").val($(this).data("area-status"));
			loadReport(1);
		});

		$(document).on("click", ".por-drill", function () {
			var stateVal = String($(this).data("key"));
			if (!$("#por_state option").filter(function () { return $(this).val() === stateVal; }).length) {
				$("#por_state").append($("<option>").val(stateVal).text(stateVal));
			}
			$("#por_state").val(stateVal);
			$("#por_area_status").val("all");
			setGroup("pincode");
			loadReport(1);
		});

		$(document).on("click", ".por-back-states", function () {
			$("#por_state").val("");
			setGroup("state");
			loadReport(1);
		});

		$(document).on("click", ".por-clear-state", function () {
			$("#por_state").val("");
			loadReport(1);
		});

		function renderModalRows(filter) {
			$(".por-modal-filter").removeClass("active");
			$('.por-modal-filter[data-state="' + filter + '"]').addClass("active");
			var rows = $.grep(modalRows, function (r) {
				if (filter === "all") { return true; }
				if (filter === "not_ordered") { return r.state !== "ordered"; }
				return r.state === filter;
			});
			if (!rows.length) {
				$("#por_modal_body").html('<div class="orp-modal-empty"><i class="fa fa-inbox"></i>No customers in this list.</div>');
				return;
			}
			var html = '<table class="orp-modal-table"><thead><tr>'
				+ '<th class="orp-col-num">Sr.</th>'
				+ '<th>Customer</th>'
				+ '<th>Mobile</th>'
				+ '<th>City</th>'
				+ '<th>Sales Person</th>'
				+ '<th class="orp-col-right">Period Orders</th>'
				+ '<th class="orp-col-right">Period Amount</th>'
				+ '<th class="orp-col-right">Last Order</th>'
				+ '<th class="orp-col-status">Status</th>'
				+ '</tr></thead><tbody>';
			$.each(rows, function (i, row) {
				var company = row.company != null ? row.company : (row.cells && row.cells[0] ? row.cells[0] : "");
				var person = row.person || "";
				var mobile = row.mobile != null ? row.mobile : (row.cells ? row.cells[1] : "");
				var city = row.city != null ? row.city : (row.cells ? row.cells[2] : "");
				var sales = row.sales_person != null ? row.sales_person : (row.cells ? row.cells[3] : "");
				var orders = row.period_orders != null ? row.period_orders : (row.cells ? row.cells[4] : "0");
				var amount = row.period_amount != null ? row.period_amount : (row.cells ? row.cells[5] : "0.00");
				var lastOrder = row.last_order != null ? row.last_order : (row.cells ? row.cells[6] : "-");
				var stateClass = row.state ? ("orp-row-" + row.state) : "";
				html += '<tr class="' + esc(stateClass) + '">'
					+ '<td class="orp-col-num">' + (i + 1) + '</td>'
					+ '<td class="orp-modal-cust"><span class="orp-name">' + esc(company) + '</span>'
					+ (person ? '<span class="orp-sub">' + esc(person) + '</span>' : '')
					+ '</td>'
					+ '<td class="orp-col-mobile">' + esc(mobile || "-") + '</td>'
					+ '<td class="orp-col-city">' + esc(city || "-") + '</td>'
					+ '<td class="orp-col-sales">' + esc(sales || "-") + '</td>'
					+ '<td class="orp-col-right">' + esc(orders) + '</td>'
					+ '<td class="orp-col-right">' + esc(amount) + '</td>'
					+ '<td class="orp-col-right">' + esc(lastOrder || "-") + '</td>'
					+ '<td class="orp-col-status"><span class="orp-badge orp-badge-' + esc(row.state) + '">'
					+ esc(row.state_label || "") + '</span></td>'
					+ '</tr>';
			});
			html += "</tbody></table>";
			$("#por_modal_body").html(html);
		}

		$(document).on("click", ".por-modal-filter", function () {
			renderModalRows(String($(this).data("state")));
		});

		$(document).on("click", ".por-customers", function () {
			var areaKey = String($(this).data("key"));
			var areaLabel = String($(this).data("label") || "");
			var tab = String($(this).data("tab") || "all");
			modalRows = [];
			$(".por-modal-filter .badge").text("0");
			$("#por_modal_title").html('<i class="fa fa-users"></i> Customers');
			$("#por_modal_meta").hide().empty();
			$("#por_modal_body").html("");
			$("#por_modal_loading").show();
			$("#porCustomerModal").modal("show");
			$.ajax({
				url: "pincode_order_report_ajax.php",
				type: "GET",
				dataType: "json",
				data: params({action: "customers", area_key: areaKey, area_label: areaLabel}),
				success: function (res) {
					$("#por_modal_loading").hide();
					if (!res || res.ack != 1) {
						$("#por_modal_body").html('<div class="alert alert-danger">' + esc(res && res.ack_msg ? res.ack_msg : "Failed to load customers.") + "</div>");
						return;
					}
					$("#por_modal_title").html('<i class="fa fa-map-marker"></i> ' + esc(res.title));
					$("#por_modal_meta").html(
						'<span class="orp-meta-item"><i class="fa fa-calendar"></i> Period: <b>' + esc(res.period_label) + '</b></span>'
						+ '<span class="orp-meta-item">Period Orders: <b>' + esc(res.orders_total != null ? res.orders_total : "-") + '</b></span>'
						+ '<span class="orp-meta-item">Amount: <b>' + esc(res.amount_total || "-") + '</b></span>'
					).show();
					$.each(res.counts || {}, function (k, v) {
						$('.por-modal-filter .badge[data-count="' + k + '"]').text(v);
					});
					modalRows = res.rows || [];
					modalColumns = res.columns || [];
					renderModalRows(tab);
				},
				error: function () {
					$("#por_modal_loading").hide();
					$("#por_modal_body").html('<div class="alert alert-danger">Failed to load customers.</div>');
				}
			});
		});

		$("#por_excel_btn").on("click", function () {
			window.location.href = "pincode_order_report_ajax.php?" + $.param(params({action: "export"}));
		});

		updatePeriodText();
		loadReport(1);
	})();
</script>
</body>
</html>
