<?php
$page_id = 405;
$page_slug = 'master_activity_dashboard';
include('connect.php');

if (!armor_is_master_activity_user()) {
	$db->rp_location('access_denied.php?msg=access_denied');
	exit;
}

$defaultFrom = '01-01-' . date('Y');
$defaultTo = date('d-m-Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Employee Sales Activity | <?php echo SITETITLE; ?></title>
<?php include('include_css.php'); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/jquery-ui/jquery-ui.min.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<link rel="stylesheet" href="<?php echo ADMINSITEURL; ?>css/lightbox.css"/>
<style>
.master-activity-header {
	background: #364150;
	color: #fff;
	padding: 15px 0;
	margin-bottom: 20px;
}
.master-activity-header h2 { margin: 0; color: #fff; }
.master-activity-header .logout-btn { margin-top: 8px; }
#activity_table th, #activity_table td {
	text-align: center;
	vertical-align: middle !important;
	font-size: 13px;
}
#activity_table th:first-child, #activity_table td:first-child,
#activity_table th:nth-child(2), #activity_table td:nth-child(2) { text-align: left; }
.total-row td { font-weight: bold; background: #f5f5f5 !important; }
.detail-link, .count-link { cursor: pointer; color: #337ab7; font-weight: 600; }
.count-link:hover { text-decoration: underline; color: #23527c; }
.count-zero { color: #999; }
#detail_modal_body, #iframe_modal_body { max-height: 75vh; overflow-y: auto; }
#map_canvas { min-height: 400px; }
</style>
</head>
<body class="page-md">
<div class="master-activity-header">
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				<h2><i class="fa fa-bar-chart"></i> All Employee Sales Activity</h2>
				<small>Welcome, <?php echo htmlspecialchars($_SESSION[SITE_SESS . 'SESS_NAME']); ?></small>
			</div>
			<div class="col-md-4 text-right logout-btn">
				<a href="logout.php" class="btn btn-danger"><i class="fa fa-sign-out"></i> Logout</a>
			</div>
		</div>
	</div>
</div>

<div class="page-content">
	<div class="container">
		<div class="portlet light bordered">
			<div class="portlet-body">
				<div class="row" style="margin-bottom:15px;">
					<div class="col-md-3">
						<label>From Date</label>
						<input type="text" class="form-control date-picker" id="date_from" value="<?php echo $defaultFrom; ?>" readonly>
					</div>
					<div class="col-md-3">
						<label>To Date</label>
						<input type="text" class="form-control date-picker" id="date_to" value="<?php echo $defaultTo; ?>" readonly>
					</div>
					<div class="col-md-3" style="margin-top:25px;">
						<button type="button" class="btn btn-primary" id="btn_search"><i class="fa fa-search"></i> Search</button>
					</div>
				</div>
				<div id="report_loader" class="text-center" style="display:none;padding:30px;">
					<i class="fa fa-spinner fa-spin fa-2x"></i> Loading...
				</div>
				<div id="report_content"></div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" style="width:95%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="detail_modal_title">Activity Detail</h4>
			</div>
			<div class="modal-body" id="detail_modal_body"></div>
		</div>
	</div>
</div>

<div class="modal fade" id="map_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Location Map</h4>
			</div>
			<div class="modal-body"><div id="map_canvas"></div></div>
		</div>
	</div>
</div>

<div class="modal fade" id="iframe_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" style="width:95%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="iframe_modal_title">Full Detail</h4>
			</div>
			<div class="modal-body" id="iframe_modal_body" style="padding:0;">
				<iframe id="iframe_detail" src="" style="width:100%;height:75vh;border:none;"></iframe>
			</div>
		</div>
	</div>
</div>

<?php include('include_js.php'); ?>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script src="<?php echo ADMINSITEURL; ?>js/lightbox.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4"></script>
<script>
var metricLabels = {
	attendance: 'Attendance', visits: 'Visits', followups: 'Followups', raw_data: 'Raw Data',
	inquiry: 'Inquiry', leads: 'Leads', quotations: 'Quotation', orders: 'Orders',
	dispatch: 'Dispatch', invoice: 'Invoice', complain: 'Complain', expense: 'Expense', leave: 'Leave'
};

function bindDetailModalEvents() {
	$('#detail_modal_body').off('click.maMap').on('click.maMap', '.ma-map-btn', function() {
		var $b = $(this);
		var mapUrl = ($b.data('map-type') === 'attendance') ? 'get_attendance_map_dashboard.php' : 'get_visit_map_dashboard.php';
		$('#map_canvas').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
		$('#map_modal').modal('show');
		$.ajax({
			url: mapUrl,
			data: {
				lat: $b.data('lat'),
				lng: $b.data('lng'),
				app_address: $b.data('address'),
				date: $b.data('date'),
				salesexename: $b.data('name')
			},
			success: function(html) { $('#map_canvas').html(html); },
			error: function() { $('#map_canvas').html('<div class="alert alert-danger">Map not available.</div>'); }
		});
	});

	$('#detail_modal_body').off('click.maIframe').on('click.maIframe', '.ma-iframe-btn', function() {
		var url = $(this).data('url');
		$('#iframe_modal_title').text('Full Detail');
		$('#iframe_detail').attr('src', url);
		$('#iframe_modal').modal('show');
	});
}

function loadReport() {
	$('#report_loader').show();
	$('#report_content').html('');
	$.ajax({
		url: 'master_activity_dashboard_get_ajax.php',
		type: 'POST',
		data: { date: $('#date_from').val(), date1: $('#date_to').val() },
		success: function(html) {
			$('#report_loader').hide();
			$('#report_content').html(html);
			if ($('#activity_table').length) {
				$('#activity_table').DataTable({ paging: false, searching: true, ordering: true, info: false });
			}
		},
		error: function() {
			$('#report_loader').hide();
			$('#report_content').html('<div class="alert alert-danger">Failed to load report.</div>');
		}
	});
}

function showCellDetail(salesId, metric, salesName) {
	var label = metricLabels[metric] || metric;
	$('#detail_modal_title').text(salesName + ' — ' + label);
	$('#detail_modal_body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
	$('#detail_modal').modal('show');
	$.ajax({
		url: 'master_activity_detail_get_ajax.php',
		type: 'POST',
		data: {
			sales_id: salesId,
			metric: metric,
			date: $('#date_from').val(),
			date1: $('#date_to').val()
		},
		success: function(html) {
			$('#detail_modal_body').html(html);
			bindDetailModalEvents();
		},
		error: function() {
			$('#detail_modal_body').html('<div class="alert alert-danger">Failed to load detail.</div>');
		}
	});
}

function showEmployeeDetail(salesId, salesName) {
	$('#detail_modal_title').text(salesName + ' - Activity Summary');
	$('#detail_modal_body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
	$('#detail_modal').modal('show');
	$.ajax({
		url: 'dashboard_statical_report_get_ajax.php',
		type: 'POST',
		data: { sales_id: salesId, date: $('#date_from').val(), date1: $('#date_to').val() },
		success: function(html) {
			$('#detail_modal_body').html(html);
			bindDetailModalEvents();
		},
		error: function() {
			$('#detail_modal_body').html('<div class="alert alert-danger">Failed to load detail.</div>');
		}
	});
}

$(function() {
	$('.date-picker').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true });
	$('#btn_search').on('click', loadReport);
	$('#iframe_modal').on('hidden.bs.modal', function() { $('#iframe_detail').attr('src', ''); });
	loadReport();
});
</script>
</body>
</html>
