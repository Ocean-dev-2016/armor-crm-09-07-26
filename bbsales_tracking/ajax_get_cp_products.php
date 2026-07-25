<?php
/**
 * Channel Partner simple order — product options (same format as ajax_get_product.php).
 * Filters by CP top_category + price list when available.
 */
$page_id = 565;
$page_slug = 'page_order';
include("connect.php");
require_once('../include/product.class.php');

header('Content-Type: text/html; charset=UTF-8');

if (!function_exists('cp_is_channel_partner_login') || !cp_is_channel_partner_login($db)) {
	echo '<option value="">Select Product</option>';
	require_once "disconnect.php";
	exit;
}

$product = new Product();
$cid = (int) cp_get_login_channel_partner_id();
if (isset($_REQUEST['cid']) && (int) $_REQUEST['cid'] > 0) {
	$reqCid = (int) $_REQUEST['cid'];
	if ($reqCid === $cid) {
		$cid = $reqCid;
	}
}

$order_unit_arr = array("-1" => "Box", "-2" => "Strip", "-3" => "Pallet", "1" => "Caret", "2" => "Big Box", "100" => "Nos");

$exec_r = $db->rp_getData("executive", "top_category_id,price_list_id", "id='" . $cid . "' AND isDelete=0", "", 0);
$exec_d = $exec_r ? mysqli_fetch_assoc($exec_r) : array();
$top_category_id = isset($exec_d['top_category_id']) ? trim($exec_d['top_category_id']) : '';
$price_list_id = isset($exec_d['price_list_id']) ? (int) $exec_d['price_list_id'] : 0;

$where = "isDelete=0 AND isActive=1";
$where_base = $where;

$tcids = array();
if ($top_category_id != '' && $top_category_id != '0') {
	foreach (explode(',', $top_category_id) as $t) {
		$t = (int) trim($t);
		if ($t > 0) {
			$tcids[] = $t;
		}
	}
}

/* Prefer products present in CP price list */
$price_list_pids = array();
if ($price_list_id > 0) {
	$pl_r = $db->rp_getData("product_price_list", "DISTINCT pid", "price_list_id='" . $price_list_id . "' AND isDelete=0", "", 0);
	if ($pl_r) {
		while ($pl = mysqli_fetch_assoc($pl_r)) {
			$pid = (int) $pl['pid'];
			if ($pid > 0) {
				$price_list_pids[$pid] = $pid;
			}
		}
	}
}

if (!empty($price_list_pids)) {
	$where .= " AND id IN (" . implode(',', $price_list_pids) . ")";
} else if (!empty($tcids)) {
	$where .= " AND tcid IN (" . implode(',', $tcids) . ")";
}

echo '<option value="">Select Product</option>';

$product_list_d = $db->rp_getData('product', "*", $where, "name ASC", 0);
/* Fallback if filters return nothing */
if (!$product_list_d || mysqli_num_rows($product_list_d) == 0) {
	if (!empty($tcids) && !empty($price_list_pids)) {
		$where = $where_base . " AND tcid IN (" . implode(',', $tcids) . ")";
		$product_list_d = $db->rp_getData('product', "*", $where, "name ASC", 0);
	}
	if ((!$product_list_d || mysqli_num_rows($product_list_d) == 0) && !empty($tcids)) {
		$where = $where_base . " AND tcid IN (" . implode(',', $tcids) . ")";
		$product_list_d = $db->rp_getData('product', "*", $where, "name ASC", 0);
	}
	if (!$product_list_d || mysqli_num_rows($product_list_d) == 0) {
		$product_list_d = $db->rp_getData('product', "*", $where_base, "name ASC", 0);
	}
}
if ($product_list_d) {
	while ($product_list_r = mysqli_fetch_assoc($product_list_d)) {
		$current_prodcuts = $product->aj_getProductDetail($product_list_r['id'], $cid);
		if (empty($current_prodcuts)) {
			continue;
		}
		foreach ($current_prodcuts as $product_detail) {
			$cat_no = isset($product_detail['catno']) ? $product_detail['catno'] : $db->rp_getValue("product_weight_price", "catno", "product_id='" . $product_detail['pro_id'] . "' AND weight_id='" . $product_detail['weight_id'] . "' AND isDelete=0");
			$stock_qty = $db->rp_getValue("product_weight_price", "stock_qty", "product_id='" . $product_detail['pro_id'] . "' AND isDelete=0");
			$hsncode = $db->rp_getValue("product", "hsn_code", "id='" . $product_detail['pro_id'] . "' AND isDelete=0");
			$top_cat_name = $db->rp_getValue("top_category_master", "name", "id='" . $product_list_r['tcid'] . "' AND isDelete=0", 0);
			$category_name = $db->rp_getValue("category_master", "name", "id='" . $product_list_r['cid'] . "' AND isDelete=0", 0);
			$unit_id_main = $db->rp_getValue("product", "unit_id", "id='" . $product_detail['pro_id'] . "' AND isDelete=0");
			$gst = $db->rp_getValue("product", "igst", "id='" . $product_detail['pro_id'] . "' AND isDelete=0", 0);
			$pro_master_price = $product_detail['orignal_price'];

			if ($product_detail['is_including'] == 1) {
				if ($gst != "") {
					$gst_val = 1 + ($gst / 100);
					$product_detail['orignal_price'] = $db->rp_num($product_detail['orignal_price'] / $gst_val);
				} else {
					$product_detail['orignal_price'] = $db->rp_num($product_detail['orignal_price'] / 1);
				}
			}

			$item_order_unit = $db->rp_getValue("product", "customer_unit_id", "id='" . $product_detail['pro_id'] . "' AND isDelete=0");
			$unit_name = isset($order_unit_arr[$item_order_unit]) ? $order_unit_arr[$item_order_unit] : '';

			/* Proper label: Product - Weight - #CatNo (same idea as main order form) */
			$label = isset($product_detail['name']) ? $product_detail['name'] : '';
			if ($label == '') {
				$label = trim($product_detail['product_name']);
				if ($cat_no != '') {
					$label .= " - " . $cat_no;
				}
			}

			$optName = isset($product_detail['name1']) ? $product_detail['name1'] : htmlentities($label);
			?>
<option value="<?php echo $product_detail['id']; ?>"
	data-weight-id="<?php echo $product_detail['weight_id']; ?>"
	data-name="<?php echo $optName; ?>"
	data-weight="<?php echo $product_detail['weight_id']; ?>"
	data-pricelist="<?php echo $product_detail['sell_price']; ?>"
	data-inner_size="<?php echo $product_detail['bag_qty']; ?>"
	data-outer_size="<?php echo $product_detail['box_qty']; ?>"
	data-pro_id="<?php echo $product_detail['pro_id']; ?>"
	data-discount="<?= $product_detail['discountPer']; ?>"
	data-original-price="<?= $product_detail['orignal_price']; ?>"
	data-stock_qty="<?= $product_detail['qty']; ?>"
	data-brand-id="<?= $product_detail['brand_id']; ?>"
	data-catno="<?= htmlentities($cat_no); ?>"
	data-hsncode="<?= htmlentities($hsncode); ?>"
	data-stock="<?= $stock_qty; ?>"
	data-cid="<?= $product_list_r['cid']; ?>"
	data-tcid="<?= $product_list_r['tcid']; ?>"
	data-topcat_name="<?= htmlentities($top_cat_name); ?>"
	data-cat_name="<?= htmlentities($category_name); ?>"
	data-gst="<?= $gst; ?>"
	data-unit_name="<?= htmlentities($unit_name); ?>"
	data-pro_master_price="<?= $pro_master_price; ?>"
	data-is_including="<?= $product_detail['is_including']; ?>"
	data-price_list_amount="<?= $product_detail['price_list_amount']; ?>"
	data-unit_main_id="<?= $unit_id_main; ?>"
	data-item_order_unit="<?= $item_order_unit; ?>"
	data-pro_weight="<?= $product_detail['pro_weight']; ?>"
	data-inner_discount="<?= $product_detail['inner_discount']; ?>"
	data-outer_discount="<?= $product_detail['outer_discount']; ?>"
	data-is_premium="<?= $product_detail['is_premium']; ?>"
	data-loose_discount="<?= $product_detail['loose_discount']; ?>"
	data-min_sell_price="<?= $product_detail['minimum_selling_price']; ?>"
><?php echo htmlentities($label); ?></option>
			<?php
		}
	}
}

require_once "disconnect.php";
?>
