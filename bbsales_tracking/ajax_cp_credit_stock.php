<?php
/**
 * Armor admin: credit Channel Partner stock from a CP supply order.
 * Allowed only after Account Approval (status >= 4).
 * Optionally marks order as Dispatched (status=5).
 */
$page_id = 565;
$page_slug = 'page_order';
include("connect.php");
require_once dirname(__FILE__) . '/../include/class.channel_partner_stock.php';
header('Content-Type: application/json; charset=utf-8');

if (function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db)) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Only Armor admin can credit CP stock.'));
	exit;
}

$canUpdate = (
	(isset($rights['update_flag']) && (int) $rights['update_flag'] === 1)
	|| (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0)
);
if (!$canUpdate) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Update permission required.'));
	exit;
}

$orderId = isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : 0;
$markDispatch = isset($_REQUEST['mark_dispatch']) ? (int) $_REQUEST['mark_dispatch'] : 1;
if ($orderId <= 0) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Invalid order.'));
	exit;
}

$order_r = $db->rp_getData(
	"orders",
	"id,order_no,status,channel_partner_order_flag,cp_order_mode,channel_partner_customer_id,cp_stock_credited",
	"id='" . $orderId . "' AND isDelete=0",
	"",
	0
);
if (!$order_r) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Order not found.'));
	exit;
}
$order = mysqli_fetch_assoc($order_r);

if ((int) $order['channel_partner_order_flag'] !== 1) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Not a Channel Partner order.'));
	exit;
}

$mode = isset($order['cp_order_mode']) ? $order['cp_order_mode'] : '';
$endCust = isset($order['channel_partner_customer_id']) ? (int) $order['channel_partner_customer_id'] : 0;
if ($mode === 'customer' || $endCust > 0) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Customer sale orders debit stock; they cannot credit.'));
	exit;
}

$status = (int) $order['status'];
if ($status < 4) {
	echo json_encode(array(
		'ack' => 0,
		'ack_msg' => 'Account Approval required first. Current status must be Account Approved (or later) before Dispatch / Stock Credit.'
	));
	exit;
}

$stockObj = new ChannelPartnerStock($db);
$result = $stockObj->creditFromOrder($orderId);

if (!empty($result['ack']) && $markDispatch === 1 && $status < 5) {
	$db->rp_update("orders", array("status" => 5, "modified_date" => date('Y-m-d H:i:s')), "id='" . $orderId . "'", 0);
	$result['ack_msg'] = (isset($result['ack_msg']) ? $result['ack_msg'] : 'Stock credited.') . ' Order marked Dispatched.';
	$result['dispatched'] = 1;
}

echo json_encode($result);
require_once 'disconnect.php';
