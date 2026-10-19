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
