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
		$requestedIds = $this->normalizeIds($requestedIds);
		$data = array(
			"range" => $range,
			"remark_labels" => $this->remarkLabels,
			"reason_labels" => $this->reasonLabels,
			"employees" => array(),
			"require_employee" => empty($requestedIds),
		);
		// Do not load all employees/customers when none selected (heavy page load).
		if (empty($requestedIds)) {
			return $data;
		}

		$employees = $this->getAccessibleEmployees($requestedIds, $rights);
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
				"total_kilometer" => 0,
				"km_expense_amount" => 0,
				"expense_per_km" => 0,
				"salary" => null,
				"total_sales" => 0,
				"total_visits" => 0,
				"completed_visits" => 0,
				"open_visits" => 0,
				"kra_assigned" => 0,
				"total_quotations" => 0,
				"total_quotations_count" => 0,
				"approved_pi" => 0,
				"approved_pi_count" => 0,
				"total_duration_minutes" => 0,
			);
			$data['employees'][$id] = $employee;
		}

		$employeeIds = array_keys($employees);
		$this->loadAssignedCustomers($data, $employeeIds);
		foreach ($data['employees'] as $employeeId => $employee) {
			$data['employees'][$employeeId]['kpi']['kra_assigned'] = count($employee['accounts']);
		}
		$this->loadVisits($data, $employeeIds);
		foreach ($data['employees'] as $employeeId => $employee) {
			uasort($data['employees'][$employeeId]['accounts'], array($this, "sortAccounts"));
		}
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

		$visitRows = array();
		$visitIds = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$visitId = (int) $row['id'];
			$visitIds[] = $visitId;
			$row['consultant_form'] = null;
			$row['high_rate_form'] = null;
			$row['high_rate_items'] = array();
			$visitRows[] = $row;
		}

		$formsByVisit = $this->loadVisitFormsByVisitIds($visitIds);
		foreach ($visitRows as $idx => $row) {
			$vid = (int) $row['id'];
			if (isset($formsByVisit[$vid])) {
				$visitRows[$idx]['consultant_form'] = $formsByVisit[$vid]['consultant_form'];
				$visitRows[$idx]['high_rate_form'] = $formsByVisit[$vid]['high_rate_form'];
				$visitRows[$idx]['high_rate_items'] = $formsByVisit[$vid]['high_rate_items'];
			}
		}

		foreach ($visitRows as $row) {
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
				$account['total_duration_minutes'] = 0;
				$data['employees'][$employeeId]['accounts'][$key] = $account;
			}
			if (!isset($data['employees'][$employeeId]['accounts'][$key]['dates'][$visitDate])) {
				$data['employees'][$employeeId]['accounts'][$key]['dates'][$visitDate] = array();
			}
			$data['employees'][$employeeId]['accounts'][$key]['dates'][$visitDate][] = $row;
			$data['employees'][$employeeId]['accounts'][$key]['total_visits']++;
			if ($row['duration_minutes'] !== null) {
				$data['employees'][$employeeId]['accounts'][$key]['total_duration_minutes'] += (int) $row['duration_minutes'];
				$data['employees'][$employeeId]['kpi']['total_duration_minutes'] += (int) $row['duration_minutes'];
			}

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
	}

	/**
	 * Pre-load ALL customer types assigned to selected sales employee(s) via executive.seid
	 * (Dealer / Distributor / Outlet / Project / etc.) even when no visit exists yet.
	 */
	private function loadAssignedCustomers(&$data, $employeeIds)
	{
		if (empty($employeeIds)) {
			return;
		}
		$executiveColumns = $this->db->rp_getTableColumnNames("executive");
		$pincodeColumn = in_array("pincode", $executiveColumns) ? "pincode" : (in_array("zip", $executiveColumns) ? "zip" : "");
		$pincodeSelect = ($pincodeColumn != "") ? "e." . $pincodeColumn . " AS account_pincode" : "'' AS account_pincode";
		$hasCustomerType = in_array("type_of_executive", $executiveColumns);

		$typeSelect = $hasCustomerType
			? ", e.type_of_executive, ct.name AS customer_type_name"
			: ", '' AS type_of_executive, '' AS customer_type_name";
		$typeJoin = $hasCustomerType
			? " LEFT JOIN customer_type ct ON ct.id=e.type_of_executive AND ct.isDelete=0 "
			: "";

		$sql = "SELECT
				e.id,
				e.seid,
				e.client_code,
				e.company_name,
				e.cname,
				e.turnover,
				e.gst,
				e.address,
				e.main_city,
				" . $pincodeSelect . "
				" . $typeSelect . "
			FROM executive e
			" . $typeJoin . "
			WHERE e.isDelete=0
				AND e.seid IN (" . implode(",", $employeeIds) . ")
				AND e.seid!=0
				AND e.seid IS NOT NULL
				AND e.seid!=''
				AND (
					(e.company_name IS NOT NULL AND e.company_name!='')
					OR (e.cname IS NOT NULL AND e.cname!='')
				)
			ORDER BY e.company_name ASC, e.cname ASC";

		$result = $this->safeQuery($sql);
		if (!$result) {
			return;
		}

		while ($row = mysqli_fetch_assoc($result)) {
			$employeeId = (int) $row['seid'];
			if (!isset($data['employees'][$employeeId])) {
				continue;
			}
			$key = "C" . (int) $row['id'];
			if (isset($data['employees'][$employeeId]['accounts'][$key])) {
				continue;
			}
			$company = trim((string) $row['company_name']);
			$person = trim((string) $row['cname']);
			$typeName = trim((string) $row['customer_type_name']);
			if ($typeName == "") {
				$typeName = "Customer";
			}
			$data['employees'][$employeeId]['accounts'][$key] = array(
				"key" => $key,
				"type" => $typeName,
				"code" => $row['client_code'],
				"company" => ($company != "") ? $company : $person,
				"person" => $person,
				"turnover" => $row['turnover'],
				"gst" => $row['gst'],
				"address" => $row['address'],
				"city" => $row['main_city'],
				"pincode" => isset($row['account_pincode']) ? $row['account_pincode'] : "",
				"dates" => array(),
				"total_visits" => 0,
				"total_duration_minutes" => 0,
			);
		}
	}

	/**
	 * Batch-load Need Approval (consultant) + High Rate forms for visit IDs.
	 */
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
		// Total KM By Bike — only kilometer expenses with Bike subcategory (Expense Master)
		$this->applyGroupedKpi(
			$data,
			"SELECT e.sales_executive_id AS employee_id,SUM(CAST(e.total_kilometer AS DECIMAL(14,2))) AS metric
			 FROM expense e
			 LEFT JOIN expence_sub_category s ON s.id=e.subcategory_id AND s.isDelete=0
			 WHERE e.isDelete=0 AND e.expense_status=1 AND e.expense_type=2
			 AND e.sales_executive_id IN (" . $ids . ")
			 AND DATE(e.expense_date) BETWEEN '" . $from . "' AND '" . $to . "'
			 AND (
				LOWER(IFNULL(s.slug,''))='bike'
				OR LOWER(IFNULL(s.name,''))='bike'
			 )
			 GROUP BY e.sales_executive_id",
			"total_kilometer"
		);
		// KM expense amount kept internally (popup / calc); not shown as KPI box
		$this->applyGroupedKpi(
			$data,
			"SELECT e.sales_executive_id AS employee_id,
				SUM(
					CAST(IFNULL(e.total_kilometer,0) AS DECIMAL(14,2))
					* CAST(
						IFNULL(
							NULLIF(s.fix_amount,0),
							IFNULL(e.fix_amount,0)
						) AS DECIMAL(14,4)
					)
				) AS metric
			 FROM expense e
			 LEFT JOIN expence_sub_category s ON s.id=e.subcategory_id AND s.isDelete=0
			 WHERE e.isDelete=0 AND e.expense_status=1 AND e.expense_type=2
			 AND e.sales_executive_id IN (" . $ids . ")
			 AND DATE(e.expense_date) BETWEEN '" . $from . "' AND '" . $to . "'
			 AND (
				LOWER(IFNULL(s.slug,''))='bike'
				OR LOWER(IFNULL(s.name,''))='bike'
			 )
			 GROUP BY e.sales_executive_id",
			"km_expense_amount"
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
			"total_quotations_count"
		);
		$this->applyGroupedKpi(
			$data,
			"SELECT sales_id AS employee_id,SUM(grand_total) AS metric
			 FROM quotation_detail
			 WHERE isDelete=0 AND sales_id IN (" . $ids . ")
			 AND DATE(quotation_date) BETWEEN '" . $from . "' AND '" . $to . "'
			 GROUP BY sales_id",
			"total_quotations"
		);
		// Total PI Approved = Approved Sales Orders (status=1)
		// includes: Quotation transferred to Order + Direct Sales Order created
		$this->applyGroupedKpi(
			$data,
			"SELECT sales_id AS employee_id,COUNT(*) AS metric
			 FROM orders
			 WHERE isDelete=0 AND status=1 AND sales_id IN (" . $ids . ")
			 AND DATE(order_date) BETWEEN '" . $from . "' AND '" . $to . "'
			 GROUP BY sales_id",
			"approved_pi_count"
		);
		$this->applyGroupedKpi(
			$data,
			"SELECT sales_id AS employee_id,SUM(grand_total) AS metric
			 FROM orders
			 WHERE isDelete=0 AND status=1 AND sales_id IN (" . $ids . ")
			 AND DATE(order_date) BETWEEN '" . $from . "' AND '" . $to . "'
			 GROUP BY sales_id",
			"approved_pi"
		);
		foreach ($data['employees'] as $employeeId => $employee) {
			$totalKm = isset($employee['kpi']['total_kilometer']) ? (float) $employee['kpi']['total_kilometer'] : 0;
			$kmExpense = isset($employee['kpi']['km_expense_amount']) ? (float) $employee['kpi']['km_expense_amount'] : 0;
			$data['employees'][$employeeId]['kpi']['expense_per_km'] = ($totalKm > 0) ? ($kmExpense / $totalKm) : 0;
		}
	}

	/**
	 * Approved expense breakdown by expense category (Hotel, Travelling, Food, etc.)
	 */
	public function getApprovedExpenseBreakdown($employeeId, $fromDate, $toDate, $rights)
	{
		$employeeId = (int) $employeeId;
		$range = $this->normalizeDateRange($fromDate, $toDate);
		$accessible = $this->getAccessibleEmployees(array($employeeId), $rights);
		if ($employeeId <= 0 || empty($accessible) || !isset($accessible[$employeeId])) {
			return array(
				"ack" => 0,
				"ack_msg" => "Employee not accessible.",
				"employee" => null,
				"range" => $range,
				"rows" => array(),
				"total_amount" => 0,
				"total_count" => 0,
			);
		}

		$employee = $accessible[$employeeId];
		$from = $range['from'];
		$to = $range['to'];
		$rows = array();
		$totalAmount = 0;
		$totalCount = 0;

		$sql = "SELECT
				e.category_id,
				e.subcategory_id,
				e.expense_type,
				COALESCE(NULLIF(TRIM(c.name), ''), 'Other / Uncategorized') AS category_name,
				COALESCE(NULLIF(TRIM(s.name), ''), '-') AS subcategory_name,
				COUNT(*) AS expense_count,
				SUM(e.pass_expense_amount) AS approved_amount,
				SUM(CAST(IFNULL(e.total_kilometer,0) AS DECIMAL(14,2))) AS total_km,
				SUM(
					CAST(IFNULL(e.total_kilometer,0) AS DECIMAL(14,2))
					* CAST(IFNULL(NULLIF(s.fix_amount,0), IFNULL(e.fix_amount,0)) AS DECIMAL(14,4))
				) AS km_calc_amount,
				MAX(IFNULL(NULLIF(s.fix_amount,0), IFNULL(e.fix_amount,0))) AS master_rate
			FROM expense e
			LEFT JOIN expence_category c ON c.id=e.category_id AND c.isDelete=0
			LEFT JOIN expence_sub_category s ON s.id=e.subcategory_id AND s.isDelete=0
			WHERE e.isDelete=0
				AND e.expense_status=1
				AND e.sales_executive_id='" . $employeeId . "'
				AND DATE(e.expense_date) BETWEEN '" . $from . "' AND '" . $to . "'
			GROUP BY e.category_id, e.subcategory_id, e.expense_type, c.name, s.name
			ORDER BY c.name ASC, s.name ASC, approved_amount DESC";

		$result = $this->safeQuery($sql);
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$amount = (float) $row['approved_amount'];
				$count = (int) $row['expense_count'];
				$isKm = ((int) $row['expense_type'] === 2);
				$totalKm = (float) $row['total_km'];
				$kmCalc = (float) $row['km_calc_amount'];
				$rate = (float) $row['master_rate'];
				$totalAmount += $amount;
				$totalCount += $count;
				$rows[] = array(
					"category_id" => (int) $row['category_id'],
					"subcategory_id" => (int) $row['subcategory_id'],
					"category_name" => $row['category_name'],
					"subcategory_name" => $row['subcategory_name'],
					"expense_type" => (int) $row['expense_type'],
					"is_km" => $isKm ? 1 : 0,
					"expense_count" => $count,
					"approved_amount" => $amount,
					"total_km" => $totalKm,
					"master_rate" => $rate,
					"km_calc_amount" => $kmCalc,
				);
			}
		}

		return array(
			"ack" => 1,
			"ack_msg" => "OK",
			"employee" => $employee,
			"range" => $range,
			"rows" => $rows,
			"total_amount" => $totalAmount,
			"total_count" => $totalCount,
		);
	}

	/**
	 * Detailed rows for KPI popup (approved expense, sales, visits, quotations, PI, etc.)
	 */
	public function getKpiDetail($employeeId, $fromDate, $toDate, $kpiType, $rights)
	{
		$employeeId = (int) $employeeId;
		$kpiType = trim((string) $kpiType);
		$range = $this->normalizeDateRange($fromDate, $toDate);
		$accessible = $this->getAccessibleEmployees(array($employeeId), $rights);
		$empty = array(
			"ack" => 0,
			"ack_msg" => "Employee not accessible.",
			"title" => "KPI Detail",
			"employee" => null,
			"range" => $range,
			"columns" => array(),
			"rows" => array(),
			"footer_note" => "",
			"total_label" => "",
		);
		if ($employeeId <= 0 || empty($accessible) || !isset($accessible[$employeeId])) {
			return $empty;
		}

		$employee = $accessible[$employeeId];
		$from = $range['from'];
		$to = $range['to'];
		$base = array(
			"ack" => 1,
			"ack_msg" => "OK",
			"employee" => $employee,
			"range" => $range,
			"footer_note" => "",
			"total_label" => "",
		);

		if ($kpiType === "approved_expense" || $kpiType === "expense_salary") {
			$break = $this->getApprovedExpenseBreakdown($employeeId, $fromDate, $toDate, $rights);
			$rows = array();
			foreach ($break['rows'] as $r) {
				$rows[] = array(
					$r['category_name'],
					isset($r['subcategory_name']) ? $r['subcategory_name'] : "-",
					(int) $r['expense_count'],
					!empty($r['is_km']) ? $r['total_km'] : "-",
					!empty($r['is_km']) ? number_format((float) $r['master_rate'], 2) : "-",
					!empty($r['is_km']) ? number_format((float) $r['km_calc_amount'], 2) : "-",
					number_format((float) $r['approved_amount'], 2),
				);
			}
			$base['title'] = ($kpiType === "expense_salary") ? "Expense + Salary Detail" : "Approved Expense — Category Wise";
			$base['columns'] = array("Category", "Sub Category", "Count", "KM", "Rate/KM", "KM Calc", "Approved Amount");
			$base['rows'] = $rows;
			$base['total_label'] = "Total Approved: " . number_format((float) $break['total_amount'], 2) . " | Count: " . (int) $break['total_count'];
			$base['footer_note'] = ($kpiType === "expense_salary")
				? "Salary mapping is not configured (N/A). Expense above is approved expense only."
				: "KM Calc = KM × Expense Master fix_amount.";
			return $base;
		}

		if ($kpiType === "salary") {
			$base['title'] = "Salary";
			$base['columns'] = array("Info");
			$base['rows'] = array(array("Salary remains N/A until employee to sales person salary mapping is configured."));
			$base['footer_note'] = "";
			return $base;
		}

		if ($kpiType === "km_bike") {
			$sql = "SELECT e.expense_date, e.start_kilometer, e.end_kilometer, e.total_kilometer,
					e.fix_amount, e.total, e.pass_expense_amount, e.pass_remark,
					COALESCE(s.name,'Bike') AS sub_name, IFNULL(s.fix_amount, e.fix_amount) AS master_rate
				FROM expense e
				LEFT JOIN expence_sub_category s ON s.id=e.subcategory_id AND s.isDelete=0
				WHERE e.isDelete=0 AND e.expense_status=1 AND e.expense_type=2
					AND e.sales_executive_id='" . $employeeId . "'
					AND DATE(e.expense_date) BETWEEN '" . $from . "' AND '" . $to . "'
					AND (LOWER(IFNULL(s.slug,''))='bike' OR LOWER(IFNULL(s.name,''))='bike')
				ORDER BY e.expense_date, e.id";
			$rows = array();
			$totalKm = 0;
			$totalAmt = 0;
			$res = $this->safeQuery($sql);
			if ($res) {
				while ($r = mysqli_fetch_assoc($res)) {
					$km = (float) $r['total_kilometer'];
					$rate = (float) $r['master_rate'];
					$calc = $km * $rate;
					$totalKm += $km;
					$totalAmt += (float) $r['pass_expense_amount'];
					$rows[] = array(
						date("d/m/Y", strtotime($r['expense_date'])),
						$r['sub_name'],
						$r['start_kilometer'],
						$r['end_kilometer'],
						number_format($km, 2),
						number_format($rate, 2),
						number_format($calc, 2),
						number_format((float) $r['pass_expense_amount'], 2),
					);
				}
			}
			$base['title'] = "Total KM By Bike — Detail";
			$base['columns'] = array("Date", "Vehicle", "Start KM", "End KM", "Total KM", "Rate", "KM Calc", "Approved Amt");
			$base['rows'] = $rows;
			$base['total_label'] = "Total KM: " . number_format($totalKm, 2) . " | Approved: " . number_format($totalAmt, 2);
			return $base;
		}

		if ($kpiType === "total_sales") {
			$sql = "SELECT id, order_no, order_date, company_name, customer_name, status, grand_total,
					IFNULL(quotation_id,0) AS quotation_id
				FROM orders
				WHERE isDelete=0 AND status NOT IN (-1,-2,3) AND sales_id='" . $employeeId . "'
					AND DATE(order_date) BETWEEN '" . $from . "' AND '" . $to . "'
				ORDER BY order_date DESC, id DESC";
			$rows = array();
			$total = 0;
			$res = $this->safeQuery($sql);
			if ($res) {
				while ($r = mysqli_fetch_assoc($res)) {
					$total += (float) $r['grand_total'];
					$src = ((int) $r['quotation_id'] > 0) ? "From Quotation" : "Direct Order";
					$rows[] = array(
						$r['order_no'],
						date("d/m/Y", strtotime($r['order_date'])),
						$r['company_name'],
						$r['customer_name'],
						$src,
						$this->orderStatusLabel($r['status']),
						number_format((float) $r['grand_total'], 2),
					);
				}
			}
			$base['title'] = "Total Sales — Order Detail";
			$base['columns'] = array("Order No", "Date", "Company", "Customer", "Source", "Status", "Amount");
			$base['rows'] = $rows;
			$base['total_label'] = "Orders: " . count($rows) . " | Amount: " . number_format($total, 2);
			return $base;
		}

		if ($kpiType === "pi_approved") {
			$adminTable = defined("CTABLE_ADMIN") ? CTABLE_ADMIN : "dealer_distributor_network";
			$sql = "SELECT o.id, o.order_no, o.order_date, o.company_name, o.customer_name, o.grand_total,
					IFNULL(o.quotation_id,0) AS quotation_id, IFNULL(o.approve_by_id,0) AS approve_by_id,
					IFNULL(a.name,'') AS approved_by_name
				FROM orders o
				LEFT JOIN " . $adminTable . " a ON a.id=o.approve_by_id AND a.isDelete=0
				WHERE o.isDelete=0 AND o.status=1 AND o.sales_id='" . $employeeId . "'
					AND DATE(o.order_date) BETWEEN '" . $from . "' AND '" . $to . "'
				ORDER BY o.order_date DESC, o.id DESC";
			$rows = array();
			$total = 0;
			$res = $this->safeQuery($sql);
			if ($res) {
				while ($r = mysqli_fetch_assoc($res)) {
					$total += (float) $r['grand_total'];
					$src = ((int) $r['quotation_id'] > 0) ? "Quotation → Order" : "Direct Sales Order";
					$approvedBy = trim((string) $r['approved_by_name']);
					if ($approvedBy === "") {
						$approvedBy = ((int) $r['approve_by_id'] > 0) ? ("User #" . (int) $r['approve_by_id']) : "-";
					}
					$rows[] = array(
						$r['order_no'],
						date("d/m/Y", strtotime($r['order_date'])),
						$r['company_name'],
						$r['customer_name'],
						$src,
						"Approved",
						$approvedBy,
						number_format((float) $r['grand_total'], 2),
					);
				}
			}
			$base['title'] = "Total PI Approved — Detail";
			$base['columns'] = array("PI / Order No", "Date", "Company", "Customer", "Type", "Status", "Approved By", "Amount");
			$base['rows'] = $rows;
			$base['total_label'] = "Approved PI/Orders: " . count($rows) . " | Amount: " . number_format($total, 2);
			$base['footer_note'] = "Includes Quotation transferred to Order and Direct Sales Order (status = Approved).";
			return $base;
		}

		if ($kpiType === "total_quotation") {
			$adminTable = defined("CTABLE_ADMIN") ? CTABLE_ADMIN : "dealer_distributor_network";
			$quoCols = $this->db->rp_getTableColumnNames("quotation_detail");
			$hasApproveBy = is_array($quoCols) && in_array("approve_by_id", $quoCols);
			if ($hasApproveBy) {
				$sql = "SELECT q.id, q.quotation_no, q.quotation_date, q.company_name, q.customer_name, q.status, q.grand_total,
						IFNULL(q.approve_by_id,0) AS approve_by_id, IFNULL(a.name,'') AS approved_by_name
					FROM quotation_detail q
					LEFT JOIN " . $adminTable . " a ON a.id=q.approve_by_id AND a.isDelete=0
					WHERE q.isDelete=0 AND q.sales_id='" . $employeeId . "'
						AND DATE(q.quotation_date) BETWEEN '" . $from . "' AND '" . $to . "'
					ORDER BY q.quotation_date DESC, q.id DESC";
			} else {
				$sql = "SELECT id, quotation_no, quotation_date, company_name, customer_name, status, grand_total,
						0 AS approve_by_id, '' AS approved_by_name
					FROM quotation_detail
					WHERE isDelete=0 AND sales_id='" . $employeeId . "'
						AND DATE(quotation_date) BETWEEN '" . $from . "' AND '" . $to . "'
					ORDER BY quotation_date DESC, id DESC";
			}
			$rows = array();
			$total = 0;
			$res = $this->safeQuery($sql);
			if ($res) {
				while ($r = mysqli_fetch_assoc($res)) {
					$total += (float) $r['grand_total'];
					$approvedBy = trim((string) $r['approved_by_name']);
					if ($approvedBy === "") {
						$approvedBy = ((int) $r['approve_by_id'] > 0) ? ("User #" . (int) $r['approve_by_id']) : "-";
					}
					$rows[] = array(
						$r['quotation_no'],
						date("d/m/Y", strtotime($r['quotation_date'])),
						$r['company_name'],
						$r['customer_name'],
						$this->quotationStatusLabel($r['status']),
						$approvedBy,
						number_format((float) $r['grand_total'], 2),
					);
				}
			}
			$base['title'] = "Total Quotation — Detail";
			$base['columns'] = array("Quotation No", "Date", "Company", "Customer", "Status", "Approved By", "Amount");
			$base['rows'] = $rows;
			$base['total_label'] = "Quotations: " . count($rows) . " | Amount: " . number_format($total, 2);
			return $base;
		}

		if ($kpiType === "kra_assigned") {
			$execCols = $this->db->rp_getTableColumnNames("executive");
			$hasCustomerType = in_array("type_of_executive", $execCols);
			$typeSelect = $hasCustomerType
				? ", COALESCE(ct.name,'Customer') AS customer_type"
				: ", 'Customer' AS customer_type";
			$typeJoin = $hasCustomerType
				? " LEFT JOIN customer_type ct ON ct.id=e.type_of_executive AND ct.isDelete=0 "
				: "";
			$sql = "SELECT e.client_code, e.company_name, e.cname, e.state, e.main_city, e.phone
					" . $typeSelect . "
				FROM executive e
				" . $typeJoin . "
				WHERE e.isDelete=0 AND e.seid='" . $employeeId . "'
					AND e.seid!=0 AND e.seid IS NOT NULL AND e.seid!=''
				ORDER BY e.company_name ASC, e.cname ASC
				LIMIT 500";
			$rows = array();
			$res = $this->safeQuery($sql);
			if ($res) {
				while ($r = mysqli_fetch_assoc($res)) {
					$rows[] = array(
						$r['client_code'],
						$r['company_name'],
						$r['cname'],
						$r['customer_type'],
						$r['state'],
						$r['main_city'],
						$r['phone'],
					);
				}
			}
			$totalAssigned = (int) $this->db->rp_getTotalRecord(
				"executive",
				"isDelete=0 AND seid='" . $employeeId . "' AND seid!=0 AND seid IS NOT NULL AND seid!=''",
				0
			);
			$base['title'] = "KRA Assigned Customers";
			$base['columns'] = array("Code", "Firm Name", "Person", "Type", "State", "City", "Phone");
			$base['rows'] = $rows;
			$base['total_label'] = "Assigned: " . $totalAssigned . (count($rows) < $totalAssigned ? " (showing first " . count($rows) . ")" : "");
			return $base;
		}

		if ($kpiType === "total_visit" || $kpiType === "total_duration" || $kpiType === "completed_open") {
			$sql = "SELECT v.id, v.start_date_time, v.stop_date_time, v.firm_name, v.client_name, v.remark, v.stop_remark,
					v.remark_code, v.reason_code, pm.name AS purpose_name,
					e.company_name, e.client_code
				FROM visit v
				LEFT JOIN executive e ON e.id=v.customer_id
				LEFT JOIN purpose_master pm ON pm.id=v.purpose_id
				WHERE v.isDelete=0 AND v.user_id='" . $employeeId . "'
					AND DATE(CASE
						WHEN v.start_date_time IS NOT NULL AND v.start_date_time!='' AND v.start_date_time!='0000-00-00 00:00:00'
						THEN v.start_date_time ELSE v.created_date END)
					BETWEEN '" . $from . "' AND '" . $to . "'
				ORDER BY v.start_date_time DESC, v.id DESC
				LIMIT 500";
			$rows = array();
			$completed = 0;
			$open = 0;
			$totalMins = 0;
			$res = $this->safeQuery($sql);
			if ($res) {
				while ($r = mysqli_fetch_assoc($res)) {
					$isDone = $this->isValidDateTime($r['stop_date_time']);
					if ($isDone) {
						$completed++;
					} else {
						$open++;
					}
					$mins = $this->durationMinutes($r['start_date_time'], $r['stop_date_time']);
					if ($mins !== null) {
						$totalMins += (int) $mins;
					}
					$account = trim((string) $r['company_name']);
					if ($account == "") {
						$account = trim((string) $r['firm_name']);
					}
					if ($account == "") {
						$account = trim((string) $r['client_name']);
					}
					$codes = $this->normalizeRemarkCodes($r);
					$code = ($codes['reason_code'] != "") ? $codes['reason_code'] : $codes['remark_code'];
					if ($code == "") {
						$code = $isDone ? "Done" : "Open";
					}
					$rows[] = array(
						(int) $r['id'],
						$this->isValidDateTime($r['start_date_time']) ? date("d/m/Y H:i", strtotime($r['start_date_time'])) : "-",
						$r['client_code'],
						$account,
						isset($r['purpose_name']) ? $r['purpose_name'] : "",
						$code,
						$isDone ? "Completed" : "Open",
						($mins === null) ? "-" : $this->formatDurationLabel($mins),
					);
				}
			}
			$titleMap = array(
				"total_visit" => "Total Visit — Detail",
				"total_duration" => "Total Duration — Visit Detail",
				"completed_open" => "Completed / Open Visits — Detail",
			);
			$base['title'] = isset($titleMap[$kpiType]) ? $titleMap[$kpiType] : "Visit Detail";
			$base['columns'] = array("Visit #", "Start", "Code", "Account", "Purpose", "Outcome", "Status", "Duration");
			$base['rows'] = $rows;
			$base['total_label'] = "Visits: " . count($rows) . " | Completed: " . $completed . " | Open: " . $open . " | Duration: " . $this->formatDurationLabel($totalMins);
			return $base;
		}

		$empty['ack'] = 0;
		$empty['ack_msg'] = "Unknown KPI type.";
		$empty['employee'] = $employee;
		return $empty;
	}

	private function orderStatusLabel($status)
	{
		$status = (int) $status;
		$map = array(
			-2 => "Disapproved",
			-1 => "Draft/Cart",
			0 => "Pending",
			1 => "Approved",
			3 => "Cancelled",
			4 => "Account Approved",
		);
		return isset($map[$status]) ? $map[$status] : ("Status " . $status);
	}

	private function quotationStatusLabel($status)
	{
		$status = (int) $status;
		$map = array(
			-2 => "Disapproved",
			-1 => "Draft",
			0 => "Pending",
			1 => "Approved",
			3 => "Cancelled",
			4 => "Converted to Order",
			5 => "Lost",
		);
		return isset($map[$status]) ? $map[$status] : ("Status " . $status);
	}

	private function formatDurationLabel($minutes)
	{
		$minutes = (int) $minutes;
		if ($minutes < 0) {
			return "-";
		}
		$hours = (int) floor($minutes / 60);
		$mins = $minutes % 60;
		if ($hours > 0) {
			return $hours . "h " . $mins . "m";
		}
		return $mins . " min";
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
