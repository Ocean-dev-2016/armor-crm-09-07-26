<?php
/**
 * Shared data builder for the Employee Visit KRA screen and Excel export.
 * Kept PHP 5.6 compatible.
 */
class EmployeeVisitKraReport
{
	private $db;
	private $remarkLabels = array(
		"A" => "OLD CUSTOMER VISIT",
		"B" => "PAYMENT COLLECTION VISIT",
		"C" => "NEED APPROVAL",
		"D" => "NEW CUSTOMER",
		"E" => "HIGH RATE",
		"F" => "SHORT NOTE",
		"G" => "CALL TO ORDER",
	);
	private $reasonLabels = array(
		"A1" => "Next Week Order",
		"A2" => "Next Month Order",
		"B1" => "Payment Collection With Order",
		"C1" => "Private Consultant",
		"C2" => "Government Consultant",
		"D1" => "Next Week Order",
		"D2" => "Next Month Order",
		"E1" => "High Rate Form",
	);

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function getRemarkLabels()
	{
		return $this->remarkLabels;
	}

	public function getReasonLabels()
	{
		return $this->reasonLabels;
	}

	public function normalizeDateRange($fromDate, $toDate, $maxDays = 62)
	{
		$today = date("Y-m-d");
		$from = $this->normalizeDate($fromDate);
		$to = $this->normalizeDate($toDate);
		if ($from == "" && $to == "") {
			$from = date("Y-m-01");
			$to = date("Y-m-t");
		} else if ($from == "") {
			$from = $to;
		} else if ($to == "") {
			$to = $from;
		}
		if ($from > $to) {
			$tmp = $from;
			$from = $to;
			$to = $tmp;
		}
		$maxTo = date("Y-m-d", strtotime($from . " +" . ((int) $maxDays - 1) . " days"));
		$wasLimited = false;
		if ($to > $maxTo) {
			$to = $maxTo;
			$wasLimited = true;
		}
		return array(
			"from" => $from,
			"to" => $to,
			"today" => $today,
			"was_limited" => $wasLimited,
			"dates" => $this->getDates($from, $to),
		);
	}

	private function normalizeDate($value)
	{
		$value = trim((string) $value);
		if ($value == "") {
			return "";
		}
		$time = strtotime($value);
		return ($time === false) ? "" : date("Y-m-d", $time);
	}

	private function getDates($from, $to)
	{
		$dates = array();
		$current = strtotime($from);
		$last = strtotime($to);
		while ($current <= $last) {
			$dates[] = date("Y-m-d", $current);
			$current = strtotime("+1 day", $current);
		}
		return $dates;
	}

	public function normalizeIds($value)
	{
		if (!is_array($value)) {
			$value = explode(",", (string) $value);
		}
		$ids = array();
		foreach ($value as $id) {
			$id = (int) $id;
			if ($id > 0) {
				$ids[$id] = $id;
			}
		}
		return array_values($ids);
	}

	public function getAccessibleEmployees($requestedIds, $rights)
	{
		$requestedIds = $this->normalizeIds($requestedIds);
		$where = "se.isDelete=0 AND se.isActive=1";
		$referenceId = isset($_SESSION[SITE_SESS . 'REFERANCE_ID']) ? (int) $_SESSION[SITE_SESS . 'REFERANCE_ID'] : 0;
		$adminType = isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) ? (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] : 0;

		if ($adminType != 0 && !empty($rights['personal_flag'])) {
			$where .= " AND se.id='" . $referenceId . "'";
		} else if ($adminType != 0 && !empty($rights['chain_vise_flag']) && $referenceId > 0) {
			$type = $this->db->rp_getValue("sales_executive", "type", "id='" . $referenceId . "' AND isDelete=0", 0);
			$hierarchyColumn = "";
			if ($type == "sales_manager") {
				$hierarchyColumn = "sm_id";
			} else if ($type == "area_sales_manager") {
				$hierarchyColumn = "asm_id";
			} else if ($type == "sales_officer") {
				$hierarchyColumn = "so_id";
			} else if ($type == "sales_executive") {
				$hierarchyColumn = "se_id";
			}
			if ($hierarchyColumn != "") {
				$where .= " AND (se.id='" . $referenceId . "' OR se." . $hierarchyColumn . "='" . $referenceId . "')";
			} else {
				$where .= " AND se.id='" . $referenceId . "'";
			}
		}

		if (!empty($requestedIds)) {
			$where .= " AND se.id IN (" . implode(",", $requestedIds) . ")";
		}
		$sql = "SELECT se.id,se.name,se.phone,se.type,se.state,se.city
			FROM sales_executive se
			WHERE " . $where . "
			ORDER BY se.name";
		$result = $this->safeQuery($sql);
		$employees = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$id = (int) $row['id'];
				$employees[$id] = array(
					"id" => $id,
					"name" => $row['name'],
					"phone" => $row['phone'],
					"type" => $row['type'],
					"state" => $row['state'],
					"city" => $row['city'],
				);
			}
		}
		return $employees;
	}

	public function build($fromDate, $toDate, $requestedIds, $rights)
	{
		$range = $this->normalizeDateRange($fromDate, $toDate);
		$employees = $this->getAccessibleEmployees($requestedIds, $rights);
		$data = array(
			"range" => $range,
			"remark_labels" => $this->remarkLabels,
			"reason_labels" => $this->reasonLabels,
			"employees" => array(),
		);
		if (empty($employees)) {
			return $data;
		}

		foreach ($employees as $id => $employee) {
			$employee['accounts'] = array();
			$employee['daily'] = array();
			foreach ($range['dates'] as $date) {
				$employee['daily'][$date] = $this->emptyDaily();
			}
			$employee['kpi'] = array(
				"approved_expense" => 0,
				"salary" => null,
				"total_sales" => 0,
				"total_visits" => 0,
				"completed_visits" => 0,
				"open_visits" => 0,
				"total_quotations" => 0,
				"approved_pi" => 0,
			);
			$data['employees'][$id] = $employee;
		}

		$employeeIds = array_keys($employees);
		$this->loadVisits($data, $employeeIds);
		$this->loadKpis($data, $employeeIds);
		return $data;
	}

	private function emptyDaily()
	{
		return array(
			"total" => 0,
			"completed" => 0,
			"open" => 0,
			"codes" => array("A" => 0, "B" => 0, "C" => 0, "D" => 0, "E" => 0, "F" => 0, "G" => 0),
		);
	}

	private function loadVisits(&$data, $employeeIds)
	{
		$from = $data['range']['from'];
		$to = $data['range']['to'];
		$executiveColumns = $this->db->rp_getTableColumnNames("executive");
		$pincodeColumn = in_array("pincode", $executiveColumns) ? "pincode" : (in_array("zip", $executiveColumns) ? "zip" : "");
		$pincodeSelect = ($pincodeColumn != "") ? "e." . $pincodeColumn . " AS account_pincode" : "'' AS account_pincode";
		$sql = "SELECT
				v.*,
				pm.name AS purpose_name,
				e.client_code AS account_code,
				e.company_name AS account_company,
				e.cname AS account_person,
				e.turnover AS account_turnover,
				e.gst AS account_gst,
				e.address AS account_address,
				e.main_city AS account_city,
				" . $pincodeSelect . ",
				noi.company_name AS inquiry_company,
				noi.person_name AS inquiry_person,
				noi.mobile_number AS inquiry_mobile
			FROM visit v
			LEFT JOIN executive e ON e.id=v.customer_id
			LEFT JOIN no_order_inquiry noi ON noi.id=v.inquiry_id
			LEFT JOIN purpose_master pm ON pm.id=v.purpose_id
			WHERE v.isDelete=0
				AND v.user_id IN (" . implode(",", $employeeIds) . ")
				AND DATE(CASE
					WHEN v.start_date_time IS NOT NULL AND v.start_date_time!='' AND v.start_date_time!='0000-00-00 00:00:00'
					THEN v.start_date_time ELSE v.created_date END)
				BETWEEN '" . $from . "' AND '" . $to . "'
			ORDER BY v.user_id,v.start_date_time,v.id";
		$result = $this->safeQuery($sql);
		if (!$result) {
			return;
		}

		while ($row = mysqli_fetch_assoc($result)) {
			$employeeId = (int) $row['user_id'];
			if (!isset($data['employees'][$employeeId])) {
				continue;
			}
			$visitDateSource = $this->isValidDateTime($row['start_date_time']) ? $row['start_date_time'] : $row['created_date'];
			$visitDate = date("Y-m-d", strtotime($visitDateSource));
			$completed = $this->isValidDateTime($row['stop_date_time']);
			$codes = $this->normalizeRemarkCodes($row);
			$row['normalized_remark_code'] = $codes['remark_code'];
			$row['normalized_reason_code'] = $codes['reason_code'];
			$row['remark_label'] = isset($this->remarkLabels[$codes['remark_code']]) ? $this->remarkLabels[$codes['remark_code']] : "";
			$row['reason_label'] = isset($this->reasonLabels[$codes['reason_code']]) ? $this->reasonLabels[$codes['reason_code']] : "";
			$row['visit_date'] = $visitDate;
			$row['is_completed'] = $completed;
			$row['duration_minutes'] = $this->durationMinutes($row['start_date_time'], $row['stop_date_time']);

			$account = $this->resolveAccount($row);
			$key = $account['key'];
			if (!isset($data['employees'][$employeeId]['accounts'][$key])) {
				$account['dates'] = array();
				$account['total_visits'] = 0;
				$data['employees'][$employeeId]['accounts'][$key] = $account;
			}
			if (!isset($data['employees'][$employeeId]['accounts'][$key]['dates'][$visitDate])) {
				$data['employees'][$employeeId]['accounts'][$key]['dates'][$visitDate] = array();
			}
			$data['employees'][$employeeId]['accounts'][$key]['dates'][$visitDate][] = $row;
			$data['employees'][$employeeId]['accounts'][$key]['total_visits']++;

			$data['employees'][$employeeId]['daily'][$visitDate]['total']++;
			$data['employees'][$employeeId]['kpi']['total_visits']++;
			if ($completed) {
				$data['employees'][$employeeId]['daily'][$visitDate]['completed']++;
				$data['employees'][$employeeId]['kpi']['completed_visits']++;
			} else {
				$data['employees'][$employeeId]['daily'][$visitDate]['open']++;
				$data['employees'][$employeeId]['kpi']['open_visits']++;
			}
			if (isset($data['employees'][$employeeId]['daily'][$visitDate]['codes'][$codes['remark_code']])) {
				$data['employees'][$employeeId]['daily'][$visitDate]['codes'][$codes['remark_code']]++;
			}
		}

		foreach ($data['employees'] as $employeeId => $employee) {
			uasort($data['employees'][$employeeId]['accounts'], array($this, "sortAccounts"));
		}
	}

	public function sortAccounts($a, $b)
	{
		return strcasecmp($a['company'], $b['company']);
	}

	private function resolveAccount($row)
	{
		if ((int) $row['customer_id'] > 0) {
			return array(
				"key" => "C" . (int) $row['customer_id'],
				"type" => "Existing Customer",
				"code" => $row['account_code'],
				"company" => ($row['account_company'] != "") ? $row['account_company'] : $row['account_person'],
				"person" => $row['account_person'],
				"turnover" => $row['account_turnover'],
				"gst" => $row['account_gst'],
				"address" => $row['account_address'],
				"city" => $row['account_city'],
				"pincode" => $row['account_pincode'],
			);
		}
		if ((int) $row['inquiry_id'] > 0) {
			return array(
				"key" => "I" . (int) $row['inquiry_id'],
				"type" => "Inquiry",
				"code" => "",
				"company" => ($row['inquiry_company'] != "") ? $row['inquiry_company'] : $row['inquiry_person'],
				"person" => $row['inquiry_person'],
				"turnover" => "",
				"gst" => "",
				"address" => "",
				"city" => "",
				"pincode" => "",
			);
		}
		$company = ($row['firm_name'] != "") ? $row['firm_name'] : (($row['client_name'] != "") ? $row['client_name'] : "Visit #" . $row['id']);
		return array(
			"key" => "N" . (int) $row['id'],
			"type" => "New Customer",
			"code" => "",
			"company" => $company,
			"person" => $row['client_name'],
			"turnover" => "",
			"gst" => "",
			"address" => $row['stop_app_address'],
			"city" => "",
			"pincode" => "",
		);
	}

	private function normalizeRemarkCodes($row)
	{
		$remark = strtoupper(trim((string) $row['remark_code']));
		$reason = strtoupper(trim((string) $row['reason_code']));
		$text = trim((string) $row['stop_remark']);
		if ($remark == "" && preg_match('/\(([A-G])\)/i', $text, $match)) {
			$remark = strtoupper($match[1]);
		}
		if ($reason == "" && preg_match('/\b([A-G]\d)\b/i', $text, $match)) {
			$reason = strtoupper($match[1]);
		}
		if ($remark == "" && $reason != "") {
			$remark = substr($reason, 0, 1);
		}
		return array("remark_code" => $remark, "reason_code" => $reason);
	}

	private function isValidDateTime($value)
	{
		return ($value != "" && $value != "0000-00-00 00:00:00" && strtotime($value) !== false);
	}

	private function durationMinutes($start, $stop)
	{
		if (!$this->isValidDateTime($start) || !$this->isValidDateTime($stop)) {
			return null;
		}
		$seconds = strtotime($stop) - strtotime($start);
		return ($seconds >= 0) ? (int) floor($seconds / 60) : null;
	}

	private function loadKpis(&$data, $employeeIds)
	{
		$ids = implode(",", $employeeIds);
		$from = $data['range']['from'];
		$to = $data['range']['to'];

		$this->applyGroupedKpi(
			$data,
			"SELECT sales_executive_id AS employee_id,SUM(pass_expense_amount) AS metric
			 FROM expense
			 WHERE isDelete=0 AND expense_status=1 AND sales_executive_id IN (" . $ids . ")
			 AND DATE(expense_date) BETWEEN '" . $from . "' AND '" . $to . "'
			 GROUP BY sales_executive_id",
			"approved_expense"
		);
		$this->applyGroupedKpi(
			$data,
			"SELECT sales_id AS employee_id,SUM(grand_total) AS metric
			 FROM orders
			 WHERE isDelete=0 AND status NOT IN (-1,-2,3) AND sales_id IN (" . $ids . ")
			 AND DATE(order_date) BETWEEN '" . $from . "' AND '" . $to . "'
			 GROUP BY sales_id",
			"total_sales"
		);
		$this->applyGroupedKpi(
			$data,
			"SELECT sales_id AS employee_id,COUNT(*) AS metric
			 FROM quotation_detail
			 WHERE isDelete=0 AND sales_id IN (" . $ids . ")
			 AND DATE(quotation_date) BETWEEN '" . $from . "' AND '" . $to . "'
			 GROUP BY sales_id",
			"total_quotations"
		);
		$piSql = "SELECT pi_sales.employee_id,COUNT(DISTINCT pi_sales.pi_id) AS metric
			FROM (
				SELECT q.sales_id AS employee_id,pi.id AS pi_id
				FROM proforma_invoice_info pi
				INNER JOIN quotation_detail q ON q.proforma_invoice_id=pi.id AND q.isDelete=0
				WHERE pi.isDelete=0 AND pi.status=1
					AND q.sales_id IN (" . $ids . ")
					AND DATE(CASE WHEN pi.modified_date IS NOT NULL AND pi.modified_date!='' AND pi.modified_date!='0000-00-00 00:00:00'
						THEN pi.modified_date ELSE pi.invoice_date END) BETWEEN '" . $from . "' AND '" . $to . "'
				UNION
				SELECT o.sales_id AS employee_id,pi.id AS pi_id
				FROM proforma_invoice_info pi
				INNER JOIN orders o ON o.proforma_invoice_id=pi.id AND o.isDelete=0
				WHERE pi.isDelete=0 AND pi.status=1
					AND o.sales_id IN (" . $ids . ")
					AND DATE(CASE WHEN pi.modified_date IS NOT NULL AND pi.modified_date!='' AND pi.modified_date!='0000-00-00 00:00:00'
						THEN pi.modified_date ELSE pi.invoice_date END) BETWEEN '" . $from . "' AND '" . $to . "'
			) pi_sales
			GROUP BY pi_sales.employee_id";
		$this->applyGroupedKpi($data, $piSql, "approved_pi");
	}

	private function applyGroupedKpi(&$data, $sql, $key)
	{
		$result = $this->safeQuery($sql);
		if (!$result) {
			return;
		}
		while ($row = mysqli_fetch_assoc($result)) {
			$id = (int) $row['employee_id'];
			if (isset($data['employees'][$id])) {
				$data['employees'][$id]['kpi'][$key] = (float) $row['metric'];
			}
		}
	}

	private function safeQuery($sql)
	{
		$result = @mysqli_query($this->db->myconn, $sql);
		return ($result === false) ? false : $result;
	}
}
