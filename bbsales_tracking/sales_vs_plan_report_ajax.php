<?php
$page_id = 667;
$page_slug = 'sales_vs_plan_report';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable     = "sales_vs_plan";
$ctable1    = "sales_vs_plan";

$ctable_where = "isDelete=0";
// Get the total number of rows in the table
// print_r($_REQUEST);exit;
if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
    $sales_id = $db->rp_getData("sales_executive", "*", "name LIKE '%" . $_REQUEST['searchName'] . "%' OR phone LIKE '%" . $_REQUEST['searchName'] . "%'  AND isDelete=0", "", 0);
    if ($sales_id) {
        while ($K = mysqli_fetch_assoc($sales_id)) {
            $USER_IDS[] = $K['id'];
        }
        $USER_IDS = implode(",", $USER_IDS);
        $ctable_where .= " AND sales_id IN (" . $USER_IDS . ") ";
    } else {
        $ctable_where .= " AND sales_id IN (0) ";
    }
}

if (empty($_REQUEST['sales_executive']) || empty($_REQUEST['filter_month']) || empty($_REQUEST['filter_year'])) {
    echo '<div style="text-align: center; padding: 20px; font-size: 18px; color: red;">';
    echo 'Please select all filters';
    echo '</div>';
    include("disconnect.php");
    exit();
}


if (isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive'] != "" && $_REQUEST['sales_executive'] != "null") {
    $ctable_where .= " AND sales_executive_id  = " . $_REQUEST['sales_executive'] . "";
}

if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
    if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 2) //sales executive and its chain wise order
    {
        if ($rights['personal_flag'] == 1) {
            $check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
            $ctable_where .= " AND sales_id='" . $check_id . "' ";
        } else {
            if ($rights['chain_vise_flag'] == 1) {
                $check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];

                $get_sales_type = $db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='" . $check_id . "'", 0);
                if ($get_sales_type == "sales_manager") {
                    $sales_executive_type = "Regional Sales Manager";
                    $key = "sm_id";
                    $WhereCondition .= ' ' . $key . '=' . $check_id;
                } else if ($get_sales_type == "area_sales_manager") {
                    $sales_executive_type = "National Sales Manager"; //Business Development Manager
                    $key = "asm_id";
                    $WhereCondition .= ' ' . $key . '=' . $check_id;
                } else if ($get_sales_type == "sales_officer") {
                    $sales_executive_type = "Area Sales Manager"; //Area Sales Manager
                    $key = "so_id";
                    $WhereCondition .= ' ' . $key . '=' . $check_id;
                } else if ($get_sales_type == "sales_executive") {
                    $sales_executive_type = "Sales Officer";
                    $key = "se_id";
                    $WhereCondition .= ' ' . $key . '=' . $check_id;
                } else {
                    $WhereCondition .= ' type = "service_engineer"';
                }

                $data = $db->rp_getData("sales_executive", "id", $WhereCondition, "", 0);

                $SALEID1 = array();
                if ($data) {
                    while ($data_d = mysqli_fetch_assoc($data)) {
                        $SALEID1[] = $data_d['id'];
                    }
                }
                if (!empty($SALEID1)) {
                    $SALEID1 = implode(",", $SALEID1);

                    $ctable_where .= "  AND sales_id IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
                } else {
                    $ctable_where .= "  AND sales_id IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
                }
            }
        }
    }
}

if (isset($_REQUEST['filter_month']) && $_REQUEST['filter_month'] != "") {
    $ctable_where .= " AND month ='" . $_REQUEST['filter_month'] . "' ";
    $selected_month = $_REQUEST['filter_month'];
}
if (isset($_REQUEST['filter_year']) && $_REQUEST['filter_year'] != "") {
    $ctable_where .= " AND year ='" . $_REQUEST['filter_year'] . "' ";
    $selected_year = $_REQUEST['filter_year'];
}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 100;

$get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where, 0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows / $item_per_page);

//get starting position to fetch the records
$page_position = (($page_number - 1) * $item_per_page);
$ctable_r = $db->rp_getData($ctable, "id,sales_executive_id,executive_id,plan_type,expended_order_amount,year,month", "", 0);

if ($ctable_r) {
    while ($row = mysqli_fetch_assoc($ctable_r)) {
        $sales_name_get = $db->rp_getValue("sales_executive", "name", "isDelete=0 AND isActive=1 AND id='" . $_REQUEST['sales_executive'] . "'", 0);

        if (empty($display_month_year)) {
            $months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
            $month_num = (int)$_REQUEST['filter_month'];
            $month_name = isset($months[$month_num]) ? $months[$month_num] : ' ';
            $display_month_year = $month_name . " " . $_REQUEST['filter_year'];
        }
    }

?>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        caption {
            font-size: 20px;
            margin: 10px;
            font-weight: bold;
        }

        .plan-header {
            text-align: center;
            font-weight: bold;
        }

        .header-row {
            background-color: #e6e6e6;
        }
    </style>

    <form action="" id="print_info" name="frm" method="post">
        <table>
            <!-- <caption>Monthly Order Planner</caption> -->
            <thead>
                <tr>
                    <th colspan="9" style="text-align: center; font-size: medium;">Monthly Order Planner</th>
                </tr>
                <tr>
                    <th colspan="9">Person Name: <?= $sales_name_get ?>
                        <span style="float: right !important;">Month: <?= htmlspecialchars($display_month_year) ?></span>
                    </th>
                </tr>
                <tr class="header-row">
                    <!-- <th rowspan="2">Sr/no.</th> -->
                    <th colspan="3" class="plan-header">Plan - A</th>
                    <th colspan="3" class="plan-header">Plan - B</th>
                    <th colspan="3" class="plan-header">Plan - C</th>
                </tr>

            </thead>
          <tbody>
<tr>

<!-- ================= PLAN 1 ================= -->
<td colspan="3" style="vertical-align: top; padding:0;">
<table>
<tr>
    <td><b>Client Code</b></td>
   <td><b>GST Number </b></td>
   <td><b>Turnover </b></td>
    <td><b>Client Name</b></td>
    <td><b>Expected Order</b></td>
    <td><b>Archived Amount</b></td>
    <td><b>Visit</b></td>
</tr>

<?php
$plan_a_r = $db->rp_getData($ctable, "executive_id,expended_order_amount", "isDelete=0 AND isActive=1 AND plan_type=1 AND $ctable_where", "", 0);

if ($plan_a_r) {
    $total_exp_amt_plan1 = 0;
    $total_odr_amt_plan1 = 0;

    while ($plan_a_d = mysqli_fetch_assoc($plan_a_r)) {

        // ✅ GET CLIENT DATA
        $executive_data = $db->rp_getData("executive", "client_code, gst , turnover,  company_name", "id='" . $plan_a_d['executive_id'] . "'");
        $executive_row = mysqli_fetch_assoc($executive_data);

        $turnover = $executive_row['turnover'];
        $gst = $executive_row['gst'];
        $client_code = $executive_row['client_code'];
        $company_name = $executive_row['company_name'];

        // ✅ ORDER + VISIT
        $get_order_amount = $db->rp_getValue("orders", "SUM(grand_total)", "customer_id='" . $plan_a_d['executive_id'] . "' AND MONTH(order_date)='$selected_month' AND YEAR(order_date)='$selected_year' AND sales_id='" . $_REQUEST['sales_executive'] . "'", 0);

        $visit = $db->rp_getTotalRecord("visit", "customer_id='" . $plan_a_d['executive_id'] . "' AND MONTH(created_date)='$selected_month' AND YEAR(created_date)='$selected_year'", 0);

        $style = ($get_order_amount < $plan_a_d['expended_order_amount'] && $plan_a_d['expended_order_amount'] > 0)
            ? "background-color:#ff9999"
            : "";
?>

<tr>
    
    <td><?= $client_code ?></td>
    <td>
<?= !empty($gst) ? $gst : '' ?>
</td>

<td>
<?= !empty($turnover) ? "$turnover" : '' ?>
</td>
    <td><?= $company_name ?></td>
    <td style="text-align:right;"><?= $plan_a_d['expended_order_amount'] ?></td>
    <td style="text-align:right; <?= $style ?>"><?= $get_order_amount ?></td>
    <td style="text-align:right;"><?= $visit ?></td>
</tr>

<?php
        $total_exp_amt_plan1 += $plan_a_d['expended_order_amount'];
        $total_odr_amt_plan1 += $get_order_amount;
    }
}
?>

<tr>
    <td><b>Total</b></td>
    <td></td>
   
    
    <td></td>
    <td></td>
     <td style="text-align:right;"><b><?= $total_exp_amt_plan1 ?></b></td>
        <td></td>   
        <td style="text-align:right;"><b><?= $total_odr_amt_plan1 ?></b></td>
        
</tr>
</table>
</td>

<!-- ================= PLAN 2 ================= -->
<td colspan="3" style="vertical-align: top; padding:0;">
<table>
<tr>
    <td><b>Client Code</b></td>
    <td><b>GST Number </b></td>
    
   <td><b>Turnover </b></td>
    <td><b>Client Name</b></td>
    <td><b>Expected Order</b></td>
    <td><b>Archived Amount</b></td>
    <td><b>Visit</b></td>
</tr>

<?php
$plan_b_r = $db->rp_getData($ctable, "executive_id,expended_order_amount", "isDelete=0 AND isActive=1 AND plan_type=2 AND $ctable_where", "", 0);

if ($plan_b_r) {
    $total_exp_amt_plan2 = 0;
    $total_odr_amt_plan2 = 0;

    while ($plan_b_d = mysqli_fetch_assoc($plan_b_r)) {

        $executive_data = $db->rp_getData("executive", "client_code, gst ,  turnover , company_name", "id='" . $plan_b_d['executive_id'] . "'");
        $row = mysqli_fetch_assoc($executive_data);
        
      $gst = $row['gst'];
      $turnover = $row['turnover'];
        $client_code = $row['client_code'];
        $company_name = $row['company_name'];

        $get_order_amount = $db->rp_getValue("orders", "SUM(grand_total)", "customer_id='" . $plan_b_d['executive_id'] . "' AND MONTH(order_date)='$selected_month' AND YEAR(order_date)='$selected_year' AND sales_id='" . $_REQUEST['sales_executive'] . "'", 0);

        $visit = $db->rp_getTotalRecord("visit", "customer_id='" . $plan_b_d['executive_id'] . "' AND MONTH(created_date)='$selected_month' AND YEAR(created_date)='$selected_year'", 0);

        $style = ($get_order_amount < $plan_b_d['expended_order_amount']) ? "background-color:#ff9999" : "";
?>

<tr>
    <td><?= $client_code ?></td>
  <td>
<?= !empty($gst) ? $gst : '' ?>
</td>

<td>
<?= !empty($turnover) ? "$turnover" : '' ?>
</td>
    <td><?= $company_name ?></td>
    <td style="text-align:right;"><?= $plan_b_d['expended_order_amount'] ?></td>
    <td style="text-align:right; <?= $style ?>"><?= $get_order_amount ?></td>
    <td style="text-align:right;"><?= $visit ?></td>
</tr>

<?php
        $total_exp_amt_plan2 += $plan_b_d['expended_order_amount'];
        $total_odr_amt_plan2 += $get_order_amount;
    }
}
?>

<tr>
    <td><b>Total</b></td>
    <td></td>

   
    <td></td> 
        <td></td>
            <td style="text-align:right;"><b><?= $total_exp_amt_plan2 ?></b></td>
            <td></td>
            <td style="text-align:right;"><b><?= $total_odr_amt_plan2 ?></b></td>
</tr>
</table>
</td>

<!-- ================= PLAN 3 ================= -->
<td colspan="3" style="vertical-align: top; padding:0;">
<table>
<tr>
    <td><b>Client Code</b></td>
    <td><b>GST Number </b></td>
   <td><b>Turnover </b></td>
    <td><b>Client Name</b></td>
    <td><b>Expected Order</b></td>
    <td><b>Archived Amount</b></td>
    <td><b>Visit</b></td>
</tr>

<?php
$plan_c_r = $db->rp_getData($ctable, "executive_id,expended_order_amount", "isDelete=0 AND isActive=1 AND plan_type=3 AND $ctable_where", "", 0);

if ($plan_c_r) {
    $total_exp_amt_plan3 = 0;
    $total_odr_amt_plan3 = 0;

    while ($plan_c_d = mysqli_fetch_assoc($plan_c_r)) {

        $executive_data = $db->rp_getData("executive", "client_code, gst ,  turnover , company_name", "id='" . $plan_c_d['executive_id'] . "'");
        $row = mysqli_fetch_assoc($executive_data);
        
        $gst = $row['gst'];
        $turnover = $row['turnover'];
        $client_code = $row['client_code'];
        $company_name = $row['company_name'];

        $get_order_amount = $db->rp_getValue("orders", "SUM(grand_total)", "customer_id='" . $plan_c_d['executive_id'] . "' AND MONTH(order_date)='$selected_month' AND YEAR(order_date)='$selected_year' AND sales_id='" . $_REQUEST['sales_executive'] . "'", 0);

        $visit = $db->rp_getTotalRecord("visit", "customer_id='" . $plan_c_d['executive_id'] . "' AND MONTH(created_date)='$selected_month' AND YEAR(created_date)='$selected_year'", 0);

        $style = ($get_order_amount < $plan_c_d['expended_order_amount']) ? "background-color:#ff9999" : "";
?>

<tr>
    <td><?= $client_code ?></td>
   <td>
<?= !empty($gst) ? $gst : '' ?>
</td>

<td>
<?= !empty($turnover) ? "$turnover" : '' ?>
</td>
    <td><?= $company_name ?></td>
    <td style="text-align:right;"><?= $plan_c_d['expended_order_amount'] ?></td>
    <td style="text-align:right; <?= $style ?>"><?= $get_order_amount ?></td>
    <td style="text-align:right;"><?= $visit ?></td>
</tr>

<?php
        $total_exp_amt_plan3 += $plan_c_d['expended_order_amount'];
        $total_odr_amt_plan3 += $get_order_amount;
    }
}
?>

<tr>
    <td><b>Total</b></td>
    <td></td>
    
    
    <td></td>
    
        <td></td>
        <td style="text-align:right;"><b><?= $total_exp_amt_plan3 ?></b></td>
        <td></td>
        <td style="text-align:right;"><b><?= $total_odr_amt_plan3 ?></b></td>
</tr>
</table>
</td>

</tr>
</tbody>

        </table>
    </form>
<?php
} else {
?>
    <td colspan="9" style="text-align: center; font-size: medium;">no data Found</td>
<?php
}
?>

<?php

include("disconnect.php");
?>