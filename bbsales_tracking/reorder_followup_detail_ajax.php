<?php
/**
 * Popup detail for Re-Order Followup Report:
 * type = visit | followup | quotation
 */
$page_id = 677;
$page_slug = 'reorder_followup_report';
include("connect.php");

header('Content-Type: text/html; charset=utf-8');

$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
$customerId = isset($_REQUEST['customer_id']) ? (int) $_REQUEST['customer_id'] : 0;
$customerName = isset($_REQUEST['customer_name']) ? trim($_REQUEST['customer_name']) : '';

if ($customerId <= 0 || !in_array($type, array('visit', 'followup', 'quotation'), true)) {
	echo '<div class="alert alert-warning" style="margin:0;">Invalid request.</div>';
	include("disconnect.php");
	exit;
}

$company = $db->rp_getValue("executive", "company_name", "id='" . $customerId . "'", 0);
if ($company == "") {
	$company = $customerName;
}
?>
<style>
.ror-pop-meta { margin-bottom: 12px; color: #666; font-size: 13px; }
.ror-pop-table { width: 100%; margin: 0; }
.ror-pop-table th { background: #f0f3f6; white-space: nowrap; }
.ror-pop-table td { vertical-align: middle !important; font-size: 12px; }
.ror-pop-empty { text-align: center; padding: 24px; color: #999; }
</style>
<div class="ror-pop-meta">
	<b>Customer:</b> <?php echo htmlspecialchars($company); ?>
	&nbsp;|&nbsp; <b>ID:</b> <?php echo (int) $customerId; ?>
</div>
<?php
if ($type === 'visit') {
	$where = "isDelete=0 AND customer_id='" . $customerId . "' AND entry_flag IN (2,5)";
	$total = (int) $db->rp_getTotalRecord("visit", $where, 0);
	$r = $db->rp_getData("visit", "id,start_date_time,end_date_time,remark,user_id,app_address", $where, "id DESC", 0);
	?>
	<div style="margin-bottom:8px;"><span class="label label-info">Total Visits: <?php echo $total; ?></span></div>
	<div class="table-responsive" style="max-height:420px;overflow:auto;">
		<table class="table table-striped table-bordered ror-pop-table">
			<thead>
				<tr>
					<th>#</th>
					<th>Date &amp; Time</th>
					<th>Sales Person</th>
					<th>Remark</th>
					<th>Address</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($r && mysqli_num_rows($r) > 0) {
				$i = 0;
				while ($row = mysqli_fetch_assoc($r)) {
					$i++;
					$dt = (!empty($row['start_date_time']) && $row['start_date_time'] != "0000-00-00 00:00:00")
						? date('d-m-Y h:i A', strtotime($row['start_date_time'])) : "-";
					$sp = $db->rp_getValue("sales_executive", "name", "id='" . $row['user_id'] . "'", 0);
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo htmlspecialchars($dt); ?></td>
						<td><?php echo htmlspecialchars($sp); ?></td>
						<td><?php echo htmlspecialchars(strip_tags((string) $row['remark'])); ?></td>
						<td><?php echo htmlspecialchars(strip_tags((string) $row['app_address'])); ?></td>
					</tr>
					<?php
				}
			} else {
				echo '<tr><td colspan="5" class="ror-pop-empty">No visit found.</td></tr>';
			}
			?>
			</tbody>
		</table>
	</div>
	<?php
} else if ($type === 'followup') {
	$where = "isDelete=0 AND entry_type IN (2,5) AND (visitor_id='" . $customerId . "' OR (reference_table='executive' AND reference_id='" . $customerId . "'))";
	$total = (int) $db->rp_getTotalRecord("followup", $where, 0);
	$r = $db->rp_getData("followup", "id,followup_date,description,through,user_id,response,status", $where, "followup_date DESC", 0);
	$throughMap = array("1" => "Call", "2" => "Sms", "3" => "Email", "4" => "Whatsapp", "5" => "Visit");
	?>
	<div style="margin-bottom:8px;"><span class="label label-success">Total Follow-ups: <?php echo $total; ?></span></div>
	<div class="table-responsive" style="max-height:420px;overflow:auto;">
		<table class="table table-striped table-bordered ror-pop-table">
			<thead>
				<tr>
					<th>#</th>
					<th>Date &amp; Time</th>
					<th>Through</th>
					<th>Sales Person</th>
					<th>Description</th>
					<th>Response</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($r && mysqli_num_rows($r) > 0) {
				$i = 0;
				while ($row = mysqli_fetch_assoc($r)) {
					$i++;
					$dt = (!empty($row['followup_date']) && $row['followup_date'] != "0000-00-00 00:00:00")
						? date('d-m-Y h:i A', strtotime($row['followup_date'])) : "-";
					$sp = $db->rp_getValue("sales_executive", "name", "id='" . $row['user_id'] . "'", 0);
					$th = isset($throughMap[$row['through']]) ? $throughMap[$row['through']] : "-";
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo htmlspecialchars($dt); ?></td>
						<td><?php echo htmlspecialchars($th); ?></td>
						<td><?php echo htmlspecialchars($sp); ?></td>
						<td><?php echo htmlspecialchars(strip_tags((string) $row['description'])); ?></td>
						<td><?php echo htmlspecialchars(strip_tags((string) $row['response'])); ?></td>
					</tr>
					<?php
				}
			} else {
				echo '<tr><td colspan="6" class="ror-pop-empty">No follow-up found.</td></tr>';
			}
			?>
			</tbody>
		</table>
	</div>
	<?php
} else if ($type === 'quotation') {
	$where = "isDelete=0 AND customer_id='" . $customerId . "' AND status!=-1 AND status!=4";
	$total = (int) $db->rp_getTotalRecord("quotation_detail", $where, 0);
	$valueTotal = (float) $db->rp_getValue("quotation_detail", "SUM(grand_total)", $where, 0);
	$r = $db->rp_getData("quotation_detail", "id,quotation_no,quotation_date,status,grand_total", $where, "id DESC", 0);
	$statusLbl = array("0" => "Pending", "1" => "Approved", "3" => "Cancelled", "5" => "Lost", "-2" => "Disapproved");
	?>
	<div style="margin-bottom:8px;">
		<span class="label label-warning">Total Quotations (Not converted): <?php echo $total; ?></span>
		&nbsp;
		<span class="label label-danger">Value Total: &#8377; <?php echo number_format($valueTotal, 2); ?></span>
	</div>
	<div class="table-responsive" style="max-height:420px;overflow:auto;">
		<table class="table table-striped table-bordered ror-pop-table">
			<thead>
				<tr>
					<th>#</th>
					<th>Quotation No</th>
					<th>Date</th>
					<th>Status</th>
					<th>Amount</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($r && mysqli_num_rows($r) > 0) {
				$i = 0;
				while ($row = mysqli_fetch_assoc($r)) {
					$i++;
					$qNo = !empty($row['quotation_no']) ? $row['quotation_no'] : ("QT/" . $row['id']);
					$qDate = (!empty($row['quotation_date']) && $row['quotation_date'] != "0000-00-00")
						? date('d-m-Y', strtotime($row['quotation_date'])) : "-";
					$st = isset($statusLbl[(string) $row['status']]) ? $statusLbl[(string) $row['status']] : ("Status " . $row['status']);
					$amt = number_format((float) $row['grand_total'], 2);
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo htmlspecialchars($qNo); ?></td>
						<td><?php echo htmlspecialchars($qDate); ?></td>
						<td><?php echo htmlspecialchars($st); ?></td>
						<td><?php echo htmlspecialchars($amt); ?></td>
						<td><a class="btn btn-xs blue" target="_blank" href="quotation_viewer.php?quotation_id=<?php echo (int) $row['id']; ?>">View</a></td>
					</tr>
					<?php
				}
			} else {
				echo '<tr><td colspan="6" class="ror-pop-empty">No quotation pending for order conversion.</td></tr>';
			}
			?>
			</tbody>
		</table>
	</div>
	<?php
}

include("disconnect.php");
