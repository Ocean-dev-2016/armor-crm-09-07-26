<?php
$page_id = 405;
$page_slug = 'master_activity_detail_ajax';
include('connect.php');

if (!armor_is_master_activity_user()) {
	echo '<div class="alert alert-danger">Access denied.</div>';
	exit;
}

$salesId = isset($_REQUEST['sales_id']) ? (int) $_REQUEST['sales_id'] : 0;
$metric = isset($_REQUEST['metric']) ? trim($_REQUEST['metric']) : '';
$dateFrom = isset($_REQUEST['date']) ? $_REQUEST['date'] : '';
$dateTo = isset($_REQUEST['date1']) ? $_REQUEST['date1'] : '';

$labels = armor_master_activity_metric_labels();
if ($salesId <= 0 || !isset($labels[$metric])) {
	echo '<div class="alert alert-warning">Invalid request.</div>';
	exit;
}

$salesName = $db->rp_getValue('sales_executive', 'name', "id='" . $salesId . "'", 0);
$where = armor_master_activity_metric_where($metric, $salesId, $dateFrom, $dateTo);
$metricLabel = $labels[$metric];
?>
<style>
.ma-detail-table { font-size: 12px; }
.ma-detail-table th { background: #f5f5f5; white-space: nowrap; }
.ma-view-btn { margin: 2px 0; }
</style>
<h4><b><?php echo htmlspecialchars($salesName); ?></b> — <?php echo htmlspecialchars($metricLabel); ?> Detail</h4>
<div class="table-responsive">
<?php
switch ($metric) {
	case 'attendance':
		$res = $db->rp_getData('attendance', '*', $where, 'date_time DESC', 0);
		?>
		<table class="table table-bordered table-striped ma-detail-table">
			<thead><tr><th>Sr.</th><th>Date Time</th><th>In/Out</th><th>Address</th><th>Map</th><th>Photo</th></tr></thead>
			<tbody>
			<?php
			$sr = 1;
			if ($res && mysqli_num_rows($res) > 0) {
				while ($row = mysqli_fetch_assoc($res)) {
					$dt = date('d-m-Y h:i A', strtotime($row['date_time']));
					$map = armor_master_activity_map_btn($row['latitude'], $row['longitude'], $row['app_address'], $dt, $salesName, 'attendance');
					$imgUrl = armor_attendance_image(isset($row['image_path']) ? $row['image_path'] : '');
					$photo = ($imgUrl && $imgUrl != DEFAULTIMG)
						? '<a href="' . htmlspecialchars($imgUrl) . '" data-lightbox="att_' . (int) $row['id'] . '"><img src="' . htmlspecialchars($imgUrl) . '" style="height:60px;border:1px solid #ccc;border-radius:4px;"></a>'
						: '-';
					echo '<tr><td>' . $sr++ . '</td><td>' . $dt . '</td><td>' . htmlspecialchars($row['inout_status']) . '</td><td>' . htmlspecialchars(stripslashes($row['app_address'])) . '</td><td>' . $map . '</td><td>' . $photo . '</td></tr>';
				}
			} else {
				echo '<tr><td colspan="6" class="text-center">No records found.</td></tr>';
			}
			?>
			</tbody>
		</table>
		<?php
		break;

	case 'visits':
		$res = $db->rp_getData('visit', '*', $where, 'created_date DESC', 0);
		?>
		<table class="table table-bordered table-striped ma-detail-table">
			<thead>
				<tr>
					<th>Sr.</th><th>Customer</th><th>Date</th><th>Start Address</th><th>Start Map</th><th>Start Photo</th><th>Start Time</th>
					<th>Stop Address</th><th>Stop Map</th><th>Stop Photo</th><th>Stop Time</th><th>Purpose</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$sr = 1;
			if ($res && mysqli_num_rows($res) > 0) {
				while ($row = mysqli_fetch_assoc($res)) {
					$cust = '-';
					if ($row['customer_id'] > 0) {
						$cust = $db->rp_getValue('executive', 'cname', "id='" . $row['customer_id'] . "'", 0);
					} elseif ($row['inquiry_id'] > 0) {
						$cust = $db->rp_getValue('no_order_inquiry', 'company_name', "id='" . $row['inquiry_id'] . "'", 0);
					} elseif ($row['firm_name'] != '') {
						$cust = $row['firm_name'];
					}
					$dt = date('d-m-Y H:i', strtotime($row['created_date']));
					$startMap = armor_master_activity_map_btn($row['latitude'], $row['longitude'], $row['app_address'], $dt, $salesName, 'visit');
					$stopMap = armor_master_activity_map_btn($row['stop_latitude'], $row['stop_longitude'], $row['stop_app_address'], $dt, $salesName, 'visit');
					$startPhoto = armor_master_activity_render_images($db, $row, 'image_path', 'visit_s_' . $row['id']);
					$stopPhoto = armor_master_activity_render_images($db, $row, 'stop_image_path', 'visit_e_' . $row['id']);
					$startTime = ($row['start_date_time'] != '0000-00-00 00:00:00') ? date('d-m-Y h:i A', strtotime($row['start_date_time'])) : '-';
					$stopTime = ($row['stop_date_time'] != '0000-00-00 00:00:00') ? date('d-m-Y h:i A', strtotime($row['stop_date_time'])) : '-';
					echo '<tr>';
					echo '<td>' . $sr++ . '</td>';
					echo '<td>' . htmlspecialchars(stripslashes($cust)) . '</td>';
					echo '<td>' . $dt . '</td>';
					echo '<td>' . htmlspecialchars(stripslashes($row['app_address'])) . '</td>';
					echo '<td>' . $startMap . '</td>';
					echo '<td>' . $startPhoto . '</td>';
					echo '<td>' . $startTime . '</td>';
					echo '<td>' . htmlspecialchars(stripslashes($row['stop_app_address'])) . '</td>';
					echo '<td>' . $stopMap . '</td>';
					echo '<td>' . $stopPhoto . '</td>';
					echo '<td>' . $stopTime . '</td>';
					echo '<td>' . htmlspecialchars(stripslashes($row['visit_purpose'])) . '</td>';
					echo '</tr>';
				}
			} else {
				echo '<tr><td colspan="12" class="text-center">No records found.</td></tr>';
			}
			?>
			</tbody>
		</table>
		<?php
		break;

	case 'followups':
		$res = $db->rp_getData('followup', '*', $where, 'followup_date DESC', 0);
		?>
		<table class="table table-bordered table-striped ma-detail-table">
			<thead><tr><th>Sr.</th><th>Followup Date</th><th>Customer/Inquiry</th><th>Response</th><th>Remark</th><th>Status</th></tr></thead>
			<tbody>
			<?php
			$sr = 1;
			if ($res && mysqli_num_rows($res) > 0) {
				while ($row = mysqli_fetch_assoc($res)) {
					$ref = '-';
					if ($row['customer_id'] > 0) {
						$ref = $db->rp_getValue('executive', 'cname', "id='" . $row['customer_id'] . "'", 0);
					} elseif ($row['inquiry_id'] > 0) {
						$ref = $db->rp_getValue('no_order_inquiry', 'company_name', "id='" . $row['inquiry_id'] . "'", 0);
					}
					echo '<tr><td>' . $sr++ . '</td><td>' . date('d-m-Y', strtotime($row['followup_date'])) . '</td><td>' . htmlspecialchars(stripslashes($ref)) . '</td><td>' . htmlspecialchars(stripslashes($row['response'])) . '</td><td>' . htmlspecialchars(stripslashes($row['remark'])) . '</td><td>' . (int) $row['status'] . '</td></tr>';
				}
			} else {
				echo '<tr><td colspan="6" class="text-center">No records found.</td></tr>';
			}
			?>
			</tbody>
		</table>
		<?php
		break;

	case 'raw_data':
	case 'inquiry':
	case 'leads':
		$res = $db->rp_getData('no_order_inquiry', '*', $where, 'inquiry_date DESC', 0);
		?>
		<table class="table table-bordered table-striped ma-detail-table">
			<thead><tr><th>Sr.</th><th>Inquiry No.</th><th>Company</th><th>Person</th><th>Mobile</th><th>City</th><th>Date</th><th>Status</th></tr></thead>
			<tbody>
			<?php
			$sr = 1;
			if ($res && mysqli_num_rows($res) > 0) {
				while ($row = mysqli_fetch_assoc($res)) {
					echo '<tr><td>' . $sr++ . '</td><td>' . htmlspecialchars($row['inquiry_no']) . '</td><td>' . htmlspecialchars(stripslashes($row['company_name'])) . '</td><td>' . htmlspecialchars(stripslashes($row['person_name'])) . '</td><td>' . htmlspecialchars($row['mobile_number']) . '</td><td>' . htmlspecialchars($row['city']) . '</td><td>' . date('d-m-Y', strtotime($row['inquiry_date'])) . '</td><td>' . (int) $row['status'] . '</td></tr>';
				}
			} else {
				echo '<tr><td colspan="8" class="text-center">No records found.</td></tr>';
			}
			?>
			</tbody>
		</table>
		<?php
		break;

	case 'quotations':
		$res = $db->rp_getData('quotation_detail', '*', $where, 'quotation_date DESC', 0);
		?>
		<table class="table table-bordered table-striped ma-detail-table">
			<thead><tr><th>Sr.</th><th>Quotation No.</th><th>Customer</th><th>Date</th><th>Amount</th><th>Status</th><th>Full Detail</th></tr></thead>
			<tbody>
			<?php
			$sr = 1;
			if ($res && mysqli_num_rows($res) > 0) {
				while ($row = mysqli_fetch_assoc($res)) {
					$cust = $db->rp_getValue('executive', 'cname', "id='" . $row['customer_id'] . "'", 0);
					$amt = isset($row['grand_total']) ? number_format((float) $row['grand_total'], 2) : '-';
					echo '<tr><td>' . $sr++ . '</td><td>' . htmlspecialchars($row['quotation_no']) . '</td><td>' . htmlspecialchars(stripslashes($cust)) . '</td><td>' . date('d-m-Y', strtotime($row['quotation_date'])) . '</td><td>' . $amt . '</td><td>' . (int) $row['status'] . '</td><td><button type="button" class="btn btn-xs btn-info ma-view-btn ma-iframe-btn" data-url="quotation_viewer.php?quotation_id=' . (int) $row['id'] . '"><i class="fa fa-eye"></i> View Full</button></td></tr>';
				}
			} else {
				echo '<tr><td colspan="7" class="text-center">No records found.</td></tr>';
			}
			?>
			</tbody>
		</table>
		<?php
		break;

	case 'orders':
		$res = $db->rp_getData('orders', '*', $where, 'order_date DESC', 0);
		?>
		<table class="table table-bordered table-striped ma-detail-table">
			<thead><tr><th>Sr.</th><th>Order No.</th><th>Customer</th><th>Date</th><th>Amount</th><th>Status</th><th>Full Detail</th></tr></thead>
			<tbody>
			<?php
			$sr = 1;
			if ($res && mysqli_num_rows($res) > 0) {
				while ($row = mysqli_fetch_assoc($res)) {
					$cust = $db->rp_getValue('executive', 'cname', "id='" . $row['customer_id'] . "'", 0);
					$amt = isset($row['grand_total']) ? number_format((float) $row['grand_total'], 2) : '-';
					echo '<tr><td>' . $sr++ . '</td><td>' . htmlspecialchars($row['order_no']) . '</td><td>' . htmlspecialchars(stripslashes($cust)) . '</td><td>' . date('d-m-Y', strtotime($row['order_date'])) . '</td><td>' . $amt . '</td><td>' . (int) $row['status'] . '</td><td><button type="button" class="btn btn-xs btn-info ma-view-btn ma-iframe-btn" data-url="order_viewer.php?order_id=' . (int) $row['id'] . '"><i class="fa fa-eye"></i> View Full</button></td></tr>';
				}
			} else {
				echo '<tr><td colspan="7" class="text-center">No records found.</td></tr>';
			}
			?>
			</tbody>
		</table>
		<?php
		break;

	case 'dispatch':
		$res = $db->rp_getData('dispatch_detail', '*', $where, 'dispatch_date DESC', 0);
		?>
		<table class="table table-bordered table-striped ma-detail-table">
			<thead><tr><th>Sr.</th><th>Dispatch No.</th><th>Order No.</th><th>Customer</th><th>Date</th><th>Status</th></tr></thead>
			<tbody>
			<?php
			$sr = 1;
			if ($res && mysqli_num_rows($res) > 0) {
				while ($row = mysqli_fetch_assoc($res)) {
					$orderNo = $db->rp_getValue('orders', 'order_no', "id='" . $row['order_id'] . "'", 0);
					$cust = $db->rp_getValue('executive', 'cname', "id='" . $row['customer_id'] . "'", 0);
					echo '<tr><td>' . $sr++ . '</td><td>' . htmlspecialchars($row['dispatch_no']) . '</td><td>' . htmlspecialchars($orderNo) . '</td><td>' . htmlspecialchars(stripslashes($cust)) . '</td><td>' . date('d-m-Y', strtotime($row['dispatch_date'])) . '</td><td>' . (int) $row['status'] . '</td></tr>';
				}
			} else {
				echo '<tr><td colspan="6" class="text-center">No records found.</td></tr>';
			}
			?>
			</tbody>
		</table>
		<?php
		break;

	case 'invoice':
		$res = $db->rp_getData('invoice_new', '*', $where, 'adate DESC', 0);
		?>
		<table class="table table-bordered table-striped ma-detail-table">
			<thead><tr><th>Sr.</th><th>Invoice No.</th><th>Customer</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
			<tbody>
			<?php
			$sr = 1;
			if ($res && mysqli_num_rows($res) > 0) {
				while ($row = mysqli_fetch_assoc($res)) {
					$cust = $db->rp_getValue('executive', 'cname', "id='" . $row['customer_id'] . "'", 0);
					$amt = isset($row['grand_total']) ? number_format((float) $row['grand_total'], 2) : '-';
					echo '<tr><td>' . $sr++ . '</td><td>' . htmlspecialchars($row['invoice_no']) . '</td><td>' . htmlspecialchars(stripslashes($cust)) . '</td><td>' . date('d-m-Y', strtotime($row['adate'])) . '</td><td>' . $amt . '</td><td>' . (int) $row['status'] . '</td></tr>';
				}
			} else {
				echo '<tr><td colspan="6" class="text-center">No records found.</td></tr>';
			}
			?>
			</tbody>
		</table>
		<?php
		break;

	case 'complain':
		$res = $db->rp_getData('complain', '*', $where, 'complain_date DESC', 0);
		?>
		<table class="table table-bordered table-striped ma-detail-table">
			<thead><tr><th>Sr.</th><th>Complain No.</th><th>Customer</th><th>Date</th><th>Subject</th><th>Status</th><th>Photo</th></tr></thead>
			<tbody>
			<?php
			$sr = 1;
			if ($res && mysqli_num_rows($res) > 0) {
				while ($row = mysqli_fetch_assoc($res)) {
					$cust = $db->rp_getValue('executive', 'cname', "id='" . $row['customer_id'] . "'", 0);
					$photo = armor_master_activity_render_images($db, $row, 'image_path', 'complain_' . $row['id']);
					echo '<tr><td>' . $sr++ . '</td><td>' . htmlspecialchars($row['complain_no']) . '</td><td>' . htmlspecialchars(stripslashes($cust)) . '</td><td>' . date('d-m-Y', strtotime($row['complain_date'])) . '</td><td>' . htmlspecialchars(stripslashes($row['subject'])) . '</td><td>' . (int) $row['status'] . '</td><td>' . $photo . '</td></tr>';
				}
			} else {
				echo '<tr><td colspan="7" class="text-center">No records found.</td></tr>';
			}
			?>
			</tbody>
		</table>
		<?php
		break;

	case 'expense':
		$res = $db->rp_getData('expense', '*', $where, 'expense_date DESC', 0);
		?>
		<table class="table table-bordered table-striped ma-detail-table">
			<thead><tr><th>Sr.</th><th>Category</th><th>Date</th><th>Amount</th><th>Remark</th><th>Photo</th><th>Detail</th></tr></thead>
			<tbody>
			<?php
			$sr = 1;
			if ($res && mysqli_num_rows($res) > 0) {
				while ($row = mysqli_fetch_assoc($res)) {
					$cat = $db->rp_getValue('expense_category', 'name', "id='" . $row['expense_category_id'] . "'", 0);
					$photo = armor_master_activity_render_images($db, $row, 'image_path', 'expense_' . $row['id']);
					echo '<tr><td>' . $sr++ . '</td><td>' . htmlspecialchars(stripslashes($cat)) . '</td><td>' . date('d-m-Y', strtotime($row['expense_date'])) . '</td><td>' . number_format((float) $row['request_amount'], 2) . '</td><td>' . htmlspecialchars(stripslashes($row['remark'])) . '</td><td>' . $photo . '</td><td><button type="button" class="btn btn-xs btn-info ma-view-btn ma-iframe-btn" data-url="expense_view.php?id=' . (int) $row['id'] . '"><i class="fa fa-eye"></i> View</button></td></tr>';
				}
			} else {
				echo '<tr><td colspan="7" class="text-center">No records found.</td></tr>';
			}
			?>
			</tbody>
		</table>
		<?php
		break;

	case 'leave':
		$res = $db->rp_getData('leave_request', '*', $where, 'start_date DESC', 0);
		?>
		<table class="table table-bordered table-striped ma-detail-table">
			<thead><tr><th>Sr.</th><th>From</th><th>To</th><th>Days</th><th>Reason</th><th>Status</th></tr></thead>
			<tbody>
			<?php
			$sr = 1;
			if ($res && mysqli_num_rows($res) > 0) {
				while ($row = mysqli_fetch_assoc($res)) {
					echo '<tr><td>' . $sr++ . '</td><td>' . date('d-m-Y', strtotime($row['start_date'])) . '</td><td>' . date('d-m-Y', strtotime($row['end_date'])) . '</td><td>' . htmlspecialchars($row['total_days']) . '</td><td>' . htmlspecialchars(stripslashes($row['reason'])) . '</td><td>' . (int) $row['status'] . '</td></tr>';
				}
			} else {
				echo '<tr><td colspan="6" class="text-center">No records found.</td></tr>';
			}
			?>
			</tbody>
		</table>
		<?php
		break;
}
?>
</div>
