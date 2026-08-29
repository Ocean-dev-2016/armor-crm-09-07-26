<?php
$page_id = 566;
$page_slug = 'page_order_ajax';
require_once("connect_in.php");
include("../include/no_to_word.php");

$ntw = new NumToWord_RP;
$quotation_id	= $_REQUEST['quotation_id'];
$cart_detail_r 	= $db->rp_getData("quotation_detail", "*", "id='" . $quotation_id . "'", "", 0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);
$order_date = ($cart_detail_d['order_date'] != "0000-00-00 00:00:00") ? date("d-m-Y", strtotime($cart_detail_d['order_date'])) : "";
$type_of_customer = $db->rp_getValue("executive", "type_of_executive", "id =  '" . $cart_detail_d['customer_id'] . "' ", 0);
if ($type_of_customer == 3) {
	$customer_id = $db->rp_getValue("executive", "dealer_distributor_id", "id= '" . $cart_detail_d['customer_id'] . "' ", 0);
} else if ($type_of_customer == 2) {
	$customer_id = $db->rp_getValue("executive", "super_stockist_id", "id = '" . $cart_detail_d['customer_id'] . "' ");
} else {
	$customer_id = "";
}


if (isset($cart_detail_d['customer_id']) && !empty($cart_detail_d['customer_id'])) {
	$customer_address = $db->rp_getData("executive", "address,company_name,zip", "id = '" . $cart_detail_d['customer_id'] . "' ", "");
	$customer_address_d = mysqli_fetch_assoc($customer_address);
}
// if(isset($_REQUEST['quotation_id']) && !empty($_REQUEST['quotation_id']))
// {
// 	$cat_id = $db->rp_getValue("quotation_product_item","top_cat_id","quotation_id = '".$_REQUEST['quotation_id']."' ",0);
// }
// if ($cat_id == 1) 
// {
// 	$header_image = SITEURL.'images/category_sheetal_icecream.jpg';
// }
// else if($cat_id == 2)
// {
// 	$header_image = SITEURL.'images/category_jadore_ice_cream.jpg';
// }
// else
// {
// 	$header_image = SITEURL.'images/sheetal_ice_creame.jpg';
// }




$company_detail_r = $db->rp_getData("company_master", "*", "id='" . $cart_detail_d['type_of_company'] . "' AND isDelete=0", "", 0);

$company_detail_d = mysqli_fetch_assoc($company_detail_r);

$headerImgHeight = defined('HEADER_IMAGE_HEIGHT') ? (int) HEADER_IMAGE_HEIGHT : 184;
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Quotation Print</title>
	<style>
		@page {
			size: A4 portrait;
			margin: 5mm;
		}

		* { box-sizing: border-box; }

		html, body {
			margin: 0;
			padding: 0;
			background: #fff;
			font-family: Arial, Helvetica, sans-serif;
			color: #000;
			font-size: 10px;
			line-height: 1.25;
			-webkit-print-color-adjust: exact;
			print-color-adjust: exact;
		}

		.quote-wrap {
			width: 100%;
			max-width: 200mm;
			margin: 0 auto;
			padding: 0;
			background: #fff;
		}

		table {
			border: 1px solid #595959;
			border-collapse: collapse;
			font-size: 10px;
			width: 100% !important;
			max-width: 100%;
			background: #fff;
			margin: 0;
			table-layout: fixed;
		}

		table, td, th { border: 1px solid #595959; }

		td, th {
			padding: 2px 3px !important;
			height: auto !important;
			vertical-align: top;
			word-wrap: break-word;
			overflow-wrap: break-word;
			line-height: 1.2;
		}

		img {
			max-width: 100%;
			height: auto;
			display: block;
		}

		.header-cell,
		.footer-cell {
			padding: 0 !important;
			margin: 0 !important;
			line-height: 0 !important;
			font-size: 0 !important;
			text-align: center;
			vertical-align: middle;
			height: auto !important;
			border: 1px solid #595959 !important;
		}

		.header-cell {
			border-bottom: 1px solid #595959 !important;
		}

		.footer-cell {
			border-top: 1px solid #595959 !important;
		}

		.header-img,
		.footer-img {
			width: 100% !important;
			height: auto !important;
			max-height: <?= $headerImgHeight ?>px !important;
			object-fit: contain !important;
			object-position: center center;
			display: block !important;
			padding: 0 !important;
			margin: 0 auto !important;
			border: 0;
		}

		.product-img {
			width: 28px !important;
			height: 28px !important;
			object-fit: contain;
			margin: 0 auto;
		}

		.product-filler-row td {
			height: 28px !important;
			padding: 4px 3px !important;
		}

		.product-items-table td,
		.product-items-table th {
			vertical-align: middle !important;
		}

		.product-items-table .model {
			text-align: left !important;
			vertical-align: middle !important;
		}

		.product-items-table .product-item-row td,
		.product-items-table .product-filler-row td {
			height: 30px !important;
			vertical-align: middle !important;
		}

		.product-items-table .image-width {
			vertical-align: middle !important;
			text-align: center !important;
		}

		.product-items-table .product-img {
			margin: 0 auto;
		}

		.quote-main-body > table,
		.quote-main-body .product-items-table,
		.quote-main-body .summary-outer,
		.quote-main-body .quote-footer-table {
			border-left: 1px solid #595959 !important;
			border-right: 1px solid #595959 !important;
		}

		.quote-footer-table {
			margin-top: 0 !important;
			border: 1px solid #595959 !important;
			border-top: none !important;
			width: 100% !important;
			border-collapse: collapse !important;
		}

		.quote-footer-table td.footer-cell {
			border-left: 1px solid #595959 !important;
			border-right: 1px solid #595959 !important;
			border-bottom: 1px solid #595959 !important;
			border-top: none !important;
		}

		.summary-outer { table-layout: fixed !important; width: 100% !important; border-collapse: collapse; }
		.summary-outer > tbody > tr > td { border: 1px solid #595959; vertical-align: top; }

		.terms-cell {
			width: 58%;
			padding: 3px 5px !important;
			font-size: 9px !important;
			line-height: 1.2 !important;
		}

		.totals-cell { width: 42%; padding: 0 !important; }

		.totals-inner {
			width: 100% !important;
			border: none !important;
			border-collapse: collapse;
			table-layout: fixed !important;
			margin: 0 !important;
		}

		.totals-inner td {
			border: 1px solid #595959;
			padding: 2px 4px !important;
			font-size: 10px !important;
			height: auto !important;
		}

		.totals-inner tr:first-child td { border-top: none; }
		.totals-inner tr td:first-child { border-left: none; }
		.totals-inner tr td:last-child { border-right: none; }
		.totals-inner tr:last-child td { border-bottom: none; }

		.tot-label { width: 45%; text-align: left; font-weight: bold; }
		.tot-pct { width: 18%; text-align: center; font-weight: bold; }
		.tot-amt { width: 37%; text-align: right; font-weight: bold; }

		.text-center { text-align: center !important; }
		.text-right { text-align: right !important; }
		.text-left { text-align: left !important; }

		.no-border-left { border-left: hidden; }
		.no-border-right { border-right: hidden; }
		.no-border-bottom { border-bottom: hidden !important; }
		.no-border-top { border-top: hidden !important; }
		.border td { border-bottom: hidden !important; }
		.color { background: #D3D3D3; }
		.font-size td { font-size: 10px !important; }
		.image-width { width: 6% !important; }
		.bg-gray { background-color: #E5E5E5 !important; }
		.font-13 { font-size: 9px !important; line-height: 1.2 !important; }

		h5 { margin: 1px 0 !important; font-size: 11px !important; }
		p { margin: 0 0 1px 0 !important; font-size: 9px !important; line-height: 1.2 !important; }

		.title-bar td {
			padding: 3px !important;
			font-size: 11px !important;
		}

		.no-print-empty { display: none !important; }

		@media print {
			html, body {
				margin: 0 !important;
				padding: 0 !important;
				width: 100% !important;
				height: auto !important;
			}

			.quote-wrap {
				max-width: 100% !important;
				width: 100% !important;
				margin: 0 !important;
				transform: none !important;
			}

			.quote-main-body {
				page-break-after: always;
			}

			.quote-suggest-body {
				page-break-before: always !important;
				break-before: page;
			}

			.quote-main-body .summary-outer {
				page-break-inside: avoid !important;
			}

			.qp-suggest-print-section {
				page-break-inside: auto;
			}

			.qp-suggest-print-grid tr {
				page-break-inside: auto !important;
			}

			.qp-suggest-print-cell {
				page-break-inside: avoid !important;
			}

			.no-print-empty { display: none !important; }

			.header-img,
			.footer-img {
				height: auto !important;
				max-height: <?= $headerImgHeight ?>px !important;
				width: 100% !important;
				object-fit: contain !important;
			}

			.product-img {
				width: 24px !important;
				height: 24px !important;
			}

			.product-filler-row td,
			.product-items-table .product-item-row td {
				height: 28px !important;
				vertical-align: middle !important;
			}

			.header-cell,
			.footer-cell {
				border-left: 1px solid #595959 !important;
				border-right: 1px solid #595959 !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.header-cell {
				border-top: 1px solid #595959 !important;
			}

			.footer-cell {
				border-bottom: 1px solid #595959 !important;
			}

			.quote-main-body > table,
			.quote-main-body .product-items-table,
			.quote-main-body .summary-outer,
			.quote-main-body .quote-footer-table {
				border-left: 1px solid #595959 !important;
				border-right: 1px solid #595959 !important;
			}

			.quote-footer-wrap {
				page-break-inside: avoid;
			}
		}
	</style>
	<?php
	require_once('../include/quotation_pi_suggest_products_helper.php');
	echo armor_quotation_pi_suggest_styles();
	?>
</head>

<body class="print-a4">
	<div class="quote-wrap">
	<div class="quote-main-body">
	<table>
		<tbody class="<?= isset($cl) ? $cl : ''; ?>">
			<tr>
				<td colspan="16" class="header-cell">
					<?php
					if (isset($company_detail_d) && $company_detail_d != "" && $company_detail_d != 0 && !empty($company_detail_d['image_path'])) {
						$headerSrc = SITEURL . HEADER . $company_detail_d['image_path'];
					?>
						<img class="header-img" src="<?= $headerSrc ?>" alt="Header">
					<?php
					} else {
					?>
						<img class="header-img" src="<?= SITEURL ?>images/craftbox_header.jpg" alt="Header">
					<?php
					}
					?>
				</td>
			</tr>
			<tr class="title-bar" style="background-color: <?= VIEW_COLOR ?>; color: #000;">
				<td colspan="16" align="center" style="color: #000;"><b>Rate Confirmation Quotation</b></td>
			</tr>
			<tr>
				<td colspan="8" style="vertical-align: top;">
					<strong>Buyer</strong>
					<h5 style="font-weight: 600;text-transform: uppercase;"><strong><?php echo $cart_detail_d['company_name']; ?></strong></h5>
					<p><?php echo wordwrap($cart_detail_d['address'], 50, "<br>\n") . "  <br/>" . $cart_detail_d['city'] . " , " . $cart_detail_d['state'] . " , " . $cart_detail_d['country'] ?></p>
					<?php if (!empty($customer_address_d['zip'])) { ?>
						<p><strong>Pincode :</strong> <?= $customer_address_d['zip']; ?></p>
					<?php } ?>
					<?php if (!empty($cart_detail_d['contact_number'])) { ?>
						<p><strong>Mobile No. : </strong><?= $cart_detail_d['contact_number'] ?></p>
					<?php } ?>
					<?php if (!empty($cart_detail_d['email'])) { ?>
						<p><strong>Email : </strong><?= $cart_detail_d['email'] ?></p>
					<?php } ?>
					<?php if (!empty($cart_detail_d['gst'])) { ?>
						<p><strong>GSTIN / UIN : </strong><?= $cart_detail_d['gst'] ?></p>
					<?php } ?>
				</td>
				<td colspan="8" style="text-align: left;vertical-align: top;">
					<p><b>Quotation No. : </b><?= $cart_detail_d['quotation_no'] ?></p>
					<p>
						<b>Quotation Date : </b>
						<?php
						if ($cart_detail_d['quotation_date'] != "0000-00-00") {
							echo date('d-M-Y', strtotime($cart_detail_d['quotation_date']));
						}
						?>
					</p>
					<p><b>Client Code : </b><?= $cart_detail_d['client_code'] ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="product-items-table">
		<tbody class="<?= isset($cl) ? $cl : ''; ?>">
			<tr class="text-center" style="background-color: <?= VIEW_COLOR ?>; color: #000;">
				<th colspan="1" class="text-center" style="width:4%; color: #000;">SR</th>
				<th colspan="1" class="image-width text-center" style="width:6%; color: #000;">Image</th>
				<th colspan="4" class="text-center" style="width:30%; color: #000;">Description of Goods</th>
				<th colspan="1" class="text-center" style="width:8%; color: #000;">Brand<br />Name </th>
				<th colspan="1" class="text-center" style="width:8%; color: #000;">Weight<br />(in kg)</th>
				<th colspan="1" class="text-center" style="width:6%; color: #000;">Qty</th>
				<th colspan="1" class="text-center" style="width:8%; color: #000;">Rate</th>
				<th colspan="1" class="text-center" style="width:8%; color: #000;">Discount %</th>
				<th colspan="1" class="text-center" style="width:10%; color: #000;">Discounted Value</th>
				<th colspan="1" class="text-center" style="width:12%; color: #000;">Total Amount</th>
			</tr>
			<?php
			$ITEMS = array();
			$total_item_discount = 0;
			$total_mrp_amount = 0;
			$items1 = $db->rp_getData("quotation_product_item", "*", "quotation_id='" . $quotation_id . "'");
			while ($item1 = mysqli_fetch_assoc($items1)) {
				$item1['display_order'] = $db->rp_getValue("product", "display_order", "id='" . $item1['pro_id'] . "' AND isDelete=0");
				$item1['weight_display_order'] = $db->rp_getValue("weight", "display_order", "id='" . $item1['weight_id'] . "' AND isDelete=0");
				$item1['image_path'] = $db->rp_getValue("product", "image_path", "id='" . $item1['pro_id'] . "' AND isDelete=0", "", 0);

				$item1['image_path'] = $db->rp_getValue("product", "image_path", "id='" . $item1['pro_id'] . "' AND 
						isDelete=0", "", 0);
				if ($item1['image_path'] != "") {
					$img = SITEURL . PRODUCT . $item1['image_path'];
				} else {
					$img = SITEURL . "images/no_image_found.jpg";
				}

				$ITEMS[] = $item1;
			}
			if ($items1) {
				$count = 0;
				foreach ($ITEMS as $item) {
					$pro_name = $db->rp_getValue("product", "name", "id='" . $item['pro_id'] . "' AND isDelete=0");
					$size = $db->rp_getValue("weight", "name", "id='" . $item['weight_id'] . "' AND isDelete=0");
					$product_code = $db->rp_getValue("product_weight_price", "catno", "product_id='" . $item['pro_id'] . "' AND weight_id='" . $item['weight_id'] . "'", 0);
					$unit_id = $db->rp_getValue("product", "display_unit", "id='" . $item['pro_id'] . "' AND isDelete=0");
					$unit_name = $db->rp_getValue("unit", "name", "id='" . $unit_id . "' AND isDelete=0");

					$hsncode = $db->rp_getValue("product", "hsn_code", "id='" . $item['pro_id'] . "' AND isDelete=0", 0);

					$count++;

					if ($cart_detail_d['currency_code'] == 1) {
						$currency = CURR;
					} else if ($cart_detail_d['currency_code'] == 2) {
						$currency = DOLLAR;
					}

					$totalproqty += $item['pro_qty'];
					$totalprice1 += $item['totalprice'];

					$item_original_price = floatval($item['original_price']);
					$item_discount_per = floatval($item['discount']);
					$item_discount_val = floatval($item['discount_amount']);
					if ($item_discount_val <= 0 && $item_discount_per > 0 && $item_original_price > 0) {
						$item_discount_val = ($item_original_price * $item_discount_per) / 100;
					}
					if ($item_discount_per <= 0 && $item_discount_val > 0 && $item_original_price > 0) {
						$item_discount_per = ($item_discount_val / $item_original_price) * 100;
					}
					if ($item_discount_val <= 0 && $item_original_price > 0 && floatval($item['unitprice']) > 0) {
						$item_discount_val = $item_original_price - floatval($item['unitprice']);
						if ($item_discount_per <= 0 && $item_discount_val > 0) {
							$item_discount_per = ($item_discount_val / $item_original_price) * 100;
						}
					}
					$item_discount_total = $item_discount_val * floatval($item['pro_qty']);
					$total_item_discount += $item_discount_total;
					$total_mrp_amount += ($item_original_price > 0 ? $item_original_price : floatval($item['unitprice'])) * floatval($item['pro_qty']);
			?>
					<tr class="product-item-row">
						<td colspan="1" class="text-center srno"><strong><?php echo $count; ?></strong></td>
						<?php
						if ($item['image_path'] != "") {
						?>
							<td colspan="1" class="image-width text-center"><img class="product-img" src="<?php echo SITEURL . PRODUCT . $item['image_path'] ?>"></td>
						<?php
						} else {
						?>
							<td colspan="1" class="image-width text-center"><img class="product-img" src="<?php echo SITEURL . PRODUCT . 'default.png' ?>"></td>
						<?php
						}
						?>
						<td colspan="4" class="model" style="position: relative;"><?php if ($item['weight_id'] != -1) {
																						echo "<b>#".$product_code."</b>-".$pro_name . " - " . $size;
																					} else {
																						echo "<b>#".$product_code."</b>-".$pro_name;
																					} ?><?= (isset($item['pro_description']) && $item['pro_description'] != "") ? "<br/><br/>" . $item['pro_description'] : "" ?></strong></td>
						<td colspan="1" class="text-center"><?php echo $db->rp_getValue("order_item_brand_master", "name", "isDelete=0 AND isActive=1 AND id='" . $item['order_item_brand_id'] . "'") ?></td>
						<td colspan="1" class="text-center">
							<?php
							$weight = $db->rp_getValue("product_weight_price", "pro_weight", "product_id='" . $item['pro_id'] . "' AND weight_id='" . $item['weight_id'] . "'");
							echo $kg = $weight / 1000;

							$weight_total += $kg;
							?>

						</td>
						<td colspan="1" class="text-center"><?= $item['pro_qty'] ?></td>
						<td colspan="1" class="text-center"><?php echo round($item_original_price > 0 ? $item_original_price : $item['unitprice'], 2); ?></td>
						<td colspan="1" class="text-center"><?php echo round($item_discount_per, 2); ?></td>
						<td colspan="1" class="text-center"><?php echo $currency . ' ' . round($item_discount_total, 2); ?></td>
						<td colspan="1" class="text-center"><?php echo $currency . ' ' . round($item['totalprice'], 2); ?></td>
					</tr>
					<?php
				}

				$printMinProductRows = 13;
				if (!isset($count)) {
					$count = 0;
				}
				if ($count < $printMinProductRows) {
					for ($fi = 0; $fi < ($printMinProductRows - $count); $fi++) {
				?>
					<tr class="product-filler-row">
						<td colspan="1">&nbsp;</td>
						<td colspan="1">&nbsp;</td>
						<td colspan="4">&nbsp;</td>
						<td colspan="1">&nbsp;</td>
						<td colspan="1">&nbsp;</td>
						<td colspan="1">&nbsp;</td>
						<td colspan="1">&nbsp;</td>
						<td colspan="1">&nbsp;</td>
						<td colspan="1">&nbsp;</td>
						<td colspan="1">&nbsp;</td>
					</tr>
				<?php
					}
				}
			}
			?>
		</tbody>
	</table>
	<?php
	// Resolve Grand Total for print (fallback when DB grand_total_rounded is 0)
	if (!isset($totalprice1)) { $totalprice1 = 0; }
	if (!isset($total_item_discount)) { $total_item_discount = 0; }
	if (!isset($total_mrp_amount)) { $total_mrp_amount = 0; }
	if (!isset($currency)) { $currency = CURR; }
	$total_tax_amt = floatval($totalprice1) - floatval($cart_detail_d['cash_discount_amount']) - floatval($cart_detail_d['additional_discount_amount']) + floatval($cart_detail_d['transport_charge']) + floatval($cart_detail_d['packing_charge']);
	$calc_before_round = $total_tax_amt + floatval($cart_detail_d['igst_amount']) + floatval($cart_detail_d['tcs_amount']);
	$display_grand_total = floatval($cart_detail_d['grand_total_rounded']);
	if ($display_grand_total <= 0) {
		$display_grand_total = floatval($cart_detail_d['grand_total']);
	}
	if ($display_grand_total <= 0) {
		$display_grand_total = round($calc_before_round);
	}
	$display_roundoff = $cart_detail_d['roundoff'];
	if ((string)$display_roundoff === '' || $display_roundoff === null) {
		$display_roundoff = round($calc_before_round) - $calc_before_round;
	}
	$overall_discount_per = ($total_mrp_amount > 0) ? round(($total_item_discount / $total_mrp_amount) * 100, 2) : 0;
	$overall_discount_per_txt = rtrim(rtrim(number_format($overall_discount_per, 2, '.', ''), '0'), '.') . '%';
	?>
	<table class="summary-outer">
		<tbody>
			<tr>
				<td class="terms-cell">
					<span class="font-13"><b>Terms & Condition : </b></span>
					<span class="font-13"><?= $cart_detail_d['terms_comdition'] ?></span>
					<span class="font-13" style="color: red;"><b> This quotation is valid for 7 days.</b></span><br>
					<span class="font-13"><b>Grand Total In Words</b> :
						<?php
						$grand_total_words = $ntw->rp_convertNumToWord($display_grand_total);
						echo ucwords(strtolower($grand_total_words)); ?>
					</span><br>
					<span class="font-13">
						<b>Bank Details : </b>
						<?php
						if (isset($company_detail_d) && $company_detail_d != "" && $company_detail_d != 0) {
							echo html_entity_decode($company_detail_d['bank_details']);
						} else {
						?>
							Bank Name : <?= COMPANY_BANK ?>, Bank Branch : <?= COMPANY_BANK_BRANCH ?>,
							A/c : <?= COMPANY_BANK_ACC_NO ?>, IFSC : <?= COMPANY_BANK_IFSC ?>
						<?php
						}
						?>
					</span><br>
					<span class="font-13"><b>Remarks :</b> <?php echo $cart_detail_d['remarks'] ?></span><br>
					<span class="font-13" style="color: red;">Contact Sales Person : <?= strip_tags($cart_detail_d['faithfully']) ?></span>
					<?php
					$modified_name = explode(",", $cart_detail_d['modified_by']);
					$last_modified_id = array_slice($modified_name, -1)[0];
					$modified_by_name = $db->rp_getValue("dealer_distributor_network", "name", "id='" . $last_modified_id . "'");
					?>
					<span class="font-13" style="color: red;"> | Edited By : <?= $modified_by_name ?></span>
				</td>
				<td class="totals-cell">
					<table class="totals-inner">
						<tr>
							<td class="tot-label">Discount</td>
							<td class="tot-pct"><?php echo $overall_discount_per_txt; ?></td>
							<td class="tot-amt"><?php echo $currency . ' ' . $db->rp_number_format($total_item_discount, 2); ?></td>
						</tr>
						<tr>
							<td class="tot-label" colspan="2">Sub Total</td>
							<td class="tot-amt"><?php echo $currency . ' ' . $db->rp_number_format($totalprice1, 2); ?></td>
						</tr>
						<?php if ($cart_detail_d['cash_discount_amount'] != "" && $cart_detail_d['cash_discount_amount'] != "0") { ?>
						<tr>
							<td class="tot-label" colspan="2">Cash Discount</td>
							<td class="tot-amt"><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['cash_discount_amount'], 2); ?></td>
						</tr>
						<?php } ?>
						<?php if ($cart_detail_d['additional_discount_amount'] != "" && $cart_detail_d['additional_discount_amount'] != "0") { ?>
						<tr>
							<td class="tot-label" colspan="2">Additional Discount</td>
							<td class="tot-amt"><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['additional_discount_amount'], 2); ?></td>
						</tr>
						<?php } ?>
						<tr>
							<td class="tot-label" colspan="2">Total Taxable Amount</td>
							<td class="tot-amt"><?php echo $currency . ' ' . $db->rp_number_format($total_tax_amt, 2); ?></td>
						</tr>
						<?php
						if ($cart_detail_d['igst_amount'] != "0") {
							if (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) {
						?>
						<tr>
							<td class="tot-label" colspan="2">C GST</td>
							<td class="tot-amt"><?= ($currency . $db->rp_number_format(($cart_detail_d['igst_amount']) / 2, 2)) ?></td>
						</tr>
						<tr>
							<td class="tot-label" colspan="2">S GST</td>
							<td class="tot-amt"><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'] / 2, 2)) ?></td>
						</tr>
						<?php
							} else {
						?>
						<tr>
							<td class="tot-label" colspan="2">IGST</td>
							<td class="tot-amt"><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'], 2)) ?></td>
						</tr>
						<?php
							}
						}
						if ($cart_detail_d['tcs_amount'] != "" && $cart_detail_d['tcs_amount'] != "0") {
						?>
						<tr>
							<td class="tot-label" colspan="2">TCS (<?= TCS_CHARGE_IN_PER ?>%)</td>
							<td class="tot-amt"><?= $currency . number_format($cart_detail_d['tcs_amount'], 2) ?></td>
						</tr>
						<?php } ?>
						<tr>
							<td class="tot-label" colspan="2">Round Off</td>
							<td class="tot-amt"><?php echo $currency . $display_roundoff; ?></td>
						</tr>
						<tr style="background-color: <?= GRAND_TOTAL_COLOR ?>; font-size: 14px;">
							<td class="tot-label" colspan="2" style="background-color: <?= GRAND_TOTAL_COLOR ?>;">Grand Total</td>
							<td class="tot-amt" style="background-color: <?= GRAND_TOTAL_COLOR ?>;"><?php echo $currency . ' ' . $db->rp_number_format($display_grand_total, 2); ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	<!-- hsn summary -->
	<table>
		<?php
		if (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) {
			$gst_or_igst = "Total GST Rate";
			$gst_or_igst1 = "Total GST Amount";
		} else {
			$gst_or_igst = "IGST% Rate";
			$gst_or_igst1 = "IGST Amount";
		}
		?>
		<tr>
			<th class="text-center" colspan="2">HSN/SAC</th>
			<!-- <th class="text-center" colspan="1">Qty</th> -->
			<th class="text-center" colspan="2">Taxable Value</th>
			<th class="text-center" colspan="2"><?php echo $gst_or_igst; ?></th>
			<th class="text-center" colspan="2"><?php echo $gst_or_igst1; ?></th>
			<th class="text-center" colspan="1">CGST% Rate</th>
			<th class="text-center" colspan="2">CGST Amount</th>
			<th class="text-center" colspan="2">SGST% Rate</th>
			<th class="text-center" colspan="3">SGST Amount</th>
		</tr>
		<?php
		$ITEMS = array();
		$items1 = $db->rp_getData("quotation_product_item", "*", "isDelete=0 AND quotation_id='" . $quotation_id . "' GROUP BY hsn_code", "", 0);
		while ($item = mysqli_fetch_assoc($items1)) {
			// echo $items1;exit;
			$ITEMS[] = $item1;
			if ($items1) {
				$gst_rate = $db->rp_getValue("product", "igst", "id='" . $item['pro_id'] . "' AND isDelete=0", 0);
				$count = 0;
				$totalprice = 0;
				$final_price = 0;
				$boxqty = 0;
				$cartoonqty = 0;
				$totalproqty = 0;
				$totalrate = 0;
				$totaldiscount = 0;
				$gst_per_amount = 0;
				$GST = 0;

				if ($cart_detail_d['igst_amount'] != 0) {
					$GST = $gst_rate;
					$CGST = $gst_rate / 2;
					$SGST = $gst_rate / 2;
				} else {
					$GST = "";
					$CGST = "";
					$SGST = "";
				}

				$Invoice = $db->rp_getData("quotation_product_item", "*", "isDelete=0 AND quotation_id='" . $quotation_id . "' AND hsn_code='" . $item['hsn_code'] . "'", "", 0);
				$InvoiceIds = array();
				while ($Invoice_d = mysqli_fetch_assoc($Invoice)) {
					$InvoiceIds[] = $Invoice_d['id'];
				}
				$InvoiceIds = implode(",", $InvoiceIds);
				// echo $InvoiceIds;exit;
				$total_pro_qty = $db->rp_getValue("quotation_product_item", "SUM(pro_qty)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0);
				$total_pro_taxable = $db->rp_getValue("quotation_product_item", "SUM(taxable)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0);


				$cash_amount = ($total_pro_taxable * $cart_detail_d['cash_discount']) / 100;
				if ($cash_amount > $total_pro_taxable) {
					// $SubPrice=$cash_amount-$total_pro_taxable;
					$SubPrice = $total_pro_taxable;
				} else {
					// $SubPrice=$total_pro_taxable-$cash_amount;
					$SubPrice = $total_pro_taxable;
				}
				// $gst_per_amount=($SubPrice*$GST)/100;
				if ($cart_detail_d['igst_amount'] != 0) {
					$gst_per_amount += $db->rp_getValue("quotation_product_item", "SUM(igst_amount)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0);
					if (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) {
						$cgst_per_amount = ($gst_per_amount) / 2;
						$sgst_per_amount = ($gst_per_amount) / 2;
						// $cgst_per_amount=($SubPrice*$CGST)/100;
						// $sgst_per_amount=($SubPrice*$SGST)/100;
						$CGST = $GST / 2;
						$SGST = $GST / 2;
					} else {
						$cgst_per_amount = "";
						$sgst_per_amount = "";
						$CGST = "";
						$SGST = "";
					}
				} else {
					$gst_per_amount = "";
					$cgst_per_amount = "";
					$sgst_per_amount = "";
					$CGST = "";
					$SGST = "";
				}
		?>
				<tr>
					<td colspan="2" class="box_qty " style="text-align: center;"><?= $db->rp_getValue("product", "hsn_code", "isDelete=0 AND id='" . $item['pro_id'] . "'", 0) ?></td>
					<!-- <td colspan="1" class="text-center b_qty "><?php echo $total_pro_qty; ?></td> -->
					<td colspan="2" class="text-right rate "><?php echo CURR . ' ' . number_format($total_pro_taxable, 2); ?></td>
					<td colspan="2" class="text-right b_qty "><?php echo $GST; ?><?= ($GST) ? "%" : ""; ?></td>
					<td colspan="2" class="text-right b_qty "><?= ($gst_per_amount) ? CURR : ""; ?><?= number_format($gst_per_amount, 2);  ?></td>
					<td colspan="1" class="text-right b_qty "><?php echo $CGST; ?><?= ($CGST) ? "%" : ""; ?></td>
					<td colspan="2" class="text-right b_qty "><?= ($CGST) ? CURR : ""; ?><?= number_format($cgst_per_amount, 2);  ?></td>
					<td colspan="2" class="text-right b_qty "><?php echo $SGST; ?><?= ($SGST) ? "%" : ""; ?></td>
					<td colspan="3" class="text-right b_qty "><?= ($SGST) ? CURR : ""; ?><?= number_format($sgst_per_amount, 2);  ?></td>
				</tr>
		<?php
				$Total_total_pro_qty += $total_pro_qty;
				$Total_total_pro_taxable += $total_pro_taxable;
				$Total_gst_per_amount += $gst_per_amount;
				$Total_cgst_per_amount += $cgst_per_amount;
				$Total_sgst_per_amount += $sgst_per_amount;
			}
		}
		?>
		<tr>
			<td colspan="2" class="text-center"><strong>Total</strong></td>
			<!-- <td colspan="1" class="text-right"><b><?php echo $Total_total_pro_qty; ?></b></td> -->
			<td colspan="2" class="text-right"><b><?php echo  CURR . ' ' . number_format($Total_total_pro_taxable, 2); ?></b></td>

			<td colspan="2" class="text-right"><b><?php ?></b></td>
			<td colspan="2" class="text-right"><b><?= ($Total_gst_per_amount) ? CURR : ""; ?><?= number_format($Total_gst_per_amount, 2); ?></b></td>
			<td colspan="1" class="text-right"><b><?php ?></b></td>
			<td colspan="2" class="text-right"><b><?= ($Total_cgst_per_amount) ? CURR : ""; ?><?= number_format($Total_cgst_per_amount, 2); ?></b></td>
			<td colspan="2" class="text-right"><b><?php ?></b></td>
			<td colspan="3" class="text-right"><b><?= ($Total_sgst_per_amount) ? CURR : ""; ?><?= number_format($Total_sgst_per_amount, 2); ?></b></td>
		</tr>
	</table>
	<!-- hsn summary -->
	<!-- <table>
			<tbody>
				<tr>
					<td colspan="8" rowspan="4" class="no-border-right text-left">
						<br><br><br><br>
						<strong style="margin-right: 36px;">Customer Signature</strong>
					</td> 
					<td colspan="8" rowspan="4" class="text-right">
						<strong style="margin-right: 25px;">
							For, 
							<?php
							if (isset($company_detail_d) && $company_detail_d != "" && $company_detail_d != 0) {
								echo $company_detail_d['name'];
							} else {
								echo CLIENT_BRAND_NAME;
							}
							?>

						<br>
						</strong>
						<br><br><br>
						<strong  style="margin-right: 25px;">
						Authorised SIgnatory</strong>
					</td>
				</tr>
			</tbody> 
		</table> -->

	<div class="quote-footer-wrap">
	<table class="quote-footer-table" style="border: 1px solid #595959;border-collapse: collapse;width:100%;">
		<tbody>
			<tr>
				<td class="footer-cell">
					<?php
					if (isset($company_detail_d['footer_image_path']) && $company_detail_d['footer_image_path'] != "") {
					?>
						<img class="footer-img" src="<?= SITEURL . FOOTER . $company_detail_d['footer_image_path'] ?>" alt="Footer">
					<?php
					} else {
					?>
						<img class="footer-img" src="<?= SITEURL ?>images/white_footer.jpg" alt="Footer">
					<?php
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>
	</div><!-- /.quote-footer-wrap -->

	</div><!-- /.quote-main-body -->
	<div class="quote-suggest-body">
	<?php armor_quotation_pi_echo_suggest_block_for_quotation($db, $quotation_id, false); ?>
	</div>
	</div>
</body>

</html>