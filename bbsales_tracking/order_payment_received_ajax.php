<?php
/**
 * Mark Sales Order as Payment Received — amount + payment type against order_no.
 */
$page_id = 565;
$page_slug = 'page_order';
include("connect.php");
header('Content-Type: application/json; charset=utf-8');

$canUpdate = (
	(isset($rights['update_flag']) && (int) $rights['update_flag'] === 1)
	|| (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0)
);
if (!$canUpdate) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Update permission required.'));
	exit;
}

$orderId = isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : 0;
$paidAmount = isset($_REQUEST['paid_amount']) ? (float) $_REQUEST['paid_amount'] : 0;
$paymentType = isset($_REQUEST['payment_type']) ? (int) $_REQUEST['payment_type'] : 0;
$remark = isset($_REQUEST['remark']) ? trim($_REQUEST['remark']) : '';

if ($orderId <= 0) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Invalid order.'));
	exit;
}
if ($paidAmount <= 0) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Please enter Payment Received Amount.'));
	exit;
}
if (!in_array($paymentType, array(1, 2, 3, 4), true)) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Please select Payment Type.'));
	exit;
}

$colCheck = @mysqli_query($db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'payment_received_flag'");
if (!$colCheck || mysqli_num_rows($colCheck) === 0) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Please run db_sync to add payment_received columns.'));
	exit;
}

$order = $db->rp_getData(
	"orders",
	"id,order_no,customer_id,customer_type,sales_id,grand_total,payment_received_flag,isDelete,status",
	"id='" . $orderId . "' AND isDelete=0",
	"",
	0
);
if (!$order) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Order not found.'));
	exit;
}
$row = mysqli_fetch_assoc($order);
if ((int) $row['payment_received_flag'] === 1) {
	echo json_encode(array('ack' => 1, 'ack_msg' => 'Payment already marked as received for ' . $row['order_no'], 'already' => 1));
	exit;
}

$now = date('Y-m-d H:i:s');
$payDate = date('Y-m-d');
$by = isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) ? (int) $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] : 0;
$orderNo = $row['order_no'];

$updRows = array(
	"payment_received_flag" => 1,
	"payment_received_date" => $now,
	"payment_received_by" => $by,
);
$amtCol = @mysqli_query($db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'payment_received_amount'");
if ($amtCol && mysqli_num_rows($amtCol) > 0) {
	$updRows['payment_received_amount'] = $paidAmount;
}
$typeCol = @mysqli_query($db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'payment_received_type'");
if ($typeCol && mysqli_num_rows($typeCol) > 0) {
	$updRows['payment_received_type'] = $paymentType;
}

$upd = $db->rp_update("orders", $updRows, "id='" . $orderId . "' AND isDelete=0", 0);
if (!$upd) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Failed to update order.'));
	exit;
}

// Also save in payment table against this Order (receipt_type=2, invoice_id=order id)
$paymentTypes = array(1 => 'By Cash', 2 => 'By Cheque', 3 => 'Online', 4 => 'Other');
$payRemark = $remark != '' ? $remark : ('Payment Received against Order ' . $orderNo);
$payInsertOk = false;
$payTable = @mysqli_query($db->myconn, "SHOW TABLES LIKE 'payment'");
if ($payTable && mysqli_num_rows($payTable) > 0) {
	require_once dirname(__FILE__) . '/../include/class.payment.php';
	$paymentObj = new Payment();
	$payDetail = array(
		'customer_type' => isset($row['customer_type']) ? $row['customer_type'] : '',
		'customer_id' => (int) $row['customer_id'],
		'sales_executive_id' => (int) $row['sales_id'],
		'paid_amount' => $paidAmount,
		'payment_date' => $payDate,
		'payment_type' => $paymentType,
		'remark' => $payRemark,
		'cheque_no' => '',
		'receipt_type' => 2,
		'invoice_id' => $orderId,
	);
	$payRes = $paymentObj->InsertPayment($payDetail);
	$payInsertOk = (isset($payRes['ack']) && (int) $payRes['ack'] === 1);
}

$typeLabel = isset($paymentTypes[$paymentType]) ? $paymentTypes[$paymentType] : '';
echo json_encode(array(
	'ack' => 1,
	'ack_msg' => 'Payment Received saved for Order ' . $orderNo . ' — Amount: ' . number_format($paidAmount, 2) . ($typeLabel != '' ? ' (' . $typeLabel . ')' : ''),
	'order_no' => $orderNo,
	'paid_amount' => $paidAmount,
	'payment_type' => $paymentType,
	'payment_received_date' => $now,
	'payment_saved' => $payInsertOk ? 1 : 0,
));
exit;
