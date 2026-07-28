<?php
/**
 * Remark Analysis — Remark Wise Report data builder (PHP 5.6 compatible).
 * Groups visits by Visit Remark Code hierarchy (A/A1/A2, B/B1, …).
 */
class RemarkAnalysisReport
{
	private $db;

	/** Parent remark codes (A–G) */
	private $remarkLabels = array(
		"A" => "Old Customer Visit",
		"B" => "Payment Collection Visit",
		"C" => "Need Approval",
		"D" => "New Customer",
		"E" => "High Rate",
		"F" => "Short Note",
		"G" => "Call to Order",
	);

	/** Child reason codes under parents */
	private $reasonLabels = array(
		"A1" => "Next Week Order",
		"A2" => "Next Month Order",
		"B1" => "Payment Collection With Order",
		"C1" => "Private Consultant Approval",
		"C2" => "Government Consultant Approval",
		"D1" => "Next Week Order",
		"D2" => "Next Month Order",
		"E1" => "Open Form",
	);

	/** Hierarchy: parent => child codes (empty = no children) */
	private $hierarchy = array(
		"A" => array("A1", "A2"),
		"B" => array("B1"),
		"C" => array("C1", "C2"),
		"D" => array("D1", "D2"),
		"E" => array("E1"),
		"F" => array(),
		"G" => array(),
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

	public function getHierarchy()
	{
		return $this->hierarchy;
	}

	public function normalizeDateRange($fromDate, $toDate, $maxDays = 365)
	{
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
			"was_limited" => $wasLimited,
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
		$sql = "SELECT se.id, se.name, se.phone, se.type
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
				);
			}
		}
		return $employees;
	}

	public function getCustomerOptions($limit = 5000)
	{
		$limit = (int) $limit;
		if ($limit < 1) {
			$limit = 5000;
		}
		$sql = "SELECT id, company_name, cname, client_code
			FROM executive
			WHERE isDelete=0
			ORDER BY company_name ASC
			LIMIT " . $limit;
		$result = $this->safeQuery($sql);
		$customers = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$name = trim($row['company_name']);
				if ($name == "") {
					$name = trim($row['cname']);
				}
				if ($name == "") {
					$name = "Customer #" . $row['id'];
				}
				$code = trim($row['client_code']);
				$label = ($code != "") ? ($code . " - " . $name) : $name;
				$customers[] = array(
					"id" => (int) $row['id'],
					"label" => $label,
				);
			}
		}
		return $customers;
	}

	/**
	 * @param string $fromDate
	 * @param string $toDate
	 * @param array|string $employeeIds
	 * @param array|string $customerIds
	 * @param string $remarkFilter parent (A) or child (A1) code; empty = all
	 * @param array $rights
	 */
	public function build($fromDate, $toDate, $employeeIds, $customerIds, $remarkFilter, $rights)
	{
		$range = $this->normalizeDateRange($fromDate, $toDate);
		$requestedEmployeeIds = $this->normalizeIds($employeeIds);
		$employees = $this->getAccessibleEmployees($requestedEmployeeIds, $rights);
		$customerIds = $this->normalizeIds($customerIds);
		$remarkFilter = strtoupper(trim((string) $remarkFilter));

		$summary = $this->emptySummary();
		$data = array(
			"range" => $range,
			"remark_labels" => $this->remarkLabels,
			"reason_labels" => $this->reasonLabels,
			"hierarchy" => $this->hierarchy,
			"summary" => $summary,
			"visits" => array(),
			"total_visits" => 0,
			"total_duration_minutes" => 0,
			"total_approved_expense" => 0,
			"total_kilometer" => 0,
			"expense_per_km" => 0,
			"employees" => $employees,
			"query_error" => "",
		);

		$adminType = isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) ? (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] : 0;
		$hasEmployeeRestriction = !empty($requestedEmployeeIds);
		if (!$hasEmployeeRestriction && $adminType != 0 && !empty($rights['personal_flag'])) {
			$hasEmployeeRestriction = true;
		}
		if (!$hasEmployeeRestriction && $adminType != 0 && !empty($rights['chain_vise_flag'])) {
			$hasEmployeeRestriction = true;
		}

		if ($hasEmployeeRestriction && empty($employees)) {
			return $data;
		}

		$whereExtra = "";
		if ($hasEmployeeRestriction) {
			$employeeIdList = implode(",", array_keys($employees));
			$whereExtra .= " AND v.user_id IN (" . $employeeIdList . ")";
		}
		if (!empty($customerIds)) {
			$whereExtra .= " AND v.customer_id IN (" . implode(",", $customerIds) . ")";
		}

		$sql = "SELECT
				v.id,
				v.user_id,
				v.customer_id,
				v.inquiry_id,
				v.remark_code,
				v.reason_code,
				v.stop_remark,
				v.note,
				v.remark,
				v.start_date_time,
				v.stop_date_time,
				v.created_date,
				v.firm_name,
				v.client_name,
				se.name AS sales_person_name,
				e.company_name AS customer_company,
				e.cname AS customer_person,
				e.client_code AS customer_code,
				e.turnover AS customer_turnover,
				e.turnover_year AS customer_turnover_year,
				e.gst AS customer_gst,
				e.address AS customer_address,
				e.main_city AS customer_city,
				e.phone AS customer_phone,
				e.mobile_no1 AS customer_mobile_no1,
				noi.company_name AS inquiry_company,
				noi.person_name AS inquiry_person,
				noi.mobile_number AS inquiry_mobile
			FROM visit v
			LEFT JOIN sales_executive se ON se.id = v.user_id
			LEFT JOIN executive e ON e.id = v.customer_id
			LEFT JOIN no_order_inquiry noi ON noi.id = v.inquiry_id
			WHERE v.isDelete = 0
				AND DATE(CASE
					WHEN v.start_date_time IS NOT NULL AND v.start_date_time != '0000-00-00 00:00:00'
					THEN v.start_date_time
					ELSE v.created_date
				END)
				BETWEEN '" . $range['from'] . "' AND '" . $range['to'] . "'
				" . $whereExtra . "
			ORDER BY v.start_date_time DESC, v.id DESC";

		$result = $this->safeQuery($sql);
		if (!$result) {
			$data['query_error'] = $this->lastQueryError;
			return $data;
		}

		$visitRows = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$codes = $this->normalizeRemarkCodes($row);
			$parent = $codes['remark_code'];
			$child = $codes['reason_code'];

			if ($remarkFilter != "") {
				if (strlen($remarkFilter) == 1) {
					if ($parent != $remarkFilter) {
						continue;
					}
				} else {
					if ($child != $remarkFilter && !($child == "" && $parent == substr($remarkFilter, 0, 1))) {
						continue;
					}
				}
			}

			// Prefer start_date_time (same as Visit / KRA reports) for display date.
			$visitDateSource = $this->isValidDateTime($row['start_date_time'])
				? $row['start_date_time']
				: $row['created_date'];
			$visitDate = ($visitDateSource != "" && strtotime($visitDateSource) !== false)
				? date("Y-m-d", strtotime($visitDateSource))
				: "";

			$customerName = "";
			if ((int) $row['customer_id'] > 0) {
				$customerName = ($row['customer_company'] != "") ? $row['customer_company'] : $row['customer_person'];
			} else if ((int) $row['inquiry_id'] > 0) {
				$customerName = ($row['inquiry_company'] != "") ? $row['inquiry_company'] : $row['inquiry_person'];
			} else {
				$customerName = ($row['firm_name'] != "") ? $row['firm_name'] : $row['client_name'];
			}
			if ($customerName == "") {
				$customerName = "-";
			}

			$custMobile = "";
			if (isset($row['customer_phone']) && trim((string) $row['customer_phone']) != "") {
				$custMobile = trim($row['customer_phone']);
			} else if (isset($row['customer_mobile_no1']) && trim((string) $row['customer_mobile_no1']) != "") {
				$custMobile = trim($row['customer_mobile_no1']);
			} else if (isset($row['inquiry_mobile']) && trim((string) $row['inquiry_mobile']) != "") {
				$custMobile = trim($row['inquiry_mobile']);
			}

			$durationMinutes = $this->durationMinutes($row['start_date_time'], $row['stop_date_time']);

			$visitId = (int) $row['id'];
			$visitRows[] = array(
				"id" => $visitId,
				"visit_date" => $visitDate,
				"sales_person" => $row['sales_person_name'],
				"customer_name" => $customerName,
				"customer_code" => $row['customer_code'],
				"customer_person" => isset($row['customer_person']) ? $row['customer_person'] : "",
				"customer_turnover" => isset($row['customer_turnover']) ? $row['customer_turnover'] : "",
				"customer_turnover_year" => isset($row['customer_turnover_year']) ? $row['customer_turnover_year'] : "",
				"customer_gst" => isset($row['customer_gst']) ? $row['customer_gst'] : "",
				"customer_address" => isset($row['customer_address']) ? $row['customer_address'] : "",
				"customer_city" => isset($row['customer_city']) ? $row['customer_city'] : "",
				"customer_mobile" => $custMobile,
				"inquiry_mobile" => isset($row['inquiry_mobile']) ? $row['inquiry_mobile'] : "",
				"customer_type" => ((int) $row['customer_id'] > 0) ? "Existing Customer" : (((int) $row['inquiry_id'] > 0) ? "Inquiry" : "New Customer"),
				"remark_code" => $parent,
				"reason_code" => $child,
				"remark_label" => isset($this->remarkLabels[$parent]) ? $this->remarkLabels[$parent] : "",
				"reason_label" => isset($this->reasonLabels[$child]) ? $this->reasonLabels[$child] : "",
				"stop_remark" => $row['stop_remark'],
				"note" => isset($row['note']) ? $row['note'] : "",
				"meeting_purpose" => $row['remark'],
				"duration_minutes" => $durationMinutes,
				"user_id" => (int) $row['user_id'],
				"is_completed" => $this->isValidDateTime($row['stop_date_time']) ? 1 : 0,
				"consultant_form" => null,
				"high_rate_form" => null,
				"high_rate_items" => array(),
				"has_consultant_form" => 0,
				"has_high_rate_form" => 0,
				"approved_expense" => 0,
				"total_kilometer" => 0,
				"expense_per_km" => 0,
			);

			if ($durationMinutes !== null) {
				$data['total_duration_minutes'] += (int) $durationMinutes;
			}

			if ($parent != "" && isset($data['summary']['parents'][$parent])) {
				$data['summary']['parents'][$parent]++;
			} else if ($parent == "") {
				$data['summary']['unknown']++;
			}
			if ($child != "" && isset($data['summary']['children'][$child])) {
				$data['summary']['children'][$child]++;
			}
		}

		$this->attachExpenseMetrics($data, $visitRows);
		$data['visits'] = $visitRows;
		$data['total_visits'] = count($visitRows);
		return $data;
	}

	private function attachExpenseMetrics(&$data, &$visitRows)
	{
		if (empty($visitRows)) {
			return;
		}
		$employeeIds = array();
		$dayKeysUsed = array();
		foreach ($visitRows as $row) {
			if (!empty($row['user_id'])) {
				$employeeIds[(int) $row['user_id']] = (int) $row['user_id'];
			}
		}
		if (empty($employeeIds)) {
			return;
		}
		$expenseMap = $this->loadExpenseMetricMap(array_values($employeeIds), $data['range']['from'], $data['range']['to']);
		foreach ($visitRows as $idx => $row) {
			$key = ((int) $row['user_id']) . '|' . $row['visit_date'];
			if (isset($expenseMap[$key])) {
				$visitRows[$idx]['approved_expense'] = (float) $expenseMap[$key]['approved_expense'];
				$visitRows[$idx]['total_kilometer'] = (float) $expenseMap[$key]['total_kilometer'];
				$visitRows[$idx]['expense_per_km'] = (float) $expenseMap[$key]['expense_per_km'];
				$dayKeysUsed[$key] = $expenseMap[$key];
			}
		}
		foreach ($dayKeysUsed as $metric) {
			$data['total_approved_expense'] += (float) $metric['approved_expense'];
			$data['total_kilometer'] += (float) $metric['total_kilometer'];
		}
		$data['expense_per_km'] = ($data['total_kilometer'] > 0) ? ($data['total_approved_expense'] / $data['total_kilometer']) : 0;
	}

	private function loadExpenseMetricMap($employeeIds, $from, $to)
	{
		$map = array();
		if (empty($employeeIds)) {
			return $map;
		}
		$sql = "SELECT sales_executive_id AS employee_id,
				DATE(expense_date) AS expense_day,
				SUM(pass_expense_amount) AS approved_expense,
				SUM(total_kilometer) AS total_kilometer
			FROM expense
			WHERE isDelete=0 AND expense_status=1
				AND sales_executive_id IN (" . implode(",", $employeeIds) . ")
				AND DATE(expense_date) BETWEEN '" . $from . "' AND '" . $to . "'
			GROUP BY sales_executive_id, DATE(expense_date)";
		$result = $this->safeQuery($sql);
		if (!$result) {
			return $map;
		}
		while ($row = mysqli_fetch_assoc($result)) {
			$key = (int) $row['employee_id'] . '|' . $row['expense_day'];
			$expense = (float) $row['approved_expense'];
			$km = (float) $row['total_kilometer'];
			$map[$key] = array(
				'approved_expense' => $expense,
				'total_kilometer' => $km,
				'expense_per_km' => ($km > 0) ? ($expense / $km) : 0,
			);
		}
		return $map;
	}

	/**
	 * Attach consultant / high-rate forms for a subset of visits (e.g. current page).
	 */
	public function attachFormsToVisits($visits)
	{
		if (empty($visits) || !is_array($visits)) {
			return $visits;
		}
		$visitIds = array();
		foreach ($visits as $visit) {
			$id = isset($visit['id']) ? (int) $visit['id'] : 0;
			if ($id > 0) {
				$visitIds[] = $id;
			}
		}
		$formsByVisit = $this->loadVisitFormsByVisitIds($visitIds);
		foreach ($visits as $idx => $visit) {
			$vid = isset($visit['id']) ? (int) $visit['id'] : 0;
			$visits[$idx]['consultant_form'] = null;
			$visits[$idx]['high_rate_form'] = null;
			$visits[$idx]['high_rate_items'] = array();
			$visits[$idx]['has_consultant_form'] = 0;
			$visits[$idx]['has_high_rate_form'] = 0;
			if ($vid > 0 && isset($formsByVisit[$vid])) {
				$visits[$idx]['consultant_form'] = $formsByVisit[$vid]['consultant_form'];
				$visits[$idx]['high_rate_form'] = $formsByVisit[$vid]['high_rate_form'];
				$visits[$idx]['high_rate_items'] = $formsByVisit[$vid]['high_rate_items'];
				$visits[$idx]['has_consultant_form'] = !empty($formsByVisit[$vid]['consultant_form']) ? 1 : 0;
				$visits[$idx]['has_high_rate_form'] = !empty($formsByVisit[$vid]['high_rate_form']) ? 1 : 0;
			}
		}
		return $visits;
	}

	private function loadVisitFormsByVisitIds($visitIds)
	{
		$out = array();
		if (empty($visitIds)) {
			return $out;
		}
		$ids = array();
		foreach ($visitIds as $id) {
			$id = (int) $id;
			if ($id > 0) {
				$ids[$id] = $id;
				$out[$id] = array(
					"consultant_form" => null,
					"high_rate_form" => null,
					"high_rate_items" => array(),
				);
			}
		}
		if (empty($ids)) {
			return $out;
		}
		$idList = implode(",", $ids);

		$cfRes = $this->safeQuery("SELECT * FROM visit_consultant_form WHERE isDelete=0 AND visit_id IN (" . $idList . ") ORDER BY id DESC");
		if ($cfRes) {
			while ($cf = mysqli_fetch_assoc($cfRes)) {
				$vid = (int) $cf['visit_id'];
				if (isset($out[$vid]) && $out[$vid]['consultant_form'] === null) {
					$out[$vid]['consultant_form'] = $cf;
				}
			}
		}

		$hrRes = $this->safeQuery("SELECT * FROM visit_high_rate_form WHERE isDelete=0 AND visit_id IN (" . $idList . ") ORDER BY id DESC");
		$hrFormIds = array();
		if ($hrRes) {
			while ($hr = mysqli_fetch_assoc($hrRes)) {
				$vid = (int) $hr['visit_id'];
				if (isset($out[$vid]) && $out[$vid]['high_rate_form'] === null) {
					$out[$vid]['high_rate_form'] = $hr;
					$hrFormIds[(int) $hr['id']] = $vid;
				}
			}
		}

		if (!empty($hrFormIds)) {
			$itemRes = $this->safeQuery(
				"SELECT * FROM visit_high_rate_form_item
				 WHERE isDelete=0 AND high_rate_form_id IN (" . implode(",", array_keys($hrFormIds)) . ")
				 ORDER BY sort_order ASC, id ASC"
			);
			if ($itemRes) {
				while ($it = mysqli_fetch_assoc($itemRes)) {
					if ($it['given_rate'] == "" && $it['qty'] == "" && $it['customer_rate'] == "" && empty($it['remark'])) {
						continue;
					}
					$fid = (int) $it['high_rate_form_id'];
					if (!isset($hrFormIds[$fid])) {
						continue;
					}
					$vid = $hrFormIds[$fid];
					$out[$vid]['high_rate_items'][] = $it;
				}
			}
		}

		return $out;
	}

	private function emptySummary()
	{
		$parents = array();
		$children = array();
		foreach ($this->hierarchy as $parent => $kids) {
			$parents[$parent] = 0;
			foreach ($kids as $kid) {
				$children[$kid] = 0;
			}
		}
		return array(
			"parents" => $parents,
			"children" => $children,
			"unknown" => 0,
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

	private $lastQueryError = "";

	private function safeQuery($sql)
	{
		$this->lastQueryError = "";
		$result = mysqli_query($this->db->myconn, $sql);
		if ($result === false) {
			$error = mysqli_error($this->db->myconn);
			$this->lastQueryError = $error;
			/* If a new column is missing, retry without it so old data still shows */
			if (strpos($error, "Unknown column 'v.note'") !== false || strpos($error, "Unknown column `v`.`note`") !== false || strpos($error, "Unknown column 'note'") !== false) {
				$sqlFallback = preg_replace('/\s*v\.note\s*,?\s*/i', ' ', $sql);
				$result = @mysqli_query($this->db->myconn, $sqlFallback);
				if ($result === false) {
					$this->lastQueryError = mysqli_error($this->db->myconn);
				} else {
					$this->lastQueryError = "";
				}
			} else if (strpos($error, "Unknown column 'e.mobile_no1'") !== false || strpos($error, "Unknown column 'e.turnover_year'") !== false) {
				$sqlFallback = str_replace('e.turnover_year AS customer_turnover_year,', "'' AS customer_turnover_year,", $sql);
				$sqlFallback = str_replace('e.mobile_no1 AS customer_mobile_no1,', "'' AS customer_mobile_no1,", $sqlFallback);
				$result = @mysqli_query($this->db->myconn, $sqlFallback);
				if ($result === false) {
					$this->lastQueryError = mysqli_error($this->db->myconn);
				} else {
					$this->lastQueryError = "";
				}
			} else if (strpos($error, "Unknown column 'v.remark_code'") !== false || strpos($error, "Unknown column 'remark_code'") !== false) {
				/* remark_code column missing — use empty string placeholders */
				$sqlFallback = str_replace('v.remark_code,', "'' AS remark_code,", $sql);
				$sqlFallback = str_replace('v.reason_code,', "'' AS reason_code,", $sqlFallback);
				$sqlFallback = str_replace('v.note,', '', $sqlFallback);
				$result = @mysqli_query($this->db->myconn, $sqlFallback);
				if ($result === false) {
					$this->lastQueryError = mysqli_error($this->db->myconn);
				} else {
					$this->lastQueryError = "";
				}
			}
		}
		return $result;
	}
}
