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
}
