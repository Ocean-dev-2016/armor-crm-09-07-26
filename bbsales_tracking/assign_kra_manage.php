<?php
$page_id = 672;
$page_slug = 'assign_kra';
$main_page = 'Assign KRA';
$page_title = 'Assign KRA';
$page_hierarchy = array(
	array('link' => '', 'title' => 'Sales Team Reports'),
	array('link' => 'assign_kra_manage.php', 'title' => $page_title),
);
include('connect.php');

$filter_customer_state = isset($_REQUEST['customer_state']) ? $db->clean($_REQUEST['customer_state']) : '';

$customer_where = "isDelete=0 AND isActive=1 AND company_name!='' AND company_name IS NOT NULL";
if ($filter_customer_state !== '') {
	$customer_where .= " AND state='" . $filter_customer_state . "'";
}
$customer_r = $db->rp_getData('executive', 'id, company_name, state, client_code, cname', $customer_where, 'company_name ASC', 0);

$state_r = $db->rp_getData('class', 'name', 'isDelete=0', 'name ASC', 0);
$sales_r = $db->rp_getData(
	'sales_executive',
	'id, name, state',
	"isDelete=0 AND isActive=1 AND type IN ('sales_executive','sales_officer','area_sales_manager','area_manager','service_executive')",
	'name ASC',
	0
);

$customer_count = $customer_r ? mysqli_num_rows($customer_r) : 0;
$states_list = array();
if ($state_r) {
	while ($st = mysqli_fetch_assoc($state_r)) {
		$states_list[] = $st['name'];
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
	<?php include('include_css.php'); ?>
	<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
	<style>
		.assign-kra-wrap { padding: 15px 20px 5px; }
		.assign-kra-row {
			display: flex;
			flex-wrap: wrap;
			align-items: flex-end;
			margin: 0 -10px;
		}
		.assign-kra-col {
			padding: 0 10px 15px;
			box-sizing: border-box;
		}
		.assign-kra-col.state-col { width: 18%; min-width: 150px; flex: 0 0 18%; }
		.assign-kra-col.customer-col { width: 36%; min-width: 260px; flex: 1 1 36%; max-width: 42%; }
		.assign-kra-col.sales-col { width: 28%; min-width: 200px; flex: 0 0 28%; }
		.assign-kra-col.action-col { width: 12%; min-width: 110px; flex: 0 0 12%; }
		.assign-kra-col label {
			display: block;
			font-weight: 600;
			margin-bottom: 6px;
			color: #333;
		}
		.assign-kra-col .fs-wrap,
		.assign-kra-col .select2-container {
			width: 100% !important;
			max-width: 100%;
			position: relative;
			box-sizing: border-box;
		}
		.assign-kra-col .fs-label-wrap {
			min-height: 34px;
			line-height: 22px;
			border: 1px solid #e5e5e5;
			background: #fff;
			box-sizing: border-box;
		}
		.assign-kra-col.customer-col .fs-dropdown {
			left: 0 !important;
			right: auto !important;
			width: 100% !important;
			min-width: 100% !important;
			max-width: 100% !important;
			box-sizing: border-box;
			z-index: 1050;
		}
		.assign-kra-col.customer-col .fs-option {
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		.assign-kra-col.customer-col .fs-option .fs-label {
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			display: block;
			line-height: 1.35;
		}
		.assign-kra-col .select2-container .select2-choice {
			height: 34px;
			line-height: 32px;
		}
		.assign-kra-col .form-control {
			height: 34px;
		}
		.assign-kra-col .btn-assign {
			width: 100%;
			height: 34px;
			padding: 6px 12px;
		}
		.kra-badge {
			font-size: 11px;
			font-weight: normal;
			background: #3598dc;
			color: #fff;
			padding: 2px 8px;
			border-radius: 10px;
			margin-left: 6px;
			vertical-align: middle;
		}
		.filter-row {
			display: flex;
			flex-wrap: wrap;
			align-items: flex-end;
			margin: 0 -8px 10px;
		}
		.filter-row .filter-col {
			padding: 0 8px 10px;
			flex: 1;
			min-width: 180px;
		}
		.filter-row .filter-col.actions {
			flex: 0 0 auto;
			min-width: 160px;
		}
		.filter-row label {
			display: block;
			font-weight: 600;
			margin-bottom: 5px;
			font-size: 13px;
		}
		#kra_results .table thead th {
			background: #f5f5f5;
			font-size: 13px;
			white-space: nowrap;
		}
		#kra_results .table tbody td {
			font-size: 13px;
			vertical-align: middle;
		}
		.kra-total-bar {
			margin-bottom: 12px;
		}
		#assign_sp_summary {
			display: none;
			margin-top: 8px;
			border: 1px solid #d9e2ea;
			background: #fff;
		}
		#assign_sp_summary .sp-summary-head {
			background: #2f6f44;
			color: #fff;
			padding: 10px 14px;
			font-size: 15px;
			font-weight: 700;
		}
		#assign_sp_summary .sp-summary-head small {
			display: block;
			font-size: 12px;
			font-weight: 400;
			opacity: 0.92;
			margin-top: 2px;
		}
		#assign_sp_summary .sp-summary-kpis {
			display: flex;
			flex-wrap: wrap;
			gap: 8px;
			padding: 10px;
			background: #f5f7f9;
		}
		#assign_sp_summary .sp-kpi {
			min-width: 160px;
			flex: 1;
			border: 1px solid #dce3e8;
			background: #fff;
			padding: 8px 10px;
		}
		#assign_sp_summary .sp-kpi-label {
			color: #63717c;
			font-size: 11px;
			text-transform: uppercase;
			letter-spacing: 0.3px;
		}
		#assign_sp_summary .sp-kpi-value {
			font-size: 16px;
			font-weight: 700;
			margin-top: 3px;
			color: #2c3e50;
		}
		#assign_sp_summary .sp-kpi-sub {
			font-size: 12px;
			color: #666;
			margin-top: 2px;
		}
		@media (max-width: 991px) {
			.assign-kra-col.state-col,
			.assign-kra-col.customer-col,
			.assign-kra-col.sales-col,
			.assign-kra-col.action-col {
				width: 100%;
				min-width: 0;
				max-width: 100%;
				flex: 1 1 100%;
			}
		}
	</style>
</head>
<body class="page-md">
<?php include('header.php'); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1>
					<a href="dashboard.php" class="primary"><i class="fa fa-arrow-circle-o-left" style="font-size:22px!important;"></i></a>
					&nbsp;<?php $db->pageBar($page_hierarchy); ?>
				</h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<?php $db->printErrorMessage(); ?>
					<?php $db->printSuccessMessage(); ?>
				</div>
			</div>

			<!-- Assign Form -->
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption"><i class="fa fa-filter"></i> Assign Customer to Sales Person</div>
					<div class="tools"><a href="javascript:;" class="collapse"></a></div>
				</div>
				<div class="portlet-body">
					<div class="alert alert-info" style="margin-bottom:15px;padding:10px 15px;">
						<i class="fa fa-info-circle"></i>
						Customer: <strong>Code - Customer Name - State</strong> &nbsp;|&nbsp;
						Sales Person: <strong>Name - State</strong> &nbsp;|&nbsp;
						Sales person list filters by selected customer state &nbsp;|&nbsp;
						Assigned customers appear in mobile app
					</div>

					<div class="assign-kra-wrap">
						<div class="assign-kra-row">
							<div class="assign-kra-col state-col">
								<label>Filter By State</label>
								<select class="form-control noSelect2" id="customer_state_filter">
									<option value="">All States</option>
									<?php foreach ($states_list as $stName) {
										$sel = ($filter_customer_state === $stName) ? 'selected' : '';
										echo '<option value="' . htmlspecialchars($stName) . '" ' . $sel . '>' . htmlspecialchars($stName) . '</option>';
									} ?>
								</select>
							</div>

							<div class="assign-kra-col customer-col">
								<label>
									Select Customer(s) <span class="text-danger">*</span>
									<span class="kra-badge"><?php echo number_format($customer_count); ?></span>
								</label>
								<select class="form-control noSelect2" id="customer_ids" name="customer_ids[]" multiple="multiple">
									<?php
									if ($customer_r) {
										while ($c = mysqli_fetch_assoc($customer_r)) {
											$firm = trim($c['company_name']);
											$state = trim($c['state']);
											$code = trim($c['client_code']);
											$parts = array();
											if ($code !== '') {
												$parts[] = $code;
											}
											if ($firm !== '') {
												$parts[] = $firm;
											}
											if ($state !== '') {
												$parts[] = $state;
											}
											$label = implode(' - ', $parts);
											echo '<option value="' . (int) $c['id'] . '" data-state="' . htmlspecialchars($state, ENT_QUOTES) . '" data-client-code="' . htmlspecialchars($code, ENT_QUOTES) . '" title="' . htmlspecialchars($label, ENT_QUOTES) . '">'
												. htmlspecialchars($label) . '</option>';
										}
									}
									?>
								</select>
							</div>

							<div class="assign-kra-col sales-col">
								<label>Select Sales Person <span class="text-danger">*</span></label>
								<select class="form-control noSelect2" id="sales_person_id">
									<option value="">Select Sales Person</option>
								</select>
							</div>

							<div class="assign-kra-col action-col">
								<label>&nbsp;</label>
								<button type="button" class="btn btn-success btn-assign" id="btn_assign_kra">
									<i class="fa fa-check"></i> Assign
								</button>
							</div>
						</div>

						<div id="assign_sp_summary">
							<div class="sp-summary-head">
								<span id="sp_summary_name">KEY RESULT AREA</span>
								<small id="sp_summary_meta"></small>
							</div>
							<div class="sp-summary-kpis">
								<div class="sp-kpi">
									<div class="sp-kpi-label">KRA Assigned</div>
									<div class="sp-kpi-value" id="sp_kpi_assigned">0</div>
								</div>
								<div class="sp-kpi">
									<div class="sp-kpi-label">Total Quotation</div>
									<div class="sp-kpi-value" id="sp_kpi_quotation_count">0</div>
									<div class="sp-kpi-sub" id="sp_kpi_quotation_value"><?php echo CURR; ?> 0.00</div>
								</div>
								<div class="sp-kpi">
									<div class="sp-kpi-label">Total PI Approved</div>
									<div class="sp-kpi-value" id="sp_kpi_pi_count">0</div>
									<div class="sp-kpi-sub" id="sp_kpi_pi_value"><?php echo CURR; ?> 0.00</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Assigned List -->
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption"><i class="fa fa-list"></i> Assigned Customers List</div>
					<div class="tools"><a href="javascript:;" class="collapse"></a></div>
				</div>
				<div class="portlet-body">
					<div class="filter-row">
						<div class="filter-col">
							<label>Search</label>
							<input type="text" class="form-control input-sm" id="searchName" placeholder="Firm / Person / Code / Phone">
						</div>
						<div class="filter-col">
							<label>State</label>
							<select class="form-control input-sm noSelect2" id="filter_state">
								<option value="">All States</option>
								<?php foreach ($states_list as $stName) {
									echo '<option value="' . htmlspecialchars($stName) . '">' . htmlspecialchars($stName) . '</option>';
								} ?>
							</select>
						</div>
						<div class="filter-col">
							<label>Sales Person</label>
							<select class="form-control input-sm noSelect2" id="filter_seid">
								<option value="">All Sales Persons</option>
								<?php
								if ($sales_r) {
									while ($sp = mysqli_fetch_assoc($sales_r)) {
										$lbl = $sp['name'] . ($sp['state'] !== '' ? ' - ' . $sp['state'] : '');
										echo '<option value="' . (int) $sp['id'] . '">' . htmlspecialchars($lbl) . '</option>';
									}
								}
								?>
							</select>
						</div>
						<div class="filter-col actions">
							<label>&nbsp;</label>
							<button type="button" class="btn btn-primary btn-sm" id="btn_filter"><i class="fa fa-search"></i> Search</button>
							<button type="button" class="btn btn-default btn-sm" id="btn_clear"><i class="fa fa-times"></i> Clear</button>
						</div>
					</div>

					<div class="loading-div" style="display:none;text-align:center;padding:30px;">
						<img src="assets/admin/layout/img/ajax-loader.gif" alt="Loading">
					</div>
					<div id="kra_results"></div>
				</div>
			</div>

		</div>
	</div>
</div>
<?php include('include_js.php'); ?>
<script type="text/javascript" src="js/fSelect.js"></script>
<script>
var kraPage = 1;
var kraShow = 25;

function initAssignKraSelects() {
	var $customer = $('#customer_ids');
	if ($customer.closest('.fs-wrap').length) {
		$customer.fSelect('destroy');
	}
	['#sales_person_id', '#filter_state', '#filter_seid', '#customer_state_filter'].forEach(function(sel) {
		if ($(sel).data('select2')) {
			$(sel).select2('destroy');
		}
	});

	$('#customer_state_filter').select2({ allowClear: true, width: '100%' });
	$('#filter_state, #filter_seid').select2({ allowClear: true, width: '100%' });

	$customer.fSelect({
		placeholder: 'Search and select customer(s)',
		showSearch: true,
		numDisplayed: 2,
		overflowText: '{n} customers selected'
	});

	$('#sales_person_id').select2({
		allowClear: true,
		width: '100%',
		placeholder: 'Select Sales Person'
	});
}

function loadAssignedGrid(page) {
	kraPage = page || 1;
	$('.loading-div').show();
	$('#kra_results').html('');
	$.ajax({
		url: 'assign_kra_get_ajax.php',
		type: 'GET',
		data: {
			searchName: $('#searchName').val(),
			filter_state: $('#filter_state').val(),
			filter_seid: $('#filter_seid').val(),
			page: kraPage,
			show: kraShow
		},
		success: function(html) {
			$('.loading-div').hide();
			$('#kra_results').html(html);
			$('#kra_results .pagination a').off('click').on('click', function(e) {
				e.preventDefault();
				var p = $(this).data('page');
				if (p) {
					loadAssignedGrid(p);
				}
			});
		},
		error: function() {
			$('.loading-div').hide();
			$('#kra_results').html('<div class="alert alert-danger">Failed to load assigned customers.</div>');
		}
	});
}

function getSelectedCustomerStates() {
	var states = [];
	$('#customer_ids option:selected').each(function() {
		var st = $.trim($(this).attr('data-state') || '');
		if (st !== '' && $.inArray(st, states) === -1) {
			states.push(st);
		}
	});
	return states;
}

function loadSalesPersons(states) {
	var $sp = $('#sales_person_id');
	hideSalesPersonSummary();
	$sp.prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');
	$.ajax({
		url: 'assign_kra_ajax.php',
		type: 'POST',
		dataType: 'json',
		data: { mode: 'get_sales_persons', states: states.join(',') },
		success: function(res) {
			var html = '<option value="">Select Sales Person</option>';
			if (res && res.ack == 1 && res.results && res.results.length) {
				$.each(res.results, function(i, row) {
					html += '<option value="' + row.id + '">' + $('<div>').text(row.text).html() + '</option>';
				});
			} else {
				html = '<option value="">No sales person for this state</option>';
			}
			$sp.html(html).prop('disabled', false).trigger('change');
		},
		error: function() {
			$sp.html('<option value="">Select Sales Person</option>').prop('disabled', false).trigger('change');
			toastr.error('Failed to load sales persons.');
		}
	});
}

function refreshSalesPersonsByCustomers() {
	var states = getSelectedCustomerStates();
	if (!states.length) {
		$('#sales_person_id').html('<option value="">Select Sales Person</option>').trigger('change');
		return;
	}
	loadSalesPersons(states);
}

function clearCustomerSelection() {
	$('#customer_ids').val([]);
	$('#customer_ids').closest('.fs-wrap').find('.fs-option.selected').removeClass('selected');
	$('#customer_ids').fSelect('reloadDropdownLabel');
}

function hideSalesPersonSummary() {
	$('#assign_sp_summary').hide();
}

function loadSalesPersonSummary(spId) {
	if (!spId) {
		hideSalesPersonSummary();
		return;
	}
	$.ajax({
		url: 'assign_kra_ajax.php',
		type: 'POST',
		dataType: 'json',
		data: { mode: 'get_sales_summary', sales_person_id: spId },
		success: function(res) {
			if (!res || res.ack != 1) {
				hideSalesPersonSummary();
				return;
			}
			$('#sp_summary_name').text('KEY RESULT AREA - ' + String(res.name || '').toUpperCase());
			var metaParts = [];
			if (res.phone) metaParts.push(res.phone);
			if (res.type_label) metaParts.push(res.type_label);
			if (res.state) metaParts.push(res.state);
			$('#sp_summary_meta').text(metaParts.join(' | '));
			$('#sp_kpi_assigned').text(res.kra_assigned || 0);
			$('#sp_kpi_quotation_count').text((res.quotation_count || 0) + ' Nos');
			$('#sp_kpi_quotation_value').html(res.quotation_value_label || '');
			$('#sp_kpi_pi_count').text((res.pi_count || 0) + ' Nos');
			$('#sp_kpi_pi_value').html(res.pi_value_label || '');
			$('#assign_sp_summary').show();
		},
		error: function() {
			hideSalesPersonSummary();
		}
	});
}

jQuery(document).ready(function() {
	setTimeout(initAssignKraSelects, 150);

	$('#customer_state_filter').on('change', function() {
		var st = $(this).val();
		window.location.href = st ? ('assign_kra_manage.php?customer_state=' + encodeURIComponent(st)) : 'assign_kra_manage.php';
	});

	$(document).on('click', '.assign-kra-col.customer-col .fs-option', function() {
		setTimeout(refreshSalesPersonsByCustomers, 80);
	});

	$('#sales_person_id').on('change', function() {
		loadSalesPersonSummary($(this).val());
	});

	$('#btn_assign_kra').on('click', function() {
		var ids = $('#customer_ids').val();
		var spId = $('#sales_person_id').val();
		if (!ids || !ids.length) {
			toastr.error('Please select at least one customer.');
			return;
		}
		if (!spId) {
			toastr.error('Please select sales person.');
			return;
		}
		if (!confirm('Assign ' + ids.length + ' customer(s) to selected sales person?')) {
			return;
		}
		var $btn = $(this).prop('disabled', true);
		$.ajax({
			url: 'assign_kra_ajax.php',
			type: 'POST',
			dataType: 'json',
			data: { mode: 'assign', customer_ids: ids.join(','), sales_person_id: spId },
			success: function(res) {
				$btn.prop('disabled', false);
				if (res && res.ack == 1) {
					toastr.success(res.ack_msg);
					clearCustomerSelection();
					loadSalesPersonSummary(spId);
					loadAssignedGrid(1);
				} else {
					toastr.error((res && res.ack_msg) ? res.ack_msg : 'Assignment failed.');
				}
			},
			error: function() {
				$btn.prop('disabled', false);
				toastr.error('Assignment request failed.');
			}
		});
	});

	$('#btn_filter').on('click', function() { loadAssignedGrid(1); });
	$('#btn_clear').on('click', function() {
		$('#searchName').val('');
		$('#filter_state').select2('val', '');
		$('#filter_seid').select2('val', '');
		loadAssignedGrid(1);
	});
	$('#searchName').on('keypress', function(e) {
		if (e.which === 13) { loadAssignedGrid(1); return false; }
	});

	loadAssignedGrid(1);
});
</script>
</body>
</html>
