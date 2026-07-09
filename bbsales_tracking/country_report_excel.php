<?php
$page_id=558;$page_slug='page_class';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
// $file_name  = "Sales Officer Report"."_".date("d-m-Y").".xlsx";
$file_name  = "countrydata_".date('d_m_Y')."_".strtotime("now").".xlsx";
$Where = "";

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  $Where .= " (
              name like '%".$_REQUEST['searchName']."%'         
            ) AND ";
}

// if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
// {
//   $Where .= " type = '".$_REQUEST['type']."' AND";
// }

// if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL)
// {
//   $Where .= " state = '".$_REQUEST['state']."' AND ";
// }

// if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL)
// {
//   $Where .= " city = '".$_REQUEST['city']."' AND";
// }

$Where .= " isDelete=0";

$ctable1_r = $db->rp_getData("country","id,name",$Where,"id DESC",0);

/*for log*/
$flag = "Web";
$module_name = "Country";
$log_description = $module_name." Export Excel By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
$last_id = "";
$ctable = "country";
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
// $column2  = 'C';
// $column3  = 'D';
// $column4  = 'E';
// $column5  = 'F';
// $column6  = 'G';

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
// $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Sales Officer Type");
$objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Country Name");
// $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Top Category");
// $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Phone");
// $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "State");
// $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "City");
  
  
  
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
    if($row['type']=="sales_manager")
    {
      $sales_executive_type="Regional Sales Manager";
    }

    if($row['type']=="area_sales_manager")
    {
      $sales_executive_type="Business Development Manager";
    }
    
    if($row['type']=="sales_officer")
    {
      $sales_executive_type="Area Sales Manager";
    }
    
    if($row['type']=="sales_executive")
    {
      $sales_executive_type="Sales Officer";
    }

  $count++;
  $column = 'A';
  for($j=0; $j<2;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    // else if($j==1)
    // {
    //   $value = $sales_executive_type;
    // }
    else if($j==1)
    {
      $value = $row['name'];
      
    }
    else if($j==2)
    {
      $value = $db->rp_getValue("top_category_master","name","id='".$row['tcid']."'");
    }
    else if($j==4)
    {
      $value = $row['phone'];
    } 
    else if($j==5)
    {
      $value = $row['state'];
    } 
    else if($j==6)
    {
      $value = $row['city'];
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