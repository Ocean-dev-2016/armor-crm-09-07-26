<?php
/**
 * One-time Channel Partner demo reset + seed runner.
 *
 * Local CLI:
 *   php bbsales_tracking/cp_demo_reset_seed.php key=armor_cp_seed_2026 confirm=YES
 *
 * Live URL after deployment:
 *   https://armor-crm.oceanhub.co.in/bbsales_tracking/cp_demo_reset_seed.php?key=armor_cp_seed_2026&confirm=YES
 */
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

if (php_sapi_name() === 'cli' && !empty($argv)) {
	foreach ($argv as $arg) {
		if (strpos($arg, '=') !== false) {
			list($k, $v) = explode('=', $arg, 2);
			$_GET[$k] = $v;
		}
	}
}

define('CP_SEED_KEY', 'armor_cp_seed_2026');
if (!isset($_GET['key']) || $_GET['key'] !== CP_SEED_KEY || !isset($_GET['confirm']) || strtoupper($_GET['confirm']) !== 'YES') {
	header('HTTP/1.1 403 Forbidden');
	die('Unauthorized. Use ?key=armor_cp_seed_2026&confirm=YES');
}

require_once __DIR__ . '/../include/define.php';
require_once __DIR__ . '/../include/function.class.php';

$db = new Functions();
$db->connect();
mysqli_set_charset($db->myconn, 'utf8');

function cp_seed_out($text)
{
	echo $text . (php_sapi_name() === 'cli' ? PHP_EOL : "<br>\n");
}

function cp_seed_columns($db, $table)
{
	static $cache = array();
	if (isset($cache[$table])) {
		return $cache[$table];
	}
	$cache[$table] = array();
	$r = mysqli_query($db->myconn, "SHOW COLUMNS FROM `" . $table . "`");
	if ($r) {
		while ($row = mysqli_fetch_assoc($r)) {
			$cache[$table][$row['Field']] = true;
		}
	}
	return $cache[$table];
}

function cp_seed_insert_assoc($db, $table, $assoc)
{
	$cols = cp_seed_columns($db, $table);
	$rows = array();
	$values = array();
	foreach ($assoc as $key => $value) {
		if (isset($cols[$key])) {
			$rows[] = $key;
			$values[] = ($value === null) ? '' : $value;
		}
	}
	return $db->rp_insert($table, $values, $rows, 0);
}

function cp_seed_update_assoc($db, $table, $assoc, $where)
{
	$cols = cp_seed_columns($db, $table);
	$rows = array();
	foreach ($assoc as $key => $value) {
		if (isset($cols[$key])) {
			$rows[$key] = $value;
		}
	}
	if (empty($rows)) {
		return false;
	}
	return $db->rp_update($table, $rows, $where, 0);
}

function cp_seed_fetch_all($result)
{
	$rows = array();
	if ($result) {
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}
	}
	return $rows;
}

function cp_seed_first_value($db, $query, $field, $fallback = 0)
{
	$r = mysqli_query($db->myconn, $query);
	if ($r && ($row = mysqli_fetch_assoc($r))) {
		return isset($row[$field]) ? $row[$field] : $fallback;
	}
	return $fallback;
}

function cp_seed_make_gst($n)
{
	return '24ABCDE12' . str_pad((string) $n, 2, '0', STR_PAD_LEFT) . 'F1Z5';
}

function cp_seed_payment_status($index)
{
	return ($index % 2 === 0) ? 1 : 0;
}

$companyTypeId = (int) cp_seed_first_value($db, "SELECT id FROM company_master WHERE isDelete=0 ORDER BY id ASC LIMIT 1", 'id', 0);
$priceListId = (int) cp_seed_first_value($db, "SELECT id FROM price_list WHERE isDelete=0 ORDER BY id ASC LIMIT 1", 'id', 0);
$customerTypeId = (int) cp_seed_first_value($db, "SELECT id FROM customer_type WHERE isDelete=0 ORDER BY id ASC LIMIT 1", 'id', 0);
$salesId = (int) cp_seed_first_value($db, "SELECT id FROM sales_executive WHERE isDelete=0 AND isActive=1 ORDER BY id ASC LIMIT 1", 'id', 0);
$dispatchStatusId = (int) cp_seed_first_value($db, "SELECT id FROM dispatch_order_status WHERE isDelete=0 ORDER BY id ASC LIMIT 1", 'id', 1);

$productRows = cp_seed_fetch_all(mysqli_query(
	$db->myconn,
	"SELECT pwp.product_id, pwp.weight_id, pwp.catno, pwp.price, p.name AS product_name
	 FROM product_weight_price pwp
	 LEFT JOIN product p ON p.id = pwp.product_id
	 WHERE pwp.isDelete=0
	 ORDER BY pwp.id ASC
	 LIMIT 4"
));
if (empty($productRows)) {
	die('No product_weight_price records found. Add products first.');
}

cp_seed_out('Starting CP reset + seed...');

/* Soft delete old CP-related data */
$oldCpIds = array();
$oldCpR = mysqli_query($db->myconn, "SELECT id FROM executive WHERE channel_partner_flag=1 AND customer_flag=0 AND isDelete=0");
if ($oldCpR) {
	while ($row = mysqli_fetch_assoc($oldCpR)) {
		$oldCpIds[] = (int) $row['id'];
	}
}

if (!empty($oldCpIds)) {
	$cpIdCsv = implode(',', $oldCpIds);
	$oldCustomerIds = array();
	$oldCustR = mysqli_query($db->myconn, "SELECT id FROM channel_partner_customer WHERE channel_partner_id IN (" . $cpIdCsv . ") AND isDelete=0");
	if ($oldCustR) {
		while ($row = mysqli_fetch_assoc($oldCustR)) {
			$oldCustomerIds[] = (int) $row['id'];
		}
	}
	$orderIds = array();
	$orderR = mysqli_query($db->myconn, "SELECT id FROM orders WHERE customer_id IN (" . $cpIdCsv . ") AND channel_partner_order_flag=1 AND isDelete=0");
	if ($orderR) {
		while ($row = mysqli_fetch_assoc($orderR)) {
			$orderIds[] = (int) $row['id'];
		}
	}
	if (!empty($orderIds)) {
		$orderCsv = implode(',', $orderIds);
		@mysqli_query($db->myconn, "UPDATE order_product_item SET isDelete=1 WHERE order_id IN (" . $orderCsv . ")");
		@mysqli_query($db->myconn, "UPDATE orders SET isDelete=1 WHERE id IN (" . $orderCsv . ")");
		if ($db->rp_getTotalRecord("payment", "1=1") !== false) {
			@mysqli_query($db->myconn, "UPDATE payment SET isDelete=1 WHERE receipt_type=2 AND invoice_id IN (" . $orderCsv . ")");
		}
	}
	if (!empty($oldCustomerIds)) {
		$oldCustomerCsv = implode(',', $oldCustomerIds);
		@mysqli_query($db->myconn, "UPDATE channel_partner_customer SET isDelete=1 WHERE id IN (" . $oldCustomerCsv . ")");
	}
	@mysqli_query($db->myconn, "UPDATE customer_inward_stock SET isDelete=1 WHERE customer_id IN (" . $cpIdCsv . ")");
	@mysqli_query($db->myconn, "UPDATE dealer_distributor_network SET isDelete=1 WHERE customer_id IN (" . $cpIdCsv . ") AND type=3");
	@mysqli_query($db->myconn, "UPDATE executive SET isDelete=1 WHERE id IN (" . $cpIdCsv . ")");
	cp_seed_out('Old CP data soft-deleted: ' . count($oldCpIds) . ' CP(s).');
} else {
	cp_seed_out('No old CP data found.');
}

$seedData = array(
	array(
		'cp' => array('company' => 'Aarav Safety Solutions', 'person' => 'Aarav Shah', 'city' => 'Ahmedabad', 'state' => 'Gujarat'),
		'customers' => array(
			array('company' => 'Metro Mall Projects', 'person' => 'Ritesh Mehta'),
			array('company' => 'Shiv Buildtech', 'person' => 'Harsh Patel'),
			array('company' => 'Orbit Electricals', 'person' => 'Neel Joshi'),
		),
	),
	array(
		'cp' => array('company' => 'BlazeSecure Enterprise', 'person' => 'Mihir Dave', 'city' => 'Surat', 'state' => 'Gujarat'),
		'customers' => array(
			array('company' => 'Sunrise Heights', 'person' => 'Jignesh Vora'),
			array('company' => 'Pulse Engineering', 'person' => 'Karan Rana'),
			array('company' => 'Silver Line Infra', 'person' => 'Dhruv Shah'),
			array('company' => 'Lotus MEP Works', 'person' => 'Ankit Desai'),
		),
	),
	array(
		'cp' => array('company' => 'Core Fire Distributors', 'person' => 'Vivek Trivedi', 'city' => 'Vadodara', 'state' => 'Gujarat'),
		'customers' => array(
			array('company' => 'Prime Tech Park', 'person' => 'Yash Modi'),
			array('company' => 'Royal Interiors', 'person' => 'Meet Solanki'),
			array('company' => 'Nexa Control Systems', 'person' => 'Rohan Gajjar'),
		),
	),
	array(
		'cp' => array('company' => 'Delta Protection House', 'person' => 'Parth Bhatt', 'city' => 'Rajkot', 'state' => 'Gujarat'),
		'customers' => array(
			array('company' => 'Skyview Plaza', 'person' => 'Dev Soni'),
			array('company' => 'Capital Services', 'person' => 'Ravi Kansara'),
			array('company' => 'Everest Electro', 'person' => 'Nirav Kapadia'),
			array('company' => 'Om Facility Care', 'person' => 'Shyam Tiwari'),
		),
	),
	array(
		'cp' => array('company' => 'Elite Alarm Network', 'person' => 'Sanket Parmar', 'city' => 'Mumbai', 'state' => 'Maharashtra'),
		'customers' => array(
			array('company' => 'Westbay Residency', 'person' => 'Piyush Jain'),
			array('company' => 'Secure Grid Systems', 'person' => 'Aman Verma'),
			array('company' => 'Titan Buildspaces', 'person' => 'Abhishek Singh'),
		),
	),
);

$createdCps = array();
$cpCount = 0;
$totalCustomerCount = 0;
$totalOrderCount = 0;
$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');

foreach ($seedData as $cpIndex => $entry) {
	$cpCount++;
	$mobile = '90000010' . $cpCount;
	$passwordPlain = 'Armor@123';
	$clientCode = 'CP' . str_pad((string) $cpCount, 3, '0', STR_PAD_LEFT);
	$cpId = cp_seed_insert_assoc($db, 'executive', array(
		'company_name' => $entry['cp']['company'],
		'cname' => $entry['cp']['person'],
		'mobile_no1' => $mobile,
		'phone' => $mobile,
		'email' => 'cp' . $cpCount . '@armor-demo.test',
		'address' => $entry['cp']['company'] . ', ' . $entry['cp']['city'],
		'shipping_address' => $entry['cp']['company'] . ', ' . $entry['cp']['city'],
		'billing_address' => $entry['cp']['company'] . ', ' . $entry['cp']['city'],
		'country' => 'India',
		'state' => $entry['cp']['state'],
		'city' => $entry['cp']['city'],
		'main_city' => $entry['cp']['city'],
		'zip' => '36000' . $cpCount,
		'client_code' => $clientCode,
		'type_of_executive' => $customerTypeId,
		'type_of_company' => $companyTypeId,
		'price_list_id' => $priceListId,
		'seid' => $salesId,
		'gst' => cp_seed_make_gst($cpCount),
		'password' => md5($passwordPlain),
		'channel_partner_flag' => 1,
		'customer_flag' => 0,
		'isActive' => 1,
		'isDelete' => 0,
		'adate' => $now,
		'inquiry_date' => $today,
	));

	if (!$cpId) {
		cp_seed_out('Failed to create CP: ' . $entry['cp']['company']);
		continue;
	}

	cp_seed_insert_assoc($db, 'dealer_distributor_network', array(
		'name' => $entry['cp']['person'],
		'type' => 3,
		'admin_type' => 1,
		'user_id' => 0,
		'customer_id' => $cpId,
		'sales_executive_id' => 0,
		'username' => $mobile,
		'phone' => $mobile,
		'email' => 'cp' . $cpCount . '@armor-demo.test',
		'password' => md5($passwordPlain),
		'isDelete' => 0,
		'adate' => $now,
	));

	/* Seed stock so CP dashboard/stock pages are meaningful */
	foreach ($productRows as $pIndex => $prod) {
		cp_seed_insert_assoc($db, 'customer_inward_stock', array(
			'pro_id' => (int) $prod['product_id'],
			'weight_id' => (int) $prod['weight_id'],
			'pro_name' => $prod['product_name'] ? $prod['product_name'] : ('Product ' . ($pIndex + 1)),
			'pro_qty' => 25 + ($pIndex * 5) + $cpCount,
			'planning_date' => $today,
			'remark' => 'Seed stock for CP demo',
			'expiry_date' => date('Y-m-d', strtotime('+1 year')),
			'customer_id' => $cpId,
			'sales_id' => $salesId,
			'isDelete' => 0,
		));
	}

	/* One own order per CP for admin CP ledger/payment flow */
	$ownQty = 0;
	$ownSubTotal = 0;
	$ownOrderId = cp_seed_insert_assoc($db, 'orders', array(
		'order_no' => 'CP-OWN-' . str_pad((string) $cpCount, 3, '0', STR_PAD_LEFT),
		'customer_id' => $cpId,
		'customer_name' => $entry['cp']['person'],
		'company_name' => $entry['cp']['company'],
		'customer_type' => $customerTypeId,
		'contact_number' => $mobile,
		'address' => $entry['cp']['company'] . ', ' . $entry['cp']['city'],
		'city' => $entry['cp']['city'],
		'state' => $entry['cp']['state'],
		'country' => 'India',
		'email' => 'cp' . $cpCount . '@armor-demo.test',
		'order_date' => date('Y-m-d', strtotime('-' . (10 + $cpCount) . ' days')),
		'modify_date' => $now,
		'modified_date' => $now,
		'sales_id' => $salesId,
		'channel_partner_order_flag' => 1,
		'cp_order_mode' => 'own',
		'channel_partner_customer_id' => 0,
		'payment_received_flag' => 1,
		'payment_received_amount' => 0,
		'payment_received_date' => $now,
		'payment_received_type' => 3,
		'dispatch_status' => $dispatchStatusId,
		'status' => 5,
		'gst_apply_flag' => 1,
		'remarks' => 'Seeded own CP order',
		'isDelete' => 0,
	));
	if ($ownOrderId) {
		foreach (array_slice($productRows, 0, 2) as $prod) {
			$qty = 8 + $cpCount;
			$rate = (float) $prod['price'];
			$total = $qty * $rate;
			$ownQty += $qty;
			$ownSubTotal += $total;
			cp_seed_insert_assoc($db, 'order_product_item', array(
				'order_id' => $ownOrderId,
				'pro_id' => (int) $prod['product_id'],
				'weight_id' => (int) $prod['weight_id'],
				'pro_name' => $prod['product_name'],
				'pro_qty' => $qty,
				'dispatched_qty' => $qty,
				'remaining_qty' => 0,
				'unitprice' => $rate,
				'price' => $rate,
				'totalprice' => $total,
				'adate' => $now,
				'modify_date' => $now,
				'isDelete' => 0,
			));
		}
		cp_seed_update_assoc($db, 'orders', array(
			'total_qty' => $ownQty,
			'total_amount' => $ownSubTotal,
			'subtotal' => $ownSubTotal,
			'grand_total' => $ownSubTotal,
			'payment_received_amount' => $ownSubTotal,
		), "id='" . (int) $ownOrderId . "'");
		$totalOrderCount++;
	}

	foreach ($entry['customers'] as $custIndex => $cust) {
		$totalCustomerCount++;
		$custMobile = '910' . str_pad((string) ($cpCount * 10 + $custIndex + 1), 7, '0', STR_PAD_LEFT);
		$cpCustomerId = cp_seed_insert_assoc($db, 'channel_partner_customer', array(
			'channel_partner_id' => $cpId,
			'company_name' => $cust['company'],
			'person_name' => $cust['person'],
			'mobile_no' => $custMobile,
			'email' => 'customer' . $cpCount . '_' . ($custIndex + 1) . '@armor-demo.test',
			'gst' => cp_seed_make_gst($cpCount + $custIndex + 10),
			'country' => 'India',
			'state' => $entry['cp']['state'],
			'city' => $entry['cp']['city'],
			'pincode' => '3601' . $custIndex . $cpCount,
			'isActive' => 1,
			'isDelete' => 0,
		));
		if (!$cpCustomerId) {
			continue;
		}

		$orderNo = 'CP-CUST-' . $cpCount . '-' . ($custIndex + 1);
		$paymentReceived = cp_seed_payment_status($custIndex);
		$orderDate = date('Y-m-d', strtotime('-' . (($cpCount * 3) + $custIndex + 1) . ' days'));
		$orderId = cp_seed_insert_assoc($db, 'orders', array(
			'order_no' => $orderNo,
			'customer_id' => $cpId,
			'customer_name' => $cust['person'],
			'company_name' => $cust['company'],
			'customer_type' => $customerTypeId,
			'contact_number' => $custMobile,
			'address' => $cust['company'] . ', ' . $entry['cp']['city'],
			'city' => $entry['cp']['city'],
			'state' => $entry['cp']['state'],
			'country' => 'India',
			'email' => 'customer' . $cpCount . '_' . ($custIndex + 1) . '@armor-demo.test',
			'order_date' => $orderDate,
			'modify_date' => $now,
			'modified_date' => $now,
			'sales_id' => $salesId,
			'channel_partner_order_flag' => 1,
			'cp_order_mode' => 'customer',
			'channel_partner_customer_id' => $cpCustomerId,
			'payment_received_flag' => $paymentReceived,
			'payment_received_amount' => 0,
			'payment_received_date' => $paymentReceived ? $now : null,
			'payment_received_type' => $paymentReceived ? 1 : 0,
			'dispatch_status' => $paymentReceived ? $dispatchStatusId : 0,
			'status' => $paymentReceived ? 5 : 0,
			'gst_apply_flag' => ($custIndex % 2 === 0) ? 1 : 0,
			'shipping_address' => $cust['company'] . ', ' . $entry['cp']['city'],
			'remarks' => 'Seeded CP customer order',
			'isDelete' => 0,
		));

		if ($orderId) {
			$totalQty = 0;
			$subTotal = 0;
			foreach (array_slice($productRows, 0, 2 + ($custIndex % 2)) as $prod) {
				$qty = 2 + $custIndex;
				$rate = (float) $prod['price'];
				$total = $qty * $rate;
				$totalQty += $qty;
				$subTotal += $total;
				cp_seed_insert_assoc($db, 'order_product_item', array(
					'order_id' => $orderId,
					'pro_id' => (int) $prod['product_id'],
					'weight_id' => (int) $prod['weight_id'],
					'pro_name' => $prod['product_name'],
					'pro_qty' => $qty,
					'dispatched_qty' => $paymentReceived ? $qty : 0,
					'remaining_qty' => $paymentReceived ? 0 : $qty,
					'unitprice' => $rate,
					'price' => $rate,
					'totalprice' => $total,
					'adate' => $now,
					'modify_date' => $now,
					'isDelete' => 0,
				));
			}
			$receivedAmount = $paymentReceived ? $subTotal : 0;
			cp_seed_update_assoc($db, 'orders', array(
				'total_qty' => $totalQty,
				'total_amount' => $subTotal,
				'subtotal' => $subTotal,
				'grand_total' => $subTotal,
				'payment_received_amount' => $receivedAmount,
			), "id='" . (int) $orderId . "'");
			$totalOrderCount++;
		}
	}

	$createdCps[] = array(
		'company' => $entry['cp']['company'],
		'person' => $entry['cp']['person'],
		'username' => $mobile,
		'password' => $passwordPlain,
	);
}

cp_seed_out('Seed completed.');
cp_seed_out('Channel Partners created: ' . $cpCount);
cp_seed_out('CP customers created: ' . $totalCustomerCount);
cp_seed_out('Orders created: ' . $totalOrderCount);
cp_seed_out(str_repeat('-', 40));
cp_seed_out('CP Login Credentials');
foreach ($createdCps as $cp) {
	cp_seed_out($cp['company'] . ' | User: ' . $cp['username'] . ' | Password: ' . $cp['password']);
}
