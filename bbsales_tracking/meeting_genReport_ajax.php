<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Meeting_Report"."_".date("d-m-Y").".xlsx";
$Where = "";

// Get the total number of rows in the table

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) 
{
  $Query=$_REQUEST['searchName'];
  
  $Where.=" (title LIKE '%".$Query."%' )" ;

  $customer_Data = $db->rp_getData("executive","id","company_name LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
  if($customer_Data)
  {
      while($customer_Data_d=mysqli_fetch_assoc($customer_Data))
    {
      $customer_ids[]=$customer_Data_d['id'];
    }
    $customer_ids=implode(",",$customer_ids);
    $Where .= " OR customer_id IN (".$customer_ids.") AND";
  }
  else
  {
    $Where .= " customer_id IN ('') AND"; 
  }

}

if(isset($_REQUEST["type"]) && $_REQUEST["type"]!="" && $_REQUEST["type"]!=undefined)
{
  $Where .= " meeting_type LIKE '%".trim($_REQUEST["type"])."%' AND";
  $type = $_REQUEST['type'];
}

$Where .= " isDelete=0";
$ctable1_r = $db->rp_getData("meeting","id,meeting_type,customer_id,meeting_date,meeting_venue,gift_details,expence",$Where,"id DESC",0);

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


 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Meeting Type");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Customer");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Meeting Date & Time");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Meeting Address");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Gift Details");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Expence");
//end of adding column names  

$rowCount = 2; 
$count =0; 

while($row = mysqli_fetch_assoc($ctable1_r))  
{  
  $count++;
  $column = 'A';
  for($j=0; $j<7;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      $value = $db->rp_getValue("meeting_type","name","slug='".$row['meeting_type']."'",0);
    } 
    else if($j==2)
    {
      $value = $db->rp_getValue("executive","company_name","id='".$row['customer_id']."'",0);
    }
    else if($j==3)
    {
      $value = date("d-m-Y H:i:s",strtotime($row['meeting_date']));
    }
    else if($j==4)
    {
      $value = $row['meeting_venue'];
    }
    else if($j==5)
    {
      $value = $row['gift_details'];
    }
    else if($j==6)
    {
      $value = $row['expence'];
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