<?php
/**
 * Channel Partner stock movements (PHP 5.6 compatible).
 * Uses customer_inward_stock: positive qty = IN (Armor → CP), negative = OUT (CP → end customer).
 */
class ChannelPartnerStock
{
	private $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function getAvailableQty($cpId, $proId, $weightId = '')
	{
		$cpId = (int) $cpId;
		$proId = (int) $proId;
		$where = "customer_id='" . $cpId . "' AND pro_id='" . $proId . "' AND isDelete=0";
		if ($weightId !== '' && $weightId !== null) {
			$w = mysqli_real_escape_string($this->db->myconn, (string) $weightId);
			$where .= " AND weight_id='" . $w . "'";
		}
		$sum = $this->db->rp_getValue("customer_inward_stock", "COALESCE(SUM(pro_qty),0)", $where, 0);
		return (float) $sum;
	}

	public function getStockSummary($cpId)
	{
		$cpId = (int) $cpId;
		$rows = array();
		$r = $this->db->rp_getData(
			"customer_inward_stock",
			"pro_id, weight_id, pro_name, SUM(pro_qty) AS total_qty",
			"customer_id='" . $cpId . "' AND isDelete=0 GROUP BY pro_id, weight_id, pro_name HAVING SUM(pro_qty) != 0",
			"pro_name ASC",
			0
		);
		if ($r) {
			while ($d = mysqli_fetch_assoc($r)) {
				$rows[] = $d;
			}
		}
		return $rows;
	}

	/**
	 * Product + Cat No wise main stock (aggregates all weight variants of same product/code).
	 */
	public function getMainStockByProductCode($cpId)
	{
		$cpId = (int) $cpId;
		$summary = $this->getStockSummary($cpId);
		$grouped = array();
		foreach ($summary as $r) {
			$proId = (int) $r['pro_id'];
			$weightId = isset($r['weight_id']) ? (string) $r['weight_id'] : '-1';
			$catno = $this->db->rp_getValue(
				"product_weight_price",
				"catno",
				"product_id='" . $proId . "' AND weight_id='" . mysqli_real_escape_string($this->db->myconn, $weightId) . "'",
				0
			);
			if ($catno === '' || $catno === null) {
				$catno = $this->db->rp_getValue("product_weight_price", "catno", "product_id='" . $proId . "' AND isDelete=0", 0);
			}
			$key = $proId . '|' . ($catno ? $catno : '-');
			if (!isset($grouped[$key])) {
				$grouped[$key] = array(
					'pro_id' => $proId,
					'pro_name' => $r['pro_name'],
					'catno' => $catno ? $catno : '-',
					'total_qty' => 0,
				);
			}
			$grouped[$key]['total_qty'] += (float) $r['total_qty'];
		}
		$rows = array_values($grouped);
		usort($rows, function ($a, $b) {
			return strcasecmp($a['pro_name'], $b['pro_name']);
		});
		return $rows;
	}

	/**
	 * Inward / Outward ledger rows with bill no + date.
	 */
	public function getStockMovements($cpId)
	{
		$cpId = (int) $cpId;
		$rows = array();
		$hasRef = false;
		$colRef = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `customer_inward_stock` LIKE 'ref_order_id'");
		if ($colRef && mysqli_num_rows($colRef) > 0) {
			$hasRef = true;
		}
		$hasTxn = false;
		$colTxn = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `customer_inward_stock` LIKE 'txn_type'");
		if ($colTxn && mysqli_num_rows($colTxn) > 0) {
			$hasTxn = true;
		}

		$select = "id, pro_id, weight_id, pro_name, pro_qty, planning_date, remark, created_date";
		if ($hasTxn) {
			$select .= ", txn_type";
		}
		if ($hasRef) {
			$select .= ", ref_order_id";
		}

		$r = $this->db->rp_getData(
			"customer_inward_stock",
			$select,
			"customer_id='" . $cpId . "' AND isDelete=0",
			"planning_date ASC, id ASC",
			0
		);
		if (!$r) {
			return $rows;
		}

		while ($d = mysqli_fetch_assoc($r)) {
			$qty = (float) $d['pro_qty'];
			$txn = '';
			if ($hasTxn && !empty($d['txn_type'])) {
				$txn = strtolower($d['txn_type']);
			} else {
				$txn = ($qty >= 0) ? 'in' : 'out';
			}

			$billNo = '';
			$billDate = '';
			$refOrderId = ($hasRef && isset($d['ref_order_id'])) ? (int) $d['ref_order_id'] : 0;
			if ($refOrderId > 0) {
				$ord = $this->db->rp_getData("orders", "order_no,order_date", "id='" . $refOrderId . "'", "", 0);
				if ($ord && $o = mysqli_fetch_assoc($ord)) {
					$billNo = $o['order_no'];
					$billDate = $o['order_date'];
				}
			}
			if ($billNo === '' && !empty($d['remark'])) {
				if (preg_match('/(?:Order|order)\s+([A-Za-z0-9\-\/]+)/', $d['remark'], $m)) {
					$billNo = $m[1];
				}
			}

			$date = !empty($d['planning_date']) ? $d['planning_date'] : '';
			if ($date === '' && !empty($d['created_date'])) {
				$date = date('Y-m-d', strtotime($d['created_date']));
			}
			if ($billDate === '') {
				$billDate = $date;
			}

			$weightId = isset($d['weight_id']) ? (string) $d['weight_id'] : '-1';
			$catno = $this->db->rp_getValue(
				"product_weight_price",
				"catno",
				"product_id='" . (int) $d['pro_id'] . "' AND weight_id='" . mysqli_real_escape_string($this->db->myconn, $weightId) . "'",
				0
			);
			if ($catno === '' || $catno === null) {
				$catno = $this->db->rp_getValue("product_weight_price", "catno", "product_id='" . (int) $d['pro_id'] . "' AND isDelete=0", 0);
			}

			$wname = '';
			if ($weightId !== '' && $weightId !== '-1') {
				$wCheck = @mysqli_query($this->db->myconn, "SHOW TABLES LIKE 'weight'");
				if ($wCheck && mysqli_num_rows($wCheck) > 0) {
					$wname = $this->db->rp_getValue("weight", "name", "id='" . mysqli_real_escape_string($this->db->myconn, $weightId) . "'", 0);
				}
			}

			$rows[] = array(
				'id' => (int) $d['id'],
				'date' => $date,
				'bill_no' => $billNo,
				'bill_date' => $billDate,
				'ref_order_id' => $refOrderId,
				'txn_type' => $txn,
				'pro_id' => (int) $d['pro_id'],
				'pro_name' => $d['pro_name'],
				'catno' => $catno ? $catno : '-',
				'weight' => $wname ? $wname : '',
				'qty_in' => ($qty > 0) ? $qty : 0,
				'qty_out' => ($qty < 0) ? abs($qty) : 0,
				'qty' => $qty,
				'remark' => isset($d['remark']) ? $d['remark'] : '',
			);
		}
		return $rows;
	}

	/**
	 * Insert one stock movement row.
	 * $qty > 0 credit, $qty < 0 debit.
	 */
	public function addMovement($cpId, $proId, $weightId, $proName, $qty, $remark, $refOrderId = 0, $salesId = 0, $txnType = '')
	{
		$cpId = (int) $cpId;
		$proId = (int) $proId;
		$qty = (float) $qty;
		if ($cpId <= 0 || $proId <= 0 || $qty == 0) {
			return array("ack" => 0, "ack_msg" => "Invalid stock movement.");
		}
		if ($txnType === '') {
			$txnType = ($qty > 0) ? 'in' : 'out';
		}
		$today = date('Y-m-d');
		$farExpiry = date('Y-m-d', strtotime('+10 years'));
		$values = array(
			$proId,
			($weightId !== '' && $weightId !== null) ? $weightId : '-1',
			$proName,
			$qty,
			$today,
			$remark,
			$farExpiry,
			$cpId,
			(int) $salesId
		);
		$columns = array(
			"pro_id", "weight_id", "pro_name", "pro_qty",
			"planning_date", "remark", "expiry_date", "customer_id", "sales_id"
		);

		/* Optional ledger columns if db_sync added them */
		$colTxn = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `customer_inward_stock` LIKE 'txn_type'");
		if ($colTxn && mysqli_num_rows($colTxn) > 0) {
			$columns[] = "txn_type";
			$values[] = $txnType;
		}
		$colRef = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `customer_inward_stock` LIKE 'ref_order_id'");
		if ($colRef && mysqli_num_rows($colRef) > 0) {
			$columns[] = "ref_order_id";
			$values[] = (int) $refOrderId;
		}

		$id = $this->db->rp_insert("customer_inward_stock", $values, $columns, 0);
		if ($id) {
			return array("ack" => 1, "ack_msg" => "Stock updated.", "id" => $id);
		}
		return array("ack" => 0, "ack_msg" => "Stock insert failed.");
	}

	/**
	 * Credit CP stock from Armor Channel Partner order line items (once).
	 * Applies to CP "own" / Armor→CP supply orders (not end-customer portal sales).
	 */
	public function creditFromOrder($orderId)
	{
		$orderId = (int) $orderId;
		$order = $this->getOrder($orderId);
		if (!$order) {
			return array("ack" => 0, "ack_msg" => "Order not found.");
		}
		if ((int) $order['channel_partner_order_flag'] !== 1) {
			return array("ack" => 0, "ack_msg" => "Not a Channel Partner order.");
		}
		$mode = isset($order['cp_order_mode']) ? $order['cp_order_mode'] : '';
		if ($mode === 'customer') {
			return array("ack" => 0, "ack_msg" => "Customer orders debit stock, they do not credit.");
		}
		if ($this->hasFlag($order, 'cp_stock_credited')) {
			return array("ack" => 1, "ack_msg" => "Stock already credited.", "already" => 1);
		}

		$cpId = (int) $order['customer_id'];
		$salesId = isset($order['sales_id']) ? (int) $order['sales_id'] : 0;
		$orderNo = $order['order_no'];
		$items = $this->getOrderItems($orderId);
		if (empty($items)) {
			return array("ack" => 0, "ack_msg" => "No order items.");
		}

		$ok = 0;
		foreach ($items as $it) {
			$qty = (float) $it['pro_qty'];
			if ($qty <= 0) {
				continue;
			}
			$res = $this->addMovement(
				$cpId,
				(int) $it['pro_id'],
				$it['weight_id'],
				$it['pro_name'],
				$qty,
				"IN from Armor Order " . $orderNo,
				$orderId,
				$salesId,
				'in'
			);
			if (!empty($res['ack'])) {
				$ok++;
			}
		}
		if ($ok > 0) {
			$this->setOrderFlag($orderId, 'cp_stock_credited', 1);
			return array("ack" => 1, "ack_msg" => "Credited " . $ok . " product line(s) to CP stock.", "lines" => $ok);
		}
		return array("ack" => 0, "ack_msg" => "No stock lines credited.");
	}

	/**
	 * Debit CP stock for portal customer order (once). Validates availability first.
	 */
	public function debitForCustomerOrder($orderId, $force = false)
	{
		$orderId = (int) $orderId;
		$order = $this->getOrder($orderId);
		if (!$order) {
			return array("ack" => 0, "ack_msg" => "Order not found.");
		}
		if ((int) $order['channel_partner_order_flag'] !== 1) {
			return array("ack" => 0, "ack_msg" => "Not a Channel Partner order.");
		}
		$mode = isset($order['cp_order_mode']) ? $order['cp_order_mode'] : '';
		$endCust = isset($order['channel_partner_customer_id']) ? (int) $order['channel_partner_customer_id'] : 0;
		if ($mode !== 'customer' && $endCust <= 0) {
			return array("ack" => 0, "ack_msg" => "Not a CP customer order.");
		}
		if ($this->hasFlag($order, 'cp_stock_debited')) {
			return array("ack" => 1, "ack_msg" => "Stock already debited.", "already" => 1);
		}

		$cpId = (int) $order['customer_id'];
		$salesId = isset($order['sales_id']) ? (int) $order['sales_id'] : 0;
		$orderNo = $order['order_no'];
		$items = $this->getOrderItems($orderId);
		if (empty($items)) {
			return array("ack" => 0, "ack_msg" => "No order items.");
		}

		/* Validate stock */
		$shortages = array();
		foreach ($items as $it) {
			$need = (float) $it['pro_qty'];
			if ($need <= 0) {
				continue;
			}
			$have = $this->getAvailableQty($cpId, (int) $it['pro_id'], $it['weight_id']);
			if ($have + 0.0001 < $need) {
				$shortages[] = $it['pro_name'] . " (need " . $need . ", have " . $have . ")";
			}
		}
		if (!empty($shortages) && !$force) {
			return array(
				"ack" => 0,
				"ack_msg" => "Insufficient CP stock: " . implode("; ", $shortages),
				"shortages" => $shortages
			);
		}

		$ok = 0;
		foreach ($items as $it) {
			$qty = (float) $it['pro_qty'];
			if ($qty <= 0) {
				continue;
			}
			$res = $this->addMovement(
				$cpId,
				(int) $it['pro_id'],
				$it['weight_id'],
				$it['pro_name'],
				-1 * $qty,
				"OUT for Customer Order " . $orderNo,
				$orderId,
				$salesId,
				'out'
			);
			if (!empty($res['ack'])) {
				$ok++;
			}
		}
		if ($ok > 0) {
			$this->setOrderFlag($orderId, 'cp_stock_debited', 1);
			return array("ack" => 1, "ack_msg" => "Debited " . $ok . " product line(s) from CP stock.", "lines" => $ok);
		}
		return array("ack" => 0, "ack_msg" => "No stock lines debited.");
	}

	/**
	 * Credit stock back when CP customer order is deleted/edited (reverse of debit).
	 */
	public function creditBackForCustomerOrder($orderId)
	{
		$orderId = (int) $orderId;
		$order = $this->getOrder($orderId);
		if (!$order) {
			return array("ack" => 0, "ack_msg" => "Order not found.");
		}
		if ((int) $order['channel_partner_order_flag'] !== 1) {
			return array("ack" => 0, "ack_msg" => "Not a Channel Partner order.");
		}
		if (!$this->hasFlag($order, 'cp_stock_debited')) {
			return array("ack" => 1, "ack_msg" => "No stock debit to reverse.", "already" => 1);
		}

		$cpId = (int) $order['customer_id'];
		$salesId = isset($order['sales_id']) ? (int) $order['sales_id'] : 0;
		$orderNo = isset($order['order_no']) ? $order['order_no'] : ('#' . $orderId);
		$items = $this->getOrderItems($orderId);
		$ok = 0;
		foreach ($items as $it) {
			$qty = (float) $it['pro_qty'];
			if ($qty <= 0) {
				continue;
			}
			$res = $this->addMovement(
				$cpId,
				(int) $it['pro_id'],
				$it['weight_id'],
				$it['pro_name'],
				$qty,
				"IN reverse Customer Order " . $orderNo . " (edit/delete)",
				$orderId,
				$salesId,
				'in'
			);
			if (!empty($res['ack'])) {
				$ok++;
			}
		}
		$this->setOrderFlag($orderId, 'cp_stock_debited', 0);
		return array(
			"ack" => 1,
			"ack_msg" => "Credited back " . $ok . " product line(s) to CP stock.",
			"lines" => $ok,
		);
	}

	/**
	 * Pre-check stock for items array before placing order.
	 * $items: array of array(pid, weight_id, qty, pro_name)
	 */
	public function validateItemsStock($cpId, $items)
	{
		$shortages = array();
		foreach ($items as $it) {
			$need = (float) $it['qty'];
			if ($need <= 0) {
				continue;
			}
			$have = $this->getAvailableQty($cpId, (int) $it['pid'], isset($it['weight_id']) ? $it['weight_id'] : '');
			if ($have + 0.0001 < $need) {
				$name = isset($it['pro_name']) ? $it['pro_name'] : ('Product #' . $it['pid']);
				$shortages[] = $name . " (need " . $need . ", have " . $have . ")";
			}
		}
		if (!empty($shortages)) {
			return array("ack" => 0, "ack_msg" => "Insufficient stock: " . implode("; ", $shortages), "shortages" => $shortages);
		}
		return array("ack" => 1, "ack_msg" => "OK");
	}

	private function getOrder($orderId)
	{
		$r = $this->db->rp_getData("orders", "*", "id='" . (int) $orderId . "' AND isDelete=0", "", 0);
		if (!$r) {
			return null;
		}
		return mysqli_fetch_assoc($r);
	}

	private function getOrderItems($orderId)
	{
		$rows = array();
		$r = $this->db->rp_getData(
			"order_product_item",
			"pro_id, weight_id, pro_name, pro_qty",
			"order_id='" . (int) $orderId . "' AND isDelete=0",
			"",
			0
		);
		if ($r) {
			while ($d = mysqli_fetch_assoc($r)) {
				$rows[] = $d;
			}
		}
		return $rows;
	}

	private function hasFlag($order, $col)
	{
		$colCheck = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE '" . $col . "'");
		if (!($colCheck && mysqli_num_rows($colCheck) > 0)) {
			return false;
		}
		return (isset($order[$col]) && (int) $order[$col] === 1);
	}

	private function setOrderFlag($orderId, $col, $val)
	{
		$colCheck = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE '" . $col . "'");
		if (!($colCheck && mysqli_num_rows($colCheck) > 0)) {
			return false;
		}
		return $this->db->rp_update("orders", array($col => (int) $val), "id='" . (int) $orderId . "'", 0);
	}

	/**
	 * Backfill outward for CP customer orders already dispatched and/or paid but not stock-debited.
	 */
	public function backfillMissingOutward($cpId = 0)
	{
		$cpId = (int) $cpId;
		$where = "channel_partner_order_flag=1 AND isDelete=0
			AND (cp_order_mode='customer' OR channel_partner_customer_id>0)
			AND (IFNULL(cp_stock_debited,0)=0)
			AND (
				IFNULL(dispatch_status,0)>0
				OR IFNULL(payment_received_flag,0)=1
				OR status>=5
			)";
		if ($cpId > 0) {
			$where .= " AND customer_id='" . $cpId . "'";
		}
		$done = 0;
		$fail = 0;
		$msgs = array();
		$r = $this->db->rp_getData("orders", "id,order_no", $where, "id ASC", 0);
		if ($r) {
			while ($o = mysqli_fetch_assoc($r)) {
				$res = $this->debitForCustomerOrder((int) $o['id']);
				if (!empty($res['ack'])) {
					$done++;
				} else {
					$fail++;
					$msgs[] = $o['order_no'] . ': ' . (isset($res['ack_msg']) ? $res['ack_msg'] : 'fail');
				}
			}
		}
		return array(
			'ack' => 1,
			'ack_msg' => 'Backfill outward: ' . $done . ' order(s) posted' . ($fail ? (', ' . $fail . ' failed') : ''),
			'done' => $done,
			'fail' => $fail,
			'errors' => $msgs,
		);
	}

	/**
	 * App API — My Stock tab 1: Main Stock (Product & Code) — same as web.
	 */
	public function GetMyStockMain($cpId, $searchName = '')
	{
		$cpId = (int) $cpId;
		if ($cpId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'channel_partner_id is required.');
		}
		$whereCp = "id='" . $cpId . "' AND channel_partner_flag=1 AND customer_flag=0 AND isDelete=0";
		if ((int) $this->db->rp_getTotalRecord('executive', $whereCp, 0) <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Invalid Channel Partner.');
		}

		$this->backfillMissingOutward($cpId);

		$company = $this->db->rp_getValue('executive', 'company_name', "id='" . $cpId . "'", 0);
		$rows = $this->getMainStockByProductCode($cpId);
		$search = trim($searchName);
		$result = array();
		$sr = 0;
		foreach ($rows as $r) {
			$code = (isset($r['catno']) && $r['catno'] !== '' && $r['catno'] !== '-') ? $r['catno'] : '';
			$name = isset($r['pro_name']) ? $r['pro_name'] : '';
			$qty = isset($r['total_qty']) ? (float) $r['total_qty'] : 0;
			$label = $code !== '' ? ($code . ' - ' . $name) : $name;
			if ($search !== '') {
				$hay = strtolower($label . ' ' . $code . ' ' . $name);
				if (strpos($hay, strtolower($search)) === false) {
					continue;
				}
			}
			$sr++;
			$result[] = array(
				'sr' => $sr,
				'pro_id' => (int) $r['pro_id'],
				'product_code' => $code,
				'product_name' => $name,
				'product_label' => $label,
				'available_qty' => round($qty, 2),
			);
		}

		return array(
			'ack' => 1,
			'ack_msg' => 'My Stock (Main) ready',
			'channel_partner_id' => $cpId,
			'company_name' => $company ? $company : '',
			'view' => 'main',
			'title' => 'Main Stock (Product & Code)',
			'total' => count($result),
			'result' => $result,
		);
	}

	/**
	 * App API — My Stock tab 2: Inward / Outward ledger — same as web.
	 */
	public function GetMyStockMovements($cpId, $searchName = '')
	{
		$cpId = (int) $cpId;
		if ($cpId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'channel_partner_id is required.');
		}
		$whereCp = "id='" . $cpId . "' AND channel_partner_flag=1 AND customer_flag=0 AND isDelete=0";
		if ((int) $this->db->rp_getTotalRecord('executive', $whereCp, 0) <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Invalid Channel Partner.');
		}

		$this->backfillMissingOutward($cpId);

		$company = $this->db->rp_getValue('executive', 'company_name', "id='" . $cpId . "'", 0);
		$moves = $this->getStockMovements($cpId);
		$search = trim($searchName);
		$all = array();
		$sr = 0;
		$running = 0;
		foreach ($moves as $m) {
			$code = (isset($m['catno']) && $m['catno'] !== '' && $m['catno'] !== '-') ? $m['catno'] : '';
			$name = isset($m['pro_name']) ? $m['pro_name'] : '';
			$label = $code !== '' ? ($code . ' - ' . $name) : $name;
			$running += (float) $m['qty'];
			$isIn = ($m['txn_type'] === 'in' || (float) $m['qty_in'] > 0);
			$dateShow = '';
			if (!empty($m['date']) && $m['date'] != '0000-00-00') {
				$dateShow = date('d-m-Y', strtotime($m['date']));
			}
			$sr++;
			$all[] = array(
				'sr' => $sr,
				'id' => (int) $m['id'],
				'date' => isset($m['date']) ? $m['date'] : '',
				'date_display' => $dateShow,
				'bill_no' => $m['bill_no'] !== '' ? $m['bill_no'] : '-',
				'bill_date' => isset($m['bill_date']) ? $m['bill_date'] : '',
				'ref_order_id' => (int) $m['ref_order_id'],
				'txn_type' => $isIn ? 'in' : 'out',
				'txn_label' => $isIn ? 'INWARD' : 'OUTWARD',
				'pro_id' => (int) $m['pro_id'],
				'product_code' => $code,
				'product_name' => $name,
				'product_label' => $label,
				'qty_in' => round((float) $m['qty_in'], 2),
				'qty_out' => round((float) $m['qty_out'], 2),
				'balance' => round($running, 2),
				'remark' => isset($m['remark']) ? $m['remark'] : '',
			);
		}

		$result = array();
		if ($search === '') {
			$result = $all;
		} else {
			$sr2 = 0;
			foreach ($all as $row) {
				$hay = strtolower($row['product_label'] . ' ' . $row['bill_no'] . ' ' . $row['product_name'] . ' ' . $row['product_code']);
				if (strpos($hay, strtolower($search)) === false) {
					continue;
				}
				$sr2++;
				$row['sr'] = $sr2;
				$result[] = $row;
			}
		}

		return array(
			'ack' => 1,
			'ack_msg' => 'My Stock (Inward/Outward) ready',
			'channel_partner_id' => $cpId,
			'company_name' => $company ? $company : '',
			'view' => 'inout',
			'title' => 'Inward / Outward (Bill No & Date)',
			'total' => count($result),
			'result' => $result,
		);
	}
}
