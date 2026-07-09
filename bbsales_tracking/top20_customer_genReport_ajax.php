<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 

$file_name  = "Top_20_Customer_Report"."_".date("d-m-Y").".xlsx";
// $file_name  = CUSTOMERVISIT_EXPORT_EXCEL;
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  $_REQUEST['searchName']=urldecode($_REQUEST['searchName']);
  $company_name=$db->rp_getValue("executive","id","isDelete=0 AND company_name LIKE '%".$_REQUEST['searchName']."%' ",0);
  $c_name=$db->rp_getValue("executive","id","isDelete=0 AND cname LIKE '%".$_REQUEST['searchName']."%' ");
  $ctable_where .= " customer_id='".$company_name."' OR customer_id='".$c_name."' AND ";
}

if(isset($_REQUEST['df1']) && $_REQUEST['df1']!=""){
  // echo $_REQUEST['df'];exit;
  $date_filter_query = urldecode( $_REQUEST['df1'] );

  $date_filter_query_ex=explode(" to ",$date_filter_query);

  $ctable_where .= " ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  )  AND ";
}

$ctable_where.=" isDelete=0 AND (status = 1 OR status = 2 Or status = 4) GROUP BY customer_id ";
// $ctable1_r = $db->rp_getData("source_of_inquiry","*",$ctable_where,"id DESC",0);  
$ctable1_r = $db->rp_getData("orders","SUM(grand_total),customer_id",$ctable_where,"SUM(grand_total) DESC",0,"20");
 
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
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Company Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Customer Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Order Amount");
  
//end of adding column names  

$rowCount = 2; 
$count =0;
$order=0; 
while($row = mysqli_fetch_array($ctable1_r))  
{  
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
      $value = $db->rp_getValue("executive","company_name","isDelete=0 AND id='".$row['customer_id']."' ",0);;
    }
    else if($j==2)
    {
      $value = $db->rp_getValue("executive","cname","isDelete=0 AND id='".$row['customer_id']."' ",0);;
    }
    else if($j==3)
    {
      $value = $db->rp_num($row['SUM(grand_total)'],2);
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
// echo "tsetz"; exit;

?>