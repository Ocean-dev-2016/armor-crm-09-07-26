<?php
/**
 * Admin / Sales Executive — Receive Payment from Armor customers.
 */
$page_id = 565;
$page_slug = 'armor_payment_receive';
$page = 'armor_payment_receive';
$main_page = 'orders';
$page_title = "Receive Payment";
$page_hierarchy = array(
	array("link" => "", "title" => "Order History"),
	array("link" => "armor_payment_receive.php", "title" => $page_title)
);
include("connect.php");

$is_cp = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
if ($is_cp) {
	$db->addErrorMessage("This page is only for Armor admin / sales executive.");
	$db->rp_location("channel_partner_payment.php");
}

$customer_id = isset($_REQUEST['customer_id']) ? (int) $_REQUEST['customer_id'] : 0;
$customer_name = $customer_id > 0 ? $db->rp_getValue("executive", "company_name", "id='" . $customer_id . "'", 0) : '';
$orders = array();

if ($customer_id > 0) {
	$where = "customer_id='" . $customer_id . "' AND isDelete=0 AND status NOT IN (-2,3) AND (channel_partner_order_flag=0 OR channel_partner_order_flag IS NULL) AND (channel_partner_customer_id=0 OR channel_partner_customer_id IS NULL)";
	$or = $db->rp_getData(
		"orders",
		"id,order_no,order_date,grand_total,payment_received_flag,payment_received_amount,status",
		$where,
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
.pay-card { background:#fff; border:1px solid #ddd; padding:20px; margin-bottom:20px; }
.pay-card h4 { margin-top:0; color:#1f4e79; border-bottom:2px solid #1f4e79; padding-bottom:8px; }
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

			<div class="pay-card">
				<h4><i class="fa fa-user"></i> 1. Select Armor Customer</h4>
				<form method="get" action="armor_payment_receive.php" class="form-inline">
					<div class="form-group" style="min-width:440px;">
						<select name="customer_id" id="customer_id" class="form-control input-large" required onchange="this.form.submit()">
							<option value="">-- Select Customer --</option>
							<?php
							$custWhere = "isDelete=0 AND channel_partner_flag=0";
							if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 2 && $_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
								$custWhere .= " AND sales_executive_id='" . (int) $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
							}
							$custR = $db->rp_getData("executive", "id,company_name,cname,client_code", $custWhere, "company_name ASC", 0);
							if ($custR) {
								while ($cust = mysqli_fetch_assoc($custR)) {
									$sel = ($customer_id == (int) $cust['id']) ? 'selected' : '';
									$label = $cust['company_name'];
									if (!empty($cust['cname'])) {
										$label .= ' / ' . $cust['cname'];
									}
									if (!empty($cust['client_code'])) {
										$label .= ' (' . $cust['client_code'] . ')';
									}
									echo '<option value="' . (int) $cust['id'] . '" ' . $sel . '>' . htmlspecialchars($label) . '</option>';
								}
							}
							?>
						</select>
					</div>
				</form>
			</div>

			<?php if ($customer_id > 0) { ?>
			<div class="pay-card">
				<h4><i class="fa fa-file-text-o"></i> 2. Against Armor Order — Payment Receive</h4>
				<p><strong>Customer:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
				<?php if (empty($orders)) { ?>
					<div class="alert alert-warning">No direct Armor orders found for this customer.</div>
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
									data-party-name="<?php echo htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8'); ?>">
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
			<div class="modal-header" style="background:#1f4e79;color:#fff;">
				<button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
				<h4 class="modal-title"><i class="fa fa-money"></i> Payment Received</h4>
			</div>
			<div class="modal-body">
				<input type="hidden" id="pr_order_id" value="">
				<div class="form-group"><label>Customer Name</label><input type="text" class="form-control" id="pr_party_name" readonly></div>
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
