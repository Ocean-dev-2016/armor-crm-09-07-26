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
define('DB_SYNC_VERSION', '2026.07.11.2');

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
db_sync_log('INFO', 'Changes: executive.channel_partner_flag, channel_partner_customer table + APIs 223-228');

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
} else {
	$allReady = false;
	db_sync_log('FAIL', 'MISSING: table api_table');
}

db_sync_log('INFO', '--- API Runtime Checks ---');

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
	db_sync_log('INFO', 'RESULT: Database is READY for Channel Partner Customer module.');
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
			<li>Table <code>channel_partner_customer</code> (+ all columns &amp; indexes)</li>
			<li>Column <code>executive.channel_partner_flag</code></li>
			<li>Page URLs in <code>page_table</code> id=555</li>
			<li>APIs in <code>api_table</code> id 223, 224, 225, 226, 227, 228</li>
		</ul>
		<p><strong>Safe:</strong> Idempotent — run multiple times; existing data is not deleted.</p>
		<p><strong>Security:</strong> Delete <code>db_sync.php</code> from live after final READY confirmation.</p>
	</div>
</body>
</html>
