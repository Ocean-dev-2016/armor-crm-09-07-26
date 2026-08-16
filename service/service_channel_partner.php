<?php
/**
 * Channel Partner App APIs — My Customers + Customer Order Cart
 *
 * Endpoint: POST service/service_channel_partner.php
 * Auth: key=1226 + s=<service id>
 *
 * After Common Login (#2), use result.channel_partner_id for all calls.
 *
 * #241 get_cp_my_customers                 — List (web My Customers)
 * #242 add_cp_my_customer                  — Add Customer
 * #243 update_cp_my_customer               — Edit Customer
 * #244 get_cp_my_customer_detail           — One customer (edit form)
 * #245 delete_cp_my_customer               — Soft delete
 * #246 get_cp_customer_form_masters        — Country list + form field map
 * #247 get_cp_dashboard                    — Login Dashboard (Web + Mobile same)
 * #248 get_cp_customer_order_products      — Products for Customer Order (rate + stock)
 * #249 get_cp_customer_order_cart          — Get cart
 * #250 add_cp_customer_order_cart          — Add to cart
 * #251 update_cp_customer_order_cart_item  — Update cart item qty/rate/disc
 * #252 remove_cp_customer_order_cart_item  — Remove cart item
 * #253 clear_cp_customer_order_cart        — Clear cart
 * #254 place_cp_customer_order             — Place order (stock debit)
 * #255 get_cp_customer_orders              — List Customer Orders
 * #256 get_cp_customer_order_detail        — Order detail + items
 * #257 get_cp_my_stock                     — My Stock Main (Product & Code)
 * #258 get_cp_my_stock_movements           — My Stock Inward / Outward ledger
 * #259 get_cp_payment_parties              — Receive Payment: party list + payment types
 * #260 get_cp_payment_orders               — Orders for party (partial: remaining_amount, can_receive=1 until remaining 0)
 * #261 save_cp_receive_payment             — Save payment (partial accumulate; flag=1 only when remaining 0)
 * #262 get_cp_party_ledger                 — Party Ledger (each receipt is a credit; pending stays Dr)
 * #263 delete_cp_customer_order            — Delete Pending customer order (+ stock credit)
 * #264 update_cp_customer_order            — Edit Pending customer order
 * #265 get_cp_payment_pdf                  — Receive Payment Print PDF (Received / Pending per order)
 * #266 add_cp_customer_order_item          — Edit Order: add item (+ Add Item)
 * #267 update_cp_customer_order_status     — Status: Pending → Dispatched (same as web dropdown)
 * #268 delete_cp_customer_order_item       — Edit Order: delete one item
 * #269 download_cp_customer_order_pdf      — Customer Order PDF download (Pending → file_url)
 *
 * Also available (older): service_genral.php #224/#225 same customer table.
 */
include('connect.php');

if ($is_valid_api_key) {
	if ($is_valid_service) {
		require_once('../include/class.channel_partner_customer.php');
		require_once('../include/class.channel_partner_order.php');
		require_once('../include/class.channel_partner_stock.php');
		require_once('../include/class.channel_partner_payment.php');
		require_once('../include/class.channel_partner_ledger.php');
		$objCP = new ChannelPartnerCustomer();
		$objCPOrder = new ChannelPartnerOrder();
		$objCPStock = new ChannelPartnerStock($db);
		$objCPPay = new ChannelPartnerPayment($db);
		$objCPLedger = new ChannelPartnerLedger($db);

		$channel_partner_id = isset($_REQUEST['channel_partner_id']) ? (int) $_REQUEST['channel_partner_id'] : 0;

		if ($service == 'get_cp_my_customers' || $service == 241) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
					'developer_msg' => 'Missing channel_partner_id',
				));
			} else {
				$detail = array(
					'channel_partner_id' => $channel_partner_id,
					'search_name' => isset($_REQUEST['search_name']) ? $db->clean($_REQUEST['search_name']) : '',
					'ul' => isset($_REQUEST['ul']) ? $db->clean($_REQUEST['ul']) : '0',
					'll' => isset($_REQUEST['ll']) ? $db->clean($_REQUEST['ll']) : '50',
				);
				$db->printJSON($objCP->GetChannelPartnerCustomerList($detail));
			}
		} else if ($service == 'add_cp_my_customer' || $service == 242) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required (from Login #2).',
				));
			} else {
				$detail = array(
					'channel_partner_id' => $channel_partner_id,
					'company_name' => isset($_REQUEST['company_name']) ? $db->clean($_REQUEST['company_name']) : '',
					'person_name' => isset($_REQUEST['person_name']) ? $db->clean($_REQUEST['person_name']) : '',
					'mobile_no' => isset($_REQUEST['mobile_no']) ? $db->clean($_REQUEST['mobile_no']) : '',
					'email' => isset($_REQUEST['email']) ? $db->clean($_REQUEST['email']) : '',
					'gst' => isset($_REQUEST['gst']) ? $db->clean($_REQUEST['gst']) : '',
					'country' => isset($_REQUEST['country']) ? $db->clean($_REQUEST['country']) : 'India',
					'state' => isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : '',
					'city' => isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : '',
					'pincode' => isset($_REQUEST['pincode']) ? $db->clean($_REQUEST['pincode']) : '',
					'address' => isset($_REQUEST['address']) ? $db->clean($_REQUEST['address']) : '',
				);
				$db->printJSON($objCP->InsertChannelPartnerCustomer($detail));
			}
		} else if ($service == 'update_cp_my_customer' || $service == 243) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required (from Login #2).',
				));
			} else {
				$detail = array(
					'id' => isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : '',
					'channel_partner_id' => $channel_partner_id,
					'company_name' => isset($_REQUEST['company_name']) ? $db->clean($_REQUEST['company_name']) : '',
					'person_name' => isset($_REQUEST['person_name']) ? $db->clean($_REQUEST['person_name']) : '',
					'mobile_no' => isset($_REQUEST['mobile_no']) ? $db->clean($_REQUEST['mobile_no']) : '',
					'email' => isset($_REQUEST['email']) ? $db->clean($_REQUEST['email']) : '',
					'gst' => isset($_REQUEST['gst']) ? $db->clean($_REQUEST['gst']) : '',
					'country' => isset($_REQUEST['country']) ? $db->clean($_REQUEST['country']) : 'India',
					'state' => isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : '',
					'city' => isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : '',
					'pincode' => isset($_REQUEST['pincode']) ? $db->clean($_REQUEST['pincode']) : '',
					'address' => isset($_REQUEST['address']) ? $db->clean($_REQUEST['address']) : '',
				);
				$db->printJSON($objCP->UpdateChannelPartnerCustomer($detail));
			}
		} else if ($service == 'get_cp_my_customer_detail' || $service == 244) {
			$detail = array(
				'id' => isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : '',
				'channel_partner_id' => $channel_partner_id,
			);
			$db->printJSON($objCP->GetChannelPartnerCustomerDetail($detail));
		} else if ($service == 'delete_cp_my_customer' || $service == 245) {
			$detail = array(
				'id' => isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : '',
				'channel_partner_id' => $channel_partner_id,
			);
			$db->printJSON($objCP->DeleteChannelPartnerCustomer($detail));
		} else if ($service == 'get_cp_customer_form_masters' || $service == 246) {
			$countries = array();
			$cr = $db->rp_getData('country', 'id,name', 'isDelete=0', 'name ASC', 0);
			if ($cr) {
				while ($c = mysqli_fetch_assoc($cr)) {
					$countries[] = array(
						'id' => (int) $c['id'],
						'name' => $c['name'],
					);
				}
			}
			$db->printJSON(array(
				'ack' => 1,
				'ack_msg' => 'Form masters ready',
				'default_country' => 'India',
				'fields' => array(
					array('key' => 'company_name', 'label' => 'Customer Name', 'required' => 1),
					array('key' => 'person_name', 'label' => 'Person Name', 'required' => 1),
					array('key' => 'mobile_no', 'label' => 'Mobile No', 'required' => 1),
					array('key' => 'email', 'label' => 'Email', 'required' => 0),
					array('key' => 'gst', 'label' => 'GST', 'required' => 0),
					array('key' => 'address', 'label' => 'Address', 'required' => 0),
					array('key' => 'country', 'label' => 'Country', 'required' => 1),
					array('key' => 'state', 'label' => 'State', 'required' => 1),
					array('key' => 'city', 'label' => 'City', 'required' => 1),
					array('key' => 'pincode', 'label' => 'Pincode', 'required' => 0),
				),
				'countries' => $countries,
				'state_api' => array(
					'url' => 'service/service_genral.php',
					's' => 28,
					'slug' => 'get_state',
					'note' => 'Send country_id (India). Result.id = class.id (state id). Use name for saving customer.state',
				),
				'city_api' => array(
					'url' => 'service/service_genral.php',
					's' => 38,
					'slug' => 'get_city',
					'note' => 'REQUIRED: state_id = class.id from get_state (#28). Example Gujarat state_id=12. Also accepts state=Gujarat. Do NOT send city_id here.',
				),
				'list_api' => array('s' => 241, 'slug' => 'get_cp_my_customers'),
				'add_api' => array('s' => 242, 'slug' => 'add_cp_my_customer'),
				'dashboard_api' => array('s' => 247, 'slug' => 'get_cp_dashboard'),
				'order_apis' => array(
					'products' => array('s' => 248, 'slug' => 'get_cp_customer_order_products'),
					'cart' => array('s' => 249, 'slug' => 'get_cp_customer_order_cart'),
					'add_cart' => array('s' => 250, 'slug' => 'add_cp_customer_order_cart'),
					'place_order' => array('s' => 254, 'slug' => 'place_cp_customer_order'),
					'orders' => array('s' => 255, 'slug' => 'get_cp_customer_orders'),
					'order_detail' => array('s' => 256, 'slug' => 'get_cp_customer_order_detail'),
					'update_status' => array('s' => 267, 'slug' => 'update_cp_customer_order_status'),
					'download_order_pdf' => array('s' => 269, 'slug' => 'download_cp_customer_order_pdf'),
				),
			));
		} else if ($service == 'get_cp_dashboard' || $service == 247) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
					'developer_msg' => 'Missing channel_partner_id',
				));
			} else {
				$limit = isset($_REQUEST['recent_limit']) ? (int) $_REQUEST['recent_limit'] : 5;
				$db->printJSON($objCP->GetChannelPartnerDashboard($channel_partner_id, $limit));
			}
		} else if ($service == 'get_cp_customer_order_products' || $service == 248) {
			$detail = array(
				'channel_partner_id' => $channel_partner_id,
				'search_name' => isset($_REQUEST['search_name']) ? $db->clean($_REQUEST['search_name']) : (isset($_REQUEST['search']) ? $db->clean($_REQUEST['search']) : ''),
				'only_in_stock' => isset($_REQUEST['only_in_stock']) ? (int) $_REQUEST['only_in_stock'] : 0,
			);
			$db->printJSON($objCPOrder->GetOrderProducts($detail));
		} else if ($service == 'get_cp_customer_order_cart' || $service == 249) {
			$detail = array(
				'channel_partner_id' => $channel_partner_id,
				'channel_partner_customer_id' => isset($_REQUEST['channel_partner_customer_id'])
					? (int) $_REQUEST['channel_partner_customer_id']
					: (isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : 0),
			);
			if (isset($_REQUEST['gst_apply_flag']) && $_REQUEST['gst_apply_flag'] !== '') {
				$detail['gst_apply_flag'] = (int) $_REQUEST['gst_apply_flag'];
			}
			$db->printJSON($objCPOrder->GetCart($detail));
		} else if ($service == 'add_cp_customer_order_cart' || $service == 250) {
			$detail = array(
				'channel_partner_id' => $channel_partner_id,
				'channel_partner_customer_id' => isset($_REQUEST['channel_partner_customer_id'])
					? (int) $_REQUEST['channel_partner_customer_id']
					: (isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : 0),
				'pwp_id' => isset($_REQUEST['pwp_id']) ? (int) $_REQUEST['pwp_id'] : (isset($_REQUEST['line_product']) ? (int) $_REQUEST['line_product'] : 0),
				'catno' => isset($_REQUEST['catno']) ? $db->clean($_REQUEST['catno']) : (isset($_REQUEST['cat_no']) ? $db->clean($_REQUEST['cat_no']) : ''),
				'product_id' => isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : (isset($_REQUEST['pid']) ? (int) $_REQUEST['pid'] : (isset($_REQUEST['pro_id']) ? (int) $_REQUEST['pro_id'] : 0)),
				'pid' => isset($_REQUEST['pid']) ? (int) $_REQUEST['pid'] : (isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : 0),
				'weight_id' => isset($_REQUEST['weight_id']) ? $_REQUEST['weight_id'] : '',
				'qty' => isset($_REQUEST['qty']) ? $_REQUEST['qty'] : '',
				'rate' => isset($_REQUEST['rate']) ? $_REQUEST['rate'] : null,
				'discount' => isset($_REQUEST['discount']) ? $_REQUEST['discount'] : null,
				'products' => isset($_REQUEST['products']) ? $_REQUEST['products'] : '',
			);
			if (isset($_REQUEST['gst_apply_flag']) && $_REQUEST['gst_apply_flag'] !== '') {
				$detail['gst_apply_flag'] = (int) $_REQUEST['gst_apply_flag'];
			} else if (isset($_REQUEST['without_gst']) && (int) $_REQUEST['without_gst'] === 1) {
				$detail['gst_apply_flag'] = 0;
			} else {
				$detail['gst_apply_flag'] = 1;
			}
			$db->printJSON($objCPOrder->AddToCart($detail));
		} else if ($service == 'update_cp_customer_order_cart_item' || $service == 251) {
			$detail = array(
				'channel_partner_id' => $channel_partner_id,
				'cart_item_id' => isset($_REQUEST['cart_item_id']) ? (int) $_REQUEST['cart_item_id'] : 0,
				'qty' => isset($_REQUEST['qty']) ? $_REQUEST['qty'] : '',
				'rate' => isset($_REQUEST['rate']) ? $_REQUEST['rate'] : null,
				'discount' => isset($_REQUEST['discount']) ? $_REQUEST['discount'] : null,
			);
			$db->printJSON($objCPOrder->UpdateCartItem($detail));
		} else if ($service == 'remove_cp_customer_order_cart_item' || $service == 252) {
			$detail = array(
				'channel_partner_id' => $channel_partner_id,
				'cart_item_id' => isset($_REQUEST['cart_item_id']) ? (int) $_REQUEST['cart_item_id'] : 0,
			);
			$db->printJSON($objCPOrder->RemoveCartItem($detail));
		} else if ($service == 'clear_cp_customer_order_cart' || $service == 253) {
			$db->printJSON($objCPOrder->ClearCart(array('channel_partner_id' => $channel_partner_id)));
		} else if ($service == 'place_cp_customer_order' || $service == 254) {
			$detail = array(
				'channel_partner_id' => $channel_partner_id,
				'channel_partner_customer_id' => isset($_REQUEST['channel_partner_customer_id']) ? (int) $_REQUEST['channel_partner_customer_id'] : 0,
				'address' => isset($_REQUEST['address']) ? $_REQUEST['address'] : '',
				'shipping_address' => isset($_REQUEST['shipping_address']) ? $_REQUEST['shipping_address'] : '',
				'billing_address' => isset($_REQUEST['billing_address']) ? $_REQUEST['billing_address'] : '',
				'remark' => isset($_REQUEST['remark']) ? $_REQUEST['remark'] : '',
				'products' => isset($_REQUEST['products']) ? $_REQUEST['products'] : '',
				'pwp_id' => isset($_REQUEST['pwp_id']) ? (int) $_REQUEST['pwp_id'] : 0,
				'qty' => isset($_REQUEST['qty']) ? $_REQUEST['qty'] : '',
				'rate' => isset($_REQUEST['rate']) ? $_REQUEST['rate'] : null,
				'discount' => isset($_REQUEST['discount']) ? $_REQUEST['discount'] : null,
			);
			if (isset($_REQUEST['gst_apply_flag']) && $_REQUEST['gst_apply_flag'] !== '') {
				$detail['gst_apply_flag'] = (int) $_REQUEST['gst_apply_flag'];
			} else if (isset($_REQUEST['without_gst']) && (int) $_REQUEST['without_gst'] === 1) {
				$detail['gst_apply_flag'] = 0;
			} else if (isset($_REQUEST['with_gst']) && $_REQUEST['with_gst'] !== '') {
				$detail['gst_apply_flag'] = ((int) $_REQUEST['with_gst'] === 0) ? 0 : 1;
			} else {
				$detail['gst_apply_flag'] = 1;
			}
			$db->printJSON($objCPOrder->PlaceOrder($detail));
		} else if ($service == 'get_cp_customer_orders' || $service == 255) {
			$detail = array(
				'channel_partner_id' => $channel_partner_id,
				'search_name' => isset($_REQUEST['search_name']) ? $db->clean($_REQUEST['search_name']) : '',
				'ul' => isset($_REQUEST['ul']) ? $db->clean($_REQUEST['ul']) : '0',
				'll' => isset($_REQUEST['ll']) ? $db->clean($_REQUEST['ll']) : '50',
			);
			$db->printJSON($objCPOrder->GetOrderList($detail));
		} else if ($service == 'get_cp_customer_order_detail' || $service == 256) {
			$detail = array(
				'channel_partner_id' => $channel_partner_id,
				'order_id' => isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : (isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0),
			);
			$db->printJSON($objCPOrder->GetOrderDetail($detail));
		} else if ($service == 'delete_cp_customer_order' || $service == 263) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
				));
			} else {
				$detail = array(
					'channel_partner_id' => $channel_partner_id,
					'order_id' => isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : (isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0),
				);
				$db->printJSON($objCPOrder->DeleteCustomerOrder($detail));
			}
		} else if ($service == 'update_cp_customer_order' || $service == 264) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
				));
			} else {
				$detail = array(
					'channel_partner_id' => $channel_partner_id,
					'order_id' => isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : (isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0),
					'channel_partner_customer_id' => isset($_REQUEST['channel_partner_customer_id'])
						? (int) $_REQUEST['channel_partner_customer_id']
						: (isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : 0),
					'address' => isset($_REQUEST['address']) ? $_REQUEST['address'] : '',
					'shipping_address' => isset($_REQUEST['shipping_address']) ? $_REQUEST['shipping_address'] : '',
					'billing_address' => isset($_REQUEST['billing_address']) ? $_REQUEST['billing_address'] : '',
					'remark' => isset($_REQUEST['remark']) ? $_REQUEST['remark'] : (isset($_REQUEST['remarks']) ? $_REQUEST['remarks'] : ''),
					'products' => isset($_REQUEST['products']) ? $_REQUEST['products'] : '',
					'pwp_id' => isset($_REQUEST['pwp_id']) ? (int) $_REQUEST['pwp_id'] : 0,
					'qty' => isset($_REQUEST['qty']) ? $_REQUEST['qty'] : '',
					'rate' => isset($_REQUEST['rate']) ? $_REQUEST['rate'] : null,
					'discount' => isset($_REQUEST['discount']) ? $_REQUEST['discount'] : null,
					'catno' => isset($_REQUEST['catno']) ? $db->clean($_REQUEST['catno']) : '',
					'product_id' => isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : 0,
					'weight_id' => isset($_REQUEST['weight_id']) ? $_REQUEST['weight_id'] : '',
					'sub_total' => isset($_REQUEST['sub_total']) ? $_REQUEST['sub_total'] : (isset($_REQUEST['subtotal']) ? $_REQUEST['subtotal'] : null),
					'gst_amount' => isset($_REQUEST['gst_amount']) ? $_REQUEST['gst_amount'] : null,
					'grand_total' => isset($_REQUEST['grand_total']) ? $_REQUEST['grand_total'] : null,
				);
				if (isset($_REQUEST['gst_apply_flag']) && $_REQUEST['gst_apply_flag'] !== '') {
					$detail['gst_apply_flag'] = (int) $_REQUEST['gst_apply_flag'];
				} else if (isset($_REQUEST['without_gst']) && (int) $_REQUEST['without_gst'] === 1) {
					$detail['gst_apply_flag'] = 0;
				} else if (isset($_REQUEST['with_gst']) && $_REQUEST['with_gst'] !== '') {
					$detail['gst_apply_flag'] = ((int) $_REQUEST['with_gst'] === 0) ? 0 : 1;
				}
				$db->printJSON($objCPOrder->UpdateCustomerOrder($detail));
			}
		} else if ($service == 'add_cp_customer_order_item' || $service == 266) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
				));
			} else {
				$detail = array(
					'channel_partner_id' => $channel_partner_id,
					'order_id' => isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : (isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0),
					'pwp_id' => isset($_REQUEST['pwp_id']) ? (int) $_REQUEST['pwp_id'] : 0,
					'qty' => isset($_REQUEST['qty']) ? $_REQUEST['qty'] : 0,
					'rate' => isset($_REQUEST['rate']) ? $_REQUEST['rate'] : null,
					'discount' => isset($_REQUEST['discount']) ? $_REQUEST['discount'] : null,
					'catno' => isset($_REQUEST['catno']) ? $db->clean($_REQUEST['catno']) : '',
					'product_id' => isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : 0,
					'weight_id' => isset($_REQUEST['weight_id']) ? $_REQUEST['weight_id'] : '',
					'amount' => isset($_REQUEST['amount']) ? $_REQUEST['amount'] : (isset($_REQUEST['line_base']) ? $_REQUEST['line_base'] : null),
					'line_base' => isset($_REQUEST['line_base']) ? $_REQUEST['line_base'] : null,
					'item_gst_amount' => isset($_REQUEST['item_gst_amount']) ? $_REQUEST['item_gst_amount'] : (isset($_REQUEST['line_gst_amount']) ? $_REQUEST['line_gst_amount'] : null),
					'sub_total' => isset($_REQUEST['sub_total']) ? $_REQUEST['sub_total'] : (isset($_REQUEST['subtotal']) ? $_REQUEST['subtotal'] : null),
					'gst_amount' => isset($_REQUEST['gst_amount']) ? $_REQUEST['gst_amount'] : null,
					'grand_total' => isset($_REQUEST['grand_total']) ? $_REQUEST['grand_total'] : null,
				);
				if (isset($_REQUEST['gst_apply_flag']) && $_REQUEST['gst_apply_flag'] !== '') {
					$detail['gst_apply_flag'] = (int) $_REQUEST['gst_apply_flag'];
				}
				$db->printJSON($objCPOrder->AddCustomerOrderItem($detail));
			}
		} else if ($service == 'delete_cp_customer_order_item' || $service == 'remove_cp_customer_order_item' || $service == 268) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
				));
			} else {
				$detail = array(
					'channel_partner_id' => $channel_partner_id,
					'order_id' => isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : (isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0),
					'item_id' => isset($_REQUEST['item_id']) ? (int) $_REQUEST['item_id'] : 0,
					'order_item_id' => isset($_REQUEST['order_item_id']) ? (int) $_REQUEST['order_item_id'] : 0,
					'cart_item_id' => isset($_REQUEST['cart_item_id']) ? (int) $_REQUEST['cart_item_id'] : 0,
				);
				if (isset($_REQUEST['gst_apply_flag']) && $_REQUEST['gst_apply_flag'] !== '') {
					$detail['gst_apply_flag'] = (int) $_REQUEST['gst_apply_flag'];
				}
				$db->printJSON($objCPOrder->DeleteCustomerOrderItem($detail));
			}
		} else if ($service == 'update_cp_customer_order_status' || $service == 'dispatch_cp_customer_order' || $service == 267) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
				));
			} else {
				$detail = array(
					'channel_partner_id' => $channel_partner_id,
					'order_id' => isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : (isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0),
					'status' => isset($_REQUEST['status']) ? $db->clean($_REQUEST['status']) : '',
					'action' => isset($_REQUEST['action']) ? $db->clean($_REQUEST['action']) : '',
					'dispatch_status' => isset($_REQUEST['dispatch_status']) ? $db->clean($_REQUEST['dispatch_status']) : '',
					'dispatch_status_id' => isset($_REQUEST['dispatch_status_id']) ? (int) $_REQUEST['dispatch_status_id'] : 0,
				);
				$db->printJSON($objCPOrder->UpdateCustomerOrderStatus($detail));
			}
		} else if ($service == 'get_cp_my_stock' || $service == 257) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
				));
			} else {
				$search = isset($_REQUEST['search_name']) ? $db->clean($_REQUEST['search_name']) : '';
				$db->printJSON($objCPStock->GetMyStockMain($channel_partner_id, $search));
			}
		} else if ($service == 'get_cp_my_stock_movements' || $service == 258) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
				));
			} else {
				$search = isset($_REQUEST['search_name']) ? $db->clean($_REQUEST['search_name']) : '';
				$db->printJSON($objCPStock->GetMyStockMovements($channel_partner_id, $search));
			}
		} else if ($service == 'get_cp_payment_parties' || $service == 259) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
				));
			} else {
				$detail = array(
					'channel_partner_id' => $channel_partner_id,
					'search_name' => isset($_REQUEST['search_name']) ? $db->clean($_REQUEST['search_name']) : '',
				);
				$db->printJSON($objCPPay->GetPaymentParties($detail));
			}
		} else if ($service == 'get_cp_payment_orders' || $service == 260) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
				));
			} else {
				$detail = array(
					'channel_partner_id' => $channel_partner_id,
					'party_id' => isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : (isset($_REQUEST['channel_partner_customer_id']) ? (int) $_REQUEST['channel_partner_customer_id'] : 0),
				);
				$db->printJSON($objCPPay->GetPaymentOrders($detail));
			}
		} else if ($service == 'save_cp_receive_payment' || $service == 261) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
				));
			} else {
				$detail = array(
					'channel_partner_id' => $channel_partner_id,
					'order_id' => isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : 0,
					'paid_amount' => isset($_REQUEST['paid_amount']) ? $_REQUEST['paid_amount'] : 0,
					'payment_type' => isset($_REQUEST['payment_type']) ? (int) $_REQUEST['payment_type'] : 0,
					'remark' => isset($_REQUEST['remark']) ? $_REQUEST['remark'] : '',
				);
				$db->printJSON($objCPPay->SaveReceivePayment($detail));
			}
		} else if ($service == 'get_cp_party_ledger' || $service == 262) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
				));
			} else {
				$detail = array(
					'channel_partner_id' => $channel_partner_id,
					'party_id' => isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : 0,
				);
				$db->printJSON($objCPLedger->GetPartyLedger($detail));
			}
		} else if ($service == 'get_cp_payment_pdf' || $service == 265) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
				));
			} else {
				$detail = array(
					'channel_partner_id' => $channel_partner_id,
					'party_id' => isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : (isset($_REQUEST['channel_partner_customer_id']) ? (int) $_REQUEST['channel_partner_customer_id'] : 0),
				);
				$db->printJSON($objCPPay->GetPaymentPdf($detail));
			}
		} else if ($service == 'download_cp_customer_order_pdf' || $service == 'get_cp_customer_order_pdf' || $service == 269) {
			if ($channel_partner_id <= 0) {
				$db->printJSON(array(
					'ack' => 0,
					'ack_msg' => 'channel_partner_id is required. Use value from Login API #2 result.channel_partner_id',
				));
			} else {
				$detail = array(
					'channel_partner_id' => $channel_partner_id,
					'order_id' => isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : (isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0),
				);
				$db->printJSON($objCPOrder->DownloadCustomerOrderPdf($detail));
			}
		} else {
			$db->printJSON(array(
				'ack' => 0,
				'ack_msg' => 'Invalid Channel Partner service. Use s=241 to 269.',
				'developer_msg' => 'Unknown service: ' . $service,
			));
		}
	} else {
		$db->printJSON(array('ack' => 0, 'ack_msg' => 'Invalid Service.', 'developer_msg' => 'Register APIs 241-269 via db_sync'));
	}
} else {
	$db->printJSON(array('ack' => 0, 'ack_msg' => 'Invalid API key.', 'developer_msg' => 'Use key=1226'));
}
