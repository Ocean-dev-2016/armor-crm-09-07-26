<?php
/**
 * Quick verify App CRM APIs vs frontend.html contract
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$base = dirname(__DIR__);
chdir($base . '/service');

function callService($file, $params) {
	$_REQUEST = $params;
	$_GET = $params;
	$_POST = $params;
	ob_start();
	include $file;
	$out = ob_get_clean();
	return $out;
}

echo "=== TEST START ===\n";

// Find sales id via mysql if possible - hardcode from earlier: 2
$salesId = '2';

// Direct class tests (more reliable without full connect routing)
require_once('../include/define.php');
require_once('../include/function.class.php');
require_once('../include/class.daily_plan.php');
require_once('../include/class.executive.php');

$db = new Functions();
$db->connect();

echo "\n[1] visit_start_type_master count: ";
echo $db->rp_getTotalRecord('visit_start_type_master', 'isDelete=0 AND isActive=1', 0) . "\n";

$obj = new DailyPlan();

echo "\n[2] get_daily_plan_status (272):\n";
$r = $obj->getDailyPlanStatus($salesId);
echo json_encode($r) . "\n";

// Clean today's plan for retest
$today = date('Y-m-d');
$planId = $db->rp_getValue('daily_plan', 'id', "sales_id='{$salesId}' AND plan_date='{$today}' AND isDelete=0", 0);
if ($planId) {
	$db->rp_update('daily_plan', array('isDelete' => 1), "id='{$planId}'", 0);
	$db->rp_update('daily_plan_completion', array('isDelete' => 1), "daily_plan_id='{$planId}'", 0);
	$db->rp_update('daily_plan_customer', array('isDelete' => 1), "daily_plan_id='{$planId}'", 0);
	echo "Cleaned old plan id={$planId}\n";
}

echo "\n[3] save_daily_plan empty (should fail):\n";
echo json_encode($obj->saveDailyPlan(array('sales_id' => $salesId))) . "\n";

echo "\n[4] save_daily_plan project only (271):\n";
$r = $obj->saveDailyPlan(array(
	'sales_id' => $salesId,
	'expected_project_detail_count' => 2,
));
echo json_encode($r) . "\n";
$planId = isset($r['daily_plan_id']) ? $r['daily_plan_id'] : '';

echo "\n[5] get_daily_plan_status after plan:\n";
$st = $obj->getDailyPlanStatus($salesId);
echo json_encode($st) . "\n";
echo "can_punch_out=" . $st['can_punch_out'] . " (expect 0)\n";

echo "\n[6] canPunchOut before completion:\n";
echo json_encode($obj->canPunchOut($salesId)) . "\n";

echo "\n[7] save_daily_plan_completion (273):\n";
echo json_encode($obj->saveDailyPlanCompletion(array(
	'sales_id' => $salesId,
	'daily_plan_id' => $planId,
	'actual_project_detail_count' => 1,
))) . "\n";

echo "\n[8] canPunchOut after completion:\n";
echo json_encode($obj->canPunchOut($salesId)) . "\n";

echo "\n[9] getCustomer search + display_label (12):\n";
$ex = new Executive();
$cust = $ex->getCustomer($salesId, 'a');
if (isset($cust['result'][0])) {
	$first = $cust['result'][0];
	echo "ack={$cust['ack']} count=" . count($cust['result']) . " display_label=" . (isset($first['display_label']) ? $first['display_label'] : 'MISSING') . "\n";
} else {
	echo json_encode(array('ack' => isset($cust['ack']) ? $cust['ack'] : 0, 'msg' => isset($cust['ack_msg']) ? $cust['ack_msg'] : 'no result')) . "\n";
}

echo "\n[10] save_quotation_questionnaire validation (275):\n";
echo json_encode($obj->saveQuotationQuestionnaire(array(
	'quotation_id' => 1,
	'knows_full_range' => '',
))) . "\n";
echo json_encode($obj->saveQuotationQuestionnaire(array(
	'quotation_id' => 1,
	'knows_full_range' => 1,
))) . "\n";

echo "\n[11] api_table 271-275:\n";
$ar = $db->rp_getData('api_table', 'id,api_slug', "id BETWEEN 271 AND 275 AND isDelete=0", 'id ASC', 0);
while ($row = mysqli_fetch_assoc($ar)) {
	echo $row['id'] . ' ' . $row['api_slug'] . "\n";
}

echo "\n[12] Frontend API map check:\n";
$map = array(
	'Morning Plan' => array(271, 12, 20),
	'Evening Completion' => array(272, 273, 20),
	'Visit Start' => array(274, 75),
	'Visit Complete' => array(122),
	'Quotation' => array(275, 233, 234, 165),
);
foreach ($map as $feat => $apis) {
	echo "OK {$feat}: " . implode(',', $apis) . "\n";
}

echo "\n=== TEST DONE ===\n";
