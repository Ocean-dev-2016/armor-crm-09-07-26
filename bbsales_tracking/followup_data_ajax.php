<?php
$page_id = 400;$page_slug = 'dashboard';

include("connect.php");
$ctable     = "followup";
$ctable1    = "Followup";
// print_r($_REQUEST);exit;  
// $inquiry_id = $_REQUEST['inquiry_id'];
$followup_flag = $_REQUEST['followup_flag'];
$ctable_where = "";
if ($_REQUEST['followup_flag'] == "manual_invoice_import") {
    $ctable_where .= "reference_id = '" . $_REQUEST['reference_id'] . "' AND isDelete=0  AND reference_table='manual_invoice_import'";
}else {
    $ctable_where .= "visitor_id = '" . $_REQUEST['visitor_id'] . "' AND isDelete=0 ";
}

if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
    $loginid = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
    // $loginid=$db->rp_getValue("dealer_distributor_network","sales_executive_id","isDelete=0 AND id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'",0);
    // echo $loginid;
    if ($rights['personal_flag'] == 1) {
        $ctable_where .= " AND status!=-1 AND (inquiry_assign_to='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "' OR inquiry_created_by='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "' OR user_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "' OR created_by='" . $loginid . "')";
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
                $ctable_where .= " AND status!=-1 AND (inquiry_assign_to IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR inquiry_created_by IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR user_id IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR created_by='" . $loginid . "')";
            } else {
                $ctable_where .= " AND status!=-1 AND (inquiry_assign_to IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR inquiry_created_by IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR user_id IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR created_by='" . $loginid . "')";
            }
        } else {
            $ctable_where .= " AND status!=-1";
        }
    }
} else {
    $ctable_where .= " AND status!=-1";
}


$ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "followup_date DESC", 0);
?>
<style type="text/css">
    .cc1 {
        width: auto;
        height: 300px;
        overflow-x: scroll;
        overflow-y: scroll;
        margin: 0 !important;
    }
</style>
<div class="cc1 table-scrollable table-responsive">
    <table class="table table-striped table-bordered table-hover table-responsive">
        <tbody>
            <tr>
                <?php
                if ($reference_table == "sales_executive") {
                    $company_name = "<b>" . $db->rp_getValue("executive", "company_name", "id='" . $_REQUEST['reference_id'] . "'") . "</b>" . " - " . $db->rp_getValue("executive", "cname", "id='" . $_REQUEST['reference_id'] . "'") . " - " . $db->rp_getValue("executive", "mobile_no1", "isDelete=0 AND id='" . $_REQUEST['reference_id'] . "'");
                } else if ($_REQUEST['followup_flag'] == "inquiry_followup") {
                    $company_name = "<b>INQ" . $_REQUEST['reference_id'] . "</b> - " . $db->rp_getValue("no_order_inquiry", "company_name", "id='" . $_REQUEST['reference_id'] . "'") . " - " . $db->rp_getValue("no_order_inquiry", "person_name", "id='" . $_REQUEST['reference_id'] . "'") . " - " . $db->rp_getValue("no_order_inquiry", "mobile_number", "isDelete=0 AND id='" . $_REQUEST['reference_id'] . "'");
                }
                //  echo $company_name;
                ?>
                <h4 class="pull-left"><?= $company_name ?></h4>
            </tr>
            <tr>
                <th class="fix-th1" style="width: 5%">No.</th>
                <th class="fix-th1">Response</th>
                <th class="fix-th1" style="width: 15%">Date Details</th>
                <th class="fix-th1">Sales Person Name</th>
                <!-- <th class="fix-th1">Followup Date and Time</th> -->
                <th class="fix-th1">Through - Description</th>
                <th class="fix-th1">Followup Status</th> 
            </tr>
            <?php
            if (mysqli_num_rows($ctable_r) > 0) {
                while ($ctable_d = mysqli_fetch_array($ctable_r)) {
                    $entry_type_status = array("1" => "Admin Panel", "2" => "Sales App", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
            ?>
                    <tr style="background-color: <?= $status_color_code ?>;">

                        <td><?php echo ++$count; ?></td>
                        <td><?php echo $ctable_d['response']; ?>
                            <?php
                            if ($ctable_d['status'] == 0) {
                            ?>
                                <a type="button" id="response_followup_btn" href="#FollowupResponse" data-toggle="modal" target="#FollowupResponse" class="btn btn-circle btn-sm yellow" data-mode="add" data-sales-id="<?= $ctable_d['sales_executive_id']; ?>" data-date="<?php echo date('d-m-Y H:i:s', strtotime($ctable_d['followup_date'])); ?>" data-id="<?php echo $ctable_d['id']; ?>" data-visitor_id="<?php echo $ctable_d['visitor_id']; ?>" data-followup-flag="<?php echo $followup_flag; ?>" data-next_action="<?php echo $ctable_d['next_action']; ?>"> Response</a>
                            <?php
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($ctable_d['created_date'] == "0000-00-00 00:00:00" || $ctable_d['created_date'] == "1970-01-01 00:00:00") {
                                $created_date = "";
                            } else {
                                $created_date = date('d-m-Y', strtotime($ctable_d['created_date']));
                            }
                            if ($ctable_d['followup_date'] == "0000-00-00 00:00:00") {
                                $followup_date = "";
                            } else {
                                $followup_date = date('d-m-Y', strtotime($ctable_d['followup_date']));
                            }
                            if ($ctable_d['response_date'] == "0000-00-00 00:00:00") {
                                $response_date = "";
                            } else {
                                $response_date = date('d-m-Y', strtotime($ctable_d['response_date']));
                            }
                            echo "<b>CD: </b>" . $created_date . "<br>" . "<b>FD: </b>" . $followup_date . "<br>" . "<b>RD: </b>" . $response_date;
                            ?>

                        </td>
                        <td><?= $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['user_id'] . "'"); ?></td>
                        <td>
                            <?php
                            if ($ctable_d['through'] == '1') {
                                $slug = "call";
                            } else if ($ctable_d['through'] == '2') {
                                $slug = "sms";
                            } else if ($ctable_d['through'] == '3') {
                                $slug = "email";
                            } else if ($ctable_d['through'] == '4') {
                                $slug = "Whatsapp";
                            }
                            echo "<b>" . $slug . "</b>" . " - " . $ctable_d['description'];
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($ctable_d['followup_status'] == 0) {
                                $slug = "Generate";
                            }else if ($ctable_d['followup_status'] == 1) {
                                $slug = "In Followup";
                            } else if ($ctable_d['followup_status'] == 2) {
                                $slug = "Positive";
                            } else if ($ctable_d['followup_status'] == 3) {
                                $slug = "Buy Later";
                            } else if ($ctable_d['followup_status'] == 4) {
                                $slug = "Hot";
                            }else if ($ctable_d['followup_status'] == 5) {
                                $slug = "Cold";
                            } else if ($ctable_d['followup_status'] == 6) {
                                $slug = "Warm";
                            }else if ($ctable_d['followup_status'] == 11) {
                                $slug = "Lost";
                            } else if ($ctable_d['followup_status'] == -1) {
                                $slug = "My Work";
                            } else if ($ctable_d['followup_status'] == -2) {
                                $slug = "Cancel";
                            }
                            echo "<b>" . $slug . "</b>" . " - " . $ctable_d['description'];
                            ?>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>