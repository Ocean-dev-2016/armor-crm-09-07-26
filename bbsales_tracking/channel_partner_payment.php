<?php
/**
 * Channel Partner — Receive Payment (Tally / Miracle style).
 * Party (customer) + Against Order + Amount + Payment Type
 */
$page_id = 565;
$page_slug = 'channel_partner_payment';
$page = 'channel_partner_payment';
$main_page = 'channel_partner';
$page_title = "Receive Payment";
$page_hierarchy = array(
	array("link" => "", "title" => "Channel Partner"),
	array("link" => "channel_partner_payment.php", "title" => $page_title)
);
include("connect.php");

if (!function_exists('cp_is_channel_partner_login') || !cp_is_channel_partner_login($db)) {
	$db->addErrorMessage("Only Channel Partner login can receive customer payments.");
	$db->rp_location("dashboard.php");
}

$cp_id = (int) cp_get_login_channel_partner_id();
$cp_name = $db->rp_getValue("executive", "company_name", "id='" . $cp_id . "'", 0);

$parties = array();
$pr = $db->rp_getData(
	"channel_partner_customer",
	"id,company_name,person_name,mobile_no",
	"channel_partner_id='" . $cp_id . "' AND isDelete=0",
	"company_name ASC",
	0
);
if ($pr) {
	while ($p = mysqli_fetch_assoc($pr)) {
		$parties[] = $p;
	}
}

$selected_party = isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : 0;
$orders = array();
if ($selected_party > 0) {
	$or = $db->rp_getData(
		"orders",
		"id,order_no,order_date,grand_total,payment_received_flag,payment_received_amount,status",
		"customer_id='" . $cp_id . "' AND channel_partner_order_flag=1 AND channel_partner_customer_id='" . $selected_party . "' AND isDelete=0 AND status NOT IN (-2,3)",
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
.cp-party-label { font-size:16px; font-weight:700; color:#333; }
.table-orders td, .table-orders th { vertical-align:middle !important; }
</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><?php $db->pageBar($page_hierarchy); ?></h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<?php $db->printErrorMessage(); ?>
			<?php $db->printSuccessMessage(); ?>

			<div class="alert alert-info">
				<strong><?php echo htmlspecialchars($cp_name); ?></strong> —
				Receive payment from your customer (Party) against Sales Order — Tally / Miracle style.
			</div>

			<div class="cp-pay-card">
				<h4><i class="fa fa-user"></i> 1. Select Party / Customer</h4>
				<form method="get" action="channel_partner_payment.php" class="form-inline">
					<div class="form-group" style="min-width:420px;">
						<select name="party_id" id="party_id" class="form-control input-large" required onchange="this.form.submit()">
							<option value="">-- Select Party Name --</option>
							<?php foreach ($parties as $p) { ?>
								<option value="<?php echo (int) $p['id']; ?>" <?php echo ($selected_party == (int) $p['id']) ? 'selected' : ''; ?>>
									<?php echo htmlspecialchars($p['company_name'] . (!empty($p['person_name']) ? ' / ' . $p['person_name'] : '') . (!empty($p['mobile_no']) ? ' (' . $p['mobile_no'] . ')' : '')); ?>
								</option>
							<?php } ?>
						</select>
					</div>
				</form>
			</div>

			<?php if ($selected_party > 0) {
				$partyRow = null;
				foreach ($parties as $p) {
					if ((int) $p['id'] === $selected_party) {
						$partyRow = $p;
						break;
					}
				}
			?>
			<div class="cp-pay-card">
				<h4>
					<i class="fa fa-file-text-o"></i> 2. Against Order — Payment Receive
					<span class="pull-right" style="margin-top:-4px;">
						<button type="button" class="btn btn-sm green" id="btn_share_payment_pdf"
							data-type="payment" data-party-id="<?php echo (int) $selected_party; ?>"
							title="Share PDF on WhatsApp (CP number)">
							<i class="fa fa-whatsapp"></i> Share PDF
						</button>
						<a class="btn btn-sm red-haze" target="_blank"
							href="channel_partner_payment_print.php?party_id=<?php echo (int) $selected_party; ?>"
							title="Print / Save as PDF">
							<i class="fa fa-file-pdf-o"></i> Print PDF
						</a>
						<a class="btn btn-sm yellow-crusta" style="color:#fff;"
							href="channel_partner_payment_excel.php?party_id=<?php echo (int) $selected_party; ?>"
							title="Export Excel">
							<i class="fa fa-file-excel-o"></i> Export Excel
						</a>
					</span>
				</h4>
				<p class="cp-party-label">Party: <?php echo htmlspecialchars($partyRow ? $partyRow['company_name'] : ''); ?></p>
				<?php if (empty($orders)) { ?>
					<div class="alert alert-warning">No orders found for this party.</div>
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
							<td>
								<?php if ($paid === 1) { ?>
									<span class="label label-success">Received <?php echo number_format((float) $o['payment_received_amount'], 2); ?></span>
								<?php } else { ?>
									<span class="label label-danger">Pending</span>
								<?php } ?>
							</td>
							<td>
								<?php if ($paid !== 1) { ?>
								<button type="button" class="btn btn-sm btn-success btn-receive"
									data-order-id="<?php echo (int) $o['id']; ?>"
									data-order-no="<?php echo htmlspecialchars($o['order_no'], ENT_QUOTES, 'UTF-8'); ?>"
									data-grand-total="<?php echo (float) $o['grand_total']; ?>"
									data-party-name="<?php echo htmlspecialchars($partyRow ? $partyRow['company_name'] : '', ENT_QUOTES, 'UTF-8'); ?>">
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

<!-- Payment modal -->
<div id="paymentReceivedModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background:#2f6f44;color:#fff;">
				<button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
				<h4 class="modal-title"><i class="fa fa-money"></i> Payment Received (Against Order)</h4>
			</div>
			<div class="modal-body">
				<input type="hidden" id="pr_order_id" value="">
				<div class="form-group">
					<label>Party / Customer Name</label>
					<input type="text" class="form-control" id="pr_party_name" readonly style="font-weight:600;background:#f7f7f7;">
				</div>
				<div class="form-group">
					<label>Against Order Number</label>
					<input type="text" class="form-control" id="pr_order_no" readonly>
				</div>
				<div class="form-group">
					<label>Order Amount</label>
					<input type="text" class="form-control" id="pr_order_amount" readonly>
				</div>
				<div class="form-group">
					<label>Payment Received Amount <code>*</code></label>
					<input type="number" step="0.01" min="0.01" class="form-control" id="pr_paid_amount">
				</div>
				<div class="form-group">
					<label>Payment Type <code>*</code></label>
					<select class="form-control" id="pr_payment_type">
						<option value="">--- Select ---</option>
						<option value="1">By Cash</option>
						<option value="2">By Cheque</option>
						<option value="3">Online / NEFT / RTGS / UPI</option>
						<option value="4">Other</option>
					</select>
				</div>
				<div class="form-group">
					<label>Remark</label>
					<textarea class="form-control" id="pr_remark" rows="2"></textarea>
				</div>
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
<script src="js/cp_share_pdf.js"></script>
<script>
$('#btn_share_payment_pdf').on('click', function () {
	var $btn = $(this);
	cpSharePdfFile({
		$btn: $btn,
		type: 'payment',
		party_id: $btn.data('party-id') || 0
	});
});
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
	if (!orderId || !paidAmount || parseFloat(paidAmount) <= 0) {
		alert('Enter valid amount');
		return;
	}
	if (!paymentType) {
		alert('Select Payment Type');
		return;
	}
	$.ajax({
		url: 'order_payment_received_ajax.php',
		type: 'POST',
		dataType: 'json',
		data: {
			order_id: orderId,
			paid_amount: paidAmount,
			payment_type: paymentType,
			remark: remark
		},
		success: function (result) {
			if (result && result.ack == 1) {
				toastr.success(result.ack_msg);
				$('#paymentReceivedModal').modal('hide');
				setTimeout(function () { location.reload(); }, 700);
			} else {
				toastr.error((result && result.ack_msg) ? result.ack_msg : 'Failed');
			}
		},
		error: function () {
			alert('Request failed');
		}
	});
});
</script>
</body>
</html>
