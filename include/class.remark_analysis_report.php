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
		$employees = $this->getAccessibleEmployees($employeeIds, $rights);
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
			"employees" => $employees,
		);

		if (empty($employees)) {
			return $data;
		}

		$employeeIdList = implode(",", array_keys($employees));
		$whereExtra = "";
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
				noi.company_name AS inquiry_company,
				noi.person_name AS inquiry_person
			FROM visit v
			LEFT JOIN sales_executive se ON se.id = v.user_id
			LEFT JOIN executive e ON e.id = v.customer_id
			LEFT JOIN no_order_inquiry noi ON noi.id = v.inquiry_id
			WHERE v.isDelete = 0
				AND v.user_id IN (" . $employeeIdList . ")
				AND DATE(CASE
					WHEN v.start_date_time IS NOT NULL AND v.start_date_time != '' AND v.start_date_time != '0000-00-00 00:00:00'
					THEN v.start_date_time ELSE v.created_date END)
				BETWEEN '" . $range['from'] . "' AND '" . $range['to'] . "'
				" . $whereExtra . "
			ORDER BY v.start_date_time DESC, v.id DESC";

		$result = $this->safeQuery($sql);
		if (!$result) {
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

			$visitDateSource = $this->isValidDateTime($row['start_date_time']) ? $row['start_date_time'] : $row['created_date'];
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

			$visitId = (int) $row['id'];
			$visitRows[] = array(
				"id" => $visitId,
				"visit_date" => $visitDate,
				"sales_person" => $row['sales_person_name'],
				"customer_name" => $customerName,
				"customer_code" => $row['customer_code'],
				"remark_code" => $parent,
				"reason_code" => $child,
				"remark_label" => isset($this->remarkLabels[$parent]) ? $this->remarkLabels[$parent] : "",
				"reason_label" => isset($this->reasonLabels[$child]) ? $this->reasonLabels[$child] : "",
				"stop_remark" => $row['stop_remark'],
				"meeting_purpose" => $row['remark'],
				"is_completed" => $this->isValidDateTime($row['stop_date_time']) ? 1 : 0,
				"consultant_form" => null,
				"high_rate_form" => null,
				"high_rate_items" => array(),
				"has_consultant_form" => 0,
				"has_high_rate_form" => 0,
			);

			if ($parent != "" && isset($data['summary']['parents'][$parent])) {
				$data['summary']['parents'][$parent]++;
			} else if ($parent == "") {
				$data['summary']['unknown']++;
			}
			if ($child != "" && isset($data['summary']['children'][$child])) {
				$data['summary']['children'][$child]++;
			}
		}

		$data['visits'] = $visitRows;
		$data['total_visits'] = count($visitRows);
		return $data;
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

	private function safeQuery($sql)
	{
		return @mysqli_query($this->db->myconn, $sql);
	}
}
