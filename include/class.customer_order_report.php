<?php
require_once(__DIR__ . "/class.employee_visit_kra_report.php");

/**
 * Customer Order Report — per customer final order count / amount for a period,
 * last final order date (lifetime) and "not ordered" detection.
 *
 * Final order = orders.status in FINAL_STATUSES (approved PI / order and later stages).
 * Pending (0), Draft (-1), Disapproved (-2) and Cancelled (3) are NOT counted.
 */
class CustomerOrderReport
{
	const FINAL_STATUSES = "1,2,4,5,6,7";
	const PAGE_SIZE = 50;
	const AREA_NONE = "__none__";

	private $db;
	private $conn;

	public function __construct($db)
	{
		$this->db = $db;
		$this->conn = $db->myconn;
	}

	public function getIntervals()
	{
		return array(
			"1" => "Last 1 Month",
			"2" => "Last 2 Months",
			"3" => "Last 3 Months",
			"6" => "Last 6 Months",
			"12" => "Last 12 Months",
			"custom" => "Custom",
		);
	}

	public function getStatusFilters()
	{
		return array(
			"all" => "All Customers",
			"ordered" => "Ordered In Period",
			"not_ordered" => "Not Ordered In Period",
			"old_only" => "Ordered Before, Not In Period",
			"never" => "Never Ordered",
		);
	}

	public function getSortOptions()
	{
		return array(
			"last_order_asc" => "Last Order (Oldest / Never First)",
			"last_order_desc" => "Last Order (Recent First)",
			"orders_desc" => "Period Orders (High to Low)",
			"amount_desc" => "Period Amount (High to Low)",
			"name_asc" => "Customer Name (A-Z)",
		);
	}

	public function getCustomerTypes()
	{
		$types = array();
		$res = $this->safeQuery("SELECT id,name FROM customer_type WHERE isDelete=0 ORDER BY display_order ASC, name ASC");
		if ($res) {
			while ($row = mysqli_fetch_assoc($res)) {
				$types[(int) $row['id']] = $row['name'];
			}
		}
		return $types;
	}

	public function getAccessibleEmployees($rights)
	{
		$kra = new EmployeeVisitKraReport($this->db);
		return $kra->getAccessibleEmployees(array(), $rights);
	}

	/**
	 * Admin (type 0) and all-data users see every customer, including unassigned ones.
	 */
	public function isRestrictedUser($rights)
	{
		$adminType = isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) ? (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] : 0;
		if ($adminType == 0) {
			return false;
		}
		return !empty($rights['personal_flag']) || !empty($rights['chain_vise_flag']);
	}

	public function normalizeFilters($req, $rights)
	{
		$intervals = $this->getIntervals();
		$interval = isset($req['interval']) ? (string) $req['interval'] : "6";
		if (!isset($intervals[$interval])) {
			$interval = "6";
		}

		$today = date("Y-m-d");
		if ($interval === "custom") {
			$from = $this->normalizeDate(isset($req['from_date']) ? $req['from_date'] : "");
			$to = $this->normalizeDate(isset($req['to_date']) ? $req['to_date'] : "");
			if ($to === "") {
				$to = $today;
			}
			if ($from === "") {
				$from = date("Y-m-d", strtotime($to . " -6 months +1 day"));
			}
			if ($from > $to) {
				$tmp = $from;
				$from = $to;
				$to = $tmp;
			}
		} else {
			$to = $today;
			$from = date("Y-m-d", strtotime($today . " -" . (int) $interval . " months +1 day"));
		}

		$statusFilters = $this->getStatusFilters();
		$status = isset($req['order_status']) ? (string) $req['order_status'] : "all";
		if (!isset($statusFilters[$status])) {
			$status = "all";
		}

		$sorts = $this->getSortOptions();
		$sort = isset($req['sort']) ? (string) $req['sort'] : "last_order_asc";
		if (!isset($sorts[$sort])) {
			$sort = "last_order_asc";
		}

		$accessible = $this->getAccessibleEmployees($rights);
		$employeeIds = array();
		if (isset($req['employee_ids']) && $req['employee_ids'] !== "") {
			$raw = is_array($req['employee_ids']) ? $req['employee_ids'] : explode(",", (string) $req['employee_ids']);
			foreach ($raw as $id) {
				$id = (int) $id;
				if ($id > 0 && isset($accessible[$id])) {
					$employeeIds[$id] = $id;
				}
			}
		}

		$typeId = isset($req['customer_type']) ? (int) $req['customer_type'] : 0;
		$page = isset($req['page']) ? max(1, (int) $req['page']) : 1;

		return array(
			"interval" => $interval,
			"from" => $from,
			"to" => $to,
			"order_status" => $status,
			"sort" => $sort,
			"employee_ids" => array_values($employeeIds),
			"accessible_ids" => array_keys($accessible),
			"restricted" => $this->isRestrictedUser($rights),
			"customer_type" => $typeId,
			"include_leads" => !empty($req['include_leads']) ? 1 : 0,
			"search" => isset($req['search']) ? trim((string) $req['search']) : "",
			"page" => $page,
		);
	}

	public function build($filters, $paginate = true)
	{
		$fromSql = $this->baseFromSql($filters);
		$where = $this->baseWhereSql($filters);

		$summary = array(
			"total" => 0,
			"ordered" => 0,
			"not_ordered" => 0,
			"old_only" => 0,
			"never" => 0,
			"orders" => 0,
			"amount" => 0,
		);
		$sumRes = $this->safeQuery(
			"SELECT COUNT(*) AS total,
				SUM(CASE WHEN IFNULL(p.period_orders,0)>0 THEN 1 ELSE 0 END) AS ordered,
				SUM(CASE WHEN IFNULL(p.period_orders,0)=0 THEN 1 ELSE 0 END) AS not_ordered,
				SUM(CASE WHEN IFNULL(p.period_orders,0)=0 AND l.customer_id IS NOT NULL THEN 1 ELSE 0 END) AS old_only,
				SUM(CASE WHEN l.customer_id IS NULL THEN 1 ELSE 0 END) AS never,
				SUM(IFNULL(p.period_orders,0)) AS orders,
				SUM(IFNULL(p.period_amount,0)) AS amount
			" . $fromSql . " WHERE " . $where
		);
		if ($sumRes && ($row = mysqli_fetch_assoc($sumRes))) {
			foreach ($summary as $key => $val) {
				$summary[$key] = ($key === "amount") ? (float) $row[$key] : (int) $row[$key];
			}
		}

		$listWhere = $where . $this->statusWhereSql($filters['order_status']);
		$totalRows = 0;
		$cntRes = $this->safeQuery("SELECT COUNT(*) AS c " . $fromSql . " WHERE " . $listWhere);
		if ($cntRes && ($row = mysqli_fetch_assoc($cntRes))) {
			$totalRows = (int) $row['c'];
		}

		$limitSql = "";
		$totalPages = 1;
		$page = 1;
		if ($paginate) {
			$totalPages = max(1, (int) ceil($totalRows / self::PAGE_SIZE));
			$page = min($filters['page'], $totalPages);
			$limitSql = " LIMIT " . (($page - 1) * self::PAGE_SIZE) . "," . self::PAGE_SIZE;
		}

		$rows = array();
		$res = $this->safeQuery(
			"SELECT e.id, e.client_code, e.company_name, e.cname, e.mobile_no1, e.phone,
				e.main_city, e.city, e.state, e.seid,
				IFNULL(se.name,'') AS sales_person,
				IFNULL(ct.name,'') AS customer_type_name,
				IFNULL(p.period_orders,0) AS period_orders,
				IFNULL(p.period_amount,0) AS period_amount,
				l.last_order_date,
				IFNULL(l.life_orders,0) AS life_orders,
				IFNULL(l.life_amount,0) AS life_amount
			" . $fromSql . " WHERE " . $listWhere . $this->orderBySql($filters['sort']) . $limitSql
		);
		$todayTs = strtotime(date("Y-m-d"));
		if ($res) {
			while ($r = mysqli_fetch_assoc($res)) {
				$last = $r['last_order_date'];
				$hasLast = ($last !== null && $last !== "" && $last !== "0000-00-00");
				$periodOrders = (int) $r['period_orders'];
				if ($periodOrders > 0) {
					$state = "ordered";
				} else if ($hasLast) {
					$state = "old_only";
				} else {
					$state = "never";
				}
				$company = trim((string) $r['company_name']);
				$person = trim((string) $r['cname']);
				$city = trim((string) $r['main_city']) !== "" ? trim((string) $r['main_city']) : trim((string) $r['city']);
				$mobile = trim((string) $r['mobile_no1']) !== "" ? trim((string) $r['mobile_no1']) : trim((string) $r['phone']);
				$rows[] = array(
					"id" => (int) $r['id'],
					"code" => $r['client_code'],
					"company" => ($company !== "") ? $company : $person,
					"person" => $person,
					"mobile" => $mobile,
					"city" => $city,
					"state" => $r['state'],
					"type" => $r['customer_type_name'] !== "" ? $r['customer_type_name'] : "Customer",
					"sales_person" => $r['sales_person'] !== "" ? $r['sales_person'] : "Not Assigned",
					"period_orders" => $periodOrders,
					"period_amount" => (float) $r['period_amount'],
					"life_orders" => (int) $r['life_orders'],
					"life_amount" => (float) $r['life_amount'],
					"last_order_date" => $hasLast ? $last : "",
					"days_since" => $hasLast ? (int) floor(($todayTs - strtotime($last)) / 86400) : null,
					"state" => $state,
				);
			}
		}

		return array(
			"filters" => $filters,
			"summary" => $summary,
			"rows" => $rows,
			"total_rows" => $totalRows,
			"page" => $page,
			"total_pages" => $totalPages,
			"page_size" => self::PAGE_SIZE,
		);
	}

	/**
	 * Human-readable list of active filters (shown as chips above the report).
	 */
	public function describeFilters($filters, $rights)
	{
		$chips = array();
		if (!empty($filters['employee_ids'])) {
			$employees = $this->getAccessibleEmployees($rights);
			$names = array();
			foreach ($filters['employee_ids'] as $id) {
				if (isset($employees[$id])) {
					$names[] = $employees[$id]['name'];
				}
			}
			if (!empty($names)) {
				$chips[] = "Sales Person: " . (count($names) > 3 ? count($names) . " selected" : implode(", ", $names));
			}
		} else {
			$chips[] = $filters['restricted'] ? "Sales Person: My Team" : "Sales Person: All";
		}
		if ($filters['customer_type'] > 0) {
			$types = $this->getCustomerTypes();
			if (isset($types[$filters['customer_type']])) {
				$chips[] = "Type: " . $types[$filters['customer_type']];
			}
		}
		if (isset($filters['state']) && $filters['state'] !== "") {
			$chips[] = "State: " . ($filters['state'] === self::AREA_NONE ? "Not Set" : $filters['state']);
		}
		if (isset($filters['pincode']) && $filters['pincode'] !== "") {
			$chips[] = "Pincode: " . ($filters['pincode'] === self::AREA_NONE ? "Not Set" : $filters['pincode'] . "*");
		}
		if ($filters['search'] !== "") {
			$chips[] = "Search: " . $filters['search'];
		}
		$chips[] = $filters['include_leads'] ? "Leads: Included" : "Leads: Excluded";
		return $chips;
	}

	public function getAreaStatusFilters()
	{
		return array(
			"all" => "All Areas",
			"no_orders" => "No Order In Period",
			"with_orders" => "Order Received In Period",
		);
	}

	public function getAreaSortOptions()
	{
		return array(
			"not_ordered_desc" => "Not Ordered Customers (High to Low)",
			"amount_desc" => "Period Amount (High to Low)",
			"customers_desc" => "Total Customers (High to Low)",
			"coverage_asc" => "Order Coverage % (Low to High)",
			"name_asc" => "State / Pincode (A-Z)",
		);
	}

	public function getStates($filters)
	{
		$where = "e.isDelete=0 AND TRIM(IFNULL(e.state,''))!=''";
		if ($filters['restricted']) {
			$ids = !empty($filters['accessible_ids']) ? implode(",", array_map("intval", $filters['accessible_ids'])) : "0";
			$where .= " AND e.seid IN (" . $ids . ")";
		}
		$states = array();
		$res = $this->safeQuery("SELECT MAX(TRIM(e.state)) AS state_name FROM executive e WHERE " . $where . "
			GROUP BY UPPER(TRIM(e.state)) ORDER BY state_name ASC");
		if ($res) {
			while ($row = mysqli_fetch_assoc($res)) {
				$states[] = $row['state_name'];
			}
		}
		return $states;
	}

	public function normalizeAreaFilters($req, $rights)
	{
		$filters = $this->normalizeFilters($req, $rights);
		$filters['order_status'] = "all";
		$filters['group_by'] = (isset($req['group_by']) && $req['group_by'] === "state") ? "state" : "pincode";
		$filters['state'] = isset($req['state']) ? trim((string) $req['state']) : "";
		$filters['pincode'] = isset($req['pincode']) ? trim((string) $req['pincode']) : "";

		$statuses = $this->getAreaStatusFilters();
		$filters['area_status'] = (isset($req['area_status']) && isset($statuses[$req['area_status']])) ? $req['area_status'] : "all";
		$sorts = $this->getAreaSortOptions();
		$filters['area_sort'] = (isset($req['area_sort']) && isset($sorts[$req['area_sort']])) ? $req['area_sort'] : "not_ordered_desc";
		return $filters;
	}

	/**
	 * State-wise or Pincode-wise customer order coverage.
	 * Pincode = executive.zip, State = executive.state.
	 */
	public function buildArea($filters, $paginate = true)
	{
		$fromSql = $this->baseFromSql($filters);
		$where = $this->baseWhereSql($filters);
		$isState = ($filters['group_by'] === "state");
		$keyExpr = $isState ? "UPPER(TRIM(IFNULL(e.state,'')))" : "TRIM(IFNULL(e.zip,''))";

		$having = "";
		if ($filters['area_status'] === "no_orders") {
			$having = " HAVING ordered=0";
		} else if ($filters['area_status'] === "with_orders") {
			$having = " HAVING ordered>0";
		}

		$groupSql = "SELECT " . $keyExpr . " AS area_key,
				MAX(TRIM(IFNULL(e.state,''))) AS state_name,
				MAX(TRIM(IFNULL(e.main_city,''))) AS city_name,
				COUNT(DISTINCT TRIM(IFNULL(e.zip,''))) AS pincode_count,
				COUNT(*) AS customers,
				SUM(CASE WHEN IFNULL(p.period_orders,0)>0 THEN 1 ELSE 0 END) AS ordered,
				SUM(CASE WHEN IFNULL(p.period_orders,0)=0 THEN 1 ELSE 0 END) AS not_ordered,
				SUM(CASE WHEN l.customer_id IS NULL THEN 1 ELSE 0 END) AS never,
				SUM(IFNULL(p.period_orders,0)) AS orders,
				SUM(IFNULL(p.period_amount,0)) AS amount,
				MAX(l.last_order_date) AS last_order_date
			" . $fromSql . " WHERE " . $where . " GROUP BY area_key" . $having;

		$summary = array(
			"areas" => 0,
			"areas_no_order" => 0,
			"customers" => 0,
			"ordered" => 0,
			"not_ordered" => 0,
			"orders" => 0,
			"amount" => 0,
		);
		$sumRes = $this->safeQuery("SELECT COUNT(*) AS areas,
				SUM(CASE WHEN t.ordered=0 THEN 1 ELSE 0 END) AS areas_no_order,
				SUM(t.customers) AS customers, SUM(t.ordered) AS ordered, SUM(t.not_ordered) AS not_ordered,
				SUM(t.orders) AS orders, SUM(t.amount) AS amount
			FROM (" . $groupSql . ") t");
		if ($sumRes && ($row = mysqli_fetch_assoc($sumRes))) {
			foreach ($summary as $key => $val) {
				$summary[$key] = ($key === "amount") ? (float) $row[$key] : (int) $row[$key];
			}
		}

		$totalRows = $summary['areas'];
		$limitSql = "";
		$totalPages = 1;
		$page = 1;
		if ($paginate) {
			$totalPages = max(1, (int) ceil($totalRows / self::PAGE_SIZE));
			$page = min($filters['page'], $totalPages);
			$limitSql = " LIMIT " . (($page - 1) * self::PAGE_SIZE) . "," . self::PAGE_SIZE;
		}

		$orderSql = " ORDER BY not_ordered DESC, customers DESC, area_key ASC";
		if ($filters['area_sort'] === "amount_desc") {
			$orderSql = " ORDER BY amount DESC, orders DESC, area_key ASC";
		} else if ($filters['area_sort'] === "customers_desc") {
			$orderSql = " ORDER BY customers DESC, area_key ASC";
		} else if ($filters['area_sort'] === "coverage_asc") {
			$orderSql = " ORDER BY (t.ordered/t.customers) ASC, customers DESC, area_key ASC";
		} else if ($filters['area_sort'] === "name_asc") {
			$orderSql = $isState ? " ORDER BY (area_key='') ASC, area_key ASC" : " ORDER BY (area_key='') ASC, state_name ASC, area_key ASC";
		}

		$rows = array();
		$todayTs = strtotime(date("Y-m-d"));
		$res = $this->safeQuery("SELECT * FROM (" . $groupSql . ") t" . $orderSql . $limitSql);
		if ($res) {
			while ($r = mysqli_fetch_assoc($res)) {
				$customers = (int) $r['customers'];
				$ordered = (int) $r['ordered'];
				$last = $r['last_order_date'];
				$hasLast = ($last !== null && $last !== "" && $last !== "0000-00-00");
				$rows[] = array(
					"key" => ($r['area_key'] === "") ? self::AREA_NONE : $r['area_key'],
					"label" => ($r['area_key'] === "") ? "Not Set" : ($isState ? $r['state_name'] : $r['area_key']),
					"state" => $r['state_name'] !== "" ? $r['state_name'] : "-",
					"city" => $r['city_name'] !== "" ? $r['city_name'] : "-",
					"pincode_count" => (int) $r['pincode_count'],
					"customers" => $customers,
					"ordered" => $ordered,
					"not_ordered" => (int) $r['not_ordered'],
					"never" => (int) $r['never'],
					"coverage" => $customers > 0 ? round(($ordered * 100) / $customers, 1) : 0,
					"orders" => (int) $r['orders'],
					"amount" => (float) $r['amount'],
					"last_order_date" => $hasLast ? $last : "",
					"days_since" => $hasLast ? (int) floor(($todayTs - strtotime($last)) / 86400) : null,
				);
			}
		}

		return array(
			"filters" => $filters,
			"summary" => $summary,
			"rows" => $rows,
			"total_rows" => $totalRows,
			"page" => $page,
			"total_pages" => $totalPages,
			"page_size" => self::PAGE_SIZE,
		);
	}

	/**
	 * Final orders of one customer. Period orders first; when none, latest lifetime orders.
	 */
	public function getCustomerOrders($customerId, $filters, $lifetime = false)
	{
		$customerId = (int) $customerId;
		$out = array("customer" => "", "in_period" => true, "rows" => array(), "total" => 0);
		if ($customerId <= 0) {
			return $out;
		}

		$custWhere = "e.id='" . $customerId . "' AND e.isDelete=0";
		if ($filters['restricted']) {
			$ids = !empty($filters['accessible_ids']) ? implode(",", array_map("intval", $filters['accessible_ids'])) : "0";
			$custWhere .= " AND e.seid IN (" . $ids . ")";
		}
		$cRes = $this->safeQuery("SELECT e.company_name, e.cname FROM executive e WHERE " . $custWhere . " LIMIT 1");
		if (!$cRes || !($c = mysqli_fetch_assoc($cRes))) {
			return $out;
		}
		$out['customer'] = trim((string) $c['company_name']) !== "" ? $c['company_name'] : $c['cname'];

		$base = "SELECT o.id, o.order_no, o.order_date, o.status, o.grand_total,
				IFNULL(o.quotation_id,0) AS quotation_id, IFNULL(se.name,'') AS sales_person
			FROM orders o
			LEFT JOIN sales_executive se ON se.id=o.sales_id
			WHERE o.isDelete=0 AND o.customer_id='" . $customerId . "'
				AND o.status IN (" . self::FINAL_STATUSES . ")";
		if ($lifetime) {
			$out['in_period'] = false;
			$out['lifetime'] = true;
			$rows = $this->fetchOrderRows($this->safeQuery($base . " ORDER BY o.order_date DESC, o.id DESC LIMIT 500"));
			$total = 0;
			foreach ($rows as $row) {
				$total += $row['amount'];
			}
			$out['rows'] = $rows;
			$out['total'] = $total;
			return $out;
		}
		$res = $this->safeQuery($base . " AND o.order_date BETWEEN '" . $filters['from'] . "' AND '" . $filters['to'] . "' ORDER BY o.order_date DESC, o.id DESC");
		$rows = $this->fetchOrderRows($res);
		if (empty($rows)) {
			$out['in_period'] = false;
			$rows = $this->fetchOrderRows($this->safeQuery($base . " ORDER BY o.order_date DESC, o.id DESC LIMIT 10"));
		}
		$total = 0;
		foreach ($rows as $row) {
			$total += $row['amount'];
		}
		$out['rows'] = $rows;
		$out['total'] = $total;
		return $out;
	}

	public function orderStatusLabel($status)
	{
		$map = array(
			1 => "Approved",
			2 => "Dispatched",
			4 => "Account Approved",
			5 => "Dispatch",
			6 => "Order Complete",
			7 => "LR Pending",
		);
		$status = (int) $status;
		return isset($map[$status]) ? $map[$status] : ("Status " . $status);
	}

	private function fetchOrderRows($res)
	{
		$rows = array();
		if (!$res) {
			return $rows;
		}
		while ($r = mysqli_fetch_assoc($res)) {
			$rows[] = array(
				"order_no" => $r['order_no'],
				"order_date" => $r['order_date'],
				"source" => ((int) $r['quotation_id'] > 0) ? "Quotation / PI" : "Direct Order",
				"status" => $this->orderStatusLabel($r['status']),
				"sales_person" => $r['sales_person'],
				"amount" => (float) $r['grand_total'],
			);
		}
		return $rows;
	}

	private function baseFromSql($filters)
	{
		$statuses = self::FINAL_STATUSES;
		return "FROM executive e
			LEFT JOIN sales_executive se ON se.id=e.seid
			LEFT JOIN customer_type ct ON ct.id=e.type_of_executive AND ct.isDelete=0
			LEFT JOIN (
				SELECT customer_id, COUNT(*) AS period_orders, SUM(grand_total) AS period_amount
				FROM orders
				WHERE isDelete=0 AND status IN (" . $statuses . ")
					AND order_date BETWEEN '" . $filters['from'] . "' AND '" . $filters['to'] . "'
				GROUP BY customer_id
			) p ON p.customer_id=e.id
			LEFT JOIN (
				SELECT customer_id, MAX(order_date) AS last_order_date, COUNT(*) AS life_orders, SUM(grand_total) AS life_amount
				FROM orders
				WHERE isDelete=0 AND status IN (" . $statuses . ")
				GROUP BY customer_id
			) l ON l.customer_id=e.id ";
	}

	private function baseWhereSql($filters)
	{
		$where = "e.isDelete=0
			AND ((e.company_name IS NOT NULL AND e.company_name!='') OR (e.cname IS NOT NULL AND e.cname!=''))";

		if (!$filters['include_leads']) {
			$where .= " AND IFNULL(e.customer_flag,0)!=1";
		}

		if (!empty($filters['employee_ids'])) {
			$where .= " AND e.seid IN (" . implode(",", array_map("intval", $filters['employee_ids'])) . ")";
		} else if ($filters['restricted']) {
			$ids = !empty($filters['accessible_ids']) ? implode(",", array_map("intval", $filters['accessible_ids'])) : "0";
			$where .= " AND e.seid IN (" . $ids . ")";
		}

		if ($filters['customer_type'] > 0) {
			$where .= " AND e.type_of_executive='" . (int) $filters['customer_type'] . "'";
		}

		if (isset($filters['state']) && $filters['state'] !== "") {
			if ($filters['state'] === self::AREA_NONE) {
				$where .= " AND TRIM(IFNULL(e.state,''))=''";
			} else {
				$st = mysqli_real_escape_string($this->conn, strtoupper($filters['state']));
				$where .= " AND UPPER(TRIM(IFNULL(e.state,'')))='" . $st . "'";
			}
		}

		if (isset($filters['pincode']) && $filters['pincode'] !== "") {
			if ($filters['pincode'] === self::AREA_NONE) {
				$where .= " AND TRIM(IFNULL(e.zip,''))=''";
			} else {
				$pin = mysqli_real_escape_string($this->conn, $filters['pincode']);
				$where .= " AND TRIM(IFNULL(e.zip,'')) LIKE '" . $pin . "%'";
			}
		}

		if ($filters['search'] !== "") {
			$s = mysqli_real_escape_string($this->conn, $filters['search']);
			$where .= " AND (e.company_name LIKE '%" . $s . "%' OR e.cname LIKE '%" . $s . "%'
				OR e.client_code LIKE '%" . $s . "%' OR e.main_city LIKE '%" . $s . "%'
				OR e.mobile_no1 LIKE '%" . $s . "%')";
		}
		return $where;
	}

	private function statusWhereSql($status)
	{
		if ($status === "ordered") {
			return " AND IFNULL(p.period_orders,0)>0";
		}
		if ($status === "not_ordered") {
			return " AND IFNULL(p.period_orders,0)=0";
		}
		if ($status === "old_only") {
			return " AND IFNULL(p.period_orders,0)=0 AND l.customer_id IS NOT NULL";
		}
		if ($status === "never") {
			return " AND l.customer_id IS NULL";
		}
		return "";
	}

	private function orderBySql($sort)
	{
		if ($sort === "last_order_desc") {
			return " ORDER BY (l.last_order_date IS NULL) ASC, l.last_order_date DESC, e.company_name ASC";
		}
		if ($sort === "orders_desc") {
			return " ORDER BY period_orders DESC, period_amount DESC, e.company_name ASC";
		}
		if ($sort === "amount_desc") {
			return " ORDER BY period_amount DESC, period_orders DESC, e.company_name ASC";
		}
		if ($sort === "name_asc") {
			return " ORDER BY e.company_name ASC, e.cname ASC";
		}
		return " ORDER BY (l.last_order_date IS NULL) DESC, l.last_order_date ASC, e.company_name ASC";
	}

	private function normalizeDate($value)
	{
		$value = trim((string) $value);
		if ($value === "") {
			return "";
		}
		$ts = strtotime(str_replace("/", "-", $value));
		return ($ts === false) ? "" : date("Y-m-d", $ts);
	}

	private function safeQuery($sql)
	{
		$result = @mysqli_query($this->conn, $sql);
		return ($result === false) ? false : $result;
	}
}
