<?php
/**
 * Soft-delete Pending CP customer order (+ stock credit back).
 */
$page_id = 565;
$page_slug = 'channel_partner_order';
include("connect.php");
header('Content-Type: application/json');

if (!function_exists('cp_is_channel_partner_login') || !cp_is_channel_partner_login($db)) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Access denied.'));
	exit;
}

$cp_id = (int) cp_get_login_channel_partner_id();
$order_id = isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : 0;
if ($order_id <= 0) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'order_id is required.'));
	exit;
}

require_once dirname(__FILE__) . '/../include/class.channel_partner_order.php';
$obj = new ChannelPartnerOrder();
$res = $obj->DeleteCustomerOrder(array(
	'channel_partner_id' => $cp_id,
	'order_id' => $order_id,
));
echo json_encode($res);
