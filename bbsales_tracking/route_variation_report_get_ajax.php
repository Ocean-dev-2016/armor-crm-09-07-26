<?php
// print_r($_REQUEST);
$page_id = 663;
$page_slug = 'route_variation_report';

/*
 * @author Dinesh
 */

include("connect.php");

$ctable = "master_route";
$ctable1 = "master_route";

$ctable_where = "";

// Get the total number of rows in the table

// echo $fromdate_to_loop;
// echo $todate_to_loop;
// // // echo $todate;
// exit();
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
    $sales_id = $db->rp_getData("sales_executive", "id", "name LIKE '%" . $_REQUEST['searchName'] . "%' AND isDelete = 0", "", 0);
    
    if($sales_id) {
        $SALESID = array();
        
        while ($sales_d = mysqli_fetch_assoc($sales_id)) {
            $SALESID[] = $sales_d['id'];
        }
        
        $SALESID = implode(",", $SALESID);
        $ctable_where .= "sales_executive_id IN (" . $SALESID . ") AND ";
    } else {
        $ctable_where .= "sales_executive_id IN (0) AND ";
        // $ctable_where .= "(sales_executive_id IN (0) OR ";
    }
}

if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id'] != "" && $_REQUEST['sales_executive_id'] != "null") {
    $ctable_where .= " sales_id IN (" . $_REQUEST['sales_executive_id'] . ") AND ";
    $sales_id = $_REQUEST['sales_executive_id'];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE'] != 0 && $_SESSION[SITE_SESS.'_ADMIN_TYPE'] != 14) {
    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    $ctable_where .= " sales_executive_id='" . $check_id . "' AND ";
} else {
    $ctable_where .= " ";
}


$ctable_where .= " isDelete = 0";


$item_per_page = ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])) {
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    
    if(!is_numeric($page_number)) {
        die('Invalid page number!'); //in case of an invalid page number
    }
} else {
    $page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where, 0); //hold total records in variable

//break records into pages
$total_pages = ceil($get_total_rows / $item_per_page);

//get starting position to fetch the records
$page_position = (($page_number - 1) * $item_per_page);

// if($_REQUEST['sales_executive_id'] != "null" && $_REQUEST['sales_executive_id'] != "") {
    // $ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "id DESC limit $page_position, $item_per_page", 0);
// }
?>
<style type="text/css">

</style>
<form action="" name="print_info1" id="print_info1" method="post">
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
          
            <tr>
                <th>No.</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Sales Person Name</th>
                <th>Assigned Route</th>
                <th>Change Route</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $monthName = isset($_REQUEST['filter_month'])?$db->clean($_REQUEST['filter_month']):0; // Specify the month name here
                $yearName = isset($_REQUEST['filter_year'])?$db->clean($_REQUEST['filter_year']):0; // Specify the year name here


                if ($sales_id != "" && $monthName != "" && $yearName != "") 
                {
                    
                    // Get the numeric format of the month
                    $timestamp = strtotime($monthName);
                    $monthNumber = date('m', $timestamp);

                    $fromdate_to_loop = $yearName."-".$monthNumber."-"."01";

                    $todate_to_loop = date('Y-m-t', strtotime("$yearName-$monthNumber-01"));

                    $ctable_where .= " AND ( DATE(start_date)>='".date("Y-m-d",strtotime($fromdate_to_loop))."' AND DATE(start_date)<='".date("Y-m-d",strtotime($todate_to_loop))."'  ) ";

                    $ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "id DESC", 0);

                    while($ctable_d = mysqli_fetch_assoc($ctable_r))
                    {
                        $count = 0;
                        ?>
                        <tr>
                            <td><?php echo ++$count; ?></td>
                            <td><?php echo $ctable_d['start_date']; ?></td>
                            <td><?php echo $ctable_d['end_date']; ?></td>
                            <td><?php echo $db->rp_getValue("sales_executive", "name", "isDelete=0 AND id='" . $ctable_d['sales_id'] . "'"); ?></td>
                            <td><?php echo $db->rp_getValue("area", "name", "isDelete=0 AND id='" . $ctable_d['area_id'] . "'",0); ?></td>
                            <td>
                                <?php
                                    $visit_data_r = $db->rp_getData("visit","customer_id","isDelete=0 AND user_id='".$sales_id."' AND ( DATE(start_date_time)>='".date("Y-m-d",strtotime($ctable_d['start_date']))."' AND DATE(start_date_time)<='".date("Y-m-d",strtotime($ctable_d['end_date']))."'  ) GROUP BY customer_id ","",0);

                                    if ($visit_data_r) 
                                    {
                                        while($visit_data_d = mysqli_fetch_assoc($visit_data_r))
                                        {
                                            $area_name = $db->rp_getValue("executive","city","isDelete=0 AND id='".$visit_data_d['customer_id']."'");

                                            $area_id = $db->rp_getValue("area","id","isDelete=0 AND name LIKE '%".strtolower(trim($area_name))."%' ");

                                            if ($area_id != $ctable_d['area_id']) 
                                            {
                                                ?>
                                                    <li><?php echo $area_name; ?></li>
                                                <?php
                                            }
                                        }
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                else
                {
                    ?>
                    <tr>
                        <td colspan="6" align="center"><strong>Please Select Sales Person Or Month Or Year</strong></td>
                    </tr>
                    <?php
                }

            ?>
        </tbody>

    </table>
</form>
<div class="row">
    <div class="col-md-6">
        <div class="dataTables_info">
            Rows Limit:
            <select id="numRecords" onChange="changeDisplayRowCount(this.value);">
                <option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "") {
                                        echo ' selected="selected"';
                                    } ?>>100</option>
                <option value="500" <?php if ($_REQUEST["show"] == 500) {
                                        echo ' selected="selected"';
                                    } ?>>500</option>
                <option value="1000" <?php if ($_REQUEST["show"] == 1000) {
                                            echo ' selected="selected"';
                                        } ?>>1000</option>
                <option value="2000" <?php if ($_REQUEST["show"] == 2000) {
                                            echo ' selected="selected"';
                                        } ?>>2000</option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="dataTables_paginate paging_simple_numbers">
            <ul class="pagination">
                <?php echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once "disconnect.php"; ?>