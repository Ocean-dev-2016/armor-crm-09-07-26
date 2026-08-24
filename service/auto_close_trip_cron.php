<?php
/**
 * Daily cron — auto-close forgotten vehicle trips at 11:30 PM.
 * Schedule: 30 23 * * * php /path/to/service/auto_close_trip_cron.php
 * Or URL: /service/auto_close_trip_cron.php
 * Optional test: /service/auto_close_trip_cron.php?date=2025-08-23
 */
error_reporting(1);
date_default_timezone_set('Asia/Kolkata');
require_once(__DIR__ . "/../include/define.php");
require_once(__DIR__ . "/../include/function.class.php");
require_once(__DIR__ . "/../include/expense.class.php");

$db = new Admin();
$db->connect();
$expense = new Expense();

// One-time revert: ?revert=4719,4720,4721
if (isset($_REQUEST['revert']) && $_REQUEST['revert'] != '') {
	$tripIds = explode(',', $_REQUEST['revert']);
	$result = $expense->revertAutoClosedTrips($tripIds);
	header('Content-Type: application/json');
	echo json_encode($result);
	exit;
}

$targetDate = isset($_REQUEST['date']) ? $_REQUEST['date'] : '';
$options = array();
// Past date in URL = manual catch-up for that day only (not today's trips during daytime)
if ($targetDate != "" && date('Y-m-d', strtotime($targetDate)) < date('Y-m-d')) {
	$options['allow_early'] = true;
}
$result = $expense->autoCloseForgottenTrips($targetDate, $options);

header('Content-Type: application/json');
echo json_encode($result);
