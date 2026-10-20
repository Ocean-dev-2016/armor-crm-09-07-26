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
			'brm_reminder_admin_id' => "ALTER TABLE `followup` ADD COLUMN `brm_reminder_admin_id` int(11) NOT NULL DEFAULT 0 COMMENT 'BRM admin login id for private next-followup reminder'",
			'brm_reminder_sent' => "ALTER TABLE `followup` ADD COLUMN `brm_reminder_sent` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=BRM reminder already shown'",
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
			AND IFNULL(brm_reminder_admin_id,0)=0
			AND REPLACE(REPLACE(LOWER(IFNULL(description,'')), '-', ''), ' ', '') LIKE '%reorder%'";
		@mysqli_query($conn, $sql);
		return (int) mysqli_affected_rows($conn);
	}
}

if (!function_exists('reorder_brm_ensure_customer_assign_column')) {
	function reorder_brm_ensure_customer_assign_column($db)
	{
		static $done = false;
		if ($done) {
			return true;
		}
		$conn = reorder_brm_conn($db);
		if (!$conn) {
			return false;
		}
		$res = @mysqli_query($conn, "SHOW COLUMNS FROM `executive` LIKE 'brm_admin_id'");
		if (!$res || mysqli_num_rows($res) == 0) {
			@mysqli_query($conn, "ALTER TABLE `executive` ADD COLUMN `brm_admin_id` int(11) NOT NULL DEFAULT 0 COMMENT 'BRM1/BRM2 dealer_distributor_network.id for Re-Order report split'");
		}
		$done = true;
		return true;
	}
}

if (!function_exists('reorder_brm_login_ids')) {
	/**
	 * @return array{brm1:int,brm2:int}
	 */
	function reorder_brm_login_ids($db)
	{
		static $cache = null;
		if ($cache !== null) {
			return $cache;
		}
		$cache = array('brm1' => 0, 'brm2' => 0);
		$conn = reorder_brm_conn($db);
		if (!$conn) {
			return $cache;
		}
		$r = @mysqli_query($conn, "SELECT id, username, admin_type FROM dealer_distributor_network
			WHERE isDelete=0 AND (
				username IN ('BRM1','BRM2')
				OR admin_type IN (23,32)
			)");
		if ($r) {
			while ($row = mysqli_fetch_assoc($r)) {
				$u = strtoupper(trim($row['username']));
				$at = (int) $row['admin_type'];
				if ($u === 'BRM1' || $at === 23) {
					$cache['brm1'] = (int) $row['id'];
				}
				if ($u === 'BRM2' || $at === 32) {
					$cache['brm2'] = (int) $row['id'];
				}
			}
		}
		return $cache;
	}
}

if (!function_exists('reorder_brm_is_brm_login')) {
	/** Current session is BRM1 or BRM2 login (not Super Admin). */
	function reorder_brm_is_brm_login($db)
	{
		$adminType = isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) ? (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] : -1;
		if ($adminType === 0) {
			return false;
		}
		/* Known BRM admin_type ids + slug check */
		if ($adminType === 23 || $adminType === 32) {
			return true;
		}
		$slug = strtolower((string) $db->rp_getValue("admin_type", "slug", "id='" . $adminType . "' AND isDelete=0", 0));
		if ($slug === 'brm1' || $slug === 'brm2') {
			return true;
		}
		$name = strtoupper(trim((string) $db->rp_getValue("admin_type", "name", "id='" . $adminType . "' AND isDelete=0", 0)));
		if ($name === 'BRM1' || $name === 'BRM2') {
			return true;
		}
		$ids = reorder_brm_login_ids($db);
		$sess = isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) ? (int) $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] : 0;
		return ($sess > 0 && ($sess === $ids['brm1'] || $sess === $ids['brm2']));
	}
}

if (!function_exists('reorder_brm_auto_divide_customers')) {
	/**
	 * Default split: unassigned customers → BRM1 / BRM2 by customer id (odd/even).
	 * Does not change seid / sales person.
	 */
	function reorder_brm_auto_divide_customers($db)
	{
		reorder_brm_ensure_customer_assign_column($db);
		$conn = reorder_brm_conn($db);
		if (!$conn) {
			return 0;
		}
		$ids = reorder_brm_login_ids($db);
		$b1 = (int) $ids['brm1'];
		$b2 = (int) $ids['brm2'];
		if ($b1 <= 0 || $b2 <= 0) {
			return 0;
		}
		$sql = "UPDATE executive
			SET brm_admin_id = IF(MOD(id,2)=1, {$b1}, {$b2})
			WHERE isDelete=0 AND IFNULL(brm_admin_id,0)=0";
		@mysqli_query($conn, $sql);
		return (int) mysqli_affected_rows($conn);
	}
}

if (!function_exists('reorder_brm_scoped_admin_id')) {
	/**
	 * If current user is BRM1/BRM2 → return their admin sess id for customer filter.
	 * Super Admin / other roles → 0 (see all).
	 */
	function reorder_brm_scoped_admin_id($db)
	{
		if (!reorder_brm_is_brm_login($db)) {
			return 0;
		}
		return isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) ? (int) $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] : 0;
	}
}

if (!function_exists('reorder_brm_customer_filter_sql')) {
	/**
	 * SQL fragment: followup rows limited to customers assigned to this BRM login.
	 * Empty string when no scope (show all).
	 */
	function reorder_brm_customer_filter_sql($db, $followupAlias = '')
	{
		$adminId = reorder_brm_scoped_admin_id($db);
		if ($adminId <= 0) {
			return '';
		}
		reorder_brm_ensure_customer_assign_column($db);
		$cidExpr = reorder_report_customer_id_expr($followupAlias);
		return " ({$cidExpr}) IN (
			SELECT e.id FROM executive e WHERE e.isDelete=0 AND e.brm_admin_id='" . (int) $adminId . "'
		) AND ";
	}
}

if (!function_exists('reorder_report_customer_id_expr')) {
	/**
	 * SQL expression to resolve followup → executive/customer id.
	 */
	function reorder_report_customer_id_expr($alias = 'f')
	{
		$a = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $alias);
		$p = ($a !== '') ? ($a . '.') : '';
		return "CASE
			WHEN {$p}reference_table='executive' THEN {$p}reference_id
			WHEN {$p}reference_table='quotation_detail' THEN (
				SELECT qd.customer_id FROM quotation_detail qd WHERE qd.id={$p}reference_id LIMIT 1
			)
			WHEN {$p}reference_table='manual_invoice_import' THEN (
				SELECT mi.customer_id FROM manually_invoice_outstanding_import mi WHERE mi.id={$p}reference_id LIMIT 1
			)
			ELSE IFNULL({$p}visitor_id,0)
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

		$brmScope = isset($opts['brm_admin_id']) ? (int) $opts['brm_admin_id'] : reorder_brm_scoped_admin_id($db);
		if ($brmScope > 0) {
			reorder_brm_ensure_customer_assign_column($db);
			$where .= " AND q.customer_id IN (
				SELECT e.id FROM executive e WHERE e.isDelete=0 AND e.brm_admin_id='" . $brmScope . "'
			)";
		}

		if ($salesId > 0) {
			/* Match quotation.sales_id, or customer.seid when quotation has no sales */
			$where .= " AND (
				q.sales_id='" . $salesId . "'
				OR (IFNULL(q.sales_id,0)=0 AND q.customer_id IN (
					SELECT e.id FROM executive e WHERE e.isDelete=0 AND e.seid='" . $salesId . "'
				))
			)";
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
			$salesId = (int) $r['sales_id'];
			if ($salesId <= 0) {
				$salesId = (int) $db->rp_getValue("executive", "seid", "id='" . $cid . "'", 0);
			}
			$dt = !empty($r['last_quo_date']) && $r['last_quo_date'] != '0000-00-00'
				? $r['last_quo_date'] . ' 00:00:00' : '';
			$rows[] = array(
				'id' => 0,
				'ror_quotation_only' => 1,
				'visitor_id' => $cid,
				'reference_table' => 'executive',
				'reference_id' => $cid,
				'user_id' => $salesId,
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

if (!function_exists('reorder_brm_mark_private_reminder')) {
	/**
	 * Tag Next Followup created from Re-Order BRM Response for private BRM login reminder.
	 */
	function reorder_brm_mark_private_reminder($db, $followupId, $adminId)
	{
		reorder_brm_ensure_columns($db);
		$followupId = (int) $followupId;
		$adminId = (int) $adminId;
		if ($followupId <= 0 || $adminId <= 0) {
			return false;
		}
		return (bool) $db->rp_update("followup", array(
			"brm_reminder_admin_id" => $adminId,
			"brm_reminder_sent" => 0,
			"send_to_brm" => 0,
		), "id='" . $followupId . "' AND isDelete=0", 0);
	}
}

if (!function_exists('reorder_brm_fire_due_reminders')) {
	/**
	 * Fire due BRM private next-followup reminders for one admin login.
	 * Due = followup_date <= now, status pending, not yet reminded.
	 *
	 * @return array list of toast payloads
	 */
	function reorder_brm_fire_due_reminders($db, $adminId)
	{
		$toasts = array();
		$adminId = (int) $adminId;
		if ($adminId <= 0) {
			return $toasts;
		}
		reorder_brm_ensure_columns($db);
		$conn = reorder_brm_conn($db);
		if (!$conn) {
			return $toasts;
		}
		$now = date('Y-m-d H:i:s');
		$sql = "SELECT id, visitor_id, reference_id, reference_table, description, through, followup_date, user_id
			FROM followup
			WHERE isDelete=0 AND IFNULL(status,0)=0
			AND IFNULL(brm_reminder_admin_id,0)='" . $adminId . "'
			AND IFNULL(brm_reminder_sent,0)=0
			AND followup_date IS NOT NULL
			AND followup_date!='0000-00-00 00:00:00'
			AND followup_date<='" . mysqli_real_escape_string($conn, $now) . "'
			ORDER BY followup_date ASC
			LIMIT 20";
		$res = @mysqli_query($conn, $sql);
		if (!$res) {
			return $toasts;
		}
		$throughMap = array("1" => "Call", "2" => "SMS", "3" => "Email", "4" => "Whatsapp", "5" => "Visit");
		while ($row = mysqli_fetch_assoc($res)) {
			$fid = (int) $row['id'];
			$cust = "Customer";
			if ($row['reference_table'] == "executive" || $row['reference_table'] == "sales_executive") {
				$cid = ($row['reference_table'] == "executive") ? (int) $row['reference_id'] : (int) $row['visitor_id'];
				$cust = $db->rp_getValue("executive", "company_name", "id='" . $cid . "'", 0);
			} else if ($row['reference_table'] == "quotation_detail") {
				$cust = $db->rp_getValue("quotation_detail", "company_name", "id='" . (int) $row['reference_id'] . "'", 0);
			} else if (!empty($row['visitor_id'])) {
				$cust = $db->rp_getValue("executive", "company_name", "id='" . (int) $row['visitor_id'] . "'", 0);
			}
			if ($cust == "") {
				$cust = "Customer";
			}
			$thru = isset($throughMap[$row['through']]) ? $throughMap[$row['through']] : "Followup";
			$ftime = date("d-m-Y h:i A", strtotime($row['followup_date']));
			$title = "BRM Reminder — " . $cust;
			$desc = "Followup due: " . $ftime . " via " . $thru;
			if (trim((string) $row['description']) !== "") {
				$desc .= " | " . strip_tags($row['description']);
			}

			/* Private notification for this BRM login only (not HR broadcast) */
			require_once dirname(__FILE__) . '/push_notification.class.php';
			$objPush = new PushNotification();
			$objPush->commonNotification($adminId, $fid, "followup", $title, $desc, "admin", "brm_followup_reminder");

			$db->rp_update("followup", array("brm_reminder_sent" => 1), "id='" . $fid . "'", 0);

			$toasts[] = array(
				'id' => $fid,
				'title' => $title,
				'sales_name' => 'BRM',
				'customer_name' => $cust,
				'followup_time' => $ftime,
				'through_label' => $thru,
				'description' => strip_tags((string) $row['description']),
				'link' => 'reorder_followup_report.php',
			);
		}
		return $toasts;
	}
}

if (!function_exists('reorder_report_quotation_only_value_total')) {
	/**
	 * Sum of grand_total for all open quotations in Quotation Pending (No Re-Order) view.
	 */
	function reorder_report_quotation_only_value_total($db, $opts = array())
	{
		$conn = reorder_brm_conn($db);
		if (!$conn) {
			return 0.0;
		}
		$where = reorder_report_quo_only_where($db, $opts);
		$sql = "SELECT IFNULL(SUM(q.grand_total),0) AS amt FROM quotation_detail q WHERE {$where}";
		$res = @mysqli_query($conn, $sql);
		if (!$res) {
			return 0.0;
		}
		$row = mysqli_fetch_assoc($res);
		return (float) $row['amt'];
	}
}

if (!function_exists('reorder_brm_assign_stats')) {
	/**
	 * Customer counts by BRM assignment (executive.brm_admin_id only — does not touch seid).
	 * @return array{brm1_id:int,brm2_id:int,brm1:int,brm2:int,unassigned:int,total:int}
	 */
	function reorder_brm_assign_stats($db)
	{
		reorder_brm_ensure_customer_assign_column($db);
		$ids = reorder_brm_login_ids($db);
		$out = array(
			'brm1_id' => (int) $ids['brm1'],
			'brm2_id' => (int) $ids['brm2'],
			'brm1' => 0,
			'brm2' => 0,
			'unassigned' => 0,
			'total' => 0,
		);
		$conn = reorder_brm_conn($db);
		if (!$conn) {
			return $out;
		}
		$b1 = $out['brm1_id'];
		$b2 = $out['brm2_id'];
		$r = @mysqli_query($conn, "SELECT IFNULL(brm_admin_id,0) AS bid, COUNT(*) AS c
			FROM executive WHERE isDelete=0 GROUP BY IFNULL(brm_admin_id,0)");
		if ($r) {
			while ($row = mysqli_fetch_assoc($r)) {
				$bid = (int) $row['bid'];
				$c = (int) $row['c'];
				$out['total'] += $c;
				if ($b1 > 0 && $bid === $b1) {
					$out['brm1'] = $c;
				} else if ($b2 > 0 && $bid === $b2) {
					$out['brm2'] = $c;
				} else if ($bid === 0) {
					$out['unassigned'] = $c;
				}
			}
		}
		return $out;
	}
}

if (!function_exists('reorder_brm_set_customer_admin')) {
	/**
	 * Assign / exchange customer to BRM1 or BRM2. Only updates executive.brm_admin_id.
	 * Never changes seid / sales person.
	 * @param int $customerId executive.id
	 * @param int $brmAdminId dealer_distributor_network.id of BRM1/BRM2 (0 = unassign)
	 */
	function reorder_brm_set_customer_admin($db, $customerId, $brmAdminId)
	{
		$customerId = (int) $customerId;
		$brmAdminId = (int) $brmAdminId;
		if ($customerId <= 0) {
			return false;
		}
		reorder_brm_ensure_customer_assign_column($db);
		if ($brmAdminId > 0) {
			$ids = reorder_brm_login_ids($db);
			if ($brmAdminId !== (int) $ids['brm1'] && $brmAdminId !== (int) $ids['brm2']) {
				return false;
			}
		}
		return (bool) $db->rp_update(
			"executive",
			array("brm_admin_id" => $brmAdminId),
			"id='" . $customerId . "' AND isDelete=0",
			0
		);
	}
}
