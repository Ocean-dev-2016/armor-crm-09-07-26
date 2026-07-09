<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Complain_Report"."_".date("d-m-Y").".xlsx";
$Where = "";
$_REQUEST['searchName'] = urldecode($_REQUEST['searchName']);

// Get the total number of rows in the table

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
  $Query=$_REQUEST['searchName'];
  $Where.=" (id LIKE '%".trim($Query)."%'  OR complain_no LIKE '%".trim($Query)."%' ) AND";
}

if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!=undefined){
  // $Where .= " user_id='".$_REQUEST["sales_executive"]."' AND ";
  $Where .= " user_id='".implode(" ", $_REQUEST["sales_executive"])."' AND ";
}

if(isset($_REQUEST["customer_id"]) && $_REQUEST["customer_id"]!="" && $_REQUEST["customer_id"]!=undefined){
  // $Where .= " customer_id='".$_REQUEST["customer_id"]."' AND "; 
  $Where .= " customer_id='".implode(" , ", $_REQUEST['customer_id'])."' AND ";
}

if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="")
{
  $Where .= " class_id='".implode(" , ", $_REQUEST['class_id'])."' AND ";
   // $where .= " class_id='".implode(" , ", $_REQUEST['class_id'])."' AND ";
}

if(isset($_REQUEST['area_id']) && $_REQUEST['area_id']!="")
{
  $Where .= " area_id='".implode(" , ", $_REQUEST['area_id'])."' AND ";
}

if(isset($_REQUEST['df1']) && $_REQUEST['df1']!="")
{
  $date_filter_query = urldecode( $_REQUEST['df1'] );

  $date_filter_query_ex=explode(" to ",$date_filter_query);

  $Where .= " ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) AND ";
}
// print_r($_REQUEST['status_id']); exit();
if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="")
{
  // $Where .= " status='".$_REQUEST['status_id']."' AND ";
  $Where .= " status='".implode(" , ", $_REQUEST['status_id'])."' AND ";
  // $where .= " AND status IN (".$_REQUEST['status_id'].")";
   // $where .= " status='".implode(" ", $_REQUEST['status_id'])."' AND ";
}

$Where .= " isDelete=0";
$ctable1_r = $db->rp_getData("orders","*",$Where,"id DESC",0);

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
$column10  = 'K';
// $column11  = 'L';
// $column12  = 'M';
// $column13  = 'N';
// $column14  = 'O';
// $column15  = 'P';


 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Customer Type");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Customer Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Customer State");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Customer City");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Total Order");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Total Order Value");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Total Visit");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Total Complain");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Total Invoice");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Total Invoice Value");
  // $objPHPExcel->getActiveSheet()->setCellValue($column11.$rowCount, "Longitude");
  // $objPHPExcel->getActiveSheet()->setCellValue($column12.$rowCount, "Address");
  // $objPHPExcel->getActiveSheet()->setCellValue($column13.$rowCount, "State");
  // $objPHPExcel->getActiveSheet()->setCellValue($column14.$rowCount, "City");
  // $objPHPExcel->getActiveSheet()->setCellValue($column15.$rowCount,  "Status");
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
$status_array = array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done","-3"=>"Cancel");
$complain_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
  $count++;
  $column = 'A';
  for($j=0; $j<16;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      $value =  $row['type'];
    } 
    else if($j==2)
    {
      $value = stripslashes($ctable_d['name']);
    }
    else if($j==3)
    {
      $value = $db->rp_getValue("sales_executive","name","id='".$row['user_id']."'");
    }
    else if($j==4)
    {
      $value = $db->rp_getValue("executive","cname","id='".$row['customer_id']."'");

      $value .= "  " .$db->rp_getValue("executive","phone","id='".$row['customer_id']."'");
    }
    else if($j==5)
    {
      $value = $row['title'];
    }
     else if($j==6)
    {
      $value =$complain_type_array[$row['complain_type']];
    }
     else if($j==7)
    {
      $value = $db->rp_getValue("complain_category","name","id='".$row['complain_cat_id']."'");
    }
     else if($j==8)
    {
      $value = $db->rp_getValue("complain_sub_category","name","id='".$row['complain_subcat_id']."'");
    }
    else if($j==9)
    {
      $value = $row['remark'];
    }
    else if($j==10)
    {
      $value = $row['latitude'];
    }
    else if($j==11)
    {
      $value = $row['longitude'];
    }
    else if($j==12)
    {
      $value = $row['app_address'];
    }
      else if($j==13)
    {
      $value = $row['state'];
    }
      else if($j==14)
    {
      $value = $row['city'];
    }
    
    else if($j==15)
    {
      $value = $status_array[$row['status']];
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