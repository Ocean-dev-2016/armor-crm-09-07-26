<?php
$page_id = 668;
$page_slug = 'consultant_apptoval_process_report';
/*
 * @author Ravi Patel
 */
include("connect.php");
require_once("../include/consultant_approval_process_helper.php");
$ctable     = "sales_vs_consultant_approval_process";
$ctable1    = "sales_vs_consultant_approval_process";

$ctable_where = "isDelete=0";
// Get the total number of rows in the table
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




if (
    isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL &&
    isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL
) {
    $ctable_where = "process_four_purchase_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND process_four_purchase_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "'";
} else if (
    isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive'] != "" && $_REQUEST['sales_executive'] != "null"
) {
    $ctable_where .= " AND process_one_sales_executive_id = " . $_REQUEST['sales_executive'];
}

if (isset($_REQUEST['approval_type']) && $_REQUEST['approval_type'] != "" && $_REQUEST['approval_type'] != "null") {
    $approval_type = (int) $_REQUEST['approval_type'];
    if ($approval_type > 0) {
        $ctable_where .= " AND process_one_approval_type = '" . $approval_type . "'";
    }
}


if (
    (empty($_REQUEST['sales_executive']) || $_REQUEST['sales_executive'] == "null") &&
    (empty($_REQUEST['FromDate']) || empty($_REQUEST['ToDate']))
) {
    echo '<div style="text-align: center; padding: 20px; font-size: 18px; color: red;">';
    echo 'Please select all filters';
    echo '</div>';
    include("disconnect.php");
    exit();
}


if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
    if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 2) //sales executive and its chain wise order
    {
        if ($rights['personal_flag'] == 1) {
            $check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
            $ctable_where .= " AND process_one_sales_executive_id='" . $check_id . "' ";
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



$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 100;

$get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where, 0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows / $item_per_page);

//get starting position to fetch the records
$page_position = (($page_number - 1) * $item_per_page);
$ctable_r = $db->rp_getData($ctable, "*", "", 0);

if ($ctable_r) {
    while ($row = mysqli_fetch_assoc($ctable_r)) {
        $sales_name_get = $db->rp_getValue("sales_executive", "name", "isDelete=0 AND isActive=1 AND id='" . $_REQUEST['sales_executive'] . "'", 0);
    }

?>
    <style>
        .consultant-report-wrap {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            margin-bottom: 20px;
        }

        .consultant-report-table {
            width: 100%;
            min-width: 1500px;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .consultant-report-table th,
        .consultant-report-table td {
            border: 1px solid #333;
            padding: 8px 6px;
            text-align: left;
            vertical-align: top;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        .consultant-report-table th {
            background-color: #f2f2f2;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
        }

        .consultant-report-table td.col-num,
        .consultant-report-table th.col-num {
            text-align: center;
            vertical-align: middle;
        }

        .consultant-report-table .plan-header {
            text-align: center;
            font-weight: bold;
        }

        .consultant-report-table .header-row {
            background-color: #e6e6e6;
        }

        .consultant-report-table .report-title th {
            font-size: 16px;
            background: #3598dc;
            color: #fff;
        }

        .consultant-report-table .report-person th {
            background: #f5f5f5;
            text-align: left;
            font-size: 13px;
        }
    </style>
    <?php echo consultant_approval_process_styles(); ?>

    <form action="" id="print_info" name="frm" method="post">
        <div class="consultant-report-wrap">
        <table class="consultant-report-table">
            <colgroup>
                <col style="width:3%;">
                <col style="width:6%;">
                <col style="width:5%;">
                <col style="width:6%;">
                <col style="width:9%;">
                <col style="width:9%;">
                <col style="width:7%;">
                <col style="width:7%;">
                <col style="width:8%;">
                <col style="width:7%;">
                <col style="width:7%;">
                <col style="width:6%;">
                <col style="width:6%;">
                <col style="width:6%;">
                <col style="width:7%;">
                <col style="width:6%;">
                <col style="width:4%;">
            </colgroup>
            <thead>
                <tr class="report-title">
                    <th colspan="17">Consultant Approval Report</th>
                </tr>
                <tr class="report-person">
                    <th colspan="17">
                        Person Name: <?= $sales_name_get ?>
                    </th>
                </tr>
                <tr class="header-row">
                    <th rowspan="2" class="col-num">Sr No</th>
                    <th rowspan="2" class="col-num">Date</th>
                    <th rowspan="2" class="col-num">Total Visit</th>
                    <th rowspan="2" class="col-num">Customer Wise<br>Visit Count</th>
                    <th colspan="2" class="plan-header">Process - 1</th>
                    <th colspan="3" class="plan-header">Process - 2<br><small>Type Of BOQ MAKER</small></th>
                    <th colspan="2" class="plan-header">Process - 3</th>
                    <th colspan="5" class="plan-header">Process - 4</th>
                    <th rowspan="2"></th>
                </tr>
                <tr class="header-row">
                    <th>Approval Type</th>
                    <th>Customer Name</th>
                    <th>Name</th>
                    <th>Number</th>
                    <th>Mail ID</th>
                    <th>Project Name</th>
                    <th>Project Location</th>
                    <th>Product Name</th>
                    <th>Contractor Office Name</th>
                    <th>Contractor Office Mobile</th>
                    <th>Contractor Office Email</th>
                    <th>Purchase Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $db->rp_getData($ctable, "*", "isDelete=0 AND isActive=1 AND $ctable_where", "", 0);
                if ($result) {
                    $sr = 1;
                    while ($d = mysqli_fetch_assoc($result)) {
                        $purchase_date = $d['process_four_purchase_date'];
                        if ($purchase_date && $purchase_date <= date("Y-m-d")) {
                            $style = "background-color: #ffb3b3;";
                        } else {
                            $style = "";
                        }
                        $excutive_name_data = $db->rp_getValue("executive", "company_name", "isDelete=0 AND id='" . $d['process_one_executive_id'] . "'");

                        $entry_date = '';
                        if (!empty($d['created_date']) && $d['created_date'] != '0000-00-00 00:00:00') {
                            $entry_date = date('d-m-Y', strtotime($d['created_date']));
                        } elseif (!empty($d['modified_date']) && $d['modified_date'] != '0000-00-00 00:00:00') {
                            $entry_date = date('d-m-Y', strtotime($d['modified_date']));
                        }

                        $customer_id = (int) $d['process_one_executive_id'];
                        $sales_id = (int) $d['process_one_sales_executive_id'];
                        $customer_wise_visit_count = 0;
                        $total_visit = 0;
                        if ($customer_id > 0) {
                            $customer_wise_visit_count = (int) $db->rp_getTotalRecord("visit", "isDelete=0 AND customer_id='" . $customer_id . "'", 0);
                            if ($sales_id > 0) {
                                $total_visit = (int) $db->rp_getTotalRecord("visit", "isDelete=0 AND customer_id='" . $customer_id . "' AND user_id='" . $sales_id . "'", 0);
                            }
                        }
                        $projectCells = consultant_approval_render_project_cells(
                            isset($d['process_three_project_name']) ? $d['process_three_project_name'] : '',
                            isset($d['process_three_project_location']) ? $d['process_three_project_location'] : ''
                        );
                ?>
                        <tr style="<?= $style ?>">
                            <td class="col-num"><?= $sr++ ?></td>
                            <td class="col-num"><?= $entry_date ?></td>
                            <td class="col-num"><?= $total_visit ?></td>
                            <td class="col-num"><?= $customer_wise_visit_count ?></td>
                            <td><?= $db->approval_type_arr[$d['process_one_approval_type']] ?: '' ?></td>
                            <td><?= $excutive_name_data ?: '' ?></td>

                            <td><?= $d['process_two_consultant_name'] ?: '' ?></td>
                            <td><?= $d['process_two_consultant_mobile'] ?: '' ?></td>
                            <td><?= $d['process_two_consultant_email'] ?: '' ?></td>

                            <td class="consultant-project-cell"><?= $projectCells['name_html'] ?></td>
                            <td class="consultant-project-cell"><?= $projectCells['location_html'] ?></td>

                            <td><?= $d['process_four_product_name'] ?: '' ?></td>
                            <td><?= $d['process_four_contractor_name'] ?: '' ?></td>
                            <td><?= $d['process_four_contractor_mobile'] ?: '' ?></td>
                            <td><?= $d['process_four_contractor_email'] ?: '' ?></td>
                            <td class="col-num">
                                <?= (!empty($d['process_four_purchase_date']) && $d['process_four_purchase_date'] != '0000-00-00')
                                    ? date('d-m-Y', strtotime($d['process_four_purchase_date']))
                                    : '' ?>
                            </td>

                            <?php if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) { ?>
                                <td class="col-num">
                                    <div class="btn-group">
                                        <button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
                                            <i class="fa fa-gear"></i>
                                        </button>
                                        <ul role="menu" class="dropdown-menu">
                                            <li>
                                                <a onClick="del_conf('<?= $d['id'] ?>');" title="Delete">
                                                    <span class="text-danger"><i class="fa fa-times"></i> &nbsp;Delete</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a onClick="status_conf('<?= $d['id'] ?>');" title="Delete">
                                                    <span class="text-success"><i class="fa fa-status"></i> &nbsp;Complate</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            <?php } ?>
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='17' style='text-align:center;'>No Records Found</td></tr>";
                }
                ?>
            </tbody>
        </table>
        </div>
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