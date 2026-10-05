<?php
/**
 * Approval Entry module — table ensure + helpers.
 */

if (!function_exists('armor_approval_entry_ensure_table')) {
	function armor_approval_entry_ensure_table($db)
	{
		static $done = false;
		if ($done) {
			return;
		}
		$done = true;
		$sql = "CREATE TABLE IF NOT EXISTS `approval_entry` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`entry_date` date DEFAULT NULL,
			`customer_id` int(11) DEFAULT NULL,
			`company_name` varchar(255) DEFAULT NULL,
			`type_of_company` varchar(255) DEFAULT NULL,
			`person_name` varchar(255) DEFAULT NULL,
			`designation` varchar(255) DEFAULT NULL,
			`mobile` varchar(50) DEFAULT NULL,
			`email` varchar(255) DEFAULT NULL,
			`amount` decimal(15,2) DEFAULT NULL,
			`given_by` varchar(255) DEFAULT NULL,
			`payment_mode` varchar(50) DEFAULT NULL,
			`payment_status` varchar(50) DEFAULT 'Not Done',
			`reminder_date` date DEFAULT NULL,
			`reminder_notified` tinyint(1) NOT NULL DEFAULT 0,
			`approval_status` varchar(50) DEFAULT 'Pending',
			`attachment` varchar(255) DEFAULT NULL,
			`project_name` text,
			`project_builder` varchar(255) DEFAULT NULL,
			`contractor` varchar(255) DEFAULT NULL,
			`isDelete` tinyint(1) NOT NULL DEFAULT 0,
			`created_by` int(11) DEFAULT NULL,
			`created_date` datetime DEFAULT NULL,
			`modified_date` datetime DEFAULT NULL,
			PRIMARY KEY (`id`),
			KEY `idx_customer` (`customer_id`),
			KEY `idx_entry_date` (`entry_date`),
			KEY `idx_payment_status` (`payment_status`),
			KEY `idx_delete` (`isDelete`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		@$db->query($sql);

		armor_approval_entry_ensure_column($db, 'payment_status', "ALTER TABLE `approval_entry` ADD COLUMN `payment_status` varchar(50) DEFAULT 'Not Done' AFTER `payment_mode`");
		armor_approval_entry_ensure_column($db, 'attachment', "ALTER TABLE `approval_entry` ADD COLUMN `attachment` varchar(255) DEFAULT NULL AFTER `approval_status`");
		armor_approval_entry_ensure_column($db, 'reminder_date', "ALTER TABLE `approval_entry` ADD COLUMN `reminder_date` date DEFAULT NULL AFTER `payment_status`");
		armor_approval_entry_ensure_column($db, 'reminder_notified', "ALTER TABLE `approval_entry` ADD COLUMN `reminder_notified` tinyint(1) NOT NULL DEFAULT 0 AFTER `reminder_date`");
	}
}

if (!function_exists('armor_approval_entry_ensure_column')) {
	function armor_approval_entry_ensure_column($db, $column, $alterSql)
	{
		$conn = isset($db->myconn) ? $db->myconn : null;
		if (!$conn) {
			@$db->query($alterSql);
			return;
		}
		$res = @mysqli_query($conn, "SHOW COLUMNS FROM `approval_entry` LIKE '" . mysqli_real_escape_string($conn, $column) . "'");
		$has = ($res && mysqli_num_rows($res) > 0);
		if (!$has) {
			@mysqli_query($conn, $alterSql);
		}
	}
}

if (!function_exists('armor_approval_payment_modes')) {
	function armor_approval_payment_modes()
	{
		return array('Cash', 'Online');
	}
}

if (!function_exists('armor_approval_payment_status_options')) {
	function armor_approval_payment_status_options()
	{
		return array('Done', 'Not Done');
	}
}

if (!function_exists('armor_approval_status_options')) {
	function armor_approval_status_options()
	{
		return array('Yes', 'No', 'Pending');
	}
}

if (!function_exists('armor_approval_upload_dir')) {
	function armor_approval_upload_dir()
	{
		$dir = dirname(__DIR__) . '/uploads/approval_attachment/';
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		return $dir;
	}
}

if (!function_exists('armor_approval_attachment_url')) {
	function armor_approval_attachment_url($filename)
	{
		$filename = trim((string)$filename);
		if ($filename === '') {
			return '';
		}
		return SITEURL . 'uploads/approval_attachment/' . rawurlencode($filename);
	}
}

if (!function_exists('armor_approval_handle_attachment_upload')) {
	function armor_approval_handle_attachment_upload($fileField = 'attachment')
	{
		if (!isset($_FILES[$fileField]) || !is_array($_FILES[$fileField])) {
			return array('ok' => true, 'filename' => '', 'error' => '');
		}
		$f = $_FILES[$fileField];
		if (!isset($f['error']) || (int)$f['error'] === UPLOAD_ERR_NO_FILE) {
			return array('ok' => true, 'filename' => '', 'error' => '');
		}
		if ((int)$f['error'] !== UPLOAD_ERR_OK) {
			return array('ok' => false, 'filename' => '', 'error' => 'Attachment upload failed.');
		}
		if ((int)$f['size'] > 5 * 1024 * 1024) {
			return array('ok' => false, 'filename' => '', 'error' => 'Attachment max size is 5 MB.');
		}
		$orig = basename((string)$f['name']);
		$ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
		$allowed = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'zip');
		if ($ext === '' || !in_array($ext, $allowed, true)) {
			return array('ok' => false, 'filename' => '', 'error' => 'Invalid attachment type. Allowed: jpg, png, pdf, doc, xls, zip.');
		}
		$safe = 'appr_' . date('YmdHis') . '_' . substr(sha1(uniqid((string)mt_rand(), true)), 0, 8) . '.' . $ext;
		$dest = armor_approval_upload_dir() . $safe;
		if (!@move_uploaded_file($f['tmp_name'], $dest)) {
			return array('ok' => false, 'filename' => '', 'error' => 'Could not save attachment.');
		}
		return array('ok' => true, 'filename' => $safe, 'error' => '');
	}
}

if (!function_exists('armor_approval_calc_reminder_date')) {
	/**
	 * Reminder = 2 months before Due Date.
	 */
	function armor_approval_calc_reminder_date($entry_date)
	{
		$entry_date = trim((string)$entry_date);
		if ($entry_date === '' || $entry_date === '0000-00-00') {
			return null;
		}
		$ts = strtotime($entry_date . ' -2 months');
		if ($ts === false) {
			return null;
		}
		return date('Y-m-d', $ts);
	}
}

if (!function_exists('armor_approval_sync_reminder_date')) {
	function armor_approval_sync_reminder_date($db, $id, $entry_date, $forceResetNotify = false)
	{
		$id = (int)$id;
		$reminder = armor_approval_calc_reminder_date($entry_date);
		if ($id <= 0 || $reminder === null) {
			return $reminder;
		}
		$upd = array(
			'reminder_date' => $reminder,
			'modified_date' => date('Y-m-d H:i:s'),
		);
		if ($forceResetNotify) {
			$upd['reminder_notified'] = 0;
		} else {
			$old = $db->rp_getValue('approval_entry', 'reminder_date', "id='" . $id . "'", 0);
			if ($old != $reminder) {
				$upd['reminder_notified'] = 0;
			}
		}
		$db->rp_update('approval_entry', $upd, "id='" . $id . "'");
		return $reminder;
	}
}

if (!function_exists('armor_approval_create_reminder_notification')) {
	function armor_approval_create_reminder_notification($db, $row)
	{
		$id = (int)$row['id'];
		if ($id <= 0) {
			return false;
		}
		$company = !empty($row['company_name']) ? $row['company_name'] : 'Company';
		$amount = ($row['amount'] !== null && $row['amount'] !== '') ? number_format((float)$row['amount'], 2) : '-';
		$entryDisp = !empty($row['entry_date']) ? date('d/M/Y', strtotime($row['entry_date'])) : '-';
		$reminderDate = !empty($row['reminder_date']) ? date('d/M/Y', strtotime($row['reminder_date'])) : date('d/M/Y');
		$title = 'Approval Due Date Reminder — ' . $company;
		$desc = 'Due Date: ' . $entryDisp . ' | Reminder (2 months before Due Date): ' . $reminderDate . ' | Amount: ' . $amount;
		if (!empty($row['person_name'])) {
			$desc .= ' | Person: ' . $row['person_name'];
		}
		$userId = isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) ? (int)$_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] : 0;
		$respective = !empty($row['reminder_date']) ? date('Y-m-d H:i:s', strtotime($row['reminder_date'])) : date('Y-m-d H:i:s');
		$rows = array(
			'user_id',
			'referance_id',
			'referance_type',
			'notification_title',
			'notification_description',
			'notification_type',
			'type_slug',
			'respective_date',
			'user_type',
			'isDelete',
			'isActive',
			'created_date',
		);
		$values = array(
			$userId,
			$id,
			'approval_entry',
			$title,
			$desc,
			'approval_reminder',
			'approval_reminder',
			$respective,
			'admin',
			0,
			1,
			date('Y-m-d H:i:s'),
		);
		$db->rp_insert('notification', $values, $rows, 0);
		$db->rp_update('approval_entry', array(
			'reminder_notified' => 1,
			'modified_date' => date('Y-m-d H:i:s'),
		), "id='" . $id . "'");
		return true;
	}
}

if (!function_exists('armor_approval_fire_due_reminders')) {
	function armor_approval_fire_due_reminders($db)
	{
		$today = date('Y-m-d');
		$fired = array();

		/* Recalc reminder_date = Due Date - 2 months for pending reminders */
		$pending = $db->rp_getData(
			'approval_entry',
			'id, entry_date, reminder_date',
			"isDelete=0 AND reminder_notified=0 AND entry_date IS NOT NULL AND entry_date!='0000-00-00'",
			'',
			0
		);
		if ($pending) {
			while ($m = mysqli_fetch_assoc($pending)) {
				armor_approval_sync_reminder_date($db, (int)$m['id'], $m['entry_date'], false);
			}
		}

		/* Fill any still-missing reminder_date */
		$missing = $db->rp_getData(
			'approval_entry',
			'id, entry_date',
			"isDelete=0 AND entry_date IS NOT NULL AND entry_date!='0000-00-00' AND (reminder_date IS NULL OR reminder_date='0000-00-00' OR reminder_date='')",
			'',
			0
		);
		if ($missing) {
			while ($m = mysqli_fetch_assoc($missing)) {
				armor_approval_sync_reminder_date($db, (int)$m['id'], $m['entry_date'], true);
			}
		}

		$r = $db->rp_getData(
			'approval_entry',
			'*',
			"isDelete=0 AND reminder_notified=0 AND reminder_date IS NOT NULL AND reminder_date!='0000-00-00' AND reminder_date<='" . $today . "'",
			'reminder_date ASC, id ASC',
			0
		);
		if ($r) {
			while ($row = mysqli_fetch_assoc($r)) {
				if (armor_approval_create_reminder_notification($db, $row)) {
					$fired[] = array(
						'id' => (int)$row['id'],
						'company_name' => $row['company_name'],
						'entry_date' => $row['entry_date'],
						'reminder_date' => $row['reminder_date'],
						'amount' => $row['amount'],
					);
				}
			}
		}
		return $fired;
	}
}

if (!function_exists('armor_approval_module_password_default')) {
	function armor_approval_module_password_default()
	{
		return 'Armor@2040';
	}
}

if (!function_exists('armor_approval_ensure_setting_table')) {
	function armor_approval_ensure_setting_table($db)
	{
		static $done = false;
		if ($done) {
			return;
		}
		$done = true;
		@$db->query("CREATE TABLE IF NOT EXISTS `approval_module_setting` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`setting_key` varchar(100) NOT NULL,
			`setting_value` text,
			`modified_date` datetime DEFAULT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `uk_setting_key` (`setting_key`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	}
}

if (!function_exists('armor_approval_get_setting')) {
	function armor_approval_get_setting($db, $key, $default = '')
	{
		armor_approval_ensure_setting_table($db);
		$keyClean = method_exists($db, 'clean') ? $db->clean($key) : addslashes($key);
		$val = $db->rp_getValue('approval_module_setting', 'setting_value', "setting_key='" . $keyClean . "'", 0);
		if ($val === null || $val === false || $val === '') {
			return $default;
		}
		return $val;
	}
}

if (!function_exists('armor_approval_set_setting')) {
	function armor_approval_set_setting($db, $key, $value)
	{
		armor_approval_ensure_setting_table($db);
		$keyClean = method_exists($db, 'clean') ? $db->clean($key) : addslashes($key);
		$now = date('Y-m-d H:i:s');
		$exists = $db->rp_getValue('approval_module_setting', 'id', "setting_key='" . $keyClean . "'", 0);
		if ($exists) {
			$db->rp_update('approval_module_setting', array(
				'setting_value' => $value,
				'modified_date' => $now,
			), "setting_key='" . $keyClean . "'");
		} else {
			$db->rp_insert('approval_module_setting', array($key, $value, $now), array('setting_key', 'setting_value', 'modified_date'), 0);
		}
		return true;
	}
}

if (!function_exists('armor_approval_module_password_hash')) {
	/** Returns stored hash; seeds default password hash if missing. Never echo plain password. */
	function armor_approval_module_password_hash($db = null)
	{
		if ($db === null && isset($GLOBALS['db'])) {
			$db = $GLOBALS['db'];
		}
		if (!$db) {
			return password_hash(armor_approval_module_password_default(), PASSWORD_DEFAULT);
		}
		$hash = armor_approval_get_setting($db, 'module_password_hash', '');
		if ($hash === '') {
			$hash = password_hash(armor_approval_module_password_default(), PASSWORD_DEFAULT);
			armor_approval_set_setting($db, 'module_password_hash', $hash);
		}
		return $hash;
	}
}

if (!function_exists('armor_approval_module_password')) {
	/** @deprecated use verify — kept for compatibility; returns default plain only as fallback seed. */
	function armor_approval_module_password()
	{
		return armor_approval_module_password_default();
	}
}

if (!function_exists('armor_approval_unlock_session_key')) {
	function armor_approval_unlock_session_key()
	{
		return SITE_SESS . '_APPROVAL_MODULE_UNLOCK';
	}
}

if (!function_exists('armor_approval_is_unlocked')) {
	function armor_approval_is_unlocked()
	{
		$key = armor_approval_unlock_session_key();
		return !empty($_SESSION[$key]);
	}
}

if (!function_exists('armor_approval_set_unlocked')) {
	function armor_approval_set_unlocked($unlocked = true)
	{
		$key = armor_approval_unlock_session_key();
		if ($unlocked) {
			$_SESSION[$key] = 1;
		} else {
			unset($_SESSION[$key]);
		}
	}
}

if (!function_exists('armor_approval_verify_password')) {
	function armor_approval_verify_password($input, $db = null)
	{
		$input = (string)$input;
		if ($db === null && isset($GLOBALS['db'])) {
			$db = $GLOBALS['db'];
		}
		if ($db) {
			$hash = armor_approval_module_password_hash($db);
			if (password_verify($input, $hash)) {
				return true;
			}
			/* One-time migrate: if still default and hash mismatch due to old plain storage */
			$plainOld = armor_approval_get_setting($db, 'module_password', '');
			if ($plainOld !== '' && $input === $plainOld) {
				$newHash = password_hash($input, PASSWORD_DEFAULT);
				armor_approval_set_setting($db, 'module_password_hash', $newHash);
				armor_approval_set_setting($db, 'module_password', '');
				return true;
			}
			return false;
		}
		$expected = armor_approval_module_password_default();
		if (function_exists('hash_equals')) {
			return hash_equals($expected, $input);
		}
		return ($expected === $input);
	}
}

if (!function_exists('armor_approval_change_password')) {
	function armor_approval_change_password($db, $current, $newPassword)
	{
		$current = (string)$current;
		$newPassword = (string)$newPassword;
		if ($newPassword === '' || strlen($newPassword) < 6) {
			return array('ok' => false, 'message' => 'New password must be at least 6 characters.');
		}
		if (!armor_approval_verify_password($current, $db)) {
			return array('ok' => false, 'message' => 'Current password is incorrect.');
		}
		$hash = password_hash($newPassword, PASSWORD_DEFAULT);
		armor_approval_set_setting($db, 'module_password_hash', $hash);
		armor_approval_set_setting($db, 'module_password', '');
		return array('ok' => true, 'message' => 'Password changed successfully.');
	}
}

if (!function_exists('armor_approval_require_unlock')) {
	/**
	 * Block Approval module pages until password unlock for current session.
	 * @param bool $isAjax JSON response instead of redirect
	 */
	function armor_approval_require_unlock($isAjax = false)
	{
		if (armor_approval_is_unlocked()) {
			return true;
		}
		if ($isAjax) {
			if (!headers_sent()) {
				header('Content-Type: application/json; charset=utf-8');
			}
			echo json_encode(array(
				'success' => false,
				'locked' => true,
				'message' => 'Approval module is locked. Please enter password.',
			));
			exit;
		}
		$return = 'approval_manage.php';
		if (!empty($_SERVER['REQUEST_URI'])) {
			$base = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
			if ($base !== '' && preg_match('/^approval_/i', $base)) {
				$return = $base;
				$qs = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
				if ($qs) {
					$return .= '?' . $qs;
				}
			}
		}
		header('Location: approval_unlock.php?return=' . rawurlencode($return));
		exit;
	}
}
