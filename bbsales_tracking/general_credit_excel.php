<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Generel_credit_note"."_".date("d-m-Y").".xlsx";
// $file_name  = SALESEXECUTIVE_EXPORT_EXCEL;
$Where = "";

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  // $customer_r=$db->rp_getData("invoice","DISTINCT customer_id","customer_name like '%".$_REQUEST['searchName']."%'","",0);
  $customer_r=$db->rp_getData("executive","id","company_name like '%".$_REQUEST['searchName']."%' OR cname like '%".$_REQUEST['searchName']."%'","",0);
  $cust_id=array();
  if($customer_r){
    while($customer_d=mysqli_fetch_assoc($customer_r))
    {
      $cust_id[]=$customer_d['id'];
    }
  }
  $cust_id=implode(",",$cust_id);
  $Where .="customer_id IN (".$cust_id.") AND";
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
 $Where .= "  payment_type = '".$_REQUEST['type']."' AND ";
 $type=$_REQUEST['type'];
}
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
  $Where .= "  payment_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' AND ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
     $Where .= "  payment_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' AND ";
}

$Where .= " isDelete=0";

$ctable1_r = $db->rp_getData("general_credit_note","*",$Where,"payment_date DESC",0);

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

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
$objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Company Name");
$objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Customer Name");
$objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Discount Type");
$objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Sales Person Name	");
$objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Credit Note No.");
$objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Payment by");
$objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Credit Note Date");
$objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Payment Amount");
  
  
  
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($ctable_d = mysqli_fetch_assoc($ctable1_r))  
{  
        if($ctable_d['payment_type']==1)
        {
          $type = "By Cash"; 
        }
        else if ($ctable_d['payment_type']==2)
        {
          $type = "By Cheque"; 
        }
        else if ($ctable_d['payment_type']==3)
        {
          $type = "Online"; 
        }
        else if ($ctable_d['payment_type']==4)
        {
          $type = "Other"; 
        }

  $count++;
  $column = 'A';
  for($j=0; $j<9;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      $value = $db->rp_getValue("executive","company_name","id='".$ctable_d['customer_id']."'");
    }
    else if($j==2)
    {
      $value = $db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'");
      
    }

    else if($j==3)
    {
       $get_discount_type_r=$db->rp_getData("discount_type","name","isDelete=0 AND id IN(".$ctable_d['discount_type_id'].")");
      $get_names=array();
      while($get_discount_type_d=mysqli_fetch_assoc($get_discount_type_r)) 
      {
            $get_names[]=$get_discount_type_d["name"];
      }
      $get_names=implode(",", $get_names);
      $value = $get_names;
      
    }
    else if($j==4)
    {
      $value = $db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_executive_id']."'");;
    }
    else if($j==5)
    {
      $value = $ctable_d['receipt_no'];
    }
    else if($j==6)
    {
      $value = $type;
    } 
    else if($j==7)
    {
      $value = date('d-m-Y',strtotime($ctable_d['payment_date']));
    } 
    else if($j==8)
    {
      $value = $db->rp_num($ctable_d['paid_amount']);
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