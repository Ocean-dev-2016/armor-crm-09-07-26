<?php

if (!function_exists('consultant_approval_decode_json_array')) {
	function consultant_approval_decode_json_array($raw)
	{
		$raw = trim((string) $raw);
		if ($raw === '') {
			return null;
		}

		$candidates = array($raw);
		$decodedHtml = html_entity_decode($raw, ENT_QUOTES, 'UTF-8');
		if ($decodedHtml !== $raw) {
			$candidates[] = $decodedHtml;
		}
		$stripped = stripslashes($raw);
		if ($stripped !== $raw) {
			$candidates[] = $stripped;
		}
		if ($decodedHtml !== $raw) {
			$candidates[] = stripslashes($decodedHtml);
		}

		foreach ($candidates as $candidate) {
			$candidate = preg_replace('/^\xEF\xBB\xBF/', '', trim((string) $candidate));
			if ($candidate === '') {
				continue;
			}
			$decoded = json_decode($candidate, true);
			if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
				return $decoded;
			}
		}

		return null;
	}
}

if (!function_exists('consultant_approval_regex_parse_product_groups')) {
	function consultant_approval_regex_parse_product_groups($raw)
	{
		$groups = array();
		if (!preg_match_all('/"([^"]+)"\s*:\s*\[(.*?)\]/s', (string) $raw, $matches, PREG_SET_ORDER)) {
			return $groups;
		}

		foreach ($matches as $match) {
			$items = array();
			if (preg_match_all('/"((?:\\\\.|[^"\\\\])*)"/', $match[2], $productMatches)) {
				foreach ($productMatches[1] as $product) {
					$product = trim(stripslashes($product));
					if ($product !== '') {
						$items[] = $product;
					}
				}
			}
			if (!empty($items)) {
				$groups[] = array(
					'category' => stripslashes($match[1]),
					'items' => $items,
				);
			}
		}

		return $groups;
	}
}

if (!function_exists('consultant_approval_parse_list_field')) {
	function consultant_approval_parse_list_field($raw)
	{
		$raw = trim((string) $raw);
		if ($raw === '') {
			return array();
		}

		$decoded = consultant_approval_decode_json_array($raw);
		if (is_array($decoded)) {
			$items = array();
			foreach ($decoded as $item) {
				$item = trim((string) $item);
				if ($item !== '') {
					$items[] = $item;
				}
			}
			if (!empty($items)) {
				return $items;
			}
		}

		if (preg_match('/\(\s*\d+\s*\)/', $raw)) {
			$parts = preg_split('/\(\s*\d+\s*\)\s*/', $raw, -1, PREG_SPLIT_NO_EMPTY);
			$items = array();
			if (is_array($parts)) {
				foreach ($parts as $part) {
					$part = trim($part, " \t\n\r\0\x0B,;");
					if ($part !== '') {
						$items[] = $part;
					}
				}
			}
			if (!empty($items)) {
				return $items;
			}
		}

		$normalized = trim($raw, " \t\n\r\0\x0B[]()");
		if ($normalized === '') {
			return array();
		}

		$parts = preg_split('/\s*,\s*/', $normalized);
		$items = array();
		if (is_array($parts)) {
			foreach ($parts as $part) {
				$part = trim($part, " \t\n\r\0\x0B\"'");
				if ($part !== '') {
					$items[] = $part;
				}
			}
		}

		return !empty($items) ? $items : array($raw);
	}
}

if (!function_exists('consultant_approval_render_project_list_html')) {
	function consultant_approval_render_project_list_html($items)
	{
		$items = array_values(array_filter(array_map('trim', (array) $items), function ($item) {
			return $item !== '';
		}));

		if (empty($items)) {
			return '';
		}

		$html = '<div class="consultant-project-list">';
		$total = count($items);
		$showPlus = $total > 1;

		foreach ($items as $index => $item) {
			$item = preg_replace('/^\(\s*\d+\s*\)\s*/', '', trim((string) $item));
			$isLast = ($index === $total - 1);
			$html .= '<div class="consultant-project-item' . ($isLast ? ' is-last' : '') . '">';
			if ($showPlus) {
				$html .= '<span class="consultant-project-plus" title="Project ' . ($index + 1) . '">+</span>';
				$html .= '<span class="consultant-project-text">(' . ($index + 1) . ') ' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</span>';
			} else {
				$html .= '<span class="consultant-project-text">' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</span>';
			}
			$html .= '</div>';
		}

		$html .= '</div>';
		return $html;
	}
}

if (!function_exists('consultant_approval_format_project_text')) {
	function consultant_approval_format_project_text($raw)
	{
		$items = consultant_approval_parse_list_field($raw);
		$lines = array();
		$total = count($items);
		foreach ($items as $index => $item) {
			$item = preg_replace('/^\(\s*\d+\s*\)\s*/', '', trim((string) $item));
			if ($item === '') {
				continue;
			}
			if ($total > 1) {
				$lines[] = '(' . ($index + 1) . ') ' . $item;
			} else {
				$lines[] = $item;
			}
			if ($index < $total - 1) {
				$lines[] = '--------------------';
			}
		}
		return implode("\n", $lines);
	}
}

if (!function_exists('consultant_approval_render_project_cells')) {
	function consultant_approval_render_project_cells($nameRaw, $locationRaw)
	{
		$names = consultant_approval_parse_list_field($nameRaw);
		$locations = consultant_approval_parse_list_field($locationRaw);
		$count = max(count($names), count($locations));

		if ($count === 0) {
			return array(
				'name_html' => '',
				'location_html' => '',
			);
		}

		for ($i = count($names); $i < $count; $i++) {
			$names[] = '';
		}
		for ($i = count($locations); $i < $count; $i++) {
			$locations[] = '';
		}

		return array(
			'name_html' => consultant_approval_render_project_list_html($names),
			'location_html' => consultant_approval_render_project_list_html($locations),
		);
	}
}

if (!function_exists('consultant_approval_parse_product_groups')) {
	function consultant_approval_parse_product_groups($raw)
	{
		$raw = trim((string) $raw);
		if ($raw === '') {
			return array();
		}

		$decoded = consultant_approval_decode_json_array($raw);
		if (is_array($decoded)) {
			$isCategoryMap = false;
			foreach ($decoded as $key => $value) {
				if (is_string($key) && is_array($value)) {
					$isCategoryMap = true;
					break;
				}
			}

			if ($isCategoryMap) {
				$groups = array();
				foreach ($decoded as $category => $products) {
					$items = array();
					if (is_array($products)) {
						foreach ($products as $product) {
							$product = trim((string) $product);
							if ($product !== '') {
								$items[] = $product;
							}
						}
					} else {
						$product = trim((string) $products);
						if ($product !== '') {
							$items[] = $product;
						}
					}
					if (!empty($items)) {
						$groups[] = array(
							'category' => trim((string) $category),
							'items' => $items,
						);
					}
				}
				return $groups;
			}

			$items = array();
			foreach ($decoded as $product) {
				$product = trim((string) $product);
				if ($product !== '') {
					$items[] = $product;
				}
			}
			if (!empty($items)) {
				return array(array('category' => '', 'items' => $items));
			}
		}

		$regexGroups = consultant_approval_regex_parse_product_groups($raw);
		if (!empty($regexGroups)) {
			return $regexGroups;
		}

		$items = consultant_approval_parse_list_field($raw);
		if (!empty($items)) {
			return array(array('category' => '', 'items' => $items));
		}

		return array();
	}
}

if (!function_exists('consultant_approval_render_product_list_html')) {
	function consultant_approval_render_product_list_html($raw)
	{
		$groups = consultant_approval_parse_product_groups($raw);
		if (empty($groups)) {
			return '';
		}

		$html = '<div class="consultant-product-list">';
		$totalGroups = count($groups);
		foreach ($groups as $gIndex => $group) {
			$isLastGroup = ($gIndex === $totalGroups - 1);
			$html .= '<div class="consultant-product-group' . ($isLastGroup ? ' is-last' : '') . '">';
			if ($group['category'] !== '') {
				$html .= '<div class="consultant-product-category">' . htmlspecialchars($group['category'], ENT_QUOTES, 'UTF-8') . '</div>';
			}
			foreach ($group['items'] as $item) {
				$html .= '<div class="consultant-product-item">';
				$html .= '<span class="consultant-product-tick" title="Selected">&#10003;</span>';
				$html .= '<span class="consultant-product-text">' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</span>';
				$html .= '</div>';
			}
			$html .= '</div>';
		}
		$html .= '</div>';
		return $html;
	}
}

if (!function_exists('consultant_approval_format_product_text')) {
	function consultant_approval_format_product_text($raw)
	{
		$groups = consultant_approval_parse_product_groups($raw);
		if (empty($groups)) {
			return '';
		}

		$blocks = array();
		foreach ($groups as $group) {
			$lines = array();
			if ($group['category'] !== '') {
				$lines[] = $group['category'];
			}
			foreach ($group['items'] as $item) {
				$lines[] = '✓ ' . $item;
			}
			$blocks[] = implode("\n", $lines);
		}
		return implode("\n--------------------\n", $blocks);
	}
}

if (!function_exists('consultant_approval_process_styles')) {
	function consultant_approval_process_styles()
	{
		return '<style type="text/css">
.consultant-project-list {
	margin: 0;
	padding: 0;
}
.consultant-project-item {
	display: flex;
	align-items: flex-start;
	gap: 8px;
	margin: 0;
	padding: 8px 0 10px 0;
	line-height: 1.45;
	border-bottom: 1px solid #607d8b;
}
.consultant-project-item.is-last {
	border-bottom: 0;
	padding-bottom: 0;
}
.consultant-project-plus {
	display: inline-block;
	min-width: 16px;
	height: 16px;
	line-height: 14px;
	text-align: center;
	border: 1px solid #3598dc;
	color: #3598dc;
	border-radius: 2px;
	font-size: 12px;
	font-weight: bold;
	flex: 0 0 16px;
	margin-top: 1px;
}
.consultant-project-text {
	display: block;
	word-break: break-word;
}
.consultant-report-table td.consultant-project-cell,
.consultant-report-table td.consultant-product-cell {
	vertical-align: top;
	min-width: 140px;
	padding-top: 8px !important;
	padding-bottom: 8px !important;
}
.consultant-product-list {
	margin: 0;
	padding: 0;
}
.consultant-product-group {
	margin: 0;
	padding: 6px 0 10px 0;
	border-bottom: 1px solid #607d8b;
}
.consultant-product-group.is-last {
	border-bottom: 0;
	padding-bottom: 0;
}
.consultant-product-category {
	font-weight: bold;
	font-size: 11px;
	margin: 0 0 6px 0;
	padding: 0 0 4px 0;
	color: #333;
	border-bottom: 1px solid #90a4ae;
}
.consultant-product-item {
	display: flex;
	align-items: flex-start;
	gap: 6px;
	margin: 0 0 5px 0;
	line-height: 1.45;
}
.consultant-product-item:last-child {
	margin-bottom: 0;
}
.consultant-product-tick {
	display: inline-block;
	min-width: 14px;
	color: #27ae60;
	font-size: 12px;
	font-weight: bold;
	flex: 0 0 14px;
	margin-top: 1px;
}
.consultant-product-text {
	display: block;
	word-break: break-word;
}
@media print {
	.consultant-project-plus,
	.consultant-product-tick,
	.consultant-project-item,
	.consultant-product-group,
	.consultant-product-category {
		-webkit-print-color-adjust: exact;
		print-color-adjust: exact;
	}
	.consultant-project-item,
	.consultant-product-group,
	.consultant-product-category {
		border-bottom-color: #555 !important;
	}
}
</style>';
	}
}

if (!function_exists('consultant_approval_ensure_reminder_column')) {
	function consultant_approval_ensure_reminder_column($db, $column, $alterSql)
	{
		$conn = isset($db->myconn) ? $db->myconn : null;
		if (!$conn) {
			@$db->query($alterSql);
			return;
		}
		$res = @mysqli_query($conn, "SHOW COLUMNS FROM `sales_vs_consultant_approval_process` LIKE '" . mysqli_real_escape_string($conn, $column) . "'");
		$has = ($res && mysqli_num_rows($res) > 0);
		if (!$has) {
			@mysqli_query($conn, $alterSql);
		}
	}
}

if (!function_exists('consultant_approval_ensure_reminder_columns')) {
	/**
	 * Ensure purchase-date reminder columns exist (Purchase Date - 15 days).
	 */
	function consultant_approval_ensure_reminder_columns($db)
	{
		static $done = false;
		if ($done) {
			return;
		}
		$done = true;
		consultant_approval_ensure_reminder_column(
			$db,
			'purchase_reminder_date',
			"ALTER TABLE `sales_vs_consultant_approval_process` ADD COLUMN `purchase_reminder_date` date DEFAULT NULL AFTER `process_four_purchase_date`"
		);
		consultant_approval_ensure_reminder_column(
			$db,
			'purchase_reminder_notified',
			"ALTER TABLE `sales_vs_consultant_approval_process` ADD COLUMN `purchase_reminder_notified` tinyint(1) NOT NULL DEFAULT 0 AFTER `purchase_reminder_date`"
		);
	}
}

if (!function_exists('consultant_approval_calc_purchase_reminder_date')) {
	/**
	 * Reminder = 15 days before Purchase Date.
	 */
	function consultant_approval_calc_purchase_reminder_date($purchase_date)
	{
		$purchase_date = trim((string) $purchase_date);
		if ($purchase_date === '' || $purchase_date === '0000-00-00') {
			return null;
		}
		$ts = strtotime($purchase_date . ' -15 days');
		if ($ts === false) {
			return null;
		}
		return date('Y-m-d', $ts);
	}
}

if (!function_exists('consultant_approval_sync_purchase_reminder')) {
	function consultant_approval_sync_purchase_reminder($db, $id, $purchase_date, $forceResetNotify = false)
	{
		consultant_approval_ensure_reminder_columns($db);
		$id = (int) $id;
		$reminder = consultant_approval_calc_purchase_reminder_date($purchase_date);
		if ($id <= 0 || $reminder === null) {
			return $reminder;
		}
		$upd = array(
			'purchase_reminder_date' => $reminder,
		);
		if ($forceResetNotify) {
			$upd['purchase_reminder_notified'] = 0;
		} else {
			$old = $db->rp_getValue('sales_vs_consultant_approval_process', 'purchase_reminder_date', "id='" . $id . "'", 0);
			if ($old != $reminder) {
				$upd['purchase_reminder_notified'] = 0;
			}
		}
		$db->rp_update('sales_vs_consultant_approval_process', $upd, "id='" . $id . "'");
		return $reminder;
	}
}

if (!function_exists('consultant_approval_create_purchase_reminder_notification')) {
	function consultant_approval_create_purchase_reminder_notification($db, $row)
	{
		$id = (int) $row['id'];
		if ($id <= 0) {
			return false;
		}
		$customerName = '';
		if (!empty($row['process_one_executive_id'])) {
			$customerName = $db->rp_getValue('executive', 'company_name', "isDelete=0 AND id='" . (int) $row['process_one_executive_id'] . "'", 0);
		}
		if ($customerName == '') {
			$customerName = !empty($row['process_four_contractor_name']) ? $row['process_four_contractor_name'] : 'Customer';
		}
		$salesName = '';
		if (!empty($row['process_one_sales_executive_id'])) {
			$salesName = $db->rp_getValue('sales_executive', 'name', "isDelete=0 AND id='" . (int) $row['process_one_sales_executive_id'] . "'", 0);
		}
		$purchaseDisp = !empty($row['process_four_purchase_date']) ? date('d/M/Y', strtotime($row['process_four_purchase_date'])) : '-';
		$reminderDate = !empty($row['purchase_reminder_date']) ? date('d/M/Y', strtotime($row['purchase_reminder_date'])) : date('d/M/Y');
		$title = 'Consultant Purchase Date Reminder — ' . $customerName;
		$desc = 'Purchase Date: ' . $purchaseDisp . ' | Reminder (15 days before): ' . $reminderDate;
		if ($salesName != '') {
			$desc .= ' | Sales Person: ' . $salesName;
		}
		if (!empty($row['process_two_consultant_name'])) {
			$desc .= ' | Consultant: ' . $row['process_two_consultant_name'];
		}
		$userId = isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) ? (int) $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] : 0;
		$respective = !empty($row['purchase_reminder_date']) ? date('Y-m-d H:i:s', strtotime($row['purchase_reminder_date'])) : date('Y-m-d H:i:s');
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
			'consultant_approval_process',
			$title,
			$desc,
			'consultant_purchase_reminder',
			'consultant_purchase_reminder',
			$respective,
			'admin',
			0,
			1,
			date('Y-m-d H:i:s'),
		);
		$db->rp_insert('notification', $values, $rows, 0);
		$db->rp_update('sales_vs_consultant_approval_process', array(
			'purchase_reminder_notified' => 1,
		), "id='" . $id . "'");
		return true;
	}
}

if (!function_exists('consultant_approval_fire_purchase_reminders')) {
	function consultant_approval_fire_purchase_reminders($db)
	{
		consultant_approval_ensure_reminder_columns($db);
		$today = date('Y-m-d');
		$fired = array();
		$table = 'sales_vs_consultant_approval_process';

		$pending = $db->rp_getData(
			$table,
			'id, process_four_purchase_date, purchase_reminder_date',
			"isDelete=0 AND purchase_reminder_notified=0 AND process_four_purchase_date IS NOT NULL AND process_four_purchase_date!='0000-00-00'",
			'',
			0
		);
		if ($pending) {
			while ($m = mysqli_fetch_assoc($pending)) {
				consultant_approval_sync_purchase_reminder($db, (int) $m['id'], $m['process_four_purchase_date'], false);
			}
		}

		$missing = $db->rp_getData(
			$table,
			'id, process_four_purchase_date',
			"isDelete=0 AND process_four_purchase_date IS NOT NULL AND process_four_purchase_date!='0000-00-00' AND (purchase_reminder_date IS NULL OR purchase_reminder_date='0000-00-00' OR purchase_reminder_date='')",
			'',
			0
		);
		if ($missing) {
			while ($m = mysqli_fetch_assoc($missing)) {
				consultant_approval_sync_purchase_reminder($db, (int) $m['id'], $m['process_four_purchase_date'], true);
			}
		}

		$r = $db->rp_getData(
			$table,
			'*',
			"isDelete=0 AND purchase_reminder_notified=0 AND purchase_reminder_date IS NOT NULL AND purchase_reminder_date!='0000-00-00' AND purchase_reminder_date<='" . $today . "'",
			'purchase_reminder_date ASC, id ASC',
			0
		);
		if ($r) {
			while ($row = mysqli_fetch_assoc($r)) {
				if (consultant_approval_create_purchase_reminder_notification($db, $row)) {
					$customerName = '';
					if (!empty($row['process_one_executive_id'])) {
						$customerName = $db->rp_getValue('executive', 'company_name', "isDelete=0 AND id='" . (int) $row['process_one_executive_id'] . "'", 0);
					}
					$fired[] = array(
						'id' => (int) $row['id'],
						'customer_name' => $customerName ? $customerName : 'Customer',
						'purchase_date' => $row['process_four_purchase_date'],
						'reminder_date' => $row['purchase_reminder_date'],
					);
				}
			}
		}
		return $fired;
	}
}

if (!function_exists('consultant_approval_list_active_purchase_reminders')) {
	/**
	 * Rows currently in Purchase Date reminder window:
	 * reminder_date <= today AND purchase_date >= today (15-day window before purchase).
	 */
	function consultant_approval_list_active_purchase_reminders($db, $limit = 20)
	{
		consultant_approval_ensure_reminder_columns($db);
		$today = date('Y-m-d');
		$limit = (int) $limit;
		if ($limit < 1) {
			$limit = 20;
		}
		$table = 'sales_vs_consultant_approval_process';

		/* Keep reminder_date in sync for upcoming purchases */
		$upcoming = $db->rp_getData(
			$table,
			'id, process_four_purchase_date',
			"isDelete=0 AND process_four_purchase_date IS NOT NULL AND process_four_purchase_date!='0000-00-00' AND process_four_purchase_date>='" . $today . "'",
			'',
			0
		);
		if ($upcoming) {
			while ($m = mysqli_fetch_assoc($upcoming)) {
				consultant_approval_sync_purchase_reminder($db, (int) $m['id'], $m['process_four_purchase_date'], false);
			}
		}

		$list = array();
		$r = $db->rp_getData(
			$table,
			'*',
			"isDelete=0 AND process_four_purchase_date IS NOT NULL AND process_four_purchase_date!='0000-00-00'"
				. " AND purchase_reminder_date IS NOT NULL AND purchase_reminder_date!='0000-00-00'"
				. " AND purchase_reminder_date<='" . $today . "'"
				. " AND process_four_purchase_date>='" . $today . "'",
			'process_four_purchase_date ASC, id ASC',
			0,
			'0,' . $limit
		);
		if ($r) {
			while ($row = mysqli_fetch_assoc($r)) {
				$customerName = '';
				if (!empty($row['process_one_executive_id'])) {
					$customerName = $db->rp_getValue('executive', 'company_name', "isDelete=0 AND id='" . (int) $row['process_one_executive_id'] . "'", 0);
				}
				$salesName = '';
				if (!empty($row['process_one_sales_executive_id'])) {
					$salesName = $db->rp_getValue('sales_executive', 'name', "isDelete=0 AND id='" . (int) $row['process_one_sales_executive_id'] . "'", 0);
				}
				$purchaseRaw = $row['process_four_purchase_date'];
				$daysLeft = (int) floor((strtotime($purchaseRaw) - strtotime($today)) / 86400);
				$list[] = array(
					'id' => (int) $row['id'],
					'customer_name' => $customerName ? $customerName : 'Customer',
					'sales_name' => $salesName ? $salesName : '',
					'consultant_name' => !empty($row['process_two_consultant_name']) ? $row['process_two_consultant_name'] : '',
					'purchase_date' => date('d-m-Y', strtotime($purchaseRaw)),
					'purchase_date_raw' => $purchaseRaw,
					'reminder_date' => !empty($row['purchase_reminder_date']) ? date('d-m-Y', strtotime($row['purchase_reminder_date'])) : '',
					'days_left' => $daysLeft,
				);
			}
		}
		return $list;
	}
}
