<?php
$page_id = 583;
$page_slug = 'page_followup';
include("connect.php");
$ctable     = "followup";
$ctable1    = "followup";
$isFillter = filter_var($_REQUEST['isFillter'], FILTER_VALIDATE_BOOLEAN);

$ctable_where = "";
// Get the total number of rows in the table

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
    $ctable_where .= "(";
    $phone_id = array();
    $exe_ids_r = $db->rp_getData("executive", "*", "mobile_no1 LIKE '%" . trim($_REQUEST['searchName']) . "%' ", "", 0);
    if ($exe_ids_r) {
        while ($exe_id_d = mysqli_fetch_assoc($exe_ids_r)) {
            $phone_id[] = $exe_id_d['id'];
        }
        $phone_no_id = implode(",", $phone_id);
        $ctable_where .= "visitor_id IN (" . $phone_no_id . ") ";
    }
    $exe_ids_r1 = $db->rp_getData("no_order_inquiry", "*", "mobile_number LIKE '%" . trim($_REQUEST['searchName']) . "%' ", "", 0);
    if ($exe_ids_r1) {
        if ($phone_id) {
            $ctable_where .= " OR ";
        }
        while ($exe_id_d1 = mysqli_fetch_assoc($exe_ids_r1)) {
            $inqArr[] = $exe_id_d1['id'];
        }

        $inqids = implode(",", $inqArr);
        $ctable_where .= " reference_id IN (" . $inqids . ")";
    }
    $ctable_where .= ") AND ";
    $isFillter = true;
}
// echo $ctable_where;exit;

if ($_REQUEST['df'] != "" && $_REQUEST['df'] != undefined) {
    if (isset($_REQUEST['df']) && $_REQUEST['df'] != "" && $_REQUEST['df'] != NULL && $_REQUEST['df'] != undefined) {
        //echo $_REQUEST['df'];exit;
        $date_filter_query = urldecode($_REQUEST['df']);
        $date_filter_query_ex = explode(" to ", $date_filter_query);
        $ctable_where .= " ( DATE(followup_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(followup_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) AND ";
        $isFillter = true;
    }
}
if ($_REQUEST['df'] != "" && $_REQUEST['df'] != undefined) {
    if (isset($_REQUEST['df']) && $_REQUEST['df'] != "" && $_REQUEST['df'] != NULL && $_REQUEST['df'] != undefined) {
        $date_filter_query = urldecode($_REQUEST['df']);
        $date_filter_query_ex = explode(" to ", $date_filter_query);
        $ctable_where .= " (DATE(followup_date) >= '" . date("Y-m-d", strtotime($date_filter_query_ex[0])) . "' AND DATE(followup_date) <= '" . date("Y-m-d", strtotime($date_filter_query_ex[1])) . "') AND ";
        $isFillter = true;
    }
} elseif ($_REQUEST['df1'] != "" && $_REQUEST['df1'] != undefined) {
    if (isset($_REQUEST['df1']) && $_REQUEST['df1'] != "" && $_REQUEST['df1'] != NULL && $_REQUEST['df1'] != undefined) {
        $date_filter_query_1 = urldecode($_REQUEST['df1']);
        $date_filter_query_1_ex = explode(" to ", $date_filter_query_1);
        $ctable_where .= " (DATE(created_date) >= '" . date("Y-m-d", strtotime($date_filter_query_1_ex[0])) . "' AND DATE(created_date) <= '" . date("Y-m-d", strtotime($date_filter_query_1_ex[1])) . "') AND ";
        $isFillter = true;
    }
} elseif ($_REQUEST['df2'] != "" && $_REQUEST['df2'] != undefined) {

    // echo $_REQUEST['df2'];exit;
    if (isset($_REQUEST['df2']) && $_REQUEST['df2'] != "" && $_REQUEST['df2'] != NULL && $_REQUEST['df2'] != undefined) {
        $date_filter_query_2 = urldecode($_REQUEST['df2']);
        $date_filter_query_2_ex = explode(" to ", $date_filter_query_2);
        $ctable_where .= " (DATE(response_date) >= '" . date("Y-m-d", strtotime($date_filter_query_2_ex[0])) . "' AND DATE(response_date) <= '" . date("Y-m-d", strtotime($date_filter_query_2_ex[1])) . "') AND ";
        $isFillter = true;
    }
} else {
    if ($_REQUEST['followup_type'] == "today") {
        $today = date('Y-m-d');
        $ctable_where .= "DATE(followup_date) = '" . $today . "' AND ";
        $isFillter = true;
    }

    if ($_REQUEST['followup_type'] == "future") {
        $future = date('Y-m-d');
        $ctable_where .= "DATE(followup_date) > '" . $future . "' AND response = '' AND ";
        $isFillter = true;
    }

    if ($_REQUEST['followup_type'] == "pending") {
        $today = date('Y-m-d');
        $ctable_where .= "DATE(followup_date) < '" . $today . "' AND response = '' AND ";
        $isFillter = true;
    }

    if ($_REQUEST['followup_type'] == "today,pending") {
        $today = date('Y-m-d');
        $ctable_where .= "DATE(followup_date) <= '" . $today . "' AND response = '' AND ";
        $isFillter = true;
    }

    if ($_REQUEST['followup_type'] == "today,future") {
        $today = date('Y-m-d');
        $ctable_where .= "DATE(followup_date) >= '" . $today . "' AND response = '' AND ";
        $isFillter = true;
    }

    if ($_REQUEST['followup_type'] == "future,pending") {
        $today = date('Y-m-d');
        $ctable_where .= "(DATE(followup_date) < '" . $today . "' OR DATE(followup_date) > '" . $today . "') AND response = '' AND ";
        $isFillter = true;
    }

    if ($_REQUEST['followup_type'] == "all") {
        $isFillter = true;
    }

    if ($_REQUEST['followup_type'] == "responsed") {
        $ctable_where .= "response != '' AND ";
        $isFillter = true;
    }
}


if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined && $_REQUEST['todate'] != "01-01-1970") {
    $ctable_where .= "  DATE(followup_date) >= '" . $_REQUEST['todate'] . "' AND ";
    $isFillter = true;
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined && $_REQUEST['fromdate'] != "01-01-1970") {
    $ctable_where .= "  DATE(followup_date) <= '" . $_REQUEST['fromdate'] . "' AND ";
    $isFillter = true;
}


if (isset($_REQUEST['reference_media_id']) && $_REQUEST['reference_media_id'] != "") {
    $ctable_where .= " refrence_media_id='" . $_REQUEST['reference_media_id'] . "' AND";
    $isFillter = true;
}

if (isset($_REQUEST['executive']) && $_REQUEST['executive'] != "" && $_REQUEST['executive'] != NULL) {
    $ctable_where .= " visitor_id= '" . $_REQUEST['executive'] . "' AND ";
    $isFillter = true;
}

if (isset($_REQUEST['through']) && $_REQUEST['through'] != "" && $_REQUEST['through'] != NULL) {
    $ctable_where .= "through= '" . $_REQUEST['through'] . "' AND ";
    $isFillter = true;
}

if (isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive'] != "" && $_REQUEST['sales_executive'] != NULL && $_REQUEST['sales_executive'] != undefined) {
    $ctable_where .= " user_id= '" . $_REQUEST['sales_executive'] . "' AND ";
    $sales_executive = $_REQUEST['sales_executive'];
    $isFillter = true;
}

if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
    $loginid = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
    if ($rights['personal_flag'] == 1) {
        $ctable_where .= " (inquiry_assign_to='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "' OR inquiry_created_by='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "' OR user_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "' OR created_by='" . $loginid . "') AND ";
        $sales_executive = $_REQUEST['sales_executive'];
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
                // echo "d0";
                $SALEID1 = implode(",", $SALEID1);
                $ctable_where .= " status!=-1 AND (inquiry_assign_to IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR inquiry_created_by IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR user_id IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR created_by='" . $loginid . "') AND ";
            } else {
                // echo "der0";
                $ctable_where .= " status!=-1 AND (inquiry_assign_to IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR inquiry_created_by IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR user_id IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR created_by='" . $loginid . "') AND ";
            }
        }
        // else
        // {

        // }
    }
}

// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
//     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
//     $ctable_where .= "user_id='".$check_id."' AND ";
// }

//service_executive user not show condition start --//
$CID = array();
$SEID = array();
$sales_type_r = $db->rp_getData("sales_executive", "*", "type='service_executive'", "", 0);
while ($sales_type_d = mysqli_fetch_array($sales_type_r)) {
    $SEID[] = $sales_type_d['id'];
}
$SEID = implode(",", $SEID);
if ($SEID) {
    $ctable_where .= " user_id NOT IN ('" . $SEID . "') AND ";
}
//service_executive user not show condition end --//

$ctable_where .= " isDelete=0";
$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 100;
if (isset($_REQUEST["page"])) {
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if (!is_numeric($page_number)) {
        die('Invalid page number!');
    } //incase of invalid page number
} else {
    $page_number = 1; //if there's no page number, set it to 1
}

if ($isFillter) {
    $get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where, 0); //hold total records in variable
    //break records into pages
    $total_pages = ceil($get_total_rows / $item_per_page);

    //get starting position to fetch the records
    $page_position = (($page_number - 1) * $item_per_page);

    $ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "id DESC limit $page_position, $item_per_page", 0);
}
?>
<style type="text/css">
    #portal-drivers {
        width: auto;
        height: 600px;
        overflow-x: scroll;
        overflow-y: scroll;
        border: 1px solid #e7ecf1;
        margin: 10px 0 !important;
    }

    .fix-th {
        background-color: #f5f5f5 !important;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .fix-th1 {
        background-color: #e5e5e5 !important;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table-scrollable {
        width: auto;
        height: 450px;
        overflow-x: scroll;
        overflow-y: scroll;
        border: 1px solid #e7ecf1;
        margin: 10px 0 !important;
    }
</style>
<div id="portal-drivers table-scrollable">
    <table id="datatable_1" class="table table-striped table-bordered table-hover table">
        <thead class="fix-th">
            <tr>
                <?php
                if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
                ?>
                    <th></th>
                <?php
                }
                ?>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>
                    <?php
                    if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
                    ?>
                        <select class="form-control" name="sales_executive" id="sales_executive" onchange="searchBysalesExecutive(this.value);">
                            <option value="">Select Sales Person</option>
                            <?php
                            $executives_r = $db->rp_getData("sales_executive", "*", "isDelete=0 AND type!='service_executive' AND isActive=1 ", "", 0);
                            if ($executives_r) {
                                while ($executive_d = mysqli_fetch_array($executives_r)) {
                            ?>
                                    <option value="<?php echo $executive_d['id']; ?>" <?= ($sales_executive == $executive_d['id']) ? "selected" : ""; ?>><?php echo $executive_d['name']; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    <?php
                    }
                    ?>
                </th>

                <th>
                    <div class="input-group">
                        <input class="form-control datetimerange-picker-input-1" id="material_request_filter_input_1" value="<?php echo $date_filter_query_1; ?>" name="df1" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
                        <span class="input-group-addon datetimerange-picker-btn-1">
                            <i class="fa fa-calendar"></i>
                        </span>

                        <span class="input-group-btn">
                            <!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
                        </span>
                    </div>
                    <button class="btn btn-success filterBtn_1" type="submit" value="search">Filter</button>
                </th>
                <th>
                    <div class="input-group">
                        <input class="form-control datetimerange-picker-input" id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
                        <span class="input-group-addon datetimerange-picker-btn">
                            <i class="fa fa-calendar"></i>
                        </span>

                        <span class="input-group-btn">
                            <!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
                        </span>
                    </div>
                    <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
                </th>
                <th></th>
                <th>
                    <select class="form-control input-small" id="through" name="through">
                        <option value="">Select Status</option>
                        <option value="1" <?= ("1" == $_REQUEST['through']) ? "selected" : ""; ?>>call</option>
                        <option value="2" <?= ("2" == $_REQUEST['through']) ? "selected" : ""; ?>>sms</option>
                        <option value="3" <?= ("3" == $_REQUEST['through']) ? "selected" : ""; ?>>email</option>
                        <option value="4" <?= ("4" == $_REQUEST['through']) ? "selected" : ""; ?>>Whatsapp</option>
                        <option value="5" <?= ("5" == $_REQUEST['through']) ? "selected" : ""; ?>>Visit</option>
                    </select>
                </th>
                <th></th>
                <th></th>
                <th>
                    <div class="input-group">
                        <input class="form-control datetimerange-picker-input-2" id="material_request_filter_input_2" value="<?php echo $date_filter_query_2; ?>" name="df2" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
                        <span class="input-group-addon datetimerange-picker-btn-2">
                            <i class="fa fa-calendar"></i>
                        </span>

                        <span class="input-group-btn">
                            <!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
                        </span>
                    </div>
                    <button class="btn btn-success filterBtn_2" type="submit" value="search">Filter</button>
                </th>
                <th></th>
                <!-- <th></th> -->
                <!-- <th></th> -->
                <!-- <th></th> -->
            </tr>
            <tr>
                <?php
                if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
                ?>
                    <th></th>
                <?php
                }
                ?>
                <th class="fix-th1" style="width: 5%">No.</th>
                <th class="fix-th1">Inq No / <br />Bill No.</th>
                <th class="fix-th1">Customer Name</th>
                <th class="fix-th1">Person Name</th>
                <th class="fix-th1">Client Code</th>
                <th class="fix-th1">Mobile No</th>
                <th class="fix-th1">Sales Person Name</th>
                <th class="fix-th1">Created Date and Time</th>
                <th class="fix-th1">Followup Date and Time</th>
                <th class="fix-th1">Description</th>
                <th class="fix-th1">Through</th>
                <th class="fix-th1">Type of Follow up</th>
                <th class="fix-th1">Entry Type</th>
                <th class="fix-th1">Response Date</th>
                <th class="fix-th1">Response</th>
                <!-- <th>Response Entry Type</th> -->
                <!-- <th>Response Entry Update Type</th> -->

                <!-- <th>Update Entry Type</th> -->
            </tr>
        </thead>
        <tbody>
            <?php
            if ($isFillter) {
                if (mysqli_num_rows($ctable_r) > 0) {
                    $count = 0;
                    $entry_type_status = array("1" => "Admin Panel", "2" => "Sales App", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
                    $response_entry_type_status = array("1" => "Admin Panel", "2" => "Sales App", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
                    // $msg = array("1"=>"Call","2"=>"Sms","3"=>"Email");
                    while ($ctable_d = mysqli_fetch_array($ctable_r)) {
                        //$followupdate = date('d-m-Y',strtotime($ctable_d['followup_date']));
                        //$responsedate = date('d-m-Y',strtotime($ctable_d['response_date']));

            ?>
                        <tr>
                            <?php
                            if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
                            ?>
                                <td><?php $ctable_d['id']; ?>
                                    <div class="btn-group">
                                        <button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle"> <i class="fa fa-gear"></i>
                                        </button>
                                        <ul role="menu" class="dropdown-menu">
                                            <?php
                                            /*if($rights['delete_flag']==1 && $ctable_d['id']!=-1)
                                {*/
                                            ?>
                                            <li>
                                                <a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><span class="text-danger"><i class="fa fa-times"></i> &nbsp;Delete</span></a>
                                            </li>
                                            <?php
                                            // }
                                            ?>
                                        </ul>
                                    </div>
                                </td>
                            <?php
                            }
                            ?>
                            <td><?php echo ++$count; ?> <?php $ctable_d['reference_table']; ?></td>
                            <?php
                            if ($ctable_d['reference_table'] == "no_order_inquiry") {
                            ?>
                                <td><a target="_blank" href="followup.php?mode=inquiry_followup&inquiry_id=<?= $ctable_d['reference_id'] ?>&sales_id=<?= $ctable_d['user_id'] ?>"><?php echo "INQ/" . $ctable_d['reference_id']; ?></a></td>
                            <?php
                            } else if ($ctable_d['reference_table'] == "manual_invoice_import") {
                            ?>
                                <td><?= $db->rp_getValue("manually_invoice_outstanding_import", "bill_no", "id='" . $ctable_d['reference_id'] . "' AND isDelete=0"); ?></td>
                            <?php
                            } else {
                            ?>
                                <td></td>
                            <?php
                            }
                            ?>
                            <td>
                                <?php
                                $company_name = "";
                                $person_name = "";
                                $mobile_no = "";
                                $client_code = "";
                                $customer_flag_text = "";
                                if ($ctable_d['reference_table'] == "sales_executive") {
                                    $get_customer_r = $db->rp_getData("executive", "company_name,cname,mobile_no1,client_code,customer_flag", "id='" . $ctable_d['visitor_id'] . "'");
                                    $get_customer_d = mysqli_fetch_assoc($get_customer_r);

                                    if ($get_customer_d['customer_flag'] == 1) {
                                        $customer_flag_text = " - P";
                                    } else if ($get_customer_d['customer_flag'] == 0) {
                                        $customer_flag_text = " - C";
                                    }
                                    $followup_flag = "followup";
                                    $company_name = "<b>" . $get_customer_d['company_name'] . "</b>";
                                    $person_name = $get_customer_d['cname'];
                                    $client_code = $get_customer_d['client_code'];
                                    $mobile_no = $get_customer_d['mobile_no1'];
                                } else if ($ctable_d['reference_table'] == "quotation_detail") {
                                    $get_customer_r = $db->rp_getData("quotation_detail", "company_name,customer_name,client_code,customer_id,customer_flag", "id='" . $ctable_d['reference_id'] . "'");
                                    $get_customer_d = mysqli_fetch_assoc($get_customer_r);
                                    if ($get_customer_d['customer_flag'] == 1) {
                                        $customer_flag_text = " - P";
                                    } else if ($get_customer_d['customer_flag'] == 0) {
                                        $customer_flag_text = " - C";
                                    }
                                    $followup_flag = "quotation_followup";
                                    $company_name = "<b>" . $get_customer_d['company_name'] . "</b>";
                                    $person_name = $get_customer_d['customer_name'];
                                    $client_code = $get_customer_d['client_code'];
                                    $mobile_no = $db->rp_getValue("executive", "mobile_no1", "isDelete=0 AND id = '" . $get_customer_d['customer_id'] . "'  ");
                                } else if ($ctable_d['reference_table'] == "manual_invoice_import") {
                                    $followup_flag = "manual_invoice_import";
                                    $customer_id = $db->rp_getValue("manually_invoice_outstanding_import", "customer_id", "id='" . $ctable_d['reference_id'] . "'");

                                    $get_customer_r = $db->rp_getData("executive", "company_name,cname,mobile_no1,client_code", "id='" . $customer_id . "'");
                                    $get_customer_d = mysqli_fetch_assoc($get_customer_r);

                                    if ($get_customer_d['customer_flag'] == 1) {
                                        $customer_flag_text = " - P";
                                    } else if ($get_customer_d['customer_flag'] == 0) {
                                        $customer_flag_text = " - C";
                                    }

                                    $company_name = "<b>" . $get_customer_d['company_name'] . "</b>";
                                    $person_name = $get_customer_d['cname'];
                                    $client_code = $get_customer_d['client_code'];
                                    $mobile_no = $get_customer_d['mobile_no1'];
                                } else if ($ctable_d['reference_table'] == "no_order_inquiry") {
                                    $followup_flag = "inquiry_followup";
                                    $company_name = $db->rp_getValue("no_order_inquiry", "company_name", "id='" . $ctable_d['reference_id'] . "'");
                                    $person_name = $db->rp_getValue("no_order_inquiry", "person_name", "id='" . $ctable_d['reference_id'] . "'");
                                    $mobile_no = $db->rp_getValue("no_order_inquiry", "mobile_number", "id='" . $ctable_d['reference_id'] . "'");
                                } else if ($ctable_d['reference_table'] == "customer_inquiry") {
                                    $followup_flag = "leads_followup";
                                    $company_name = $db->rp_getValue("customer_inquiry", "company_name", "id='" . $ctable_d['reference_id'] . "'");
                                    $person_name = $db->rp_getValue("no_order_inquiry", "person_name", "id='" . $ctable_d['reference_id'] . "'");
                                } else if ($ctable_d['reference_table'] == "executive") {
                                    $get_customer_r = $db->rp_getData("executive", "company_name,cname,mobile_no1,client_code,customer_flag", "id='" . $ctable_d['reference_id'] . "'");
                                    $get_customer_d = mysqli_fetch_assoc($get_customer_r);

                                    if ($get_customer_d['customer_flag'] == 1) {
                                        $customer_flag_text = " - P";
                                    } else if ($get_customer_d['customer_flag'] == 0) {
                                        $customer_flag_text = " - C";
                                    }

                                    $followup_flag = "customer_followup";
                                    $company_name = $get_customer_d['company_name'];
                                    $person_name = $get_customer_d['cname'];
                                    $client_code = $get_customer_d['client_code'];
                                    $mobile_no = $get_customer_d['mobile_no1'];
                                }
                                echo $company_name . $customer_flag_text;
                                ?>
                            </td>
                            <td><?= $person_name ?></td>
                            <td><?= $client_code ?></td>
                            <td> <?php echo $mobile_no; ?></td>
                            <td><?php echo $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['user_id'] . "'"); ?></td>
                            <?php if ($ctable_d['created_date'] == "0000-00-00 00:00:00" || $ctable_d['created_date'] == "1970-01-01 00:00:00") { ?>
                                <td></td>
                            <?php
                            } else { ?>
                                <td><?php echo date('d-m-Y h:i A', strtotime($ctable_d['created_date'])); ?></td>
                            <?php } ?>
                            <?php if ($ctable_d['followup_date'] == "0000-00-00 00:00:00" || $ctable_d['followup_date'] == "1970-01-01 00:00:00") { ?>
                                <td></td>
                            <?php
                            } else { ?>
                                <td><?php echo date('d-m-Y h:i A', strtotime($ctable_d['followup_date'])); ?></td>
                            <?php } ?>
                            <td><?php echo $ctable_d['description']; ?></td>
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
                                } else if ($ctable_d['through'] == '5') {
                                    $slug = "Visit";
                                }

                                echo $slug;
                                ?>
                            </td>

                            <td>
                                <?php
                                if ($ctable_d['reference_table'] == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry", "id", "id='" . $ctable_d['reference_id'] . "' AND inquiry_lead_flag = '0'", 0)) {
                                    $slagf = "Inquiry";
                                } else if ($ctable_d['reference_table']  == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry", "id", "id='" . $ctable_d['reference_id'] . "' AND inquiry_lead_flag = '-1'", 0)) {
                                    $slagf = "Prospects";
                                } else if ($ctable_d['reference_table']  == "no_order_inquiry" &&  $db->rp_getValue("no_order_inquiry", "id", "id='" . $ctable_d['reference_id'] . "' AND inquiry_lead_flag = '1'", 0)) {
                                    $slagf = "Leads";
                                } else if ($ctable_d['reference_table'] == "sales_executive") {
                                    $slagf = "Sales Executive";
                                } else if ($ctable_d['reference_table'] == "manual_invoice_import") {
                                    $slagf = "Manually Invoice Import";
                                } else if ($ctable_d['reference_table'] == "customer_inquiry") {

                                    $slagf = "Customer Inquiry";
                                } else if ($ctable_d['reference_table'] == "quotation_followup" || $ctable_d['reference_table'] == "quotation_detail") {
                                    $slagf = "Quotation";
                                } else if ($ctable_d['reference_table'] == "executive" || $ctable_d['reference_table'] == "customer_inquiry") {
                                    $slagf = "Customer";
                                }
                                echo $slagf;
                                ?>
                            </td>

                            <td><?php echo $entry_type_status[$ctable_d['entry_type']]; ?></td>


                            <!-- responsedate-->
                            <?php if ($ctable_d['response_date'] == "0000-00-00 00:00:00") { ?>
                                <td></td>
                            <?php
                            } else { ?>
                                <td><?php echo date('d-m-Y', strtotime($ctable_d['response_date'])); ?></td>
                            <?php } ?>
                            <!-- responsedate-->
                            <td><?php echo $ctable_d['response']; ?>
                                <?php if ($ctable_d['status'] == 0) {
                                ?>
                                    <a type="button" href="#FollowupResponse" data-toggle="modal" target="#FollowupResponse" class="btn btn-circle btn-sm yellow" data-mode="add" data-followup-flag="<?= $followup_flag; ?>" data-sales-id="<?= $ctable_d['user_id']; ?>" data-visitor_id="<?= $ctable_d['visitor_id'] ?>" data-date="<?php echo date('d-m-Y H:i:s', strtotime($ctable_d['followup_date'])); ?>" data-id="<?php echo $ctable_d['id']; ?>" data-ref_id="<?php echo $ctable_d['reference_id']; ?>" data-next_action="<?php echo $ctable_d['next_action']; ?>">Response</a>
                                <?php } ?>
                            </td>

                            <!-- <td><?php echo $response_entry_type_status[$ctable_d['response_entry_flag']]; ?></td>  -->
                            <!-- <td><?php echo $response_entry_type_status[$ctable_d['response_update_flag']]; ?></td>  -->

                            <!-- <td><?php echo $entry_type_status[$ctable_d['update_entry_flag']]; ?></td> -->
                        </tr>
                <?php
                    }
                }
            } else {
                ?>
                <tr>
                    <td class="text-center" colspan="14">
                        <h3><strong><?= FILTER_INFO ?></strong></h3>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

</div>
<div class="row">
    <div class="col-md-6">
        <div class="dataTables_info"> Rows Limit:
            <select id="numRecords" onChange="changeDisplayRowCount(this.value);">
                <option value="50" <?php if ($_REQUEST["show"] == 50 || $_REQUEST["show"] == "") {
                                        echo ' selected="selected"';
                                    }  ?>>50</option>
                <option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "") {
                                        echo ' selected="selected"';
                                    }  ?>>100</option>
                <option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {
                                        echo ' selected="selected"';
                                    }  ?>>500</option>
                <option value="1000" <?php if ($_REQUEST["show"] == 1000) {
                                            echo ' selected="selected"';
                                        }  ?>>1000</option>
                <option value="2000" <?php if ($_REQUEST["show"] == 2000) {
                                            echo ' selected="selected"';
                                        }  ?>>2000</option>
                <option value="5000" <?php if ($_REQUEST["show"] == 5000) {
                                            echo ' selected="selected"';
                                        }  ?>>5000</option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="dataTables_paginate paging_simple_numbers">
            <ul class="pagination">
                <?php
                echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
                ?>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#sales_executive").select2();
    $("#through").select2();
</script>
<script type="text/javascript">
    $(".filterBtn").on("click", function() {
        df = $("#material_request_filter_input").val();
        // alert(df);
        df = encodeURI(df)
        displayRecords(100, 1);
    })
    $(".filterBtn_1").on("click", function() {
        df1 = $("#material_request_filter_input_1").val();
        // alert(df);
        df1 = encodeURI(df1)
        displayRecords(100, 1);
    })
    $(".datetimerange-picker-btn").on("click", function() {
        $(".datetimerange-picker-input", $(this).closest(".date")).focus();
    });
    $(".datetimerange-picker-btn-1").on("click", function() {
        $(".datetimerange-picker-input-1", $(this).closest(".date")).focus();
    });

    $(".datetimerange-picker-input-1").daterangepicker({
        "format": "dd-mm-yy ",
        autoUpdateInput: false,
        timePicker: false,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });
    $('.datetimerange-picker-input-1').on('apply.daterangepicker', function(ev, picker) {
        $(".datetimerange-picker-input-1").val(picker.startDate.format('DD-MM-YYYY') + " to " + picker.endDate.format('DD-MM-YYYY'));
    });



    $(".datetimerange-picker-input").daterangepicker({
        "format": "dd-mm-yy ",
        autoUpdateInput: false,
        timePicker: false,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });
    $('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
        $(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY') + " to " + picker.endDate.format('DD-MM-YYYY'));
    });


    //response date filter
    $(".filterBtn_2").on("click", function() {
        df2 = $("#material_request_filter_input_2").val();
        // alert(df2);
        df2 = encodeURI(df2)
        displayRecords(100, 1);
    })
    $(".datetimerange-picker-btn").on("click", function() {
        $(".datetimerange-picker-input", $(this).closest(".date")).focus();
    });
    $(".datetimerange-picker-btn-2").on("click", function() {
        $(".datetimerange-picker-input-2", $(this).closest(".date")).focus();
    });

    $(".datetimerange-picker-input-2").daterangepicker({
        "format": "dd-mm-yy ",
        autoUpdateInput: false,
        timePicker: false,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });
    $('.datetimerange-picker-input-2').on('apply.daterangepicker', function(ev, picker) {
        $(".datetimerange-picker-input-2").val(picker.startDate.format('DD-MM-YYYY') + " to " + picker.endDate.format('DD-MM-YYYY'));
    });

    //response date filter end
</script>
<?php require_once("disconnect.php"); ?>