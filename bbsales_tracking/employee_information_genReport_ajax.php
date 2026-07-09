<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Leave_Request_Report"."_".date("d-m-Y").".xlsx";
$Where = "";

// Get the total number of rows in the table

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) 
{
  $Query=$_REQUEST['searchName'];
  
  $Where.= " sales_executive_name like '%".$_REQUEST['searchName']."%' AND ";
}

if(isset($_REQUEST["leave_type"]) && $_REQUEST["leave_type"]!="" && $_REQUEST["leave_type"]!=undefined)
{
  $Where .= " leave_type='".$_REQUEST["leave_type"]."' AND ";
}

if(isset($_REQUEST["status"]) && $_REQUEST["status"]!="" && $_REQUEST["status"]!=undefined)
{
  if($_REQUEST['status']==-1)
  {
    $Where .= " status='0' AND ";
  }
  else
  {
    $Where .= " status='".$_REQUEST["status"]."' AND ";
  }
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!="")
{
  $date_filter_query = urldecode( $_REQUEST['df'] );

  $date_filter_query_ex=explode(" to ",$date_filter_query);

  $Where .= " ( DATE(start_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(start_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) AND ";
}

$Where .= " isDelete='0' AND id!='0'";
$ctable1_r = $db->rp_getData("leave_request","id,end_date,end_time,start_date,start_time,sales_executive_name,leave_type,created_date,status,cancel_reject_reason,entry_flag,update_entry_flag",$Where,"id DESC",0);

// Instantiate a new PHPExcel object 
$objPHPExcel = new PHPExcel();  

// Set the active Excel worksheet to sheet 0 
$objPHPExcel->setActiveSheetIndex(0);  
// Initialise the Excel row number 
$rowCount = 1;  

//start of printing column names as names of MySQL fields  
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


 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Sales Executive Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Leave Type");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Leave Request Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "From Date & Time");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "To Date & Time");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Total Days Of Leave");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Cancel/Reject Reason");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Status");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Entry Type");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Update Entry Type");
//end of adding column names  

$rowCount = 2; 
$count =0; 

$status_array = array('0' =>"GENERATED",'1' =>"ACCEPTED",'2' =>"REJECTED",'3' =>"CANCEL");

$entry_type_status = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");

while($row = mysql_fetch_assoc($ctable1_r))  
{ 

  $datetime1 = new DateTime($row['end_date']."".$row['end_time']);
  $datetime2 = new DateTime($row['start_date']."".$row['start_time']);
  $interval = $datetime1->diff($datetime2);
  $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');

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
      $value = $row['sales_executive_name'];
    } 
    else if($j==2)
    {
      $value = $db->rp_getValue("leave_type","name","id='".$row['leave_type']."'");
    }
    else if($j==3)
    {
      $value = date("d-m-Y",strtotime($row['created_date']));
    }
    else if($j==4)
    {
      $value = "From Date & Time: ".date("d-m-Y",strtotime($row['start_date'])). " " . date("h:i a",strtotime($row['start_time']));
    }
    else if($j==5)
    {
      $value = "To Date & Time: ".date("d-m-Y",strtotime($row['end_date'])). " " . date("h:i a",strtotime($row['end_time']));
    }
    else if($j==6)
    {
      $value = $elapsed;
    }
    else if($j==7)
    {
      $value = $row['cancel_reject_reason'];
    }
    else if($j==8)
    {
      $value = $status_array[$row['status']];
    }
     else if($j==9)
    {
      $value = $entry_type_status[$row['entry_flag']];
    }
     else if($j==10)
    {
      $value = $entry_type_status[$row['update_entry_flag']];
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
echo json_encode($arr);
?>