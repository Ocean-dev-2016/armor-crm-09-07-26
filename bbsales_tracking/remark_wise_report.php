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
		.rar-hr-header-table th { background:#f8f8f8; font-size:12px; }
		.rar-hr-header-table td { font-size:12px; }
		.rar-hr-product-table th, .rar-hr-product-table td { font-size:11px; }
		.rar-hr-payment-section .rar-pay-opt.active { font-weight:700; color:#1a7a3a; }
		#rarFormModalFooter .btn-print-hr { background:#3598dc; color:#fff; }
		#rarFormModalFooter .btn-share-hr { background:#25d366; color:#fff; }
		body.rar-modal-open {
			overflow: hidden !important;
		}
		#rarFormModal {
			overflow: hidden !important;
			padding-right: 0 !important;
		}
		#rarFormModal.rar-hr-full-modal .rar-form-modal-dialog {
			width: 94%;
			max-width: 1150px;
			margin: 12px auto;
			height: auto;
			max-height: calc(100vh - 24px);
		}
		#rarFormModal.rar-hr-full-modal .modal-content {
			height: auto;
			max-height: calc(100vh - 24px);
			display: flex;
			flex-direction: column;
			border-radius: 6px;
			overflow: hidden;
			box-shadow: 0 10px 40px rgba(0,0,0,0.28);
		}
		#rarFormModal.rar-hr-full-modal .modal-header {
			flex: 0 0 auto;
			border-radius: 0;
			padding: 12px 18px;
		}
		#rarFormModal.rar-hr-full-modal .modal-footer {
			flex: 0 0 auto;
			border-radius: 0;
			padding: 10px 18px;
		}
		#rarFormModal.rar-hr-full-modal .modal-body {
			flex: 0 1 auto;
			overflow: visible !important;
			max-height: none !important;
			padding: 10px 16px 12px;
		}
		#rarFormModal.rar-hr-full-modal .rar-hr-info-panel {
			padding: 10px 12px;
			margin-bottom: 8px;
		}
		#rarFormModal.rar-hr-full-modal .rar-hr-info-grid {
			margin-bottom: 8px;
			gap: 8px;
		}
		#rarFormModal.rar-hr-full-modal .rar-hr-info-item {
			padding: 6px 10px;
		}
		#rarFormModal.rar-hr-full-modal .rar-hr-customer-row {
			margin-bottom: 8px;
			padding-bottom: 8px;
		}
		#rarFormModal.rar-hr-full-modal .rar-hr-company {
			font-size: 15px;
		}
		#rarFormModal.rar-hr-full-modal .rar-hr-product-table {
			margin-bottom: 8px !important;
		}
		#rarFormModal.rar-hr-full-modal .rar-hr-product-table th,
		#rarFormModal.rar-hr-full-modal .rar-hr-product-table td {
			font-size: 10px !important;
			padding: 3px 5px !important;
			line-height: 1.2;
			vertical-align: middle;
		}
		#rarFormModal.rar-hr-full-modal .rar-hr-payment-section {
			margin-bottom: 0 !important;
			padding: 8px 12px !important;
		}
		#rarFormModal.rar-hr-full-modal .rar-hr-modal-fit {
			transform-origin: top center;
		}
		#rarFormModal.rar-hr-full-modal .rar-visit-form-block,
		#rarFormModal.rar-hr-full-modal .rar-form-section {
			margin-bottom: 0;
		}
		.rar-hr-info-panel {
			background: linear-gradient(135deg, #f8fbff 0%, #f4f8fc 100%);
			border: 1px solid #c5d9ea;
			border-left: 4px solid #3598dc;
			border-radius: 6px;
			padding: 14px 16px;
			margin-bottom: 14px;
		}
		.rar-hr-info-grid {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
			margin-bottom: 12px;
		}
		.rar-hr-info-item {
			flex: 1 1 160px;
			background: #fff;
			border: 1px solid #dde8f2;
			border-radius: 5px;
			padding: 8px 12px;
			min-width: 140px;
		}
		.rar-hr-info-item .rar-hr-label {
			display: block;
			font-size: 10px;
			text-transform: uppercase;
			letter-spacing: 0.4px;
			color: #7a8a99;
			font-weight: 600;
			margin-bottom: 3px;
		}
		.rar-hr-info-item .rar-hr-value {
			display: block;
			font-size: 13px;
			font-weight: 700;
			color: #2c3e50;
			line-height: 1.3;
		}
		.rar-hr-customer-row {
			display: flex;
			flex-wrap: wrap;
			align-items: center;
			gap: 10px;
			margin-bottom: 10px;
			padding-bottom: 10px;
			border-bottom: 1px dashed #d0dde8;
		}
		.rar-hr-code {
			display: inline-block;
			background: #1a7a3a;
			color: #fff;
			font-size: 12px;
			font-weight: 700;
			padding: 4px 10px;
			border-radius: 4px;
			letter-spacing: 0.3px;
		}
		.rar-hr-company {
			font-size: 16px;
			font-weight: 700;
			color: #1a3a5c;
		}
		.rar-hr-bottom-row {
			display: flex;
			flex-wrap: wrap;
			gap: 12px;
		}
		.rar-hr-addr-block {
			flex: 1 1 60%;
			min-width: 220px;
		}
		.rar-hr-addr-block .rar-hr-label,
		.rar-hr-turn-block .rar-hr-label {
			display: block;
			font-size: 10px;
			text-transform: uppercase;
			color: #7a8a99;
			font-weight: 600;
			margin-bottom: 3px;
		}
		.rar-hr-addr-block .rar-hr-value {
			font-size: 12px;
			color: #444;
			line-height: 1.45;
		}
		.rar-hr-turn-block {
			flex: 1 1 200px;
			background: #fff8e6;
			border: 1px solid #f0d9a8;
			border-radius: 5px;
			padding: 8px 12px;
		}
		.rar-hr-turn-block .rar-hr-value {
			font-size: 12px;
			font-weight: 600;
			color: #8a5a00;
		}
		.rar-highrate-block .rar-hr-product-table {
			margin-bottom: 12px;
		}
		.rar-highrate-block .rar-hr-product-table thead th {
			background: #eef4fa !important;
			color: #2c5282;
			font-size: 11px;
			font-weight: 700;
			border-color: #c5d5e5 !important;
		}
		.rar-hr-payment-section {
			border: 1px solid #d5e3ef !important;
			border-radius: 6px !important;
			padding: 12px 14px !important;
			background: #f9fcff !important;
		}
		.rar-form-section {
			margin-bottom: 10px;
		}
		.rar-form-section-title {
			font-size: 13px;
			font-weight: 700;
			margin-bottom: 8px;
			padding-bottom: 6px;
			border-bottom: 2px solid #ddd;
		}
		.rar-consultant-title {
			color: #1a7a3a;
			border-bottom-color: #1a7a3a;
		}
		.rar-highrate-title {
			color: #c85a12;
			border-bottom-color: #c85a12;
		}
		.rar-form-detail-table th {
			background: #f8f8f8 !important;
			font-size: 11px !important;
			width: 140px;
		}
		.rar-form-detail-table td {
			font-size: 11px !important;
		}
		#rarFormModal .rar-form-detail-table th,
		#rarFormModal .rar-form-detail-table td {
			padding: 4px 6px !important;
			line-height: 1.25;
		}
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
							<?php if ((isset($rights['export_excel_flag']) && (int) $rights['export_excel_flag'] === 1) || (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0)) { ?>
								<button type="button" class="btn green-haze" id="rar_excel_btn"><i class="fa fa-file-excel-o"></i> Export Excel</button>
							<?php } ?>
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

<div class="modal fade" id="rarFormModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog rar-form-modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background:#3598dc;color:#fff;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color:#fff;opacity:1;">&times;</button>
				<h4 class="modal-title" id="rarFormModalTitle">Form Detail</h4>
			</div>
			<div class="modal-body" id="rarFormModalBody">
				<div class="text-center text-muted">Loading...</div>
			</div>
			<div class="modal-footer" id="rarFormModalFooter">
				<button type="button" class="btn btn-print-hr" id="rarBtnPrintHr" style="display:none;"><i class="fa fa-print"></i> Print</button>
				<button type="button" class="btn btn-share-hr" id="rarBtnShareHr" style="display:none;"><i class="fa fa-share-alt"></i> Share</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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

	$("#rar_excel_btn").on("click", function () {
		var employees = $("#rar_employee_ids").val() || [];
		var customers = $("#rar_customer_ids").val() || [];
		$.ajax({
			method: "POST",
			url: "remark_wise_report_excel.php",
			data: {
				from_date: $("#rar_from_date").val(),
				to_date: $("#rar_to_date").val(),
				employee_ids: employees.join(","),
				customer_ids: customers.join(","),
				remark_code: $("#rar_remark_code").val()
			},
			dataType: "text",
			timeout: 600000,
			beforeSend: function () {
				$(".loading-div").show();
				if (!$("#rar_export_status").length) {
					$(".loading-div").append('<div id="rar_export_status" style="margin-top:10px;font-weight:600;"></div>');
				}
				$("#rar_export_status").text("Exporting Excel (sheet-wise forms)… please wait.");
			},
			success: function (raw) {
				$(".loading-div").hide();
				$("#rar_export_status").text("");
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
				$("#rar_export_status").text("");
				var msg = "Excel download failed";
				if (status === "timeout") {
					msg = "Excel export timed out. Please try a smaller date range.";
				} else if (xhr && xhr.responseText) {
					try {
						var parsed = $.parseJSON(xhr.responseText);
						if (parsed && parsed.ack_msg) {
							msg = parsed.ack_msg;
						}
					} catch (e) {}
				}
				alert(msg);
			}
		});
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

	var currentFormVisitId = 0;
	var currentHasConsultant = false;
	var currentHasHighRate = false;

	$("#rar-results").on("click", ".rar-view-form-btn", function () {
		var visitId = $(this).data("visit-id");
		var title = $(this).data("title") || "Form Detail";
		var hasConsultant = $(this).data("has-consultant") == 1;
		var hasHighRate = $(this).data("has-high-rate") == 1;
		var hasFormModal = $(this).data("has-form-modal") == 1;
		var content = $("#rar-form-" + visitId).html() || "<div class='alert alert-warning'>Form data not found.</div>";
		currentFormVisitId = visitId;
		currentHasConsultant = hasConsultant;
		currentHasHighRate = hasHighRate;
		$("#rarFormModalTitle").text(title + " (Visit #" + visitId + ")");
		$("#rarFormModalBody").html(content);
		if (hasConsultant || hasHighRate) {
			$("#rarBtnPrintHr, #rarBtnShareHr").show();
		} else {
			$("#rarBtnPrintHr, #rarBtnShareHr").hide();
		}
		if (hasFormModal) {
			$("#rarFormModal").addClass("rar-hr-full-modal");
		} else {
			$("#rarFormModal").removeClass("rar-hr-full-modal");
		}
		$("#rarFormModal").modal("show");
	});

	$("#rarFormModal").on("show.bs.modal", function () {
		$("body").addClass("rar-modal-open");
	});
	$("#rarFormModal").on("shown.bs.modal", function () {
		rarFitHighRateModal();
	});
	$("#rarFormModal").on("hidden.bs.modal", function () {
		$("body").removeClass("rar-modal-open");
		$(this).removeClass("rar-hr-full-modal");
		$(this).find(".rar-hr-modal-fit").css({ transform: "none", width: "100%", marginBottom: 0 });
	});

	function rarFitHighRateModal() {
		var $modal = $("#rarFormModal");
		if (!$modal.hasClass("rar-hr-full-modal")) {
			return;
		}
		var $dialog = $modal.find(".rar-form-modal-dialog");
		var $content = $modal.find(".modal-content");
		var $body = $modal.find(".modal-body");
		var $fit = $modal.find(".rar-hr-modal-fit");
		if (!$fit.length) {
			return;
		}

		$fit.css({ transform: "none", width: "100%", marginBottom: 0 });
		$dialog.css({ height: "auto" });
		$content.css({ height: "auto" });
		$body.css({ overflow: "visible", maxHeight: "none" });

		var maxH = $(window).height() - 24;
		var headerH = $modal.find(".modal-header").outerHeight(true) || 0;
		var footerH = $modal.find(".modal-footer").outerHeight(true) || 0;
		var bodyPad = (parseInt($body.css("padding-top"), 10) || 0) + (parseInt($body.css("padding-bottom"), 10) || 0);
		var available = maxH - headerH - footerH - bodyPad - 4;
		var needed = $fit[0].scrollHeight;

		if (needed > available && available > 100) {
			var scale = Math.max(0.72, available / needed);
			$fit.css({
				transform: "scale(" + scale + ")",
				width: (100 / scale) + "%",
				marginBottom: "-" + Math.round((1 - scale) * needed * 0.42) + "px"
			});
		}
	}

	$(window).on("resize", function () {
		if ($("#rarFormModal").hasClass("in")) {
			rarFitHighRateModal();
		}
	});

	$("#rarBtnPrintHr").on("click", function () {
		if (!currentFormVisitId) {
			return;
		}
		var printUrl = "remark_wise_high_rate_print.php?visit_id=" + currentFormVisitId;
		if (currentHasConsultant) {
			printUrl = "remark_wise_consultant_print.php?visit_id=" + currentFormVisitId;
		}
		window.open(printUrl, "_blank", "width=820,height=700");
	});

	$("#rarBtnShareHr").on("click", function () {
		if (!currentFormVisitId) {
			return;
		}
		var shareTitle = $.trim($("#rarFormModalTitle").text()) || "Visit Form";
		var text = rarBuildHighRateShareText($("#rarFormModalBody"));
		if (navigator.share) {
			navigator.share({ title: shareTitle, text: text }).catch(function () {});
			return;
		}
		window.open("https://wa.me/?text=" + encodeURIComponent(text), "_blank");
	});

	function rarBuildHighRateShareText($wrap) {
		var lines = [];
		lines.push("VISIT FORM DETAILS");
		$wrap.find(".rar-hr-info-item").each(function () {
			var label = $.trim($(this).find(".rar-hr-label").text());
			var val = $.trim($(this).find(".rar-hr-value").text());
			if (label && val) {
				lines.push(label + ": " + val);
			}
		});
		var code = $.trim($wrap.find(".rar-hr-code").text());
		var company = $.trim($wrap.find(".rar-hr-company").text());
		if (code || company) {
			lines.push("Client: " + code + " | " + company);
		}
		var addr = $.trim($wrap.find(".rar-hr-addr-block .rar-hr-value").text());
		var turn = $.trim($wrap.find(".rar-hr-turn-block .rar-hr-value").text());
		if (addr) lines.push("Address: " + addr);
		if (turn) lines.push("Turnover: " + turn);

		var $consultant = $wrap.find(".rar-consultant-block");
		if ($consultant.length) {
			lines.push("");
			lines.push("CONSULTANT FORM:");
			$consultant.find(".rar-form-detail-table tr").each(function () {
				var label = $.trim($(this).find("th").text());
				var val = $.trim($(this).find("td").text());
				if (label && val) lines.push(label + ": " + val);
			});
		}

		var $products = $wrap.find(".rar-hr-product-table tbody tr");
		if ($products.length) {
			lines.push("");
			lines.push("HIGH RATE PRODUCTS:");
			$products.each(function () {
				var tds = $(this).find("td");
				if (tds.length >= 4) {
					lines.push("- " + $.trim($(tds[0]).text()) + " | Given: " + $.trim($(tds[1]).text()) + " | Qty: " + $.trim($(tds[2]).text()) + " | Cust.Rate: " + $.trim($(tds[3]).text()));
				}
			});
		}
		var pay = [];
		$wrap.find(".rar-hr-payment-options .rar-pay-opt.active").each(function () {
			pay.push($.trim($(this).text()));
		});
		if (pay.length) {
			lines.push("");
			lines.push("Payment Condition: " + pay.join(", "));
		}
		return lines.join("\n");
	}

	loadReport(1);
})();
</script>
</body>
</html>
