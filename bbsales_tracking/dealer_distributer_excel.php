<?php
$page_id=406;$page_slug='page_admin';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
// $file_name  = "Sales Officer Report"."_".date("d-m-Y").".xlsx";
$file_name  = "systemuser_Report_".date('d_m_Y')."_".strtotime("now").".xlsx";
$Where = "";
$TYPE=array(1=>"Customer",2=>"Sales Exective");

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  $ctable_where .= " (
              name like '%".$_REQUEST['searchName']."%'         
            ) AND ";
}

$ctable_where .= " isDelete=0";

$ctable1_r = $db->rp_getData("dealer_distributor_network","*",$ctable_where,"id DESC",0);

/*for log*/
$flag = "Web";
$module_name = "Category";
$log_description = $module_name." Export Excel By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
$last_id = "";
$ctable = "dealer_distributor_network";
$db->insertLog($ctable,$last_id,"excel","",$insert,0,$log_description,$flag,$module_name,$user_id,"");
/*for log*/

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

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
$objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, " Name");
$objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, " UserName");
$objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, " Admin Type");
$objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, " Type");
$objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, " Email");

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
  // print_r($row);exit();
  $count++;
  $column = 'A';
  for($j=0; $j<6;$j++)  
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
      $value = $row['username'];
    }
    else if($j==3)
    {
      $value = $db->rp_getValue("admin_type","name","id='".$row['admin_type']."'",0);
    }
    else if($j==4)
    {
      $value = $TYPE[$row['type']];
    }
    else if($j==5)
    {
      $value = $row['email'];
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