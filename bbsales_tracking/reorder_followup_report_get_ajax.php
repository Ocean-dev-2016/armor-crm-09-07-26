<?php
/**
 * Ajax list for Re-Order Followup Report (page_id 677).
 * Reuses followuplist_get_ajax with reorder_only filter.
 */
$page_id = 677;
$page_slug = 'reorder_followup_report';
include("connect.php");

$_REQUEST['reorder_only'] = 1;
if (!isset($_REQUEST['followup_type']) || $_REQUEST['followup_type'] === '' || $_REQUEST['followup_type'] === 'undefined') {
	$_REQUEST['followup_type'] = 'today';
}
if (!isset($_REQUEST['isFillter']) || $_REQUEST['isFillter'] === '' || $_REQUEST['isFillter'] === 'false') {
	$_REQUEST['isFillter'] = 'true';
}

$__followuplist_ajax_embedded = true;
include __DIR__ . '/followuplist_get_ajax.php';
