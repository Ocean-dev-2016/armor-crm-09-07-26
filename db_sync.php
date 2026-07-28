<?php
/**
 * Armor CRM — Database Sync (idempotent)
 *
 * Safe to run multiple times on local or live.
 * Live URL: https://armor-crm.oceanhub.co.in/db_sync.php?key=armor_cp_sync_2026
 *
 * Delete this file from production after all checks show READY.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('DB_SYNC_KEY', 'armor_cp_sync_2026');
define('DB_SYNC_VERSION', '2026.07.28.1');

if (!isset($_GET['key']) || $_GET['key'] !== DB_SYNC_KEY) {
	header('HTTP/1.1 403 Forbidden');
	die('Unauthorized. Use: db_sync.php?key=armor_cp_sync_2026');
}

require_once __DIR__ . '/include/app.config.loader.php';
require_once __DIR__ . '/include/json_polyfill.php';
$config = armor_get_app_config();

$host = isset($config['db_host']) ? $config['db_host'] : 'localhost';
$user = isset($config['db_user']) ? $config['db_user'] : '';
$pass = isset($config['db_pass']) ? $config['db_pass'] : '';
$name = isset($config['db_name']) ? $config['db_name'] : '';
$ports = isset($config['db_ports']) ? $config['db_ports'] : array(3306);

$conn = null;
$lastError = '';
foreach ($ports as $port) {
	$conn = @mysqli_connect($host, $user, $pass, $name, (int) $port);
	if ($conn) {
		break;
	}
	$lastError = mysqli_connect_error();
}

if (!$conn) {
	die('DB connection failed: ' . $lastError);
}

mysqli_set_charset($conn, 'utf8');

$results = array();
$stats = array('OK' => 0, 'SKIP' => 0, 'FAIL' => 0, 'INFO' => 0, 'CHECK' => 0);

function db_sync_log($status, $message, $error = '')
{
	global $results, $stats;
	$row = array('status' => $status, 'query' => $message);
	if ($error !== '') {
		$row['error'] = $error;
	}
	$results[] = $row;
	if (isset($stats[$status])) {
		$stats[$status]++;
	}
}

function db_sync_table_exists($conn, $table)
{
	$table = mysqli_real_escape_string($conn, $table);
	$res = mysqli_query($conn, "SHOW TABLES LIKE '{$table}'");
	return ($res && mysqli_num_rows($res) > 0);
}

function db_sync_column_exists($conn, $table, $column)
{
	if (!db_sync_table_exists($conn, $table)) {
		return false;
	}
	$table = mysqli_real_escape_string($conn, $table);
	$column = mysqli_real_escape_string($conn, $column);
	$res = mysqli_query($conn, "SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
	return ($res && mysqli_num_rows($res) > 0);
}

function db_sync_index_exists($conn, $table, $indexName)
{
	if (!db_sync_table_exists($conn, $table)) {
		return false;
	}
	$table = mysqli_real_escape_string($conn, $table);
	$indexName = mysqli_real_escape_string($conn, $indexName);
	$res = mysqli_query($conn, "SHOW INDEX FROM `{$table}` WHERE Key_name = '{$indexName}'");
	return ($res && mysqli_num_rows($res) > 0);
}

function db_sync_run_query($conn, $sql, $okMessage)
{
	if (mysqli_query($conn, $sql)) {
		db_sync_log('OK', $okMessage);
		return true;
	}
	db_sync_log('FAIL', $okMessage, mysqli_error($conn));
	return false;
}

function db_sync_add_column_if_missing($conn, $table, $column, $definition, $afterColumns = array())
{
	if (db_sync_column_exists($conn, $table, $column)) {
		db_sync_log('SKIP', $table . '.' . $column . ' already exists');
		return true;
	}

	$after = '';
	if (is_array($afterColumns)) {
		foreach ($afterColumns as $afterColumn) {
			if (db_sync_column_exists($conn, $table, $afterColumn)) {
				$after = " AFTER `{$afterColumn}`";
				break;
			}
		}
	}

	$sql = "ALTER TABLE `{$table}` ADD `{$column}` {$definition}{$after}";
	return db_sync_run_query($conn, $sql, 'Add column ' . $table . '.' . $column);
}

function db_sync_add_index_if_missing($conn, $table, $indexName, $columnsSql)
{
	if (db_sync_index_exists($conn, $table, $indexName)) {
		db_sync_log('SKIP', 'Index ' . $table . '.' . $indexName . ' already exists');
		return true;
	}
	$sql = "ALTER TABLE `{$table}` ADD INDEX `{$indexName}` ({$columnsSql})";
	return db_sync_run_query($conn, $sql, 'Add index ' . $table . '.' . $indexName);
}

function db_sync_append_page_urls($conn, $pageId, $newUrls)
{
	if (!db_sync_table_exists($conn, 'page_table')) {
		db_sync_log('SKIP', 'page_table not found — skip page URL update');
		return false;
	}

	$pageId = (int) $pageId;
	$res = mysqli_query($conn, "SELECT `page_urls` FROM `page_table` WHERE `id`={$pageId} AND `isDelete`=0 LIMIT 1");
	if (!$res || mysqli_num_rows($res) == 0) {
		db_sync_log('SKIP', 'page_table id=' . $pageId . ' not found — skip page URL update');
		return false;
	}

	$row = mysqli_fetch_assoc($res);
	$urls = array();
	if (!empty($row['page_urls'])) {
		foreach (explode(',', $row['page_urls']) as $url) {
			$url = trim($url);
			if ($url !== '') {
				$urls[] = $url;
			}
		}
	}

	$added = false;
	foreach ($newUrls as $url) {
		$url = trim($url);
		if ($url !== '' && !in_array($url, $urls, true)) {
			$urls[] = $url;
			$added = true;
		}
	}

	if (!$added) {
		db_sync_log('SKIP', 'page_table id=' . $pageId . ' already has Channel Partner Customer URLs');
		return true;
	}

	$newUrlsStr = mysqli_real_escape_string($conn, implode(',', $urls));
	$sql = "UPDATE `page_table` SET `page_urls`='{$newUrlsStr}' WHERE `id`={$pageId}";
	return db_sync_run_query($conn, $sql, 'Update page_table id=' . $pageId . ' with Channel Partner Customer URLs');
}

db_sync_log('INFO', '--- Armor CRM DB Sync v' . DB_SYNC_VERSION . ' ---');
db_sync_log('INFO', 'Changes: Channel Partner, Expense, Visit APIs, Employee Chat module');

function db_sync_register_api_if_missing($conn, $id, $slug, $title, $url)
{
	if (!db_sync_table_exists($conn, 'api_table')) {
		db_sync_log('SKIP', 'api_table not found — skip API id=' . $id);
		return false;
	}
	$id = (int) $id;
	$res = mysqli_query($conn, "SELECT id FROM `api_table` WHERE `id`={$id} AND `isDelete`=0 LIMIT 1");
	if ($res && mysqli_num_rows($res) > 0) {
		db_sync_log('SKIP', 'api_table id=' . $id . ' (' . $slug . ') already exists');
		return true;
	}
	$slugEsc = mysqli_real_escape_string($conn, $slug);
	$titleEsc = mysqli_real_escape_string($conn, $title);
	$urlEsc = mysqli_real_escape_string($conn, $url);
	$descEsc = mysqli_real_escape_string($conn, '<p>' . $title . '</p>');
	$now = date('Y-m-d H:i:s');
	$sql = "INSERT INTO `api_table` (`id`,`api_slug`,`api_title`,`api_description`,`api_url`,`author`,`last_modification_date`,`isDelete`,`created_by`,`created_by_type`,`created_date`)
		VALUES ({$id},'{$slugEsc}','{$titleEsc}','{$descEsc}','{$urlEsc}','Armor CRM','{$now}',0,1,0,'{$now}')";
	return db_sync_run_query($conn, $sql, 'Register API id=' . $id . ' ' . $slug);
}


/* ------------------------------------------------------------------
 * STEP 1 — Create main table if not exists
 * ------------------------------------------------------------------ */
$createTableSql = "CREATE TABLE IF NOT EXISTS `channel_partner_customer` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`channel_partner_id` int(11) NOT NULL DEFAULT 0 COMMENT 'FK executive.id',
	`company_name` varchar(255) NOT NULL COMMENT 'Customer Name',
	`person_name` varchar(255) NOT NULL,
	`mobile_no` varchar(20) NOT NULL,
	`email` varchar(255) DEFAULT NULL,
	`gst` varchar(50) DEFAULT NULL,
	`country` varchar(100) DEFAULT NULL,
	`state` varchar(100) DEFAULT NULL,
	`city` varchar(100) DEFAULT NULL,
	`pincode` varchar(20) DEFAULT NULL,
	`created_by` int(11) NOT NULL DEFAULT 0,
	`created_by_type` varchar(20) NOT NULL DEFAULT '',
	`modified_by` int(11) NOT NULL DEFAULT 0,
	`modified_by_type` varchar(20) NOT NULL DEFAULT '',
	`created_date` datetime DEFAULT NULL,
	`modified_date` datetime DEFAULT NULL,
	`created_at` datetime DEFAULT NULL,
	`updated_at` datetime DEFAULT NULL,
	`isActive` tinyint(1) NOT NULL DEFAULT 1,
	`isDelete` tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_cp_customer_mobile` (`mobile_no`),
	KEY `idx_cp_customer_delete` (`isDelete`),
	KEY `idx_cp_customer_partner` (`channel_partner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

db_sync_run_query($conn, $createTableSql, 'Create table channel_partner_customer (if not exists)');

/* ------------------------------------------------------------------
 * STEP 2 — executive.channel_partner_flag
 * ------------------------------------------------------------------ */
if (!db_sync_column_exists($conn, 'executive', 'channel_partner_flag')) {
	$afterExecutive = db_sync_column_exists($conn, 'executive', 'turnover_year') ? ' AFTER `turnover_year`' : '';
	$sql = "ALTER TABLE `executive` ADD `channel_partner_flag` TINYINT NOT NULL DEFAULT 0{$afterExecutive}";
	db_sync_run_query($conn, $sql, 'Add executive.channel_partner_flag');
} else {
	db_sync_log('SKIP', 'executive.channel_partner_flag already exists');
}

/* ------------------------------------------------------------------
 * STEP 3 — Ensure all channel_partner_customer columns (old DB upgrade)
 * ------------------------------------------------------------------ */
if (db_sync_table_exists($conn, 'channel_partner_customer')) {
	$cpColumns = array(
		'channel_partner_id' => array("int(11) NOT NULL DEFAULT 0 COMMENT 'FK executive.id'", array('id')),
		'company_name' => array("varchar(255) NOT NULL COMMENT 'Customer Name'", array('channel_partner_id', 'id')),
		'person_name' => array("varchar(255) NOT NULL", array('company_name')),
		'mobile_no' => array("varchar(20) NOT NULL", array('person_name')),
		'email' => array("varchar(255) DEFAULT NULL", array('mobile_no')),
		'gst' => array("varchar(50) DEFAULT NULL", array('email')),
		'country' => array("varchar(100) DEFAULT NULL", array('gst')),
		'state' => array("varchar(100) DEFAULT NULL", array('country', 'gst')),
		'city' => array("varchar(100) DEFAULT NULL", array('state')),
		'pincode' => array("varchar(20) DEFAULT NULL", array('city')),
		'created_by' => array("int(11) NOT NULL DEFAULT 0", array('pincode', 'city')),
		'created_by_type' => array("varchar(20) NOT NULL DEFAULT ''", array('created_by')),
		'modified_by' => array("int(11) NOT NULL DEFAULT 0", array('created_by_type', 'created_by')),
		'modified_by_type' => array("varchar(20) NOT NULL DEFAULT ''", array('modified_by')),
		'created_date' => array("datetime DEFAULT NULL", array('modified_by_type', 'modified_by', 'pincode')),
		'modified_date' => array("datetime DEFAULT NULL", array('created_date', 'created_at')),
		'created_at' => array("datetime DEFAULT NULL", array('modified_date', 'created_date')),
		'updated_at' => array("datetime DEFAULT NULL", array('created_at', 'modified_date')),
		'isActive' => array("tinyint(1) NOT NULL DEFAULT 1", array('updated_at', 'modified_date', 'created_date')),
		'isDelete' => array("tinyint(1) NOT NULL DEFAULT 0", array('isActive')),
	);

	foreach ($cpColumns as $column => $meta) {
		db_sync_add_column_if_missing($conn, 'channel_partner_customer', $column, $meta[0], $meta[1]);
	}

	db_sync_add_index_if_missing($conn, 'channel_partner_customer', 'idx_cp_customer_mobile', '`mobile_no`');
	db_sync_add_index_if_missing($conn, 'channel_partner_customer', 'idx_cp_customer_delete', '`isDelete`');
	db_sync_add_index_if_missing($conn, 'channel_partner_customer', 'idx_cp_customer_partner', '`channel_partner_id`');
}

/* ------------------------------------------------------------------
 * STEP 4 — Legacy timestamp sync (created_at/updated_at -> created_date/modified_date)
 * ------------------------------------------------------------------ */
if (db_sync_table_exists($conn, 'channel_partner_customer')) {
	if (db_sync_column_exists($conn, 'channel_partner_customer', 'created_at')
		&& db_sync_column_exists($conn, 'channel_partner_customer', 'created_date')) {
		db_sync_run_query(
			$conn,
			"UPDATE `channel_partner_customer` SET `created_date` = `created_at` WHERE (`created_date` IS NULL OR `created_date` = '0000-00-00 00:00:00') AND `created_at` IS NOT NULL AND `created_at` != '0000-00-00 00:00:00'",
			'Sync channel_partner_customer.created_at -> created_date'
		);
	}
	if (db_sync_column_exists($conn, 'channel_partner_customer', 'updated_at')
		&& db_sync_column_exists($conn, 'channel_partner_customer', 'modified_date')) {
		db_sync_run_query(
			$conn,
			"UPDATE `channel_partner_customer` SET `modified_date` = `updated_at` WHERE (`modified_date` IS NULL OR `modified_date` = '0000-00-00 00:00:00') AND `updated_at` IS NOT NULL AND `updated_at` != '0000-00-00 00:00:00'",
			'Sync channel_partner_customer.updated_at -> modified_date'
		);
	}
}

/* ------------------------------------------------------------------
 * STEP 5 — Register Channel Partner Customer pages in page_table (id=555)
 * ------------------------------------------------------------------ */
db_sync_append_page_urls($conn, 555, array(
	'channel_partner_customer_manage.php',
	'channel_partner_customer_crud.php',
	'channel_partner_customer_get_ajax.php',
));

db_sync_append_page_urls($conn, 650, array(
	'channel_partner_stock_manage.php',
	'channel_partner_stock_get_ajax.php',
));

/* ------------------------------------------------------------------
 * STEP 5a — Seed Channel Partner (admin_type=1 / CP) portal rights
 * Pages: 555 customers, 565 orders (personal), 650 stock (view)
 * ------------------------------------------------------------------ */
function db_sync_ensure_cp_page_right($conn, $adminId, $pageId, $flags)
{
	$adminId = (int) $adminId;
	$pageId = (int) $pageId;
	$check = mysqli_query($conn, "SELECT id FROM page_admin_right WHERE admin_id='{$adminId}' AND page_id='{$pageId}' AND isDelete=0 LIMIT 1");
	if ($check && mysqli_num_rows($check) > 0) {
		$row = mysqli_fetch_assoc($check);
		$set = array();
		foreach ($flags as $col => $val) {
			$set[] = "`" . mysqli_real_escape_string($conn, $col) . "`='" . (int) $val . "'";
		}
		$sql = "UPDATE page_admin_right SET " . implode(',', $set) . " WHERE id='" . (int) $row['id'] . "'";
		db_sync_run_query($conn, $sql, "Update CP rights page {$pageId} for admin_type {$adminId}");
		return;
	}
	$cols = array('page_id', 'admin_id', 'isDelete', 'created_by', 'created_by_type', 'created_date');
	$vals = array($pageId, $adminId, 0, 1, 0, "'" . date('Y-m-d H:i:s') . "'");
	foreach ($flags as $col => $val) {
		$cols[] = $col;
		$vals[] = (int) $val;
	}
	$sql = "INSERT INTO page_admin_right (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
	db_sync_run_query($conn, $sql, "Insert CP rights page {$pageId} for admin_type {$adminId}");
}

$cpAdminTypeId = 0;
$cpTypeRes = mysqli_query($conn, "SELECT id FROM admin_type WHERE (id=1 OR name='CP' OR slug='super_stockist') AND isDelete=0 ORDER BY id ASC LIMIT 1");
if ($cpTypeRes && $cpTypeRow = mysqli_fetch_assoc($cpTypeRes)) {
	$cpAdminTypeId = (int) $cpTypeRow['id'];
}
if ($cpAdminTypeId > 0) {
	db_sync_ensure_cp_page_right($conn, $cpAdminTypeId, 555, array(
		'view_flag' => 1,
		'insert_flag' => 1,
		'update_flag' => 1,
		'delete_flag' => 1,
		'personal_flag' => 1,
		'chain_vise_flag' => 0,
		'all_data_flag' => 0,
		'export_excel_flag' => 0,
		'print_flag' => 0,
	));
	db_sync_ensure_cp_page_right($conn, $cpAdminTypeId, 565, array(
		'view_flag' => 1,
		'insert_flag' => 1,
		'update_flag' => 1,
		'delete_flag' => 0,
		'personal_flag' => 1,
		'chain_vise_flag' => 0,
		'all_data_flag' => 0,
		'export_excel_flag' => 0,
		'print_flag' => 1,
	));
	db_sync_ensure_cp_page_right($conn, $cpAdminTypeId, 650, array(
		'view_flag' => 1,
		'insert_flag' => 0,
		'update_flag' => 0,
		'delete_flag' => 0,
		'personal_flag' => 1,
		'chain_vise_flag' => 0,
		'all_data_flag' => 0,
		'export_excel_flag' => 0,
		'print_flag' => 0,
	));
} else {
	db_sync_log('INFO', 'CP admin_type not found; skip portal rights seed');
}

/* Employee Visit KRA reuses Visit Report page 599 permissions. */
db_sync_append_page_urls($conn, 599, array(
	'employee_visit_kra_report.php',
	'employee_visit_kra_report_get_ajax.php',
	'employee_visit_kra_report_excel.php',
));

/* ------------------------------------------------------------------
 * STEP 5b — Register Channel Partner Customer mobile APIs (223-228)
 * ------------------------------------------------------------------ */
$cpApiBase = 'service_genral.php?key=1226';
db_sync_register_api_if_missing($conn, 223, 'get_channel_partner_list', 'Get Channel Partner List', $cpApiBase . '&s=223');
db_sync_register_api_if_missing($conn, 224, 'get_channel_partner_customer_list', 'Get Channel Partner Customer List', $cpApiBase . '&s=224&channel_partner_id=&search_name=&ul=0&ll=50');
db_sync_register_api_if_missing($conn, 225, 'add_channel_partner_customer', 'Add Channel Partner Customer', $cpApiBase . '&s=225&channel_partner_id=&company_name=&person_name=&mobile_no=&email=&gst=&country=India&state=&city=&pincode=');
db_sync_register_api_if_missing($conn, 226, 'update_channel_partner_customer', 'Update Channel Partner Customer', $cpApiBase . '&s=226&id=&channel_partner_id=&company_name=&person_name=&mobile_no=&email=&gst=&country=&state=&city=&pincode=');
db_sync_register_api_if_missing($conn, 227, 'delete_channel_partner_customer', 'Delete Channel Partner Customer', $cpApiBase . '&s=227&id=');
db_sync_register_api_if_missing($conn, 228, 'get_channel_partner_customer_detail', 'Get Channel Partner Customer Detail', $cpApiBase . '&s=228&id=');

$requiredCpApis = array(
	223 => 'get_channel_partner_list',
	224 => 'get_channel_partner_customer_list',
	225 => 'add_channel_partner_customer',
	226 => 'update_channel_partner_customer',
	227 => 'delete_channel_partner_customer',
	228 => 'get_channel_partner_customer_detail',
);

/* ------------------------------------------------------------------
 * STEP 5c — Advance Expense (expense_claim_type + APIs 229-230)
 * ------------------------------------------------------------------ */
db_sync_add_column_if_missing(
	$conn,
	'expence_category',
	'expense_claim_type',
	"tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Regular, 2=Advance'",
	array('name', 'image_path')
);
db_sync_add_column_if_missing(
	$conn,
	'expense',
	'expense_claim_type',
	"tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Regular, 2=Advance'",
	array('expense_type', 'category_id', 'subcategory_id')
);
db_sync_add_column_if_missing(
	$conn,
	'expense',
	'advance_expense_type',
	"tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=None, 1=Brand Approval, 2=Travelling'",
	array('expense_claim_type', 'expense_type')
);

$visitApiBase = 'service_visit.php?key=1226';
db_sync_register_api_if_missing($conn, 229, 'get_expense_claim_type', 'Get Expense Claim Type', $visitApiBase . '&s=229');
$seApiBase = 'service_sales_executive.php?key=1226';
db_sync_register_api_if_missing($conn, 230, 'add_advance_expense', 'Add Advance Expense', $seApiBase . '&s=230&sales_executive_id=&category_id=&total=&remark=');

$requiredAdvanceApis = array(
	229 => 'get_expense_claim_type',
	230 => 'add_advance_expense',
);

/* Seed Advance expense categories (claim_type=2) if missing */
if (db_sync_table_exists($conn, 'expence_category') && db_sync_column_exists($conn, 'expence_category', 'expense_claim_type')) {
	$advanceCategoryNames = array(
		'Advance Brand Approval Expense',
		'Advance Travelling Expense',
	);
	foreach ($advanceCategoryNames as $advName) {
		$advEsc = mysqli_real_escape_string($conn, $advName);
		$advCheck = mysqli_query($conn, "SELECT id, expense_claim_type FROM `expence_category` WHERE name='{$advEsc}' AND isDelete=0 LIMIT 1");
		if ($advCheck && mysqli_num_rows($advCheck) > 0) {
			$advRow = mysqli_fetch_assoc($advCheck);
			if ((int)$advRow['expense_claim_type'] !== 2) {
				db_sync_run_query(
					$conn,
					"UPDATE `expence_category` SET `expense_claim_type`=2, `isActive`=1 WHERE `id`='" . (int)$advRow['id'] . "'",
					"SET claim_type=2 for category: {$advName}"
				);
			} else {
				db_sync_log('SKIP', "Advance category already ready: {$advName}");
			}
		} else {
			db_sync_run_query(
				$conn,
				"INSERT INTO `expence_category` (`name`, `image_path`, `expense_claim_type`, `isActive`, `isDelete`) VALUES ('{$advEsc}', '', 2, 1, 0)",
				"INSERT Advance category: {$advName}"
			);
		}
	}

	/* Soft-delete duplicate misspelled Advance categories (keep clean 2 names) */
	$keepAdvance = array(
		'Advance Brand Approval Expense',
		'Advance Travelling Expense',
	);
	$dupRes = mysqli_query($conn, "SELECT id, name FROM `expence_category` WHERE isDelete=0 AND (expense_claim_type=2 OR name LIKE 'Advance%')");
	if ($dupRes) {
		while ($dupRow = mysqli_fetch_assoc($dupRes)) {
			$norm = strtolower(preg_replace('/\s+/', ' ', trim($dupRow['name'])));
			$norm = str_replace('expence', 'expense', $norm);
			$keep = false;
			foreach ($keepAdvance as $keepName) {
				if ($norm === strtolower($keepName)) {
					// Keep exact preferred spelling only once; soft-delete other spelling variants
					if ($dupRow['name'] === $keepName) {
						$keep = true;
					}
					break;
				}
			}
			if (!$keep && (strpos($norm, 'advance brand') !== false || strpos($norm, 'advance travell') !== false)) {
				db_sync_run_query(
					$conn,
					"UPDATE `expence_category` SET `isDelete`=1, `isActive`=0 WHERE `id`='" . (int)$dupRow['id'] . "'",
					"Soft-delete duplicate Advance category: " . $dupRow['name']
				);
			}
		}
	}

	/* Seed Regular OCE category if missing */
	$oceEsc = mysqli_real_escape_string($conn, 'OCE');
	$oceCheck = mysqli_query($conn, "SELECT id, expense_claim_type FROM `expence_category` WHERE name='{$oceEsc}' AND isDelete=0 LIMIT 1");
	if ($oceCheck && mysqli_num_rows($oceCheck) > 0) {
		$oceRow = mysqli_fetch_assoc($oceCheck);
		if ((int)$oceRow['expense_claim_type'] !== 1) {
			db_sync_run_query(
				$conn,
				"UPDATE `expence_category` SET `expense_claim_type`=1, `isActive`=1 WHERE `id`='" . (int)$oceRow['id'] . "'",
				"SET claim_type=1 for category: OCE"
			);
		} else {
			db_sync_log('SKIP', 'Regular category already ready: OCE');
		}
	} else {
		db_sync_run_query(
			$conn,
			"INSERT INTO `expence_category` (`name`, `image_path`, `expense_claim_type`, `isActive`, `isDelete`) VALUES ('{$oceEsc}', '', 1, 1, 0)",
			"INSERT Regular category: OCE"
		);
	}
}

/* ------------------------------------------------------------------
 * STEP 5d — Visit End Remark / Reason (visit columns + APIs 231-232)
 * ------------------------------------------------------------------ */
db_sync_add_column_if_missing(
	$conn,
	'visit',
	'remark_code',
	"varchar(10) NOT NULL DEFAULT '' COMMENT 'Visit end remark code A-G'",
	array('stop_remark')
);
db_sync_add_column_if_missing(
	$conn,
	'visit',
	'reason_code',
	"varchar(10) NOT NULL DEFAULT '' COMMENT 'Visit end reason code e.g. A1,B1,C1'",
	array('remark_code', 'stop_remark')
);
db_sync_add_column_if_missing(
	$conn,
	'visit',
	'approval_type',
	"varchar(10) NOT NULL DEFAULT '' COMMENT '1=Private Consultant, 2=Government Consultant'",
	array('reason_code', 'remark_code', 'stop_remark')
);
db_sync_add_column_if_missing(
	$conn,
	'visit',
	'visit_followup_id',
	"int(11) NOT NULL DEFAULT 0 COMMENT 'Auto-created followup.id for Visit Stop Short Note'",
	array('approval_type', 'reason_code', 'remark_code')
);
/* App stopVisit: note + date (followup_date) after stop_remark */
db_sync_add_column_if_missing(
	$conn,
	'visit',
	'note',
	"text COMMENT 'Extra note from App stopVisit (after stop_remark)'",
	array('stop_remark', 'remark')
);
db_sync_add_column_if_missing(
	$conn,
	'visit',
	'followup_date',
	"datetime DEFAULT NULL COMMENT 'App stopVisit date param → followup_date (format Y-m-d H:i)'",
	array('note', 'stop_remark')
);

db_sync_register_api_if_missing($conn, 231, 'get_visit_remark_reason', 'Get Visit Remark Reason', $visitApiBase . '&s=231');
db_sync_register_api_if_missing($conn, 232, 'get_visit_approval_type', 'Get Visit Approval Type', $visitApiBase . '&s=232');
db_sync_register_api_if_missing($conn, 233, 'save_visit_consultant_form', 'Save Visit Consultant Detail Form', $visitApiBase . '&s=233');
db_sync_register_api_if_missing($conn, 234, 'save_visit_high_rate_form', 'Save Visit High Rate Analysis Form', $visitApiBase . '&s=234');
db_sync_register_api_if_missing($conn, 235, 'get_visit_high_rate_products', 'Get Visit High Rate Product List', $visitApiBase . '&s=235');

$requiredVisitRemarkApis = array(
	231 => 'get_visit_remark_reason',
	232 => 'get_visit_approval_type',
	233 => 'save_visit_consultant_form',
	234 => 'save_visit_high_rate_form',
	235 => 'get_visit_high_rate_products',
);

/* ------------------------------------------------------------------
 * STEP 5e — Visit End C1/C2 Consultant Form + E1 High Rate Form tables
 * ------------------------------------------------------------------ */
$createConsultantFormSql = "CREATE TABLE IF NOT EXISTS `visit_consultant_form` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`visit_id` int(11) NOT NULL DEFAULT 0,
	`user_id` int(11) NOT NULL DEFAULT 0,
	`customer_id` int(11) NOT NULL DEFAULT 0,
	`inquiry_id` int(11) NOT NULL DEFAULT 0,
	`reason_code` varchar(10) NOT NULL DEFAULT '' COMMENT 'C1 or C2',
	`approval_type` varchar(10) NOT NULL DEFAULT '' COMMENT '1=Private, 2=Government',
	`consultant_type` varchar(20) NOT NULL DEFAULT '' COMMENT 'private / government',
	`form_title` varchar(100) NOT NULL DEFAULT '',
	`firm_name` varchar(255) NOT NULL DEFAULT '',
	`address` text,
	`city` varchar(100) NOT NULL DEFAULT '',
	`state` varchar(100) NOT NULL DEFAULT '',
	`pincode` varchar(20) NOT NULL DEFAULT '',
	`contact_person` varchar(255) NOT NULL DEFAULT '',
	`mobile` varchar(30) NOT NULL DEFAULT '',
	`email` varchar(255) NOT NULL DEFAULT '',
	`followup_id` int(11) NOT NULL DEFAULT 0,
	`created_date` datetime DEFAULT NULL,
	`isActive` tinyint(1) NOT NULL DEFAULT 1,
	`isDelete` tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_vcf_visit` (`visit_id`),
	KEY `idx_vcf_user` (`user_id`),
	KEY `idx_vcf_delete` (`isDelete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
db_sync_run_query($conn, $createConsultantFormSql, 'Create table visit_consultant_form (if not exists)');

$createHighRateFormSql = "CREATE TABLE IF NOT EXISTS `visit_high_rate_form` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`visit_id` int(11) NOT NULL DEFAULT 0,
	`user_id` int(11) NOT NULL DEFAULT 0,
	`customer_id` int(11) NOT NULL DEFAULT 0,
	`inquiry_id` int(11) NOT NULL DEFAULT 0,
	`reason_code` varchar(10) NOT NULL DEFAULT 'E1',
	`customer_name` varchar(255) NOT NULL DEFAULT '',
	`payment_option` varchar(50) NOT NULL DEFAULT '' COMMENT 'Advance / 30 Days',
	`payment_remark` varchar(255) NOT NULL DEFAULT '',
	`followup_id` int(11) NOT NULL DEFAULT 0,
	`created_date` datetime DEFAULT NULL,
	`isActive` tinyint(1) NOT NULL DEFAULT 1,
	`isDelete` tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_vhrf_visit` (`visit_id`),
	KEY `idx_vhrf_user` (`user_id`),
	KEY `idx_vhrf_delete` (`isDelete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
db_sync_run_query($conn, $createHighRateFormSql, 'Create table visit_high_rate_form (if not exists)');

$createHighRateItemSql = "CREATE TABLE IF NOT EXISTS `visit_high_rate_form_item` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`high_rate_form_id` int(11) NOT NULL DEFAULT 0,
	`visit_id` int(11) NOT NULL DEFAULT 0,
	`product_name` varchar(255) NOT NULL DEFAULT '',
	`given_rate` varchar(50) NOT NULL DEFAULT '',
	`qty` varchar(50) NOT NULL DEFAULT '',
	`customer_rate` varchar(50) NOT NULL DEFAULT '',
	`remark` text,
	`sort_order` int(11) NOT NULL DEFAULT 0,
	`isDelete` tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_vhrfi_form` (`high_rate_form_id`),
	KEY `idx_vhrfi_visit` (`visit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
db_sync_run_query($conn, $createHighRateItemSql, 'Create table visit_high_rate_form_item (if not exists)');

db_sync_add_column_if_missing(
	$conn,
	'visit_high_rate_form',
	'payment_option',
	"varchar(50) NOT NULL DEFAULT '' COMMENT 'Advance / 30 Days'",
	array('customer_name', 'reason_code')
);
db_sync_add_column_if_missing(
	$conn,
	'visit_high_rate_form',
	'payment_remark',
	"varchar(255) NOT NULL DEFAULT ''",
	array('payment_option', 'customer_name')
);
db_sync_add_column_if_missing(
	$conn,
	'visit_high_rate_form_item',
	'remark',
	"text",
	array('customer_rate', 'qty', 'given_rate')
);
db_sync_add_column_if_missing(
	$conn,
	'visit_high_rate_form_item',
	'product_slug',
	"varchar(80) NOT NULL DEFAULT '' COMMENT 'High Rate fixed product slug'",
	array('product_name', 'visit_id')
);

/* App FCM/device notification token — Android login sends as "token", stored as device_id */
db_sync_add_column_if_missing(
	$conn,
	'sales_executive_login',
	'device_id',
	"text COMMENT 'App notification token (Android Query token)'",
	array('refreshToken', 'imei')
);
db_sync_add_column_if_missing(
	$conn,
	'sales_executive',
	'device_id',
	"text COMMENT 'Latest app notification token (Android Query token)'",
	array('refreshToken', 'imei')
);

db_sync_add_column_if_missing(
	$conn,
	'visit',
	'consultant_form_id',
	"int(11) NOT NULL DEFAULT 0 COMMENT 'FK visit_consultant_form.id for C1/C2'",
	array('visit_followup_id', 'approval_type')
);
db_sync_add_column_if_missing(
	$conn,
	'visit',
	'high_rate_form_id',
	"int(11) NOT NULL DEFAULT 0 COMMENT 'FK visit_high_rate_form.id for E1'",
	array('consultant_form_id', 'visit_followup_id')
);

/* ------------------------------------------------------------------
 * STEP 5f — Channel Partner Order flag on orders
 * ------------------------------------------------------------------ */
db_sync_add_column_if_missing(
	$conn,
	'orders',
	'channel_partner_order_flag',
	"tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Channel Partner Order, 0=Normal Order'",
	array('customer_type', 'customer_id', 'customer_flag')
);
db_sync_add_column_if_missing(
	$conn,
	'orders',
	'channel_partner_customer_id',
	"int(11) NOT NULL DEFAULT 0 COMMENT 'FK channel_partner_customer.id (end customer for CP order)'",
	array('channel_partner_order_flag', 'customer_id')
);
db_sync_add_column_if_missing(
	$conn,
	'orders',
	'cp_portal_order_flag',
	"tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=CP portal simple order pending Convert to Order'",
	array('channel_partner_customer_id', 'channel_partner_order_flag')
);
db_sync_add_column_if_missing(
	$conn,
	'orders',
	'cp_order_mode',
	"varchar(20) NOT NULL DEFAULT '' COMMENT 'own|customer — CP portal order mode'",
	array('cp_portal_order_flag', 'channel_partner_customer_id')
);

/* ------------------------------------------------------------------
 * STEP 5g — Payment Received flag on orders (Pending Payment 45 days)
 * ------------------------------------------------------------------ */
db_sync_add_column_if_missing(
	$conn,
	'orders',
	'payment_received_flag',
	"tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Payment Received marked from Order History'",
	array('channel_partner_customer_id', 'channel_partner_order_flag', 'status')
);
db_sync_add_column_if_missing(
	$conn,
	'orders',
	'payment_received_date',
	"datetime DEFAULT NULL COMMENT 'When Payment Received was marked'",
	array('payment_received_flag')
);
db_sync_add_column_if_missing(
	$conn,
	'orders',
	'payment_received_by',
	"int(11) NOT NULL DEFAULT 0 COMMENT 'dealer_distributor_network.id who marked payment received'",
	array('payment_received_date', 'payment_received_flag')
);
db_sync_add_column_if_missing(
	$conn,
	'orders',
	'payment_received_amount',
	"decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount received against order'",
	array('payment_received_by', 'payment_received_flag')
);
db_sync_add_column_if_missing(
	$conn,
	'orders',
	'payment_received_type',
	"tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Cash 2=Cheque 3=Online 4=Other'",
	array('payment_received_amount', 'payment_received_flag')
);
db_sync_append_page_urls($conn, 565, array(
	'order_payment_received_ajax.php',
	'dealer_orders_manage.php',
	'ajax_cp_credit_stock.php',
	'ajax_cp_dispatch_order.php',
	'channel_partner_sales_report.php',
	'channel_partner_ledger.php',
	'channel_partner_payment.php',
));

/* ------------------------------------------------------------------
 * STEP 5h — CP stock ledger flags + print header/footer
 * ------------------------------------------------------------------ */
db_sync_add_column_if_missing(
	$conn,
	'orders',
	'cp_stock_credited',
	"tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Armor order qty credited to CP customer_inward_stock'",
	array('cp_order_mode', 'channel_partner_order_flag')
);
db_sync_add_column_if_missing(
	$conn,
	'orders',
	'cp_stock_debited',
	"tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=CP customer order qty debited from CP stock'",
	array('cp_stock_credited', 'cp_order_mode')
);
db_sync_add_column_if_missing(
	$conn,
	'customer_inward_stock',
	'txn_type',
	"varchar(10) NOT NULL DEFAULT '' COMMENT 'in|out for CP stock movements'",
	array('sales_id', 'customer_id', 'pro_qty')
);
db_sync_add_column_if_missing(
	$conn,
	'customer_inward_stock',
	'ref_order_id',
	"int(11) NOT NULL DEFAULT 0 COMMENT 'Related orders.id for CP stock in/out'",
	array('txn_type', 'sales_id')
);
db_sync_add_column_if_missing(
	$conn,
	'executive',
	'cp_print_header',
	"text COMMENT 'Channel Partner SO/PI header HTML/text'",
	array('channel_partner_flag', 'company_name')
);
db_sync_add_column_if_missing(
	$conn,
	'executive',
	'cp_print_footer',
	"text COMMENT 'Channel Partner SO/PI footer HTML/text'",
	array('cp_print_header', 'channel_partner_flag')
);
db_sync_add_column_if_missing(
	$conn,
	'executive',
	'cp_print_company_name',
	"varchar(255) NOT NULL DEFAULT '' COMMENT 'CP PI display company name'",
	array('cp_print_footer', 'cp_print_header')
);
db_sync_add_column_if_missing(
	$conn,
	'executive',
	'cp_print_gst',
	"varchar(30) NOT NULL DEFAULT '' COMMENT 'CP PI GSTIN'",
	array('cp_print_company_name', 'cp_print_footer')
);
db_sync_add_column_if_missing(
	$conn,
	'executive',
	'cp_print_pan',
	"varchar(30) NOT NULL DEFAULT '' COMMENT 'CP PI PAN'",
	array('cp_print_gst', 'cp_print_company_name')
);
db_sync_add_column_if_missing(
	$conn,
	'executive',
	'cp_print_header_image',
	"varchar(255) NOT NULL DEFAULT '' COMMENT 'CP PI header banner image'",
	array('cp_print_pan', 'cp_print_gst')
);
db_sync_add_column_if_missing(
	$conn,
	'executive',
	'cp_print_footer_image',
	"varchar(255) NOT NULL DEFAULT '' COMMENT 'CP PI footer banner image'",
	array('cp_print_header_image', 'cp_print_pan')
);
db_sync_add_column_if_missing(
	$conn,
	'executive',
	'cp_print_address',
	"text COMMENT 'CP PI address HTML'",
	array('cp_print_footer_image', 'cp_print_header_image')
);
db_sync_add_column_if_missing(
	$conn,
	'executive',
	'cp_print_bank_details',
	"text COMMENT 'CP PI bank details HTML'",
	array('cp_print_address', 'cp_print_footer_image')
);
db_sync_add_column_if_missing(
	$conn,
	'executive',
	'cp_print_terms',
	"text COMMENT 'CP PI terms and conditions HTML'",
	array('cp_print_bank_details', 'cp_print_address')
);
db_sync_append_page_urls($conn, 650, array(
	'channel_partner_stock_manage.php',
	'channel_partner_stock_get_ajax.php',
	'channel_partner_print_settings.php',
));

/* ------------------------------------------------------------------
 * STEP 6 — Final verification (every run)
 * ------------------------------------------------------------------ */
$requiredExecutiveColumns = array('channel_partner_flag');
$requiredCpColumns = array(
	'id', 'channel_partner_id', 'company_name', 'person_name', 'mobile_no',
	'email', 'gst', 'country', 'state', 'city', 'pincode',
	'created_by', 'created_by_type', 'modified_by', 'modified_by_type',
	'created_date', 'modified_date', 'isActive', 'isDelete'
);
$optionalCpColumns = array('created_at', 'updated_at');
$requiredIndexes = array(
	'channel_partner_customer' => array('idx_cp_customer_mobile', 'idx_cp_customer_delete', 'idx_cp_customer_partner'),
);

$allReady = true;

db_sync_log('INFO', '--- Final Schema Verification ---');

foreach ($requiredExecutiveColumns as $column) {
	if (db_sync_column_exists($conn, 'executive', $column)) {
		db_sync_log('CHECK', 'READY: executive.' . $column);
	} else {
		$allReady = false;
		db_sync_log('FAIL', 'MISSING: executive.' . $column);
	}
}

if (!db_sync_table_exists($conn, 'channel_partner_customer')) {
	$allReady = false;
	db_sync_log('FAIL', 'MISSING: table channel_partner_customer');
} else {
	db_sync_log('CHECK', 'READY: table channel_partner_customer');

	foreach ($requiredCpColumns as $column) {
		if (db_sync_column_exists($conn, 'channel_partner_customer', $column)) {
			db_sync_log('CHECK', 'READY: channel_partner_customer.' . $column);
		} else {
			$allReady = false;
			db_sync_log('FAIL', 'MISSING: channel_partner_customer.' . $column);
		}
	}

	foreach ($requiredIndexes['channel_partner_customer'] as $indexName) {
		if (db_sync_index_exists($conn, 'channel_partner_customer', $indexName)) {
			db_sync_log('CHECK', 'READY: index channel_partner_customer.' . $indexName);
		} else {
			$allReady = false;
			db_sync_log('FAIL', 'MISSING: index channel_partner_customer.' . $indexName);
		}
	}

	foreach ($optionalCpColumns as $column) {
		if (db_sync_column_exists($conn, 'channel_partner_customer', $column)) {
			db_sync_log('CHECK', 'READY (optional): channel_partner_customer.' . $column);
		} else {
			db_sync_log('INFO', 'Optional column not present: channel_partner_customer.' . $column);
		}
	}

	if (db_sync_table_exists($conn, 'page_table')) {
		$pageRes = mysqli_query($conn, "SELECT `page_urls` FROM `page_table` WHERE `id`=555 AND `isDelete`=0 LIMIT 1");
		if ($pageRes && $pageRow = mysqli_fetch_assoc($pageRes)) {
			$pageUrls = explode(',', $pageRow['page_urls']);
			$cpUrlChecks = array(
				'channel_partner_customer_manage.php',
				'channel_partner_customer_crud.php',
				'channel_partner_customer_get_ajax.php',
			);
			foreach ($cpUrlChecks as $cpUrl) {
				if (in_array($cpUrl, $pageUrls, true)) {
					db_sync_log('CHECK', 'READY: page_table.555 includes ' . $cpUrl);
				} else {
					db_sync_log('INFO', 'page_table.555 missing URL: ' . $cpUrl . ' (module may still work via page_id 555 rights)');
				}
			}
		}
	}

	$countRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `channel_partner_customer` WHERE isDelete=0");
	if ($countRes) {
		$countRow = mysqli_fetch_assoc($countRes);
		db_sync_log('INFO', 'Active channel_partner_customer rows: ' . (int) $countRow['total']);
	}

	$cpCountRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `executive` WHERE channel_partner_flag=1 AND customer_flag=0 AND isDelete=0");
	if ($cpCountRes) {
		$cpCountRow = mysqli_fetch_assoc($cpCountRes);
		db_sync_log('INFO', 'Active channel partners in executive: ' . (int) $cpCountRow['total']);
	}
}

if (db_sync_table_exists($conn, 'api_table')) {
	db_sync_log('INFO', '--- API Registration Verification (223-228) ---');
	foreach ($requiredCpApis as $apiId => $apiSlug) {
		$apiId = (int) $apiId;
		$apiSlugEsc = mysqli_real_escape_string($conn, $apiSlug);
		$apiRes = mysqli_query($conn, "SELECT id, api_slug FROM `api_table` WHERE `id`={$apiId} AND `isDelete`=0 LIMIT 1");
		if ($apiRes && mysqli_num_rows($apiRes) > 0) {
			$apiRow = mysqli_fetch_assoc($apiRes);
			if ($apiRow['api_slug'] === $apiSlug) {
				db_sync_log('CHECK', 'READY: api_table id=' . $apiId . ' (' . $apiSlug . ')');
			} else {
				$allReady = false;
				db_sync_log('FAIL', 'api_table id=' . $apiId . ' exists but slug mismatch (found: ' . $apiRow['api_slug'] . ')');
			}
		} else {
			$allReady = false;
			db_sync_log('FAIL', 'MISSING: api_table id=' . $apiId . ' (' . $apiSlug . ')');
		}
	}

	db_sync_log('INFO', '--- API Registration Verification (229-230 Advance Expense) ---');
	foreach ($requiredAdvanceApis as $apiId => $apiSlug) {
		$apiId = (int) $apiId;
		$apiRes = mysqli_query($conn, "SELECT id, api_slug FROM `api_table` WHERE `id`={$apiId} AND `isDelete`=0 LIMIT 1");
		if ($apiRes && mysqli_num_rows($apiRes) > 0) {
			$apiRow = mysqli_fetch_assoc($apiRes);
			if ($apiRow['api_slug'] === $apiSlug) {
				db_sync_log('CHECK', 'READY: api_table id=' . $apiId . ' (' . $apiSlug . ')');
			} else {
				$allReady = false;
				db_sync_log('FAIL', 'api_table id=' . $apiId . ' exists but slug mismatch (found: ' . $apiRow['api_slug'] . ')');
			}
		} else {
			$allReady = false;
			db_sync_log('FAIL', 'MISSING: api_table id=' . $apiId . ' (' . $apiSlug . ')');
		}
	}

	db_sync_log('INFO', '--- API Registration Verification (231-232 Visit Remark/Reason) ---');
	foreach ($requiredVisitRemarkApis as $apiId => $apiSlug) {
		$apiId = (int) $apiId;
		$apiRes = mysqli_query($conn, "SELECT id, api_slug FROM `api_table` WHERE `id`={$apiId} AND `isDelete`=0 LIMIT 1");
		if ($apiRes && mysqli_num_rows($apiRes) > 0) {
			$apiRow = mysqli_fetch_assoc($apiRes);
			if ($apiRow['api_slug'] === $apiSlug) {
				db_sync_log('CHECK', 'READY: api_table id=' . $apiId . ' (' . $apiSlug . ')');
			} else {
				$allReady = false;
				db_sync_log('FAIL', 'api_table id=' . $apiId . ' exists but slug mismatch (found: ' . $apiRow['api_slug'] . ')');
			}
		} else {
			$allReady = false;
			db_sync_log('FAIL', 'MISSING: api_table id=' . $apiId . ' (' . $apiSlug . ')');
		}
	}

	foreach (array('remark_code', 'reason_code', 'approval_type', 'visit_followup_id', 'consultant_form_id', 'high_rate_form_id', 'note', 'followup_date') as $visitCol) {
		if (db_sync_column_exists($conn, 'visit', $visitCol)) {
			db_sync_log('CHECK', 'READY: visit.' . $visitCol);
		} else {
			$allReady = false;
			db_sync_log('FAIL', 'MISSING: visit.' . $visitCol);
		}
	}

	foreach (array('sales_executive' => 'device_id', 'sales_executive_login' => 'device_id') as $devTable => $devCol) {
		if (db_sync_column_exists($conn, $devTable, $devCol)) {
			db_sync_log('CHECK', 'READY: ' . $devTable . '.' . $devCol);
		} else {
			$allReady = false;
			db_sync_log('FAIL', 'MISSING: ' . $devTable . '.' . $devCol);
		}
	}

	if (db_sync_column_exists($conn, 'orders', 'channel_partner_order_flag')) {
		db_sync_log('CHECK', 'READY: orders.channel_partner_order_flag');
	} else {
		$allReady = false;
		db_sync_log('FAIL', 'MISSING: orders.channel_partner_order_flag');
	}
	foreach (array('cp_portal_order_flag', 'cp_order_mode') as $cpPortalCol) {
		if (db_sync_column_exists($conn, 'orders', $cpPortalCol)) {
			db_sync_log('CHECK', 'READY: orders.' . $cpPortalCol);
		} else {
			$allReady = false;
			db_sync_log('FAIL', 'MISSING: orders.' . $cpPortalCol);
		}
	}

	foreach (array('payment_received_flag', 'payment_received_date', 'payment_received_by', 'payment_received_amount', 'payment_received_type') as $payCol) {
		if (db_sync_column_exists($conn, 'orders', $payCol)) {
			db_sync_log('CHECK', 'READY: orders.' . $payCol);
		} else {
			$allReady = false;
			db_sync_log('FAIL', 'MISSING: orders.' . $payCol);
		}
	}

	foreach (array('visit_consultant_form', 'visit_high_rate_form', 'visit_high_rate_form_item') as $formTable) {
		if (db_sync_table_exists($conn, $formTable)) {
			db_sync_log('CHECK', 'READY: table ' . $formTable);
		} else {
			$allReady = false;
			db_sync_log('FAIL', 'MISSING: table ' . $formTable);
		}
	}

	if (db_sync_column_exists($conn, 'expence_category', 'expense_claim_type')) {
		db_sync_log('CHECK', 'READY: expence_category.expense_claim_type');
	} else {
		$allReady = false;
		db_sync_log('FAIL', 'MISSING: expence_category.expense_claim_type');
	}
	if (db_sync_column_exists($conn, 'expense', 'expense_claim_type')) {
		db_sync_log('CHECK', 'READY: expense.expense_claim_type');
	} else {
		$allReady = false;
		db_sync_log('FAIL', 'MISSING: expense.expense_claim_type');
	}
} else {
	$allReady = false;
	db_sync_log('FAIL', 'MISSING: table api_table');
}

db_sync_log('INFO', '--- API Runtime Checks ---');

/* ------------------------------------------------------------------
 * STEP 6 — Employee Chat (employee-to-employee)
 * ------------------------------------------------------------------ */
db_sync_run_query($conn, "CREATE TABLE IF NOT EXISTS `employee_chat_thread` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_one_id` int(11) NOT NULL DEFAULT 0 COMMENT 'dealer_distributor_network.id (smaller)',
	`user_two_id` int(11) NOT NULL DEFAULT 0 COMMENT 'dealer_distributor_network.id (larger)',
	`last_message_id` int(11) NOT NULL DEFAULT 0,
	`last_message_date` datetime DEFAULT NULL,
	`created_date` datetime DEFAULT NULL,
	`modified_date` datetime DEFAULT NULL,
	`isActive` tinyint(1) NOT NULL DEFAULT 1,
	`isDelete` tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE KEY `uk_ect_pair` (`user_one_id`,`user_two_id`),
	KEY `idx_ect_delete` (`isDelete`),
	KEY `idx_ect_last` (`last_message_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8", 'Create table employee_chat_thread');

db_sync_run_query($conn, "CREATE TABLE IF NOT EXISTS `employee_chat_message` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`thread_id` int(11) NOT NULL DEFAULT 0,
	`sender_id` int(11) NOT NULL DEFAULT 0 COMMENT 'dealer_distributor_network.id',
	`message_text` text,
	`is_read` tinyint(1) NOT NULL DEFAULT 0,
	`read_date` datetime DEFAULT NULL,
	`created_date` datetime DEFAULT NULL,
	`isActive` tinyint(1) NOT NULL DEFAULT 1,
	`isDelete` tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_ecm_thread` (`thread_id`),
	KEY `idx_ecm_sender` (`sender_id`),
	KEY `idx_ecm_unread` (`thread_id`,`is_read`,`isDelete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8", 'Create table employee_chat_message');

// page_table id=670
$chatPageCheck = mysqli_query($conn, "SELECT id FROM page_table WHERE id=670 LIMIT 1");
if ($chatPageCheck && mysqli_num_rows($chatPageCheck) > 0) {
	db_sync_append_page_urls($conn, 670, array(
		'employee_chat_manage.php',
		'employee_chat_ajax.php',
	));
	db_sync_run_query($conn, "UPDATE page_table SET page_title='Employee Chat', page_slug='employee_chat', isDelete=0, isActive=1 WHERE id=670", 'Update page_table id=670 Employee Chat');
} else {
	$now = date('Y-m-d H:i:s');
	$urls = 'employee_chat_manage.php,employee_chat_ajax.php';
	db_sync_run_query($conn, "INSERT INTO page_table (id, page_title, page_slug, page_count, page_urls, isActive, isDelete, adate, created_date)
		VALUES (670, 'Employee Chat', 'employee_chat', 0, '{$urls}', 1, 0, '{$now}', '{$now}')", 'Insert page_table id=670 Employee Chat');
}

// Seed view/insert rights for all active admin types (Super Admin already bypasses)
$adminTypesRes = mysqli_query($conn, "SELECT id FROM admin_type WHERE isDelete=0");
if ($adminTypesRes) {
	while ($at = mysqli_fetch_assoc($adminTypesRes)) {
		$aid = (int) $at['id'];
		if ($aid === 0) {
			continue;
		}
		$chk = mysqli_query($conn, "SELECT id FROM page_admin_right WHERE admin_id='{$aid}' AND page_id=670 AND isDelete=0 LIMIT 1");
		if ($chk && mysqli_num_rows($chk) > 0) {
			$rid = (int) mysqli_fetch_assoc($chk)['id'];
			db_sync_run_query($conn, "UPDATE page_admin_right SET view_flag=1, insert_flag=1, update_flag=1, delete_flag=0, all_data_flag=1 WHERE id='{$rid}'", "Chat rights update admin_type {$aid}");
		} else {
			$now = date('Y-m-d H:i:s');
			db_sync_run_query($conn, "INSERT INTO page_admin_right (page_id, admin_id, view_flag, insert_flag, update_flag, delete_flag, all_data_flag, personal_flag, chain_vise_flag, isDelete, created_by, created_by_type, created_date)
				VALUES (670, {$aid}, 1, 1, 1, 0, 1, 0, 0, 0, 1, 0, '{$now}')", "Chat rights insert admin_type {$aid}");
		}
	}
}

// page_table id=671 — Remark Wise Report
$rarPageCheck = mysqli_query($conn, "SELECT id FROM page_table WHERE id=671 LIMIT 1");
if ($rarPageCheck && mysqli_num_rows($rarPageCheck) > 0) {
	db_sync_append_page_urls($conn, 671, array(
		'remark_wise_report.php',
		'remark_wise_report_get_ajax.php',
		'remark_wise_report_excel.php',
	));
	db_sync_run_query($conn, "UPDATE page_table SET page_title='Remark Wise Report', page_slug='remark_wise_report', isDelete=0, isActive=1 WHERE id=671", 'Update page_table id=671 Remark Wise Report');
} else {
	$now = date('Y-m-d H:i:s');
	$urls = 'remark_wise_report.php,remark_wise_report_get_ajax.php,remark_wise_report_excel.php';
	db_sync_run_query($conn, "INSERT INTO page_table (id, page_title, page_slug, page_count, page_urls, isActive, isDelete, adate, created_date)
		VALUES (671, 'Remark Wise Report', 'remark_wise_report', 0, '{$urls}', 1, 0, '{$now}', '{$now}')", 'Insert page_table id=671 Remark Wise Report');
}

$adminTypesResRar = mysqli_query($conn, "SELECT id FROM admin_type WHERE isDelete=0");
if ($adminTypesResRar) {
	while ($at = mysqli_fetch_assoc($adminTypesResRar)) {
		$aid = (int) $at['id'];
		if ($aid === 0) {
			continue;
		}
		$chk = mysqli_query($conn, "SELECT id FROM page_admin_right WHERE admin_id='{$aid}' AND page_id=671 AND isDelete=0 LIMIT 1");
		if ($chk && mysqli_num_rows($chk) > 0) {
			$rid = (int) mysqli_fetch_assoc($chk)['id'];
			db_sync_run_query($conn, "UPDATE page_admin_right SET view_flag=1, insert_flag=1, update_flag=1, delete_flag=0, all_data_flag=1 WHERE id='{$rid}'", "Remark Report rights update admin_type {$aid}");
		} else {
			$now = date('Y-m-d H:i:s');
			db_sync_run_query($conn, "INSERT INTO page_admin_right (page_id, admin_id, view_flag, insert_flag, update_flag, delete_flag, all_data_flag, personal_flag, chain_vise_flag, isDelete, created_by, created_by_type, created_date)
				VALUES (671, {$aid}, 1, 1, 1, 0, 1, 0, 0, 0, 1, 0, '{$now}')", "Remark Report rights insert admin_type {$aid}");
		}
	}
}

if (db_sync_table_exists($conn, 'employee_chat_thread') && db_sync_table_exists($conn, 'employee_chat_message')) {
	db_sync_log('CHECK', 'READY: employee_chat_thread + employee_chat_message');
} else {
	$allReady = false;
	db_sync_log('FAIL', 'MISSING: employee chat tables');
}

$chatApiBase = 'service_employee_chat.php?key=1226';
db_sync_register_api_if_missing($conn, 236, 'get_employee_chat_users', 'Get Employee Chat Users', $chatApiBase . '&s=236&sales_executive_id=&search=');
db_sync_register_api_if_missing($conn, 237, 'get_employee_chat_threads', 'Get Employee Chat Threads', $chatApiBase . '&s=237&sales_executive_id=');
db_sync_register_api_if_missing($conn, 238, 'get_employee_chat_messages', 'Get Employee Chat Messages', $chatApiBase . '&s=238&sales_executive_id=&thread_id=&after_id=0');
db_sync_register_api_if_missing($conn, 239, 'send_employee_chat_message', 'Send Employee Chat Message', $chatApiBase . '&s=239&sales_executive_id=&thread_id=&message_text=');
db_sync_register_api_if_missing($conn, 240, 'get_employee_chat_unread', 'Get Employee Chat Unread Count', $chatApiBase . '&s=240&sales_executive_id=');

$apiKeyCountRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `api_key_table` WHERE api_key='1226' AND isDelete=0");
if ($apiKeyCountRes) {
	$apiKeyCountRow = mysqli_fetch_assoc($apiKeyCountRes);
	if ((int) $apiKeyCountRow['total'] > 0) {
		db_sync_log('CHECK', 'READY: api_key_table contains key 1226');
	} else {
		$allReady = false;
		db_sync_log('FAIL', 'MISSING: api_key_table key 1226 (App APIs will reject all requests)');
	}
} else {
	$allReady = false;
	db_sync_log('FAIL', 'Could not verify api_key_table: ' . mysqli_error($conn));
}

$apiRuntimeChecks = array(
	'function.class.php' => function () {
		require_once __DIR__ . '/include/function.class.php';
		return class_exists('Functions') ? 'Functions class loaded' : 'Functions class missing';
	},
	'class.executive.php' => function () {
		require_once __DIR__ . '/include/class.executive.php';
		return class_exists('Executive') ? 'Executive class loaded' : 'Executive class missing';
	},
	'class.channel_partner_customer.php' => function () {
		require_once __DIR__ . '/include/class.channel_partner_customer.php';
		return class_exists('ChannelPartnerCustomer') ? 'ChannelPartnerCustomer class loaded' : 'class missing';
	},
	'json_encode (JSON extension)' => function () {
		if (!function_exists('json_encode')) {
			return 'NOT available - PHP JSON extension is DISABLED. Enable it in cPanel > Select PHP Version > Extensions (check "json"). All App APIs return 500 without it.';
		}
		$json = json_encode(array('ack' => 0, 'ack_msg' => 'test'));
		return ($json !== false) ? 'json_encode ok' : 'json_encode failed';
	},
	'mysql_real_escape_string' => function () {
		return function_exists('mysql_real_escape_string') ? 'available (legacy)' : 'NOT available (use mysqli only)';
	},
);

foreach ($apiRuntimeChecks as $label => $callback) {
	try {
		$detail = $callback();
		$isMysqlLegacyInfo = ($label === 'mysql_real_escape_string' && strpos($detail, 'NOT available') !== false);
		if ($isMysqlLegacyInfo) {
			db_sync_log('INFO', 'API runtime: ' . $label . ' -> ' . $detail . ' (expected on PHP 5.6+ with mysqli; CRM uses $db->clean())');
		} elseif (strpos($detail, 'NOT available') !== false || strpos($detail, 'missing') !== false || strpos($detail, 'failed') !== false) {
			db_sync_log('FAIL', 'API runtime: ' . $label . ' -> ' . $detail);
			$allReady = false;
		} else {
			db_sync_log('CHECK', 'API runtime: ' . $label . ' -> ' . $detail);
		}
	} catch (Exception $e) {
		$allReady = false;
		db_sync_log('FAIL', 'API runtime: ' . $label . ' -> ' . $e->getMessage());
	}
}

if ($allReady) {
	db_sync_log('INFO', 'RESULT: Database is READY for Armor CRM updates.');
} else {
	db_sync_log('FAIL', 'RESULT: Some DB objects are still missing. Re-run this page after fixing errors.');
}

header('Content-Type: text/html; charset=utf-8');
$environment = isset($config['environment']) ? $config['environment'] : 'unknown';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Armor CRM DB Sync</title>
	<style>
		body { font-family: Arial, sans-serif; margin: 30px; background: #f5f5f5; }
		.box { background: #fff; padding: 20px; border-radius: 6px; max-width: 980px; }
		.ok, .check { color: #0a7a2f; }
		.fail { color: #b00020; }
		.skip { color: #666; }
		.info { color: #0057b8; }
		.summary { background: #f8f8f8; padding: 12px; border-radius: 4px; margin-bottom: 16px; }
		.ready { color: #0a7a2f; font-weight: bold; }
		.notready { color: #b00020; font-weight: bold; }
		ul { padding-left: 20px; }
		li { margin-bottom: 6px; word-break: break-word; }
	</style>
</head>
<body>
	<div class="box">
		<h2>Armor CRM — Database Sync</h2>
		<div class="summary">
			<p>Version: <strong><?php echo htmlspecialchars(DB_SYNC_VERSION); ?></strong></p>
			<p>Environment: <strong><?php echo htmlspecialchars($environment); ?></strong></p>
			<p>Database: <strong><?php echo htmlspecialchars($name); ?></strong></p>
			<p>
				Status:
				<span class="<?php echo $allReady ? 'ready' : 'notready'; ?>">
					<?php echo $allReady ? 'READY' : 'NOT READY'; ?>
				</span>
			</p>
			<p>
				OK: <?php echo (int) $stats['OK']; ?> |
				SKIP: <?php echo (int) $stats['SKIP']; ?> |
				CHECK: <?php echo (int) $stats['CHECK']; ?> |
				FAIL: <?php echo (int) $stats['FAIL']; ?> |
				INFO: <?php echo (int) $stats['INFO']; ?>
			</p>
		</div>
		<ul>
			<?php foreach ($results as $row) { ?>
				<li class="<?php echo strtolower($row['status']); ?>">
					<strong><?php echo htmlspecialchars($row['status']); ?></strong>:
					<?php echo htmlspecialchars($row['query']); ?>
					<?php if (isset($row['error'])) { ?>
						<br><span class="fail"><?php echo htmlspecialchars($row['error']); ?></span>
					<?php } ?>
				</li>
			<?php } ?>
		</ul>
		<p><strong>Live deploy steps:</strong></p>
		<ol>
			<li>Upload all PHP/code files to live server</li>
			<li>Open this URL in browser:<br>
				<code>https://armor-crm.oceanhub.co.in/db_sync.php?key=armor_cp_sync_2026</code>
			</li>
			<li>Wait until status shows <span class="ready">READY</span> and FAIL = 0</li>
			<li>Test Channel Partner Customer web page + App APIs (#223-#228)</li>
		</ol>
		<p><strong>This sync creates/updates:</strong></p>
		<ul>
			<li>Employee Chat tables + page 670 + rights rights</li>
			<li>Table <code>channel_partner_customer</code> (+ all columns &amp; indexes)</li>
			<li>Column <code>executive.channel_partner_flag</code></li>
			<li>Page URLs in <code>page_table</code> id=555</li>
			<li>APIs in <code>api_table</code> id 223, 224, 225, 226, 227, 228</li>
			<li>Advance Expense APIs 229-230 + <code>expense_claim_type</code> columns + Advance categories seed</li>
			<li>Visit Remark/Reason APIs 231-234 + <code>visit.remark_code</code>, <code>reason_code</code>, <code>approval_type</code>, <code>visit_followup_id</code></li>
			<li>API <code>#233 save_visit_consultant_form</code> — Consultant Detail SAVE AND NEXT</li>
			<li>API <code>#234 save_visit_high_rate_form</code> — High Rate Analysis SAVE AND NEXT (accepts product <code>slug</code>)</li>
			<li>API <code>#235 get_visit_high_rate_products</code> — fixed product list with slugs</li>
			<li>Visit forms: <code>visit_consultant_form</code> (C1/C2), <code>visit_high_rate_form</code> + <code>visit_high_rate_form_item</code> (E1)</li>
			<li>Column <code>orders.channel_partner_order_flag</code> (1=Channel Partner Order)</li>
			<li>Columns <code>orders.cp_portal_order_flag</code>, <code>cp_order_mode</code> (CP portal order → Convert to Order)</li>
			<li>Columns <code>orders.payment_received_flag</code>, <code>payment_received_date</code>, <code>payment_received_by</code>, <code>payment_received_amount</code>, <code>payment_received_type</code> (Pending Payment 45 days)</li>
			<li>Column <code>sales_executive.device_id</code> + <code>sales_executive_login.device_id</code> (App login <code>token</code> → device_id for notifications)</li>
			<li>Column <code>visit.note</code> + <code>visit.followup_date</code> (App stopVisit <code>note</code> / <code>date</code> → <code>followup_date</code>, format <code>Y-m-d H:i</code>)</li>
		</ul>
		<p><strong>Safe:</strong> Idempotent — run multiple times; existing data is not deleted.</p>
		<p><strong>Security:</strong> Delete <code>db_sync.php</code> from live after final READY confirmation.</p>
	</div>
</body>
</html>
