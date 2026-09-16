<?php
/**
 * Daily Plan + Completion helpers for App Punch IN / Punch OUT flow.
 * Services: 236 save_daily_plan, 237 get_daily_plan_status, 238 save_daily_plan_completion
 */
require_once("main.class.php");
require_once("function.class.php");

class DailyPlan extends Functions
{
	public $db;

	function __construct()
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db = $db;
	}

	/**
	 * Parse customers JSON from request (string or array).
	 */
	public function parseCustomers($raw)
	{
		if (is_array($raw)) {
			return $raw;
		}
		if ($raw === "" || $raw === null) {
			return array();
		}
		$decoded = json_decode($raw, true);
		return is_array($decoded) ? $decoded : array();
	}

	/**
	 * Check if customer belongs to sales executive (executive.seid).
	 */
	public function isCustomerAssigned($customer_id, $sales_id)
	{
		$customer_id = (int) $customer_id;
		$sales_id = (int) $sales_id;
		if ($customer_id <= 0 || $sales_id <= 0) {
			return false;
		}
		$count = $this->db->rp_getTotalRecord(
			"executive",
			"id='" . $customer_id . "' AND seid='" . $sales_id . "' AND isDelete=0 AND isActive=1 AND IFNULL(channel_partner_flag,0)=0",
			0
		);
		return ($count > 0);
	}

	/**
	 * Save morning daily plan (service 236).
	 */
	public function saveDailyPlan($detail)
	{
		$sales_id = isset($detail['sales_id']) ? (int) $detail['sales_id'] : 0;
		$plan_date = isset($detail['plan_date']) && $detail['plan_date'] != ""
			? date('Y-m-d', strtotime($detail['plan_date']))
			: date('Y-m-d');

		$expected_order_amount = isset($detail['expected_order_amount']) && $detail['expected_order_amount'] !== ""
			? $detail['expected_order_amount'] : null;
		$expected_approval_count = isset($detail['expected_approval_count']) && $detail['expected_approval_count'] !== ""
			? $detail['expected_approval_count'] : null;
		$expected_project_detail_count = isset($detail['expected_project_detail_count']) && $detail['expected_project_detail_count'] !== ""
			? $detail['expected_project_detail_count'] : null;

		if ($sales_id <= 0) {
			return array("ack" => 0, "ack_msg" => "Sales id is required.", "developer_msg" => "sales_id missing");
		}

		$sales_ok = $this->db->rp_getTotalRecord("sales_executive", "id='" . $sales_id . "' AND isDelete=0", 0);
		if ($sales_ok <= 0) {
			return array("ack" => 0, "ack_msg" => "Invalid sales executive.", "developer_msg" => "sales_id not found");
		}

		$orderAmt = ($expected_order_amount !== null && $expected_order_amount !== "") ? (float) $expected_order_amount : 0;
		$approvalCnt = ($expected_approval_count !== null && $expected_approval_count !== "") ? (int) $expected_approval_count : 0;
		$projectCnt = ($expected_project_detail_count !== null && $expected_project_detail_count !== "") ? (int) $expected_project_detail_count : 0;

		if ($orderAmt <= 0 && $approvalCnt <= 0 && $projectCnt <= 0) {
			return array(
				"ack" => 0,
				"ack_msg" => "At least one target is required",
				"developer_msg" => "All expected fields empty or 0"
			);
		}

		$existing = $this->db->rp_getValue(
			"daily_plan",
			"id",
			"sales_id='" . $sales_id . "' AND plan_date='" . $plan_date . "' AND isDelete=0",
			0
		);
		if ($existing != "" && $existing != "0" && $existing !== false) {
			return array(
				"ack" => 0,
				"ack_msg" => "Daily plan already submitted",
				"developer_msg" => "Plan exists for sales_id + plan_date",
				"daily_plan_id" => (string) $existing
			);
		}

		$customers = $this->parseCustomers(isset($detail['customers']) ? $detail['customers'] : "");
		$needCustomer = ($orderAmt > 0 || $approvalCnt > 0);
		if ($needCustomer && empty($customers)) {
			return array(
				"ack" => 0,
				"ack_msg" => "Customer selection required",
				"developer_msg" => "Order/approval filled but no customers"
			);
		}

		$validCustomers = array();
		foreach ($customers as $c) {
			$cid = isset($c['customer_id']) ? (int) $c['customer_id'] : 0;
			$tt = isset($c['target_type']) ? strtolower(trim($c['target_type'])) : "";
			if ($cid <= 0 || ($tt != "order" && $tt != "approval")) {
				continue;
			}
			if (!$this->isCustomerAssigned($cid, $sales_id)) {
				return array(
					"ack" => 0,
					"ack_msg" => "Invalid customer",
					"developer_msg" => "customer_id " . $cid . " not assigned to sales_id"
				);
			}
			$validCustomers[] = array("customer_id" => $cid, "target_type" => $tt);
		}

		if ($needCustomer && empty($validCustomers)) {
			return array(
				"ack" => 0,
				"ack_msg" => "Customer selection required",
				"developer_msg" => "No valid customers in list"
			);
		}

		$now = date('Y-m-d H:i:s');
		$rows = array("sales_id", "plan_date", "created_date", "isDelete", "isActive");
		$values = array($sales_id, $plan_date, $now, 0, 1);
		if ($orderAmt > 0) {
			$rows[] = "expected_order_amount";
			$values[] = $orderAmt;
		}
		if ($approvalCnt > 0) {
			$rows[] = "expected_approval_count";
			$values[] = $approvalCnt;
		}
		if ($projectCnt > 0) {
			$rows[] = "expected_project_detail_count";
			$values[] = $projectCnt;
		}
		$plan_id = $this->db->rp_insert("daily_plan", $values, $rows, 0);
		if (!$plan_id) {
			return array("ack" => 0, "ack_msg" => "Failed to save daily plan.", "developer_msg" => "daily_plan insert failed");
		}

		foreach ($validCustomers as $vc) {
			$this->db->rp_insert(
				"daily_plan_customer",
				array($plan_id, $vc['customer_id'], $vc['target_type'], $now, 0),
				array("daily_plan_id", "customer_id", "target_type", "created_date", "isDelete"),
				0
			);
		}

		return array(
			"ack" => 1,
			"ack_msg" => "Daily plan saved successfully.",
			"developer_msg" => "daily_plan insert success",
			"daily_plan_id" => (string) $plan_id,
			"result" => array(
				"id" => (string) $plan_id,
				"sales_id" => (string) $sales_id,
				"plan_date" => $plan_date,
				"expected_order_amount" => ($orderAmt > 0 ? number_format($orderAmt, 2, '.', '') : null),
				"expected_approval_count" => ($approvalCnt > 0 ? (string) $approvalCnt : null),
				"expected_project_detail_count" => ($projectCnt > 0 ? (string) $projectCnt : null),
			)
		);
	}

	/**
	 * Get today's plan status (service 237).
	 */
	public function getDailyPlanStatus($sales_id, $plan_date = "")
	{
		$sales_id = (int) $sales_id;
		if ($sales_id <= 0) {
			return array("ack" => 0, "ack_msg" => "Sales id is required.", "developer_msg" => "sales_id missing");
		}
		$plan_date = ($plan_date != "") ? date('Y-m-d', strtotime($plan_date)) : date('Y-m-d');

		$plan_r = $this->db->rp_getData(
			"daily_plan",
			"*",
			"sales_id='" . $sales_id . "' AND plan_date='" . $plan_date . "' AND isDelete=0",
			"id DESC",
			0,
			"1"
		);
		if (!$plan_r) {
			return array(
				"ack" => 1,
				"ack_msg" => "No daily plan for today.",
				"plan_submitted" => 0,
				"completion_submitted" => 0,
				"can_punch_out" => 0,
				"result" => null
			);
		}
		$plan = mysqli_fetch_assoc($plan_r);
		if (!$plan) {
			return array(
				"ack" => 1,
				"plan_submitted" => 0,
				"completion_submitted" => 0,
				"can_punch_out" => 0,
				"result" => null
			);
		}

		$customers = array();
		$cust_r = $this->db->rp_getData(
			"daily_plan_customer",
			"*",
			"daily_plan_id='" . $plan['id'] . "' AND isDelete=0",
			"",
			0
		);
		if ($cust_r) {
			while ($c = mysqli_fetch_assoc($cust_r)) {
				$code = $this->db->rp_getValue("executive", "client_code", "id='" . $c['customer_id'] . "'", 0);
				$name = $this->db->rp_getValue("executive", "cname", "id='" . $c['customer_id'] . "'", 0);
				if ($name == "") {
					$name = $this->db->rp_getValue("executive", "company_name", "id='" . $c['customer_id'] . "'", 0);
				}
				$label = trim(($code != "" ? $code : "") . " - " . $name);
				$customers[] = array(
					"customer_id" => (string) $c['customer_id'],
					"customer_code" => $code ? $code : "",
					"customer_name" => $name ? $name : "",
					"display_label" => $label,
					"target_type" => $c['target_type'],
				);
			}
		}

		$completion = null;
		$completion_submitted = 0;
		$can_punch_out = 0;
		$comp_r = $this->db->rp_getData(
			"daily_plan_completion",
			"*",
			"daily_plan_id='" . $plan['id'] . "' AND isDelete=0",
			"id DESC",
			0,
			"1"
		);
		if ($comp_r) {
			$comp = mysqli_fetch_assoc($comp_r);
			if ($comp) {
				$completion_submitted = 1;
				$can_punch_out = 1;
				$completion = array(
					"actual_order_amount" => $comp['actual_order_amount'],
					"actual_approval_count" => $comp['actual_approval_count'],
					"actual_project_detail_count" => $comp['actual_project_detail_count'],
				);
			}
		}

		$result = array(
			"daily_plan_id" => (string) $plan['id'],
			"plan_date" => $plan['plan_date'],
			"expected_order_amount" => $plan['expected_order_amount'],
			"expected_approval_count" => $plan['expected_approval_count'],
			"expected_project_detail_count" => $plan['expected_project_detail_count'],
			"customers" => $customers,
		);
		if ($completion) {
			$result['completion'] = $completion;
		}

		return array(
			"ack" => 1,
			"ack_msg" => "Daily plan found.",
			"plan_submitted" => 1,
			"completion_submitted" => $completion_submitted,
			"can_punch_out" => $can_punch_out,
			"result" => $result
		);
	}

	/**
	 * Save evening completion (service 238).
	 */
	public function saveDailyPlanCompletion($detail)
	{
		$sales_id = isset($detail['sales_id']) ? (int) $detail['sales_id'] : 0;
		$daily_plan_id = isset($detail['daily_plan_id']) ? (int) $detail['daily_plan_id'] : 0;

		if ($sales_id <= 0 || $daily_plan_id <= 0) {
			return array("ack" => 0, "ack_msg" => "sales_id and daily_plan_id are required.", "developer_msg" => "params missing");
		}

		$plan_r = $this->db->rp_getData(
			"daily_plan",
			"*",
			"id='" . $daily_plan_id . "' AND sales_id='" . $sales_id . "' AND isDelete=0",
			"",
			0,
			"1"
		);
		if (!$plan_r) {
			return array("ack" => 0, "ack_msg" => "Daily plan not found", "developer_msg" => "Invalid daily_plan_id");
		}
		$plan = mysqli_fetch_assoc($plan_r);
		if (!$plan) {
			return array("ack" => 0, "ack_msg" => "Daily plan not found", "developer_msg" => "Invalid daily_plan_id");
		}

		$existing = $this->db->rp_getValue(
			"daily_plan_completion",
			"id",
			"daily_plan_id='" . $daily_plan_id . "' AND isDelete=0",
			0
		);
		if ($existing != "" && $existing != "0" && $existing !== false) {
			return array(
				"ack" => 0,
				"ack_msg" => "Completion already submitted",
				"developer_msg" => "Duplicate completion for plan",
				"completion_id" => (string) $existing,
				"can_punch_out" => 1
			);
		}

		$actual_order = isset($detail['actual_order_amount']) ? $detail['actual_order_amount'] : null;
		$actual_approval = isset($detail['actual_approval_count']) ? $detail['actual_approval_count'] : null;
		$actual_project = isset($detail['actual_project_detail_count']) ? $detail['actual_project_detail_count'] : null;

		if ($plan['expected_order_amount'] !== null && $plan['expected_order_amount'] !== "" && (float) $plan['expected_order_amount'] > 0) {
			if ($actual_order === null || $actual_order === "") {
				return array(
					"ack" => 0,
					"ack_msg" => "Please fill completion for all morning targets",
					"developer_msg" => "actual_order_amount missing"
				);
			}
		}
		if ($plan['expected_approval_count'] !== null && $plan['expected_approval_count'] !== "" && (int) $plan['expected_approval_count'] > 0) {
			if ($actual_approval === null || $actual_approval === "") {
				return array(
					"ack" => 0,
					"ack_msg" => "Please fill completion for all morning targets",
					"developer_msg" => "actual_approval_count missing"
				);
			}
		}
		if ($plan['expected_project_detail_count'] !== null && $plan['expected_project_detail_count'] !== "" && (int) $plan['expected_project_detail_count'] > 0) {
			if ($actual_project === null || $actual_project === "") {
				return array(
					"ack" => 0,
					"ack_msg" => "Please fill completion for all morning targets",
					"developer_msg" => "actual_project_detail_count missing"
				);
			}
		}

		$now = date('Y-m-d H:i:s');
		$rows = array("daily_plan_id", "submitted_at", "isDelete");
		$values = array($daily_plan_id, $now, 0);
		if ($actual_order !== null && $actual_order !== "") {
			$rows[] = "actual_order_amount";
			$values[] = $actual_order;
		}
		if ($actual_approval !== null && $actual_approval !== "") {
			$rows[] = "actual_approval_count";
			$values[] = (int) $actual_approval;
		}
		if ($actual_project !== null && $actual_project !== "") {
			$rows[] = "actual_project_detail_count";
			$values[] = (int) $actual_project;
		}
		$comp_id = $this->db->rp_insert("daily_plan_completion", $values, $rows, 0);

		if (!$comp_id) {
			return array("ack" => 0, "ack_msg" => "Failed to save completion.", "developer_msg" => "insert failed");
		}

		return array(
			"ack" => 1,
			"ack_msg" => "Daily completion saved. You can now punch out.",
			"developer_msg" => "daily_plan_completion insert success",
			"completion_id" => (string) $comp_id,
			"can_punch_out" => 1
		);
	}

	/**
	 * Can punch out? Used by add_attendance type=Out.
	 */
	public function canPunchOut($sales_id, $plan_date = "")
	{
		$status = $this->getDailyPlanStatus($sales_id, $plan_date);
		if (!isset($status['can_punch_out']) || $status['can_punch_out'] != 1) {
			$plan_id = "";
			if (isset($status['result']['daily_plan_id'])) {
				$plan_id = $status['result']['daily_plan_id'];
			}
			return array(
				"ok" => 0,
				"require_completion" => 1,
				"daily_plan_id" => $plan_id,
				"plan_submitted" => isset($status['plan_submitted']) ? $status['plan_submitted'] : 0,
				"ack_msg" => ($plan_id == "")
					? "Please submit daily plan first"
					: "Please submit daily completion before punch out.",
				"developer_msg" => ($plan_id == "") ? "daily_plan missing" : "daily_plan_completion missing"
			);
		}
		return array(
			"ok" => 1,
			"daily_plan_id" => $status['result']['daily_plan_id']
		);
	}

	/**
	 * Link attendance IN id to today's plan.
	 */
	public function linkAttendanceIn($sales_id, $attendance_id, $plan_date = "")
	{
		$sales_id = (int) $sales_id;
		$attendance_id = (int) $attendance_id;
		$plan_date = ($plan_date != "") ? date('Y-m-d', strtotime($plan_date)) : date('Y-m-d');
		$plan_id = $this->db->rp_getValue(
			"daily_plan",
			"id",
			"sales_id='" . $sales_id . "' AND plan_date='" . $plan_date . "' AND isDelete=0",
			0
		);
		if ($plan_id != "" && $plan_id != "0" && $plan_id !== false) {
			$this->db->rp_update("daily_plan", array("attendance_in_id" => $attendance_id), "id='" . $plan_id . "'", 0);
			return $plan_id;
		}
		return false;
	}

	/**
	 * Link attendance OUT id to completion.
	 */
	public function linkAttendanceOut($sales_id, $attendance_id, $plan_date = "")
	{
		$sales_id = (int) $sales_id;
		$attendance_id = (int) $attendance_id;
		$plan_date = ($plan_date != "") ? date('Y-m-d', strtotime($plan_date)) : date('Y-m-d');
		$plan_id = $this->db->rp_getValue(
			"daily_plan",
			"id",
			"sales_id='" . $sales_id . "' AND plan_date='" . $plan_date . "' AND isDelete=0",
			0
		);
		if ($plan_id == "" || $plan_id == "0" || $plan_id === false) {
			return false;
		}
		$comp_id = $this->db->rp_getValue(
			"daily_plan_completion",
			"id",
			"daily_plan_id='" . $plan_id . "' AND isDelete=0",
			0
		);
		if ($comp_id != "" && $comp_id != "0" && $comp_id !== false) {
			$this->db->rp_update("daily_plan_completion", array("attendance_out_id" => $attendance_id), "id='" . $comp_id . "'", 0);
			return $comp_id;
		}
		return false;
	}

	/**
	 * Save visit completion answers (5 Yes/No).
	 */
	public function saveVisitCompletionAnswer($visit_id, $answers)
	{
		$visit_id = (int) $visit_id;
		if ($visit_id <= 0) {
			return array("ack" => 0, "ack_msg" => "Visit id required.", "developer_msg" => "visit_id missing");
		}
		$fields = array('order_came', 'approval_came', 'project_detail_came', 'contract_detail_came', 'payment_came');
		$data = array();
		foreach ($fields as $f) {
			if (!isset($answers[$f]) || ($answers[$f] !== "0" && $answers[$f] !== "1" && $answers[$f] !== 0 && $answers[$f] !== 1)) {
				return array(
					"ack" => 0,
					"ack_msg" => "Please answer all visit completion questions.",
					"developer_msg" => $f . " missing or invalid"
				);
			}
			$data[$f] = ((int) $answers[$f]) ? 1 : 0;
		}

		$existing = $this->db->rp_getValue("visit_completion_answer", "id", "visit_id='" . $visit_id . "' AND isDelete=0", 0);
		$now = date('Y-m-d H:i:s');
		if ($existing != "" && $existing != "0" && $existing !== false) {
			$this->db->rp_update(
				"visit_completion_answer",
				array(
					"order_came" => $data['order_came'],
					"approval_came" => $data['approval_came'],
					"project_detail_came" => $data['project_detail_came'],
					"contract_detail_came" => $data['contract_detail_came'],
					"payment_came" => $data['payment_came'],
				),
				"id='" . $existing . "'",
				0
			);
			return array("ack" => 1, "id" => (string) $existing);
		}

		$id = $this->db->rp_insert(
			"visit_completion_answer",
			array(
				$visit_id,
				$data['order_came'],
				$data['approval_came'],
				$data['project_detail_came'],
				$data['contract_detail_came'],
				$data['payment_came'],
				$now,
				0
			),
			array(
				"visit_id",
				"order_came",
				"approval_came",
				"project_detail_came",
				"contract_detail_came",
				"payment_came",
				"created_date",
				"isDelete"
			),
			0
		);
		return array("ack" => 1, "id" => (string) $id);
	}

	/**
	 * Save quotation submit questionnaire (service 240).
	 */
	public function saveQuotationQuestionnaire($detail)
	{
		$quotation_id = isset($detail['quotation_id']) ? (int) $detail['quotation_id'] : 0;
		if ($quotation_id <= 0 && isset($detail['cart_id'])) {
			$quotation_id = (int) $detail['cart_id'];
		}
		$knows = isset($detail['knows_full_range']) ? $detail['knows_full_range'] : "";
		if ($quotation_id <= 0) {
			return array("ack" => 0, "ack_msg" => "quotation_id is required.", "developer_msg" => "quotation_id missing");
		}
		if ($knows !== "0" && $knows !== "1" && $knows !== 0 && $knows !== 1) {
			return array("ack" => 0, "ack_msg" => "Please select Yes or No for full range.", "developer_msg" => "knows_full_range invalid");
		}
		$knows = (int) $knows;
		$reason = isset($detail['not_buying_reason']) && $detail['not_buying_reason'] !== ""
			? (int) $detail['not_buying_reason'] : null;
		$high_rate_form_id = isset($detail['high_rate_form_id']) && $detail['high_rate_form_id'] !== ""
			? (int) $detail['high_rate_form_id'] : null;
		$consultant_form_id = isset($detail['consultant_form_id']) && $detail['consultant_form_id'] !== ""
			? (int) $detail['consultant_form_id'] : null;
		$remark = isset($detail['remark']) ? $detail['remark'] : "";

		if ($knows == 0) {
			if ($reason === null || !in_array($reason, array(1, 2, 3), true)) {
				return array("ack" => 0, "ack_msg" => "Please select a reason.", "developer_msg" => "not_buying_reason required");
			}
			if ($reason == 2 && (!$high_rate_form_id || $high_rate_form_id <= 0)) {
				return array("ack" => 0, "ack_msg" => "Please complete High Rate form", "developer_msg" => "high_rate_form_id required");
			}
			if ($reason == 3 && (!$consultant_form_id || $consultant_form_id <= 0)) {
				return array("ack" => 0, "ack_msg" => "Please complete Approval form", "developer_msg" => "consultant_form_id required");
			}
		} else {
			$reason = null;
			$high_rate_form_id = null;
			$consultant_form_id = null;
		}

		$now = date('Y-m-d H:i:s');
		$rows = array("quotation_id", "knows_full_range", "created_date", "isDelete");
		$values = array($quotation_id, $knows, $now, 0);
		if ($reason !== null) {
			$rows[] = "not_buying_reason";
			$values[] = $reason;
		}
		if ($high_rate_form_id !== null && $high_rate_form_id > 0) {
			$rows[] = "high_rate_form_id";
			$values[] = $high_rate_form_id;
		}
		if ($consultant_form_id !== null && $consultant_form_id > 0) {
			$rows[] = "consultant_form_id";
			$values[] = $consultant_form_id;
		}
		if ($remark != "") {
			$rows[] = "remark";
			$values[] = $remark;
		}
		$qid = $this->db->rp_insert("quotation_submit_questionnaire", $values, $rows, 0);
		if (!$qid) {
			return array("ack" => 0, "ack_msg" => "Failed to save questionnaire.", "developer_msg" => "insert failed");
		}

		/* Optional link on quotation_detail if column exists */
		@$this->db->rp_update("quotation_detail", array("questionnaire_id" => $qid), "id='" . $quotation_id . "'", 0);

		return array(
			"ack" => 1,
			"ack_msg" => "Questionnaire saved.",
			"questionnaire_id" => (string) $qid,
			"can_submit_quotation" => 1
		);
	}
}
