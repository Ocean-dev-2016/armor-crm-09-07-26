image.png<?php
$page_id = 674;
$page_slug = 'approval_entry';
include('connect.php');
require_once('../include/approval_entry_helper.php');
armor_approval_entry_ensure_table($db);

header('Content-Type: application/json; charset=utf-8');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

/* Allow due-reminder cron-like call without unlock; all other actions need unlock */
if ($action !== 'fire_due_reminders') {
	armor_approval_require_unlock(true);
}

if ($action === 'delete') {
	$id = (int)(isset($_REQUEST['id']) ? $_REQUEST['id'] : 0);
	if ($id <= 0) {
		echo json_encode(array('success' => false, 'message' => 'Invalid id.'));
		exit;
	}
	if ((int)$_SESSION[SITE_SESS . '_ADMIN_TYPE'] !== 0 && empty($rights['delete_flag'])) {
		echo json_encode(array('success' => false, 'message' => 'You do not have delete permission.'));
		exit;
	}
	$db->rp_update('approval_entry', array(
		'isDelete' => 1,
		'modified_date' => date('Y-m-d H:i:s'),
	), "id='" . $id . "'");
	echo json_encode(array('success' => true, 'message' => 'Deleted successfully.'));
	exit;
}

if ($action === 'company_type_name') {
	$id = (int)(isset($_REQUEST['id']) ? $_REQUEST['id'] : 0);
	$name = '';
	if ($id > 0) {
		$name = $db->rp_getValue('company_master', 'name', "id='" . $id . "' AND isDelete=0", 0);
	}
	echo json_encode(array('success' => true, 'name' => $name ? $name : ''));
	exit;
}

if ($action === 'fire_due_reminders') {
	$fired = armor_approval_fire_due_reminders($db);
	echo json_encode(array(
		'success' => true,
		'count' => count($fired),
		'reminders' => $fired,
	));
	exit;
}

if ($action === 'change_password') {
	$current = isset($_REQUEST['current_password']) ? (string)$_REQUEST['current_password'] : '';
	$newPass = isset($_REQUEST['new_password']) ? (string)$_REQUEST['new_password'] : '';
	$confirm = isset($_REQUEST['confirm_password']) ? (string)$_REQUEST['confirm_password'] : '';
	if ($newPass !== $confirm) {
		echo json_encode(array('success' => false, 'message' => 'New password and confirm password do not match.'));
		exit;
	}
	$result = armor_approval_change_password($db, $current, $newPass);
	echo json_encode(array(
		'success' => !empty($result['ok']),
		'message' => isset($result['message']) ? $result['message'] : 'Failed.',
	));
	exit;
}

echo json_encode(array('success' => false, 'message' => 'Invalid action.'));
