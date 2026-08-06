<?php
/**
 * Channel Partner — Customer Order + Cart (App API, same as web CP Customer Order).
 * PHP 5.6 compatible.
 */
require_once dirname(__FILE__) . '/main.class.php';
require_once dirname(__FILE__) . '/function.class.php';
require_once dirname(__FILE__) . '/orders.class.php';
require_once dirname(__FILE__) . '/product.class.php';
require_once dirname(__FILE__) . '/class.channel_partner_stock.php';

class ChannelPartnerOrder
{
	public $db;
	private $objOrder;
	private $objProduct;
	private $objStock;

	function __construct()
	{
		$db = new Functions();
		$db->connect();
		$this->db = $db;
		$this->objOrder = new Order();
		$this->objProduct = new Product();
		$this->objStock = new ChannelPartnerStock($db);
	}

	private function validateCp($cpId)
	{
		$cpId = (int) $cpId;
		if ($cpId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'channel_partner_id is required.');
		}
		$where = "id='" . $cpId . "' AND channel_partner_flag=1 AND customer_flag=0 AND isDelete=0";
		if ((int) $this->db->rp_getTotalRecord('executive', $where, 0) <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Invalid Channel Partner.');
		}
		return array('ack' => 1);
	}

	private function getCpExec($cpId)
	{
		$r = $this->db->rp_getData('executive', '*', "id='" . (int) $cpId . "' AND isDelete=0", '', 0);
		return $r ? mysqli_fetch_assoc($r) : array();
	}

	private function assertCustomer($cpId, $custId)
	{
		$custId = (int) $custId;
		if ($custId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'channel_partner_customer_id is required.');
		}
		$own = (int) $this->db->rp_getTotalRecord(
			'channel_partner_customer',
			"id='" . $custId . "' AND channel_partner_id='" . (int) $cpId . "' AND isDelete=0",
			0
		);
		if ($own <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Customer does not belong to this Channel Partner.');
		}
		return array('ack' => 1);
	}

	private function getCustomer($cpId, $custId)
	{
		$r = $this->db->rp_getData(
			'channel_partner_customer',
			'*',
			"id='" . (int) $custId . "' AND channel_partner_id='" . (int) $cpId . "' AND isDelete=0",
			'',
			0
		);
		return $r ? mysqli_fetch_assoc($r) : array();
	}

	private function cartWhere($cpId)
	{
		return "customer_id='" . (int) $cpId . "' AND status=-1 AND isDelete=0"
			. " AND channel_partner_order_flag=1 AND cp_order_mode='customer'";
	}

	private function getDraftCartId($cpId)
	{
		$id = $this->db->rp_getValue('orders', 'id', $this->cartWhere($cpId) . " ORDER BY id DESC", 0);
		return ($id !== '' && $id !== null && $id !== false) ? (int) $id : 0;
	}

	/**
	 * Auto Order No — same as web AddToCart for CP (OUTLETS_ORDER_NO = PI/{id}).
	 * Draft cart was created without order_no; assign before/after place.
	 */
	private function ensureOrderNo($orderId)
	{
		$orderId = (int) $orderId;
		if ($orderId <= 0) {
			return '';
		}
		$existing = $this->db->rp_getValue('orders', 'order_no', "id='" . $orderId . "' AND isDelete=0", 0);
		if ($existing !== '' && $existing !== null && $existing !== false && trim((string) $existing) !== '') {
			return trim((string) $existing);
		}
		$prefix = defined('OUTLETS_ORDER_NO') ? OUTLETS_ORDER_NO : 'PI/';
		$orderNo = $prefix . str_pad((string) $orderId, 2, '0', STR_PAD_LEFT);
		$this->db->rp_update(
			'orders',
			array('order_no' => $orderNo, 'modified_date' => date('Y-m-d H:i:s')),
			"id='" . $orderId . "' AND isDelete=0",
			0
		);
		return $orderNo;
	}

	private function workflowStatus($status, $paidFlag, $grandTotal, $paidAmount)
	{
		$status = (int) $status;
		$paidFlag = (int) $paidFlag;
		$grandTotal = (float) $grandTotal;
		$paidAmount = (float) $paidAmount;
		$isPaid = ($paidFlag === 1 && $paidAmount > 0);
		$isDispatched = ($status >= 5 && $status != 3 && $status != -2);
		$baki = $isPaid ? 0 : max(0, $grandTotal - $paidAmount);
		if ($isPaid) {
			return array('status' => 'completed', 'status_label' => 'Completed', 'baki_amount' => 0);
		}
		if ($isDispatched) {
			return array('status' => 'pending_payment', 'status_label' => 'Pending Payment', 'baki_amount' => round($baki, 2));
		}
		return array('status' => 'pending', 'status_label' => 'Pending', 'baki_amount' => round($baki, 2));
	}

	/**
	 * Resolve product_weight_price.id (pwp_id) — same key web uses as line_product[].
	 * Accepts: pwp_id | catno | product_id/pid (+ optional weight_id).
	 */
	private function resolvePwpId($input)
	{
		if (!is_array($input)) {
			$input = array();
		}
		$pwpId = 0;
		if (isset($input['pwp_id']) && (int) $input['pwp_id'] > 0) {
			$pwpId = (int) $input['pwp_id'];
		} else if (isset($input['line_product']) && (int) $input['line_product'] > 0) {
			$pwpId = (int) $input['line_product'];
		}
		if ($pwpId > 0) {
			$ok = (int) $this->db->rp_getTotalRecord('product_weight_price', "id='" . $pwpId . "' AND isDelete=0", 0);
			if ($ok <= 0) {
				return array('ack' => 0, 'ack_msg' => 'Invalid pwp_id. Use product_weight_price.id from Products API #248.');
			}
			return array('ack' => 1, 'pwp_id' => $pwpId);
		}

		$catno = '';
		if (isset($input['catno']) && trim($input['catno']) !== '') {
			$catno = trim($input['catno']);
		} else if (isset($input['cat_no']) && trim($input['cat_no']) !== '') {
			$catno = trim($input['cat_no']);
		}
		if ($catno !== '') {
			$s = $this->db->clean($catno);
			/* Exact Cat No first (web Cat No), then LIKE */
			$pwpId = (int) $this->db->rp_getValue(
				'product_weight_price',
				'id',
				"catno='" . $s . "' AND isDelete=0",
				0
			);
			if ($pwpId <= 0) {
				$pwpId = (int) $this->db->rp_getValue(
					'product_weight_price',
					'id',
					"catno LIKE '%" . $s . "%' AND isDelete=0",
					0
				);
			}
			if ($pwpId <= 0) {
				return array('ack' => 0, 'ack_msg' => 'No product found for Cat No: ' . $catno);
			}
			return array('ack' => 1, 'pwp_id' => $pwpId);
		}

		$pid = 0;
		if (isset($input['product_id']) && (int) $input['product_id'] > 0) {
			$pid = (int) $input['product_id'];
		} else if (isset($input['pid']) && (int) $input['pid'] > 0) {
			$pid = (int) $input['pid'];
		} else if (isset($input['pro_id']) && (int) $input['pro_id'] > 0) {
			$pid = (int) $input['pro_id'];
		}
		$weightId = '';
		if (isset($input['weight_id']) && $input['weight_id'] !== '' && $input['weight_id'] !== null) {
			$weightId = $input['weight_id'];
		}

		if ($pid > 0) {
			$w = "product_id='" . $pid . "' AND isDelete=0";
			if ($weightId !== '') {
				$w .= " AND weight_id='" . $this->db->clean($weightId) . "'";
			}
			$cnt = (int) $this->db->rp_getTotalRecord('product_weight_price', $w, 0);
			if ($cnt <= 0) {
				return array('ack' => 0, 'ack_msg' => 'No variant found for product_id=' . $pid);
			}
			if ($cnt > 1 && $weightId === '') {
				return array(
					'ack' => 0,
					'ack_msg' => 'Multiple variants for this product. Send pwp_id from #248, or product_id + weight_id, or catno.',
					'product_id' => $pid,
					'hint' => 'Call #248 with search_name=product_id to pick pwp_id',
				);
			}
			$pwpId = (int) $this->db->rp_getValue('product_weight_price', 'id', $w, 0);
			if ($pwpId <= 0) {
				return array('ack' => 0, 'ack_msg' => 'Could not resolve pwp_id for product_id=' . $pid);
			}
			return array('ack' => 1, 'pwp_id' => $pwpId);
		}

		return array(
			'ack' => 0,
			'ack_msg' => 'Send pwp_id (preferred), OR catno, OR product_id (+ weight_id if multiple sizes).',
		);
	}

	/**
	 * Build one order line item from product_weight_price.id (same as web form line_product).
	 */
	public function buildItemFromPwp($cpId, $pwpId, $qty, $rate = null, $discount = null)
	{
		$cpId = (int) $cpId;
		$pwpId = (int) $pwpId;
		$qty = (float) $qty;
		if ($pwpId <= 0 || $qty <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Valid product and qty are required.');
		}

		$pwp_r = $this->db->rp_getData('product_weight_price', '*', "id='" . $pwpId . "'", '', 0);
		if (!$pwp_r) {
			return array('ack' => 0, 'ack_msg' => 'Product variant not found.');
		}
		$pwp = mysqli_fetch_assoc($pwp_r);
		$pid = (int) $pwp['product_id'];
		$weight_id = $pwp['weight_id'];
		if ($pid <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Invalid product.');
		}

		$cp_exec = $this->getCpExec($cpId);
		$price_list_id = isset($cp_exec['price_list_id']) ? (int) $cp_exec['price_list_id'] : 0;

		$details = $this->objProduct->aj_getProductDetail($pid, $cpId);
		$match = null;
		if (!empty($details)) {
			foreach ($details as $d) {
				if ((int) $d['id'] === $pwpId || (string) $d['weight_id'] === (string) $weight_id) {
					$match = $d;
					break;
				}
			}
			if ($match === null) {
				$match = $details[0];
			}
		}

		$original_price = $match ? $match['orignal_price'] : $pwp['price'];
		$sell_price = $match ? $match['sell_price'] : $pwp['price'];
		if ($price_list_id > 0) {
			$pl_price = $this->db->rp_getValue(
				'product_price_list',
				'discounted_price',
				"pid='" . $pid . "' AND weight_id='" . $weight_id . "' AND price_list_id='" . $price_list_id . "' AND isDelete=0",
				0
			);
			$pl_orig = $this->db->rp_getValue(
				'product_price_list',
				'price',
				"pid='" . $pid . "' AND weight_id='" . $weight_id . "' AND price_list_id='" . $price_list_id . "' AND isDelete=0",
				0
			);
			if ($pl_price !== '' && $pl_price !== null && $pl_price !== false) {
				$sell_price = $pl_price;
			}
			if ($pl_orig !== '' && $pl_orig !== null && $pl_orig !== false) {
				$original_price = $pl_orig;
			}
		}

		if ($rate !== null && $rate !== '' && is_numeric($rate)) {
			$sell_price = (float) $rate;
			$original_price = (float) $rate;
		}

		$disc = 0;
		if ($discount !== null && $discount !== '' && is_numeric($discount)) {
			$disc = (float) $discount;
		} else if ($match && isset($match['discountPer'])) {
			$disc = (float) $match['discountPer'];
		}
		if ($disc < 0) {
			$disc = 0;
		}
		if ($disc > 100) {
			$disc = 100;
		}

		$rate_before_disc = (float) $sell_price;
		if ($disc > 0) {
			$sell_price = $rate_before_disc - (($rate_before_disc * $disc) / 100);
		}
		$discount_amount = $rate_before_disc - (float) $sell_price;

		$inner = isset($pwp['inner_size']) && (float) $pwp['inner_size'] > 0 ? (float) $pwp['inner_size'] : 1;
		$outer = isset($pwp['outer_size']) && (float) $pwp['outer_size'] > 0 ? (float) $pwp['outer_size'] : 1;
		$bag = $qty / $inner;
		$cartoon = $bag / $outer;
		$pro_name = $match && !empty($match['name']) ? $match['name'] : $this->db->rp_getValue('product', 'name', "id='" . $pid . "'");
		$brand_id = $match && isset($match['brand_id']) ? $match['brand_id'] : $this->db->rp_getValue('product', 'brand_id', "id='" . $pid . "'");
		$item_order_unit = $this->db->rp_getValue('product', 'customer_unit_id', "id='" . $pid . "' AND isDelete=0");
		if ($item_order_unit === '' || $item_order_unit === null) {
			$item_order_unit = 100;
		}
		$gst = (float) $this->db->rp_getValue('product', 'igst', "id='" . $pid . "' AND isDelete=0", 0);
		$available = $this->objStock->getAvailableQty($cpId, $pid, $weight_id);

		$item = array(
			'qty' => $qty,
			'pid' => $pid,
			'original_price' => ($original_price !== null && $original_price !== '') ? $original_price : 0,
			'price' => ($sell_price !== null && $sell_price !== '') ? $sell_price : 0,
			'pro_name' => $pro_name,
			'weight_id' => $weight_id,
			'cartoon_qty' => $cartoon,
			'box_qty' => $bag,
			'loose' => 0,
			'brand_id' => ($brand_id !== null && $brand_id !== '') ? $brand_id : 0,
			'pro_description' => '',
			'cd_discount' => 0,
			'ad_discount' => 0,
			'gst_amount_item' => 0,
			'taxable_amount' => 0,
			'sub_total' => 0,
			'other_charge' => 0,
			'fright_charge' => 0,
			'discount_amount' => $discount_amount,
			'discount' => $disc,
			'is_including' => isset($pwp['is_including']) && $pwp['is_including'] !== null && $pwp['is_including'] !== '' ? $pwp['is_including'] : 0,
			'item_order_unit' => $item_order_unit,
			'order_qty' => $qty,
			'order_item_brand_id' => ($brand_id !== null && $brand_id !== '') ? $brand_id : 0,
			'pwp_id' => $pwpId,
			'gst_percent' => $gst,
			'available_stock' => $available,
			'rate_before_discount' => $rate_before_disc,
		);

		return array('ack' => 1, 'item' => $item);
	}

	/**
	 * Products for Customer Order form (CP pricing + My Stock qty).
	 */
	public function GetOrderProducts($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}
		$search = isset($detail['search_name']) ? trim($detail['search_name']) : '';
		$only_in_stock = !empty($detail['only_in_stock']) ? 1 : 0;

		$cp_exec = $this->getCpExec($cpId);
		$top_category_id = isset($cp_exec['top_category_id']) ? trim($cp_exec['top_category_id']) : '';
		$price_list_id = isset($cp_exec['price_list_id']) ? (int) $cp_exec['price_list_id'] : 0;

		$where = 'isDelete=0 AND isActive=1';
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
		$price_list_pids = array();
		if ($price_list_id > 0) {
			$pl_r = $this->db->rp_getData('product_price_list', 'DISTINCT pid', "price_list_id='" . $price_list_id . "' AND isDelete=0", '', 0);
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
			$where .= ' AND id IN (' . implode(',', $price_list_pids) . ')';
		} else if (!empty($tcids)) {
			$where .= ' AND tcid IN (' . implode(',', $tcids) . ')';
		}
		if ($search != '') {
			$s = $this->db->clean($search);
			$matchIds = array();
			/* Product id exact */
			if (ctype_digit($search)) {
				$matchIds[(int) $search] = (int) $search;
			}
			/* Cat No → product_ids (same as product_get_ajax search) */
			$cr = $this->db->rp_getData(
				'product_weight_price',
				'DISTINCT product_id',
				"catno LIKE '%" . $s . "%' AND isDelete=0",
				'',
				0
			);
			if ($cr) {
				while ($crow = mysqli_fetch_assoc($cr)) {
					$mid = (int) $crow['product_id'];
					if ($mid > 0) {
						$matchIds[$mid] = $mid;
					}
				}
			}
			if (!empty($matchIds)) {
				$where .= " AND (name LIKE '%" . $s . "%' OR id IN (" . implode(',', $matchIds) . "))";
			} else {
				$where .= " AND name LIKE '%" . $s . "%'";
			}
		}

		$product_list_d = $this->db->rp_getData('product', '*', $where, 'name ASC', 0);
		if (!$product_list_d || mysqli_num_rows($product_list_d) == 0) {
			if (!empty($tcids)) {
				$where2 = $where_base . ' AND tcid IN (' . implode(',', $tcids) . ')';
				if ($search != '') {
					$s2 = $this->db->clean($search);
					$where2 .= " AND (name LIKE '%" . $s2 . "%'";
					if (ctype_digit($search)) {
						$where2 .= " OR id='" . (int) $search . "'";
					}
					$cr2 = $this->db->rp_getData('product_weight_price', 'DISTINCT product_id', "catno LIKE '%" . $s2 . "%' AND isDelete=0", '', 0);
					$ids2 = array();
					if ($cr2) {
						while ($x = mysqli_fetch_assoc($cr2)) {
							$ids2[] = (int) $x['product_id'];
						}
					}
					if (!empty($ids2)) {
						$where2 .= " OR id IN (" . implode(',', $ids2) . ")";
					}
					$where2 .= ')';
				}
				$product_list_d = $this->db->rp_getData('product', '*', $where2, 'name ASC', 0);
			}
			if (!$product_list_d || mysqli_num_rows($product_list_d) == 0) {
				$where3 = $where_base;
				if ($search != '') {
					$s3 = $this->db->clean($search);
					$where3 .= " AND (name LIKE '%" . $s3 . "%'";
					if (ctype_digit($search)) {
						$where3 .= " OR id='" . (int) $search . "'";
					}
					$cr3 = $this->db->rp_getData('product_weight_price', 'DISTINCT product_id', "catno LIKE '%" . $s3 . "%' AND isDelete=0", '', 0);
					$ids3 = array();
					if ($cr3) {
						while ($x = mysqli_fetch_assoc($cr3)) {
							$ids3[] = (int) $x['product_id'];
						}
					}
					if (!empty($ids3)) {
						$where3 .= " OR id IN (" . implode(',', $ids3) . ")";
					}
					$where3 .= ')';
				}
				$product_list_d = $this->db->rp_getData('product', '*', $where3, 'name ASC', 0);
			}
		}

		$result = array();
		$searchLower = ($search != '') ? strtolower($search) : '';
		if ($product_list_d) {
			while ($product_list_r = mysqli_fetch_assoc($product_list_d)) {
				$variants = $this->objProduct->aj_getProductDetail($product_list_r['id'], $cpId);
				if (empty($variants)) {
					continue;
				}
				foreach ($variants as $product_detail) {
					$pid = (int) $product_detail['pro_id'];
					$weight_id = $product_detail['weight_id'];
					$gst = (float) $this->db->rp_getValue('product', 'igst', "id='" . $pid . "' AND isDelete=0", 0);
					$available = $this->objStock->getAvailableQty($cpId, $pid, $weight_id);
					if ($only_in_stock && $available <= 0) {
						continue;
					}
					$cat_no = isset($product_detail['catno']) ? $product_detail['catno'] : '';
					if ($cat_no === '' || $cat_no === null) {
						$cat_no = $this->db->rp_getValue(
							'product_weight_price',
							'catno',
							"id='" . (int) $product_detail['id'] . "' AND isDelete=0",
							0
						);
					}
					/* If searching by Cat No / product id, keep matching variants only when possible */
					if ($searchLower !== '') {
						$nameHay = strtolower($product_list_r['name'] . ' ' . (isset($product_detail['name']) ? $product_detail['name'] : ''));
						$catHay = strtolower((string) $cat_no);
						$idMatch = (ctype_digit($search) && ((int) $search === $pid || (int) $search === (int) $product_detail['id']));
						$catMatch = ($catHay !== '' && strpos($catHay, $searchLower) !== false);
						$nameMatch = (strpos($nameHay, $searchLower) !== false);
						if (!$idMatch && !$catMatch && !$nameMatch) {
							continue;
						}
					}
					$label = !empty($product_detail['name']) ? $product_detail['name'] : $product_list_r['name'];
					$display = $label;
					if ($cat_no != '') {
						$display .= ' - #' . $cat_no;
					}
					$rate = isset($product_detail['sell_price']) ? (float) $product_detail['sell_price'] : 0;
					$disc = isset($product_detail['discountPer']) ? (float) $product_detail['discountPer'] : 0;
					$result[] = array(
						'pwp_id' => (int) $product_detail['id'],
						'product_id' => $pid,
						'pid' => $pid,
						'weight_id' => $weight_id,
						'product_name' => $label,
						'display_name' => $display,
						'catno' => $cat_no,
						'cat_no' => $cat_no,
						'rate' => round($rate, 2),
						'original_price' => isset($product_detail['orignal_price']) ? round((float) $product_detail['orignal_price'], 2) : round($rate, 2),
						'discount' => round($disc, 2),
						'gst_percent' => $gst,
						'available_stock' => round($available, 2),
						'brand_id' => isset($product_detail['brand_id']) ? $product_detail['brand_id'] : 0,
						'is_including' => isset($product_detail['is_including']) ? $product_detail['is_including'] : 0,
					);
				}
			}
		}

		return array(
			'ack' => 1,
			'ack_msg' => 'Products ready',
			'total' => count($result),
			'search_name' => $search,
			'search_hint' => 'search_name matches Product Name, Cat No (catno), or Product id',
			'pwp_id_note' => 'pwp_id = product_weight_price.id (same as web line_product). Use this when adding to cart.',
			'result' => $result,
		);
	}

	private function ensureDraftCart($cpId, $custId, $gstApplyFlag = 1)
	{
		$cpId = (int) $cpId;
		$custId = (int) $custId;
		$gstApplyFlag = ((int) $gstApplyFlag === 0) ? 0 : 1;
		$cartId = $this->getDraftCartId($cpId);
		$cp_exec = $this->getCpExec($cpId);
		$sales_executive_id = !empty($cp_exec['seid']) ? $cp_exec['seid'] : 0;

		if ($cartId > 0) {
			$upd = array(
				'channel_partner_customer_id' => $custId,
				'cp_order_mode' => 'customer',
				'channel_partner_order_flag' => 1,
				'cp_portal_order_flag' => 1,
				'modified_date' => date('Y-m-d H:i:s'),
			);
			$colGst = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'gst_apply_flag'");
			if ($colGst && mysqli_num_rows($colGst) > 0) {
				$upd['gst_apply_flag'] = $gstApplyFlag;
			}
			$this->db->rp_update('orders', $upd, "id='" . $cartId . "' AND isDelete=0", 0);
			$this->ensureOrderNo($cartId);
			return array('ack' => 1, 'cart_id' => $cartId, 'sales_executive_id' => $sales_executive_id);
		}

		/* Soft-delete other leftover drafts so CP cart is clean */
		$this->db->rp_update(
			'orders',
			array('isDelete' => 1, 'modified_date' => date('Y-m-d H:i:s')),
			"customer_id='" . $cpId . "' AND status=-1 AND isDelete=0",
			0
		);

		$rows = array(
			'customer_id', 'company_name', 'sales_id', 'order_date', 'status',
			'channel_partner_order_flag', 'cp_portal_order_flag', 'cp_order_mode',
			'channel_partner_customer_id', 'isDelete', 'isActive', 'created_date',
		);
		$values = array(
			$cpId,
			isset($cp_exec['company_name']) ? $cp_exec['company_name'] : '',
			$sales_executive_id,
			date('Y-m-d'),
			-1,
			1,
			1,
			'customer',
			$custId,
			0,
			1,
			date('Y-m-d H:i:s'),
		);
		$colGst = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'gst_apply_flag'");
		if ($colGst && mysqli_num_rows($colGst) > 0) {
			$rows[] = 'gst_apply_flag';
			$values[] = $gstApplyFlag;
		}
		$cartId = (int) $this->db->rp_insert('orders', $values, $rows, 0);
		if ($cartId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Failed to create cart.');
		}
		$this->ensureOrderNo($cartId);
		return array('ack' => 1, 'cart_id' => $cartId, 'sales_executive_id' => $sales_executive_id);
	}

	private function formatCartResponse($cpId, $cartId)
	{
		$cartId = (int) $cartId;
		$order_r = $this->db->rp_getData('orders', '*', "id='" . $cartId . "' AND isDelete=0", '', 0);
		if (!$order_r || mysqli_num_rows($order_r) == 0) {
			return array(
				'ack' => 1,
				'ack_msg' => 'Cart empty',
				'cart_id' => 0,
				'channel_partner_id' => (int) $cpId,
				'channel_partner_customer_id' => 0,
				'party_id' => 0,
				'customer_name' => '',
				'gst_apply_flag' => 1,
				'items' => array(),
				'sub_total' => 0,
				'estimated_gst' => 0,
				'grand_total' => 0,
				'total_qty' => 0,
				'total_items' => 0,
				'app_note' => 'Select party then call #249 with channel_partner_customer_id to bind party, OR send it with #250 Add Cart.',
			);
		}
		$order = mysqli_fetch_assoc($order_r);
		$gstFlag = isset($order['gst_apply_flag']) ? (int) $order['gst_apply_flag'] : 1;
		if ($gstFlag !== 0) {
			$gstFlag = 1;
		}
		$items = array();
		$sub = 0;
		$gstTot = 0;
		$qtyTot = 0;
		$ir = $this->db->rp_getData('order_product_item', '*', "order_id='" . $cartId . "' AND isDelete=0", 'id ASC', 0);
		if ($ir) {
			while ($row = mysqli_fetch_assoc($ir)) {
				$pid = (int) $row['pro_id'];
				$qty = (float) $row['pro_qty'];
				$rate = (float) $row['unitprice'];
				/* unitprice already net after discount in AddToCart path; use discount % if stored */
				$disc = isset($row['discount']) ? (float) $row['discount'] : 0;
				$lineBase = isset($row['totalprice']) ? (float) $row['totalprice'] : ($qty * $rate);
				$gstPct = (float) $this->db->rp_getValue('product', 'igst', "id='" . $pid . "' AND isDelete=0", 0);
				$gstAmt = $gstFlag ? (($lineBase * $gstPct) / 100) : 0;
				$pwpId = (int) $this->db->rp_getValue(
					'product_weight_price',
					'id',
					"product_id='" . $pid . "' AND weight_id='" . $this->db->clean($row['weight_id']) . "' AND isDelete=0",
					0
				);
				$catno = '';
				if ($pwpId > 0) {
					$catno = $this->db->rp_getValue('product_weight_price', 'catno', "id='" . $pwpId . "'", 0);
				}
				$available = $this->objStock->getAvailableQty($cpId, $pid, $row['weight_id']);
				$items[] = array(
					'cart_item_id' => (int) $row['id'],
					'pwp_id' => $pwpId,
					'product_id' => $pid,
					'pid' => $pid,
					'weight_id' => $row['weight_id'],
					'product_name' => $row['pro_name'],
					'catno' => $catno ? $catno : '',
					'cat_no' => $catno ? $catno : '',
					'qty' => round($qty, 2),
					'rate' => round($rate, 2),
					'discount' => round($disc, 2),
					'gst_percent' => $gstPct,
					'amount' => round($lineBase + $gstAmt, 2),
					'line_base' => round($lineBase, 2),
					'gst_amount' => round($gstAmt, 2),
					'available_stock' => round($available, 2),
				);
				$sub += $lineBase;
				$gstTot += $gstAmt;
				$qtyTot += $qty;
			}
		}

		$partyId = isset($order['channel_partner_customer_id']) ? (int) $order['channel_partner_customer_id'] : 0;
		$partyName = '';
		$partyMobile = '';
		if ($partyId > 0) {
			$partyName = $this->db->rp_getValue('channel_partner_customer', 'company_name', "id='" . $partyId . "'", 0);
			$partyMobile = $this->db->rp_getValue('channel_partner_customer', 'mobile_no', "id='" . $partyId . "'", 0);
		}

		return array(
			'ack' => 1,
			'ack_msg' => 'Cart ready',
			'cart_id' => $cartId,
			'channel_partner_id' => (int) $cpId,
			'channel_partner_customer_id' => $partyId,
			'party_id' => $partyId,
			'customer_name' => $partyName ? $partyName : '',
			'customer_mobile' => $partyMobile ? $partyMobile : '',
			'gst_apply_flag' => $gstFlag,
			'items' => $items,
			'sub_total' => round($sub, 2),
			'estimated_gst' => round($gstFlag ? $gstTot : 0, 2),
			'grand_total' => round($sub + ($gstFlag ? $gstTot : 0), 2),
			'total_qty' => round($qtyTot, 2),
			'total_items' => count($items),
		);
	}

	/**
	 * Get draft cart. Optionally bind/set party with channel_partner_customer_id (App party dropdown).
	 */
	public function GetCart($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}

		$custId = 0;
		if (isset($detail['channel_partner_customer_id']) && (int) $detail['channel_partner_customer_id'] > 0) {
			$custId = (int) $detail['channel_partner_customer_id'];
		} else if (isset($detail['party_id']) && (int) $detail['party_id'] > 0) {
			$custId = (int) $detail['party_id'];
		}

		if ($custId > 0) {
			$custCheck = $this->assertCustomer($cpId, $custId);
			if ($custCheck['ack'] != 1) {
				return $custCheck;
			}
			$gstFlag = isset($detail['gst_apply_flag']) ? (int) $detail['gst_apply_flag'] : 1;
			if ($gstFlag !== 0) {
				$gstFlag = 1;
			}
			$ens = $this->ensureDraftCart($cpId, $custId, $gstFlag);
			if ($ens['ack'] != 1) {
				return $ens;
			}
			$cart = $this->formatCartResponse($cpId, (int) $ens['cart_id']);
			$cart['ack_msg'] = 'Cart ready (party set)';
			return $cart;
		}

		$cartId = $this->getDraftCartId($cpId);
		if ($cartId <= 0) {
			return $this->formatCartResponse($cpId, 0);
		}
		return $this->formatCartResponse($cpId, $cartId);
	}

	public function AddToCart($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$custId = 0;
		if (isset($detail['channel_partner_customer_id']) && (int) $detail['channel_partner_customer_id'] > 0) {
			$custId = (int) $detail['channel_partner_customer_id'];
		} else if (isset($detail['party_id']) && (int) $detail['party_id'] > 0) {
			$custId = (int) $detail['party_id'];
		}
		$gstFlag = isset($detail['gst_apply_flag']) ? (int) $detail['gst_apply_flag'] : 1;
		if ($gstFlag !== 0) {
			$gstFlag = 1;
		}

		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}

		/* If App forgot party, reuse party already saved on draft cart */
		if ($custId <= 0) {
			$existingCartId = $this->getDraftCartId($cpId);
			if ($existingCartId > 0) {
				$custId = (int) $this->db->rp_getValue(
					'orders',
					'channel_partner_customer_id',
					"id='" . $existingCartId . "' AND isDelete=0",
					0
				);
			}
		}

		$custCheck = $this->assertCustomer($cpId, $custId);
		if ($custCheck['ack'] != 1) {
			return $custCheck;
		}

		$itemsIn = array();
		/* Batch: products must be JSON array of objects.
		 * If products is blank / number / invalid → use single pwp_id path (normal App Add button). */
		$useBatch = false;
		if (isset($detail['products']) && $detail['products'] !== '' && $detail['products'] !== null) {
			$products = is_array($detail['products']) ? $detail['products'] : json_decode($detail['products'], true);
			if (is_array($products) && !empty($products) && isset($products[0]) && is_array($products[0])) {
				$useBatch = true;
				foreach ($products as $p) {
					$resolved = $this->resolvePwpId($p);
					if ($resolved['ack'] != 1) {
						return $resolved;
					}
					$qty = isset($p['qty']) ? (float) $p['qty'] : 0;
					$rate = isset($p['rate']) ? $p['rate'] : (isset($p['price']) ? $p['price'] : null);
					$disc = isset($p['discount']) ? $p['discount'] : null;
					$built = $this->buildItemFromPwp($cpId, (int) $resolved['pwp_id'], $qty, $rate, $disc);
					if ($built['ack'] != 1) {
						return $built;
					}
					$itemsIn[] = $built['item'];
				}
			}
		}

		if (!$useBatch) {
			$resolved = $this->resolvePwpId($detail);
			if ($resolved['ack'] != 1) {
				return $resolved;
			}
			$qty = isset($detail['qty']) ? (float) $detail['qty'] : 0;
			if ($qty <= 0) {
				return array('ack' => 0, 'ack_msg' => 'qty is required and must be > 0.');
			}
			$rate = isset($detail['rate']) ? $detail['rate'] : null;
			$disc = isset($detail['discount']) ? $detail['discount'] : null;
			$built = $this->buildItemFromPwp($cpId, (int) $resolved['pwp_id'], $qty, $rate, $disc);
			if ($built['ack'] != 1) {
				return $built;
			}
			$itemsIn[] = $built['item'];
		}

		if (empty($itemsIn)) {
			return array(
				'ack' => 0,
				'ack_msg' => 'Please add at least one product. Send pwp_id + qty (do NOT send products unless JSON array).',
				'example' => array(
					'pwp_id' => 2095,
					'qty' => 1,
					'channel_partner_customer_id' => 28,
				),
			);
		}

		/* Stock check against My Stock */
		$stockCheckItems = array();
		foreach ($itemsIn as $it) {
			$stockCheckItems[] = array(
				'pid' => $it['pid'],
				'weight_id' => $it['weight_id'],
				'qty' => $it['qty'],
				'pro_name' => $it['pro_name'],
			);
		}
		/* Also include existing cart qty for same products */
		$cartIdExisting = $this->getDraftCartId($cpId);
		if ($cartIdExisting > 0) {
			foreach ($itemsIn as &$itRef) {
				$existQty = (float) $this->db->rp_getValue(
					'order_product_item',
					'pro_qty',
					"order_id='" . $cartIdExisting . "' AND pro_id='" . (int) $itRef['pid'] . "' AND weight_id='" . $this->db->clean($itRef['weight_id']) . "' AND isDelete=0",
					0
				);
				$itRef['_need_total'] = $itRef['qty'] + $existQty;
			}
			unset($itRef);
			$stockCheckItems = array();
			foreach ($itemsIn as $it) {
				$stockCheckItems[] = array(
					'pid' => $it['pid'],
					'weight_id' => $it['weight_id'],
					'qty' => isset($it['_need_total']) ? $it['_need_total'] : $it['qty'],
					'pro_name' => $it['pro_name'],
				);
			}
		}
		$stockCheck = $this->objStock->validateItemsStock($cpId, $stockCheckItems);
		if (empty($stockCheck['ack'])) {
			return array(
				'ack' => 0,
				'ack_msg' => isset($stockCheck['ack_msg']) ? $stockCheck['ack_msg'] : 'Insufficient stock.',
			);
		}

		$ens = $this->ensureDraftCart($cpId, $custId, $gstFlag);
		if ($ens['ack'] != 1) {
			return $ens;
		}
		$cartId = (int) $ens['cart_id'];
		$salesId = $ens['sales_executive_id'];

		$orderDetail = array(
			'order_id' => $cartId,
			'cid' => $cpId,
			'customer_id' => $cpId,
			'sales_executive_id' => $salesId,
			'isDelete' => 0,
		);
		$_REQUEST['c_type'] = 'channel_partner';
		$reply = $this->objOrder->AddToCart($orderDetail, $itemsIn);
		if (empty($reply['ack'])) {
			return array(
				'ack' => 0,
				'ack_msg' => isset($reply['ack_msg']) ? $reply['ack_msg'] : 'Failed to add to cart.',
				'developer_msg' => isset($reply['developer_msg']) ? $reply['developer_msg'] : '',
			);
		}
		$cartId = isset($reply['order_id']) ? (int) $reply['order_id'] : $cartId;
		$this->db->rp_update(
			'orders',
			array(
				'channel_partner_order_flag' => 1,
				'cp_portal_order_flag' => 1,
				'cp_order_mode' => 'customer',
				'channel_partner_customer_id' => $custId,
			),
			"id='" . $cartId . "'",
			0
		);

		$cart = $this->formatCartResponse($cpId, $cartId);
		$cart['ack_msg'] = 'Product added to cart.';
		$cart['order_no'] = $this->ensureOrderNo($cartId);
		return $cart;
	}

	public function UpdateCartItem($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$itemId = isset($detail['cart_item_id']) ? (int) $detail['cart_item_id'] : 0;
		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}
		$cartId = $this->getDraftCartId($cpId);
		if ($cartId <= 0 || $itemId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Cart item not found.');
		}
		$ir = $this->db->rp_getData(
			'order_product_item',
			'*',
			"id='" . $itemId . "' AND order_id='" . $cartId . "' AND isDelete=0",
			'',
			0
		);
		if (!$ir || mysqli_num_rows($ir) == 0) {
			return array('ack' => 0, 'ack_msg' => 'Cart item not found.');
		}
		$row = mysqli_fetch_assoc($ir);
		$qty = isset($detail['qty']) && $detail['qty'] !== '' ? (float) $detail['qty'] : (float) $row['pro_qty'];
		if ($qty <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Qty must be greater than 0.');
		}

		$pwpId = (int) $this->db->rp_getValue(
			'product_weight_price',
			'id',
			"product_id='" . (int) $row['pro_id'] . "' AND weight_id='" . $this->db->clean($row['weight_id']) . "' AND isDelete=0",
			0
		);
		$rate = isset($detail['rate']) && $detail['rate'] !== '' ? $detail['rate'] : null;
		$disc = isset($detail['discount']) && $detail['discount'] !== '' ? $detail['discount'] : null;
		if ($rate === null) {
			/* reverse: current unitprice is net; keep as rate if no discount change without rate */
			$rate = (float) $row['unitprice'];
			if ($disc === null && isset($row['discount']) && (float) $row['discount'] > 0) {
				/* approximate rate before discount */
				$d = (float) $row['discount'];
				$rate = $rate / (1 - ($d / 100));
			}
		}
		if ($disc === null) {
			$disc = isset($row['discount']) ? $row['discount'] : 0;
		}

		$built = $this->buildItemFromPwp($cpId, $pwpId, $qty, $rate, $disc);
		if ($built['ack'] != 1) {
			return $built;
		}
		$it = $built['item'];

		$stockCheck = $this->objStock->validateItemsStock($cpId, array(array(
			'pid' => $it['pid'],
			'weight_id' => $it['weight_id'],
			'qty' => $it['qty'],
			'pro_name' => $it['pro_name'],
		)));
		if (empty($stockCheck['ack'])) {
			return array('ack' => 0, 'ack_msg' => isset($stockCheck['ack_msg']) ? $stockCheck['ack_msg'] : 'Insufficient stock.');
		}

		$upd = array(
			'pro_qty' => $it['qty'],
			'remaining_qty' => $it['qty'],
			'unitprice' => $it['price'],
			'totalprice' => $this->db->rp_num($it['qty'] * $it['price']),
			'box_qty' => $it['box_qty'],
			'cartoon_qty' => $it['cartoon_qty'],
			'discount' => $it['discount'],
			'discount_amount' => $it['discount_amount'],
			'modified_date' => date('Y-m-d H:i:s'),
		);
		$this->db->rp_update('order_product_item', $upd, "id='" . $itemId . "' AND order_id='" . $cartId . "'", 0);

		$cart = $this->formatCartResponse($cpId, $cartId);
		$cart['ack_msg'] = 'Cart item updated.';
		return $cart;
	}

	public function RemoveCartItem($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$itemId = isset($detail['cart_item_id']) ? (int) $detail['cart_item_id'] : 0;
		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}
		$cartId = $this->getDraftCartId($cpId);
		if ($cartId <= 0 || $itemId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Cart item not found.');
		}
		$own = (int) $this->db->rp_getTotalRecord(
			'order_product_item',
			"id='" . $itemId . "' AND order_id='" . $cartId . "' AND isDelete=0",
			0
		);
		if ($own <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Cart item not found.');
		}
		$this->db->rp_update(
			'order_product_item',
			array('isDelete' => 1, 'modified_date' => date('Y-m-d H:i:s')),
			"id='" . $itemId . "' AND order_id='" . $cartId . "'",
			0
		);
		$cart = $this->formatCartResponse($cpId, $cartId);
		$cart['ack_msg'] = 'Item removed from cart.';
		return $cart;
	}

	public function ClearCart($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}
		$cartId = $this->getDraftCartId($cpId);
		if ($cartId > 0) {
			$this->db->rp_update(
				'order_product_item',
				array('isDelete' => 1, 'modified_date' => date('Y-m-d H:i:s')),
				"order_id='" . $cartId . "' AND isDelete=0",
				0
			);
			$this->db->rp_update(
				'orders',
				array('isDelete' => 1, 'modified_date' => date('Y-m-d H:i:s')),
				"id='" . $cartId . "'",
				0
			);
		}
		return array('ack' => 1, 'ack_msg' => 'Cart cleared.', 'cart_id' => 0, 'items' => array());
	}

	public function PlaceOrder($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$custId = isset($detail['channel_partner_customer_id']) ? (int) $detail['channel_partner_customer_id'] : 0;
		$gstFlag = $this->resolveGstApplyFlag($detail);
		if ($gstFlag !== 0) {
			$gstFlag = 1;
		}

		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}

		$cartId = $this->getDraftCartId($cpId);
		/* Allow one-shot place with products[] (no prior cart) — same as web Submit */
		if ($cartId <= 0 && !empty($detail['products'])) {
			$add = $this->AddToCart($detail);
			if (empty($add['ack'])) {
				return $add;
			}
			$cartId = isset($add['cart_id']) ? (int) $add['cart_id'] : 0;
		}
		if ($cartId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Cart is empty. Add products first.');
		}

		$order_r = $this->db->rp_getData('orders', '*', "id='" . $cartId . "' AND isDelete=0 AND status=-1", '', 0);
		if (!$order_r) {
			return array('ack' => 0, 'ack_msg' => 'Cart not found.');
		}
		$order = mysqli_fetch_assoc($order_r);
		if ($custId <= 0) {
			$custId = isset($order['channel_partner_customer_id']) ? (int) $order['channel_partner_customer_id'] : 0;
		}
		$custCheck = $this->assertCustomer($cpId, $custId);
		if ($custCheck['ack'] != 1) {
			return $custCheck;
		}

		$itemCount = (int) $this->db->rp_getTotalRecord('order_product_item', "order_id='" . $cartId . "' AND isDelete=0", 0);
		if ($itemCount <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Please add at least one product.');
		}

		/* Stock validate */
		$stockItems = array();
		$ir = $this->db->rp_getData('order_product_item', '*', "order_id='" . $cartId . "' AND isDelete=0", '', 0);
		if ($ir) {
			while ($it = mysqli_fetch_assoc($ir)) {
				$stockItems[] = array(
					'pid' => $it['pro_id'],
					'weight_id' => $it['weight_id'],
					'qty' => $it['pro_qty'],
					'pro_name' => $it['pro_name'],
				);
			}
		}
		$stockCheck = $this->objStock->validateItemsStock($cpId, $stockItems);
		if (empty($stockCheck['ack'])) {
			return array('ack' => 0, 'ack_msg' => isset($stockCheck['ack_msg']) ? $stockCheck['ack_msg'] : 'Insufficient stock.');
		}

		$cp_exec = $this->getCpExec($cpId);
		$pre_cp_cust = $this->getCustomer($cpId, $custId);
		$sales_executive_id = !empty($cp_exec['seid']) ? $cp_exec['seid'] : 0;

		$billing_address = !empty($cp_exec['billing_address']) ? $cp_exec['billing_address'] : (isset($cp_exec['address']) ? $cp_exec['address'] : '');
		$shipping_address = !empty($cp_exec['shipping_address']) ? $cp_exec['shipping_address'] : $billing_address;
		$name_gstin = isset($cp_exec['gst']) ? $cp_exec['gst'] : '';
		$booking_pincode = isset($cp_exec['zip']) ? $cp_exec['zip'] : '';
		$booking_place = !empty($cp_exec['booking_place'])
			? $cp_exec['booking_place']
			: trim((isset($cp_exec['main_city']) ? $cp_exec['main_city'] : '') . (isset($cp_exec['state']) && $cp_exec['state'] != '' ? ', ' . $cp_exec['state'] : ''));

		if (!empty($pre_cp_cust)) {
			$name_gstin = !empty($pre_cp_cust['gst']) ? $pre_cp_cust['gst'] : $name_gstin;
			$addrParts = array();
			if (!empty($pre_cp_cust['address'])) {
				$addrParts[] = $pre_cp_cust['address'];
			}
			foreach (array('city', 'state', 'pincode', 'country') as $ak) {
				if (!empty($pre_cp_cust[$ak])) {
					$addrParts[] = $pre_cp_cust[$ak];
				}
			}
			$endAddr = implode(', ', $addrParts);
			if ($endAddr != '') {
				$billing_address = $endAddr;
				$shipping_address = $endAddr;
			}
			$booking_place = !empty($pre_cp_cust['city'])
				? ($pre_cp_cust['city'] . (!empty($pre_cp_cust['state']) ? ', ' . $pre_cp_cust['state'] : ''))
				: $booking_place;
			$booking_pincode = !empty($pre_cp_cust['pincode']) ? $pre_cp_cust['pincode'] : $booking_pincode;
		}

		/* Optional override address from App */
		if (isset($detail['address']) && trim($detail['address']) !== '') {
			$shipping_address = trim($detail['address']);
			$billing_address = $shipping_address;
		}
		if (isset($detail['shipping_address']) && trim($detail['shipping_address']) !== '') {
			$shipping_address = trim($detail['shipping_address']);
		}
		if (isset($detail['billing_address']) && trim($detail['billing_address']) !== '') {
			$billing_address = trim($detail['billing_address']);
		}

		$placeDetail = array(
			'order_id' => $cartId,
			'cid' => $cpId,
			'customer_id' => $cpId,
			'sales_executive_id' => $sales_executive_id,
			'cash_discount' => isset($cp_exec['cash_discount']) ? $cp_exec['cash_discount'] : '',
			'additional_discount' => isset($cp_exec['additional_discount']) ? $cp_exec['additional_discount'] : '',
			'cash_discount_amount' => '',
			'additional_discount_amount' => '',
			'cash_discount_flag' => 0,
			'gst_apply_flag' => $gstFlag,
			'tcs_apply_flag' => 0,
			'transport_charge' => '',
			'packing_charge' => '',
			'transport_charge_gst' => '',
			'packing_charge_gst' => '',
			'cd_gst' => '',
			'ad_gst' => '',
			'booking_place' => $booking_place,
			'booking_pincode' => $booking_pincode,
			'shipping_address' => $shipping_address,
			'billing_address' => $billing_address,
			'name_gstin' => $name_gstin,
			'type_of_company' => isset($cp_exec['type_of_company']) ? $cp_exec['type_of_company'] : '',
			'terms_comdition' => !empty($cp_exec['cp_print_header']) ? $cp_exec['cp_print_header'] : '',
			'faithfully' => !empty($cp_exec['cp_print_footer']) ? $cp_exec['cp_print_footer'] : '',
			'remarks' => isset($detail['remark']) ? $detail['remark'] : (isset($detail['remarks']) ? $detail['remarks'] : ''),
			'round_off' => '',
			/* PlaceOrderPanel requires these keys as strings (null → broken SQL) — same as web channel_partner_order_simple.php */
			'chalan_no' => '',
			'po_no' => '',
			'po_date' => date('Y-m-d'),
			'transport_name' => '',
			'transport_through' => '',
			'vendor_code' => '',
			'tendor_code' => '',
			'terms_condition_id' => '',
			'max_dispatch_date' => date('Y-m-d'),
			'channel_partner_customer_id' => $custId,
			'channel_partner_order_flag' => 1,
			'cp_portal_order_flag' => 1,
			'cp_order_mode' => 'customer',
		);

		$_REQUEST['c_type'] = 'channel_partner';
		$placed = $this->objOrder->PlaceOrderPanel($placeDetail);
		/* PlaceOrderPanel may return void/array — verify order status */
		$chk = $this->db->rp_getData('orders', 'id,order_no,status,grand_total', "id='" . $cartId . "' AND isDelete=0", '', 0);
		$chkRow = $chk ? mysqli_fetch_assoc($chk) : array();
		if (empty($chkRow) || (int) $chkRow['status'] === -1) {
			/* Fallback finalize (App): PlaceOrderPanel fails when null fields break SQL.
			 * Same end state as web success — status 0 + totals from cart lines. */
			$sub = 0;
			$qtyTot = 0;
			$gstTot = 0;
			$ir2 = $this->db->rp_getData('order_product_item', '*', "order_id='" . $cartId . "' AND isDelete=0", '', 0);
			if ($ir2) {
				while ($it2 = mysqli_fetch_assoc($ir2)) {
					$line = isset($it2['totalprice']) ? (float) $it2['totalprice'] : ((float) $it2['pro_qty'] * (float) $it2['unitprice']);
					$sub += $line;
					$qtyTot += (float) $it2['pro_qty'];
					$pct = (float) $this->db->rp_getValue('product', 'igst', "id='" . (int) $it2['pro_id'] . "' AND isDelete=0", 0);
					if ($gstFlag) {
						$gstTot += ($line * $pct) / 100;
					}
				}
			}
			$grand = $sub + $gstTot;
			$fallbackUpd = array(
				'total_qty' => $qtyTot,
				'subtotal' => $this->db->rp_num($sub),
				'grand_total' => $this->db->rp_num($grand),
				'remaining_amount' => round($grand),
				'igst_amount' => $this->db->rp_num($gstTot),
				'status' => 0,
				'order_date' => date('Y-m-d'),
				'remarks' => $placeDetail['remarks'],
				'billing_address' => $billing_address,
				'shipping_address' => $shipping_address,
				'booking_place' => $booking_place,
				'booking_pincode' => $booking_pincode,
				'sales_id' => $sales_executive_id,
				'channel_partner_order_flag' => 1,
				'cp_portal_order_flag' => 1,
				'cp_order_mode' => 'customer',
				'channel_partner_customer_id' => $custId,
				'modified_date' => date('Y-m-d H:i:s'),
			);
			$colGst = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'gst_apply_flag'");
			if ($colGst && mysqli_num_rows($colGst) > 0) {
				$fallbackUpd['gst_apply_flag'] = $gstFlag;
			}
			$okFallback = $this->db->rp_update('orders', $fallbackUpd, "id='" . $cartId . "' AND status=-1 AND isDelete=0", 0);
			$chk = $this->db->rp_getData('orders', 'id,order_no,status,grand_total', "id='" . $cartId . "' AND isDelete=0", '', 0);
			$chkRow = $chk ? mysqli_fetch_assoc($chk) : array();
			if (!$okFallback || empty($chkRow) || (int) $chkRow['status'] === -1) {
				$dev = 'PlaceOrderPanel did not finalize order';
				if (is_array($placed) && isset($placed['ack_msg'])) {
					$dev = $placed['ack_msg'];
				}
				return array(
					'ack' => 0,
					'ack_msg' => 'Failed to place order.',
					'developer_msg' => $dev,
					'cart_id' => $cartId,
					'hint' => 'Cart OK (#249). Deploy latest class.channel_partner_order.php PlaceOrder fix.',
				);
			}
		}

		$portalUpd = array(
			'channel_partner_order_flag' => 1,
			'cp_portal_order_flag' => 1,
			'cp_order_mode' => 'customer',
			'channel_partner_customer_id' => $custId,
			'shipping_address' => $shipping_address,
			'billing_address' => $billing_address,
		);
		$colGst = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'gst_apply_flag'");
		if ($colGst && mysqli_num_rows($colGst) > 0) {
			$portalUpd['gst_apply_flag'] = $gstFlag;
		}
		if (!empty($placeDetail['terms_comdition']) || !empty($placeDetail['faithfully'])) {
			$portalUpd['terms_comdition'] = $placeDetail['terms_comdition'];
			$portalUpd['faithfully'] = $placeDetail['faithfully'];
		}
		$this->db->rp_update('orders', $portalUpd, "id='" . $cartId . "'", 0);

		/* Force GST mode + totals (PlaceOrderPanel may ignore Without GST) */
		$this->syncGstModeOnOrder($cartId, $gstFlag);

		/* Always assign Order No (PI/{id}) if blank — same as web AddToCart */
		$orderNo = $this->ensureOrderNo($cartId);
		$chk = $this->db->rp_getData('orders', 'id,order_no,status,grand_total,gst_apply_flag,igst_amount,subtotal', "id='" . $cartId . "' AND isDelete=0", '', 0);
		$chkRow = $chk ? mysqli_fetch_assoc($chk) : $chkRow;
		if (empty($chkRow['order_no']) && $orderNo != '') {
			$chkRow['order_no'] = $orderNo;
		}

		$debitRes = $this->objStock->debitForCustomerOrder($cartId);
		if (empty($debitRes['ack'])) {
			return array(
				'ack' => 1,
				'ack_msg' => 'Order placed but stock debit failed: ' . (isset($debitRes['ack_msg']) ? $debitRes['ack_msg'] : ''),
				'order_id' => $cartId,
				'order_no' => isset($chkRow['order_no']) ? $chkRow['order_no'] : $orderNo,
				'gst_apply_flag' => $gstFlag,
				'stock_debited' => 0,
			);
		}

		return array(
			'ack' => 1,
			'ack_msg' => 'Order placed successfully. Stock deducted.',
			'order_id' => $cartId,
			'order_no' => isset($chkRow['order_no']) ? $chkRow['order_no'] : $orderNo,
			'grand_total' => isset($chkRow['grand_total']) ? round((float) $chkRow['grand_total'], 2) : 0,
			'sub_total' => isset($chkRow['subtotal']) ? round((float) $chkRow['subtotal'], 2) : 0,
			'gst_amount' => isset($chkRow['igst_amount']) ? round((float) $chkRow['igst_amount'], 2) : 0,
			'gst_apply_flag' => $gstFlag,
			'channel_partner_customer_id' => $custId,
			'stock_debited' => 1,
			'print_url' => 'bbsales_tracking/channel_partner_order_print.php?order_id=' . $cartId,
		);
	}

	public function GetOrderList($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}
		$search = isset($detail['search_name']) ? trim($detail['search_name']) : '';
		$ul = isset($detail['ul']) ? (int) $detail['ul'] : 0;
		$ll = isset($detail['ll']) ? (int) $detail['ll'] : 50;
		if ($ll <= 0) {
			$ll = 50;
		}

		$where = "o.customer_id='" . $cpId . "' AND o.channel_partner_order_flag=1 AND o.cp_order_mode='customer'"
			. " AND o.channel_partner_customer_id>0 AND o.isDelete=0 AND o.status!=-1";
		if ($search != '') {
			$s = $this->db->clean($search);
			$where .= " AND (o.order_no LIKE '%" . $s . "%' OR c.company_name LIKE '%" . $s . "%' OR c.person_name LIKE '%" . $s . "%' OR c.mobile_no LIKE '%" . $s . "%')";
		}

		$countSql = "SELECT COUNT(*) AS total FROM orders o"
			. " LEFT JOIN channel_partner_customer c ON c.id=o.channel_partner_customer_id"
			. " WHERE " . $where;
		$countRes = mysqli_query($this->db->myconn, $countSql);
		$total = 0;
		if ($countRes && $cr = mysqli_fetch_assoc($countRes)) {
			$total = (int) $cr['total'];
		}

		$sql = "SELECT o.id, o.order_no, o.order_date, o.grand_total, o.payment_received_flag, o.payment_received_amount,"
			. " o.status, o.channel_partner_customer_id, o.shipping_address, o.billing_address,"
			. " c.company_name AS party_name, c.person_name, c.mobile_no, c.address AS customer_address"
			. " FROM orders o"
			. " LEFT JOIN channel_partner_customer c ON c.id=o.channel_partner_customer_id"
			. " WHERE " . $where
			. " ORDER BY o.id DESC LIMIT " . $ul . "," . $ll;
		$res = mysqli_query($this->db->myconn, $sql);
		$result = array();
		if ($res) {
			while ($row = mysqli_fetch_assoc($res)) {
				$oid = (int) $row['id'];
				if ($oid > 0 && (trim((string) $row['order_no']) === '')) {
					$row['order_no'] = $this->ensureOrderNo($oid);
				}
				$qty = $this->db->rp_getValue('order_product_item', 'SUM(pro_qty)', "order_id='" . (int) $row['id'] . "' AND isDelete=0", 0);
				$wf = $this->workflowStatus($row['status'], $row['payment_received_flag'], $row['grand_total'], $row['payment_received_amount']);
				$party = trim($row['party_name']);
				if ($party == '') {
					$party = trim($row['person_name']);
				}
				$paidFlag = (int) $row['payment_received_flag'];
				$paidAmt = (float) $row['payment_received_amount'];
				$result[] = array(
					'order_id' => (int) $row['id'],
					'order_no' => $row['order_no'],
					'order_date' => $row['order_date'],
					'order_date_display' => ($row['order_date'] != '' && $row['order_date'] != '0000-00-00') ? date('d-m-Y', strtotime($row['order_date'])) : '-',
					'channel_partner_customer_id' => (int) $row['channel_partner_customer_id'],
					'customer_name' => $party != '' ? $party : '-',
					'mobile_no' => isset($row['mobile_no']) ? $row['mobile_no'] : '',
					'address' => !empty($row['shipping_address']) ? $row['shipping_address'] : (isset($row['customer_address']) ? $row['customer_address'] : ''),
					'total_qty' => round((float) $qty, 2),
					'amount' => round((float) $row['grand_total'], 2),
					'amount_display' => number_format((float) $row['grand_total'], 2),
					'payment_label' => ($paidFlag === 1 && $paidAmt > 0) ? ('Received ' . number_format($paidAmt, 2)) : 'Pending',
					'payment_received_flag' => $paidFlag,
					'payment_received_amount' => round($paidAmt, 2),
					'status' => $wf['status'],
					'status_label' => $wf['status_label'],
					'baki_amount' => $wf['baki_amount'],
					'order_status_code' => (int) $row['status'],
					'can_edit' => ($wf['status'] === 'pending') ? 1 : 0,
					'can_delete' => ($wf['status'] === 'pending') ? 1 : 0,
				);
			}
		}

		return array(
			'ack' => 1,
			'ack_msg' => 'Customer orders fetched.',
			'total' => $total,
			'result' => $result,
		);
	}

	/**
	 * Pending only — not dispatched / not paid.
	 */
	private function assertOrderModifiable($order)
	{
		if (!$order) {
			return array('ack' => 0, 'ack_msg' => 'Order not found.');
		}
		$status = isset($order['status']) ? (int) $order['status'] : 0;
		$paidFlag = isset($order['payment_received_flag']) ? (int) $order['payment_received_flag'] : 0;
		$paidAmt = isset($order['payment_received_amount']) ? (float) $order['payment_received_amount'] : 0;
		if ($paidFlag === 1 && $paidAmt > 0) {
			return array('ack' => 0, 'ack_msg' => 'Paid / Completed order cannot be edited or deleted.');
		}
		if ($status >= 5 && $status != 3 && $status != -2) {
			return array('ack' => 0, 'ack_msg' => 'Dispatched order cannot be edited or deleted.');
		}
		if ($status == 3 || $status == -2) {
			return array('ack' => 0, 'ack_msg' => 'Cancelled order cannot be modified.');
		}
		return array('ack' => 1);
	}

	private function loadCpCustomerOrder($cpId, $orderId)
	{
		$where = "id='" . (int) $orderId . "' AND customer_id='" . (int) $cpId . "' AND channel_partner_order_flag=1"
			. " AND cp_order_mode='customer' AND isDelete=0 AND status!=-1";
		$or = $this->db->rp_getData('orders', '*', $where, '', 0);
		if (!$or || mysqli_num_rows($or) == 0) {
			return null;
		}
		return mysqli_fetch_assoc($or);
	}

	/**
	 * Same formula as web channel_partner_order_simple.js:
	 * gross = qty * rate
	 * base  = gross - (gross * discount% / 100)
	 * gst   = gst_apply ? base * product.igst% / 100 : 0
	 * grand = base + gst
	 */
	private function resolveGstApplyFlag($detail, $order = null)
	{
		if (is_array($detail)) {
			if (array_key_exists('gst_apply_flag', $detail) && $detail['gst_apply_flag'] !== '' && $detail['gst_apply_flag'] !== null) {
				return ((int) $detail['gst_apply_flag'] === 0) ? 0 : 1;
			}
			/* App aliases */
			if (isset($detail['without_gst']) && (string) $detail['without_gst'] !== '' && (int) $detail['without_gst'] === 1) {
				return 0;
			}
			if (isset($detail['with_gst']) && (string) $detail['with_gst'] !== '') {
				return ((int) $detail['with_gst'] === 0) ? 0 : 1;
			}
		}
		if (is_array($order) && array_key_exists('gst_apply_flag', $order) && $order['gst_apply_flag'] !== '' && $order['gst_apply_flag'] !== null) {
			return ((int) $order['gst_apply_flag'] === 0) ? 0 : 1;
		}
		return 1;
	}

	/**
	 * Persist GST mode + recalculate line GST and order totals (After Place / Update).
	 */
	private function syncGstModeOnOrder($orderId, $gstFlag)
	{
		$orderId = (int) $orderId;
		$gstFlag = ((int) $gstFlag === 0) ? 0 : 1;
		if ($orderId <= 0) {
			return;
		}
		$ir = $this->db->rp_getData('order_product_item', '*', "order_id='" . $orderId . "' AND isDelete=0", 'id ASC', 0);
		if ($ir) {
			while ($row = mysqli_fetch_assoc($ir)) {
				$line = isset($row['totalprice']) ? (float) $row['totalprice'] : ((float) $row['pro_qty'] * (float) $row['unitprice']);
				$pct = (float) $this->db->rp_getValue('product', 'igst', "id='" . (int) $row['pro_id'] . "' AND isDelete=0", 0);
				$gstAmt = $gstFlag ? (($line * $pct) / 100) : 0;
				$colIg = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `order_product_item` LIKE 'igst_amount'");
				if ($colIg && mysqli_num_rows($colIg) > 0) {
					$this->db->rp_update(
						'order_product_item',
						array('igst_amount' => $this->db->rp_num($gstAmt)),
						"id='" . (int) $row['id'] . "'",
						0
					);
				}
			}
		}
		$this->applyOrderTotals($orderId, $gstFlag);
	}

	private function sumOrderItemTotals($orderId, $gstFlag)
	{
		$sub = 0;
		$gstTot = 0;
		$qtyTot = 0;
		$gstFlag = ((int) $gstFlag === 0) ? 0 : 1;
		$ir = $this->db->rp_getData('order_product_item', '*', "order_id='" . (int) $orderId . "' AND isDelete=0", 'id ASC', 0);
		if ($ir) {
			while ($row = mysqli_fetch_assoc($ir)) {
				$qty = (float) $row['pro_qty'];
				$line = isset($row['totalprice']) ? (float) $row['totalprice'] : ($qty * (float) $row['unitprice']);
				$sub += $line;
				$qtyTot += $qty;
				if ($gstFlag) {
					if (isset($row['igst_amount']) && $row['igst_amount'] !== '' && $row['igst_amount'] !== null) {
						$gstTot += (float) $row['igst_amount'];
					} else {
						$pct = (float) $this->db->rp_getValue('product', 'igst', "id='" . (int) $row['pro_id'] . "' AND isDelete=0", 0);
						$gstTot += ($line * $pct) / 100;
					}
				}
			}
		}
		return array(
			'sub_total' => round($sub, 2),
			'gst_amount' => round($gstTot, 2),
			'grand_total' => round($sub + $gstTot, 2),
			'total_qty' => round($qtyTot, 2),
		);
	}

	private function applyOrderTotals($orderId, $gstFlag, $detail = null)
	{
		$calc = $this->sumOrderItemTotals($orderId, $gstFlag);
		$sub = $calc['sub_total'];
		$gst = $calc['gst_amount'];
		$grand = $calc['grand_total'];

		/* Optional app-sent totals (trust when provided) */
		if (is_array($detail)) {
			if (isset($detail['sub_total']) && $detail['sub_total'] !== '' && is_numeric($detail['sub_total'])) {
				$sub = round((float) $detail['sub_total'], 2);
			} else if (isset($detail['subtotal']) && $detail['subtotal'] !== '' && is_numeric($detail['subtotal'])) {
				$sub = round((float) $detail['subtotal'], 2);
			}
			if (isset($detail['gst_amount']) && $detail['gst_amount'] !== '' && is_numeric($detail['gst_amount'])) {
				$gst = round((float) $detail['gst_amount'], 2);
			} else if (isset($detail['igst_amount']) && $detail['igst_amount'] !== '' && is_numeric($detail['igst_amount'])) {
				$gst = round((float) $detail['igst_amount'], 2);
			}
			if (isset($detail['grand_total']) && $detail['grand_total'] !== '' && is_numeric($detail['grand_total'])) {
				$grand = round((float) $detail['grand_total'], 2);
			}
			if (!$gstFlag) {
				$gst = 0;
				if (!isset($detail['grand_total']) || $detail['grand_total'] === '' || !is_numeric($detail['grand_total'])) {
					$grand = $sub;
				}
			}
		}

		$upd = array(
			'total_qty' => $calc['total_qty'],
			'subtotal' => $this->db->rp_num($sub),
			'igst_amount' => $this->db->rp_num($gst),
			'grand_total' => $this->db->rp_num($grand),
			'remaining_amount' => round($grand),
			'modified_date' => date('Y-m-d H:i:s'),
		);
		$colGst = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'gst_apply_flag'");
		if ($colGst && mysqli_num_rows($colGst) > 0) {
			$upd['gst_apply_flag'] = ((int) $gstFlag === 0) ? 0 : 1;
		}
		$this->db->rp_update('orders', $upd, "id='" . (int) $orderId . "'", 0);

		return array(
			'sub_total' => round($sub, 2),
			'gst_amount' => round($gst, 2),
			'grand_total' => round($grand, 2),
			'total_qty' => $calc['total_qty'],
			'gst_apply_flag' => ((int) $gstFlag === 0) ? 0 : 1,
		);
	}

	/**
	 * Soft-delete CP customer order (Pending only) + credit stock back.
	 */
	public function DeleteCustomerOrder($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$orderId = isset($detail['order_id']) ? (int) $detail['order_id'] : (isset($detail['id']) ? (int) $detail['id'] : 0);
		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}
		if ($orderId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'order_id is required.');
		}
		$order = $this->loadCpCustomerOrder($cpId, $orderId);
		if (!$order) {
			return array('ack' => 0, 'ack_msg' => 'Order not found.');
		}
		$mod = $this->assertOrderModifiable($order);
		if ($mod['ack'] != 1) {
			return $mod;
		}

		$credit = $this->objStock->creditBackForCustomerOrder($orderId);
		$this->db->rp_update(
			'order_product_item',
			array('isDelete' => 1, 'modified_date' => date('Y-m-d H:i:s')),
			"order_id='" . $orderId . "' AND isDelete=0",
			0
		);
		$this->db->rp_update(
			'orders',
			array(
				'isDelete' => 1,
				'status' => 3,
				'modified_date' => date('Y-m-d H:i:s'),
			),
			"id='" . $orderId . "'",
			0
		);

		return array(
			'ack' => 1,
			'ack_msg' => 'Order deleted successfully.' . (empty($credit['ack']) ? '' : (' ' . $credit['ack_msg'])),
			'order_id' => $orderId,
			'order_no' => isset($order['order_no']) ? $order['order_no'] : '',
			'stock_credited' => (!empty($credit['ack']) && empty($credit['already'])) ? 1 : 0,
		);
	}

	/**
	 * Append one product line to Pending CP customer order (Edit Order → + Add Item).
	 * Same product+weight merges qty on existing line.
	 */
	public function AddCustomerOrderItem($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$orderId = isset($detail['order_id']) ? (int) $detail['order_id'] : (isset($detail['id']) ? (int) $detail['id'] : 0);
		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}
		if ($orderId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'order_id is required.');
		}
		$order = $this->loadCpCustomerOrder($cpId, $orderId);
		if (!$order) {
			return array('ack' => 0, 'ack_msg' => 'Order not found.');
		}
		$mod = $this->assertOrderModifiable($order);
		if ($mod['ack'] != 1) {
			return $mod;
		}

		$resolved = $this->resolvePwpId($detail);
		if ($resolved['ack'] != 1) {
			return $resolved;
		}
		$qty = isset($detail['qty']) ? (float) $detail['qty'] : 0;
		if ($qty <= 0) {
			return array('ack' => 0, 'ack_msg' => 'qty is required and must be > 0.');
		}
		$rate = isset($detail['rate']) ? $detail['rate'] : (isset($detail['price']) ? $detail['price'] : null);
		$disc = isset($detail['discount']) ? $detail['discount'] : null;
		$built = $this->buildItemFromPwp($cpId, (int) $resolved['pwp_id'], $qty, $rate, $disc);
		if ($built['ack'] != 1) {
			return $built;
		}
		$it = $built['item'];

		$existRow = null;
		$existR = $this->db->rp_getData(
			'order_product_item',
			'*',
			"order_id='" . $orderId . "' AND pro_id='" . (int) $it['pid'] . "' AND weight_id='" . $this->db->clean($it['weight_id']) . "' AND isDelete=0",
			'id ASC',
			0
		);
		if ($existR && mysqli_num_rows($existR) > 0) {
			$existRow = mysqli_fetch_assoc($existR);
		}

		$existQty = $existRow ? (float) $existRow['pro_qty'] : 0;
		$stockDebited = !empty($order['cp_stock_debited']) && (int) $order['cp_stock_debited'] === 1;
		$needForCheck = $stockDebited ? $qty : ($existQty + $qty);
		$stockCheck = $this->objStock->validateItemsStock($cpId, array(
			array(
				'pid' => $it['pid'],
				'weight_id' => $it['weight_id'],
				'qty' => $needForCheck,
				'pro_name' => $it['pro_name'],
			),
		));
		if (empty($stockCheck['ack'])) {
			return array(
				'ack' => 0,
				'ack_msg' => isset($stockCheck['ack_msg']) ? $stockCheck['ack_msg'] : 'Insufficient stock.',
			);
		}

		$gstFlag = $this->resolveGstApplyFlag($detail, $order);

		$newQty = $existQty + $qty;
		$lineBase = $newQty * (float) $it['price'];
		$gstPct = isset($it['gst_percent']) ? (float) $it['gst_percent'] : 0;
		$gstAmt = $gstFlag ? (($lineBase * $gstPct) / 100) : 0;
		$itemId = 0;
		$merged = 0;

		/* Optional app-sent line amount / gst for NEW qty only (when not merge) */
		$appLineAmount = null;
		$appLineGst = null;
		if (isset($detail['amount']) && $detail['amount'] !== '' && is_numeric($detail['amount'])) {
			$appLineAmount = (float) $detail['amount'];
		} else if (isset($detail['total']) && $detail['total'] !== '' && is_numeric($detail['total'])) {
			$appLineAmount = (float) $detail['total'];
		} else if (isset($detail['line_base']) && $detail['line_base'] !== '' && is_numeric($detail['line_base'])) {
			$appLineAmount = (float) $detail['line_base'];
		}
		if (isset($detail['item_gst_amount']) && $detail['item_gst_amount'] !== '' && is_numeric($detail['item_gst_amount'])) {
			$appLineGst = (float) $detail['item_gst_amount'];
		} else if (isset($detail['gst_amount']) && $detail['gst_amount'] !== '' && is_numeric($detail['gst_amount']) && !isset($detail['sub_total']) && !isset($detail['grand_total'])) {
			/* per-line gst only when order totals not also sent under same key ambiguity — skip if order-level keys present */
			$appLineGst = (float) $detail['gst_amount'];
		}

		if ($existRow) {
			$saveLineBase = $lineBase;
			$saveGstAmt = $gstAmt;
			if ($appLineAmount !== null && !$merged) {
				/* merge: keep server lineBase for combined qty */
			}
			$updItem = array(
				'pro_qty' => $newQty,
				'remaining_qty' => $newQty,
				'unitprice' => $it['price'],
				'totalprice' => $this->db->rp_num($saveLineBase),
				'discount' => $it['discount'],
				'discount_amount' => $it['discount_amount'],
				'box_qty' => $it['box_qty'],
				'cartoon_qty' => $it['cartoon_qty'],
				'modified_date' => date('Y-m-d H:i:s'),
			);
			$colIg = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `order_product_item` LIKE 'igst_amount'");
			if ($colIg && mysqli_num_rows($colIg) > 0) {
				$updItem['igst_amount'] = $this->db->rp_num($saveGstAmt);
			}
			$this->db->rp_update('order_product_item', $updItem, "id='" . (int) $existRow['id'] . "'", 0);
			$itemId = (int) $existRow['id'];
			$merged = 1;
		} else {
			$saveLineBase = ($appLineAmount !== null) ? $appLineAmount : ($qty * (float) $it['price']);
			$saveGstAmt = $gstFlag ? (($appLineGst !== null) ? $appLineGst : (($saveLineBase * $gstPct) / 100)) : 0;
			$rows = array(
				'order_id', 'pro_id', 'weight_id', 'pro_name', 'pro_qty', 'remaining_qty',
				'unitprice', 'totalprice', 'discount', 'discount_amount', 'box_qty', 'cartoon_qty',
				'brand_id', 'isDelete', 'isActive', 'created_date',
			);
			$values = array(
				$orderId,
				$it['pid'],
				$it['weight_id'],
				$it['pro_name'],
				$qty,
				$qty,
				$it['price'],
				$this->db->rp_num($saveLineBase),
				$it['discount'],
				$it['discount_amount'],
				$it['box_qty'],
				$it['cartoon_qty'],
				$it['brand_id'],
				0,
				1,
				date('Y-m-d H:i:s'),
			);
			$colIg = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `order_product_item` LIKE 'igst_amount'");
			if ($colIg && mysqli_num_rows($colIg) > 0) {
				$rows[] = 'igst_amount';
				$values[] = $this->db->rp_num($saveGstAmt);
			}
			$this->db->rp_insert('order_product_item', $values, $rows, 0);
			$itemId = (int) $this->db->rp_getValue('order_product_item', 'MAX(id)', "order_id='" . $orderId . "'", 0);
			$merged = 0;
		}

		$this->applyOrderTotals($orderId, $gstFlag, $detail);
		$this->ensureOrderNo($orderId);

		$stockMsg = '';
		if ($stockDebited) {
			$salesId = isset($order['sales_id']) ? (int) $order['sales_id'] : 0;
			$orderNo = isset($order['order_no']) ? $order['order_no'] : $this->ensureOrderNo($orderId);
			$debitRes = $this->objStock->addMovement(
				$cpId,
				(int) $it['pid'],
				$it['weight_id'],
				$it['pro_name'],
				-1 * $qty,
				'OUT add item Customer Order ' . $orderNo,
				$orderId,
				$salesId,
				'out'
			);
			if (empty($debitRes['ack'])) {
				$stockMsg = ' (Stock: ' . (isset($debitRes['ack_msg']) ? $debitRes['ack_msg'] : 'debit failed') . ')';
			}
		}

		$detailOut = $this->GetOrderDetail(array('channel_partner_id' => $cpId, 'order_id' => $orderId));
		return array(
			'ack' => 1,
			'ack_msg' => ($merged ? 'Item quantity updated on order.' : 'Item added to order.') . $stockMsg,
			'order_id' => $orderId,
			'order_no' => isset($order['order_no']) ? $order['order_no'] : $this->ensureOrderNo($orderId),
			'item_id' => $itemId,
			'merged' => $merged,
			'added_qty' => round($qty, 2),
			'sub_total' => isset($detailOut['result']['sub_total']) ? $detailOut['result']['sub_total'] : 0,
			'gst_amount' => isset($detailOut['result']['gst_amount']) ? $detailOut['result']['gst_amount'] : 0,
			'grand_total' => isset($detailOut['result']['grand_total']) ? $detailOut['result']['grand_total'] : 0,
			'result' => isset($detailOut['result']) ? $detailOut['result'] : array(),
		);
	}

	/**
	 * Update Pending CP customer order — party / address / remark / products.
	 * products: JSON [{pwp_id, qty, rate, discount}] replaces all lines when sent.
	 */
	public function UpdateCustomerOrder($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$orderId = isset($detail['order_id']) ? (int) $detail['order_id'] : (isset($detail['id']) ? (int) $detail['id'] : 0);
		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}
		if ($orderId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'order_id is required.');
		}
		$order = $this->loadCpCustomerOrder($cpId, $orderId);
		if (!$order) {
			return array('ack' => 0, 'ack_msg' => 'Order not found.');
		}
		$mod = $this->assertOrderModifiable($order);
		if ($mod['ack'] != 1) {
			return $mod;
		}

		$custId = isset($detail['channel_partner_customer_id']) ? (int) $detail['channel_partner_customer_id'] : 0;
		if ($custId <= 0) {
			$custId = isset($order['channel_partner_customer_id']) ? (int) $order['channel_partner_customer_id'] : 0;
		}
		$custCheck = $this->assertCustomer($cpId, $custId);
		if ($custCheck['ack'] != 1) {
			return $custCheck;
		}

		$gstFlag = $this->resolveGstApplyFlag($detail, $order);

		$replaceItems = false;
		$itemsIn = array();
		if (!empty($detail['products'])) {
			$products = is_array($detail['products']) ? $detail['products'] : json_decode($detail['products'], true);
			if (is_array($products) && !empty($products)) {
				$replaceItems = true;
				foreach ($products as $p) {
					$resolved = $this->resolvePwpId($p);
					if ($resolved['ack'] != 1) {
						return $resolved;
					}
					$qty = isset($p['qty']) ? (float) $p['qty'] : 0;
					$rate = isset($p['rate']) ? $p['rate'] : (isset($p['price']) ? $p['price'] : null);
					$disc = isset($p['discount']) ? $p['discount'] : null;
					$built = $this->buildItemFromPwp($cpId, (int) $resolved['pwp_id'], $qty, $rate, $disc);
					if ($built['ack'] != 1) {
						return $built;
					}
					/* Optional app line totals */
					if (isset($p['line_base']) && $p['line_base'] !== '' && is_numeric($p['line_base'])) {
						$built['item']['app_line_base'] = (float) $p['line_base'];
					} else if (isset($p['amount']) && $p['amount'] !== '' && is_numeric($p['amount'])) {
						$built['item']['app_line_base'] = (float) $p['amount'];
					}
					if (isset($p['item_gst_amount']) && $p['item_gst_amount'] !== '' && is_numeric($p['item_gst_amount'])) {
						$built['item']['app_line_gst'] = (float) $p['item_gst_amount'];
					} else if (isset($p['gst_amount']) && $p['gst_amount'] !== '' && is_numeric($p['gst_amount'])) {
						$built['item']['app_line_gst'] = (float) $p['gst_amount'];
					}
					$itemsIn[] = $built['item'];
				}
			}
		} else if (isset($detail['pwp_id']) && (int) $detail['pwp_id'] > 0) {
			$replaceItems = true;
			$resolved = $this->resolvePwpId($detail);
			if ($resolved['ack'] != 1) {
				return $resolved;
			}
			$qty = isset($detail['qty']) ? (float) $detail['qty'] : 0;
			$built = $this->buildItemFromPwp($cpId, (int) $resolved['pwp_id'], $qty, isset($detail['rate']) ? $detail['rate'] : null, isset($detail['discount']) ? $detail['discount'] : null);
			if ($built['ack'] != 1) {
				return $built;
			}
			$itemsIn[] = $built['item'];
		}

		if ($replaceItems) {
			if (empty($itemsIn)) {
				return array('ack' => 0, 'ack_msg' => 'Please keep at least one product.');
			}
			$stockCheckItems = array();
			foreach ($itemsIn as $it) {
				$stockCheckItems[] = array(
					'pid' => $it['pid'],
					'weight_id' => $it['weight_id'],
					'qty' => $it['qty'],
					'pro_name' => $it['pro_name'],
				);
			}
			/* After credit-back, full qty must be available again */
			$this->objStock->creditBackForCustomerOrder($orderId);

			$stockCheck = $this->objStock->validateItemsStock($cpId, $stockCheckItems);
			if (empty($stockCheck['ack'])) {
				/* re-debit old stock so order stays consistent if validation fails */
				$this->objStock->debitForCustomerOrder($orderId, true);
				return array(
					'ack' => 0,
					'ack_msg' => isset($stockCheck['ack_msg']) ? $stockCheck['ack_msg'] : 'Insufficient stock.',
				);
			}

			$this->db->rp_update(
				'order_product_item',
				array('isDelete' => 1, 'modified_date' => date('Y-m-d H:i:s')),
				"order_id='" . $orderId . "' AND isDelete=0",
				0
			);

			foreach ($itemsIn as $it) {
				$lineBase = isset($it['app_line_base']) ? (float) $it['app_line_base'] : ((float) $it['qty'] * (float) $it['price']);
				$gstPct = isset($it['gst_percent']) ? (float) $it['gst_percent'] : 0;
				if (isset($it['app_line_gst'])) {
					$gstAmt = $gstFlag ? (float) $it['app_line_gst'] : 0;
				} else {
					$gstAmt = $gstFlag ? (($lineBase * $gstPct) / 100) : 0;
				}
				$rows = array(
					'order_id', 'pro_id', 'weight_id', 'pro_name', 'pro_qty', 'remaining_qty',
					'unitprice', 'totalprice', 'discount', 'discount_amount', 'box_qty', 'cartoon_qty',
					'brand_id', 'isDelete', 'isActive', 'created_date',
				);
				$values = array(
					$orderId,
					$it['pid'],
					$it['weight_id'],
					$it['pro_name'],
					$it['qty'],
					$it['qty'],
					$it['price'],
					$this->db->rp_num($lineBase),
					$it['discount'],
					$it['discount_amount'],
					$it['box_qty'],
					$it['cartoon_qty'],
					$it['brand_id'],
					0,
					1,
					date('Y-m-d H:i:s'),
				);
				$colIg = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `order_product_item` LIKE 'igst_amount'");
				if ($colIg && mysqli_num_rows($colIg) > 0) {
					$rows[] = 'igst_amount';
					$values[] = $this->db->rp_num($gstAmt);
				}
				$this->db->rp_insert('order_product_item', $values, $rows, 0);
			}

			$totals = $this->applyOrderTotals($orderId, $gstFlag, $detail);
			/* Ensure line GST matches mode (esp. Without GST → 0) */
			$this->syncGstModeOnOrder($orderId, $gstFlag);
			if (is_array($detail) && (isset($detail['sub_total']) || isset($detail['grand_total']) || isset($detail['gst_amount']))) {
				$this->applyOrderTotals($orderId, $gstFlag, $detail);
			}
			$upd = array(
				'channel_partner_customer_id' => $custId,
				'modified_date' => date('Y-m-d H:i:s'),
			);
		} else {
			$upd = array(
				'channel_partner_customer_id' => $custId,
				'modified_date' => date('Y-m-d H:i:s'),
			);
			/* Header-only update can still save app totals / gst flag */
			if (isset($detail['sub_total']) || isset($detail['subtotal']) || isset($detail['gst_amount']) || isset($detail['grand_total']) || array_key_exists('gst_apply_flag', $detail) || isset($detail['without_gst']) || isset($detail['with_gst'])) {
				$this->syncGstModeOnOrder($orderId, $gstFlag);
			} else {
				$colGst = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'gst_apply_flag'");
				if ($colGst && mysqli_num_rows($colGst) > 0 && array_key_exists('gst_apply_flag', $detail)) {
					$upd['gst_apply_flag'] = $gstFlag;
				}
			}
		}

		if (isset($detail['remark']) || isset($detail['remarks'])) {
			$upd['remarks'] = isset($detail['remark']) ? $detail['remark'] : $detail['remarks'];
		}
		if (isset($detail['address']) && trim($detail['address']) !== '') {
			$upd['shipping_address'] = trim($detail['address']);
			$upd['billing_address'] = trim($detail['address']);
		}
		if (isset($detail['shipping_address']) && trim($detail['shipping_address']) !== '') {
			$upd['shipping_address'] = trim($detail['shipping_address']);
		}
		if (isset($detail['billing_address']) && trim($detail['billing_address']) !== '') {
			$upd['billing_address'] = trim($detail['billing_address']);
		}

		$this->db->rp_update('orders', $upd, "id='" . $orderId . "'", 0);
		$this->ensureOrderNo($orderId);

		if ($replaceItems) {
			$debit = $this->objStock->debitForCustomerOrder($orderId, true);
			if (empty($debit['ack'])) {
				return array(
					'ack' => 1,
					'ack_msg' => 'Order updated but stock debit failed: ' . (isset($debit['ack_msg']) ? $debit['ack_msg'] : ''),
					'order_id' => $orderId,
					'stock_debited' => 0,
				);
			}
		}

		$detailOut = $this->GetOrderDetail(array('channel_partner_id' => $cpId, 'order_id' => $orderId));
		return array(
			'ack' => 1,
			'ack_msg' => 'Order updated successfully.',
			'order_id' => $orderId,
			'order_no' => isset($order['order_no']) ? $order['order_no'] : $this->ensureOrderNo($orderId),
			'sub_total' => isset($detailOut['result']['sub_total']) ? $detailOut['result']['sub_total'] : 0,
			'gst_amount' => isset($detailOut['result']['gst_amount']) ? $detailOut['result']['gst_amount'] : 0,
			'grand_total' => isset($detailOut['result']['grand_total']) ? $detailOut['result']['grand_total'] : 0,
			'gst_apply_flag' => isset($detailOut['result']['gst_apply_flag']) ? $detailOut['result']['gst_apply_flag'] : 1,
			'result' => isset($detailOut['result']) ? $detailOut['result'] : array(),
		);
	}

	public function GetOrderDetail($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$orderId = isset($detail['order_id']) ? (int) $detail['order_id'] : (isset($detail['id']) ? (int) $detail['id'] : 0);
		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}
		if ($orderId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'order_id is required.');
		}
		$where = "id='" . $orderId . "' AND customer_id='" . $cpId . "' AND channel_partner_order_flag=1"
			. " AND cp_order_mode='customer' AND isDelete=0 AND status!=-1";
		$or = $this->db->rp_getData('orders', '*', $where, '', 0);
		if (!$or || mysqli_num_rows($or) == 0) {
			return array('ack' => 0, 'ack_msg' => 'Order not found.');
		}
		$row = mysqli_fetch_assoc($or);
		$partyId = (int) $row['channel_partner_customer_id'];
		$cust = $this->getCustomer($cpId, $partyId);
		$wf = $this->workflowStatus($row['status'], $row['payment_received_flag'], $row['grand_total'], $row['payment_received_amount']);

		$items = array();
		$gstFlag = $this->resolveGstApplyFlag(array(), $row);
		$ir = $this->db->rp_getData('order_product_item', '*', "order_id='" . $orderId . "' AND isDelete=0", 'id ASC', 0);
		if ($ir) {
			while ($it = mysqli_fetch_assoc($ir)) {
				$lineBase = isset($it['totalprice']) ? (float) $it['totalprice'] : ((float) $it['pro_qty'] * (float) $it['unitprice']);
				$gstPct = (float) $this->db->rp_getValue('product', 'igst', "id='" . (int) $it['pro_id'] . "' AND isDelete=0", 0);
				$lineGst = 0;
				if ($gstFlag) {
					$lineGst = isset($it['igst_amount']) ? (float) $it['igst_amount'] : (($lineBase * $gstPct) / 100);
				}
				$pwpId = (int) $this->db->rp_getValue(
					'product_weight_price',
					'id',
					"product_id='" . (int) $it['pro_id'] . "' AND weight_id='" . $this->db->clean($it['weight_id']) . "' AND isDelete=0",
					0
				);
				$items[] = array(
					'item_id' => (int) $it['id'],
					'pwp_id' => $pwpId,
					'pid' => (int) $it['pro_id'],
					'weight_id' => $it['weight_id'],
					'product_name' => $it['pro_name'],
					'qty' => round((float) $it['pro_qty'], 2),
					'rate' => round((float) $it['unitprice'], 2),
					'discount' => isset($it['discount']) ? round((float) $it['discount'], 2) : 0,
					'gst_percent' => round($gstPct, 2),
					'line_base' => round($lineBase, 2),
					'amount' => round($lineBase, 2),
					'gst_amount' => round($lineGst, 2),
					'line_total' => round($lineBase + $lineGst, 2),
				);
			}
		}

		/* Prefer live item sum (correct columns: orders.subtotal / orders.igst_amount) */
		$calc = $this->sumOrderItemTotals($orderId, $gstFlag);
		$subTotal = $calc['sub_total'];
		$gstAmount = $calc['gst_amount'];
		$grandTotal = $calc['grand_total'];
		if (isset($row['subtotal']) && (float) $row['subtotal'] > 0) {
			$subTotal = round((float) $row['subtotal'], 2);
		}
		if (isset($row['igst_amount'])) {
			$gstAmount = $gstFlag ? round((float) $row['igst_amount'], 2) : 0;
		}
		if (isset($row['grand_total']) && (float) $row['grand_total'] > 0) {
			$grandTotal = round((float) $row['grand_total'], 2);
		} else {
			$grandTotal = round($subTotal + $gstAmount, 2);
		}

		return array(
			'ack' => 1,
			'ack_msg' => 'Order detail ready',
			'result' => array(
				'order_id' => (int) $row['id'],
				'order_no' => $row['order_no'],
				'order_date' => $row['order_date'],
				'order_date_display' => ($row['order_date'] != '' && $row['order_date'] != '0000-00-00') ? date('d-m-Y', strtotime($row['order_date'])) : '-',
				'channel_partner_customer_id' => $partyId,
				'customer_name' => !empty($cust['company_name']) ? $cust['company_name'] : '',
				'person_name' => !empty($cust['person_name']) ? $cust['person_name'] : '',
				'mobile_no' => !empty($cust['mobile_no']) ? $cust['mobile_no'] : '',
				'gst' => !empty($cust['gst']) ? $cust['gst'] : '',
				'address' => !empty($row['shipping_address']) ? $row['shipping_address'] : (!empty($cust['address']) ? $cust['address'] : ''),
				'shipping_address' => isset($row['shipping_address']) ? $row['shipping_address'] : '',
				'billing_address' => isset($row['billing_address']) ? $row['billing_address'] : '',
				'gst_apply_flag' => $gstFlag,
				'sub_total' => $subTotal,
				'gst_amount' => $gstAmount,
				'grand_total' => $grandTotal,
				'payment_received_flag' => (int) $row['payment_received_flag'],
				'payment_received_amount' => round((float) $row['payment_received_amount'], 2),
				'status' => $wf['status'],
				'status_label' => $wf['status_label'],
				'baki_amount' => $wf['baki_amount'],
				'order_status_code' => (int) $row['status'],
				'can_edit' => ($wf['status'] === 'pending') ? 1 : 0,
				'can_delete' => ($wf['status'] === 'pending') ? 1 : 0,
				'items' => $items,
				'print_url' => 'bbsales_tracking/channel_partner_order_print.php?order_id=' . (int) $row['id'],
			),
		);
	}
}
