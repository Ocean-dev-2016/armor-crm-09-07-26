<?php
/**
 * Re-Order → BRM helpers
 * - Ensure send_to_brm columns
 * - Detect Re-Order description
 * - Auto / manual mark sent to BRM
 */

if (!function_exists('reorder_brm_is_reorder_desc')) {
	function reorder_brm_is_reorder_desc($description)
	{
		$n = strtolower(str_replace(array('-', ' ', '_'), '', (string) $description));
		return ($n !== '' && strpos($n, 'reorder') !== false);
	}
}

if (!function_exists('reorder_brm_conn')) {
	function reorder_brm_conn($db)
	{
		if (isset($db->myconn) && $db->myconn) {
			return $db->myconn;
		}
		if (isset($db->conn) && $db->conn) {
			return $db->conn;
		}
		if (isset($db->link) && $db->link) {
			return $db->link;
		}
		return null;
	}
}

if (!function_exists('reorder_brm_ensure_columns')) {
	function reorder_brm_ensure_columns($db)
	{
		static $done = false;
		if ($done) {
			return true;
		}
		$conn = reorder_brm_conn($db);
		if (!$conn) {
			return false;
		}
		$cols = array(
			'send_to_brm' => "ALTER TABLE `followup` ADD COLUMN `send_to_brm` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=sent to BRM Re-Order Report'",
			'send_to_brm_date' => "ALTER TABLE `followup` ADD COLUMN `send_to_brm_date` datetime DEFAULT NULL",
			'send_to_brm_by' => "ALTER TABLE `followup` ADD COLUMN `send_to_brm_by` int(11) NOT NULL DEFAULT 0",
		);
		foreach ($cols as $col => $alter) {
			$res = @mysqli_query($conn, "SHOW COLUMNS FROM `followup` LIKE '" . mysqli_real_escape_string($conn, $col) . "'");
			if (!$res || mysqli_num_rows($res) == 0) {
				@mysqli_query($conn, $alter);
			}
		}
		$done = true;
		return true;
	}
}

if (!function_exists('reorder_brm_mark_sent')) {
	function reorder_brm_mark_sent($db, $followupId, $adminId = 0)
	{
		reorder_brm_ensure_columns($db);
		$followupId = (int) $followupId;
		if ($followupId <= 0) {
			return false;
		}
		$adminId = (int) $adminId;
		return (bool) $db->rp_update("followup", array(
			"send_to_brm" => 1,
			"send_to_brm_date" => date("Y-m-d H:i:s"),
			"send_to_brm_by" => $adminId,
		), "id='" . $followupId . "' AND isDelete=0", 0);
	}
}

if (!function_exists('reorder_brm_auto_send_pending')) {
	/**
	 * Auto-send all Admin Panel Re-Order followups not yet flagged for BRM.
	 * Safe to call multiple times (idempotent).
	 */
	function reorder_brm_auto_send_pending($db, $adminId = 0)
	{
		reorder_brm_ensure_columns($db);
		$conn = reorder_brm_conn($db);
		if (!$conn) {
			return 0;
		}
		$adminId = (int) $adminId;
		$now = date('Y-m-d H:i:s');
		$sql = "UPDATE followup
			SET send_to_brm=1,
				send_to_brm_date=IF(send_to_brm_date IS NULL OR send_to_brm_date='0000-00-00 00:00:00', '{$now}', send_to_brm_date),
				send_to_brm_by=IF(IFNULL(send_to_brm_by,0)=0, {$adminId}, send_to_brm_by)
			WHERE isDelete=0
			AND entry_type='1'
			AND IFNULL(send_to_brm,0)=0
			AND REPLACE(REPLACE(LOWER(IFNULL(description,'')), '-', ''), ' ', '') LIKE '%reorder%'";
		@mysqli_query($conn, $sql);
		return (int) mysqli_affected_rows($conn);
	}
}

if (!function_exists('reorder_report_customer_id_expr')) {
	/**
	 * SQL expression to resolve followup → executive/customer id.
	 */
	function reorder_report_customer_id_expr($alias = 'f')
	{
		$a = preg_replace('/[^a-zA-Z0-9_]/', '', $alias);
		return "CASE
			WHEN {$a}.reference_table='executive' THEN {$a}.reference_id
			WHEN {$a}.reference_table='quotation_detail' THEN (
				SELECT qd.customer_id FROM quotation_detail qd WHERE qd.id={$a}.reference_id LIMIT 1
			)
			WHEN {$a}.reference_table='manual_invoice_import' THEN (
				SELECT mi.customer_id FROM manually_invoice_outstanding_import mi WHERE mi.id={$a}.reference_id LIMIT 1
			)
			ELSE IFNULL({$a}.visitor_id,0)
		END";
	}
}

if (!function_exists('reorder_report_brm_customer_ids')) {
	/**
	 * Customer IDs that already have Admin Re-Order followup sent to BRM.
	 * @return int[]
	 */
	function reorder_report_brm_customer_ids($db)
	{
		static $cache = null;
		if ($cache !== null) {
			return $cache;
		}
		$cache = array();
		$conn = reorder_brm_conn($db);
		if (!$conn) {
			return $cache;
		}
		$cidExpr = reorder_report_customer_id_expr('f');
		$sql = "SELECT DISTINCT cid FROM (
			SELECT ({$cidExpr}) AS cid
			FROM followup f
			WHERE f.isDelete=0
			AND f.entry_type='1'
			AND IFNULL(f.send_to_brm,0)='1'
			AND REPLACE(REPLACE(LOWER(IFNULL(f.description,'')), '-', ''), ' ', '') LIKE '%reorder%'
		) t WHERE cid>0";
		$res = @mysqli_query($conn, $sql);
		if ($res) {
			while ($r = mysqli_fetch_assoc($res)) {
				$cache[] = (int) $r['cid'];
			}
		}
		return $cache;
	}
}

if (!function_exists('reorder_report_quo_only_where')) {
	function reorder_report_quo_only_where($db, $opts = array())
	{
		$conn = reorder_brm_conn($db);
		$salesId = isset($opts['sales_executive']) ? (int) $opts['sales_executive'] : 0;
		$createdFrom = isset($opts['created_from']) ? trim($opts['created_from']) : '';
		$createdTo = isset($opts['created_to']) ? trim($opts['created_to']) : '';
		$searchName = isset($opts['searchName']) ? trim($opts['searchName']) : '';

		$exclude = reorder_report_brm_customer_ids($db);
		$notIn = !empty($exclude) ? implode(',', array_map('intval', $exclude)) : '0';

		$where = "q.isDelete=0 AND q.status!=-1 AND q.status!=4 AND IFNULL(q.customer_id,0)>0
			AND q.customer_id NOT IN ({$notIn})";

		if ($salesId > 0) {
			$where .= " AND q.sales_id='" . $salesId . "'";
		}
		if ($createdFrom !== '' && $createdTo !== '' && $conn) {
			$where .= " AND DATE(q.quotation_date) >= '" . mysqli_real_escape_string($conn, $createdFrom) . "'
				AND DATE(q.quotation_date) <= '" . mysqli_real_escape_string($conn, $createdTo) . "'";
		}
		if ($searchName !== '' && $conn) {
			$sn = mysqli_real_escape_string($conn, $searchName);
			$where .= " AND (
				q.company_name LIKE '%{$sn}%'
				OR q.customer_name LIKE '%{$sn}%'
				OR q.client_code LIKE '%{$sn}%'
				OR q.customer_id IN (
					SELECT e.id FROM executive e WHERE e.isDelete=0 AND e.mobile_no1 LIKE '%{$sn}%'
				)
			)";
		}
		return $where;
	}
}

if (!function_exists('reorder_report_quotation_only_customers')) {
	/**
	 * Customers with open quotations (not cart, not converted to order)
	 * who do NOT have any Admin Re-Order followup sent to BRM.
	 *
	 * @param object $db
	 * @param array $opts sales_executive, created_from, created_to, searchName, offset, limit
	 * @return array list of synthetic followup-like rows
	 */
	function reorder_report_quotation_only_customers($db, $opts = array())
	{
		$conn = reorder_brm_conn($db);
		if (!$conn) {
			return array();
		}

		$offset = isset($opts['offset']) ? max(0, (int) $opts['offset']) : 0;
		$limit = isset($opts['limit']) ? max(0, (int) $opts['limit']) : 0;
		$where = reorder_report_quo_only_where($db, $opts);

		$limSql = '';
		if ($limit > 0) {
			$limSql = " LIMIT " . (int) $offset . ", " . (int) $limit;
		}

		$sql = "SELECT q.customer_id,
				MAX(q.quotation_date) AS last_quo_date,
				SUBSTRING_INDEX(GROUP_CONCAT(q.sales_id ORDER BY q.quotation_date DESC, q.id DESC), ',', 1) AS sales_id,
				COUNT(q.id) AS quo_cnt
			FROM quotation_detail q
			WHERE {$where}
			GROUP BY q.customer_id
			ORDER BY last_quo_date DESC, q.customer_id DESC
			{$limSql}";

		$res = @mysqli_query($conn, $sql);
		$rows = array();
		if (!$res) {
			return $rows;
		}
		while ($r = mysqli_fetch_assoc($res)) {
			$cid = (int) $r['customer_id'];
			if ($cid <= 0) {
				continue;
			}
			$dt = !empty($r['last_quo_date']) && $r['last_quo_date'] != '0000-00-00'
				? $r['last_quo_date'] . ' 00:00:00' : '';
			$rows[] = array(
				'id' => 0,
				'ror_quotation_only' => 1,
				'visitor_id' => $cid,
				'reference_table' => 'executive',
				'reference_id' => $cid,
				'user_id' => (int) $r['sales_id'],
				'created_date' => $dt,
				'followup_date' => $dt,
				'description' => 'Quotation Pending',
				'through' => '',
				'response' => '',
				'status' => 1,
				'next_action' => '',
				'quo_cnt' => (int) $r['quo_cnt'],
			);
		}
		return $rows;
	}
}

if (!function_exists('reorder_report_quotation_only_count')) {
	function reorder_report_quotation_only_count($db, $opts = array())
	{
		$conn = reorder_brm_conn($db);
		if (!$conn) {
			return 0;
		}
		$where = reorder_report_quo_only_where($db, $opts);
		$sql = "SELECT COUNT(DISTINCT q.customer_id) AS c FROM quotation_detail q WHERE {$where}";
		$res = @mysqli_query($conn, $sql);
		if (!$res) {
			return 0;
		}
		$row = mysqli_fetch_assoc($res);
		return (int) $row['c'];
	}
}
