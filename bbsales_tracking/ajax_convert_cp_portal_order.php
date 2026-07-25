<?php
/**
 * Admin: Convert CP portal order → routine Channel Partner order (regular format + pricing).
 */
$page_id = 565;
$page_slug = 'page_order';
include("connect.php");
include("../include/orders.class.php");
include("../include/product.class.php");

header('Content-Type: application/json; charset=UTF-8');

$replay = array("ack" => 0, "ack_msg" => "Convert failed.");

if (function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db)) {
	$replay['ack_msg'] = "Only admin can convert portal orders.";
	echo json_encode($replay);
	require_once "disconnect.php";
	exit;
}

$order_id = isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : 0;
if ($order_id <= 0) {
	$replay['ack_msg'] = "Invalid order.";
	echo json_encode($replay);
	require_once "disconnect.php";
	exit;
}

$colCheck = @mysqli_query($db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'cp_portal_order_flag'");
if (!($colCheck && mysqli_num_rows($colCheck) > 0)) {
	$replay['ack_msg'] = "Please run db_sync (cp_portal_order_flag missing).";
	echo json_encode($replay);
	require_once "disconnect.php";
	exit;
}

$order_r = $db->rp_getData(
	"orders",
	"*",
	"id='" . $order_id . "' AND isDelete=0",
	"",
	0
);
if (!$order_r) {
	$replay['ack_msg'] = "Order not found.";
	echo json_encode($replay);
	require_once "disconnect.php";
	exit;
}
$order = mysqli_fetch_assoc($order_r);

if ((int) $order['channel_partner_order_flag'] !== 1) {
	$replay['ack_msg'] = "Not a Channel Partner order.";
	echo json_encode($replay);
	require_once "disconnect.php";
	exit;
}
if ((int) $order['cp_portal_order_flag'] !== 1) {
	$replay['ack_msg'] = "Order already converted / not a portal order.";
	echo json_encode($replay);
	require_once "disconnect.php";
	exit;
}

$cp_id = (int) $order['customer_id'];
$price_list_id = (int) $db->rp_getValue("executive", "price_list_id", "id='" . $cp_id . "' AND isDelete=0", 0);
$productObj = new Product();

/* Refresh line pricing from CP price list (regular Armor pricing) */
$items_r = $db->rp_getData("order_product_item", "*", "order_id='" . $order_id . "' AND isDelete=0", "", 0);
if ($items_r) {
	while ($it = mysqli_fetch_assoc($items_r)) {
		$pid = (int) $it['pro_id'];
		$weight_id = $it['weight_id'];
		$qty = (float) $it['pro_qty'];
		if ($pid <= 0 || $qty <= 0) {
			continue;
		}

		$original_price = $it['original_price'];
		$unitprice = $it['unitprice'];
		$discount = $it['discount'];

		if ($price_list_id > 0) {
			$pl_r = $db->rp_getData(
				"product_price_list",
				"price,discounted_price,discount",
				"pid='" . $pid . "' AND weight_id='" . $weight_id . "' AND price_list_id='" . $price_list_id . "' AND isDelete=0",
				"",
				0
			);
			if ($pl_r && $pl = mysqli_fetch_assoc($pl_r)) {
				if ($pl['price'] !== '' && $pl['price'] !== null) {
					$original_price = $pl['price'];
				}
				if ($pl['discounted_price'] !== '' && $pl['discounted_price'] !== null) {
					$unitprice = $pl['discounted_price'];
				}
				if ($pl['discount'] !== '' && $pl['discount'] !== null) {
					$discount = $pl['discount'];
				}
			}
		} else {
			$details = $productObj->aj_getProductDetail($pid, $cp_id);
			if (!empty($details)) {
				foreach ($details as $d) {
					if ((string) $d['weight_id'] === (string) $weight_id) {
						$original_price = $d['orignal_price'];
						$unitprice = $d['sell_price'];
						$discount = isset($d['discountPer']) ? $d['discountPer'] : $discount;
						break;
					}
				}
			}
		}

		$totalprice = $db->rp_num($qty * (float) $unitprice);
		$discount_amount = ((float) $original_price > 0) ? $db->rp_num((float) $original_price - (float) $unitprice) : 0;

		$db->rp_update(
			"order_product_item",
			array(
				"unitprice" => $unitprice,
				"original_price" => $original_price,
				"totalprice" => $totalprice,
				"discount" => $discount,
				"discount_amount" => $discount_amount,
				"modified_date" => date("Y-m-d H:i:s"),
			),
			"id='" . (int) $it['id'] . "'",
			0
		);
	}
}

/* Recalc order totals via PlaceOrderPanel */
$objOrder = new Order();
$detail = array(
	"order_id" => $order_id,
	"cid" => $cp_id,
	"customer_id" => $cp_id,
	"cash_discount" => isset($order['cash_discount']) ? $order['cash_discount'] : "",
	"cash_discount_amount" => isset($order['cash_discount_amount']) ? $order['cash_discount_amount'] : "",
	"additional_discount" => isset($order['additional_discount']) ? $order['additional_discount'] : "",
	"additional_discount_amount" => isset($order['additional_discount_amount']) ? $order['additional_discount_amount'] : "",
	"remarks" => isset($order['remarks']) ? $order['remarks'] : "",
	"chalan_no" => isset($order['chalan_no']) ? $order['chalan_no'] : "",
	"po_no" => isset($order['po_no']) ? $order['po_no'] : "",
	"po_date" => isset($order['po_date']) ? $order['po_date'] : date('Y-m-d'),
	"terms_comdition" => isset($order['terms_comdition']) ? $order['terms_comdition'] : "",
	"faithfully" => isset($order['faithfully']) ? $order['faithfully'] : "",
	"transport_name" => isset($order['transport_name']) ? $order['transport_name'] : "",
	"transport_through" => isset($order['transport_through']) ? $order['transport_through'] : "",
	"transport_charge" => isset($order['transport_charge']) ? $order['transport_charge'] : "",
	"billing_address" => isset($order['billing_address']) ? $order['billing_address'] : "",
	"shipping_address" => isset($order['shipping_address']) ? $order['shipping_address'] : "",
	"packing_charge" => isset($order['packing_charge']) ? $order['packing_charge'] : "",
	"name_gstin" => isset($order['gst']) ? $order['gst'] : "",
	"vendor_code" => isset($order['vendor_code']) ? $order['vendor_code'] : "",
	"tendor_code" => isset($order['tendor_code']) ? $order['tendor_code'] : "",
	"round_off" => isset($order['round_off']) ? $order['round_off'] : "",
	"type_of_company" => isset($order['type_of_company']) ? $order['type_of_company'] : "",
	"terms_condition_id" => isset($order['terms_condition_id']) ? $order['terms_condition_id'] : "",
	"max_dispatch_date" => isset($order['max_dispatch_date']) ? $order['max_dispatch_date'] : date('Y-m-d'),
	"gst_apply_flag" => isset($order['gst_apply_flag']) ? $order['gst_apply_flag'] : "",
	"tcs_apply_flag" => isset($order['tcs_apply_flag']) ? $order['tcs_apply_flag'] : "",
	"cash_discount_flag" => 0,
	"sales_executive_id" => isset($order['sales_id']) ? $order['sales_id'] : "",
);
$objOrder->PlaceOrderPanel($detail);

/* Move into routine CP order pipeline */
$upd = array(
	"cp_portal_order_flag" => 0,
	"channel_partner_order_flag" => 1,
	"status" => 0,
);
$ok = $db->rp_update("orders", $upd, "id='" . $order_id . "'", 0);

if ($ok) {
	$module_name = "Order";
	$flag = "Web";
	$cp_name = $db->rp_getValue("executive", "company_name", "id='" . $cp_id . "'", 0);
	$log_description = $module_name . " " . $order['order_no'] . " Converted from CP Portal (" . $cp_name . ") to Channel Partner Order with pricing By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
	$user_id = isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) ? $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] : 0;
	$db->insertLog("orders", $order_id, "status_change", "", array(), 0, $log_description, $flag, $module_name, $user_id, $cp_id);

	$replay = array(
		"ack" => 1,
		"ack_msg" => "Converted to regular Channel Partner Order with pricing.",
		"order_id" => $order_id,
		/* Open regular Armor order format (edit) with pricing */
		"redirect" => "orders_crud.php?mode=edit&id=" . $order_id . "&c_type=channel_partner",
	);
} else {
	$replay['ack_msg'] = "Update failed.";
}

echo json_encode($replay);
require_once "disconnect.php";
?>
