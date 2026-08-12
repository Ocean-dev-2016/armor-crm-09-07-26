<?php
/**
 * CP Customer Order PDF — public download for App (no CRM web session).
 * GET: key=1226&channel_partner_id=&order_id=&file= (file optional)
 *
 * Used as file_url from API #269 download_cp_customer_order_pdf.
 */
error_reporting(0);
date_default_timezone_set('Asia/Kolkata');
include('../include/define.php');
include('../include/function.class.php');

$db = new Admin();
$db->connect();

$key = isset($_REQUEST['key']) ? $db->clean($_REQUEST['key']) : '';
if ($key === '' || !$db->checkAPIKey($key)) {
	header('HTTP/1.1 403 Forbidden');
	header('Content-Type: text/plain; charset=utf-8');
	echo 'Invalid API key.';
	exit;
}

require_once('../include/class.channel_partner_order.php');
$objCPOrder = new ChannelPartnerOrder();
$objCPOrder->StreamCustomerOrderPdf(array(
	'channel_partner_id' => isset($_REQUEST['channel_partner_id']) ? (int) $_REQUEST['channel_partner_id'] : 0,
	'order_id' => isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : (isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0),
	'file' => isset($_REQUEST['file']) ? $_REQUEST['file'] : '',
));
