<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "route_report".time()."_".date("d-m-Y").".xlsx";
$ctable = "master_route";
$ctable1 = "master_route";

$ctable_where = "";

// Get the total number of rows in the table

// echo $fromdate_to_loop;
// echo $todate_to_loop;
// // // echo $todate;
// exit();
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
    $sales_id = $db->rp_getData("sales_executive", "id", "name LIKE '%" . $_REQUEST['searchName'] . "%' AND isDelete = 0", "", 0);
    
    if($sales_id) {
        $SALESID = array();
        
        while ($sales_d = mysqli_fetch_assoc($sales_id)) {
            $SALESID[] = $sales_d['id'];
        }
        
        $SALESID = implode(",", $SALESID);
        $ctable_where .= "sales_executive_id IN (" . $SALESID . ") AND ";
    } else {
        $ctable_where .= "sales_executive_id IN (0) AND ";
        // $ctable_where .= "(sales_executive_id IN (0) OR ";
    }
}

if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id'] != "" && $_REQUEST['sales_executive_id'] != "null") {
    $ctable_where .= " sales_id IN (" . $_REQUEST['sales_executive_id'] . ") AND ";
    $sales_id = $_REQUEST['sales_executive_id'];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE'] != 0 && $_SESSION[SITE_SESS.'_ADMIN_TYPE'] != 14) {
    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    $ctable_where .= " sales_executive_id='" . $check_id . "' AND ";
} else {
    $ctable_where .= " ";
}


$ctable_where .= " isDelete = 0";

$monthName = isset($_REQUEST['filter_month'])?$db->clean($_REQUEST['filter_month']):0; // Specify the month name here
$yearName = isset($_REQUEST['filter_year'])?$db->clean($_REQUEST['filter_year']):0; // Specify the year name here

$timestamp = strtotime($monthName);
$monthNumber = date('m', $timestamp);

$fromdate_to_loop = $yearName."-".$monthNumber."-"."01";

$todate_to_loop = date('Y-m-t', strtotime("$yearName-$monthNumber-01"));

$ctable_where .= " AND ( DATE(start_date)>='".date("Y-m-d",strtotime($fromdate_to_loop))."' AND DATE(start_date)<='".date("Y-m-d",strtotime($todate_to_loop))."'  ) ";

$ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "id DESC", 0);

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



 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Start Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "End Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Sales Officer Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Assigned Route");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Change Route");


$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable_r))  
{  
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
      $value = $row['start_date'];
    } 
    else if($j==2)
    {
      $value = $row['end_date'];
    }
    else if($j==3)
    {
      $value = $db->rp_getValue("sales_executive", "name", "isDelete=0 AND id='" . $row['sales_id'] . "'");
    }
    else if($j==4)
    {
      $value = $db->rp_getValue("area", "name", "isDelete=0 AND id='" . $row['area_id'] . "'",0);
    }
    else if($j==5)
    {
      $visit_data_r = $db->rp_getData("visit","customer_id","isDelete=0 AND user_id='".$sales_id."' AND ( DATE(start_date_time)>='".date("Y-m-d",strtotime($row['start_date']))."' AND DATE(start_date_time)<='".date("Y-m-d",strtotime($row['end_date']))."'  ) GROUP BY customer_id ","",0);

      if ($visit_data_r) 
      {
          while($visit_data_d = mysqli_fetch_assoc($visit_data_r))
          {
              $area_name = $db->rp_getValue("executive","city","isDelete=0 AND id='".$visit_data_d['customer_id']."'");

              $area_id = $db->rp_getValue("area","id","isDelete=0 AND name LIKE '%".strtolower(trim($area_name))."%' ");

              if ($area_id != $row['area_id']) 
              {
                $array_of_area[] = $area_name;
              }
          }
          $value = implode(" , ",$array_of_area);
      }
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
$objWriter->save(ROUTE_REPORT_FILES.$file_name); 
$file_path1 = trim(ADMINFOLDER."/report/route_variation/".$file_name);
$arr = array("file_path"=>$file_path1);
require_once 'disconnect.php';
echo json_encode($arr);
?>