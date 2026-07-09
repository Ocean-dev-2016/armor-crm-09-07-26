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
if (empty($_REQUEST['sales_executive']) || empty($_REQUEST['filter_month']) || empty($_REQUEST['filter_year'])) {
    echo '<div style="text-align: center; padding: 20px; font-size: 18px; color: red;">';
    echo 'Please select all filters';
    echo '</div>';
    exit();
}
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

$sales_name_get = $db->rp_getValue("sales_executive", "name", "isDelete=0 AND isActive=1 AND id='" . $_REQUEST['sales_executive'] . "'", 0);
if (empty($display_month_year)) {
    $months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
    $month_num = (int)$_REQUEST['filter_month'];
    $month_name = isset($months[$month_num]) ? $months[$month_num] : ' ';
    $display_month_year = $month_name . " " . $_REQUEST['filter_year'];
}

?>
<style>
    .mainDiv,
    table {
        height: auto;
        width: 100%;
        font-family: Calibri, sans-serif;
        font-style: normal;
        font-weight: 400;
        padding: 0;
        text-decoration: none;
        font-size: 10pt;
        margin: auto;
        padding: auto;
    }

    tr {
        height: 30px;
    }

    table,
    td,
    th {
        border-collapse: collapse;
        border: 1px solid #000;
    }

    td,
    th {
        padding: 5px;
    }

    th {
        border: 1px solid #595959;
        background: #f0e6cc;
    }

    .text-right {
        text-align: right;
    }

    .center {
        text-align: center;
    }

    .space {
        padding: 10px;
    }

    .no-border {
        border-bottom: 1px solid #fff;
    }

    .plan-header {
        text-align: center;
        font-weight: bold;
    }

    .header-row {
        background-color: #e6e6e6;
    }
</style>

<table>
    <!-- <caption>Monthly Order Planner</caption> -->
    <thead>
        <tr>
            <th colspan="9" style="text-align: center; font-size: medium;">Monthly Order Planner</th>
        </tr>
        <tr>
            <th colspan="8">Sales Name: <?= $sales_name_get ?></th>
            <th colspan="1">Month: <?= htmlspecialchars($display_month_year) ?></th>
        </tr>
        <tr class="header-row">
            <!-- <th rowspan="2">Sr/no.</th> -->
            <th colspan="3" class="plan-header">Plan - A</th>
            <th colspan="3" class="plan-header">Plan - B</th>
            <th colspan="3" class="plan-header">Plan - C</th>
        </tr>

    </thead>
  <tbody>
<?php $count = 1; ?>
<tr>

<!-- ================= PLAN A ================= -->
<td colspan="3" style="vertical-align: top; padding: 0;">
    <table>
        <tr>
            <td><b>Code</b></td>
           <td><b>GST Number </b></td>
            <td><b>Turnover </b></td>
            <td><b>Client Name</b></td>
            <td><b>Expected Order</b></td>
            <td><b>Achieved Amount</b></td>
            <td><b>Visit</b></td>
        </tr>

        <?php
        $total_exp_amt_plan1 = 0;
        $total_odr_amt_plan1 = 0;

        $plan_a_r = $db->rp_getData($ctable,
            "executive_id,expended_order_amount",
            "isDelete=0 AND isActive=1 AND plan_type=1 AND $ctable_where","",0);

        if ($plan_a_r){
            while ($plan_a_d = mysqli_fetch_assoc($plan_a_r)) {

                $client = mysqli_fetch_assoc(
                    $db->rp_getData("executive","client_code, gst ,turnover , company_name",
                    "isDelete=0 AND id='".$plan_a_d['executive_id']."'")
                );
                
                $gst = isset($client['gst']) ? $client['gst'] : '';
                  $turnover = isset($client['turnover']) ? $client['turnover'] : '';

                $code = isset($client['client_code']) ? $client['client_code'] : '';
                $name = isset($client['company_name']) ? $client['company_name'] : '';

                $order_amt = $db->rp_getValue("orders","SUM(grand_total)",
                    "customer_id='".$plan_a_d['executive_id']."'
                    AND MONTH(order_date)='".$selected_month."'
                    AND YEAR(order_date)='".$selected_year."'
                    AND sales_id='".$_REQUEST['sales_executive']."'",0);

                $visit = $db->rp_getTotalRecord("visit",
                    "isDelete=0 AND customer_id='".$plan_a_d['executive_id']."'
                    AND MONTH(created_date)='".$selected_month."'
                    AND YEAR(created_date)='".$selected_year."'",0);

                $style = ($order_amt < $plan_a_d['expended_order_amount'] && $plan_a_d['expended_order_amount'] > 0)
                        ? "background:#ff9999;" : "";
        ?>

        <tr>
            <td><?php echo $code; ?></td>

<td>
    <?= !empty($gst) ? $gst : '' ?>
</td>

<td>
    <?= !empty($turnover) ? "" . $turnover . "" : '' ?>
</td>
            <td><?php echo $name; ?></td>
            <td align="right"><?php echo $plan_a_d['expended_order_amount']; ?></td>
            <td align="right" style="<?php echo $style; ?>"><?php echo $order_amt; ?></td>
            <td align="right"><?php echo $visit; ?></td>
        </tr>

        <?php
                $total_exp_amt_plan1 += $plan_a_d['expended_order_amount'];
                $total_odr_amt_plan1 += $order_amt;
            }
        }
        ?>

        <tr class="header-row">
            <td colspan="4"><b>Total</b></td>
            <td align="right"><b><?php echo $total_exp_amt_plan1; ?></b></td>
             <td></td>
            <td align="right"><b><?php echo $total_odr_amt_plan1; ?></b></td>
           
        </tr>
    </table>
</td>


<!-- ================= PLAN B ================= -->
<td colspan="3" style="vertical-align: top; padding: 0;">
    <table>
        <tr>
            <td><b>Code</b></td>
           <td><b>GST Number </b></td>
            <td><b>Turnover </b></td>
            <td><b>Client Name</b></td>
            <td><b>Expected Order</b></td>
            <td><b>Achieved Amount</b></td>
            <td><b>Visit</b></td>
        </tr>

        <?php
        $total_exp_amt_plan2 = 0;
        $total_odr_amt_plan2 = 0;

        $plan_b_r = $db->rp_getData($ctable,
            "executive_id,expended_order_amount",
            "isDelete=0 AND isActive=1 AND plan_type=2 AND $ctable_where","",0);

        if ($plan_b_r){
            while ($plan_b_d = mysqli_fetch_assoc($plan_b_r)) {

                $client = mysqli_fetch_assoc(
                    $db->rp_getData("executive","client_code, gst , turnover ,company_name",
                    "isDelete=0 AND id='".$plan_b_d['executive_id']."'")
                );
                
                $turnover = isset($client['turnover']) ? $client['turnover'] : '';
                $gst = isset($client['gst']) ? $client['gst'] : '';
                $code = isset($client['client_code']) ? $client['client_code'] : '';
                $name = isset($client['company_name']) ? $client['company_name'] : '';

                $order_amt = $db->rp_getValue("orders","SUM(grand_total)",
                    "customer_id='".$plan_b_d['executive_id']."'
                    AND MONTH(order_date)='".$selected_month."'
                    AND YEAR(order_date)='".$selected_year."'
                    AND sales_id='".$_REQUEST['sales_executive']."'",0);

                $visit = $db->rp_getTotalRecord("visit",
                    "isDelete=0 AND customer_id='".$plan_b_d['executive_id']."'
                    AND MONTH(created_date)='".$selected_month."'
                    AND YEAR(created_date)='".$selected_year."'",0);

                $style = ($order_amt < $plan_b_d['expended_order_amount'] && $plan_b_d['expended_order_amount'] > 0)
                        ? "background:#ff9999;" : "";
        ?>

        <tr>
            <td><?php echo $code; ?></td>
<td>
    <?= !empty($gst) ? $gst : '' ?>
</td>

<td>
    <?= !empty($turnover) ? "" . $turnover . "" : '' ?>
</td>
            <td><?php echo $name; ?></td>
            <td align="right"><?php echo $plan_b_d['expended_order_amount']; ?></td>
            <td align="right" style="<?php echo $style; ?>"><?php echo $order_amt; ?></td>
            <td align="right"><?php echo $visit; ?></td>
        </tr>

        <?php
                $total_exp_amt_plan2 += $plan_b_d['expended_order_amount'];
                $total_odr_amt_plan2 += $order_amt;
            }
        }
        ?>

        <tr class="header-row">
            <td colspan="4"><b>Total</b></td>
            <td align="right"><b><?php echo $total_exp_amt_plan2; ?></b></td>
                 <td></td>
            <td align="right"><b><?php echo $total_odr_amt_plan2; ?></b></td>
       
        </tr>
    </table>
</td>


<!-- ================= PLAN C ================= -->
<td colspan="3" style="vertical-align: top; padding: 0;">
    <table>
        <tr>
            <td><b>Code</b></td>
            <td><b>GST Number </b></td>
            <td><b>Turnover </b></td>
            <td><b>Client Name</b></td>
            <td><b>Expected Order</b></td>
            <td><b>Achieved Amount</b></td>
            <td><b>Visit</b></td>
        </tr>

        <?php
        $total_exp_amt_plan3 = 0;
        $total_odr_amt_plan3 = 0;

        $plan_c_r = $db->rp_getData($ctable,
            "executive_id,expended_order_amount",
            "isDelete=0 AND isActive=1 AND plan_type=3 AND $ctable_where","",0);

        if ($plan_c_r){
            while ($plan_c_d = mysqli_fetch_assoc($plan_c_r)) {

                $client = mysqli_fetch_assoc(
                    $db->rp_getData("executive","client_code, gst ,turnover ,company_name",
                    "isDelete=0 AND id='".$plan_c_d['executive_id']."'")
                );
                
                $turnover = isset($client['turnover']) ? $client['turnover'] : '';
                 $gst = isset($client['gst']) ? $client['gst'] : '';

                $code = isset($client['client_code']) ? $client['client_code'] : '';
                $name = isset($client['company_name']) ? $client['company_name'] : '';

                $order_amt = $db->rp_getValue("orders","SUM(grand_total)",
                    "customer_id='".$plan_c_d['executive_id']."'
                    AND MONTH(order_date)='".$selected_month."'
                    AND YEAR(order_date)='".$selected_year."'
                    AND sales_id='".$_REQUEST['sales_executive']."'",0);

                $visit = $db->rp_getTotalRecord("visit",
                    "isDelete=0 AND customer_id='".$plan_c_d['executive_id']."'
                    AND MONTH(created_date)='".$selected_month."'
                    AND YEAR(created_date)='".$selected_year."'",0);

                $style = ($order_amt < $plan_c_d['expended_order_amount'] && $plan_c_d['expended_order_amount'] > 0)
                        ? "background:#ff9999;" : "";
        ?>

        <tr>
            <td><?php echo $code; ?></td>
                 <td>
    <?= !empty($gst) ? $gst : '' ?>
</td>

<td>
    <?= !empty($turnover) ? " " . $turnover . "" : '' ?>
</td>
            <td><?php echo $name; ?></td>
            <td align="right"><?php echo $plan_c_d['expended_order_amount']; ?></td>
            <td align="right" style="<?php echo $style; ?>"><?php echo $order_amt; ?></td>
            <td align="right"><?php echo $visit; ?></td>
        </tr>

        <?php
                $total_exp_amt_plan3 += $plan_c_d['expended_order_amount'];
                $total_odr_amt_plan3 += $order_amt;
            }
        }
        ?>

        <tr class="header-row">
            <td colspan="4"><b>Total</b></td>
            <td align="right"><b><?php echo $total_exp_amt_plan3; ?></b></td>
                 <td></td>
            <td align="right"><b><?php echo $total_odr_amt_plan3; ?></b></td>

        </tr>
    </table>
</td>

</tr>
</tbody>
</table>
<?php
include("disconnect.php");
?>