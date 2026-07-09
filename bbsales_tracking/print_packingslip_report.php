<?php
$page_id   = 612;
$page_slug = 'packing_slip';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable     = "packing_slip";
$ctable1    = "Packing Slip";
$ctable_where = "";
$ctable_where .=" isDelete=0 ";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    // $ctable_where .= " (sales_name like '%".$db->clean($_REQUEST['searchName'])."%' OR company_name like '%".$db->clean($_REQUEST['searchName'])."%' ) AND ";
    $dispatch_no_d=$db->rp_getValue("dispatch_detail","id"," dispatch_no LIKE '%".$_REQUEST['searchName'] ."%' ",0);
    $_REQUEST['searchName'] = $db->clean($_REQUEST['searchName']);
    $ctable_where .= " AND ( packing_slip_no like '%" . $_REQUEST['searchName'] . "%' OR dispatch_id = '".$dispatch_no_d."'  )";
}
//for admin login
// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
//     if($rights['personal_flag']==1)
//     {
//         $ctable_where .=" AND created_by ='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."' ";
//         $customer_type=$db->rp_getValue("sales_executive","type","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
//         $filter_where .=" AND id IN (".$customer_type.") ";
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
//             // $ctable_where .=" AND customer_type IN (".$customer_type.")  ";
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

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
    $page_number = 1; //if there's no page number, set it to 1
}
//status


if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL && $_REQUEST['status']!=undefined)
{
    $ctable_where .= " AND status = '".$_REQUEST['status']."' ";
}



if(isset($_REQUEST['df1']) && $_REQUEST['df1']!="" && $_REQUEST['df1']!=NULL && $_REQUEST['df1']!=undefined)
{
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df1'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(packing_slip_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(packing_slip_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}


$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);
?>

<style>
.mainDiv, table{
  height: auto;   
  width:100%;
  font-family: Calibri,sans-serif;
  font-style: normal;
  font-weight: 400;
  padding: 0;
  text-decoration: none;
  font-size: 10pt;
  margin:auto;
  padding:auto;
}
tr{height: 30px;}
table , td, th { border-collapse: collapse;border: 1px solid #000;}
td, th {  padding: 5px;}
th { border: 1px solid #595959;background: #f0e6cc;}
.text-right{text-align: right;}
.center{text-align:center;}
.space{ padding: 10px;}
.no-border{border-bottom: 1px solid #fff;}
h2
{
    text-transform: uppercase;
    margin-bottom: 0px;
}
</style>

<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
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
        if($ctable_r){
            $count = 0;
            $sales_name='';
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
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
                <td><?php echo ++$count; ?></td>
                <td><?=$ctable_d['packing_slip_no'];?></td>

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
                    //$sales_id=$db->rp_getValue('dispatch_detail','sales_id','id="'.$ctable_d['dispatch_id'].'" AND isDelete=0 AND isActive=1');
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