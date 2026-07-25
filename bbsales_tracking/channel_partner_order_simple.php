<?php
/**
 * Channel Partner simple order form (two separate modes):
 *  - cp_mode=own      → CP's own order (no end-customer)
 *  - cp_mode=customer → order for CP's customer
 */
$page_id = 565;
$page_slug = 'page_order';
include("connect.php");
include('../include/product.class.php');
include("../include/orders.class.php");

$objOrder = new Order();

if (!function_exists('cp_is_channel_partner_login') || !cp_is_channel_partner_login($db)) {
	$db->addErrorMessage("This page is only for Channel Partner login.");
	$db->rp_location("dashboard.php");
}

$cp_login_id = (int) cp_get_login_channel_partner_id();
$cp_mode = (isset($_REQUEST['cp_mode']) && $_REQUEST['cp_mode'] == 'own') ? 'own' : 'customer';

$cp_exec_d = array();
$cp_r = $db->rp_getData("executive", "*", "id='" . $cp_login_id . "' AND isDelete=0", "", 0);
if ($cp_r) {
	$cp_exec_d = mysqli_fetch_assoc($cp_r);
}
if (empty($cp_exec_d)) {
	$db->addErrorMessage("Channel Partner profile not found.");
	$db->rp_location("dashboard.php");
}

$selected_cp_customer_id = 0;
if (isset($_REQUEST['cp_customer_id']) && (int) $_REQUEST['cp_customer_id'] > 0) {
	$selected_cp_customer_id = (int) $_REQUEST['cp_customer_id'];
} else if (isset($_REQUEST['channel_partner_customer_id']) && (int) $_REQUEST['channel_partner_customer_id'] > 0) {
	$selected_cp_customer_id = (int) $_REQUEST['channel_partner_customer_id'];
}

$cp_customers = array();
$cp_customers_r = $db->rp_getData(
	"channel_partner_customer",
	"*",
	"channel_partner_id='" . $cp_login_id . "' AND isDelete=0",
	"company_name ASC",
	0
);
if ($cp_customers_r) {
	while ($custRow = mysqli_fetch_assoc($cp_customers_r)) {
		$cp_customers[] = $custRow;
	}
}

$next_id = (int) $db->getLastInsertId("orders");
$order_no_display = "ORD-" . $next_id;
$order_date = date("d-m-Y");

$page_title = ($cp_mode == 'own') ? "Add My Order" : "Add Customer Order";
$page_hierarchy = array(
	array("link" => "", "title" => "Sales & Marketing"),
	array("link" => "channel_partner_customer_manage.php", "title" => "Channel Partner"),
	array("link" => "channel_partner_order_simple.php?cp_mode=" . $cp_mode, "title" => $page_title)
);

/* ---------- SUBMIT ---------- */
if (isset($_REQUEST['btn_save']) || isset($_REQUEST['submit'])) {
	if (!isset($rights['insert_flag']) || (int) $rights['insert_flag'] != 1) {
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$cp_mode = (isset($_REQUEST['cp_mode']) && $_REQUEST['cp_mode'] == 'own') ? 'own' : 'customer';

	/* Prefer visible form fields (line_product = product_weight_price.id) */
	$line_products = isset($_REQUEST['line_product']) ? $_REQUEST['line_product'] : array();
	$line_qtys = isset($_REQUEST['line_qty']) ? $_REQUEST['line_qty'] : array();
	if (!is_array($line_products)) {
		$line_products = array();
	}
	if (!is_array($line_qtys)) {
		$line_qtys = array();
	}

	$item = array();
	$productObj = new Product();
	$price_list_id = isset($cp_exec_d['price_list_id']) ? (int) $cp_exec_d['price_list_id'] : 0;

	for ($i = 0; $i < count($line_products); $i++) {
		$pwp_id = (int) $line_products[$i];
		$qty = isset($line_qtys[$i]) ? (float) $line_qtys[$i] : 0;
		if ($pwp_id <= 0 || $qty <= 0) {
			continue;
		}

		$pwp_r = $db->rp_getData("product_weight_price", "*", "id='" . $pwp_id . "'", "", 0);
		if (!$pwp_r) {
			continue;
		}
		$pwp = mysqli_fetch_assoc($pwp_r);
		$pid = (int) $pwp['product_id'];
		$weight_id = $pwp['weight_id'];
		if ($pid <= 0) {
			continue;
		}

		$details = $productObj->aj_getProductDetail($pid, $cp_login_id);
		$match = null;
		if (!empty($details)) {
			foreach ($details as $d) {
				if ((int) $d['id'] === $pwp_id || (string) $d['weight_id'] === (string) $weight_id) {
					$match = $d;
					break;
				}
			}
			if ($match === null) {
				$match = $details[0];
			}
		}

		$original_price = $match ? $match['orignal_price'] : $pwp['price'];
		$sell_price = $match ? $match['sell_price'] : $pwp['price'];
		if ($price_list_id > 0) {
			$pl_price = $db->rp_getValue(
				"product_price_list",
				"discounted_price",
				"pid='" . $pid . "' AND weight_id='" . $weight_id . "' AND price_list_id='" . $price_list_id . "' AND isDelete=0",
				0
			);
			$pl_orig = $db->rp_getValue(
				"product_price_list",
				"price",
				"pid='" . $pid . "' AND weight_id='" . $weight_id . "' AND price_list_id='" . $price_list_id . "' AND isDelete=0",
				0
			);
			if ($pl_price !== '' && $pl_price !== null && $pl_price !== false) {
				$sell_price = $pl_price;
			}
			if ($pl_orig !== '' && $pl_orig !== null && $pl_orig !== false) {
				$original_price = $pl_orig;
			}
		}

		$inner = isset($pwp['inner_size']) && (float) $pwp['inner_size'] > 0 ? (float) $pwp['inner_size'] : 1;
		$outer = isset($pwp['outer_size']) && (float) $pwp['outer_size'] > 0 ? (float) $pwp['outer_size'] : 1;
		$bag = $qty / $inner;
		$cartoon = $bag / $outer;
		$discount = $match && isset($match['discountPer']) ? $match['discountPer'] : 0;
		$discount_amount = ((float) $original_price > 0) ? ((float) $original_price - (float) $sell_price) : 0;
		$pro_name = $match && !empty($match['name']) ? $match['name'] : $db->rp_getValue("product", "name", "id='" . $pid . "'");
		$brand_id = $match && isset($match['brand_id']) ? $match['brand_id'] : $db->rp_getValue("product", "brand_id", "id='" . $pid . "'");
		$item_order_unit = $db->rp_getValue("product", "customer_unit_id", "id='" . $pid . "' AND isDelete=0");
		if ($item_order_unit === '' || $item_order_unit === null) {
			$item_order_unit = 100;
		}

		$item[] = array(
			"qty" => $qty,
			"pid" => $pid,
			"original_price" => ($original_price !== null && $original_price !== '') ? $original_price : 0,
			"price" => ($sell_price !== null && $sell_price !== '') ? $sell_price : 0,
			"pro_name" => $pro_name,
			"weight_id" => $weight_id,
			"cartoon_qty" => $cartoon,
			"box_qty" => $bag,
			"loose" => 0,
			"brand_id" => ($brand_id !== null && $brand_id !== '') ? $brand_id : 0,
			"pro_description" => "",
			"cd_discount" => 0,
			"ad_discount" => 0,
			"gst_amount_item" => 0,
			"taxable_amount" => 0,
			"sub_total" => 0,
			"other_charge" => 0,
			"fright_charge" => 0,
			"discount_amount" => $discount_amount,
			"discount" => ($discount !== null && $discount !== '') ? $discount : 0,
			"is_including" => isset($pwp['is_including']) && $pwp['is_including'] !== null && $pwp['is_including'] !== '' ? $pwp['is_including'] : 0,
			"item_order_unit" => $item_order_unit,
			"order_qty" => $qty,
			"order_item_brand_id" => ($brand_id !== null && $brand_id !== '') ? $brand_id : 0,
		);
	}

	if (empty($item)) {
		$db->addErrorMessage("Please add at least one product with quantity.");
		$db->rp_location("channel_partner_order_simple.php?cp_mode=" . $cp_mode);
	}

	$billing_address = !empty($cp_exec_d['billing_address']) ? $cp_exec_d['billing_address'] : (isset($cp_exec_d['address']) ? $cp_exec_d['address'] : "");
	$shipping_address = !empty($cp_exec_d['shipping_address']) ? $cp_exec_d['shipping_address'] : $billing_address;
	$name_gstin = isset($cp_exec_d['gst']) ? $cp_exec_d['gst'] : "";
	$booking_pincode = isset($cp_exec_d['zip']) ? $cp_exec_d['zip'] : "";
	$booking_place = !empty($cp_exec_d['booking_place'])
		? $cp_exec_d['booking_place']
		: trim((isset($cp_exec_d['main_city']) ? $cp_exec_d['main_city'] : '') . (isset($cp_exec_d['state']) && $cp_exec_d['state'] != '' ? ', ' . $cp_exec_d['state'] : ''));

	$cp_end_id = 0;
	if ($cp_mode == 'customer') {
		$cp_end_id = isset($_REQUEST['channel_partner_customer_id']) ? (int) $_REQUEST['channel_partner_customer_id'] : 0;
		if ($cp_end_id <= 0) {
			$db->addErrorMessage("Please select Customer.");
			$db->rp_location("channel_partner_order_simple.php?cp_mode=customer");
		}
		$ownCpCust = $db->rp_getTotalRecord(
			"channel_partner_customer",
			"id='" . $cp_end_id . "' AND channel_partner_id='" . $cp_login_id . "' AND isDelete=0",
			0
		);
		if ($ownCpCust <= 0) {
			$db->addErrorMessage("Invalid customer selected.");
			$db->rp_location("channel_partner_order_simple.php?cp_mode=customer");
		}
		$pre_cp_cust_r = $db->rp_getData(
			"channel_partner_customer",
			"*",
			"id='" . $cp_end_id . "' AND channel_partner_id='" . $cp_login_id . "' AND isDelete=0",
			"",
			0
		);
		if ($pre_cp_cust_r && $pre_cp_cust = mysqli_fetch_assoc($pre_cp_cust_r)) {
			$name_gstin = !empty($pre_cp_cust['gst']) ? $pre_cp_cust['gst'] : $name_gstin;
			$addrParts = array_filter(array($pre_cp_cust['city'], $pre_cp_cust['state'], $pre_cp_cust['pincode'], $pre_cp_cust['country']));
			$endAddr = implode(', ', $addrParts);
			if ($endAddr != "") {
				$billing_address = $endAddr;
				$shipping_address = $endAddr;
			}
			$booking_place = !empty($pre_cp_cust['city'])
				? ($pre_cp_cust['city'] . (!empty($pre_cp_cust['state']) ? ', ' . $pre_cp_cust['state'] : ''))
				: $booking_place;
			$booking_pincode = !empty($pre_cp_cust['pincode']) ? $pre_cp_cust['pincode'] : $booking_pincode;
		}
	}

	$sales_executive_id = !empty($cp_exec_d['seid']) ? $cp_exec_d['seid'] : 0;
	$order_date_post = isset($_REQUEST['order_date']) ? $_REQUEST['order_date'] : date('d-m-Y');

	/* Clear leftover draft carts so portal always creates a fresh order */
	$db->rp_update(
		"orders",
		array("isDelete" => 1, "modified_date" => date("Y-m-d H:i:s")),
		"customer_id='" . (int) $cp_login_id . "' AND status=-1 AND isDelete=0",
		0
	);

	$detail = array();
	$detail['order_id'] = "";
	$detail['isDelete'] = 0;
	$detail['remarks'] = "";
	$detail['terms_comdition'] = "";
	$detail['faithfully'] = "";
	$detail['vendor_code'] = "";
	$detail['tendor_code'] = "";
	$detail['uid'] = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
	$detail['cash_discount'] = isset($cp_exec_d['cash_discount']) ? $cp_exec_d['cash_discount'] : "";
	$detail['additional_discount'] = isset($cp_exec_d['additional_discount']) ? $cp_exec_d['additional_discount'] : "";
	$detail['cash_discount_amount'] = "";
	$detail['additional_discount_amount'] = "";
	$detail['gst_apply_flag'] = "";
	$detail['tcs_apply_flag'] = "";
	$detail['transport_charge_gst'] = "";
	$detail['packing_charge_gst'] = "";
	$detail['cd_gst'] = "";
	$detail['ad_gst'] = "";
	$detail['booking_place'] = $booking_place;
	$detail['booking_pincode'] = $booking_pincode;
	$detail['cid'] = $cp_login_id;
	$detail['customer_id'] = $cp_login_id;
	$detail['sales_executive_id'] = $sales_executive_id;
	$detail['order_date'] = date('Y-m-d', strtotime($order_date_post));
	$detail['order_no'] = "";
	$detail['brand_id'] = "";
	$detail['chalan_no'] = "";
	$detail['po_no'] = "";
	$detail['po_date'] = date('Y-m-d');
	$detail['transport_name'] = "";
	$detail['transport_through'] = "";
	$detail['transport_charge'] = "";
	$detail['packing_charge'] = "";
	$detail['shipping_address'] = $shipping_address;
	$detail['billing_address'] = $billing_address;
	$detail['name_gstin'] = $name_gstin;
	$detail['apply_scheme'] = "";
	$detail['type_of_company'] = isset($cp_exec_d['type_of_company']) ? $cp_exec_d['type_of_company'] : "";
	$detail['terms_condition_id'] = "";
	$detail['max_dispatch_date'] = date('Y-m-d');
	$detail['channel_partner_order_flag'] = 1;
	$detail['cp_portal_order_flag'] = 1;
	$detail['cp_order_mode'] = $cp_mode;
	if ($cp_end_id > 0) {
		$detail['channel_partner_customer_id'] = $cp_end_id;
	}

	$_REQUEST['c_type'] = 'channel_partner';

	$reply = $objOrder->AddToCart($detail, $item);
	if ($reply['ack'] == 1) {
		unset($detail['order_date']);
		$detail['cash_discount_flag'] = 0;
		$detail['order_id'] = $reply['order_id'];
		$detail['round_off'] = "";
		$objOrder->PlaceOrderPanel($detail);

		/* Ensure portal flags after place */
		$portalUpd = array(
			"channel_partner_order_flag" => 1,
			"cp_portal_order_flag" => 1,
		);
		$colPortal = @mysqli_query($db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'cp_portal_order_flag'");
		if (!($colPortal && mysqli_num_rows($colPortal) > 0)) {
			unset($portalUpd['cp_portal_order_flag']);
		}
		$colMode = @mysqli_query($db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'cp_order_mode'");
		if ($colMode && mysqli_num_rows($colMode) > 0) {
			$portalUpd['cp_order_mode'] = $cp_mode;
		}
		if ($cp_end_id > 0) {
			$portalUpd['channel_partner_customer_id'] = $cp_end_id;
		}
		$db->rp_update("orders", $portalUpd, "id='" . (int) $reply['order_id'] . "'", 0);

		$db->addSuccessMessage("Order placed successfully.");
		$db->rp_location("dealer_orders_manage.php?type=channel_partner&msg=inserted");
	} else {
		$err = isset($reply['ack_msg']) ? $reply['ack_msg'] : "Order save failed.";
		if (!empty($reply['developer_msg']) && $reply['developer_msg'] != $err) {
			$err .= " (" . $reply['developer_msg'] . ")";
		}
		$db->addErrorMessage($err);
		$db->rp_location("channel_partner_order_simple.php?cp_mode=" . $cp_mode);
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
.cp-order-banner {
	background: #5bc0de;
	color: #fff;
	padding: 12px 16px;
	border-radius: 2px;
	margin-bottom: 18px;
	font-weight: 600;
}
.cp-order-section-title {
	font-size: 13px;
	font-weight: 700;
	letter-spacing: 0.4px;
	color: #555;
	margin: 18px 0 10px;
	text-transform: uppercase;
}
.cp-product-table th {
	background: #f5f5f5;
	font-weight: 600;
}
.cp-product-table td, .cp-product-table th {
	vertical-align: middle !important;
}
.cp-actions {
	margin-top: 24px;
	text-align: center;
}
.cp-actions .btn {
	min-width: 140px;
	margin: 0 6px;
	text-transform: uppercase;
	font-weight: 600;
}
</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1>
					<a href="dealer_orders_manage.php?type=channel_partner" class="primary">
						<i class="fa fa-arrow-circle-o-left" style="font-size:22px!important;"></i>
					</a>
					&nbsp;<?php $db->pageBar($page_hierarchy); ?>
				</h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<?php $db->printErrorMessage(); ?>
					<?php $db->printSuccessMessage(); ?>

					<div class="portlet light">
						<div class="portlet-body">
							<div class="cp-order-banner">
								Order Details - Please fill in the <?php echo ($cp_mode == 'own') ? 'product' : 'customer and product'; ?> information below.
							</div>

							<form method="post" action="" id="cpSimpleOrderForm" autocomplete="off">
								<input type="hidden" name="cp_mode" value="<?php echo htmlspecialchars($cp_mode); ?>">
								<input type="hidden" name="c_type" value="channel_partner">
								<input type="hidden" name="channel_partner_order_flag" value="1">
								<input type="hidden" name="cp_portal_order_flag" value="1">
								<input type="hidden" name="customer_id" id="customer_id" value="<?php echo (int) $cp_login_id; ?>">

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Order No</label>
											<input type="text" class="form-control" value="<?php echo htmlspecialchars($order_no_display); ?>" readonly>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Order Date</label>
											<input type="text" class="form-control" name="order_date" id="order_date" value="<?php echo htmlspecialchars($order_date); ?>" readonly>
										</div>
									</div>
								</div>

								<?php if ($cp_mode == 'customer') { ?>
								<div class="cp-order-section-title">Customer Selection</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Customer <code>*</code></label>
											<select class="form-control" name="channel_partner_customer_id" id="channel_partner_customer_id" required>
												<option value="">Select Customer</option>
												<?php
												foreach ($cp_customers as $cust) {
													$label = trim($cust['company_name']);
													if ($label == '' && !empty($cust['person_name'])) {
														$label = $cust['person_name'];
													}
													if ($label == '' && !empty($cust['mobile_no'])) {
														$label = $cust['mobile_no'];
													}
													$sel = ($selected_cp_customer_id == $cust['id']) ? 'selected' : '';
													echo '<option value="' . (int) $cust['id'] . '" ' . $sel . '>' . htmlspecialchars($label) . '</option>';
												}
												?>
											</select>
											<?php if (empty($cp_customers)) { ?>
												<p class="help-block text-danger">No customers found. Please add from <a href="channel_partner_customer_manage.php">My Customers</a>.</p>
											<?php } ?>
										</div>
									</div>
								</div>
								<?php } else { ?>
								<div class="cp-order-section-title">Order For</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Channel Partner</label>
											<input type="text" class="form-control" value="<?php echo htmlspecialchars($cp_exec_d['company_name'] . (!empty($cp_exec_d['cname']) ? ' - ' . $cp_exec_d['cname'] : '')); ?>" readonly>
										</div>
									</div>
								</div>
								<?php } ?>

								<div class="cp-order-section-title">Product Information</div>
								<div class="table-responsive">
									<table class="table table-bordered cp-product-table" id="cpProductTable">
										<thead>
											<tr>
												<th style="width:55%;">Product <code>*</code></th>
												<th style="width:25%;">Qty <code>*</code></th>
												<th style="width:20%;">Action</th>
											</tr>
										</thead>
										<tbody id="cpProductBody">
											<tr class="cp-product-row">
												<td>
													<select class="form-control cp-product-select noSelect2" name="line_product[]" style="width:100%;">
														<option value="">Select Product</option>
													</select>
												</td>
												<td>
													<input type="text" class="form-control cp-qty" name="line_qty[]" placeholder="Qty" onkeypress="return isNumberKey(event);">
												</td>
												<td>
													<button type="button" class="btn btn-success btn-sm cp-add-row" title="Add row"><i class="fa fa-plus"></i></button>
													<button type="button" class="btn btn-danger btn-sm cp-remove-row" title="Remove" style="display:none;"><i class="fa fa-minus"></i></button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>

								<div id="cpHiddenItems"></div>

								<div class="cp-actions">
									<button type="submit" name="btn_save" value="1" class="btn btn-success" id="btnSubmitOrder">Submit Order</button>
									<a href="dealer_orders_manage.php?type=channel_partner" class="btn btn-default">Cancel</a>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript">
var cpCustomerId = "<?php echo (int) $cp_login_id; ?>";
var productOptionsHtml = '<option value="">Select Product</option>';

function isNumberKey(evt) {
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	}
	return true;
}

function initCpProductSelect($el) {
	if (!$el || !$el.length) {
		return;
	}
	if ($el.hasClass("select2-hidden-accessible") || $el.data("select2")) {
		try { $el.select2("destroy"); } catch (e) {}
	}
	$el.select2({
		width: "100%",
		placeholder: "Select Product"
	});
}

function loadCpProducts() {
	$.ajax({
		url: "ajax_get_cp_products.php",
		type: "POST",
		data: { cid: cpCustomerId },
		success: function (html) {
			productOptionsHtml = html;
			$("#cpProductBody .cp-product-select").each(function () {
				var $sel = $(this);
				var cur = $sel.val();
				if ($sel.hasClass("select2-hidden-accessible") || $sel.data("select2")) {
					try { $sel.select2("destroy"); } catch (e) {}
				}
				$sel.html(productOptionsHtml);
				if (cur) {
					$sel.val(cur);
				}
				initCpProductSelect($sel);
			});
		},
		error: function () {
			toastr.error("Product list load failed. Please refresh.");
		}
	});
}

function refreshRowActions() {
	var rows = $("#cpProductBody .cp-product-row");
	rows.each(function (idx) {
		$(this).find(".cp-remove-row").toggle(rows.length > 1);
		$(this).find(".cp-add-row").toggle(idx === rows.length - 1);
	});
}

function getRowProductId($row) {
	var $sel = $row.find(".cp-product-select");
	var pwpId = $sel.val();
	if ((!pwpId || pwpId === "" || pwpId === null) && ($sel.hasClass("select2-hidden-accessible") || $sel.data("select2"))) {
		try {
			var d = $sel.select2("data");
			if (d) {
				if ($.isArray(d) && d.length && d[0].id) {
					pwpId = d[0].id;
				} else if (d.id) {
					pwpId = d.id;
				}
			}
		} catch (e) {}
	}
	if ((!pwpId || pwpId === "") ) {
		pwpId = $sel.find("option:selected").val();
	}
	return (pwpId === null || pwpId === undefined) ? "" : String(pwpId);
}

function prepareCpOrderSubmit() {
	var $wrap = $("#cpHiddenItems");
	$wrap.empty();
	var count = 0;

	$("#cpProductBody .cp-product-row").each(function () {
		var $row = $(this);
		var $sel = $row.find(".cp-product-select");
		var pwpId = getRowProductId($row);
		var qty = $.trim($row.find(".cp-qty").val());

		if (pwpId !== "" && pwpId !== "0" && qty !== "" && parseFloat(qty) > 0) {
			/* Keep native select in sync for Select2 */
			$sel.val(pwpId);
			$wrap.append($('<input type="hidden" name="line_product[]" />').val(pwpId));
			$wrap.append($('<input type="hidden" name="line_qty[]" />').val(qty));
			count++;
		}
	});

	/* Avoid duplicate empty posts from visible fields */
	if (count > 0) {
		$("#cpProductBody .cp-product-select").removeAttr("name");
		$("#cpProductBody .cp-qty").removeAttr("name");
	}
	return count > 0;
}

$(document).ready(function () {
	loadCpProducts();
	refreshRowActions();

	if ($.fn.datepicker) {
		$("#order_date").datepicker({
			format: "dd-mm-yyyy",
			autoclose: true,
			todayHighlight: true
		});
	}

	$(document).on("click", ".cp-add-row", function () {
		var $src = $(this).closest("tr");
		var $row = $src.clone();
		$row.find(".select2-container").remove();
		var $sel = $row.find(".cp-product-select");
		$sel.removeClass("select2-hidden-accessible").removeAttr("data-select2-id").removeAttr("aria-hidden").removeAttr("tabindex");
		$sel.find("option").removeAttr("data-select2-id");
		$sel.addClass("noSelect2");
		$sel.attr("name", "line_product[]");
		$sel.html(productOptionsHtml).val("");
		$row.find(".cp-qty").attr("name", "line_qty[]").val("");
		$("#cpProductBody").append($row);
		initCpProductSelect($sel);
		refreshRowActions();
	});

	$(document).on("click", ".cp-remove-row", function () {
		if ($("#cpProductBody .cp-product-row").length <= 1) {
			return;
		}
		var $row = $(this).closest("tr");
		var $sel = $row.find(".cp-product-select");
		if ($sel.hasClass("select2-hidden-accessible") || $sel.data("select2")) {
			try { $sel.select2("destroy"); } catch (e) {}
		}
		$row.find(".select2-container").remove();
		$row.remove();
		refreshRowActions();
	});

	$("#cpSimpleOrderForm").on("submit", function (e) {
		<?php if ($cp_mode == 'customer') { ?>
		if (!$("#channel_partner_customer_id").val()) {
			toastr.error("Please Select Customer.");
			e.preventDefault();
			return false;
		}
		<?php } ?>
		if (!prepareCpOrderSubmit()) {
			toastr.error("Please add at least one Product with Qty.");
			/* restore names if blocked */
			$("#cpProductBody .cp-product-select").attr("name", "line_product[]");
			$("#cpProductBody .cp-qty").attr("name", "line_qty[]");
			e.preventDefault();
			return false;
		}
		return true;
	});
});
</script>
</body>
</html>
