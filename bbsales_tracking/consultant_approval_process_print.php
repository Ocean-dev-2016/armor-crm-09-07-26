<?php
$page_id = 668;
$page_slug = 'consultant_apptoval_process_report';
/*
 * @author Ravi Patel
 */
include("connect.php");
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
            <thead>
                <tr>
                    <th colspan="14" style="text-align: center; font-size: medium;">Consultant Approval Report</th>
                </tr>
                <tr>
                    <th colspan="14">
                        Person Name: <?= $sales_name_get ?>
                    </th>
                </tr>
                <tr class="header-row">
                    <th rowspan="2">Sr No</th>
                    <th colspan="2" class="plan-header">Process - 1</th>
                    <th colspan="3" class="plan-header">Process - 2<br><small>Type Of BOQ MAKER</small></th>
                    <th colspan="2" class="plan-header">Process - 3</th>
                    <th colspan="5" class="plan-header">Process - 4</th>
                    <!-- <th></th> -->
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
                    <!-- <th></th> -->
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $db->rp_getData($ctable, "*", "isDelete=0 AND isActive=1 AND $ctable_where", "", 0);
                if ($result) {
                    $sr = 1;
                    while ($d = mysqli_fetch_assoc($result)) {
                        $purchase_date = $d['process_four_purchase_date'];
                        if ($purchase_date && $purchase_date >= date("Y-m-d")) {
                            $style = "background-color: #ffb3b3;";
                        } else {
                            $style = "";
                        }
                        $excutive_name_data = $db->rp_getValue("executive", "company_name", "isDelete=0 AND id='" . $d['process_one_executive_id'] . "'");
                ?>
                        <tr style="<?= $style ?>">
                            <td><?= $sr++ ?></td>
                            <td><?= $db->approval_type_arr[$d['process_one_approval_type']] ?: '' ?></td>
                            <td><?= $excutive_name_data ?: '' ?></td>

                            <td><?= $d['process_two_consultant_name'] ?: '' ?></td>
                            <td><?= $d['process_two_consultant_mobile'] ?: '' ?></td>
                            <td><?= $d['process_two_consultant_email'] ?: '' ?></td>

                            <td><?= $d['process_three_project_name'] ?: '' ?></td>
                            <td><?= $d['process_three_project_location'] ?: '' ?></td>

                            <td><?= $d['process_four_product_name'] ?: '' ?></td>
                            <td><?= $d['process_four_contractor_name'] ?: '' ?></td>
                            <td><?= $d['process_four_contractor_mobile'] ?: '' ?></td>
                            <td><?= $d['process_four_contractor_email'] ?: '' ?></td>
                            <td>
                                <?= (!empty($d['process_four_purchase_date']) && $d['process_four_purchase_date'] != '0000-00-00')
                                    ? date('d-m-Y', strtotime($d['process_four_purchase_date']))
                                    : '' ?>
                            </td>

                            <!-- <?php if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) { ?>
                                <td>
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
                            <?php } ?> -->
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='13' style='text-align:center;'>No Records Found</td></tr>";
                }
                ?>
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