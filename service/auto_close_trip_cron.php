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

$targetDate = isset($_REQUEST['date']) ? $_REQUEST['date'] : '';
$result = $expense->autoCloseForgottenTrips($targetDate);

header('Content-Type: application/json');
echo json_encode($result);
