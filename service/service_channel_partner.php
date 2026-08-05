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
 *
 * Also available (older): service_genral.php #224/#225 same customer table.
 */
include('connect.php');

if ($is_valid_api_key) {
	if ($is_valid_service) {
		require_once('../include/class.channel_partner_customer.php');
		require_once('../include/class.channel_partner_order.php');
		$objCP = new ChannelPartnerCustomer();
		$objCPOrder = new ChannelPartnerOrder();

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
				'search_name' => isset($_REQUEST['search_name']) ? $db->clean($_REQUEST['search_name']) : '',
				'only_in_stock' => isset($_REQUEST['only_in_stock']) ? (int) $_REQUEST['only_in_stock'] : 0,
			);
			$db->printJSON($objCPOrder->GetOrderProducts($detail));
		} else if ($service == 'get_cp_customer_order_cart' || $service == 249) {
			$db->printJSON($objCPOrder->GetCart(array('channel_partner_id' => $channel_partner_id)));
		} else if ($service == 'add_cp_customer_order_cart' || $service == 250) {
			$detail = array(
				'channel_partner_id' => $channel_partner_id,
				'channel_partner_customer_id' => isset($_REQUEST['channel_partner_customer_id']) ? (int) $_REQUEST['channel_partner_customer_id'] : 0,
				'gst_apply_flag' => isset($_REQUEST['gst_apply_flag']) ? (int) $_REQUEST['gst_apply_flag'] : 1,
				'pwp_id' => isset($_REQUEST['pwp_id']) ? (int) $_REQUEST['pwp_id'] : 0,
				'qty' => isset($_REQUEST['qty']) ? $_REQUEST['qty'] : '',
				'rate' => isset($_REQUEST['rate']) ? $_REQUEST['rate'] : null,
				'discount' => isset($_REQUEST['discount']) ? $_REQUEST['discount'] : null,
				'products' => isset($_REQUEST['products']) ? $_REQUEST['products'] : '',
			);
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
				'gst_apply_flag' => isset($_REQUEST['gst_apply_flag']) ? (int) $_REQUEST['gst_apply_flag'] : 1,
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
		} else {
			$db->printJSON(array(
				'ack' => 0,
				'ack_msg' => 'Invalid Channel Partner service. Use s=241 to 256.',
				'developer_msg' => 'Unknown service: ' . $service,
			));
		}
	} else {
		$db->printJSON(array('ack' => 0, 'ack_msg' => 'Invalid Service.', 'developer_msg' => 'Register APIs 241-256 via db_sync'));
	}
} else {
	$db->printJSON(array('ack' => 0, 'ack_msg' => 'Invalid API key.', 'developer_msg' => 'Use key=1226'));
}
