<?php
/**
 * Ajax list for Re-Order Followup Report (page_id 677).
 * Shows ONLY followups already sent to BRM (send_to_brm=1).
 */
$page_id = 677;
$page_slug = 'reorder_followup_report';
include("connect.php");
require_once dirname(__DIR__) . '/include/reorder_brm_helper.php';

/* Ensure columns + auto-send any Admin Re-Order not yet flagged (idempotent) */
$adminId = isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) ? (int) $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] : 0;
reorder_brm_ensure_columns($db);
reorder_brm_auto_send_pending($db, $adminId);

$_REQUEST['reorder_only'] = 1;
if (!isset($_REQUEST['followup_type']) || $_REQUEST['followup_type'] === '' || $_REQUEST['followup_type'] === 'undefined') {
	/* Default: Pending only (no response) — overdue + today + future */
	$_REQUEST['followup_type'] = 'future,pending';
}
if (!isset($_REQUEST['isFillter']) || $_REQUEST['isFillter'] === '' || $_REQUEST['isFillter'] === 'false') {
	$_REQUEST['isFillter'] = 'true';
}

$__followuplist_ajax_embedded = true;
include __DIR__ . '/followuplist_get_ajax.php';
