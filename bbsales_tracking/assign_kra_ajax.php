<?php
$mode = isset($_REQUEST['mode']) ? trim($_REQUEST['mode']) : '';

if ($mode === 'assign') {
	$page_id = 672;
	$page_slug = 'assign_kra';
	include('connect.php');
} else {
	include('connect_in.php');
}

header('Content-Type: application/json; charset=utf-8');

if (isset($db) && method_exists($db, 'clean')) {
	$mode = $db->clean($mode);
}
function assign_kra_app_sales_where()
{
	return "isDelete=0 AND isActive=1 AND type IN ('sales_executive','sales_officer','area_sales_manager','area_manager','service_executive')";
}

if ($mode === 'get_customers') {
	$search = isset($_REQUEST['q']) ? $db->clean($_REQUEST['q']) : '';
	$where = "isDelete=0 AND isActive=1 AND company_name!=''";
	if ($search !== '') {
		$where .= " AND (company_name LIKE '%" . $search . "%' OR cname LIKE '%" . $search . "%' OR client_code LIKE '%" . $search . "%' OR state LIKE '%" . $search . "%')";
	}
	$rows = array();
	$r = $db->rp_getData('executive', 'id, company_name, state, client_code', $where, 'company_name ASC', 0, 500);
	if ($r) {
		while ($d = mysqli_fetch_assoc($r)) {
			$firm = trim($d['company_name']);
			$state = trim($d['state']);
			$code = trim($d['client_code']);
			$parts = array();
			if ($code !== '') {
				$parts[] = $code;
			}
			if ($firm !== '') {
				$parts[] = $firm;
			}
			if ($state !== '') {
				$parts[] = $state;
			}
			$label = implode(' - ', $parts);
			$rows[] = array(
				'id' => (int) $d['id'],
				'text' => $label,
				'state' => $state,
				'company_name' => $firm,
				'client_code' => $d['client_code'],
			);
		}
	}
	echo json_encode(array('ack' => 1, 'results' => $rows));
	include 'disconnect.php';
	exit;
}

if ($mode === 'get_customer_states') {
	$customer_ids = isset($_REQUEST['customer_ids']) ? $_REQUEST['customer_ids'] : '';
	$states = array();
	if ($customer_ids !== '') {
		foreach (explode(',', $customer_ids) as $cid) {
			$cid = (int) trim($cid);
			if ($cid <= 0) {
				continue;
			}
			$st = $db->rp_getValue('executive', 'state', 'id=' . $cid . ' AND isDelete=0', 0);
			if ($st !== false && trim($st) !== '') {
				$states[] = trim($st);
			}
		}
	}
	$states = array_values(array_unique($states));
	echo json_encode(array('ack' => 1, 'states' => $states));
	include 'disconnect.php';
	exit;
}

if ($mode === 'get_sales_persons') {
	$states = isset($_REQUEST['states']) ? $_REQUEST['states'] : '';
	$stateList = array();
	if ($states !== '') {
		foreach (explode(',', $states) as $st) {
			$st = trim($st);
			if ($st !== '') {
				$stateList[] = $st;
			}
		}
		$stateList = array_unique($stateList);
	}

	// Filter sales persons using "Add Class Area" mapping (sales_executive_map_area),
	// not relying on sales_executive.state (which may differ from assigned area state).
	$where = assign_kra_app_sales_where();
	if (!empty($stateList)) {
		$escapedStateNames = array();
		foreach ($stateList as $st) {
			$escapedStateNames[] = "'" . $db->clean($st) . "'";
		}

		// state table is `class` and cities belong to `class.id` via `city.state_id`.
		$stateWhere = "class.name IN (" . implode(',', $escapedStateNames) . ") AND class.isDelete=0";

		$where .= " AND id IN (
			SELECT DISTINCT sam.sales_executive_id
			FROM sales_executive_map_area sam
			INNER JOIN city c ON c.id = sam.city_id
			INNER JOIN class ON class.id = c.state_id
			WHERE sam.isDelete=0 AND {$stateWhere}
		)";
	}

	$rows = array();
	$r = $db->rp_getData('sales_executive', 'id, name, state, type', $where, 'name ASC', 0);
	if ($r) {
		while ($d = mysqli_fetch_assoc($r)) {
			$name = trim($d['name']);
			$state = trim($d['state']);
			$rows[] = array(
				'id' => (int) $d['id'],
				'text' => $name . ($state !== '' ? ' - ' . $state : ''),
				'state' => $state,
				'name' => $name,
			);
		}
	}
	echo json_encode(array('ack' => 1, 'results' => $rows));
	include 'disconnect.php';
	exit;
}

if ($mode === 'get_sales_summary') {
	$sales_person_id = isset($_REQUEST['sales_person_id']) ? (int) $_REQUEST['sales_person_id'] : 0;
	if ($sales_person_id <= 0) {
		echo json_encode(array('ack' => 0, 'ack_msg' => 'Invalid sales person.'));
		include 'disconnect.php';
		exit;
	}

	$sp_r = $db->rp_getData(
		'sales_executive',
		'id, name, phone, type, state, city',
		'id=' . $sales_person_id . ' AND ' . assign_kra_app_sales_where(),
		'',
		0
	);
	if (!$sp_r || !($sp = mysqli_fetch_assoc($sp_r))) {
		echo json_encode(array('ack' => 0, 'ack_msg' => 'Sales person not found.'));
		include 'disconnect.php';
		exit;
	}

	$kra_assigned = (int) $db->rp_getTotalRecord(
		'executive',
		"isDelete=0 AND seid='" . $sales_person_id . "' AND seid!=0 AND seid IS NOT NULL AND seid!=''",
		0
	);

	$quotation_count = (int) $db->rp_getValue(
		'quotation_detail',
		'COUNT(*)',
		"isDelete=0 AND sales_id='" . $sales_person_id . "'",
		0
	);
	$quotation_value = (float) $db->rp_getValue(
		'quotation_detail',
		'SUM(grand_total)',
		"isDelete=0 AND sales_id='" . $sales_person_id . "'",
		0
	);

	$pi_count = (int) $db->rp_getValue(
		'orders',
		'COUNT(*)',
		"isDelete=0 AND status=1 AND sales_id='" . $sales_person_id . "'",
		0
	);
	$pi_value = (float) $db->rp_getValue(
		'orders',
		'SUM(grand_total)',
		"isDelete=0 AND status=1 AND sales_id='" . $sales_person_id . "'",
		0
	);

	$typeLabel = isset($sp['type']) ? ucwords(str_replace('_', ' ', $sp['type'])) : '';
	$quotation_value_label = CURR . ' ' . $db->rp_number_format((float) $quotation_value, 2);
	$pi_value_label = CURR . ' ' . $db->rp_number_format((float) $pi_value, 2);

	echo json_encode(array(
		'ack' => 1,
		'id' => (int) $sp['id'],
		'name' => $sp['name'],
		'phone' => isset($sp['phone']) ? $sp['phone'] : '',
		'type_label' => $typeLabel,
		'state' => isset($sp['state']) ? $sp['state'] : '',
		'city' => isset($sp['city']) ? $sp['city'] : '',
		'kra_assigned' => $kra_assigned,
		'quotation_count' => $quotation_count,
		'quotation_value' => $quotation_value,
		'quotation_value_label' => $quotation_value_label,
		'pi_count' => $pi_count,
		'pi_value' => $pi_value,
		'pi_value_label' => $pi_value_label,
	));
	include 'disconnect.php';
	exit;
}

if ($mode === 'assign') {
	if (
		!isset($rights)
		|| (
			(!isset($rights['insert_flag']) || $rights['insert_flag'] != 1)
			&& (!isset($rights['update_flag']) || $rights['update_flag'] != 1)
			&& $_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0
		)
	) {
		echo json_encode(array('ack' => 0, 'ack_msg' => 'You do not have permission to assign customers.'));
		include 'disconnect.php';
		exit;
	}

	$customer_ids = isset($_REQUEST['customer_ids']) ? $_REQUEST['customer_ids'] : '';
	$sales_person_id = isset($_REQUEST['sales_person_id']) ? (int) $_REQUEST['sales_person_id'] : 0;

	if ($customer_ids === '' || $sales_person_id <= 0) {
		echo json_encode(array('ack' => 0, 'ack_msg' => 'Please select customer(s) and sales person.'));
		include 'disconnect.php';
		exit;
	}

	$idArr = array();
	foreach (explode(',', $customer_ids) as $cid) {
		$cid = (int) trim($cid);
		if ($cid > 0) {
			$idArr[] = $cid;
		}
	}
	$idArr = array_unique($idArr);

	if (empty($idArr)) {
		echo json_encode(array('ack' => 0, 'ack_msg' => 'Please select at least one customer.'));
		include 'disconnect.php';
		exit;
	}

	$spCheck = $db->rp_getTotalRecord('sales_executive', 'id=' . $sales_person_id . ' AND ' . assign_kra_app_sales_where(), 0);
	if ($spCheck <= 0) {
		echo json_encode(array('ack' => 0, 'ack_msg' => 'Invalid sales person selected.'));
		include 'disconnect.php';
		exit;
	}

	$updated = 0;
	$now = date('Y-m-d H:i:s');
	foreach ($idArr as $cid) {
		$ok = $db->rp_update('executive', array(
			'seid' => $sales_person_id,
			'modify_date' => $now,
		), 'id=' . $cid . ' AND isDelete=0', 0);
		if ($ok) {
			$updated++;
		}
	}

	if ($updated > 0) {
		$spName = $db->rp_getValue('sales_executive', 'name', 'id=' . $sales_person_id . ' AND isDelete=0', 0);
		echo json_encode(array(
			'ack' => 1,
			'ack_msg' => $updated . ' customer(s) assigned to ' . $spName . '. They will appear in the mobile app after sync.',
			'updated' => $updated,
		));
	} else {
		echo json_encode(array('ack' => 0, 'ack_msg' => 'Assignment failed. Please try again.'));
	}
	include 'disconnect.php';
	exit;
}

echo json_encode(array('ack' => 0, 'ack_msg' => 'Invalid request.'));
include 'disconnect.php';
