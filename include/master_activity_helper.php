<?php
/**
 * Master activity viewer (Pritesh) — login ensure, access check, employee stats.
 */

if (!defined('MASTER_ACTIVITY_USERNAME')) {
	define('MASTER_ACTIVITY_USERNAME', 'Pritesh');
}
if (!defined('MASTER_ACTIVITY_PASSWORD')) {
	define('MASTER_ACTIVITY_PASSWORD', 'Admin@123');
}

function armor_master_activity_username_matches($username)
{
	return trim((string) $username) === MASTER_ACTIVITY_USERNAME;
}

function armor_ensure_master_activity_user($db)
{
	$username = MASTER_ACTIVITY_USERNAME;
	$existingId = $db->rp_getValue(CTABLE_ADMIN, 'id', "username='" . $db->clean($username) . "'", 0);
	if (!$existingId || $existingId == -1) {
		$oldId = $db->rp_getValue(CTABLE_ADMIN, 'id', "username='Pritesh Sir'", 0);
		if ($oldId && $oldId != -1) {
			$existingId = (int) $oldId;
		}
	}
	$passwordHash = md5(MASTER_ACTIVITY_PASSWORD);
	$adate = date('Y-m-d H:i:s');

	if ($existingId && $existingId != -1) {
		$db->rp_update(
			CTABLE_ADMIN,
			array(
				'name' => 'Pritesh',
				'username' => $username,
				'password' => $passwordHash,
				'admin_type' => 0,
				'type' => 0,
				'isDelete' => 0,
			),
			"id='" . (int) $existingId . "'",
			0
		);
		return (int) $existingId;
	}

	$rows = array(
		'name',
		'type',
		'admin_type',
		'user_id',
		'sales_executive_id',
		'username',
		'email',
		'password',
		'isDelete',
		'adate',
	);
	$values = array(
		'Pritesh',
		0,
		0,
		0,
		0,
		$username,
		'',
		$passwordHash,
		0,
		$adate,
	);

	$insertId = $db->rp_insert(CTABLE_ADMIN, $values, $rows, 0);
	return $insertId ? (int) $insertId : 0;
}

function armor_is_master_activity_user()
{
	if (!isset($_SESSION[SITE_SESS . '_MASTER_ACTIVITY_VIEW']) || $_SESSION[SITE_SESS . '_MASTER_ACTIVITY_VIEW'] != 1) {
		return false;
	}
	if (!isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) || (int) $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] <= 0) {
		return false;
	}

	global $db;
	if (!isset($db) || !is_object($db)) {
		return true;
	}

	$adminId = (int) $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
	$uname = $db->rp_getValue(CTABLE_ADMIN, 'username', "id='" . $adminId . "' AND isDelete=0", 0);
	if ($uname == -1 || !armor_master_activity_username_matches($uname)) {
		return false;
	}

	return true;
}

function armor_master_activity_allowed_pages()
{
	return array(
		'master_activity_dashboard.php',
		'master_activity_dashboard_get_ajax.php',
		'master_activity_detail_get_ajax.php',
		'dashboard_statical_report_get_ajax.php',
		'get_visit_map_dashboard.php',
		'get_attendance_map_dashboard.php',
		'quotation_viewer.php',
		'order_viewer.php',
		'expense_view.php',
		'logout.php',
	);
}

function armor_master_activity_date_where($dateField, $dateFrom, $dateTo)
{
	$where = '';
	if ($dateFrom == '' && $dateTo == '') {
		$where .= " AND year($dateField) = '" . date('Y') . "'";
	} else {
		if ($dateFrom != '') {
			$where .= " AND DATE($dateField) >= '" . date('Y-m-d', strtotime($dateFrom)) . "'";
		}
		if ($dateTo != '') {
			$where .= " AND DATE($dateField) <= '" . date('Y-m-d', strtotime($dateTo)) . "'";
		}
	}
	return $where;
}

function armor_master_activity_counts_for_sales($db, $salesId, $dateFrom, $dateTo)
{
	$salesId = (int) $salesId;

	$baseInquiry = " isDelete=0 AND inquiry_assign_to='" . $salesId . "'";
	$baseInquiry .= armor_master_activity_date_where('inquiry_date', $dateFrom, $dateTo);

	$baseProspect = $baseInquiry . " AND inquiry_lead_flag='-1'";
	$baseInquiryOnly = $baseInquiry . " AND inquiry_lead_flag != '-1'";
	$baseLead = $baseInquiry . " AND inquiry_lead_flag='1'";

	$quotationWhere = " isDelete=0 AND sales_id='" . $salesId . "'";
	$quotationWhere .= armor_master_activity_date_where('quotation_date', $dateFrom, $dateTo);

	$orderWhere = " isDelete=0 AND sales_id='" . $salesId . "'";
	$orderWhere .= armor_master_activity_date_where('order_date', $dateFrom, $dateTo);

	$dispatchWhere = " isDelete=0 AND sales_id='" . $salesId . "'";
	$dispatchWhere .= armor_master_activity_date_where('dispatch_date', $dateFrom, $dateTo);

	$invoiceWhere = " isDelete=0 AND sales_id='" . $salesId . "'";
	$invoiceWhere .= armor_master_activity_date_where('adate', $dateFrom, $dateTo);

	$visitWhere = " isDelete=0 AND user_id='" . $salesId . "'";
	$visitWhere .= armor_master_activity_date_where('created_date', $dateFrom, $dateTo);

	$followupWhere = " isDelete=0 AND user_id='" . $salesId . "'";
	$followupWhere .= armor_master_activity_date_where('followup_date', $dateFrom, $dateTo);

	$complainWhere = " isDelete=0 AND user_id='" . $salesId . "'";
	$complainWhere .= armor_master_activity_date_where('complain_date', $dateFrom, $dateTo);

	$expenseWhere = " isDelete=0 AND sales_executive_id='" . $salesId . "'";
	$expenseWhere .= armor_master_activity_date_where('expense_date', $dateFrom, $dateTo);

	$leaveWhere = " isDelete=0 AND sales_executive_id='" . $salesId . "'";
	$leaveWhere .= armor_master_activity_date_where('start_date', $dateFrom, $dateTo);

	$attendanceWhere = " isDelete=0 AND sales_id='" . $salesId . "'";
	$attendanceWhere .= armor_master_activity_date_where('date_time', $dateFrom, $dateTo);

	return array(
		'attendance' => (int) $db->rp_getTotalRecord('attendance', $attendanceWhere, 0),
		'visits' => (int) $db->rp_getTotalRecord('visit', $visitWhere, 0),
		'followups' => (int) $db->rp_getTotalRecord('followup', $followupWhere, 0),
		'raw_data' => (int) $db->rp_getTotalRecord('no_order_inquiry', $baseProspect, 0),
		'inquiry' => (int) $db->rp_getTotalRecord('no_order_inquiry', $baseInquiryOnly, 0),
		'leads' => (int) $db->rp_getTotalRecord('no_order_inquiry', $baseLead, 0),
		'quotations' => (int) $db->rp_getTotalRecord('quotation_detail', $quotationWhere, 0),
		'orders' => (int) $db->rp_getTotalRecord('orders', $orderWhere, 0),
		'dispatch' => (int) $db->rp_getTotalRecord('dispatch_detail', $dispatchWhere, 0),
		'invoice' => (int) $db->rp_getTotalRecord('invoice_new', $invoiceWhere, 0),
		'complain' => (int) $db->rp_getTotalRecord('complain', $complainWhere, 0),
		'expense' => (int) $db->rp_getTotalRecord('expense', $expenseWhere, 0),
		'leave' => (int) $db->rp_getTotalRecord('leave_request', $leaveWhere, 0),
	);
}

function armor_master_activity_all_employees($db, $dateFrom, $dateTo)
{
	$rows = array();
	$totals = array(
		'attendance' => 0,
		'visits' => 0,
		'followups' => 0,
		'raw_data' => 0,
		'inquiry' => 0,
		'leads' => 0,
		'quotations' => 0,
		'orders' => 0,
		'dispatch' => 0,
		'invoice' => 0,
		'complain' => 0,
		'expense' => 0,
		'leave' => 0,
	);

	$res = $db->rp_getData(
		'sales_executive',
		'*',
		"isDelete=0 AND isActive=1 AND type!='service_engineer' AND type!='service_executive'",
		'name ASC',
		0
	);

	if ($res) {
		while ($row = mysqli_fetch_assoc($res)) {
			$counts = armor_master_activity_counts_for_sales($db, $row['id'], $dateFrom, $dateTo);
			$rows[] = array(
				'id' => (int) $row['id'],
				'name' => stripslashes($row['name']),
				'type' => $row['type'],
				'counts' => $counts,
			);
			foreach ($totals as $key => $val) {
				$totals[$key] += $counts[$key];
			}
		}
	}

	return array('employees' => $rows, 'totals' => $totals);
}

function armor_master_activity_metric_where($metric, $salesId, $dateFrom, $dateTo)
{
	$salesId = (int) $salesId;
	$metric = (string) $metric;

	switch ($metric) {
		case 'attendance':
			return " isDelete=0 AND sales_id='" . $salesId . "'" . armor_master_activity_date_where('date_time', $dateFrom, $dateTo);
		case 'visits':
			return " isDelete=0 AND user_id='" . $salesId . "'" . armor_master_activity_date_where('created_date', $dateFrom, $dateTo);
		case 'followups':
			return " isDelete=0 AND user_id='" . $salesId . "'" . armor_master_activity_date_where('followup_date', $dateFrom, $dateTo);
		case 'raw_data':
			return " isDelete=0 AND inquiry_assign_to='" . $salesId . "' AND inquiry_lead_flag='-1'" . armor_master_activity_date_where('inquiry_date', $dateFrom, $dateTo);
		case 'inquiry':
			return " isDelete=0 AND inquiry_assign_to='" . $salesId . "' AND inquiry_lead_flag != '-1'" . armor_master_activity_date_where('inquiry_date', $dateFrom, $dateTo);
		case 'leads':
			return " isDelete=0 AND inquiry_assign_to='" . $salesId . "' AND inquiry_lead_flag='1'" . armor_master_activity_date_where('inquiry_date', $dateFrom, $dateTo);
		case 'quotations':
			return " isDelete=0 AND sales_id='" . $salesId . "'" . armor_master_activity_date_where('quotation_date', $dateFrom, $dateTo);
		case 'orders':
			return " isDelete=0 AND sales_id='" . $salesId . "'" . armor_master_activity_date_where('order_date', $dateFrom, $dateTo);
		case 'dispatch':
			return " isDelete=0 AND sales_id='" . $salesId . "'" . armor_master_activity_date_where('dispatch_date', $dateFrom, $dateTo);
		case 'invoice':
			return " isDelete=0 AND sales_id='" . $salesId . "'" . armor_master_activity_date_where('adate', $dateFrom, $dateTo);
		case 'complain':
			return " isDelete=0 AND user_id='" . $salesId . "'" . armor_master_activity_date_where('complain_date', $dateFrom, $dateTo);
		case 'expense':
			return " isDelete=0 AND sales_executive_id='" . $salesId . "'" . armor_master_activity_date_where('expense_date', $dateFrom, $dateTo);
		case 'leave':
			return " isDelete=0 AND sales_executive_id='" . $salesId . "'" . armor_master_activity_date_where('start_date', $dateFrom, $dateTo);
		default:
			return '1=0';
	}
}

function armor_master_activity_metric_labels()
{
	return array(
		'attendance' => 'Attendance',
		'visits' => 'Visits',
		'followups' => 'Followups',
		'raw_data' => 'Raw Data',
		'inquiry' => 'Inquiry',
		'leads' => 'Leads',
		'quotations' => 'Quotation',
		'orders' => 'Orders',
		'dispatch' => 'Dispatch',
		'invoice' => 'Invoice',
		'complain' => 'Complain',
		'expense' => 'Expense',
		'leave' => 'Leave',
	);
}

function armor_master_activity_render_images($db, $row, $imageField, $lightboxKey)
{
	$html = '';
	if (empty($row[$imageField])) {
		return '-';
	}
	$img = explode(',', $row[$imageField]);
	$imgpath = array();
	for ($i = 0; $i < sizeof($img); $i++) {
		if ($img[$i] == '') {
			continue;
		}
		$url = SITEURL . 'resource/image/' . $db->rp_getValue('media', 'url', "reference_id='" . $row['id'] . "' AND id='" . $img[$i] . "'", 0);
		if ($url && strpos($url, 'resource/image/') !== false && $db->rp_getValue('media', 'url', "reference_id='" . $row['id'] . "' AND id='" . $img[$i] . "'", 0) != -1) {
			$imgpath[] = $url;
		}
	}
	for ($i = 0; $i < sizeof($imgpath); $i++) {
		if ($i == 0) {
			$html .= '<a href="' . htmlspecialchars($imgpath[$i]) . '" data-lightbox="' . htmlspecialchars($lightboxKey) . '" data-title="' . htmlspecialchars($lightboxKey) . '"><img src="' . htmlspecialchars($imgpath[$i]) . '" style="height:60px;border:1px solid #ccc;border-radius:4px;"></a>';
		} else {
			$html .= '<div class="hidden"><a href="' . htmlspecialchars($imgpath[$i]) . '" data-lightbox="' . htmlspecialchars($lightboxKey) . '" data-title="' . htmlspecialchars($lightboxKey) . '"><img src="' . htmlspecialchars($imgpath[$i]) . '"></a></div>';
		}
	}
	return $html != '' ? $html : '-';
}

function armor_master_activity_count_cell($count, $salesId, $metric, $salesName)
{
	$count = (int) $count;
	if ($count <= 0) {
		return '<span class="count-zero">0</span>';
	}
	$safeName = htmlspecialchars($salesName, ENT_QUOTES);
	return '<a href="javascript:void(0)" class="count-link" onclick="showCellDetail(' . (int) $salesId . ', \'' . htmlspecialchars($metric, ENT_QUOTES) . '\', \'' . $safeName . '\')">' . $count . '</a>';
}

function armor_master_activity_map_btn($lat, $lng, $address, $dateLabel, $salesName, $mapType)
{
	if ($lat == '' || $lng == '' || $lat == '0' || $lng == '0') {
		return '-';
	}
	$address = htmlspecialchars(stripslashes($address), ENT_QUOTES);
	$salesName = htmlspecialchars($salesName, ENT_QUOTES);
	$dateLabel = htmlspecialchars($dateLabel, ENT_QUOTES);
	return '<a href="javascript:void(0)" class="ma-map-btn" data-map-type="' . htmlspecialchars($mapType) . '" data-lat="' . htmlspecialchars($lat) . '" data-lng="' . htmlspecialchars($lng) . '" data-address="' . $address . '" data-date="' . $dateLabel . '" data-name="' . $salesName . '"><img src="' . SITEURL . 'resource/map.png" style="height:50px;" alt="Map"></a>';
}
