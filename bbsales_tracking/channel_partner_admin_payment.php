<?php
/**
 * Admin / Sales Executive — Receive Payment from Channel Partner against Armor orders.
 */
$page_id = 565;
$page_slug = 'channel_partner_admin_payment';
$page = 'channel_partner_admin_payment';
$main_page = 'channel_partner';
$page_title = "CP Receive Payment";
$page_hierarchy = array(
	array("link" => "", "title" => "Channel Partner"),
	array("link" => "channel_partner_admin_payment.php", "title" => $page_title)
);
include("connect.php");

$is_cp = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
if ($is_cp) {
	$db->addErrorMessage("This page is only for admin / sales executive.");
	$db->rp_location("channel_partner_payment.php");
}

$cp_id = isset($_REQUEST['cp_id']) ? (int) $_REQUEST['cp_id'] : 0;
$cp_name = $cp_id > 0 ? $db->rp_getValue("executive", "company_name", "id='" . $cp_id . "'", 0) : '';

$orders = array();
if ($cp_id > 0) {
	$or = $db->rp_getData(
		"orders",
		"id,order_no,order_date,grand_total,payment_received_flag,payment_received_amount,status",
		"customer_id='" . $cp_id . "' AND channel_partner_order_flag=1 AND isDelete=0 AND status NOT IN (-2,3) AND (cp_order_mode='own' OR cp_order_mode='' OR cp_order_mode IS NULL OR channel_partner_customer_id=0 OR channel_partner_customer_id IS NULL)",
		"id DESC",
		0
	);
	if ($or) {
		while ($o = mysqli_fetch_assoc($or)) {
			$orders[] = $o;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<style>
.cp-pay-card { background:#fff; border:1px solid #ddd; padding:20px; margin-bottom:20px; }
.cp-pay-card h4 { margin-top:0; color:#2f6f44; border-bottom:2px solid #2f6f44; padding-bottom:8px; }
.table-orders td, .table-orders th { vertical-align:middle !important; }
</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title"><h1><?php $db->pageBar($page_hierarchy); ?></h1></div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<?php $db->printErrorMessage(); ?>
			<?php $db->printSuccessMessage(); ?>

			<div class="cp-pay-card">
				<h4><i class="fa fa-user"></i> 1. Select Channel Partner</h4>
				<form method="get" action="channel_partner_admin_payment.php" class="form-inline">
					<div class="form-group" style="min-width:420px;">
						<select name="cp_id" id="cp_id" class="form-control input-large" required onchange="this.form.submit()">
							<option value="">-- Select Channel Partner --</option>
							<?php
							$cps = $db->rp_getData("executive", "id,company_name,client_code", "channel_partner_flag=1 AND customer_flag=0 AND isDelete=0", "company_name ASC", 0);
							if ($cps) {
								while ($cp = mysqli_fetch_assoc($cps)) {
									$sel = ($cp_id == (int) $cp['id']) ? 'selected' : '';
									echo '<option value="' . (int) $cp['id'] . '" ' . $sel . '>' . htmlspecialchars($cp['company_name'] . (!empty($cp['client_code']) ? ' (' . $cp['client_code'] . ')' : '')) . '</option>';
								}
							}
							?>
						</select>
					</div>
					<?php if ($cp_id > 0) { ?>
						<a class="btn default" href="channel_partner_cp_ledger.php?cp_id=<?php echo (int) $cp_id; ?>">Open CP Ledger</a>
					<?php } ?>
				</form>
			</div>

			<?php if ($cp_id > 0) { ?>
			<div class="cp-pay-card">
				<h4><i class="fa fa-file-text-o"></i> 2. Against Armor Order — Payment Receive</h4>
				<p><strong>Channel Partner:</strong> <?php echo htmlspecialchars($cp_name); ?></p>
				<?php if (empty($orders)) { ?>
					<div class="alert alert-warning">No CP orders found.</div>
				<?php } else { ?>
				<table class="table table-bordered table-striped table-orders">
					<thead>
						<tr>
							<th>Order No</th>
							<th>Date</th>
							<th>Order Amount</th>
							<th>Payment Status</th>
							<th>Receive</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($orders as $o) {
						$paid = isset($o['payment_received_flag']) ? (int) $o['payment_received_flag'] : 0;
						?>
						<tr>
							<td><strong><?php echo htmlspecialchars($o['order_no']); ?></strong></td>
							<td><?php echo date('d-m-Y', strtotime($o['order_date'])); ?></td>
							<td><?php echo number_format((float) $o['grand_total'], 2); ?></td>
							<td><?php if ($paid === 1) { ?><span class="label label-success">Received <?php echo number_format((float) $o['payment_received_amount'], 2); ?></span><?php } else { ?><span class="label label-danger">Pending</span><?php } ?></td>
							<td>
								<?php if ($paid !== 1) { ?>
								<button type="button" class="btn btn-sm btn-success btn-receive"
									data-order-id="<?php echo (int) $o['id']; ?>"
									data-order-no="<?php echo htmlspecialchars($o['order_no'], ENT_QUOTES, 'UTF-8'); ?>"
									data-grand-total="<?php echo (float) $o['grand_total']; ?>"
									data-party-name="<?php echo htmlspecialchars($cp_name, ENT_QUOTES, 'UTF-8'); ?>">
									<i class="fa fa-money"></i> Receive Payment
								</button>
								<?php } else { echo '-'; } ?>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>

<div id="paymentReceivedModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background:#2f6f44;color:#fff;">
				<button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
				<h4 class="modal-title"><i class="fa fa-money"></i> Payment Received (Against CP Order)</h4>
			</div>
			<div class="modal-body">
				<input type="hidden" id="pr_order_id" value="">
				<div class="form-group"><label>Channel Partner Name</label><input type="text" class="form-control" id="pr_party_name" readonly></div>
				<div class="form-group"><label>Against Order Number</label><input type="text" class="form-control" id="pr_order_no" readonly></div>
				<div class="form-group"><label>Order Amount</label><input type="text" class="form-control" id="pr_order_amount" readonly></div>
				<div class="form-group"><label>Payment Received Amount <code>*</code></label><input type="number" step="0.01" min="0.01" class="form-control" id="pr_paid_amount"></div>
				<div class="form-group"><label>Payment Type <code>*</code></label>
					<select class="form-control" id="pr_payment_type">
						<option value="">--- Select ---</option>
						<option value="1">By Cash</option>
						<option value="2">By Cheque</option>
						<option value="3">Online / NEFT / RTGS / UPI</option>
						<option value="4">Other</option>
					</select>
				</div>
				<div class="form-group"><label>Remark</label><textarea class="form-control" id="pr_remark" rows="2"></textarea></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-success" id="pr_save_btn"><i class="fa fa-save"></i> Save Payment</button>
			</div>
		</div>
	</div>
</div>

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script>
$('.btn-receive').on('click', function () {
	$('#pr_order_id').val($(this).data('order-id'));
	$('#pr_order_no').val($(this).data('order-no'));
	$('#pr_order_amount').val($(this).data('grand-total'));
	$('#pr_party_name').val($(this).data('party-name'));
	$('#pr_paid_amount').val($(this).data('grand-total'));
	$('#pr_payment_type').val('');
	$('#pr_remark').val('Payment received from ' + $(this).data('party-name') + ' against Order ' + $(this).data('order-no'));
	$('#paymentReceivedModal').modal('show');
});
$('#pr_save_btn').on('click', function () {
	var orderId = $('#pr_order_id').val();
	var paidAmount = $.trim($('#pr_paid_amount').val());
	var paymentType = $('#pr_payment_type').val();
	var remark = $.trim($('#pr_remark').val());
	if (!orderId || !paidAmount || parseFloat(paidAmount) <= 0) { alert('Enter valid amount'); return; }
	if (!paymentType) { alert('Select Payment Type'); return; }
	$.ajax({
		url: 'order_payment_received_ajax.php',
		type: 'POST',
		dataType: 'json',
		data: { order_id: orderId, paid_amount: paidAmount, payment_type: paymentType, remark: remark },
		success: function (result) {
			if (result && result.ack == 1) {
				toastr.success(result.ack_msg);
				$('#paymentReceivedModal').modal('hide');
				setTimeout(function () { location.reload(); }, 700);
			} else {
				toastr.error((result && result.ack_msg) ? result.ack_msg : 'Failed');
			}
		},
		error: function () { toastr.error('Server error while saving payment.'); }
	});
});
</script>
</body>
</html>
