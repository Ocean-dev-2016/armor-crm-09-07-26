<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "LR_Report"."_".date("d-m-Y").".xlsx";
// $file_name  = LR_EXPORT_EXCEL;
$Where = "";
$ctable_where = "";
$ctable_where .= " isDelete=0 ";
$_REQUEST['searchName'] = urldecode($_REQUEST['searchName']); 

// Get the total number of rows in the table

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
  $Query=$_REQUEST['searchName'];
  $getR = $db->rp_getData("invoice_new","id","invoice_no LIKE '%".$Query."%' AND isDelete=0");
  if($getR)
  {
    $invoice_id = array();
    while($getD = mysqli_fetch_assoc($getR))
    {
      $invoice_id[] = $getD['id'];
    }
    $invoice_id = implode(",",$invoice_id);
    if($invoice_id!="")
    {
      $ctable_where.=" AND invoice_id IN (".$invoice_id.") ";  
    }
    else
    {
      $ctable_where.=" AND (id LIKE '%".$Query."%'  OR lr_number LIKE '%".$Query."%' )";   
    } 
  }
  else
  {
    $ctable_where.=" AND (id LIKE '%".$Query."%'  OR lr_number LIKE '%".$Query."%' )";
  }
}



$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
  $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
  if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
  $page_number = 1; //if there's no page number, set it to 1
}






// $Where .= " isDelete=0";
$ctable1_r = $db->rp_getData("lr_detail","*",$ctable_where,"id ASC",0);

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
// $column7  = 'H';
// $column8  = 'I';
// $column9  = 'J';
// $column10  = 'K';
// $column11  = 'L';
// $column12  = 'M';
// $column13  = 'N';
// $column14  = 'O';
// $column15  = 'P';


 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Invoice No");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "LR Number");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Remark");
 
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
$status_array = array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done","-3"=>"Cancel");
$complain_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
$ENTRY_FLAG = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");
  $count++;
  $column = 'A';
  for($j=0; $j<4;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      $value = $db->rp_getValue("invoice_new","invoice_no","isDelete=0 AND id='".$row['invoice_id']."'");
    } 
    else if($j==2)
    {
      
           $value = $row['lr_number'];
      
    }
    else if($j==3)
    {
      $value = $row['remark'];
    }
    else if($j==4)
    {
      $value = $db->rp_getValue("executive","cname","id='".$row['customer_id']."'");

      $value .= "  " .$db->rp_getValue("executive","phone","id='".$row['customer_id']."'");
    }
    // else if($j==5)
    // {
    //   $value = $row['title'];
    // }
     else if($j==5)
    {
      $value =$complain_type_array[$row['complain_type']];
    }
     else if($j==6)
    {
      $value = $db->rp_getValue("complain_category","name","id='".$row['complain_cat_id']."'");
    }
     else if($j==7)
    {
      $value = $db->rp_getValue("complain_sub_category","name","id='".$row['complain_subcat_id']."'");
    }
    else if($j==8)
    {
      $value = $row['remark'];
    }
    // else if($j==10)
    // {
    //   $value = $row['latitude'];
    // }
    // else if($j==11)
    // {
    //   $value = $row['longitude'];
    // }
    else if($j==9)
    {
      $value = $row['app_address'];
    }
      else if($j==10)
    {
      $value = $db->rp_getValue("class","name","id='".$row['class_id']."'");
    }
      else if($j==11)
    {
      $value = $db->rp_getValue("area","name","id='".$row['area_id']."'");
    }
    
    else if($j==12)
    {
      $value = $status_array[$row['status']];
    }
    else if($j==13)
    {
      $value = $db->rp_getValue("sales_executive","name","id='".$row['complain_assign_to']."'",0);
    }
    else if($j==14)
    {
      $value = $ENTRY_FLAG[$row['entry_flag']];
    }
    // else if($j==14)
    // {
    //   $value = $ENTRY_FLAG[$row['update_entry_flag']];
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