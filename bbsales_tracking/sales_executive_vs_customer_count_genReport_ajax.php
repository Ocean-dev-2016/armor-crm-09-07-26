<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 

$file_name  = "Sales_executive_Vs_Customer_count_Report"."_".date("d-m-Y").".xlsx";
// $file_name  = CUSTOMERVISIT_EXPORT_EXCEL;
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  $_REQUEST['searchName']=urldecode($_REQUEST['searchName']);
  $ctable_where .= " ( name like '%".$db->clean($_REQUEST['searchName'])."%' ) AND ";
}

if(isset($_REQUEST['df1']) && $_REQUEST['df1']!=""){
  // echo $_REQUEST['df'];exit;
  $date_filter_query = urldecode( $_REQUEST['df1'] );

  $date_filter_query_ex=explode(" to ",$date_filter_query);

  $ctable_where .= " ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  )  AND ";
}

$ctable_where .= " isDelete=0";
$ctable1_r = $db->rp_getData("sales_executive","*",$ctable_where,"id DESC",0);  
 
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
 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Id");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Sales Person Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Customer Count");
  
//end of adding column names  

$rowCount = 2; 
$count =0;
$order=0; 
while($row = mysqli_fetch_array($ctable1_r))  
{  
  $count++;
  $column = 'A';
  for($j=0; $j<3;$j++)  
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
      $value = $db->rp_getTotalRecord("executive","isDelete=0 AND seid='".$row['id']."' ");
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
require_once("disconnect.php");
echo json_encode($arr); 
// echo "tsetz"; exit;

?>