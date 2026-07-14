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

	public function GetChannelPartnerList()
	{
		$tableCheck = $this->ensureTableReady();
		if ($tableCheck['ack'] != 1) {
			return $tableCheck;
		}

		$result = array();
		$cp_r = $this->db->rp_getData(
			"executive",
			"*",
			"channel_partner_flag=1 AND customer_flag=0 AND isDelete=0",
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
		return $this->GetEditDataChannelPartnerCustomer($detail);
	}

	private function validateRequiredFields($detail, $is_update = false)
	{
		$required = array(
			'channel_partner_id' => 'Channel Partner',
			'company_name' => 'Customer Name',
			'person_name' => 'Person Name',
			'mobile_no' => 'Mobile No',
			'country' => 'Country',
			'state' => 'State',
			'city' => 'City',
		);
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

		$fieldCheck = $this->validateRequiredFields($detail, true);
		if ($fieldCheck['ack'] != 1) {
			return $fieldCheck;
		}

		extract($detail);

		$cpCheck = $this->validateChannelPartnerId($channel_partner_id);
		if ($cpCheck['ack'] != 1) {
			return array("ack" => 0, "developer_msg" => "Invalid channel partner", "ack_msg" => $cpCheck['ack_msg']);
		}

		$dup_where = "mobile_no = '" . $mobile_no . "' AND id!='" . $id . "' AND isDelete=0";
		if ($this->db->rp_dupCheck($this->ctable, $dup_where)) {
			return array("ack" => 0, "developer_msg" => "Mobile already exists", "ack_msg" => "This mobile number is already registered.");
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
			"modified_date" => date('Y-m-d H:i:s'),
		);
		$where = "id='" . $id . "' AND isDelete=0";
		$uid = $this->db->rp_update($this->ctable, $rows, $where, 0);

		if ($uid != 0) {
			return array("ack" => 1, "developer_msg" => "Updated", "ack_msg" => "Channel Partner Customer updated successfully.");
		}

		return array("ack" => 0, "developer_msg" => "Update failed", "ack_msg" => "Failed to update Channel Partner Customer.");
	}

	public function GetEditDataChannelPartnerCustomer($detail)
	{
		$where = " id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable, "*", $where, 0);
		if (!$ctable_r || mysqli_num_rows($ctable_r) == 0) {
			return array("ack" => 0, "developer_msg" => "Not found", "ack_msg" => "Record not found.");
		}

		$ctable_d = mysqli_fetch_array($ctable_r);
		$created_display = !empty($ctable_d['created_date']) ? $ctable_d['created_date'] : $ctable_d['created_at'];
		$updated_display = !empty($ctable_d['modified_date']) ? $ctable_d['modified_date'] : $ctable_d['updated_at'];

		$result = array(
			'channel_partner_id' => $ctable_d['channel_partner_id'],
			'company_name' => htmlentities($ctable_d['company_name']),
			'person_name' => htmlentities($ctable_d['person_name']),
			'mobile_no' => htmlentities($ctable_d['mobile_no']),
			'email' => htmlentities($ctable_d['email']),
			'gst' => htmlentities($ctable_d['gst']),
			'country' => htmlentities($ctable_d['country']),
			'state' => htmlentities($ctable_d['state']),
			'city' => htmlentities($ctable_d['city']),
			'pincode' => htmlentities($ctable_d['pincode']),
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
		$rows = array("isDelete" => "1", "modified_date" => date('Y-m-d H:i:s'));
		$where = "id='" . $id . "' AND isDelete=0";
		$uid = $this->db->rp_update($this->ctable, $rows, $where, 0);

		if ($uid != 0) {
			return array("ack" => 1, "developer_msg" => "Deleted", "ack_msg" => "Channel Partner Customer deleted successfully.");
		}

		return array("ack" => 0, "developer_msg" => "Delete failed", "ack_msg" => "Failed to delete Channel Partner Customer.");
	}
}
