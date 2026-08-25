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


?>
<html>

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Quotation Print</title>
	<style>
		@page {
			size: A4 portrait;
			margin: 8mm 8mm 8mm 8mm;
		}

		* {
			box-sizing: border-box;
		}

		html,
		body {
			margin: 0;
			padding: 0;
			background: #fff;
			font-family: Arial, Helvetica, sans-serif;
			color: #000;
			-webkit-print-color-adjust: exact;
			print-color-adjust: exact;
		}

		.quote-wrap {
			width: 100%;
			max-width: 194mm;
			margin: 0 auto;
			padding: 0;
			background: #fff;
		}

		.mainDiv,
		table {
			border: 1px solid #595959;
			border-collapse: collapse;
			font-size: 12px;
			width: 100% !important;
			max-width: 100%;
			background-color: #FFF;
			margin: 0;
			table-layout: fixed;
		}

		table,
		td,
		th {
			border: 1px solid #595959;
		}

		td,
		th {
			padding: 4px 5px;
			height: auto;
			vertical-align: top;
			word-wrap: break-word;
			overflow-wrap: break-word;
		}

		img {
			max-width: 100%;
			height: auto;
			display: block;
		}

		.header-cell {
			padding: 0 !important;
			margin: 0 !important;
			line-height: 0;
			font-size: 0;
			overflow: hidden;
		}

		.header-img {
			width: 100% !important;
			max-width: 100% !important;
			height: auto !important;
			max-height: none !important;
			object-fit: fill;
			display: block !important;
			padding: 0 !important;
			margin: 0 !important;
			border: 0;
		}

		.footer-img {
			width: 100% !important;
			max-width: 100% !important;
			height: auto !important;
			max-height: 90px;
			object-fit: fill;
			display: block !important;
			padding: 0 !important;
			margin: 0 !important;
		}

		.product-img {
			width: 45px;
			height: auto;
			margin: 0 auto;
			object-fit: contain;
		}

		.text-center {
			text-align: center !important;
		}

		.text-right {
			text-align: right !important;
		}

		.text-left {
			text-align: left !important;
		}

		.no-border-left {
			border-left: hidden;
		}

		.no-border-right {
			border-right: hidden;
		}

		.no-border-bottom {
			border-bottom: hidden !important;
		}

		.no-border-top {
			border-top: hidden !important;
		}

		.border td {
			border-bottom: hidden !important;
		}

		.color {
			background: #D3D3D3;
		}

		.font-size td {
			font-size: 13px !important;
		}

		.image-width {
			width: 8% !important;
		}

		.border-r-width {
			border-right-width: 5px;
		}

		.border-gray {
			border-right-color: #E5E5E5;
		}

		.border-blue {
			border-right-color: <?= VIEW_COLOR ?>;
		}

		.vertical-top {
			vertical-align: top;
		}

		.height-5 {
			height: 5px;
		}

		.bg-gray {
			background-color: #E5E5E5 !important;
		}

		.font-13 {
			font-size: 12px !important;
		}

		.headerBorder {
			border: 22px solid #eb268f;
			border-bottom: none;
			border-right: none;
			border-left: none;
		}

		h5 {
			margin: 4px 0;
		}

		p {
			margin: 2px 0;
		}

		@media print {
			html,
			body {
				margin: 0 !important;
				padding: 0 !important;
				width: 100% !important;
			}

			.quote-wrap {
				max-width: 100% !important;
				width: 100% !important;
				margin: 0 !important;
			}

			table {
				page-break-inside: auto;
			}

			tr {
				page-break-inside: avoid;
				page-break-after: auto;
			}

			thead {
				display: table-header-group;
			}

			.no-print-empty {
				display: none !important;
			}

			img.header-img {
				width: 100% !important;
				max-width: 100% !important;
				height: auto !important;
				max-height: none !important;
				object-fit: fill !important;
			}

			img.footer-img {
				width: 100% !important;
				max-height: 80px !important;
			}
		}
	</style>
</head>

<body>
	<div class="quote-wrap">
	<table>
		<tbody class="<?= $cl; ?>">
			<tr>
				<td colspan="16" class="header-cell">
					<?php
					if (isset($company_detail_d) && $company_detail_d != "" && $company_detail_d != 0 && !empty($company_detail_d['image_path'])) {
						$headerSrc = SITEURL . "images/header/" . $company_detail_d['image_path'];
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
			<tr style="background-color: <?= VIEW_COLOR ?>; color: #000;">
				<td colspan="16" align="center" style="color: #000; padding: 6px;"><b>Rate Confirmation Quotation</b></td>
			</tr>
			<tr>
				<td colspan="8" style="vertical-align: top;">
					Buyer
					<h5 style="font-weight: 600;text-transform: uppercase;"><strong><?php echo $cart_detail_d['company_name']; ?></strong></h5>
					<p style="margin:0"><?php echo wordwrap($cart_detail_d['address'], 40, "<br>\n") . "  <br/>" . $cart_detail_d['city'] . " , " . $cart_detail_d['state'] . " , " . $cart_detail_d['country'] ?></p>
					<?php
					if (!empty($customer_address_d['zip'])) {
					?>
						<p style="margin:0"><strong>Pincode :</strong> <?= $customer_address_d['zip']; ?></p>

					<?php
					}
					?>
					<?php
					if (!empty($cart_detail_d['contact_number'])) {
					?>
						<p style="margin:0"><strong>Mobile No. : </strong><?= $cart_detail_d['contact_number'] ?></p>
					<?php
					}
					?>

					<?php
					if (!empty($cart_detail_d['email'])) {
					?>
						<p style="margin:0"><strong>Email : </strong><?= $cart_detail_d['email'] ?></p>
					<?php
					}
					?>
					<?php
					if (!empty($cart_detail_d['gst'])) {
					?>
						<p style="margin:0"><strong>GSTIN / UIN : </strong><?= $cart_detail_d['gst'] ?></p>
					<?php
					}
					?>
				</td>
				<td colspan="8" style="text-align: left;vertical-align: top;">
					<p><b>Quotation No. : </b><?= $cart_detail_d['quotation_no'] ?></p>
					<p>
						<b>Quotation Date : </b>
						<?php
						if ($cart_detail_d['quotation_date'] != "0000-00-00") {
							echo date('d-M-Y', strtotime($cart_detail_d['quotation_date']));
						} else {
							echo "";
						}
						?>
					</p>
					<p><b>Client Code : </b><?= $cart_detail_d['client_code'] ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<table>
		<tbody class="<?= $cl; ?>">
			<tr class="text-center" style="background-color: <?= VIEW_COLOR ?>; color: #000;">
				<th colspan="1" class="text-center" style="width:4%; color: #000;">SR</th>
				<th colspan="1" class="image-width text-center" style="width:8%; color: #000;">Image</th>
				<th colspan="4" class="text-center" style="width:28%; color: #000;">Description of Goods</th>
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
					<tr>
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

				if ($count < 5) {
					for ($i = 0; $i < 5 - $count; $i++) {
					?>
						<tr class="border no-print-empty">
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="4"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
						</tr>
			<?php
					}
				}
			}
			?>
			<tr class="no-print-empty">
				<td colspan="1"></td>
				<td colspan="1"></td>
				<td colspan="4"></td>
				<td colspan="1"></td>
				<td colspan="1"></td>
				<td colspan="1"></td>
				<td colspan="1"></td>
				<td colspan="1"></td>
				<td colspan="1"></td>
				<td colspan="1"></td>
			</tr>


			<!-- <tr>
					<td colspan="1"></td>
					<td colspan="5" class="text-center"><b></b></td>
					<td colspan="2" class="text-center"><b><?php echo $totalproqty; ?></b></td>
					<td colspan="3" class="text-center"></td>
					<td colspan="3" class="rate text-center"></td>
					<td colspan="3"></td>
					<td colspan="3"></td>
					<td colspan=""></td>
				</tr> -->
		</tbody>
	</table>
	<?php
	// Resolve Grand Total for print (fallback when DB grand_total_rounded is 0)
	if (!isset($totalprice1)) { $totalprice1 = 0; }
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
	// Dynamic rowspan for terms column (avoid blank spacer rows)
	$terms_rowspan = 5; // Discount, Sub Total, Taxable, Round Off, Grand Total
	if ($cart_detail_d['cash_discount_amount'] != "" && $cart_detail_d['cash_discount_amount'] != "0") { $terms_rowspan++; }
	if ($cart_detail_d['additional_discount_amount'] != "" && $cart_detail_d['additional_discount_amount'] != "0") { $terms_rowspan++; }
	if ($cart_detail_d['igst_amount'] != "" && $cart_detail_d['igst_amount'] != "0") {
		$terms_rowspan += (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) ? 2 : 1;
	}
	if ($cart_detail_d['tcs_amount'] != "" && $cart_detail_d['tcs_amount'] != "0") { $terms_rowspan++; }
	?>
	<table>
		<tbody class="<?= $cl; ?>">
			<tr class="font-size">
				<td colspan="8" class="" rowspan="<?= $terms_rowspan ?>" style="vertical-align: top;">
					<span class="font-13"><b>Terms & Condition : </b></span><br>
					<span class="font-13"><?= $cart_detail_d['terms_comdition'] ?></span><br>
					<span class="font-13" style="color: red;"><b>This quotation is valid for 7 days.</b></span><br>
					<br>
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
							Bank Name : <?= COMPANY_BANK ?>, Bank Branch : <?= COMPANY_BANK_BRANCH ?><br>
							Bank Account No : <?= COMPANY_BANK_ACC_NO ?>, Bank IFSC Code : <?= COMPANY_BANK_IFSC ?>
						<?php
						}
						?>
					</span><br>
					<span class="font-13"><b>Remarks</b></span><br>
					<span class="font-13"><?php echo $cart_detail_d['remarks'] ?></span><br>
					<span style="color: red;">Contact Sales Person : <?= strip_tags($cart_detail_d['faithfully']) ?> &nbsp; </span>
					<?php
					$modified_name = explode(",", $cart_detail_d['modified_by']);
					$last_modified_id = array_slice($modified_name, -1)[0];
					$modified_by_name = $db->rp_getValue("dealer_distributor_network", "name", "id='" . $last_modified_id . "'");
					?>
					<br /><span style="color: red;">Edited By : <?= $modified_by_name ?> &nbsp; </span>
				</td>
				<td colspan="2" class="text-left font-13"><strong>Discount</strong></td>
				<td colspan="2" class="text-center font-13"><strong><?php
					$overall_discount_per = ($total_mrp_amount > 0) ? round(($total_item_discount / $total_mrp_amount) * 100, 2) : 0;
					echo rtrim(rtrim(number_format($overall_discount_per, 2, '.', ''), '0'), '.') . '%';
				?></strong></td>
				<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($total_item_discount, 2); ?></strong></td>

			</tr>
			<tr>
				<td colspan="4" class="text-left font-13"><strong>Sub Total</strong></td>
				<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($totalprice1, 2); ?></strong></td>
			</tr>

			<?php if ($cart_detail_d['cash_discount_amount'] != "" && $cart_detail_d['cash_discount_amount'] != "0") { ?>
				<tr>
					<td colspan="4" class="text-left font-13"><strong>Cash Discount</strong></td>
					<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['cash_discount_amount'], 2); ?></strong></td>
				</tr>
			<?php } ?>

			<?php if ($cart_detail_d['additional_discount_amount'] != "" && $cart_detail_d['additional_discount_amount'] != "0") { ?>
				<tr>
					<td colspan="4" class="text-left font-13"><strong>Additional Discount</strong></td>
					<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['additional_discount_amount'], 2); ?></strong></td>
				</tr>
			<?php } ?>
			<!-- <tr>
					<td colspan="4" class="text-left font-13"><strong>Transport Charge</strong></td>
					<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['transport_charge'], 2); ?></strong></td>
				</tr>
				<tr>
					<td colspan="4" class="text-left font-13"><strong>Packing & Forwarding Charge</strong></td>
					<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['packing_charge'], 2); ?></strong></td>
				</tr> -->
			<?php /* $total_tax_amt already calculated above for grand total */ ?>
			<tr>
				<td colspan="4" class="text-left font-13"><strong>Total Taxable Amount</strong></td>
				<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($total_tax_amt, 2); ?></strong></td>
			</tr>
			<?php
			if ($cart_detail_d['igst_amount'] != "0") {
				if ($cart_detail_d['type_of_executive'] == 7) {
					if (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) {
			?>
						<tr>
							<td colspan="4" class="text-left"><strong>C GST</strong></td>
							<td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format(($cart_detail_d['igst_amount']) / 2, 2)) ?></strong> </td>
						</tr>
						<tr>
							<td colspan="4" class="text-left"><strong>S GST</strong></td>
							<td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'] / 2, 2)) ?></strong></td>
						</tr>
					<?php
					} else {
					?>
						<tr>
							<td colspan="4" class="text-left"><strong>IGST</strong></td>
							<td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'], 2)) ?></strong></td>
						</tr>
					<?php
					}
				} else {
					if (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) {
					?>
						<tr>
							<td colspan="4" class="text-left"><strong>C GST</strong></td>
							<td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'] / 2, 2)) ?></strong> </td>
						</tr>
						<tr>
							<td colspan="4" class="text-left"><strong>S GST</strong></td>
							<td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'] / 2, 2)) ?></strong></td>
						</tr>
					<?php
					} else {
					?>
						<tr>
							<td colspan="4" class="text-left"><strong>IGST</strong></td>
							<td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'], 2)) ?></strong></td>
						</tr>
				<?php
					}
				}
			}
			?>
			<?php
			if ($cart_detail_d['tcs_amount'] != "" && $cart_detail_d['tcs_amount'] != "0") {
			?>
				<tr>
					<td colspan="4">
						<strong>TCS (<?= TCS_CHARGE_IN_PER ?>%)</strong>
					</td>
					<td colspan="4" class="text-right"><strong><?= $currency . number_format($cart_detail_d['tcs_amount'], 2) ?></strong></td>
				</tr>
			<?php
			}
			?>
			<tr>
				<td colspan="4">
					<strong>Round Off</strong>
				</td>
				<td colspan="4" class="text-right"><strong>
						<?php echo $currency . $display_roundoff; ?>
					</strong></td>
			</tr>
			<tr style="background-color:  <?= GRAND_TOTAL_COLOR ?>;font-size: 16px;">
				<td colspan="4">
					<strong>Grand Total</strong>
				</td>
				<td colspan="4" class="text-right" style="background-color: <?= GRAND_TOTAL_COLOR ?>;font-size: 16px;"><strong>
						<?php
						echo $currency . ' ' . $db->rp_number_format($display_grand_total, 2);
						?>
					</strong>
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

	<table style="border: 1px solid #595959;border-collapse: collapse;">
		<tbody>
			<tr>
				<td class="header-cell">
					<?php
					if (isset($company_detail_d['footer_image_path']) && $company_detail_d['footer_image_path'] != "") {
					?>
						<img class="footer-img" src="<?= SITEURL ?>images/header/<?= $company_detail_d['footer_image_path'] ?>" alt="Footer">
					<?php
					} else {
					?>
						<img class="footer-img" style="max-height: 40px;" src="<?= SITEURL ?>images/white_footer.jpg" alt="Footer">
					<?php
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>
	</div>
</body>

</html>