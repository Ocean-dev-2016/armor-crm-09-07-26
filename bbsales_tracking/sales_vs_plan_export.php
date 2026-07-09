<?php
ob_start();

// DEBUG (remove later)
ini_set('display_errors', 1);
error_reporting(E_ALL);

include("connect.php");

// REQUEST VALUES (PHP 5 safe)
$sales_executive = isset($_REQUEST['sales_executive']) ? intval($_REQUEST['sales_executive']) : 0;
$filter_month    = isset($_REQUEST['filter_month']) ? intval($_REQUEST['filter_month']) : 0;
$filter_year     = isset($_REQUEST['filter_year']) ? intval($_REQUEST['filter_year']) : 0;

if ($sales_executive == 0 || $filter_month == 0 || $filter_year == 0) {
    echo "Please select all filters";
    exit;
}

// MONTH NAME (PHP 5 compatible array)
$months = array(
    1 => 'January', 2 => 'February', 3 => 'March',
    4 => 'April', 5 => 'May', 6 => 'June',
    7 => 'July', 8 => 'August', 9 => 'September',
    10 => 'October', 11 => 'November', 12 => 'December'
);

$month_name = isset($months[$filter_month]) ? $months[$filter_month] : '';
$display_month_year = $month_name . " " . $filter_year;

// SALES NAME
$sales_name = $db->rp_getValue("sales_executive", "name", "id='".$sales_executive."'", 0);

// COMMON WHERE
$where = "isDelete=0 AND isActive=1 
          AND sales_executive_id='".$sales_executive."' 
          AND month='".$filter_month."' 
          AND year='".$filter_year."'";

// FETCH DATA FUNCTION
function getPlanData($db, $plan, $where, $sales_executive, $filter_month, $filter_year) {

    $data = array();

    $q = $db->rp_getData(
        "sales_vs_plan",
        "executive_id,expended_order_amount",
        $where." AND plan_type='".$plan."'",
        "",
        0
    );

    if ($q) {
        while ($r = mysqli_fetch_assoc($q)) {

            $exec = $db->rp_getData(
                "executive",
                "client_code,gst, turnover, company_name",
                "id='".$r['executive_id']."'"
            );

            $ex = mysqli_fetch_assoc($exec);

            $order = $db->rp_getValue(
                "orders",
                "SUM(grand_total)",
                "customer_id='".$r['executive_id']."' 
                AND MONTH(order_date)='".$filter_month."'
                AND YEAR(order_date)='".$filter_year."'
                AND sales_id='".$sales_executive."'",
                0
            );

            $visit = $db->rp_getTotalRecord(
                "visit",
                "customer_id='".$r['executive_id']."'
                AND MONTH(created_date)='".$filter_month."'
                AND YEAR(created_date)='".$filter_year."'",
                0
            );

            $data[] = array(
                'code'  => isset($ex['client_code']) ? $ex['client_code'] : '',
                'gst'  => isset($ex['gst']) ? $ex['gst'] : '',
                 'turnover'  => isset($ex['turnover']) ? $ex['turnover'] : '',
                'name'  => isset($ex['company_name']) ? $ex['company_name'] : '',
                'exp'   => isset($r['expended_order_amount']) ? $r['expended_order_amount'] : 0,
                'ach'   => $order ? $order : 0,
                'visit' => $visit ? $visit : 0
            );
        }
    }

    return $data;
}

// GET DATA
$planA = getPlanData($db, 1, $where, $sales_executive, $filter_month, $filter_year);
$planB = getPlanData($db, 2, $where, $sales_executive, $filter_month, $filter_year);
$planC = getPlanData($db, 3, $where, $sales_executive, $filter_month, $filter_year);

// MAX ROW
$max = max(count($planA), count($planB), count($planC));

// NO DATA
if ($max == 0) {
    echo "No data found";
    exit;
}

// HTML START
$html = '
<style>
table { border-collapse: collapse; width:100%; }
th, td { border:1px solid #000; padding:6px; text-align:left; }
th { background:#f2f2f2; }
</style>

<table>
<tr>
    <th colspan="21" style="text-align:center;">Monthly Order Planner</th>
</tr>
<tr>
    <th colspan="21">
        Person Name: '.$sales_name.'
        <span style="float:right;">Month: '.$display_month_year.'</span>
    </th>
</tr>

<tr>
    <th colspan="7">Plan A</th>
    <th colspan="7">Plan B</th>
    <th colspan="7">Plan C</th>
</tr>

<tr>
    <th>Code</th><th>GST Number</th><th>Turnover</th><th>Name</th><th>Exp</th><th>Ach</th><th>Visit</th>
    <th>Code</th><th>GST Number</th><th>Turnover</th><th>Name</th><th>Exp</th><th>Ach</th><th>Visit</th>
    <th>Code</th><th>GST Number</th><th>Turnover</th><th>Name</th><th>Exp</th><th>Ach</th><th>Visit</th>
</tr>
';

// LOOP
for ($i = 0; $i < $max; $i++) {

    $a = isset($planA[$i]) ? $planA[$i] : array('code'=>'','gst'=>'','turnover'=>'','name'=>'','exp'=>'','ach'=>'','visit'=>'');
    $b = isset($planB[$i]) ? $planB[$i] : array('code'=>'','gst'=>'','turnover'=>'','name'=>'','exp'=>'','ach'=>'','visit'=>'');
    $c = isset($planC[$i]) ? $planC[$i] : array('code'=>'','gst'=>'','turnover'=>'','name'=>'','exp'=>'','ach'=>'','visit'=>'');

   $data = [$a, $b, $c];

$html .= "<tr>";

foreach ($data as $row) {
    $html .= "
        <td>{$row['code']}</td>
        <td>{$row['gst']}</td>
        <td>{$row['turnover']}</td>
        <td>{$row['name']}</td>
        <td>{$row['exp']}</td>
        <td>{$row['ach']}</td>
        <td>{$row['visit']}</td>
    ";
}

$html .= "</tr>";
}

$html .= "</table>";

// FILE NAME
$fileName = "sales_vs_plan_" . date("Ymd_His") . ".xls";

// HEADERS
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$fileName);
header("Pragma: no-cache");
header("Expires: 0");

// OUTPUT
echo $html;

include("disconnect.php");

ob_end_flush();
exit;
?>