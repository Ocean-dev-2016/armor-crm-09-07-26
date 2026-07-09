<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Sales Officer Report"."_".date("d-m-Y").".xlsx";
$Where = "";

// Get the total number of rows in the table


$Where = "id='".$_REQUEST['id']."' AND isDelete=0 ";

$ctable1_r = $db->rp_getData("sales_executive","*",$Where,"id DESC",0);

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
// $column10= 'K';

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
$objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Sales Officer Type");
$objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Phone");
$objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Name");
$objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Email");
$objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Address");
$objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Pin Code");
$objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Country");
$objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "State");
$objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "City");
// $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "IMEI");
// $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Zone");
  
  
  
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
    

  $count++;
  $column = 'A';
  for($j=0; $j<10;$j++)  
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
      $value = $row['type'];
      
    }
    else if($j==3)
    {
      $value = $row['phone'];
    }
    else if($j==4)
    {
      $value = $row['email'];
    } 
    else if($j==5)
    {
      $value = $row['address'];
    } 
    else if($j==6)
    {
      $value = $row['zip'];
    } 
    else if($j==7)
    {
      $value = $row['country'];
    } 
    else if($j==8)
    {
      $value = $row['state'];
    } 
    else if($j==9)
    {
      $value = $row['city'];
    } 
    // else if($j==10)
    // {
    //   $value = $row['imei'];
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
$objWriter->save(INQUIRY_REPORT_FILES.$file_name); 
$file_path1 = trim(ADMINFOLDER."/inquiry_documents/".$file_name);
$arr = array("file_path"=>$file_path1);
require_once 'disconnect.php'; 
echo json_encode($arr);
?>