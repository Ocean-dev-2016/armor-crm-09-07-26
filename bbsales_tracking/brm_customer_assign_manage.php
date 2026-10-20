<?php
/**
 * Admin: BRM1 / BRM2 customer assignment list + exchange.
 * Only updates executive.brm_admin_id — does NOT change sales person (seid) or other reports.
 */
$page_id = 678;
$page_slug = 'brm_customer_assign';
include("connect.php");
require_once dirname(__DIR__) . '/include/reorder_brm_helper.php';

$adminType = isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) ? (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] : -1;
if ($adminType !== 0) {
	$db->rp_location("dashboard.php");
	exit;
}

reorder_brm_ensure_customer_assign_column($db);
reorder_brm_auto_divide_customers($db);
$stats = reorder_brm_assign_stats($db);

$page_title = "BRM Customer Assign";
$page_hierarchy = array(
	array("link" => "", "title" => "Customer Reports"),
	array("link" => "brm_customer_assign_manage.php", "title" => $page_title),
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
	<?php include("include_css.php"); ?>
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
			<div class="row" style="margin-bottom:15px;">
				<div class="col-md-3">
					<div class="dashboard-stat red-intense">
						<div class="visual"><i class="fa fa-users"></i></div>
						<div class="details">
							<div class="number" id="stat_total"><?php echo (int) $stats['total']; ?></div>
							<div class="desc">Total Customers</div>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="dashboard-stat red-sunglo">
						<div class="visual"><i class="fa fa-user"></i></div>
						<div class="details">
							<div class="number" id="stat_brm1"><?php echo (int) $stats['brm1']; ?></div>
							<div class="desc">Assigned to BRM1</div>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="dashboard-stat red">
						<div class="visual"><i class="fa fa-user"></i></div>
						<div class="details">
							<div class="number" id="stat_brm2"><?php echo (int) $stats['brm2']; ?></div>
							<div class="desc">Assigned to BRM2</div>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="dashboard-stat grey">
						<div class="visual"><i class="fa fa-question"></i></div>
						<div class="details">
							<div class="number" id="stat_unassigned"><?php echo (int) $stats['unassigned']; ?></div>
							<div class="desc">Unassigned</div>
						</div>
					</div>
				</div>
			</div>

			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption"><i class="fa fa-exchange"></i> Customer → BRM Assignment</div>
				</div>
				<div class="portlet-body">
					<div class="row" style="margin-bottom:12px;">
						<div class="col-md-4">
							<input type="text" class="form-control" id="brm_search" placeholder="Search company / person / mobile / code">
						</div>
						<div class="col-md-3">
							<select class="form-control" id="brm_filter">
								<option value="">All Assignments</option>
								<option value="brm1">BRM1 only</option>
								<option value="brm2">BRM2 only</option>
								<option value="unassigned">Unassigned</option>
							</select>
						</div>
						<div class="col-md-2">
							<button type="button" class="btn btn-primary" id="brm_search_btn"><i class="fa fa-search"></i> Search</button>
						</div>
						<div class="col-md-3 text-right">
							<a href="reorder_followup_report.php" class="btn blue">Re-Order Report</a>
						</div>
					</div>
					<div id="brm_assign_results">
						<div class="text-center" style="padding:30px;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript">
var brm1Id = <?php echo (int) $stats['brm1_id']; ?>;
var brm2Id = <?php echo (int) $stats['brm2_id']; ?>;

function loadBrmAssign(page) {
	page = page || 1;
	$('#brm_assign_results').html('<div class="text-center" style="padding:30px;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
	$.get('brm_customer_assign_ajax.php', {
		action: 'list',
		page: page,
		search: $('#brm_search').val() || '',
		filter: $('#brm_filter').val() || ''
	}, function (html) {
		$('#brm_assign_results').html(html);
	});
}

function refreshBrmStats() {
	$.getJSON('brm_customer_assign_ajax.php', { action: 'stats' }, function (res) {
		if (!res || !res.ok) return;
		$('#stat_total').text(res.stats.total);
		$('#stat_brm1').text(res.stats.brm1);
		$('#stat_brm2').text(res.stats.brm2);
		$('#stat_unassigned').text(res.stats.unassigned);
	});
}

function assignBrm(customerId, brmAdminId) {
	if (!customerId) return;
	$.post('brm_customer_assign_ajax.php', {
		action: 'assign',
		customer_id: customerId,
		brm_admin_id: brmAdminId
	}, function (res) {
		if (res && res.ok) {
			if (typeof toastr !== 'undefined') {
				toastr.success(res.msg || 'Updated');
			}
			refreshBrmStats();
			loadBrmAssign($('#brm_page').val() || 1);
		} else {
			alert((res && res.msg) ? res.msg : 'Update failed');
		}
	}, 'json');
}

function updateBrmSelectedCount() {
	$('#brm_selected_count').text($('.brm-row-check:checked').length);
}

function bulkAssignBrm(brmAdminId) {
	var ids = [];
	$('.brm-row-check:checked').each(function () {
		ids.push($(this).val());
	});
	if (!ids.length) {
		alert('Please select at least one customer.');
		return;
	}
	var label = (parseInt(brmAdminId, 10) === parseInt(brm1Id, 10)) ? 'BRM1' : ((parseInt(brmAdminId, 10) === parseInt(brm2Id, 10)) ? 'BRM2' : 'Unassigned');
	if (!confirm('Assign ' + ids.length + ' selected customer(s) to ' + label + '?')) {
		return;
	}
	$.post('brm_customer_assign_ajax.php', {
		action: 'bulk_assign',
		brm_admin_id: brmAdminId,
		customer_ids: ids.join(',')
	}, function (res) {
		if (res && res.ok) {
			if (typeof toastr !== 'undefined') {
				toastr.success(res.msg || 'Updated');
			} else {
				alert(res.msg || 'Updated');
			}
			refreshBrmStats();
			loadBrmAssign($('#brm_page').val() || 1);
		} else {
			alert((res && res.msg) ? res.msg : 'Bulk assign failed');
		}
	}, 'json');
}

$(document).ready(function () {
	loadBrmAssign(1);
	$('#brm_search_btn').on('click', function () { loadBrmAssign(1); });
	$('#brm_search').on('keypress', function (e) {
		if (e.which === 13) { loadBrmAssign(1); }
	});
	$('#brm_filter').on('change', function () { loadBrmAssign(1); });
	$(document).on('click', '.brm-page-link', function (e) {
		e.preventDefault();
		loadBrmAssign($(this).data('page'));
	});
	$(document).on('change', '.brm-assign-select', function () {
		var cid = $(this).data('cid');
		var val = $(this).val();
		assignBrm(cid, val);
	});
	$(document).on('change', '#brm_check_all', function () {
		$('.brm-row-check').prop('checked', $(this).is(':checked'));
		updateBrmSelectedCount();
	});
	$(document).on('change', '.brm-row-check', function () {
		updateBrmSelectedCount();
	});
	$(document).on('click', '#brm_bulk_brm1', function () {
		bulkAssignBrm(brm1Id);
	});
	$(document).on('click', '#brm_bulk_brm2', function () {
		bulkAssignBrm(brm2Id);
	});
	$(document).on('click', '#brm_bulk_clear', function () {
		$('#brm_check_all').prop('checked', false);
		$('.brm-row-check').prop('checked', false);
		updateBrmSelectedCount();
	});
});
</script>
</body>
</html>
