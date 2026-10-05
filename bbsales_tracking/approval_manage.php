<?php
$page_id = 674;
$page_slug = 'approval_entry';
$ctable = 'approval_entry';
$ctable1 = 'Approval';
$main_page = 'approval_entry';
$page = 'manage_approval_entry';
$page_title = 'Manage Approval';
$page_hierarchy = array(
	array('link' => '', 'title' => 'Approval'),
	array('link' => 'approval_manage.php', 'title' => $page_title),
);
include('connect.php');
require_once('../include/approval_entry_helper.php');
armor_approval_require_unlock(false);
armor_approval_entry_ensure_table($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include('include_css.php'); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<style type="text/css">
#approval_grid_wrap {
	width: 100%;
	max-width: 100%;
	overflow-x: auto;
	overflow-y: visible;
	-webkit-overflow-scrolling: touch;
}
.portlet.light > .portlet-body {
	overflow: hidden;
}
#approval_grid_wrap.dropdown-open {
	overflow: visible;
}
.portlet.light.dropdown-open > .portlet-body {
	overflow: visible;
}
#results table.dataTable {
	width: 100% !important;
	margin: 0 !important;
}
#results table th,
#results table td {
	white-space: nowrap;
	vertical-align: middle;
}
a.approval-customer-visit {
	color: #3598dc;
	font-weight: 600;
	text-decoration: underline;
	cursor: pointer;
}
a.approval-customer-visit:hover {
	color: #1a6fa8;
}
.approval-badge {
	display: inline-block;
	min-width: 70px;
	padding: 3px 8px;
	border-radius: 10px;
	font-size: 11px;
	font-weight: 600;
	text-align: center;
	color: #fff;
}
.approval-badge-yes { background: #26a69a; }
.approval-badge-no { background: #e7505a; }
.approval-badge-pending { background: #f3c200; color: #333; }

/* Visit popup */
#approvalVisitModal .modal-dialog {
	width: 92%;
	max-width: 1100px;
	margin: 30px auto;
}
#approvalVisitModal .modal-header {
	background: #3598dc;
	color: #fff;
	border-radius: 0;
	padding: 12px 18px;
}
#approvalVisitModal .modal-header .close {
	color: #fff;
	opacity: 0.9;
	text-shadow: none;
	margin-top: 2px;
}
#approvalVisitModal .modal-title {
	font-size: 16px;
	font-weight: 600;
}
#approvalVisitModal .modal-body {
	padding: 15px 18px;
	background: #f7f9fb;
}
#approvalVisitModal .modal-footer {
	background: #fff;
	margin-top: 0;
	padding: 10px 18px;
}
.av-visit-wrap {
	background: #fff;
	border: 1px solid #e7ecf1;
	border-radius: 3px;
}
.av-filter-bar {
	padding: 14px 16px 8px;
	border-bottom: 1px solid #eef1f5;
	background: #fff;
}
.av-filter-bar .control-label {
	font-weight: 600;
	font-size: 12px;
	color: #666;
	display: block;
	margin-bottom: 4px;
}
.av-filter-actions {
	padding-top: 0;
}
.av-filter-actions .btn {
	margin-right: 6px;
	margin-bottom: 6px;
}
.av-summary {
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	justify-content: space-between;
	gap: 10px;
	padding: 10px 16px;
	background: #edf4fb;
	border-bottom: 1px solid #d9e6f2;
	font-size: 13px;
}
.av-summary-left,
.av-summary-mid {
	margin-right: 12px;
}
.av-summary i {
	color: #3598dc;
	margin-right: 5px;
}
.av-total-badge {
	display: inline-block;
	background: #3598dc;
	color: #fff;
	padding: 4px 12px;
	border-radius: 12px;
	font-size: 12px;
}
.av-table-wrap {
	margin: 0;
	border: 0;
	max-height: 420px;
	overflow: auto;
}
.av-visit-table {
	margin-bottom: 0 !important;
	width: 100%;
}
.av-visit-table thead th {
	background: #eef2f7 !important;
	color: #333;
	font-weight: 600;
	white-space: nowrap;
	vertical-align: middle !important;
	text-align: center;
	border-bottom: 1px solid #dce3ec !important;
	padding: 10px 8px !important;
}
.av-visit-table tbody td {
	vertical-align: middle !important;
	padding: 9px 8px !important;
	white-space: normal;
	word-break: break-word;
}
.av-visit-table tbody td:nth-child(1),
.av-visit-table tbody td:nth-child(2),
.av-visit-table tbody td:nth-child(3),
.av-visit-table tbody td:nth-child(4) {
	white-space: nowrap;
}
.av-empty {
	padding: 28px 12px !important;
	color: #777;
	background: #fafbfc;
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

					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption"><i class="fa fa-filter"></i> Filters</div>
							<div class="tools"><a href="javascript:;" class="collapse"></a></div>
						</div>
						<div class="portlet-body">
							<form class="form-inline" role="form" onSubmit="return searchByName();">
								<div class="form-group" style="margin-right:10px;margin-bottom:8px;">
									<label>Search:&nbsp;</label>
									<input type="text" class="form-control input-medium" name="searchName" id="searchName" placeholder="Company / Person / Project" />
								</div>
								<div class="form-group" style="margin-right:10px;margin-bottom:8px;">
									<label>Date:&nbsp;</label>
									<input type="text" class="form-control input-medium" id="date_filter" name="date_filter" placeholder="Select date range" readonly />
								</div>
								<div class="form-group" style="margin-right:10px;margin-bottom:8px;">
									<label>Approval:&nbsp;</label>
									<select class="form-control input-small" id="status_filter" name="status_filter">
										<option value="">All</option>
										<option value="Pending">Pending</option>
										<option value="Yes">Yes</option>
										<option value="No">No</option>
									</select>
								</div>
								<div class="form-group" style="margin-bottom:8px;">
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
									<?php echo $db->getAddButton($ctable, '', 'approval_crud.php?mode=add'); ?>
									&nbsp;
									<a href="javascript:;" class="btn btn-warning" id="btnApprovalChangePassword" title="Change Approval module password">
										<i class="fa fa-key"></i> Change Password
									</a>
								</div>
								<div class="col-md-6 text-right" style="padding-top:6px;">
									<span id="approval_total_amount_box" class="label label-primary" style="font-size:14px;padding:8px 14px;display:inline-block;">
										Total Amount: <strong id="approval_total_amount">0.00</strong>
									</span>
								</div>
							</div>
						</div>
						<div class="portlet-body">
							<div class="loading-div" style="display:none;">
								<img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin:10% auto;display:block;">
							</div>
							<div class="table-responsive" id="approval_grid_wrap">
								<div id="results"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="approvalVisitModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-map-marker"></i> Customer Visit Data</h4>
			</div>
			<div class="modal-body" id="approvalVisitModalBody">
				<div class="text-center" style="padding:30px;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="approvalChangePasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document" style="max-width:420px;">
		<div class="modal-content">
			<div class="modal-header" style="background:#f3c200;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-key"></i> Change Approval Password</h4>
			</div>
			<div class="modal-body">
				<form id="approvalChangePasswordForm" autocomplete="off" onsubmit="return false;">
					<div class="form-group">
						<label>Current Password</label>
						<input type="password" class="form-control" id="ap_current_password" autocomplete="off" required>
					</div>
					<div class="form-group">
						<label>New Password</label>
						<input type="password" class="form-control" id="ap_new_password" autocomplete="new-password" required>
					</div>
					<div class="form-group">
						<label>Confirm New Password</label>
						<input type="password" class="form-control" id="ap_confirm_password" autocomplete="new-password" required>
					</div>
					<p class="help-block" style="margin-bottom:0;">Password is never shown on screen. Min 6 characters.</p>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn yellow" id="ap_save_password_btn"><i class="fa fa-save"></i> Save Password</button>
			</div>
		</div>
	</div>
</div>

<?php include('footer.php'); ?>
<?php include('include_js.php'); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
var searchName = '';
var date_filter = '';
var status_filter = '';
var data_url = 'approval_get_ajax.php';

function searchByName() {
	searchName = $('#searchName').val();
	status_filter = $('#status_filter').val();
	displayRecords(100, 1);
	return false;
}

function clearSearchByName() {
	searchName = '';
	date_filter = '';
	status_filter = '';
	$('#searchName').val('');
	$('#date_filter').val('');
	$('#status_filter').val('');
	displayRecords(100, 1);
}

function displayRecords(numRecords, page) {
	$('.loading-div').show();
	$('#results').html('');
	$.post(data_url, {
		page: page || 1,
		show: numRecords || 100,
		searchName: searchName,
		date_filter: date_filter,
		status_filter: status_filter
	}, function (html) {
		$('.loading-div').hide();
		$('#results').html(html);
		loadDataTable();
	}).fail(function () {
		$('.loading-div').hide();
		$('#results').html('<div class="alert alert-danger">Failed to load listing.</div>');
	});
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
		"aoColumnDefs": [
			{ "bSortable": false, "aTargets": [0] }
		]
	});
	$('#approval_grid_wrap').scrollLeft(0);
}

function del_conf(id) {
	if (!confirm('Are you sure you want to delete this Approval entry?')) {
		return;
	}
	$.post('approval_ajax.php', { action: 'delete', id: id }, function (res) {
		if (res && res.success) {
			toastr.success(res.message || 'Deleted.');
			displayRecords(100, 1);
		} else {
			toastr.error((res && res.message) ? res.message : 'Delete failed.');
		}
	}, 'json').fail(function () {
		toastr.error('Delete failed.');
	});
}

function fireDueReminders() {
	$.post('approval_ajax.php', { action: 'fire_due_reminders' }, function (res) {
		if (!res || !res.success || !res.count) {
			return;
		}
		for (var i = 0; i < res.reminders.length; i++) {
			var r = res.reminders[i];
			var company = r.company_name || 'Company';
			var remDate = r.reminder_date || '';
			toastr.warning(
				'1-Year reminder (2 months before) for ' + company + (remDate ? ' — ' + remDate : ''),
				'Approval Reminder',
				{ timeOut: 8000 }
			);
		}
		displayRecords(100, 1);
	}, 'json');
}

$(document).on('click', '#btnApprovalChangePassword', function () {
	$('#ap_current_password').val('');
	$('#ap_new_password').val('');
	$('#ap_confirm_password').val('');
	$('#approvalChangePasswordModal').modal('show');
});

$(document).on('click', '#ap_save_password_btn', function () {
	var current = $('#ap_current_password').val();
	var newPass = $('#ap_new_password').val();
	var confirm = $('#ap_confirm_password').val();
	if (!current || !newPass || !confirm) {
		toastr.error('Please fill all password fields.');
		return;
	}
	if (newPass !== confirm) {
		toastr.error('New password and confirm password do not match.');
		return;
	}
	if (newPass.length < 6) {
		toastr.error('New password must be at least 6 characters.');
		return;
	}
	var $btn = $(this);
	$btn.prop('disabled', true);
	$.post('approval_ajax.php', {
		action: 'change_password',
		current_password: current,
		new_password: newPass,
		confirm_password: confirm
	}, function (res) {
		$btn.prop('disabled', false);
		if (res && res.success) {
			toastr.success(res.message || 'Password changed.');
			$('#approvalChangePasswordModal').modal('hide');
			$('#ap_current_password').val('');
			$('#ap_new_password').val('');
			$('#ap_confirm_password').val('');
		} else {
			toastr.error((res && res.message) ? res.message : 'Password change failed.');
		}
	}, 'json').fail(function () {
		$btn.prop('disabled', false);
		toastr.error('Password change failed.');
	});
});

$(document).on('click', '.approval-customer-visit', function (e) {
	e.preventDefault();
	var customerId = $(this).data('customer-id');
	var company = $(this).data('company') || '';
	$('#approvalVisitModal .modal-title').html('<i class="fa fa-map-marker"></i> Visit Data — ' + $('<div/>').text(company).html());
	loadApprovalVisits(customerId, company, '', '');
	$('#approvalVisitModal').modal('show');
});

function approvalVisitDefaultFrom() {
	var d = new Date();
	d.setDate(1);
	d.setMonth(d.getMonth() - 1);
	var m = ('0' + (d.getMonth() + 1)).slice(-2);
	var day = ('0' + d.getDate()).slice(-2);
	return d.getFullYear() + '-' + m + '-' + day;
}
function approvalVisitDefaultTo() {
	var d = new Date();
	var m = ('0' + (d.getMonth() + 1)).slice(-2);
	var day = ('0' + d.getDate()).slice(-2);
	return d.getFullYear() + '-' + m + '-' + day;
}

function loadApprovalVisits(customerId, company, fromDate, toDate) {
	$('#approvalVisitModalBody').html('<div class="text-center" style="padding:30px;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
	var params = {
		customer_id: customerId,
		company_name: company || ''
	};
	if (fromDate) {
		params.from_date = fromDate;
	}
	if (toDate) {
		params.to_date = toDate;
	}
	$.get('approval_visit_ajax.php', params, function (html) {
		$('#approvalVisitModalBody').html(html);
	}).fail(function () {
		$('#approvalVisitModalBody').html('<div class="alert alert-danger">Failed to load visit data.</div>');
	});
}

$(document).on('click', '#av_filter_btn', function () {
	var customerId = $('#av_customer_id').val();
	var company = $('#av_company_name').val() || '';
	var fromDate = $('#av_from_date').val();
	var toDate = $('#av_to_date').val();
	loadApprovalVisits(customerId, company, fromDate, toDate);
});

$(document).on('click', '#av_reset_btn', function () {
	var customerId = $('#av_customer_id').val();
	var company = $('#av_company_name').val() || '';
	loadApprovalVisits(customerId, company, approvalVisitDefaultFrom(), approvalVisitDefaultTo());
});

$(document).on('show.bs.dropdown', '#results .btn-group', function () {
	$('#approval_grid_wrap').addClass('dropdown-open');
	$('#approval_grid_wrap').closest('.portlet.light').addClass('dropdown-open');
});
$(document).on('hide.bs.dropdown', '#results .btn-group', function () {
	$('#approval_grid_wrap').removeClass('dropdown-open');
	$('#approval_grid_wrap').closest('.portlet.light').removeClass('dropdown-open');
});

$(document).ready(function () {
	if ($.fn.daterangepicker) {
		$('#date_filter').daterangepicker({
			autoUpdateInput: false,
			locale: { format: 'YYYY-MM-DD', cancelLabel: 'Clear' }
		});
		$('#date_filter').on('apply.daterangepicker', function (ev, picker) {
			date_filter = picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD');
			$(this).val(date_filter);
			displayRecords(100, 1);
		});
		$('#date_filter').on('cancel.daterangepicker', function () {
			date_filter = '';
			$(this).val('');
			displayRecords(100, 1);
		});
	}
	$('#status_filter').on('change', function () {
		status_filter = $(this).val();
		displayRecords(100, 1);
	});
	fireDueReminders();
	displayRecords(100, 1);
});
</script>
</body>
</html>
