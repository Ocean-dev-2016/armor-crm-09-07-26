<?php
$page_id   = 612;
$page_slug = 'packing_slip';
include("connect.php");
$ctable       = "packing_slip";

$ctable_where .= "isDelete=0";
if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") 
{
    $dispatch_no_d=$db->rp_getValue("dispatch_detail","id"," dispatch_no LIKE '%".$_REQUEST['searchName'] ."%' ",0);
    $_REQUEST['searchName'] = $db->clean($_REQUEST['searchName']);
    $ctable_where .= " AND ( packing_slip_no like '%" . $_REQUEST['searchName'] . "%' OR dispatch_id = '".$dispatch_no_d."'  )";
}

// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
//     if($rights['personal_flag']==1)
//     {
//         $ctable_where .=" AND created_by ='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."' ";


//         // $customer_type=$db->rp_getValue("sales_executive","type","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
//         // $filter_where .=" AND id IN (".$customer_type.") ";
//     }
//     else
//     {
//         if($rights['all_data_flag']==1)
//         {
            
//         }
//         else
//         {
//             // $customer_type=$db->rp_getValue("sales_executive","customer_type","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
//             // //$CustomerType = implode(",", $customer_type);
//             // $ctable_where .="  customer_type IN (".$customer_type.") AND ";
//             // $filter_where .="  AND id IN (".$customer_type.")  ";
//             // $sales_type_r=$db->rp_getData("sales_executive","*","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'","",0);
//             // $sales_type_d = mysqli_fetch_array($sales_type_r);
//             // $SALESTYPE = array('1' => "sales_manager",'2' => "area_sales_manager",'3' => "sales_officer",'4' => "sales_executive",'6' => "service_executive", );

//             // $sales_type['sales_type'] = explode(",", $sales_type_d['sales_type']);
//             // $S_Type = array();
//             // foreach ($sales_type['sales_type'] as $key => $value) {
//             //     $S_Type[] = $SALESTYPE[$value];
//             // }
//             // $sales_type['sales_type'] = implode("','", $S_Type);
//             // $filter_where_se .="  AND type IN ('".$sales_type['sales_type']."')  ";
//         }   
//     }
// }


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{

     
     if($rights['personal_flag']==1)
     {


        $ctable_where .= " AND created_by='" . $_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] . "'";

     }
     else
     {


        if($rights['chain_vise_flag'] == 1)
        {
            // echo "test";exit();


                $check_id=$db->rp_getValue("dealer_distributor_network","sales_executive_id","isDelete=0 AND sales_executive_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'");

                // $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

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
                        $get_created_by_ids=$db->rp_getData("dealer_distributor_network","id","isDelete=0 AND sales_executive_id IN (".$SALEID1.")");
                        $cerated_by_arr=array();
                        if($get_created_by_ids)
                        {
                            while($data_d_c=mysqli_fetch_assoc($get_created_by_ids))
                            {
                                $cerated_by_arr[]=$data_d_c['id'];
                            }
                        }

                        if(!empty($cerated_by_arr))
                        {

                            $cerated_by_arr=implode(",", $cerated_by_arr);
                    
                            $ctable_where .= " AND created_by IN (".$cerated_by_arr.','.$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'].")"; 

                        }
                        else
                        {
                             $ctable_where .= " AND created_by IN (".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'].")";  
                        }

                    
                        // $ctable_where .= " AND sales_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")"; 
                }
               
        }
        
    }
  
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!="" && $_REQUEST['df']!=NULL && $_REQUEST['df']!=undefined)
{
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(packing_slip_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(packing_slip_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}

if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL && $_REQUEST['status']!=undefined)
{
    $ctable_where .= " AND status = '".$_REQUEST['status']."' ";
}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
    $page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "id DESC limit $page_position, $item_per_page",0);
?>
<!-- <style>
.table-scrollable 
{
    width: auto;
    height: 600px;
    overflow-x: scroll;
    overflow-y: scroll;
    border: 1px solid #e7ecf1;
    margin: 10px 0 !important;
}
</style> -->
<style type="text/css">
    .fix-th
    {
        background-color: #f5f5f5 !important;position: sticky;top: 0; z-index: 1;
    }
    .fix-th1
    {
        background-color: #e5e5e5 !important;position: sticky;top: 0; z-index: 1;
    }
</style>
<form action="" name="frm" id="frm" method="post">
    <div class="table-responsive">
        <table id="datatable_1" class="table table-striped table-bordered table-hover">
            <thead class="fix-th">
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>
                        <label>Filter By Date</label>
                        <!-- <div class="input-group">
                            <input class="form-control datetimerange-picker-input" id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
                            <span class="input-group-addon datetimerange-picker-btn">
                            <i class="fa fa-calendar"></i>
                            </span>
                                        
                            <span class="input-group-btn">
                                <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
                            </span>
                        </div> -->

                        <div class="input-group">
                            <input class="form-control datetimerange-picker-input" id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
                            <span class="input-group-addon datetimerange-picker-btn">
                                <i class="fa fa-calendar"></i>
                            </span>

                            <span class="input-group-btn">
                                <!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
                            </span>
                        </div>
                    </th>
                    <th></th>
                    <th></th>
                    <th>
                        <select class="form-control input-small" id="status">
                            <option value="">Select Status</option>
                            <option value="0" <?=("0"==$_REQUEST['status'])?"selected":"";?>>Pending</option>
                            <option value="1" <?=("1"==$_REQUEST['status'])?"selected":"";?>>Invoice Generated</option>
                        </select>
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <th style="width: 5%;"></th>
                    <th style="width: 5%;">No.</th>
                    <th>Packing Slip No.</th>
                    <th>Packing Slip Date</th>
                    <th>Dispatch No.</th>
                    <th>Invoice No.</th>
                    <th>Status</th>
                    <th>Company Name</th>
                    <th>Customer Type</th>
                    <th>Sales Person Name</th>
                    <th>Total Baggage</th>
                    <th>Total Item Qty</th>
                    <th>Total Baggage Weight</th>
                    <th>Actual Baggage Weight</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($ctable_r) {
                    $count = 0;
                    while ($ctable_d = mysqli_fetch_array($ctable_r)) {
                        $packing_slip_status_array = array("0"=>"Pending","1"=>"Invoice Generate");
                        if ($ctable_d['status'] == 1) 
                        {
                            $style = "style='background-color: #FFFF99;'";
                        }
                        else
                        {
                            $style = "style='background-color: #add8e6;'";
                        }
                    ?>
                        <tr <?= $style ?>>
                            <td>
                                <?php
                                if ($rights['update_flag'] == 1) {
                                ?>
                                    <div class="btn-group">
                                        <button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
                                            <i class="fa fa-gear"></i>
                                        </button>
                                        <ul role="menu" class="dropdown-menu">
                                            <?php
                                            if($ctable_d['status']==0)
                                            {
                                                ?>
                                                <li>
                                                   <a href="packing_slip_crud_new.php?mode=edit&dispatch_id=<?=$ctable_d['dispatch_id'];?>&packing_id=<?=$ctable_d['id'];?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i>&nbsp;Edit</span></a>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                            <?php
                                            if ($rights['delete_flag'] == 1 && $ctable_d['id'] != -1 && $ctable_d['status']==0) 
                                            {
                                                ?>
                                                <li>
                                                    <a onClick="del_conf('<?=$ctable_d['id'];?>');" title="Delete"><span class="text-danger"><i class="fa fa-times"></i>&nbsp;Delete</span>
                                                    </a>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                            <li>
                                                <?php
                                                $file_path="view_packing_slip.php?id=".$ctable_d['id'];
                                                if($rights['update_flag']==1)
                                                {
                                                    ?>
                                                    <a target="_blank" href="<?php echo $file_path;?>"  title="save"><span class="text-success"><i class="fa fa-file-pdf-o"></i>&nbsp;Download</span>
                                                    </a>
                                                    <?php 
                                                }
                                                ?>
                                            </li>
                                            <li>
                                                <?php
                                                if($ctable_d['status']==0)
                                                {
                                                    ?>
                                                    <!-- <a class="" target="_blank" href="invoice_new_crud.php?mode=add&packing_slip_id=<?php echo $ctable_d['id'] ?>" title="save"><i class="fa fa-file-pdf-o"></i> Generate Invoice</a> -->
                                                    <a class="" target="_blank" href="invoice_crud_new.php?mode=add&packing_slip_id=<?php echo $ctable_d['id'] ?>" title="save"><i class="fa fa-file-pdf-o"></i> Generate Invoice</a>
                                                    <?php 
                                                }
                                                ?>
                                            </li>
                                        </ul>
                                    </div>
                                <?php
                                }
                                ?>
                            </td>
                            <td><?=++$count;?></td>

                            <td><a target="_blank" href='view_packing_slip.php?id=<?= $ctable_d['id'] ?>'><?=$ctable_d['packing_slip_no'];?></a></td>

                            <td><?=date("d-m-Y",strtotime($ctable_d['packing_slip_date']));?></td>

                            <td>
                                <?php
                                    $mainArray = array();
                                    $dids = explode(",",$ctable_d['dispatch_id']);
                                    foreach ($dids as $key => $value) {
                                        $mainArray[$key][] = "<a href='view_dispatch.php?id=".$value."'>";
                                        $mainArray[$key][] = $db->rp_getValue("dispatch_detail","dispatch_no","id IN (".$value.")",0);

                                        $invoice_no=$db->rp_getValue("invoice_new","invoice_no","dispatch_ids='".$value."'",0);
                                        $invoice_id=$db->rp_getValue("invoice_new","id","dispatch_ids='".$value."'",0);

                                        $mainArray[$key][] = "</a>";
                                        $mainArray[$key] = implode("", $mainArray[$key]);
                                    }
                                    echo implode(",<br/>", $mainArray);
                                ?>
                            </td>
                            <td><a href='invoice_viewer.php?invoice_id=<?= $invoice_id ?>'><?php echo $invoice_no; ?></a></td>
                            <td><?php echo $packing_slip_status_array[$ctable_d['status']] ?></td>
                            <td><?=$db->rp_getValue('executive','company_name','id="'.$ctable_d['customer_id'].'" AND isDelete=0 AND isActive=1')?></td>
                            <td>
                                <?php 
                                $type =  $db->rp_getValue('executive','type_of_executive','id="'.$ctable_d['customer_id'].'" AND isDelete=0 AND isActive=1');
                                if($type=='1'){ $slug="Super Stockist";}else if($type=='2'){$slug="Distributor";}else if($type=='3'){$slug="Dealer";}else if($type=='4'){$slug="B2B Customer";}else if($type=='6'){$slug="B2C Customer";}else if($type=='normal_user'){$slug="Normal Customer";}echo stripslashes($slug)
                                ?>
                                
                            </td>
                            <td><?php 
                                // $sales_id=$db->rp_getValue('dispatch_detail','sales_id','id="'.$ctable_d['dispatch_id'].'" AND isDelete=0 AND isActive=1');
                                $sales_id=$db->rp_getValue('dealer_distributor_network','sales_executive_id','id="'.$ctable_d['created_by'].'" ');
                                echo $db->rp_getValue('sales_executive','name','id="'.$sales_id.'" AND isDelete=0 AND isActive=1');
                                ?>
                            </td>

                            <td class="text-right">
                                <?=$db->rp_num($db->rp_getValue('packing_slip_item','MAX(main_carton_type_count)','packing_slip_id="'.$ctable_d['id'].'" AND isDelete=0 AND isActive=1'),3);?>
                            </td>

                            <td class="text-right">
                                <?=$db->rp_num($db->rp_getValue('packing_slip_item','SUM(pro_qty)','packing_slip_id="'.$ctable_d['id'].'" AND isDelete=0 AND isActive=1'),3);?>
                            </td>

                            <td class="text-right">
                                <?php
                                    $Mdata = $db->rp_getData('packing_slip_item','main_carton_type_weight','packing_slip_id="'.$ctable_d['id'].'" AND isDelete=0 AND isActive=1 GROUP BY main_carton_type_count');
                                    $total = 0;
                                    while ( $MdataD = mysqli_fetch_assoc($Mdata) )
                                    {
                                        $total += $MdataD['main_carton_type_weight'];
                                    }
                                ?>
                                <?=$db->rp_num( $total+$db->rp_getValue('packing_slip_item','SUM(pro_weight)','packing_slip_id="'.$ctable_d['id'].'" AND isDelete=0 AND isActive=1'),3);?>
                            </td>

                            <td class="text-right">
                                <?php
                                    $Mdata = $db->rp_getData('packing_slip_item','main_carton_whole_actual_weight','packing_slip_id="'.$ctable_d['id'].'" AND isDelete=0 AND isActive=1 GROUP BY main_carton_type_count');
                                    $total = 0;
                                    while ( $MdataD = mysqli_fetch_assoc($Mdata) )
                                    {
                                        $total += $MdataD['main_carton_whole_actual_weight'];
                                    }
                                ?>
                                <?=$db->rp_num($total,3);?>
                            </td>
                            
                        </tr>
                    <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="dataTables_info"> Rows Limit:
                <select id="numRecords" onChange="changeDisplayRowCount(this.value);">
                    <option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "") {echo ' selected="selected"';}  ?>>100</option>
                    <option value="200" <?php if ($_REQUEST["show"] == 200) {echo ' selected="selected"';}  ?>>200</option>
                    <option value="300" <?php if ($_REQUEST["show"] == 300) {echo ' selected="selected"';}  ?>>300</option>
                    <option value="400" <?php if ($_REQUEST["show"] == 400) {echo ' selected="selected"';}  ?>>400</option>
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
</form>
<script type="text/javascript">
    $(".filterBtn").on("click",function()
    {
        sales_executive = $("#sales_executive").val();
        customer_id = $("#customer_id").val();
        df1=$("#material_request_filter_input").val();
        df1 = encodeURI(df1)
        displayRecords(100,1);
    })
    $(".datetimerange-picker-btn").on("click",function(){
        $(".datetimerange-picker-input",$(this).closest(".date")).focus();
    });
    $(".datetimerange-picker-input").daterangepicker({"format":"dd-mm-yy ",autoUpdateInput: false,timePicker:false,ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
}});
    $('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
 $(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY')+" to "+picker.endDate.format('DD-MM-YYYY'));
});
</script>
<script type="text/javascript">
    $("#status").select2(); 
</script>
<?php require_once 'disconnect.php';  ?>