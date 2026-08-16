<?php
/**
 * Channel Partner — Payment Receive Statement print (A4 — same layout as SO / PI).
 */
$page_id = 565;
$page_slug = 'channel_partner_payment';
include("connect.php");
include("../include/no_to_word.php");

if (!function_exists('cp_is_channel_partner_login') || !cp_is_channel_partner_login($db)) {
	$db->addErrorMessage("Only Channel Partner login can print payment list.");
	$db->rp_location("dashboard.php");
}

$cp_id = (int) cp_get_login_channel_partner_id();
$party_id = isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : 0;

if ($party_id <= 0) {
	$db->addErrorMessage("Please select a Party first.");
	$db->rp_location("channel_partner_payment.php");
}

$cp_r = $db->rp_getData("executive", "*", "id='" . $cp_id . "' AND isDelete=0", "", 0);
$cp = $cp_r ? mysqli_fetch_assoc($cp_r) : array();
$cp_name = isset($cp['company_name']) ? $cp['company_name'] : '';

$pi_company = !empty($cp['cp_print_company_name']) ? $cp['cp_print_company_name'] : $cp_name;
$pi_gst = !empty($cp['cp_print_gst']) ? $cp['cp_print_gst'] : (isset($cp['gst']) ? $cp['gst'] : '');
$pi_pan = isset($cp['cp_print_pan']) ? $cp['cp_print_pan'] : '';
$pi_address = isset($cp['cp_print_address']) ? $cp['cp_print_address'] : '';
$pi_bank = isset($cp['cp_print_bank_details']) ? $cp['cp_print_bank_details'] : '';
$pi_terms = isset($cp['cp_print_terms']) ? $cp['cp_print_terms'] : '';

$headerImgFile = isset($cp['cp_print_header_image']) ? $cp['cp_print_header_image'] : '';
$footerImgFile = isset($cp['cp_print_footer_image']) ? $cp['cp_print_footer_image'] : '';
$headerImgSrc = '';
$footerImgSrc = '';
if ($headerImgFile != '' && defined('HEADER_A') && file_exists(HEADER_A . $headerImgFile)) {
	$headerImgSrc = HEADER_A . $headerImgFile;
}
if ($footerImgFile != '' && defined('HEADER_A') && file_exists(HEADER_A . $footerImgFile)) {
	$footerImgSrc = HEADER_A . $footerImgFile;
}

$party = $db->rp_getData(
	"channel_partner_customer",
	"*",
	"id='" . $party_id . "' AND channel_partner_id='" . $cp_id . "' AND isDelete=0",
	"",
	0
);
$partyRow = ($party) ? mysqli_fetch_assoc($party) : null;
if (!$partyRow) {
	$db->addErrorMessage("Invalid Party.");
	$db->rp_location("channel_partner_payment.php");
}

$party_addr = implode(', ', array_filter(array(
	isset($partyRow['address']) ? $partyRow['address'] : '',
	isset($partyRow['city']) ? $partyRow['city'] : '',
	isset($partyRow['state']) ? $partyRow['state'] : '',
	isset($partyRow['pincode']) ? $partyRow['pincode'] : '',
)));

$orders = array();
$selectCols = "id,order_no,order_date,grand_total,payment_received_flag,payment_received_amount,status";
$colPayDate = @mysqli_query($db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'payment_received_date'");
if ($colPayDate && mysqli_num_rows($colPayDate) > 0) {
	$selectCols .= ",payment_received_date";
}
$colPayType = @mysqli_query($db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'payment_received_type'");
if ($colPayType && mysqli_num_rows($colPayType) > 0) {
	$selectCols .= ",payment_received_type";
}
$or = $db->rp_getData(
	"orders",
	$selectCols,
	"customer_id='" . $cp_id . "' AND channel_partner_order_flag=1 AND channel_partner_customer_id='" . $party_id . "' AND isDelete=0 AND status NOT IN (-2,3)",
	"order_date ASC, id ASC",
	0
);
if ($or) {
	while ($o = mysqli_fetch_assoc($or)) {
		$orders[] = $o;
	}
}

$payTypeLabel = array(1 => 'Cash', 2 => 'Cheque', 3 => 'Online', 4 => 'Other');
$currency = defined('CURR') ? CURR : 'Rs.';
$doc_title = 'PAYMENT RECEIVE STATEMENT';
$ntw = new NumToWord_RP();

/* Pre-calc totals for words */
$totalOrder = 0;
$totalPaid = 0;
$totalPending = 0;
foreach ($orders as $o) {
	$pay = cp_order_payment_state($o['grand_total'], $o['payment_received_amount']);
	$orderAmt = $pay['order_amount'];
	$paidAmt = $pay['paid_amount'];
	$totalOrder += $orderAmt;
	$totalPaid += $paidAmt;
	$totalPending += $pay['remaining_amount'];
}
$wordsReceived = '';
try {
	$wordsReceived = strtoupper(trim($ntw->rp_convertNumToWord(number_format($totalPaid, 2, '.', ''))));
} catch (Exception $e) {
	$wordsReceived = '';
}
$minRows = 8;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo htmlspecialchars($doc_title); ?> | <?php echo htmlspecialchars($partyRow['company_name']); ?></title>
<style>
	* { box-sizing: border-box; }
	html, body { margin: 0; padding: 0; }
	body { font-family: Calibri, Arial, sans-serif; font-size: 13px; color: #222; background: #eee; }
	.toolbar { background: #333; color: #fff; padding: 10px 16px; text-align: center; }
	.toolbar a, .toolbar button {
		display: inline-block; margin: 0 6px; padding: 8px 16px; background: #1a6b8a; color: #fff;
		text-decoration: none; border: 0; border-radius: 3px; cursor: pointer; font-weight: 600;
	}
	.toolbar .btn-muted { background: #666; }
	.sheet {
		width: 210mm;
		min-height: 297mm;
		max-width: 100%;
		margin: 12px auto;
		background: #fff;
		border: 1px solid #999;
		box-shadow: 0 2px 8px rgba(0,0,0,.08);
		display: flex;
		flex-direction: column;
	}
	.sheet-inner { flex: 1 1 auto; display: flex; flex-direction: column; padding: 0; }
	.sheet-body { flex: 1 1 auto; }
	.sheet-footer { flex: 0 0 auto; margin-top: auto; }
	table.main { width: 100%; border-collapse: collapse; }
	table.main td, table.main th { border: 1px solid #595959; padding: 6px 8px; vertical-align: top; }
	.header-img { width: 100%; display: block; padding: 0; margin: 0; }
	.header-fallback {
		padding: 14px 16px; border-bottom: 2px solid #333; background: #fafafa; text-align: center; font-weight: 700; font-size: 16px;
	}
	.title-row { background: #A9A9A9; text-align: center; font-weight: 700; letter-spacing: 1px; font-size: 15px; }
	.th-head { background: #1a6b8a; color: #fff; text-align: center; font-size: 12px; }
	.text-right { text-align: right; }
	.text-center { text-align: center; }
	.status-ok { color: #1e7e34; font-weight: 700; }
	.status-pending { color: #c0392b; font-weight: 700; }
	.footer-img { width: 100%; display: block; }
	.muted { color: #666; font-size: 11px; }
	.blank-row td { height: 22px; border-top: none; }
	@media print {
		body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
		.toolbar, .no-print { display: none !important; }
		.sheet {
			margin: 0 auto;
			border: 0;
			box-shadow: none;
			width: 210mm;
			min-height: 297mm;
		}
		@page { size: A4 portrait; margin: 0; }
	}
</style>
</head>
<body>
<div class="toolbar no-print">
	<button type="button" onclick="window.print();">Print / Save as PDF</button>
	<a class="btn-muted" href="channel_partner_payment.php?party_id=<?php echo (int) $party_id; ?>">Back</a>
	<a class="btn-muted" href="javascript:window.close();">Close</a>
</div>

<div class="sheet">
	<div class="sheet-inner">
		<div class="sheet-body">
			<?php if ($headerImgSrc != '') { ?>
				<img class="header-img" src="<?php echo htmlspecialchars($headerImgSrc); ?>" alt="Header">
			<?php } else { ?>
				<div class="header-fallback">
					<?php echo htmlspecialchars($pi_company != '' ? $pi_company : 'Channel Partner'); ?>
					<div class="muted" style="font-weight:400;margin-top:4px;">Upload header image from PI Format Settings.</div>
				</div>
			<?php } ?>

			<table class="main">
				<tr><td colspan="6" class="title-row"><?php echo $doc_title; ?></td></tr>
				<tr>
					<td colspan="3" style="width:55%;">
						<strong>Party / Buyer</strong><br>
						<span style="font-size:15px;font-weight:700;text-transform:uppercase;"><?php echo htmlspecialchars($partyRow['company_name']); ?></span><br>
						<?php if (!empty($partyRow['person_name'])) { echo htmlspecialchars($partyRow['person_name']) . '<br>'; } ?>
						<?php if ($party_addr != '') { echo htmlspecialchars($party_addr) . '<br>'; } ?>
						<?php if (!empty($partyRow['mobile_no'])) { echo '<strong>Mobile:</strong> ' . htmlspecialchars($partyRow['mobile_no']) . '<br>'; } ?>
						<?php if (!empty($partyRow['email'])) { echo '<strong>Email:</strong> ' . htmlspecialchars($partyRow['email']) . '<br>'; } ?>
						<?php if (!empty($partyRow['gst'])) { echo '<strong>GSTIN:</strong> ' . htmlspecialchars($partyRow['gst']); } ?>
					</td>
					<td colspan="3">
						<strong>Document:</strong> Payment Receive Statement<br>
						<strong>Print Date:</strong> <?php echo date('d-M-Y'); ?><br>
						<strong>From:</strong> <?php echo htmlspecialchars($pi_company); ?><br>
						<?php if ($pi_gst != '') { echo '<strong>Seller GSTIN:</strong> ' . htmlspecialchars($pi_gst) . '<br>'; } ?>
						<?php if ($pi_pan != '') { echo '<strong>PAN:</strong> ' . htmlspecialchars($pi_pan) . '<br>'; } ?>
						<?php if (trim(strip_tags($pi_address)) != '') { ?>
							<br><strong>Address:</strong><br><?php echo html_entity_decode($pi_address); ?>
						<?php } ?>
					</td>
				</tr>
			</table>

			<table class="main" style="margin-top:-1px;">
				<tr class="th-head">
					<th style="width:6%;">Sr</th>
					<th style="width:20%;">Order / Bill No</th>
					<th style="width:12%;">Date</th>
					<th style="width:16%;">Order Amount</th>
					<th style="width:14%;">Received</th>
					<th style="width:14%;">Pending</th>
					<th style="width:18%;">Payment Status</th>
				</tr>
				<?php
				$sr = 0;
				$rowCount = 0;
				if (!empty($orders)) {
					foreach ($orders as $o) {
						$sr++;
						$rowCount++;
						$pay = cp_order_payment_state($o['grand_total'], $o['payment_received_amount']);
						$orderAmt = $pay['order_amount'];
						$paidAmt = $pay['paid_amount'];
						$typeId = isset($o['payment_received_type']) ? (int) $o['payment_received_type'] : 0;
						$typeTxt = ($paidAmt > 0.009 && isset($payTypeLabel[$typeId])) ? ' / ' . $payTypeLabel[$typeId] : '';
						$payDate = '';
						if ($paidAmt > 0.009 && !empty($o['payment_received_date']) && $o['payment_received_date'] != '0000-00-00 00:00:00') {
							$payDate = date('d-m-Y', strtotime($o['payment_received_date']));
						}
						?>
						<tr>
							<td class="text-center"><?php echo $sr; ?></td>
							<td><strong><?php echo htmlspecialchars($o['order_no']); ?></strong></td>
							<td class="text-center"><?php echo date('d-m-Y', strtotime($o['order_date'])); ?></td>
							<td class="text-right"><?php echo number_format($orderAmt, 2); ?></td>
							<td class="text-right"><?php echo number_format($paidAmt, 2); ?></td>
							<td class="text-right" style="color:<?php echo $pay['remaining_amount'] > 0.009 ? '#c0392b' : '#1e7e34'; ?>;font-weight:700;">
								<?php echo number_format($pay['remaining_amount'], 2); ?>
							</td>
							<td class="text-center <?php echo $pay['is_paid'] ? 'status-ok' : 'status-pending'; ?>">
								<?php echo $pay['status_short'] . $typeTxt; ?>
								<?php if ($payDate != '') { ?><div class="muted"><?php echo $payDate; ?></div><?php } ?>
							</td>
						</tr>
						<?php
					}
				} else {
					$rowCount = 1;
				echo '<tr><td colspan="7" class="text-center">No orders found for this party.</td></tr>';
				}
				/* Blank filler rows — SO/PI style full page body */
				for ($i = $rowCount; $i < $minRows; $i++) {
					echo '<tr class="blank-row"><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
				}
				?>
			</table>

			<table class="main" style="margin-top:-1px;">
				<tr>
					<td style="width:55%;background-color:lightgray;">
						<?php if ($pi_gst != '') { ?>
							<strong>GSTIN NO. : <?php echo htmlspecialchars($pi_gst); ?></strong>
						<?php } else { ?>
							<strong>Seller :</strong> <?php echo htmlspecialchars($pi_company); ?>
						<?php } ?>
					</td>
					<td style="width:25%;background-color:lightgray;"><strong>Total Order Amount</strong></td>
					<td class="text-right" style="width:20%;background-color:lightgray;"><strong><?php echo $currency . ' ' . number_format($totalOrder, 2); ?></strong></td>
				</tr>
				<tr>
					<td rowspan="5" style="vertical-align:top;">
						<?php if (trim(strip_tags($pi_bank)) != '') { ?>
							<strong>Bank Details</strong><br>
							<?php echo html_entity_decode($pi_bank); ?>
							<br><br>
						<?php } ?>
						<?php if (trim(strip_tags($pi_terms)) != '') { ?>
							<strong>Terms &amp; Condition :</strong><br>
							<?php echo html_entity_decode($pi_terms); ?>
							<br><br>
						<?php } ?>
						<strong>Amount Received (in words):</strong><br>
						<?php echo $wordsReceived != '' ? htmlspecialchars($wordsReceived) . ' ONLY' : '—'; ?>
						<br><br>
						<span class="muted">This is a computer generated Payment Receive Statement against Sales Order(s).</span>
					</td>
					<td><strong>Total Received</strong></td>
					<td class="text-right status-ok"><strong><?php echo $currency . ' ' . number_format($totalPaid, 2); ?></strong></td>
				</tr>
				<tr>
					<td><strong>Pending Amount</strong></td>
					<td class="text-right status-pending"><strong><?php echo $currency . ' ' . number_format($totalPending, 2); ?></strong></td>
				</tr>
				<tr>
					<td style="background:#f0f0f0;"><strong>Closing Balance</strong></td>
					<td class="text-right" style="background:#f0f0f0;"><strong><?php echo $currency . ' ' . number_format($totalPending, 2); ?> Dr</strong></td>
				</tr>
				<tr>
					<td colspan="2" style="height:48px;vertical-align:bottom;" class="text-right">
						<strong>For, <?php echo htmlspecialchars($pi_company); ?></strong><br>
						<span class="muted">Authorised Signatory</span>
					</td>
				</tr>
			</table>
		</div>

		<div class="sheet-footer">
			<?php if ($footerImgSrc != '') { ?>
				<img class="footer-img" src="<?php echo htmlspecialchars($footerImgSrc); ?>" alt="Footer">
			<?php } ?>
		</div>
	</div>
</div>
</body>
</html>
<?php
require_once 'disconnect.php';
?>
