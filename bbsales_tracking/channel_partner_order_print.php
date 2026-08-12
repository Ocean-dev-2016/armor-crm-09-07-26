<?php
/**
 * Channel Partner Sales Order / Pro Forma Invoice print view.
 * Uses CP PI format from SO/PI Format Settings (images, bank, terms — like Admin Company Master).
 */
$page_id = 565;
$page_slug = 'page_order';
$api_download = (isset($_REQUEST['api_download']) && (string) $_REQUEST['api_download'] === '1');

/* Embedded from API #269 — reuse existing $db, do not reload connect (avoids 500 fatal) */
if (!empty($GLOBALS['cp_order_pdf_embed']) && !empty($GLOBALS['cp_order_pdf_db'])) {
	$db = $GLOBALS['cp_order_pdf_db'];
	$api_download = true;
} else if ($api_download) {
	include('connect_in.php');
} else {
	include('connect.php');
}

$order_id = isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : 0;
if ($order_id <= 0) {
	if ($api_download) {
		header('Content-Type: text/html; charset=utf-8');
		echo '<html><body>Invalid order.</body></html>';
		exit;
	}
	$db->addErrorMessage("Invalid order.");
	$db->rp_location("channel_partner_customer_manage.php");
}

$is_cp = (function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db));
$cp_login_id = $is_cp ? (int) cp_get_login_channel_partner_id() : 0;
$is_admin = (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0);

$ord_r = $db->rp_getData("orders", "*", "id='" . $order_id . "' AND isDelete=0", "", 0);
$ord = $ord_r ? mysqli_fetch_assoc($ord_r) : null;
if (empty($ord)) {
	if ($api_download) {
		header('Content-Type: text/html; charset=utf-8');
		echo '<html><body>Order not found.</body></html>';
		exit;
	}
	$db->addErrorMessage("Order not found.");
	$db->rp_location("channel_partner_customer_manage.php");
}

$is_cp_portal = !empty($ord['channel_partner_order_flag']);
/* api_download=1 is server-side only (App PDF #269) — ownership checked in API before fetch */
if (!$api_download) {
	if ($is_cp) {
		if ((int) $ord['customer_id'] !== $cp_login_id) {
			$db->addErrorMessage("Access denied for this order.");
			$db->rp_location("channel_partner_customer_manage.php");
		}
	} else if (!$is_admin && !$is_cp_portal) {
		$db->addErrorMessage("Access denied.");
		$db->rp_location("dashboard.php");
	}
}

$cp_id = (int) $ord['customer_id'];
$cp_r = $db->rp_getData("executive", "*", "id='" . $cp_id . "' AND isDelete=0", "", 0);
$cp = $cp_r ? mysqli_fetch_assoc($cp_r) : array();

$pi_company = !empty($cp['cp_print_company_name']) ? $cp['cp_print_company_name'] : (isset($cp['company_name']) ? $cp['company_name'] : '');
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
/* App PDF (#269): mPDF needs absolute image paths when HTML is fetched via HTTP */
if ($api_download) {
	if ($headerImgSrc != '') {
		$rp = @realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . $headerImgSrc);
		if ($rp) {
			$headerImgSrc = str_replace('\\', '/', $rp);
		}
	}
	if ($footerImgSrc != '') {
		$rp = @realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . $footerImgSrc);
		if ($rp) {
			$footerImgSrc = str_replace('\\', '/', $rp);
		}
	}
}

$buyer_name = isset($ord['company_name']) ? $ord['company_name'] : '';
$buyer_gst = isset($ord['gst']) ? $ord['gst'] : (isset($ord['name_gstin']) ? $ord['name_gstin'] : '');
$buyer_addr = !empty($ord['billing_address']) ? $ord['billing_address'] : (isset($ord['address']) ? $ord['address'] : '');
$buyer_mobile = '';
$buyer_email = '';

$cp_end_id = isset($ord['channel_partner_customer_id']) ? (int) $ord['channel_partner_customer_id'] : 0;
if ($cp_end_id > 0) {
	$end_r = $db->rp_getData("channel_partner_customer", "*", "id='" . $cp_end_id . "' AND isDelete=0", "", 0);
	if ($end_r && $end = mysqli_fetch_assoc($end_r)) {
		$buyer_name = !empty($end['company_name']) ? $end['company_name'] : (!empty($end['person_name']) ? $end['person_name'] : $buyer_name);
		$buyer_gst = !empty($end['gst']) ? $end['gst'] : $buyer_gst;
		$buyer_mobile = isset($end['mobile_no']) ? $end['mobile_no'] : '';
		$buyer_email = isset($end['email']) ? $end['email'] : '';
		$addrParts = array_filter(array(
			isset($end['address']) ? $end['address'] : '',
			isset($end['city']) ? $end['city'] : '',
			isset($end['state']) ? $end['state'] : '',
			isset($end['pincode']) ? $end['pincode'] : '',
			isset($end['country']) ? $end['country'] : ''
		));
		if (!empty($addrParts)) {
			$buyer_addr = implode(', ', $addrParts);
		}
	}
}

$doc_title = "PRO FORMA INVOICE";
$gst_on = isset($ord['gst_apply_flag']) ? (int) $ord['gst_apply_flag'] : 1;

$items = array();
$items_r = $db->rp_getData("order_product_item", "*", "order_id='" . $order_id . "' AND isDelete=0", "id ASC", 0);
if (!$items_r || mysqli_num_rows($items_r) == 0) {
	$items_r = $db->rp_getData("order_product_item", "*", "order_id='" . $order_id . "'", "id ASC", 0);
}
if ($items_r) {
	while ($it = mysqli_fetch_assoc($items_r)) {
		$items[] = $it;
	}
}

$currency = defined('CURR') ? CURR : 'Rs.';
$order_date = (!empty($ord['order_date']) && $ord['order_date'] != '0000-00-00' && $ord['order_date'] != '0000-00-00 00:00:00')
	? date('d-M-Y', strtotime($ord['order_date']))
	: '';

$sub_total = 0;
$total_qty = 0;
foreach ($items as $it) {
	$sub_total += isset($it['totalprice']) ? (float) $it['totalprice'] : ((float) $it['pro_qty'] * (float) $it['price']);
	$total_qty += isset($it['pro_qty']) ? (float) $it['pro_qty'] : 0;
}

$igst_amount = isset($ord['igst_amount']) ? (float) $ord['igst_amount'] : 0;
$cd = isset($ord['cash_discount_amount']) ? (float) $ord['cash_discount_amount'] : 0;
$ad = isset($ord['additional_discount_amount']) ? (float) $ord['additional_discount_amount'] : 0;
$taxable = $sub_total - $cd - $ad;
$grand = isset($ord['grand_total']) && $ord['grand_total'] !== '' && $ord['grand_total'] !== null
	? (float) $ord['grand_total']
	: ($taxable + ($gst_on ? $igst_amount : 0));

$saved_msg = isset($_REQUEST['saved']) && $_REQUEST['saved'] == '1';
$auto_print = isset($_REQUEST['p']) && $_REQUEST['p'] == '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo htmlspecialchars($doc_title); ?> | <?php echo htmlspecialchars(isset($ord['order_no']) ? $ord['order_no'] : ''); ?></title>
<style>
	body { font-family: Calibri, Arial, sans-serif; font-size: 13px; color: #222; margin: 0; background: #eee; }
	.toolbar { background: #333; color: #fff; padding: 10px 16px; text-align: center; }
	.toolbar a, .toolbar button {
		display: inline-block; margin: 0 6px; padding: 8px 16px; background: #1a6b8a; color: #fff;
		text-decoration: none; border: 0; border-radius: 3px; cursor: pointer; font-weight: 600;
	}
	.toolbar .btn-muted { background: #666; }
	.ok-banner { background: #dff0d8; color: #3c763d; padding: 10px 16px; text-align: center; font-weight: 600; }
	.sheet {
		width: 210mm; max-width: 100%; margin: 16px auto; background: #fff;
		border: 1px solid #999; box-shadow: 0 2px 8px rgba(0,0,0,.08);
	}
	.sheet-inner { padding: 0; }
	table.main { width: 100%; border-collapse: collapse; }
	table.main td, table.main th { border: 1px solid #595959; padding: 6px 8px; vertical-align: top; }
	.header-img { width: 100%; display: block; padding: 0; margin: 0; }
	.header-fallback {
		padding: 14px 16px; border-bottom: 2px solid #333; background: #fafafa; text-align: center; font-weight: 700; font-size: 16px;
	}
	.header-note { padding: 6px 12px; font-size: 12px; border-bottom: 1px solid #ccc; white-space: pre-wrap; }
	.title-row { background: #A9A9A9; text-align: center; font-weight: 700; letter-spacing: 1px; font-size: 15px; }
	.th-head { background: #1a6b8a; color: #fff; text-align: center; font-size: 12px; }
	.text-right { text-align: right; }
	.text-center { text-align: center; }
	.footer-img { width: 100%; display: block; }
	.footer-block { font-size: 12px; line-height: 1.4; padding: 10px 12px; border-top: 1px solid #999; background: #fafafa; }
	.muted { color: #666; font-size: 11px; }
	@media print {
		body { background: #fff; }
		.toolbar, .ok-banner, .no-print { display: none !important; }
		.sheet { margin: 0; border: 0; box-shadow: none; width: 100%; }
	}
</style>
</head>
<body>
<?php if (!$api_download) { ?>
<div class="toolbar no-print">
	<button type="button" onclick="window.print();">Print</button>
	<a href="channel_partner_order_simple.php?cp_mode=customer">New Order</a>
	<a class="btn-muted" href="channel_partner_customer_manage.php">My Customers</a>
	<a class="btn-muted" href="channel_partner_print_settings.php">PI Format Settings</a>
</div>
<?php if ($saved_msg) { ?>
<div class="ok-banner no-print">Order saved successfully. You can print this Sales Order / PI now.</div>
<?php } ?>
<?php } ?>

<div class="sheet">
	<div class="sheet-inner">
		<?php if ($headerImgSrc != '') { ?>
			<img class="header-img" src="<?php echo htmlspecialchars($headerImgSrc); ?>" alt="Header">
		<?php } else { ?>
			<div class="header-fallback">
				<?php echo htmlspecialchars($pi_company != '' ? $pi_company : 'Channel Partner'); ?>
				<?php if ($headerImgSrc == '') { ?>
					<div class="muted" style="font-weight:400;margin-top:4px;">Upload header image from PI Format Settings.</div>
				<?php } ?>
			</div>
		<?php } ?>

		<table class="main">
			<tr><td colspan="6" class="title-row"><?php echo $doc_title; ?></td></tr>
			<tr>
				<td colspan="3" style="width:55%;">
					<strong>Buyer</strong><br>
					<span style="font-size:15px;font-weight:700;text-transform:uppercase;"><?php echo htmlspecialchars($buyer_name); ?></span><br>
					<?php if ($buyer_addr != '') { echo nl2br(htmlspecialchars($buyer_addr)) . '<br>'; } ?>
					<?php if ($buyer_mobile != '') { echo '<strong>Mobile:</strong> ' . htmlspecialchars($buyer_mobile) . '<br>'; } ?>
					<?php if ($buyer_email != '') { echo '<strong>Email:</strong> ' . htmlspecialchars($buyer_email) . '<br>'; } ?>
					<?php if ($buyer_gst != '') { echo '<strong>GSTIN:</strong> ' . htmlspecialchars($buyer_gst); } ?>
				</td>
				<td colspan="3">
					<strong>Order No.:</strong> <?php echo htmlspecialchars($ord['order_no']); ?><br>
					<strong>Order Date:</strong> <?php echo htmlspecialchars($order_date); ?><br>
					<strong>Pricing:</strong> <?php echo $gst_on ? 'With GST' : 'Without GST'; ?><br>
					<strong>From:</strong> <?php echo htmlspecialchars($pi_company); ?><br>
					<?php if ($pi_gst != '') { echo '<strong>Seller GSTIN:</strong> ' . htmlspecialchars($pi_gst) . '<br>'; } ?>
					<?php if ($pi_pan != '') { echo '<strong>PAN:</strong> ' . htmlspecialchars($pi_pan) . '<br>'; } ?>
					<?php if (trim(strip_tags($pi_address)) != '') { ?>
						<br><strong>Address:</strong><br><?php echo html_entity_decode($pi_address); ?>
					<?php } ?>
					<?php if (!empty($ord['shipping_address'])) { ?>
						<br><strong>Shipping:</strong><br><?php echo nl2br(htmlspecialchars($ord['shipping_address'])); ?>
					<?php } ?>
				</td>
			</tr>
		</table>

		<table class="main" style="margin-top:-1px;">
			<tr class="th-head">
				<th style="width:6%;">Sr</th>
				<th style="width:40%;">Product</th>
				<th style="width:12%;">HSN</th>
				<th style="width:10%;">Qty</th>
				<th style="width:14%;">Rate</th>
				<th style="width:18%;">Amount</th>
			</tr>
			<?php
			$sr = 0;
			if (!empty($items)) {
				foreach ($items as $it) {
					$sr++;
					$pro_name = !empty($it['pro_name']) ? $it['pro_name'] : $db->rp_getValue("product", "name", "id='" . (int) $it['pro_id'] . "'");
					$size = $db->rp_getValue("weight", "name", "id='" . (int) $it['weight_id'] . "' AND isDelete=0");
					$catno = $db->rp_getValue("product_weight_price", "catno", "product_id='" . (int) $it['pro_id'] . "' AND weight_id='" . (int) $it['weight_id'] . "'", 0);
					$hsn = $db->rp_getValue("product", "hsn_code", "id='" . (int) $it['pro_id'] . "' AND isDelete=0", 0);
					$gst_pct = $db->rp_getValue("product", "igst", "id='" . (int) $it['pro_id'] . "' AND isDelete=0", 0);
					$qty = isset($it['pro_qty']) ? (float) $it['pro_qty'] : 0;
					$rate = isset($it['price']) ? (float) $it['price'] : 0;
					$amt = isset($it['totalprice']) ? (float) $it['totalprice'] : ($qty * $rate);
					$label = $pro_name;
					if ($size != '') {
						$label .= ' - ' . $size;
					}
					if ($catno != '') {
						$label .= ' (#' . $catno . ')';
					}
					?>
					<tr>
						<td class="text-center"><?php echo $sr; ?></td>
						<td>
							<?php echo htmlspecialchars($label); ?>
							<?php if ($gst_on && $gst_pct !== '' && $gst_pct !== null) { ?>
								<div class="muted">GST <?php echo htmlspecialchars($gst_pct); ?>%</div>
							<?php } ?>
						</td>
						<td class="text-center"><?php echo htmlspecialchars($hsn); ?></td>
						<td class="text-center"><?php echo $qty; ?></td>
						<td class="text-right"><?php echo number_format($rate, 2); ?></td>
						<td class="text-right"><?php echo number_format($amt, 2); ?></td>
					</tr>
					<?php
				}
			} else {
				echo '<tr><td colspan="6" class="text-center">No items</td></tr>';
			}
			?>
			<tr>
				<td colspan="3" class="text-right"><strong>Total Qty</strong></td>
				<td class="text-center"><strong><?php echo $total_qty; ?></strong></td>
				<td></td>
				<td class="text-right"><strong><?php echo $currency . ' ' . number_format($sub_total, 2); ?></strong></td>
			</tr>
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
				<td style="width:25%;background-color:lightgray;"><strong>Sub Total</strong></td>
				<td class="text-right" style="width:20%;background-color:lightgray;"><strong><?php echo $currency . ' ' . number_format($sub_total, 2); ?></strong></td>
			</tr>
			<tr>
				<td rowspan="8" style="vertical-align:top;">
					<?php if (trim(strip_tags($pi_bank)) != '') { ?>
						<strong>Bank Details</strong><br>
						<?php echo html_entity_decode($pi_bank); ?>
						<br><br>
					<?php } ?>
					<?php if (trim(strip_tags($pi_terms)) != '') { ?>
						<strong>Terms &amp; Condition :</strong><br>
						<?php echo html_entity_decode($pi_terms); ?>
						<br>
					<?php } ?>
					<span class="muted" style="color:red;"><b>This Pro Forma Invoice is valid for 7 days.</b></span>
					<?php if (!empty($ord['remarks'])) { ?>
						<br><br><strong>Note:</strong><br><?php echo nl2br(htmlspecialchars($ord['remarks'])); ?>
					<?php } ?>
				</td>
				<td><strong>Taxable Amount</strong></td>
				<td class="text-right"><?php echo $currency . ' ' . number_format($taxable, 2); ?></td>
			</tr>
			<?php if ($cd > 0) { ?>
			<tr>
				<td><strong>Cash Discount</strong></td>
				<td class="text-right"><?php echo $currency . ' ' . number_format($cd, 2); ?></td>
			</tr>
			<?php } ?>
			<?php if ($ad > 0) { ?>
			<tr>
				<td><strong>Additional Discount</strong></td>
				<td class="text-right"><?php echo $currency . ' ' . number_format($ad, 2); ?></td>
			</tr>
			<?php } ?>
			<?php if ($gst_on && $igst_amount > 0) { ?>
			<tr>
				<td><strong>GST / IGST</strong></td>
				<td class="text-right"><?php echo $currency . ' ' . number_format($igst_amount, 2); ?></td>
			</tr>
			<?php } else if (!$gst_on) { ?>
			<tr>
				<td><strong>GST</strong></td>
				<td class="text-right">Not Applied (Without GST)</td>
			</tr>
			<?php } ?>
			<tr>
				<td style="background:#f0f0f0;"><strong>Grand Total</strong></td>
				<td class="text-right" style="background:#f0f0f0;"><strong><?php echo $currency . ' ' . number_format($grand, 2); ?></strong></td>
			</tr>
		</table>

		<?php if ($footerImgSrc != '') { ?>
			<img class="footer-img" src="<?php echo htmlspecialchars($footerImgSrc); ?>" alt="Footer">
		<?php } ?>
	</div>
</div>

<?php if ($auto_print) { ?>
<script>window.onload = function () { window.print(); };</script>
<?php } ?>
</body>
</html>
<?php include("disconnect.php"); ?>
