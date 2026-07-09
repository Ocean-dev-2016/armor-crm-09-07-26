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
			return array("ack" => 1, "developer_msg" => "Inserted", "ack_msg" => "Channel Partner Customer added successfully.");
		}

		return array("ack" => 0, "developer_msg" => "Insert failed", "ack_msg" => "Failed to add Channel Partner Customer. Please run db_sync.php once.");
	}

	public function UpdateChannelPartnerCustomer($detail)
	{
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
		$rows = array("isDelete" => "1", "modified_date" => date('Y-m-d H:i:s'));
		$where = "id='" . $detail['id'] . "'";
		$uid = $this->db->rp_update($this->ctable, $rows, $where);

		if ($uid != 0) {
			return array("ack" => 1, "developer_msg" => "Deleted", "ack_msg" => "Channel Partner Customer deleted successfully.");
		}

		return array("ack" => 0, "developer_msg" => "Delete failed", "ack_msg" => "Failed to delete Channel Partner Customer.");
	}
}
