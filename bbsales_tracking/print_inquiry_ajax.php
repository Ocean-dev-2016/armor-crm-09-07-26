<?php

$page_id = 572;
$page_slug = 'no_order_inquiry_page';
include("connect.php");
$ctable     = "no_order_inquiry";
$ctable1    = "Inquiry";
$Where = "";

// $_REQUEST['searchName'] = urldecode($_REQUEST['searchName']);


// if (isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName'])) {
//     $Query = $_REQUEST['searchName'];
//     //$Where.=" (company_name like '%".$Query."%' OR mobile_number like '%".$Query."%' OR id like '%".$Query."%' OR person_name like '%".$Query."%' OR country like '%".$Query."%' OR state like '%".$Query."%' OR city like '%".$Query."%' OR email_address like '%".$Query."%' OR pincode like '%".$Query."%') AND ";

//     $Where .= " (company_name like '%" . $Query . "%' OR mobile_number like '%" . $Query . "%'  OR person_name like '%" . $Query . "%' OR pincode like '%" . $Query . "%') AND ";
// }

// if (isset($_REQUEST['c_type']) && $_REQUEST['c_type'] != "" && $_REQUEST['c_type'] != NULL) {
//     $Where .= "executive_type = '" . $_REQUEST['c_type'] . "' AND ";
// }

// if (isset($_REQUEST['company_type']) && $_REQUEST['company_type'] != "" && $_REQUEST['company_type'] != NULL) {
//     // echo "hell0";die;
//     $Where .= " AND type_of_company = '" . $_REQUEST['company_type'] . "' ";
//     $company_type = $_REQUEST['company_type'];
// }

// if (isset($_REQUEST['industry_type']) && $_REQUEST['industry_type'] != "") {
//     $Where .= " industry_type_id='" . $_REQUEST['industry_type'] . "' AND ";
//     $industry_type = $_REQUEST['industry_type'];
// }

// if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != "") {
//     $Where .= "status='" . $_REQUEST['status_id'] . "' AND ";
// }

// if (isset($_REQUEST['type']) && $_REQUEST['type'] != "" && $_REQUEST['type'] != NULL) {
//     $Where .= "inquiry_created_by = '" . $_REQUEST['type'] . "' AND ";
// }

// if (isset($_REQUEST['dealer_id']) && $_REQUEST['dealer_id'] != "" && $_REQUEST['dealer_id'] != NULL) {
//     $Where .= "dealer_id = '" . $_REQUEST['dealer_id'] . "' AND ";
// }

// if (isset($_REQUEST['country']) && $_REQUEST['country'] != "" && $_REQUEST['country'] != NULL) {
//     $Where .= "country = '" . $_REQUEST['country'] . "' AND ";
// }

// if (isset($_REQUEST['state']) && $_REQUEST['state'] != "" && $_REQUEST['state'] != NULL) {
//     $Where .= "state = '" . $_REQUEST['state'] . "' AND ";
// }

// if (isset($_REQUEST['city']) && $_REQUEST['city'] != "" && $_REQUEST['city'] != NULL) {
//     $Where .= "main_city = '" . $_REQUEST['city'] . "' AND ";
// }
// if (isset($_REQUEST['route']) && $_REQUEST['route'] != "" && $_REQUEST['route'] != NULL) {
//     $Where .= " city = '" . $_REQUEST['route'] . "' AND ";
//     $route = $_REQUEST['route'];
// }
// // update code 
// if (isset($_REQUEST['source_id']) && $_REQUEST['source_id'] != "" && $_REQUEST['source_id'] != NULL) {
//     $Where .= "  source_of_inquiry = '" . $_REQUEST['source_id'] . "' AND ";
//     $source_id = $_REQUEST['source_id'];
// }

// if (isset($_REQUEST['assigned_to']) && $_REQUEST['assigned_to'] != "" && $_REQUEST['assigned_to'] != NULL) {
//     $Where .= "inquiry_assign_to = '" . $_REQUEST['assigned_to'] . "' AND ";
// }

// if (isset($_REQUEST['end_followup']) && $_REQUEST['end_followup'] != "") {
//     $Where .= " followup_reason_id='" . $_REQUEST['end_followup'] . "' AND ";
//     // $status_id=$_REQUEST['status_id'];
// }

// // update code 

// if (isset($_REQUEST['df']) && $_REQUEST['df'] != "") {
//     //echo $_REQUEST['df'];exit;
//     $date_filter_query = urldecode($_REQUEST['df']);

//     $date_filter_query_ex = explode(" to ", $date_filter_query);

//     $Where .= " ( DATE(datetime)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(datetime)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) AND ";
// }


// // if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// // {
// //     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
// //     $Where .= " (inquiry_assign_to = '".$check_id."' OR inquiry_created_by = '".$check_id."') AND ";
// // }


// if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
//     if ($rights['personal_flag'] == 1) {

//         $check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
//         $Where .= " AND (inquiry_assign_to = '" . $check_id . "' OR inquiry_created_by = '" . $check_id . "') ";
//     } else {
//         if ($rights['chain_vise_flag'] == 1) {

//             $check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];

//             $get_sales_type = $db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='" . $check_id . "'", 0);
//             if ($get_sales_type == "sales_manager") {
//                 $sales_executive_type = "Regional Sales Manager";
//                 $key = "sm_id";
//                 $WhereCondition .= ' ' . $key . '=' . $check_id;
//             } else if ($get_sales_type == "area_sales_manager") {
//                 $sales_executive_type = "National Sales Manager"; //Business Development Manager
//                 $key = "asm_id";
//                 $WhereCondition .= ' ' . $key . '=' . $check_id;
//             } else if ($get_sales_type == "sales_officer") {
//                 $sales_executive_type = "Area Sales Manager"; //Area Sales Manager
//                 $key = "so_id";
//                 $WhereCondition .= ' ' . $key . '=' . $check_id;
//             } else if ($get_sales_type == "sales_executive") {
//                 $sales_executive_type = "Sales Officer";
//                 $key = "se_id";
//                 $WhereCondition .= ' ' . $key . '=' . $check_id;
//             } else {
//                 $WhereCondition .= ' type = "service_engineer"';
//             }

//             $data = $db->rp_getData("sales_executive", "id", $WhereCondition, "", 0);

//             $SALEID1 = array();
//             if ($data) {
//                 while ($data_d = mysqli_fetch_assoc($data)) {
//                     $SALEID1[] = $data_d['id'];
//                 }
//             }
//             if (!empty($SALEID1)) {
//                 $SALEID1 = implode(",", $SALEID1);

//                 $Where .= " AND (inquiry_assign_to IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR inquiry_created_by IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "))";
//             } else {
//                 $Where .= " AND ( inquiry_assign_to IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ") OR inquiry_created_by IN(" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "))";
//             }
//         } else {
//         }
//     }
// }
// $item_per_page =  ($_REQUEST["numRecords"] <> "" && is_numeric($_REQUEST["numRecords"])) ? intval($_REQUEST["numRecords"]) : 50;

// if (isset($_REQUEST["page"])) {

//     $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
//     if (!is_numeric($page_number)) {
//         die('Invalid page number!');
//     } //incase of invalid page number
// } else {
//     $page_number = 1; //if there's no page number, set it to 1
// }

// $get_total_rows = $db->rp_getTotalRecord($ctable, $Where, 0);
// $total_pages = ceil($get_total_rows / $item_per_page);


// //get starting position to fetch the records
// $page_position = (($page_number - 1) * $item_per_page);
// if ($_REQUEST['inquiry_type'] == "-1") {
//     // $Where .= " isDelete=0 AND isActive=1 AND (inquiry_lead_flag = '-1' OR inquiry_lead_flag='0')";
//     $Where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '-1'";
// } else if ($_REQUEST['inquiry_type'] == "0") {
//     // $Where .= " isDelete=0 AND isActive=1 AND (inquiry_lead_flag = '0' OR inquiry_lead_flag = '1')";
//     $Where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '0'";
// } else {
//     // $Where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '1'";
//     $Where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '1'";
// }
$datas = json_decode($_REQUEST['data'], true);
$ctable = $datas['table'];
$Where = $datas['where'];
$columns = $datas['data'];
$order_by = $datas['order_by'];
$limit = $datas['limit'];
$ctable_r = $db->rp_getData($ctable, $columns, $Where, $order_by, 0, $limit);
/*for log*/
// $flag = "Web";
// if ($_REQUEST['inquiry_type'] == "-1") {
//     $module_name = "Raw Data";
//     $log_description = $module_name . " Printed By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
// } else if ($_REQUEST['inquiry_type'] == "0") {
//     $module_name = "Inquiry";
//     $log_description = $module_name . " Printed By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
// } else {
//     $module_name = "Lead";
//     $log_description = $module_name . " Printed By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
// }
// $last_id = "";
// $db->insertLog($ctable, $last_id, "insert", "", $insert, 0, $log_description, $flag, $module_name, $user_id);

// print_r($_REQUEST);exit;
/*for log*/
?>
<!-- <style type="text/css">
.table-scrollable 
{
    width: auto;
    height: 810px;
    overflow-x: scroll;
    overflow-y: scroll;
}
</style> -->
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

    h2 {
        text-transform: uppercase;
        margin-bottom: 0px;
    }
</style>
<table id="datatable_11" class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th colspan="26" class="center">
                <h2>Inquiry Report <?= date("d-m-Y h:i a"); ?> Printed By : <?= $_SESSION[SITE_SESS . 'SESS_NAME']; ?></h2>
            </th>
        </tr>
        <tr>
            <th>Sr No.</th>
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


            <!-- <th>Image Path</th>          
            <th>Product Name</th>                
            <th>Quantity</th>                
            <th>U/W</th>                
            <th>U/W Remark</th>                
            <th>Quotation</th>                
            <th>Quotation Remark</th>                
            <th>Customer Requirement Detail Note</th>     -->
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
                    <td><?php echo ++$count; ?></td>
                    <td>
                        <?php
                        if ($ctable_d['status'] == 0) {
                            $status = "Generate";
                        } else if ($ctable_d['status'] == 1) {
                            $status = "In Followup";
                        } else if ($ctable_d['status'] == 2) {
                            $status = "Position";
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
<?php require_once "disconnect.php"; ?>