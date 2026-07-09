<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Sales Officer Report"."_".date("d-m-Y").".xlsx";
$Where = "";

// Get the total number of rows in the table

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
  $Query=$_REQUEST['searchName'];
  $Where.= " (name like '%".$db->clean($_REQUEST['searchName'])."%' OR email like '%".$db->clean($_REQUEST['searchName'])."%' OR phone  LIKE '%".$db->clean($_REQUEST['searchName'])."%' OR username  LIKE '%".$db->clean($_REQUEST['searchName'])."%'
  ) AND ";
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
  $Where .= " type = '".$_REQUEST['type']."' AND";
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL)
{
  $Where .= " state = '".$_REQUEST['state']."' AND ";
}

if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL)
{
  $Where .= " city = '".$_REQUEST['city']."' AND";
}

if(isset($_REQUEST['main_city']) && $_REQUEST['main_city']!="" && $_REQUEST['main_city']!=NULL)
{
  $Where .= " main_city = '".$_REQUEST['main_city']."' AND";
}

if(isset($_REQUEST['zone']) && $_REQUEST['zone']!="" && $_REQUEST['zone']!=NULL)
{
  $Where .= " zone = '".$_REQUEST['zone']."' AND";
}

$Where .= " isDelete=0";

$ctable1_r = $db->rp_getData("sales_executive","username,id,type,name,email,phone,state,main_city,city,zone",$Where,"id DESC",0);

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

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
$objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Sales Officer Type");
$objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Name");
$objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Username");
$objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Email");
$objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "CUG No");
$objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "State");
$objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "City");
$objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Route");
$objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Zone");
  
  
  
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
    if($row['type']=="sales_manager")
    {
      $sales_executive_type="M.D.";
    }

    if($row['type']=="area_sales_manager")
    {
      $sales_executive_type="General Manager";
    }
    
    if($row['type']=="sales_officer")
    {
      $sales_executive_type="Regional Sales Manager";
    }
    
    if($row['type']=="sales_executive")
    {
      $sales_executive_type="Sales Officer";
    }
    if($row['type']=="area_manager")
    {
      $sales_executive_type="Area Sales Manager";
    }

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
      $value = $sales_executive_type;
    }
    else if($j==2)
    {
      $value = $row['name'];
      
    }
    else if($j==3)
    {
      $value = $row['username'];
      
    }
    else if($j==4)
    {
      $value = $row['email'];
    }
    else if($j==5)
    {
      $value = $row['phone'];
    } 
    else if($j==6)
    {
      $value = $row['state'];
    } 
    else if($j==7)
    {
      $value = $row['main_city'];
    } 
 else if($j==8)
    {
      $value = $row['city'];
    } 

    else if($j==9)
    {
      $value = $db->rp_getValue("zone","name","id='".$row['zone']."' AND isDelete=0",0);
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