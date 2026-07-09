<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Complain_Report"."_".date("d-m-Y").".xlsx";
$Where = "";

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){

  $Where .= " (request_no like '%".$db->clean($_REQUEST['searchName'])."%') AND ";

}
// print_r($_REQUEST["sales_executive"]);exit;
if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!=undefined){
  $Where .= " user_id='".$_REQUEST["sales_executive"]."' AND ";
  $sid = $_REQUEST["sales_executive"];
}

if(isset($_REQUEST["customer_id"]) && $_REQUEST["customer_id"]!="" && $_REQUEST["customer_id"]!=undefined){
  $Where .= "customer_id='".$_REQUEST["customer_id"]."' AND ";
  $cid = $_REQUEST["customer_id"];
}

if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="")
{
  $Where .= "status='".$_REQUEST['status_id']."' AND ";
  $status_id=$_REQUEST['status_id'];
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
  //echo $_REQUEST['df'];exit;
  $date_filter_query = urldecode( $_REQUEST['df'] );

  $date_filter_query_ex=explode(" to ",$date_filter_query);

  $Where .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}

$Where .= " isDelete=0";
$ctable1_r = $db->rp_getData("request","id,request_no,user_id,customer_id,request_type,request_cat_id,request_subcat_id,remark,app_address,created_Date,status",$Where,"id DESC",0);

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



 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Request No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Date and Time");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Sales Officer Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Customer Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Source of Request");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Request Category");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Request Sub Category");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Description");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Address");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Status");
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
$status_array = array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done","-3"=>"Cancel");
$complain_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
$request_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
  $count++;
  $column = 'A';
  for($j=0; $j<11;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      $value = "#".$row['request_no'];
    } 
    else if($j==2)
    {
      $value = date("d-m-Y h:i A",strtotime($row['created_Date']));
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
      $value =stripslashes($request_type_array[$row['request_type']]);
    }
     else if($j==6)
    {
      $value =$db->rp_getValue("complain_category","name","id='".$row['request_cat_id']."'");
    }
     else if($j==7)
    {
      $value = $db->rp_getValue("complain_sub_category","name","id='".$row['request_subcat_id']."'");
    }
     else if($j==8)
    {
      $value = stripslashes($row['remark']);
    }
    else if($j==9)
    {
      $value =  $row['app_address'];
    }
    else if($j==10)
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