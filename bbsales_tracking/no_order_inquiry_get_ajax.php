<?php
// var_dump($_REQUEST);exit;
if ($_REQUEST['inquiry_type'] == "-1") {
    $page_id = 621;
    $page_slug = 'prospect_inquiry';
} else if ($_REQUEST['inquiry_type'] == "0") {
    $page_id        = 572;
    $page_slug      = 'no_order_inquiry';
} else {
    $page_id = 620;
    $page_slug = 'lead_page';
}
include("connect.php");
$ctable     = "no_order_inquiry";
$ctable1    = "Inquiry";
$Where = "";

// print_r($_REQUEST);exit();
if ($_REQUEST['inquiry_type'] == "-1") {
    $page_id = 621;
    $page_slug = 'prospect_inquiry';
    $txt1     = "Raw Data";
} else if ($_REQUEST['inquiry_type'] == "0") {
    $page_id        = 572;
    $page_slug      = 'no_order_inquiry';
    $txt1     = "Inquiry";
} else {
    $page_id = 620;
    $page_slug = 'lead_page';
    $txt1     = "Lead";
}
$ctable     = "no_order_inquiry";
$ctable1    = "Inquiry";
$ctable_where = "";
$_REQUEST['searchName'] = urldecode($_REQUEST['searchName']);
//echo "gfdfg";exit();


if (isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName'])) {
    $Query = $_REQUEST['searchName'];
    // $Where.=" (company_name like '%".$Query."%' OR mobile_number like '%".$Query."%' OR id like '%".$Query."%' OR person_name like '%".$Query."%' OR country like '%".$Query."%' OR state like '%".$Query."%' OR city like '%".$Query."%' OR email_address like '%".$Query."%' OR pincode like '%".$Query."%' ) AND ";

    $Where .= " (company_name like '%" . $Query . "%' OR mobile_number like '%" . $Query . "%'  OR person_name like '%" . $Query . "%'  OR pincode like '%" . $Query . "%' ) AND ";
}

if (isset($_REQUEST['c_type']) && $_REQUEST['c_type'] != "" && $_REQUEST['c_type'] != NULL) {
    $Where .= "executive_type = '" . $_REQUEST['c_type'] . "' AND ";
    $c_type = $_REQUEST['c_type'];
}

if (isset($_REQUEST['company_type']) && $_REQUEST['company_type'] != "" && $_REQUEST['company_type'] != NULL) {
    // echo "hell0";die;
    $Where .= " type_of_company = '" . $_REQUEST['company_type'] . "'  AND ";
    $company_type = $_REQUEST['company_type'];
}
if (isset($_REQUEST['status1']) && $_REQUEST['status1'] != "") {
    $Where .= "status IN (" . $_REQUEST['status1'] . ") AND ";
}

if (isset($_REQUEST['industry_type']) && $_REQUEST['industry_type'] != "") {
    $Where .= " industry_type_id='" . $_REQUEST['industry_type'] . "' AND ";
    $industry_type = $_REQUEST['industry_type'];
}

if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != "") {
    $Where .= "status='" . $_REQUEST['status_id'] . "' AND ";
    $status_id = $_REQUEST['status_id'];
    // echo $Where;exit();
}
if (isset($_REQUEST['type']) && $_REQUEST['type'] != "" && $_REQUEST['type'] != NULL && $_REQUEST['type'] != undefined) {
    $Where .= "inquiry_created_by = '" . $_REQUEST['type'] . "' AND ";
    $type = $_REQUEST['type'];
}

if (isset($_REQUEST['dealer_id']) && $_REQUEST['dealer_id'] != "" && $_REQUEST['dealer_id'] != NULL) {
    $Where .= "dealer_id = '" . $_REQUEST['dealer_id'] . "' AND ";
}

if (isset($_REQUEST['country']) && $_REQUEST['country'] != "" && $_REQUEST['country'] != NULL) {
    $Where .= "country = '" . $_REQUEST['country'] . "' AND ";
    $country .= $_REQUEST['country'];
}

if (isset($_REQUEST['state']) && $_REQUEST['state'] != "" && $_REQUEST['state'] != NULL) {
    $Where .= "state = '" . $_REQUEST['state'] . "' AND ";
    $state = $_REQUEST['state'];
}

if (isset($_REQUEST['city']) && $_REQUEST['city'] != "" && $_REQUEST['city'] != NULL) {
    $Where .= "main_city = '" . $_REQUEST['city'] . "' AND ";
    $city = $_REQUEST['city'];
}
if (isset($_REQUEST['route']) && $_REQUEST['route'] != "" && $_REQUEST['route'] != NULL) {
    $Where .= " city = '" . $_REQUEST['route'] . "' AND ";
    $route = $_REQUEST['route'];
}
// update code 
if (isset($_REQUEST['source_id']) && $_REQUEST['source_id'] != "" && $_REQUEST['source_id'] != NULL) {
    $Where .= "  source_of_inquiry = '" . $_REQUEST['source_id'] . "' AND ";
    $source_id = $_REQUEST['source_id'];
}

if (isset($_REQUEST['assigned_to']) && $_REQUEST['assigned_to'] != "" && $_REQUEST['assigned_to'] != NULL && $_REQUEST['assigned_to'] != undefined) {
    $Where .= "inquiry_assign_to = '" . $_REQUEST['assigned_to'] . "' AND ";
    $assigned_to = $_REQUEST['assigned_to'];
}

if (isset($_REQUEST['end_followup']) && $_REQUEST['end_followup'] != "") {
    $Where .= " followup_reason_id='" . $_REQUEST['end_followup'] . "' AND ";
    // $status_id=$_REQUEST['status_id'];
}
if (isset($_REQUEST['inquiry_month']) && $_REQUEST['inquiry_month'] != "" && $_REQUEST['inquiry_month'] != NULL && $_REQUEST['inquiry_month'] != undefined) {
    $Where .= "  MONTH(inquiry_date) = '" . $_REQUEST['inquiry_month'] . "' AND ";
    // $type=$_REQUEST['type'];
}
if (isset($_REQUEST['inquiry_year']) && $_REQUEST['inquiry_year'] != "" && $_REQUEST['inquiry_year'] != NULL && $_REQUEST['inquiry_year'] != "undefined") {
    //$Where .= " AND year(inquiry_date) = '".$_REQUEST['inquiry_year']."' ";
    $Where .= "  year(inquiry_date) = '" . $_REQUEST['inquiry_year'] . "' AND ";
    // $type=$_REQUEST['type'];
}

if (isset($_REQUEST['lead_month']) && $_REQUEST['lead_month'] != "" && $_REQUEST['lead_month'] != NULL) {
    $Where .= " MONTH(inquiry_date) = '" . $_REQUEST['lead_month'] . "' AND ";
    // $type=$_REQUEST['type'];
}
if (isset($_REQUEST['lead_year']) && $_REQUEST['lead_year'] != "" && $_REQUEST['lead_year'] != NULL) {
    $Where .= " year(inquiry_date) = '" . $_REQUEST['lead_year'] . "' AND ";
    // $type=$_REQUEST['type'];
}
if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != "01-01-1970") {
    $Where .= "  inquiry_date>='" . $_REQUEST['todate'] . "' AND ";
    // $type=$_REQUEST['type'];
}
if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != "01-01-1970") {
    $Where .= "  inquiry_date<='" . $_REQUEST['fromdate'] . "' AND ";
    // $type=$_REQUEST['type'];
}

if (isset($_REQUEST['lead_todate']) && $_REQUEST['lead_todate'] != "" && $_REQUEST['lead_todate'] != NULL) {
    $Where .= "  inquiry_date>='" . $_REQUEST['lead_todate'] . "' AND ";
    // $type=$_REQUEST['type'];
}

if (isset($_REQUEST['lead_fromdate']) && $_REQUEST['lead_fromdate'] != "" && $_REQUEST['lead_fromdate'] != NULL) {
    $Where .= "  inquiry_date<='" . $_REQUEST['lead_fromdate'] . "' AND ";
    // $type=$_REQUEST['type'];
}
if (isset($_REQUEST['lead_year']) && $_REQUEST['lead_year'] != "" && $_REQUEST['lead_year'] != NULL) {
    $Where .= "  year(inquiry_date) = '" . $_REQUEST['lead_year'] . "' AND ";
    // $type=$_REQUEST['type'];
}


// update code 

if (isset($_REQUEST['df']) && $_REQUEST['df'] != "") {
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode($_REQUEST['df']);
    $date_filter_query_ex = explode(" to ", $date_filter_query);
    $Where .= " ( DATE(datetime)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(datetime)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) AND ";
}

//echo "435";exit();
// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
//     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
//     $Where .= " (inquiry_assign_to = '".$check_id."' OR inquiry_created_by = '".$check_id."') AND ";
// }

// Get the total number of rows in the table

if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
    if ($rights['personal_flag'] == 1) {

        $check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
        $where .= "(inquiry_assign_to = '" . $check_id . "' OR inquiry_created_by = '" . $check_id . "') AND ";
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
                $Where .= "(inquiry_assign_to IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR inquiry_created_by IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")) AND ";
            } else {
                $Where .= "( inquiry_assign_to IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR inquiry_created_by IN(" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")) AND ";
            }
        }
    }
}
if ($_REQUEST['inquiry_type'] == "-1") {
    // $Where .= " isDelete=0 AND isActive=1 AND (inquiry_lead_flag = '-1' OR inquiry_lead_flag='0')";
    $Where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '-1'";
} else if ($_REQUEST['inquiry_type'] == "0") {
    // $Where .= " isDelete=0 AND isActive=1 AND (inquiry_lead_flag = '0' OR inquiry_lead_flag = '1')";
    $Where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '0'";
} else {
    // $Where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '1'";
    $Where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '1'";
}
//break records into pages
$item_per_page =  ($_REQUEST["numRecords"] <> "" && is_numeric($_REQUEST["numRecords"])) ? intval($_REQUEST["numRecords"]) : 50;

if (isset($_REQUEST["page"])) {

    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if (!is_numeric($page_number)) {
        die('Invalid page number!');
    } //incase of invalid page number
} else {
    $page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable, $Where, 0);
$total_pages = ceil($get_total_rows / $item_per_page);


//get starting position to fetch the records
$page_position = (($page_number - 1) * $item_per_page);


$ctable_r = $db->rp_getData($ctable, "*", $Where, "id DESC limit $page_position, $item_per_page", 0);
$export_data_where = [
    "table" => $ctable,
    "data" => "*",
    "where" => $Where,
    "order_by" => "id DESC",
    "limit" => "$page_position, $item_per_page",
];
$export_data_where = json_encode($export_data_where);
//$ctable_r = $db->rp_getData($ctable,"*",$Where,"id DESC",0);
/*for log*/
$flag = "Web";
if ($_REQUEST['inquiry_type'] == "-1") {
    $module_name = "Prospect";
    $log_description = $module_name . " Printed By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
} else if ($_REQUEST['inquiry_type'] == "0") {
    $module_name = "Inquiry";
    $log_description = $module_name . " Printed By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
} else {
    $module_name = "Lead";
    $log_description = $module_name . " Printed By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
}
$last_id = "";
$db->insertLog($ctable, $last_id, "insert", "", $insert, 0, $log_description, $flag, $module_name, $user_id);
?>
<style type="text/css">
    .table-scrollable {
        width: auto;
        height: 810px;
        overflow-x: scroll;
        overflow-y: scroll;
        margin: 0 !important;
    }

    .dataTables_scrollHeadInner {
        padding-left: 0 !important;
    }
</style>
<div class="table-scrollable">
    <table id="datatable_11" class="table table-striped table-bordered table-hover">
        <thead>

            <tr>
                <?php
                if ($_REQUEST['inquiry_type'] == "-1") {
                ?><th></th>
                <?php
                }
                ?>
                <th></th>
                <th></th>
                <th></th>
                <th>
                    <select class="form-control" id="status_id" name="status_id">

                        <option value="">Select Status</option>
                        <option <?= ($_REQUEST["status_id"] == 0 && $_REQUEST["status_id"] != "") ? "selected" : ""; ?> value="0">Generate</option>
                        <option <?= ($_REQUEST["status_id"] == 2 && $_REQUEST["status_id"] != "") ? "selected" : ""; ?> value="2">Positive</option>
                        <option <?= ($_REQUEST["status_id"] == 1) ? "selected" : ""; ?> value="1">In Followup</option>
                        <option <?= ($_REQUEST["status_id"] == 4) ? "selected" : ""; ?> value="4">Hot</option>
                        <option <?= ($_REQUEST["status_id"] == 5) ? "selected" : ""; ?> value="5">Cold</option>
                        <option <?= ($_REQUEST["status_id"] == 6) ? "selected" : ""; ?> value="6">Warm</option>
                        <option <?= ($_REQUEST["status_id"] == -2) ? "selected" : ""; ?> value="-2">Cancel</option>
                        <option <?= ($_REQUEST["status_id"] == -1) ? "selected" : ""; ?> value="-1">My Work</option>
                        <option <?= ($_REQUEST["status_id"] == 3) ? "selected" : ""; ?> value="3">Buy Later</option>
                        <option <?= ($_REQUEST["status_id"] == 11) ? "selected" : ""; ?> value="11">Lost</option>

                        <!--  <option <?= ($status_id == 0 && $status_id != "") ? "selected" : ""; ?> value="0">Generate</option>
                        <option <?= ($status_id == 1) ? "selected" : ""; ?> value="1">In Followup</option> 
                        <option <?= ($status_id == 2) ? "selected" : ""; ?> value="2">Interested</option>
                        <option <?= ($status_id == -1) ? "selected" : ""; ?> value="-1">Not Interested</option>
                        <option <?= ($status_id == 3) ? "selected" : ""; ?> value="3">Working</option> -->
                    </select>
                </th>
                <th>
                    <select class="form-control" name="source_id" id="source_id">
                        <option value="">Source Medium By</option>
                        <?php
                        $source_d = $db->rp_getData("source_of_inquiry", "*", "isDelete=0", "", 0);
                        if ($source_d) {
                            while ($source_r = mysqli_fetch_assoc($source_d)) {
                        ?>
                                <option value="<?php echo $source_r["id"]; ?>" <?= ($_REQUEST["source_id"] == $source_r["id"]) ? "selected" : ""; ?>>
                                    <?php echo $db->clean($source_r["name"]); ?>
                                </option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </th>
                <th>
                    <div class="input-group">
                        <input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
                        <span class="input-group-addon datetimerange-picker-btn">
                            <i class="fa fa-calendar"></i>
                        </span>

                        <span class="input-group-btn">
                        </span>
                    </div>
                    <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
                </th>
                <th>
                    <select class="form-control" name="type" id="type" onChange="getSalesExecutive(this.value);">
                        <option value="">Select Inquiry Taken By</option>
                        <?php
                        $se_r = $db->rp_getData("sales_executive", "*", "isDelete=0 AND isActive=1");

                        if ($se_r) {
                            while ($se_d = mysqli_fetch_assoc($se_r)) {
                        ?>
                                <option value="<?php echo $se_d['id']; ?>" <?= ($type == $se_d['id']) ? "selected" : ""; ?>><?php echo $se_d['name']; ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </th>
                <th>
                    <select class="form-control" name="assigned_to" id="assigned_to" onChange="getSalesExecutive(this.value);">
                        <option value="">Select Inquiry Assigned By</option>
                        <?php
                        $se_r = $db->rp_getData("sales_executive", "*", "isDelete=0 AND isActive=1");

                        if ($se_r) {
                            while ($se_d = mysqli_fetch_assoc($se_r)) {
                        ?>
                                <option value="<?php echo $se_d['id']; ?>" <?= ($assigned_to == $se_d['id']) ? "selected" : ""; ?>><?php echo $se_d['name']; ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </th>
                <th>
                    <select class="form-control input-small" id="company_type">
                        <option value="">Select Company</option>
                        <?php
                        $company_r = $db->rp_getData("company_master", "*", "isDelete=0", "", 0);
                        if ($company_r) {
                            while ($company_d = mysqli_fetch_assoc($company_r)) {
                        ?>
                                <option value="<?= $company_d['id'] ?>" <?= ($company_d['id'] == $company_type) ? "selected" : ""; ?>>
                                    <?= $company_d['name']; ?>
                                </option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </th>
                <th>
                    <select class="form-control" id="c_type" name="c_type" style="width:120px;text-align:center;margin: auto;">
                        <option value="">Select Customer Type</option>
                        <?php
                        $customer_type = $db->rp_getData("customer_type", "*", "isDelete=0");
                        if ($customer_type) {
                            while ($customer_type_d = mysqli_fetch_assoc($customer_type)) {
                        ?>
                                <option value="<?= $customer_type_d['id'] ?>" <?= ($c_type == $customer_type_d['id']) ? "selected" : ""; ?>>
                                    <?= $customer_type_d['name'] ?>
                                </option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </th>
                <th>
                    <select class="form-control b-3" id="industry_type" name="industry_type">
                        <option value="">Select Type of Industry</option>
                        <?php
                        $customer_type = $db->rp_getData("industry_type", "*", "isDelete=0");
                        if ($customer_type) {
                            while ($customer_type_d = mysqli_fetch_assoc($customer_type)) {
                        ?>
                                <option value="<?= $customer_type_d['id'] ?>" <?= ($industry_type == $customer_type_d['id']) ? "selected" : ""; ?>>
                                    <?= $customer_type_d['name'] ?>
                                </option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>
                    <select class="form-control" name="country" id="country" onChange="filter_country(this.value);">
                        <option value="">Select Country</option>
                        <?php
                        $country_r = $db->rp_getData("country", "*", 0);
                        if (mysqli_num_rows($country_r) > 0) {
                            while ($country_d = mysqli_fetch_array($country_r)) {
                        ?>
                                <option value="<?php echo $country_d['name']; ?>" <?= ($country == $country_d['name']) ? "selected" : ""; ?>>
                                    <?php echo $country_d['name']; ?>
                                </option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </th>
                <th>
                    <select class="form-control" name="state" id="state" onChange="filter_state(this.value);">
                        <option value="">Select State</option>
                    </select>
                </th>
                <th>
                    <select class="form-control" name="city" id="city" onChange="filter_city(this.value);">
                        <option value="">Select City</option>
                    </select>
                </th>
                <th>
                    <select class="form-control" name="route" id="route">
                        <option value="">Select route</option>
                    </select>
                </th>
                <th></th>
                <th>
                    <select class="form-control" id="end_followup" name="end_followup">
                        <option value="">Select Followup End Type</option>
                        <?php
                        $followup_end_r = $db->rp_getData("followup_reason", "*", "isDelete=0");
                        if ($followup_end_r) {
                            while ($followup_end_d = mysqli_fetch_assoc($followup_end_r)) {
                        ?>
                                <option value="<?= $followup_end_d["id"] ?>" <?= ($_REQUEST["end_followup"] == $followup_end_d["id"]) ? "selected" : ""; ?>>
                                    <?= $db->clean($followup_end_d["name"]) ?>
                                </option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>

            <tr>
                <?php
                if ($_REQUEST['inquiry_type'] == "-1") {
                ?>
                    <th style="width: 5%;">
                        <input type="checkbox" id="select_all">
                    </th>
                <?php
                }
                ?>
                <th style="width: 5%;"></th>
                <th>Sr No.</th>
                <th style="width: 5%;">Followup</th>
                <th>Status</th>
                <th>Source Medium</th>
                <th>Inquiry Date</th>
                <th>Inquiry Taken By</th>
                <th>Inquiry Assigned To</th>
                <th>Company Type</th>
                <th>Customer Type</th>
                <th>industry Type</th>
                <th>Inquiry No.</th>
                <th>Description</th>
                <th>Firm Name</th>
                <!-- <th>Dealer</th> -->
                <th>Person Name</th>
                <th>Mobile Number</th>
                <th>Email Address</th>
                <th>Country</th>
                <th>State</th>
                <th>City</th>
                <th>Route</th>
                <th>Pincode</th>
                <th>End Followup Reason</th>
                <th>Cancel Reason</th>
                <th>Quotation Lost Reason</th>
                <th>Inquiry Type</th>
                <th>Entry Type</th>
                <th>Update Entry Type</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($ctable_r) > 0) {
                $u_w_flag_arr = array('1' => "YES", '0' => "NO");
                $quotation_flag_arr = array('1' => "YES", '2' => "NO");
                $count = 0;
                while ($ctable_d = mysqli_fetch_array($ctable_r)) {

                    $entry_type_status = array("1" => "Admin Panel", "2" => "customer", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
                    $inq_statuss = array("-1" => "Prospect", "0" => "Inquiry", "1" => "Lead", "2" => "Convert To Inquiry", "3" => "Convert To Lead");
                    // $inquiry_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp","4"=>"India Mart","5"=>"Just Dial","6"=>"Trade India","7"=>"Ali baba","8"=>"Facebook","9"=>"Instagram");

                    $inquiry_type_array_data_r = $db->rp_getData("source_of_inquiry", "*", "isDelete=0", "", 0);
                    $inquiry_type_array_data = array();
                    while ($inquiry_type_array_data_d = mysqli_fetch_assoc($inquiry_type_array_data_r)) {
                        $inquiry_type_array_data[$inquiry_type_array_data_d['id']] = $inquiry_type_array_data_d['name'];
                    }
                    $inquiry_type_array = $inquiry_type_array_data;
            ?>
                    <tr>
                        <?php
                        if ($_REQUEST['inquiry_type'] == "-1") {
                        ?>
                            <td>
                                <input type="checkbox" class="inquiry_multi" id="inquiry_multi<?php echo $ctable_d['id']; ?>" data-inquiry_id="<?php echo $ctable_d["id"]; ?>">
                            </td>
                        <?php
                        }
                        ?>
                        <td>
                            <?php
                            if ($rights['update_flag'] == 1) {
                            ?>
                                <div class="btn-group">
                                    <button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle" style="padding:5px!important">
                                        <i class="fa fa-gear"></i>
                                    </button>
                                    <ul role="menu" class="dropdown-menu">
                                        <li>
                                            <a href="<?php echo $ctable; ?>_crud.php?mode=edit&type=<?= $_REQUEST['inquiry_type'] ?>&id=<?php echo $ctable_d['id']; ?>" title="Edit">
                                                <span class="text-primary">
                                                    <i class="fa fa-pencil"></i>
                                                    &nbsp;Edit
                                                </span>
                                            </a>
                                        </li>

                                        <?php
                                        if ($rights['delete_flag'] == 1 && $ctable_d['id'] != -1) {
                                        ?>
                                            <li>
                                                <a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete">
                                                    <span class="text-danger">
                                                        <i class="fa fa-times"></i>
                                                        &nbsp;Delete
                                                    </span>
                                                </a>
                                            </li>
                                        <?php
                                        }
                                        ?>
                                        <?php
                                        if ($ctable_d['inquiry_type']  == -1) {
                                        ?>
                                            <li>
                                                <a type="button" class="view-guideline" onclick="
                                GenerateInquiry('<?php echo $ctable_d['id'] ?>');" style="color:#797b00;background: none;outline: none;border: none;">
                                                    <i class="fa fa-file fa-lg fa-fw"> </i>Generate Inquiry
                                                </a>
                                            </li>
                                        <?php
                                        }
                                        ?>
                                        <?php
                                        if ($ctable_d['inquiry_type']  == 0) {
                                        ?>
                                            <li>
                                                <a type="button" class="view-guideline" onclick="
                                GenerateLead('<?php echo $ctable_d['id'] ?>');" style="color:#797b00;background: none;outline: none;border: none;">
                                                    <i class="fa fa-file fa-lg fa-fw"> </i>Generate Lead
                                                </a>
                                            </li>
                                        <?php
                                        }
                                        ?>


                                        <?php
                                        if ($ctable_d['inquiry_type']  == 1) {
                                        ?>
                                            <li>
                                                <a target="_blank" href="quotation_crud.php?mode=add&inquiry_id=<?php echo $ctable_d['id']; ?>" title="Quotation">
                                                    <span class="text-warning">
                                                        <i class="fa fa-file"></i>
                                                        &nbsp;Quotation
                                                    </span>
                                                </a>
                                            </li>
                                        <?php
                                        }
                                        ?>
                                        <li>
                                            <a type="button" onclick="CancelInquiry('<?php echo $ctable_d['id'] ?>')">
                                                <span style="color: black;" class="text-primary">
                                                    <i class="fa fa-eye"></i>
                                                    &nbsp;Lost <?= $txt1 ?>
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            <?php
                            }
                            ?>
                        </td>

                        <td><?php echo ++$count; ?></td>
                        <td>
                            <?php
                            $SEID = $db->rp_getvalue("dealer_distributor_network", "sales_executive_id", "id='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "' ", 0);
                            ?>
                            <a href="followup.php?mode=inquiry_followup&inquiry_id=<?php echo $ctable_d['id'] ?>&sales_id=<?php echo $SEID ?>&visitor_id=<?php echo $ctable_d['dealer_id'] ?>">
                                <span class="text-success">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </a>
                        </td>
                        <td>
                            <?php
                            if ($ctable_d['status'] == 0) {
                                $status = "Generate";
                            } else if ($ctable_d['status'] == 1) {
                                $status = "In Followup";
                            } else if ($ctable_d['status'] == 2) {
                                $status = "Positive";
                            } else if ($ctable_d['status'] == -1) {
                                $status = "My Work";
                            } else if ($ctable_d['status'] == 3) {
                                $status = "Buy Later";
                                //05-08-2021 by milan old
                                // $status="Working";
                            } else if ($ctable_d['status'] == -2) {
                                $status = "Cancel";
                            } else if ($ctable_d['status'] == 4) {
                                $status = "Hot";
                            } else if ($ctable_d['status'] == 5) {
                                $status = "Cold";
                            } else if ($ctable_d['status'] == 6) {
                                $status = "Warm";
                            }
                            // else if($ctable_d['status']==7)
                            // {
                            //     $status="Wrong Call";
                            // }
                            // else if($ctable_d['status']==8)
                            // {
                            //     $status="Will Interested";
                            // }
                            // else if($ctable_d['status']==9)
                            // {
                            //     $status="Not Working";
                            // }
                            // else if($ctable_d['status']==10)
                            // {
                            //     $status="Not Doing Business";
                            // }
                            else if ($ctable_d['status'] == 11) {
                                $status = "Lost";
                            } else {
                                $status = "";
                            }
                            echo $status;
                            ?>
                            <!-- <select class="form-control" disabled="disabled" id="inquiry_status<?= $ctable_d['id'] ?>" style="width:200px;text-align:center;margin: auto;">
                            <option value="">Select Status</option>
                            <option <?= ($ctable_d['status'] == 0) ? "selected" : ""; ?> value="0">Generate</option>              
                            <option <?= ($ctable_d['status'] == 1) ? "selected" : ""; ?> value="1">In Followup</option>
                            <option <?= ($ctable_d['status'] == 2) ? "selected" : ""; ?> value="2">Interested</option>
                            <option <?= ($ctable_d['status'] == -1) ? "selected" : ""; ?> value="-1">Not Interested</option>
                            <option <?= ($ctable_d['status'] == 3) ? "selected" : ""; ?> value="3">Dipson Working</option>
                        </select> -->
                            <!-- <a href="javascript:void(0);" id="editStatus_<?php echo $ctable_d['id']; ?>" onClick="editStatus('<?php echo $ctable_d['id']; ?>')">Edit</a>                    
                            <span id="editStatus2_<?php echo $ctable_d['id']; ?>" style="display:none;">
                                <a href="javascript:void(0);" id="saveEditStatus<?php echo $ctable_d['id']; ?>" onClick="saveEditStatus('<?php echo $ctable_d['id']; ?>')">Save</a> |
                                <a href="javascript:void(0);" id="cancelEditStatus<?php echo $ctable_d['id']; ?>" onClick="cancelEditStatus('<?php echo $ctable_d['id']; ?>')">Cancel</a>
                            </span> -->
                        </td>
                        <td><?php echo $inquiry_type_array[$ctable_d['source_of_inquiry']]; ?></td>
                        <td>
                            <?php
                            if ($ctable_d['datetime'] != "0000-00-00 00:00:00") {
                                echo date('d-m-Y', strtotime($ctable_d['datetime']));
                            } else {
                                echo "";
                            }
                            ?>
                        </td>
                        <?php $action = $db->rp_getValue("no_order_inquiry_action", "name", "id='" . $ctable_d['action'] . "'"); ?>
                        <td>
                            <?php
                            $sales_executive = $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['inquiry_created_by'] . "'"); ?>
                            <?php echo stripslashes($sales_executive); ?>
                        </td>
                        <td>
                            <?php
                            $inquiry_assign = $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['inquiry_assign_to'] . "'"); ?>
                            <?php echo stripslashes($inquiry_assign); ?>
                        </td>
                        <td>
                            <?= $db->rp_getValue("company_master", "name", "id='" . $ctable_d['type_of_company'] . "'") ?>
                        </td>
                        <td>
                            <?php echo $db->rp_getValue("customer_type", "name", "id='" . $ctable_d['executive_type'] . "'"); ?>
                        </td>
                        <td><?php echo $db->rp_getValue("industry_type", "name", "id='" . $ctable_d['industry_type_id'] . "'"); ?></td>
                        <td>#INQ/<?php echo $ctable_d['id']; ?></td>
                        <td><?php echo $ctable_d['description']; ?></td>
                        <td>
                            <span class="<?php echo ($ctable_d['isActive'] == 0) ? "text-danger" : "text-success"; ?>"><?php echo $ctable_d['company_name']; ?></span>
                        </td>
                        <!-- <td><?php echo $db->rp_getValue("executive", "company_name", "id='" . $ctable_d['dealer_id'] . "'"); ?></td> -->
                        <td>
                            <span class="<?php echo ($ctable_d['isActive'] == 0) ? "text-danger" : "text-success"; ?>"><?php echo $ctable_d['person_name']; ?></span>
                        </td>
                        <td><?php echo $ctable_d['mobile_number']; ?></td>
                        <td><?php echo $ctable_d['email_address']; ?></td>
                        <td><?php echo $ctable_d['country']; ?></td>
                        <td><?php echo $ctable_d['state']; ?></td>
                        <td><?php echo $ctable_d['main_city']; ?></td>
                        <td><?php echo $ctable_d['city']; ?></td>
                        <td><?php echo $ctable_d['pincode']; ?></td>
                        <td><?php echo $db->rp_getValue("followup_reason", "name", "id='" . $ctable_d['followup_reason_id'] . "'"); ?></td>
                        <td><?php echo $ctable_d['cancel_inq_remark']; ?></td>
                        <td><?php echo $ctable_d['lost_reason']; ?></td>
                        <td><?php echo $inq_statuss[$ctable_d['inq_status']]; ?></td>
                        <td><?php echo $entry_type_status[$ctable_d['entry_flag']]; ?></td>
                        <td><?php echo $entry_type_status[$ctable_d['update_entry_flag']]; ?></td>


                        <!-- <td>
                        <?php
                        if ($ctable_d['image_path'] != "") {
                            $img = explode(",", $ctable_d['image_path']);
                            $imgpath = array();
                            for ($i = 0; $i < sizeof($img); $i++) {
                                $imgpath[] = SITEURL . "resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $ctable_d["id"] . "' AND id='" . $img[$i] . "'", 0);
                            }

                            for ($i = 0; $i < sizeof($imgpath); $i++) {
                                if ($i == 0) {
                        ?>
                                    <a href="<?= $imgpath[$i] ?>" data-lightbox="complain<?= $count ?>" data-title="complain <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 80px;"></a>
                                <?php
                                } else {
                                ?>
                                    <div class="hidden">
                                        <a href="<?= $imgpath[$i] ?>" data-lightbox="complain<?= $count ?>" data-title="complain <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 80px;"></a>
                                    </div>
                                <?php
                                }
                            }
                        } else {
                            $img = $ctable_d['image_path'] = DEFAULTIMG;
                                ?>
                            <a href="<?= $img ?>" data-lightbox="attendance<?= $count ?>" data-title="attendance <?= $ctable_d['id'] ?>"><img src="<?= $img ?>" style="height: 80px;"></a>
                        <?php
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $product_r = $db->rp_getData("product", "*", "id=" . $ctable_d['product_id'] . " AND isDelete=0 AND isActive=1", "", 0);
                        if ($product_r) {
                            while ($product_d = mysqli_fetch_assoc($product_r)) {
                                $product_weight = $db->rp_getData("product_weight_price", "weight_id", "product_id='" . $product_d['id'] . "' AND isDelete=0");
                                while ($product_weight_d = mysqli_fetch_assoc($product_weight)) {
                                    $weight_name = $db->rp_getValue("weight", "name", "id='" . $product_weight_d['weight_id'] . "' AND isDelete=0", 0);
                                }
                                echo $product_d['name'] . "-" . $weight_name;
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <?= $ctable_d['quantity'] ?>
                    </td>
                    <td>
                        <?= $u_w_flag_arr[$ctable_d['u_w_flag']] ?>
                    </td>
                    <td>
                        <?= $ctable_d['u_w_remark'] ?>
                    </td>
                    <td>
                        <?= $quotation_flag_arr[$ctable_d['quotation_flag']] ?>
                    </td>
                    <td>
                        <?= $ctable_d['quotation_remark'] ?>
                    </td>
                    <td>
                        <?= $ctable_d['customer_requirement'] ?>
                    </td> -->
                        <!-- <td>
                        <a href="followup.php?mode=inquiry_followup&inquiry_id=<?php echo $ctable_d['id'] ?>" class="btn btn-primary btn-sm" title="track">Followup</a>
                        <?php
                        if ($ctable_d['executive_type'] != "" && $ctable_d['status'] == 3) { ?>
                            <a style="margin-top: 20px;" href="executive_crud.php?type=<?php echo $ctable_d['executive_type'] ?>&inquiry_id=<?php echo $ctable_d['id'] ?>&mode=add" class="btn btn-success btn-sm" title="track">Create Customer</a>
                        <?php
                        }
                        ?>
                    </td> -->
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr>
                    <th colspan="23" style="text-align: center;">No Data Found</th>
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

                <option value="100" <?php if ($_REQUEST["numRecords"] == 100) {
                                        echo ' selected="selected"';
                                    }  ?>>100</option>
                <option value="500" <?php if ($_REQUEST["numRecords"] == 500) {
                                        echo 'selected="selected"';
                                    }  ?>>500</option>
                <option value="2000" <?php if ($_REQUEST["numRecords"] == 2000) {
                                            echo ' selected="selected"';
                                        }  ?>>2000</option>
                <option value="5000" <?php if ($_REQUEST["numRecords"] == 5000) {
                                            echo ' selected="selected"';
                                        }  ?>>5000</option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="dataTables_paginate paging_simple_numbers">
            <ul class="pagination">
                <?php

                // exit();
                echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);

                ?>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    const EXPORT_DETAILS = "<?= addslashes($export_data_where) ?>";
    $("#status_id").select2();
    $("#source_id").select2();
    $("#type").select2();
    $("#assigned_to").select2();
    $("#company_type").select2();
    $("#c_type").select2();
    $("#industry_type").select2();
    $("#country").select2();
    $("#state").select2();
    $("#city").select2();
    $("#route").select2();
    $("#end_followup").select2();
    $(".filterBtn").on("click", function() {
        sales_executive = $("#sales_executive").val();
        customer_id = $("#customer_id").val();
        df1 = $("#material_request_filter_input").val();
        df1 = encodeURI(df1)
        callAjax();
    })
    $(".datetimerange-picker-btn").on("click", function() {
        $(".datetimerange-picker-input", $(this).closest(".date")).focus();
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

    /*this is for multiple inquiry selecter*/
    $(document).on('click', '#select_all', function() {
        $(".inquiry_multi").prop("checked", this.checked);
        $("#select_count").html($("input.inquiry_multi:checked").length + " Selected");
    });
    $(document).on('click', '.inquiry_multi', function() {
        if ($('.inquiry_multi:checked').length == $('.inquiry_multi').length) {
            $('#select_all').prop('checked', true);
        } else {
            $('#select_all').prop('checked', false);
        }
        $("#select_count").html($("input.inquiry_multi:checked").length + " Selected");
    });
    /*this is for multiple inquiry selecter*/
    function genInquiryPrint() {
        var myWindow = window.open("print_inquiry_ajax.php?data=" + EXPORT_DETAILS, '', 'width=700,height=800');
        myWindow.print();
    }

    function genExcelReport() {
        $.ajax({
            method: "POST",
            url: "inquiry_report_excel.php",
            data: {
                data: EXPORT_DETAILS
            },
            dataType: 'json',
            beforeSend: function() {
                // $("#loading-modal").modal('show');
                $('.preloader').fadeIn('slow');
            },
            success: function(result) {
                // $("#loading-modal").modal('hide');
                $('.preloader').fadeOut('slow');
                window.location.href = "<?= SITEURL ?>" + result.file_path;
            },
        });
    }
</script>
<?php require_once "disconnect.php"; ?>