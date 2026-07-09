<?php
$page_id=569;$page_slug='dispatch_pages';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Dispatch Report"."_".date("d-m-Y").".xlsx";
$Where = "";

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  // $Where .= " (sales_name like '%".$db->clean($_REQUEST['searchName'])."%' OR company_name like '%".$db->clean($_REQUEST['searchName'])."%' ) AND ";
  $Where .= " (sales_name like '%".$db->clean($_REQUEST['searchName'])."%' OR company_name like '%".$db->clean($_REQUEST['searchName'])."%' OR dispatch_no like '%".$db->clean($_REQUEST['searchName'])."%' OR order_no like '%".$db->clean($_REQUEST['searchName'])."%' ) AND ";
}

if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL && $_REQUEST['status']!=null && $_REQUEST['status']!="NULL" && $_REQUEST['status']!="null" && $_REQUEST['status']!=UNDEFINED && $_REQUEST['status']!=undefined && $_REQUEST['status']!="UNDEFINED" && $_REQUEST['status']!="undefined")
{
  $Where .= " status = '".$_REQUEST['status']."' AND ";
}

///For ToDate & FromDate
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
    $Where .= " dispatch_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' AND ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
     $Where .= " dispatch_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' AND ";
}

if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!=""  && $_REQUEST['sales_id']!=NULL && $_REQUEST['sales_id']!=undefined)
{
  $Where .= " sales_id = '".$db->clean($_REQUEST['sales_id'])."' AND ";
}

if(isset($_REQUEST['company_name']) && $_REQUEST['company_name']!="")
{
  $Where .= " customer_id = '".$db->clean($_REQUEST['company_name'])."' AND ";
}

if(isset($_REQUEST['df1']) && $_REQUEST['df1']!="" && $_REQUEST['df1']!=NULL && $_REQUEST['df1']!=undefined)
{
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df1'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $Where .= " ( DATE(dispatch_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(dispatch_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) AND ";
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL && $_REQUEST['type']!=null && $_REQUEST['type']!="NULL" && $_REQUEST['type']!="null" && $_REQUEST['type']!=UNDEFINED && $_REQUEST['type']!=undefined && $_REQUEST['type']!="UNDEFINED" && $_REQUEST['type']!="undefined")
{
  $Where .= " order_type = '".$_REQUEST['type']."' AND ";
}

/*if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
  $ctable_where .= " ";
} else {
  $Where .= " sales_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "' AND ";
}*/
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    if($rights['personal_flag']==1)
    {
        $Where .=" created_by ='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."' AND ";
        $customer_type=$db->rp_getValue("sales_executive","customer_type","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
        $filter_where .=" AND id IN (".$customer_type.") ";
    }
    else
    {
        if($rights['all_data_flag']==1)
        {
            
        }
        else
        {
            $customer_type=$db->rp_getValue("sales_executive","customer_type","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
            //$CustomerType = implode(",", $customer_type);
            $Order_IDS_r=$db->rp_getData("orders","*","customer_type IN (".$customer_type.") ","",0);
            $ORDER_IDS = array();
            while($Order_IDS_d = mysqli_fetch_array($Order_IDS_r))
            {
                $ORDER_IDS[] = $Order_IDS_d['id'];
            }
            $order_ids= implode(",", $ORDER_IDS);
            // print_r($order_ids);exit;
            $Where .=" order_id IN (".$order_ids.") AND ";
        }   
    }
}
$Where .=" isDelete=0 ";




// if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="")
// {
// 	$Where .= "  sales_id = '".$db->clean($_REQUEST['sales_executive_id'])."' AND";
// }

// if(isset($_REQUEST['customer_type']) && $_REQUEST['customer_type']!="")
// {
// 	$Where .= "  customer_type = '".$db->clean($_REQUEST['customer_type'])."' AND";
// }

//for admin login

// for customer login


$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
//status
// if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
// {
//  $Where .= " AND  status = '".$_REQUEST['status']."' ";
// }
// ///For ToDate & FromDate
// if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL && $_REQUEST['ToDate']!=undefined)
// {
//   $Where .= " AND order_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
// }

// if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL && $_REQUEST['FromDate']!=undefined)
// {
//      $Where .= " AND order_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
// }
// if(isset($_REQUEST['df']) && $_REQUEST['df']!="" && $_REQUEST['df']!=NULL && $_REQUEST['df']!=undefined)
// {
// 	//echo $_REQUEST['df'];exit;
// 	$date_filter_query = urldecode( $_REQUEST['df'] );

// 	$date_filter_query_ex=explode(" to ",$date_filter_query);

// 	$Where .= " AND  ( DATE(order_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(order_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
// }

// if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
// {
//  $Where .= " AND  customer_type = '".$_REQUEST['type']."' ";
// }

// if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
// {
//  $Where .= " AND  sales_id = '".$_REQUEST['sales_id']."' ";
// }

// if(isset($_REQUEST['qid']) && $_REQUEST['qid']!="")
// {
//   $Where .= " AND   quotation_id = '".$db->clean($_REQUEST['qid'])."' ";
// }




// $Where .= " AND isDelete=0";
$ctable1_r = $db->rp_getData("dispatch_detail","*",$Where,"id DESC",0);

// Instantiate a new PHPExcel object 
$objPHPExcel = new PHPExcel();  

// Set the active Excel worksheet to sheet 0 
$objPHPExcel->setActiveSheetIndex(0);  
// Initialise the Excel row number 
$rowCount = 1;  

//start of printing column names as names of mysqli fields  
$column   = 'A';
$column1  = 'B';
$column2  = 'C';
$column3  = 'D';
$column4  = 'E';
$column5  = 'F';
$column6  = 'G';
$column7  = 'H';
$column8  = 'I';
$column9  = 'J';
$column10  = 'K';

 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");

  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Dispatch No");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Order No");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Dispatch Qty");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Amount");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Company Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Sales Person Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Order Type");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Dispatch Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Status");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Transport Charge");
  
//end of adding column names  

$rowCount = 2; 
$count =0;

$customer_type = array('1' => "Super Stockist",'2' => "Distributor",'3' => "Dealer",'3' => "B2B Customer" ,'normal_user' => "Normal Customer");

$status_array = array("0"=>"Pending","1"=>"Complete","2"=>"Packing Slip Created");

while($row = mysqli_fetch_assoc($ctable1_r))  
{  
  $count++;
  $column = 'A';
  for($j=0; $j<11;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      $value = stripslashes($row['dispatch_no']);
    }
    else if($j==2)
    {
      $value = $db->rp_getValue("orders","order_no","id='".$row['order_id']."'");
    }
    else if($j==3)
    {
      $value = stripslashes($row['dispatch_qty']);
    }
    else if($j==4)
    {
      $value = stripslashes($db->rp_num($row['grand_total']));
    }
    else if($j==5)
    {
      $value = stripslashes($row['company_name']);
    }
    else if($j==6)
    {
    
        $value = stripslashes($row['sales_name']);
        
    }
    else if($j==7)
    {
        if($row['order_type']==1)
          {
            $type = "Super Stockist"; 
          }
          else if ($row['order_type']==2)
          {
            $type = "Distributor"; 
          }
          else if ($row['order_type']==3)
          {
            $type = "Dealer"; 
          }
          else if ($row['order_type']==4)
          {
            $type = "B2B Customer"; 
          }
          else if ($row['order_type']==6)
          {
            $type = "B2C Customer"; 
          }
          else if ($row['order_type']="normal_user")
          {
            $type = "Normal Customer"; 
          }
           $value = $type;
    }
    else if($j==8)
    {
      $value = date('d-m-Y',strtotime($row['dispatch_date']));
    }
    else if($j==9)
    {
      $value = $status_array[$row['status']];
    }
    else if($j==10)
    {
      $value = $db->rp_getValue("orders","transport_charge","id='".$row['order_id']."'");
    }
  
    $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
    $column++;
  }  
  $rowCount++;
}

// Redirect output to a client’s web browser (Excel5) 
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=".$file_name);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(INQUIRY_REPORT_FILES.$file_name); 
$file_path1 = trim(ADMINFOLDER."/inquiry_documents/".$file_name);
$arr = array("file_path"=>trim($file_path1));
require_once 'disconnect.php';
echo json_encode($arr);
?>