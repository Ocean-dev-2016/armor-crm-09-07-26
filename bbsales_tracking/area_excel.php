<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
// $file_name  = "Sales Officer Report"."_".date("d-m-Y").".xlsx";
$file_name  = "Area Master".date('d_m_Y')."_".strtotime("now").".xlsx";
$Where = "";

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    $ctable_where .= " (name like '%".$_REQUEST['searchName']."%') AND ";
    
    // $data_search = $db->rp_getData("area","class_id","name = '".$_REQUEST['searchName']."' AND isDelete=0","id DESC",0);
    // if($data_search)
    // {
    //     while($data_search_d=mysqli_fetch_assoc($data_search))
    //     {
    //         $order_ids[]=$data_search_d['class_id'];
    //     }
    //     $order_ids=implode(",",$order_ids);
    //     $ctable_where .= "  Id IN (".$order_ids.") AND ";
    // }
    
}
$ctable_where .= " isDelete=0";

if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="" && $_REQUEST['class_id']!=NULL)
{
 $ctable_where .= " AND class_id = '".$_REQUEST['class_id']."' ";
}
// if(isset($_REQUEST['country_id']) && $_REQUEST['country_id']!="" && $_REQUEST['country_id']!=NULL)
// {
//  $ctable_where .= " AND country_id = '".$_REQUEST['country_id']."' ";
// }

$ctable1_r = $db->rp_getData("area","*",$ctable_where,"id ASC",0);

/*for log*/
$flag = "Web";
$module_name = "city";
$log_description = $module_name." Export Excel By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
$last_id = "";
$ctable = "area";
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
// $column4  = 'E';
// $column5  = 'F';
// $column6  = 'G';

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
// $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Sales Officer Type");
$objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "  Country Name");
$objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "State Name");
$objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "City Name");
// $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "State");
// $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "City");
  
  
  
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
    if($row['type']=="sales_manager")
    {
      $sales_executive_type="Business Development Manager";
    }

    if($row['type']=="area_sales_manager")
    {
      $sales_executive_type="Sales Cordinator";
    }
    
    if($row['type']=="sales_officer")
    {
      $sales_executive_type="Sales Representator";
    }
    
    if($row['type']=="sales_executive")
    {
      $sales_executive_type="Sales Officer";
    }

  $count++;
  $column = 'A';
  for($j=0; $j<4;$j++)  
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
      $country_name=$db->rp_getValue("class","country_id","id='".$row['class_id']."' AND isDelete=0",0);
      $country_name_d=$db->rp_getValue("country","name","id='".$country_name."' AND isDelete=0",0);
      $value = $country_name_d;
      
    }
    else if($j==2)
    {
      $value = $value = $db->rp_getValue("class","name","isDelete=0 AND id='".$row['class_id']."' "); 
    }

    else if($j==3)
    {

        $value = $row['name'];
      
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