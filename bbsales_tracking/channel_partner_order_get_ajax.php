<?php
$page_id = 565;
$page_slug = 'channel_partner_order';
include("connect.php");

if (!function_exists('cp_is_channel_partner_login') || !cp_is_channel_partner_login($db)) {
	echo '<div class="alert alert-danger">Access denied.</div>';
	require_once("disconnect.php");
	exit;
}

$cp_id = (int) cp_get_login_channel_partner_id();
$search = isset($_REQUEST['searchName']) ? trim($_REQUEST['searchName']) : '';
$item_per_page = (isset($_REQUEST["show"]) && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 10;
$page_number = (isset($_REQUEST["page"]) && is_numeric($_REQUEST["page"])) ? intval($_REQUEST["page"]) : 1;
if ($page_number <= 0) {
	$page_number = 1;
}

$where = "o.customer_id='" . $cp_id . "' AND o.channel_partner_order_flag=1 AND o.cp_order_mode='customer' AND o.channel_partner_customer_id>0 AND o.isDelete=0";
if ($search != '') {
	$s = mysqli_real_escape_string($db->myconn, $search);
	$where .= " AND (order_no LIKE '%" . $s . "%' OR company_name LIKE '%" . $s . "%' OR channel_partner_customer_id IN (
		SELECT id FROM channel_partner_customer
		WHERE channel_partner_id='" . $cp_id . "' AND isDelete=0
		AND (company_name LIKE '%" . $s . "%' OR person_name LIKE '%" . $s . "%' OR mobile_no LIKE '%" . $s . "%')
	))";
}

$get_total_rows = $db->rp_getTotalRecord("orders", $where);
$total_pages = ceil($get_total_rows / $item_per_page);
$page_position = (($page_number - 1) * $item_per_page);

$sql = "SELECT o.id, o.order_no, o.order_date, o.grand_total, o.payment_received_flag, o.payment_received_amount,
	o.status, o.channel_partner_customer_id, c.company_name AS party_name, c.person_name
	FROM orders o
	LEFT JOIN channel_partner_customer c ON c.id=o.channel_partner_customer_id
	WHERE $where
	ORDER BY o.id DESC
	LIMIT $page_position, $item_per_page";
$res = mysqli_query($db->myconn, $sql);

/**
 * CP customer-order workflow status for list:
 * Pending → Dispatched → Pending Payment (baki) → Completed
 */
function cp_customer_order_workflow($status, $paidFlag, $grandTotal, $paidAmount)
{
	$status = (int) $status;
	$paidFlag = (int) $paidFlag;
	$grandTotal = (float) $grandTotal;
	$paidAmount = (float) $paidAmount;
	$isPaid = ($paidFlag === 1 && $paidAmount > 0);
	$isDispatched = ($status >= 5 && $status != 3 && $status != -2);
	$baki = $isPaid ? 0 : max(0, $grandTotal - $paidAmount);

	if ($isPaid) {
		return array(
			'phase' => 'completed',
			'label' => 'Completed',
			'class' => 'success',
			'baki' => 0,
			'can_dispatch' => false,
		);
	}
	if ($isDispatched) {
		return array(
			'phase' => 'pending_payment',
			'label' => 'Pending Payment',
			'class' => 'warning',
			'baki' => $baki,
			'can_dispatch' => false,
		);
	}
	return array(
		'phase' => 'pending',
		'label' => 'Pending',
		'class' => 'warning',
		'baki' => $baki,
		'can_dispatch' => true,
	);
}
?>
<table id="datatable_1" class="table table-bordered table-striped dataTable">
	<thead>
		<tr>
			<th style="width:5%;">#</th>
			<th style="width:12%;">Order No</th>
			<th style="width:10%;">Date</th>
			<th>Customer / Party</th>
			<th style="width:8%;">Qty</th>
			<th style="width:11%;">Amount</th>
			<th style="width:12%;">Payment</th>
			<th style="width:18%;">Status</th>
			<th style="width:10%;">Action</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if ($res && mysqli_num_rows($res) > 0) {
		$sr = $page_position + 1;
		while ($row = mysqli_fetch_assoc($res)) {
			$party = trim($row['party_name']);
			if ($party == '') {
				$party = trim($row['person_name']);
			}
			if ($party == '') {
				$party = '-';
			}
			$qty = $db->rp_getValue("order_product_item", "SUM(pro_qty)", "order_id='" . (int) $row['id'] . "' AND isDelete=0", 0);
			if ($qty === '' || $qty === null) {
				$qty = $db->rp_getValue("order_product_item", "SUM(pro_qty)", "order_id='" . (int) $row['id'] . "'", 0);
			}
			$paidAmt = (float) $row['payment_received_amount'];
			$paidFlag = (int) $row['payment_received_flag'];
			$paid = ($paidFlag === 1 && $paidAmt > 0)
				? 'Received ' . number_format($paidAmt, 2)
				: 'Pending';
			$wf = cp_customer_order_workflow($row['status'], $paidFlag, $row['grand_total'], $paidAmt);
			$partyId = (int) $row['channel_partner_customer_id'];
			?>
			<tr>
				<td><?php echo $sr++; ?></td>
				<td>
					<a href="channel_partner_order_print.php?order_id=<?php echo (int) $row['id']; ?>" target="_blank">
						<?php echo htmlspecialchars($row['order_no']); ?>
					</a>
				</td>
				<td><?php echo (!empty($row['order_date']) && $row['order_date'] != '0000-00-00') ? date('d-m-Y', strtotime($row['order_date'])) : '-'; ?></td>
				<td><?php echo htmlspecialchars($party); ?></td>
				<td><?php echo ($qty !== '' && $qty !== null) ? number_format((float) $qty, 2) : '0.00'; ?></td>
				<td><?php echo number_format((float) $row['grand_total'], 2); ?></td>
				<td><?php echo $paid; ?></td>
				<td>
					<?php if ($wf['can_dispatch']) { ?>
						<select class="form-control input-sm cp-status-update"
							data-order-id="<?php echo (int) $row['id']; ?>"
							data-order-no="<?php echo htmlspecialchars($row['order_no']); ?>"
							style="min-width:140px;">
							<option value="pending" selected>Pending</option>
							<option value="dispatch">Dispatched</option>
						</select>
					<?php } else if ($wf['phase'] === 'pending_payment') { ?>
						<span class="label label-warning">Pending Payment</span>
						<?php if ($wf['baki'] > 0) { ?>
							<div style="margin-top:4px;font-size:11px;color:#c0392b;font-weight:600;">
								Baki: <?php echo number_format($wf['baki'], 2); ?>
							</div>
						<?php } ?>
						<div style="margin-top:4px;">
							<a class="btn btn-xs green" href="channel_partner_payment.php?party_id=<?php echo $partyId; ?>" title="Receive Payment">
								<i class="fa fa-inr"></i> Receive
							</a>
						</div>
					<?php } else { ?>
						<span class="label label-success">Completed</span>
					<?php } ?>
				</td>
				<td>
					<a class="btn btn-xs blue" target="_blank" href="channel_partner_order_print.php?order_id=<?php echo (int) $row['id']; ?>">
						<i class="fa fa-print"></i> Print
					</a>
				</td>
			</tr>
			<?php
		}
	} else {
		?>
		<tr>
			<td colspan="9" style="text-align:center;">No customer orders found.</td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>
<?php if ($total_pages > 1) { ?>
<div class="row">
	<div class="col-md-6">
		<div class="dataTables_info">Total Records: <?php echo $get_total_rows; ?></div>
	</div>
	<div class="col-md-6">
		<div class="dataTables_paginate paging_simple_numbers">
			<ul class="pagination">
				<?php for ($i = 1; $i <= $total_pages; $i++) { ?>
					<li class="<?php echo ($i == $page_number) ? 'active' : ''; ?>">
						<a href="javascript:void(0);" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
<input type="hidden" id="numRecords" value="<?php echo $item_per_page; ?>">
<?php } ?>
<?php require_once("disconnect.php"); ?>
