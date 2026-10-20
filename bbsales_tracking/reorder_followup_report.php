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
	/* Default on page load: Today Followup + Pending Followup */
	$_REQUEST['followup_type'] = 'today,pending';
	$_GET['followup_type'] = 'today,pending';
}

include __DIR__ . '/followuplist_manage.php';
