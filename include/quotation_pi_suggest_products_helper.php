<?php

/**

 * Suggested product range for Quotation / PI (Sales Order) — photo, name, rate at bottom.

 * Product codes from estimate sheets (photo 2 + photo 3).

 */



if (!function_exists('armor_quotation_pi_suggest_table')) {

	function armor_quotation_pi_suggest_table()

	{

		return 'quotation_pi_suggest_product';

	}

}



if (!function_exists('armor_quotation_pi_suggest_category_map')) {

	function armor_quotation_pi_suggest_category_map()

	{

		return array(

			'hydrant' => array(

				'title' => 'HYDRANT ACCESSORIES',

				'catnos' => array(

					'2327', '2294', '2344', '3073', '2706', '2494', '2860', '2863', '2915', '2037', '2217', '2184',

					'2149', '2158', '2359', '2352', '2803', '2384', '2794', '2660', '2115', '2010', '2021', '2024',

				),

			),

			'plumbing' => array(

				'title' => 'PLUMBING ACCESSORIES',

				'catnos' => array(

					'2086', '2088', '2095', '2065', '2068', '2075', '2080', '2084', '2091', '2093', '2061', '2067', '2071', '2078',

					'2426', '2427', '2429',

					'2442', '2447', '2443', '2444', '2446', '2445',

					'2433', '2670', '2667', '2435', '2668', '2669', '2774',

					'2708', '3005', '2713', '3006', '2718', '2693', '3007', '2700', '2707',

					'2677', '2678', '2679', '2680', '2681',

					'2612',

				),

			),

			'sprinkler' => array(

				'title' => 'SPRINKLER ACCESSORIES',

				'catnos' => array(

					'2987', '2985', '2988',

					'2716', '2691', '2694', '2697', '2701',

					'2717', '2692', '2695', '2699', '2702',

					'2595', '2967', '2710', '2980', '2646', '2650', '3009',

					'2747', '2745', '2746',

					'2731', '2729', '2730',

					'2734', '2783', '2787',

					'2034', '2740',

					'2764', '2719', '3013',

				),

			),

		);

	}

}



if (!function_exists('armor_quotation_pi_suggest_default_catnos')) {

	function armor_quotation_pi_suggest_default_catnos()

	{

		$catnos = array();

		foreach (armor_quotation_pi_suggest_category_map() as $group) {

			foreach ($group['catnos'] as $catno) {

				$catnos[] = $catno;

			}

		}

		return $catnos;

	}

}



if (!function_exists('armor_quotation_pi_suggest_catno_order_map')) {

	function armor_quotation_pi_suggest_catno_order_map()

	{

		$order = array();

		$pos = 0;

		foreach (armor_quotation_pi_suggest_category_map() as $group) {

			foreach ($group['catnos'] as $catno) {

				$order[(string) $catno] = $pos++;

			}

		}

		return $order;

	}

}



if (!function_exists('armor_quotation_pi_sort_catnos_by_sheet_order')) {

	function armor_quotation_pi_sort_catnos_by_sheet_order($catnos)

	{

		if (!is_array($catnos) || empty($catnos)) {

			return array();

		}

		$orderMap = armor_quotation_pi_suggest_catno_order_map();

		$rows = array();

		foreach ($catnos as $catno) {

			$catno = trim((string) $catno);

			if ($catno === '') {

				continue;

			}

			$rows[] = array(

				'catno' => $catno,

				'order' => isset($orderMap[$catno]) ? (int) $orderMap[$catno] : 999999,

			);

		}

		usort($rows, function ($a, $b) {

			if ($a['order'] === $b['order']) {

				return strcmp($a['catno'], $b['catno']);

			}

			return ($a['order'] < $b['order']) ? -1 : 1;

		});

		$out = array();

		foreach ($rows as $row) {

			$out[] = $row['catno'];

		}

		return $out;

	}

}



if (!function_exists('armor_quotation_pi_sort_items_by_category_order')) {

	function armor_quotation_pi_sort_items_by_category_order($items, $categoryKey)

	{

		$map = armor_quotation_pi_suggest_category_map();

		if (!isset($map[$categoryKey]['catnos'])) {

			return $items;

		}

		$positions = array_flip(array_map('strval', $map[$categoryKey]['catnos']));

		usort($items, function ($a, $b) use ($positions) {

			$catA = isset($a['catno']) ? (string) $a['catno'] : '';

			$catB = isset($b['catno']) ? (string) $b['catno'] : '';

			$posA = isset($positions[$catA]) ? (int) $positions[$catA] : 999999;

			$posB = isset($positions[$catB]) ? (int) $positions[$catB] : 999999;

			if ($posA === $posB) {

				return strcmp($catA, $catB);

			}

			return ($posA < $posB) ? -1 : 1;

		});

		return $items;

	}

}



if (!function_exists('armor_quotation_pi_group_items_by_category')) {

	function armor_quotation_pi_group_items_by_category($items)

	{

		$map = armor_quotation_pi_suggest_category_map();

		$catnoToGroup = array();

		foreach ($map as $key => $group) {

			foreach ($group['catnos'] as $catno) {

				$catnoToGroup[(string) $catno] = $key;

			}

		}

		$grouped = array();

		foreach ($map as $key => $group) {

			$grouped[$key] = array(

				'title' => $group['title'],

				'items' => array(),

			);

		}

		$other = array();

		foreach ($items as $item) {

			$catno = isset($item['catno']) ? (string) $item['catno'] : '';

			if ($catno !== '' && isset($catnoToGroup[$catno])) {

				$grouped[$catnoToGroup[$catno]]['items'][] = $item;

			} else {

				$other[] = $item;

			}

		}

		$out = array();

		foreach ($grouped as $key => $group) {

			if (!empty($group['items'])) {

				$group['items'] = armor_quotation_pi_sort_items_by_category_order($group['items'], $key);

				$out[$key] = $group;

			}

		}

		if (!empty($other)) {

			$out['other'] = array(

				'title' => 'OTHER PRODUCTS',

				'items' => $other,

			);

		}

		return $out;

	}

}



if (!function_exists('armor_quotation_pi_suggest_ensure_table')) {

	function armor_quotation_pi_suggest_ensure_table($db)

	{

		$table = armor_quotation_pi_suggest_table();

		if ($db->tableExists($table)) {

			return true;

		}

		$sql = "CREATE TABLE IF NOT EXISTS `" . $table . "` (

			`id` int(11) NOT NULL AUTO_INCREMENT,

			`catno` varchar(50) NOT NULL DEFAULT '',

			`display_order` int(11) NOT NULL DEFAULT 0,

			`isActive` tinyint(1) NOT NULL DEFAULT 1,

			`isDelete` tinyint(1) NOT NULL DEFAULT 0,

			`created_date` datetime DEFAULT NULL,

			`modified_date` datetime DEFAULT NULL,

			PRIMARY KEY (`id`),

			KEY `idx_catno` (`catno`),

			KEY `idx_active` (`isActive`,`isDelete`)

		) ENGINE=InnoDB DEFAULT CHARSET=utf8";

		$ok = @mysqli_query($db->myconn, $sql);

		if (!$ok) {

			return false;

		}

		armor_quotation_pi_suggest_seed_defaults($db);

		return true;

	}

}



if (!function_exists('armor_quotation_pi_suggest_seed_defaults')) {

	function armor_quotation_pi_suggest_seed_defaults($db)

	{

		$table = armor_quotation_pi_suggest_table();

		if (!$db->tableExists($table)) {

			return;

		}

		$count = (int) $db->rp_getTotalRecord($table, 'isDelete=0');

		if ($count > 0) {

			return;

		}

		$now = date('Y-m-d H:i:s');

		$order = 0;

		foreach (armor_quotation_pi_suggest_default_catnos() as $catno) {

			$order++;

			$db->rp_insert($table, array(

				'catno' => $catno,

				'display_order' => $order,

				'isActive' => 1,

				'isDelete' => 0,

				'created_date' => $now,

				'modified_date' => $now,

			), 0);

		}

	}

}



if (!function_exists('armor_quotation_pi_is_print_request')) {

	function armor_quotation_pi_is_print_request()

	{

		return (isset($_REQUEST['print']) && (string) $_REQUEST['print'] === '1')

			|| (isset($_REQUEST['p']) && (string) $_REQUEST['p'] === '1');

	}

}



if (!function_exists('armor_quotation_pi_suggest_sync_from_map')) {

	function armor_quotation_pi_suggest_sync_from_map($db)

	{

		armor_quotation_pi_suggest_ensure_table($db);

		$table = armor_quotation_pi_suggest_table();

		if (!$db->tableExists($table)) {

			return;

		}

		$catnos = armor_quotation_pi_suggest_default_catnos();

		$now = date('Y-m-d H:i:s');

		$order = 0;

		$mapSet = array();

		foreach ($catnos as $catno) {

			$catno = trim((string) $catno);

			if ($catno === '') {

				continue;

			}

			$order++;

			$mapSet[$catno] = 1;

			$catnoEsc = $db->clean($catno);

			$existingId = (int) $db->rp_getValue($table, 'id', "catno='" . $catnoEsc . "'", 0);

			$row = array(

				'catno' => $catno,

				'display_order' => $order,

				'isActive' => 1,

				'isDelete' => 0,

				'modified_date' => $now,

			);

			if ($existingId > 0) {

				$db->rp_update($table, $row, "id='" . $existingId . "'", 0);

			} else {

				$row['created_date'] = $now;

				$db->rp_insert($table, $row, 0);

			}

		}

		$res = $db->rp_getData($table, 'id,catno', 'isDelete=0', '', 0);

		if ($res) {

			while ($row = mysqli_fetch_assoc($res)) {

				$catno = trim((string) $row['catno']);

				if ($catno === '' || isset($mapSet[$catno])) {

					continue;

				}

				$db->rp_update($table, array(

					'isActive' => 0,

					'isDelete' => 1,

					'modified_date' => $now,

				), "id='" . (int) $row['id'] . "'", 0);

			}

		}

	}

}



if (!function_exists('armor_quotation_pi_suggest_catnos')) {

	function armor_quotation_pi_suggest_catnos($db = null)

	{

		if ($db !== null && !armor_quotation_pi_is_print_request()) {

			armor_quotation_pi_suggest_sync_from_map($db);

		}

		return armor_quotation_pi_sort_catnos_by_sheet_order(armor_quotation_pi_suggest_default_catnos());

	}

}



if (!function_exists('armor_quotation_pi_get_product_name')) {

	function armor_quotation_pi_get_product_name($db, $productId)

	{

		$productId = (int) $productId;

		if ($productId <= 0) {

			return '';

		}

		$name = $db->rp_getValue('product', 'name', "id='" . $productId . "' AND isDelete=0", 0);

		if ($name === '' || $name === null) {

			$name = $db->rp_getValue('product', 'name1', "id='" . $productId . "' AND isDelete=0", 0);

		}

		return $name ? html_entity_decode(strip_tags($name), ENT_QUOTES, 'UTF-8') : '';

	}

}



if (!function_exists('armor_quotation_pi_normalize_catno')) {

	function armor_quotation_pi_normalize_catno($catno)

	{

		$catno = (string) $catno;

		$catno = str_replace(array("\t", "\r", "\n", "\0", "\x0B"), '', $catno);

		return trim($catno);

	}

}



if (!function_exists('armor_quotation_pi_catno_sql_normalized')) {

	function armor_quotation_pi_catno_sql_normalized($column = 'catno')

	{

		return "TRIM(REPLACE(REPLACE(REPLACE(" . $column . ", CHAR(9), ''), CHAR(10), ''), CHAR(13), ''))";

	}

}



if (!function_exists('armor_quotation_pi_resolve_pwp_row')) {

	function armor_quotation_pi_resolve_pwp_row($db, $catno)

	{

		$catno = armor_quotation_pi_normalize_catno($catno);

		if ($catno === '') {

			return null;

		}

		$catnoEsc = $db->clean($catno);

		$catnoSql = armor_quotation_pi_catno_sql_normalized('catno');

		$whereList = array(

			"isDelete=0 AND catno='" . $catnoEsc . "'",

			"isDelete=0 AND " . $catnoSql . "='" . $catnoEsc . "'",

		);

		$candidates = array();

		foreach ($whereList as $where) {

			$pwpRes = $db->rp_getData('product_weight_price', '*', $where, 'id ASC', 0);

			if (!$pwpRes) {

				continue;

			}

			while ($pwp = mysqli_fetch_assoc($pwpRes)) {

				if (armor_quotation_pi_normalize_catno($pwp['catno']) !== $catno) {

					continue;

				}

				$proId = (int) $pwp['product_id'];

				if ($proId <= 0) {

					continue;

				}

				$isDeleted = (int) $db->rp_getValue('product', 'isDelete', "id='" . $proId . "'", 0);

				if ($isDeleted === 1) {

					continue;

				}

				$isActive = isset($pwp['isActive']) ? (string) $pwp['isActive'] : '1';

				$candidates[] = array(

					'row' => $pwp,

					'active' => ($isActive !== '0'),

				);

			}

		}

		if (empty($candidates)) {

			return null;

		}

		foreach ($candidates as $candidate) {

			if ($candidate['active']) {

				return $candidate['row'];

			}

		}

		return $candidates[0]['row'];

	}

}



if (!function_exists('armor_quotation_pi_lookup_product_by_catno')) {

	function armor_quotation_pi_lookup_product_by_catno($db, $catno)

	{

		$catno = trim((string) $catno);

		if ($catno === '') {

			return null;

		}

		$pwp = armor_quotation_pi_resolve_pwp_row($db, $catno);

		if (!$pwp) {

			return null;

		}

		$proId = (int) $pwp['product_id'];

		$name = armor_quotation_pi_get_product_name($db, $proId);

		$imagePath = $db->rp_getValue('product', 'image_path', "id='" . $proId . "' AND isDelete=0", 0);

		return array(

			'catno' => $catno,

			'product_id' => $proId,

			'name' => $name !== '' ? $name : $catno,

			'image' => armor_quotation_pi_product_image_url($imagePath ? $imagePath : ''),

		);

	}

}



if (!function_exists('armor_quotation_pi_get_admin_suggest_rows')) {

	function armor_quotation_pi_get_admin_suggest_rows($db)

	{

		armor_quotation_pi_suggest_sync_from_map($db);

		$table = armor_quotation_pi_suggest_table();

		$selectedMap = array();

		$selectedOrder = array();

		$res = $db->rp_getData($table, '*', 'isDelete=0', 'display_order ASC, id ASC', 0);

		if ($res) {

			while ($row = mysqli_fetch_assoc($res)) {

				$catno = trim((string) $row['catno']);

				if ($catno === '') {

					continue;

				}

				$selectedMap[$catno] = array(

					'isActive' => (int) $row['isActive'],

					'display_order' => (int) $row['display_order'],

				);

				$selectedOrder[] = $catno;

			}

		}

		$pool = array();

		foreach (armor_quotation_pi_suggest_default_catnos() as $catno) {

			$pool[$catno] = 1;

		}

		foreach ($selectedOrder as $catno) {

			$pool[$catno] = 1;

		}

		$rows = array();

		$orderIndex = 0;

		foreach ($pool as $catno => $_) {

			$orderIndex++;

			$info = armor_quotation_pi_lookup_product_by_catno($db, $catno);

			$isSelected = isset($selectedMap[$catno]) && (int) $selectedMap[$catno]['isActive'] === 1;

			$displayOrder = isset($selectedMap[$catno]) ? (int) $selectedMap[$catno]['display_order'] : 9999 + $orderIndex;

			$rows[] = array(

				'catno' => $catno,

				'name' => $info ? $info['name'] : '',

				'image' => $info ? $info['image'] : armor_quotation_pi_product_image_url(''),

				'product_id' => $info ? (int) $info['product_id'] : 0,

				'is_selected' => $isSelected ? 1 : 0,

				'display_order' => $displayOrder,

				'found' => $info ? 1 : 0,

			);

		}

		usort($rows, function ($a, $b) {

			if ((int) $a['display_order'] === (int) $b['display_order']) {

				return strcmp($a['catno'], $b['catno']);

			}

			return ((int) $a['display_order'] < (int) $b['display_order']) ? -1 : 1;

		});

		return $rows;

	}

}



if (!function_exists('armor_quotation_pi_save_suggest_products')) {

	function armor_quotation_pi_save_suggest_products($db, $selectedCatnos)

	{

		armor_quotation_pi_suggest_ensure_table($db);

		$table = armor_quotation_pi_suggest_table();

		if (!is_array($selectedCatnos)) {

			return array('ack' => 0, 'ack_msg' => 'Invalid product list.');

		}

		$now = date('Y-m-d H:i:s');

		$db->rp_update($table, array('isDelete' => 1, 'isActive' => 0, 'modified_date' => $now), '1=1', 0);

		$order = 0;

		$saved = 0;

		foreach ($selectedCatnos as $catno) {

			$catno = trim((string) $catno);

			if ($catno === '') {

				continue;

			}

			$order++;

			$catnoEsc = $db->clean($catno);

			$existingId = (int) $db->rp_getValue($table, 'id', "catno='" . $catnoEsc . "'", 0);

			$row = array(

				'catno' => $catno,

				'display_order' => $order,

				'isActive' => 1,

				'isDelete' => 0,

				'modified_date' => $now,

			);

			if ($existingId > 0) {

				$db->rp_update($table, $row, "id='" . $existingId . "'", 0);

			} else {

				$row['created_date'] = $now;

				$db->rp_insert($table, $row, 0);

			}

			$saved++;

		}

		return array('ack' => 1, 'ack_msg' => 'Suggested products saved (' . $saved . ' selected).', 'count' => $saved);

	}

}



if (!function_exists('armor_quotation_pi_product_image_url')) {

	function armor_quotation_pi_product_image_url($imagePath)

	{

		$imagePath = trim((string) $imagePath);

		$base = (defined('SITEURL') ? SITEURL : '');

		$default = $base . PRODUCT . 'default.png';

		if ($imagePath === '') {

			return $default;

		}

		if (preg_match('/^https?:\/\//i', $imagePath)) {

			return $imagePath;

		}

		if ($base !== '' && strpos($imagePath, $base) === 0) {

			return $imagePath;

		}

		if (strpos($imagePath, PRODUCT) !== false) {

			$parts = explode(PRODUCT, $imagePath);

			$imagePath = end($parts);

		}

		return $base . PRODUCT . ltrim($imagePath, '/');

	}

}



if (!function_exists('armor_quotation_pi_suggest_display_discount_percent')) {

	function armor_quotation_pi_suggest_display_discount_percent()

	{

		return 44;

	}

}



if (!function_exists('armor_quotation_pi_suggest_styles')) {

	function armor_quotation_pi_suggest_styles()

	{

		return '<style type="text/css">

.qp-suggest-print-section {

	width: 100%;

	margin: 0;

	page-break-inside: auto;

	font-family: Arial, Helvetica, sans-serif;

}

.qp-suggest-wrap-table {

	width: 100% !important;

	border-collapse: collapse;

	border: none !important;

	margin: 0;

}

.qp-suggest-wrap-table td {

	padding: 0 !important;

	border: none !important;

	vertical-align: top;

}

.quote-wrap .qp-suggest-print-section,

.main-container .qp-suggest-print-section {

	border: none;

}

.quote-suggest-body {

	width: 100%;

}

.quote-main-body + .quote-suggest-body .qp-suggest-wrap-table,

.main-container .quote-suggest-body .qp-suggest-wrap-table {

	border-top: none;

}

.qp-suggest-print-header {

	text-align: center;

	margin: 0;

	padding: 10px 8px;

	border: none !important;

	border-bottom: 1px solid #595959 !important;

	background: #4a4a4a;

}

.qp-suggest-print-title {

	font-size: 14px;

	font-weight: bold;

	text-transform: uppercase;

	letter-spacing: 1px;

	color: #fff;

}

.qp-suggest-print-subtitle {

	font-size: 10px;

	color: #e0e0e0;

	margin-top: 3px;

}

.qp-suggest-print-grid {

	width: 100%;

	border-collapse: collapse;

	table-layout: fixed;

	border: none;

}

.qp-suggest-print-grid td.qp-suggest-print-cell {

	width: 25%;

	min-height: 0;

	height: auto;

	vertical-align: top;

	border: 1px solid #595959;

	padding: 0 !important;

	box-sizing: border-box;

	background: #fff;

	overflow: hidden;

	page-break-inside: avoid;

	break-inside: avoid-page;

}

.qp-suggest-print-box {

	display: block;

	width: 100%;

	min-height: 0;

	height: auto;

	box-sizing: border-box;

	page-break-inside: avoid;

	break-inside: avoid-page;

	overflow: hidden;

}

.qp-suggest-product-row {

	page-break-inside: auto;

	break-inside: auto;

}

.qp-suggest-print-cell-empty {

	border: none !important;

	background: transparent !important;

}

.qp-suggest-cell-inner {

	box-sizing: border-box;

	padding: 0 4px 2px;

	width: 100%;

	height: auto;

}

.qp-prod-text-inner {

	box-sizing: border-box;

	padding: 0 1px;

}

.qp-prod-card {

	width: 100%;

	min-height: 0;

	height: auto;

	border: none !important;

	border-collapse: collapse;

	table-layout: fixed;

}

.qp-prod-card td {

	border: none !important;

	vertical-align: top;

}

.qp-prod-badge-row {

	height: 18px;

	padding: 1px 4px 0 !important;

	text-align: right !important;

	vertical-align: top !important;

}

.qp-prod-badge-bar {

	display: flex;

	align-items: center;

	justify-content: flex-end;

	gap: 4px;

	width: 100%;

	min-height: 16px;

	box-sizing: border-box;

}

.qp-prod-disc-label {

	display: inline-block;

	border: 1px solid #d9534f;

	color: #d9534f;

	font-size: 8.5px;

	font-weight: bold;

	line-height: 1.1;

	padding: 1px 4px;

	background: #fff;

	white-space: nowrap;

}

.qp-prod-disc-wrap {

	display: inline-block;

	padding: 1px;

	flex: 0 0 auto;

}

.qp-prod-disc {

	display: inline-block;

	width: 18px;

	height: 18px;

	line-height: 18px;

	border-radius: 50%;

	background: #e74c3c;

	color: #fff;

	font-size: 8px;

	font-weight: bold;

	text-align: center;

}

.qp-prod-img-cell {

	height: 36px;

	background: #f7f7f7;

	border-bottom: 1px solid #e8e8e8 !important;

	padding: 1px !important;

	text-align: center;

	vertical-align: middle !important;

}

.qp-prod-img-box {

	width: 100%;

	height: 34px;

	line-height: 34px;

	text-align: center;

	overflow: hidden;

}

.qp-prod-img {

	max-width: 96%;

	max-height: 32px;

	width: auto;

	height: auto;

	vertical-align: middle;

	object-fit: contain;

}

.qp-prod-code-cell {

	height: auto;

	min-height: 12px;

	font-size: 9.5px;

	font-weight: 600;

	color: #555555 !important;

	letter-spacing: 0.2px;

	padding: 1px 4px 0 !important;

	line-height: 1.1;

}

.qp-prod-name-cell {

	height: auto;

	min-height: 18px;

	max-height: 24px;

	font-size: 8.5px;

	font-weight: bold;

	color: #000000 !important;

	line-height: 1.1;

	padding: 1px 4px 0 !important;

	overflow: hidden;

	text-transform: uppercase;

	text-align: left;

}

.qp-prod-price-cell {

	height: auto;

	min-height: 20px;

	padding: 1px 4px 4px !important;

	text-align: center !important;

	vertical-align: bottom !important;

	overflow: visible !important;

}

.qp-prod-price-wrap {

	text-align: center;

	width: 100%;

	padding: 0 4px;

	box-sizing: border-box;

}

.qp-prod-price-line {

	display: inline-block;

	font-size: 11px;

	font-weight: bold;

	color: #0a5c24 !important;

	line-height: 1.4;

	text-align: center;

	white-space: nowrap;

	max-width: 100%;

}

.qp-prod-price {

	font-size: 11px;

	font-weight: bold;

	color: #0a5c24 !important;

	line-height: 1.4;

	text-align: center;

}

.qp-prod-unit {

	display: inline;

	font-size: 10px;

	font-weight: 600;

	color: #333333 !important;

	line-height: 1.4;

	white-space: nowrap;

}

.qp-prod-disc {

	display: inline-block;

	width: 30px;

	height: 30px;

	line-height: 30px;

	border-radius: 50%;

	background: #e74c3c;

	color: #fff;

	font-size: 9px;

	font-weight: bold;

	text-align: center;

	box-sizing: border-box;

	margin: 0;

}

.qp-suggest-cat-header {

	background: #ffeb3b !important;

	color: #000 !important;

	font-size: 11px !important;

	font-weight: bold !important;

	text-align: center !important;

	text-transform: uppercase !important;

	letter-spacing: 0.6px;

	padding: 8px 6px !important;

	border: 1px solid #595959 !important;

	height: auto !important;

}

.qp-prod-card-empty td {

	min-height: 0;

}

.qp-suggest-grid {

	margin: 0 -8px;

}

.qp-suggest-grid .qp-suggest-col {

	padding: 0 8px 16px;

	float: left;

	width: 20%;

}

@media (max-width: 1200px) {

	.qp-suggest-grid .qp-suggest-col { width: 25%; }

}

@media (max-width: 992px) {

	.qp-suggest-grid .qp-suggest-col { width: 33.333%; }

}

@media (max-width: 768px) {

	.qp-suggest-grid .qp-suggest-col { width: 50%; }

}

.qp-suggest-card {

	border: 1px solid #ddd;

	border-radius: 6px;

	padding: 10px 8px;

	text-align: center;

	height: 100%;

	background: #fff;

	box-shadow: 0 1px 3px rgba(0,0,0,0.06);

	transition: border-color 0.2s, box-shadow 0.2s;

	position: relative;

}

.qp-suggest-card .qp-suggest-info {

	position: relative;

	text-align: left;

}

.qp-suggest-card .qp-suggest-code-side {

	position: absolute;

	top: 0;

	right: 0;

	font-size: 9px;

	color: #777;

	font-weight: normal;

}

.qp-suggest-card:hover {

	border-color: #3598dc;

	box-shadow: 0 2px 8px rgba(53,152,220,0.15);

}

.qp-suggest-card .qp-suggest-img-wrap {

	height: 95px;

	line-height: 95px;

	margin-bottom: 8px;

}

.qp-suggest-card img {

	max-height: 90px;

	max-width: 100%;

	vertical-align: middle;

	object-fit: contain;

}

.qp-suggest-card .qp-suggest-name {

	font-size: 11px;

	font-weight: bold;

	min-height: 34px;

	line-height: 1.3;

	color: #111;

	margin: 0 24px 6px 0;

}

.qp-suggest-card .qp-suggest-price-row {

	display: flex;

	align-items: center;

	gap: 6px;

	flex-wrap: wrap;

}

.qp-suggest-card .qp-suggest-rate {

	font-size: 12px;

	color: #1a7f37;

	font-weight: bold;

}

.qp-suggest-card .qp-suggest-disc-badge {

	display: inline-flex;

	align-items: center;

	justify-content: center;

	min-width: 30px;

	height: 30px;

	padding: 0 5px;

	border-radius: 50%;

	background: #e74c3c;

	color: #fff;

	font-size: 10px;

	font-weight: bold;

	line-height: 1;

}

.qp-suggest-cat-title {

	background: #ffeb3b;

	color: #000;

	font-size: 13px;

	font-weight: bold;

	text-align: center;

	text-transform: uppercase;

	padding: 8px 10px;

	margin: 12px 0 10px;

	border: 1px solid #d4c400;

	border-radius: 3px;

	clear: both;

}

.qp-suggest-card .qp-suggest-add-btn {

	margin-top: 8px;

}

@media print {

	.quote-suggest-body {

		page-break-before: auto !important;

		break-before: auto;

	}

	.quote-footer-wrap,
	.quote-footer-table {
		page-break-inside: avoid;
		break-inside: avoid;
	}

	.qp-suggest-print-section {

		page-break-inside: auto;

		-webkit-print-color-adjust: exact;

		print-color-adjust: exact;

	}

	.qp-suggest-print-grid {

		border: 1px solid #595959 !important;

	}

	.qp-suggest-product-row {

		page-break-inside: auto !important;

		break-inside: auto !important;

		display: table-row !important;

	}

	.qp-suggest-print-grid td.qp-suggest-print-cell {

		min-height: 0 !important;

		height: auto !important;

		overflow: hidden !important;

		page-break-inside: avoid !important;

		break-inside: avoid-page !important;

	}

	.qp-suggest-print-box,
	.qp-suggest-cell-inner,
	.qp-prod-card {

		min-height: 0 !important;

		height: auto !important;

		overflow: hidden !important;

		page-break-inside: avoid !important;

		break-inside: avoid-page !important;

	}

	.qp-prod-badge-row {

		height: 22px !important;

		padding: 2px 6px 0 !important;

	}

	.qp-prod-img-cell {

		height: 44px !important;

		padding: 2px !important;

	}

	.qp-prod-img-box {

		height: 40px !important;

		line-height: 40px !important;

	}

	.qp-prod-img {

		max-height: 36px !important;

	}

	.qp-prod-name-cell {

		min-height: 22px !important;

		max-height: 28px !important;

		font-size: 9.5px !important;

		padding: 1px 6px 0 !important;

	}

	.qp-prod-price-cell {

		padding: 0 6px 4px !important;

	}

	.qp-prod-code-cell {

		padding: 2px 6px 0 !important;

		font-size: 10px !important;

	}

	.qp-prod-price-cell,
	.qp-prod-price-wrap,
	.qp-prod-price-line,
	.qp-prod-price,
	.qp-prod-unit,
	.qp-prod-code-cell,
	.qp-prod-name-cell {

		overflow: visible !important;

		-webkit-print-color-adjust: exact !important;

		print-color-adjust: exact !important;

	}

	.qp-prod-price-line,
	.qp-prod-price {

		color: #0a5c24 !important;

		font-weight: bold !important;

	}

	.qp-prod-unit {

		color: #333333 !important;

		font-weight: 600 !important;

	}

	.qp-prod-code-cell {

		color: #555555 !important;

	}

	.qp-prod-name-cell {

		color: #000000 !important;

	}

	.qp-suggest-print-cell-empty {

		display: none !important;

		height: 0 !important;

		border: none !important;

		padding: 0 !important;

		line-height: 0 !important;

	}

	.qp-suggest-cat-header {

		page-break-after: auto;

		break-after: auto;

		padding: 4px 6px !important;

		line-height: 1.2 !important;

	}

	.qp-suggest-print-header {

		padding: 6px 8px !important;

	}

	.qp-suggest-print-grid {

		border-collapse: separate !important;

		border-spacing: 0 !important;

	}

	.qp-suggest-cell-inner {

		padding-left: 8px !important;

		padding-right: 8px !important;

		padding-bottom: 4px !important;

		box-sizing: border-box !important;

	}

	.qp-prod-badge-row {

		padding: 4px 8px 0 !important;

		text-align: right !important;

	}

	.qp-prod-badge-bar {

		display: flex !important;

		align-items: center !important;

		justify-content: flex-end !important;

		gap: 6px !important;

	}

	.qp-prod-disc-label {

		border: 1px solid #d9534f !important;

		color: #d9534f !important;

		-webkit-print-color-adjust: exact !important;

		print-color-adjust: exact !important;

	}

	.qp-prod-code-cell {

		padding: 4px 2px 0 !important;

	}

	.qp-prod-name-cell {

		padding: 3px 2px 0 !important;

	}

	.qp-prod-price-cell {

		padding: 0 2px 10px !important;

	}

		.qp-prod-disc,
	.qp-suggest-cat-header,
	.qp-suggest-print-header {

		-webkit-print-color-adjust: exact;

		print-color-adjust: exact;

	}

}

</style>';

	}

}



if (!function_exists('armor_quotation_pi_build_suggest_item_from_pwp')) {

	function armor_quotation_pi_build_suggest_item_from_pwp($db, $pwp, $catno, $customerId = 0)

	{

		$proId = (int) $pwp['product_id'];

		if ($proId <= 0) {

			return null;

		}

		$order_unit_arr = array('-1' => 'Box', '-2' => 'Strip', '-3' => 'Pallet', '1' => 'Caret', '2' => 'Big Box', '100' => 'Nos');

		$name = armor_quotation_pi_get_product_name($db, $proId);

		$imagePath = $db->rp_getValue('product', 'image_path', "id='" . $proId . "' AND isDelete=0", 0);

		$itemOrderUnit = $db->rp_getValue('product', 'unit_id', "id='" . $proId . "' AND isDelete=0", 0);

		$unitName = isset($order_unit_arr[$itemOrderUnit]) ? $order_unit_arr[$itemOrderUnit] : 'Nos';

		$rate = isset($pwp['price']) ? (float) $pwp['price'] : 0;

		if ($customerId > 0) {

			$priceListId = (int) $db->rp_getValue('executive', 'price_list_id', "id='" . (int) $customerId . "'", 0);

			$weightId = (int) $pwp['weight_id'];

			if ($priceListId > 0 && $weightId !== 0) {

				$listPrice = $db->rp_getValue(

					'product_price_list',

					'discounted_price',

					"pid='" . $proId . "' AND weight_id='" . $weightId . "' AND price_list_id='" . $priceListId . "' AND isDelete=0",

					0

				);

				if ($listPrice !== '' && $listPrice !== null && (float) $listPrice > 0) {

					$rate = (float) $listPrice;

				}

			}

		}

		return array(

			'product_id' => $proId,

			'weight_id' => (int) $pwp['weight_id'],

			'option_value' => (int) $pwp['id'],

			'catno' => (string) $catno,

			'name' => html_entity_decode(strip_tags($name ? $name : $catno), ENT_QUOTES, 'UTF-8'),

			'rate' => $rate,

			'rate_label' => number_format($rate, 2),

			'discount_per' => armor_quotation_pi_suggest_display_discount_percent(),

			'image' => armor_quotation_pi_product_image_url($imagePath ? $imagePath : ''),

			'pro_id' => $proId,

			'item_order_unit' => $itemOrderUnit,

			'unit_name' => $unitName,

		);

	}

}



if (!function_exists('armor_quotation_pi_get_suggest_products')) {

	function armor_quotation_pi_get_suggest_products($db, $customerId, $excludeProductIds = array())

	{

		$productObj = null;

		if (!armor_quotation_pi_is_print_request()) {

			require_once dirname(__FILE__) . '/product.class.php';

			$productObj = new Product();

		}

		$customerId = (int) $customerId;

		$exclude = array();

		if (is_array($excludeProductIds)) {

			foreach ($excludeProductIds as $pid) {

				$pid = (int) $pid;

				if ($pid > 0) {

					$exclude[$pid] = 1;

				}

			}

		}



		$catnos = armor_quotation_pi_suggest_catnos($db);

		$items = array();

		$seenCatno = array();



		foreach ($catnos as $catno) {

			$catno = armor_quotation_pi_normalize_catno($catno);

			if ($catno === '' || isset($seenCatno[$catno])) {

				continue;

			}

			$pwp = armor_quotation_pi_resolve_pwp_row($db, $catno);

			if (!$pwp) {

				continue;

			}

			$proId = (int) $pwp['product_id'];

			if ($proId <= 0 || isset($exclude[$proId])) {

				continue;

			}

			$item = armor_quotation_pi_build_suggest_item_from_pwp($db, $pwp, $catno, $customerId);

			if ($item === null) {

				continue;

			}

			if (!armor_quotation_pi_is_print_request()) {

				$details = $productObj->aj_getProductDetail($proId, $customerId);

				if (!empty($details)) {

					foreach ($details as $detail) {

						if (armor_quotation_pi_normalize_catno($detail['catno']) !== $catno) {

							continue;

						}

						$rate = isset($detail['orignal_price']) ? (float) $detail['orignal_price'] : (float) $item['rate'];

						if (isset($detail['sell_price']) && (float) $detail['sell_price'] > 0) {

							$rate = (float) $detail['sell_price'];

						}

						$originalPrice = isset($detail['orignal_price']) ? (float) $detail['orignal_price'] : $rate;

						$discountPer = 0;

						if (isset($detail['discountPer']) && $detail['discountPer'] !== '' && $detail['discountPer'] !== null) {

							$discountPer = (float) $detail['discountPer'];

						}

						if ($discountPer <= 0 && $originalPrice > 0 && $rate > 0 && $rate < $originalPrice) {

							$discountPer = round((($originalPrice - $rate) / $originalPrice) * 100);

						}

						$item['rate'] = $rate;

						$item['rate_label'] = number_format($rate, 2);

						$item['discount_per'] = max(0, (int) round($discountPer));

						if (!empty($detail['name1'])) {

							$item['name'] = html_entity_decode(strip_tags($detail['name1']), ENT_QUOTES, 'UTF-8');

						}

						if (!empty($detail['image_path'])) {

							$item['image'] = armor_quotation_pi_product_image_url($detail['image_path']);

						}

						if (isset($detail['weight_id'])) {

							$item['weight_id'] = (int) $detail['weight_id'];

						}

						if (isset($detail['id'])) {

							$item['option_value'] = (int) $detail['id'];

						}

						break;

					}

				}

			}

			$items[] = $item;

			$seenCatno[$catno] = 1;

		}



		return $items;

	}

}



if (!function_exists('armor_quotation_pi_build_option_html')) {

	function armor_quotation_pi_build_option_html($db, $customerId, $productId, $weightId)

	{

		require_once dirname(__FILE__) . '/product.class.php';

		$product = new Product();

		$order_unit_arr = array('-1' => 'Box', '-2' => 'Strip', '-3' => 'Pallet', '1' => 'Caret', '2' => 'Big Box', '100' => 'Nos');

		$product_list_r = $db->rp_getData('product', '*', "id='" . (int) $productId . "' AND isDelete=0", '', 0);

		if (!$product_list_r) {

			return '';

		}

		$product_list_r = mysqli_fetch_assoc($product_list_r);

		$current_prodcuts = $product->aj_getProductDetail($product_list_r['id'], $customerId);

		if (empty($current_prodcuts)) {

			return '';

		}

		foreach ($current_prodcuts as $product_detail) {

			if ((int) $product_detail['weight_id'] !== (int) $weightId) {

				continue;

			}

			$cat_no = $db->rp_getValue('product_weight_price', 'catno', "product_id='" . $product_detail['pro_id'] . "' AND weight_id='" . $product_detail['weight_id'] . "' AND isDelete=0");

			$stock_qty = $db->rp_getValue('product_weight_price', 'stock_qty', "product_id='" . $product_detail['pro_id'] . "' AND isDelete=0");

			$hsncode = $db->rp_getValue('product', 'hsn_code', "id='" . $product_detail['pro_id'] . "' AND isDelete=0");

			$top_cat_name = $db->rp_getValue('top_category_master', 'name', "id='" . $product_list_r['tcid'] . "' AND isDelete=0", 0);

			$category_name = $db->rp_getValue('category_master', 'name', "id='" . $product_list_r['cid'] . "' AND isDelete=0", 0);

			$gst = $db->rp_getValue('product', 'igst', "id='" . $product_detail['pro_id'] . "' AND isDelete=0", 0);

			$pro_master_price = $product_detail['orignal_price'];

			$item_order_unit = $db->rp_getValue('product', 'unit_id', "id='" . $product_detail['pro_id'] . "' AND isDelete=0");

			$unit_name = isset($order_unit_arr[$item_order_unit]) ? $order_unit_arr[$item_order_unit] : '';

			$last_quotation_id = $db->rp_getValue('quotation_detail', 'id', "customer_id='" . (int) $customerId . "' AND isDelete=0 ORDER BY id DESC", 0);

			$last_quotation_price = $db->rp_getValue('quotation_product_item', 'original_price', "quotation_id='" . $last_quotation_id . "' AND isDelete=0");



			return '<option data-weight-id="' . (int) $product_detail['weight_id'] . '" data-name="' . htmlspecialchars($product_detail['name1'], ENT_QUOTES) . '" data-weight="' . (int) $product_detail['weight_id'] . '" data-pricelist="' . $product_detail['sell_price'] . '" data-inner_size="' . $product_detail['bag_qty'] . '" data-outer_size="' . $product_detail['box_qty'] . '" data-pro_id="' . (int) $product_detail['pro_id'] . '" value="' . (int) $product_detail['id'] . '" data-discount="' . $product_detail['discountPer'] . '" data-original-price="' . $product_detail['orignal_price'] . '" data-stock_qty="' . $product_detail['qty'] . '" data-last_quot_price="' . $last_quotation_price . '" data-brand-id="' . $product_detail['brand_id'] . '" data-catno="' . $cat_no . '" data-hsncode="' . $hsncode . '" data-stock="' . $stock_qty . '" data-cid="' . $product_list_r['cid'] . '" data-tcid="' . $product_list_r['tcid'] . '" data-topcat_name="' . htmlspecialchars($top_cat_name, ENT_QUOTES) . '" data-cat_name="' . htmlspecialchars($category_name, ENT_QUOTES) . '" data-gst="' . $gst . '" data-unit_name="' . $unit_name . '" data-pro_master_price="' . $pro_master_price . '" data-is_including="' . $product_detail['is_including'] . '" data-item_order_unit="' . $item_order_unit . '" data-is_premium="' . $product_detail['is_premium'] . '">' . htmlspecialchars($product_detail['product_name'] . ' - ' . $cat_no) . '</option>';

		}

		return '';

	}

}



if (!function_exists('armor_quotation_pi_render_print_item')) {

	function armor_quotation_pi_render_print_item($item)

	{

		$img = htmlspecialchars($item['image'], ENT_QUOTES);

		$name = htmlspecialchars($item['name'], ENT_QUOTES);

		$catno = htmlspecialchars($item['catno'], ENT_QUOTES);

		$curr = defined('CURR') ? CURR : 'INR';

		$unit = isset($item['unit_name']) ? htmlspecialchars($item['unit_name'], ENT_QUOTES) : 'Nos';

		$defaultImg = htmlspecialchars(armor_quotation_pi_product_image_url(''), ENT_QUOTES);

		$discountPer = armor_quotation_pi_suggest_display_discount_percent();



		$html = '<table class="qp-prod-card" cellpadding="0" cellspacing="0" width="100%">';

		$html .= '<tr><td class="qp-prod-badge-row" valign="top" style="padding:1px 4px 0 !important;">';

		$html .= '<div class="qp-prod-badge-bar" style="display:flex;align-items:center;justify-content:flex-end;gap:4px;">';

		$html .= '<span class="qp-prod-disc-label">Discount</span>';

		$html .= '<span class="qp-prod-disc-wrap"><span class="qp-prod-disc">' . (int) $discountPer . '%</span></span>';

		$html .= '</div>';

		$html .= '</td></tr>';

		$html .= '<tr><td class="qp-prod-img-cell" align="center" valign="middle" style="padding:1px !important;">';

		$html .= '<div class="qp-prod-img-box"><img src="' . $img . '" alt="" class="qp-prod-img" onerror="this.onerror=null;this.src=\'' . $defaultImg . '\';"></div>';

		$html .= '</td></tr>';

		$html .= '<tr><td class="qp-prod-code-cell" valign="top" style="padding:1px 4px 0 !important;color:#555 !important;"><div class="qp-prod-text-inner">' . $catno . '</div></td></tr>';

		$html .= '<tr><td class="qp-prod-name-cell" valign="top" style="padding:1px 4px 0 !important;color:#000 !important;"><div class="qp-prod-text-inner">' . $name . '</div></td></tr>';

		$html .= '<tr><td class="qp-prod-price-cell" align="center" valign="bottom" style="padding:1px 4px 4px !important;overflow:visible !important;">';

		$html .= '<div class="qp-prod-price-wrap">';

		$html .= '<span class="qp-prod-price-line" style="color:#0a5c24 !important;font-size:10px;font-weight:bold;line-height:1.2;">' . $curr . ' ' . $item['rate_label'] . ' <span class="qp-prod-unit" style="color:#333 !important;font-size:9px;font-weight:600;">/ ' . $unit . '</span></span>';

		$html .= '</div>';

		$html .= '</td></tr>';

		$html .= '</table>';

		return $html;

	}

}



if (!function_exists('armor_quotation_pi_render_print_empty_cell')) {

	function armor_quotation_pi_render_print_empty_cell()

	{

		return '<table class="qp-prod-card qp-prod-card-empty" cellpadding="0" cellspacing="0" width="100%"><tr><td>&nbsp;</td></tr></table>';

	}

}



if (!function_exists('armor_quotation_pi_render_suggest_grid')) {

	function armor_quotation_pi_render_suggest_grid($items, $clickable = true)

	{

		if (empty($items)) {

			return '<div class="alert alert-info">All suggested products are already added above.</div>';

		}

		$curr = defined('CURR') ? CURR : 'INR';

		$groups = armor_quotation_pi_group_items_by_category($items);

		$html = '<div class="qp-suggest-grid-wrap">';

		foreach ($groups as $group) {

			$html .= '<div class="qp-suggest-cat-title">' . htmlspecialchars($group['title'], ENT_QUOTES) . '</div>';

			$html .= '<div class="qp-suggest-grid clearfix">';

			foreach ($group['items'] as $item) {

				$img = htmlspecialchars($item['image'], ENT_QUOTES);

				$name = htmlspecialchars($item['name'], ENT_QUOTES);

				$unit = isset($item['unit_name']) ? htmlspecialchars($item['unit_name'], ENT_QUOTES) : 'Nos';

				$discountPer = armor_quotation_pi_suggest_display_discount_percent();

				$html .= '<div class="qp-suggest-col">';

				$html .= '<div class="qp-suggest-card">';

				if ($clickable) {

					$html .= '<a href="javascript:void(0)" class="qp-suggest-add" data-product-id="' . (int) $item['product_id'] . '" data-weight-id="' . (int) $item['weight_id'] . '" data-catno="' . htmlspecialchars($item['catno'], ENT_QUOTES) . '" style="display:block;color:inherit;text-decoration:none;">';

				}

				$html .= '<div class="qp-suggest-img-wrap"><img src="' . $img . '" alt="" onerror="this.onerror=null;this.src=\'' . htmlspecialchars(armor_quotation_pi_product_image_url(''), ENT_QUOTES) . '\';"></div>';

				$html .= '<div class="qp-suggest-info">';

				$html .= '<span class="qp-suggest-code-side">' . htmlspecialchars($item['catno'], ENT_QUOTES) . '</span>';

				$html .= '<div class="qp-suggest-name">' . $name . '</div>';

				$html .= '<div class="qp-suggest-price-row">';

				$html .= '<span class="qp-suggest-rate">' . $curr . ' ' . $item['rate_label'] . ' / ' . $unit . '</span>';

				$html .= '<span class="qp-suggest-disc-badge">' . (int) $discountPer . '%</span>';

				$html .= '</div></div>';

				if ($clickable) {

					$html .= '<div class="qp-suggest-add-btn"><span class="label label-primary"><i class="fa fa-plus"></i> Add to Quote</span></div></a>';

				}

				$html .= '</div></div>';

			}

			$html .= '</div>';

		}

		$html .= '</div>';

		return $html;

	}

}



if (!function_exists('armor_quotation_pi_render_print_block')) {

	function armor_quotation_pi_render_print_block($db, $customerId, $excludeProductIds = array(), $includeStyles = true)

	{

		$items = armor_quotation_pi_get_suggest_products($db, $customerId, $excludeProductIds);

		if (empty($items)) {

			return '';

		}



		$cols = 4;

		$groups = armor_quotation_pi_group_items_by_category($items);

		$html = '';

		if ($includeStyles) {

			$html .= armor_quotation_pi_suggest_styles();

		}

		$html .= '<table class="qp-suggest-wrap-table" cellpadding="0" cellspacing="0"><tr><td>';

		$html .= '<div class="qp-suggest-print-section">';

		$html .= '<div class="qp-suggest-print-header">';

		$html .= '<div class="qp-suggest-print-title">Suggested Product Range</div>';

		$html .= '<div class="qp-suggest-print-subtitle">Please mention Product Code when placing your order</div>';

		$html .= '</div>';

		$html .= '<table class="qp-suggest-print-grid" cellpadding="0" cellspacing="0"><tbody>';



		foreach ($groups as $group) {

			$html .= '<tr><td colspan="' . $cols . '" class="qp-suggest-cat-header">' . htmlspecialchars($group['title'], ENT_QUOTES) . '</td></tr>';

			$chunks = array_chunk($group['items'], $cols);

			foreach ($chunks as $row) {

				$html .= '<tr class="qp-suggest-product-row">';

				for ($i = 0; $i < $cols; $i++) {

					if (!isset($row[$i])) {

						$html .= '<td class="qp-suggest-print-cell qp-suggest-print-cell-empty"></td>';

						continue;

					}

					$html .= '<td class="qp-suggest-print-cell">';

					$html .= '<div class="qp-suggest-print-box">';

					$html .= '<div class="qp-suggest-cell-inner" style="padding-left:12px;padding-right:12px;box-sizing:border-box;">';

					$html .= armor_quotation_pi_render_print_item($row[$i]);

					$html .= '</div>';

					$html .= '</div>';

					$html .= '</td>';

				}

				$html .= '</tr>';

			}

		}



		$html .= '</tbody></table></div>';

		$html .= '</td></tr></table>';

		return $html;

	}

}



if (!function_exists('armor_quotation_pi_echo_suggest_block_for_quotation')) {

	function armor_quotation_pi_echo_suggest_block_for_quotation($db, $quotationId, $includeStyles = true)

	{

		$quotationId = (int) $quotationId;

		if ($quotationId <= 0) {

			return;

		}

		$customerId = (int) $db->rp_getValue('quotation_detail', 'customer_id', "id='" . $quotationId . "' AND isDelete=0", 0);

		$excludeProIds = array();

		$qItemsRes = $db->rp_getData('quotation_product_item', 'pro_id', "quotation_id='" . $quotationId . "' AND isDelete=0", '', 0);

		if ($qItemsRes) {

			while ($qi = mysqli_fetch_assoc($qItemsRes)) {

				$excludeProIds[] = (int) $qi['pro_id'];

			}

		}

		echo armor_quotation_pi_render_print_block($db, $customerId, $excludeProIds, $includeStyles);

	}

}



if (!function_exists('armor_quotation_pi_echo_suggest_block_for_order')) {

	function armor_quotation_pi_echo_suggest_block_for_order($db, $orderId, $includeStyles = true)

	{

		$orderId = (int) $orderId;

		if ($orderId <= 0) {

			return;

		}

		$customerId = (int) $db->rp_getValue('orders', 'customer_id', "id='" . $orderId . "' AND isDelete=0", 0);

		$excludeProIds = array();

		$oItemsRes = $db->rp_getData('order_product_item', 'pro_id', "order_id='" . $orderId . "' AND isDelete=0", '', 0);

		if ($oItemsRes) {

			while ($oi = mysqli_fetch_assoc($oItemsRes)) {

				$excludeProIds[] = (int) $oi['pro_id'];

			}

		}

		echo armor_quotation_pi_render_print_block($db, $customerId, $excludeProIds, $includeStyles);

	}

}



if (!function_exists('armor_quotation_pi_suggest_pi_view_overrides')) {

	function armor_quotation_pi_suggest_pi_view_overrides()

	{

		return '<style type="text/css">

.quote-suggest-body {

	width: 100% !important;

	max-width: 100%;

	margin: 0;

	box-sizing: border-box;

	background: #fff;

	border: none;

	border-top: 1px solid #595959;

}

.quote-suggest-body table {

	width: 100% !important;

	max-width: 100% !important;

	margin: 0 !important;

	border-collapse: collapse;

	table-layout: auto;

}

.quote-suggest-body .qp-suggest-print-grid {

	table-layout: fixed !important;

	width: 100% !important;

}

.quote-suggest-body .qp-suggest-print-grid td.qp-suggest-print-cell {

	width: 25% !important;

	min-height: 0;

	height: auto !important;

	padding: 0 !important;

	vertical-align: top;

	overflow: hidden;

}

.quote-suggest-body td,

.quote-suggest-body th {

	height: auto !important;

	padding: 0 !important;

}

.quote-suggest-body .qp-suggest-wrap-table,

.quote-suggest-body .qp-suggest-wrap-table td {

	border: none !important;

	padding: 0 !important;

}

.quote-suggest-body table.qp-suggest-print-grid td.qp-suggest-print-cell,

.quote-suggest-body table.qp-suggest-print-grid td.qp-suggest-cat-header {

	border: 1px solid #595959 !important;

}

.quote-suggest-body .qp-prod-card,

.quote-suggest-body .qp-prod-card td {

	border: none !important;

	height: auto !important;

}

.quote-suggest-body .qp-prod-img-cell {

	border-bottom: 1px solid #e8e8e8 !important;

	height: 36px !important;

	padding: 1px !important;

}

.quote-suggest-body .qp-prod-badge-row {

	height: 18px !important;

	padding: 1px 4px 0 !important;

}

.quote-suggest-body .qp-prod-code-cell {

	padding: 1px 4px 0 !important;

	font-size: 9.5px !important;

	line-height: 1.1 !important;

	height: auto !important;

}

.quote-suggest-body .qp-prod-name-cell {

	padding: 1px 4px 0 !important;

	font-size: 8.5px !important;

	line-height: 1.1 !important;

	height: auto !important;

	min-height: 18px !important;

	max-height: 24px !important;

}

.quote-suggest-body .qp-prod-price-cell {

	padding: 1px 4px 4px !important;

	height: auto !important;

}

.quote-suggest-body .qp-prod-price-line,

.quote-suggest-body .qp-prod-price {

	color: #0a5c24 !important;

	font-weight: bold;

	font-size: 10px !important;

	line-height: 1.2 !important;

}

.quote-suggest-body .qp-prod-unit {

	color: #333 !important;

	font-size: 9px !important;

	font-weight: 600;

}

.quote-suggest-body .qp-suggest-print-header {

	border: none !important;

	border-bottom: 1px solid #595959 !important;

}

</style>';

	}

}



if (!function_exists('armor_quotation_pi_order_view_layout_styles')) {

	function armor_quotation_pi_order_view_layout_styles()

	{

		$headerH = defined('HEADER_IMAGE_HEIGHT') ? (int) HEADER_IMAGE_HEIGHT : 184;

		return '<style type="text/css">

.main-container {

	padding: 20px;

	width: 100% !important;

	max-width: 980px;

	background-color: #FFF;

	margin: auto;

	box-sizing: border-box;

}

.quote-wrap {

	width: 100%;

	border: 1px solid #595959;

	box-sizing: border-box;

	background: #fff;

}

.quote-main-body,

.quote-suggest-body,

.quote-summary-body {

	width: 100%;

	box-sizing: border-box;

	background: #fff;

}

.quote-main-body table,

.quote-summary-body table {

	margin: 0 !important;

	width: 100% !important;

	max-width: 100% !important;

	border-collapse: collapse !important;

}

.quote-main-body table {

	border: none !important;

}

.quote-main-body > table + table,

.quote-main-body .product-items-table,

.quote-main-body .quote-footer-wrap {

	margin-top: 0 !important;

}

.quote-footer-wrap {

	margin: 0;

	padding: 0;

	border-top: 1px solid #595959;

}

.quote-summary-totals-block {

	width: 100%;

}

.quote-summary-details-table,

.quote-summary-amounts-table {

	width: 100% !important;

	border-collapse: collapse !important;

}

.quote-summary-amounts-cell {

	padding: 0 !important;

	vertical-align: top;

}

.quote-summary-info-cell,

.quote-summary-terms-cell {

	vertical-align: top;

}

.quote-summary-amounts-table td,

.quote-summary-amounts-table th {

	border: 1px solid #595959;

}

.product-items-table .product-item-row td,

.product-items-table .product-filler-row td {

	height: 30px;

	vertical-align: middle !important;

}

.product-items-table .model {

	text-align: left !important;

}

.quote-main-body .product-items-table {

	table-layout: fixed;

}

.quote-summary-body {

	border-top: 1px solid #595959;

}

.quote-suggest-body {

	border-top: 1px solid #595959;

	margin: 0;

	padding: 0;

}

.quote-table,

table.quote-table {

	width: 100% !important;

	max-width: 100%;

	border-collapse: collapse;

	box-sizing: border-box;

}

.quote-table,

.quote-table td,

.quote-table th {

	border: 1px solid #595959;

}

.quote-table td,

.quote-table th {

	padding: 5px;

	height: auto;

	vertical-align: top;

}

.quote-header-cell,

.quote-footer-cell {

	padding: 0 !important;

	margin: 0 !important;

	line-height: 0 !important;

	font-size: 0 !important;

	text-align: center;

	vertical-align: top;

	width: 100%;

	border-left: none !important;

	border-right: none !important;

	background: #fff;

}

.quote-header-cell {

	border-top: none !important;

	border-bottom: 1px solid #595959 !important;

}

.quote-footer-cell {

	border-top: 1px solid #595959 !important;

	border-bottom: none !important;

}

.quote-header-img,

.quote-footer-img {

	width: 100% !important;

	max-width: 100% !important;

	height: auto !important;

	max-height: ' . $headerH . 'px;

	object-fit: contain;

	object-position: center center;

	display: block;

	padding: 0 !important;

	margin: 0 auto;

	border: 0;

}

.product-items-table td,

.product-items-table th {

	vertical-align: middle !important;

}

.quote-suggest-body table.qp-suggest-print-grid,

.quote-suggest-body .qp-prod-card,

.quote-suggest-body .qp-prod-card td,

.quote-suggest-body .qp-suggest-wrap-table,

.quote-suggest-body .qp-suggest-wrap-table td {

	border-color: transparent;

}

.quote-suggest-body table.qp-suggest-print-grid td.qp-suggest-print-cell,

.quote-suggest-body table.qp-suggest-print-grid td.qp-suggest-cat-header {

	border: 1px solid #595959 !important;

}

.quote-suggest-body .qp-suggest-print-grid td.qp-suggest-print-cell {

	min-height: 0;

	height: auto;

	padding: 0 !important;

	overflow: hidden;

}

.quote-suggest-body .qp-prod-card,

.quote-suggest-body .qp-prod-card td {

	border: none !important;

}

.quote-suggest-body .qp-prod-img-cell {

	border-bottom: 1px solid #e8e8e8 !important;

	height: 36px !important;

	padding: 1px !important;

}

.quote-suggest-body .qp-prod-img {

	max-height: 32px;

	max-width: 96%;

	object-fit: contain;

}

.qp-suggest-print-header {

	border: none !important;

	border-bottom: 1px solid #595959 !important;

}

@media print {

	.main-container {

		padding: 10px !important;

		max-width: 100% !important;

		width: 100% !important;

	}

	.quote-suggest-body .qp-suggest-print-grid td.qp-suggest-print-cell,

	.quote-suggest-body .qp-prod-card,

	.quote-suggest-body .qp-suggest-print-box,

	.quote-suggest-body .qp-suggest-cell-inner {

		min-height: 0 !important;

		height: auto !important;

		page-break-inside: avoid !important;

		break-inside: avoid-page !important;

	}

}

</style>';

	}

}



if (!function_exists('armor_quotation_pi_suggest_pi_view_head_assets')) {

	function armor_quotation_pi_suggest_pi_view_head_assets()

	{

		return armor_quotation_pi_suggest_styles()

			. armor_quotation_pi_suggest_pi_view_overrides()

			. armor_quotation_pi_order_view_layout_styles();

	}

}

