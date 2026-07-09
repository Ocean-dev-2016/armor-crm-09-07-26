<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 

$file_name  = "Lead_Source_Report".date('d_m_Y')."_".strtotime("now").".xlsx";
// $file_name  = CUSTOMERVISIT_EXPORT_EXCEL;
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){

  $sales_id = $db->rp_getData("sales_executive","*","name LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
  if($sales_id)
  {   
    while($K=mysqli_fetch_assoc($sales_id))
    {
      $USER_IDS[]=$K['id'];
    }
    $USER_IDS=implode(",",$USER_IDS);
    $ctable_where .="user_id IN (".$USER_IDS.") ";
  }
  else
  {
    $ctable_where .="user_id IN (0) ";
  }

  $customer_id = $db->rp_getData("executive","*","cname LIKE '%".$_REQUEST['searchName']."%' OR phone LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
  if($customer_id)
  {   
    while($K1=mysqli_fetch_assoc($customer_id))
    {
      $CUSTOMER_IDS[]=$K1['id'];
    }
    $CUSTOMER_IDS=implode(",",$CUSTOMER_IDS);
    $ctable_where .=" OR customer_id IN (".$CUSTOMER_IDS.") ";
  }
  else
  {
    $ctable_where .=" OR customer_id IN (0)  ";
  }
  $ctable_where .=" AND ";
}


  

if(isset($_REQUEST['df1']) && $_REQUEST['df1']!=""){
  // echo $_REQUEST['df'];exit;
  $date_filter_query = urldecode( $_REQUEST['df1'] );

  $date_filter_query_ex=explode(" to ",$date_filter_query);

  $ctable_where .= " ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  )  AND ";
}

$ctable_where .= " isDelete=0";
$ctable1_r = $db->rp_getData("source_of_inquiry","*",$ctable_where,"id DESC",0);  
 
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
 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Id");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Source Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Lead Source Count");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Order Value");
  
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_array($ctable1_r))  
{  
  $order=0; 
  $count++;
  $column = 'A';
  for($j=0; $j<4;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    } 
    else if($j==1)
    {
      $value = $row['name'];
    }
    else if($j==2)
    {
      $value = $db->rp_getTotalRecord("no_order_inquiry","isDelete=0 AND inquiry_lead_flag=1 AND source_of_inquiry='".$row['id']."' ");
    }
    else if($j==3)
    {
      $lead_r=$db->rp_getData("no_order_inquiry","*","isDelete=0 AND inquiry_lead_flag=1 AND source_of_inquiry='".$row['id']."' GROUP BY dealer_id","",0);
      while($lead_d=mysqli_fetch_assoc($lead_r))
      {
        $order+=$db->rp_getValue("orders","SUM(grand_total)","isDelete=0 AND (status = 1 OR status = 2 Or status = 4) AND customer_id='".$lead_d['dealer_id']."' ",0);
      }
      if($order!='null' || $order!="")
      {
        $value = $order;
      }
      else
      {
        $value = 0;
      }
    }
    else
    {
      $value = $row[$j];   
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
$arr = array("file_path"=>$file_path1);
require_once 'disconnect.php';
echo json_encode($arr); 

?>