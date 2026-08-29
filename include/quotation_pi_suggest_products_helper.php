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



if (!function_exists('armor_quotation_pi_suggest_default_catnos')) {

	function armor_quotation_pi_suggest_default_catnos()

	{

		return array(

			'2327', '2344', '3073', '2706', '2494', '2860', '2863', '2915', '2037', '2184',

			'2803', '2384', '2359', '2352', '2794', '2659', '2158', '2149', '2424', '2612',

			'2446', '2678', '2010', '2024', '2071', '2700', '2668', '2487', '2646', '3004',

			'2980', '2692', '2691', '2730', '2746', '2595', '2787', '2710', '2967', '2650',

			'2719', '2711', '2576', '2034', '2754', '2906', '3077',

		);

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



if (!function_exists('armor_quotation_pi_suggest_catnos')) {

	function armor_quotation_pi_suggest_catnos($db = null)

	{

		if ($db !== null) {

			armor_quotation_pi_suggest_ensure_table($db);

			$table = armor_quotation_pi_suggest_table();

			if ($db->tableExists($table)) {

				$res = $db->rp_getData($table, 'catno', 'isDelete=0 AND isActive=1', 'display_order ASC, id ASC', 0);

				if ($res && mysqli_num_rows($res) > 0) {

					$catnos = array();

					while ($row = mysqli_fetch_assoc($res)) {

						$catno = trim((string) $row['catno']);

						if ($catno !== '') {

							$catnos[] = $catno;

						}

					}

					if (!empty($catnos)) {

						return $catnos;

					}

				}

			}

		}

		return armor_quotation_pi_suggest_default_catnos();

	}

}



if (!function_exists('armor_quotation_pi_lookup_product_by_catno')) {

	function armor_quotation_pi_lookup_product_by_catno($db, $catno)

	{

		$catno = trim((string) $catno);

		if ($catno === '') {

			return null;

		}

		$catnoEsc = $db->clean($catno);

		$pwpRes = $db->rp_getData(

			'product_weight_price',

			'*',

			"isDelete=0 AND catno='" . $catnoEsc . "'",

			'id ASC',

			0

		);

		if (!$pwpRes) {

			return null;

		}

		$pwp = mysqli_fetch_assoc($pwpRes);

		if (!$pwp) {

			return null;

		}

		$proId = (int) $pwp['product_id'];

		$name = $db->rp_getValue('product', 'name1', "id='" . $proId . "' AND isDelete=0", 0);

		$imagePath = $db->rp_getValue('product', 'image_path', "id='" . $proId . "' AND isDelete=0", 0);

		return array(

			'catno' => $catno,

			'product_id' => $proId,

			'name' => $name ? html_entity_decode(strip_tags($name), ENT_QUOTES, 'UTF-8') : $catno,

			'image' => armor_quotation_pi_product_image_url($imagePath ? $imagePath : ''),

		);

	}

}



if (!function_exists('armor_quotation_pi_get_admin_suggest_rows')) {

	function armor_quotation_pi_get_admin_suggest_rows($db)

	{

		armor_quotation_pi_suggest_ensure_table($db);

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

	border: 1px solid #595959 !important;

	margin: 0;

}

.qp-suggest-wrap-table td {

	padding: 0 !important;

	border-left: 1px solid #595959 !important;

	border-right: 1px solid #595959 !important;

	border-top: none !important;

	border-bottom: none !important;

	vertical-align: top;

}

.quote-wrap .qp-suggest-print-section,

.main-container .qp-suggest-print-section {

	border-left: 1px solid #595959;

	border-right: 1px solid #595959;

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

	margin-bottom: 10px;

	padding: 8px 6px;

	border: 1px solid #595959;

	background: #f5f5f5;

}

.qp-suggest-print-title {

	font-size: 15px;

	font-weight: bold;

	text-transform: uppercase;

	letter-spacing: 0.5px;

	color: #222;

}

.qp-suggest-print-subtitle {

	font-size: 11px;

	color: #555;

	margin-top: 4px;

}

.qp-suggest-print-grid {

	width: 100%;

	border-collapse: collapse;

	table-layout: fixed;

}

.qp-suggest-print-grid td.qp-suggest-print-cell {

	width: 25%;

	vertical-align: top;

	border: 1px solid #595959;

	padding: 6px;

}

.qp-suggest-print-card {

	text-align: center;

	min-height: 155px;

}

.qp-suggest-print-img-wrap {

	height: 88px;

	margin-bottom: 6px;

	display: flex;

	align-items: center;

	justify-content: center;

	overflow: hidden;

}

.qp-suggest-print-img {

	max-width: 100%;

	max-height: 84px;

	width: auto;

	height: auto;

	object-fit: contain;

	object-position: center center;

}

.qp-suggest-print-code {

	font-size: 13px;

	font-weight: bold;

	color: #000;

	margin-bottom: 4px;

}

.qp-suggest-print-name {

	font-size: 10px;

	line-height: 1.35;

	color: #333;

	min-height: 28px;

	margin-bottom: 4px;

	word-wrap: break-word;

}

.qp-suggest-print-rate {

	font-size: 11px;

	font-weight: bold;

	color: #1a7f37;

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

	font-weight: 600;

	min-height: 34px;

	line-height: 1.3;

	color: #333;

	margin-bottom: 4px;

}

.qp-suggest-card .qp-suggest-code {

	font-size: 12px;

	font-weight: bold;

	color: #000;

	margin-bottom: 2px;

}

.qp-suggest-card .qp-suggest-rate {

	font-size: 12px;

	color: #1a7f37;

	font-weight: bold;

	margin-top: 4px;

}

.qp-suggest-card .qp-suggest-add-btn {

	margin-top: 8px;

}

@media print {

	.quote-suggest-body {

		page-break-before: always !important;

		break-before: page;

	}

	.quote-footer-wrap,
	.quote-footer-table {
		page-break-inside: avoid;
		break-inside: avoid;
	}

	.qp-suggest-print-section {

		page-break-inside: auto;

	}

	.qp-suggest-print-grid tr {

		page-break-inside: auto !important;

	}

	.qp-suggest-print-grid td.qp-suggest-print-cell {

		page-break-inside: avoid;

	}

	.qp-suggest-print-card {
		min-height: 118px !important;
	}

	.qp-suggest-print-img-wrap {
		height: 62px !important;
	}

	.qp-suggest-print-img {
		max-height: 58px !important;
	}

	.qp-suggest-print-name {
		font-size: 10px !important;
		line-height: 1.2 !important;
	}

	.qp-suggest-print-grid td.qp-suggest-print-cell {
		padding: 4px !important;
	}

}

</style>';

	}

}



if (!function_exists('armor_quotation_pi_get_suggest_products')) {

	function armor_quotation_pi_get_suggest_products($db, $customerId, $excludeProductIds = array())

	{

		require_once dirname(__FILE__) . '/product.class.php';

		$productObj = new Product();

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



		$order_unit_arr = array('-1' => 'Box', '-2' => 'Strip', '-3' => 'Pallet', '1' => 'Caret', '2' => 'Big Box', '100' => 'Nos');

		$catnos = armor_quotation_pi_suggest_catnos($db);

		$items = array();

		$seenProduct = array();



		foreach ($catnos as $catno) {

			$catnoEsc = $db->clean($catno);

			$pwpRes = $db->rp_getData(

				'product_weight_price',

				'*',

				"isDelete=0 AND catno='" . $catnoEsc . "'",

				'id ASC',

				0

			);

			if (!$pwpRes) {

				continue;

			}

			while ($pwp = mysqli_fetch_assoc($pwpRes)) {

				$proId = (int) $pwp['product_id'];

				if ($proId <= 0 || isset($exclude[$proId]) || isset($seenProduct[$proId])) {

					continue;

				}

				$details = $productObj->aj_getProductDetail($proId, $customerId);

				if (empty($details)) {

					continue;

				}

				foreach ($details as $detail) {

					if ((string) $detail['catno'] !== (string) $catno) {

						continue;

					}

					$seenProduct[$proId] = 1;

					$rate = isset($detail['orignal_price']) ? (float) $detail['orignal_price'] : 0;

					if (isset($detail['sell_price']) && (float) $detail['sell_price'] > 0) {

						$rate = (float) $detail['sell_price'];

					}

					$itemOrderUnit = $db->rp_getValue('product', 'unit_id', "id='" . $proId . "' AND isDelete=0", 0);

					$unitName = isset($order_unit_arr[$itemOrderUnit]) ? $order_unit_arr[$itemOrderUnit] : 'Nos';

					$imagePath = isset($detail['image_path']) ? $detail['image_path'] : '';

					if ($imagePath === '') {

						$rawImg = $db->rp_getValue('product', 'image_path', "id='" . $proId . "' AND isDelete=0", 0);

						$imagePath = $rawImg ? $rawImg : '';

					}

					$items[] = array(

						'product_id' => $proId,

						'weight_id' => (int) $detail['weight_id'],

						'option_value' => isset($detail['id']) ? (int) $detail['id'] : (int) $pwp['id'],

						'catno' => $catno,

						'name' => html_entity_decode(strip_tags($detail['name1']), ENT_QUOTES, 'UTF-8'),

						'rate' => $rate,

						'rate_label' => number_format($rate, 2),

						'image' => armor_quotation_pi_product_image_url($imagePath),

						'pro_id' => $proId,

						'item_order_unit' => $itemOrderUnit,

						'unit_name' => $unitName,

					);

					break;

				}

			}

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



		$html = '<div class="qp-suggest-print-card">';

		$html .= '<div class="qp-suggest-print-img-wrap"><img src="' . $img . '" alt="" class="qp-suggest-print-img" onerror="this.onerror=null;this.src=\'' . $defaultImg . '\';"></div>';

		$html .= '<div class="qp-suggest-print-code">Code: ' . $catno . '</div>';

		$html .= '<div class="qp-suggest-print-name">' . $name . '</div>';

		$html .= '<div class="qp-suggest-print-rate">' . $curr . ' ' . $item['rate_label'] . ' / ' . $unit . '</div>';

		$html .= '</div>';

		return $html;

	}

}



if (!function_exists('armor_quotation_pi_render_suggest_grid')) {

	function armor_quotation_pi_render_suggest_grid($items, $clickable = true)

	{

		if (empty($items)) {

			return '<div class="alert alert-info">All suggested products are already added above.</div>';

		}

		$curr = defined('CURR') ? CURR : 'INR';

		$html = '<div class="qp-suggest-grid clearfix">';

		foreach ($items as $item) {

			$img = htmlspecialchars($item['image'], ENT_QUOTES);

			$name = htmlspecialchars($item['name'], ENT_QUOTES);

			$unit = isset($item['unit_name']) ? htmlspecialchars($item['unit_name'], ENT_QUOTES) : 'Nos';

			$html .= '<div class="qp-suggest-col">';

			$html .= '<div class="qp-suggest-card">';

			if ($clickable) {

				$html .= '<a href="javascript:void(0)" class="qp-suggest-add" data-product-id="' . (int) $item['product_id'] . '" data-weight-id="' . (int) $item['weight_id'] . '" data-catno="' . htmlspecialchars($item['catno'], ENT_QUOTES) . '" style="display:block;color:inherit;text-decoration:none;">';

			}

			$html .= '<div class="qp-suggest-img-wrap"><img src="' . $img . '" alt="" onerror="this.onerror=null;this.src=\'' . htmlspecialchars(armor_quotation_pi_product_image_url(''), ENT_QUOTES) . '\';"></div>';

			$html .= '<div class="qp-suggest-code">Code: ' . htmlspecialchars($item['catno']) . '</div>';

			$html .= '<div class="qp-suggest-name">' . $name . '</div>';

			$html .= '<div class="qp-suggest-rate">' . $curr . ' ' . $item['rate_label'] . ' / ' . $unit . '</div>';

			if ($clickable) {

				$html .= '<div class="qp-suggest-add-btn"><span class="label label-primary"><i class="fa fa-plus"></i> Add to Quote</span></div></a>';

			}

			$html .= '</div></div>';

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

		$html = '';

		if ($includeStyles) {

			$html .= armor_quotation_pi_suggest_styles();

		}

		$html .= '<table class="qp-suggest-wrap-table" cellpadding="0" cellspacing="0"><tr><td>';

		$html .= '<div class="qp-suggest-print-section">';

		$viewColor = defined('VIEW_COLOR') ? VIEW_COLOR : '#E5E5E5';

		$html .= '<div class="qp-suggest-print-header" style="background-color:' . htmlspecialchars($viewColor, ENT_QUOTES) . ';">';

		$html .= '<div class="qp-suggest-print-title">Suggested Product Range</div>';

		$html .= '<div class="qp-suggest-print-subtitle">Please mention Product Code when placing your order</div>';

		$html .= '</div>';

		$html .= '<table class="qp-suggest-print-grid" cellpadding="0" cellspacing="0"><tbody>';



		$chunks = array_chunk($items, $cols);

		foreach ($chunks as $row) {

			$html .= '<tr>';

			for ($i = 0; $i < $cols; $i++) {

				$html .= '<td class="qp-suggest-print-cell">';

				if (isset($row[$i])) {

					$html .= armor_quotation_pi_render_print_item($row[$i]);

				}

				$html .= '</td>';

			}

			$html .= '</tr>';

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

