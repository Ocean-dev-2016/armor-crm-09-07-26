<?php
$page_id = 566;
$page_slug = 'page_order_ajax';
require_once("connect_in.php");
include("../include/no_to_word.php");
require_once(dirname(__FILE__) . '/../include/quotation_pi_suggest_products_helper.php');

$ntw = new NumToWord_RP;
$order_id	= $_REQUEST['order_id'];
$orderViewEmbedded = (basename($_SERVER['SCRIPT_NAME']) !== 'view_order_new_print.php');
$cart_detail_r 	= $db->rp_getData("orders", "*", "id='" . $order_id . "'", "", 0);
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
	$customer_address = $db->rp_getData("executive", "address,company_name,zip,mobile_no1,email,gst", "id = '" . $cart_detail_d['customer_id'] . "' ", '');
	$customer_address_d = mysqli_fetch_assoc($customer_address);
}

// if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id']))
// {
// 	$cat_id = $db->rp_getValue("order_product_item","top_cat_id","order_id = '".$_REQUEST['order_id']."' ",0);
// }
// if ($cat_id == 1) 
// {
// 	$header_image = SITEURL.'images/craftbox_header.jpg';
// }
// else if($cat_id == 2)
// {
// 	$header_image = SITEURL.'images/craftbox_header.jpg';
// }
// else
// {
// 	$header_image = SITEURL.'images/craftbox_header.jpg';
// }

$company_detail_r = $db->rp_getData("company_master", "*", "id='" . $cart_detail_d['type_of_company'] . "' AND isDelete=0", "", 0);

$company_detail_d = mysqli_fetch_assoc($company_detail_r);

$order_unit_arr = array("-1" => "Box", "-2" => "Strip", "-3" => "Pallet", "1" => "Caret", "2" => "Big Box", "100" => "Nos");
?>
<html>

<head>
	<?php echo armor_quotation_pi_suggest_pi_view_head_assets(); ?>
	<style>
		.mainDiv,
		body > table {
			border: 1px solid #595959;
			border-collapse: collapse;
			font-size: 13px;
			width: 250mm !important;
			background-color: #FFF;
			margin: auto;
			padding: auto;
		}

		body > table,
		body > table td,
		body > table th {
			border: 1px solid #595959;
		}

		body > table td,
		body > table th {
			padding: 5px;
			height: 20px;
		}

		.text-center {
			text-align: center !important;
		}

		.text-right {
			text-align: right !important;
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
			font-size: 15px !important;
		}

		.image-width {
			width: 10% !important;
			min-width: 10% !important;
			max-width: 10% !important;
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
			font-size: 13px !important;
		}

		.headerBorder {
			border: 22px solid #eb268f;
			border-bottom: none;
			border-right: none;
			border-left: none;
		}

		.bolde-style {
			font-weight: bold;
		}
	</style>
</head>

<body>
	<table>
		<!-- buyer & company detail division hear-->
		<tbody class="<?= $cl; ?>">
			<!-- <tr class="headerBorder">
					<td colspan="6" class="no-border-right" style="background: #f1f1f1;padding: 0px !important;">
						<img style="width: 100%;padding: 0px !important;"  src="<?= $header_image; ?>">
					</td>
					<td colspan="6" class="" style="font-size: 27px;background: #f1f1f1;">
						<?php
						if ($customer_address) {
						?>
								<h2><strong><?= $customer_address_d['company_name']; ?></strong></h2>
								<p class="font-13">
									<i class="fa fa-location-arrow"></i> <?= $customer_address_d['address'] ?>
								</p>	
								<?php
							}
								?>

						
							<br>
						<p class="font-13">
							<i class="fa fa-location-arrow"></i> <?= FACTORY_ADDRESS ?>
							<br>
							<i class="fa fa-envelope"></i> info@sheetalicecream.com <i class="fa fa-globe"></i> www.scplco.com
						<br>
						<strong>CIN: L15205GJ2013PLC077205</strong>
							<i class="fa fa-location-arrow"></i><strong> Office Address : </strong><?= CLIENT_ADDRESS ?>
							<br> 
							 <i class="fa fa-phone"></i> <?= OFFICE_PHONE ?><br> <?= OFFICE_EMAIL ?> 
						</p>
					</td>
				</tr> -->
			<td colspan="21">
				<!-- <center><img style="width: 150px; padding: 0px !important; height: 50px;"  src="<?= VIEW_LOGO_All ?>"></center> -->

				<?php
				if (isset($company_detail_d['image_path']) && $company_detail_d['image_path'] != "") {
				?>
					<img style="width: 933px;height: 184px;padding: 0px !important;" src="<?= HEADER_A . $company_detail_d['image_path'] ?>">
				<?php
				} else {
				?>
					<img style="width: 933px;height: 184px;padding: 0px !important;" src="../images/craftbox_header.jpg">
				<?php
				}
				?>
			</td>
			<tr style="background-color:#A9A9A9; color: #000;">
				<td colspan="21" align="center" style="color: #000;"><b>PRO FORMA INVOICE</b></td>
			</tr>
			<tr>
				<td colspan="8" rowspan="4" style="text-align: left;vertical-align: top;">
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
					if (!empty($customer_address_d['mobile_no1'])) {
					?>
						<p style="margin:0"><strong>Mobile No. : </strong><?= $customer_address_d['mobile_no1'] ?></p>
					<?php
					}
					?>

					<?php
					if (!empty($customer_address_d['email'])) {
					?>
						<p style="margin:0"><strong>Email : </strong><?= $customer_address_d['email'] ?></p>
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
				<td colspan="2">
					<p>
						<span class="bolde-style">Billing Address:</span><br>
						<?php echo wordwrap($cart_detail_d['billing_address'], 40, "<br>") ?>
						<!-- <span><?= $cart_detail_d['billing_address']; ?></span> -->
					</p>
					<br>
					<?php
					if ($cart_detail_d['shipping_address'] != "") {
					?>
						<p>
							<span class="bolde-style">Shipping Address:</span><br>
							<?php echo wordwrap($cart_detail_d['shipping_address'], 40, "<br> ") ?>

						</p>
						<br>
					<?php
					} else {
					?>
						<br><br><br><br><br>
					<?php
					}
					?>
				</td>

				<td colspan="5">
					<p><b>Order No. : <?= $cart_detail_d['order_no'] ?></b></p>
					<p><b>Order Date : <?= date('d-M-Y', strtotime($cart_detail_d['order_date'])); ?></b></p>
					<!-- <p><b>Parcle : </b></p> -->
					<p><b>Booking Add. :</b><?= $cart_detail_d['booking_place']; ?></p>
					<p><b>Booking Pincode. : </b><?= $cart_detail_d['booking_pincode']; ?></p>
					<p><b>Transport : </b><?php echo $db->rp_getValue("transport_master", "name", "isDelete=0 AND id='" . $cart_detail_d['transport_name'] . "'", 0); ?></p>
					<p><b>Sales Person : </b><?php echo $db->rp_getValue("sales_executive", "name", "isDelete=0 AND id='" . $cart_detail_d['sales_id'] . "'", 0); ?></p>
					<p><b>C C ATTACH : </b><?php if ($cart_detail_d['lr_image'] == "") {
												echo "NO";
											} else {
												echo "Yes";
											} ?>
					</p>
					<p><b>Max. Dispatch Dt. :</b><?php $max_dispatch_date = date('d-m-Y', strtotime($cart_detail_d['max_dispatch_date']));
													if ($expected_dispatch_date != '01-01-1970' && $max_dispatch_date != '01-01-1970' && '00-00-0000') {
														echo $max_dispatch_date;
													} ?>
					</p>
					<p><b>Client Code : </b><?= $db->rp_getValue("executive", "client_code", "id='" . $cart_detail_d['customer_id'] . "' AND isDelete=0"); ?></p>
				</td>
			</tr>
		</tbody>
		<!-- buyer & company detail division end-->
		<!-- product detail division hear-->
		<table>
			<tbody>
				<tr class="text-center" style="background-color: <?= VIEW_COLOR ?>; color: #000;">
					<th colspan="1" class="text-center" style="width:4%; color: #000;">SR No.</th>
					<!-- <th colspan="1" class="image-width text-center">Image</th>  -->
					<th colspan="3" class="text-center" style="width:22%; color: #000;">Product Name</th>
					<th colspan="1" class="text-center" style="width:8%; color: #000;">Brand <br> Name</th>
					<th colspan="1" class="text-center" style="width:8%; color: #000;">HSN Code</th>
					<th colspan="1" class="text-center" style="width:6%; color: #000;">Qty</th>
					<th colspan="1" class="text-center" style="width:8%; color: #000;">Weight<br />(in kg)</th>
					<th colspan="1" class="text-center" style="width:8%; color: #000;">Rate</th>
					<th colspan="1" class="text-center" style="width:8%; color: #000;">Discount %</th>
					<th colspan="1" class="text-center" style="width:10%; color: #000;">Discounted Value</th>
					<th colspan="1" class="text-center" style="width:8%; color: #000;">GST /<br>IGST %</th>
					<th colspan="1" class="text-center" style="width:10%; color: #000;">Total Amount</th>
				</tr>
				<?php
				$ITEMS = array();
				$total_item_discount = 0;
				$total_mrp_amount = 0;
				$items1 = $db->rp_getData("order_product_item", "*", "order_id='" . $order_id . "' AND isDelete=0");
				while ($item1 = mysqli_fetch_assoc($items1)) {
					$item1['display_order'] = $db->rp_getValue("product", "display_order", "id='" . $item1['pro_id'] . "' AND isDelete=0");
					$item1['weight_display_order'] = $db->rp_getValue("weight", "display_order", "id='" . $item1['weight_id'] . "' AND isDelete=0");
					$item1['image_path'] = $db->rp_getValue("product", "image_path", "id='" . $item1['pro_id'] . "' AND isDelete=0", "", 0);
					if ($item1['image_path'] != "") {
						$img = SITEURL . PRODUCT . $item1['image_path'];
					} else {
						$img = SITEURL . "images/no_image_found.jpg";
					}

					$ITEMS[] = $item1;
				}
				if ($items1) {

					$count = 0;
					$GST = 0;
					foreach ($ITEMS as $item) {
						if (isset($item['isDelete']) && (int) $item['isDelete'] === 1) {
							continue;
						}

						$pro_name = $db->rp_getValue("product", "name", "id='" . $item['pro_id'] . "' AND isDelete=0");
						$size = $db->rp_getValue("weight", "name", "id='" . $item['weight_id'] . "' AND isDelete=0");
						$product_code = $db->rp_getValue("product_weight_price", "catno", "product_id='" . $item['pro_id'] . "' AND weight_id='" . $item['weight_id'] . "'", 0);
						$hsncode = $db->rp_getValue("product", "hsn_code", "id='" . $item['pro_id'] . "' AND isDelete=0", 0);

						$count++;

						if ($cart_detail_d['currency_code'] == 1) {
							$currency = CURR;
						} else if ($cart_detail_d['currency_code'] == 2) {
							$currency = DOLLAR;
						}

						$totalproqty += $item['pro_qty'];
						$totalprice1 += $item['totalprice'];

						/*$qty_inner= $item['pro_qty']/$item['inner_size'];
							$qty_outer= $item['pro_qty']/$item['outer_size'];*/

						$qty_product = $item['pro_qty'];

						$inner_unit = $db->rp_getValue("product_weight_price", "inner_unit", "product_id='" . $item['pro_id'] . "' AND weight_id='" . $item['weight_id'] . "'");
						$outer_unit = $db->rp_getValue("product_weight_price", "outer_unit", "product_id='" . $item['pro_id'] . "' AND weight_id='" . $item['weight_id'] . "'");

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
							<!--  <?php
									if ($item['image_path'] != "") {
									?>
									<td colspan="1" class="image-width text-center"><img style="width: 80px;" src="<?php echo SITEURL . PRODUCT . $item['image_path'] ?>"></td>
									<?php
									} else {
									?>
									<td colspan="1" class="image-width text-center"><img style="width: 80px;" src="<?php echo SITEURL . PRODUCT . 'default.png' ?>"></td>
									<?php
									}
									?>  -->
							<td colspan="3" class="model" style="position: relative;">
								<?php
								if ($item['weight_id'] != -1) {
									echo "<b>#".$product_code."</b>-".$pro_name . " - " . $size;
								} else {
									echo "<b>#".$product_code."</b>-".$pro_name;
								}
								?>
								<?= (isset($item['pro_description']) && $item['pro_description'] != "") ? "<br/><br/>" . $item['pro_description'] : "" ?>
							</td>
							<td colspan="1" class="text-center"><?php echo $db->rp_getValue("order_item_brand_master", "name", "isDelete=0 AND isActive=1 AND id='" . $item['order_item_brand_id'] . "'") ?></td>
							<td colspan="1" class="text-center"> <?= $hsncode ?></td>
							<td colspan="1" class="text-center"><?= $qty_product;
																$totalqty += $qty_product;
																?></td>

							<td colspan="1" class="text-center">
								<?php
								$weight = $db->rp_getValue("product_weight_price", "pro_weight", "product_id='" . $item['pro_id'] . "' AND weight_id='" . $item['weight_id'] . "'");
								echo $kg = $weight / 1000;

								$weight_total += $kg;

								?>

							</td>
							<td colspan="1" class="text-center">
								<?php
								$display_rate = $item_original_price > 0 ? $item_original_price : floatval($item['unitprice']);
								if ($cart_detail_d['customer_type'] == 1 || $cart_detail_d['customer_type'] == 2) {
									echo round($display_rate * $item['inner_size'], 2);
								} else {
									echo round($display_rate, 2);
								}
								?>
							</td>
							<td colspan="1" class="text-center"><?php echo round($item_discount_per, 2); ?></td>
							<td colspan="1" class="text-center"><?php echo $currency . ' ' . round($item_discount_total, 2); ?></td>
							<td colspan="1" class="text-center"><?= $pro_gst = $db->rp_getValue("product", "igst", "id='" . $item['pro_id'] . "' AND isDelete=0", 0); ?></td>
							<td colspan="1" class="text-center"><?php echo $currency . ' ' . round($item['totalprice'], 2); ?></td>
						</tr>
						<?php
					}

					if ($count < 5) {
						for ($i = 0; $i < 12 - $count; $i++) {
						?>
							<tr class="border">
								<td colspan="1"></td>
								<!-- <td colspan="1"></td> -->
								<td colspan="3"></td>
								<td colspan="1"></td>
								<td colspan="1"></td>
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
				<tr>
					<td colspan="1"></td>
					<!-- <td colspan="1"></td> -->
					<td colspan="3"></td>
					<td colspan="1"></td>
					<td colspan="1"></td>
					<td colspan="1"></td>
					<td colspan="1"></td>
					<td colspan="1"></td>
					<td colspan="1"></td>
					<td colspan="1"></td>
					<td colspan="1"></td>
					<td colspan="1"></td>
				</tr>
				<tr>
					<td colspan="1"></td>
					<!-- 	<td colspan="1"></td> -->
					<td colspan="3"></td>
					<td colspan="1"></td>
					<td colspan="1"><strong>Total</strong></td>
					<td colspan="1" style="text-align: center"><?php echo $totalqty; ?></td>
					<td colspan="1" style="text-align: center"><?php echo $weight_total; ?></td>
					<td colspan="1"></td>
					<td colspan="1"></td>
					<td colspan="1"></td>
					<td colspan="1"></td>
					<td colspan="1"></td>
				</tr>
			</tbody>
		</table>
		<!-- product detail division end-->
		<?php
		// Grand Total = Taxable + IGST + TCS (stored grand_total_rounded often misses GST)
		if (!isset($totalprice1)) { $totalprice1 = 0; }
		$total_tax_amt = floatval($totalprice1) - floatval($cart_detail_d['cash_discount_amount']) - floatval($cart_detail_d['additional_discount_amount']) + floatval($cart_detail_d['transport_charge']) + floatval($cart_detail_d['packing_charge']);
		$igst_amt = floatval($cart_detail_d['igst_amount']);
		$tcs_amt = floatval($cart_detail_d['tcs_amount']);
		$calc_before_round = $total_tax_amt + $igst_amt + $tcs_amt;
		$calc_rounded = round($calc_before_round);
		$display_grand_total = floatval($cart_detail_d['grand_total_rounded']);
		$stored_grand = floatval($cart_detail_d['grand_total']);
		if ($display_grand_total <= 0) {
			$display_grand_total = $stored_grand;
		}
		// Stored total equals taxable (GST not added) — use calculated inclusive total
		if (($igst_amt > 0 || $tcs_amt > 0) && abs($display_grand_total - $total_tax_amt) <= 1) {
			if (abs($stored_grand - $calc_rounded) <= 1 && $stored_grand > $display_grand_total) {
				$display_grand_total = round($stored_grand);
			} else {
				$display_grand_total = $calc_rounded;
			}
		}
		if ($display_grand_total <= 0) {
			$display_grand_total = $calc_rounded;
		}
		$display_roundoff = $cart_detail_d['roundoff'];
		if ((string)$display_roundoff === '' || $display_roundoff === null || (($igst_amt > 0 || $tcs_amt > 0) && abs(floatval($cart_detail_d['grand_total_rounded']) - $total_tax_amt) <= 1)) {
			$display_roundoff = $db->rp_num($calc_rounded - $calc_before_round, 2);
		}
		// Dynamic rowspan for bank/terms (ends before Round Off / Bill Amount In Words)
		$terms_rowspan = 2; // Sub Total, Taxable
		if ($cart_detail_d['cash_discount_amount'] != "" && $cart_detail_d['cash_discount_amount'] != "0") { $terms_rowspan++; }
		if ($cart_detail_d['additional_discount_amount'] != "" && $cart_detail_d['additional_discount_amount'] != "0") { $terms_rowspan++; }
		if ($cart_detail_d['igst_amount'] != "" && $cart_detail_d['igst_amount'] != "0") {
			$terms_rowspan += (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) ? 2 : 1;
		}
		if ($cart_detail_d['tcs_amount'] != "" && $cart_detail_d['tcs_amount'] != "0") { $terms_rowspan++; }
		?>
		<div class="quote-suggest-body">
		<?php armor_quotation_pi_echo_suggest_block_for_order($db, $order_id, false); ?>
		</div>
		<!-- Terms & Condition & Amount detail division hear-->
		<table>
			<tbody class="<?= $cl; ?>">
				<tr class="font-size">
					<td colspan="13" class="" rowspan="1" style="vertical-align: top;background-color: lightgray;">
						<strong>GSTIN NO. : <?= $company_detail_d['gst'] ?></strong>
					</td>
					<td colspan="2" class="text-left font-13" style="background-color: lightgray;"><strong>Discount</strong></td>
					<td colspan="2" class="text-center font-13" style="background-color: lightgray;"><strong><?php
						$overall_discount_per = ($total_mrp_amount > 0) ? round(($total_item_discount / $total_mrp_amount) * 100, 2) : 0;
						echo rtrim(rtrim(number_format($overall_discount_per, 2, '.', ''), '0'), '.') . '%';
					?></strong></td>
					<td colspan="4" class="text-right font-13" style="background-color: lightgray;"><strong><?php echo $currency . ' ' . $db->rp_number_format($total_item_discount, 2); ?></strong></td>

				</tr>
				<tr>
					<td colspan="13" class="" rowspan="<?= $terms_rowspan ?>" style="vertical-align: top;">
						<b>
							<?php
							if (isset($company_detail_d['bank_details']) && $company_detail_d['bank_details'] != "") {
								echo html_entity_decode($company_detail_d['bank_details']);
							} else {
							?>
								Bank Name : <?= COMPANY_BANK ?>, Bank Branch : <?= COMPANY_BANK_BRANCH ?><br>
								Bank Account No : <?= COMPANY_BANK_ACC_NO ?>, Bank IFSC Code : <?= COMPANY_BANK_IFSC ?>
							<?php
							}
							?>
						</b>
						<span class="font-13"><b>Terms & Condition : </b></span>
						<span class="font-13"><?= $cart_detail_d['terms_comdition'] ?></span>
						<br>
						<span class="font-13" style="color: red;"><b>This Pro Forma Invoice is valid for 7 days.</b></span>
						<br>
						<span class="font-13"><b>Note</b></span>
						<span class="font-13"><?php echo $cart_detail_d['remarks'] ?></span>
						<br>
						<span style="color: red;">Contact Sales Person : <?= strip_tags($cart_detail_d['faithfully']) ?> &nbsp; </span>
						<?php
						$modified_name = explode(",", $cart_detail_d['modified_by']);
						$last_modified_id = array_slice($modified_name, -1)[0];
						$modified_by_name = $db->rp_getValue("dealer_distributor_network", "name", "id='" . $last_modified_id . "'");
						?>
						<br /><span style="color: red;">Edited By : <?= $modified_by_name ?> &nbsp; </span>
					</td>
					<td colspan="4" class="text-left font-13"><strong>Sub Total</strong></td>
					<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($totalprice1, 2); ?></strong></td>
				</tr>

				<?php if ($cart_detail_d['cash_discount_amount'] != "" && $cart_detail_d['cash_discount_amount'] != "0") { ?>
				<tr>
						<td colspan="4" class="text-left " style="font-weight: 700;font-size: 14px;">Cash Discount</td>
						<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['cash_discount_amount'], 2); ?></strong></td>
				</tr>
				<?php } ?>

				<?php if ($cart_detail_d['additional_discount_amount'] != "" && $cart_detail_d['additional_discount_amount'] != "0") { ?>
				<tr>
						<td colspan="4" class="text-left " style="font-weight: 700;font-size: 14px;"><strong>Additional Discount</strong></td>
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
				<tr>
					<!-- <td colspan="13" class="" rowspan="4" style="vertical-align: top;">
							<b>
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
							</b>
							<span class="font-13"><b>Terms & Condition : </b></span>
							<span class="font-13"><?= $cart_detail_d['terms_comdition'] ?></span>
							<span class="font-13"><b>Remarks</b></span><br>
							<span class="font-13"><?php echo $cart_detail_d['remarks'] ?></span>
							<br>
							<span style="color: red;">Contact Sales Person : <?= strip_tags($cart_detail_d['faithfully']) ?> &nbsp; </span>
							<?php
							$modified_name = explode(",", $cart_detail_d['modified_by']);
							$last_modified_id = array_slice($modified_name, -1)[0];
							$modified_by_name = $db->rp_getValue("dealer_distributor_network", "name", "id='" . $last_modified_id . "'");
							?>
							<br/><span style="color: red;">Edited By : <?= $modified_by_name ?> &nbsp; </span>
						</td> -->
					<td colspan="4" class="text-left " style="font-weight: 700;font-size: 14px;"><strong>Total Taxable Amount</strong></td>
					<?php /* $total_tax_amt already calculated above for grand total */ ?>
					<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($total_tax_amt, 2); ?></strong></td>
				</tr>
				<?php
				if ($cart_detail_d['igst_amount'] != "0") {
					if ($cart_detail_d['type_of_executive'] == 8) {
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
					<td colspan="13" rowspan="1">
						<!-- <b>Total GST In Words</b> :
							<?php

							// echo $totalprice1;exit;
							if ($cart_detail_d['cash_discount_amount'] != "" && $cart_detail_d['cash_discount_amount'] != "0" && $cart_detail_d['additional_discount_amount'] != "" && $cart_detail_d['additional_discount_amount'] != "0") {
								$sub_total = $db->rp_number_format($cart_detail_d['subtotal'], 2);
								$gst_amts =  $sub_total * $pro_gst / 100;
								// echo $pro_gst;exit;
								$gst_amount = $db->rp_number_format($gst_amts, 2);
								$total_gst = $ntw->rp_convertNumToWord($gst_amount);
								echo ucwords(strtolower($total_gst));
							} else {
								$gst_amt = $totalprice1 * $pro_gst / 100;
								$Total_gst = $ntw->rp_convertNumToWord($gst_amt);
								echo ucwords(strtolower($Total_gst));
							}
							?>
							<br> -->
						<b>Bill Amount In Words</b> :
						<?php
						$grand_total_words = $ntw->rp_convertNumToWord($display_grand_total);
						echo ucwords(strtolower($grand_total_words));
						?>
					</td>
					<td colspan="4">
						<strong>Round Off</strong>
					</td>
					<td colspan="4" class="text-right"><strong>
							<?php echo $currency . $display_roundoff; ?>
						</strong></td>
				</tr>
				<tr>
					<td colspan="13" rowspan="1" style="background-color: #FFFF33">
						<b>Note : KINDLY RELEASE PAYMENT FOR DISPATCH CLEARANCE</b>

					</td>
					<td colspan="4" style="background-color: <?= GRAND_TOTAL_COLOR ?>;font-size: 16px;">
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
		<!-- Terms & Condition & Amount detail division end-->
		<!-- HSN summery detail division hear-->
		<?php
		if ($cart_detail_d['igst_amount'] != 0) {
		?>
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
				$items1 = $db->rp_getData("order_product_item", "*", "isDelete=0 AND order_id='" . $order_id . "' GROUP BY hsn_code", "", 0);
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

						$Invoice = $db->rp_getData("order_product_item", "*", "isDelete=0 AND order_id='" . $order_id . "' AND hsn_code='" . $item['hsn_code'] . "'", "", 0);
						$InvoiceIds = array();
						while ($Invoice_d = mysqli_fetch_assoc($Invoice)) {
							$InvoiceIds[] = $Invoice_d['id'];
						}
						$InvoiceIds = implode(",", $InvoiceIds);
						// echo $InvoiceIds;exit;
						$total_pro_qty = $db->rp_getValue("order_product_item", "SUM(pro_qty)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0);
						$total_pro_taxable = floatval($db->rp_getValue("order_product_item", "SUM(taxable)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0));
						if ($total_pro_taxable <= 0) {
							$total_pro_taxable = floatval($db->rp_getValue("order_product_item", "SUM(totalprice)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0));
						}

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
							$gst_per_amount += $db->rp_getValue("order_product_item", "SUM(igst_amount)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0);
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
		<?php
		}
		?>
		<!-- HSN summery detail division end-->
		<!-- Customer SIgnatory & Authorised SIgnatory detail division hear-->
		<!-- <table>
				<tbody>
					<tr>
						<td colspan="8" rowspan="4" class="no-border-right text-left">
							<br><br><br><br>
							<strong style="margin-right: 36px;">Customer Signature</strong>
						</td> 
						<td colspan="8" rowspan="4" class="text-right">
							<strong style="margin-right: 25px;">For, 
								<?php
								if (isset($company_detail_d['name']) && $company_detail_d['name'] != "") {
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
		<!-- Customer SIgnatory & Authorised SIgnatory detail division end-->
		<!-- Footer detail division hear-->
		<table>
			<tbody>
				<tr>
					<td colspan="8" rowspan="4">
						<?php
						if (isset($company_detail_d['footer_image_path']) && $company_detail_d['footer_image_path'] != "") {
						?>
							<img style="width: 933px;height: 184px;padding: 0px !important;" src="<?= FOOTER_A . $company_detail_d['footer_image_path'] ?>">
						<?php
						} else {
						?>
							<img style="width: 933px;height: 184px;padding: 0px !important;" src="../images/white_footer.jpg">
						<?php
						}
						?>
					</td>
				</tr>
			</tbody>
		</table>
		<!-- Footer detail division end-->
	</table>

</body>

</html>