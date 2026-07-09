<?php
$page_id=651;$page_slug='deep_freezer_scheme';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
// $file_name  = "Complain_Report"."_".date("d-m-Y").".xlsx";

$file_name  = DEEP_FREEZER_EXPORT_EXCEL;
$Where = "";
$ctable_where = "";
$_REQUEST['searchName'] = urldecode($_REQUEST['searchName']); 

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  $ctable_where .= " (mobile_no like '%".$db->clean($_REQUEST['searchName'])."%') AND ";
}

$ctable_where .= " isDelete=0 ";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
  $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
  if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
  $page_number = 1; //if there's no page number, set it to 1
}
// print_r($_REQUEST["sales_executive"]);exit;
// if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!=undefined){
//   // $ctable_where .= " AND user_id='".$_REQUEST["sales_executive"]."'";
//   $ctable_where .= " AND user_id='".implode(" , ", $_REQUEST['sales_executive'])."'  ";
//   $sid = $_REQUEST["sales_executive"];
// }


// if(isset($_REQUEST["customer_id"]) && $_REQUEST["customer_id"]!="" && $_REQUEST["customer_id"]!=undefined){
//   // $ctable_where .= " AND customer_id='".$_REQUEST["customer_id"]."'";
//    $ctable_where .= " AND customer_id='".implode(" , ", $_REQUEST['customer_id'])."'  ";
//   $cid = $_REQUEST["customer_id"];
// }


if(isset($_REQUEST['status']) && $_REQUEST['status']!=""  && $_REQUEST['status']!='null') 
{
  // $ctable_where .= " AND status='".$_REQUEST['status_id']."' ";
  $ctable_where .= " AND status='".implode(" , ", $_REQUEST['status'])."'  ";
  $status=$_REQUEST['status'];
}

if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!=undefined && $_REQUEST["sales_executive"]!="null"){
  
  // $ctable_where .= " AND user_id='".$_REQUEST["sales_executive"]."'";
   // $ctable_where .= " AND user_id='".implode(" , ", $_REQUEST['sales_executive'])."'  ";
  $ctable_where .= " AND user_id IN (".$_REQUEST['sales_executive'].")";
  
}

if(isset($_REQUEST["customer_id"]) && $_REQUEST["customer_id"]!="" && $_REQUEST["customer_id"]!=undefined){
  $ctable_where .= " AND customer_id='".$_REQUEST["customer_id"]."'";
//   $cid = $_REQUEST["customer_id"];
 }

if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="" && $_REQUEST['class_id']!="null")
{
  //$ctable_where .= " AND class_id='".$_REQUEST['class_id']."' ";
  $ctable_where .= " AND class_id IN (".$_REQUEST['class_id'].") ";
  
}

if(isset($_REQUEST['area_id']) && $_REQUEST['area_id']!="" && $_REQUEST['area_id']!="null")
{
  //$ctable_where .= " AND area_id='".$_REQUEST['area_id']."' ";
  $ctable_where .= " AND area_id IN (".$_REQUEST['area_id'].")";
  
}



if(isset($_REQUEST['df1']) && $_REQUEST['df1']!=""){
  //echo $_REQUEST['df'];exit;
  $date_filter_query = urldecode( $_REQUEST['df1'] );

  $date_filter_query_ex=explode(" to ",$date_filter_query);

  $ctable_where .= " AND ( DATE(complain_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(complain_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    $ctable_where .= " AND (complain_assign_to = '".$check_id."' OR complain_created_by = '".$check_id."') ";
}
// $Where .= " isDelete=0";
$ctable1_r = $db->rp_getData("freezer_scheme","*",$ctable_where,"id DESC",0);

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
$column11  = 'L';   
 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Serial No");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Types Of Customer");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Customer");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Center");
  // $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Title");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Contact Person");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Mobile Number");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Distributor");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Order Amt");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Utr");
  $objPHPExcel->getActiveSheet()->setCellValue($column11.$rowCount, "Status");
 

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
   $status = array('0' => "Pending",'1' => "Approve");

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
      $value = $row['serial_no'];
    } 
    else if($j==2)
    {
      if($row['created_date']=="1970-01-01" || $row['created_date']=="0000-00-00")
        { $value = "";}
      else
      {
           $value = date("d-m-Y",strtotime($row['created_date']));
      }
    }
    else if($j==3)
    {
      $value = $db->rp_getValue("customer_type","name","id='".$row['executive_type']."'");
    }
    else if($j==4)
    {
      $value = $db->rp_getValue("executive","cname","id='".$row['customer_id']."'");

     
    }
    // else if($j==5)
    // {
    //   $value = $row['title'];
    // }
     else if($j==5)
    {
      $value = $row['center'];
    }
     else if($j==6)
    {
      $value =  $row['contact_person']; 
    }
     else if($j==7)
    {
      $value =  $row['mobile_no']; 
    }
    else if($j==8)
    {
      $value =  $db->rp_getValue("executive","company_name","id='".$row['distributor_agency']."'");
    }
    else if($j==9)
    { 
      $order_amt = $db->rp_getValue("orders","SUM(grand_total)","customer_id='".$row['customer_id']."' AND isDelete=0");
      $order_amt = ($order_amt)?$order_amt:0;
      $value = $db->rp_num($order_amt); 
    } 
    else if($j==10)
    {
      $value =  $row['utr']; 
    } 
    else if($j==11)
    {
      $value = $status[$row['status']];
    } 
    // else if($j==14)
    // {
    //   $value = $ENTRY_FLAG[$row['update_entry_flag']];
    // }
    
    $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
    $column++;
  }  
  $rowCount++;
}
// Redirect output to a client’s web browser (Excel5) 

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=".$file_name);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(FREEZER_REPORT_FILES.$file_name); 
$file_path1 = trim(ADMINFOLDER."/freezer_scheme_document/".$file_name);
$arr = array("file_path"=>$file_path1);
require_once 'disconnect.php'; 
echo json_encode($arr);
?>