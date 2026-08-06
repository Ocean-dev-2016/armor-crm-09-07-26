<?php
/**
 * Channel Partner simple order form (two separate modes):
 *  - cp_mode=own      -> CP's own order (no end-customer)
 *  - cp_mode=customer -> order for CP's customer
 */
$page_id = 565;
$page_slug = 'page_order';
include("connect.php");
include('../include/product.class.php');
include("../include/orders.class.php");
require_once dirname(__FILE__) . '/../include/class.channel_partner_stock.php';

$objOrder = new Order();
$cpStockObj = new ChannelPartnerStock($db);

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
if ($cp_mode === 'own') {
	$db->addErrorMessage("Channel Partner own order can no longer be created from CP login. Please contact Armor Fire Admin / Sales Executive.");
	$db->rp_location("channel_partner_order_manage.php");
}

$selected_cp_customer_id = 0;
if (isset($_REQUEST['cp_customer_id']) && (int) $_REQUEST['cp_customer_id'] > 0) {
	$selected_cp_customer_id = (int) $_REQUEST['cp_customer_id'];
} else if (isset($_REQUEST['channel_partner_customer_id']) && (int) $_REQUEST['channel_partner_customer_id'] > 0) {
	$selected_cp_customer_id = (int) $_REQUEST['channel_partner_customer_id'];
}

$edit_order_id = isset($_REQUEST['edit_order_id']) ? (int) $_REQUEST['edit_order_id'] : 0;
$edit_order_no = '';
$edit_gst_flag = 1;
$edit_lines_js = array();
if ($edit_order_id > 0 && $cp_mode == 'customer') {
	$eo = $db->rp_getData(
		"orders",
		"*",
		"id='" . $edit_order_id . "' AND customer_id='" . $cp_login_id . "' AND channel_partner_order_flag=1 AND cp_order_mode='customer' AND isDelete=0 AND status!=-1",
		"",
		0
	);
	if (!$eo || !($edit_ord = mysqli_fetch_assoc($eo))) {
		$db->addErrorMessage("Order not found for edit.");
		$db->rp_location("channel_partner_order_manage.php");
	}
	$st = (int) $edit_ord['status'];
	$pf = (int) $edit_ord['payment_received_flag'];
	$pa = (float) $edit_ord['payment_received_amount'];
	if (($pf === 1 && $pa > 0) || ($st >= 5 && $st != 3 && $st != -2)) {
		$db->addErrorMessage("Only Pending orders can be edited.");
		$db->rp_location("channel_partner_order_manage.php");
	}
	$selected_cp_customer_id = (int) $edit_ord['channel_partner_customer_id'];
	$edit_order_no = isset($edit_ord['order_no']) ? $edit_ord['order_no'] : '';
	/* Exact 0 = Without GST; missing column / null → default With GST */
	$edit_gst_flag = 1;
	if (array_key_exists('gst_apply_flag', $edit_ord) && $edit_ord['gst_apply_flag'] !== null && $edit_ord['gst_apply_flag'] !== '') {
		$edit_gst_flag = ((int) $edit_ord['gst_apply_flag'] === 0) ? 0 : 1;
	}
	$eir = $db->rp_getData("order_product_item", "*", "order_id='" . $edit_order_id . "' AND isDelete=0", "id ASC", 0);
	if ($eir) {
		while ($eit = mysqli_fetch_assoc($eir)) {
			$pid = (int) $eit['pro_id'];
			$weightId = isset($eit['weight_id']) ? $eit['weight_id'] : '';
			$pwp = (int) $db->rp_getValue(
				"product_weight_price",
				"id",
				"product_id='" . $pid . "' AND weight_id='" . $db->clean($weightId) . "' AND isDelete=0",
				0
			);
			/* App-saved rows sometimes have weight_id -1 / blank — fallback by product_id */
			if ($pwp <= 0 && $pid > 0) {
				$pwp = (int) $db->rp_getValue(
					"product_weight_price",
					"id",
					"product_id='" . $pid . "' AND isDelete=0",
					0
				);
			}
			$lineBase = isset($eit['totalprice']) ? (float) $eit['totalprice'] : ((float) $eit['pro_qty'] * (float) $eit['unitprice']);
			$lineGst = isset($eit['igst_amount']) ? (float) $eit['igst_amount'] : 0;
			$gstPct = (float) $db->rp_getValue("product", "igst", "id='" . $pid . "' AND isDelete=0", 0);
			$edit_lines_js[] = array(
				'pwp_id' => $pwp,
				'qty' => (float) $eit['pro_qty'],
				'rate' => (float) $eit['unitprice'],
				'discount' => isset($eit['discount']) ? (float) $eit['discount'] : 0,
				'amount' => round($lineBase, 2),
				'gst_amount' => round($lineGst, 2),
				'gst_percent' => $gstPct,
			);
		}
	}
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

$page_title = ($edit_order_id > 0)
	? ("Edit Customer Order" . ($edit_order_no != '' ? ' — ' . $edit_order_no : ''))
	: (($cp_mode == 'own') ? "Add My Order (to Armor)" : "Customer Order (CP Format & Pricing)");
$page_hierarchy = array(
	array("link" => "", "title" => "Sales & Marketing"),
	array("link" => "channel_partner_customer_manage.php", "title" => "Channel Partner"),
	array("link" => "channel_partner_order_manage.php", "title" => "Manage Customer Order"),
	array("link" => "channel_partner_order_simple.php?cp_mode=" . $cp_mode . ($edit_order_id > 0 ? '&edit_order_id=' . $edit_order_id : ''), "title" => $page_title)
);

/* ---------- SUBMIT ---------- */
if (isset($_REQUEST['btn_save']) || isset($_REQUEST['submit'])) {
	$post_edit_check = isset($_REQUEST['edit_order_id']) ? (int) $_REQUEST['edit_order_id'] : 0;
	$canSave = ($post_edit_check > 0)
		? (isset($rights['update_flag']) && (int) $rights['update_flag'] == 1) || (isset($rights['insert_flag']) && (int) $rights['insert_flag'] == 1)
		: (isset($rights['insert_flag']) && (int) $rights['insert_flag'] == 1);
	if (!$canSave) {
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$cp_mode = (isset($_REQUEST['cp_mode']) && $_REQUEST['cp_mode'] == 'own') ? 'own' : 'customer';

	/* Prefer visible form fields (line_product = product_weight_price.id) */
	$line_products = isset($_REQUEST['line_product']) ? $_REQUEST['line_product'] : array();
	$line_qtys = isset($_REQUEST['line_qty']) ? $_REQUEST['line_qty'] : array();
	$line_prices = isset($_REQUEST['line_price']) ? $_REQUEST['line_price'] : array();
	$line_discounts = isset($_REQUEST['line_discount']) ? $_REQUEST['line_discount'] : array();
	if (!is_array($line_products)) {
		$line_products = array();
	}
	if (!is_array($line_qtys)) {
		$line_qtys = array();
	}
	if (!is_array($line_prices)) {
		$line_prices = array();
	}
	if (!is_array($line_discounts)) {
		$line_discounts = array();
	}
	$gst_apply_flag = isset($_REQUEST['gst_apply_flag']) ? (int) $_REQUEST['gst_apply_flag'] : 1;
	if ($gst_apply_flag !== 0) {
		$gst_apply_flag = 1;
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

		/* Editable rate from form (CP pricing) */
		if (isset($line_prices[$i]) && $line_prices[$i] !== '' && is_numeric($line_prices[$i])) {
			$sell_price = (float) $line_prices[$i];
			$original_price = (float) $line_prices[$i];
		}

		$discount = 0;
		if (isset($line_discounts[$i]) && $line_discounts[$i] !== '' && is_numeric($line_discounts[$i])) {
			$discount = (float) $line_discounts[$i];
			if ($discount < 0) {
				$discount = 0;
			}
			if ($discount > 100) {
				$discount = 100;
			}
		} else if ($match && isset($match['discountPer'])) {
			$discount = $match['discountPer'];
		}

		/* Apply discount % on rate → net sell price */
		$rate_before_disc = (float) $sell_price;
		if ((float) $discount > 0) {
			$sell_price = $rate_before_disc - (($rate_before_disc * (float) $discount) / 100);
		}
		$discount_amount = $rate_before_disc - (float) $sell_price;

		$inner = isset($pwp['inner_size']) && (float) $pwp['inner_size'] > 0 ? (float) $pwp['inner_size'] : 1;
		$outer = isset($pwp['outer_size']) && (float) $pwp['outer_size'] > 0 ? (float) $pwp['outer_size'] : 1;
		$bag = $qty / $inner;
		$cartoon = $bag / $outer;
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

	/* Customer orders must have enough CP stock */
	if ($cp_mode == 'customer') {
		$stockCheckItems = array();
		foreach ($item as $itRow) {
			$stockCheckItems[] = array(
				"pid" => $itRow['pid'],
				"weight_id" => $itRow['weight_id'],
				"qty" => $itRow['qty'],
				"pro_name" => $itRow['pro_name'],
			);
		}
		$stockCheck = $cpStockObj->validateItemsStock($cp_login_id, $stockCheckItems);
		if (empty($stockCheck['ack'])) {
			$db->addErrorMessage(isset($stockCheck['ack_msg']) ? $stockCheck['ack_msg'] : "Insufficient stock.");
			$db->rp_location("channel_partner_order_simple.php?cp_mode=customer");
		}
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

	/* ---------- EDIT existing Pending order ---------- */
	$post_edit_id = isset($_REQUEST['edit_order_id']) ? (int) $_REQUEST['edit_order_id'] : 0;
	if ($post_edit_id > 0 && $cp_mode == 'customer') {
		if (empty($item)) {
			$db->addErrorMessage("Please add at least one product.");
			$db->rp_location("channel_partner_order_simple.php?cp_mode=customer&edit_order_id=" . $post_edit_id);
		}
		$productsPayload = array();
		for ($i = 0; $i < count($line_products); $i++) {
			$pwp_id = (int) $line_products[$i];
			$qty = isset($line_qtys[$i]) ? (float) $line_qtys[$i] : 0;
			if ($pwp_id <= 0 || $qty <= 0) {
				continue;
			}
			$productsPayload[] = array(
				'pwp_id' => $pwp_id,
				'qty' => $qty,
				'rate' => isset($line_prices[$i]) ? $line_prices[$i] : null,
				'discount' => isset($line_discounts[$i]) ? $line_discounts[$i] : 0,
			);
		}
		require_once dirname(__FILE__) . '/../include/class.channel_partner_order.php';
		$objCpOrd = new ChannelPartnerOrder();
		$updRes = $objCpOrd->UpdateCustomerOrder(array(
			'channel_partner_id' => $cp_login_id,
			'order_id' => $post_edit_id,
			'channel_partner_customer_id' => $cp_end_id,
			'gst_apply_flag' => $gst_apply_flag,
			'address' => $shipping_address,
			'shipping_address' => $shipping_address,
			'billing_address' => $billing_address,
			'products' => $productsPayload,
		));
		if (!empty($updRes['ack'])) {
			$db->addSuccessMessage(isset($updRes['ack_msg']) ? $updRes['ack_msg'] : "Order updated.");
			$db->rp_location("channel_partner_order_manage.php");
		}
		$db->addErrorMessage(isset($updRes['ack_msg']) ? $updRes['ack_msg'] : "Order update failed.");
		$db->rp_location("channel_partner_order_simple.php?cp_mode=customer&edit_order_id=" . $post_edit_id);
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
	$detail['gst_apply_flag'] = $gst_apply_flag;
	$detail['tcs_apply_flag'] = 0;
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
	/* Print header/footer from CP settings */
	$detail['terms_comdition'] = !empty($cp_exec_d['cp_print_header']) ? $cp_exec_d['cp_print_header'] : "";
	$detail['faithfully'] = !empty($cp_exec_d['cp_print_footer']) ? $cp_exec_d['cp_print_footer'] : "";
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
		/* Persist header/footer on order */
		if (!empty($detail['terms_comdition']) || !empty($detail['faithfully'])) {
			$portalUpd['terms_comdition'] = $detail['terms_comdition'];
			$portalUpd['faithfully'] = $detail['faithfully'];
		}
		$colGst = @mysqli_query($db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'gst_apply_flag'");
		if ($colGst && mysqli_num_rows($colGst) > 0) {
			$portalUpd['gst_apply_flag'] = $gst_apply_flag;
		}
		$db->rp_update("orders", $portalUpd, "id='" . (int) $reply['order_id'] . "'", 0);

		/* Debit CP stock for customer orders */
		if ($cp_mode == 'customer') {
			$debitRes = $cpStockObj->debitForCustomerOrder((int) $reply['order_id']);
			if (empty($debitRes['ack'])) {
				$db->addErrorMessage("Order saved but stock debit failed: " . (isset($debitRes['ack_msg']) ? $debitRes['ack_msg'] : ''));
				$db->rp_location("channel_partner_customer_manage.php");
			}
		}

		$db->addSuccessMessage("Order placed successfully." . ($cp_mode == 'customer' ? " Stock deducted." : ""));
		$db->rp_location("channel_partner_order_print.php?order_id=" . (int) $reply['order_id'] . "&saved=1");
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
.cp-order-wrap { width: 100%; max-width: none; margin: 0; }
.page-content > .container,
.page-head > .container { width: 100% !important; max-width: 100% !important; }
.cp-order-banner {
	background: linear-gradient(90deg, #1a6b8a 0%, #2a9bb5 100%);
	color: #fff;
	padding: 14px 18px;
	border-radius: 4px;
	margin-bottom: 20px;
	font-size: 14px;
	line-height: 1.45;
}
.cp-order-section {
	border: 1px solid #e4e7eb;
	border-radius: 4px;
	padding: 16px 18px 8px;
	margin-bottom: 18px;
	background: #fff;
}
.cp-order-section-title {
	font-size: 12px;
	font-weight: 700;
	letter-spacing: 0.6px;
	color: #1a6b8a;
	margin: 0 0 14px;
	text-transform: uppercase;
	border-bottom: 2px solid #e8f4f8;
	padding-bottom: 8px;
}
.cp-gst-box {
	display: block;
	padding: 10px 14px;
	background: #f7fafb;
	border: 1px solid #d9e8ee;
	border-radius: 4px;
}
.cp-gst-box label {
	font-weight: 600;
	margin-right: 16px;
	cursor: pointer;
}
.cp-gst-box input[type="radio"] {
	margin-right: 4px;
}
.cp-product-table th {
	background: #1a6b8a;
	color: #fff;
	font-weight: 600;
	font-size: 12px;
	white-space: nowrap;
}
.cp-product-table td, .cp-product-table th {
	vertical-align: middle !important;
}
.cp-product-table .form-control {
	height: 34px;
	padding: 4px 8px;
}
.cp-product-table .select2-container { width: 100% !important; }
.cp-customer-wrap .select2-container { width: 100% !important; max-width: 420px; }
.cp-product-loader {
	display: none;
	padding: 18px;
	text-align: center;
	background: #f7fafb;
	border: 1px dashed #b8d4de;
	border-radius: 4px;
	margin-bottom: 12px;
	color: #1a6b8a;
	font-weight: 600;
}
.cp-product-loader img {
	height: 28px;
	margin-right: 8px;
	vertical-align: middle;
}
.cp-product-table-wrap { display: none; }
.cp-totals-box {
	background: #f7fafb;
	border: 1px solid #d9e8ee;
	border-radius: 4px;
	padding: 12px 16px;
	margin-top: 8px;
}
.cp-totals-box .cp-tot-row {
	display: flex;
	justify-content: space-between;
	padding: 4px 0;
	font-size: 14px;
}
.cp-totals-box .cp-tot-grand {
	font-size: 16px;
	font-weight: 700;
	color: #1a6b8a;
	border-top: 1px solid #c5dce6;
	margin-top: 6px;
	padding-top: 8px;
}
.cp-actions {
	margin-top: 22px;
	text-align: center;
}
.cp-actions .btn {
	min-width: 150px;
	margin: 0 6px;
	text-transform: uppercase;
	font-weight: 600;
	padding: 10px 18px;
}
.cp-amt-cell { font-weight: 600; text-align: right; white-space: nowrap; }
.cp-rate-input, .cp-disc-input, .cp-qty { text-align: right; }
</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1>
					<a href="channel_partner_customer_manage.php" class="primary">
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
							<div class="cp-order-wrap">
							<div class="cp-order-banner">
								<?php if ($cp_mode == 'customer') { ?>
									<strong>Customer Order</strong> — CP format &amp; your pricing. Choose With GST / Without GST. Stock deducts from My Stock after save. Print uses your SO/PI Header–Footer.
								<?php } else { ?>
									<strong>Own order to Armor</strong> — for supply stock. Prefer Armor customer login for own purchase orders.
								<?php } ?>
							</div>

							<form method="post" action="" id="cpSimpleOrderForm" autocomplete="off">
								<input type="hidden" name="cp_mode" value="<?php echo htmlspecialchars($cp_mode); ?>">
								<input type="hidden" name="c_type" value="channel_partner">
								<input type="hidden" name="channel_partner_order_flag" value="1">
								<input type="hidden" name="cp_portal_order_flag" value="1">
								<input type="hidden" name="customer_id" id="customer_id" value="<?php echo (int) $cp_login_id; ?>">
								<?php if ($edit_order_id > 0) { ?>
								<input type="hidden" name="edit_order_id" value="<?php echo (int) $edit_order_id; ?>">
								<?php } ?>
								<input type="hidden" name="gst_apply_flag" id="gst_apply_flag" value="<?php echo (int) $edit_gst_flag; ?>">

								<div class="cp-order-section">
									<div class="cp-order-section-title">Order Details</div>
									<div class="row">
										<div class="col-md-3 col-sm-6">
											<div class="form-group">
												<label>Order No</label>
												<input type="text" class="form-control" value="<?php echo htmlspecialchars($edit_order_id > 0 && $edit_order_no != '' ? $edit_order_no : $order_no_display); ?>" readonly>
											</div>
										</div>
										<div class="col-md-3 col-sm-6">
											<div class="form-group">
												<label>Order Date</label>
												<input type="text" class="form-control" name="order_date" id="order_date" value="<?php echo htmlspecialchars($order_date); ?>" readonly>
											</div>
										</div>
										<div class="col-md-4 col-sm-12">
											<div class="form-group">
												<label>Pricing Type <code>*</code></label>
												<div class="cp-gst-box">
													<label>
														<input type="radio" name="gst_apply_ui" value="1" <?php echo ((int) $edit_gst_flag === 1) ? 'checked' : ''; ?> class="cp-gst-radio"> With GST
													</label>
													<label>
														<input type="radio" name="gst_apply_ui" value="0" <?php echo ((int) $edit_gst_flag === 0) ? 'checked' : ''; ?> class="cp-gst-radio"> Without GST
													</label>
												</div>
											</div>
										</div>
									</div>
								</div>

								<?php if ($cp_mode == 'customer') { ?>
								<div class="cp-order-section">
									<div class="cp-order-section-title">Customer Selection</div>
									<div class="row">
										<div class="col-md-4 col-sm-6 cp-customer-wrap">
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
								</div>
								<?php } else { ?>
								<div class="cp-order-section">
									<div class="cp-order-section-title">Order For</div>
									<div class="row">
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Channel Partner</label>
												<input type="text" class="form-control" value="<?php echo htmlspecialchars($cp_exec_d['company_name'] . (!empty($cp_exec_d['cname']) ? ' - ' . $cp_exec_d['cname'] : '')); ?>" readonly>
											</div>
										</div>
									</div>
								</div>
								<?php } ?>

								<div class="cp-order-section">
									<div class="cp-order-section-title">Product Information</div>

									<div class="cp-product-loader" id="cpProductLoader">
										<img src="assets/admin/layout/img/ajax-loader.gif" alt=""> Loading products... Please wait
									</div>

									<div class="cp-product-table-wrap" id="cpProductTableWrap">
									<div class="table-responsive">
										<table class="table table-bordered cp-product-table" id="cpProductTable">
											<thead>
												<tr>
													<th style="width:32%;">Product <code>*</code></th>
													<th style="width:10%;">Qty <code>*</code></th>
													<th style="width:11%;">Rate <code>*</code></th>
													<th style="width:10%;">Disc %</th>
													<th style="width:9%;">GST %</th>
													<th style="width:14%;">Amount</th>
													<th style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="cpProductBody">
												<tr class="cp-product-row">
													<td>
														<select class="form-control cp-product-select noSelect2" name="line_product[]" style="width:100%;" disabled>
															<option value="">Select Product</option>
														</select>
													</td>
													<td>
														<input type="text" class="form-control cp-qty" name="line_qty[]" placeholder="Qty" onkeypress="return isNumberKey(event);">
													</td>
													<td>
														<input type="text" class="form-control cp-rate cp-rate-input" name="line_price[]" placeholder="0.00" onkeypress="return isNumberKey(event);">
													</td>
													<td>
														<input type="text" class="form-control cp-disc cp-disc-input" name="line_discount[]" placeholder="0" value="0" onkeypress="return isNumberKey(event);">
													</td>
													<td class="text-center">
														<span class="cp-gst-pct">—</span>
														<input type="hidden" class="cp-gst-val" value="0">
													</td>
													<td class="cp-amt-cell">
														<span class="cp-line-amt">0.00</span>
													</td>
													<td class="text-center">
														<button type="button" class="btn btn-success btn-sm cp-add-row" title="Add row"><i class="fa fa-plus"></i></button>
														<button type="button" class="btn btn-danger btn-sm cp-remove-row" title="Remove" style="display:none;"><i class="fa fa-minus"></i></button>
													</td>
												</tr>
											</tbody>
										</table>
									</div>

									<div class="row">
										<div class="col-md-4 col-md-offset-8">
											<div class="cp-totals-box">
												<div class="cp-tot-row"><span>Sub Total</span><span id="cpSubTotal">0.00</span></div>
												<div class="cp-tot-row" id="cpDiscRow" style="display:none;"><span>Total Discount</span><span id="cpDiscTotal">0.00</span></div>
												<div class="cp-tot-row" id="cpGstRow"><span>Estimated GST</span><span id="cpGstTotal">0.00</span></div>
												<div class="cp-tot-row cp-tot-grand"><span>Grand Total</span><span id="cpGrandTotal">0.00</span></div>
												<p class="help-block" style="margin:8px 0 0;font-size:11px;color:#888;">Final GST is calculated on save as per product &amp; customer state (same as Admin SO).</p>
											</div>
										</div>
									</div>
									</div>
								</div>

								<div id="cpHiddenItems"></div>

								<div class="cp-actions">
									<button type="submit" name="btn_save" value="1" class="btn btn-success" id="btnSubmitOrder" disabled><?php echo ($edit_order_id > 0) ? 'Update Order' : 'Submit Order'; ?></button>
									<a href="channel_partner_order_manage.php" class="btn btn-default">Cancel</a>
									<a href="channel_partner_print_settings.php" class="btn btn-info">SO/PI Format Settings</a>
								</div>
							</form>
							</div>
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
var cpProductsReady = false;
var cpEditLines = <?php echo json_encode($edit_lines_js); ?>;
var cpSkipProductFill = false;

function isNumberKey(evt) {
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	}
	return true;
}

function fmtAmt(n) {
	n = parseFloat(n) || 0;
	return n.toFixed(2);
}

function initCpProductSelect($el) {
	if (!$el || !$el.length || !cpProductsReady) {
		return;
	}
	if ($el.hasClass("select2-hidden-accessible") || $el.data("select2")) {
		try { $el.select2("destroy"); } catch (e) {}
	}
	$el.prop("disabled", false);
	$el.select2({
		width: "100%",
		placeholder: "Select Product",
		allowClear: true
	});
}

function showProductLoader(show) {
	if (show) {
		$("#cpProductLoader").show();
		$("#cpProductTableWrap").hide();
		$("#btnSubmitOrder").prop("disabled", true);
	} else {
		$("#cpProductLoader").hide();
		$("#cpProductTableWrap").show();
		$("#btnSubmitOrder").prop("disabled", false);
	}
}

function loadCpProducts() {
	showProductLoader(true);
	cpProductsReady = false;
	$.ajax({
		url: "ajax_get_cp_products.php",
		type: "POST",
		data: { cid: cpCustomerId },
		success: function (html) {
			productOptionsHtml = html || '<option value="">Select Product</option>';
			cpProductsReady = true;
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
			showProductLoader(false);
			applyCpEditLines();
		},
		error: function () {
			showProductLoader(false);
			toastr.error("Product list load failed. Please refresh.");
		}
	});
}

function applyCpEditLines() {
	if (!cpEditLines || !cpEditLines.length || !cpProductsReady) {
		return;
	}
	var lines = cpEditLines.slice();
	cpEditLines = [];
	var $first = $("#cpProductBody .cp-product-row").first();
	function fillRow($row, line) {
		var $sel = $row.find(".cp-product-select");
		var pwpId = line.pwp_id ? String(line.pwp_id) : "";
		/* Skip catalog overwrite so saved App/Web rate+discount stay intact */
		cpSkipProductFill = true;
		if (pwpId !== "" && pwpId !== "0") {
			$sel.val(pwpId).trigger("change");
		}
		cpSkipProductFill = false;

		var $opt = $sel.find("option:selected");
		var gst = parseFloat($opt.attr("data-gst"));
		if (isNaN(gst) || gst <= 0) {
			gst = parseFloat(line.gst_percent) || 0;
		}
		$row.find(".cp-gst-val").val(gst);
		$row.find(".cp-gst-pct").text(gst > 0 ? gst + "%" : "—");

		$row.find(".cp-qty").val(line.qty > 0 ? line.qty : "");
		$row.find(".cp-rate").val(line.rate > 0 ? fmtAmt(line.rate) : "");
		$row.find(".cp-disc").val((line.discount !== undefined && line.discount !== null) ? line.discount : 0);

		recalcRow($row);

		/* Fallback: if calc still 0 but DB has amount (App-saved line) */
		var baseNow = parseFloat($row.data("base")) || 0;
		if (baseNow <= 0 && line.amount && parseFloat(line.amount) > 0) {
			var withGst = parseInt($("#gst_apply_flag").val(), 10) === 1;
			var savedBase = parseFloat(line.amount) || 0;
			var savedGst = withGst ? (parseFloat(line.gst_amount) || 0) : 0;
			$row.find(".cp-line-amt").text(fmtAmt(savedBase + savedGst));
			$row.data("gross", savedBase);
			$row.data("discamt", 0);
			$row.data("base", savedBase);
			$row.data("gstamt", savedGst);
			recalcTotals();
		}
	}
	fillRow($first, lines[0]);
	for (var i = 1; i < lines.length; i++) {
		$first.find(".cp-add-row").trigger("click");
		var $row = $("#cpProductBody .cp-product-row").last();
		fillRow($row, lines[i]);
	}
	refreshRowActions();
	recalcTotals();
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

function fillRowFromProduct($row) {
	var $opt = $row.find(".cp-product-select option:selected");
	var rate = $opt.data("pricelist");
	var gst = $opt.data("gst");
	var disc = $opt.data("discount");
	if (rate === undefined || rate === null || rate === "") {
		rate = $opt.attr("data-pricelist");
	}
	if (gst === undefined || gst === null || gst === "") {
		gst = $opt.attr("data-gst");
	}
	if (disc === undefined || disc === null || disc === "") {
		disc = $opt.attr("data-discount");
	}
	rate = parseFloat(rate) || 0;
	gst = parseFloat(gst) || 0;
	disc = parseFloat(disc) || 0;
	$row.find(".cp-rate").val(rate > 0 ? fmtAmt(rate) : "");
	$row.find(".cp-disc").val(disc > 0 ? fmtAmt(disc) : "0");
	$row.find(".cp-gst-val").val(gst);
	$row.find(".cp-gst-pct").text(gst > 0 ? gst + "%" : "—");
	recalcRow($row);
}

function recalcRow($row) {
	var qty = parseFloat($row.find(".cp-qty").val()) || 0;
	var rate = parseFloat($row.find(".cp-rate").val()) || 0;
	var disc = parseFloat($row.find(".cp-disc").val()) || 0;
	if (disc < 0) { disc = 0; }
	if (disc > 100) { disc = 100; }
	var gst = parseFloat($row.find(".cp-gst-val").val()) || 0;
	var withGst = parseInt($("#gst_apply_flag").val(), 10) === 1;
	var gross = qty * rate;
	var discAmt = gross * disc / 100;
	var base = gross - discAmt;
	var gstAmt = withGst ? (base * gst / 100) : 0;
	$row.find(".cp-line-amt").text(fmtAmt(base + gstAmt));
	$row.data("gross", gross);
	$row.data("discamt", discAmt);
	$row.data("base", base);
	$row.data("gstamt", gstAmt);
	recalcTotals();
}

function recalcTotals() {
	var sub = 0, gstTot = 0, discTot = 0;
	$("#cpProductBody .cp-product-row").each(function () {
		sub += parseFloat($(this).data("base")) || 0;
		gstTot += parseFloat($(this).data("gstamt")) || 0;
		discTot += parseFloat($(this).data("discamt")) || 0;
	});
	var withGst = parseInt($("#gst_apply_flag").val(), 10) === 1;
	$("#cpSubTotal").text(fmtAmt(sub));
	$("#cpDiscTotal").text(fmtAmt(discTot));
	$("#cpDiscRow").toggle(discTot > 0);
	$("#cpGstTotal").text(fmtAmt(withGst ? gstTot : 0));
	$("#cpGstRow").toggle(withGst);
	$("#cpGrandTotal").text(fmtAmt(sub + (withGst ? gstTot : 0)));
}

function prepareCpOrderSubmit() {
	var $wrap = $("#cpHiddenItems");
	$wrap.empty();
	var count = 0;
	var gstFlag = $(".cp-gst-radio:checked").val();
	$("#gst_apply_flag").val(gstFlag === "0" ? "0" : "1");

	$("#cpProductBody .cp-product-row").each(function () {
		var $row = $(this);
		var $sel = $row.find(".cp-product-select");
		var pwpId = getRowProductId($row);
		var qty = $.trim($row.find(".cp-qty").val());
		var rate = $.trim($row.find(".cp-rate").val());
		var disc = $.trim($row.find(".cp-disc").val());

		if (pwpId !== "" && pwpId !== "0" && qty !== "" && parseFloat(qty) > 0) {
			$sel.val(pwpId);
			$wrap.append($('<input type="hidden" name="line_product[]" />').val(pwpId));
			$wrap.append($('<input type="hidden" name="line_qty[]" />').val(qty));
			$wrap.append($('<input type="hidden" name="line_price[]" />').val(rate !== "" ? rate : "0"));
			$wrap.append($('<input type="hidden" name="line_discount[]" />').val(disc !== "" ? disc : "0"));
			count++;
		}
	});

	if (count > 0) {
		$("#cpProductBody .cp-product-select").removeAttr("name");
		$("#cpProductBody .cp-qty").removeAttr("name");
		$("#cpProductBody .cp-rate").removeAttr("name");
		$("#cpProductBody .cp-disc").removeAttr("name");
	}
	return count > 0;
}

$(document).ready(function () {
	showProductLoader(true);
	loadCpProducts();
	refreshRowActions();
	recalcTotals();

	<?php if ($cp_mode == 'customer') { ?>
	if ($.fn.select2) {
		$("#channel_partner_customer_id").select2({
			width: "100%",
			placeholder: "Select Customer",
			allowClear: true
		});
	}
	<?php } ?>

	if ($.fn.datepicker) {
		$("#order_date").datepicker({
			format: "dd-mm-yyyy",
			autoclose: true,
			todayHighlight: true
		});
	}

	$(document).on("change", ".cp-gst-radio", function () {
		$("#gst_apply_flag").val($(this).val());
		$("#cpProductBody .cp-product-row").each(function () {
			recalcRow($(this));
		});
	});

	$(document).on("change", ".cp-product-select", function () {
		if (cpSkipProductFill) {
			return;
		}
		fillRowFromProduct($(this).closest("tr"));
	});

	$(document).on("keyup change", ".cp-qty, .cp-rate, .cp-disc", function () {
		recalcRow($(this).closest("tr"));
	});

	$(document).on("click", ".cp-add-row", function () {
		if (!cpProductsReady) {
			toastr.warning("Please wait, products are still loading.");
			return;
		}
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
		$row.find(".cp-rate").attr("name", "line_price[]").val("");
		$row.find(".cp-disc").attr("name", "line_discount[]").val("0");
		$row.find(".cp-gst-pct").text("—");
		$row.find(".cp-gst-val").val("0");
		$row.find(".cp-line-amt").text("0.00");
		$row.removeData("base").removeData("gstamt").removeData("discamt").removeData("gross");
		$("#cpProductBody").append($row);
		initCpProductSelect($sel);
		refreshRowActions();
		recalcTotals();
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
		recalcTotals();
	});

	$("#cpSimpleOrderForm").on("submit", function (e) {
		if (!cpProductsReady) {
			toastr.error("Please wait until products finish loading.");
			e.preventDefault();
			return false;
		}
		<?php if ($cp_mode == 'customer') { ?>
		if (!$("#channel_partner_customer_id").val()) {
			toastr.error("Please Select Customer.");
			e.preventDefault();
			return false;
		}
		<?php } ?>
		var missingRate = false;
		$("#cpProductBody .cp-product-row").each(function () {
			var pwpId = getRowProductId($(this));
			var qty = $.trim($(this).find(".cp-qty").val());
			var rate = $.trim($(this).find(".cp-rate").val());
			if (pwpId && qty && parseFloat(qty) > 0 && (rate === "" || isNaN(parseFloat(rate)))) {
				missingRate = true;
			}
		});
		if (missingRate) {
			toastr.error("Please enter Rate for all selected products.");
			e.preventDefault();
			return false;
		}
		if (!prepareCpOrderSubmit()) {
			toastr.error("Please add at least one Product with Qty.");
			$("#cpProductBody .cp-product-select").attr("name", "line_product[]");
			$("#cpProductBody .cp-qty").attr("name", "line_qty[]");
			$("#cpProductBody .cp-rate").attr("name", "line_price[]");
			$("#cpProductBody .cp-disc").attr("name", "line_discount[]");
			e.preventDefault();
			return false;
		}
		return true;
	});
});
</script>
</body>
</html>
<?php include("disconnect.php"); ?>
