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
$buyer_state = '';

$cp_end_id = isset($ord['channel_partner_customer_id']) ? (int) $ord['channel_partner_customer_id'] : 0;
if ($cp_end_id > 0) {
	$end_r = $db->rp_getData("channel_partner_customer", "*", "id='" . $cp_end_id . "' AND isDelete=0", "", 0);
	if ($end_r && $end = mysqli_fetch_assoc($end_r)) {
		$buyer_name = !empty($end['company_name']) ? $end['company_name'] : (!empty($end['person_name']) ? $end['person_name'] : $buyer_name);
		$buyer_gst = !empty($end['gst']) ? $end['gst'] : $buyer_gst;
		$buyer_mobile = isset($end['mobile_no']) ? $end['mobile_no'] : '';
		$buyer_email = isset($end['email']) ? $end['email'] : '';
		$buyer_state = isset($end['state']) ? trim($end['state']) : '';
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
if ($items_r) {
	while ($it = mysqli_fetch_assoc($items_r)) {
		$items[] = $it;
	}
}

$currency = defined('CURR') ? CURR : 'Rs.';
$order_date = (!empty($ord['order_date']) && $ord['order_date'] != '0000-00-00' && $ord['order_date'] != '0000-00-00 00:00:00')
	? date('d-M-Y', strtotime($ord['order_date']))
	: '';

if (!function_exists('cp_pi_line_calc')) {
	/**
	 * Rate = original (before discount). Amount = after discount.
	 * Discounted Value = Rate * Qty * Disc%  (same as Admin PI).
	 */
	function cp_pi_line_calc($it)
	{
		$qty = isset($it['pro_qty']) ? (float) $it['pro_qty'] : 0;
		$unitNet = isset($it['unitprice']) ? (float) $it['unitprice'] : 0;
		$orig = isset($it['original_price']) ? (float) $it['original_price'] : 0;
		$discPct = isset($it['discount']) ? (float) $it['discount'] : 0;
		$discAmt = isset($it['discount_amount']) ? (float) $it['discount_amount'] : 0;
		$lineAmt = isset($it['totalprice']) ? (float) $it['totalprice'] : 0;

		if ($orig <= 0.00001 && $discPct > 0 && $discPct < 100 && $unitNet > 0) {
			$orig = $unitNet / (1 - ($discPct / 100));
		}
		if ($orig <= 0.00001 && isset($it['price']) && (float) $it['price'] > 0) {
			$orig = (float) $it['price'];
		}
		if ($orig <= 0.00001) {
			$orig = $unitNet;
		}
		if ($discAmt <= 0.00001 && $discPct > 0 && $orig > 0) {
			$discAmt = ($orig * $discPct) / 100;
		}
		if ($discPct <= 0.00001 && $discAmt > 0 && $orig > 0) {
			$discPct = ($discAmt / $orig) * 100;
		}
		if ($discAmt <= 0.00001 && $orig > 0 && $unitNet > 0 && ($orig - $unitNet) > 0.009) {
			$discAmt = $orig - $unitNet;
			if ($discPct <= 0.00001) {
				$discPct = ($discAmt / $orig) * 100;
			}
		}
		if ($lineAmt <= 0.00001) {
			$net = ($unitNet > 0) ? $unitNet : max(0, $orig - $discAmt);
			$lineAmt = $qty * $net;
		}
		$discTotal = $discAmt * $qty;
		if ($discTotal <= 0.00001 && $orig > 0 && $qty > 0) {
			$mrpLine = $orig * $qty;
			if ($mrpLine - $lineAmt > 0.009) {
				$discTotal = $mrpLine - $lineAmt;
				if ($discPct <= 0.00001 && $mrpLine > 0) {
					$discPct = ($discTotal / $mrpLine) * 100;
				}
			}
		}
		$mrpLine = $orig * $qty;
		return array(
			'qty' => $qty,
			'rate' => round($orig, 2),
			'disc_pct' => round($discPct, 2),
			'disc_value' => round($discTotal, 2),
			'amount' => round($lineAmt, 2),
			'mrp' => round($mrpLine, 2),
		);
	}
}

$sub_total = 0;
$total_qty = 0;
$igst_amount = 0;
$total_item_discount = 0;
$total_mrp_amount = 0;
foreach ($items as $k => $it) {
	$ln = cp_pi_line_calc($it);
	$items[$k]['_pi'] = $ln;
	$sub_total += $ln['amount'];
	$total_qty += $ln['qty'];
	$total_item_discount += $ln['disc_value'];
	$total_mrp_amount += $ln['mrp'];
	if ($gst_on) {
		$lineGst = isset($it['igst_amount']) ? (float) $it['igst_amount'] : 0;
		if ($lineGst <= 0.00001) {
			$pct = (float) $db->rp_getValue("product", "igst", "id='" . (int) $it['pro_id'] . "' AND isDelete=0", 0);
			if ($pct > 0) {
				$lineGst = ($ln['amount'] * $pct) / 100;
			}
		}
		$igst_amount += $lineGst;
	}
}
$sub_total = round($sub_total, 2);
$total_item_discount = round($total_item_discount, 2);
$total_mrp_amount = round($total_mrp_amount, 2);
$overall_discount_per = ($total_mrp_amount > 0.00001)
	? round(($total_item_discount / $total_mrp_amount) * 100, 2)
	: 0;

$cd = isset($ord['cash_discount_amount']) ? (float) $ord['cash_discount_amount'] : 0;
$ad = isset($ord['additional_discount_amount']) ? (float) $ord['additional_discount_amount'] : 0;
$taxable = round($sub_total - $cd - $ad, 2);
if ($taxable < 0) {
	$taxable = 0;
}
if (!$gst_on) {
	$igst_amount = 0;
	$grand = $taxable;
} else {
	if ($igst_amount <= 0 && isset($ord['igst_amount']) && (float) $ord['igst_amount'] > 0) {
		$igst_amount = (float) $ord['igst_amount'];
	}
	/* GST on taxable (after cash / additional discount) */
	if ($sub_total > 0.00001 && ($cd > 0.009 || $ad > 0.009) && $igst_amount > 0) {
		$igst_amount = round(($igst_amount * $taxable) / $sub_total, 2);
	}
	$igst_amount = round($igst_amount, 2);
	$grand = round($taxable + $igst_amount, 2);
}

/* Place of supply = CP Customer. Gujarat (GSTIN 24) = CGST+SGST; any other state = IGST.
 * Do not match against seller GSTIN — a wrong/other CP print GST was splitting CGST/SGST for Maharashtra. */
if (!function_exists('cp_pi_is_gujarat_state')) {
	function cp_pi_is_gujarat_state($state)
	{
		$s = strtolower(trim((string) $state));
		if ($s === '') {
			return false;
		}
		$s = preg_replace('/[^a-z]/', '', $s);
		return ($s === 'gujarat' || $s === 'gj' || strpos($s, 'gujarat') === 0);
	}
}
$GUJARAT_GST_CODE = '24';
$buyer_gst_clean = strtoupper(preg_replace('/\s+/', '', (string) $buyer_gst));
$buyer_gst_code = (strlen($buyer_gst_clean) >= 2 && ctype_digit(substr($buyer_gst_clean, 0, 2))) ? substr($buyer_gst_clean, 0, 2) : '';
$gst_same_state = true;
if ($buyer_gst_code !== '') {
	$gst_same_state = ($buyer_gst_code === $GUJARAT_GST_CODE);
} else if ($buyer_state !== '') {
	$gst_same_state = cp_pi_is_gujarat_state($buyer_state);
}

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
	.th-head { background: #1a6b8a; color: #fff; text-align: center; font-size: 11px; }
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
				<th style="width:5%;">Sr</th>
				<th style="width:28%;">Product</th>
				<th style="width:10%;">HSN</th>
				<th style="width:8%;">Qty</th>
				<th style="width:12%;">Rate</th>
				<th style="width:9%;">Discount %</th>
				<th style="width:13%;">Discounted Value</th>
				<th style="width:15%;">Amount</th>
			</tr>
			<?php
			$sr = 0;
			if (!empty($items)) {
				foreach ($items as $it) {
					$sr++;
					$ln = isset($it['_pi']) ? $it['_pi'] : cp_pi_line_calc($it);
					$pro_name = !empty($it['pro_name']) ? $it['pro_name'] : $db->rp_getValue("product", "name", "id='" . (int) $it['pro_id'] . "'");
					$size = $db->rp_getValue("weight", "name", "id='" . (int) $it['weight_id'] . "' AND isDelete=0");
					$catno = $db->rp_getValue("product_weight_price", "catno", "product_id='" . (int) $it['pro_id'] . "' AND weight_id='" . (int) $it['weight_id'] . "'", 0);
					$hsn = $db->rp_getValue("product", "hsn_code", "id='" . (int) $it['pro_id'] . "' AND isDelete=0", 0);
					$gst_pct = $db->rp_getValue("product", "igst", "id='" . (int) $it['pro_id'] . "' AND isDelete=0", 0);
					$qty = $ln['qty'];
					$qtyShow = (abs($qty - round($qty)) < 0.00001) ? (string) (int) round($qty) : number_format($qty, 2);
					$label = $pro_name;
					if ($size != '' && stripos($label, $size) === false) {
						$label .= ' - ' . $size;
					}
					if ($catno != '' && strpos($label, '#' . $catno) === false && strpos($label, $catno) === false) {
						$label .= ' (#' . $catno . ')';
					}
					?>
					<tr>
						<td class="text-center"><?php echo $sr; ?></td>
						<td>
							<?php echo htmlspecialchars($label); ?>
							<?php if ($gst_on && $gst_pct !== '' && $gst_pct !== null) { ?>
								<div class="muted"><?php echo $gst_same_state ? 'GST' : 'IGST'; ?> <?php echo htmlspecialchars($gst_pct); ?>%</div>
							<?php } ?>
						</td>
						<td class="text-center"><?php echo htmlspecialchars($hsn); ?></td>
						<td class="text-center"><?php echo $qtyShow; ?></td>
						<td class="text-right"><?php echo number_format($ln['rate'], 2); ?></td>
						<td class="text-center"><?php echo number_format($ln['disc_pct'], 2); ?></td>
						<td class="text-right"><?php echo number_format($ln['disc_value'], 2); ?></td>
						<td class="text-right"><?php echo number_format($ln['amount'], 2); ?></td>
					</tr>
					<?php
				}
			} else {
				echo '<tr><td colspan="8" class="text-center">No items</td></tr>';
			}
			$qtyTotShow = (abs($total_qty - round($total_qty)) < 0.00001) ? (string) (int) round($total_qty) : number_format($total_qty, 2);
			?>
			<tr>
				<td colspan="3" class="text-right"><strong>Total Qty</strong></td>
				<td class="text-center"><strong><?php echo $qtyTotShow; ?></strong></td>
				<td></td>
				<td></td>
				<td class="text-right"><strong><?php echo number_format($total_item_discount, 2); ?></strong></td>
				<td class="text-right"><strong><?php echo $currency . ' ' . number_format($sub_total, 2); ?></strong></td>
			</tr>
		</table>
		<?php
		/* Rowspan covers: Sub Total + optional CD/AD + Taxable + GST row(s) + Grand Total */
		$gst_footer_rows = 1;
		if ($gst_on && $gst_same_state && $igst_amount > 0) {
			$gst_footer_rows = 2;
		}
		$terms_rowspan = 1;
		if ($cd > 0) {
			$terms_rowspan++;
		}
		if ($ad > 0) {
			$terms_rowspan++;
		}
		$terms_rowspan++; /* Taxable Amount */
		$terms_rowspan += $gst_footer_rows;
		$terms_rowspan++; /* Grand Total */
		$discPctLabel = rtrim(rtrim(number_format($overall_discount_per, 2, '.', ''), '0'), '.');
		if ($discPctLabel === '') {
			$discPctLabel = '0';
		}
		?>
		<table class="main" style="margin-top:-1px;">
			<tr>
				<td style="width:52%;background-color:lightgray;">
					<?php if ($pi_gst != '') { ?>
						<strong>GSTIN NO. : <?php echo htmlspecialchars($pi_gst); ?></strong>
					<?php } else { ?>
						<strong>Seller :</strong> <?php echo htmlspecialchars($pi_company); ?>
					<?php } ?>
				</td>
				<td style="width:26%;background-color:lightgray;"><strong>Discount (<?php echo $discPctLabel; ?>%)</strong></td>
				<td class="text-right" style="width:22%;background-color:lightgray;"><strong><?php echo $currency . ' ' . number_format($total_item_discount, 2); ?></strong></td>
			</tr>
			<tr>
				<td rowspan="<?php echo (int) $terms_rowspan; ?>" style="vertical-align:top;">
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
				<td><strong>Sub Total</strong></td>
				<td class="text-right"><strong><?php echo $currency . ' ' . number_format($sub_total, 2); ?></strong></td>
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
			<tr>
				<td><strong>Taxable Amount</strong></td>
				<td class="text-right"><?php echo $currency . ' ' . number_format($taxable, 2); ?></td>
			</tr>
			<?php if ($gst_on) { ?>
				<?php if ($gst_same_state && $igst_amount > 0) { ?>
			<tr>
				<td><strong>CGST</strong></td>
				<td class="text-right"><?php echo $currency . ' ' . number_format($igst_amount / 2, 2); ?></td>
			</tr>
			<tr>
				<td><strong>SGST</strong></td>
				<td class="text-right"><?php echo $currency . ' ' . number_format($igst_amount / 2, 2); ?></td>
			</tr>
				<?php } else { ?>
			<tr>
				<td><strong>IGST</strong></td>
				<td class="text-right"><?php echo $currency . ' ' . number_format($igst_amount, 2); ?></td>
			</tr>
				<?php } ?>
			<?php } else { ?>
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
