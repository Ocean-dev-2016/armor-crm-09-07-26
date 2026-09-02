<?php

/*
 * @author Ravi Patel
 * Order PDF — same template as print view (suggested products) + compressed images.
 */

$staic = isset($staic) ? $staic : (isset($_REQUEST['staic']) ? $_REQUEST['staic'] : 0);
if ($staic == 2 && !isset($db)) {
	$page_id = 420;
	$page_slug = 'page_customer';
	require_once("connect_in.php");
}

$order_id = isset($order_id) ? (int) $order_id : (isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : 0);
$file_path = '';

if ($order_id > 0) {
	require_once dirname(__FILE__) . '/../include/armor_pdf_export_helper.php';

	if ($staic == 2 && isset($db)) {
		$uname = str_replace(" ", "-", stripslashes($db->rp_getValue("orders", "company_name", "id='" . $order_id . "'", 0)));
		$uname = str_replace("/", "-", $uname);
		$order_no = str_replace("/", "-", stripslashes($db->rp_getValue("orders", "order_no", "id='" . $order_id . "'", 0)));
	} else {
		$uname = 'Order';
		$order_no = $order_id;
	}

	$fileName = $uname . "_" . date('d_m_Y') . "_" . "Order_" . $order_no . 'pdf';
	$saveRelative = $fileName . '/' . $fileName . '.pdf';

	$gen = armor_pdf_export_generate(
		'view_order_new_1.php',
		array('order_id' => $order_id),
		array('quote-wrap', 'PRO FORMA', 'quote-main-body'),
		$saveRelative
	);

	if ($gen['ok']) {
		if ($staic == 2 && isset($db)) {
			$quotation_no = $db->rp_getValue("orders", "order_no", "id='" . $order_id . "'");
			$customer_id = $db->rp_getValue("orders", "customer_id", "id='" . $order_id . "'");
			$flag = "Web";
			$ctable = "orders";
			$module_name = "Orders";
			$log_description = $module_name . " " . $quotation_no . " PDF Download By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
			$user_id = isset($user_id) ? $user_id : 0;
			$db->insertLog($ctable, $order_id, "insert", "", array(), 0, $log_description, $flag, $module_name, $user_id, $customer_id);
			echo $gen['url'];
			exit;
		}
		$file_path = $gen['path'];
	} elseif ($staic == 2) {
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(array('ack' => 0, 'ack_msg' => isset($gen['error']) ? $gen['error'] : 'Order PDF Not Generate!!'));
		exit;
	}
}

?>
