<?php
/**
 * Channel Partner: mark own customer-order dispatch status + optional order status=5.
 */
$page_id = 565;
$page_slug = 'page_order';
include("connect.php");
header('Content-Type: application/json; charset=utf-8');

if (!function_exists('cp_is_channel_partner_login') || !cp_is_channel_partner_login($db)) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Only Channel Partner login can use this.'));
	exit;
}

$cpId = (int) cp_get_login_channel_partner_id();
$orderId = isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : 0;
$dispatchStatus = isset($_REQUEST['dispatch_status']) ? (int) $_REQUEST['dispatch_status'] : 0;
if ($dispatchStatus <= 0) {
	$dispatchStatus = (int) $db->rp_getValue("dispatch_order_status", "id", "isDelete=0", 0);
	if ($dispatchStatus <= 0) {
		mysqli_query($db->myconn, "INSERT INTO dispatch_order_status (name, isDelete, isActive) VALUES ('Dispatched', 0, 1)");
		$dispatchStatus = (int) mysqli_insert_id($db->myconn);
	}
	if ($dispatchStatus <= 0) {
		$dispatchStatus = 1;
	}
}

if ($orderId <= 0) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Invalid order.'));
	exit;
}

$order_r = $db->rp_getData(
	"orders",
	"id,order_no,customer_id,channel_partner_order_flag,cp_order_mode,channel_partner_customer_id,status",
	"id='" . $orderId . "' AND isDelete=0",
	"",
	0
);
if (!$order_r) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Order not found.'));
	exit;
}
$order = mysqli_fetch_assoc($order_r);
if ((int) $order['customer_id'] !== $cpId || (int) $order['channel_partner_order_flag'] !== 1) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Not your Channel Partner order.'));
	exit;
}
$mode = isset($order['cp_order_mode']) ? $order['cp_order_mode'] : '';
$endCust = isset($order['channel_partner_customer_id']) ? (int) $order['channel_partner_customer_id'] : 0;
if ($mode !== 'customer' && $endCust <= 0) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Dispatch applies to your customer orders only.'));
	exit;
}

$upd = array(
	"dispatch_status" => $dispatchStatus,
	"modified_date" => date('Y-m-d H:i:s'),
);
/* Mark order as Dispatched when a positive dispatch status chosen */
if ($dispatchStatus > 0 && (int) $order['status'] < 5) {
	$upd['status'] = 5;
}

$ok = $db->rp_update("orders", $upd, "id='" . $orderId . "' AND customer_id='" . $cpId . "'", 0);
if ($ok) {
	echo json_encode(array(
		'ack' => 1,
		'ack_msg' => 'Dispatch status updated for ' . $order['order_no'],
		'order_id' => $orderId,
		'dispatch_status' => $dispatchStatus,
	));
} else {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Update failed.'));
}
require_once 'disconnect.php';
