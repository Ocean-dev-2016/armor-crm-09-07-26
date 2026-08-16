<?php
require_once("main.class.php");
require_once("function.class.php");

class ChannelPartnerCustomer extends Functions
{
	public $db;
	public $ctable = "channel_partner_customer";

	function __construct($id = "")
	{
		$db = new Functions();
		$db->connect();
		$this->db = $db;
	}

	private function ensureTableReady()
	{
		if (!$this->db->tableExists($this->ctable)) {
			return array("ack" => 0, "developer_msg" => "Table missing", "ack_msg" => "Database table not found. Please run db_sync.php?key=armor_cp_sync_2026 once.");
		}
		return array("ack" => 1);
	}

	/**
	 * Sales IDs whose assigned Channel Partners this executive may see:
	 * self + reporting team (same chain as GetDealer / SE customer list).
	 */
	private function getChannelPartnerSalesIds($salesId)
	{
		$salesId = (int) $salesId;
		if ($salesId <= 0) {
			return array();
		}
		$ids = array($salesId);
		$get_sales_type = $this->db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='" . $salesId . "'", 0);
		$key = '';
		if ($get_sales_type == "sales_manager") {
			$key = "sm_id";
		} else if ($get_sales_type == "area_sales_manager") {
			$key = "asm_id";
		} else if ($get_sales_type == "sales_officer") {
			$key = "so_id";
		} else if ($get_sales_type == "sales_executive" || $get_sales_type == "area_manager") {
			$key = "se_id";
		}
		if ($key != '') {
			$team_r = $this->db->rp_getData("sales_executive", "id", "isDelete=0 AND isActive=1 AND " . $key . "='" . $salesId . "'", "", 0);
			if ($team_r) {
				while ($row = mysqli_fetch_assoc($team_r)) {
					$ids[] = (int) $row['id'];
				}
			}
		}
		$ids = array_values(array_unique(array_filter($ids)));
		return $ids;
	}

	public function GetChannelPartnerList($detail = array())
	{
		$tableCheck = $this->ensureTableReady();
		if ($tableCheck['ack'] != 1) {
			return $tableCheck;
		}

		$salesId = 0;
		if (is_array($detail)) {
			if (isset($detail['sales_id']) && (int) $detail['sales_id'] > 0) {
				$salesId = (int) $detail['sales_id'];
			} else if (isset($detail['sales_executive_id']) && (int) $detail['sales_executive_id'] > 0) {
				$salesId = (int) $detail['sales_executive_id'];
			}
		}
		if ($salesId <= 0) {
			return array(
				"ack" => 0,
				"developer_msg" => "sales_id missing",
				"ack_msg" => "sales_id is required.",
				"total" => 0,
				"result" => array(),
			);
		}

		$seidIn = $this->getChannelPartnerSalesIds($salesId);
		if (empty($seidIn)) {
			$seidIn = array($salesId);
		}

		$result = array();
		$cp_r = $this->db->rp_getData(
			"executive",
			"*",
			"channel_partner_flag=1 AND customer_flag=0 AND isDelete=0 AND seid IN (" . implode(",", $seidIn) . ")",
			"company_name ASC",
			0
		);
		if ($cp_r) {
			while ($cp_d = mysqli_fetch_assoc($cp_r)) {
				$label = trim($cp_d['company_name']);
				if ($cp_d['cname'] != "") {
					$label .= " - " . $cp_d['cname'];
				}
				if ($cp_d['mobile_no1'] != "") {
					$label .= " (" . $cp_d['mobile_no1'] . ")";
				}
				$type_name = $this->db->rp_getValue("customer_type", "name", "id='" . $cp_d['type_of_executive'] . "' AND isDelete=0", 0);
				$company_type_name = $this->db->rp_getValue("company_master", "name", "id='" . $cp_d['type_of_company'] . "' AND isDelete=0", 0);
				$price_list_name = $this->db->rp_getValue("price_list", "pricelist_name", "id='" . $cp_d['price_list_id'] . "' AND isDelete=0", 0);
				$sales_person_name = $this->db->rp_getValue("sales_executive", "name", "id='" . $cp_d['seid'] . "' AND isDelete=0", 0);

				$result[] = array(
					"id" => (int) $cp_d['id'],
					"company_name" => $cp_d['company_name'] != "" ? $cp_d['company_name'] : "",
					"person_name" => $cp_d['cname'] != "" ? $cp_d['cname'] : "",
					"mobile_no" => $cp_d['mobile_no1'] != "" ? $cp_d['mobile_no1'] : "",
					"phone" => $cp_d['phone'] != "" ? $cp_d['phone'] : "",
					"email" => $cp_d['email'] != "" ? $cp_d['email'] : "",
					"gst" => $cp_d['gst'] != "" ? $cp_d['gst'] : "",
					"client_code" => $cp_d['client_code'] != "" ? $cp_d['client_code'] : "",
					"state" => $cp_d['state'] != "" ? $cp_d['state'] : "",
					"city" => $cp_d['city'] != "" ? $cp_d['city'] : "",
					"main_city" => $cp_d['main_city'] != "" ? $cp_d['main_city'] : "",
					"pincode" => $cp_d['zip'] != "" ? $cp_d['zip'] : "",
					"address" => $cp_d['address'] != "" ? $cp_d['address'] : "",
					"type_of_executive" => $cp_d['type_of_executive'] != "" ? $cp_d['type_of_executive'] : "",
					"customer_type_name" => $type_name ? trim(preg_replace('/\s+/', ' ', $type_name)) : "",
					"type_of_company" => $cp_d['type_of_company'] != "" ? $cp_d['type_of_company'] : "",
					"type_of_company_name" => $company_type_name ? $company_type_name : "",
					"price_list_id" => $cp_d['price_list_id'] != "" ? $cp_d['price_list_id'] : "",
					"price_list_name" => $price_list_name ? $price_list_name : "",
					"sales_id" => $cp_d['seid'] != "" ? $cp_d['seid'] : "",
					"sales_person_name" => $sales_person_name ? $sales_person_name : "",
					"display_name" => $label,
				);
			}
		}

		return array(
			"ack" => 1,
			"developer_msg" => "Fetched",
			"ack_msg" => "Channel Partner list fetched successfully.",
			"total" => count($result),
			"result" => $result,
		);
	}

	public function GetChannelPartnerCustomerList($detail)
	{
		$tableCheck = $this->ensureTableReady();
		if ($tableCheck['ack'] != 1) {
			return $tableCheck;
		}

		$channel_partner_id = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$search_name = isset($detail['search_name']) ? $detail['search_name'] : "";
		$ul = isset($detail['ul']) ? (int) $detail['ul'] : 0;
		$ll = isset($detail['ll']) ? (int) $detail['ll'] : 50;
		if ($ll <= 0) {
			$ll = 50;
		}

		$where = "isDelete=0";
		if ($channel_partner_id > 0) {
			$where .= " AND channel_partner_id='" . $channel_partner_id . "'";
		}
		if ($search_name != "") {
			$search = $this->db->clean($search_name);
			$where .= " AND (
				company_name LIKE '%" . $search . "%' OR
				person_name LIKE '%" . $search . "%' OR
				mobile_no LIKE '%" . $search . "%' OR
				email LIKE '%" . $search . "%'
			)";
		}

		$limit = "id DESC LIMIT " . $ul . "," . $ll;
		$result = array();
		$list_r = $this->db->rp_getData($this->ctable, "*", $where, $limit, 0);
		if ($list_r) {
			while ($row = mysqli_fetch_assoc($list_r)) {
				$cp_name = "-";
				if (!empty($row['channel_partner_id'])) {
					$cp_name = $this->db->rp_getValue("executive", "company_name", "id='" . (int) $row['channel_partner_id'] . "'", 0);
					if ($cp_name == "") {
						$cp_name = $this->db->rp_getValue("executive", "cname", "id='" . (int) $row['channel_partner_id'] . "'", 0);
					}
				}
				$result[] = array(
					"id" => (int) $row['id'],
					"channel_partner_id" => (int) $row['channel_partner_id'],
					"channel_partner_name" => $cp_name,
					"company_name" => $row['company_name'],
					"person_name" => $row['person_name'],
					"mobile_no" => $row['mobile_no'],
					"email" => $row['email'],
					"gst" => $row['gst'],
					"country" => $row['country'],
					"state" => $row['state'],
					"city" => $row['city'],
					"pincode" => $row['pincode'],
					"address" => isset($row['address']) ? $row['address'] : "",
				);
			}
		}

		$total = $this->db->rp_getTotalRecord($this->ctable, $where);
		return array(
			"ack" => 1,
			"developer_msg" => "Fetched",
			"ack_msg" => "Channel Partner Customer list fetched successfully.",
			"total" => (int) $total,
			"result" => $result,
		);
	}

	public function GetChannelPartnerCustomerDetail($detail)
	{
		$tableCheck = $this->ensureTableReady();
		if ($tableCheck['ack'] != 1) {
			return $tableCheck;
		}
		if (empty($detail['id'])) {
			return array("ack" => 0, "ack_msg" => "Customer id is required.");
		}
		$reply = $this->GetEditDataChannelPartnerCustomer($detail, true);
		if ($reply['ack'] != 1) {
			return $reply;
		}
		/* Optional ownership check for CP App */
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		if ($cpId > 0 && (int) $reply['result']['channel_partner_id'] !== $cpId) {
			return array("ack" => 0, "ack_msg" => "Customer does not belong to this Channel Partner.");
		}
		return $reply;
	}

	/**
	 * Ensure customer id belongs to given channel_partner_id.
	 */
	public function AssertCustomerBelongsToCp($customerId, $channelPartnerId)
	{
		$customerId = (int) $customerId;
		$channelPartnerId = (int) $channelPartnerId;
		if ($customerId <= 0 || $channelPartnerId <= 0) {
			return array("ack" => 0, "ack_msg" => "Invalid customer / channel partner.");
		}
		$own = $this->db->rp_getTotalRecord(
			$this->ctable,
			"id='" . $customerId . "' AND channel_partner_id='" . $channelPartnerId . "' AND isDelete=0",
			0
		);
		if ($own <= 0) {
			return array("ack" => 0, "ack_msg" => "Customer does not belong to this Channel Partner.");
		}
		return array("ack" => 1);
	}

	private function validateRequiredFields($detail, $is_update = false)
	{
		$required = array(
			'channel_partner_id' => 'Channel Partner',
			'company_name' => 'Customer Name',
			'person_name' => 'Person Name',
			'country' => 'Country',
			'state' => 'State',
			'city' => 'City',
		);
		/* mobile_no required on Add only; optional on Update (API #243 / #226) */
		if (!$is_update) {
			$required['mobile_no'] = 'Mobile No';
		}
		if ($is_update) {
			$required['id'] = 'Customer id';
		}
		foreach ($required as $field => $label) {
			if (!isset($detail[$field]) || trim($detail[$field]) === '') {
				return array("ack" => 0, "ack_msg" => "Please enter " . $label . ".");
			}
		}
		return array("ack" => 1);
	}

	private function validateChannelPartnerId($channel_partner_id)
	{
		if (empty($channel_partner_id) || (int) $channel_partner_id <= 0) {
			return array("ack" => 0, "ack_msg" => "Please select Channel Partner.");
		}

		$where = "id='" . (int) $channel_partner_id . "' AND channel_partner_flag=1 AND customer_flag=0 AND isDelete=0";
		$count = $this->db->rp_getTotalRecord("executive", $where);
		if ($count == 0) {
			return array("ack" => 0, "ack_msg" => "Selected Channel Partner is invalid.");
		}

		return array("ack" => 1);
	}

	public function InsertChannelPartnerCustomer($detail)
	{
		$tableCheck = $this->ensureTableReady();
		if ($tableCheck['ack'] != 1) {
			return $tableCheck;
		}

		/* Web form defaults country to India */
		if (!isset($detail['country']) || trim($detail['country']) === '') {
			$detail['country'] = 'India';
		}

		$fieldCheck = $this->validateRequiredFields($detail);
		if ($fieldCheck['ack'] != 1) {
			return $fieldCheck;
		}

		extract($detail);

		$cpCheck = $this->validateChannelPartnerId($channel_partner_id);
		if ($cpCheck['ack'] != 1) {
			return array("ack" => 0, "developer_msg" => "Invalid channel partner", "ack_msg" => $cpCheck['ack_msg']);
		}

		$dup_where = "mobile_no = '" . $mobile_no . "' AND isDelete=0";
		if ($this->db->rp_dupCheck($this->ctable, $dup_where)) {
			return array("ack" => 0, "developer_msg" => "Mobile already exists", "ack_msg" => "This mobile number is already registered.");
		}

		if (!isset($address) || $address === null) {
			$address = "";
		}

		$rows = array(
			"channel_partner_id",
			"company_name",
			"person_name",
			"mobile_no",
			"email",
			"gst",
			"country",
			"state",
			"city",
			"pincode",
			"address",
			"isActive",
			"isDelete"
		);
		$values = array(
			(int) $channel_partner_id,
			$company_name,
			$person_name,
			$mobile_no,
			$email,
			$gst,
			$country,
			$state,
			$city,
			$pincode,
			$address,
			1,
			0
		);

		$uid = $this->db->rp_insert($this->ctable, $values, $rows, 0);
		if ($uid != 0) {
			return array("ack" => 1, "developer_msg" => "Inserted", "ack_msg" => "Channel Partner Customer added successfully.", "inserted_id" => $uid);
		}

		return array("ack" => 0, "developer_msg" => "Insert failed", "ack_msg" => "Failed to add Channel Partner Customer. Please run db_sync.php once.");
	}

	public function UpdateChannelPartnerCustomer($detail)
	{
		$tableCheck = $this->ensureTableReady();
		if ($tableCheck['ack'] != 1) {
			return $tableCheck;
		}

		if (!isset($detail['country']) || trim($detail['country']) === '') {
			$detail['country'] = 'India';
		}

		$fieldCheck = $this->validateRequiredFields($detail, true);
		if ($fieldCheck['ack'] != 1) {
			return $fieldCheck;
		}

		extract($detail);

		$cpCheck = $this->validateChannelPartnerId($channel_partner_id);
		if ($cpCheck['ack'] != 1) {
			return array("ack" => 0, "developer_msg" => "Invalid channel partner", "ack_msg" => $cpCheck['ack_msg']);
		}

		$ownCheck = $this->AssertCustomerBelongsToCp($id, $channel_partner_id);
		if ($ownCheck['ack'] != 1) {
			return $ownCheck;
		}

		/* Edit: if mobile_no blank/omitted, keep existing value */
		if (!isset($mobile_no) || trim($mobile_no) === '') {
			$existing_mobile = $this->db->rp_getValue($this->ctable, "mobile_no", "id='" . (int) $id . "' AND isDelete=0", 0);
			$mobile_no = ($existing_mobile !== false && $existing_mobile !== null) ? $existing_mobile : "";
		} else {
			$dup_where = "mobile_no = '" . $mobile_no . "' AND id!='" . $id . "' AND isDelete=0";
			if ($this->db->rp_dupCheck($this->ctable, $dup_where)) {
				return array("ack" => 0, "developer_msg" => "Mobile already exists", "ack_msg" => "This mobile number is already registered.");
			}
		}

		$rows = array(
			"channel_partner_id" => (int) $channel_partner_id,
			"company_name" => $company_name,
			"person_name" => $person_name,
			"mobile_no" => $mobile_no,
			"email" => $email,
			"gst" => $gst,
			"country" => $country,
			"state" => $state,
			"city" => $city,
			"pincode" => $pincode,
			"address" => isset($address) ? $address : "",
			"modified_date" => date('Y-m-d H:i:s'),
		);
		$where = "id='" . $id . "' AND isDelete=0";
		$uid = $this->db->rp_update($this->ctable, $rows, $where, 0);

		if ($uid != 0) {
			return array("ack" => 1, "developer_msg" => "Updated", "ack_msg" => "Channel Partner Customer updated successfully.");
		}

		return array("ack" => 0, "developer_msg" => "Update failed", "ack_msg" => "Failed to update Channel Partner Customer.");
	}

	public function GetEditDataChannelPartnerCustomer($detail, $forApi = false)
	{
		$where = " id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable, "*", $where, 0);
		if (!$ctable_r || mysqli_num_rows($ctable_r) == 0) {
			return array("ack" => 0, "developer_msg" => "Not found", "ack_msg" => "Record not found.");
		}

		$ctable_d = mysqli_fetch_array($ctable_r);
		$created_display = !empty($ctable_d['created_date']) ? $ctable_d['created_date'] : $ctable_d['created_at'];
		$updated_display = !empty($ctable_d['modified_date']) ? $ctable_d['modified_date'] : $ctable_d['updated_at'];

		$enc = function ($v) use ($forApi) {
			$v = ($v === null) ? '' : $v;
			return $forApi ? $v : htmlentities($v);
		};

		$result = array(
			'id' => (int) $ctable_d['id'],
			'channel_partner_id' => (int) $ctable_d['channel_partner_id'],
			'company_name' => $enc($ctable_d['company_name']),
			'person_name' => $enc($ctable_d['person_name']),
			'mobile_no' => $enc($ctable_d['mobile_no']),
			'email' => $enc($ctable_d['email']),
			'gst' => $enc($ctable_d['gst']),
			'country' => $enc($ctable_d['country']),
			'state' => $enc($ctable_d['state']),
			'city' => $enc($ctable_d['city']),
			'pincode' => $enc($ctable_d['pincode']),
			'address' => $enc(isset($ctable_d['address']) ? $ctable_d['address'] : ''),
			'created_at' => $created_display,
			'updated_at' => $updated_display,
		);

		return array("ack" => 1, "developer_msg" => "Fetched", "ack_msg" => "Success", "result" => $result);
	}

	public function DeleteChannelPartnerCustomer($detail)
	{
		$tableCheck = $this->ensureTableReady();
		if ($tableCheck['ack'] != 1) {
			return $tableCheck;
		}
		if (empty($detail['id'])) {
			return array("ack" => 0, "ack_msg" => "Customer id is required.");
		}

		extract($detail);
		$cpId = isset($channel_partner_id) ? (int) $channel_partner_id : 0;
		if ($cpId > 0) {
			$ownCheck = $this->AssertCustomerBelongsToCp($id, $cpId);
			if ($ownCheck['ack'] != 1) {
				return $ownCheck;
			}
		}

		$rows = array("isDelete" => "1", "modified_date" => date('Y-m-d H:i:s'));
		$where = "id='" . $id . "' AND isDelete=0";
		if ($cpId > 0) {
			$where .= " AND channel_partner_id='" . $cpId . "'";
		}
		$uid = $this->db->rp_update($this->ctable, $rows, $where, 0);

		if ($uid != 0) {
			return array("ack" => 1, "developer_msg" => "Deleted", "ack_msg" => "Channel Partner Customer deleted successfully.");
		}

		return array("ack" => 0, "developer_msg" => "Delete failed", "ack_msg" => "Failed to delete Channel Partner Customer.");
	}

	/**
	 * CP Login Dashboard — same data for Web + Mobile App.
	 * Summary cards + recent orders + payment follow-up.
	 */
	public function GetChannelPartnerDashboard($channel_partner_id, $recent_limit = 5)
	{
		$cpId = (int) $channel_partner_id;
		$recent_limit = (int) $recent_limit;
		if ($recent_limit <= 0 || $recent_limit > 20) {
			$recent_limit = 5;
		}
		if ($cpId <= 0) {
			return array("ack" => 0, "ack_msg" => "channel_partner_id is required.");
		}

		$cpCheck = $this->validateChannelPartnerId($cpId);
		if ($cpCheck['ack'] != 1) {
			return array("ack" => 0, "ack_msg" => $cpCheck['ack_msg']);
		}

		$cp_r = $this->db->rp_getData(
			"executive",
			"id,company_name,cname,mobile_no1,phone,email,client_code,gst",
			"id='" . $cpId . "' AND isDelete=0",
			"",
			0
		);
		$cp = $cp_r ? mysqli_fetch_assoc($cp_r) : array();
		$company_name = !empty($cp['company_name']) ? $cp['company_name'] : (!empty($cp['cname']) ? $cp['cname'] : 'Channel Partner');
		$person_name = !empty($cp['cname']) ? $cp['cname'] : '';
		$phone = !empty($cp['mobile_no1']) ? $cp['mobile_no1'] : (isset($cp['phone']) ? $cp['phone'] : '');

		$orderWhere = "customer_id='" . $cpId . "' AND channel_partner_order_flag=1 AND channel_partner_customer_id>0 AND isDelete=0 AND status NOT IN (-2,3)";
		$pendingPayWhere = $orderWhere . " AND IFNULL(payment_received_amount,0) < IFNULL(grand_total,0)";

		$my_customers = (int) $this->db->rp_getTotalRecord($this->ctable, "channel_partner_id='" . $cpId . "' AND isDelete=0", 0);
		$customer_orders = (int) $this->db->rp_getTotalRecord("orders", $orderWhere, 0);
		$pending_payments = (int) $this->db->rp_getTotalRecord("orders", $pendingPayWhere, 0);
		$products_in_stock = (int) $this->db->rp_getValue("customer_inward_stock", "COUNT(DISTINCT pro_id)", "customer_id='" . $cpId . "' AND isDelete=0", 0);

		$recent_orders = array();
		$recentR = $this->db->rp_getData(
			"orders",
			"id,order_no,order_date,grand_total,status,channel_partner_customer_id,payment_received_flag,payment_received_amount",
			$orderWhere,
			"id DESC LIMIT " . $recent_limit,
			0
		);
		if ($recentR) {
			while ($row = mysqli_fetch_assoc($recentR)) {
				$recent_orders[] = $this->formatDashboardOrderRow($row);
			}
		}

		$payment_followup = array();
		$pendR = $this->db->rp_getData(
			"orders",
			"id,order_no,order_date,grand_total,status,channel_partner_customer_id,payment_received_flag,payment_received_amount",
			$pendingPayWhere,
			"id DESC LIMIT " . $recent_limit,
			0
		);
		if ($pendR) {
			while ($row = mysqli_fetch_assoc($pendR)) {
				$fmt = $this->formatDashboardOrderRow($row);
				$payment_followup[] = array(
					'order_id' => $fmt['order_id'],
					'order_no' => $fmt['order_no'],
					'party_id' => $fmt['party_id'],
					'party_name' => $fmt['party_name'],
					'order_date' => $fmt['order_date'],
					'order_date_display' => $fmt['order_date_display'],
					'amount' => $fmt['amount'],
					'amount_display' => $fmt['amount_display'],
					'baki_amount' => $fmt['baki_amount'],
					'baki_amount_display' => number_format($fmt['baki_amount'], 2),
					'status' => $fmt['status'],
					'status_label' => $fmt['status_label'],
				);
			}
		}

		return array(
			'ack' => 1,
			'ack_msg' => 'Dashboard ready',
			'developer_msg' => 'CP dashboard (web + mobile)',
			'result' => array(
				'channel_partner_id' => $cpId,
				'company_name' => $company_name,
				'person_name' => $person_name,
				'phone' => $phone,
				'email' => isset($cp['email']) ? $cp['email'] : '',
				'client_code' => isset($cp['client_code']) ? $cp['client_code'] : '',
				'gst' => isset($cp['gst']) ? $cp['gst'] : '',
				'welcome_title' => $company_name,
				'welcome_subtitle' => 'Customers, orders, stock ane payments — ahiya thi manage kari sakay.',
				'summary' => array(
					'my_customers' => array(
						'key' => 'my_customers',
						'label' => 'My Customers',
						'hint' => 'Parties under you',
						'value' => $my_customers,
						'icon' => 'users',
					),
					'customer_orders' => array(
						'key' => 'customer_orders',
						'label' => 'Customer Orders',
						'hint' => 'All customer SOs',
						'value' => $customer_orders,
						'icon' => 'cart',
					),
					'pending_payments' => array(
						'key' => 'pending_payments',
						'label' => 'Pending Payments',
						'hint' => 'Baki payments',
						'value' => $pending_payments,
						'icon' => 'rupee',
					),
					'products_in_stock' => array(
						'key' => 'products_in_stock',
						'label' => 'Products In Stock',
						'hint' => 'Available items',
						'value' => $products_in_stock,
						'icon' => 'stock',
					),
				),
				/* Flat counts for easy App binding */
				'counts' => array(
					'my_customers' => $my_customers,
					'customer_orders' => $customer_orders,
					'pending_payments' => $pending_payments,
					'products_in_stock' => $products_in_stock,
				),
				'recent_orders' => $recent_orders,
				'payment_followup' => $payment_followup,
				'menus' => array(
					array('key' => 'my_customers', 'label' => 'My Customers', 'api' => 241),
					array('key' => 'customer_order', 'label' => 'Customer Order', 'api' => 255),
					array('key' => 'my_stock', 'label' => 'My Stock', 'api' => 257),
					array('key' => 'receive_payment', 'label' => 'Receive Payment', 'api' => 259),
					array('key' => 'party_ledger', 'label' => 'Party Ledger', 'api' => 262),
					array('key' => 'so_pi_format', 'label' => 'SO / PI Format', 'api' => 0),
				),
				'actions' => array(
					'new_customer_order' => array('label' => 'New Customer Order', 'hint' => 'Create customer SO'),
					'view_all_orders' => array('label' => 'View all'),
					'receive_payment' => array('label' => 'Receive'),
				),
			),
		);
	}

	private function formatDashboardOrderRow($row)
	{
		$partyId = isset($row['channel_partner_customer_id']) ? (int) $row['channel_partner_customer_id'] : 0;
		$partyName = '';
		if ($partyId > 0) {
			$partyName = $this->db->rp_getValue($this->ctable, "company_name", "id='" . $partyId . "'", 0);
		}
		$status = isset($row['status']) ? (int) $row['status'] : 0;
		$paidFlag = isset($row['payment_received_flag']) ? (int) $row['payment_received_flag'] : 0;
		$paidAmt = isset($row['payment_received_amount']) ? (float) $row['payment_received_amount'] : 0;
		$grand = isset($row['grand_total']) ? (float) $row['grand_total'] : 0;
		$isPaid = ($paidAmt > 0.009 && ($grand - $paidAmt) <= 0.009);
		$isDispatched = ($status >= 5 && $status != 3 && $status != -2);
		if ($isPaid) {
			$statusKey = 'completed';
			$statusLabel = 'Completed';
			$baki = 0;
		} else if ($isDispatched) {
			$statusKey = 'pending_payment';
			$statusLabel = 'Pending Payment';
			$baki = max(0, $grand - $paidAmt);
		} else {
			$statusKey = 'pending';
			$statusLabel = 'Pending';
			$baki = max(0, $grand - $paidAmt);
		}
		$orderDate = isset($row['order_date']) ? $row['order_date'] : '';
		$dateDisplay = ($orderDate != '' && $orderDate != '0000-00-00') ? date('d-m-Y', strtotime($orderDate)) : '-';

		return array(
			'order_id' => (int) $row['id'],
			'order_no' => $row['order_no'],
			'party_id' => $partyId,
			'party_name' => $partyName != '' ? $partyName : '-',
			'order_date' => $orderDate,
			'order_date_display' => $dateDisplay,
			'amount' => round($grand, 2),
			'amount_display' => number_format($grand, 2),
			'payment_received_flag' => $isPaid ? 1 : 0,
			'payment_received_amount' => round($paidAmt, 2),
			'remaining_amount' => round($baki, 2),
			'is_partial' => (!$isPaid && $paidAmt > 0.009) ? 1 : 0,
			'can_receive' => $isPaid ? 0 : 1,
			'baki_amount' => round($baki, 2),
			'status' => $statusKey,
			'status_label' => $statusLabel,
			'order_status_code' => $status,
		);
	}
}
