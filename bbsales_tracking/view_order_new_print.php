<?php
/**
 * PI / Sales Order print — same layout as screen view (quotation-style).
 */
if (!isset($_REQUEST['order_id']) && isset($_GET['order_id'])) {
	$_REQUEST['order_id'] = $_GET['order_id'];
}
if (!isset($_REQUEST['print']) && !isset($_REQUEST['p'])) {
	$_REQUEST['print'] = '1';
}
require_once dirname(__FILE__) . '/view_order_new_1.php';
