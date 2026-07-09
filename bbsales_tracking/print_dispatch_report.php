<?php
$page_id=569;$page_slug='dispatch_pages';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "dispatch_detail";
$ctable1 	= "Dispatch_detail";
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	// $ctable_where .= " (sales_name like '%".$db->clean($_REQUEST['searchName'])."%' OR company_name like '%".$db->clean($_REQUEST['searchName'])."%' ) AND ";
  $ctable_where .= " (sales_name like '%".$db->clean($_REQUEST['searchName'])."%' OR company_name like '%".$db->clean($_REQUEST['searchName'])."%' OR dispatch_no like '%".$db->clean($_REQUEST['searchName'])."%' OR order_no like '%".$db->clean($_REQUEST['searchName'])."%' ) AND ";
}
//for admin login


$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
//status
$ctable_where .="isDelete=0";

if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL && $_REQUEST['status']!=undefined)
{
 	$ctable_where .= " AND status = '".$_REQUEST['status']."' ";
}

///For ToDate & FromDate
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
  	$ctable_where .= " AND dispatch_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
     $ctable_where .= " AND dispatch_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL && $_REQUEST['type']!=undefined)
{
 	$ctable_where .= " AND order_type = '".$_REQUEST['type']."' ";
}

/*if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
	$ctable_where .= " ";
} else {
	$ctable_where .= " AND sales_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
}*/
// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
//     if($rights['personal_flag']==1)
//     {
//         $ctable_where .=" AND created_by ='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."' ";
//         $customer_type=$db->rp_getValue("sales_executive","customer_type","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
//         $filter_where .=" AND id IN (".$customer_type.") ";
//     }
//     else
//     {
//         if($rights['all_data_flag']==1)
//         {
            
//         }
//         else
//         {
//             $customer_type=$db->rp_getValue("sales_executive","customer_type","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
//             //$CustomerType = implode(",", $customer_type);
//             $Order_IDS_r=$db->rp_getData("orders","*","customer_type IN (".$customer_type.") ","",0);
//             $ORDER_IDS = array();
//             while($Order_IDS_d = mysqli_fetch_array($Order_IDS_r))
//             {
//                 $ORDER_IDS[] = $Order_IDS_d['id'];
//             }
//             $order_ids= implode(",", $ORDER_IDS);
//             // print_r($order_ids);exit;
//             $ctable_where .=" AND order_id IN (".$order_ids.")  ";
//         }   
//     }
// }




if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{

     
     if($rights['personal_flag']==1)
     {

        $ctable_where .= " AND sales_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";

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
                    
                        $ctable_where .= " AND sales_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")"; 
                    
                    
                }
                else
                {
                        $ctable_where .= " AND sales_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";      
                }
        }
        else
        {
            
        }
    }
  
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!=""  && $_REQUEST['sales_id']!=NULL && $_REQUEST['sales_id']!=undefined)
{
  $ctable_where .= " AND sales_id = '".$db->clean($_REQUEST['sales_id'])."' ";
}

if(isset($_REQUEST['company_name']) && $_REQUEST['company_name']!="")
{
  $ctable_where .= " AND customer_id = '".$db->clean($_REQUEST['company_name'])."' ";
}

if(isset($_REQUEST['df1']) && $_REQUEST['df1']!="" && $_REQUEST['df1']!=NULL && $_REQUEST['df1']!=undefined)
{
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df1'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(dispatch_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(dispatch_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
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
                <th>No</th>
                <th>Dispatch No</th>
                <th>Order No</th>
                <th style="text-align:right;">Dispatch Qty</th>
				<!-- <th style="text-align:right;">Amount</th> -->
                <th class="fix-th1" style="text-align:right;">Transport Charge</th>
                <th>Company Name</th>
                <th>Sales Person Name</th>
                <th>Order Type</th>
				<th>Dispatch Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if($ctable_r){
            $count = 0;
            $sales_name='';
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $dispatch_status_array = array("0"=>"Pending","1"=>"Complete","2"=>"Packing Slip Created");
         
			
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?php echo stripslashes($ctable_d['dispatch_no']); ?></td>
				<td><?php
					$order_no=$db->rp_getValue("orders","order_no","id='".$ctable_d['order_id']."'");
					echo $order_no; ?>
				</td>
                <td align="right"><?php echo stripslashes($ctable_d['dispatch_qty']); ?></td>
                <!-- <td align="right"><?php echo stripslashes($db->rp_num($ctable_d['grand_total'])); ?></td> -->
                <td align="right"><?php echo CURR.$db->rp_getValue("orders","transport_charge","id='".$ctable_d['order_id']."'"); ?></td>
                <td><?php echo stripslashes($ctable_d['company_name']); ?></td>
                <td><?php 
				if($ctable_d['sales_name']=="")
				{
					echo "--";
				}
				else
				{
				echo stripslashes($ctable_d['sales_name']); ?></td>
				<?php
				}
				?>
                <td><?php if($ctable_d['order_type']=='1'){ $slug="Super Stockist";}else if($ctable_d['order_type']=='2'){$slug="Distributor";}else if($ctable_d['order_type']=='3'){
                            $slug="Dealer";}else if($ctable_d['order_type']=='4'){$slug="B2B Customer";}else if($ctable_d['order_type']=='6'){$slug="B2C Customer";}else if($ctable_d['order_type']=='normal_user'){$slug="Normal Customer";}echo stripslashes($slug);?></td>
                <td><?php echo date('d-m-Y',strtotime($ctable_d['dispatch_date'])); ?></td>
                <td><?php echo $dispatch_status_array[$ctable_d['status']]; ?></td>
            </tr>
        <?php
            }
        }
		
        ?>
        </tbody>
    </table>