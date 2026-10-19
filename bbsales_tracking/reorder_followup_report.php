<?php
/**
 * Re-Order Followup Report — Customer Reports submenu
 * Shows followups whose Description is Re-Order / RE ORDER / etc.
 * Default: Today's followups (same as Today's Followup, filtered).
 */
$page_id = 677;
$page_slug = 'reorder_followup_report';
$__reorder_followup_report = true;

if (!isset($_REQUEST['followup_type']) || $_REQUEST['followup_type'] === '') {
	/* Show all already-sent-to-BRM Re-Order followups by default */
	$_REQUEST['followup_type'] = 'all';
	$_GET['followup_type'] = 'all';
}

include __DIR__ . '/followuplist_manage.php';
