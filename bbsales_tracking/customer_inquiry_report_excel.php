<?php
$page_id=572;$page_slug='customer_inquiry_page';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Customer Inquiry Report"."_".date("d-m-Y").".xlsx";
$Where = "";

// Get the total number of rows in the table

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
  $Query=$_REQUEST['searchName'];
  $Where.=" (company_name like '%".$Query."%' OR mobile_number like '%".$Query."%' OR id like '%".$Query."%' OR person_name like '%".$Query."%' OR country like '%".$Query."%' OR state like '%".$Query."%' OR city like '%".$Query."%') AND";
}

if(isset($_REQUEST['c_type']) && $_REQUEST['c_type']!="" && $_REQUEST['c_type']!=NULL)
{
  $Where .= "executive_type = '".$_REQUEST['c_type']."' AND ";
}

if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="")
{
  $Where .= "status='".$_REQUEST['status_id']."' AND ";
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
 $Where .= "sales_executive_id = '".$_REQUEST['type']."' AND ";
}


if(isset($_REQUEST['country']) && $_REQUEST['country']!="" && $_REQUEST['country']!=NULL)
{
  $Where .= "country = '".$_REQUEST['country']."' AND ";
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL)
{
  $Where .= "state = '".$_REQUEST['state']."' AND ";
}

if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL)
{
  $Where .= "city = '".$_REQUEST['city']."' AND ";
}
if(isset($_REQUEST['assigned_to']) && $_REQUEST['assigned_to']!="" && $_REQUEST['assigned_to']!=NULL)
{
    $ctable_where .= "inquiry_assign_to = '".$_REQUEST['assigned_to']."' ";
    $assigned_to=$_REQUEST['assigned_to'];
}
$Where .= " isDelete=0";
$ctable1_r = $db->rp_getData("customer_inquiry","id,source_of_inquiry,executive_type,company_name,person_name,mobile_number,country,state,city,date_of_call,inquiry_created_by,inquiry_assign_to",$Where,"id DESC",0); 

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
$column11  = 'L';




 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Source Medium");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Customer Type");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Company Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Person Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Mobile Number");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Country");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "State");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "City");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Date Of Call");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Inquiry Taken By");
  $objPHPExcel->getActiveSheet()->setCellValue($column11.$rowCount, "Inquiry Assigned To");
  
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_array($ctable1_r))  
{ 
  $count++;
  $column = 'A';
  for($j=0; $j<mysqli_num_fields($ctable1_r);$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
       $value = $db->rp_getValue("source_of_inquiry","name","id='".$row['source_of_inquiry']."'");
    }
    else if($j==2)
    {
      $value = $db->rp_getValue("customer_type","name","id='".$row['executive_type']."'");
    }
    else if($j==3)
    {
      $value = $row['company_name'];
    }
    else if($j==4)
    {
      $value = $row['person_name'];
    }
    else if($j==5)
    {
      $value = $row['mobile_number'];
    }
    else if($j==6)
    {
      $value = $row['country'];
    }
    else if($j==7)
    {
      $value = $row['state'];
    }
    else if($j==8)
    {
      $value = $row['city'];
    }
    else if($j==9)
    {
      $value = date('d-m-Y',strtotime($row['date_of_call']));
    }
    else if($j==10)
    {
      $value=$db->rp_getValue("sales_executive","name","id='".$row['inquiry_created_by']."'");;
    }
    else if($j==11)
    {
      $value = $db->rp_getValue("sales_executive","name","id='".$row['inquiry_assign_to']."'");
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