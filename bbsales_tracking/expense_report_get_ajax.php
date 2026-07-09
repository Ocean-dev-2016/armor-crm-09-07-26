<?php
$page_id=596;$page_slug='expense_report_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable     = "expense";
$ctable1    = "Expense";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    $sales_id = $db->rp_getData("sales_executive","id","name LIKE '%".$_REQUEST['searchName']."%'  AND isDelete=0","",0);
    if($sales_id){
        $SALESID = array();
        while ($sales_d=mysqli_fetch_assoc($sales_id)) {
            $SALESID[]=$sales_d['id'];
            # code...
        }
        $SALESID = implode(",", $SALESID);
        $ctable_where .=" sales_executive_id IN (".$SALESID.") AND ";
        // $ctable_where .="(sales_executive_id IN (".$SALESID.") OR ";
    }
    else
    {
        $ctable_where .=" sales_executive_id IN (0) AND ";
        // $ctable_where .="(sales_executive_id IN (0) OR ";
    }

    // $cat_id = $db->rp_getData("expence_category","id","name LIKE '%".$_REQUEST['searchName']."%'  AND isDelete=0","",0);
    // if($cat_id){
    //     $CATID = array();
    //     while ($cat_d=mysqli_fetch_assoc($cat_id)) {
    //         $CATID[]=$cat_d['id'];
    //         # code...
    //     }
    //     $CATID = implode(",", $CATID);
    //     $ctable_where .="category_id IN (".$CATID.")) AND ";
    // }
    // else
    // {
    //     $ctable_where .="category_id IN (0)) AND ";
    // }
}


if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="" && $_REQUEST['sales_executive_id']!="null")
{
    //$ctable_where .=" sales_executive_id='".$_REQUEST['sales_executive_id']."' AND ";
    $ctable_where .=" sales_executive_id IN (".$_REQUEST['sales_executive_id'].") AND ";
}

if(isset($_REQUEST['c_id']) && $_REQUEST['c_id']!="" && $_REQUEST['c_id']!="null")
{
    //$ctable_where .=" category_id='".$_REQUEST['c_id']."' AND ";
    $ctable_where .=" category_id IN (".$_REQUEST['c_id'].") AND ";
}

if(isset($_REQUEST['expense_status']) && $_REQUEST['expense_status']!="" && $_REQUEST['expense_status']!="null")
{
    $ctable_where .=" expense_status IN (".$_REQUEST['expense_status'].") AND ";
}

// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0 && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!=14)
// {
//     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
//     $ctable_where .= " sales_executive_id='".$check_id."' AND ";
// }
// else
// {
//     $ctable_where .= " ";   
// }

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{

    if($rights['personal_flag']==1)
    {
        $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
        $ctable_where .= "  sales_executive_id='".$check_id."' AND ";
    }
    else
    {
        if($rights['chain_vise_flag'] == 1)
        {

            $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

                $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
                if ($get_sales_type== "sales_manager") 
                {
                    $sales_executive_type = "Regional Sales Manager";
                    $key="sm_id";
                    $WhereCondition.=' ' .$key.'='.$check_id;
                }

                else if ($get_sales_type == "area_sales_manager") 
                {
                    $sales_executive_type = "National Sales Manager";//Business Development Manager
                    $key="asm_id";
                    $WhereCondition.=' ' .$key.'='.$check_id;
                }

                else if ($get_sales_type == "sales_officer") 
                {
                    $sales_executive_type = "Area Sales Manager";//Area Sales Manager
                    $key="so_id";
                    $WhereCondition.=' ' .$key.'='.$check_id;
                }
                else if ($get_sales_type == "sales_executive") 
                {
                    $sales_executive_type = "Sales Officer";
                    $key="se_id";
                    $WhereCondition.=' ' .$key.'='.$check_id;
                }
                else
                {
                    $WhereCondition.=' type = "service_engineer"';
                }

                $data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);

                $SALEID1=array();
                if($data)
                {
                    while($data_d=mysqli_fetch_assoc($data))
                    {
                        $SALEID1[]=$data_d['id'];
                    }
                }
                if(!empty($SALEID1))
                {
                    $SALEID1=implode(",", $SALEID1);
                    
                        $ctable_where .= "   sales_executive_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";  
                    
                    
                }
                else
                {
                        $ctable_where .= "   sales_executive_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";       
                }

        }
        else
        {
                

        }
    }

}

$ctable_where .= " isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
    $page_number = 1; //if there's no page number, set it to 1
}


if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
    $ctable_where .= " AND expense_date  <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
    $ctable_where .= " AND expense_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."')";
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
$imgExts = array("gif", "jpg", "jpeg", "png", "tiff", "tif");
?>
<style type="text/css">
.table-scrollable {
        width: auto;
        height: 450px;
        overflow-x: scroll;
        overflow-y: scroll;
        border: 1px solid #e7ecf1;
        margin: 10px 0 !important;
    }

    .fix-th
    {
        background-color: #f5f5f5 !important;position: sticky;top: 0; z-index: 1;
    }
    .fix-th1
    {
        background-color: #e5e5e5 !important;position: sticky;top: 0; z-index: 1;
    }
</style>
<!-- <button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</button>
<button type="button" class="btn green-haze export" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i> &nbsp; Excel</button> -->

<form action="" name="print_info1" id="print_info1" method="post">
    <div class="table-scrollable">
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead class="fix-th">
            <!-- --- milan --- 22-06-2021 --- -->
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td id="total-req-value"></td>
                <td id="total-pass-value"></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <!-- --- milan --- 22-06-2021 --- -->
            <tr>
                <th class="fix-th1">No.</th>
                <th class="fix-th1">Sales Person Name</th>
                <th class="fix-th1">Category Name</th>                
                <th class="fix-th1">Sub Category Name</th>                
                <th class="fix-th1">Start Km</th>
                <th class="fix-th1">End Km</th>
                <th class="fix-th1">Total Km</th>
                <th class="fix-th1">Request Amount</th>
                <th class="fix-th1">Passed Amount</th>
                <th class="fix-th1">Expense Status</th>
                <th class="fix-th1">Created Date</th>
                <th class="fix-th1">Remark</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(mysqli_num_rows($ctable_r)>0){
                $count = 0;
                while($ctable_d = mysqli_fetch_array($ctable_r)){
                //print_r($ctable_d);
            ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_executive_id']."'"); ?></td>
                <td><?php echo $db->rp_getValue("expence_category","name","id='".$ctable_d['category_id']."'",0); ?></td>  
                <td><?php echo $db->rp_getValue("expence_sub_category","name","id='".$ctable_d['subcategory_id']."'",0); ?></td>              
                <td><?php echo $db->rp_number_format($ctable_d['start_kilometer'],2); ?></td>
                <td><?php echo $db->rp_number_format($ctable_d['end_kilometer'],2); ?></td>
                <td><?php echo ($ctable_d['total_kilometer'])?$db->rp_number_format($ctable_d['total_kilometer'],2):""; ?></td>
                <td><?php echo $db->rp_num($ctable_d['total'],2) ?></td>
                <td><?php echo $db->rp_num($ctable_d['pass_expense_amount'],2) ?></td>
                

                <!-- --- milan --- 22-06-2021 --- -->
                <?php 
                $total_req_amount += $ctable_d['total'];
                $total_pass_amount += $ctable_d['pass_expense_amount'];
                ?>
                <!-- --- milan --- 22-06-2021 --- -->
                <td>
                    <?php if($ctable_d['expense_status']==0){echo "Pending"; }else if($ctable_d['expense_status']==1){echo "Passed"; } else if($ctable_d['expense_status']==2){echo "Rejected"; }?>
                </td>
                 
                <td><?php echo date("d-m-Y",strtotime($ctable_d['created_date'])); ?></td>
                <td><?php echo $ctable_d['remark']; ?></td>
            </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>
</form>
<div class="row">
    <div class="col-md-6">
        <div class="dataTables_info"> Rows Limit:
            <select id="numRecords" onChange="changeDisplayRowCount(this.value);">
                <option value="50" <?php if ($_REQUEST["show"] == 50 || $_REQUEST["show"] == "") {echo ' selected="selected"';}  ?>>50</option>
                <option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "") {echo ' selected="selected"';}  ?>>100</option>
                <option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {echo ' selected="selected"';}  ?>>500</option>  
                <option value="1000" <?php if ($_REQUEST["show"] == 1000) {echo ' selected="selected"';}  ?>>1000</option>
                <option value="2000" <?php if ($_REQUEST["show"] == 2000) {echo ' selected="selected"';}  ?>>2000</option>
                <option value="5000" <?php if ($_REQUEST["show"] == 5000) {echo ' selected="selected"';}  ?>>5000</option>
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

<!-- --- milan --- 22-06-2021 --- -->
<script type="text/javascript">
    $("#total-req-value").html("<?php echo $db->rp_number_format($total_req_amount,2); ?>");
    $("#total-pass-value").html("<?php echo $db->rp_number_format($total_pass_amount,2); ?>");
</script>
<!-- --- milan --- 22-06-2021 --- -->
<?php include("disconnect.php"); ?>